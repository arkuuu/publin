<?php

namespace arkuuu\Publin;

/**
 * Class Permission
 *
 * @package arkuuu\Publin
 */
class Permission extends Entity
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
