<?php

namespace rePok {
    require_once dirname(__DIR__) . '/private/class/common.php';

    $searchQuery = $_GET['search'] ?? null;
    $page = (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1);
    $limit = sprintf("LIMIT %s,%s", (($page - 1) * $lpp), $lpp);

    if ($searchQuery == null) {
        error(400, "Your search query is empty.");
    }

    $query_start = microtime(true);
// currently selects all uploaded videos
    $videoData = $sql->query("
SELECT DISTINCT $userfields $videofields FROM videos v 
	JOIN users u ON v.author = u.id 
	JOIN tag_index ti ON (ti.video_id = v.id) 
	JOIN tag_meta t ON (t.tag_id = ti.tag_id) 	
	WHERE 
		v.title LIKE CONCAT('%', ?, '%') 
	OR 
		v.description LIKE CONCAT('%', ?, '%') 
	OR
		t.name LIKE CONCAT('%', ?, '%') 
	AND 
		flags != 0x2
ORDER BY v.id DESC $limit", [$searchQuery, $searchQuery, $searchQuery]);
    $videos = $sql->fetchArray($videoData);

    foreach ($videos as &$video) {
        $video['tags'] = VideoTags::getVideoTags($video['id']);
    }

    $count = $sql->result("
SELECT COUNT(DISTINCT v.id) FROM videos v
 	JOIN tag_index ti ON (ti.video_id = v.id) 
	JOIN tag_meta t ON (t.tag_id = ti.tag_id)
	WHERE 
		v.title LIKE CONCAT('%', ?, '%') 
	OR v.description 
		LIKE CONCAT('%', ?, '%') 
	OR
		t.name LIKE CONCAT('%', ?, '%') 
	AND 
		flags != 0x2", [$searchQuery, $searchQuery, $searchQuery]);
    $query_end = microtime(true);

    $duration = substr($query_end - $query_start, 0, 4);

    $twig = twigloader();

    echo $twig->render('results.twig', [
        'videos' => $videos,
        'query' => $searchQuery,
        'page' => $page,
        'count' => $count,
        'duration' => $duration,
    ]);
}
