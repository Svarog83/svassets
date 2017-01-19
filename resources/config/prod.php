<?php

// Doctrine: DB options
$app['db.options'] = [ 'driver'   => 'pdo_mysql',
					   'dbname'   => 'svassets',
					   'host'     => 'localhost',
					   'user'     => 'root',
					   'password' => '',
					   'logging'  => TRUE ];

$app['orm.proxies_dir'] = './var/cache/doctrine/proxies/';

$app['orm.em.options'] = [ "mappings" => [ // Using actual filesystem paths
										   [ "type"      => "annotation",
											 "namespace" => "App\\Entities",
											 //			"path" => __DIR__."../../src/App/Entities",
											 "path"      => "./src/App/Entities", ], ], ];

/*$app['db.options'] = array(
    'driver' => 'pdo_sqlite',
    'path' => __DIR__.'/../../var/database.dat',
);*/
