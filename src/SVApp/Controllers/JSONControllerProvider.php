<?php

namespace SVApp\Controllers;

use Silex\Api\ControllerProviderInterface;
use Silex\Application as App;
use SVApp\Repositories\Ticker;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints as Assert;

class JSONControllerProvider implements ControllerProviderInterface {
	/**
	 * @var App $app
	 */
	private $app;

	public function connect(App $app) {
		$this->app = $app;

		$controllers = $app['controllers_factory'];

		$controllers->get('/assets',
			function () use ($app) {

				return $this->getAllAssets();
			})->bind('assets');

		return $controllers;
	}

	public function getAllAssets() {

		$tickerRepository = new Ticker($this->app);

		/**
		 * @var \SVApp\Entities\Ticker[] $tickersEntities
		 */
		$tickersEntities = $tickerRepository->getList();

		$tickersArr = [];
		foreach ($tickersEntities AS $tickersEnt) {
			$tickersArr[] = $tickersEnt->getArray();
		}

		return new JsonResponse($tickersArr);
	}

}
