<?php

namespace arkuuu\Publin;

/**
 * Class Url
 *
 * @package arkuuu\Publin
 */
class Url extends Entity
{

    protected $id;

    protected $name;

    protected $url;


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
    public function getUrl()
    {
        return $this->url;
    }
}
