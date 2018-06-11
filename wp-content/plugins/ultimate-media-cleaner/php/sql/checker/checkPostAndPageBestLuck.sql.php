<?php

namespace UMC\sql\checker;

use UMC\model\Option;
/**
 *
 * @author nicearma
 */
class SqlCheckPostAndPageBestLuck extends SqlCheckAbstract {

    function check($src, $id, Option $option) {

        $posts = $this->db->posts;
        if (!empty($id)) {
            //FIND in the post parent the reference, this will useful if the image is used where was uploaded
            $sql = "SELECT id"
                    . " FROM $posts"
                    . " WHERE  post_parent in"
                    . " (SELECT post_parent FROM $posts"
                    . " WHERE id=" . $id . " )"
                    . " and post_content LIKE '%/$src%'";
        }
        return $this->db->get_col($sql);
    }

}
