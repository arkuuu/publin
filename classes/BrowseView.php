<?php

/**
 * View for browse page
 *
 * TODO: comment
 */
class BrowseView extends View {

	/**
	 * @var	BrowseModel
	 */
	private $model;

	/**
	 * The path to the template file
	 * @var	string
	 */
	private $template;



	/**
	 * Constructs the browse view.
	 *
	 * @param	BrowseModel		$model		The browse model
	 * @param	string			$template	The template folder
	 */
	public function __construct(BrowseModel $model, $template = 'dev') {

		$this -> model = $model;
		$this -> template = './templates/'.$template.'/browse.html';
	}


	/**
	 * Returns the content of the template file using parent method.
	 *
	 * @return	string
	 */
	public function getContent() {
		return parent::getContent($this -> template);
	}


	/**
	 * Shows the page title.
	 *
	 * @return	string
	 */
	public function showPageTitle() {

		return $this -> showBrowseType();
	}


	/**
	 * Shows the browse.
	 *
	 * @return	string
	 */
	public function showBrowseType() {

		if ($this -> model -> getBrowseType() == 'recent') {
			return 'Recently added publications';
		}
		else {
			$string = array(
								'' => '',
								'recent' => 'recent',
								'author' => 'author',
								'key_term' => 'key term',
								'study_field' => 'field of study',
								'type' => 'type',
							);

			return 'Browse by '.$string[$this -> model -> getBrowseType()];
			// TODO: Error when not in array!
		}
	}

	/**
	 * Shows the number of found entries.
	 *
	 * @return	int
	 */
	public function showBrowseNum() {
		return $this -> model -> getBrowseNum();
	}


	/**
	 * Shows the browse list.
	 *
	 * @return	string
	 */
	public function showBrowseList() {

		$string = '';
		$url = array(
						'author' => './?p=author&amp;id=',
						'recent' => './?p=publication&amp;id=',
						'key_term' => './?p=browse&amp;by=key_term&amp;id=',
						'study_field' => './?p=browse&amp;by=study_field&amp;id=',
						'type' => './?p=browse&amp;by=type&amp;id=',
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
						.'<li><a href="?p=browse&amp;by=key_term">Key Term</a></li>'."\n"
						.'<li><a href="?p=browse&amp;by=type">Type</a></li>'."\n";
		}

		return $string;
	}


	/**
	 * Returns true if there is a browse result.
	 *
	 * This is used to determine whether the result list or the browse list should be shown,
	 * so this returns true even if the browse result is empty.
	 *
	 * @return	boolean
	 */
	public function isBrowseResult() {
		return $this -> model -> isBrowseResult();
	}


	/**
	 * Shows the browse results.
	 *
	 * @return	string
	 */
	public function showBrowseResult($style = 'apa') {

		$string = '';
		$url = './?p=publication&amp;id=';

		foreach ($this -> model -> getBrowseResult() as $publication) {
			$string .= '<li>'.Citation::getCitation($publication, $style)
					.' - <a href="'.$url.$publication -> getId().'">show</a></li>'."\n";
		}

		return $string;
	}
	
}
