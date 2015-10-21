<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

if (! extension_loaded('openssl')) {
    die('You must enable the openssl extension.');
}

session_cache_limiter(false);

    if (session_id() === '') {
        session_regenerate_id();
        session_start();
    }

if (preg_match('/\.(?:png|jpg|jpeg|gif|txt|css|js)$/', $_SERVER["REQUEST_URI"]))

    return false; // serve the requested resource as-is.
else {
    $app = require __DIR__ . '/../src/app.php';
    $app->run();

}

		