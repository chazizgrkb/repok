#!/usr/bin/php
<?php
namespace RePok {

    use FFMpeg\Coordinate;
    use FFMpeg\FFMpeg;
    use FFMpeg\FFProbe;
    use FFMpeg\Filters;
    use FFMpeg\Format\Video\FLV;
    use FFMpeg\Format\Video\x264;

    require_once dirname(__DIR__) . '/class/common.php';
    require_once dirname(__DIR__) . '/external/FLV.php';

    $config = [
        'timeout' => 3600, // The timeout for the underlying process
        'ffmpeg.threads' => 12,   // The number of threads that FFmpeg should use
        'ffmpeg.binaries' => ($ffmpegPath ? $ffmpegPath : 'ffmpeg'),
        'ffprobe.binaries' => ($ffprobePath ? $ffprobePath : 'ffprobe'),
    ];

    $new = $argv[1];
    $target_file = $argv[2];
    try {
        $ffmpeg = FFMpeg::create($config);
        $ffprobe = FFProbe::create($config);
        $h264 = new X264();
        $flv = new FLV();

        $h264->setAudioKiloBitrate(56)->setAdditionalParameters(array('-ar', '22050'));
        $flv->setAudioKiloBitrate(80)->setAdditionalParameters(array('-ar', '22050'));


        $video = $ffmpeg->open($target_file);

        //get frame count
        $duration = $ffprobe
            ->streams($target_file)    // extracts file informations
            ->videos()              // filters video streams
            ->first()               // returns the first video stream
            ->get('nb_frames');    // returns the duration property

        //get fractional framerate
        $fracFramerate = $ffprobe
            ->streams($target_file)    // extracts file informations
            ->videos()              // filters video streams
            ->first()               // returns the first video stream
            ->get("avg_frame_rate");

        //get the actual framerate
        $framerate = explode("/", $fracFramerate)[0] / explode("/", $fracFramerate)[1];


        //this doesn't scale too well with short videos.
        $seccount = round($duration / 4);
        $seccount2 = $seccount * 1.5;
        $seccount3 = $seccount2 + $seccount - 1;

        $frame = $video->frame(Coordinate\TimeCode::fromSeconds($seccount / $framerate));
        $frame->filters()->custom('scale=120x90');
        $frame->save(dirname(__DIR__) . '/../dynamic/thumbs/' . $new . '.1.jpg');

        $frame = $video->frame(Coordinate\TimeCode::fromSeconds($seccount2 / $framerate));
        $frame->filters()->custom('scale=120x90');
        $frame->save(dirname(__DIR__) . '/../dynamic/thumbs/' . $new . '.2.jpg');

        $frame = $video->frame(Coordinate\TimeCode::fromSeconds($seccount3 / $framerate));
        $frame->filters()->custom('scale=120x90');
        $frame->save(dirname(__DIR__) . '/../dynamic/thumbs/' . $new . '.3.jpg');

        $video->filters()->resize(new Coordinate\Dimension(320, 240), Filters\Video\ResizeFilter::RESIZEMODE_INSET, true)
            ->custom('format=yuv420p');
        $video->save($h264, dirname(__DIR__) . '/../dynamic/videos/' . $new . '.mp4');
        $video->save($flv, dirname(__DIR__) . '/../dynamic/videos/' . $new . '.flv');
        debug_print_backtrace();
        unlink($target_file);
        delete_directory($preload_folder);

        $videoData = $sql->fetch("SELECT $userfields v.* FROM videos v JOIN users u ON v.author = u.id WHERE v.video_id = ?", [$new]);

        $sql->query("UPDATE videos SET videolength = ?, flags = ? WHERE video_id = ?",
            [ceil($duration), $videoData['flags'] ^ 0x2, $new]);
    } catch (Exception $e) {
        echo "rePok uploader processor FAIL:" . $e->getMessage();
    }

    clearstatcache();

    if (0 == filesize(dirname(__DIR__) . '/../dynamic/videos/' . $new . ".mp4")) {
        unlink(dirname(__DIR__) . '/../dynamic/videos/' . $new . ".mp4");
        delete_directory($preload_folder);
        $failcount++;
    }

    if ($failcount == 1) {
        unlink(dirname(__DIR__) . '/../dynamic/videos/' . $new . ".mp4");
        delete_directory($preload_folder);
        die("Fuck");
    }
}