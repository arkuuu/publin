<?php

namespace publin\src;

use BadMethodCallException;
use DomainException;

class FormatHandler {

	private $parser;


	public function __construct($format) {

		$file = './modules/'.$format.'.php';

		if (!file_exists($file)) {
			throw new DomainException('file '.$file.' not found');
		}

		include $file;

		if (!class_exists($format)) {
			throw new DomainException('parser for '.$format.' not found in file '.$file);
		}

		$this->parser = new $format();
	}


	public function export($data) {

		if (!method_exists($this->parser, 'export')) {
			throw new BadMethodCallException('parser for '.get_class($this->parser).' offers no export');
		}

		return $this->parser->export($data);
	}


	public function import($data) {

		if (!method_exists($this->parser, 'import')) {
			throw new BadMethodCallException('parser for '.get_class($this->parser).' offers no import');
		}

		return $this->parser->import($data);
	}


}
