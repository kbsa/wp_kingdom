<?php

namespace UMC\model;

class Logger {
    //LoggerType
    public $type;
    //string
    public $message;
    
    public function __construct($message, $type) {
        $this->message = $message;
        $this->type = $type;
    }
}