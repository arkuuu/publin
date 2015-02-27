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
		if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
			$this->id = (int)$_GET['id'];
		}
		if (!empty($_GET['m'])) {
			$this->mode = $_GET['m'];
		}
		if (!empty($_GET['by'])) {
			$this->by = $_GET['by'];
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


	public function getPost($name = '') {

		if (!empty($name) && isset($this->post[$name])) {

			// TODO: maybe trim?
			return $this->post[$name];
		}
		else if (!empty($name)) {
			// TODO exception here?
			return '';
		}
		else {
			return $this->post;
		}
	}
}
