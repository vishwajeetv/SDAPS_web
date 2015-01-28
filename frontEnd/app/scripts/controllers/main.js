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

    $scope.generateCharts = function()
        {
            var getReportsMethod = Restangular.all('index.php/form/generate-reports-from-db');

            getReportsMethod.post().then(function (response) {

                console.log(response.body);

                $scope.chartConfig = {
                    var report = response.body;
                    var totalReportGradeCount = report.total.gradeCount;
                    var totalCount = report.total.count;

                    options: 
                    {
                        chart: 
                        {
                            type: 'line',
                        }
                    },
                    title: {
                        text: title
                    },
                    xAxis: {
                        categories: ['1-3 times', '3-6 times', 'more than 6 times']
                    },
                    yAxis: {
                        labels: {
                            formatter:function() {
                                var pcnt = (this.value / totalCount) * 100;
                                return Highcharts.numberFormat(pcnt,0,',') + '%';
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

                            ['times_1_3', totalReportGradeCount.times_1_3],
                            ['3-6_times', totalReportGradeCount.times_3_6],
                            ['6_more_times', totalReportGradeCount.times_6_more]
                        ]
                    }]
                } 
                

            });
        };

                // $scope.chartConfig = {
                //     var report = response.body;
                //     var totalReportGradeCount = report.total.gradeCount;
                //     var totalCount = report.total.count;

                //     options: 
                //     {
                //         chart: 
                //         {
                //             type: 'line',
                //         }
                //     },
                //     title: {
                //         text: title
                //     },
                //     xAxis: {
                //         categories: ['1-3 times', '3-6 times', 'more than 6 times']
                //     },
                //     yAxis: {
                //         labels: {
                //             formatter:function() {
                //                 var pcnt = (this.value / totalCount) * 100;
                //                 return Highcharts.numberFormat(pcnt,0,',') + '%';
                //             }
                //         }
                //     },
                //     tooltip: {
                //         formatter: function () {
                //             var pcnt = (this.y / totalCount) * 100;
                //             return Highcharts.numberFormat(pcnt) + '%';
                //         }
                //     },
                //     plotOptions: {
                //         series: {
                //             shadow: false,
                //             borderWidth: 0,
                //             dataLabels: {
                //                 enabled: true,
                //                 formatter: function () {
                //                     var pcnt = (this.y / totalCount) * 100;
                //                     return Highcharts.numberFormat(pcnt) + '%';
                //                 }
                //             }
                //         }
                //     },
                //     series: [{
                //         type: 'bar',
                //         colorByPoint: true,
                //         data: [

                //             ['times_1_3', totalReportGradeCount.times_1_3],
                //             ['3-6_times', totalReportGradeCount.times_3_6],
                //             ['6_more_times', totalReportGradeCount.times_6_more]
                //         ]
                //     }]
                // });   
                
          


        $scope.onFileSelect = function($files) {

            if (!sessionStorage.authenticated)
            {
                $location.path('/');
            }

            console.log($files); // undefined
            //$files: an array of files selected, each file has name, size, and type.
            for (var i = 0; i < $files.length; i++) {
                var file = $files[i];
                $scope.upload = $upload.upload({
                    url: 'http://192.168.2.232/sdaps/public/form/upload-form', //upload.php script, node.js route, or servlet url
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
