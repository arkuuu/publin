<?php

/**
 * Parent class for all views
 *
 * TODO: comment
 */
abstract class View {

	/**
	 * Returns the content of the template file.
	 *
	 * @param	string	$file	The file path
	 *
	 * @return	string
	 */
	public function getContent($file) {

		if (file_exists($file)) {
			
			ob_start();
			include $file;
			$output = ob_get_contents();
			ob_end_clean();

			return $output;
		}
		else {
			return 'Could not find template '.$file.'!';
		}
	}
	

	/**
	 * Shows emtpy page title and must be overwritten by child class.
	 *
	 * @return	string
	 */
	public function showPageTitle() {
		return 'TITLE MISSING';
	}


	/**
	 * Shows empty meta tags and should be overwritten by child class if needed.
	 *
	 * @return	string
	 */
	public function showMetaTags() {
		return "\n";
	}


	// // Could be used to let a template safely call methods of an view.
	// public function view($function, $param = null) {
	// 	$function = 'view'.$function;

	// 	if (is_callable(array($this, $function))) {
	// 		if (isset($param)) {
	// 			return $this -> $function($param);
	// 		}
	// 		else {
	// 			return $this -> $function();
	// 		}
	// 	}
	// 	else {
	// 		return 'method '.$function.'('.$param.') not found in '.get_class($this).'!';
	// 	}
	// }
	
}
