<?php

namespace arkuuu\Publin;

/**
 * Class BrowseView
 *
 * @package arkuuu\Publin
 */
class BrowseView extends View
{

    /**
     * @var    BrowseModel
     */
    protected $model;

    /**
     * @var array
     */
    protected $browse_type_list;

    /**
     * @var mixed
     */
    protected $browse_type;


    /**
     * Constructs the browse view.
     *
     * @param BrowseModel $model The browse model
     */
    public function __construct(BrowseModel $model)
    {
        parent::__construct('browse');
        $this->model = $model;

        $this->browse_type_list =
            array(
                'recent'      => array(
                    'name'       => 'Recent Publications',
                    'url'        => './?p=browse&amp;by=recent',
                    'result_url' => './?p=publication&amp;id=',
                ),
                'author'      => array(
                    'name'       => 'Authors',
                    'url'        => './?p=browse&amp;by=author',
                    'result_url' => './?p=author&amp;id=',
                ),
                'keyword'     => array(
                    'name'       => 'Keywords',
                    'url'        => './?p=browse&amp;by=keyword',
                    'result_url' => './?p=keyword&amp;id=',
                ),
                'study_field' => array(
                    'name'       => 'Fields of Study',
                    'url'        => './?p=browse&amp;by=study_field',
                    'result_url' => './?p=study_field&amp;id=',
                ),
                'type'        => array(
                    'name'       => 'Types',
                    'url'        => './?p=browse&amp;by=type',
                    'result_url' => './?p=type&amp;id=',
                ),
                'year'        => array(
                    'name'       => 'Years',
                    'url'        => './?p=browse&amp;by=year',
                    'result_url' => './?p=browse&amp;by=year&amp;id=',
                ),
            );

        if (!array_key_exists($this->model->getBrowseType(), $this->browse_type_list)) {
            $this->browse_type = array(
                'name'       => '',
                'url'        => '',
                'result_url' => '',
            );
        } else {
            $this->browse_type = $this->browse_type_list[$this->model->getBrowseType()];
        }
    }


    /**
     * Shows the page title.
     *
     * @return string
     */
    public function showPageTitle()
    {
        return $this->showBrowseType();
    }


    /**
     * Shows the browse.
     *
     * @return string
     */
    public function showBrowseType()
    {
        return $this->browse_type['name'] ?: 'Browse';
    }


    /**
     * Shows the browse list.
     *
     * @return string
     */
    public function showBrowseList()
    {
        $string = '';
        $browse_list = $this->model->getBrowseList();
        $browse_type = $this->model->getBrowseType();

        if (!empty($browse_list)) {
            if ($browse_type == 'year') {
                foreach ($browse_list as $year) {
                    $string .= '<a class="list-group-item list-group-item-action" href="'.$this->browse_type['result_url'].$year.'">'.$year.'</a>'."\n";
                }
            } else if ($browse_type == 'author') {
                /* @var $author Author */
                foreach ($browse_list as $author) {
                    $string .= '<a class="list-group-item list-group-item-action" href="'.$this->browse_type['result_url'].$author->getId().'">'.$author->getLastName().', '.$author->getFirstName().'</a>'."\n";
                }
            } else {
                /* @var $object Object */
                foreach ($browse_list as $object) {
                    $string .= '<a class="list-group-item list-group-item-action" href="'.$this->browse_type['result_url'].$object->getId().'">'.$object->getName().'</a>'."\n";
                }
            }
        } else if (!empty($browse_type)) {
            // No results, but browse type is requested.
            $string .= '<span class="list-group-item disabled">Nothing found</span>';
        } else {
            foreach ($this->browse_type_list as $browse_type) {
                $string .= '<a class="list-group-item list-group-item-action" href="'.$browse_type['url'].'">'.$browse_type['name'].'</a>'."\n";
            }
        }

        return $string;
    }


    /**
     * Returns true if there is a browse result.
     *
     * This is used to determine whether the result list or the browse list should be shown,
     * so this returns true even if the browse result is empty.
     *
     * @return boolean
     */
    public function isBrowseResult()
    {
        return $this->model->isBrowseResult();
    }


    /**
     * Shows the browse results.
     *
     * @return string
     */
    public function showBrowseResult()
    {
        $string = '';

        foreach ($this->model->getBrowseResult() as $publication) {

            $string .= '<li class="list-group-item">'.$this->showCitation($publication).'</li>'."\n";
        }

        return $string;
    }
}
