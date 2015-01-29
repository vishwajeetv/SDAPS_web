'use strict';

/**
 * @ngdoc function
 * @name sdapsApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the sdapsApp
 */
angular.module('sdapsApp')
  .controller('MainCtrl', function ($scope, $timeout, Restangular, toastr, $upload, $sce, $location) {
    $scope.awesomeThings = [
      'HTML5 Boilerplate',
      'AngularJS',
      'Karma'
    ];

        $scope.trustAsHtml = function (value) {
            return $sce.trustAsHtml(value);
        };


        if (!sessionStorage.authenticated)
        {
            $location.path('/');
        }

        $timeout(function () {
            $scope.retrieveDepartments();
        }, 1);

        $scope.logout = function (){

            delete sessionStorage.authenticated;
            $location.path('/');

        };

        $scope.uploadedFiles = null;

        $scope.department = null;

        $scope.processForms = function()
        {

                var processFormsData = {
                    filesData : []
            };

                for(var i=0; i < $scope.uploadedFiles.length; i++ )
                {
                    processFormsData.filesData.push(
                        {
                            'fileName' : $scope.uploadedFiles[i].fileName,
                            'total_pages' : $scope.uploadedFiles[i].totalPages
                        }
                    );
                }
            processFormsData.department = $scope.department;

            console.log(processFormsData);
            var processFormsMethod = Restangular.all('form/start-forms-processing');

            processFormsMethod.post(processFormsData).then(function (response) {

                toastr.success(response.header.message, 'Success');
                console.log(response.body);
            }, function () {
                toastr.error('Sorry, something went wrong', 'Error');
            });
        };
        $scope.onFileSelect = function($files) {


            console.log($files); // undefined
            //$files: an array of files selected, each file has name, size, and type.
            for (var i = 0; i < $files.length; i++) {
                var file = $files[i];
                $scope.upload = $upload.upload({
                    url: 'http://192.168.2.232/sdaps/public/form/upload-form', //upload.php script, node.js route, or servlet url
                    data: file,
                    file: file
                }).progress(function(evt) {
                    console.log('percent: ' + parseInt(100.0 * evt.loaded / evt.total));
                }).success(function(data, status, headers, config) {
                    // file is uploaded successfully

                    $scope.uploadedFiles = data.body;
                    console.log($scope.uploadedFiles);
                    toastr.success(data.header.message, 'Success');
                }).error(function(data, status, headers, config) {
                    // file is uploaded successfully

                    $scope.uploadedFiles = data.body;
                    console.log($scope.uploadedFiles);
                    toastr.error("Something went wrong", 'error');
                });
            }
        };


        $scope.departments =  null;

        $scope.retrieveDepartments = function()
        {
            var retrieveDepartmentsMethod = Restangular.all('form/retrieve-departments');

            retrieveDepartmentsMethod.post().then(function (response) {

                console.log(response.body);
                $scope.departments = response.body;
            }, function () {
                toastr.error('Sorry, can not retrieve departments, something went wrong', 'Error');
            });
        };

  });
