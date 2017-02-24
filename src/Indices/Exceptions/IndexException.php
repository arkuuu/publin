<?php

namespace arkuuu\Publin\Indices\Exceptions;

use Exception;

/**
 * This exception indicates general problems with the usage of the
 * indices. It can be thrown e.g. if one tries get an index from
 * the index factory by providing a name which cannot be resolved
 * to an existing index.
 *
 * @package arkuuu\Publin\Indices\Exceptions
 */
class IndexException extends Exception
{

}
