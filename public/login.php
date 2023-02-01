<?php
namespace rePok {
    require_once dirname(__DIR__) . '/private/class/common.php';

    $page = "login";

    if ($log) redirect('./');

    $error = '';

    if (isset($_GET['resetted'])) $error .= 'Password successfully reset! Please login with your new password.';

    if (isset($_POST['field_command'])) {
        $name = ($_POST['field_login_username'] ?? null);
        $pass = ($_POST['field_login_password'] ?? null);

        $logindata = $sql->fetch("SELECT id,password,token FROM users WHERE name = ?", [$name]);

        if (!$name || !$pass || !$logindata || !password_verify($pass, $logindata['password'])) $error .= 'Invalid credentials.';

        if ($error == '') {
            setcookie("REPOK_TOKEN", $logindata['token'], 2147483647);
            $nid = $sql->result("SELECT id FROM users WHERE token = ?", [$logindata['token']]);
            $sql->query("UPDATE users SET lastview = ?, ip = ? WHERE id = ?", [time(), getUserIpAddr(), $nid]);

            redirect('./');
        }
    }

    $twig = twigloader();
    echo $twig->render('login.twig', ['error' => $error]);

}
