<?php
$url = 'http://iss.moex.com/iss/engines/stock/markets/index/securities/MOEXREPO/securities.json?iss.meta=off';
echo file_get_contents($url);