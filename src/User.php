<?php

namespace publin\src;

use InvalidArgumentException;

class User extends Entity {


	private $id;
	private $name;
	private $mail;
	private $date_register;
	private $date_last_login;
	/**
	 * @var Role[]
	 */
	private $roles;
	/**
	 * @var Permission[]
	 */
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


	public function getData() {

		return get_object_vars($this);
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
				throw new InvalidArgumentException('must be array with Role objects');
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


	/**
	 * @return Permission[]
	 */
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
				throw new InvalidArgumentException('must be array with Permission objects');
			}
		}
	}


	public function hasPermission($permission_name) {

		foreach ($this->permissions as $permission) {
			if ($permission->getName() === $permission_name) {
				return true;
			}
		}

		return false;
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
}
