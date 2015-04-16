<?php


namespace publin\src;

/**
 * Class SearchView
 *
 * @package publin\src
 */
class SearchView extends View {

	private $results;


	/**
	 * @param array $results
	 * @param array $errors
	 */
	public function __construct(array $results, array $errors = array()) {

		parent::__construct('search', $errors);
		$this->results = $results;
	}


	/**
	 * @return string
	 */
	public function showTitle() {

		return 'Search';
	}


	/**
	 * @return bool|string
	 */
	public function showResults() {

		$string = '';
		$author_url = './?p=author&id=';
		if (!empty($this->results)) {
			foreach ($this->results as $result) {
				if ($result instanceof Publication) {
					$string .= '<li>'.$this->showCitation($result).'</li>';
				}
				else if ($result instanceof Author) {
					$string .= '<li><a href="'.$this->html($author_url.$result->getId()).'">'.$this->html($result->getLastName().', '.$result->getFirstName()).'</a></li>';
				}
			}

			return '<ul>'.$string.'</ul>';
		}
		else {
			return false;
		}
	}
}
