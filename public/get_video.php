<?php

namespace rePok {
    require_once dirname(__DIR__) . '/private/class/common.php';
    $id = ($_GET['video_id'] ?? null);

    header("Location:" . Videos::getFlashVideo($id));
}