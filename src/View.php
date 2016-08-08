<?php

namespace publin\src;

use Exception;
use publin\src\exceptions\NotFoundException;
use publin\config\Config;

/**
 * Class View
 *
 * @package publin\src
 */
class View {

	/**
	 * The path to the template file
	 *
	 * @var    string
	 */
	protected $template = 'modern';

	protected $content;

	protected $errors;


	/**
	 * @param       $content
	 * @param array $errors
	 */
	public function __construct($content, $errors = array()) {

		$this->content = $content;
		$this->errors = $errors;
	}


	/**
	 * @return string
	 * @throws NotFoundException
	 */
	public function displayContentOnly() {

		$content = './templates/'.$this->template.'/'.$this->content.'.html';

		if (file_exists($content)) {

			ob_start();
			/** @noinspection PhpIncludeInspection */
			include $content;
			$output = ob_get_contents();
			ob_end_clean();

			return $output;
		}
		else {
			throw new NotFoundException('Could not find template '.$content.'!');
		}
	}


	/**
	 * Returns the content of the page.
	 *
	 * @return string
	 * @throws Exception
	 */
	public function display() {

		header('Content-Type: text/html; charset=UTF-8');

		$header = './templates/'.$this->template.'/header.html';
		$menu = './templates/'.$this->template.'/menu.html';
		$content = './templates/'.$this->template.'/'.$this->content.'.html';
		$footer = './templates/'.$this->template.'/footer.html';

		if (file_exists($header) && file_exists($menu) && file_exists($footer)) {
			if (file_exists($content)) {

				ob_start();
				/** @noinspection PhpIncludeInspection */
				include $header;
				/** @noinspection PhpIncludeInspection */
				include $menu;
				/** @noinspection PhpIncludeInspection */
				include $content;
				/** @noinspection PhpIncludeInspection */
				include $footer;
				$output = ob_get_contents();
				ob_end_clean();

				return $output;
			}
			else {
				throw new NotFoundException('Could not find template '.$content.'!');
			}
		}
		else {
			throw new Exception('Could not find master template!');
		}
	}


	/**
	 * Shows page title.
	 *
	 * @return    string
	 */
	public function showPageTitle() {

		$string = mb_convert_case($this->content, MB_CASE_TITLE, 'UTF-8');
		$string = str_replace('_', ' ', $string);

		return $this->html($string);
	}

	/**
	 * Returns the root URL of the application
	 * specified in the Config class. This method
	 * can be used to generate absolute URLs in
	 * the templates.
	 *
	 * @return string
	 */
	public function getRootUrl() {

	    return (Config::USE_SSL ? Config::ROOT_URL_SSL : Config::ROOT_URL);
	}

	/**
	 * @param $input
	 *
	 * @return string
	 */
	public function html($input) {

		return htmlspecialchars($input);
	}


	/**
	 * Shows empty meta tags and should be overwritten by child class if needed.
	 *
	 * @return    string
	 */
	public function showMetaTags() {

		return "\n";
	}


	/**
	 * @return bool|string
	 */
	public function showUserName() {

		if (isset($_SESSION['user'])) {
			/* @var $user User */
			$user = $_SESSION['user'];

			return $this->html($user->getName());
		}
		else {
			return false;
		}
	}


	/**
	 * @param $permission_name
	 *
	 * @return bool
	 */
	public function hasPermission($permission_name) {

		if (isset($_SESSION['user'])) {
			/* @var $user User */
			$user = $_SESSION['user'];
			if ($user->hasPermission($permission_name)) {
				return true;
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}


	/**
	 * @param $page
	 * @param $title
	 *
	 * @return string
	 */
	public function showLink($page, $title) {

		$url = Request::createUrl(array('p' => $page));

		if ($this->content == $page) {
			return '<a href="'.$this->html($url).'" class="active">'.$this->html($title).'</a>';
		}
		else {
			return '<a href="'.$this->html($url).'">'.$this->html($title).'</a>';
		}
	}


	/**
	 * @param Publication $publication
	 * @param int         $max_authors
	 *
	 * @return string
	 */
	public function showCitation(Publication $publication, $max_authors = 6) {

		$citation = '<div class="citation">';

		/* shows the title and links to the publication page */
		$url = Request::createUrl(array('p' => 'publication', 'id' => $publication->getId()));
		$citation .= '<a href="'.$this->html($url).'" class="title">'.$this->html($publication->getTitle()).'</a><br/>';

		/* creates list of authors */
		$authors = $publication->getAuthors();
		$num = count($authors);
		$authors_string = '';
		if ($num > 0) {
			$i = 1;
			foreach ($authors as $author) {
				if ($i == 1) {
					/* first author */
					$authors_string .= $author->getFirstName(true).' '
						.$author->getLastName();
				}
				else if ($i > $max_authors) {
					/* break with "et al." if too many authors */
					$authors_string .= ' et al.';
					break;
				}
				else if ($i == $num) {
					/* last author */
					$authors_string .= ' and '.$author->getFirstName(true).' '
						.$author->getLastName();
				}
				else {
					/* all other authors */
					$authors_string .= ', '.$author->getFirstName(true).' '
						.$author->getLastName();
				}
				$i++;
			}
		}

		/* shows list of authors */
		$citation .= '<span class="authors">'.$this->html($authors_string).'</span>';

		/* appends publish date behind the authors */
		$citation .= ' <span class="year">('.$this->html($publication->getDatePublished('Y')).')</span>';

		/* shows journal or booktitle and additional data*/
		if ($publication->getJournal()) {
			$citation .= '<br/><span class="journal">'.$this->html($publication->getJournal()).'</span>';

			if ($publication->getVolume()) {
				$citation .= ', <span class="volume">'.$this->html($publication->getVolume()).'</span>';

				if ($publication->getNumber()) {
					$citation .= ' <span class="number">('.$this->html($publication->getNumber()).')</span>';
				}
			}
			if ($publication->getPages('-')) {
				$citation .= ', <span class="pages">'.$this->html($publication->getPages('-')).'</span>';
			}
		}
		else if ($publication->getBooktitle()) {
			$citation .= '<br/>In: <span class="booktitle">'.$this->html($publication->getBooktitle()).'</span>';

			if ($publication->getPages('-')) {
				$citation .= ', <span class="pages">'.$this->html($publication->getPages('-')).'</span>';
			}
		}

		$links = array();
		/* shows full text link */
		if ($file = $publication->getFullTextFile()) {
			$title = $file->getExtension() == '.pdf' ? 'PDF' : 'FULLTEXT';
			$url = Request::createUrl(array('p' => 'publication', 'id' => $publication->getId(), 'file_id' => $file->getId()));
			$links[] = '<a href="'.$this->html($url).'" target="_blank">'.$this->html($title).'</a>';
		}
		/* shows DOI link */
		if ($publication->getDoi()) {
			$links[] = '<a href="http://dx.doi.org/'.$this->html($publication->getDoi()).'" target="_blank">DOI</a>';
		}
		/* shows URLs */
		if ($publication->getUrls()) {
			foreach ($publication->getUrls() as $url) {
				$links[] = '<a href="'.$this->html($url->getUrl()).'" target="_blank">'.$this->html($url->getName()).'</a>';
			}
		}

		/* shows links if there are any */
		if (!empty($links)) {
			$citation .= '<br/><span class="links">'.implode(' - ', $links).'</span>';
		}

		return $citation.'</div>';
	}


	/**
	 * @return bool|string
	 */
	public function showErrors() {

		if (!empty($this->errors)) {
			$string = '<div class="error">
	<!--<span class="message">Errors occurred while processing your request!</span>-->
	<ul>';
			foreach ($this->errors as $error) {
				$string .= '<li>'.$this->html($error).'</li>';
			}

			return $string.'</ul></div>';
		}
		else {
			return false;
		}
	}
}
