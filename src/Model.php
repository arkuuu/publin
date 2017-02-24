<?php

namespace arkuuu\Publin;

/**
 * Class Model
 *
 * @package arkuuu\Publin
 */
class Model
{

    /**
     * @var Database
     */
    protected $db;


    /**
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }
}
