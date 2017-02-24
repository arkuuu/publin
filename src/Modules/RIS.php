<?php

namespace arkuuu\Publin\Modules;

use arkuuu\Publin\Publication;
use arkuuu\Publin\Request;
use Exception;

/**
 * Class RIS
 *
 * @package arkuuu\Publin\Modules
 */
class RIS extends Module
{

    /**
     * @param Publication[] $publications
     *
     * @return string
     * @throws Exception
     */
    public function exportMultiple(array $publications)
    {
        $result = '';
        foreach ($publications as $publication) {
            if ($publication instanceof Publication) {
                $result .= $this->export($publication)."\n\n";
            }
        }

        return $result;
    }


    /**
     * @param Publication $publication
     *
     * @return string
     * @throws Exception
     */
    public function export(Publication $publication)
    {
        // http://de.wikipedia.org/wiki/RIS_%28Dateiformat%29

        $result = '';

        $fields = $this->createFields($publication);
        foreach ($fields as $field) {
            if ($field[1]) {
                $result .= "\n".$field[0].'  - '.htmlspecialchars($field[1]);
            }
        }
        $result .= "\n".'ER  -';

        return $result;
    }


    /**
     * @param Publication $publication
     *
     * @return array
     * @throws Exception
     */
    private function createFields(Publication $publication)
    {
        $fields = array();
        $fields[] = array('TY', $this->encodeType($publication->getTypeName()));
        foreach ($publication->getAuthors() as $keyword) {
            if ($keyword->getLastName() && $keyword->getFirstName()) {
                $fields[] = array('AU', $keyword->getLastName().', '.$keyword->getFirstName());
            }
        }
        $fields[] = array('T1', $publication->getTitle()); // TODO: check if valid
        $fields[] = array('JA', $publication->getJournal()); // TODO: check if valid
        $fields[] = array('TI', $publication->getBooktitle()); // TODO: check if valid
        $fields[] = array('VL', $publication->getVolume());
        $fields[] = array('IS', $publication->getNumber());
        $fields[] = array('SP', $publication->getFirstPage());
        $fields[] = array('EP', $publication->getLastPage());
        $fields[] = array('PY', $publication->getDatePublished('Y/m/d'));
        $fields[] = array('PB', $publication->getPublisher());
        $fields[] = array('N1', $publication->getNote());

        $file = $publication->getFullTextFile();
        if ($file) {
            $fields[] = array(
                'L1',
                Request::createUrl(array(
                    'p'       => 'publication',
                    'id'      => $publication->getId(),
                    'file_id' => $file->getId(),
                ), true),
            );
        }
        $fields[] = array('UR', $publication->getDoi());
        $fields[] = array('SN', $publication->getIsbn());
        $fields[] = array('AB', $publication->getAbstract());
        foreach ($publication->getKeywords() as $keyword) {
            $fields[] = array('KW', $keyword->getName());
        }

        return $fields;
    }


    /**
     * @param $type
     *
     * @return string
     * @throws Exception
     */
    private function encodeType($type)
    {
        switch ($type) {
            case 'article':
                return 'JOUR';
                break;
            case 'inproceedings':
                return 'CONF';
                break;
            case 'incollection':
            case 'inbook':
                return 'CHAP';
                break;
            case 'book':
                return 'BOOK';
                break;
            case 'mastersthesis':
            case 'phdthesis':
                return 'THES';
                break;
            case 'techreport':
                return 'RPRT'; // TODO: check if valid
                break;
            case 'misc':
                return 'GEN';
                break;
            case 'unpublished':
                return 'UNPB';
                break;
            default:
                throw new Exception('unknown or missing publication type');
                break;
        }
    }
}
