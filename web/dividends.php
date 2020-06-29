<?php

require_once '../vendor/raveren/kint/Kint.class.php';

$debugInfo = (boolean)(int)$_REQUEST['debugInfo'];
$withDetails = (boolean)(int)$_REQUEST['withDetails'];

$upcoming = array_key_exists('upcoming', $_REQUEST) ? $_REQUEST['upcoming'] : 1;
$onlyApproved = array_key_exists('is_approved', $_REQUEST) ? $_REQUEST['is_approved'] : NULL;

if ($debugInfo) {
	$s = !Kint::dump(microtime(), 'started');
}
$URL = "https://smart-lab.ru/dividends?year=&quarter=&upcoming=$upcoming";
if ($onlyApproved !== NULL) {
	$URL .= '&is_approved=$onlyApproved';
}
$htmlContent = file_get_contents($URL);
$htmlContent = preg_replace('/ +/', ' ', $htmlContent);
if ($debugInfo) {
	Kint::dump($URL);
	$s = !Kint::dump(microtime(), 'got content');
}
$result = preg_match_all(
	/** @lang text */
	"/<tr [class=\"dividend_approved\"]?[^>]*>(\n?\r?.*?)<\/tr>/s", $htmlContent, $matches);
if ($debugInfo) {
	$s = !Kint::dump(microtime(), 'got rows');
	Kint::dump($matches);
}

$resultsArr = [];
$rowsStr = '';

//$neededColumns = [1 => 'Ticker', 4 => 'Date', 6 => 'Year', 7 => 'Period', 8 => 'Amount'];
$neededColumns = [1 => 'Ticker', 9 => 'Date', 3 => 'Year', 4 => 'Period', 5 => 'Amount'];
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
		$s = !Kint::dump(microtime(), 'got TDs');
		Kint::dump($tdMatches);
	}
	foreach ((array)$tdMatches[1] AS $oneColumn) {
		if (array_key_exists($counter, $neededColumns)) {
			$columnName = $neededColumns[$counter];
			$columnText = str_replace(',', '.', trim(strip_tags($oneColumn)));
			if ($columnName === 'Date') {
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
				Kint::dump($columnText);
			}
			if ($columnName === 'Amount') {
				$columnText = preg_replace('/[^0-9\.]/', '', $columnText);
			}
			$resultsArr[$resultsCounter][$columnName] = $columnText;
		}
		$counter++;
	}

	//if current array with ticker's dividends does not have all required fields
	//we just remove such element
	if (count($resultsArr[$resultsCounter]) < 6) {
		unset($resultsArr[$resultsCounter]);
	}
	else {
		$resultsCounter++;
	}
}

if ($debugInfo) {
	$s = !Kint::dump(microtime(), 'finished preparing results array');
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