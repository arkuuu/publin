<?php


namespace publin\src;

class Request {


	public function __construct() {
	}


	public static function getUrl() {

		return self::createUrl($_GET);
	}


	public static function createUrl(array $parameters) {

		$url = http_build_query($parameters);
		if (empty($url)) {
			return Config::ROOT_URL;
		}
		else {
			return Config::ROOT_URL.'?'.$url;
		}
	}


	/**
	 * @param string $name
	 *
	 * @return bool|array|string
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
	 * @return bool|array|string
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
