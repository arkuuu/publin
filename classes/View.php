<?php

abstract class View {


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
	

	public function viewPageTitle() {
		return 'TITLE MISSING';
	}


	public function viewMetaTags() {
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
