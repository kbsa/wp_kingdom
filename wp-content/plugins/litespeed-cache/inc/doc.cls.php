<?php

/**
 * The Doc class.
 *
 * @since     	2.2.7
 * @package    	LiteSpeed_Cache
 * @subpackage 	LiteSpeed_Cache/inc
 * @author     	LiteSpeed Technologies <info@litespeedtech.com>
 */

class LiteSpeed_Cache_Doc
{
	// private static $_instance ;

	/**
	 * Privacy policy
	 *
	 * @since 2.2.7
	 * @access public
	 */
	public static function privacy_policy()
	{
		return __( 'This site utilizes caching in order to facilitate a faster response time and better user experience. Caching potentially stores a duplicate copy of every web page that is on display on this site. All cache files are temporary, and are never accessed by any third party, except as necessary to obtain technical support from the cache plugin vendor. Cache files expire on a schedule set by the site administrator, but may easily be purged by the admin before their natural expiration, if necessary.', 'litespeed-cache' ) ;
	}

}