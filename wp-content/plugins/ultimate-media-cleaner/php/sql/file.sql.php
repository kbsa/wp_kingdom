<?php

namespace UMC\sql;

/**
 * All about database and SQL to search files information
 *
 * @author nicearma
 */
class FileSql {

    private $db;

    function __construct() {
        global $wpdb;
        $this->db = $wpdb;
    }

    /**
     * Count all image at the DB
     */
    public function count() {

        $posts = $this->db->posts;
        $postmeta = $this->db->postmeta;

        $sql = "SELECT count(*)"
                . " FROM $posts, $postmeta "
                . " where $posts.post_type='attachment'"
                . " and $posts.id=$postmeta.post_id"
                . " and $posts.post_mime_type not like '%image%'  ";
        return $this->db->get_col($sql)[0];
    }

    /**
     * Get all 
     */
    public function get($page, $size, $order) {

        $posts = $this->db->posts;
        $postmeta = $this->db->postmeta;

        $sql = "SELECT id"
                . " FROM $posts, $postmeta"
                . " where $posts.post_type='attachment'"
                . " and $posts.id=$postmeta.post_id"
                . " and $posts.post_mime_type not like '%image%'  ";
        $last = " ORDER BY  `$postmeta`.`meta_id`";

        if ($order == 0) {
            $last .= ' ASC ';
        } else {
            $last .= ' DESC ';
        }
        $sql .= $last . ' LIMIT ' . ($page * $size) . ", $size";
        return $this->db->get_col($sql);
    }

    /**
     * Get all 
     */
    public function findId($name, $directory) {

        if ($directory[0] === '/') {
            $directory = substr($directory, 1);
        }

        $postmeta = $this->db->postmeta;

        $sql = "SELECT post_id"
                . " FROM  $postmeta"
                . " where $postmeta.meta_key='_wp_attachment_metadata'"
                . " and $postmeta.meta_value like '%$directory%'"
                . " and $postmeta.meta_value like '%$name%'"
                . " limit 1";
        return $this->db->get_col($sql)[0];
    }

}
