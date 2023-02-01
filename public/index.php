<?php
/*
 * Please note that a router implementation will NOT work due to file extension-related stuff. -grkb 2/1/2023
 */

namespace rePok {
    require_once dirname(__DIR__) . '/private/class/common.php';

    $page = "index";

    $twig = twigloader();

    echo $twig->render('index.twig', [
        'videos' => Videos::getVideos("RAND()", 5),
        'tags' => VideoTags::getListOfTags("latestUse DESC", 50),
    ]);
}