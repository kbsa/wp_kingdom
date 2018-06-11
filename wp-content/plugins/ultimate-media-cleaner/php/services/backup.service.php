<?php

namespace UMC\service;

use UMC\sql\BackupSql;

class BackupService {

    static $images = 'images';
    static $regulars = 'regulars';
    static $orphans = 'orphans';
    public $directoryService;
    public $optionService;
    public $fileService;
    public $backupSql;

    public function __construct() {
        $this->directoryService = new DirectoryService();
        $this->optionService = new OptionService();
        $this->fileService = new FileService();
        $this->backupSql = new BackupSql();
    }

    public function create() {
        $this->createFolder($this->backupDirectory());
        $this->createFolder($this->backupImagesDirectory());
        $this->createFolder($this->backupRegularsDirectory());
        $this->createFolder($this->backupOrphansDirectory());
    }

    public function verify() {
        $verification = file_exists($this->backupDirectory()) && file_exists($this->backupImagesDirectory()) && file_exists($this->backupRegularsDirectory()) && file_exists($this->backupOrphansDirectory());
        return $verification;
    }

    public function regular($id) {
        $backupRegularDirectory = $this->backupRegularsDirectory();
        $file = $this->fileService->convertIdToFile($id);
        if (is_writable($backupRegularDirectory)) {

            $backupImageDirectoryById = $backupRegularDirectory . '/' . $id . '/';
            $this->createFolder($backupImageDirectoryById);

            $backupInfo = array();

            $backupInfo["directory"] = $file->directory;
            $backupInfo["posts"] = $this->backupSql->getRowPost($id);
            $backupInfo["postMeta"] = $this->backupSql->getRowPostMeta($id);

            $backupDbFile = $backupImageDirectoryById . $id . '.backup';
            if (!file_exists($backupDbFile)) {
                file_put_contents($backupDbFile, serialize($backupInfo));
            }

            $filePath = $file->src;
            $backupImage = $backupImageDirectoryById . $file->name;
            if (file_exists($filePath)) {
                copy($filePath, $backupImage);
            } else {
                return false;
            }
            return true;
        }
        return false;
    }

    public function orphan($src, $name) {
        $backupOrphanDirectory = $this->backupOrphansDirectory();
        if (is_writable($backupOrphanDirectory)) {

            $partialSrc = str_replace(DirectoryService::uploadDir(), '', $src);

            $backupName = str_replace(array('.', '/'), '_', $partialSrc);

           
            $backupInfo = array("src" => $src);

            $backupFileDirectory = $backupOrphanDirectory . '/' . $backupName;

            $backupFile = $backupFileDirectory . '/'. $backupName . '.backup';
            $this->createFolder($backupFileDirectory);
            
            if (!file_exists($backupFile)) {
                file_put_contents($backupFile, serialize($backupInfo));
            }

            $backupFile = $backupFileDirectory . '/' . $name;

            copy($src, $backupFile);
            return true;
        }
        return false;
    }

    public function image($id, $sizeName) {
        $backupImageDirectory = $this->backupImagesDirectory();
        $image = $this->fileService->convertIdToFile($id);
        if (is_writable($backupImageDirectory)) {

            $backupImageDirectoryById = $backupImageDirectory . '/' . $id . '/';
            $this->createFolder($backupImageDirectoryById);

            $backupInfo = array();

            $backupInfo["directory"] = $image->directory;
            $backupInfo["posts"] = $this->backupSql->getRowPost($id);
            $backupInfo["postMeta"] = $this->backupSql->getRowPostMeta($id);

            $backupDbFile = $backupImageDirectoryById . $id . '.backup';
            if (!file_exists($backupDbFile)) {
                file_put_contents($backupDbFile, serialize($backupInfo));
            }

            if ($sizeName == 'original') {
                $imagePath = $image->src;
                $backupImage = $backupImageDirectoryById . $image->name;
                if (file_exists($imagePath)) {
                    copy($imagePath, $backupImage);
                }

                foreach ($image->sizes as $imageSize) {
                    $imageSizePath = $imageSize->src;
                    $backupImageSize = $backupImageDirectoryById . $imageSize->name;
                    if (file_exists($imagePath)) {
                        copy($imageSizePath, $backupImageSize);
                    }
                }
            } else {

                if (array_key_exists($sizeName, $image->sizes)) {
                    $imageSize = $image->sizes[$sizeName];
                    $imageSizePath = $imageSize->src;
                    $backupImageSizePath = $backupImageDirectoryById . $imageSize->name;
                    if (file_exists($imageSizePath)) {
                        copy($imageSizePath, $backupImageSizePath);
                    }
                }
            }

            return true;
        }
        return false;
    }

    public function restoreImage($id) {
        return $this->restoreFile($this->backupImagesDirectory($id));
    }

    public function restoreRegular($id) {
       return $this->restoreFile($this->backupRegularsDirectory($id));
    }

    public function restoreOrphan($directory) {
        $backupOrphanDirectory = $this->backupOrphansDirectory($directory);
        if (!file_exists($backupOrphanDirectory)) {
            return false;
        }
        $directoryFiles = $this->directoryService->getSimpleFilesFromDirectory($backupOrphanDirectory, false);

        if (!(count($directoryFiles->files) > 0)) {
            return false;
        }

        $file = array_pop(preg_grep("/^(?!.*\\.backup)/", $directoryFiles->files));
        $backupFile = array_pop(preg_grep("/^(.*\\.backup)/", $directoryFiles->files));
        $backupFileBackup = $backupOrphanDirectory . '/' . $backupFile;
        $backupContent = unserialize(file_get_contents($backupFileBackup));
        
        $realFilePath = $backupContent["src"];
        $fileBackupPath = $backupOrphanDirectory . '/' . $file;
        rename($fileBackupPath, $realFilePath);

        $this->directoryService->deleteDirectory($backupOrphanDirectory);
    }

    protected function restoreFile($id, $backupDirectory) {
        $backupFileDirectory = $backupDirectory . '/' . $id;
        
        if (!file_exists($backupFileDirectory)) {
            return false;
        }

        $directoryFiles = $this->directoryService->getSimpleFilesFromDirectory($backupFileDirectory, false);

        if (!(count($directoryFiles->files) > 0)) {
            
            $this->directoryService->deleteDirectory($backupFileDirectory);
            return false;
        }
        $files = preg_grep("/^(?!.*\\.backup)/", $directoryFiles->files);
        $backupFile = array_pop(preg_grep("/^(.*\\.backup)/", $directoryFiles->files));
        $backupFileBackup = $backupFileDirectory . '/' . $backupFile;

        $backupContent = unserialize(file_get_contents($backupFileBackup));

        foreach ($backupContent["posts"] as $posts) {
            $this->backupSql->replacePost($posts);
        }
        foreach ($backupContent["postMeta"] as $postMeta) {
            $this->backupSql->replacePostMeta($postMeta);
        }

        $resultRename = true;
        $realUploadDirectory = DirectoryService::uploadDir() . $backupContent["directory"];

        foreach ($files as $file) {
            $fileBackupPath = $backupFileDirectory . '/' . $file;
            $realFilePath = $realUploadDirectory . $file;

            if (file_exists($realUploadDirectory) && !file_exists($realFilePath)) {
                rename($fileBackupPath, $realFilePath);
            } else {
                $resultRename = false;
            }
        }


        if ($resultRename) {
            $this->directoryService->deleteDirectory($backupFileDirectory);
        }

        return $resultRename;
    }

    public function getImages() {
        return $this->directoryService->getDirectoriesFromDirectory($this->backupImagesDirectory());
    }

    public function getRegulars() {
        return $this->directoryService->getDirectoriesFromDirectory($this->backupRegularsDirectory());
    }

    public function getOrphans() {
        return $this->directoryService->getDirectoriesFromDirectory($this->backupOrphansDirectory());
    }
    
    public function delete($directory, $type) {
        if($type === self::$images) {
            return $this->directoryService->deleteDirectory($this->backupImagesDirectory($directory));
        } elseif($type === self::$orphans) {
            return $this->directoryService->deleteDirectory($this->backupOrphansDirectory($directory));
        } elseif ($type === self::$regulars) {
            return $this->directoryService->deleteDirectory($this->backupRegularsDirectory($directory));
        }
        
        return false;
        
    }

    private function createFolder($path) {
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
    }

    private function backupDirectory($directory = null, $name = null) {
        
        $option = $this->optionService->get();

        $backupDirectory = $this->directoryService->directoryPath($option->backup->folder);

        if(!empty($directory)) {
            $backupDirectory = $backupDirectory . '/' . $directory;
        }
        
         if(!empty($name)) {
            $backupDirectory = $backupDirectory . '/' . $name;
        }
        
        return $backupDirectory;
    }

    private function backupImagesDirectory($id = null) {
        return $this->backupDirectory(self::$images, $id);
    }

    private function backupRegularsDirectory($id = null) {
         return $this->backupDirectory(self::$regulars, $id );
    }

    private function backupOrphansDirectory($directory = null) {
        return $this->backupDirectory(self::$orphans, $directory);
    }
    


}
