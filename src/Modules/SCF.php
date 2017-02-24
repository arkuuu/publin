<?php

namespace arkuuu\Publin\Modules;

/**
 * Class SCF
 *
 * @package arkuuu\Publin\Modules
 */
class SCF extends Module
{

    /**
     * @var array
     */
    private $fields;


    public function __construct()
    {
        /* Map your fields here. You can change the order or leave out fields. */
        $this->fields = array(
            /* scf field		=> your field */
            'type'             => 'type',
            'authors'          => 'authors',
            'title'            => 'title',
            'journal_name'     => 'journal',
            'booktitle'        => 'booktitle',
            'publisher'        => 'publisher',
            'year'             => 'date_published',
            'volume'           => 'volume',
            'pages_from'       => 'pages_from',
            'pages_to'         => 'pages_to',
            'number'           => 'number',
            'series'           => 'series',
            'abstract'         => 'abstract',
            'copyright'        => 'copyright',
            'url'              => 'url',
            'doi'              => 'doi',
            'isbn'             => 'isbn',
            'keywords'         => 'keywords',
            'citations'        => 'citations',
            'isi_fieldofstudy' => 'study_field',
        );
    }


    /**
     *
     * @param string $input
     *
     * @return array
     * @throws ScfInvalidFormatException
     */
    public function import($input)
    {
        // Check if string starts with a '{'. In that case $input is a single
        // object and not a array
        if (strpos($input, '{') == 0) {
            // Convert to array
            $input = '['.$input.']';
        }
        $entries = json_decode($input, $assoc = true);
        if (!$entries) {
            throw new ScfInvalidFormatException('Input is no valid JSON. JSON error: '.json_last_error_msg());
        }

        $result = array();
        foreach ($entries as $entry) {
            if (array_key_exists('citations', $entry)) {
                foreach ($entry['citations'] as $key => $citation) {
                    $citation_entry = self::extractEntry($citation);
                    $entry['citations'][$key] = $citation_entry['title'];
                    $result[] = $citation_entry;
                }
            }

            // Add the entry
            $result[] = self::extractEntry($entry);
        }

        return $result;
    }


    /**
     *
     * @param array $entry
     *
     * @return array
     */
    private function extractEntry(array $entry)
    {
        $result_entry = array();

        foreach ($entry as $scf_field => $value) {
            if (isset($this->fields[$scf_field])) {
                $your_field = $this->fields[$scf_field];
                if ($value) {
                    if ($scf_field == 'authors') {
                        $result_entry[$your_field] = array();
                        foreach ($value as $author_name) {
                            $result_entry[$your_field][] = self::extractAuthor($author_name);
                        }
                    } else if ($scf_field == 'year') {
                        $result_entry[$your_field] = self::extractDate($value);
                    } /* The rest */
                    else {
                        $result_entry[$your_field] = $value;
                    }
                }
            }
        }

        return $result_entry;
    }


    /**
     * @param    string $string
     *
     * @return    array
     */
    private function extractAuthor($string)
    {
        // Note: copied from Bibtex.php
        if (substr_count($string, ',') == 1) {
            $names = explode(',', $string);
            $given = $names[1];
            $family = $names[0];
        } else if (substr_count($string, ' ') == 1) {
            $names = explode(' ', $string);
            $given = $names[0];
            $family = $names[1];
        } else if (substr_count($string, ' ') > 1) {
            $pos = strrpos($string, ' ');
            $given = substr($string, 0, $pos);
            $family = substr($string, $pos);
        } else {
            $given = '';
            $family = '';
        }

        $author = array();
        $author['given'] = trim($given);
        $author['family'] = trim($family);

        return $author;
    }


    private function extractDate($input_year)
    {
        // TODO: may set month
        $month = 'January';
        $day = '01';
        $date = strtotime($day.' '.$month.' '.$input_year);
        if ($date) {
            return date('Y-m-d', $date);
        } else {
            return false;
        }
    }
}
