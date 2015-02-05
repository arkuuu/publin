<?php

require_once 'Type.php';


class TypeModel {


    private $db;
    private $num;


    public function __construct(Database $db) {

        $this->db = $db;
    }


    public function getNum() {

        return $this->num;
    }


    public function fetch(array $filter = array()) {

        $types = array();

        $data = $this->db->fetchTypes($filter);
        $this->num = $this->db->getNumRows();

        foreach ($data as $key => $value) {
            $types[] = new Type($value);
        }

        return $types;
    }


    public function validate(array $input) {

        $errors = array();

        // validation
        return $errors;
    }


    public function create(array $data) {

        // validation here?
        $type = new Type($data);

        return $type;
    }


    public function store(Type $type) {

        $data = $type->getData();
        $id = $this->db->insertData('list_types', $data);

        if (!empty($id)) {
            return $id;
        }
        else {
            throw new Exception('Error while inserting type to DB');

        }
    }


    public function update($id, array $data) {

    }


    public function delete($id) {

    }

}
