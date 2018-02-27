	(function() {

		var app = angular.module('studioProfileCreation', []);
		app.controller('studioProfileCreationCtrl', function($scope,$http) {
			console.log("Hi, Im in CreateProfile Ctrl");
			$scope.studioProfile = {};
			$scope.createdStudioID = 0;
			console.log(localStorage.userId);
			$scope.createStudioProfile = function (studioProfile) {
				console.log("Im in CreateProfile funcion");
				$http.post('/appStart/app/v1/registerStudio', {
							'studioName': studioProfile.studioName,
							'studioLocation': studioProfile.location,
							'studioFacebookProfileuUrl': studioProfile.fbUrl,
							'studioOfficialUrl': studioProfile.officialWebsiteUrl,
							'studioEmail': studioProfile.studioeMailAddress,
							'studioPhoneNumber': studioProfile.studioPhoneNumber,
							'userId' : localStorage.userId
						}
						).then(function(response){
							console.log(response.data.error);
							console.log(response.data.message);
							$scope.createdStudioID = response.data.createdStudioID;
							console.log($scope.createdStudioID);
							if ($scope.createdStudioID)
								{
								$http.post('/appStart/app/v1/addAvailabilities', {
											'studioId': $scope.createdStudioID,
											'availabilityId': studioProfile.selectedAvailability.availableId
										}
										).then(function(response){
											console.log(response.data.error);
											console.log(response.data.message);
										}
										);
								for (var i = 1; i <= $scope.allEvents.length; i++) {
									if(studioProfile.allEvents[i])
									{
										$http.post('/appStart/app/v1/addEvents', {
											'studioId': $scope.createdStudioID,
											'eventId': i
										}
										).then(function(response){
											console.log(response.data.error);
											console.log(response.data.message);
										}
										);
									}
								}
								}
						}
						);
				
			};

			$http.get('/appStart/app/v1/getEvents'
						).then(function(response){
							$scope.allEvents = response.data;
						});
			$http.get('/appStart/app/v1/getAvailabilities'
						).then(function(response){
							$scope.allAvailabilities = response.data;
						});
		});
	})();
