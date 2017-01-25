<?php

namespace SVApp\Controllers;

use SVApp\Entities\Asset;
use SVApp\Entities\Portfolio;
use Silex\Api\ControllerProviderInterface;
use Silex\Application as App;
use SVApp\Repositories\Ticker;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

		$controllers->post('/save_ticker', function (Request $request) use ( $app ) {

			return $this->saveTicker($request);
		});

		$controllers->post('/delete_ticker', function (Request $request) use ( $app ) {

			return $this->deleteTicker($request);
		});

		$controllers->get('/portfolio', function () use ( $app ) {

			return $this->showPortfolio();
		})->bind('portfolio');

		return $controllers;
	}

	/**
	 * @param Request $request
	 * @return bool
	 */
	public function deleteTicker($request) {
		$resultStatus = 'ok';

		$params = $request->request->all();
		$tickerRepository = new Ticker($this->app);
		$tickerEnt = $tickerRepository->findEntityByID($params['id']);

		if ($tickerEnt) {
			$em = $this->app['orm.em'];
			$em->getConnection()->beginTransaction();
			try {
				$em->remove($tickerEnt);
				$em->flush($tickerEnt);
				$em->getConnection()->commit();

			} catch (\Exception $e) {
				$em->getConnection()->rollBack();
				$em->close();

				$resultStatus = 'Some error. '.$e->getMessage();
			}
		}
		else {
			$resultStatus = 'Ticker not found';
		}

		return new JsonResponse(['result'=>$resultStatus]);
	}

	/**
	 * @param Request $request
	 * @return bool
	 */
	public function saveTicker($request) {
		$resultStatus = 'ok';

		$params = $request->request->all();
		$tickerRepository = new Ticker($this->app);
		if ($params['id']) {
			$em = $this->app['orm.em'];
			$rep = $em->getRepository($tickerRepository->entName);
			$criteria = ['Code'=> $params['id']];
			$tickerEnt = $rep->findOneBy($criteria);
		}
		else {
			$tickerEnt = $tickerRepository->setEntityNew()->getEntity();
		}

		/**
		 * @var \SVApp\Entities\Ticker $tickerEnt
		 */

		if ($tickerEnt) {
			$em = $this->app['orm.em'];
			$em->getConnection()->beginTransaction();
			try {
				$tickerEnt->setCode($params['code']);
				$tickerEnt->setDescription($params['description']);
				$tickerRepository->persistAndFlush($tickerEnt);
				$em->getConnection()->commit();

			} catch (\Exception $e) {
				$em->getConnection()->rollBack();
				$em->close();

				$resultStatus = 'Some error. '.$e->getMessage();
			}
		}
		else {
			$resultStatus = 'Ticker not found';
		}

		return new JsonResponse(['result'=>$resultStatus]);
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
		$asset = (new \SVApp\Repositories\Asset($this->app))->findEntityByID($id);
		?><pre><?= print_r( $asset->getArray() ) ?></pre><?
		return true;
	}


}
