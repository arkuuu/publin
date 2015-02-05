<?php



/**
 * Handles the meta tags.
 *
 * TODO: comment
 */
abstract class MetaTags {

    /**
     * Returns an array with all supported styles.
     *
     * @return    array
     */
    public static function getStyles() {

        return array();
    }


    /**
     * Returns the meta tags for given publication and given style.
     *
     * @param    Publication $publication The publication
     * @param    string      $style       The style
     *
     * @return    string
     */
    public static function getPublicationsMetaTags(Publication $publication, $style) {

        $tags = '';

        switch ($style) {
            case 'highwire':
                $tags =
                    '<meta name="citation_title" content="' . $publication->getTitle() . '" />' . "\n\t" .
                    '<meta name="citation_publication_date" content ="' . $publication->getYear() . '" />' . "\n\t" .
                    '<meta name="citation_online_date" content ="" />' . "\n\t";
                foreach ($publication->getAuthors() as $author) {
                    $tags .= '<meta name="citation_author" content ="' . $author->getName() . '" />' . "\n\t";
                }
                $tags .= '<meta name="citation_pdf_url" content ="" />' . "\n";

                break;

            case 'dublin_core':
                $tags = '<link rel="schema.dc" href="http://purl.org/dc/elements/1.1/">'
                    . '<meta name="dc.title" content="' . $publication->getTitle() . '">';
                break;

            default:
                # code...
                break;
        }

        return $tags;
    }

}
