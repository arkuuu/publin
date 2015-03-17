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

			if (self::getFileExtension($file['tmp_name'])) {
				$file_name = self::generateFileName();
				$success = move_uploaded_file($file['tmp_name'], self::PATH.$file_name);

				if (!$success) {
					throw new FileHandlerException('Error while uploading file to server');
				}

				// TODO: chmod -x
				return $file_name;
			}
			else {
				throw new FileHandlerException('Disallowed file type');
			}
		}
		else if ($file['error'] === UPLOAD_ERR_NO_FILE) {
			throw new FileHandlerException('No file given');
		}
		else if ($file['error'] === UPLOAD_ERR_INI_SIZE
			|| $file['error'] === UPLOAD_ERR_FORM_SIZE
			|| $file['size'] > self::MAX_SIZE
		) {
			throw new FileHandlerException('File is too big');
		}
		else {
			// TODO: change this error message
			throw new FileHandlerException('Unknown error #'.$file['error']);
		}
	}


	private static function getFileExtension($file) {

		$mime_type = self::getMimeType($file);
		$extension = self::getAllowedTypes();
		if (isset($extension[$mime_type])) {
			return $extension[$mime_type];
		}
		else {
			return false;
		}
	}


	private static function getMimeType($file) {

		$file_info = new finfo();
		$mime_type = $file_info->file($file, FILEINFO_MIME_TYPE);

		return $mime_type;
	}


	public static function getAllowedTypes() {

		return array('application/pdf' => '.pdf',
					 'image/png'       => '.png');
	}


	private static function generateFileName() {

		do {
			$file_name = uniqid(self::PREFIX, true);
		}
		while (file_exists(self::PATH.$file_name));

		return $file_name;
	}


	public static function download($file_name, $download_name = 'file') {

		$file_name = pathinfo($file_name, PATHINFO_BASENAME);
		$file = self::PATH.$file_name;

		if (file_exists($file)) {

			header('Content-Type: '.self::getMimeType($file));
			header('Content-Disposition: inline; filename="'.$download_name.self::getFileExtension($file).'"');
			header('Expires: 0'); // TODO: check all this
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: '.filesize($file));
			readfile($file);
			exit;
		}
		else {
			throw new FileHandlerException('file does not exist');
		}
	}


	public static function delete($file_name) {

		$file_name = pathinfo($file_name, PATHINFO_BASENAME);
		$file = self::PATH.$file_name;

		if (file_exists($file)) {
			if (unlink($file)) {
				return true;
			}
			else {
				throw new FileHandlerException('file could not be deleted');
			}
		}
		else {
			throw new FileHandlerException('file does not exist');
		}
	}
}
