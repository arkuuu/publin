<?php

namespace publin\src;

class UserModel {

	private $db;
	private $num;


	public function __construct(Database $db) {

		$this->db = $db;
	}


	public function getNum() {

		return $this->num;
	}


	public function fetch($mode, array $filter = array()) {

		$users = array();

		$data = $this->db->fetchUsers($filter);
		$this->num = $this->db->getNumRows();

		foreach ($data as $key => $value) {

			if ($mode) {
				$model = new RoleModel($this->db);
				$roles = $model->fetch(false, array('user_id' => $value['id']));
			}
			else {
				$roles = array();
			}
			$users[] = new User($value, $roles);
		}

		return $users;
	}


	public function store(User $user) {

		// TODO
	}


	public function addRole($user_id, $role_id) {

		$data = array('user_id' => $user_id, 'role_id' => $role_id);
		$id = $this->db->insertData('rel_user_roles', $data);

		if ($id) {
			return true;
		}
		else {
			return false;
		}
	}


	public function removeRole($user_id, $role_id) {

		$where = array('user_id' => $user_id, 'role_id' => $role_id);
		$rows = $this->db->deleteData('rel_user_roles', $where);

		if ($rows == 1) {
			return true;
		}
		else {
			// TODO: proper error handling stuff here
			return false;
		}
	}
}
