<?php

namespace arkuuu\Publin;

use arkuuu\Publin\Exceptions\NotFoundException;

/**
 * Class TypeController
 *
 * @package arkuuu\Publin
 */
class TypeController extends Controller
{

    private $db;

    private $model;


    /**
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->model = new TypeModel($db);
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
        $repo = new TypeRepository($this->db);
        $type = $repo->where('id', '=', $request->get('id'))->findSingle();
        if (!$type) {
            throw new NotFoundException('type not found');
        }

        $repo = new PublicationRepository($this->db);
        $publications = $repo->where('type_id', '=', $request->get('id'))->order('date_published', 'DESC')->find();

        $view = new TypeView($type, $publications);

        return $view->display();
    }
}
