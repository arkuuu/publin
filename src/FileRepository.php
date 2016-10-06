<?php

namespace publin\src;

/**
 * Class FileRepository
 *
 * @package publin\src
 */
class FileRepository extends Repository
{


    public function reset()
    {
        parent::reset();
        $this->select = 'SELECT self.*';
        $this->from = 'FROM `files` self';

        return $this;
    }


    /**
     * @return File[]
     */
    public function find()
    {
        $result = parent::find();
        $files = array();

        foreach ($result as $row) {
            $files[] = new File($row);
        }

        return $files;
    }


    /**
     * @return File|false
     */
    public function findSingle()
    {
        $result = parent::findSingle();

        if ($result) {
            return new File($result);
        } else {
            return false;
        }
    }
}
