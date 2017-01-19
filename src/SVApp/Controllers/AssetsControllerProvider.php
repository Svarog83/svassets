<?php

namespace SVApp\Controllers;

use SVApp\Entities\Asset;
use SVApp\Entities\Portfolio;
use Silex\Api\ControllerProviderInterface;
use Silex\Application as App;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Validator\Constraints as Assert;

class AssetsControllerProvider implements ControllerProviderInterface
{
	/**
	 * @var App $app
	 */
	private $app;

	public function connect(App $app)
	{
		$this->app = $app;

		$controllers = $app['controllers_factory'];

		$controllers
			->get('/assets', [$this, 'assets'])
			->bind('assets');


		$controllers->get('/show/{id}', function ($id) use ( $app ) {

			return $this->showAsset($id);
		});

		$controllers->get('/portfolio', function () use ( $app ) {

			return $this->showPortfolio();
		})->bind('portfolio');

		return $controllers;
	}

	public function showPortfolio()
	{
		/**
		 * @var Portfolio[] $portfolios
		 */

		$portfolios = (new \SVApp\Repositories\Portfolio($this->app))->getAllPortfolios();

		$response = $this->app['twig']->render('portfolio.html.twig', array(
			'allPortfolios' => $portfolios,
		));

		return $response;
	}

	public function assets(App $app)
	{
		$em = $app['orm.em'];
		$assetsEntities = $em->getRepository('SVApp\Entities\Asset')->findAll();

		$assets = [];
		$i=0;
		foreach ($assetsEntities AS $oneAsset) {
			/**
			 * @var Asset $oneAsset
			 */

			$assets[$i] = $oneAsset->getArray();
			$assets[$i]['TickerCode'] = $oneAsset->getTicker()->getCode();
			$i++;
		}

		/*if ( count($assets) < 9) {
			$asset = new Asset();
			$asset->setName(sha1(rand()));
			$em->persist($asset);
			$em->flush();
			$assets[] = $asset->getArray();
		}*/

		return $app['twig']->render('assets.html.twig', array(
			'assets' => $assets,
		));
	}

	public function showAsset($id)
	{
		return 'Show ' . $id;
	}


}
