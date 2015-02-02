
'use strict';

/**
 * @ngdoc function
 * @name sdapsApp.controller:AuthenticationCtrl
 * @description
 * # AuthenticationCtrl
 * Controller of the sdapsApp
 */
angular.module('sdapsApp')
    .controller('DashboardCtrl', function ($scope, $location) {

        if (!sessionStorage.authenticated)
        {
            $location.path('/');
        }

        $scope.logout = function (){

            delete sessionStorage.authenticated;
            $location.path('/');

        };

});