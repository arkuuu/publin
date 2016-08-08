<?php

namespace publin\src\indices\other;

use publin\src\indices\exceptions\IndexDataException;

/**
 * This class contains helper methods which perform some conversion
 * and removal tasks.
 *
 * @package publin\src\indices\other
 */
class IndexHelper {

    /**
     * Contains the numeric data types of PHP.
     *
     * The variable is used for different data
     * type checks in this class.
     *
     * @var array
     */
    private static $numericDataTypes = array(
        'int',
        'integer',
        'float',
        'double'
    );

    /**
     * Returns an array with all numeric data types of PHP.
     *
     * @return array Contains all numeric data types which are
     * available in PHP.
     */
    public static function getNumericDataTypes() {
        return self::$numericDataTypes;
    }

    /**
     * Converts a string with an array like syntax to an array and returns
     * the array.
     *
     * @param string $string Contains either pairs of keys and values in the
     * format '"keyName" => "value"' or just values in the format "value".
     * @param bool $containsKeys Is true, if $string contains keys pointing to
     * values or false, if $string just contains values.
     *
     * @return array The array which has been converted from $string.
     */
    public static function convertStringToArray($string, $containsKeys) {
        $array = array();

        $lines = explode(',', $string);
        foreach ($lines as $line) {
            if ($containsKeys) {
                $line = str_replace(' => ', '=>', $line);
                $line = explode('=>', $line);
                $key = self::trimUnwantedCharactersFrom($line[0], '"');
                $value = self::trimUnwantedCharactersFrom($line[1], '"');

                $array[$key] = self::convertWrongDataTypeOf($value);
            } else {
                $value = self::trimUnwantedCharactersFrom($line, '"');

                $array[] = self::convertWrongDataTypeOf($value);
            }
        }

        return $array;
    }

    /**
     * Trims unwanted characters like surrounding whitespaces, line
     * breaks and tabs which are outside of the delimiters in a string
     * and returns the string.
     *
     * @param string $string The string where the unwanted characters
     * should be removed.
     * @param string $delimiter The delimiter which identifies the
     * start and the end of the section. All characters which are
     * outside of the section are removed.
     *
     * @return string The string without the unwanted characters and
     * the delimiter.
     */
    public static function trimUnwantedCharactersFrom($string, $delimiter) {
        $start = strpos($string, $delimiter);
        $end = strrpos($string, $delimiter);
        $length = $end - $start;
        $string = substr($string, $start, $length);
        $string = str_replace($delimiter, '', $string);

        return $string;
    }

    /**
     * Converts an element with the data type string to the correct
     * data type if the element is numeric or boolean.
     *
     * @param string $element The element whose data type should be
     * converted.
     *
     * @return mixed The element with the correct data type.
     */
    public static function convertWrongDataTypeOf($element) {
        if (is_numeric($element)) {
            /*
             * Performing an addition with 0 forces PHP to convert
             * the data type from string to the correct numeric one.
             */
            $element += 0;
        } else if ($element == 'true') {
            $element = true;
        } else if ($element == 'false') {
            $element = false;
        }

        return $element;
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
    public static function checkArrayKeysExist(array $keys, array $array, $checkLevel,
        $currentLevel, array $paths = array()) {
            if ($currentLevel == $checkLevel) {
                foreach ($keys as $key) {
                    if (!array_key_exists($key, $array)) {
                        throw new IndexDataException('The array key '.$key.' doesn\'t exist.');
                    }
                }
            } else {
                $paths = self::getCompletedPaths($array, $currentLevel, $paths);
                $subarrayKeys = $paths[$currentLevel];
                foreach ($subarrayKeys as $subarrayKey) {
                    $subarray = $array[$subarrayKey];
                    self::checkArrayKeysExist(
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
    private static function getCompletedPaths(array $array, $level, array $paths) {
        if (!array_key_exists($level, $paths)) {
            $paths[$level] = array_keys($array);
        }

        return $paths;
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
    public static function checkDataTypesAreCorrect(array $dataTypes, array $array, $checkType,
        $checkLevel, $currentLevel, array $paths = array()) {
            if ($currentLevel == $checkLevel) {
                if ($checkType == 1) {
                    $dataType = $dataTypes[0];
                    foreach (array_keys($array) as $key) {
                        self::checkDataTypeIsCorrect($dataType, $key);
                    }
                } else if ($checkType == 2) {
                    $dataType = $dataTypes[0];
                    foreach ($array as $element) {
                        self::checkDataTypeIsCorrect($dataType, $element);
                    }
                } else if ($checkType == 3) {
                    foreach ($dataTypes as $key => $dataType) {
                        self::checkDataTypeIsCorrect($dataType, $array[$key]);
                    }
                }
            } else {
                $paths = self::getCompletedPaths($array, $currentLevel, $paths);
                $subarrayKeys = $paths[$currentLevel];
                foreach ($subarrayKeys as $subarrayKey) {
                    $subarray = $array[$subarrayKey];
                    self::checkDataTypesAreCorrect(
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
    public static function checkDataTypeIsCorrect($dataType, $element) {
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
    public static function convertWrongDataTypes(array $dataTypes, array $array, $conversionLevel,
        $currentLevel, array $paths = array()) {
            if ($currentLevel == $conversionLevel) {
                foreach ($dataTypes as $key => $dataType) {
                    if (in_array($dataType, self::$numericDataTypes)
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
                $paths = self::getCompletedPaths($array, $currentLevel, $paths);
                $subarrayKeys = $paths[$currentLevel];
                foreach ($subarrayKeys as $subarrayKey) {
                    $subarray = $array[$subarrayKey];
                    $array[$subarrayKey] = self::convertWrongDataTypes(
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
}
