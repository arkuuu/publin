<?php


namespace publin\src;

class Request {

	private $get;
	private $post;


	public function __construct() {

		if (!empty($_GET)) {
			$this->get = $_GET;
		}
		if (!empty($_POST)) {
			$this->post = $_POST;
		}
	}


	public static function getUrl() {

		return self::createUrl($_GET);
	}


	public static function createUrl(array $get) {

		$baseUrl = '/';
		$delimiter = '&amp;';

		$parameters = '';
		foreach ($get as $key => $value) {
			$parameters .= $key.'='.$value.$delimiter;
		}
		$parameters = substr($parameters, 0, -strlen($delimiter));

		if (empty($parameters)) {
			return $baseUrl;
		}
		else {
			return $baseUrl.'?'.$parameters;
		}
	}


	public function isPost() {

		// TODO: this will not work when both GET and POST is requested
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			return true;
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
	public function post($name = '') {

		if (!empty($name) && isset($this->post[$name])) {

			// TODO: maybe trim?
			return $this->post[$name];
		}
		else if (empty($name) && !empty($this->post)) {
			return $this->post;
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
	public function get($name = '') {

		if (!empty($name) && isset($this->get[$name])) {

			// TODO: maybe trim?
			return $this->get[$name];
		}
		else if (empty($name) && !empty($this->get)) {
			return $this->get;
		}
		else {
			return false;
		}
	}
}
