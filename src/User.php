<?php

namespace publin\src;

use InvalidArgumentException;

class User extends Entity {


	protected $id;
	protected $name;
	protected $mail;
	protected $date_register;
	protected $date_last_login;
	/**
	 * @var Role[]
	 */
	protected $roles;
	/**
	 * @var Permission[]
	 */
	protected $permissions;


	public function __construct(array $data, array $roles = array(), array $permissions = array()) {

		parent::__construct($data);
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
