'use strict';

/**
 * @ngdoc function
 * @name sdapsApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the sdapsApp
 */
angular.module('sdapsApp')
  .controller('MainCtrl', function ($scope, $timeout, Restangular, $upload) {
    $scope.awesomeThings = [
      'HTML5 Boilerplate',
      'AngularJS',
      'Karma'
    ];
        $scope.onFileSelect = function($files) {


            console.log($files); // undefined
            //$files: an array of files selected, each file has name, size, and type.
            for (var i = 0; i < $files.length; i++) {
                var file = $files[i];
                $scope.upload = $upload.upload({
                    url: 'http://localhost:8000/form/upload-form', //upload.php script, node.js route, or servlet url
                    data: file,
                    file: file,
                }).progress(function(evt) {
                    console.log('percent: ' + parseInt(100.0 * evt.loaded / evt.total));
                }).success(function(data, status, headers, config) {
                    // file is uploaded successfully
                    console.log(data);
                });
            }
        };


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
