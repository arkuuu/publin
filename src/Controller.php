<?php


namespace publin\src;

/**
 * Class Controller
 *
 * @package publin\src
 */
class Controller {

	/**
	 * @param $destination
	 */
	public static function redirect($destination) {

		if (!isset($_SESSION)) {
			session_start();
		}
		$_SESSION['referrer'] = Request::getUrl();

		header('Location: '.$destination, true);
		exit();
	}
}
