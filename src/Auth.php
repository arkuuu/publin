<?php

namespace arkuuu\Publin;

use arkuuu\Publin\Exceptions\DBDuplicateEntryException;
use arkuuu\Publin\Exceptions\DBException;
use arkuuu\Publin\Exceptions\DBForeignKeyException;
use arkuuu\Publin\Exceptions\LoginRequiredException;
use Exception;

require_once 'password_compat.php';


/**
 * Class Auth
 *
 * @package arkuuu\Publin
 */
class Auth
{

    const ACCESS_RESTRICTED_FILES = 'access_restricted_files';

    const ACCESS_HIDDEN_FILES = 'access_hidden_files';

    const SUBMIT_PUBLICATION = 'publication_submit';

    const EDIT_PUBLICATION = 'publication_edit';

    const DELETE_PUBLICATION = 'publication_delete';

    const EDIT_AUTHOR = 'author_edit';

    const DELETE_AUTHOR = 'author_delete';

    const EDIT_KEYWORD = 'keyword_edit';

    const DELETE_KEYWORD = 'keyword_delete';

    const MANAGE = 'manage';

    const ALGORITHM = PASSWORD_BCRYPT;

    const ALGORITHM_COST = 10;

    const SESSION_EXPIRE_TIME = 1000;

    private $db;


    /**
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;

        if (!isset($_SESSION)) {
            session_start();
        }
    }


    /**
     * @param int $length
     *
     * @return string
     */
    public static function generatePassword($length = 10)
    {
        $chars = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $password = '';

        for ($i = 0; $i < $length; $i++) {
            $n = rand(0, strlen($chars) - 1);
            $password .= $chars[$n];
        }

        return $password;
    }


    /**
     * @param $username
     * @param $password
     *
     * @return bool
     * @throws DBException
     */
    public function login($username, $password)
    {
        if ($this->validateLogin($username, $password) === true) {
            $repo = new UserRepository($this->db);
            $user = $repo->where('name', '=', $username)->findSingle(true);

            $this->db->query('UPDATE users SET date_last_login = NOW() WHERE id = '.$user->getId().';');

            session_regenerate_id(true);
            $_SESSION['user'] = $user;
            $_SESSION['last_activity'] = time();

            return true;
        } else {
            return false;
        }
    }


    /**
     * @param $username
     * @param $password
     *
     * @return bool
     * @throws DBDuplicateEntryException
     * @throws DBForeignKeyException
     */
    public function validateLogin($username, $password)
    {
        $query = 'SELECT password FROM users WHERE name = :name LIMIT 1;';
        $this->db->prepare($query);
        $this->db->bindValue(':name', $username);
        $this->db->execute();

        $hash = $this->db->fetchColumn();

        if (password_verify($password, $hash)) {
            if (password_needs_rehash($hash, self::ALGORITHM, array('cost' => self::ALGORITHM_COST))) {
                $this->setPassword($username, $password);
            }

            return true;
        } else {
            return false;
        }
    }


    /**
     * @param $username
     * @param $password
     *
     * @return bool
     * @throws Exception
     * @throws DBDuplicateEntryException
     * @throws DBForeignKeyException
     */
    public function setPassword($username, $password)
    {
        $hash = $this->hashPassword($password);

        $query = 'UPDATE users SET password = :hash WHERE name = :name;';
        $this->db->prepare($query);
        $this->db->bindValue(':hash', $hash);
        $this->db->bindValue(':name', $username);

        return $this->db->execute();
    }


    /**
     * @param $password
     *
     * @return mixed
     * @throws Exception
     */
    public static function hashPassword($password)
    {
        $hash = password_hash($password, self::ALGORITHM, array('cost' => self::ALGORITHM_COST));

        if ($hash !== false) {
            return $hash;
        } else {
            throw new Exception('Something wrong with hashPassword');
        }
    }


    /**
     * @param      $permission_name
     *
     * @param null $user_id
     *
     * @return bool
     * @throws LoginRequiredException
     * @throws DBDuplicateEntryException
     * @throws DBForeignKeyException
     */
    public function checkPermission($permission_name, $user_id = null)
    {
        if (is_null($user_id)) {
            $user = $this->getCurrentUser();
            $user_id = $user->getId();
        }
        $query = 'SELECT COUNT(*) FROM permissions r
            LEFT JOIN roles_permissions rrp ON (rrp.permission_id = r.id)
            LEFT JOIN users_roles rur ON (rur.role_id = rrp.role_id)
            WHERE r.name = :permission_name AND rur.user_id = :user_id;';
        $this->db->prepare($query);
        $this->db->bindValue(':permission_name', $permission_name);
        $this->db->bindValue(':user_id', $user_id);
        $this->db->execute();

        $count = $this->db->fetchColumn();

        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * @return bool|User
     * @throws LoginRequiredException
     */
    public static function getCurrentUser()
    {
        if (isset($_SESSION['user'])) {
            return $_SESSION['user'];
        } else {
            throw new LoginRequiredException();
        }
    }


    /**
     * @return bool
     */
    public function checkLoginStatus()
    {
        if (isset($_SESSION['user']) && isset($_SESSION['last_activity'])) {
            if (time() - $_SESSION['last_activity'] > self::SESSION_EXPIRE_TIME) {
                $this->logout();

                return false;
            } else {
                $_SESSION['last_activity'] = time();

                return true;
            }
        } else {
            return false;
        }
    }


    /**
     *
     */
    public function logout()
    {
        session_unset();
        session_destroy();
        session_start();
    }
}
