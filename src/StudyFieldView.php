<?php

namespace arkuuu\Publin;

/**
 * Class StudyFieldView
 *
 * @package arkuuu\Publin
 */
class StudyFieldView extends ViewWithPublications
{

    private $study_field;


    /**
     * @param StudyField $study_field
     * @param array      $publications
     */
    public function __construct(StudyField $study_field, array $publications)
    {
        parent::__construct($publications, 'studyfield');
        $this->study_field = $study_field;
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
        return $this->html($this->study_field->getName());
    }
}
