<?php


namespace publin\src;

use publin\config\Config;

/**
 * Class MailHandler
 *
 * @package publin\src
 */
class MailHandler {

	/**
	 * @param $to
	 * @param $subject
	 * @param $message
	 *
	 * @return bool
	 */
	public static function sendMail($to, $subject, $message) {

		// TODO headers
		$headers = 'From: '.Config::PHP_MAIL."\r\n".
			'Reply-To: '.Config::PHP_MAIL."\r\n".
			'X-Mailer: PHP/'.phpversion();

		return mail($to, $subject, $message, $headers);
	}
}
