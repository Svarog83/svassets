<?php

namespace SVApp\Repositories;

use Silex\Application;
use SVApp\Classes\Repository;
use SVApp;


class Portfolio extends Repository {
	public function __construct(Application $app) {
		parent::__construct($app, 'SVApp\Entities\Portfolio');
	}

	public function getAllPortfolios() {
		$qb = $this->app['orm.em']->createQueryBuilder();

		$cache_lifetime = 10;

		$qb->select('PF, ASS, TI')
		   ->from('SVApp\Entities\Portfolio', 'PF')
		   ->innerJoin('PF.Assets', 'ASS')
		   ->innerJoin('ASS.Ticker', 'TI')
		   ->orderBy('PF.pID', 'ASC')
		   ->addOrderBy('TI.Code', 'ASC');

		$query = $qb->getQuery();
		$query->setResultCacheDriver($this->app['cache'])->setResultCacheLifetime($cache_lifetime);

		return $query->getArrayResult();
	}

	/**
	 * @param $id
	 * @param $cacheAllowed
	 * @return \SVApp\Entities\Portfolio
	 */
	public function findEntityByID($id, $cacheAllowed = TRUE) {
		return parent::findEntityByID($id,$cacheAllowed);
	}
}
