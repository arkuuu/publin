<?php

require_once 'User.php';

class Auth {

	private $db;



	public function __construct(Database $db) {
		$this -> db = $db;

		if (!isset($_SESSION)) {
			session_start();
		}
	}


	public function validateLogin($user_name, $password) {
		$user_name = $this -> db -> real_escape_string($user_name);
		$password = $this -> db -> real_escape_string($password);
		// TODO: use password hash instead of clear password!

		$query = 'SELECT `id`, `name` FROM `list_users` WHERE `name` = "'.$user_name.'" AND `password` = "'.$password.'";';
		$result = $this -> db -> getData($query);

		if ($this -> db -> getNumRows() == 1) {
			$user = new User($result[0]);
			$_SESSION['user'] = $user;
			$user -> setPermissions($this -> getPermissions($user));
			return true;
		}
		else {
			return false;
		}

	}


	public function checkLoginStatus() {
		if (isset($_SESSION['user'])) {
			return true;
		}
		else {
			return false;
		}
	}


	public function checkPermission($permission_name) {

		if ($this -> checkLoginStatus()) {

			$permission_name = $this -> db -> real_escape_string($permission_name);
			$user_id = $this -> db -> real_escape_string($_SESSION['user'] -> getId());

			$query = 'SELECT DISTINCT(r.`name`) FROM `list_permissions` r 
			LEFT JOIN `rel_roles_permissions` rrp ON (rrp.`permission_id` = r.`id`)
			LEFT JOIN `rel_user_roles` rur ON (rur.`role_id` = rrp.`role_id`)
			WHERE r.`name` = "'.$permission_name.'" AND rur.`user_id` = '.$user_id.';';

			$this -> db -> getData($query);

			if ($this -> db -> getNumRows() == 1) {
				return true;
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}

	public function getPermissions(User $user) {

		$user_id = $this -> db -> real_escape_string($user -> getId());

		$query = 'SELECT DISTINCT(r.`name`) FROM `list_permissions` r 
		LEFT JOIN `rel_roles_permissions` rrp ON (rrp.`permission_id` = r.`id`)
		LEFT JOIN `rel_user_roles` rur ON (rur.`role_id` = rrp.`role_id`)
		WHERE rur.`user_id` = '.$user_id.';';

		$permissions = $this -> db -> getData($query);

		return $permissions;
	}


	public function logout() {
		session_destroy();
		session_start();
	}
}
