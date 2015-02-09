<?php

namespace publin\src;

class User extends Object {


	/**
	 * @var Role[]
	 */
	private $roles;
	private $permissions;


	public function __construct(array $data, array $roles = array(), array $permissions = array()) {

		$this->setPermissions($permissions);
		$this->setRoles($roles);
		parent::__construct($data);
	}


	/**
	 * @return Role[]
	 */
	public function getRoles() {

		return $this->roles;
	}


	public function setRoles(array $roles) {

		$this->roles = array();

		foreach ($roles as $role) {
			if ($role instanceof Role) {
				$this->roles[] = $role;
			}
			else {
				// TODO: what to do when incorrect object
			}
		}
	}


	public function hasRole($role_id) {

		foreach ($this->roles as $role) {
			if ($role->getId() == $role_id) {
				return true;
			}
		}

		return false;
	}


	public function getPermissions() {

		return $this->permissions;
	}


	public function setPermissions(array $permissions) {

		$this->permissions = array();

		foreach ($permissions as $permission) {
			if ($permission instanceof Permission) {
				$this->permissions[] = $permission;
			}
			else {
				// TODO: what to do when incorrect object
			}
		}
	}


//	public function hasPermission($permission) {
//
//		$permission = array('name' => $permission);
//		if (in_array($permission, $this->permissions)) {
//			return true;
//		}
//		else {
//			return false;
//		}
//	}

	public function getMail() {

		return $this->getData('mail');
	}


	public function getDateRegister($format) {

		if ($this->getData('date_register')) {
			return date($format, strtotime($this->getData('date_register')));
		}
		else {
			return false;
		}
	}


	public function getDateLastLogin($format) {

		if ($this->getData('date_last_login')) {
			return date($format, strtotime($this->getData('date_last_login')));
		}
		else {
			return false;
		}
	}


	public function isActive() {

		return $this->getData('active');
	}
}
