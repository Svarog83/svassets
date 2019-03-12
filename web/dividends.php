<?php

require_once '../vendor/raveren/kint/Kint.class.php';

$debugInfo = (boolean)(int)$_REQUEST['debugInfo'];
$withDetails = (boolean)(int)$_REQUEST['withDetails'];
if ($debugInfo) {
	$s = !\Kint::dump(microtime(), 'started');
}
$URL = 'https://smart-lab.ru/dividends';
$htmlContent = file_get_contents($URL);
if ($debugInfo) {
	$s = !\Kint::dump(microtime(), 'got content');
}
$result = preg_match_all(
	/** @lang text */
	"/<tr class=\"dividend_approved\"[^>]*>(\n?\r?.*?)<\/tr>/s", $htmlContent, $matches);
if ($debugInfo) {
	$s = !\Kint::dump(microtime(), 'got rows');
	\Kint::dump($matches);
}

$resultsArr = [];
$rowsStr = '';

$neededColumns = [1 => 'Ticker', 4 => 'Date', 5 => 'Year', 6 => 'Period', 7 => 'Amount'];
$resultsCounter = 0;
foreach ((array)$matches[0] AS $oneRow) {
	$str = trim($oneRow);
	if (strpos($str, 'н/расп') !== false && strpos($str, 'н/расп</td>') === false) {
		$str = str_replace('н/расп', 'не расп</td>', $str);
	}
	if (strpos($str, 'год') !== false && strpos($str, 'год</td>') === false) {
		$str = str_replace('год', 'год</td>', $str);
	}
	$result = preg_match_all(
		/** @lang text */
		"/<td[^>]*>(\n?\r?.*?)<\/td>/s", $str, $tdMatches);
	$counter = 0;
	if ($debugInfo && $withDetails) {
		$s = !\Kint::dump(microtime(), 'got TDs');
		\Kint::dump($tdMatches);
	}
	foreach ((array)$tdMatches[1] AS $oneColumn) {
		if (array_key_exists($counter, $neededColumns)) {
			$columnName = $neededColumns[$counter];
			$columnText = str_replace(',', '.', trim(strip_tags($oneColumn)));
			if ($counter === 4) {
				if (strlen($columnText) < 10) {
					unset($resultsArr[$resultsCounter]);
					break;
				}
				$estimate = strpos($columnText, ' П') !== FALSE;
				$resultsArr[$resultsCounter]['Estimate'] = $estimate;
				list ($day, $month, $year) = explode('.', substr($columnText, 0, 10));
//				$columnText = $year . '-' . $month . '-' . $day;
				$columnText = $day . '/' . $month .'/' . $year;
			}
			if ($debugInfo && $withDetails) {
				\Kint::dump($columnText);
			}
			$resultsArr[$resultsCounter][$columnName] = $columnText;
		}
		$counter++;
	}
	$resultsCounter++;
}

if ($debugInfo) {
	$s = !\Kint::dump(microtime(), 'finished preparing results array');
}

echo json_encode($resultsArr);

/*$newHTML = str_replace('REPLACE_ME', $rowsStr, $newHTML);

$document = new DOMDocument();
$document->loadHTML($newHTML);
$rows = $document->getElementsByTagName('tr');
foreach ($rows AS $oneRow) {

	ddd ( $oneRow->nodeValue );
}
$count = $matches->length;

ddd ( $count );*/