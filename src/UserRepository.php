<?php


namespace publin\src;

/**
 * Class UserRepository
 *
 * @package publin\src
 */
class UserRepository extends Repository {


    public function reset()
    {
        parent::reset();
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
				$user->setRoles($repo->where('user_id', '=', $user->getId())->order('name', 'ASC')->find());

				$repo = new PermissionRepository($this->db);
				$user->setPermissions($repo->where('user_id', '=', $user->getId())->order('name', 'ASC')->find());
			}
			$users[] = $user;
		}

		return $users;
	}


	/**
	 * @param bool $full
	 *
	 * @return User|false
	 */
	public function findSingle($full = false) {

        $result = parent::findSingle();

		if ($result) {
			$user = new User($result);

			if ($full === true) {
				$repo = new RoleRepository($this->db);
				$user->setRoles($repo->where('user_id', '=', $user->getId())->order('name', 'ASC')->find());

				$repo = new PermissionRepository($this->db);
				$user->setPermissions($repo->where('user_id', '=', $user->getId())->order('name', 'ASC')->find());
			}

			return $user;
		}
		else {
			return false;
		}
	}
}
