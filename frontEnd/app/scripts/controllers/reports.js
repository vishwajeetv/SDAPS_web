'use strict';

/**
 * @ngdoc function
 * @name sdapsApp.controller:MainCtrl
 * @description
 * # ReportsCtrl
 * Controller of the sdapsApp
 */
angular.module('sdapsApp')
  .controller('ReportsCtrl', function ($scope, $timeout, Restangular, $sce, $location, toastr) {


        $scope.trustAsHtml = function (value) {
            return $sce.trustAsHtml(value);
        };


        if (!sessionStorage.authenticated)
        {
            $location.path('/');
        }

        $timeout(function () {
            $scope.retrieveDepartments();
            //$scope.getReports();
        }, 1);

        $scope.logout = function (){

            delete sessionStorage.authenticated;
            $location.path('/');

        };

        $scope.departments =  null;

        $scope.reportsTable = new Array();

        $scope.reportsArray = new Array();

        $scope.getReports = function()
        {
            var getReportsMethod = Restangular.all('index.php/form/generate-reports-from-db');

            var getReportsData = {
                department : $scope.department
            };

            console.log($scope.department);

            getReportsMethod.post(getReportsData).then(function (response) {

                $scope.reports = response.body;
                //console.log("callback");
                //var count = Object.keys(response.body).length;
                //
                //var array = $.map(response.body, function(value, index) {
                //    return [value];
                //});
                //
                //$scope.reportsArray = array;
                //
                //var i = 0;
                //$scope.reportsArray.forEach(function(report) {
                //
                //        var key = Object.keys(response.body)[i];
                //        console.log(report);
                //        $scope.reportsTable.push(
                //            { count: {
                //                key : report['count']
                //            }   }
                //        );
                //i++;
                //    });

                //console.log(array);
                //
                //for( var i = 0; i < count; i++)
                //{
                //    var key = Object.keys(response.body)[i];
                //    $scope.reportsTable.push(
                //
                //             $scope.reports[key]
                //        );
                //}
                //console.log($scope.reportsTable);
                //$scope.reports = response.body;
                //
                //var total = response.length;
                //console.log(total);
                //total.forEach(function(report) {
                //    console.log(report);
                //    //$scope.reportTable.push(report);
                //
                //});
                toastr.success(response.header.message, 'Success');
            }, function () {
                    toastr.error('Sorry, something went wrong', 'Error');
                });
        };


        $scope.generateCharts = function() {


                var reports = $scope.reports;

                var totalReportGradeCount = reports.total.gradeCount;
                var totalCount = reports.total.count;


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

        };

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
