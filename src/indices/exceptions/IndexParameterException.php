<?php

namespace publin\src\indices\exceptions;

use Exception;

/**
 * This exception indicates a wrong usage of index parameters. It
 * can be thrown e.g. if a parameter with a wrong name is used or
 * if the parameter value doesn't meet the criteria for the lower
 * or upper bound.
 *
 * @package publin\src\indices\exceptions
 */
class IndexParameterException extends Exception
{

}
