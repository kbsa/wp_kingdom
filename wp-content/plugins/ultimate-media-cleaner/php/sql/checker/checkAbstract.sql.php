<?php
namespace UMC\sql\checker;

use \UMC\model\Option;
/**
 *
 * @author nicearma
 */
abstract class SqlCheckAbstract {

    protected $db;

    function __construct($db)
    {
        $this->db = $db;
    }

    abstract function check($src, $id, Option $option);

}