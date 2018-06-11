<?php
namespace UMC\model;

class DirectoryFiles {

    //type string
    public $directory;
    //type array
    public $files;

    public function __construct($directory, $files = []) {
        $this->directory = $directory;
        $this->files = $files;
    }

    public function addFile(\UMC\model\File $file) {
        if (!empty($file)) {
            $this->files[] = $file;
        }
    }

}
