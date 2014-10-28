<?php

class BrowseView implements View {

	private $template = './templates/browse.html';
	private $model;


	public function __construct(BrowseModel $model, $template = 'dev') {

		$this -> template = './templates/'.$template.'/browse.html';
		$this -> model = $model;
	}


	private function viewPageTitle() {
		return 'publin | Browse by '.$this -> viewBrowseType();
	}


	private function viewBrowseType() {

		$string = array(
							'author' => 'author',
							'key_term' => 'key term',
							'study_field' => 'field of study',
						);

		return $string[$this -> model -> getBrowseType()];
	}


	private function viewBrowseNum() {
		return $this -> model -> getBrowseNum();
	}


	private function viewBrowseList() {
		$string = '';
		$url = array(
						'author' => './autpage.php?id=',
						'key_term' => './browsepage.php?by=key_term&amp;id=',
						'study_field' => './browsepage.php?by=study_field&amp;id=',
					);

		foreach ($this -> model -> getBrowseList() as $object) {
			$string .= '<li><a href="'.$url[$this -> model -> getBrowseType()].$object -> getId().'">'.$object -> getName().'</a></li>';
		}

		return $string;
	}


	private function isBrowseResult() {
		return $this -> model -> isBrowseResult();
	}


	private function viewBrowseResult() {
		$string = '';
		$url = './publpage.php?id=';

		foreach ($this -> model -> getBrowseResult() as $publication) {
			$string .= '<li><a href="'.$url.$publication -> getId().'">'.$publication -> getTitle().'</a> by ';
			foreach ($publication -> getAuthors() as $author) {
				$string .= $author -> getName().', ';
			}
			$string .= $publication -> getMonth().'.'.$publication -> getYear().'</li>'."\n";
		}

		return $string;
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
			return 'Could not find template';
		}
	}
	
}
