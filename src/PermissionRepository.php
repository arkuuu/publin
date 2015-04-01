<?php


namespace publin\src;

class PermissionRepository extends QueryBuilder {


	public function select($distinct = false) {

		$distinct = ($distinct === true) ? ' DISTINCT' : '';
		$this->select = 'SELECT'.$distinct.' self.*';
		$this->from = 'FROM `list_permissions` self';

		return $this;
	}


	public function where($column, $comparator, $value, $function = null) {

		if ($column === 'role_id') {
			$table = 'rel_roles_permissions';
			$this->join .= ' LEFT JOIN `rel_roles_permissions` ON (`rel_roles_permissions`.`permission_id` = self.`id`)';
		}
		else if ($column === 'user_id') {
			$table = 'rel_user_roles';
			$this->join .= ' LEFT JOIN `rel_roles_permissions` ON (`rel_roles_permissions`.`permission_id` = self.`id`)';
			$this->join .= ' LEFT JOIN `rel_user_roles` ON (`rel_user_roles`.`role_id` = `rel_roles_permissions`.`role_id`)';
		}
		else {
			$table = 'self';
		}

		return parent::where($column, $comparator, $value, $function, $table);
	}


	/**
	 * @return Permission[]
	 */
	public function find() {

		$result = parent::find();
		$permissions = array();

		foreach ($result as $row) {
			$permissions[] = new Permission($row);
		}

		return $permissions;
	}


	/**
	 * @return Permission
	 */
	public function findSingle() {

		$result = parent::findSingle();

		return new Permission($result);
	}
}
