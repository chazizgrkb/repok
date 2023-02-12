<?php

namespace rePok {

    use Twig\Environment;
    use Twig\Loader\FilesystemLoader;

    /**
     * Twig loader, initializes Twig with standard configurations and extensions.
     *
     * @param string $subfolder Subdirectory to use in the templates/ directory.
     * @return Environment Twig object.
     */
    function twigloader($subfolder = '', $customloader = null, $customenv = null)
    {
        global $userdata, $messages, $log, $lpp, $searchQuery, $domain, $uri, $debugMode, $page;

        // this ugly hack is also on opensb
        chdir(__DIR__);
        chdir('../');
        $loader = new FilesystemLoader('templates/' . $subfolder);

        $twig = new Environment($loader, [
            'cache' => false,
            'debug' => $debugMode,
        ]);

        // Add repok specific extension
        $twig->addExtension(new RepokExtension());

        $twig->addGlobal('theme', Theme::getCurrentTheme());
        $twig->addGlobal('userdata', $userdata);
        $twig->addGlobal('message_count', $messages);
        $twig->addGlobal('log', $log);
        $twig->addGlobal('glob_lpp', $lpp);
        $twig->addGlobal('searchQuery', $searchQuery);
        $twig->addGlobal('domain', $domain);
        $twig->addGlobal('uri', $uri);
        $twig->addGlobal('pagename', substr($_SERVER['PHP_SELF'], 0, -4));
        $twig->addGlobal('page', $page);
        $twig->addGlobal("domain", $domain);
        $twig->addGlobal("page_url", sprintf("%s%s", $domain, $_SERVER['REQUEST_URI']));

        return $twig;
    }

    function comments($cmnts, $type, $id, $showheader = true)
    {
        return twigloader('components')->render('comment.twig', [
            'cmnts' => $cmnts, 'type' => $type, 'id' => $id, 'showheader' => $showheader
        ]);
    }

    function pagination($levels, $lpp, $url, $current)
    {
        $twig = twigloader('components');

        return $twig->render('pagination.twig', [
            'levels' => $levels, 'lpp' => $lpp, 'url' => $url, 'current' => $current
        ]);
    }

    function error($title, $message)
    {
        $twig = twigloader();

        echo $twig->render('_error.twig', ['err_title' => $title, 'err_message' => $message]);
        die();
    }

    function level($level, $featured = '', $pkg = false)
    {
        global $cache;
        $level['v'] = 2;
        return $cache->hitHash($level, function () use ($level, $featured, $pkg) {
            if (!$pkg) {
                if (!isset($level['visibility']) || $level['visibility'] != 1) {
                    $img = "levels/thumbs/low/" . $level['id'] . ".jpg";
                } else {
                    $img = "assets/locked_thumb.svg";
                }
            } else {
                $img = "assets/package_thumb.svg";
            }

            $page = ($pkg ? 'package' : 'level');

            return twigloader('components')->render('level.twig', ['level' => $level, 'featured' => $featured, 'img' => $img, 'page' => $page]);
        });
    }
    function relativeTime($time, $truncate = true)
    {
        if (!$time) return 'never';

        $relativeTime = new \RelativeTime\RelativeTime([
            'language' => '\RelativeTime\Languages\English',
            'separator' => ', ',
            'suffix' => true,
            'truncate' => $truncate,
        ]);

        return $relativeTime->timeAgo($time);
    }

    function redirect($url)
    {
        header(sprintf('Location: %s', $url));
        die();
    }
}