<?php

namespace publin\src;

/**
 * Class AuthorView
 *
 * @package publin\src
 */
class AuthorView extends ViewWithPublications
{

    /**
     * @var Author
     */
    private $author;

    /**
     * @var bool
     */
    private $edit_mode;

    /**
     * The array contains instances of all indices
     * which should be displayed on the author page.
     *
     * @var array
     */
    private $indices;


    /**
     * Constructs the author view.
     *
     * @param Author $author
     * @param array  $publications
     * @param array  $indices
     * @param array  $errors
     * @param bool   $edit_mode
     */
    public function __construct(
        Author $author,
        array $publications,
        array $indices,
        array $errors,
        $edit_mode = false
    ) {
        parent::__construct($publications, 'author', $errors);
        $this->author = $author;
        $this->edit_mode = $edit_mode;
        $this->indices = $indices;
    }


    /**
     * @return string
     */
    public function showPageTitle()
    {
        return $this->html($this->showName());
    }


    /**
     * @return string
     */
    public function showName()
    {
        return $this->html($this->author->getName());
    }


    /**
     * @return bool
     */
    public function isEditMode()
    {
        return $this->edit_mode;
    }


    /**
     * @param string $mode
     *
     * @return string
     */
    public function showLinkToSelf($mode = '')
    {
        $url = '?p=author&id=';
        $mode_url = '&m='.$mode;

        if (empty($mode)) {
            return $this->html($url.$this->author->getId());
        } else {
            return $this->html($url.$this->author->getId().$mode_url);
        }
    }


    /**
     * Shows the author's contact info.
     *
     * @param bool $nl2br
     *
     * @return string
     */
    public function showContact($nl2br = true)
    {
        if ($nl2br) {
            return nl2br($this->html($this->author->getContact()));
        } else {
            return $this->html($this->author->getContact());
        }
    }


    /**
     * Shows the author's text.
     *
     * @param bool $nl2br
     *
     * @return string
     */
    public function showAbout($nl2br = true)
    {
        if ($nl2br) {
            return nl2br($this->html($this->author->getAbout()));
        } else {
            return $this->html($this->author->getAbout());
        }
    }


    /**
     * Shows links to other bibliographic indexes for this author.
     *
     * @return string
     */
    public function showBibLinks()
    {
        $string = '';

        foreach (BibLink::getServices() as $service) {
            $url = BibLink::getAuthorsLink($this->author, $service);
            $string .= '<li><a href="'.$this->html($url).'" target="_blank">'.$this->html($service).'</a></li>';
        }

        return $string;
    }


    /**
     * @return string
     */
    public function showGivenName()
    {
        return $this->html($this->author->getFirstName());
    }


    /**
     * @return string
     */
    public function showFamilyName()
    {
        return $this->html($this->author->getLastName());
    }


    /**
     * Shows the author's website.
     *
     * @return string
     */
    public function showWebsite()
    {
        return $this->html($this->author->getWebsite());
    }


    /**
     * Shows a list with all indices.
     *
     * Each entry of the list contains the name of the index
     * and the calculated value.
     *
     * @return string
     */
    public function showIndices()
    {
        $string = '';

        foreach ($this->indices as $index) {
            $string .= '<li>'.$index->getName().': '.$index->getValue().'</li>'."\n";
        }

        if (!empty($string)) {
            return $string;
        } else {
            return '<li>No indices found</li>';
        }
    }
}
