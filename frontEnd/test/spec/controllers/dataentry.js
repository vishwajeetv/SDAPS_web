'use strict';

describe('Controller: DataentryCtrl', function () {

  // load the controller's module
  beforeEach(module('sdapsApp'));

  var AuthenticationCtrl,
    scope;

  // Initialize the controller and a mock scope
  beforeEach(inject(function ($controller, $rootScope) {
    scope = $rootScope.$new();
    AuthenticationCtrl = $controller('DataentryCtrl', {
      $scope: scope
    });
  }));

});
