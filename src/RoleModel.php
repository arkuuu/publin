<?php

namespace publin\src;

use InvalidArgumentException;
use RuntimeException;

class RoleModel {

	private $db;
	private $num;


	public function __construct(Database $db) {

		$this->db = $db;
	}


	public function getNum() {

		return $this->num;
	}


	/**
	 * @param Role $role
	 *
	 * @return mixed
	 * @throws exceptions\SQLException
	 */
	public function store(Role $role) {

		// TODO: store permissions too?

		$data = $role->getData();
		foreach ($data as $property => $value) {
			if (empty($value) || is_array($value)) {
				unset($data[$property]);
			}
		}

		return $this->db->insertData('list_roles', $data);
	}


	/**
	 * @param $id
	 *
	 * @return bool
	 * @throws exceptions\SQLException
	 */
	public function delete($id) {

		//TODO: this only works when no foreign key constraints fail
		if (!is_numeric($id)) {
			throw new InvalidArgumentException('param should be numeric');
		}
		$where = array('id' => $id);
		$rows = $this->db->deleteData('list_roles', $where);

		// TODO: how to get rid of these?
		if ($rows == 1) {
			return true;
		}
		else {
			throw new RuntimeException('Error while deleting role '.$id.': '.$this->db->error);
		}
	}


	/**
	 * @param       $role_id
	 * @param array $permission_ids
	 *
	 * @return bool
	 */
	public function updatePermissions($role_id, array $permission_ids) {

		if (!is_numeric($role_id)) {
			throw new InvalidArgumentException('params should be numeric');
		}

		$old_permissions = $this->db->fetchPermissions(array('role_id' => $role_id));
		$old = array();
		foreach ($old_permissions as $old_permission) {
			$old[] = $old_permission['id'];
		}

		if (empty($permission_ids)) {
			$to_add = array();
			$to_delete = $old;
		}
		else {
			$to_add = array_diff($permission_ids, $old);
			$to_delete = array_diff($old, $permission_ids);
		}

		foreach ($to_add as $permission_id) {
			$this->addPermission($role_id, $permission_id);
		}
		foreach ($to_delete as $permission_id) {
			$this->removePermission($role_id, $permission_id);
		}

		return true;
	}


	/**
	 * @param $role_id
	 * @param $permission_id
	 *
	 * @return mixed
	 * @throws exceptions\SQLException
	 */
	public function addPermission($role_id, $permission_id) {

		if (!is_numeric($role_id) || !is_numeric($permission_id)) {
			throw new InvalidArgumentException('params should be numeric');
		}

		$data = array('role_id' => $role_id, 'permission_id' => $permission_id);

		return $this->db->insertData('rel_roles_permissions', $data);
	}


	/**
	 * @param $role_id
	 * @param $permission_id
	 *
	 * @return bool
	 * @throws exceptions\SQLException
	 */
	public function removePermission($role_id, $permission_id) {

		if (!is_numeric($role_id) || !is_numeric($permission_id)) {
			throw new InvalidArgumentException('params should be numeric');
		}

		$where = array('role_id' => $role_id, 'permission_id' => $permission_id);
		$rows = $this->db->deleteData('rel_roles_permissions', $where);

		// TODO: How to get rid of this and move it to DB?
		if ($rows == 1) {
			return true;
		}
		else {
			throw new RuntimeException('Error while removing permission '.$permission_id.' from role '.$role_id.': '.$this->db->error);
		}
	}


	/**
	 * @param       $mode
	 * @param array $filter
	 *
	 * @return Role[]
	 */
	public function fetch($mode, array $filter = array()) {

		$roles = array();

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
			$roles[] = $role;
		}

		return $roles;
	}
}
