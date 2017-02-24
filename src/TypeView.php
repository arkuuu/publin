<?php

namespace arkuuu\Publin;

/**
 * Class TypeView
 *
 * @package arkuuu\Publin
 */
class TypeView extends ViewWithPublications
{

    private $type;


    /**
     * @param Type  $type
     * @param array $publications
     */
    public function __construct(Type $type, array $publications)
    {
        parent::__construct($publications, 'type');
        $this->type = $type;
    }


    /**
     * @return string
     */
    public function showPageTitle()
    {
        return $this->html($this->showName());
    }


    /**
     * @return string
     */
    public function showName()
    {
        return $this->html($this->type->getName());
    }
}
