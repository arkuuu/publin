<?php

class GenericView implements View {

	private $template;


	public function __construct($page, $template = 'dev') {

		$this -> template = './templates/'.$template.'/'.$page.'.html';
	}


	public function display() {
		$file = $this -> template;

		if (file_exists($file)) {
			
			ob_start();
			include $file;
			$output = ob_get_contents();
			ob_end_clean();

			return $output;
		}
		else {
			return 'Could not find template or page';
		}
	}

}
