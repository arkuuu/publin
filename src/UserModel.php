<?php

namespace publin\src;

use InvalidArgumentException;
use PDOException;

/**
 * Class UserModel
 *
 * @package publin\src
 */
class UserModel extends Model
{

    /**
     * @param User $user
     *
     * @return string
     * @throws exceptions\DBDuplicateEntryException
     * @throws exceptions\DBForeignKeyException
     */
    public function store(User $user)
    {
        $query = 'INSERT INTO users (name, mail) VALUES (:name, :mail);';
        $this->db->prepare($query);
        $this->db->bindValue(':name', $user->getName());
        $this->db->bindValue(':mail', $user->getMail());
        $this->db->execute();

        return $this->db->lastInsertId();
    }


    /**
     * @param       $id
     * @param array $data
     *
     * @return int
     */
    public function update($id, array $data)
    {
        $old_db = new OldDatabase();

        return $old_db->updateData('users', array('id' => $id), $data);
    }


    /**
     * @param $user_id
     * @param $role_id
     *
     * @return string
     * @throws exceptions\DBDuplicateEntryException
     * @throws exceptions\DBForeignKeyException
     */
    public function addRole($user_id, $role_id)
    {
        if (!is_numeric($user_id) || !is_numeric($role_id)) {
            throw new InvalidArgumentException('param should be numeric');
        }

        $query = 'INSERT INTO users_roles (user_id, role_id) VALUES (:user_id, :role_id);';
        $this->db->prepare($query);
        $this->db->bindValue(':user_id', (int)$user_id);
        $this->db->bindValue(':role_id', (int)$role_id);
        $this->db->execute();

        return $this->db->lastInsertId();
    }


    /**
     * @param $user_id
     * @param $role_id
     *
     * @return int
     * @throws exceptions\DBDuplicateEntryException
     * @throws exceptions\DBForeignKeyException
     */
    public function removeRole($user_id, $role_id)
    {
        if (!is_numeric($user_id) || !is_numeric($role_id)) {
            throw new InvalidArgumentException('param should be numeric');
        }

        $query = 'DELETE FROM users_roles WHERE user_id = :user_id AND role_id = :role_id;';
        $this->db->prepare($query);
        $this->db->bindValue(':user_id', (int)$user_id);
        $this->db->bindValue(':role_id', (int)$role_id);
        $this->db->execute();

        return $this->db->rowCount();
    }


    /**
     * @param $id
     *
     * @return int
     * @throws exceptions\DBDuplicateEntryException
     * @throws exceptions\DBForeignKeyException
     */
    public function delete($id)
    {
        if (!is_numeric($id)) {
            throw new InvalidArgumentException('param should be numeric');
        }

        $this->db->beginTransaction();

        try {
            $query = 'DELETE FROM users_roles WHERE user_id = :user_id;';
            $this->db->prepare($query);
            $this->db->bindValue(':user_id', (int)$id);
            $this->db->execute();

            $query = 'DELETE FROM users WHERE id = :id;';
            $this->db->prepare($query);
            $this->db->bindValue(':id', (int)$id);
            $this->db->execute();
            $row_count = $this->db->rowCount();

            $this->db->commitTransaction();

            return $row_count;
        } catch (PDOException $e) {
            $this->db->cancelTransaction();
            throw $e;
        }
    }


    /**
     * @return Validator
     */
    public function getValidator()
    {
        $validator = new Validator();
        $validator->addRule('name', 'text', true, 'Username is required but invalid');
        $validator->addRule('mail', 'email', true, 'Email address is required but invalid');
        $validator->addRule('active', 'boolean', false, 'Active is invalid');

        return $validator;
    }
}
