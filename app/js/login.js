	(function() {

		var app = angular.module('loginModule', []);
		app.controller('loginCtrl', function($scope,$http) {
			console.log("Hi, Im in Login Ctrl");
			$scope.currentView = "userProfile";
			$scope.userId = 0;
			$scope.studioId = 0;

			console.log($scope.userId);

			$scope.isloggedIn = function()
			{
				if($scope.userId) return true;
				else return false;
			};

			$scope.getCurrentView = function(view){
				if(view == $scope.currentView) 
					{return true;}
				else {return false;}
			};

			$scope.setView = function(view){
				$scope.currentView = view;
			};

			$scope.logIn = function (logIn) {
				console.log("Im in login funcion");
				this.loginDetails = logIn;
				if(this.loginDetails)
				{
					$scope.userNameOrEmail = this.loginDetails.userNameOrEmail;
					$scope.userPassword = this.loginDetails.userPassword;
					$scope.errorInForm = 0;
					$scope.errorMessage = 0;

					if ($scope.userNameOrEmail && $scope.userPassword)
					{
						$http.post('/appStart/app/v1/login', {
							'inputfromLogin': $scope.userNameOrEmail,
							'userPassword': $scope.userPassword
						}
						).then(function(response){
							console.log(response.data.error);
							console.log(response.data.message);
							localStorage.userName = response.data.userName;
							localStorage.userId = response.data.userId;
							localStorage.userApiKey = response.data.userApiKey;
							localStorage.userEmail = response.data.userEmail;
							localStorage.userPhoneNumber = response.data.userPhoneNumber;
							localStorage.lastUpdatedOn = response.data.lastUpdatedOn;
							localStorage.lastUpdatedBy = response.data.lastUpdatedBy;
						}
						);
					}

					else
					{
						$scope.errorInForm = 1;
						$scope.errorMessage = "Please fill all mandatory fields.";
						console.log("Please fill all mandatory fields");
					}
				}
				else
				{
					$scope.errorInForm = 1;
					$scope.errorMessage = "Please fill all mandatory fields.";
					console.log("Please fill all mandatory fields");
				}
			};
			if(localStorage.userId)
			{
			$scope.currentUserName = localStorage.userName;
			$scope.userName = localStorage.userName;
			$scope.userId = localStorage.userId;
			$scope.userApiKey = localStorage.userApiKey;
			$scope.userEmail = localStorage.userEmail;
			$scope.userPhoneNumber = localStorage.userPhoneNumber
			$scope.lastUpdatedBy = localStorage.lastUpdatedBy;
			$scope.lastUpdatedOn = new Date(localStorage.lastUpdatedOn).toLocaleDateString();
			};
			$scope.updateDetails = function (userName,userEmail,userPhoneNumber) {
				if(!userName || !userEmail || !userPhoneNumber)
				{
					$scope.errorInForm = 1;
					$scope.errorMessage = "Please fill all mandatory fields.";
				}
				else
				{
					$http.post('/appStart/app/v1/updateUserDetails', {
							'userId': $scope.userId,
							'userName': $scope.userName,
							'userEmail': $scope.userEmail,
							'userPhoneNumber': $scope.userPhoneNumber
						}
						).then(function(response){
							console.log(response.data.error);
							console.log(response.data.message);
							if(!response.data.error)
							{
							localStorage.userName = $scope.userName;
							localStorage.userEmail = $scope.userEmail;
							localStorage.userPhoneNumber = $scope.userPhoneNumber;
							localStorage.lastUpdatedOn = new Date();
							}
							else
							{
								$scope.errorInForm = 1;
								$scope.errorMessage = "Email or Phone Number already exists.";
							}
						}
						);
				}
			};

			if(localStorage.userId)
			{
			$http.post('/appStart/app/v1/getStudioDetails', {
								'userId': $scope.userId
							}
			).then(function(response){
				if(response.data != "null"){
				$scope.getStudioDetails = response.data.studioDetails[0];
				$scope.eventDetails = response.data.eventDetails;
				localStorage.studioId = $scope.getStudioDetails.studioId;
				if(localStorage.studioId){
					$scope.studioId = localStorage.studioId;
				}
				console.log($scope.getStudioDetails);
				}
			});
			};

			$scope.isStudioExistsWithUser = function(){
				console.log($scope.studioId);
				if($scope.studioId) return true;
				else return false;
			};
		});

	})();
