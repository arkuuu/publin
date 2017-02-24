<?php

namespace arkuuu\Publin\Indices;

use arkuuu\Publin\Indices\Exceptions\IndexDataException;
use arkuuu\Publin\Indices\Exceptions\IndexParameterException;

/**
 * This is a basic interface for indices and has to be implemented
 * by each concrete index.
 *
 * @package arkuuu\Publin\Indices
 */
interface Index
{

    /**
     * Returns the name of the index.
     *
     * @return string
     */
    public function getName();


    /**
     * Sets the parameters of the index.
     *
     * @param array $parameters The keys of the array represent the
     *                          name of the parameter, the array values represent the value
     *                          of the parameter.
     *
     * @throws IndexParameterException If the provided parameter names
     * don't exist or if the provided values don't meet the criteria
     * for the data type, the lower bound or the upper bound.
     */
    public function setParameters(array $parameters);


    /**
     * Returns an array which contains all parameters that can be
     * used to configure the index.
     *
     * @return array The keys of the array represent the name of the
     * parameter. Each key points to another array which contains keys
     * for the data type (key name: dataType), lower bound
     * (key name: from), the upper bound (key name: to) and the status
     * if the parameter has to be provided for the calculation of the
     * index (key name: required). Furthermore it contains the default
     * value of the parameter (key name: value).
     */
    public function getAvailableParameters();


    /**
     * Sets the data which should be used by the index to calculate
     * the value.
     *
     * This method facilitates the reuse of indices. By using this method
     * it is possible to recalculate the data basis in a new index, then
     * provide the new data basis to an existing index and let it
     * calculate the value.
     *
     * @param array $data The data array should contain the information
     *                    required by the index. Furthermore the data should match the
     *                    rules of the index for the data format. To obtain these rules,
     *                    the method getDataFormat() can be used.
     *
     * @throws IndexDataException If one tries to set the data of the index
     * without that the format of the provided data matches the data format
     * of the index.
     */
    public function setData(array $data);


    /**
     * Returns an array to show the data format of the index.
     *
     * @return array The concrete structure of the array may vary
     * depending on the index. Usually it should contain the names
     * of the array keys and the data types of the entries.
     */
    public function getDataFormat();


    /**
     * Returns a numeric value which is the calculated index
     * value for the author.
     *
     * @return mixed The return value may be from type integer or
     * float, depending on the definition of the index.
     */
    public function getValue();
}
