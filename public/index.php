<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../routes/web.php";

require_once __DIR__ . '/../app/core/helpers.php';
