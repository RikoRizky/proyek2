<?php

use Illuminate\Http\Request;

// Sembunyikan notifikasi deprecation PHP 8.5+ agar tidak bocor ke tampilan web
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

// Sesuaikan batas unggahan jika SAPI mengizinkan (tidak berlaku untuk semua server).
@ini_set('upload_max_filesize', '25M');
@ini_set('post_max_size', '512M');
@ini_set('max_file_uploads', '100');
@ini_set('max_input_vars', '5000');

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
