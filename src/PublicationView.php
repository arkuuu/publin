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
	 * @param array       $errors
	 * @param bool        $edit_mode
	 */
	public function __construct(Publication $publication, array $errors, $edit_mode = false) {

		parent::__construct('publication', $errors);
		$this->publication = $publication;
		$this->edit_mode = $edit_mode;
	}


	public function isEditMode() {

		return $this->edit_mode;
	}


	public function showLinkToSelf($mode = '') {

		$url = '?p=publication&id=';
		$mode_url = '&m='.$mode;
		//$url = Request::createUrl(array('p' => 'publication', 'm' => $mode, 'id' => $this->publication->getId()));
		//return $url;

		if (empty($mode)) {
			return $this->html($url.$this->publication->getId());
		}
		else {
			return $this->html($url.$this->publication->getId().$mode_url);
		}
	}


	/**
	 * Shows the page title.
	 *
	 * @return    string
	 */
	public function showPageTitle() {

		return $this->html($this->publication->getTitle());
	}


	/**
	 * Shows the publication's title.
	 *
	 * @return    string
	 */
	public function showTitle() {

		return $this->html($this->publication->getTitle());
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

				$url = '?p=author&id=';
				$id = $author->getId();
				$name = $author->getName();

				if ($id && $name) {
					$author = '<a href="'.$this->html($url.$id).'">'.$this->html($name).'</a>';
				}
				else if ($name) {
					$author = $this->html($name);
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

		return $this->html($this->publication->getDatePublished($format));
	}


	/**
	 * Shows the publication's type.
	 *
	 * @return    string
	 */
	public function showType() {

		$url = '?p=browse&by=type&id=';
		$id = $this->publication->getTypeId();
		$name = $this->publication->getTypeName();

		if ($id && $name) {
			return '<a href="'.$this->html($url.$id).'">'.$this->html($name).'</a>';
		}
		else if ($name) {
			return $this->html($name);
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

		return $this->html($this->publication->getPages($divider));
	}


	/**
	 * @return string
	 */
	public function showSchool() {

		return $this->html($this->publication->getSchool());
	}


	/**
	 * @return string
	 */
	public function showAddress() {

		return $this->html($this->publication->getAddress());
	}


	public function showLocation() {

		return $this->html($this->publication->getLocation());
	}


	/**
	 * @return string
	 */
	public function showDoi() {

		$url = 'http://dx.doi.org/';
		$doi = $this->publication->getDoi();

		if ($doi) {
			return '<a href="'.$this->html($url.$doi).'" target="_blank">'.$this->html($doi).'</a>';
		}
		else {
			return false;
		}
	}


	/**
	 * @return string
	 */
	public function showIsbn() {

		return $this->html($this->publication->getIsbn());
	}


	/**
	 * @return string
	 */
	public function showNote() {

		return $this->html($this->publication->getNote());
	}


	/**
	 * Shows the publication's abstract.
	 *
	 * @return    string
	 */
	public function showAbstract() {

		return nl2br($this->html($this->publication->getAbstract()));
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
			$url = '?p=keyword&id=';

			foreach ($keywords as $keyword) {

				$id = $keyword->getId();
				$name = $keyword->getName();

				if ($id && $name) {
					$string .= '<a href="'.$this->html($url.$id).'">'.$this->html($name).'</a>'.$separator;
				}
				else if ($name) {
					$string .= $this->html($name).$separator;
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
						'.$this->html($keyword->getName()).'
						<input type="hidden" name="keyword_id" value="'.$this->html($keyword->getId()).'"/>
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
						'.$this->html($author->getName()).'
						<input type="hidden" name="author_id" value="'.$this->html($author->getId()).'"/>
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
			$string .= '<li><a href="'.$this->html($url).'" target="_blank">'.$this->html($service).'</a></li>';
		}

		return $string;
	}


	/**
	 * Shows export formats.
	 *
	 * @param    string $format The export format
	 *
	 * @return    string
	 */
	public function showExport($format) {

		return FormatHandler::export($this->publication, $format);
	}


	public function showEditForm() {

		$string = '<form action="#" method="post" accept-charset="utf-8">
	<label for="title">Title:</label>
	<input type="text" name="title" id="title" maxlength="200" value="'.$this->publication->getTitle().'"/>
	<br/>
	<label for="journal">Journal:</label>
	<input type="text" name="journal" id="journal" maxlength="200" value="'.$this->publication->getJournal().'"/>
	<br/>
	<label for="volume">Volume:</label>
	<input type="number" name="volume" id="volume" min="0" value="'.$this->publication->getVolume().'"/>
	<br/>
	<label for="number">Number:</label>
	<input type="number" name="number" id="number" min="0" value="'.$this->publication->getNumber().'"/>
	<br/>
	<label for="booktitle">Book Title:</label>
	<input type="text" name="booktitle" id="booktitle" maxlength="200" value="'.$this->publication->getBooktitle().'"/>
	<br/>
	<label for="series">Series:</label>
	<input type="text" name="series" id="series" min="0" value="'.$this->publication->getSeries().'"/>
	<br/>
	<label for="edition">Edition:</label>
	<input type="text" name="edition" id="edition" value="'.$this->publication->getEdition().'"/>
	<br/>
	<label for="pages_from">Pages:</label>
	<input type="number" name="pages_from" id="pages_from" min="0" placeholder="12" value="'.$this->publication->getFirstPage().'"/>
	<input type="number" name="pages_to" id="pages_to" min="0" placeholder="23" value="'.$this->publication->getLastPage().'"/>
	<br/>
	<label for="note">Note:</label>
	<input type="text" name="note" id="note" maxlength="200" value="'.$this->publication->getNote().'"/>
	<br/>
	<label for="location">Location:</label>
	<input type="text" name="location" id="location" maxlength="200" value="'.$this->publication->getLocation().'"/>
	<br/>
	<label for="date_published">Date:</label>
	<input type="text" name="date_published" id="date_published" placeholder="YYYY-MM-DD"
		   value="'.$this->publication->getDatePublished('Y-m-d').'"/>
	<br/>
	<label for="publisher">Publisher:</label>
	<input type="text" name="publisher" id="publisher" maxlength="200" value="'.$this->publication->getPublisher().'"/>
	<br/>
	<label for="institution">Institution:</label>
	<input type="text" name="institution" id="institution" maxlength="200" value="'.$this->publication->getInstitution().'"/>
	<br/>
	<label for="school">School:</label>
	<input type="text" name="school" id="school" maxlength="200" value="'.$this->publication->getSchool().'"/>
	<br/>
	<label for="address">Address:</label>
	<input type="text" name="address" id="address" maxlength="200" value="'.$this->publication->getAddress().'"/>
	<br/>
	<label for="howpublished">How published:</label>
	<input type="text" name="howpublished" id="howpublished" maxlength="200" value="'.$this->publication->getHowpublished().'"/>
	<br/>
	<label for="copyright">Copyright:</label>
	<input type="text" name="copyright" id="copyright" maxlength="200" value="'.$this->publication->getCopyright().'"/>
	<br/>
	<label for="doi">DOI:</label>
	<input type="text" name="doi" id="doi" maxlength="200" value="'.$this->publication->getDoi().'"/>
	<br/>
	<label for="isbn">ISBN:</label>
	<input type="text" name="isbn" id="isbn" maxlength="200" value="'.$this->publication->getIsbn().'"/>
	<br/>
	<label for="abstract">Abstract:</label>
	<textarea name="abstract" id="abstract" rows="5" cols="50">'.$this->publication->getAbstract().'</textarea>
	<br/>
	<input type="hidden" name="action" value="edit"/>
	<input type="hidden" name="type" value="'.$this->publication->getTypeName().'"/>
	<input type="hidden" name="study_field" value="'.$this->publication->getStudyField().'"/>
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

		return $this->html($this->publication->getJournal());
	}


	/**
	 * Shows the publication's book name.
	 *
	 * @return    string
	 */
	public function showBooktitle() {

		return $this->html($this->publication->getBooktitle());
	}


	/**
	 * @return bool|string
	 */
	public function showPublisher() {

		return $this->html($this->publication->getPublisher());
	}


	/**
	 * @return string
	 */
	public function showEdition() {

		return $this->html($this->publication->getEdition());
	}


	/**
	 * @return string
	 */
	public function showInstitution() {

		return $this->html($this->publication->getInstitution());
	}


	/**
	 * @return string
	 */
	public function showHowpublished() {

		return $this->html($this->publication->getHowpublished());
	}


	public function showFirstPage() {

		return $this->html($this->publication->getFirstPage());
	}


	public function showLastPage() {

		return $this->html($this->publication->getLastPage());
	}


	public function showStudyField() {

		return $this->html($this->publication->getStudyField());
	}


	/**
	 * @return string
	 */
	public function showVolume() {

		return $this->html($this->publication->getVolume());
	}


	/**
	 * @return string
	 */
	public function showNumber() {

		return $this->html($this->publication->getNumber());
	}


	/**
	 * @return string
	 */
	public function showSeries() {

		return $this->html($this->publication->getSeries());
	}


	public function showCopyright() {

		return $this->html($this->publication->getCopyright());
	}


	public function showFiles() {

		$files = $this->publication->getFiles();
		$url = '?p=publication&id='.$this->publication->getId().'&m=file&file=';
		$string = '';

		foreach ($files as $file) {

			if ($file->isFullText()) {
				$title = 'Full text';
			}
			else if ($file->getTitle()) {
				$title = $file->getTitle();
			}
			else {
				$title = 'Unnamed file';
			}
			$restricted = $file->isRestricted() ? ' (restricted)' : '';

			$string .= '<li><a href="'.$this->html($url.$file->getId()).'" target="_blank">'.$this->html($title).'</a>'.$this->html($restricted).'</li>';
		}

		return $string;
	}


	public function showEditFiles() {

		$files = $this->publication->getFiles();
		$url = '?p=publication&id='.$this->publication->getId().'&m=file&file=';
		$string = '';

		foreach ($files as $file) {

			$full_text = $file->isFullText() ? ' (full text)' : '';
			$restricted = $file->isRestricted() ? ' (restricted)' : '';
			$string .= '<li>
						<form action="#" method="post" accept-charset="utf-8">
						<a href="'.$this->html($url.$file->getId()).'" target="_blank">'.$this->html($file->getTitle()).'</a>'.$this->html($full_text.$restricted).'
						<input type="hidden" name="file_id" value="'.$this->html($file->getId()).'"/>
						<input type="hidden" name="action" value="removeFile"/>
						<input type="submit" value="x"/>
						</form>
						</li>';
		}

		$string .= '<li><form action="#" method="post" enctype="multipart/form-data">
	<label for="file">File:</label>
	<input type="file" name="file" id="file"><br/>
	<label for="title">Description:</label>
	<input type="text" name="title" id="title"><br/>
	<input type="checkbox" name="full_text" id="full_text" value="yes">
	<label for="full_text">Full Text</label>
	<input type="checkbox" name="restricted" id="restricted" value="yes"/>
	<label for="restricted">Access Restricted</label><br/>
	<input type="hidden" name="action" value="addFile"/>
	<input type="submit" value="Upload File"/>
</form></li>';

		return $string;
	}
}
