<?php

namespace arkuuu\Publin;

use arkuuu\Publin\Exceptions\LoginRequiredException;
use arkuuu\Publin\Exceptions\NotFoundException;
use arkuuu\Publin\Exceptions\PermissionRequiredException;
use BadMethodCallException;
use Exception;
use UnexpectedValueException;

/**
 * Class KeywordController
 *
 * @package arkuuu\Publin
 */
class KeywordController extends Controller
{

    private $db;

    private $auth;

    private $model;

    private $errors;


    /**
     * @param Database $db
     * @param Auth     $auth
     */
    public function __construct(Database $db, Auth $auth)
    {
        $this->db = $db;
        $this->auth = $auth;
        $this->model = new KeywordModel($this->db);
        $this->errors = array();
    }


    /**
     * @param Request $request
     *
     * @return string
     * @throws PermissionRequiredException
     * @throws Exception
     * @throws LoginRequiredException
     * @throws NotFoundException
     */
    public function run(Request $request)
    {
        if ($request->post('action')) {
            $method = $request->post('action');
            if (method_exists($this, $method)) {
                $this->$method($request);
            } else {
                throw new BadMethodCallException;
            }
        }

        $repo = new KeywordRepository($this->db);
        $keyword = $repo->where('id', '=', $request->get('id'))->findSingle();
        if (!$keyword) {
            throw new NotFoundException('keyword not found');
        }

        $repo = new PublicationRepository($this->db);
        $publications = $repo->where('keyword_id', '=', $request->get('id'))->order('date_published', 'DESC')->find();

        if ($request->get('m') === 'edit') {
            $view = new KeywordView($keyword, $publications, $this->errors, true);
        } else {
            $view = new KeywordView($keyword, $publications, $this->errors);
        }

        return $view->display();
    }


    /** @noinspection PhpUnusedPrivateMethodInspection
     * @param Request $request
     *
     * @return bool
     * @throws PermissionRequiredException
     * @throws LoginRequiredException
     */
    private function delete(Request $request)
    {
        if (!$this->auth->checkPermission(Auth::DELETE_KEYWORD)) {
            throw new PermissionRequiredException(Auth::DELETE_KEYWORD);
        }

        $id = Validator::sanitizeNumber($request->get('id'));
        if (!$id) {
            throw new UnexpectedValueException;
        }

        $confirmed = Validator::sanitizeBoolean($request->post('delete'));
        if (!$confirmed) {
            $this->errors[] = 'Please confirm the deletion';

            return false;
        }

        $this->model->delete($id);
        $this->redirect(Request::createUrl(array('p' => 'browse', 'by' => 'keyword')));
        exit;
    }


    /** @noinspection PhpUnusedPrivateMethodInspection
     * @param Request $request
     *
     * @return bool|int
     * @throws PermissionRequiredException
     * @throws LoginRequiredException
     */
    private function edit(Request $request)
    {
        if (!$this->auth->checkPermission(Auth::EDIT_KEYWORD)) {
            throw new PermissionRequiredException(Auth::EDIT_KEYWORD);
        }

        $id = Validator::sanitizeNumber($request->get('id'));
        if (!$id) {
            throw new UnexpectedValueException;
        }

        $validator = $this->model->getValidator();
        if ($validator->validate($request->post())) {
            $input = $validator->getSanitizedResult();
            $this->model->update($id, $input);

            return true;
        } else {
            $this->errors = array_merge($this->errors, $validator->getErrors());

            return false;
        }
    }
}
