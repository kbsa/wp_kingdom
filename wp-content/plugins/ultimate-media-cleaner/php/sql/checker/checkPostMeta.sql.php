<?php

namespace UMC\sql\checker;

use UMC\model\Option;
/**
 *
 * @author nicearma
 */
class SqlCheckPostMeta extends SqlCheckAbstract {

    function check($src, $id, Option $option) {

        if ($option->check->postMeta) {
            
            $postmeta = $this->db->postmeta;

            $sql = "SELECT post_id"
                    . " FROM $postmeta"
                    . " WHERE meta_key not in ('_wp_attachment_metadata','_wp_attached_file')"
                    . " and meta_value LIKE '%/$src%'"
                    . " limit 0,1";

            return $this->db->get_col($sql);
        }

        return array();
    }

}
