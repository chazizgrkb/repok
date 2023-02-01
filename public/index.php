<?php

require_once dirname(__DIR__) . '/private/class/common.php';

$videos = $sql->fetchArray($sql->query("select * from videos"));

var_dump($videos);