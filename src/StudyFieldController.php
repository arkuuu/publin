<?php

namespace arkuuu\Publin;

use arkuuu\Publin\Exceptions\NotFoundException;

/**
 * Class StudyFieldController
 *
 * @package arkuuu\Publin
 */
class StudyFieldController extends Controller
{

    private $db;


    /**
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }


    /**
     * @param Request $request
     *
     * @return string
     * @throws NotFoundException
     * @throws \Exception
     */
    public function run(Request $request)
    {
        $repo = new StudyFieldRepository($this->db);
        $study_field = $repo->where('id', '=', $request->get('id'))->findSingle();
        if (!$study_field) {
            throw new NotFoundException('study field not found');
        }

        $repo = new PublicationRepository($this->db);
        $publications = $repo->where('study_field_id', '=', $request->get('id'))->order('date_published',
            'DESC')->find();

        $view = new StudyFieldView($study_field, $publications);

        return $view->display();
    }
}
