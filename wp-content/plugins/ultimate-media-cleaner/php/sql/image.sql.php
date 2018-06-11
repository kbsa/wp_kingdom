<?php

namespace UMC\sql;

/**
 * All about database and SQL to search image
 *
 * @author nicearma
 */
class ImageSql {

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
                . " and $posts.post_mime_type like  'image%'"
                . " and $posts.id=$postmeta.post_id"
                . " and $postmeta.meta_key='_wp_attachment_metadata' ";
        return $this->db->get_col($sql)[0];
    }

    /**
     * Get all 
     */
    public function get($page, $size, $order) {

        $posts = $this->db->posts;
        $postmeta = $this->db->postmeta;

        $sql = 'SELECT id'
                . " FROM $posts, $postmeta"
                . " where $posts.post_type='attachment'"
                . " and $posts.post_mime_type like  'image%'"
                . " and $posts.id=$postmeta.post_id"
                . " and $postmeta.meta_key='_wp_attachment_metadata' ";
        $last = " ORDER BY  `$postmeta`.`meta_id`";

        if ($order == 0) {
            $last .= ' ASC ';
        } else {
            $last .= ' DESC ';
        }
        $sql .= $last . ' LIMIT ' . ($page * $size) . ", $size";

        return $this->db->get_col($sql);
    }


}
