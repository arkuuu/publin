<?php

namespace publin\src;

/**
 * Class Author
 *
 * @package publin\src
 */
class Author extends Entity
{

    protected $id;

    protected $family;

    protected $given;

    protected $website;

    protected $contact;

    protected $about;


    /**
     * @return string|null
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Returns the full name, consisting of academic title, first name and last name.
     *
     * @return    string|null
     */
    public function getName()
    {
        if ($this->given && $this->family) {
            return $this->given.' '.$this->family;
        } else {
            return null;
        }
    }


    /**
     * Returns the last name.
     *
     * @return    string|null
     */
    public function getLastName()
    {
        return $this->family;
    }


    /**
     * Returns the first name.
     *
     * @param    $short        boolean        Set true for first letters only (optional)
     *
     * @return    string|null
     */
    public function getFirstName($short = false)
    {
        if ($this->given && $short) {
            $names = preg_split("/\s+/", $this->given);
            $string = '';
            foreach ($names as $name) {
                $string .= mb_substr($name, 0, 1).'.';
            }

            return $string;
        } else if ($this->given) {
            return $this->given;
        } else {
            return null;
        }
    }


    /**
     * Returns the website.
     *
     * @return    string|null
     */
    public function getWebsite()
    {
        return $this->website;
    }


    /**
     * Returns the contact info.
     *
     * @return    string|null
     */
    public function getContact()
    {
        return $this->contact;
    }


    /**
     * Returns the author's text.
     *
     * @return    string|null
     */
    public function getAbout()
    {
        return $this->about;
    }
}
