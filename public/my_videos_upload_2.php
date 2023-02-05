<?php

namespace rePok {
    require_once dirname(__DIR__) . '/private/class/common.php';

    $supportedFormats = ["mp4", "mkv", "wmv", "flv", "avi", "mov", "3gp"];

    if (!$log) redirect('login.php');

    $error = '';

    $failcount = 0;

// fixme: move this into a function probably?

    if (isset($_FILES['fileToUpload'])) {
        $new = randstr(11);

        $vextension = strtolower(pathinfo($_FILES['fileToUpload']['name'], PATHINFO_EXTENSION));

        $temp_name = $_FILES['fileToUpload']['tmp_name'];
        $target_file = dirname(__DIR__) . "/dynamic/preload/" . $new . "/" . $new . "." . $vextension;
        $preload_folder = dirname(__DIR__) . "/dynamic/preload/" . $new;
        $upload_file = "/dynamic/videos/" . $new . ".mp4";

        $title = ($_POST['title'] ?? '');
        $description = ($_POST['desc'] ?? '');
        $tags = ($_POST['tags'] ?? '');
        $tags2 = preg_split('/[\s,]+/', $tags); // parses both commas and spaces
        /* if (count($tags2) < 3) {
            die("Less than 3 tags!"); // we should have an actual error page, but this is alpha shit, so meh.
        } */
        $tagsIDbullshit = array();
        $number = 0;
        foreach ($tags2 as $tag) {
            $tagsIDbullshit[] = $number; // what the fuck
            if (!$sql->result("SELECT name from tag_meta WHERE name = ?", [$tag])) {
                VideoTags::insertTag($tag); //Insert tag onto database
            } else {
                VideoTags::bumpTag($tag); //Bump tag up the "lastest used" tags list by changing timestamp to current time.
            }
            $number = $sql->result("SELECT tag_id from tag_meta WHERE name = ?", [$tag]);
            $tagsIDbullshit[] = $number;
        }

        if (!in_array(strtolower($vextension), $supportedFormats, true)) {
            echo "<h1>Your video is an incompatible format.<br>To continue uploading this video, convert it to a supported format.</h1>";
            die();
        }

        if (!file_exists($preload_folder)) {
            mkdir($preload_folder);
        }

        if (move_uploaded_file($temp_name, $target_file)) {
            Videos::addVideo($new, $title, $description, $userdata['id'], $upload_file);

            $numID = $sql->result("SELECT id from videos WHERE video_id = ?", [$new]);

            foreach ($tagsIDbullshit as $tagID) {
                if (!$sql->result("SELECT tag_id from tag_index WHERE tag_id = ? AND video_id = ?", [$tagID, $numID])) {
                    $sql->query("INSERT INTO tag_index (video_id, tag_id) VALUES (?,?)", [$numID, $tagID]);
                }
            }

            if (str_starts_with(php_uname(), "Windows")) {
                pclose(popen(sprintf('start /B php %s "%s" "%s" > %s', dirname(__DIR__) . '\private\scripts\processingworker.php', $new, $target_file, dirname(__DIR__) . '/dynamic/videos/' . $new . '.log'), "r"));
            } else {
                system(sprintf('php %s "%s" "%s" > %s 2>&1 &', dirname(__DIR__) . '/private/scripts/processingworker.php', $new, $target_file, dirname(__DIR__) . '/dynamic/videos/' . $new . '.log'));
            }

            redirect('/my_videos_upload_2.php?v=' . $new);
        }
    }

    $twig = twigloader();
    echo $twig->render('my_videos_upload_2.twig', [
        'error' => $error,
        'title' => ($_POST["title"] ?? ''),
        'desc' => ($_POST["desc"] ?? ''),
        'tags' => ($_POST["tags"] ?? ''),
        'videoid' => ($_GET["v"] ?? '')
    ]);
}