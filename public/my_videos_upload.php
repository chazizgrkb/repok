<?php

namespace rePok {

    require_once dirname(__DIR__) . '/private/class/common.php';

    if (!$log) redirect('login.php');

    $twig = twigloader();
    echo $twig->render('my_videos_upload.twig');
}