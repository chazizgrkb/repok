<?php

namespace rePok {
    require_once dirname(__DIR__) . '/private/class/common.php';

    $video = htmlspecialchars($_GET["video_id"]);
    $still_id = $_GET["still_id"] ?? 2;
    $thumb = "dynamic/thumbs/" . $video . "." . $still_id . ".jpg";
    $file = file_exists(dirname(__DIR__) . "/" . $thumb) ? $thumb : "/img/thumbnail.jpg";
    header("Location:" . $file);
}