<?php

namespace publin\src;

use Exception;

class RoleModel {

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

		$data = $this->db->fetchRoles($filter);
		$this->num = $this->db->getNumRows();

		foreach ($data as $key => $value) {

			if ($mode) {
				// TODO: replace with permission model and objects
				$permissions = $this->db->fetchPermissions(array('role_id' => $value['id']));
			}
			else {
				$permissions = array();
			}
			$role = new Role($value);
			$role->setPermissions($permissions);
			$users[] = $role;
		}

		return $users;
	}


	public function store(Role $role) {

		// TODO: store permissions too?

		$data = $role->getData();
		$id = $this->db->insertData('list_roles', $data);

		if (!empty($id)) {
			return $id;
		}
		else {
			throw new Exception('Error while inserting role to DB');

		}
	}


	public function delete($role_id) {

		$where = array('id' => $role_id);
		$rows = $this->db->deleteData('list_roles', $where);

		if ($rows == 1) {
			return true;
		}
		else {
			// TODO: proper error handling stuff here
			print_r($this->db->error);

			return false;
		}
	}


	public function addPermission($role_id, $permission_id) {

		$data = array('role_id' => $role_id, 'permission_id' => $permission_id);
		$id = $this->db->insertData('rel_roles_permissions', $data);

		if ($id) {
			return true;
		}
		else {
			return false;
		}
	}


	public function removePermission($role_id, $permission_id) {

		$where = array('role_id' => $role_id, 'permission_id' => $permission_id);
		$rows = $this->db->deleteData('rel_roles_permissions', $where);

		if ($rows == 1) {
			return true;
		}
		else {
			// TODO: proper error handling stuff here
			return false;
		}
	}
}
