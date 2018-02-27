	(function() {

		var app = angular.module('signUp', []);
		app.controller('signUpCtrl', function($scope,$http) {
			console.log("Hi, Im in Sing Up Ctrl");
			$scope.signUp = function (signUp) {
				console.log("Im in SignUp funcion");
				this.signUpDetails = signUp;
				$scope.userName = this.signUpDetails.userName;
				$scope.userEmail = this.signUpDetails.userEmail;
				$scope.userPhoneNumber = this.signUpDetails.userPhoneNumber;
				$scope.userPassword = this.signUpDetails.userPassword;
				$scope.confirmPassword = this.signUpDetails.confirmPassword;
				$scope.errorInForm = 0;
				$scope.errorMessage = 0;

				if ($scope.userName && $scope.userEmail &&  $scope.userPhoneNumber && $scope.userPassword && $scope.confirmPassword)
				{
					if($scope.userPassword == $scope.confirmPassword)
					{
						$http.post('/appStart/app/v1/register', {
							'userName': $scope.userName,
							'userEmail': $scope.userEmail,
							'userPhoneNumber': $scope.userPhoneNumber,
							'userPassword': $scope.userPassword
						}
						).then(function(response){
							console.log(response.data.error);
							console.log(response.data.message	);
						}
						);
					}
					
					else
						{	$scope.errorInForm = 1;
							$scope.errorMessage = "Password and Confirm Password are not matching.";
							console.log("Passwords are not matching");
						}
					}

					else
					{
						$scope.errorInForm = 1;
						$scope.errorMessage = "Please fill all mandatory fields.";
						console.log("Please fill all mandatory fields");
					}
				};
			});

	})();
