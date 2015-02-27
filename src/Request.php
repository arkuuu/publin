<?php


namespace publin\src;

class Request {

	public $page;
	public $id;
	public $mode;
	public $by;
	private $post;


	public function __construct() {

		if (!empty($_GET['p'])) {
			$this->page = $_GET['p'];
		}
		else {
			$this->page = false;
		}
		if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
			$this->id = (int)$_GET['id'];
		}
		else {
			$this->id = false;
		}
		if (!empty($_GET['m'])) {
			$this->mode = $_GET['m'];
		}
		else {
			$this->mode = false;
		}
		if (!empty($_GET['by'])) {
			$this->by = $_GET['by'];
		}
		else {
			$this->by = false;
		}
		if (!empty($_POST)) {
			$this->post = $_POST;
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
	public function getPost($name = '') {

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
}
