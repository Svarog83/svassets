<?php
require_once '../vendor/raveren/kint/Kint.class.php';

$marketType = $_GET['marketType'] ?: '';
$secID = $_GET['secID'] ?: '';

$marketType = preg_replace('/[^a-z]/i', '', $marketType);
$secID = preg_replace('/[^a-z0-9]/i', '', $secID);

if (!$marketType || !$secID) {
	echo 'Please provide all parameters!';
	exit();
}

$url = "http://iss.moex.com/iss/engines/stock/markets/{$marketType}/securities/{$secID}/securities.json?iss.meta=off";
$content = file_get_contents($url);

//Kint::dump( $url );
//Kint::dump( $content );

echo $content;
