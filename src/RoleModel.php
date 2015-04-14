<?php

namespace publin\src;

use InvalidArgumentException;

class RoleModel extends Model {

	private $db;


	public function __construct(PDODatabase $db) {

		$this->db = $db;
	}


	public function store(Role $role) {

		$query = 'INSERT INTO `roles` (`name`) VALUES (:name);';
		$this->db->prepare($query);
		$this->db->bindValue(':name', $role->getName());
		$this->db->execute();

		return $this->db->lastInsertId();
	}


	/**
	 * @param $id
	 *
	 * @return bool
	 * @throws exceptions\DBException
	 */
	public function delete($id) {

		if (!is_numeric($id)) {
			throw new InvalidArgumentException('param should be numeric');
		}

		$query = 'DELETE FROM `roles` WHERE `id` = :id;';
		$this->db->prepare($query);
		$this->db->bindValue(':id', (int)$id);
		$this->db->execute();

		return $this->db->rowCount();
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

		$repo = new PermissionRepository($this->db);
		$old_permissions = $repo->select()->where('role_id', '=', $role_id)->order('name', 'ASC')->find();

		$old = array();
		foreach ($old_permissions as $old_permission) {
			$old[] = $old_permission->getId();
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
	 * @throws exceptions\DBException
	 */
	public function addPermission($role_id, $permission_id) {

		if (!is_numeric($role_id) || !is_numeric($permission_id)) {
			throw new InvalidArgumentException('params should be numeric');
		}

		$query = 'INSERT INTO `roles_permissions` (`role_id`, `permission_id`) VALUES (:role_id, :permission_id);';
		$this->db->prepare($query);
		$this->db->bindValue(':role_id', (int)$role_id);
		$this->db->bindValue(':permission_id', (int)$permission_id);
		$this->db->execute();

		return $this->db->lastInsertId();
	}


	/**
	 * @param $role_id
	 * @param $permission_id
	 *
	 * @return bool
	 * @throws exceptions\DBException
	 */
	public function removePermission($role_id, $permission_id) {

		if (!is_numeric($role_id) || !is_numeric($permission_id)) {
			throw new InvalidArgumentException('params should be numeric');
		}

		$query = 'DELETE FROM `roles_permissions` WHERE `role_id` = :role_id AND `permission_id` = :permission_id;';
		$this->db->prepare($query);
		$this->db->bindValue(':role_id', (int)$role_id);
		$this->db->bindValue(':permission_id', (int)$permission_id);
		$this->db->execute();

		return $this->db->rowCount();
	}


	public function getValidator() {

		$validator = new Validator();
		$validator->addRule('name', 'text', true, 'Role name is required but invalid');

		return $validator;
	}
}
