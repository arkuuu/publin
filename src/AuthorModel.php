<?php

require_once 'PublicationModel.php';
require_once 'Author.php';


class AuthorModel {


    private $db;
    private $num;


    public function __construct(Database $db) {

        $this->db = $db;
    }


    public function getNum() {

        return $this->num;
    }


    public function fetch($mode, array $filter = array()) {

        $authors = array();

        /* Gets the authors */
        $data = $this->db->fetchAuthors($filter);
        $this->num = $this->db->getNumRows();

        foreach ($data as $key => $value) {
            $author = new Author($value);

            if ($mode) {
                /* Gets the authors' publications */
                $model = new PublicationModel($this->db);
                $publications = $model->fetch(false, array('author_id' => $author->getId()));
                $author->setPublications($publications);
            }

            $authors[] = $author;
        }

        return $authors;
    }


    public function validate(array $input) {

        // TODO not sure if useful
        $allowed_fields = array('given', 'family');

        foreach ($input as $key => $value) {
            if (!in_array($key, $allowed_fields)) {
                return false;
            }
        }

        return true;
    }


    public function create(array $data) {

        $author = new Author($data);

        return $author;
    }


    public function store(Author $author) {

        // validation here?
        $data = $author->getData();
        $id = $this->db->insertData('list_authors', $data);

        if (!empty($id)) {
            return $id;
        }
        else {
            throw new Exception('Error while inserting author to DB');

        }
    }


    public function update($id, array $data) {

    }


    public function delete($id) {

    }

}
