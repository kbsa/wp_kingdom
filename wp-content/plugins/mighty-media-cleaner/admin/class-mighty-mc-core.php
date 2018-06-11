<?php
/**
 * The core functionality of the plugin.
 *
 * @link       http://hibit.co/mighty-media-cleaner
 * @since      0.1.0
 *
 * @package    Mighty_MC
 * @subpackage Mighty_MC/admin
 */

/**
 * The core functionality of the plugin.
 *
 * @package    Mighty_MC
 * @subpackage Mighty_MC/admin
 * @author     Hibit <hibit.team@gmail.com>
 */
class Mighty_MC_Core
{
	public $tables;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1.0
	 */
	public function __construct()
	{
		if (!defined('MIGHTYFOLDER')) {
			define('MIGHTYFOLDER', $this->check_mighty_folder());
		}

	}

	/**
	 * Fetch list of media from media library
	 *
	 * @since    0.1.0
	 */
	public function media_from_media_library()
	{
		$query_media_args = array(
			'post_type' => array('attachment'),
			'post_status' => array('inherit'),
			'posts_per_page' => '-1',
		);
		$query_media = new WP_Query($query_media_args);
		$files = array();
		foreach ($query_media->posts as $media) {
			$files[$media->ID] = wp_get_attachment_url($media->ID);
		}
		return $files;
	}

	/**
	 * Fetch list of media from uploads dir
	 *
	 * @since    0.1.0
	 */
	public function media_from_uploads_dir()
	{
		/*List all files in uploads directories*/
		$upload_dir = wp_upload_dir();
		$upload_dir = $upload_dir['basedir'];
		$dirs = scandir($upload_dir);
		$files = array();
		foreach ($dirs as $dir) {
			if (intval($dir) > 0) {
				$directories[] = $upload_dir . '/' . $dir;
			}
		}
		foreach ($directories as $dir) {
			if (!is_dir($dir)) {
				continue;
			}
			$di = new RecursiveDirectoryIterator($dir);
			foreach (new RecursiveIteratorIterator($di) as $filename => $file) {
				$fileName = $file->getFilename();
				if ($fileName == '.' || $fileName == '..') {
					continue;
				} else {
					if (!preg_match('/.*?[0-9]+x[0-9]+[.].*?/is', $fileName) && !preg_match('/.*@2x.*?/is', $fileName)) {
						$filename = str_replace('\\', '/', $filename);
						$pos = strpos($filename, '/uploads/');
						$filename = substr($filename, $pos);
						$files[] = $filename;
					}
				}
			}
		}
		
		return $files;
	}

	/**
	 * Find unused files in uploads dir
	 *
	 * @since    0.1.0
	 */
	public function scan_upload_dir()
	{
		$files = $this->media_from_uploads_dir();
		/*Get list ao all attachments*/
		$wp_files = $this->media_from_media_library();
		$attachedFiles = array();
		foreach ($wp_files as $file) {
			$pos = strpos($file, '/uploads/');
			$file = substr($file, $pos);
			$attachedFiles[] = $file;
		}
		$result = array_diff($files, $attachedFiles);
		return $result;
	}

	/**
	 * Copy from all tables from db and make
	 *
	 * @since    0.1.0
	 */
	public function db_files_handler()
	{
		//list tables  and export form database
		$sql = "SHOW TABLES LIKE '%'";
		global $wpdb;
		$result = $wpdb->get_results($sql);
		$tables = array();
		$upload_dir = wp_upload_dir();
		$mighty_dir = $upload_dir['basedir'] . '/mighty-mc/';
		foreach ($result as $index => $value) {
			foreach ($value as $tableName) {
				$this->tables[] = $tableName;
				$exportFile = $mighty_dir . $tableName . ".sql";
				if (file_exists($exportFile)) {
					@unlink($exportFile);
				}
				$export_str = "";
				$result = $this->query_whole_table($tableName);
				foreach($result as $record){
					$export_str .= implode("\t",$record) . "\n";
				}
				$export_str = preg_replace('/\\r\\n/', "\t", $export_str);
				$export_str = preg_replace('/\\n\\n/', "\t", $export_str);
				// output the file
				file_put_contents($exportFile, $export_str);
			}
		}
		return $this->tables;
	}

	/**
	 * Find unused files in database
	 *
	 * @since    0.1.0
	 */
	public function scan_database($mode = 'full')
	{
		$start = microtime(true);
		$upload_dir = wp_upload_dir();
		$mighty_dir = $upload_dir['basedir'] . '/mighty-mc/';
		$start = microtime(true);
		$notfounds = array();
		global $wpdb;
		if ('full' == $mode || '' == $mode) {
			$items = $this->media_from_media_library();
		} elseif (is_array($mode)) {
			$items = $mode;
		} else {
			return false;
		}
		// Start scan files for each file url
		$results = array();
		$tables = (isset($_SESSION['mc-tables'])) ? $_SESSION['mc-tables'] : $this->db_files_handler();
		foreach ($items as $id => $url) {
			$id = (string)$id;
			$filename = pathinfo($url, PATHINFO_FILENAME);
			$extension = pathinfo($url, PATHINFO_EXTENSION);
			$base = strpos($url, '/uploads/');
			$base += 9;
			$url = substr($url, $base);
			$url_without_file = str_replace($filename.'.'.$extension,'',$url);
			$url2 = addcslashes(addcslashes($url_without_file, '/'), '/');
			$results[$id]['total'] = 0;
			$notfound = true;
			$pattern_url = preg_quote($url_without_file, '/').preg_quote($filename, '/').'(-[0-9]+x[0-9]+)?[.]'.$extension;
			$pattern_url = "/^.*$pattern_url.*\$/m";
			$pattern_url2 = preg_quote($url2, '/').preg_quote($filename, '/').'(-[0-9]+x[0-9]+)?[.]'.$extension;
			$pattern_url2 = "/^.*$pattern_url2.*\$/m";
			$pattern_id = preg_quote($id, '/');
			$pattern_id = "/[^[a-z0-9#=.(-_{]]*?" . $pattern_id . "[^[a-z0-9#=.)-]]*?/i";
			foreach ($tables as $table) {
				$tableFile = $mighty_dir . $table . ".sql";
				$matches = array();
				$handle = @fopen($tableFile, "r");
				if ($handle) {
					while (!feof($handle)) {
						$buffer = fgets($handle);
						$buffer = trim(preg_replace('/\s+/', ' ', $buffer)) . ' ';
						// Eliminate extra results
						if (substr($buffer, 0, strlen($id)) == $id) {
							continue;
						}
						if ($table == $wpdb->prefix . 'postmeta') {
							if ((strpos($buffer, $url) && strpos($buffer, $id) && (strpos($buffer, '_wp_attached_file')) || strpos($buffer, '_wp_attachment_metadata'))) {
								continue;
							}
							if (strpos($buffer, '_edit_lock') && strpos($buffer, $id)) {
								continue;
							}
						}
						if ($table == $wpdb->prefix . 'options') {
							if (!preg_match("/^[0-9].*/", $buffer, $output_array) || strpos($buffer, 'active_plugins') || strpos($buffer, '_transient_feed_')) {
								continue;
							}
						}
						if ($table == $wpdb->prefix . 'posts') {
							if (strpos($buffer, $url) && substr($buffer, 0, strlen($id)) == $id) {
								continue;
							}
							// skip is post type is revision
							if (preg_match_all("/\s+revision\s+/", $buffer, $output_array)) {
								continue;
							}
						}
						if (preg_match_all($pattern_url, $buffer, $matchedURL)) {
							$matches['url'][$table][$buffer] = $matchedURL;
						}
						if (preg_match_all($pattern_url2, $buffer, $matchedURL)) {
							$matches['url'][$table][$buffer] = $matchedURL;
						}
						if (preg_match_all($pattern_id, $buffer, $matchedID) && !preg_match_all("/[:]" . $id . "[:]/", $buffer, $matche2)) {
							// TODO: Check id within shortcode or not
							$matches['id'][$table][$buffer] = $matchedID;
						}
					}
					fclose($handle);
				}
				//show results:
				$results[$id]['total'] += count($matches);
				if (count($matches)) {
					// usages found for this
					$notfound = false;
					$results[$id]['matches'][] = array($table => $matches);
					
				}
			}
			if ($notfound) {
				// this file has not usage
				$notfounds[$id] = $url;
			} else {
				// this file has usage
			}
		}
		return $notfounds;
	}

	/*
	 * Create a zip file from listed image in $images , $path_included will use for creating a zip file that images address are same as they were on the host
	 */
	public function image_zip_backup($images, $path_included)
	{
		if (!is_array($images)) {
			return;
		}
		//Create a new zip archive class
		$za = new ZipArchive();
		//create a backup.zip file in wp-content/uploads/mighty-mc
		$backupID = 'backup' . time();
		$za->open(MIGHTYFOLDER . '/backups/' . $backupID . '.zip', ZipArchive::CREATE);
		$backupIDFile = fopen(MIGHTYFOLDER . '/backups/' . $backupID . '.txt', "w") or die("Unable to open file!");
		$ids = implode(",", $images);
		fwrite($backupIDFile, $ids);
		fclose($backupIDFile);
		//Loop through each image and each of them to created zip file
		if ($path_included) {
			foreach ($images as $image) {
				$path = get_attached_file($image);
				if ($path != '' && file_exists($path)) {
					$filename = basename($path);
					$abspath = substr($path, strpos($path, "uploads")+8);
					$za->addFile($path, $abspath);
				}
			}
		} else {
			foreach ($images as $image) {
				$path = get_attached_file($image);
				if ($path != '' && file_exists($path)) {
					$filename = basename($path);
					$za->addFile($path, $filename);
				}
			}
		}
	}

	public function backup_all_media()
	{
		//Get array of all media elements
		$media = $this->media_from_media_library();
		if (!is_array($media)) {
			return;
		}
		$ids = array();
		//get id of each media element so we can pass it to image_zip_backup function
		foreach ($media as $id => $path) {
			$ids[] = $id;
		}
		$this->image_zip_backup($ids, true);
	}

	//This function check for mighty-mc folder in wp-content/uploads , this folder will used for creating backup and export files
	private function check_mighty_folder()
	{
		$upload_dir = wp_upload_dir();
		$dirname = $upload_dir['basedir'];
		if (!is_dir($dirname . "/mighty-mc")) {
			@mkdir($dirname . "/mighty-mc", 0777);
			@mkdir($dirname . "/mighty-mc/backups", 0777);
			return $dirname . "/mighty-mc";
		} else {
			return $dirname . "/mighty-mc";
		}
	}
	
	public function assoc_query_2D($sql){
		global $wpdb;
		$result = $wpdb->get_results($sql, ARRAY_A);
		$arr = array();
		foreach($result as $key=>$row)
		{
			$arr[$key] = $row;
		}
		return $arr;
	}
	
	public function query_whole_table($table, $value = '*'){
		$sql = "SELECT $value FROM $table";
		return $this->assoc_query_2D($sql);
	}
}
