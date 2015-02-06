<?php

namespace publin\src;

class User extends Object {

	private $roles;
	private $permissions = array();


	public function getPassword() {

		return $this->getData('password');
	}


	public function getRoles() {

		return $this->roles;
	}


	public function setRoles(array $roles) {

		$this->roles = $roles;
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
