<?php

namespace SVApp\Controllers;

use Silex\Api\ControllerProviderInterface;
use Silex\Application as App;


class ExpensesControllerProvider implements ControllerProviderInterface {
	/**
	 * @var App $app
	 */
	private $app;

	public function connect(App $app) {
		$this->app = $app;

		$controllers = $app['controllers_factory'];

		$controllers->get('/expenses', [ $this, 'expenses' ])->bind('expenses');

		$controllers->get('/file/{fileName}',
			function ($fileName) {

				return $this->showExpenses($fileName);
			});

		return $controllers;
	}

	public function expenses(App $app) {
		$pathToFiles  = $app['files_dir'] . '/xmls/*.xml';
		$expenseFiles = glob($pathToFiles);

		$expenses = [];
		foreach ($expenseFiles AS $oneFile) {
			$basename   = basename($oneFile);
			$expenses[] = [ 'name' => $basename,
			                'url'  => str_replace('.xml', '', $basename),
			                'date' => date('Y-m-d H:i', filemtime($oneFile)), ];
		}


		return $app['twig']->render('expenses.html.twig',
		                            [ 'expenses' => $expenses, ]);
	}

	public function showExpenses($fileName) {
		$fileName   = preg_replace('/[^a-z_]/i', '', $fileName);
		$pathToFile = $this->app['files_dir'] . '/xmls/' . $fileName . '.xml';
		$content    = file_get_contents($pathToFile);

		$parsedArr = simplexml_load_string($content);

		ddd($parsedArr->Transactions);

		return TRUE;
	}
}
