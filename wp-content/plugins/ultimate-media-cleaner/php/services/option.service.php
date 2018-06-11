<?php

namespace UMC\service;

use UMC\model\Option;
use UMC\options\Backup;
use UMC\options\Check;
use UMC\options\Ignore;
use UMC\options\Show;

class OptionService {

    protected static $NAME = 'umc_options';

    public function get() {

        $options = get_option(self::$NAME);

        if (empty($options)) {
            return null;
        }

        return unserialize($options);
    }

    public function update($jsonOption) {
        // TODO: verify if option is good
        $option = $this->jsonToOption($jsonOption);
        
        update_option(self::$NAME, serialize($option));
        return $option;
    }

    public function jsonToOption($jsonOption) {
       $option = new Option();
       
       $option->backup = new Backup();
       $option->backup->folder =  $jsonOption['backup']['folder'];
       $option->backup->active =  $jsonOption['backup']['active'];
       
       $option->check = new Check();
       $option->check->draft = $jsonOption['check']['draft'];
       $option->check->excerpt = $jsonOption['check']['excerpt'];
       $option->check->gallery = $jsonOption['check']['gallery'];
       $option->check->postMeta = $jsonOption['check']['postMeta'];
       $option->check->shortCode = $jsonOption['check']['shortCode'];
       
       $option->first = $jsonOption['first'];
       
       $option->ignore = new Ignore();
       $option->ignore->sizes = $jsonOption['ignore']['sizes'];
       
       $option->show = new Show();
       $option->show->ignoreList = $jsonOption['show']['ignoreList']; 
       $option->show->used = $jsonOption['show']['used']; 
       return $option;
    }

}
