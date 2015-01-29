'use strict';

/**
 * @ngdoc function
 * @name sdapsApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the sdapsApp
 */
angular.module('sdapsApp')
  .controller('MainCtrl', function ($scope, $timeout, Restangular, $upload, $sce) {
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

                console.log(response.body);
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
                });
            }
        };


        $scope.departments =  null;



        $scope.generateCharts = function() {
            var getReportsMethod = Restangular.all('index.php/form/generate-reports-from-db');

            getReportsMethod.post().then(function (response) {

                console.log(response.body);
                var report = response.body;
                var totalReportGradeCount = report.total.gradeCount;
                var totalCount = report.total.count;


                $scope.chartConfig = {

                    options: {
                        chart: {
                            type: 'bar'
                        }
                    },
                    title: {
                        text: "good"
                    },
                    xAxis: {
                        categories: ['1-3 times', '3-6 times', 'more than 6 times']
                    },
                    yAxis: {
                        labels: {
                            formatter: function () {
                                var pcnt = (this.value / totalCount) * 100;
                                return Highcharts.numberFormat(pcnt, 0, ',') + '%';
                            }
                        }
                    },
                    tooltip: {
                        formatter: function () {
                            var pcnt = (this.y / totalCount) * 100;
                            return Highcharts.numberFormat(pcnt) + '%';
                        }
                    },
                    plotOptions: {
                        series: {
                            shadow: false,
                            borderWidth: 0,
                            dataLabels: {
                                enabled: true,
                                formatter: function () {
                                    var pcnt = (this.y / totalCount) * 100;
                                    return Highcharts.numberFormat(pcnt) + '%';
                                }
                            }
                        }
                    },
                    series: [{
                        type: 'bar',
                        colorByPoint: true,
                        data: [

                            ['excellent', totalReportGradeCount.excellent],
                            ['good', totalReportGradeCount.good],
                            ['satisfactory', totalReportGradeCount.satisfactory],
                            ['unsatisfactory', totalReportGradeCount.unsatisfactory],
                            ['mediocre', totalReportGradeCount.mediocre]
                        ]
                    }]
                }
            });
        };

        $scope.retrieveDepartments = function()
        {
            var retrieveDepartmentsMethod = Restangular.all('form/retrieve-departments');

            retrieveDepartmentsMethod.post().then(function (response) {

                console.log(response.body);
                $scope.departments = response.body;
            });
        };

  });
