<?php

namespace RePok {

    if (!file_exists(dirname(__DIR__) . '/config/config.php')) {
        die("The required configuration file at '" . dirname(__DIR__) . "/config/config.php' cannot be found.");
    }

    // Import configuration
    require_once(dirname(__DIR__) . '/config/config.php');

    // Import composer packages
    require_once(dirname(__DIR__) . '/../vendor/autoload.php');

    // Import RePok's classes
    foreach (glob(dirname(__DIR__) . "/class/*.php") as $file) {
        require_once($file);
    }

    // Initialize MySQL class
    $sql = new MySQL($mysql_auth["host"], $mysql_auth["username"], $mysql_auth["password"], $mysql_auth["db"]);
}