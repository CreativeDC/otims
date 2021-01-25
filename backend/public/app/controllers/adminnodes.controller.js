BookDistribution.controller('adminnodesController', function adminnodesController($scope, $http, $location, constants) {
		// set our current page for pagination purposes
		 $scope.currentPage=1;
		 $scope.lastPage=1;
		 $scope.loadMoreText='Load More Nodes...';
         $scope.level_list = {};
         $scope.province_list = {};
         $scope.district_list = {};
         $scope.parent_level_list = {};

		
		//retrieve levels list from API
		$http.get(constants.API_URL + "ACRbookdis/admin/node/list", {params: { page: $scope.currentPage }})
			.success(function(response) {
				$scope.nodes = response.data;
				$scope.currentPage = response.current_page;
				$scope.lastPage = response.last_page;
				
				if($scope.currentPage >= $scope.lastPage){
					$scope.loadMoreText='All Nodes Loaded!';
				}
			});

        //retrieve levels list from API
        $http.get(constants.API_URL + "ACRbookdis/admin/level/list/all", {})
            .success(function(response) {
                $scope.level_list = response;
            });
        //retrieve levels list from API
        $http.get(constants.API_URL + "ACRbookdis/province/list/all", {})
            .success(function(response) {
                $scope.province_list = response;
            });
		
		// load more nodes
		$scope.loadMoreNodes = function() {
			// increase our current page index
			$scope.currentPage++;
			
			
			//retrieve more levels and append them to current list
			$http.get(constants.API_URL + "ACRbookdis/admin/node/list", {params: { page: $scope.currentPage }})
				.success(function(response) {
					$scope.nodes = $scope.nodes.concat(response.data);
					$scope.currentPage = response.current_page;
					$scope.lastPage = response.last_page;
					
					if($scope.currentPage >= $scope.lastPage){
						$scope.loadMoreText='All Nodes Loaded!';
					}
				});
				
		};
        // load more levels
        $scope.reloadNodes = function() {
            // increase our current page index

            //retrieve more levels and append them to current list
            $http.get(constants.API_URL + "ACRbookdis/admin/node/list", {params: { page: $scope.currentPage }})
                .success(function(response) {
                    $scope.nodes = response.data;
                    $scope.currentPage = response.current_page;
                    $scope.lastPage = response.last_page;

                    if($scope.currentPage >= $scope.lastPage){
                        $scope.loadMoreText='All Nodes Loaded!';
                    }
                });

        };
		
		// adding a node
		$scope.addNode = function() {

			$http.post(constants.API_URL + "ACRbookdis/admin/node/store", $scope.node)
				.success(function(response) {
					console.log(response);
                    $scope.reloadNodes();
					// close the modal
					$scope.closeAddNodeModal();

                    //$scope.frmAddNode.$setUntouched();
                    $scope.frmAddNode.$valid
                    $scope.frmAddNode.$setPristine();
                    $scope.frmAddNode.$setUntouched();
				})
				.error(function(response, status, headers, config) {
					// alert and log the response
					alert('Failed to add the : [Server response: '+status + '] - ' +response.name[0]);
					console.log(response);
					
				});

		}

        $scope.update_node_district = function(){
            //retrieve levels list from API
            $http.get(constants.API_URL + "ACRbookdis/district/"+$scope.node.province+"/list/all", {})
                .success(function(response) {
                    $scope.district_list = response;
                });
        }
        $scope.update_node_parent = function(){

            if($scope.node.level_id != 0){
                //retrieve levels list from API
                $http.get(constants.API_URL + "ACRbookdis/level/"+$scope.node.level_id+"/list/node", {})
                    .success(function(response) {
                        $scope.parent_level_list = response;
                    });
            }else{
                $scope.parent_level_list = [];
            }

        }

		// load the page for an individual node
		$scope.loadNodePage = function(id){
			 $location.path("node/"+id);
		}
		
		// display the modal form
		$scope.showAddNodeModal = function() {
			$('#addNodeModal').modal('show');
		}
		
		// display the modal form
		$scope.closeAddNodeModal = function() {
			$('#addNodeModal').modal('hide');
		}
	});