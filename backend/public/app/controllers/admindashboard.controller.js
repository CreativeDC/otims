BookDistribution.controller('admindashboardController', function admindashboardController($scope, $http, Upload, $location, constants, $timeout) {
    // set our current page for pagination purposes
    $scope.sentCurrentPage = 1;
    $scope.sentLastPage = 1;
    $scope.loadMoreSent = 'Load More Sent Shipments ...';
    $scope.sent_list = {};
    $scope.sent_shipment = {};
    $scope.sent_shipment_detail = {};
    $scope.sent_receive_shipment = {};
    $scope.sent_record = {};
    $scope.sent_record_langs = {};
    $scope.vendor_list = {};
    $scope.receive_list = {};
    $scope.group_receive_list = {};
    $scope.group_node_list = {};
    $scope.recieveCurrentPage = 1;
    $scope.recieveLastPage = 1;

    $scope.receive_shipment = {};
    $scope.receive_record = {};
    $scope.shipment_receive = {};
    $scope.shipment_receive_level = {};
    $scope.shipment_to_ptovince = {};
    $scope.shipment_to_district = {};
    $scope.to_node_list = {};


    $scope.temp_list = [{ title: "some1" }, { title: "some2" }];


    $scope.level_list = {};
    $scope.province_list = {};
    $scope.district_list = {};
    $scope.parent_level_list = {};
    $scope.grade_titles_list = {};
    $scope.language_list = {};
    $scope.lang_full_list = {};
    $scope.grade_list = {};
    $scope.branch_grade_list = {};
    $scope.balance_list = {};
    $scope.receipt_list = {};
    $scope.receive_list = {};
    $scope.to_beneficiary_list = {};
    $scope.from_beneficiary_list = {};
    $scope.errorMessages = {};
    $scope.successMessage = {};
    $scope.successMessageShow = false;
    $scope.successRecieveMessage = {};
    $scope.successRecieveMessageShow = false;






    $http.get(constants.API_URL + "ACRbookdis/receipt/level/list")
        .success(function (response) {
            $scope.shipment_receive_level = response;
        });

    $http.get(constants.API_URL + "ACRbookdis/province/list/all")
        .success(function (response) {
            $scope.shipment_to_ptovince = response;
        });


    $scope.level_changed = function (levelId) {

        /*changing the level cause the clearance at list of districts and province/district chosen*/
        $scope.shipment_to_district = {};
        $scope.send_shipment.province_to = {};
        $scope.send_shipment.district_to = {};
        $scope.to_node_list = {};
        /*changing the level cause the clearance at list of districts and province/district chosen*/
    }

    $scope.node_receive_changed = function (nodeId) {
        $scope.group_receive_list = [];

        $http.get(constants.API_URL + "ACRbookdis/record/received/" + nodeId + "/list")
            .success(function (response) {
                angular.forEach(response, function (value, key) {
                    $scope.group_receive_list = $scope.group_receive_list.concat(value);
                });
            });
    }
    $scope.province_changed = function (provinceId) {

        $scope.send_shipment.district_to = {};
        $scope.shipment_to_district = {};
        $scope.to_node_list = {};
        if (provinceId != 0) {
            //retrieve levels list from API
            $http.get(constants.API_URL + "ACRbookdis/district/" + provinceId + "/list/all")
                .success(function (response) {
                    $scope.shipment_to_district = response;
                });
        } else {
            $scope.shipment_to_district = {};
        }

    }

    $scope.district_changed = function (levelId, provinceId, districtId) {


        if (levelId != 0 && provinceId != 0 && districtId != 0) {

            $http.get(constants.API_URL + "ACRbookdis/child/" + levelId + "/" + provinceId + "/" + districtId + "/node")
                .success(function (response) {
                    $scope.to_node_list = response;
                });

        } else {
            $scope.to_node_list = {};
        }

    }

    $scope.node_changed = function (levelId, provinceId, districtId, nodeId) {


        if (levelId != 0 && provinceId != 0 && districtId != 0) {


            $http.get(constants.API_URL + "ACRbookdis/record/sent/" + nodeId + "/list")
                .success(function (response) {
                    $scope.sent_list = response;
                });

            $http.get(constants.API_URL + "ACRbookdis/record/received/" + nodeId + "/list")
                .success(function (response) {
                    $scope.receive_list = response;
                });

        } else {
        }

    }




    $scope.loadDetailReceivePage = function (receive_id) {




        $scope.sent_shipment_detail = {};
        $scope.sent_received_shipment_detail = {};
        $scope.sent_received_dmg_shipment_detail = {};
        $scope.sent_received_lost_shipment_detail = {};
        $scope.shipment_files = {};
        //retrieve levels list from API
        $http.get(constants.API_URL + "ACRbookdis/record/sent/" + receive_id + "/shipment/receive/get")
            .success(function (response) {
                if (response.hasOwnProperty("sent_shipment")) {
                    $scope.sent_shipment = response.sent_shipment;
                }
                //sent_received_shipment_detail
                if (response.hasOwnProperty("sent_detail")) {
                    $scope.sent_shipment_detail = response.sent_detail;
                }
                if (response.hasOwnProperty("sent_received_detail")) {
                    $scope.sent_received_shipment_detail = response.sent_received_detail;
                }
                if (response.hasOwnProperty("receive_dmg_grades_detail")) {
                    $scope.sent_received_dmg_shipment_detail = response.receive_dmg_grades_detail;
                }
                if (response.hasOwnProperty("receive_lost_grades_detail")) {
                    $scope.sent_received_lost_shipment_detail = response.receive_lost_grades_detail;
                }
                if (response.hasOwnProperty("receive_record")) {
                    // if(response.receive_record.length > 0){
                    if (response.receive_record.received == 1) {
                        $scope.sent_receive_status = "Recieved";
                        $scope.sent_receive_status_cntrl = true;
                    } else {
                        $scope.sent_receive_status = "Pending";
                        $scope.sent_receive_status_cntrl = false;
                    }
                    $scope.sent_receive_shipment = response.receive_record;

                    //alert($scope.sent_receive_shipment.re);
                    //}
                }
            });




        $scope.receive_shipment = {};
        $scope.shipment_receive = {};
        $scope.receive_record = {};
        $scope.disable_recieve = false;
        //retrieve levels list from API
        $http.get(constants.API_URL + "ACRbookdis/record/receive/" + receive_id + "/shipment/get")
            .success(function (response) {
                $scope.receive_shipment = response;
                if ($scope.receive_shipment.received == 1) {
                    $scope.disable_recieve = true;
                }

                console.log("the received package");
                console.log($scope.receive_shipment);
            });
        $http.get(constants.API_URL + "ACRbookdis/record/shipment/" + receive_id + "/receive/get")
            .success(function (response) {
                $scope.shipment_receive = response;

            });
        //retrieve levels list from API
        $http.get(constants.API_URL + "ACRbookdis/record/receive/" + receive_id + "/get")
            .success(function (response) {
                $scope.receive_record = response;
            })
            .error(function (response, status, headers, config) {
                // alert and log the response
                alert('Failed to load node please research it to get result!');


            });

        $http.get(constants.API_URL + "ACRbookdis/record/receive/" + receive_id + "/get/group")
            .success(function (response) {
                $scope.receive_record_langs = response;
            })
            .error(function (response, status, headers, config) {
                // alert and log the response
                alert('Failed to load node please research it to get result!');

            });

        $('#showReceiveDetailModal').modal('show');
    }


    $scope.setSelectedTabDetail = function (tabId) {

        $scope.animation = "scale-fade";
        $timeout(function () {
            $scope.selectedTabDetail = tabId;
        }, 1);

    }


    // display the modal form
    $scope.loadDetailSentPage = function (sent_id) {

        $scope.sent_record = {};
        $scope.sent_shipment = {};
        $scope.sent_receive_shipment = {};
        $scope.sent_record_langs = {};
        $scope.sent_receive_status = "Pending";
        $scope.sent_receive_status_cntrl = false;
        $scope.sent_shipment_detail = {};
        $scope.sent_received_shipment_detail = {};
        $scope.sent_received_dmg_shipment_detail = {};
        $scope.sent_received_lost_shipment_detail = {};
        $scope.shipment_files = {};
        //retrieve levels list from API
        $http.get(constants.API_URL + "ACRbookdis/record/sent/" + sent_id + "/shipment/get")
            .success(function (response) {
                if (response.hasOwnProperty("sent_shipment")) {
                    $scope.sent_shipment = response.sent_shipment;
                }
                //sent_received_shipment_detail
                if (response.hasOwnProperty("sent_detail")) {
                    $scope.sent_shipment_detail = response.sent_detail;
                }
                if (response.hasOwnProperty("sent_received_detail")) {
                    $scope.sent_received_shipment_detail = response.sent_received_detail;
                }
                if (response.hasOwnProperty("receive_dmg_grades_detail")) {
                    $scope.sent_received_dmg_shipment_detail = response.receive_dmg_grades_detail;
                }
                if (response.hasOwnProperty("receive_lost_grades_detail")) {
                    $scope.sent_received_lost_shipment_detail = response.receive_lost_grades_detail;
                }
                if (response.hasOwnProperty("receive_record")) {
                    // if(response.receive_record.length > 0){
                    if (response.receive_record.received == 1) {
                        $scope.sent_receive_status = "Recieved";
                        $scope.sent_receive_status_cntrl = true;
                    } else {
                        $scope.sent_receive_status = "Pending";
                        $scope.sent_receive_status_cntrl = false;
                    }
                    $scope.sent_receive_shipment = response.receive_record;

                    //alert($scope.sent_receive_shipment.re);
                    //}
                }
            });
        //retrieve levels list from API
        $http.get(constants.API_URL + "ACRbookdis/record/sent/" + sent_id + "/get")
            .success(function (response) {
                $scope.sent_record = response;
            })
            .error(function (response, status, headers, config) {
                // alert and log the response
                alert('Failed to load node please research it to get result!');
            });

        //retrieve levels list from API
        $http.get(constants.API_URL + "ACRbookdis/shipment/documents/" + sent_id + "/get")
            .success(function (response) {
                $scope.shipment_files = response;
            })
            .error(function (response, status, headers, config) {
                // alert and log the response
                alert('Failed to load node please research it to get result!');
            });


        $http.get(constants.API_URL + "ACRbookdis/record/sent/" + sent_id + "/get/group")
            .success(function (response) {
                $scope.sent_record_langs = response;
            })
            .error(function (response, status, headers, config) {
                // alert and log the response
                alert('Failed to load node please research it to get result!');

            });
        $('#showSentDetailModal').modal('show');
    }


    $scope.get_total = function (array_list) {

        $scope.total_array = 0;
        angular.forEach(array_list, function (value, key) {

            angular.forEach(value, function (val, ke) {


                $scope.total_array += val["total"];
            });
        });
        return $scope.total_array;
    }

});







