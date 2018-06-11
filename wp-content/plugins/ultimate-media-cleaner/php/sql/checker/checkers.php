<?php

namespace UMC\sql\checker;

use UMC\model\Option;
/**
 *
 * @author nicearma
 */
class Checkers {

    private $checkers = array();

    function __construct() {
        global $wpdb;
        $this->addChecker(new SqlCheckPostAndPageBestLuck($wpdb));
        $this->addChecker(new SqlCheckExcerptBestLuck($wpdb));
        $this->addChecker(new SqlCheckPostMeta($wpdb));
        $this->addChecker(new SqlCheckPostAndPageAll($wpdb));
        $this->addChecker(new SqlCheckExcerptAll($wpdb));
    }

    private function addChecker($checker) {
        array_push($this->checkers, $checker);
    }

    public function verify($src, $id, Option $options) {
        
        for ($i = 0; $i < count($this->checkers); $i++) {
            $result = call_user_func_array(array($this->checkers[$i], "check"), array($src, $id, $options));
            if (!empty($result) && count($result) > 0) {
                return true; //is used
            }
        }
        return false; //is unused/not used
    }

}
