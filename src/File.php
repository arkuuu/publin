<?php

namespace publin\src;

/**
 * Class File
 *
 * @package publin\src
 */
class File extends Entity
{

    protected $id;

    protected $name;

    protected $extension;

    protected $size;

    protected $title;

    protected $full_text;

    protected $restricted;

    protected $hidden;


    /**
     * @return string|null
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * @return string|null
     */
    public function getExtension()
    {
        return $this->extension;
    }


    /**
     * @return string|null
     */
    public function getSize()
    {
        return $this->size;
    }


    /**
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }


    /**
     * @return bool
     */
    public function isFullText()
    {
        return (bool)$this->full_text;
    }


    /**
     * @return bool
     */
    public function isRestricted()
    {
        return (bool)$this->isRestricted();
    }


    /**
     * @return bool
     */
    public function isHidden()
    {
        return (bool)$this->isHidden();
    }
}
