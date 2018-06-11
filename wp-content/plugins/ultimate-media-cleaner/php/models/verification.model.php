<?php

namespace UMC\model;

class Verification {

    public $id;
    public $name;
    public $status;
    public $sizes;

    public function __construct() {
        $this->sizes = array();
    }

}

class VerificationImage extends Verification {

    public $sizeName;

    public function __construct(Verification $verification, $sizeName) {
        parent::__construct();
        $this->id = $verification->id;
        $this->name = $verification->name;
        $this->status = $verification->status;
        $this->sizeName = $sizeName;
        $this->sizes = null; 
    }

}
