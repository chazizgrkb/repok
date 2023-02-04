<?php

namespace rePok {
    require_once dirname(__DIR__) . '/private/class/common.php';
    $id = ($_GET['v'] ?? null);
    $searchShit = ($_GET['search'] ?? null);
    $ip = getUserIpAddr();

    $videoData = Videos::getVideoData($userfields, $id);

    $commentData = VideoComments::getComments($id);

    $page = "watch";

    $allVideos = $sql->result("SELECT COUNT(id) FROM videos WHERE author=?", [$videoData['u_id']]);

    if ($sql->fetch("SELECT COUNT(video_id) FROM views WHERE video_id=? AND user=?", [$videoData['video_id'], crypt($ip, $ip)]) ['COUNT(video_id)'] < 1) {
        $sql->query("INSERT INTO views (video_id, user) VALUES (?,?)", [$videoData['video_id'], crypt($ip, $ip)]);
    }

    if ($log) {
        if ($sql->result("SELECT * from favorites WHERE video_id = ? AND user_id = ?", [$videoData['video_id'], $userdata['id']])) {
            $isFavorited = true;
        } else {
            $isFavorited = false;
        }
    } else {
        $isFavorited = false;
    }

    if (isset($_COOKIE['useFlashPlayer'])) {
        $isFlash = $_COOKIE['useFlashPlayer'];
    } elseif (isset($_GET['detectflash']) && $_GET['detectflash'] == "false") { // flash object mentions this in bypassTxt.
        $isFlash = true;
    } else {
        $isFlash = false;
    }

    $favoritesCount = $sql->fetch("SELECT COUNT(user_id) FROM favorites WHERE user_id=?", [$videoData['u_id']]) ['COUNT(user_id)'];

    $previousRecentView = $sql->result("SELECT most_recent_view from videos WHERE video_id = ?", [$id]);

    Videos::bumpVideo(time(), $id);

    $twig = twigloader();
    echo $twig->render('watch.twig', [
        'video' => $videoData,
        'comments' => $commentData,
        'favorites' => $favoritesCount,
        'recentView' => $previousRecentView,
        'allVideos' => $allVideos,
        'isFlash' => $isFlash,
        'tags' => VideoTags::getVideoTags($videoData['id']),
        'isFavorited' => $isFavorited,
        'recommendedNumber' => Videos::countRecommended($videoData['video_id']),
        'relatedTags' => VideoTags::getListOfTags("RAND()", 50),
    ]);
}