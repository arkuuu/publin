<?php

namespace publin\src;

use InvalidArgumentException;
use PDOException;

class UserModel extends Model {

	private $old_db;
	private $db;
	private $num;


	public function __construct(Database $db) {

		$this->old_db = new OldDatabase();
		$this->db = $db;
	}


	public function getNum() {

		return $this->num;
	}


	public function store(User $user) {

		$query = 'INSERT INTO `users` (`name`, `mail`) VALUES (:name, :mail);';
		$this->db->prepare($query);
		$this->db->bindValue(':name', $user->getName());
		$this->db->bindValue(':mail', $user->getMail());
		$this->db->execute();

		return $this->db->lastInsertId();
	}


	public function update($id, array $data) {


		return $this->old_db->updateData('users', array('id' => $id), $data);
	}


	public function addRole($user_id, $role_id) {

		if (!is_numeric($user_id) || !is_numeric($role_id)) {
			throw new InvalidArgumentException('param should be numeric');
		}

		$query = 'INSERT INTO `users_roles` (`user_id`, `role_id`) VALUES (:user_id, :role_id);';
		$this->db->prepare($query);
		$this->db->bindValue(':user_id', (int)$user_id);
		$this->db->bindValue(':role_id', (int)$role_id);
		$this->db->execute();

		return $this->db->lastInsertId();
	}


	public function removeRole($user_id, $role_id) {

		if (!is_numeric($user_id) || !is_numeric($role_id)) {
			throw new InvalidArgumentException('param should be numeric');
		}

		$query = 'DELETE FROM `users_roles` WHERE `user_id` = :user_id AND `role_id` = :role_id;';
		$this->db->prepare($query);
		$this->db->bindValue(':user_id', (int)$user_id);
		$this->db->bindValue(':role_id', (int)$role_id);
		$this->db->execute();

		return $this->db->rowCount();
	}


	public function delete($id) {

		if (!is_numeric($id)) {
			throw new InvalidArgumentException('param should be numeric');
		}

		$this->db->beginTransaction();

		try {
			$query = 'DELETE FROM `users_roles` WHERE `user_id` = :user_id;';
			$this->db->prepare($query);
			$this->db->bindValue(':user_id', (int)$id);
			$this->db->execute();

			$query = 'DELETE FROM `users` WHERE `id` = :id;';
			$this->db->prepare($query);
			$this->db->bindValue(':id', (int)$id);
			$this->db->execute();
			$row_count = $this->db->rowCount();

			$this->db->commitTransaction();

			return $row_count;
		}
		catch (PDOException $e) {
			$this->db->cancelTransaction();
			throw $e;
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
