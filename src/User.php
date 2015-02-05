<?php

require_once 'Object.php';


class User extends Object {

    private $role;
    private $permissions = array();


    public function getPassword() {

        return $this->getData('password');
    }


    public function getRole() {

        return $this->role;
    }


    public function setRole($role) {

        $this->role = $role;
    }


    public function getPermissions() {

        return $this->permissions;
    }


    public function setPermissions(array $permissions) {

        $this->permissions = $permissions;
    }


    public function hasPermission($permission) {

        $permission = array('name' => $permission);
        if (in_array($permission, $this->permissions)) {
            return true;
        }
        else {
            return false;
        }
    }
}
