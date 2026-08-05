<?php
// config.php — shared settings for the PHP frontend
define('API_BASE', getenv('PLACEMENT_API_BASE') ?: 'http://localhost:5000/api');
session_start();

function is_logged_in() {
    return isset($_SESSION['token']) && isset($_SESSION['user']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function require_role($roles) {
    require_login();
    if (!in_array($_SESSION['user']['role'], $roles)) {
        http_response_code(403);
        die('Access denied for role: ' . htmlspecialchars($_SESSION['user']['role']));
    }
}
