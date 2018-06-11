<?php

namespace UMC\service;

use UMC\model\Option;
use UMC\model\Verification;
use UMC\sql\checker\Checkers;
use UMC\consts\FileStatus;

class VerifyService {

    private $checkers;

    public function __construct() {
        $this->checkers = new Checkers();
    }

    public function verify($name, $id = null) {

        $verification = new Verification();
        $verification->id = $id;
        $verification->name = $name;
        $verification->status = FileStatus::not_used;
        $resultVerify = $this->checkers->verify($name, $id, new Option());
        if ($resultVerify) {
            $verification->status = FileStatus::used;
        }

        return $verification;
    }
    
    

}
