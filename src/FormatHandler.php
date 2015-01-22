<?php

/**
 * Handles the import and export formats.
 *
 * This should later be replaced by a modular system looking for export formats
 * found in the /export directory.
 *
 * TODO: comment
 * TODO: implement
 */
class FormatHandler {

	private $module;


	
	public function __construct($format) {
		$this -> module = $format;

		$file = './modules/'.$this -> module.'.php';

		if (!file_exists($file)) {
			throw new Exception('file '.$file.' not found');				
		}

		include $file;

		if (!class_exists($this -> module)) {
			throw new Exception('module '.$this -> module.' not found in file '.$file);
		}
	}


	/**
	 * Returns an array with all supported formats.
	 *
	 * @return	array
	 */
	public function getFormats() {
		// TODO
	}
	

	/**
	 * TODO: comment
	 *
	 * @param	Publication		$publication	The publication
	 * @param	string			$format			The format
	 *
	 * @return	string
	 */	
	public function export($data) {

		if (!method_exists($this -> module, 'export')) {
			throw new Exception('module '.$this -> module.' offers no export');
		}

		$module = $this -> module;
		return $module::export($data);


	}	

}
