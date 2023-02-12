<?php

namespace rePok {

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

    // Initialize Database class
    $sql = new Database($mysql_auth["host"], $mysql_auth["username"], $mysql_auth["password"], $mysql_auth["db"]);

    $userfields = Users::userfields();
    $videofields = Videos::videofields();
    $accountfields = "id, name, email, customcolor, title, about, location, powerlevel, timezone, relationshipStatus, 
    gender, joined, lastview, birthday";

    $domain = (isset($_SERVER['HTTPS']) ? "https" : "http").'://'.$_SERVER["HTTP_HOST"];

    session_name("rpsess");
    session_start();

    // Session-based authentication, as HWD claims that the openSB auth check was a shit implementation. -grkb 2/2/2023
    if (isset($_SESSION['token'])) {
        $id = $sql->result("SELECT id FROM users WHERE token = ?", [$_SESSION['token']]);

        if ($id) {
            // Valid token, logged in
            $log = true;
        } else {
            // Invalid token, not logged in
            session_destroy();
            $log = false;
        }
    } else {
        // No token, not logged in
        $log = false;
    }

    if ($log) {
        $userdata = $sql->fetch("SELECT $accountfields FROM users WHERE id = ?", [$id]);
        $messages = $sql->result("SELECT COUNT(*) FROM messages m WHERE isread = 0 AND reciever = ?", [$userdata['id']]);
        //$userbandata = $sql->fetch("SELECT * FROM bans WHERE userid = ?", [$id]);
    } else {
        $userdata['powerlevel'] = 1;
    }

    /* any browsers with mozilla 4 user agent are definitely likely going to need the
    / flash player. we don't have a proper way of detecting old browsers so just do this. -grkb 2/3/2023 */
    if (str_contains($_SERVER['HTTP_USER_AGENT'], 'Mozilla/4')) {
        $_COOKIE['useFlashPlayer'] = true;
    }

    $error_handling = new Run;
    $error_handling->pushHandler(new PrettyPageHandler);
    $error_handling->register();
}