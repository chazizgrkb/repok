<?php

namespace rePok {
    require_once dirname(__DIR__) . '/private/class/common.php';

    session_destroy();
    redirect('./');
}