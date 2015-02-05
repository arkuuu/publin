<?php

require_once 'Author.php';
require_once 'Publication.php';



/**
 * Handles the links to other bibliographic services.
 *
 * TODO: comment
 */
abstract class BibLink {

    /**
     * Returns an array with all supported services.
     *
     * @return    array
     */
    public static function getServices() {

        return array('Google', 'Base');
    }


    /**
     * Returns the link for given author and given service.
     *
     * @param    Author $author  The author
     * @param    string $service The service
     *
     * @return    string
     */
    public static function getAuthorsLink(Author $author, $service) {

        switch ($service) {
            case 'Google':
                return 'http://scholar.google.com/scholar?q='
                . urlencode('"' . $author->getFirstName() . ' ' . $author->getLastName() . '"');
                break;

            case 'Base':
                return 'http://www.base-search.net/Search/Results?lookfor=aut:'
                . urlencode('"' . $author->getFirstName() . ' ' . $author->getLastName() . '"');
                break;

            default:
                return 'unknown service!';
                break;
        }
    }


    /**
     * Returns the link for given publication and given service.
     *
     * @param    Publication $publication The publication
     * @param    string      $service     The service
     *
     * @return    string
     */
    public static function getPublicationsLink(Publication $publication, $service) {

        switch ($service) {
            case 'Google':
                return 'http://scholar.google.com/scholar?q=allintitle:'
                . urlencode('"' . $publication->getTitle() . '"');
                break;

            case 'Base':
                return 'http://www.base-search.net/Search/Results?lookfor=tit:'
                . urlencode('"' . $publication->getTitle() . '"');
                break;

            default:
                return 'unknown service!';
                break;
        }
    }

}
