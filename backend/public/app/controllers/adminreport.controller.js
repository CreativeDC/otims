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

         $scope.province_list = {};
         $scope.district_list = {};
         $scope.report_list_province = {};
         $scope.report_list_province_sent = {};
         $scope.report_list_school = {};
         $scope.report_list_province_sent = {};





    $http.get(constants.API_URL + "ACRbookdis/admin/report/types")
        .success(function(response) {
            $scope.report_types = response;
        });


    $scope.report_changed = function(report_id) {

        //Province-wise Textbooks Reciept Report
       if(report_id == 1){
           $scope.province_list = {};
           $http.get(constants.API_URL + "ACRbookdis/province/list/all")
               .success(function(response) {
                   $scope.province_list = response;
               });
           $(".datepick").datepicker();
       }

        if(report_id == 2){
            $scope.province_list = {};
            $http.get(constants.API_URL + "ACRbookdis/province/list/all")
                .success(function(response) {
                    $scope.province_list = response;
                });
            $(".datepick").datepicker();
        }

        if(report_id == 3){
            $scope.province_list = {};
            $http.get(constants.API_URL + "ACRbookdis/province/list/all")
                .success(function(response) {
                    $scope.province_list = response;
                });
            $(".datepick").datepicker();
        }

        if(report_id == 4){
            $scope.province_list = {};
            $http.get(constants.API_URL + "ACRbookdis/province/list/all")
                .success(function(response) {
                    $scope.province_list = response;
                });
            $(".datepick").datepicker();
        }

        if(report_id == 5){

            $scope.province_list = {};
            $http.get(constants.API_URL + "ACRbookdis/province/list/all")
                .success(function(response) {
                    $scope.province_list = response;
                });
            $(".datepick").datepicker();
        }



    }
    $scope.searchReport = function() {


        $http.post(constants.API_URL + "ACRbookdis/report/1/search" , $scope.search_node)
            .success(function(response) {
                $scope.report_list_province = response.reports;

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
    $scope.searchReport_sent = function() {
        $scope.report_list_province_sent = {};

        $http.post(constants.API_URL + "ACRbookdis/report/2/search" , $scope.sent_search_node)
            .success(function(response) {
                $scope.report_list_province_sent = response.reports;

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

    $scope.searchReport_school = function() {
        $scope.report_list_school = {};

        $http.post(constants.API_URL + "ACRbookdis/report/3/search" , $scope.school_search_node)
            .success(function(response) {
                $scope.report_list_school = response.reports;

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


    $scope.searchReport_central = function() {
        $scope.report_list_central = {};

        $http.post(constants.API_URL + "ACRbookdis/report/4/search" , $scope.central_search_node)
            .success(function(response) {
                $scope.report_list_central = response.reports;

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




    $scope.searchReport_school_district = function() {
        $scope.report_list_school = {};

        $http.post(constants.API_URL + "ACRbookdis/report/5/search" , $scope.sent_search_node)
            .success(function(response) {
                $scope.report_list_district_school = response.reports;

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



    $scope.province_changed = function(province_id) {
        $scope.district_list = {};

        $http.get(constants.API_URL + "ACRbookdis/district/"+province_id+"/list/all", {})
            .success(function(response) {
                $scope.district_list = response;
            });
    }


});


