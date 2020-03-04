<?php

namespace SVApp\Controllers;

use Silex\Api\ControllerProviderInterface;
use Silex\Application as App;

class VesselMasterDataControllerProvider implements ControllerProviderInterface {
	/**
	 * @var App $app
	 */
	private $app;

	public function connect(App $app) {
		$this->app = $app;

		$controllers = $app['controllers_factory'];

		$controllers->get('/{key}/{params}/{protocol}',
			function () use ($app) {

				return $this->getVesselData();
			})->bind('vesselsData');

		return $controllers;
	}

	public function getVesselData() {

		/*$str = '<MASTERDATA>
  <vessel MMSI="304932000" IMO="9375783" NAME="STAPELMOOR" PLACE_OF_BUILD="" BUILD="2007" BREADTH_EXTREME="12.4" SUMMER_DWT="2930" DISPLACEMENT_SUMMER="" CALLSIGN="V2BU8" FLAG="AG" DRAUGHT="4.36" LENGTH_OVERALL="88.53" FUEL_CONSUMPTION="" SPEED_MAX="" SPEED_SERVICE="10.5" LIQUID_OIL="" OWNER="" MANAGER="" VESSEL_TYPE="GENERAL CARGO"/>
</MASTERDATA>';*/
		$str = '<MASTERDATA TOTAL_RESULTS="1" TOTAL_PAGES="1" CURRENT_PAGE="1"> <vessel MMSI="304932001" IMO="9375783" NAME="STAPELMOOR" PLACE_OF_BUILD="" BUILD="2006" BREADTH_EXTREME="12.4" SUMMER_DWT="2930" DISPLACEMENT_SUMMER="0" CALLSIGN="V2BU8" FLAG="AG" DRAUGHT="4.36" LENGTH_OVERALL="88.53" FUEL_CONSUMPTION="" SPEED_MAX="0" SPEED_SERVICE="10.5" LIQUID_OIL="0" OWNER="Sergey Vetko" MANAGER="" MANAGER_OWNER="BOJEN REEDEREI" VESSEL_TYPE="GENERAL CARGO"/> </MASTERDATA>';
		return $str;
	}

}
