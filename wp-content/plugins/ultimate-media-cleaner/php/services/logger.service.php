<?php

namespace UMC\service;

use UMC\consts\LoggerType;

class LoggerService {

    public $actions;
    
    private static $singleton;

    private function __construct() {
        $this->actions = [];
    }

    public function addInfo($message) {
        $action = new Logger($message, LoggerType::info);
        $this->addAction($action);
    }

    public function addWarning($message) {
        $action = new Logger($message, LoggerType::warning);
        $this->addAction($action);
    }

    public function addError($message) {
        $action = new Logger($message, LoggerType::error);
        $this->addAction($action);
    }

    private function addAction($action) {
        $this->actions[] = $action;
    }

    public static function getInstance() {
        if (self::$singleton == null) {
            self::$singleton = new LoggerService();
        }
        return self::$singleton;
    }

}
