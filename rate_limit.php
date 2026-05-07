<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $limit = 10;
    $window = 60;

    $now = time();

    if (!isset($_SESSION['rl_time'])) {
        $_SESSION['rl_time'] = $now;
        $_SESSION['rl_count'] = 1;
        return;
    }

    if ($now - $_SESSION['rl_time'] > $window) {
        $_SESSION['rl_time'] = $now;
        $_SESSION['rl_count'] = 1;
        return;
    }

    $_SESSION['rl_count']++;

    if ($_SESSION['rl_count'] > $limit) {
        http_response_code(429);
        die("Слишком много запросов. Попробуйте позже.");
    }