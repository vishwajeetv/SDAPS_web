'use strict';

/**
 * @ngdoc function
 * @name sdapsApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the sdapsApp
 */
angular.module('sdapsApp')
  .controller('MainCtrl', function ($scope, $timeout, Restangular) {
    $scope.awesomeThings = [
      'HTML5 Boilerplate',
      'AngularJS',
      'Karma'
    ];

        $scope.departments =  null;
        $timeout(function () {

            $scope.getDepartments();

        }, 1);


        $scope.getDepartments = function()
        {
            var getCountriesMethod = Restangular.all('form/show');

            getCountriesMethod.post().then(function (response) {

                console.log(response.body);
                $scope.departments = response.body;
            });
        };

  });
