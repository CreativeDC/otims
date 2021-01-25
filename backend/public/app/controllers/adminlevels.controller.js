BookDistribution.controller('adminlevelsController', function adminlevelsController($scope, $http, $location, constants) {
		// set our current page for pagination purposes
		 $scope.currentPage=1;
		 $scope.lastPage=1;
		 $scope.loadMoreText='Load More levels...';
		
		//retrieve levels list from API
		$http.get(constants.API_URL + "ACRbookdis/admin/level/list", {params: { page: $scope.currentPage }})
			.success(function(response) {
				$scope.levels = response.data;
				$scope.currentPage = response.current_page;
				$scope.lastPage = response.last_page;
				
				if($scope.currentPage >= $scope.lastPage){
					$scope.loadMoreText='All Levels Loaded!';
				}
			});
		
		// load more levels
		$scope.loadMoreLevels = function() {
			// increase our current page index
			$scope.currentPage++;
			
			
			//retrieve more levels and append them to current list
			$http.get(constants.API_URL + "ACRbookdis/admin/level/list", {params: { page: $scope.currentPage }})
				.success(function(response) {
					$scope.levels = $scope.levels.concat(response.data);
					$scope.currentPage = response.current_page;
					$scope.lastPage = response.last_page;
					
					if($scope.currentPage >= $scope.lastPage){
						$scope.loadMoreText='All Levels Loaded!';
					}
				});
				
		};
        // load more levels
        $scope.reloadLevels = function() {
            // increase our current page index

            //retrieve more levels and append them to current list
            $http.get(constants.API_URL + "ACRbookdis/admin/level/list", {params: { page: $scope.currentPage }})
                .success(function(response) {
                    $scope.levels = response.data;
                    $scope.currentPage = response.current_page;
                    $scope.lastPage = response.last_page;

                    if($scope.currentPage >= $scope.lastPage){
                        $scope.loadMoreText='All Levels Loaded!';
                    }
                });

        };
		
		// adding a level
		$scope.addLevel = function() {

			$http.post(constants.API_URL + "ACRbookdis/admin/level/store", $scope.level)
				.success(function(response) {
					console.log(response);
                    // load the page for our newly created burger
                    $scope.reloadLevels();
					// close the modal
					$scope.closeAddLevelModal();
				})
				.error(function(response, status, headers, config) {
					// alert and log the response
					alert('Failed to add the burger: [Server response: '+status + '] - ' +response.name[0]);
					console.log(response);
					
				});

		}
		

		// display the modal form
		$scope.showAddLevelModal = function() {
			$('#addLevelModal').modal('show');
		}
		
		// display the modal form
		$scope.closeAddLevelModal = function() {
			$('#addLevelModal').modal('hide');
		}
	});