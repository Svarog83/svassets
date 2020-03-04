<?php

namespace SVApp\Controllers;

use Silex\Api\ControllerProviderInterface;
use Silex\Application as App;

class ExportVesselControllerProvider implements ControllerProviderInterface {
	/**
	 * @var App $app
	 */
	private $app;

	public function connect(App $app) {
		$this->app = $app;

		$controllers = $app['controllers_factory'];

		$controllers->get('/{version}/{key}/{timespan}/{msgtype}/{params}/{protocol}',
			function () use ($app) {

				return $this->getETAData();
			})->bind('vesselsETA');

		return $controllers;
	}

	public function getETAData() {
		$str = '<POS>
  <row MMSI="304932001" LAT="51.93572" LON="4.142951" SPEED="0" HEADING="193" COURSE="143" STATUS="5" TIMESTAMP="2017-05-22T15:03:32" SHIPNAME="QUEEN MARY 2" SHIPTYPE="60" TYPE_NAME="Passengers Ship" AIS_TYPE_SUMMARY="Passenger" IMO="9375783" CALLSIGN="ZCEF6" FLAG="BM" PORT_ID="106" PORT_UNLOCODE="GBSOU" CURRENT_PORT="SOUTHAMPTON66" LAST_PORT_ID="137" LAST_PORT_UNLOCODE="USNYC" LAST_PORT="NEW YORK" LAST_PORT_TIME="2017-05-15T22:33:00" DESTINATION="ZEEBRUGGE" ETA="2017-05-23T04:30:00" ETA_CALC="2017-05-22T23:33:00" LENGTH="345.03" WIDTH="48.7" DRAUGHT="103" GRT="149215" NEXT_PORT_ID="265" NEXT_PORT_UNLOCODE="BEZEE" NEXT_PORT_NAME="ZEEBRUGGE" NEXT_PORT_COUNTRY="BE" DWT="19189" YEAR_BUILT="2003" DSRC="TER"/>
</POS>';
		/*$str = '<?xml version="1.0" encoding="UTF-8"?>
<POS/>';*/
		return $str;
	}
}
