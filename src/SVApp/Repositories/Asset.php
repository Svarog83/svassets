<?php

namespace SVApp\Repositories;

use SVApp\Application;
use SVApp\Classes\Repository;
use App;


class Asset extends Repository {
	public function __construct(Application $app) {
		parent::__construct($app, 'SVApp\Entities\Asset');
	}
}