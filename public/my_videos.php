<?php

namespace rePok {

    require_once dirname(__DIR__) . '/private/class/common.php';

    if (!$log) redirect('login.php');
    $page = (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1);
    $limit = sprintf("%s,%s", (($page - 1) * $lpp), $lpp);

    $page = "myVideos";

    $twig = twigloader();

    echo $twig->render('vidlist.twig', [
        'videos' => Videos::getVideos('v.time DESC', $limit, 'v.author', $userdata['id'], false),
        'page' => $page,
        'count' => Users::getUserVideoCount($userdata['id']),
        'title' => "My Videos",
    ]);
}