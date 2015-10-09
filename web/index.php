<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == ""){
    $redirect = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: $redirect");
}

if (! extension_loaded('openssl')) {
    die('You must enable the openssl extension.');
}

session_cache_limiter(false);
session_start();

if (preg_match('/\.(?:png|jpg|jpeg|gif|txt|css|js)$/', $_SERVER["REQUEST_URI"]))
    return false; // serve the requested resource as-is.
else {
    $app = require __DIR__ . '/../src/app.php';
    $app->run();
}

		