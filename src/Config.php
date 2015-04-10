<?php


namespace publin\src;

class Config {

	const ROOT_PATH = '/publin/';
	const ROOT_URL = '/publin/';
	const ADMIN_MAIL = 'test@localhost';
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
	const OAI_REPOSITORY_IDENTIFIER = 'de.localhost';
	const OAI_BASE_URL = 'http://localhost/publin/oai/';
	const OAI_ADMIN_EMAIL = 'test@localhost';
	const OAI_RECORDS_PER_REQUEST = 10;
	const OAI_SETS_PER_REQUEST = 10;
	const OAI_RESUMPTION_TOKEN_DAYS_VALID = 2;
	const OAI_USE_XSLT = true;


	//const DOI_URL = 'http://dx.doi.org/';

	public static function setup() {

		date_default_timezone_set(self::TIMEZONE);
		mb_internal_encoding('utf8');

		return true;
	}
}

