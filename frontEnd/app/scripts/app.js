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
    .config(function(RestangularProvider) {
        RestangularProvider.setBaseUrl('http://192.168.2.22/sdaps/public');
        RestangularProvider.setDefaultHeaders({ "Content-Type": "application/json" });
    })
    .config(function(toastrConfig) {
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
    })
  .config(function ($routeProvider) {
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
  });