<?php

namespace publin\src;

use publin\config\Config;

/**
 * Class Request
 *
 * @package publin\src
 */
class Request
{


    public function __construct()
    {
    }


    /**
     * @return string
     */
    public static function getUrl()
    {
        return self::createUrl($_GET);
    }


    /**
     * @param array $parameters
     * @param bool  $absolute
     *
     * @return string
     */
    public static function createUrl(array $parameters = array(), $absolute = false)
    {
        $url = http_build_query($parameters);
        $url = $url ? '?'.$url : '';

        if ($absolute) {
            $root = Config::USE_SSL ? Config::ROOT_URL_SSL : Config::ROOT_URL;

            return $root.$url;
        } else {
            return $url;
        }
    }


    /**
     * @param string $name
     *
     * @return bool|array
     */
    public function post($name = '')
    {
        if (!empty($name) && isset($_POST[$name])) {

            return $_POST[$name];
        } else if (empty($name) && !empty($_POST)) {
            return $_POST;
        } else {
            return false;
        }
    }


    /**
     * @param string $name
     *
     * @return bool
     */
    public function get($name = '')
    {
        if (!empty($name) && !empty($_GET[$name])) {

            return $_GET[$name];
        } else if (empty($name) && !empty($_GET)) {
            return $_GET;
        } else {
            return false;
        }
    }
}
