<?php

namespace rePok {
    require_once dirname(__DIR__) . '/private/class/common.php';

    $section = ($_GET['s'] ?? null);
    $pageNumber = (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1);
    $limit = sprintf("%s,%s", (($pageNumber - 1) * $lpp), $lpp);

    if ($section == "mp") {
        $videoData = Videos::getVideos("views DESC", $limit);
        $page = "browsePopular";
    } elseif ($section == "md") {
        $videoData = Videos::getVideos("comments DESC", $limit);
        $page = "browseDiscussed";
    } elseif ($section == "mf") {
        $videoData = Videos::getVideos("favorites DESC", $limit);
        $page = "browseFavorited";
    } elseif ($section == "r") {
        $videoData = Videos::getVideos("RAND()", $limit);
        $page = "browseRandom";
    } else {
        $videoData = Videos::getVideos("v.time DESC", $limit);
        $page = "browseMain";
    }

    $count = $sql->result("SELECT COUNT(*) FROM videos v");

    $twig = twigloader();

    echo $twig->render('browse.twig', [
        'videos' => $videoData,
        'pageNumber' => $pageNumber,
        'level_count' => $count
    ]);
}