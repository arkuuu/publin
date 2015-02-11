<?php

namespace publin\src;

use Exception;
use publin\src\exceptions\NotFoundException;

/**
 * Parent class for all views
 *
 * TODO: comment
 */
class View {

	/**
	 * The path to the template file
	 *
	 * @var    string
	 */
	protected $template = 'modern';

	protected $content;


	public function __construct($content) {

		$this->content = $content;
	}


	public function displayContentOnly() {

		$content = './templates/'.$this->template.'/'.$this->content.'.html';

		if (file_exists($content)) {

			ob_start();
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

		$header = './templates/'.$this->template.'/header.html';
		$menu = './templates/'.$this->template.'/menu.html';
		$content = './templates/'.$this->template.'/'.$this->content.'.html';
		$footer = './templates/'.$this->template.'/footer.html';

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
				throw new NotFoundException('Could not find template '.$content.'!');

			}
		}
		else {
			// TODO: error
			throw new Exception('Could not find master template!');

		}
	}


	/**
	 * Shows page title.
	 *
	 * @return    string
	 */
	public function showPageTitle() {

		$string = ucfirst($this->content);    // TODO: doesn't work with non UTF chars
		$string = str_replace('_', ' ', $string);

		return $string;
	}


	/**
	 * Shows empty meta tags and should be overwritten by child class if needed.
	 *
	 * @return    string
	 */
	public function showMetaTags() {
		return "\n";
	}


	public function showUserName() {

		if (isset($_SESSION['user'])) {
			return $_SESSION['user']->getName();
		}
		else {
			return false;
		}
	}


	public function checkUserPermission($permission) {

		if (isset($_SESSION['user']) && $_SESSION['user']->hasPermission($permission)) {
			return true;
		}
		else {
			return false;
		}
	}


}
