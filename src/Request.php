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


	public function getUrl() {

		return $this->createUrl($this->get);
	}


	public static function createUrl(array $parameters) {

		$delimiter = '&';
		$url = '';

		foreach ($parameters as $key => $value) {
			if (!empty($value)) {
				$url .= $key.'='.$value.$delimiter;
			}
		}
		$url = substr($url, 0, -strlen($delimiter));

		if (empty($url)) {
			return '';
		}
		else {
			return '?'.$url;
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

		if (!empty($name) && !empty($this->get[$name])) {

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
