<?php


namespace publin\src;

class UserRepository extends QueryBuilder {


	public function select() {

		$this->select = 'SELECT self.*';
		$this->from = 'FROM `users` self';

		return $this;
	}


	/**
	 * @param bool $full
	 *
	 * @return User[]
	 */
	public function find($full = false) {

		$result = parent::find();
		$users = array();

		foreach ($result as $row) {
			$user = new User($row);

			if ($full === true) {
				$repo = new RoleRepository($this->db);
				$user->setRoles($repo->select()->where('user_id', '=', $user->getId())->order('name', 'ASC')->find());
			}
			$users[] = $user;
		}

		return $users;
	}


	/**
	 * @param bool $full
	 *
	 * @return User
	 */
	public function findSingle($full = false) {

		$result = parent::findSingle();
		$user = new User($result);

		if ($full === true) {
			$repo = new RoleRepository($this->db);
			$user->setRoles($repo->select()->where('user_id', '=', $user->getId())->order('name', 'ASC')->find());
		}

		return $user;
	}
}
