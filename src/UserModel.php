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

		//TODO: this only works when no foreign key constraints fail
		if (!is_numeric($id)) {
			throw new InvalidArgumentException('param should be numeric');
		}
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
}
