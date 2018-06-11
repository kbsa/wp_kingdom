<?php
namespace UMC\model;
/**
 * Generic HTTP response
 */
class Response {

    /**
     * HTTP code
     * @var type 
     */
    public $code;
    /**
     * Any type of response
     * @var type 
     */
    public $response;

    /**
     * Response constructor.
     * @param $code
     * @param $response
     */
    public function __construct($code, $response)
    {
        $this->code = $code;
        $this->response = $response;
    }

    public function json() {
        echo json_encode($this);
    }



}
