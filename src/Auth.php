<?php

namespace publin\src;

use publin\src\exceptions\LoginRequiredException;

class Auth {

	const ACCESS_RESTRICTED_FILES = 'access_restricted_files';
	const ACCESS_HIDDEN_FILES = 'access_hidden_files';
	const EDIT_PUBLICATION = 'publication_edit';
	const SUBMIT_PUBLICATION = 'publication_submit';
	const EDIT_AUTHOR = 'author_edit';
	const EDIT_KEYWORD = 'keyword_edit';
	const MANAGE = 'manage';

	private $old_db;
	private $db;
	private $session_expire_time = 1000;


	public function __construct(Database $db) {

		$this->old_db = $db;
		$this->db = new PDODatabase();

		if (!isset($_SESSION)) {
			session_start();
		}
	}


	/**
	 * @return string
	 */
	public static function generatePassword() {

		$chars = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
		$password_length = 10;
		$password = '';

		for ($i = 0; $i < $password_length; $i++) {
			$n = rand(0, strlen($chars) - 1);
			$password .= $chars[$n];
		}

		return $password;
	}


	/**
	 * @return bool|User
	 * @throws LoginRequiredException
	 */
	public static function getCurrentUser() {

		if (isset($_SESSION['user'])) {
			return $_SESSION['user'];
		}
		else {
			throw new LoginRequiredException();
		}
	}


	/**
	 * @param $username
	 * @param $password
	 *
	 * @return bool
	 * @throws exceptions\SQLException
	 */
	public function login($username, $password) {

		$username = $this->old_db->real_escape_string($username);
		$password = $this->old_db->real_escape_string($password);
		$password = $this->hashPassword($password);

		$query = 'SELECT `id`, `name` FROM `users` WHERE `name` = "'.$username.'" AND `password` = "'.$password.'";';
		$result = $this->old_db->getData($query);

		if ($this->old_db->getNumRows() == 1) {

			$user = new User($result[0]);
			$user->setPermissions($this->getPermissions($user));

			session_regenerate_id(true);
			$_SESSION['user'] = $user;
			$_SESSION['last_activity'] = time();

			$query = 'UPDATE `users` SET `date_last_login` = NOW() WHERE `id` = '.$user->getId().';';
			$this->old_db->changeToWriteUser();
			$this->old_db->query($query);

			return true;
		}
		else {
			return false;
		}
	}


	/**
	 * @param $password
	 *
	 * @return mixed
	 */
	public static function hashPassword($password) {

		// TODO: implement
		return $password;
	}


	/**
	 * @param User $user
	 *
	 * @return array
	 */
	public function getPermissions(User $user) {

		$repo = new PermissionRepository($this->db);
		$permissions = $repo->select(true)->where('user_id', '=', $user->getId())->find();

		return $permissions;
	}


	public function validateLogin($username, $password) {

		$username = $this->old_db->real_escape_string($username);
		$password = $this->old_db->real_escape_string($password);
		$password = $this->hashPassword($password);

		$query = 'SELECT `id`, `name` FROM `users` WHERE `name` = "'.$username.'" AND `password` = "'.$password.'";';
		$this->old_db->getData($query);

		if ($this->old_db->getNumRows() == 1) {

			return true;
		}
		else {
			return false;
		}
	}


	/**
	 * @param $permission_name
	 *
	 * @return bool
	 * @throws LoginRequiredException
	 */
	public function checkPermission($permission_name) {

		if ($this->checkLoginStatus()) {

			/* @var $user User */
			$user = $_SESSION['user'];
			$permission_name = $this->old_db->real_escape_string($permission_name);
			$user_id = $this->old_db->real_escape_string($user->getId());

			$query = 'SELECT DISTINCT(r.`name`) FROM `permissions` r
			LEFT JOIN `roles_permissions` rrp ON (rrp.`permission_id` = r.`id`)
			LEFT JOIN `users_roles` rur ON (rur.`role_id` = rrp.`role_id`)
			WHERE r.`name` = "'.$permission_name.'" AND rur.`user_id` = '.$user_id.';';

			$this->old_db->getData($query);

			if ($this->old_db->getNumRows() == 1) {
				return true;
			}
			else {
				return false;
			}
		}
		else {
			throw new LoginRequiredException();
		}
	}


	/**
	 * @return bool
	 */
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


	/**
	 *
	 */
	public function logout() {

		session_unset();
		session_destroy();
		session_start();
	}
}
