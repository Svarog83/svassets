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
		$fileName   = preg_replace('/[^a-z_0-9]/i', '', $fileName) . '.xml';
		$pathToFile = $this->app['files_dir'] . '/xmls/' . $fileName;
		$content    = file_get_contents($pathToFile);

		$parsedArr = simplexml_load_string($content);

		$totalSumRub = 0;

		$cleanedTransactions = [];
		$dateStart           = (string)$parsedArr->BeginDate;
		$dateEnd             = (string)$parsedArr->EndDate;
		foreach ($parsedArr->Transactions[0] AS $oneTransaction) {
			$cleanTransaction = [ 'CardNumber'      => substr((string)$oneTransaction->Object, -4),
								  'OperationDate'   => (string)$oneTransaction->TransactionDate,
								  'ProcessDate'     => (string)$oneTransaction->ProcessedDate,
								  'SumInSourceCur'  => $this->cleanAmount($oneTransaction->TransactionSum),
								  'Currency'        => (string)$oneTransaction->TransactionCurrency,
								  'SumInRub'        => $this->cleanAmount($oneTransaction->SumInAccountCurrency),
								  'AccountCurrency' => (string)$oneTransaction->AccountCurrency,
								  'Place'           => (string)$oneTransaction->Details,
								  'Status'          => (string)$oneTransaction->Статус, ];
			$totalSumRub      += $cleanTransaction['SumInRub'];

			$cleanedTransactions[] = $cleanTransaction;
		}


		return $this->app['twig']->render('one_expense.html.twig',
										  [ 'expenses'    => $cleanedTransactions,
											'fileName'    => $fileName,
											'dateStart'   => $dateStart,
											'dateEnd'     => $dateEnd,
											'totalSumRub' => number_format($totalSumRub, 2, '.', '') ]);
	}

	private function cleanAmount($amount) {
		return str_replace([ ',', ' ' ], [ '.', '' ], (string)$amount);
	}
}
