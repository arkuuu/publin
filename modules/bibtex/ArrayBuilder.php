<?php

namespace publin\modules\bibtex;

/**
 * Class ArrayBuilder
 * is a delegate for StateBasedBibParser.
 * usage:
 * see snippet of [[#StateBasedBibParser]]
 *
 * @package publin\modules\bibtex
 */
class ArrayBuilder
{

    private $all_entries = array();

    private $current_entry;


    /**
     *
     */
    public function beginFile()
    {
    }


    /**
     *
     */
    public function endFile()
    {
    }


    /**
     * @param $final_key
     * @param $entry_value
     */
    public function setEntryField($final_key, $entry_value)
    {
        $this->current_entry[$final_key] = $entry_value;
    }


    /**
     * @param $entry_type
     */
    public function setEntryType($entry_type)
    {
        $this->current_entry['type'] = $entry_type;
    }


    /**
     * @param $entry_key
     */
    public function setEntryKey($entry_key)
    {
        $this->current_entry['cite_key'] = $entry_key;
    }


    /**
     *
     */
    public function beginEntry()
    {
        $this->current_entry = array();
    }


    /**
     *
     */
    public function endEntry()
    {
        $this->all_entries[] = $this->current_entry;
    }


    /**
     * @return array
     */
    public function getAllEntries()
    {
        return $this->all_entries;
    }
}
