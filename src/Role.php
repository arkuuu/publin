<?php
/**
 * User: arkuuu
 * Date: 06.02.15
 * Time: 12:11
 */

namespace publin\src;

class Role extends Object {

	private $permissions = array();


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
