<?php

namespace rePok {
    require_once dirname(__DIR__) . '/private/class/common.php';

    $page = "recommended";

    if (isset($_GET['v'])) {
        $vid = $_GET['v'];
        $latestVideo = Videos::getRecommended($vid);
        $currentVideo = Videos::getVideoData($userfields, $vid);
    }

    $twig = twigloader();
    echo $twig->render('videos_list.twig', [
        'latestVideo' => $latestVideo,
        'currentVideo' => $currentVideo,
    ]);
}