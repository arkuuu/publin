<?php

namespace arkuuu\Publin;

/**
 * Class Type
 *
 * @package arkuuu\Publin
 */
class Type extends Entity
{

    protected $id;

    protected $name;

    protected $description;


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
    public function getDescription()
    {
        return $this->description;
    }
}
