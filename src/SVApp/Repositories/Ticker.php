<?php

namespace SVApp\Repositories;

use Silex\Application;
use SVApp\Classes\Repository;


class Ticker extends Repository {
	public function __construct(Application $app) {
		parent::__construct($app, 'SVApp\Entities\Ticker');
	}

	/**
	 * @param      $id
	 * @param bool $cacheAllowed
	 * @return \SVApp\Entities\Ticker
	 */
	public function findEntityByID($id, $cacheAllowed = TRUE) {

		$rep = $this->app['orm.em']->getRepository($this->entName);
		$criteria = ['Code'=> $id];

		return $rep->findOneBy($criteria);
	}
}