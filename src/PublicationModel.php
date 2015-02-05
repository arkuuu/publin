<?php

require_once 'AuthorModel.php';
require_once 'KeyTermModel.php';
require_once 'JournalModel.php';
require_once 'PublisherModel.php';
require_once 'StudyFieldModel.php';
require_once 'TypeModel.php';
require_once 'Publication.php';



class PublicationModel {

    private $db;
    private $num;


    public function __construct(Database $db) {

        $this->db = $db;
    }


    public function getNum() {

        return $this->num;
    }


    public function fetch($mode, array $filter = array()) {

        $publications = array();

        /* Gets the publications */
        $data = $this->db->fetchPublications($filter);
        $this->num = $this->db->getNumRows();

        foreach ($data as $key => $value) {
            $publication = new Publication($value);

            /* Gets the publications' authors */
            $model = new AuthorModel($this->db);
            $authors = $model->fetch(false, array('publication_id' => $publication->getId()));
            $publication->setAuthors($authors);

            if ($mode) {
                /* Gets the publications' key terms */
                $model = new KeyTermModel($this->db);
                $key_terms = $model->fetch(array('publication_id' => $publication->getId()));
                $publication->setKeyTerms($key_terms);
            }

            $publications[] = $publication;
        }

        return $publications;
    }


    public function validate(array $input) {

        $errors = array();

        // validation

        return $errors;
    }


    public function create(array $data, array $authors, array $key_terms) {

        // TODO: make to set new and return false if ID is detected.

        // validation here?
        $publication = new Publication($data);
        $publication->setAuthors($authors);
        $publication->setKeyTerms($key_terms);

        return $publication;
    }


    public function store(Publication $publication) {

        $data = $publication->getData();
        $authors = $publication->getAuthors();
        $key_terms = $publication->getKeyTerms();

        /* Stores the authors */
        $author_ids = array();
        $model = new AuthorModel($this->db);
        foreach ($authors as $author) {
            $author_ids[] = $model->store($author);
        }
        /* Stores the key terms */
        $key_term_ids = array();
        $model = new KeyTermModel($this->db);
        foreach ($key_terms as $key_term) {
            $key_term_ids[] = $model->store($key_term);
        }
        /* Stores the type */
        if (isset($data['type'])) {
            $model = new TypeModel($this->db);
            $type = $model->create(array('name' => $data['type']));
            $data['type_id'] = $model->store($type);
            unset($data['type']);
        }
        /* Stores the study field */
        if (isset($data['study_field'])) {
            $model = new StudyFieldModel($this->db);
            $study_field = $model->create(array('name' => $data['study_field']));
            $data['study_field_id'] = $model->store($study_field);
            unset($data['study_field']);
        }
        /* Stores the journal */
        if (isset($data['journal'])) {
            $model = new JournalModel($this->db);
            $journal = $model->create(array('name' => $data['journal']));
            $data['journal_id'] = $model->store($journal);
            unset($data['journal']);
        }
        /* Stores the publisher */
        if (isset($data['publisher'])) {
            $model = new PublisherModel($this->db);
            $publisher = $model->create(array('name' => $data['publisher']));
            $data['publisher_id'] = $model->store($publisher);
            unset($data['publisher']);
        }
        /* Stores the publication */
        $publication_id = $this->db->insertData('list_publications', $data);

        if (!empty($publication_id)) {

            /* Stores the relation between the publication and its authors */
            if (!empty($author_ids)) {
                $prio = 1; // TODO: really start with 1 and go up?
                foreach ($author_ids as $author_id) {
                    $data = array('publication_id' => $publication_id,
                                  'author_id'      => $author_id, 'priority' => $prio);
                    $insert = $this->db->insertData('rel_publ_to_authors', $data);
                    if (empty($insert)) {
                        throw new Exception('Error while inserting publ_author link to DB');

                    }
                    $prio++;
                }
            }
            /* Stores the relation between the publication and its key terms */
            if (!empty($key_term_ids)) {
                foreach ($key_term_ids as $key_term_id) {
                    $data = array('publication_id' => $publication_id,
                                  'key_term_id'    => $key_term_id);
                    $insert = $this->db->insertData('rel_publ_to_key_terms', $data);
                    if (empty($insert)) {
                        throw new Exception('Error while inserting publ_key_term link to DB');

                    }
                }
            }

            return $publication_id;
        }
        else {
            throw new Exception('Error while inserting publication to DB');

        }
    }


    public function update($id, array $data) {

    }


    public function delete($id) {

    }

}
