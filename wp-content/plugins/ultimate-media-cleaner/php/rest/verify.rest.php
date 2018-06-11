<?php

namespace UMC\rest;

use UMC\service\VerifyService;
use UMC\model\Response;
use UMC\consts\ResponseCode;
use UMC\model\VerificationImage;

add_action('wp_ajax_umc_verify_file', 'UMC\rest\umc_verify_file');

function umc_verify_file() {
    $verifyRest = new VerifyRest();
    $verifyRest->verify();
}

class VerifyRest {

    private $verifyService;

    public function __construct() {
        $this->verifyService = new VerifyService();
    }

    public function verify() {
        $data = get_json();
        $verification = $this->verifyService->verify($data['name'], $data['id']);
        
        if(!empty($data['sizes']) && count($data['sizes']) >0) {
            foreach ($data['sizes'] as $size) {
                $tmpVerification = $this->verifyService->verify($size['name'], $size['id']);
                $verification->sizes[] = new VerificationImage($tmpVerification, $size['sizeName']);
            }
        }

        $response = new Response(ResponseCode::successful, $verification);
        $response->json();
        wp_die();
    }

}
