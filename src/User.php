<?php

require_once 'Object.php';

class User extends Object {

	private $role;
	private $permissions;
	
	public function getPassword() {
		return $this -> getData('password');
	}

	public function getRole() {
		return $this -> role;
	}

	public function setRole($role) {
		$this -> role = $role;
	}

	public function getPermissions() {
		return $this -> permissions;
	}

	public function setPermissions(array $permissions) {
		$this -> permissions = $permissions;
	}
}
