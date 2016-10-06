<?php

namespace publin\src;

/**
 * Class CitationRepository
 *
 * @package publin\src
 */
class CitationRepository extends Repository
{

    public function reset()
    {
        parent::reset();
        $this->select = 'SELECT self.*';
        $this->from = 'FROM `citations` self';

        return $this;
    }


    /**
     * @return Citation[]
     */
    public function find()
    {
        $result = parent::find();
        $citations = array();

        foreach ($result as $row) {
            $citation = new Citation($row);

            $repo = new PublicationRepository($this->db);
            $citation->setCitationPublication($repo->where('id', '=', $citation->getCitationId())->findSingle());

            $citations[] = $citation;
        }

        return $citations;
    }
}
