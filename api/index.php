<?php

$storagePath = '/tmp/storage';
if (!is_dir($storagePath)) {
    mkdir($storagePath . '/framework/views', 0777, true);
    mkdir($storagePath . '/framework/cache', 0777, true);
    mkdir($storagePath . '/framework/sessions', 0777, true);
    mkdir($storagePath . '/logs', 0777, true);
}

$_ENV['APP_STORAGE'] = $storagePath;
putenv("APP_STORAGE=$storagePath");

require __DIR__ . '/../public/index.php';