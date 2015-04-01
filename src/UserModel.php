<?php

namespace publin\src;

use InvalidArgumentException;
use RuntimeException;

class UserModel {

	private $old_db;
	private $num;


	public function __construct(Database $db) {

		$this->old_db = $db;
	}


	public function getNum() {

		return $this->num;
	}


	public function store(User $user) {

		$data = $user->getData();
		foreach ($data as $property => $value) {
			if (empty($value) || is_array($value)) {
				unset($data[$property]);
			}
		}
		$data['password'] = Auth::hashPassword(Auth::generatePassword());

		return $this->old_db->insert('list_users', $data);
	}


	public function update($id, array $data) {

		if (isset($data['password'])) {
			$data['password'] = Auth::hashPassword($data['password']);
		}

		return $this->old_db->updateData('list_users', array('id' => $id), $data);
	}


	public function addRole($user_id, $role_id) {

		$data = array('user_id' => $user_id, 'role_id' => $role_id);

		return $this->old_db->insertData('rel_user_roles', $data);
	}


	public function removeRole($user_id, $role_id) {

		$where = array('user_id' => $user_id, 'role_id' => $role_id);
		$rows = $this->old_db->deleteData('rel_user_roles', $where);

		// TODO: How to get rid of this and move it to DB?
		if ($rows == 1) {
			return true;
		}
		else {
			throw new RuntimeException('Error while removing role '.$role_id.' from user '.$user_id.': '.$this->old_db->error);
		}
	}


	public function delete($id) {

		if (!is_numeric($id)) {
			throw new InvalidArgumentException('param should be numeric');
		}

		$where = array('user_id' => $id);
		$this->old_db->deleteData('rel_user_roles', $where);

		$where = array('id' => $id);
		$rows = $this->old_db->deleteData('list_users', $where);

		// TODO: how to get rid of these?
		if ($rows == 1) {
			return true;
		}
		else {
			throw new RuntimeException('Error while deleting user '.$id.': '.$this->old_db->error);
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
