<?php

namespace publin\src;

use BadMethodCallException;
use DomainException;

class FormatHandler {

	private $parser;


	public function __construct($format) {

		$class = '\\publin\\modules\\'.$format;
		if (!class_exists($class)) {
			throw new DomainException('parser for '.$format.' not found');
		}

		$this->parser = new $class();
	}


	public function export(Publication $publication) {

		if (!method_exists($this->parser, 'export')) {
			throw new BadMethodCallException('parser for '.get_class($this->parser).' offers no export');
		}

		return $this->parser->export($publication);
	}


	public function import($data) {

		if (!method_exists($this->parser, 'import')) {
			throw new BadMethodCallException('parser for '.get_class($this->parser).' offers no import');
		}

		return $this->parser->import($data);
	}


}
