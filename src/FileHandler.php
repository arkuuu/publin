<?php


namespace publin\src;

use finfo;
use InvalidArgumentException;
use publin\src\exceptions\FileHandlerException;

class FileHandler {

	const PATH = '/Applications/MAMP/uploads/'; // set this to somewhere outside the web-accessible folders
	const PREFIX = 'publin_'; // file prefix (optional)
	const MAX_SIZE = 200; // check php.ini for max file upload, too TODO: not working!


	public static function upload(array $file) {

		if (!(isset($file['name'])
			&& isset($file['type'])
			&& isset($file['tmp_name'])
			&& isset($file['error'])
			&& isset($file['size']))
		) {
			throw new InvalidArgumentException('no valid file upload');
		}

		if ($file['error'] === UPLOAD_ERR_OK) {

			$file_info = new finfo();
			$mime_type = $file_info->file($file['tmp_name'], FILEINFO_MIME_TYPE);

			if (self::getFileExtension($mime_type)) {
				$file_name = self::generateFileName();
				$success = move_uploaded_file($file['tmp_name'], self::PATH.$file_name);

				if (!$success) {
					throw new FileHandlerException('error while uploading file');
				}

				// TODO: chmod -x
				return $file_name;
			}
			else {
				throw new FileHandlerException('disallowed file type');
			}
		}
		else if ($file['error'] === UPLOAD_ERR_NO_FILE) {
			return false;
		}
		else if ($file['error'] === UPLOAD_ERR_INI_SIZE
			|| $file['error'] === UPLOAD_ERR_FORM_SIZE
			|| $file['size'] > self::MAX_SIZE
		) {
			throw new FileHandlerException('file is too big, error '.$file['error']);
		}
		else {
			throw new FileHandlerException('error '.$file['error']);
		}
	}


	private static function getFileExtension($mime_type) {

		$allowed = self::getAllowedFiles();

		if (isset($allowed[$mime_type])) {
			return $allowed[$mime_type];
		}
		else {
			return false;
		}
	}


	public static function getAllowedFiles() {

		return array('application/pdf' => '.pdf');
	}


	private static function generateFileName() {

		do {
			$file_name = uniqid(self::PREFIX, true);
		}
		while (file_exists(self::PATH.$file_name));

		return $file_name;
	}


	public static function download($file_name) {

		$file = self::PATH.$file_name;
		if (file_exists($file)) {
			// TODO
			return $file;
		}
		else {
			return false;
		}
	}


	public static function delete($file_name) {
		// deletes a file
	}
}
