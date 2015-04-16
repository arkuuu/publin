<?php


namespace publin\src;

use publin\Config;

/**
 * Class Request
 *
 * @package publin\src
 */
class Request {


	/**
	 *
	 */
	public function __construct() {
	}


	/**
	 * @return string
	 */
	public static function getUrl() {

		return self::createUrl($_GET);
	}


	/**
	 * @param array $parameters
	 *
	 * @return string
	 */
	public static function createUrl(array $parameters = array()) {

		$url = http_build_query($parameters);

		if (!$url) {
			return Config::ROOT_URL;
		}
		else {
			return Config::ROOT_URL.'?'.$url;
		}
	}


	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public static function post($name = '') {

		if (!empty($name) && isset($_POST[$name])) {

			return $_POST[$name];
		}
		else if (empty($name) && !empty($_POST)) {
			return $_POST;
		}
		else {
			return false;
		}
	}


	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public static function get($name = '') {

		if (!empty($name) && !empty($_GET[$name])) {

			return $_GET[$name];
		}
		else if (empty($name) && !empty($_GET)) {
			return $_GET;
		}
		else {
			return false;
		}
	}
}
