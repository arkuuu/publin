<?php

namespace publin\src;

use InvalidArgumentException;
use RuntimeException;

class UserModel {

	private $db;
	private $num;


	public function __construct(Database $db) {

		$this->db = $db;
	}


	public function getNum() {

		return $this->num;
	}


	/**
	 * @param       $mode
	 * @param array $filter
	 *
	 * @return User[]
	 */
	public function fetch($mode, array $filter = array()) {

		$users = array();

		$data = $this->db->fetchUsers($filter);
		$this->num = $this->db->getNumRows();

		foreach ($data as $key => $value) {

			if ($mode) {
				$model = new RoleModel($this->db);
				$roles = $model->fetch(false, array('user_id' => $value['id']));
				// TODO: load permissions?
			}
			else {
				$roles = array();
			}
			$users[] = new User($value, $roles);
		}

		return $users;
	}


	public function fetchById($id) {

		if (!is_numeric($id)) {
			throw new InvalidArgumentException;
		}

		$query = 'SELECT * FROM `list_users` WHERE `id` ='.$id.';';
		$data = $this->db->getData($query);
		$data = $data[0];

		$model = new RoleModel($this->db);
		$roles = $model->fetch(false, array('user_id' => $id));
		$model = new PermissionModel($this->db);
		$permissions = array();

		foreach ($roles as $role) {
			$role_permissions = $model->fetchByRole($role->getId());
			foreach ($role_permissions as $role_permission) {
				if (!in_array($role_permission, $permissions)) {
					$permissions[] = $role_permission;
				}
			}
		}

		return new User($data, $roles, $permissions);
	}


	public function store(User $user) {

		$data = $user->getData();
		foreach ($data as $property => $value) {
			if (empty($value) || is_array($value)) {
				unset($data[$property]);
			}
		}
		$data['password'] = Auth::hashPassword(Auth::generatePassword());

		return $this->db->insert('list_users', $data);
	}


	public function update($id, array $data) {

		if (isset($data['password'])) {
			$data['password'] = Auth::hashPassword($data['password']);
		}

		return $this->db->updateData('list_users', array('id' => $id), $data);
	}


	public function addRole($user_id, $role_id) {

		$data = array('user_id' => $user_id, 'role_id' => $role_id);

		return $this->db->insertData('rel_user_roles', $data);
	}


	public function removeRole($user_id, $role_id) {

		$where = array('user_id' => $user_id, 'role_id' => $role_id);
		$rows = $this->db->deleteData('rel_user_roles', $where);

		// TODO: How to get rid of this and move it to DB?
		if ($rows == 1) {
			return true;
		}
		else {
			throw new RuntimeException('Error while removing role '.$role_id.' from user '.$user_id.': '.$this->db->error);
		}
	}


	public function delete($id) {

		if (!is_numeric($id)) {
			throw new InvalidArgumentException('param should be numeric');
		}

		$where = array('user_id' => $id);
		$this->db->deleteData('rel_user_roles', $where);

		$where = array('id' => $id);
		$rows = $this->db->deleteData('list_users', $where);

		// TODO: how to get rid of these?
		if ($rows == 1) {
			return true;
		}
		else {
			throw new RuntimeException('Error while deleting user '.$id.': '.$this->db->error);
		}
	}


	public function getValidator() {

		$validator = new Validator();
		$validator->addRule('name', 'text', true, 'Username is required but invalid');
		$validator->addRule('mail', 'email', true, 'Email address is required but invalid');
		$validator->addRule('active', 'boolean', false, 'Active is invalid');

		return $validator;
	}
}
