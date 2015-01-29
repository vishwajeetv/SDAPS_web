
'use strict';

/**
 * @ngdoc function
 * @name sdapsApp.controller:AuthenticationCtrl
 * @description
 * # AuthenticationCtrl
 * Controller of the sdapsApp
 */
angular.module('sdapsApp')
    .controller('DataentryCtrl', function ($scope, $location, toastr, Restangular,$timeout, pdfDelegate) {



        $timeout(function () {
            $scope.retrieveForms();
        }, 1);

        //$scope.totalItems = 12;
        $scope.currentPage = 1;

        $scope.setPage = function (pageNo) {
            $scope.currentPage = pageNo;

        };

        $scope.pageChanged = function() {

            var formIndex = $scope.currentPage-1;
            $scope.fileName = $scope.forms[formIndex].filename;
            console.log($scope.forms[formIndex].page);
            pdfDelegate.$getByHandle('my-pdf-container').goToPage($scope.forms[formIndex].page);
        };


        $scope.storeCitizenInfo = function()
        {
            var saveCitizenInfoMethod = Restangular.all('form/show');

            saveCitizenInfoMethod.post().then(function (response) {

                console.log(response.body);
                $scope.departments = response.body;
            });
        };


        $scope.retrieveForms = function()
        {
            var retrieveFormsMethod = Restangular.all('form/retrieve-unfilled-forms');

            retrieveFormsMethod.post().then(function (response) {

                console.log(response.body);
                $scope.forms = response.body;
                $scope.totalItems = $scope.forms.length;
                console.log( $scope.forms.length);

                var formIndex = 0;

                $scope.fileName = $scope.forms[formIndex].filename;
                $scope.feedbackId = $scope.forms[formIndex]._id;
                pdfDelegate.$getByHandle('my-pdf-container').goToPage(response.body[0].page);
            }, function () {
                toastr.error('Sorry, something went wrong', 'Error');
            });
        };


        $scope.storeCitizenInfo = function()
        {
            var storeCitizenInfoMethod = Restangular.all('form/store-citizen-info');

            var citizenInfo = {
                'feedbackId' : $scope.feedbackId,
                'name' : $scope.name,
                'email' : $scope.email,
                'address' : $scope.address,
                'mobile' : $scope.mobile,
                'meeting_reason' : $scope.reason
            };

            storeCitizenInfoMethod.post(citizenInfo).then(function (response) {
                toastr.success(response.header.message, 'Success');
                console.log(response.body);
            }, function () {
                toastr.error('Sorry, something went wrong', 'Error');
            });


        };



        $scope.pdfUrl = '/images/survey.pdf';



    });
