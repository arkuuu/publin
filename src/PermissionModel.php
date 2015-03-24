<?php


namespace publin\src;

use InvalidArgumentException;

class PermissionModel {

	private $db;


	public function __construct(Database $db) {

		$this->db = $db;
	}


	public function fetchByRole($role_id) {

		if (!is_numeric($role_id)) {
			throw new InvalidArgumentException;
		}

		$query = 'SELECT
					  p.`id`,
					  p.`name`
					FROM `rel_roles_permissions` r JOIN `list_permissions` p
						ON p.`id` = r.`permission_id`
					WHERE r.`role_id` = '.$role_id.';';

		$data = $this->db->getData($query);
		$permissions = array();

		foreach ($data as $entry) {
			$permissions[] = new Permission($entry);
		}

		return $permissions;
	}
}
