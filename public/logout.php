<?php

namespace rePok {
    require_once dirname(__DIR__) . '/private/class/common.php';

    setcookie("REPOK_TOKEN", "", time() - 3600);
    redirect('./');
}