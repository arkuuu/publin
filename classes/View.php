<?php

/**
 * Parent class for all views
 *
 * TODO: comment
 */
abstract class View {

	/**
	 * The path to the template file
	 * @var	string
	 */
	protected $template_file;



	public function __construct($template_file) {
		$this -> template_file = './templates/'.$template_file;
	}


	/**
	 * Returns the content of the template file.
	 *
	 * @return	string
	 */
	public function getContent() {

		if (isset($this -> template_file)) {
			if (file_exists($this -> template_file)) {
				
				ob_start();
				include $this -> template_file;
				$output = ob_get_contents();
				ob_end_clean();

				return $output;
			}
			else {
				// TODO: error
				return 'Could not find template '.$this -> template_file.'!';
			}
		}
		else {
			// TODO: error
			return 'No template was set!';
		}


	}
	

	/**
	 * Shows page title.
	 *
	 * @return	string
	 */
	abstract public function showPageTitle();


	/**
	 * Shows empty meta tags and should be overwritten by child class if needed.
	 *
	 * @return	string
	 */
	public function showMetaTags() {	// TODO: make abstract
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
