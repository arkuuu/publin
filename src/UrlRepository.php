<?php

namespace arkuuu\Publin;

/**
 * Class UrlRepository
 *
 * @package arkuuu\Publin
 */
class UrlRepository extends Repository
{


    public function reset()
    {
        parent::reset();
        $this->select = 'SELECT self.*';
        $this->from = 'FROM `urls` self';

        return $this;
    }


    /**
     * @return Url[]
     */
    public function find()
    {
        $result = parent::find();
        $urls = array();

        foreach ($result as $row) {
            $urls[] = new Url($row);
        }

        return $urls;
    }


    /**
     * @return Url|false
     */
    public function findSingle()
    {
        $result = parent::findSingle();

        if ($result) {
            return new Url($result);
        } else {
            return false;
        }
    }
}
