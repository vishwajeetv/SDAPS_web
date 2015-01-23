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
      'restangular'
  ])
    .config(function(RestangularProvider) {
        RestangularProvider.setBaseUrl('http://localhost:8000');
        RestangularProvider.setDefaultHeaders({ "Content-Type": "application/json" });
    })
  .config(function ($routeProvider) {
    $routeProvider
      .when('/', {
        templateUrl: 'views/main.html',
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
