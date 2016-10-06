<?php

namespace publin\src;

/**
 * Class ViewWithPublications
 *
 * @package publin\src
 */
class ViewWithPublications extends View
{

    /**
     * @var Publication[]
     */
    protected $publications;


    /**
     * @param array $publications
     * @param array $type
     * @param array $errors
     */
    public function __construct(array $publications, $type, array $errors = array())
    {
        parent::__construct($type, $errors);
        $this->publications = $publications;
    }


    /**
     * @return string
     */
    public function showPublications()
    {
        $string = '';

        foreach ($this->publications as $publication) {
            $string .= '<li>'.$this->showCitation($publication).'</li>'."\n";
        }

        if (!empty($string)) {
            return $string;
        } else {
            return '<li>No publications found</li>';
        }
    }


    /**
     * @return int
     */
    public function showPublicationsNum()
    {
        return count($this->publications);
    }
}
