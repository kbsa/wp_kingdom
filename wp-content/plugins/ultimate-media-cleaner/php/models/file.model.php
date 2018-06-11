<?php
namespace UMC\model;
/**
 * Use to get files information in your WP site
 */
class File
{
    
   public $id;
   /**
     * The name in the database or the file name
     * @var
     */
    public $name;
    /**
     * TODO: i dont know for the moment
     * @var
     */
    public $status;
    /**
     * The directory in the server
     * @var
     */
    public $directory;
    /**
     * @var
     */
    public $src; //the origin src
  
    /**
     * The wordpress URL
     * @var type 
     */
    public $url;
    
    /**
     * The size
     * @var
     */
    public $size;
    /**
     * The file type, Image | Other type, useful to see what to do
     * @var
     */
    public $type;
    
    /**
     * sizes, used only for images
     * @var type 
     */
    public $sizes;

    
    public $sizeName;
    
    public $width;
    
    public $height;

}


?>