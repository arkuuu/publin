<?php

namespace publin\src;

class Role extends Object {


	/**
	 * @var array
	 */
	private $permissions = array();


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
