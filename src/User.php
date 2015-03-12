<?php

namespace publin\src;

class User {


	private $id;
	private $name;
	private $mail;
	private $active;
	private $date_register;
	private $date_last_login;
	/**
	 * @var Role[]
	 */
	private $roles;
	private $permissions;


	public function __construct(array $data, array $roles = array(), array $permissions = array()) {

		foreach ($data as $property => $value) {
			if (property_exists($this, $property)) {
				$this->$property = $value;
			}
		}

		$this->setPermissions($permissions);
		$this->setRoles($roles);
	}


	public function getId() {

		return $this->id;
	}


	public function getName() {

		return $this->name;
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
				//TODO: only while Permission Object not in use:
				$this->permissions = $permissions;
			}
		}
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


	public function getMail() {

		return $this->mail;
	}


	public function getDateRegister($format) {

		if ($this->date_register) {
			return date($format, strtotime($this->date_register));
		}
		else {
			return false;
		}
	}


	public function getDateLastLogin($format) {

		if ($this->date_last_login) {
			return date($format, strtotime($this->date_last_login));
		}
		else {
			return false;
		}
	}


	public function isActive() {

		if ($this->active) {
			return true;
		}
		else {
			return false;
		}
	}
}
