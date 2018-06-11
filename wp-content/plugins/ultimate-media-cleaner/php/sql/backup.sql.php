<?php

namespace UMC\sql;

class BackupSql {

    private $db;

    function __construct() {
        global $wpdb;
        $this->db = $wpdb;
    }

    /**
     * Get the post information
     *
     * @param type $id Id of the post
     * @return type
     */
    public function getRowPost($id) {
        $posts = $this->db->posts;
        $sql = "SELECT * FROM $posts where id=  $id;";
        return $this->db->get_results($sql, "ARRAY_A");
    }

    /**
     * Get the postmeta information
     *
     * @param type $id Id of the post
     * @return type
     */
    public function getRowPostMeta($id) {
        $postmeta = $this->db->postmeta;
        $sql = "SELECT * FROM $postmeta where post_id=$id ;";
        return $this->db->get_results($sql, "ARRAY_A");
    }
    
    public function replacePost($info) {
        $posts = $this->db->posts;
        $this->db->replace($posts, $info);
    }
    
    public function replacePostMeta($info) {
        $postMeta = $this->db->postmeta;
        $this->db->replace($postMeta, $info);
    }

}
