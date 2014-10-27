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

		switch ($this -> model -> getBrowseType()) {
			case 'author':
				return 'author';
				break;

			case 'key_term':
				return 'key term';
				break;

			case 'study_field':
				return 'field of study';
				break;

			case 'year':
				return 'year';
				break;
			default:
				# code...
				break;
		}
	}


	private function viewBrowseNum() {
		return $this -> model -> getBrowseNum();
	}


	private function viewBrowseList() {
		$list_string = '';


		if ($this -> model -> getBrowseType() == 'author') {
			$url = './autpage.php?id=';

			foreach ($this -> model -> getBrowseList() as $author) {
				$list_string .= '<li><a href="'.$url.$author -> getId().'">'.$author -> getName().'</a></li>';
			}
		}
		else if ($this -> model -> getBrowseType() == 'key_term') {
			$url = './browsepage.php?by=key_term&id=';

			foreach ($this -> model -> getBrowseList() as $key_term) {
				$list_string .= '<li><a href="'.$url.$key_term['id'].'">'.$key_term['name'].'</a></li>';
			}
		}
		else if ($this -> model -> getBrowseType() == 'study_field') {
			$url = './browsepage.php?by=study_field&id=';

			foreach ($this -> model -> getBrowseList() as $study_field) {
				$list_string .= '<li><a href="'.$url.$study_field['id'].'">'.$study_field['name'].'</a></li>';
			}
		}
		// else if ($this -> model -> getBrowseType() == 'year') {
		// 	$url = './browsepage.php?by=year:month&id=';

		// 	foreach ($this -> model -> getBrowseList() as $year) {
		// 		$list_string .= '<li><a href="'.$url.$year['year'].'">'.$year['year'].'</a></li>';
		// 	}
		// }
		// else if ($this -> model -> getBrowseType() == 'year:month') {
		// 	$url = './browsepage.php?by=year:month&id=';

		// 	foreach ($this -> model -> getBrowseList() as $key => $value) {
		// 		$list_string .= '<li><a href="'.$url.$value.'">'.$value.'</a></li>';
		// 	}
		// }


		return $list_string;
	}


	private function isBrowseResult() {
		return $this -> model -> isBrowseResult();
	}


	private function viewBrowseResult() {
		$result_string = '';
		$url = './publpage.php?id=';

		foreach ($this -> model -> getBrowseResult() as $publication) {
			$result_string .= '<li><a href="'.$url.$publication -> getId().'">'.$publication -> getTitle().'</a> by ';
			foreach ($publication -> getAuthors() as $author) {
				$result_string .= $author -> getName().', ';
			}
			$result_string .= $publication -> getMonth().'.'.$publication -> getYear().'</li>'."\n";
		}

		return $result_string;
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