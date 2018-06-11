<?php

namespace UMC\rest;

use UMC\model\Response;
use UMC\model\Status;
use UMC\consts\ResponseCode;
use UMC\service\BackupService;

/* #################################################################### */
add_action('wp_ajax_umc_backup_create', 'UMC\rest\umc_backup_create');

function umc_backup_create() {
    $backupRest = new BackupRest();
    $backupRest->create();
}

/* #################################################################### */
add_action('wp_ajax_umc_backup_verify', 'UMC\rest\umc_backup_verify');

function umc_backup_verify() {

    $backupRest = new BackupRest();
    $backupRest->verify();
}

/* #################################################################### */
add_action('wp_ajax_umc_backup_image', 'UMC\rest\umc_backup_image');

function umc_backup_image() {
    $backupRest = new BackupRest();
    $backupRest->image();
}

/* #################################################################### */
add_action('wp_ajax_umc_backup_regular', 'UMC\rest\umc_backup_regular');

function umc_backup_regular() {

    $backupRest = new BackupRest();
    $backupRest->regular();
}

/* #################################################################### */
add_action('wp_ajax_umc_backup_orphan', 'UMC\rest\umc_backup_orphan');

function umc_backup_orphan() {

    $backupRest = new BackupRest();
    $backupRest->orphan();
}

/* #################################################################### */
add_action('wp_ajax_umc_backup_restore_image', 'UMC\rest\umc_backup_restore_image');

function umc_backup_restore_image() {

    $backupRest = new BackupRest();
    $backupRest->restoreImage();
}

/* #################################################################### */
add_action('wp_ajax_umc_backup_restore_regular', 'UMC\rest\umc_backup_restore_regular');

function umc_backup_restore_regular() {

    $backupRest = new BackupRest();
    $backupRest->restoreRegular();
}

/* #################################################################### */
add_action('wp_ajax_umc_backup_restore_orphan', 'UMC\rest\umc_backup_restore_orphan');

function umc_backup_restore_orphan() {

    $backupRest = new BackupRest();
    $backupRest->restoreOrphan();
}

/* #################################################################### */
add_action('wp_ajax_umc_backup_get_images', 'UMC\rest\umc_backup_get_images');

function umc_backup_get_images() {

    $backupRest = new BackupRest();
    $backupRest->getImages();
}

/* #################################################################### */
add_action('wp_ajax_umc_backup_get_regulars', 'UMC\rest\umc_backup_get_regulars');

function umc_backup_get_regulars() {

    $backupRest = new BackupRest();
    $backupRest->getRegulars();
}

/* #################################################################### */
add_action('wp_ajax_umc_backup_get_orphans', 'UMC\rest\umc_backup_get_orphans');

function umc_backup_get_orphans() {

    $backupRest = new BackupRest();
    $backupRest->getOrphans();
}

/* #################################################################### */
add_action('wp_ajax_umc_backup_delete', 'UMC\rest\umc_backup_delete');

function umc_backup_delete() {

    $backupRest = new BackupRest();
    $backupRest->delete();
}

class BackupRest {

    private $backupService;

    public function __construct() {
        $this->backupService = new BackupService();
    }

    function create() {
        $this->backupService->create();
        $response = new Response(ResponseCode::successful);
        $response->json();
        wp_die();
    }

    function verify() {
        $status = new Status();
        $status->result = $this->backupService->verify();
        $response = new Response(ResponseCode::successful, $status);
        $response->json();
        wp_die();
    }

    function image() {
        $data = get_json();
        $result = $this->backupService->image($data['id'], $data['sizeName']);
        if ($result) {
            $response = new Response(ResponseCode::successful);
        } else {
            $response = new Response(ResponseCode::badRequest);
        }
        $response->json();
        wp_die();
    }

    function regular() {
        $data = get_json();
        $result = $this->backupService->regular($data['id']);
        if ($result) {
            $response = new Response(ResponseCode::successful);
        } else {
            $response = new Response(ResponseCode::badRequest);
        }
        $response->json();
        wp_die();
    }

    function orphan() {
        $data = get_json();

        $src = $data['src'];
        $name = $data['name'];

        $result = $this->backupService->orphan($src, $name);
        if ($result) {
            $response = new Response(ResponseCode::successful);
        } else {
            $response = new Response(ResponseCode::badRequest);
        }
        $response->json();
        wp_die();
    }

    function restoreImage() {
        $data = get_json();
        $result = $this->backupService->restoreImage($data['id']);
        if ($result) {
            $response = new Response(ResponseCode::successful);
        } else {
            $response = new Response(ResponseCode::badRequest);
        }
        $response->json();
        wp_die();
    }

    function restoreRegular() {
        $data = get_json();
        $result = $this->backupService->restoreRegular($data['id']);
        if ($result) {
            $response = new Response(ResponseCode::successful);
        } else {
            $response = new Response(ResponseCode::badRequest);
        }
        $response->json();
        wp_die();
    }

    function restoreOrphan() {
        $data = get_json();
        $result = $this->backupService->restoreOrphan($data['directory']);
        if ($result) {
            $response = new Response(ResponseCode::successful);
        } else {
            $response = new Response(ResponseCode::badRequest);
        }
        $response->json();
        wp_die();
    }

    function getImages() {
        $directory = $this->backupService->getImages();
        $response = new Response(ResponseCode::successful, $directory);
        $response->json();
        wp_die();
    }

    function getRegulars() {

        $directory = $this->backupService->getRegulars();
        $response = new Response(ResponseCode::successful, $directory);
        $response->json();
        wp_die();
    }

    function getOrphans() {

        $directory = $this->backupService->getOrphans();
        $response = new Response(ResponseCode::successful, $directory);
        $response->json();
        wp_die();
    }
    
    function delete() {
        $data = get_json();
        $this->backupService->delete($data['directory'], $data['type']);
        $response = new Response(ResponseCode::successful);
        $response->json();
        wp_die();
    }

}
