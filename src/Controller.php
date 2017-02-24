<?php

namespace arkuuu\Publin;

/**
 * Class Controller
 *
 * @package arkuuu\Publin
 */
class Controller
{

    /**
     * @param $destination
     */
    public function redirect($destination)
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['referrer'] = Request::getUrl();

        header('Location: '.$destination, true);
        exit();
    }
}
