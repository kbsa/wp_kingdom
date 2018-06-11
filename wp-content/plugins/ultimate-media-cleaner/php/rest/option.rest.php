<?php

namespace UMC\rest;

use UMC\service\OptionService;
use UMC\consts\ResponseCode;
use UMC\model\Response;

add_action('wp_ajax_umc_option_update', 'UMC\rest\umc_option_update');

function umc_option_update() {
    $optionRest = new OptionRest();
    $optionRest->update();
}

add_action('wp_ajax_umc_option_get', 'UMC\rest\umc_option_get');

function umc_option_get() {
    $optionRest = new OptionRest();
    $optionRest->get();
}

class OptionRest {

    protected $optionService;

    public function __construct() {
        $this->optionService = new OptionService();
    }

    function get() {
        $options = $this->optionService->get();
        $response = new Response(ResponseCode::successful, $options);
        $response->json();
        wp_die();
    }

    function update() {
        $data = get_json();   
        $option = $data['option'];
        $this->optionService->update($option);
        $response = new Response(ResponseCode::successful, $option);
        $response->json();
        wp_die();
    }

}
