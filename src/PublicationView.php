<?php

namespace publin\src;

use Exception;

/**
 * View for publication page
 *
 * TODO: comment
 */
class PublicationView extends View {

	/**
	 * @var    Publication
	 */
	private $publication;
	/**
	 * @var bool
	 */
	private $edit_mode;


	/**
	 * Constructs the publication view.
	 *
	 * @param Publication $publication
	 * @param bool        $edit_mode
	 */
	public function __construct(Publication $publication, $edit_mode = false) {

		parent::__construct('publication');
		$this->publication = $publication;
		$this->edit_mode = $edit_mode;
	}


	public function isEditMode() {

		return $this->edit_mode;
	}


	// TODO: error when used for previewing a submitted publication
	public function showLinkToSelf($mode = '') {

		$url = '?p=publication&amp;id=';
		$mode_url = '&amp;m='.$mode;
		//$url = Request::createUrl(array('p' => 'publication', 'm' => $mode, 'id' => $this->publication->getId()));
		//return $url;

		if (empty($mode)) {
			return $url.$this->publication->getId();
		}
		else {
			return $url.$this->publication->getId().$mode_url;
		}
	}


	/**
	 * Shows the page title.
	 *
	 * @return    string
	 */
	public function showPageTitle() {

		return $this->showTitle();
	}


	/**
	 * Shows the publication's title.
	 *
	 * @return    string
	 */
	public function showTitle() {

		return $this->publication->getTitle();
	}


	/**
	 * @return string
	 */
	public function showMetaTags() {

		$formats = array('HighwirePressTags', 'DublinCoreTags', 'PRISMTags');
		$result = '';

		foreach ($formats as $format) {
			$result .= FormatHandler::export($this->publication, $format);
		}

		return $result;
	}


	/**
	 * Shows the publication's authors.
	 *
	 * @return string
	 * @throws Exception
	 */
	public function showAuthors() {

		$result = false;
		$authors = $this->publication->getAuthors();
		$num = count($authors);

		if ($num > 0) {
			$i = 1;
			foreach ($authors as $author) {

				$url = '?p=author&amp;id=';
				$author_id = $author->getId();
				$author_name = $author->getName();

				if ($author_id && $author_name) {
					$author = '<a href="'.$url.$author_id.'">'.$author_name.'</a>';
				}
				else if ($author_name) {
					$author = $author_name;
				}
				else {
					$author = 'Unknown Author';
				}

				if ($i == 1) {
					/* first author */
					$result .= $author;
				}
				else if ($i == $num) {
					/* last author */
					$result .= ' and '.$author;
				}
				else {
					/* all other authors */
					$result .= ', '.$author;
				}
				$i++;
			}
		}

		return $result;
	}


	/**
	 * Shows the publication's publish date.
	 *
	 * @param string $format
	 *
	 * @return string
	 */
	public function showDatePublished($format = 'F Y') {

		return $this->publication->getDatePublished($format);
	}


	/**
	 * Shows the publication's type.
	 *
	 * @return    string
	 */
	public function showType() {

		$url = '?p=browse&amp;by=type&amp;id=';
		$type_name = $this->publication->getTypeName();
		$type_id = $this->publication->getTypeId();

		if ($type_id && $type_name) {
			return '<a href="'.$url.$type_id.'">'.$type_name.'</a>';
		}
		else if ($type_name) {
			return $type_name;
		}
		else {
			return false;
		}
	}


	/**
	 * @param string $divider
	 *
	 * @return string
	 */
	public function showPages($divider = '-') {

		return $this->publication->getPages($divider);
	}


	/**
	 * @return string
	 */
	public function showSchool() {

		return $this->publication->getSchool();
	}


	/**
	 * @return string
	 */
	public function showAddress() {

		return $this->publication->getAddress();
	}


	/**
	 * @return string
	 */
	public function showDoi() {

		return $this->publication->getDoi();
	}


	/**
	 * @return string
	 */
	public function showIsbn() {

		return $this->publication->getIsbn();
	}


	/**
	 * @return string
	 */
	public function showNote() {

		return $this->publication->getNote();
	}


	/**
	 * Shows the publication's abstract.
	 *
	 * @return    string
	 */
	public function showAbstract() {

		return $this->publication->getAbstract();
	}


	/**
	 * Shows the publication's references.
	 *
	 * TODO: implement
	 *
	 * @return    string
	 */
	public function showReferences() {

		return false;
	}


	/**
	 * @param string $separator
	 *
	 * @return bool|string
	 */
	public function showKeywords($separator = ', ') {

		$keywords = $this->publication->getKeywords();

		if (!empty($keywords)) {

			$string = '';
			$url = '?p=keyword&amp;id=';

			foreach ($keywords as $keyword) {

				$keyword_id = $keyword->getId();
				$keyword_name = $keyword->getName();

				if ($keyword_id && $keyword_name) {
					$string .= '<a href="'.$url.$keyword_id.'">'.$keyword_name.'</a>'.$separator;
				}
				else if ($keyword_name) {
					$string .= $keyword_name.$separator;
				}
			}

			return substr($string, 0, -(strlen($separator)));
		}
		else {
			return false;
		}
	}


	public function showEditKeywords() {

		$keywords = $this->publication->getKeywords();
		$string = '';

		foreach ($keywords as $keyword) {
			$string .= '<li>
						<form action="#" method="post" accept-charset="utf-8">
						'.$keyword->getName().'
						<input type="hidden" name="keyword_id" value="'.$keyword->getId().'"/>
						<input type="hidden" name="action" value="removeKeyword"/>
						<input type="submit" value="x"/>
						</form>
						</li>';
		}

		$string .= '<li><form action="#" method="post" accept-charset="utf-8">
					<input name="name" type="text" placeholder="Keyword"/>
					<input type="hidden" name="action" value="addKeyword"/>
					<input type="submit" value="Add"/>
					</form></li>';

		return $string;
	}


	public function showEditAuthors() {

		$authors = $this->publication->getAuthors();
		$string = '';

		foreach ($authors as $author) {
			$string .= '<li>
						<form action="#" method="post" accept-charset="utf-8">
						'.$author->getName().'
						<input type="hidden" name="author_id" value="'.$author->getId().'"/>
						<input type="hidden" name="action" value="removeAuthor"/>
						<input type="submit" value="x"/>
						</form>
						</li>';
		}

		$string .= '<li>
					<form action="#" method="post" accept-charset="utf-8">
					<input type="text" name="given" placeholder="Given Name(s)" />
					<input type="text" name="family" placeholder="Family Name" />
					<input type="hidden" name="action" value="addAuthor"/>
					<input type="submit" value="Add"/>
					</form>
					</li>';

		return $string;
	}


	/**
	 * Shows links to other bibliographic indexes for this publication.
	 *
	 * @return    string
	 */
	public function showBibLinks() {

		$string = '';

		foreach (BibLink::getServices() as $service) {
			$url = BibLink::getPublicationsLink($this->publication, $service);
			$string .= '<li><a href="'.$url.'" target="_blank">'.$service.'</a></li>';
		}

		return $string;
	}


	/**
	 * Shows links to export formats.
	 *
	 * @param    string $format The export format
	 *
	 * @return    string
	 */
	public function showExport($format) {

		return FormatHandler::export($this->publication, $format);
	}


	public function showEditForm() {

		$type = $this->publication->getTypeName();

		$string = '<form action="#" method="post" accept-charset="utf-8">
	<label for="date_published">Publication Date:</label>
	<input type="text" name="date_published" id="date_published" placeholder="YYYY-MM-DD"
		   value="'.$this->publication->getDatePublished('Y-m-d').'"/><br/>
	<label for="title">Title:</label>
	<input type="text" name="title" id="title" maxlength="200" value="'.$this->publication->getTitle().'"/><br/>';

		if ($type == 'article') {
			$string .= '<label for="journal">Journal:</label>
	<input type="text" name="journal" id="journal" maxlength="200"
		   value="'.$this->publication->getJournal().'"/><br/>';
		}

		if ($type == 'incollection' || $type == 'inproceedings') {
			$string .= '<label for="booktitle">Book Title:</label>
	<input type="text" name="booktitle" id="booktitle" maxlength="200"
		   value="'.$this->publication->getBooktitle().'"/><br/>
	<label for="pages_from">Pages:</label>
	<input type="number" name="pages_from" id="pages_from" min="0" placeholder="12"
		   value="'.$this->publication->getFirstPage().'"/>
	<input type="number" name="pages_to" id="pages_to" min="0" placeholder="23"
		   value="'.$this->publication->getLastPage().'"/><br/>';
		}

		if (in_array($type, array('article', 'book', 'incollection', 'inproceedings'))) {
			$string .= '<label for="publisher">Publisher:</label>
	<input type="text" name="publisher" id="publisher" maxlength="200"
		   value="'.$this->publication->getPublisher().'"/><br/>';
		}

		if ($type == 'book') {
			$string .= '<label for="edition">Edition:</label>
	<input type="text" name="edition" id="edition" value="'.$this->publication->getEdition().'"/>
	<br/><label for="series">Series:</label>
	<input type="number" name="series" id="series" min="0"
		   value="'.$this->publication->getSeries().'"/><br/>';
		}

		if (in_array($type, array('masterthesis', 'phdthesis', 'techreport'))) {
			$string .= '<label for="institution">Institution:</label>
	<input type="text" name="institution" id="institution" maxlength="200"
		   value="'.$this->publication->getInstitution().'"/><br/>';
		}

		if ($type == 'misc') {
			$string .= '<label for="howpublished">How published:</label>
	<input type="text" name="howpublished" id="howpublished" maxlength="200"
		   value="'.$this->publication->getHowpublished().'"/><br/>';
		}

		if ($type == 'article' || $type == 'book') {
			$string .= '<label for="volume">Volume:</label>
	<input type="number" name="volume" id="volume" min="0"
		   value="'.$this->publication->getVolume().'"/><br/>';
		}

		if ($type == 'article' || $type == 'techreport') {
			$string .= '<label for="number">Number:</label>
	<input type="number" name="number" id="number" min="0"
		   value="'.$this->publication->getNumber().'"/><br/>';
		}

		$string .= '<label for="abstract">Abstract:</label>
		<textarea name="abstract" id="abstract" rows="5" cols="50">'.$this->publication->getAbstract().'</textarea><br/>';

		$string .= '<input type="hidden" name="action" value="edit"/>
<input type="submit" value="Update"/>
<input type="reset" value="Reset changes"/>
</form>';

		return $string;
	}


	/**
	 * Shows the publication's journal name.
	 *
	 * @return    string
	 */
	public function showJournal() {

		return $this->publication->getJournal();
	}


	/**
	 * Shows the publication's book name.
	 *
	 * @return    string
	 */
	public function showBooktitle() {

		return $this->publication->getBooktitle();
	}


	/**
	 * @return bool|string
	 */
	public function showPublisher() {

		return $this->publication->getPublisher();
	}


	/**
	 * @return string
	 */
	public function showEdition() {

		return $this->publication->getEdition();
	}


	/**
	 * @return string
	 */
	public function showInstitution() {

		return $this->publication->getInstitution();
	}


	/**
	 * @return string
	 */
	public function showHowpublished() {

		return $this->publication->getHowpublished();
	}


	public function showFirstPage() {

		return $this->publication->getFirstPage();
	}


	public function showLastPage() {

		return $this->publication->getLastPage();
	}


	/**
	 * @return string
	 */
	public function showVolume() {

		return $this->publication->getVolume();
	}


	/**
	 * @return string
	 */
	public function showNumber() {

		return $this->publication->getNumber();
	}


	/**
	 * @return string
	 */
	public function showSeries() {

		return $this->publication->getSeries();
	}
}
