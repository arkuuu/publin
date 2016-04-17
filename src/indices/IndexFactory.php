<?php

namespace publin\src\indices;

use publin\src\Database;
use publin\src\indices\exceptions\IndexException;
use publin\config\Config;
use SplFileInfo;

/**
 * This factory facilitates the usage of indices. It provides
 * methods to configure all indices at once and to get single
 * indices as well as all indices.
 *
 * @package publin\src\indices
 */
class IndexFactory {

    /**
     * This is an instance of the Publin database class.
     *
     * The database should be provided when constructing
     * the index factory.
     *
     * @var Database
     */
    private $db;

    /**
     * This variable contains the instances of all indices.
     *
     * The keys of the array represent the names of the
     * indices as returned by the getName() method of an index.
     *
     * @var array
     */
    private $indices = array();

    /**
     * Maps data types with duplicate names to the name
     * used by the php function getttype($var).
     *
     * The variable is used to compare the data type
     * of a provided parameter value with the data type
     * of an available parameter.
     *
     * @var array
     */
    private $dataTypeMapping = array(
        'bool' => 'boolean',
        'int' => 'integer',
        'float' => 'double',
        'Array' => 'array'
    );

    /**
     * Constructs the index factory and creates all
     * available indices.
     *
     * @param Database $db An instance of the Publin
     * database class.
     */
    public function __construct(Database $db) {
        $this->db = $db;
        $this->createIndices();
    }

    /**
     * Searches the src/indices/implementations directory
     * for available indices and saves the created instances
     * of the found indices in the $indices variable.
     */
    private function createIndices() {
        $filePath = Config::ROOT_PATH.'src/indices/implementations';
        $fileNames = scandir($filePath);
        foreach ($fileNames as $fileName) {
            $fileInfo = new SplFileInfo($fileName);
            $fileExtension = pathinfo($fileInfo->getFilename(), PATHINFO_EXTENSION);
            if ($fileExtension == 'php') {
                $fileNameWithoutExtension = $fileInfo->getBasename('.'.$fileExtension);
                $className = '\publin\src\indices\implementations\\'.$fileNameWithoutExtension;
                $index = new $className($this->db);
                $indexName = $index->getName();
                $this->indices[$indexName] = $index;
            }
        }
    }

    /**
     * Sets the parameters of all indices.
     *
     * The method detects if a provided parameter is available
     * in a specific index and sets only the available parameters
     * for this index.
     *
     * @param array $parameters The keys of the array represent the
     * name of the parameter, the array values represent the value
     * of the parameter.
     */
    public function setParameters(array $parameters) {
        foreach ($this->indices as $index) {
            $possibleParameters = array();
            $availableParameters = $index->getAvailableParameters();
            foreach ($availableParameters as $availableParameterName => $availableParameter) {
                if (array_key_exists($availableParameterName, $parameters)) {
                    $dataType = $availableParameter['dataType'];
                    $lowerBound = $availableParameter['from'];
                    $upperBound = $availableParameter['to'];
                    $parameterValue = $parameters[$availableParameterName];
                    $parameterDataType = gettype($parameterValue);
                    if (array_key_exists($dataType, $this->dataTypeMapping)) {
                        $dataType = $this->dataTypeMapping[$dataType];
                    }

                    if ($parameterDataType == $dataType) {
                        if (is_numeric($parameterValue)) {
                            if (($parameterValue >= $lowerBound)
                                && ($parameterValue <= $upperBound)
                            ) {
                                $possibleParameters[$availableParameterName] = $parameterValue;
                            }
                        } else {
                            $possibleParameters[$availableParameterName] = $parameterValue;
                        }
                    }
                }
            }

            if (count($possibleParameters) > 0) {
                $index->setParameters($possibleParameters);
            }
        }
    }

    /**
     * Returns a single index.
     *
     * @param string $name The case-sensitive name of
     * the requested index.
     *
     * @return Index
     *
     * @throws IndexException If the provided index name
     * cannot be resolved to an existing index.
     */
    public function getIndex($name) {
        if (array_key_exists($name, $this->indices)) {
            return $this->indices[$name];
        } else {
            throw new IndexException('There is no existing index
                with the name '.$name.'.');
        }
    }

    /**
     * Returns all existing indices.
     *
     * @return array The keys of the array represent the
     * name of the index, the value stands for the instance
     * of the index.
     */
    public function getAllIndices() {
        return $this->indices;
    }
}