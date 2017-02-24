<?php

namespace arkuuu\Publin\Indices;

use arkuuu\Publin\Database;
use arkuuu\Publin\Indices\Exceptions\IndexDataException;
use arkuuu\Publin\Indices\Exceptions\IndexParameterException;
use arkuuu\Publin\Indices\Other\IndexHelper;

/**
 * This is an abstract class which takes care of some common tasks
 * for indices. The concrete indices may use it by inheriting from it.
 *
 * @package arkuuu\Publin\Indices
 */
abstract class AbstractIndex implements Index
{

    /**
     * This is an instance of the Publin database class.
     *
     * The database should be provided when constructing the index.
     *
     * @var Database
     */
    protected $db;

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
            'from'     => 1,
            'to'       => PHP_INT_MAX,
            'required' => true,
            'value'    => null,
        ),
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
     * Constructs the index.
     *
     * @param Database $db An instance of the Publin
     *                     database class.
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters)
    {
        foreach ($parameters as $name => $value) {
            if (array_key_exists($name, $this->parameters)) {
                try {
                    IndexHelper::checkDataTypeIsCorrect(
                        $this->parameters[$name]['dataType'],
                        $value
                    );
                } catch (IndexDataException $e) {
                    throw new IndexParameterException('The parameter with the name '
                        .$name.' should have the data type '
                        .$this->parameters[$name]['dataType'].' but has the data type '
                        .gettype($value));
                }

                if (in_array($this->parameters[$name]['dataType'], IndexHelper::getNumericDataTypes())) {
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
    public function getAvailableParameters()
    {
        return $this->parameters;
    }


    /**
     * {@inheritDoc}
     */
    public function getDataFormat()
    {
        return $this->dataFormat;
    }


    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
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
    protected function fetchData()
    {
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
    private function checkRequiredParameters()
    {
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
     * @param string $name     The name of the parameter.
     * @param string $dataType The data type of the parameter.
     * @param mixed  $from     The start of the range where the parameter
     *                         is defined.
     * @param mixed  $to       The end of the range where the parameter
     *                         is defined.
     * @param bool   $required Indicates if the parameter is
     *                         required for the index calculation.
     * @param mixed  $value    The value of the parameter.
     */
    protected function defineParameter($name, $dataType, $from, $to, $required, $value)
    {
        $this->parameters[$name] = array(
            'dataType' => $dataType,
            'from'     => $from,
            'to'       => $to,
            'required' => $required,
            'value'    => $value,
        );
    }
}
