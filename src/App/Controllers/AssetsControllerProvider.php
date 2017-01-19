<?php

namespace App\Controllers;

use App\Entities\Asset;
use App\Entities\Portfolio;
use Doctrine\Common\Cache\ApcCache;
use Doctrine\Common\Cache\ApcuCache;
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
		$em = $this->app['orm.em'];

		//We get the cache before anything else
		$cacheDriver = new ApcuCache();

		if ($cacheDriver->contains('_home_rssNews'))
		{
			return $cacheDriver->fetch('_home_rssNews');
		}

		//If not, we build the Response as usual and then put it in cache !

		$redis = $this->app['redis'];

		if ($this->app['cache']->contains('test')) {
			echo 'cache exists';
			echo "\n" . '<br>' . $this->app['cache']->fetch('test') . ' - this->app<br>' . "\n";
		} else {
			echo 'cache does not exist';
//			$redis->set('test', json_encode(['1', '2', '3']));
//			$this->app['cache']->save('test', json_encode(['1', '2', '3']));

			$this->app['cache']->save('test', ['3', '4', '4']);

		}

//		$redis->set('test', json_encode(['1', '2', '3']));
		$redisVal = $this->app['cache']->fetch('test');
		
		d ( $redisVal );

		echo "\n" . '<br>' . $redisVal . ' - redisVal<br>' . "\n";

		/**
		 * @var Portfolio[] $portfolios
		 */
		$portfolios = $em->getRepository('App\Entities\Portfolio')->findAll();

		$allPortfolios = [ ];
		foreach ($portfolios AS $portfolio) {

			$allPortfolios[] = [ 'name'   => $portfolio->getName(),
								 'assets' => $portfolio->getAssets() ];
		}

		$response = $this->app['twig']->render('portfolio.html.twig', array(
			'allPortfolios' => $allPortfolios,
		));

		$cacheDriver->save('_home_rssNews', $response, "900");

		return $response;
	}

	public function assets(App $app)
	{
		$em = $app['orm.em'];
		$assetsEntities = $em->getRepository('App\Entities\Asset')->findAll();

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
