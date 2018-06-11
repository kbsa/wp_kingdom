<?php

namespace UMC\rest;

use UMC\service\FileService;
use UMC\service\DirectoryService;
use UMC\model\Response;
use UMC\consts\ResponseCode;
use UMC\consts\FileType;

add_action('wp_ajax_umc_file_find_id', 'UMC\rest\umc_file_find_id');

function umc_file_find_id() {
    $fileRest = new FileRest();
    $fileRest->findId();
}

add_action('wp_ajax_umc_file_count_regular', 'UMC\rest\umc_file_count_regular');

function umc_file_count_regular() {
    $fileRest = new FileRest();
    $fileRest->countRegular();
}

add_action('wp_ajax_umc_file_count_image', 'UMC\rest\umc_file_count_image');

function umc_file_count_image() {
    $fileRest = new FileRest();
    $fileRest->countImage();
}

add_action('wp_ajax_umc_file_get_images', 'UMC\rest\umc_file_get_images');

function umc_file_get_images() {
    $fileRest = new FileRest();
    $fileRest->getImages();
}

add_action('wp_ajax_umc_file_get_regulars', 'UMC\rest\umc_file_get_regulars');

function umc_file_get_regulars() {
    $fileRest = new FileRest();
    $fileRest->getRegulars();
}

add_action('wp_ajax_umc_file_delete_regular', 'UMC\rest\umc_file_delete_regular');

function umc_file_delete_regular() {
    $fileRest = new FileRest();
    $fileRest->deleteRegular();
}

add_action('wp_ajax_umc_file_delete_image', 'UMC\rest\umc_file_delete_image');

function umc_file_delete_image() {
    $fileRest = new FileRest();
    $fileRest->deleteImage();
}

class FileRest {

    private $fileService;
    private $directoryService;

    public function __construct() {
        $this->fileService = new FileService();
        $this->directoryService = new DirectoryService();
    }

    function countRegular() {
        $count = $this->fileService->count(FileType::regular);
        $response = new Response(200, $count);
        $response->json();
        wp_die();
    }

    function countImage() {
        $count = $this->fileService->count(FileType::image);
        $response = new Response(200, $count);
        $response->json();
        wp_die();
    }

    function getImages() {
        $data = get_json();
        $file = $this->fileService->get(FileType::image, $data['page'], $data['size']);
        $response = new Response(200, $file);
        $response->json();
        wp_die();
    }

     function getRegulars() {
        $data = get_json();
        $file = $this->fileService->get(FileType::regular, $data['page'], $data['size']);
        $response = new Response(200, $file);
        $response->json();
        wp_die();
    }
    
    function deleteImage() {
        $data = get_json();
        $id = $data['id'];
        $name = $data['name'];
        $type = $data['type'];
        $sizeName = $data['sizeName'];
        $response = new Response(ResponseCode::successful);

        if (FileType::isImage($type) && !empty($id)) {
            $verification = $this->fileService->deleteImage($id, $name, $sizeName);
            $response->response = $verification;
        } else {
            $response = new Response(ResponseCode::badRequest);
        }
        $response->json();

        wp_die();
    }
    
    function deleteOrphan() {
        $data = get_json();

        $response = new Response(ResponseCode::successful);

        if (!empty($id)) {
            $verification = $this->fileService->deleteRegular($id, $name);
            $response->response = $verification;
        } else {
            $response = new Response(ResponseCode::badRequest);
        }
        $response->json();

        wp_die();
    }

    function deleteRegular() {
        $data = get_json();
        $id = $data['id'];
        $name = $data['name'];

        $response = new Response(ResponseCode::successful);

        if (!empty($id)) {
            $verification = $this->fileService->deleteRegular($id, $name);
            $response->response = $verification;
        } else {
            $response = new Response(ResponseCode::badRequest);
        }
        $response->json();

        wp_die();
    }

    function findId() {
        $data = get_json();
        $file = $this->fileService->findId($data['name'], $data['directory']);
        if (empty($file)) {
            $file = $this->directoryService->getFile($data['name'], $data['directory']);
        }
        $response = new Response(ResponseCode::successful, $file);
        $response->json();
        wp_die();
    }

}

?>