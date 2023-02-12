<?php

namespace rePok {

    require_once dirname(__DIR__) . '/private/class/common.php';

    if (!$log) redirect('login.php');

    $page = "settings";

    $coppaYearBS = date('Y') - 13;

    $birthday = explode('-', $userdata['birthday']);

    if (isset($_POST['magic'])) {
        $title = $_POST['title'] ?? null;
        $about = $_POST['about'] ?? null;
        $location = $_POST['location'] ?? null;
        $timezone = $_POST['timezone'] != 'America/New_York' ? $_POST['timezone'] : null;
        $dob = $_POST['year'] && $_POST['month'] && $_POST['day'] ? date('Y-m-d', strtotime($_POST['year'] . "-" . $_POST['month'] . "-" . $_POST['day'])) : null;
        $relationship = $_POST['relationship'] ?? null;
        $gender = $_POST['gender'] ?? null;
        $flash = $_POST['flash'] ?? 'false';
        $theme = $_POST['theme'] ?? 'yt';

        $relationStatus = Users::type_to_relationship($relationship);
        $genderShit = Users::type_to_gender($gender);

        if (!preg_match("/^((((19|[2-9]\d)\d{2})\-(0[13578]|1[02])\-(0[1-9]|[12]\d|3[01]))|(((19|[2-9]\d)\d{2})\-(0[13456789]|1[012])\-(0[1-9]|[12]\d|30))|(((19|[2-9]\d)\d{2})\-02\-(0[1-9]|1\d|2[0-8]))|(((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))\-02\-29))$/", $dob)) {
            die("Invalid date.");
        }

        setcookie('useFlashPlayer', $flash, 2147483647);
        setcookie('rptheme', $theme, 2147483647);

        $sql->query("UPDATE users SET title = ?, about = ?, location = ?, timezone = ?, birthday = ?, relationshipStatus = ?, gender = ? WHERE id = ?",
            [$title, $about, $location, $timezone, $dob, $relationStatus, $genderShit, $userdata['id']]);

        redirect(sprintf("/profile.php?user=%s&edited", $userdata['name']));
    }

    $timezones = [];
    foreach (timezone_identifiers_list() as $tz) {
        $timezones[] = $tz;
    }

    $twig = twigloader();
    echo $twig->render('settings.twig', [
        'timezones' => $timezones,
        'themes' => Theme::availableThemes(),
        'miniyear' => $coppaYearBS,
        'birthday' => $birthday,
    ]);
}