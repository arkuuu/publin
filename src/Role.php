<?php

namespace publin\src;

use InvalidArgumentException;

class Role {

	private $id;
	private $name;
	/**
	 * @var Permission[]
	 */
	private $permissions = array();


	public function __construct(array $data) {

		foreach ($data as $property => $value) {
			if (property_exists($this, $property)) {
				$this->$property = $value;
			}
		}
	}


	public function getId() {

		return $this->id;
	}


	public function getData() {

		return get_object_vars($this);
	}


	public function getName() {

		return $this->name;
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


	public function hasPermission($permission_id) {

		foreach ($this->permissions as $permission) {
			if ($permission->getId() == $permission_id) {
				return true;
			}
		}

		return false;
	}
}
