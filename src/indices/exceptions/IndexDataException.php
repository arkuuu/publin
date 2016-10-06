<?php

namespace publin\src\indices\exceptions;

use Exception;

/**
 * This exception indicates a wrong usage of index data. It can
 * be thrown e.g. if one tries to set the data of an index without
 * that the format of the provided data matches the data format
 * of the index.
 *
 * @package publin\src\indices\exceptions
 */
class IndexDataException extends Exception
{

}
