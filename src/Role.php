<?php

namespace publin\src;

class Role {

	private $id;
	private $name;
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
	 * @return array
	 */
	public function getPermissions() {

		return $this->permissions;
	}


	public function setPermissions(array $permissions) {

		$this->permissions = $permissions;
	}


	public function hasPermission($permission_id) {

		foreach ($this->permissions as $permission) {
			if ($permission['id'] == $permission_id) {
				return true;
			}
		}

		return false;
	}
}
