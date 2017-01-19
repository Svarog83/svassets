<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new App\Application('dev');
$app['http_cache']->run();
