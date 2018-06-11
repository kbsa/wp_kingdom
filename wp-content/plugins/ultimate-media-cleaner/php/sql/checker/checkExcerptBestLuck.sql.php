<?php

namespace UMC\sql\checker;

use UMC\model\Option;
/**
 *
 * @author nicearma
 */
class SqlCheckExcerptBestLuck extends SqlCheckAbstract {

    function check($src, $id, Option $option) {

        $posts = $this->db->posts;

        if ($option->check->excerpt && !empty($id)) {
            $sql = "SELECT id"
                    . " FROM $posts"
                    . " WHERE post_excerpt in "
                    . "(SELECT post_parent FROM $posts"
                    . " WHERE id=" . $id . " )"
                    . " and post_excerpt LIKE '%/$src%'"
                    . " limit 0,1";
            return $this->db->get_col($sql);
        }

        return array();
    }

}
