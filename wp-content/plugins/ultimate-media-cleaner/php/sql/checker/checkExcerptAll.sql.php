<?php
namespace UMC\sql\checker;

use UMC\model\Option;
/**
 *
 * @author nicearma
 */
class SqlCheckExcerptAll extends SqlCheckAbstract {

    function check($src, $id, Option $option) {

        if ($option->check->excerpt) {
            
            $posts = $this->db->posts;

            if ($option->check->draft) {
                $sql = "SELECT id"
                        . " FROM $posts"
                        . " WHERE post_excerpt is not null and post_excerpt!=''"
                        . " and post_type not in ('attachment','nav_menu_item')"
                        . " and post_excerpt LIKE '%/$src%' limit 0,1";
            } else {
                $sql = "SELECT id"
                        . " FROM $posts"
                        . " WHERE post_excerpt is not null and post_excerpt!=''"
                        . " and post_type not in ('attachment','nav_menu_item','revision')"
                        . " and post_status !='draft'"
                        . " and post_excerpt LIKE '%/$src%' limit 0,1";
            }

            return $this->db->get_col($sql);
        }

        return array();
    }

}
