<?php

/*
| Front controller for cPanel when the domain's document root cannot be changed.
|
| The application lives outside the web root (default: ~/healthdata) and only the
| contents of its public/ folder are copied into public_html, with this file
| replacing public_html/index.php. Change APP_DIR below if you used another folder.
*/

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

const APP_DIR = __DIR__.'/../healthdata';

if (file_exists($maintenance = APP_DIR.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require APP_DIR.'/vendor/autoload.php';

/** @var Application $app */
$app = require_once APP_DIR.'/bootstrap/app.php';

$app->usePublicPath(__DIR__);

$app->handleRequest(Request::capture());
