<?php

require_once 'View.php';


/**
 * View for all static pages
 *
 * TODO: comment
 */
class GenericView extends View {

    /**
     * @var    string
     */
    private $page;


    /**
     * Constructs the generic view.
     *
     * @param    string $page The page (=template file)
     */
    public function __construct($page) {

        parent::__construct($page);
        $this->page = $page;
    }


    /**
     * Shows the page title.
     *
     * @return    string
     */
    public function showPageTitle() {

        $string = ucfirst($this->page);    // TODO: doesn't work with non UTF chars
        $string = str_replace('_', ' ', $string);

        return $string;
    }

}
