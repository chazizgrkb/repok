<?php

namespace rePok {
    require_once dirname(__DIR__) . '/private/class/common.php';

    $page = "profile_videos";
    $pageNumber = (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1);
    $limit = sprintf("%s,%s", (($pageNumber - 1) * 5), 5);

    if (isset($_GET['id'])) {
        $userpagedata = $sql->fetch("SELECT * FROM users WHERE id = ?", [$_GET['id']]);
    } else if (isset($_GET['user'])) {
        $userpagedata = $sql->fetch("SELECT * FROM users WHERE name = ?", [$_GET['user']]);
    }

    $twig = twigloader();

    $videos = Videos::getVideos('v.time DESC', $limit, 'v.author', $userpagedata['id']);

    echo $twig->render('profile_videos.twig', [
        'id' => $userpagedata['id'],
        'name' => $userpagedata['name'],
        'allVideos' => Users::getUserVideoCount($userpagedata['id']),
        'allFavorites' => Users::getUserFavoriteCount($userpagedata['id']),
        'userpagedata' => $userpagedata,
        'videos' => $videos,
        'pageNumber' => $pageNumber,
        'level_count' => Users::getUserVideoCount($userpagedata['id']),
        'page_name' => $page
    ]);
}