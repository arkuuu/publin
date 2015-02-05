<?php



/**
 * Handles citation styles.
 *
 * TODO: comment
 * TODO: implement
 */
abstract class Citation {

    /**
     * Returns an array with all supported styles.
     *
     * @return    array
     */
    public static function getStyles() {

        return array();
    }


    /**
     * Returns the citation for given publication and given style.
     *
     * @param    Publication $publication The publication
     * @param    string      $style       The style (optional)
     *
     * @return    string
     */
    public static function getCitation(Publication $publication, $style) {

        $file = './modules/citation_styles/' . $style . '/' . $publication->getTypeName() . '.php';
        $file_fallback = './modules/citation_styles/default.php';
        $publication_url = './?p=publication&amp;id=';
        $citation = '';

        if (file_exists($file)) {
            include $file;
        }
        else {
            // TODO: show error in log
            include $file_fallback;
        }

        return $citation;
    }

}
