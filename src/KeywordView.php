<?php

namespace publin\src;

/**
 * Class KeywordView
 *
 * @package publin\src
 */
class KeywordView extends ViewWithPublications
{

    /**
     * @var Keyword
     */
    private $keyword;

    /**
     * @var bool
     */
    private $edit_mode;


    /**
     * @param Keyword $keyword
     * @param array   $publications
     * @param array   $errors
     * @param bool    $edit_mode
     */
    public function __construct(Keyword $keyword, array $publications, array $errors, $edit_mode = false)
    {
        parent::__construct($publications, 'keyword', $errors);
        $this->keyword = $keyword;
        $this->edit_mode = $edit_mode;
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
        return $this->html($this->keyword->getName());
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
        $url = '?p=keyword&id=';
        $mode_url = '&m='.$mode;

        if (empty($mode)) {
            return $this->html($url.$this->keyword->getId());
        } else {
            return $this->html($url.$this->keyword->getId().$mode_url);
        }
    }
}
