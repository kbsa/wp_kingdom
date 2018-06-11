<?php
namespace UMC\sql\checker;

use UMC\model\Option;
/**
 *
 * @author nicearma
 */
class SqlCheckPostAndPageAll extends SqlCheckAbstract {

    function check($src, $id, Option $option) {
        
        $posts = $this->db->posts;

        if ($option->check->draft) {
            $sql = "SELECT id"
                    . " FROM $posts"
                    . " WHERE post_content is not null"
                    . " and post_content!=''"
                    . " and post_type not in ('attachment','nav_menu_item')"
                    . " and post_content LIKE '%/$src%'"
                    . " limit 0,1";
        } else {
            $sql = "SELECT id"
                    . " FROM $posts"
                    . " WHERE post_content is not null and post_content!=''"
                    . " and post_type not in ('attachment','nav_menu_item','revision')"
                    . " and post_status !='draft'"
                    . " and post_content LIKE '%/$src%'"
                    . " limit 0,1";
        }

        return $this->db->get_col($sql);
    }

}
