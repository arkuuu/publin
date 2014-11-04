<?php

class BrowseView extends View {

	private $template;
	private $model;


	public function __construct(BrowseModel $model, $template = 'dev') {

		$this -> model = $model;
		$this -> template = './templates/'.$template.'/browse.html';
	}


	public function getContent() {
		return parent::getContent($this -> template);
	}


	public function viewPageTitle() {
		return 'Browse by '.$this -> viewBrowseType();
	}


	public function viewBrowseType() {

		$string = array(
							'' => '',
							'recent' => 'recent',
							'author' => 'author',
							'key_term' => 'key term',
							'study_field' => 'field of study',
						);

		return $string[$this -> model -> getBrowseType()];	// TODO: Error when not in array!
	}


	public function viewBrowseNum() {
		return $this -> model -> getBrowseNum();
	}


	public function viewBrowseList() {

		$string = '';
		$url = array(
						'author' => './?p=author&amp;id=',
						'recent' => './?p=publication&amp;id=',
						'key_term' => './?p=browse&amp;by=key_term&amp;id=',
						'study_field' => './?p=browse&amp;by=study_field&amp;id=',
					);
		$browse_list = $this -> model -> getBrowseList();

		if (!empty($browse_list)) {
			foreach ($browse_list as $object) {
				$string .= '<li><a href="'.$url[$this -> model -> getBrowseType()].$object -> getId().'">'.$object -> getName().'</a></li>'."\n";
			}
		}
		else {
			$string = '<li><a href="?p=browse&amp;by=recent">Recent</a></li>'."\n"
						.'<li><a href="?p=browse&amp;by=author">Author</a></li>'."\n"
						.'<li><a href="?p=browse&amp;by=study_field">Field of Study</a></li>'."\n"
						.'<li><a href="?p=browse&amp;by=key_term">Key Term</a></li>'."\n";
		}


		return $string;
	}


	public function isBrowseResult() {
		return $this -> model -> isBrowseResult();
	}


	public function viewBrowseResult() {

		$string = '';
		$url = './?p=publication&amp;id=';

		foreach ($this -> model -> getBrowseResult() as $publication) {
			$string .= '<li><a href="'.$url.$publication -> getId().'">'.$publication -> getTitle().'</a> by ';
			foreach ($publication -> getAuthors() as $author) {
				$string .= $author -> getName().', ';
			}
			$string .= $publication -> getMonth().'.'.$publication -> getYear().'</li>'."\n";
		}

		return $string;
	}
	
}
