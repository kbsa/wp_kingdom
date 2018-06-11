<?php

namespace UMC\rest;

use UMC\service\DirectoryService;
use UMC\model\Response;
use UMC\consts\ResponseCode;
use UMC\model\Verification;
use \UMC\consts\FileStatus;

/* #################################################################### */
add_action('wp_ajax_umc_directory_search_directory', 'UMC\rest\umc_directory_search_directory');

function umc_directory_search_directory() {
    $uploadRest = new DirectoryRest();
    $uploadRest->searchDirectories();
}

add_action('wp_ajax_umc_directory_search_file', 'UMC\rest\umc_directory_search_file');

function umc_directory_search_file() {
    $uploadRest = new DirectoryRest();
    $uploadRest->searchFiles();
}

add_action('wp_ajax_umc_directory_search_simple_file', 'UMC\rest\umc_directory_search_simple_file');

function umc_directory_search_simple_file() {
    $uploadRest = new DirectoryRest();
    $uploadRest->searchSimpleFiles();
}

add_action('wp_ajax_umc_directory_delete', 'UMC\rest\umc_directory_delete');

function umc_directory_delete() {
    $uploadRest = new DirectoryRest();
    $uploadRest->delete();
}


add_action('wp_ajax_umc_directory_delete_file', 'UMC\rest\umc_directory_delete_file');

function umc_directory_delete_file() {
    $uploadRest = new DirectoryRest();
    $uploadRest->deleteFile();
}

class DirectoryRest {

    private $directoryService;

    public function __construct() {
        $this->directoryService = new DirectoryService();
    }

    function searchDirectories() {
        $dirs = $this->directoryService->getDirectories($this->directoryService->uploadDir());
        $response = new Response(ResponseCode::successful, $dirs);
        $response->json();
        wp_die();
    }

    function searchFiles() {
        $data = get_json();
        $files = $this->directoryService->getFilesFromDirectory($data['directory']);
        $response = new Response(ResponseCode::successful, $files);
        $response->json();
        wp_die();
    }

    function searchSimpleFiles() {
        $data = get_json();
        $files = $this->directoryService->getSimpleFilesFromDirectory($data['directory']);
        $response = new Response(ResponseCode::successful, $files);
        $response->json();
        wp_die();
    }

    function delete() {
        $data = get_json();
        $response = new Response(ResponseCode::successful);
        if (!empty($data['src'])) {
            $deleted = DirectoryService::delete($data['src']);
            $verification = new Verification();
            if ($deleted) {
                $verification->status = FileStatus::deleted;
            } else {
                $verification->status = FileStatus::error_delete;
            }
            $response->response = $verification;
        } else {
            $response = new Response(ResponseCode::badRequest);
        }

        $response->json();
        wp_die();
    }
    
     function deleteFile() {
        $data = get_json();
        $src = $data['src'];
        $response = new Response(ResponseCode::successful);
        if(!$this->directoryService->delete($src)) {
            $response = new Response(ResponseCode::badRequest);
        }
        $response->json();
        wp_die();
     }

}

?>