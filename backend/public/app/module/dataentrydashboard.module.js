var BookDistribution = angular.module('BookDistribution', ['ngRoute','ngAnimate' , 'ngFileUpload' ,'angucomplete-alt' , 'ngMessages']);

 // Constants for app
BookDistribution.constant('constants', {
    //API_URL: 'http://74.208.121.179/'
    API_URL: 'http://127.0.0.1:8000/'
    //API_URL: 'http://192.168.128.88:8000/'
	});
	
// Route configuration
BookDistribution.config(function($routeProvider) {
	$routeProvider
		// route for the index page
		.when('/', {
			templateUrl : '/app/views/dataentry/reporting/dashboard.template.htm',
			controller  : 'dashboardController'
		})
		// default route
		.otherwise({
               redirectTo: '/'
        });


});

