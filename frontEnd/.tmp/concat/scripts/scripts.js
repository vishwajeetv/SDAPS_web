'use strict';
/**
 * @ngdoc overview
 * @name sdapsApp
 * @description
 * # sdapsApp
 *
 * Main module of the application.
 */
angular
  .module('sdapsApp', [
        'angular-loading-bar',
    'ngAnimate',
    'ngCookies',
    'ngResource',
    'ngRoute',
    'ngSanitize',
    'ngTouch',
        'ui.select',
      'restangular',
        'angularFileUpload',
        'pdf',
      'toastr',
        'highcharts-ng',
        'ui.bootstrap',
        'trNgGrid'
  ])
    .config(["RestangularProvider", function(RestangularProvider) {
        RestangularProvider.setBaseUrl('http://192.168.2.232/sdaps/public');
        RestangularProvider.setDefaultHeaders({ "Content-Type": "application/json" });
    }])
    .config(["toastrConfig", function(toastrConfig) {
      angular.extend(toastrConfig, {
        allowHtml: true,
        closeButton: false,
        closeHtml: '<button>&times;</button>',
        containerId: 'toast-container',
        extendedTimeOut: 1000,
        iconClasses: {
          error: 'toast-error',
          info: 'toast-info',
          success: 'toast-success',
          warning: 'toast-warning'
        },
        messageClass: 'toast-message',
        positionClass: 'toast-top-right',
        tapToDismiss: true,
        timeOut: 2000,
        titleClass: 'toast-title',
        toastClass: 'toast'
      });
    }])
  .config(["$routeProvider", function ($routeProvider) {
    $routeProvider
      .when('/', {
        templateUrl: 'views/authenticate.html',
        controller: 'AuthenticationCtrl'
      })
      .when('/main', {
        templateUrl: 'views/main.html',
        controller: 'MainCtrl'
      })
      .when('/dashboard',{
        templateUrl : 'views/dashboard.html',
        controller:'DashboardCtrl'
      })
      .when('/form', {
        templateUrl: 'views/form.html',
        controller: 'DataentryCtrl'
      })
      .when('/reports', {
        templateUrl: 'views/reports.html',
        controller: 'ReportsCtrl'
      })
      .when('/about', {
        templateUrl: 'views/about.html',
        controller: 'AboutCtrl'
      })
      .otherwise({
        redirectTo: '/'
      });
  }]);
'use strict';

/**
 * @ngdoc function
 * @name sdapsApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the sdapsApp
 */
angular.module('sdapsApp')
  .controller('MainCtrl', ["$scope", "$timeout", "Restangular", "toastr", "$upload", "$sce", function ($scope, $timeout, Restangular, toastr, $upload, $sce) {
    $scope.awesomeThings = [
      'HTML5 Boilerplate',
      'AngularJS',
      'Karma'
    ];

        $scope.trustAsHtml = function (value) {
            return $sce.trustAsHtml(value);
        };

        $timeout(function () {
            $scope.retrieveDepartments();
        }, 1);

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

  }]);
'use strict';

/**
 * @ngdoc function
 * @name sdapsApp.controller:AboutCtrl
 * @description
 * # AboutCtrl
 * Controller of the sdapsApp
 */
angular.module('sdapsApp')
  .controller('AboutCtrl', ["$scope", function ($scope) {
    $scope.awesomeThings = [
      'HTML5 Boilerplate',
      'AngularJS',
      'Karma'
    ];
  }]);


'use strict';

/**
 * @ngdoc function
 * @name sdapsApp.controller:AuthenticationCtrl
 * @description
 * # AuthenticationCtrl
 * Controller of the sdapsApp
 */
angular.module('sdapsApp')
    .controller('AuthenticationCtrl', ["$scope", "$location", "toastr", "Restangular", function ($scope, $location, toastr, Restangular) {

        if (sessionStorage.authenticated)
        {
            $location.path('/main');
        }


        $scope.login = function()
        {
            var loginUser = Restangular.all('user/sign-in');
            var userData = {

                email : $scope.username,
                password : $scope.password

            };
            loginUser.post(userData).then(function (response) {
                if (response.header.status == "success") {
                    sessionStorage.authenticated = true;

                    toastr.success(response.header.message, 'Success');
                    $location.path('/dashboard');
                }
                else {
                    toastr.error("Incorrect username or password", 'Error');
                    $scope.invalidCredentials = true;
                }

            }, function () {
                toastr.error('Sorry, something went wrong', 'Error');
            });
        };


    }]);


'use strict';

/**
 * @ngdoc function
 * @name sdapsApp.controller:AuthenticationCtrl
 * @description
 * # AuthenticationCtrl
 * Controller of the sdapsApp
 */
angular.module('sdapsApp')
    .controller('DataentryCtrl', ["$scope", "toastr", "Restangular", "$timeout", "pdfDelegate", function ($scope,toastr, Restangular,$timeout, pdfDelegate) {



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



    }]);

'use strict';

/**
 * @ngdoc function
 * @name sdapsApp.controller:MainCtrl
 * @description
 * # ReportsCtrl
 * Controller of the sdapsApp
 */
angular.module('sdapsApp')
  .controller('ReportsCtrl', ["$scope", "$timeout", "Restangular", "$sce", "toastr", function ($scope, $timeout, Restangular, $sce, toastr) {


        $scope.trustAsHtml = function (value) {
            return $sce.trustAsHtml(value);
        };

        $timeout(function () {
            $scope.retrieveDepartments();
            //$scope.getReports();
        }, 1);

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

  }]);


'use strict';

/**
 * @ngdoc function
 * @name sdapsApp.controller:AuthenticationCtrl
 * @description
 * # AuthenticationCtrl
 * Controller of the sdapsApp
 */
angular.module('sdapsApp')
    .controller('DashboardCtrl', ["$scope", "$location", function ($scope, $location) {

        if (!sessionStorage.authenticated)
        {
            $location.path('/');
        }

        $scope.logout = function (){

            delete sessionStorage.authenticated;
            $location.path('/');

        };

}]);