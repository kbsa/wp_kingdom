<?php

namespace UMC\service;

use UMC\model\DirectoryFiles;
use UMC\model\Directory;
use UMC\model\File;
use UMC\consts\FileType;
use UMC\consts\FileStatus;

class DirectoryService {

    public $log;

    public function __construct() {
        $this->log = LoggerService::getInstance();
    }

    public static function uploadDir() {
        $uploadDir = wp_upload_dir();
        $basedir = $uploadDir['basedir'];
        return $basedir;
    }

    public static function uploadUrl() {
        $uploadDir = wp_upload_dir();
        $basedir = $uploadDir['baseurl'];
        return $basedir;
    }

    public static function mimeType($path) {
        $mime = null;
        if (file_exists($path)) {
            $mime = mime_content_type($path);
        }
        return FileType::typeFromMime($mime);
    }

    public static function filePath($directory, $name) {

        return trailingslashit(self::uploadDir() . self::addFirstSlash($directory)) . basename($name);
    }
    
     public static function directoryPath($directory) {

        return trailingslashit(self::uploadDir() . self::addFirstSlash($directory));
    }
    
    public static function addFirstSlash($directory) {
        if(substr( $directory, 0, 1 ) !== "/" ) {
            $directory = '/' . $directory;
        } 
        return $directory;
    }

    public static function fileUrl($directory, $name) {
        // see at https://core.trac.wordpress.org/browser/tags/4.8/src/wp-includes/post.php#L5029
        return trailingslashit(self::uploadUrl() . $directory) . basename($name);
    }

    public function getSimpleFilesFromDirectory($directory, $addUploadDir = true) {

        $wpDirectory = $directory;

        if ($addUploadDir) {
            $wpDirectory = self::uploadDir() . $directory;
        }

        $files = array_diff(scandir($wpDirectory), array('.', '..'));

        $directoryFiles = new DirectoryFiles($directory, $files);

        return $directoryFiles;
    }

    public function getFilesFromDirectory($directory) {

        $wpDirectory = self::uploadDir() . $directory;

        $dirIterator = new \DirectoryIterator($wpDirectory);

        $iter = new \IteratorIterator($dirIterator);

        $directoryFiles = new DirectoryFiles($directory);

        foreach ($iter as $iterFile) {

            if ($iterFile->isFile()) {
                $file = new File();
                $file->name = $iterFile->getFilename();
                $file->directory = $directory;
                $file->src = $iterFile->getPathname();
                $file->url = self::fileUrl('/' . _wp_get_attachment_relative_path($file->src), $file->name);
                $file->type = self::mimeType($iterFile->getPathname());
                $file->size = $iterFile->getSize();
                $file->status = FileStatus::unknown;
                $directoryFiles->addFile($file);
            }
        }
        return $directoryFiles;
    }

    public function getFile($name, $directory) {
        $path = self::filePath($directory, $name);

        if (!file_exists($path)) {
            return null;
        }
        $fileInfo = pathinfo($path);
        $file = new File();
        $file->name = $fileInfo['basename'];
        $file->directory = $directory;
        $file->src = $fileInfo['dirname'] . '/' .  $file->name ;
        $file->url = self::fileUrl('/' . _wp_get_attachment_relative_path($file->src), $file->name);
        $file->type = self::mimeType($file->src);
        $file->size = filesize($path);
        $file->status = FileStatus::unknown;
        return $file;
    }

    public function getDirectories($base) {

        $recursiveDir = new \RecursiveDirectoryIterator($base, \RecursiveDirectoryIterator::SKIP_DOTS);
        $iter = new \RecursiveIteratorIterator(
                $recursiveDir, \RecursiveIteratorIterator::SELF_FIRST, \RecursiveIteratorIterator::CATCH_GET_CHILD
        );

        $directory = new Directory($base);

        foreach ($iter as $path => $dir) {
            if ($dir->isDir()) {
                $directory->addDirectory(str_replace($base, "", $path));
            }
        }

        return $directory;
    }

    public function getDirectoriesFromDirectory($base) {

        $dirIterator = new \DirectoryIterator($base);

        $iter = new \IteratorIterator($dirIterator);

        $directory = new Directory($base);

        foreach ($iter as $file) {
            if (!$file->isFile() && !$file->isDot()) {
                $directory->addDirectory($file->getFilename());
            }
        }

        return $directory;
    }

    public static function delete($src) {
        // trying to delete something that is not at the upload folder
        if (strstr($src, self::uploadDir()) === false) {
            return false;
        }

        if (file_exists($src)) {
            if (is_dir($src)) {
                @rmdir($src);
            } else {
                @unlink($src);
            }
        }
        clearstatcache();
        return !file_exists($src);
    }

    public function deleteDirectory($path) {
        $isUploadFolder = strstr($path, self::uploadDir());
        if (file_exists($path) && ($isUploadFolder !== false)) {

            $directories = $this->getDirectoriesFromDirectory($path);

            if (count($directories->directories) > 0) {
                foreach ($directories->directories as $directory) {
                    // TODO: WHY PATH
                    $this->deleteDirectory($path);
                }
            } else {

                $files = $this->getSimpleFilesFromDirectory($path, false);

                if (count($files->files) > 0) {
                    foreach ($files->files as $file) {
                        $filePath = $path . '/' . $file;
                        $this->delete($filePath);
                    }
                }

                $this->delete($path);
            }
        }
    }

}
