<?php

namespace rePok {
    require_once dirname(__DIR__) . '/private/class/common.php';

    $video = htmlspecialchars($_GET["video_id"]);

    if (isset($_GET['still_id'])) {
        switch ($_GET["still_id"]) {
            case 1:
                $thumb = "dynamic/thumbs/" . $video . ".1.jpg";
                break;
            case 2:
                $thumb = "dynamic/thumbs/" . $video . ".2.jpg";
                break;
            case 3:
                $thumb = "dynamic/thumbs/" . $video . ".3.jpg";
                break;
            default:
                $thumb = "dynamic/img/thumbnail.jpg";
                break;
        }
    } else {
        $thumb = "dynamic/thumbs/" . $video . ".2.jpg";
    }

    if (file_exists(dirname(__DIR__) . "/" . $thumb)) {
        $file = $thumb;
    } else {
        $file = "/img/thumbnail.jpg";
    }

    header("Location:" . $file);
}
?>