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
	protected $template;

	protected $content;



	protected function __construct($content, $template) {

		$this -> content = $content;
		$this -> template = $template;
	}


	/**
	 * Returns the content of the page.
	 *
	 * @return	string
	 */
	public function display() {
		$header = './templates/'.$this -> template.'/header.html';
		$menu = './templates/'.$this -> template.'/menu.html';
		$content = './templates/'.$this -> template.'/'.$this -> content.'.html';
		$footer = './templates/'.$this -> template.'/footer.html';

		if (file_exists($header) && file_exists($menu) && file_exists($footer)) {
			if (file_exists($content)) {
				
				ob_start();			
				include $header;
				include $menu;
				include $content;
				include $footer;
				$output = ob_get_contents();
				ob_end_clean();

				return $output;
			}
			else {
				// TODO: error
				return 'Could not find template '.$content.'!';
			}
		}
		else {
			// TODO: error
			return 'Could not find master template!';
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
