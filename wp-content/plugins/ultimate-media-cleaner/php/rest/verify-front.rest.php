<?php

namespace UMC\rest;

use UMC\service\VerifyFrontService;
use UMC\model\Response;
use UMC\consts\ResponseCode;

add_action('wp_ajax_umc_verify_front_html_sc', 'UMC\rest\umc_verify_front_html_sc');

function umc_verify_front_html_sc() {
    $verifyFrontRest = new VerifyFrontRest();
    $verifyFrontRest->getHtmlShortCodes();
}

add_action('wp_ajax_umc_verify_front_count_html_sc', 'UMC\rest\umc_verify_front_count_html_sc');

function umc_verify_front_count_html_sc() {
    $verifyFrontRest = new VerifyFrontRest();
    $verifyFrontRest->countHtmlShortCodes();
}


class VerifyFrontRest {

    public $verifyFrontService;

    public function __construct() {
        $this->verifyFrontService = new VerifyFrontService();
    }

    public function getHtmlShortCodes() {
        $data = get_json();
        $htmls = $this->verifyFrontService->getHtmlShortCodes($data['page'],$data['size']);
        $response = new Response(ResponseCode::successful, $htmls);
        $response->json();
        wp_die();
    }

    public function countHtmlShortCodes() {
        $count = $this->verifyFrontService->countHtmlShortCodes();
        $response = new Response(ResponseCode::successful, $count);
        $response->json();
        wp_die();
    }
}
