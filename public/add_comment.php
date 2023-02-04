<?php

namespace rePok {

    require_once dirname(__DIR__) . '/private/class/common.php';

    if (isset($_POST['video_id'])) {
        VideoComments::addComment($_POST['video_id'], $_POST['comment'], $userdata['id']);
        closeWindow();
    } else {
        die("invalid");
    }
}