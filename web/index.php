<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new SVApp\Application('dev');
$app['http_cache']->run();
