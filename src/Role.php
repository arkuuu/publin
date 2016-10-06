<?php

namespace publin\src;

use InvalidArgumentException;

/**
 * Class Role
 *
 * @package publin\src
 */
class Role extends Entity
{

    protected $id;

    protected $name;

    /**
     * @var Permission[]
     */
    protected $permissions = array();


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
     * @return Permission[]
     */
    public function getPermissions()
    {
        return $this->permissions;
    }


    /**
     * @param Permission[] $permissions
     *
     * @return bool
     */
    public function setPermissions(array $permissions)
    {
        $this->permissions = array();

        foreach ($permissions as $permission) {

            if ($permission instanceof Permission) {
                $this->permissions[] = $permission;
            } else {
                throw new InvalidArgumentException('must be array with Permission objects');
            }
        }

        return true;
    }


    /**
     * @param $permission_id
     *
     * @return bool
     */
    public function hasPermission($permission_id)
    {
        foreach ($this->permissions as $permission) {
            if ($permission->getId() == $permission_id) {
                return true;
            }
        }

        return false;
    }
}
