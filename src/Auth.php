<?php

namespace publin\src;

class Auth {

	private $db;
	private $session_expire_time = 1000;


	public function __construct(Database $db) {

		$this->db = $db;

		if (!isset($_SESSION)) {
			session_start();
		}
	}


	public function login($user_name, $password) {

		$user_name = $this->db->real_escape_string($user_name);
		$password = $this->db->real_escape_string($password);
		// TODO: use password hash instead of clear password!

		$query = 'SELECT `id`, `name` FROM `list_users` WHERE `name` = "'.$user_name.'" AND `password` = "'.$password.'";';
		$result = $this->db->getData($query);

		if ($this->db->getNumRows() == 1) {

			//TODO: write Login date to DB
			$user = new User($result[0]);
			$user->setPermissions($this->getPermissions($user));

			session_regenerate_id(true);
			$_SESSION['user'] = $user;
			$_SESSION['created'] = time();
			$_SESSION['last_activity'] = time();

			return true;
		}
		else {
			return false;
		}

	}


	public function getPermissions(User $user) {

		$user_id = $this->db->real_escape_string($user->getId());

		$query = 'SELECT DISTINCT(r.`name`) FROM `list_permissions` r
		LEFT JOIN `rel_roles_permissions` rrp ON (rrp.`permission_id` = r.`id`)
		LEFT JOIN `rel_user_roles` rur ON (rur.`role_id` = rrp.`role_id`)
		WHERE rur.`user_id` = '.$user_id.';';

		$permissions = $this->db->getData($query);

		return $permissions;
	}


	public function checkPermission($permission_name) {

		if ($this->checkLoginStatus()) {

			/* @var $user User */
			$user = $_SESSION['user'];
			$permission_name = $this->db->real_escape_string($permission_name);
			$user_id = $this->db->real_escape_string($user->getId());

			$query = 'SELECT DISTINCT(r.`name`) FROM `list_permissions` r
			LEFT JOIN `rel_roles_permissions` rrp ON (rrp.`permission_id` = r.`id`)
			LEFT JOIN `rel_user_roles` rur ON (rur.`role_id` = rrp.`role_id`)
			WHERE r.`name` = "'.$permission_name.'" AND rur.`user_id` = '.$user_id.';';

			$this->db->getData($query);

			if ($this->db->getNumRows() == 1) {
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


	public function checkLoginStatus() {

		if (isset($_SESSION['user']) && isset($_SESSION['last_activity'])) {
			if (time() - $_SESSION['last_activity'] > $this->session_expire_time) {
				$this->logout();

				return false;
			}
			else {
				$_SESSION['last_activity'] = time();

				return true;
			}
		}
		else {
			return false;
		}
	}


	public function logout() {

		session_unset();
		session_destroy();
		session_start();
	}
}
