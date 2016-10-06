<?php

namespace publin;

spl_autoload_register(function ($class) {

    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $root = substr(__DIR__, 0, -(strlen(__NAMESPACE__)));
    $file = $root.$path.'.php';

    if (file_exists($file)) {
        /** @noinspection PhpIncludeInspection */
        require $file;
    }
});
