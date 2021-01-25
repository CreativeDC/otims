var BookDistribution = angular.module('BookDistribution', ['ngRoute','angucomplete-alt']);

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
			templateUrl : '/app/views/admin/reporting/dashboard.template.htm',
			controller  : 'adminreportController'
		})
		// default route
		.otherwise({
               redirectTo: '/'
        });
		
			
});

	