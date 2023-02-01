<?php

namespace RePok {

    use Whoops\Handler\PrettyPageHandler;
    use Whoops\Run;

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

    $userfields = Users::userfields();
    $videofields = Videos::videofields();
    $accountfields = "id, name, email, customcolor, title, about, powerlevel, joined, lastview";

    // Cookie authentication, just like squareBracket.
    if (isset($_COOKIE['REPOK_TOKEN'])) {
        $id = $sql->result("SELECT id FROM users WHERE token = ?", [$_COOKIE['REPOK_TOKEN']]);

        if ($id) {
            // Valid cookie, logged in
            $log = true;
        } else {
            // Invalid cookie, not logged in
            $log = false;
        }
    } else {
        // No cookie, not logged in
        $log = false;
    }

    if ($log) {
        $userdata = $sql->fetch("SELECT $accountfields FROM users WHERE id = ?", [$id]);
        $messages = $sql->result("SELECT COUNT(*) FROM messages m WHERE reciever = ?", [$userdata['id']]);
        //$userbandata = $sql->fetch("SELECT * FROM bans WHERE userid = ?", [$id]);
    } else {
        $userdata['powerlevel'] = 1;
    }

    $error_handling = new Run;
    $error_handling->pushHandler(new PrettyPageHandler);
    $error_handling->register();
}