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
    'ngAnimate',
    'ngCookies',
    'ngResource',
    'ngRoute',
    'ngSanitize',
    'ngTouch',
      'restangular',
        'angularFileUpload',
        'pdf',
      'toastr',
        'highcharts-ng',
        'ui.bootstrap'
  ])
    .config(function(RestangularProvider) {
        RestangularProvider.setBaseUrl('http://localhost:8000');
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
      .when('/form', {
        templateUrl: 'views/form.html',
        controller: 'DataentryCtrl'
      })
      .when('/reports', {
        templateUrl: 'views/reports.html',
        controller: 'MainCtrl'
      })
      .when('/about', {
        templateUrl: 'views/about.html',
        controller: 'AboutCtrl'
      })
      .otherwise({
        redirectTo: '/'
      });
  });
