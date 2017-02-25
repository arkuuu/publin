<?php

namespace arkuuu\Publin;

use Exception;

/**
 * Class PublicationView
 *
 * @package arkuuu\Publin
 */
class PublicationView extends View
{

    /**
     * @var    Publication
     */
    private $publication;

    /**
     * @var bool
     */
    private $edit_mode;

    /**
     * @var array
     */
    private $all_plublications;


    /**
     * Constructs the publication view.
     *
     * @param Publication $publication
     * @param array       $errors
     * @param bool        $edit_mode
     * @param array|null  $all_plublications
     */
    public function __construct(Publication $publication, array $errors, $edit_mode = false, $all_plublications = null)
    {
        parent::__construct('publication', $errors);
        $this->publication = $publication;
        $this->edit_mode = $edit_mode;
        if ($edit_mode) {
            $this->all_plublications = $all_plublications;
        }
    }


    /**
     * @return bool
     */
    public function isEditMode()
    {
        return $this->edit_mode;
    }


    /**
     * @param null $mode
     *
     * @return string
     */
    public function showLinkToSelf($mode = null)
    {
        return $this->html(Request::createUrl(array(
            'p'  => 'publication',
            'm'  => $mode,
            'id' => $this->publication->getId(),
        )));
    }


    /**
     * Shows the page title.
     *
     * @return    string
     */
    public function showPageTitle()
    {
        return $this->html($this->publication->getTitle());
    }


    /**
     * Shows the publication's title.
     *
     * @return    string
     */
    public function showTitle()
    {
        return $this->html($this->publication->getTitle());
    }


    /**
     * @return string
     */
    public function showMetaTags()
    {
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
    public function showAuthors()
    {
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
                } else if ($name) {
                    $author = $this->html($name);
                } else {
                    $author = 'Unknown Author';
                }

                if ($i == 1) {
                    /* first author */
                    $result .= $author;
                } else if ($i == $num) {
                    /* last author */
                    $result .= ' and '.$author;
                } else {
                    /* all other authors */
                    $result .= ', '.$author;
                }
                $i++;
            }
        }

        return $result;
    }


    /**
     * Shows the publication's citations.
     *
     * @return string
     * @throws Exception
     */
    public function showCitations()
    {
        $result = false;
        $citations = $this->publication->getCitations();
        $num = count($citations);
        if ($num > 0) {
            $result .= '<ul class="list-group">';
            foreach ($citations as $citation) {
                $result .= '<li class="list-group-item">'.$this->showCitation($citation->getCitationPublication()).'</li>'."\n";
            }
            $result .= '</ul>';
        }

        return $result;
    }


    /**
     * @return string
     */
    public function showEditCitations()
    {
        $citations = $this->publication->getCitations();
        $string = '';

        foreach ($citations as $citation) {
            $citationPublication = $citation->getCitationPublication();
            $string .= '<li>
                        <form action="#" method="post" accept-charset="utf-8">
                        <a href="?p=publication&m=edit&id='.$this->html($citation->getCitationId()).'">'.$this->html($citationPublication->getTitle()).'</a>
                        <input type="hidden" name="citation_id" value="'.$this->html($citation->getId()).'"/>
                        <input type="hidden" name="action" value="removeCitation"/>
                        <input type="submit" value="x"/>
                        </form>
                        </li>';
        }

        $string .= '<li>
                    <form action="#" method="post" accept-charset="utf-8">
                    <select name="citation_id">
                        <option></option>';
        foreach ($this->all_plublications as $publication) {
            $string .= '<option value='.$publication->getId().'>'.$publication->getTitle().'</option>';
        }
        $string .= '</select>
<input type="hidden" name="action" value="addCitation"/>
<input type="submit" value="Add"/>
</form>
</li>';

        return $string;
    }


    /**
     * Shows the publication's publish date.
     *
     * @param string $format
     *
     * @return string
     */
    public function showDatePublished($format = 'F Y')
    {
        return $this->html($this->publication->getDatePublished($format));
    }


    /**
     * Shows the publication's type.
     *
     * @param bool $link
     *
     * @return string
     */
    public function showType($link = true)
    {
        if ($link) {
            $url = Request::createUrl(array('p' => 'type', 'id' => $this->publication->getTypeId()));

            return '<a href="'.$this->html($url).'">'.$this->html($this->publication->getTypeName()).'</a>';
        } else {
            return $this->html($this->publication->getTypeName());
        }
    }


    /**
     * @param string $divider
     *
     * @return string
     */
    public function showPages($divider = '-')
    {
        return $this->html($this->publication->getPages($divider));
    }


    /**
     * @return string
     */
    public function showSchool()
    {
        return $this->html($this->publication->getSchool());
    }


    /**
     * @return string
     */
    public function showAddress()
    {
        return $this->html($this->publication->getAddress());
    }


    /**
     * @return string
     */
    public function showLocation()
    {
        return $this->html($this->publication->getLocation());
    }


    /**
     * @return string
     */
    public function showDoi()
    {
        return $this->html($this->publication->getDoi());
    }


    /**
     * @return string
     */
    public function showIsbn()
    {
        return $this->html($this->publication->getIsbn());
    }


    /**
     * @return string
     */
    public function showNote()
    {
        return $this->html($this->publication->getNote());
    }


    /**
     * Shows the publication's abstract.
     *
     * @param bool $nl2br
     *
     * @return string
     */
    public function showAbstract($nl2br = true)
    {
        if ($nl2br) {
            return nl2br($this->html($this->publication->getAbstract()));
        } else {
            return $this->html($this->publication->getAbstract());
        }
    }


    /**
     * @param string $separator
     *
     * @return bool|string
     */
    public function showKeywords($separator = ', ')
    {
        $keywords = $this->publication->getKeywords();

        if (!empty($keywords)) {

            $string = '';
            $url = '?p=keyword&id=';

            foreach ($keywords as $keyword) {

                $id = $keyword->getId();
                $name = $keyword->getName();

                if ($id && $name) {
                    $string .= '<a href="'.$this->html($url.$id).'">'.$this->html($name).'</a>'.$separator;
                } else if ($name) {
                    $string .= $this->html($name).$separator;
                }
            }

            return substr($string, 0, -(strlen($separator)));
        } else {
            return false;
        }
    }


    /**
     * @return string
     */
    public function showEditKeywords()
    {
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


    /**
     * @return string
     */
    public function showEditAuthors()
    {
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
    public function showBibLinks()
    {
        $string = '';

        foreach (BibLink::getServices() as $service) {
            $url = BibLink::getPublicationsLink($this->publication, $service);
            $string .= '<a class="list-group-item list-group-item-action" href="'.$this->html($url).'" target="_blank">'.$this->html($service).'</a>';
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
    public function showExport($format)
    {
        return FormatHandler::export($this->publication, $format);
    }


    /**
     * @param string $format
     * @param string $title
     *
     * @return string
     */
    public function renderExportModal($format, $title)
    {
        $id = $this->html('cite'.ucfirst($format));
        $title = $this->html($title);
        $content = ($this->html(trim(FormatHandler::export($this->publication, $format))));

        return <<<HTML
<div class="modal fade" id="{$id}" tabindex="-1" role="dialog" aria-labelledby="{$id}Title"
		 aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="{$id}Title">{$title}</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<pre><code>{$content}</code></pre>
				</div>
			</div>
		</div>
	</div>
HTML;

    }


    /**
     * Shows the publication's journal name.
     *
     * @return    string
     */
    public function showJournal()
    {
        return $this->html($this->publication->getJournal());
    }


    /**
     * Shows the publication's book name.
     *
     * @return    string
     */
    public function showBooktitle()
    {
        return $this->html($this->publication->getBooktitle());
    }


    /**
     * @return bool|string
     */
    public function showPublisher()
    {
        return $this->html($this->publication->getPublisher());
    }


    /**
     * @return string
     */
    public function showEdition()
    {
        return $this->html($this->publication->getEdition());
    }


    /**
     * @return string
     */
    public function showInstitution()
    {
        return $this->html($this->publication->getInstitution());
    }


    /**
     * @return string
     */
    public function showHowpublished()
    {
        return $this->html($this->publication->getHowpublished());
    }


    /**
     * @return string
     */
    public function showFirstPage()
    {
        return $this->html($this->publication->getFirstPage());
    }


    /**
     * @return string
     */
    public function showLastPage()
    {
        return $this->html($this->publication->getLastPage());
    }


    /**
     * @param bool $link
     *
     * @return string
     */
    public function showStudyField($link = true)
    {
        if ($link) {
            $url = Request::createUrl(array('p' => 'study_field', 'id' => $this->publication->getStudyFieldId()));

            return '<a href="'.$this->html($url).'">'.$this->html($this->publication->getStudyField()).'</a>';
        } else {
            return $this->html($this->publication->getStudyField());
        }
    }


    /**
     * @return string
     */
    public function showVolume()
    {
        return $this->html($this->publication->getVolume());
    }


    /**
     * @return string
     */
    public function showNumber()
    {
        return $this->html($this->publication->getNumber());
    }


    /**
     * @return string
     */
    public function showSeries()
    {
        return $this->html($this->publication->getSeries());
    }


    /**
     * @return string
     */
    public function showCopyright()
    {
        return $this->html($this->publication->getCopyright());
    }


    /**
     * @return string
     */
    public function showFiles()
    {
        $files = $this->publication->getFiles();
        $string = '';

        foreach ($files as $file) {
            if (!$file->isHidden() || $this->hasPermission(Auth::ACCESS_HIDDEN_FILES)) {

                $url = Request::createUrl(array(
                    'p'       => 'publication',
                    'id'      => $this->publication->getId(),
                    'file_id' => $file->getId(),
                ));
                $full_text = $file->isFullText() ? ' (full text)' : '';
                $hidden = $file->isHidden() ? ' (hidden)' : '';
                $restricted = $file->isRestricted() ? ' (restricted)' : '';

                $string .= '<li><a href="'.$this->html($url).'" target="_blank">'.$this->html($file->getTitle().$file->getExtension()).'</a>'.$this->html($full_text.$hidden.$restricted).'</li>';
            }
        }

        return $string;
    }


    /**
     * @return bool|string
     */
    public function showFullTextFile()
    {
        $file = $this->publication->getFullTextFile();
        if ($file) {
            $url = Request::createUrl(array(
                'p'       => 'publication',
                'id'      => $this->publication->getId(),
                'file_id' => $file->getId(),
            ));
            $restricted = $file->isRestricted() ? ' (restricted)' : '';

            return '<a href="'.$this->html($url).'" target="_blank">Download full text'.$restricted.'</a>';
        } else {
            return false;
        }
    }


    /**
     * @return string
     */
    public function showEditFiles()
    {
        $files = $this->publication->getFiles();
        $string = '';

        foreach ($files as $file) {

            $url = Request::createUrl(array(
                'p'       => 'publication',
                'id'      => $this->publication->getId(),
                'file_id' => $file->getId(),
            ));
            $full_text = $file->isFullText() ? ' (full text)' : '';
            $hidden = $file->isHidden() ? ' (hidden)' : '';
            $restricted = $file->isRestricted() ? ' (restricted)' : '';

            $string .= '<li>
                        <form action="#" method="post" accept-charset="utf-8">
                        <a href="'.$this->html($url).'" target="_blank">'.$this->html($file->getTitle().$file->getExtension()).'</a>'.$this->html($full_text.$hidden.$restricted).'
                        <input type="hidden" name="file_id" value="'.$this->html($file->getId()).'"/>
                        <input type="hidden" name="action" value="removeFile"/>
                        <input type="submit" value="x" onclick="return confirm(\'Do you really want to delete the file '.$this->html($file->getTitle().$file->getExtension()).'?\')"/>
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
    <label for="restricted">Access Restricted</label>
    <input type="checkbox" name="hidden" id="hidden" value="yes"/>
    <label for="hidden">Hidden</label><br/>
    <input type="hidden" name="action" value="addFile"/>
    <input type="submit" value="Upload File"/>
</form></li>';

        return $string;
    }


    /**
     * @return bool|string
     */
    public function showUrls()
    {
        $result = false;
        $urls = $this->publication->getUrls();

        if (count($urls) > 0) {
            foreach ($urls as $url) {
                $result .= '<li><a href="'.$this->html($url->getUrl()).'" target="_blank">'.$this->html($url->getName()).'</a></li>';
            }
        }

        return $result;
    }


    /**
     * @return string
     */
    public function showEditUrls()
    {
        $urls = $this->publication->getUrls();
        $string = '';

        foreach ($urls as $url) {
            $string .= '<li>
                        <form action="#" method="post" accept-charset="utf-8">
                        '.$this->html($url->getName()).': <a href="'.$this->html($url->getUrl()).'" target="_blank">'.$this->html($url->getUrl()).'</a>
                        <input type="hidden" name="url_id" value="'.$this->html($url->getId()).'"/>
                        <input type="hidden" name="action" value="removeUrl"/>
                        <input type="submit" value="x"/>
                        </form>
                        </li>';
        }

        $string .= '<li>
                    <form action="#" method="post" accept-charset="utf-8">
                    <input type="text" name="name" placeholder="Name" />
                    <input type="text" name="url" placeholder="Url" />
                    <input type="hidden" name="action" value="addUrl"/>
                    <input type="submit" value="Add"/>
                    </form>
                    </li>';

        return $string;
    }


    /**
     * @return string
     */
    public function showPublinUrl()
    {
        return $this->html($this->publication->getPublinUrl());
    }


    /**
     * @return string
     */
    public function showForeign()
    {
        if ($this->publication->getForeign()) {
            return 'checked';
        } else {
            return '';
        }
    }
}
