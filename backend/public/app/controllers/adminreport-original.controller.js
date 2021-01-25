BookDistribution.controller('adminreportController', function adminreportController($scope, $http, $location, constants) {
		// set our current page for pagination purposes
		 $scope.sentCurrentPage=1;
		 $scope.sentLastPage=1;
		 $scope.loadMoreSent='Load More Sent Shipments ...';
         $scope.level_list = {};
         $scope.to_node_list = {};
         $scope.sent_list = {};
         $scope.receive_list = {};
         $scope.has_report = false;
         $scope.has_receive_report = false;


         $scope.report_types = {};




    $http.get(constants.API_URL + "ACRbookdis/admin/report/types")
        .success(function(response) {
            $scope.report_types = response;
        });


    $scope.level_changed = function(levelId) {

        $scope.to_node_list = {};
        if(levelId != 0){
            //retrieve levels list from API
            $http.get(constants.API_URL + "ACRbookdis/child/"+levelId+"/node")
                .success(function(response) {
                    $scope.to_node_list = response;
                });
        }else{
            $scope.to_node_list = {};
        }

    }
    $scope.searchReport = function(node_id) {


        $scope.has_report = false;
        $scope.has_receive_report = false;
        $scope.sent_list = {};
        $scope.receive_list = {};

        $http.get(constants.API_URL + "ACRbookdis/report/"+node_id.description.id+"/send/search")
            .success(function(response) {
                $scope.sent_list = response;
                $scope.has_report = true;

            }).error(function(response, status, headers, config) {
                if(status == 401){
                    $scope.has_report = false;
                    if(response.hasOwnProperty("internal_code")){
                        if(response.internal_code == 501){
                            alert(response.message);
                        }
                    }else{
                        alert("response.message");
                    }
                }

                console.log(response);

            });

    }


});


