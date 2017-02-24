<?php

namespace arkuuu\Publin;

/**
 * Class Keyword
 *
 * @package arkuuu\Publin
 */
class Keyword extends Entity
{

    protected $id;

    protected $name;


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
}
