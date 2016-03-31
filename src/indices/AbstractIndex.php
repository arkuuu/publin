<?php

namespace publin\src\indices;

use publin\src\Database;
use publin\src\indices\exceptions\IndexParameterException;
use publin\src\indices\exceptions\IndexDataException;

/**
 * This is an abstract class which takes care of some common tasks
 * for indices. The concrete indices may use it by inheriting from it.
 *
 * @package publin\src\indices
 */
abstract class AbstractIndex implements Index {

    /**
     * Contains the name of the index, e.g. 'h-index'.
     *
     * @var string
     */
    protected $name = '';

    /**
     * This variable contains all parameters which are specified
     * by the index.
     *
     * The author id is the only parameter which is required from
     * each index, as it is necessary to know the author to calculate
     * the index value for him.
     *
     * The keys of the array represent the name of the parameter.
     * Each key points to another array which contains keys for
     * the data type (key name: dataType), the lower bound
     * (key name: from), the upper bound (key name: to) and the
     * status if the parameter has to be provided for the calculation
     * of the index (key name: required). Furthermore it contains a
     * default value for the parameter (key name: value).
     *
     * @var array
     */
    protected $parameters = array(
        'authorId' => array(
            'dataType' => 'int',
            'from' => 1,
            'to' => PHP_INT_MAX,
            'required' => true,
            'value' => null
        )
    );

    /**
     * Contains the data which should be used to calculate the
     * index value.
     *
     * The data may either be fetched by the index itself from the
     * database via the $db variable or set from outside by using
     * the setData() method. The data in this array has to meet
     * the format from the $dataFormat variable.
     *
     * @var array
     */
    protected $data;

    /**
     * Contains the format of the data which is needed for the
     * calculation.
     *
     * The array keys should indicate the name of a specific type
     * (e.g. publication, author, citation) or calculation result
     * (e.g. citationCount), the array value may either contain
     * another array or the data type.
     *
     * @var array
     */
    protected $dataFormat;

    /**
     * Contains the calculated index value.
     *
     * The data type of this variable depends on the definition
     * of the concrete index. Some indices return natural numbers
     * as result, other indices provide real numbers.
     *
     * @var mixed
     */
    protected $value;

    /**
     * This is an instance of the Publin database class.
     *
     * The database should be provided when constructing the index.
     *
     * @var Database
     */
    protected $db;

    /**
     * Contains the numeric data types of PHP.
     *
     * The variable is used for different data
     * type checks in this class.
     *
     * @var array
     */
    private $numericDataTypes = array(
        'int',
        'integer',
        'float',
        'double'
    );

    /**
     * Constructs the index.
     *
     * @param Database $db An instance of the Publin
     * database class.
     */
    public function __construct(Database $db) {
        $this->db = $db;
    }

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) {
        foreach ($parameters as $name => $value) {
            if (array_key_exists($name, $this->parameters)) {
                try {
                    $this->checkDataTypeIsCorrect(
                        $this->parameters[$name]['dataType'],
                        $value
                    );
                } catch (IndexDataException $e) {
                    throw new IndexParameterException('The parameter with the name '
                        .$name.' should have the data type '
                        .$this->parameters[$name]['dataType'].' but has the data type '
                        .gettype($value));
                }

                if (in_array($this->parameters[$name]['dataType'], $this->numericDataTypes)) {
                    if (!(($value >= $this->parameters[$name]['from'])
                        && ($value <= $this->parameters[$name]['to']))
                    ) {
                        throw new IndexParameterException('The parameter with the name '
                            .$name.' is only defined in the range from '
                            .$this->parameters[$name]['from'].' to '
                            .$this->parameters[$name]['to'].'. You provided the value '
                            .$value.'.');
                    }
                }

                $this->parameters[$name]['value'] = $value;
            } else {
                throw new IndexParameterException('The '.$this->name.'
                    doesn\'t have a parameter with the name '.$name.'.');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableParameters() {
        return $this->parameters;
    }

    /**
     * {@inheritDoc}
     */
    public function getDataFormat() {
        return $this->dataFormat;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue() {
        try {
            $this->fetchData();
        } catch (IndexDataException $e) {
            /*
             * The method fetchData() is defined in this parent class to do
             * basic checks. The method is then extended by the subclasses
             * to fetch data from the database.
             *
             * In this parent class fetchData() checks if data was provided
             * from outside. If yes, it throws an IndexDataException to stop
             * the method execution in the subclasses, which is necessary to
             * prevent the subclasses from overriding the external data with
             * data from the database.
             *
             * We have to catch the exception here as we want to use the
             * external data to determine the index value by calling
             * calculateValue() in the next step.
             *
             * It is important to note that we only want to catch an
             * IndexDataException if it has been thrown because data has
             * already been set from outside. We do not want to catch an
             * exception which is thrown by the extended fetchData() method
             * in a subclass.
             *
             * If the extended fetchData() method in a subclass throws an
             * exception, this may be because of the call of setData($data)
             * at the end of the implementation of fetchData() in a subclass,
             * after all data has been fetched from the database.
             *
             * If the data validation in setData($data) then throws an
             * IndexDataException which is passed to this point, that
             * indicates that errors in the data exist which would prevent
             * the correct calculation of the index value.
             *
             * In this case we also have to pass the exception so that
             * calculateValue() is not called. When fetchData() throws
             * an exception because of existing external data, it sets
             * the exception code to 1. If the catched exception in this
             * block has the exception code 1, we don't pass the exception.
             * If it has another code (e.g. the default value 0), we pass
             * the exception.
             */
             if ($e->getCode() != 1) {
                 throw $e;
             }
        }

        $this->calculateValue();

        return $this->value;
    }

    /**
     * Fetches the data for the calculation of the index
     * if all required index parameters have a value.
     *
     * The method checks checks if data was provided from outside.
     * If not, the index fetches the data directly from the database.
     *
     * @throws IndexParameterException If a required parameter was not
     * set during the index configuration.
     * @throws IndexDataException If the data has already been set from
     * outside. Throwing this exception prevents that the data from outside
     * is overridden with the data from the database.
     */
    protected function fetchData() {
        $this->checkRequiredParameters();

        if (!is_null($this->data)) {
            /*
             * If the $data variable isn't null anymore, data was provided
             * from outside. This means, we have to stop the method execution
             * here to prevent subclasses from fetching data from the database.
             *
             * Using a return statement to stop the method execution won't work,
             * because it just ends the execution of the parent method. By using
             * an exception, we ensure that also the execution of the child method
             * is stopped. The exception should be catched in the method which
             * calls fetchData().
             *
             * To indicate that this IndexDataException is thrown to show the
             * existence of valid external data and not because of errors in the
             * data, we set the specific exception code 1 which differs from the
             * default value 0.
             */
            throw new IndexDataException('Data has still been provided
                from outside and should not be overridden.', 1);
        }
    }

    /**
     * Checks if all required index parameters have a value.
     *
     * @throws IndexParameterException If a required parameter was not
     * set during the index configuration.
     */
    private function checkRequiredParameters() {
        $missingParameters = array();

        foreach ($this->parameters as $name => $properties) {
            $required = $properties['required'];
            $value = $properties['value'];
            if ($required && is_null($value)) {
                $missingParameters[] = $name;
            }
        }

        if (count($missingParameters) > 0) {
            $missingParametersList = implode(', ', $missingParameters);
            throw new IndexParameterException('The following required
                parameters were not set: '.$missingParametersList.'.');
        }
    }

    /**
     * Calculates the value of the index and saves the value in the
     * $value attribute.
     */
    abstract protected function calculateValue();

    /**
     * Defines an index parameter by using the provided
     * method parameters.
     *
     * @param string $name The name of the parameter.
     * @param string $dataType The data type of the parameter.
     * @param mixed $from The start of the range where the parameter
     * is defined.
     * @param mixed $to The end of the range where the parameter
     * is defined.
     * @param bool $required Indicates if the parameter is
     * required for the index calculation.
     * @param mixed $value The value of the parameter.
     */
    protected function defineParameter($name, $dataType, $from, $to, $required, $value) {
        $this->parameters[$name] = array(
            'dataType' => $dataType,
            'from' => $from,
            'to' => $to,
            'required' => $required,
            'value' => $value
        );
    }

    /**
     * Checks if the provided array keys exists.
     *
     * The check is performed on the array level indicated by the
     * variable $checkLevel. The variable $paths helps the method to find
     * the correct paths down to $checkLevel. $currentLevel is used
     * together with $checkLevel to determine when the level for the
     * check is reached.
     *
     * @param array $keys The keys whose existence should be checked.
     * @param array $array The array where the keys should be checked.
     * @param int $checkLevel Indicates the level where the check should be
     * performed. Level 0 stands for the top level of the array.
     * @param int $currentLevel Indicates the level which has already
     * been reached on the way to $checkLevel.
     * @param array $path Contains all the levels from 0 to ($checkLevel - 1).
     * Each of those levels points to an array with the keys of the elements
     * which should be considered. If on one level all elements should be
     * considered, the level can be omitted. If all elements on all levels
     * should be considered, the entire $paths variable can be omitted.
     *
     * @throws IndexDataException If one of the array keys doesn't exist.
     */
    protected function checkArrayKeysExist(array $keys, array $array, $checkLevel,
        $currentLevel, array $paths = array()) {
        if ($currentLevel == $checkLevel) {
            foreach ($keys as $key) {
                if (!array_key_exists($key, $array)) {
                    throw new IndexDataException('The array key '.$key.' doesn\'t exist.');
                }
            }
        } else {
            $paths = $this->getCompletedPaths($array, $currentLevel, $paths);
            $subarrayKeys = $paths[$currentLevel];
            foreach ($subarrayKeys as $subarrayKey) {
                $subarray = $array[$subarrayKey];
                $this->checkArrayKeysExist(
                    $keys,
                    $subarray,
                    $checkLevel,
                    $currentLevel+1,
                    $paths
                );
            }
        }
    }

    /**
     * Checks if the data types are correct.
     *
     * The check is performed on the array level indicated by the
     * variable $checkLevel. The variable $paths helps the method to find
     * the correct paths down to $checkLevel. $currentLevel is used
     * together with $checkLevel to determine when the level for the
     * check is reached.
     *
     * If $checkType is 1, the data type of the array keys is compared
     * with the data type in $dataTypes[0]. If $checkType is 2, the
     * data type of all array elements with generic keys is compared with
     * the data type in $dataTypes[0]. If $checkType is 3, the data
     * type of the array elements is compared with the data type which
     * can be found in $dataTypes[$key] by using the specific key name
     * $key of an element.
     *
     * $checkType = 1 should be used to check the keys, if the array
     * contains e.g. numeric keys where the exact amount of keys and
     * the key names are determined at run time. This is e.g. the case
     * for a list of publications fetched from the database, where the
     * key name indicates the number of a publication. $checkType = 2
     * should be used to check the elements with such generic keys.
     * $checkType = 3 should only be used if the key names are known
     * before run time. This is e.g. the case when it comes to specific
     * properties of publications, where fixed key names like 'publicationId'
     * or 'citationCount' are used.
     *
     * @param array $dataTypes If $checkType is 1 or 2, $dataTypes
     * should contain only one string with the data type. If $checkType
     * is 3, $dataTypes should contain the keys of the elements
     * pointing to the data type.
     * @param array $array The array where the data types should
     * be checked.
     * @param int $checkType A value of 1 means that the keys should
     * be checked. The value 2 indicates that the elements with generic
     * keys should be verified. Finally, a value of 3 means that the
     * elements with specific key names should be checked.
     * @param int $checkLevel Indicates the level where the check should be
     * performed. Level 0 stands for the top level of the array.
     * @param int $currentLevel Indicates the level which has already
     * been reached on the way to $checkLevel.
     * @param array $paths Contains all the levels from 0 to ($checkLevel - 1).
     * Each of those levels points to an array with the keys of the elements
     * which should be considered. If on one level all elements should be
     * considered, the level can be omitted. If all elements on all levels
     * should be considered, the entire $paths variable can be omitted.
     *
     * @throws IndexDataException If the data type of one element
     * is not correct.
     */
    protected function checkDataTypesAreCorrect(array $dataTypes, array $array, $checkType,
        $checkLevel, $currentLevel, array $paths = array()) {
        if ($currentLevel == $checkLevel) {
            if ($checkType == 1) {
                $dataType = $dataTypes[0];
                foreach (array_keys($array) as $key) {
                    $this->checkDataTypeIsCorrect($dataType, $key);
                }
            } else if ($checkType == 2) {
                $dataType = $dataTypes[0];
                foreach ($array as $element) {
                    $this->checkDataTypeIsCorrect($dataType, $element);
                }
            } else if ($checkType == 3) {
                foreach ($dataTypes as $key => $dataType) {
                    $this->checkDataTypeIsCorrect($dataType, $array[$key]);
                }
            }
        } else {
            $paths = $this->getCompletedPaths($array, $currentLevel, $paths);
            $subarrayKeys = $paths[$currentLevel];
            foreach ($subarrayKeys as $subarrayKey) {
                $subarray = $array[$subarrayKey];
                $this->checkDataTypesAreCorrect(
                    $dataTypes,
                    $subarray,
                    $checkType,
                    $checkLevel,
                    $currentLevel+1,
                    $paths
                );
            }
        }
    }

    /**
     * Checks if an element has the correct data type.
     *
     * @param string $dataType The case-sensitive name of the data type.
     * @param mixed $element The element whose data type should match to
     * $dataType.
     *
     * @throws IndexDataException If the data type of the element is not
     * correct.
     */
    protected function checkDataTypeIsCorrect($dataType, $element) {
        /*
         * PHP has more than one name for some data types.
         * The php function gettype($var) which is used below, returns
         * e.g. always boolean instead of bool. Therefore it is necessary
         * to set the data type to the name used by this function to avoid
         * that this method incorrectly throws an IndexDataException.
         */
        if ($dataType == 'bool') {
            $dataType = 'boolean';
        } else if ($dataType == 'int') {
            $dataType = 'integer';
        } else if ($dataType == 'float') {
            $dataType = 'double';
        } else if (is_array($dataType)) {
            $dataType = 'array';
        }

        $elementDataType = gettype($element);
        if ($elementDataType != $dataType) {
            throw new IndexDataException('The element '.$element.' should have
                the data type '.$dataType.' but has the data type '.$elementDataType.'.');
        }
    }

    /**
     * Converts elements with wrong data types to the correct types
     * and returns the array.
     *
     * The conversion is performed on the array level indicated by
     * the variable $conversionLevel. The variable $paths helps the
     * method to find the correct paths down to $conversionLevel.
     * $currentLevel is used together with $conversionLevel to
     * determine when the level for the conversion is reached.
     *
     * This method may be used to convert elements with wrong data
     * types which may occur when fetching data from the database
     * with the fetchAll() method from the class PDOStatement.
     * Usually, the array returned by this fetchAll() method contains
     * the array elements as strings, no matter if the database column
     * has really the data type string or another type like int. This
     * causes errors when validating the data with the setData($data)
     * method. Therefore the elements with wrong data types should be
     * converted by using this method.
     *
     * @param array $dataTypes The variable should contain the keys of the
     * elements pointing to the correct data type.
     * @param array $array The array where the elements with wrong data types
     * should be converted.
     * @param int $conversionLevel Indicates the level where the conversion
     * should be performed. Level 0 stands for the top level of the array.
     * @param int $currentLevel Indicates the level which has already
     * been reached on the way to $conversionLevel.
     * @param array $paths Contains all the levels from 0 to ($conversionLevel - 1).
     * Each of those levels points to an array with the keys of the elements
     * which should be considered. If on one level all elements should be
     * considered, the level can be omitted. If all elements on all levels
     * should be considered, the entire $paths variable can be omitted.
     *
     * @return array The array with the correct data types.
     */
    protected function convertWrongDataTypes(array $dataTypes, array $array, $conversionLevel,
        $currentLevel, array $paths = array()) {
        if ($currentLevel == $conversionLevel) {
            foreach ($dataTypes as $key => $dataType) {
                if (in_array($dataType, $this->numericDataTypes)
                    && gettype($array[$key]) == 'string'
                ) {
                    /*
                     * Performing an addition with 0 forces PHP to convert
                     * the data type from string to the correct numeric one,
                     * which is one of the data types from $numericDataTypes.
                     */
                    $array[$key] += 0;
                }
            }
        } else {
            $paths = $this->getCompletedPaths($array, $currentLevel, $paths);
            $subarrayKeys = $paths[$currentLevel];
            foreach ($subarrayKeys as $subarrayKey) {
                $subarray = $array[$subarrayKey];
                $array[$subarrayKey] = $this->convertWrongDataTypes(
                    $dataTypes,
                    $subarray,
                    $conversionLevel,
                    $currentLevel+1,
                    $paths
                );
            }
        }

        return $array;
    }

    /**
     * Returns the paths which have been completed for the indicated
     * level.
     *
     * If $level doesn't exist in $paths, the method adds the keys
     * from all elements on this level to $paths by using the array
     * keys from $array.
     *
     * @param array $array Contains the elements of the level
     * together with their keys.
     * @param int $level The level where the paths should be completed.
     * @param array $paths Contains levels pointing to an array with the
     * keys of the elements which should be considered. A missing level
     * in $paths means that all elements on this level should be consired.
     *
     * @return The paths which are at least complete for $level.
     */
    private function getCompletedPaths(array $array, $level, array $paths) {
        if (!array_key_exists($level, $paths)) {
            $paths[$level] = array_keys($array);
        }

        return $paths;
    }
}
