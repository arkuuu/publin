<?php


namespace publin;

class Config {

	const ROOT_PATH = '/publin/';
	const ROOT_URL = 'http://localhost:8888/publin/'; // full url! like oai
	const ADMIN_MAIL = 'admin@localhost.de';
	const PHP_MAIL = 'noreply@localhost.de';
	const TIMEZONE = 'Europe/Berlin'; // see http://php.net/manual/en/timezones.php
	//const DEVELOPMENT_MODE = true;

	const SQL_HOST = 'localhost';
	const SQL_USER = 'root';
	const SQL_PASSWORD = 'root';
	const SQL_DATABASE = 'dev';

	const FILE_PATH = '/Applications/MAMP/uploads/';
	const FILE_NAME_PREFIX = 'publin_';
	const FILE_MAX_SIZE = 200;

	const OAI_REPOSITORY_NAME = 'publin Uni Luebeck';
	const OAI_REPOSITORY_IDENTIFIER = 'localhost.de';
	const OAI_BASE_URL = 'http://localhost:8888/publin/oai/';
	const OAI_ADMIN_EMAIL = 'test@localhost';
	const OAI_RECORDS_PER_REQUEST = 10;
	const OAI_SETS_PER_REQUEST = 10;
	const OAI_RESUMPTION_TOKEN_DAYS_VALID = 1;
	const OAI_USE_XSLT = true;

	public static function setup() {

		date_default_timezone_set(self::TIMEZONE);

		/* needed for utf8-safe string operations */
		mb_internal_encoding('utf8');

		/* treats slashes depending on magic_quotes_gpc, recommended setting is off */
		if (get_magic_quotes_gpc()) {
			function stripslashes_array(array &$array) {

				foreach ($array as $key => $value) {
					$new_key = stripslashes($key);
					if ($new_key != $key) {
						$array[$new_key] = &$value;
						unset($array[$key]);
					}
					if (is_array($value)) {
						stripslashes_array($value);
					}
					else {
						$array[$new_key] = stripslashes($value);
					}
				}
			}

			;
			stripslashes_array($_POST);
			stripslashes_array($_GET);
			stripslashes_array($_REQUEST);
			stripslashes_array($_COOKIE);
		}

		return true;
	}
}

