var $jq = jQuery.noConflict();

var myAngularModule = angular.module("TickerService", ['ngResource']);

myAngularModule.controller("TickerController", ['$scope', '$http', function ($scope, $http) {
	$scope.Code = "SVTK";
	$scope.Description = "SV Ticker";
	$scope.Index = undefined;
	$scope.num = 4;

	$http.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';

	var currentResource;
	var resetForm = function () {
		$scope.addMode = true;
		$scope.Code = undefined;
		$scope.Description = undefined;
		$scope.selectedIndex = undefined;
		$scope.Index = undefined;
	};

	$scope.tickers = [];
	$scope.addMode = true;

	$scope.add = function () {
		var value = {id:null, code: $scope.Code, description: $scope.Description};

		var addCallBackFunc = function(data) {
			console.log(data, '***********data***********');
			if (data['result'] == 'ok') {
				currentResource = {};
				currentResource.Code = $scope.Code;
				currentResource.Description = $scope.Description;
				$scope.tickers.push(currentResource);

				resetForm();
			}
			else {
				alert(data['result']);
			}
		};

		$scope.saveTicker(value, addCallBackFunc);
	};

	$scope.saveTicker = function(value, callBackFunc) {

		$http.post('assets/save_ticker', $jq.param(value)).then(function(response) {
			var responseData = response.data;
			if (typeof callBackFunc === 'function') {
				callBackFunc(responseData);
			}
		});

		return true;

	};

	$scope.update = function () {
		var value = {id: $scope.Code, code: $scope.Code, description: $scope.Description};
		var index = $scope.Index;

		var updateCallBackFunc = function(data) {
			if (data['result'] == 'ok') {
				currentResource = $scope.tickers[index];
				currentResource.Code = $scope.Code;
				currentResource.Description = $scope.Description;

				resetForm();
			}
			else {
				alert (data['result']);
			}
		};

		$scope.saveTicker(value, updateCallBackFunc);
	};

	$scope.refresh = function () {
		$http.get('json/assets').then(function(response) {
			$scope.tickers = response.data;
			resetForm();
		});
	};

	$scope.deleteTicker = function (index, id) {
		var value = {id: id};

		$http.post('assets/delete_ticker', $jq.param(value)).then(function(response) {
			var data = response.data;
			console.log(response.data, '***********response***********');

			if (data['result'] == 'ok') {
				$scope.tickers.splice(index, 1);
			}
			else {
				alert (data['result']);
			}
			resetForm();
		});
	};

	$scope.selectTicker = function (index) {
		currentResource = $scope.tickers[index];
		$scope.addMode = false;
		$scope.Code = currentResource.Code;
		$scope.Description = currentResource.Description;
		$scope.Index = index;
	};

	$scope.cancel = function () {
		resetForm();
	};

	$scope.double = function(value) { return value * 2; };
}
]);


myAngularModule.factory('Message', ['$resource', function ($resource) {
	return $resource('/api/message/resource/:id');
}]);
