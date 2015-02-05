<?php


/**
 * Handles the import and export formats.
 *
 * This should later be replaced by a modular system looking for export formats
 * found in the /export directory.
 *
 * TODO: comment
 * TODO: implement
 */
class FormatHandler {

    private $parser;


    public function __construct($format) {

        $file = './modules/'.$format.'.php';

        if (!file_exists($file)) {
            throw new Exception('file '.$file.' not found');
        }

        include $file;

        if (!class_exists($format)) {
            throw new Exception('parser for '.$format.' not found in file '.$file);
        }

        $this->parser = new $format();
    }


    public function export($data) {

        if (!method_exists($this->parser, 'export')) {
            throw new Exception('parser for '.get_class($this->parser).' offers no export');
        }

        return $this->parser->export($data);
    }


    public function import($data) {

        if (!method_exists($this->parser, 'import')) {
            throw new Exception('parser for '.get_class($this->parser).' offers no import');
        }

        return $this->parser->import($data);
    }


}
