<?php

namespace publin\src;

/**
 * Class TypeModel
 *
 * @package publin\src
 */
class TypeModel extends Model
{

    /**
     * @param Type $type
     *
     * @return string
     * @throws exceptions\DBDuplicateEntryException
     * @throws exceptions\DBForeignKeyException
     */
    public function store(Type $type)
    {
        $query = 'INSERT INTO types (name, description) VALUES (:name, :description);';
        $this->db->prepare($query);
        $this->db->bindValue(':name', $type->getName());
        $this->db->bindValue(':description', $type->getDescription());
        $this->db->execute();

        return $this->db->lastInsertId();
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
        $query = 'DELETE FROM types WHERE id = :id;';
        $this->db->prepare($query);
        $this->db->bindValue(':id', (int)$id);
        $this->db->execute();

        return $this->db->rowCount();
    }


    /**
     * @return Validator
     */
    public function getValidator()
    {
        $validator = new Validator();
        $validator->addRule('name', 'text', true, 'Name is required but invalid');

        return $validator;
    }
}
