<?php

namespace publin\src;

/**
 * Class Permission
 *
 * @package publin\src
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
