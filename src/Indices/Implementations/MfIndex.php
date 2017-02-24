<?php

namespace arkuuu\Publin\Indices\Implementations;

use arkuuu\Publin\Database;
use arkuuu\Publin\Indices\AbstractIndex;
use arkuuu\Publin\Indices\Exceptions\IndexDataException;
use arkuuu\Publin\Indices\Exceptions\IndexException;
use arkuuu\Publin\Indices\Other\IndexHelper;
use arkuuu\Publin\Indices\Other\NormalizationFactors;
use PDO;
use PDOStatement;

/**
 * This class implements the mf-index.
 *
 * It may either be used just by setting the required parameter
 * 'authorId' and using the default values for the other parameters
 * or by explicitly setting some of the other parameters to change
 * the weighting of some index properties or to completely disable
 * some factors like the field of study correction.
 *
 * @package arkuuu\Publin\Indices\Implementations
 */
class MfIndex extends AbstractIndex
{

    /**
     * {@inheritDoc}
     */
    public function __construct(Database $db)
    {
        parent::__construct($db);

        $this->name = 'mf-index';
        $this->defineParameters();
        $this->defineDataFormat();
    }


    /**
     * Defines all available parameters of the mf-index.
     *
     * Because of the huge amount of parameters, the parameter definition
     * has been transfered from the constructor to this method to ensure
     * the readability.
     *
     * The index uses two groups of parameters: The first one are on-off
     * switches. They can be used to switch nearly every property of the
     * index on and off and to limit the calculation to a specific period.
     * By default, all index properties are enabled and the limitation to
     * a period is disabled. This means that with no further configuration
     * all properties from the index definition are used and that the whole
     * career length of the scientist is considered.
     *
     * The second group of parameters consists of numeric values which allow
     * the configuration of periods, scaling factors, thresholds and weightings.
     * Their default values are based on the index definition. An overview of
     * all index parameters, together with their default values and other
     * information can be obtained by using the getAvailableParameters() method.
     */
    private function defineParameters()
    {
        /*
         * Many parameters have the same data type and the same default
         * value. Therefore the definition of the parameters can be written
         * more compact by grouping the parameters in the first step and
         * defining them depending on their group in the second step.
         */
        $parameterGroups = array(
            'onOffSwitchesForPeriods'    => array(
                'names'    => array(
                    'enableStartYear',
                    'enableEndYear',
                ),
                'dataType' => 'bool',
                'from'     => null,
                'to'       => null,
                'required' => false,
                'value'    => false,
            ),
            'onOffSwitchesForProperties' => array(
                'names'    => array(
                    'enableAgeScaling',
                    'enableCitationsScaling',
                    'enableAgeCorrection',
                    'enableCitationAgeCorrection',
                    'enablePublicationAgeCorrection',
                    'enableFieldOfStudyCorrection',
                    'enableNumberOfAuthorsCorrection',
                    'enableCitationAuthorsWeighting',
                ),
                'dataType' => 'bool',
                'from'     => null,
                'to'       => null,
                'required' => false,
                'value'    => true,
            ),
            'periods'                    => array(
                'names'    => array(
                    'startYear',
                    'endYear',
                ),
                'dataType' => 'int',
                'from'     => 0,
                'to'       => PHP_INT_MAX,
                'required' => false,
                'value'    => null,
            ),
            'ageScalingFactor'           => array(
                'dataType' => 'float',
                'from'     => 1,
                'to'       => PHP_INT_MAX,
                'required' => false,
                'value'    => 20.0,
            ),
            'citationsScalingFactor'     => array(
                'dataType' => 'float',
                'from'     => 1,
                'to'       => PHP_INT_MAX,
                'required' => false,
                'value'    => 50.0,
            ),
            'ageThreshold'               => array(
                'dataType' => 'int',
                'from'     => 1,
                'to'       => PHP_INT_MAX,
                'required' => false,
                'value'    => 20,
            ),
            'citationAgeThreshold'       => array(
                'dataType' => 'int',
                'from'     => 1,
                'to'       => PHP_INT_MAX,
                'required' => false,
                'value'    => 10,
            ),
            'publicationAgeThreshold'    => array(
                'dataType' => 'int',
                'from'     => 1,
                'to'       => PHP_INT_MAX,
                'required' => false,
                'value'    => 10,
            ),
            'foreignCitationWeighting'   => array(
                'dataType' => 'float',
                'from'     => 0,
                'to'       => 1,
                'required' => false,
                'value'    => 1.0,
            ),
            'colleagueCitationWeighting' => array(
                'dataType' => 'float',
                'from'     => 0,
                'to'       => 1,
                'required' => false,
                'value'    => 0.25,
            ),
            'selfCitationWeighting'      => array(
                'dataType' => 'float',
                'from'     => 0,
                'to'       => 1,
                'required' => false,
                'value'    => 0.05,
            ),
        );
        foreach ($parameterGroups as $parameterGroupName => $parameterGroup) {
            if (array_key_exists('names', $parameterGroup)) {
                foreach ($parameterGroup['names'] as $parameterName) {
                    $this->defineParameter(
                        $parameterName,
                        $parameterGroup['dataType'],
                        $parameterGroup['from'],
                        $parameterGroup['to'],
                        $parameterGroup['required'],
                        $parameterGroup['value']
                    );
                }
            } else {
                $this->defineParameter(
                    $parameterGroupName,
                    $parameterGroup['dataType'],
                    $parameterGroup['from'],
                    $parameterGroup['to'],
                    $parameterGroup['required'],
                    $parameterGroup['value']
                );
            }
        }
    }


    /**
     * Defines the data format of the index.
     *
     * Because of the big size of the data format, the definition has
     * been transfered from the constructor to this method to ensure the
     * readability.
     *
     * The data format of the mf-index contains more information than
     * e.g. the format of the h-index. As the age of a publication has
     * to be considered, an additional array key 'publicationYear' is
     * used. Another new key has the name 'fieldOfStudy'. Furthermore,
     * a list of all authors of a publication is stored. This is used
     * to compare a citation author with the authors of the publication
     * to check if we have a self-citation. Additionally, this allows
     * to determine the number of authors of a publication.
     *
     * As we also need more information about citations, an additional
     * subarray with the key 'citations' is used. The stored information
     * of a citation contains the id of the citing publication and its
     * year of publication which is used to calculate the age of a citation.
     *
     * Furthermore, a citation contains a publication timestamp. The timestamp
     * is more precise than the publication year and allows to get a list of
     * all persons who are considered as current or former colleagues
     * at the time when the citation occurs.
     *
     * Finally, a citation stores a list of all authors of the citing
     * publication. The citing authors can then be compared with the cited
     * authors and the colleagues to check if a citing author cites himself,
     * if he is a colleague of the considered scientist or if he is a foreign
     * scientist.
     *
     * Additionally, the first level of the array contains a new key 'author' which
     * points to a subarray with more information about the author. The subarray
     * contains the year of his first publication and the previously mentioned
     * list of colleagues. The list of colleagues is an array with timestamps
     * as keys pointing to another array with all authors who are considered
     * as a colleague of the scientist up to the date represented by the timestamp.
     */
    private function defineDataFormat()
    {
        $this->dataFormat = array(
            'publications' => array(
                'int' => array(
                    'publicationId'   => 'int',
                    'citationCount'   => 'int',
                    'publicationYear' => 'int',
                    'fieldOfStudy'    => 'string',
                    'authors'         => array(
                        'int' => array(
                            'authorId' => 'int',
                        ),
                    ),
                    'citations'       => array(
                        'int' => array(
                            'publicationId'        => 'int',
                            'publicationYear'      => 'int',
                            'publicationTimestamp' => 'int',
                            'authors'              => array(
                                'int' => array(
                                    'authorId' => 'int',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            'author'       => array(
                'firstPublicationYear' => 'int',
                'colleagues'           => array(
                    'int' => array(
                        'int' => array(
                            'authorId' => 'int',
                        ),
                    ),
                ),
            ),
        );
    }


    /**
     * Compares the citation count of two publications and returns
     * whether the first or the second has the higher citation count
     * or if they have the same citation count.
     *
     * The method has to be static as this is required to use it as
     * comparator in the php function usort($array, $comparatorMethod).
     *
     * @param array $a The first publication.
     * @param array $b The second publication.
     *
     * @return int Returns 1, if the citation count of $a is higher, -1 if
     * the citation count of $b is higher and 0, if the citation count of
     * $a and $b is equal.
     */
    private static function compareCitationCountOf(array $a, array $b)
    {
        $citationCountOfA = $a['citationCount'];
        $citationCountOfB = $b['citationCount'];

        if ($citationCountOfA == $citationCountOfB) {
            return 0;
        }

        return ($citationCountOfA > $citationCountOfB ? 1 : -1);
    }


    /**
     * {@inheritDoc}
     */
    protected function fetchData()
    {
        parent::fetchData();

        $data = array();
        $data = $this->fetchPublicationsData($data);

        $data = $this->fetchAuthorData($data);

        $this->setData($data);
    }


    /**
     * Returns an array which contains all publications of the author
     * from the database.
     *
     * @param array $data The array which should be filled with the
     *                    publications data.
     *
     * @return array A list with all publications of the author.
     */
    private function fetchPublicationsData(array $data)
    {
        $publicationsStatement = $this->getPublicationsStatement();
        $authorsStatement = $this->getAuthorsStatement();

        $publicationsStatement->bindValue(
            ':authorId',
            $this->parameters['authorId']['value'],
            PDO::PARAM_INT
        );
        $publicationsStatement->execute();

        $data['publications'] = $publicationsStatement->fetchAll(PDO::FETCH_ASSOC);
        $data = IndexHelper::convertWrongDataTypes(
            $this->dataFormat['publications']['int'],
            $data,
            2,
            0,
            array(0 => array('publications'))
        );

        foreach ($data['publications'] as $publicationNumber => $publication) {
            $data = $this->fetchPublicationAuthorsData(
                $data,
                $authorsStatement,
                $publicationNumber,
                $publication['publicationId']
            );

            $data = $this->fetchCitationsData(
                $data,
                $authorsStatement,
                $publicationNumber,
                $publication['publicationId']
            );
        }

        return $data;
    }


    /**
     * Returns a prepared publications statement which can be used
     * to fetch a list of publications by binding the value
     * ':authorId'.
     *
     * @return PDOStatement The publications statement.
     */
    private function getPublicationsStatement()
    {
        $publicationsQuery = '
            SELECT
                pub_auth.publication_id AS publicationId,
                COUNT(cit.id) AS citationCount,
                YEAR(pub.date_published) AS publicationYear,
                fos.name AS fieldOfStudy
            FROM publications_authors pub_auth
            LEFT JOIN (
                SELECT
                    cit.id AS id,
                    cit.publication_id AS publication_id,
                    cit.citation_id AS citation_id
                FROM citations cit
                RIGHT JOIN publications pub ON (cit.citation_id = pub.id)
                '.$this->getSqlFor('startYear', 'pub', 'WHERE').'
                '.$this->getSqlFor('endYear', 'pub', 'AND').'
            ) cit ON (pub_auth.publication_id = cit.publication_id)
            LEFT JOIN publications pub ON (pub_auth.publication_id = pub.id)
            LEFT JOIN study_fields fos ON (pub.study_field_id = fos.id)
            WHERE pub_auth.author_id = :authorId
            '.$this->getSqlFor('startYear', 'pub', 'AND').'
            '.$this->getSqlFor('endYear', 'pub', 'AND').'
            GROUP BY publicationId
            ORDER BY citationCount DESC
        ';
        $publicationsStatement = $this->db->prepare($publicationsQuery);

        return $publicationsStatement;
    }


    /**
     * Returns the SQL to limit the set of publications
     * to a specific period indicated by the parameters
     * 'startYear' and 'endYear'.
     *
     * @param string $type     Indicates the type for which the SQL
     *                         string is requested. Allowed types are 'startYear' and
     *                         'endYear'.
     * @param string $table    Contains either the full name
     *                         of the table which is 'publications' or an alias like
     *                         'pub'.
     * @param string $operator Contains the name of the
     *                         operator which should be used in the where clause.
     *                         Allowed names are 'WHERE' and 'AND'.
     *
     * @return string The string which can be used in a
     * where clause of an SQL statement. If the use of a
     * type like 'startYear' is disabled, an empty string is
     * returned.
     */
    private function getSqlFor($type, $table, $operator)
    {
        $enableStartYear = $this->parameters['enableStartYear']['value'];
        $enableEndYear = $this->parameters['enableEndYear']['value'];
        $startYear = $this->parameters['startYear']['value'];
        $endYear = $this->parameters['endYear']['value'];

        if (($type == 'startYear') && $enableStartYear) {
            return $operator.' YEAR('.$table.'.date_published) >= '.$startYear;
        } else if (($type == 'endYear') && $enableEndYear) {
            return $operator.' YEAR('.$table.'.date_published) <= '.$endYear;
        } else {
            return '';
        }
    }


    /**
     * Returns a prepared authors statement which can be used
     * to fetch a list of authors by binding the value
     * ':publicationId'.
     *
     * @return PDOStatement The authors statement.
     */
    private function getAuthorsStatement()
    {
        $authorsQuery = '
            SELECT pub_auth.author_id AS authorId
            FROM publications_authors pub_auth
            WHERE pub_auth.publication_id = :publicationId
        ';
        $authorsStatement = $this->db->prepare($authorsQuery);

        return $authorsStatement;
    }


    /**
     * Returns an array which contains all authors of a publication
     * from the database.
     *
     * @param array        $data              The array which should be filled with the publication
     *                                        authors data.
     * @param PDOStatement $authorsStatement  The prepared statement which
     *                                        allows to fetch publication authors data from the database by binding the
     *                                        value ':publicationId' to the id of the publication.
     * @param int          $publicationNumber The array key of the publication, which
     *                                        can be used to access the publication with the expression
     *                                        $data['publications'][$publicationNumber].
     * @param int          $publicationId     The id of the publication under which the
     *                                        publication is stored in the database. $publicationId is used to
     *                                        fetch all authors of that publication.
     *
     * @return array A list with all authors of a publication.
     */
    private function fetchPublicationAuthorsData(
        array $data,
        PDOStatement $authorsStatement,
        $publicationNumber,
        $publicationId
    ) {
        $authorsStatement->bindValue(
            ':publicationId',
            $publicationId,
            PDO::PARAM_INT
        );
        $authorsStatement->execute();

        $data['publications'][$publicationNumber]['authors'] =
            $authorsStatement->fetchAll(PDO::FETCH_ASSOC);
        $data = IndexHelper::convertWrongDataTypes(
            $this->dataFormat['publications']['int']['authors']['int'],
            $data,
            4,
            0,
            array(
                0 => array('publications'),
                1 => array($publicationNumber),
                2 => array('authors'),
            )
        );

        return $data;
    }


    /**
     * Returns an array which contains a list from the database with all
     * citations of a publication and a list of citation timestamps which
     * can be used later to fetch the colleagues of the author.
     *
     * @param array        $data              The array which should be filled with the citations data.
     * @param PDOStatement $authorsStatement  The prepared statement which
     *                                        allows to fetch citation authors data from the database by binding the
     *                                        value ':publicationId' to the id of the citation.
     * @param int          $publicationNumber The array key of the publication, which
     *                                        can be used to access the publication with the expression
     *                                        $data['publications'][$publicationNumber].
     * @param int          $publicationId     The id of the publication under which the
     *                                        publication is stored in the database. $publicationId is used to
     *                                        fetch all citations of that publication.
     *
     * @return array A list with all citations of a publication and a list
     * with the citation timestamps.
     */
    private function fetchCitationsData(
        array $data,
        PDOStatement $authorsStatement,
        $publicationNumber,
        $publicationId
    ) {
        $citationsStatement = $this->getCitationsStatement();

        $citationsStatement->bindValue(
            ':publicationId',
            $publicationId,
            PDO::PARAM_INT
        );
        $citationsStatement->execute();

        $data['publications'][$publicationNumber]['citations'] =
            $citationsStatement->fetchAll(PDO::FETCH_ASSOC);
        $data = IndexHelper::convertWrongDataTypes(
            $this->dataFormat['publications']['int']['citations']['int'],
            $data,
            4,
            0,
            array(
                0 => array('publications'),
                1 => array($publicationNumber),
                2 => array('citations'),
            )
        );

        foreach ($data['publications'][$publicationNumber]['citations']
                 as $citationNumber => $citation) {
            /*
             * As we later want to fetch all colleagues of the author
             * up to a specific timestamp, we have to create the array
             * key in $data['author']['colleagues'] at this point by
             * using the timestamp of the current citation which is stored
             * in $citation['publicationTimestamp'].
             */
            $data['author']['colleagues'][$citation['publicationTimestamp']] = array();

            $data = $this->fetchCitationAuthorsData(
                $data,
                $authorsStatement,
                $publicationNumber,
                $citationNumber,
                $citation['publicationId']
            );
        }

        return $data;
    }


    /**
     * Returns a prepared citations statement which can be used
     * to fetch a list of citations by binding the value
     * ':publicationId'.
     *
     * @return PDOStatement The citations statement.
     */
    private function getCitationsStatement()
    {
        $citationsQuery = '
            SELECT
                pub.id AS publicationId,
                YEAR(pub.date_published) AS publicationYear,
                UNIX_TIMESTAMP(pub.date_published) AS publicationTimestamp
            FROM publications pub
            LEFT JOIN citations cit ON (pub.id = cit.citation_id)
            WHERE cit.publication_id = :publicationId
            '.$this->getSqlFor('startYear', 'pub', 'AND').'
            '.$this->getSqlFor('endYear', 'pub', 'AND').'
        ';
        $citationsStatement = $this->db->prepare($citationsQuery);

        return $citationsStatement;
    }


    /**
     * Returns an array which contains all authors of a citation
     * from the database.
     *
     * @param array        $data              The array which should be filled with the citation
     *                                        authors data.
     * @param PDOStatement $authorsStatement  The prepared statement which
     *                                        allows to fetch citation authors data from the database by binding the
     *                                        value ':publicationId' to the id of the citation.
     * @param int          $publicationNumber The array key of the publication, which
     *                                        can be used to access the publication with the expression
     *                                        $data['publications'][$publicationNumber].
     * @param int          $citationNumber    The array key of the citation, which can be
     *                                        used to access the citation with the expression
     *                                        $data['publications'][$publicationNumber]['citations'][$citationNumber].
     * @param int          $citationId        The id of the citation under which the citation
     *                                        is stored in the database. $citationId is used to fetch all authors of
     *                                        that citation.
     *
     * @return array A list with all authors of a citation.
     */
    private function fetchCitationAuthorsData(
        array $data,
        $authorsStatement,
        $publicationNumber,
        $citationNumber,
        $citationId
    ) {
        $authorsStatement->bindValue(
            ':publicationId',
            $citationId,
            PDO::PARAM_INT
        );
        $authorsStatement->execute();

        $data['publications'][$publicationNumber]['citations'][$citationNumber]['authors'] =
            $authorsStatement->fetchAll(PDO::FETCH_ASSOC);
        $data = IndexHelper::convertWrongDataTypes(
            $this->dataFormat['publications']['int']['citations']['int']['authors']['int'],
            $data,
            6,
            0,
            array(
                0 => array('publications'),
                1 => array($publicationNumber),
                2 => array('citations'),
                3 => array($citationNumber),
                4 => array('authors'),
            )
        );

        return $data;
    }


    /**
     * Returns an array which contains information from the database
     * about the author like the year of his first publication and a
     * list of his colleagues.
     *
     * @param array $data The array which should be filled with the
     *                    author information.
     *
     * @return array An array which contains the author information.
     */
    private function fetchAuthorData(array $data)
    {
        $firstPublicationYearStatement = $this->getFirstPublicationYearStatement();

        $firstPublicationYearStatement->bindValue(
            ':authorId',
            $this->parameters['authorId']['value'],
            PDO::PARAM_INT
        );
        $firstPublicationYearStatement->execute();

        $firstPublicationYearResult = $firstPublicationYearStatement->fetch(PDO::FETCH_ASSOC);
        $data['author']['firstPublicationYear'] = $firstPublicationYearResult['firstPublicationYear'];
        $data = IndexHelper::convertWrongDataTypes(
            $this->dataFormat['author'],
            $data,
            1,
            0,
            array(0 => array('author'))
        );

        $data = $this->fetchColleaguesData($data);

        return $data;
    }


    /**
     * Returns a prepared statement which can be used to fetch
     * the year of the first publication of the author by binding
     * the value ':authorId'.
     *
     * @return PDOStatement The statement for the first publication year.
     */
    private function getFirstPublicationYearStatement()
    {
        $firstPublicationYearQuery = '
            SELECT YEAR(pub.date_published) AS firstPublicationYear
            FROM publications pub
            LEFT JOIN publications_authors pub_auth ON (pub.id = pub_auth.publication_id)
            WHERE pub_auth.author_id = :authorId
            ORDER BY firstPublicationYear ASC
            LIMIT 1
        ';
        $firstPublicationYearStatement = $this->db->prepare($firstPublicationYearQuery);

        return $firstPublicationYearStatement;
    }


    /**
     * Returns an array which contains a list from the database
     * with all colleagues of the author up to a specific
     * citation timestamp.
     *
     * @param array $data The array which should be filled with the
     *                    colleagues of the author.
     *
     * @return array An array which contains the colleagues.
     */
    private function fetchColleaguesData(array $data)
    {
        $colleaguesStatement = $this->getColleaguesStatement();

        if (!array_key_exists('colleagues', $data['author'])) {
            $data['author']['colleagues'] = array();
        }

        foreach (array_keys($data['author']['colleagues']) as $citationTimestamp) {
            $colleaguesStatement->bindValue(
                ':authorId',
                $this->parameters['authorId']['value'],
                PDO::PARAM_INT
            );
            $colleaguesStatement->bindValue(
                ':citationTimestamp',
                $citationTimestamp,
                PDO::PARAM_INT
            );
            $colleaguesStatement->execute();

            $data['author']['colleagues'][$citationTimestamp] =
                $colleaguesStatement->fetchAll(PDO::FETCH_ASSOC);
        }

        $data = IndexHelper::convertWrongDataTypes(
            $this->dataFormat['author']['colleagues']['int']['int'],
            $data,
            4,
            0,
            array(
                0 => array('author'),
                1 => array('colleagues'),
            )
        );

        return $data;
    }


    /**
     * Returns a prepared colleagues statement which can be used
     * to fetch a list with all colleagues of the author up to
     * a specific citation timestamp by binding the values
     * ':authorId' and ':citationTimestamp'.
     *
     * @return PDOStatement The colleagues statement.
     */
    private function getColleaguesStatement()
    {
        $colleaguesQuery = '
            SELECT pub_auth.author_id AS authorId
            FROM publications_authors pub_auth
            LEFT JOIN publications pub ON (pub_auth.publication_id = pub.id)
            WHERE pub.id IN
                (SELECT pub.id AS publicationId
                FROM publications pub
                LEFT JOIN publications_authors pub_auth ON (pub.id = pub_auth.publication_id)
                WHERE pub_auth.author_id = :authorId
                AND UNIX_TIMESTAMP(pub.date_published) < :citationTimestamp)
            AND pub_auth.author_id != :authorId
            GROUP BY authorId
        ';
        $colleaguesStatement = $this->db->prepare($colleaguesQuery);

        return $colleaguesStatement;
    }


    /**
     * {@inheritDoc}
     */
    public function setData(array $data)
    {
        $this->checkPublicationsAndAuthorData($data);

        $this->data = $data;
    }


    /**
     * Checks if the provided publications and author data is correct.
     *
     * @param array $data Contains the data of all publications
     *                    and the information about the author.
     *
     * @throws IndexDataException If $data doesn't contain valid
     * publications or valid information about the author.
     */
    private function checkPublicationsAndAuthorData(array $data)
    {
        IndexHelper::checkArrayKeysExist(array_keys($this->dataFormat), $data, 0, 0);
        IndexHelper::checkDataTypesAreCorrect($this->dataFormat, $data, 3, 0, 0);

        $this->checkPublicationsData($data);

        $this->checkAuthorData($data);
    }


    /**
     * Checks if the provided publications data is correct.
     *
     * @param array $data Contains the data of all publications.
     *
     * @throws IndexDataException If $data doesn't contain valid
     * publications. This is e.g. the case if required publication
     * attributes like 'publicationId', 'citationCount',
     * 'publicationYear', 'fieldOfStudy' or the list of authors
     * and citations are missing.
     */
    private function checkPublicationsData(array $data)
    {
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

        $this->checkPublicationAuthorsData($data);

        $this->checkCitationsData($data);
    }


    /**
     * Checks if the provided data of the publication authors is correct.
     *
     * @param array $data Contains the data of all authors of the
     *                    publications.
     *
     * @throws IndexDataException If $data doesn't contain valid
     * publication authors. This is e.g. the case if the required
     * attribute 'authorId' is missing.
     */
    private function checkPublicationAuthorsData(array $data)
    {
        IndexHelper::checkDataTypesAreCorrect(
            array('int'),
            $data,
            1,
            3,
            0,
            array(
                0 => array('publications'),
                2 => array('authors'),
            )
        );
        IndexHelper::checkDataTypesAreCorrect(
            array('array'),
            $data,
            2,
            3,
            0,
            array(
                0 => array('publications'),
                2 => array('authors'),
            )
        );
        IndexHelper::checkArrayKeysExist(
            array_keys($this->dataFormat['publications']['int']['authors']['int']),
            $data,
            4,
            0,
            array(
                0 => array('publications'),
                2 => array('authors'),
            )
        );
        IndexHelper::checkDataTypesAreCorrect(
            $this->dataFormat['publications']['int']['authors']['int'],
            $data,
            3,
            4,
            0,
            array(
                0 => array('publications'),
                2 => array('authors'),
            )
        );
    }


    /**
     * Checks if the provided data of the citations is correct.
     *
     * @param array $data Contains the data of all citations
     *                    of the publications.
     *
     * @throws IndexDataException If $data doesn't contain valid
     * citations. This is e.g. the case if citation information
     * like 'publicationId', 'publicationYear', 'publicationTimestamp'
     * or the list of citation authors is missing.
     */
    private function checkCitationsData(array $data)
    {
        IndexHelper::checkDataTypesAreCorrect(
            array('int'),
            $data,
            1,
            3,
            0,
            array(
                0 => array('publications'),
                2 => array('citations'),
            )
        );
        IndexHelper::checkDataTypesAreCorrect(
            array('array'),
            $data,
            2,
            3,
            0,
            array(
                0 => array('publications'),
                2 => array('citations'),
            )
        );
        IndexHelper::checkArrayKeysExist(
            array_keys($this->dataFormat['publications']['int']['citations']['int']),
            $data,
            4,
            0,
            array(
                0 => array('publications'),
                2 => array('citations'),
            )
        );
        IndexHelper::checkDataTypesAreCorrect(
            $this->dataFormat['publications']['int']['citations']['int'],
            $data,
            3,
            4,
            0,
            array(
                0 => array('publications'),
                2 => array('citations'),
            )
        );

        $this->checkCitationAuthorsData($data);
    }


    /**
     * Checks if the provided data of the citation authors is correct.
     *
     * @param array $data Contains the data of all citation authors of
     *                    the publications.
     *
     * @throws IndexDataException If $data doesn't contain valid
     * citation authors. This is e.g. the case if the required
     * attribute 'authorId' is missing.
     */
    private function checkCitationAuthorsData(array $data)
    {
        IndexHelper::checkDataTypesAreCorrect(
            array('int'),
            $data,
            1,
            5,
            0,
            array(
                0 => array('publications'),
                2 => array('citations'),
                4 => array('authors'),
            )
        );
        IndexHelper::checkDataTypesAreCorrect(
            array('array'),
            $data,
            2,
            5,
            0,
            array(
                0 => array('publications'),
                2 => array('citations'),
                4 => array('authors'),
            )
        );
        IndexHelper::checkArrayKeysExist(
            array_keys($this->dataFormat['publications']['int']['citations']['int']['authors']['int']),
            $data,
            6,
            0,
            array(
                0 => array('publications'),
                2 => array('citations'),
                4 => array('authors'),
            )
        );
        IndexHelper::checkDataTypesAreCorrect(
            $this->dataFormat['publications']['int']['citations']['int']['authors']['int'],
            $data,
            3,
            6,
            0,
            array(
                0 => array('publications'),
                2 => array('citations'),
                4 => array('authors'),
            )
        );
    }


    /**
     * Checks if the provided author data is correct.
     *
     * @param array $data Contains the information about the
     *                    scientist for which the index value should be calculated.
     *
     * @throws IndexDataException If $data doesn't contain valid
     * information about the author. This means e.g. that the
     * year of his first publication is missing or that there is
     * no valid list of his colleagues.
     */
    private function checkAuthorData(array $data)
    {
        IndexHelper::checkArrayKeysExist(
            array_keys($this->dataFormat['author']),
            $data,
            1,
            0,
            array(0 => array('author'))
        );
        IndexHelper::checkDataTypesAreCorrect(
            $this->dataFormat['author'],
            $data,
            3,
            1,
            0,
            array(0 => array('author'))
        );

        $this->checkColleaguesData($data);
    }


    /**
     * Checks if the provided data of the colleagues of the
     * considered scientist is correct.
     *
     * @param array $data Contains the data of all colleagues.
     *
     * @throws IndexDataException If $data doesn't contain a valid
     * list of colleagues. This is e.g. the case if the colleague
     * array doesn't have timestamps as keys or if important
     * attributes to identify a colleague like the 'authorId'
     * are missing.
     */
    private function checkColleaguesData(array $data)
    {
        IndexHelper::checkDataTypesAreCorrect(
            array('int'),
            $data,
            1,
            2,
            0,
            array(
                0 => array('author'),
                1 => array('colleagues'),
            )
        );
        IndexHelper::checkDataTypesAreCorrect(
            array('array'),
            $data,
            2,
            2,
            0,
            array(
                0 => array('author'),
                1 => array('colleagues'),
            )
        );
        IndexHelper::checkDataTypesAreCorrect(
            array('int'),
            $data,
            1,
            3,
            0,
            array(
                0 => array('author'),
                1 => array('colleagues'),
            )
        );
        IndexHelper::checkDataTypesAreCorrect(
            array('array'),
            $data,
            2,
            3,
            0,
            array(
                0 => array('author'),
                1 => array('colleagues'),
            )
        );
        IndexHelper::checkArrayKeysExist(
            array_keys($this->dataFormat['author']['colleagues']['int']['int']),
            $data,
            4,
            0,
            array(
                0 => array('author'),
                1 => array('colleagues'),
            )
        );
        IndexHelper::checkDataTypesAreCorrect(
            $this->dataFormat['author']['colleagues']['int']['int'],
            $data,
            3,
            4,
            0,
            array(
                0 => array('author'),
                1 => array('colleagues'),
            )
        );
    }


    /**
     * {@inheritDoc}
     */
    protected function calculateValue()
    {
        $this->recalculateCitationCounts();
        $this->sortPublicationsByCitationCount();
        $value =
            $this->getScalingFactorFor('scientist')
            * (
                $this->getTaperedHIndexValue()
                / $this->getAgeCorrectionFor('scientist', $this->data['author']['firstPublicationYear'])
            );

        $this->value = round($value, 2);
    }


    /**
     * Recalculates the citation counts based on the enabled properties
     * of the index.
     *
     * The method iterates over all publications and calculates the new
     * citation count of a publication by multiplying the citations scaling
     * factor, the field of study correction and the citation sum and dividing
     * by the number of authors and the publication age. The citation sum is
     * calculated by iterating over the original amount of citations and
     * dividing the citation authors weighting by the citation age.
     *
     * The new citation count of a publication is rounded and saved in the
     * class attribute $data. The rounding is necessary because the later
     * used tapered h-index expects the citation count to be an integer and
     * not a floating point value.
     */
    private function recalculateCitationCounts()
    {
        foreach ($this->data['publications'] as $publicationNumber => $publication) {
            $citationSum = 0;
            foreach ($publication['citations'] as $citation) {
                $citationSum +=
                    (
                        $this->getCitationAuthorsWeighting($publication['authors'], $citation)
                        / $this->getAgeCorrectionFor('citation', $citation['publicationYear'])
                    );
            }

            $citationCount =
                $this->getScalingFactorFor('citations')
                * $this->getFieldOfStudyCorrectionFor($publication['fieldOfStudy'])
                * (1 / $this->getNumberOfAuthorsCorrection($publication['authors']))
                * (1 / $this->getAgeCorrectionFor('publication', $publication['publicationYear']))
                * $citationSum;

            $this->data['publications'][$publicationNumber]['citationCount'] =
                intval(round($citationCount));
        }
    }


    /**
     * Returns the citation authors weighting.
     *
     * The method checks if the citation authors weighting is enabled.
     * If yes, it adds up the weightings of all citation authors in
     * the first step and calculates the mean value in the second step.
     *
     * To calculate the weighting of a specific citation author in
     * $citation['authors'], his authorId is compared with the ids of
     * the publication authors in $publicationAuthors. If there is a
     * match, the citation author is a coauthor of the publication
     * and cites himself which means we have a self-citation.
     *
     * If we have no self-citation, we check if he is a colleague by
     * using $citation['publicationTimestamp'] to get the colleague
     * list $data['author']['colleagues'][$citation['publicationTimestamp']]
     * for the date of the citation. If the citation author appears in
     * the colleague list, we have a colleague citation. Otherwise
     * we have a foreign citation.
     *
     * @param array $publicationAuthors Contains a list with all authors of
     *                                  the publication.
     * @param array $citation           Contains information on the citation like the
     *                                  timestamp and all citation authors.
     *
     * @return float The mean value of the weightings of all citation authors
     * or 1, if the citation authors weighting is disabled.
     */
    private function getCitationAuthorsWeighting(array $publicationAuthors, array $citation)
    {
        $citationAuthorsWeighting = 0;

        if ($this->parameters['enableCitationAuthorsWeighting']['value']) {
            foreach ($citation['authors'] as $citationAuthor) {
                if ($this->hasRelationshipWith($publicationAuthors, $citationAuthor)) {
                    $citationAuthorsWeighting += $this->parameters['selfCitationWeighting']['value'];
                } else {
                    $colleagues = $this->data['author']['colleagues'][$citation['publicationTimestamp']];
                    if ($this->hasRelationshipWith($colleagues, $citationAuthor)) {
                        $citationAuthorsWeighting +=
                            $this->parameters['colleagueCitationWeighting']['value'];
                    } else {
                        $citationAuthorsWeighting +=
                            $this->parameters['foreignCitationWeighting']['value'];
                    }
                }
            }

            $citationAuthorsCount = count($citation['authors']);
            $citationAuthorsWeighting /= $citationAuthorsCount;
        } else {
            $citationAuthorsWeighting = 1;
        }

        return $citationAuthorsWeighting;
    }


    /**
     * Returns if a $testPerson has a relationship with $persons.
     *
     * The method can be used to check if $testPerson has a
     * relationship with the authors of a publication, which means
     * that $testPerson cites itself. The method also allows to
     * test if $testPerson is a colleague of the considered scientist.
     *
     * @param array $persons    Contains a list with the persons. Each person
     *                          should have the attribute 'authorId'.
     * @param array $testPerson Contains a test person whose relationship
     *                          to $persons should be checked. The test person should have the
     *                          attribute 'authorId'.
     *
     * @return bool True, if $testPerson has a relationship with
     * $persons or false, if it has no connection with them.
     */
    private function hasRelationshipWith(array $persons, array $testPerson)
    {
        $testPersonAuthorId = $testPerson['authorId'];
        foreach ($persons as $person) {
            $personAuthorId = $person['authorId'];
            if ($testPersonAuthorId == $personAuthorId) {
                return true;
            }
        }

        return false;
    }


    /**
     * Returns the age correction for the specified $type.
     *
     * To calculate the age correction, the method needs a lower bound,
     * which has to be provided by using $lowerBound. For publications
     * and citations the lower bound corresponds to the publication year.
     * For scientists the lower bound is the year of the first publication.
     *
     * The upper bound is determined by the method itself by either using
     * the index parameter 'endYear', if the parameter 'enableEndYear'
     * is true or the current year, if 'enableEndYear' is false.
     *
     * If the calculated age correction is lower or equal than the value of
     * the age threshold for the specified type, the calculated value is used.
     * Otherwise, the threshold value is used.
     *
     * @param string $type       The case-sensitive name of the type for which the age
     *                           correction is calculated. Allowed names are 'scientist', 'publication'
     *                           and 'citation'.
     * @param int    $lowerBound The year which should be used as lower bound to
     *                           determine the age. For publications and citations the lower bound should
     *                           be the publication year, for scientists it should be the year of the first
     *                           publication.
     *
     * @return int The age correction or 1, if the age correction for the specified
     * type is disabled.
     */
    private function getAgeCorrectionFor($type, $lowerBound)
    {
        $ageCorrection = 1;

        if ($type == 'scientist') {
            $prefix = '';
        } else {
            $prefix = $type;
        }
        if ($this->parameters['enable'.ucfirst($prefix).'AgeCorrection']['value']) {
            if ($this->parameters['enableEndYear']['value']) {
                $upperBound = $this->parameters['endYear']['value'];
            } else {
                $upperBound = date('Y');
            }

            $age = $upperBound - $lowerBound + 1;
            $ageThreshold =
                $this->parameters[$prefix.($prefix != '' ? 'Age' : 'age').'Threshold']['value'];
            if ($age <= $ageThreshold) {
                $ageCorrection = $age;
            } else {
                $ageCorrection = $ageThreshold;
            }
        }

        return $ageCorrection;
    }


    /**
     * Returns the scaling factor for the specified $type.
     *
     * @param string $type The case-sensitive name of the type for which
     *                     the scaling factor should be returned. Allowed names are 'scientist'
     *                     and 'citations'.
     *
     * @return int The scaling factor or 1, if the scaling for the specified
     * type is disabled.
     */
    private function getScalingFactorFor($type)
    {
        $scalingFactor = 1;

        if ($type == 'scientist') {
            $prefix = 'age';
        } else {
            $prefix = $type;
        }
        if ($this->parameters['enable'.ucfirst($prefix).'Scaling']['value']) {
            $scalingFactor = $this->parameters[$prefix.'ScalingFactor']['value'];
        }

        return $scalingFactor;
    }


    /**
     * Returns the correction for the field of study indicated
     * by $fieldOfStudy.
     *
     * @param string $fieldOfStudy The case-sensitive name of the field of
     *                             study for which the correction value is requested.
     *
     * @return float The field of study correction, which may be 1, if the
     * field of study correction is disabled or if $fieldOfStudy is the
     * name of a field of study for which no correction value is available.
     */
    private function getFieldOfStudyCorrectionFor($fieldOfStudy)
    {
        $fieldOfStudyCorrection = 1;

        if ($this->parameters['enableFieldOfStudyCorrection']['value']) {
            try {
                $fieldOfStudyCorrection = NormalizationFactors::getFactorFor($fieldOfStudy);
            } catch (IndexException $e) {
                /*
                 * An IndexException indicates that no correction
                 * value is available for $fieldOfStudy, which
                 * is e.g. the case when a field of study has the
                 * name 'Not Categorized'. In this case we set no
                 * value for $fieldOfStudyCorrection and use the
                 * above defined value of 1 as return value.
                 */
            }
        }

        return $fieldOfStudyCorrection;
    }


    /**
     * Returns the number of authors correction.
     *
     * @param array $publicationAuthors Contains a list with all
     *                                  authors of the publication.
     *
     * @return int The number of authors or 1, if the number of
     * authors correction is disabled.
     */
    private function getNumberOfAuthorsCorrection(array $publicationAuthors)
    {
        $numberOfAuthorsCorrection = 1;

        if ($this->parameters['enableNumberOfAuthorsCorrection']['value']) {
            $numberOfAuthorsCorrection = count($publicationAuthors);
        }

        return $numberOfAuthorsCorrection;
    }


    /**
     * Sorts the publications in the class attribute $data['publications']
     * by their citation count in descending order.
     *
     * The use of this method may be necessary after recalculating the
     * citation counts which can result in higher citation counts for some
     * publications, while other publications may have lower citation counts
     * than before.
     *
     * The sorting has to be performed if some indices like the tapered h-index
     * should be used which require that the publications are sorted by their
     * citation count in descending order.
     */
    private function sortPublicationsByCitationCount()
    {
        usort($this->data['publications'], array($this, 'compareCitationCountOf'));
        $this->data['publications'] = array_reverse($this->data['publications']);
    }


    /**
     * Returns the value calculated by the tapered h-index.
     *
     * @return float The value calculated by the tapered h-index.
     */
    private function getTaperedHIndexValue()
    {
        $taperedHIndex = new TaperedHIndex($this->db);
        $taperedHIndex->setParameters(array('authorId' => $this->parameters['authorId']['value']));

        /*
         * The data format of the tapered h-index differs from
         * the data format of the mf-index. To avoid problems
         * with the data validation of the tapered h-index when
         * calling setData($data), we have to create and fill
         * a local $data variable which matches the data format
         * of the tapered h-index. In the next step we can set
         * the data of the tapered h-index by using the local
         * $data variable.
         */
        $data = array();
        $data['publications'] = array();
        foreach ($this->data['publications'] as $publicationNumber => $publication) {
            $data['publications'][$publicationNumber]['publicationId'] = $publication['publicationId'];
            $data['publications'][$publicationNumber]['citationCount'] = $publication['citationCount'];
        }
        $taperedHIndex->setData($data);

        return $taperedHIndex->getValue();
    }
}
