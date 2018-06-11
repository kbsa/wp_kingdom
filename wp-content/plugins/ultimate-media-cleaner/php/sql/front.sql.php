<?php

namespace UMC\sql;

use UMC\model\Option;

/**
 * All about database and SQL to search image
 *
 * @author nicearma
 */
class FrontSql {

    private $db;
    private $regex = '\\\[(\\\[?)(.*)';

    function __construct() {
        global $wpdb;
        $this->db = $wpdb;
    }

    public function countShortCodeContent(Option $option) {
        $sql = $this->getSqlShortCode($option, 'content', true);
        return $this->db->get_col($sql)[0];
    }

    public function getShortCodeContent($page, $size, Option $option) {

        $sql = $this->getSqlShortCode($option, 'content');

        $sql .= ' LIMIT ' . ($page * $size) . ", $size ;";

        return $this->db->get_col($sql);
    }

    public function countShortCodeExcerpt(Option $option) {
        $sql = $this->getSqlShortCode($option, 'excerpt', true);
        return $this->db->get_col($sql)[0];
    }

    public function getShortCodeExcerpt($page, $size, Option $option) {

        $sql = $this->getSqlShortCode($option, 'excerpt');

        $sql .= ' LIMIT ' . ($page * $size) . ", $size ;";

        return $this->db->get_col($sql);
    }

    public function getSqlShortCode(Option $option, $type, $count = false) {
        $posts = $this->db->posts;
        $select = 'id';
        switch ($type) {
            case 'excerpt':
                $condifition = 'post_excerpt';
                break;
            case 'content':
            default:
                $condifition = 'post_content';
                break;
        }
        if ($count) {
            $select = 'count(*)';
        }

        if ($option->check->draft) {

            $sql = "SELECT $select"
                    . " FROM $posts"
                    . " WHERE $condifition is not null"
                    . " and $condifition!=''"
                    . " and post_type not in ('attachment','nav_menu_item')"
                    . " AND $condifition REGEXP  '$this->regex'";
        } else {

            $sql = "SELECT $select"
                    . " FROM $posts"
                    . " WHERE $condifition is not null"
                    . " and $condifition!=''"
                    . " and post_type not in ('attachment','nav_menu_item','revision')"
                    . " and post_status !='draft'"
                    . " AND $condifition REGEXP  '$this->regex'";
        }
        return $sql;
    }

    /**
     * Get the post information (this is use for the backup)
     *
     * @param type $id Id of the post
     * @return type
     */
    public function getRowPost($id) {

        $posts = $this->db->posts;

        $sql = "SELECT *"
                . " FROM $posts"
                . " where id= $id ;";

        return $this->db->get_results($sql, "ARRAY_A");
    }

    /**
     * Get the postmeta information (This is use for the backup file)
     *
     * @param type $id Id of the post
     * @return type
     */
    public function getRowPostMeta($id) {

        $postmeta = $this->db->postmeta;

        $sql = "SELECT *"
                . " FROM $postmeta"
                . " where post_id= $id ;";

        return $this->db->get_results($sql, "ARRAY_A");
    }

}
