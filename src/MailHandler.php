<?php


namespace publin\src;

class MailHandler {

	public static function sendMail($to, $subject, $message) {

		// TODO headers
		$headers = 'From: '.Config::PHP_MAIL."\r\n".
			'Reply-To: '.Config::PHP_MAIL."\r\n".
			'X-Mailer: PHP/'.phpversion();

		return mail($to, $subject, $message, $headers);
	}
}
