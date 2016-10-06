<?php

namespace publin\src;

use publin\src\exceptions\NotFoundException;

/**
 * Class StudyFieldController
 *
 * @package publin\src
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
