<?php

namespace publin\src\indices\implementations;

use PDO;
use publin\src\Database;
use publin\src\indices\AbstractIndex;
use publin\src\indices\exceptions\IndexDataException;
use publin\src\indices\other\IndexHelper;

/**
 * This class implements the well-known h-index as defined
 * by Jorge Eduardo Hirsch in this publication:
 * http://dx.doi.org/10.1073/pnas.0507655102
 *
 * @package publin\src\indices\implementations
 */
class HIndex extends AbstractIndex
{

    /**
     * {@inheritDoc}
     */
    public function __construct(Database $db)
    {
        parent::__construct($db);

        $this->name = 'h-index';

        $this->dataFormat = array(
            'publications' => array(
                'int' => array(
                    'publicationId' => 'int',
                    'citationCount' => 'int',
                ),
            ),
        );
    }


    /**
     * {@inheritDoc}
     */
    protected function fetchData()
    {
        parent::fetchData();

        $query = '
            SELECT
                pub_auth.publication_id AS publicationId,
                COUNT(cit.id) AS citationCount
            FROM publications_authors pub_auth
            LEFT JOIN citations cit ON (pub_auth.publication_id = cit.publication_id)
            WHERE pub_auth.author_id = :authorId
            GROUP BY publicationId
            ORDER BY citationCount DESC
        ';
        $statement = $this->db->prepare($query);
        $statement->bindValue(
            ':authorId',
            $this->parameters['authorId']['value'],
            PDO::PARAM_INT
        );
        $statement->execute();

        $data = array();
        $data['publications'] = $statement->fetchAll(PDO::FETCH_ASSOC);
        $data = IndexHelper::convertWrongDataTypes(
            $this->dataFormat['publications']['int'],
            $data,
            2,
            0
        );

        $this->setData($data);
    }


    /**
     * {@inheritDoc}
     */
    public function setData(array $data)
    {
        $this->checkPublicationsData($data);

        $this->data = $data;
    }


    /**
     * Checks if the provided publications data is correct.
     *
     * @param array $data Contains the data of all publications.
     *
     * @throws IndexDataException If $data doesn't contain valid
     * publications with the required attributes 'publicationId'
     * and 'citationCount'.
     */
    private function checkPublicationsData(array $data)
    {
        IndexHelper::checkArrayKeysExist(array_keys($this->dataFormat), $data, 0, 0);
        IndexHelper::checkDataTypesAreCorrect($this->dataFormat, $data, 3, 0, 0);
        IndexHelper::checkDataTypesAreCorrect(
            array('int'),
            $data,
            1,
            1,
            0,
            array(0 => array('publications'))
        );
        IndexHelper::checkDataTypesAreCorrect(
            array('array'),
            $data,
            2,
            1,
            0,
            array(0 => array('publications'))
        );
        IndexHelper::checkArrayKeysExist(
            array_keys($this->dataFormat['publications']['int']),
            $data,
            2,
            0,
            array(0 => array('publications'))
        );
        IndexHelper::checkDataTypesAreCorrect(
            $this->dataFormat['publications']['int'],
            $data,
            3,
            2,
            0,
            array(0 => array('publications'))
        );
    }


    /**
     * {@inheritDoc}
     */
    protected function calculateValue()
    {
        $value = 0;

        $publicationNumber = 1;
        foreach ($this->data['publications'] as $publication) {
            if ($publication['citationCount'] >= $publicationNumber) {
                $value = $publicationNumber;
            } else {
                break;
            }

            $publicationNumber++;
        }

        $this->value = $value;
    }
}
