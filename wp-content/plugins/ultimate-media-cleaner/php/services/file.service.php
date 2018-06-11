<?php

namespace UMC\service;

use UMC\model\Count;
use UMC\model\File;
use UMC\sql\FileSql;
use UMC\sql\ImageSql;
use UMC\consts\FileType;
use UMC\consts\FileStatus;
use UMC\model\Verification;

class FileService {

    protected $fileSql;
    protected $imageSql;
    protected $log;

    public function __construct() {
        $this->fileSql = new FileSql();
        $this->imageSql = new ImageSql();
        $this->log = LoggerService::getInstance();
    }

    public function count($type) {
        $call = $this->getService($type);
        return new Count(call_user_func_array(array($call, 'count'), array()));
    }

    public function getService($type) {
        switch ($type) {
            case FileType::image:
                return $this->imageSql;
            case FileType::all:
            case FileType::regular:
            default:
                return $this->fileSql;
        }
    }

    public function get($type, $page, $size, $order = 0) {
        $call = $this->getService($type);
        $ids = call_user_func_array(array($call, 'get'), array($page, $size, $order));

        $files = array();

        foreach ($ids as $id) {
            $files[] = $this->convertIdToFile($id);
        }

        return $files;
    }

    public function convertIdToFile($id) {
        $attachment = wp_get_attachment_metadata($id);
        $file = null;
        //TODO: something is not right, see way, this was found in my production server
        // It seem that only image have attachment
        if (!empty($attachment) && array_key_exists('file', $attachment)) {

            $baseDirs = explode('/', $attachment["file"]);
            $name = array_pop($baseDirs);
            $directory = '/' . implode('/', $baseDirs) . '/';

            $file = $this->createBasicFile($id, $name, $directory);

            $isImage = array_key_exists('sizes', $attachment);
            if ($isImage) {
                $file->sizeName = 'original';
                $file->height = $attachment['height'];
                $file->width = $attachment['width'];
                $file->sizes = $this->getImageSizes($id, $attachment['sizes'], $file->directory, $directory);
            }
        } else {
            //get other type of file 
            $wpFilePath = str_replace(DirectoryService::uploadDir(), '', get_attached_file($id));
            
            $baseDirs = explode('/', $wpFilePath);
            $name = array_pop($baseDirs);
            $directory = '/' . implode('/', $baseDirs) . '/';
            $file = $this->createBasicFile($id, $name, $directory);
        }
        return $file;
    }

    public function createBasicFile($id, $name, $directory) {
        $file = new File();
        $file->id = $id;
        $file->name = $name;
        $file->status = FileStatus::unknown;
        $file->directory = $directory;
        $file->url = DirectoryService::fileUrl($directory, $name);
        $file->src = DirectoryService::filePath($directory, $name);
        $file->size = filesize($file->src);
        $file->type = DirectoryService::mimeType($file->src);
        return $file;
    }

    private function getImageSizes($id, $sizes, $path, $directory) {
        $imageSizes = [];
        foreach ($sizes as $name => $size) {
            $imageSize = new File();
            $imageSize->id = $id;
            $imageSize->name = $size['file'];
            $imageSize->directory = $path;
            $imageSize->src = DirectoryService::filePath($directory, $imageSize->name);
            $imageSize->url = DirectoryService::fileUrl($directory, $imageSize->name);
            $imageSize->height = $size['height'];
            $imageSize->width = $size['width'];
            $imageSize->sizeName = $name;
            $imageSize->status = FileStatus::unknown;
            $imageSize->size = $this->fileSize($imageSize->src);
            $imageSize->type = DirectoryService::mimeType($imageSize->src);
            $imageSizes[$name] = $imageSize;
        }
        return $imageSizes;
    }

    private function fileSize($path) {
        $size = -1;
        if (file_exists($path)) {
            $size = filesize($path);
        }
        return $size;
    }

    public function findId($name, $directory) {

        $url = DirectoryService::fileUrl($directory, $name);

        $id = attachment_url_to_postid($url);

        if ($id !== 0) {
            return $this->convertIdToFile($id);
        }

        $id = $this->fileSql->findId($name, $directory);

        if (!empty($id)) {
            // probably is one image size, 
            // TODO: verify this
            return $this->convertIdToFile($id);
        }
        return null;
    }

    public function findImageSizeId($name, $id) {
        $imageSize = new File();
        $attachment = wp_get_attachment_metadata($id);
        foreach ($attachment['sizes'] as $sizeName => $size) {
            if ($size['file'] === $name) {
                $imageSize->sizeName = $sizeName;
            }
        }

        return $imageSize;
    }

    public function deleteRegular($id, $name) {
        $deleted = wp_delete_attachment($id) !== false;
        $verification = new Verification();
        $verification->id = $id;
        $verification->name = $name;
        $verification->status = FileStatus::error_delete;
        if ($deleted) {
            $verification->status = FileStatus::deleted;
        }
        return $verification;
    }

    public function deleteImage($id, $name, $sizeName) {
        $file = $this->convertIdToFile($id);

        $attachment = wp_get_attachment_metadata($file->id);

        if (empty($sizeName)) {

            $this->deleteRegular($file->id);
            $verification = new Verification();
            $verification->id = $id;
            $verification->status = FileStatus::deleted;

            foreach ($file->sizes as $imageSize) {
                $verficationImageSize = new Verification();
                $verficationImageSize->id = $id;
                $verficationImageSize->name = $imageSize->name;
                $verficationImageSize->sizeName = $imageSize->sizeName;
                $verficationImageSize->status = FileStatus::deleted;
                $verification->sizes[$imageSize->sizeName] = $verficationImageSize;
            }
            return $verification;
        } else {

            $imageSize = $file->sizes[$sizeName];

            $verficationImageSize = new Verification();
            $verficationImageSize->id = $id;
            $verficationImageSize->name = $imageSize->name;
            $verficationImageSize->sizeName = $imageSize->sizeName;

            $verficationImageSize->status = FileStatus::error_delete;
            clearstatcache();

            if (!file_exists($imageSize->src)) {

                unset($attachment["sizes"][$sizeName]);
                wp_update_attachment_metadata($id, $attachment);
            } else {

                if (DirectoryService::delete($imageSize->src)) {

                    clearstatcache();

                    if (!file_exists($imageSize->src)) {

                        $verficationImageSize->status = FileStatus::deleted;

                        unset($attachment["sizes"][$imageSize->sizeName]);
                        wp_update_attachment_metadata($file->id, $attachment);
                        $imageSize->status = FileStatus::deleted;
                    }
                }
            }
            return $verficationImageSize;
        }
    }

}
