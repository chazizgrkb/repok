<?php
namespace rePok {

    require_once dirname(__DIR__) . '/private/class/common.php';

    if (!$log) redirect('login.php');
    $pageNumber = (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1);
    $limit = sprintf("LIMIT %s,%s", (($pageNumber - 1) * $lpp), $lpp);

    $page = "myFavorites";

    $twig = twigloader();

    echo $twig->render('vidlist.twig', [
        'videos' => Videos::getFavoritedVideosFromUser($limit, $userdata['id']),
        'page' => $page,
        'count' => Users::getUserFavoriteCount($userdata['id']),
        'title' => "My Favorites",
    ]);
}