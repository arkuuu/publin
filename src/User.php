<?php

namespace publin\src;

use InvalidArgumentException;

/**
 * Class User
 *
 * @package publin\src
 */
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


	/**
	 * @param array $data
	 * @param array $roles
	 * @param array $permissions
	 */
	public function __construct(array $data, array $roles = array(), array $permissions = array()) {

		parent::__construct($data);
		$this->setPermissions($permissions);
		$this->setRoles($roles);
	}


	/**
	 * @return string|null
	 */
	public function getId() {

		return $this->id;
	}


	/**
	 * @return string|null
	 */
	public function getName() {

		return $this->name;
	}


	/**
	 * @return Role[]
	 */
	public function getRoles() {

		return $this->roles;
	}


	/**
	 * @param Role[] $roles
	 *
	 * @return bool
	 */
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

		return true;
	}


	/**
	 * @param $role_id
	 *
	 * @return bool
	 */
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


	/**
	 * @param Permission[] $permissions
	 *
	 * @return bool
	 */
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

		return true;
	}


	/**
	 * @param $permission_name
	 *
	 * @return bool
	 */
	public function hasPermission($permission_name) {

		foreach ($this->permissions as $permission) {
			if ($permission->getName() === $permission_name) {
				return true;
			}
		}

		return false;
	}


	/**
	 * @return string|null
	 */
	public function getMail() {

		return $this->mail;
	}


	/**
	 * @param $format
	 *
	 * @return string|null
	 */
	public function getDateRegister($format) {

		if ($this->date_register) {
			return date($format, strtotime($this->date_register));
		}
		else {
			return null;
		}
	}


	/**
	 * @param $format
	 *
	 * @return string|null
	 */
	public function getDateLastLogin($format) {

		if ($this->date_last_login) {
			return date($format, strtotime($this->date_last_login));
		}
		else {
			return null;
		}
	}
}
