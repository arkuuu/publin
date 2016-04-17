<?php


namespace publin\src;

use BadMethodCallException;
use publin\src\exceptions\NotFoundException;
use publin\src\exceptions\PermissionRequiredException;
use UnexpectedValueException;
use publin\src\indices\IndexFactory;
use publin\config\Config;
use publin\src\indices\other\IndexHelper;

/**
 * Class AuthorController
 *
 * @package publin\src
 */
class AuthorController extends Controller {

	private $db;
	private $auth;
	private $model;
	private $errors;

	/**
	 * Contains an instance of the index factory
	 * which facilitates the use of the indices.
	 *
	 * @var IndexFactory
	 */
	private $indexFactory;

	/**
	 * @param Database $db
	 * @param Auth     $auth
	 */
	public function __construct(Database $db, Auth $auth) {

		$this->db = $db;
		$this->auth = $auth;
		$this->model = new AuthorModel($this->db);
		$this->errors = array();
		$this->indexFactory = new IndexFactory($this->db);
	}


	/**
	 * @param Request $request
	 *
	 * @return string
	 * @throws \Exception
	 * @throws exceptions\NotFoundException
	 */
	public function run(Request $request) {

		if ($request->post('action')) {
			$method = $request->post('action');
			if (method_exists($this, $method)) {
				$this->$method($request);
			}
			else {
				throw new BadMethodCallException;
			}
		}

		$repo = new AuthorRepository($this->db);
		$author = $repo->select()->where('id', '=', $request->get('id'))->findSingle();
		if (!$author) {
			throw new NotFoundException('author not found');
		}

		$repo = new PublicationRepository($this->db);
		$publications = $repo->select()->where('author_id', '=', $request->get('id'))->order('date_published', 'DESC')->find();

		/*
		 * The configuration of the index parameters and the selection
		 * of the requested indices is realized in the Config class of
		 * Publin by using class constants. As class constants can't be
		 * arrays below PHP 5.6, the configuration is done by using a
		 * string with an array like syntax. The strings have to be
		 * converted back to an array before calling e.g. the method
		 * fetchIndices($requestedIndices).
		 */
        if (!is_null(Config::INDICES_PARAMETERS)) {
            $parameters = IndexHelper::convertStringToArray(Config::INDICES_PARAMETERS, true);
        } else {
            $parameters = array();
        }
        if (!is_null(Config::INDICES_SELECTION)) {
            $requestedIndices = IndexHelper::convertStringToArray(Config::INDICES_SELECTION, false);
        } else {
            $requestedIndices = null;
        }

		$this->configureIndices($parameters, $request);
		$indices = $this->fetchIndices($requestedIndices);

		if ($request->get('m') === 'edit') {
			$view = new AuthorView($author, $publications, $indices, $this->errors, true);
		}
		else {
			$view = new AuthorView($author, $publications, $indices, $this->errors);
		}

		return $view->display();
	}

	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return bool
	 * @throws PermissionRequiredException
	 * @throws exceptions\LoginRequiredException
	 */
	private function delete(Request $request) {

		if (!$this->auth->checkPermission(Auth::DELETE_AUTHOR)) {
			throw new PermissionRequiredException(Auth::DELETE_AUTHOR);
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
		$this->redirect(Request::createUrl(array('p' => 'browse', 'by' => 'author')));
		exit;
	}


	/** @noinspection PhpUnusedPrivateMethodInspection
	 * @param Request $request
	 *
	 * @return bool|int
	 * @throws PermissionRequiredException
	 * @throws exceptions\LoginRequiredException
	 */
	private function edit(Request $request) {

		if (!$this->auth->checkPermission(Auth::EDIT_AUTHOR)) {
			throw new PermissionRequiredException(Auth::EDIT_AUTHOR);
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
		}
		else {
			$this->errors = array_merge($this->errors, $validator->getErrors());

			return false;
		}
	}

	/**
	 * Configures the indices by setting the values for the index parameters.
	 *
	 * @param array $parameters Contains the index parameters.
	 * @param Request $request Contains some input data like the author id
	 * which is necessary for the configuration of the indices.
	 */
	private function configureIndices(array $parameters, Request $request) {
	    $parameters['authorId'] = intval($request->get('id'));

	    $this->indexFactory->setParameters($parameters);
	}

	/**
	 * Fetches the requested indices.
	 *
	 * The method parameter $requestedIndices allows to control if all indices
	 * or only a subset of the implemented indices should be returned.
	 *
	 * @param array|null $requestedIndices The parameter is either an array
	 * containing the case-sensitive names of the requested indices or null,
	 * if all implemented indices should be returned.
	 *
	 * @return array The keys of the array represent the name of the index,
	 * the value stands for the instance of the index.
	 */
	private function fetchIndices(array $requestedIndices = null) {
	    if (is_null($requestedIndices)) {
	        return $this->indexFactory->getAllIndices();
	    }

	    $indices = array();
	    foreach ($requestedIndices as $indexName) {
	        $indices[$indexName] = $this->indexFactory->getIndex($indexName);
	    }

	    return $indices;
	}
}
