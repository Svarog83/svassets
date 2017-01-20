<?php

namespace SVApp\Controllers;

use Silex\Api\ControllerProviderInterface;
use Silex\Application as App;
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
		return new JsonResponse([ [ 'id' => 1, 'author' => 'test1', 'message' => 'message1' ],
								  [ 'id' => 2, 'author' => 'test2', 'message' => 'message2' ] ]);
	}

}
