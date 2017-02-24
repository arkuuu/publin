<?php

namespace arkuuu\Publin\Indices\Exceptions;

use Exception;

/**
 * This exception indicates a wrong usage of index parameters. It
 * can be thrown e.g. if a parameter with a wrong name is used or
 * if the parameter value doesn't meet the criteria for the lower
 * or upper bound.
 *
 * @package arkuuu\Publin\Indices\Exceptions
 */
class IndexParameterException extends Exception
{

}
