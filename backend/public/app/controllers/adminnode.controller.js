// create the controller and inject Angular's $scope
BookDistribution.controller('adminnodeController', function adminnodeController($scope, $http, $location, $routeParams, constants) {
		// set our current page for pagination purposes
		$scope.node_id = $routeParams.nodeID;
        $scope.node = [];
        $scope.users = [];
        $scope.users_result = [];

        $scope.searchButtonText = "Look for user";
        $scope.searching_user = false;

        $http.get(constants.API_URL + "ACRbookdis/admin/node/" + $scope.node_id +"/id")
            .success(function(response) {
                $scope.node = response;
            })
            .error(function(response, status, headers, config) {
                // log the response
                console.log(response);

                // alert and log the response
                alert('Failed to load node please refresh the page to get it working!');

        });
        $http.get(constants.API_URL + "ACRbookdis/admin/node/" + $scope.node_id +"/users")
            .success(function(response) {
                $scope.users = response;

            })
            .error(function(response, status, headers, config) {
                // log the response
                console.log(response);

                // alert and log the response
                alert('Failed to load node please refresh the page to get it working!');

        });
		// modal title
		$scope.modal_title = 'Add a new User to this node '+$scope.node.name+" as a staff";
		$scope.modal_button = "Attach user";


        // display the modal form
        $scope.reloadUser = function() {

            $http.get(constants.API_URL + "ACRbookdis/admin/node/" + $scope.node_id +"/users")
                .success(function(response) {
                    $scope.users = response;

                })
                .error(function(response, status, headers, config) {
                    // log the response
                    console.log(response);

                    // alert and log the response
                    alert('Failed to load node please refresh the page to get it working!');

                });
        }

        // display the modal form
        $scope.searchUser = function() {

            $scope.searchButtonText = "Searching the user, Please wait!";
            $scope.searching_user = true;
            $http.post(constants.API_URL + "ACRbookdis/admin/node/search/users", $scope.user_form)
                .success(function(response) {
                    console.log(response);

                    $scope.searchButtonText ="Look for user";
                    $scope.searching_user = false;
                    $scope.users_result = response;

                })
                .error(function(response, status, headers, config) {
                    // alert and log the response
                    alert('Failed to load node please research it to get result!');
                    console.log(response);

                });
        }


        // display the modal form
        $scope.attachUser = function(nodeId,userId , user_candid) {

            $http.get(constants.API_URL + "ACRbookdis/admin/node/"+nodeId+"/attach/"+userId+"/user")
                .success(function(response) {
                    console.log(response);
                    alert('The user has been attached to node successfully!');
                    user_candid.disabled = true;
                    $scope.reloadUser();
                })
                .error(function(response, status, headers, config) {
                    // alert and log the response
                    alert('Failed to load node please research it to get result!');
                    console.log(response);

                });
        }

    // display the modal form
        $scope.showAddUserModal = function() {
            $('#addUserModal').modal('show');
        }

        // display the modal form
        $scope.closeAddUserModal = function() {
            $('#addUserModal').modal('hide');
        }
	});