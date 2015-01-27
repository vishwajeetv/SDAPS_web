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

        // $scope.$watch('files', function() {
        //     $scope.upload = $upload.upload({
        //         url: 'http://localhost:8000/form/add-forms',
        //         data: {myObj: $scope.myModelObj},
        //         file: $scope.files
        //     }).progress(function(evt) {
        //         console.log('progress: ' + parseInt(100.0 * evt.loaded / evt.total) + '% file :'+ evt.config.file.name);
        //     }).success(function(data, status, headers, config) {
        //         console.log('file ' + config.file.name + 'is uploaded successfully. Response: ' + data);
        //     });
        // });

        $scope.departments =  null;
        $timeout(function () {
            $scope.getDepartments();
        }, 1);

        $scope.pdfUrl = 'pdf/ReferenceCard.pdf';

        $scope.getDepartments = function()
        {
            var getCountriesMethod = Restangular.all('form/show');

            getCountriesMethod.post().then(function (response) {

                console.log(response.body);
                $scope.departments = response.body;
            });
        };

  });
