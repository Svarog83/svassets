<?php

namespace SVApp\Repositories;

use Silex\Application;
use SVApp\Classes\Repository;


class Asset extends Repository {
	public function __construct(Application $app) {
		parent::__construct($app, 'SVApp\Entities\Asset');
	}
}