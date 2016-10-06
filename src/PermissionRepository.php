<?php

namespace publin\src;

/**
 * Class PermissionRepository
 *
 * @package publin\src
 */
class PermissionRepository extends Repository
{

    public function reset()
    {
        parent::reset();
        $this->select = 'SELECT self.*';
        $this->from = 'FROM `permissions` self';

        return $this;
    }


    /**
     * @param        $column
     * @param        $comparator
     * @param        $value
     * @param null   $function
     * @param string $table
     *
     * @return $this
     */
    public function where($column, $comparator, $value, $function = null, $table = 'self')
    {
        if ($column === 'role_id') {
            $table = 'roles_permissions';
            $this->join .= ' LEFT JOIN `roles_permissions` ON (`roles_permissions`.`permission_id` = self.`id`)';
        } else if ($column === 'user_id') {
            $this->select = 'SELECT DISTINCT self.*';
            $table = 'users_roles';
            $this->join .= ' LEFT JOIN `roles_permissions` ON (`roles_permissions`.`permission_id` = self.`id`)';
            $this->join .= ' LEFT JOIN `users_roles` ON (`users_roles`.`role_id` = `roles_permissions`.`role_id`)';
        }

        parent::where($column, $comparator, $value, $function, $table);

        return $this;
    }


    /**
     * @return Permission[]
     */
    public function find()
    {
        $result = parent::find();
        $permissions = array();

        foreach ($result as $row) {
            $permissions[] = new Permission($row);
        }

        return $permissions;
    }


    /**
     * @return Permission|false
     */
    public function findSingle()
    {
        $result = parent::findSingle();

        if ($result) {
            return new Permission($result);
        } else {
            return false;
        }
    }
}
