
'use strict';

/**
 * @ngdoc function
 * @name sdapsApp.controller:AuthenticationCtrl
 * @description
 * # AuthenticationCtrl
 * Controller of the sdapsApp
 */
angular.module('sdapsApp')
    .controller('AuthenticationCtrl', function ($scope, $location, toastr, Restangular) {


        $scope.login = function(loginForm)
        {
            var loginUser = Restangular.all('http://localhost:8000/user/login');
            var userData = {

                email : loginForm.username,
                password : loginForm.password

            };
            loginUser.post(userData).then(function (response) {
                if (response.header.status == "success") {
                    sessionStorage.authenticated = true;

                    toastr.success(response.header.message, 'Success');
                    $location.path('/main');
                }
                else {
                    toastr.error("Incorrect username or password", 'Error');
                    $scope.invalidCredentials = true;
                }

            }, function () {
                toastr.error('Sorry, something went wrong', 'Error');
            });
        };


    });
