var myAngularModule = angular.module("MessageService", ['ngResource']);

myAngularModule.controller("MessageController", ['$scope', '$http', function ($scope, $http) {
	$scope.author = "Rajnish";
	$scope.message = "Delhi";
	$scope.num = 4;

	var currentResource;
	var resetForm = function () {
		$scope.addMode = true;
		$scope.author = undefined;
		$scope.message = undefined;
		$scope.selectedIndex = undefined;
	};

	$scope.messages = [];
	$scope.addMode = true;

	$scope.add = function () {
		var key = {};
		var value = {author: $scope.author, message: $scope.message};

		console.log(value, '***********value***********');

		Message.save(key, value, function (data) {
			$scope.messages.push(data);
			resetForm();
		});
	};

	$scope.update = function () {
		var key = {id: currentResource.id};
		var value = {author: $scope.author, message: $scope.message};
		Message.save(key, value, function (data) {
			currentResource.author = data.author;
			currentResource.message = data.message;
			resetForm();
		});
	};

	$scope.refresh = function () {
		console.log('sssss**********************');
		$http.get('json/assets').then(function(response) {
			console.log(response.data, '***********response.data***********');
			$scope.messages = response.data;
			resetForm();
		});
	};

	$scope.deleteMessage = function (index, id) {
		console.log(index, '***********index***********');
		console.log(id, '***********id***********');
		/*Message.delete({id: id}, function () {
			$scope.messages.splice(index, 1);
			resetForm();
		});*/
	};

	$scope.selectMessage = function (index) {
		currentResource = $scope.messages[index];
		$scope.addMode = false;
		$scope.author = currentResource.author;
		$scope.message = currentResource.message;
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
