<?php

namespace rePok {
    require_once dirname(__DIR__) . '/private/class/common.php';

    if ($userdata['powerlevel'] < 3) error('403', "You shouldn't be here, get out!");

    if (isset($_GET['action'])) {
        if ($_GET['action'] == 'delete_comment') {
            if (isset($_GET['id'])) {
                $sql->query("DELETE FROM comments WHERE comment_id = ?", [$_GET['id']]);
            } else {
                error('400', 'you forgot the id.');
            }
        }
    }

    // this is dogshit. -grkb 2/3/2023
    $serverStats = array(
        'freeSpace' => array(
            'title' => "Free space",
            'info' => floor( disk_free_space( "/" ) / ( 1024 * 1024 ) ) . "MB / " .
                floor( disk_total_space( "/" ) / (1024 * 1024 ) ) . " MB"),
        'phpVersion' => array(
            'title' => "PHP version",
            'info' => phpversion()),
        'maxPost' => array(
            'title' => "Max POST size",
            'info' => ini_get('post_max_size')),
        'maxUpload' => array(
            'title' => "Max upload size",
            'info' => ini_get('upload_max_filesize')),
        'servIp' => array(
            'title' => "Server IP",
            'info' => $_SERVER['SERVER_ADDR']),
    );

    $latestRegisteredUsers = $sql->query("SELECT id, name, joined FROM users ORDER BY joined DESC LIMIT 15");
    $latestSeenUsers = $sql->query("SELECT id, name, lastview FROM users ORDER BY lastview DESC LIMIT 15");
    $videoData = $sql->query("SELECT $userfields $videofields FROM videos v JOIN users u ON v.author = u.id ORDER BY v.time DESC LIMIT 7");
    $comments = $sql->fetchArray($sql->query("SELECT $userfields c.comment_id, c.id, c.comment, c.author, c.date, c.deleted, v.title, (SELECT COUNT(reply_to) FROM comments WHERE reply_to = c.comment_id) AS replycount FROM comments c JOIN users u ON c.author = u.id JOIN videos v ON c.id = v.video_id ORDER BY c.date DESC"));

    $thingsToCount = ['comments', 'users', 'videos', 'views', 'messages', 'favorites'];

    $query = "SELECT ";
    foreach ($thingsToCount as $thing) {
        if ($query != "SELECT ") $query .= ", ";
        $query .= sprintf("(SELECT COUNT(*) FROM %s) %s", $thing, $thing);
    }
    $count = $sql->fetch($query);

    $twig = twigloader();
    echo $twig->render('admin.twig', [
        'latest_registered_users' => $latestRegisteredUsers,
        'latest_seen_users' => $latestSeenUsers,
        'things_to_count' => $thingsToCount,
        'count' => $count,
        'videos' => $videoData,
        'comments' => $comments,
        'serverStats' => $serverStats,
    ]);
}