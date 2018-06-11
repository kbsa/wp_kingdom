<?php

namespace UMC\consts;

/**
 * All HTTP response call
 */
abstract class FileType {

    const image = 'image';
    const all = 'all';
    const regular = 'regular';
    const unknown = 'unknown';
    
    public static function typeFromMime($mime) {
        $type = self::unknown;
        if(!empty($mime)) {
          $mime= explode('/', $mime);
          $type= $mime[0];
        }
        return $type;
    }
    
    public static function isImage($type) {
        return strstr($type, self::image) !== false;
          
    }


}