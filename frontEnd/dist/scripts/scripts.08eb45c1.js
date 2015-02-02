"use strict";angular.module("sdapsApp",["angular-loading-bar","ngAnimate","ngCookies","ngResource","ngRoute","ngSanitize","ngTouch","ui.select","restangular","angularFileUpload","pdf","toastr","highcharts-ng","ui.bootstrap","trNgGrid"]).config(["RestangularProvider",function(a){a.setBaseUrl("http://192.168.2.232/sdaps/public"),a.setDefaultHeaders({"Content-Type":"application/json"})}]).config(["toastrConfig",function(a){angular.extend(a,{allowHtml:!0,closeButton:!1,closeHtml:"<button>&times;</button>",containerId:"toast-container",extendedTimeOut:1e3,iconClasses:{error:"toast-error",info:"toast-info",success:"toast-success",warning:"toast-warning"},messageClass:"toast-message",positionClass:"toast-top-right",tapToDismiss:!0,timeOut:2e3,titleClass:"toast-title",toastClass:"toast"})}]).config(["$routeProvider",function(a){a.when("/",{templateUrl:"views/authenticate.html",controller:"AuthenticationCtrl"}).when("/main",{templateUrl:"views/main.html",controller:"MainCtrl"}).when("/dashboard",{templateUrl:"views/dashboard.html",controller:"DashboardCtrl"}).when("/form",{templateUrl:"views/form.html",controller:"DataentryCtrl"}).when("/reports",{templateUrl:"views/reports.html",controller:"ReportsCtrl"}).when("/about",{templateUrl:"views/about.html",controller:"AboutCtrl"}).otherwise({redirectTo:"/"})}]),angular.module("sdapsApp").controller("MainCtrl",["$scope","$timeout","Restangular","toastr","$upload","$sce",function(a,b,c,d,e,f){a.awesomeThings=["HTML5 Boilerplate","AngularJS","Karma"],a.trustAsHtml=function(a){return f.trustAsHtml(a)},b(function(){a.retrieveDepartments()},1),a.uploadedFiles=null,a.department=null,a.processForms=function(){for(var b={filesData:[]},e=0;e<a.uploadedFiles.length;e++)b.filesData.push({fileName:a.uploadedFiles[e].fileName,total_pages:a.uploadedFiles[e].totalPages});b.department=a.department,console.log(b);var f=c.all("form/start-forms-processing");f.post(b).then(function(a){d.success(a.header.message,"Success"),console.log(a.body)},function(){d.error("Sorry, something went wrong","Error")})},a.onFileSelect=function(b){console.log(b);for(var c=0;c<b.length;c++){var f=b[c];a.upload=e.upload({url:"http://192.168.2.232/sdaps/public/form/upload-form",data:f,file:f}).progress(function(a){console.log("percent: "+parseInt(100*a.loaded/a.total))}).success(function(b){a.uploadedFiles=b.body,console.log(a.uploadedFiles),d.success(b.header.message,"Success")}).error(function(b){a.uploadedFiles=b.body,console.log(a.uploadedFiles),d.error("Something went wrong","error")})}},a.departments=null,a.retrieveDepartments=function(){var b=c.all("form/retrieve-departments");b.post().then(function(b){console.log(b.body),a.departments=b.body},function(){d.error("Sorry, can not retrieve departments, something went wrong","Error")})}}]),angular.module("sdapsApp").controller("AboutCtrl",["$scope",function(a){a.awesomeThings=["HTML5 Boilerplate","AngularJS","Karma"]}]),angular.module("sdapsApp").controller("AuthenticationCtrl",["$scope","$location","toastr","Restangular",function(a,b,c,d){sessionStorage.authenticated&&b.path("/main"),a.login=function(){var e=d.all("user/sign-in"),f={email:a.username,password:a.password};e.post(f).then(function(d){"success"==d.header.status?(sessionStorage.authenticated=!0,c.success(d.header.message,"Success"),b.path("/dashboard")):(c.error("Incorrect username or password","Error"),a.invalidCredentials=!0)},function(){c.error("Sorry, something went wrong","Error")})}}]),angular.module("sdapsApp").controller("DataentryCtrl",["$scope","toastr","Restangular","$timeout","pdfDelegate",function(a,b,c,d,e){d(function(){a.retrieveForms()},1),a.currentPage=1,a.setPage=function(b){a.currentPage=b},a.pageChanged=function(){var b=a.currentPage-1;a.fileName=a.forms[b].filename,console.log(a.forms[b].page),e.$getByHandle("my-pdf-container").goToPage(a.forms[b].page)},a.storeCitizenInfo=function(){var b=c.all("form/show");b.post().then(function(b){console.log(b.body),a.departments=b.body})},a.retrieveForms=function(){var d=c.all("form/retrieve-unfilled-forms");d.post().then(function(b){console.log(b.body),a.forms=b.body,a.totalItems=a.forms.length,console.log(a.forms.length);var c=0;a.fileName=a.forms[c].filename,a.feedbackId=a.forms[c]._id,e.$getByHandle("my-pdf-container").goToPage(b.body[0].page)},function(){b.error("Sorry, something went wrong","Error")})},a.storeCitizenInfo=function(){var d=c.all("form/store-citizen-info"),e={feedbackId:a.feedbackId,name:a.name,email:a.email,address:a.address,mobile:a.mobile,meeting_reason:a.reason};d.post(e).then(function(a){b.success(a.header.message,"Success"),console.log(a.body)},function(){b.error("Sorry, something went wrong","Error")})},a.pdfUrl="/images/survey.pdf"}]),angular.module("sdapsApp").controller("ReportsCtrl",["$scope","$timeout","Restangular","$sce","toastr",function(a,b,c,d,e){a.trustAsHtml=function(a){return d.trustAsHtml(a)},b(function(){a.retrieveDepartments()},1),a.departments=null,a.reportsTable=new Array,a.reportsArray=new Array,a.getReports=function(){var b=c.all("index.php/form/generate-reports-from-db"),d={department:a.department};console.log(a.department),b.post(d).then(function(b){a.reports=b.body,e.success(b.header.message,"Success")},function(){e.error("Sorry, something went wrong","Error")})},a.generateCharts=function(){var b=a.reports,c=b.total.gradeCount,d=b.total.count;a.chartConfig={options:{chart:{type:"bar"}},title:{text:"good"},xAxis:{categories:["1-3 times","3-6 times","more than 6 times"]},yAxis:{labels:{formatter:function(){var a=this.value/d*100;return Highcharts.numberFormat(a,0,",")+"%"}}},tooltip:{formatter:function(){var a=this.y/d*100;return Highcharts.numberFormat(a)+"%"}},plotOptions:{series:{shadow:!1,borderWidth:0,dataLabels:{enabled:!0,formatter:function(){var a=this.y/d*100;return Highcharts.numberFormat(a)+"%"}}}},series:[{type:"bar",colorByPoint:!0,data:[["excellent",c.excellent],["good",c.good],["satisfactory",c.satisfactory],["unsatisfactory",c.unsatisfactory],["mediocre",c.mediocre]]}]}},a.retrieveDepartments=function(){var b=c.all("form/retrieve-departments");b.post().then(function(b){console.log(b.body),a.departments=b.body},function(){e.error("Sorry, can not retrieve departments, something went wrong","Error")})}}]),angular.module("sdapsApp").controller("DashboardCtrl",["$scope","$location",function(a,b){sessionStorage.authenticated||b.path("/"),a.logout=function(){delete sessionStorage.authenticated,b.path("/")}}]);