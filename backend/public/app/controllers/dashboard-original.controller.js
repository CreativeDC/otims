BookDistribution.controller('dashboardController', function dashboardController($scope, $http, $location, constants ,$timeout) {
		// set our current page for pagination purposes
		 $scope.sentCurrentPage=1;
		 $scope.sentLastPage=1;
		 $scope.loadMoreSent='Load More Sent Shipments ...';
         $scope.sent_list = {};
         $scope.sent_shipment = {};
         $scope.sent_record = {};
         $scope.sent_record_langs = {};
         $scope.vendor_list = {};

         $scope.receive_shipment = {};
         $scope.receive_record = {};
         $scope.shipment_receive = {};
         $scope.shipment_receive_level = {};
         $scope.to_node_list = {};


         $scope.temp_list = [{title:"some1"},{title:"some2"}];


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

         $scope.grade_selected = [];



         $scope.send_error = false;
         $scope.receive_error = false;
         $scope.errorReceiveMessages = {};



		//retrieve levels list from API
		$http.get(constants.API_URL + "ACRbookdis/record/sent/list", {params: { page: $scope.sentCurrentPage }})
			.success(function(response) {
				$scope.sent_list = response;
				$scope.sentCurrentPage = response.current_page;
				$scope.sentLastPage = response.last_page;
				
				if($scope.sentCurrentPage >= $scope.sentLastPage){
					$scope.loadMoreSent='All sent shipments Loaded!';
				}
			});

        $http.get(constants.API_URL + "ACRbookdis/record/balance/list")
            .success(function(response) {
                $scope.balance_list = response;
            });




    /*  loading the send and receive list*/
    $http.get(constants.API_URL + "ACRbookdis/grade/list")
        .success(function(response) {
            $scope.grade_list = response[0];
            angular.forEach($scope.grade_list, function(value, key) {
                $scope.grade_selected[value.grade.id] = false;
                //$scope.branch_grade_list[value.grade.id] = value.grade;
            });

            console.log("grade_seleceted");
            console.log($scope.grade_selected);
            console.log("grade_seleceted");
        });

    $http.get(constants.API_URL + "ACRbookdis/language/list")
        .success(function(response) {
            $scope.language_list = response;
        });

    $http.get(constants.API_URL + "ACRbookdis/language/full/list")
        .success(function(response) {
            $scope.lang_full_list = response;
        });
    /*  loading the send and receive list*/

    if($scope.is_parent == 1){

        $scope.receipt_list = {};
        $http.get(constants.API_URL + "ACRbookdis/record/receipt/list")
            .success(function(response) {
                $scope.receipt_list = response;
            });
    }

    if($scope.has_beneficiary == 1){

        $scope.to_beneficiary_list = {};
        $scope.from_beneficiary_list = {};
        $http.get(constants.API_URL + "ACRbookdis/record/to_benefici/list")
            .success(function(response) {
                $scope.to_beneficiary_list = response;
            });

        $http.get(constants.API_URL + "ACRbookdis/record/from_benefici/list")
            .success(function(response) {
                $scope.from_beneficiary_list = response;
            });
    }



    $scope.receive_list = {};

    $http.get(constants.API_URL + "ACRbookdis/record/received/list")
        .success(function(response) {
            $scope.receive_list = response;
        });



    // display the modal form
    $scope.loadDetailSentPage = function(sent_id) {

        $scope.sent_record = {};
        $scope.sent_shipment = {};
        $scope.sent_record_langs = {};
        //retrieve levels list from API
        $http.get(constants.API_URL + "ACRbookdis/record/sent/"+sent_id+"/shipment/get")
            .success(function(response) {
                $scope.sent_shipment = response;
            });
        //retrieve levels list from API
        $http.get(constants.API_URL + "ACRbookdis/record/sent/"+sent_id+"/get")
            .success(function(response) {
                $scope.sent_record = response;
            })
            .error(function(response, status, headers, config) {
                // alert and log the response
                alert('Failed to load node please research it to get result!');


            });
        $http.get(constants.API_URL + "ACRbookdis/record/sent/"+sent_id+"/get/group")
            .success(function(response) {
                $scope.sent_record_langs = response;
            })
            .error(function(response, status, headers, config) {
                // alert and log the response
                alert('Failed to load node please research it to get result!');

            });
        $('#showSentDetailModal').modal('show');
    }

    // display the modal form
    $scope.loadDetailReceivePage = function(receive_id) {


        $scope.receive_shipment = {};
        $scope.shipment_receive = {};
        $scope.receive_record = {};
        $scope.disable_recieve = false;
        //retrieve levels list from API
        $http.get(constants.API_URL + "ACRbookdis/record/receive/"+receive_id+"/shipment/get")
            .success(function(response) {
                $scope.receive_shipment = response;
                if($scope.receive_shipment.received == 1){
                    $scope.disable_recieve = true;
                }

                console.log("the received package");
                console.log($scope.receive_shipment);
            });
        $http.get(constants.API_URL + "ACRbookdis/record/shipment/"+receive_id+"/receive/get")
            .success(function(response) {
                $scope.shipment_receive = response;

            });
        //retrieve levels list from API
        $http.get(constants.API_URL + "ACRbookdis/record/receive/"+receive_id+"/get")
            .success(function(response) {
                $scope.receive_record = response;
            })
            .error(function(response, status, headers, config) {
                // alert and log the response
                alert('Failed to load node please research it to get result!');


            });

        $http.get(constants.API_URL + "ACRbookdis/record/receive/"+receive_id+"/get/group")
            .success(function(response) {
                $scope.receive_record_langs = response;
            })
            .error(function(response, status, headers, config) {
                // alert and log the response
                alert('Failed to load node please research it to get result!');

            });

        $('#showReceiveDetailModal').modal('show');
    }

    // display the modal form
    $scope.loadSentPage = function() {

        //retrieve levels list from API
       /* $http.get(constants.API_URL + "ACRbookdis/grade/list")
            .success(function(response) {
                $scope.grade_list = response;
            });

        $http.get(constants.API_URL + "ACRbookdis/language/list")
            .success(function(response) {
                $scope.language_list = response;
            });

        $http.get(constants.API_URL + "ACRbookdis/language/full/list")
            .success(function(response) {
                $scope.lang_full_list = response;
            });

        $http.get(constants.API_URL + "ACRbookdis/receipt/level/list")
            .success(function(response) {
                $scope.shipment_receive_level = response;
            });*/


        $http.get(constants.API_URL + "ACRbookdis/receipt/level/list")
            .success(function(response) {
                $scope.shipment_receive_level = response;
            });

        $('#sendModal').modal('show');
        // loading the datepicker for the nominated fields
        $(".datepick").datepicker().datepicker("setDate", new Date());
    }
    // display the modal form
    $scope.loadToBeneficiaryPage = function() {

        //retrieve levels list from API

        // It is done in the reload time of the page
        /*$http.get(constants.API_URL + "ACRbookdis/grade/list")
            .success(function(response) {
                $scope.grade_list = response;
            });

        $http.get(constants.API_URL + "ACRbookdis/language/list")
            .success(function(response) {
                $scope.language_list = response;
            });

        $http.get(constants.API_URL + "ACRbookdis/language/full/list")
            .success(function(response) {
                $scope.lang_full_list = response;
            });*/


        $('#toBeneficiaryModal').modal('show');
        // loading the datepicker for the nominated fields
        $(".datepick").datepicker().datepicker("setDate", new Date());
    }
    // display the modal form
    $scope.loadFromBenficiaryPage = function() {

        //retrieve levels list from API

        // It is done in the reload time of the page
        /*$http.get(constants.API_URL + "ACRbookdis/grade/list")
            .success(function(response) {
                $scope.grade_list = response;
            });

        $http.get(constants.API_URL + "ACRbookdis/language/list")
            .success(function(response) {
                $scope.language_list = response;
            });

        $http.get(constants.API_URL + "ACRbookdis/language/full/list")
            .success(function(response) {
                $scope.lang_full_list = response;
            });*/


        $('#fromBeneficiaryModal').modal('show');
        // loading the datepicker for the nominated fields
        $(".datepick").datepicker().datepicker("setDate", new Date());
    }
    // display the modal form
    $scope.loadReceivePage = function() {

        //retrieve levels list from API
        //retrieve levels list from API

        // It is done in the reload time of the page
        /*$http.get(constants.API_URL + "ACRbookdis/grade/list")
            .success(function(response) {
                $scope.grade_list = response;
            });

        $http.get(constants.API_URL + "ACRbookdis/language/list")
            .success(function(response) {
                $scope.language_list = response;
            });

        $http.get(constants.API_URL + "ACRbookdis/language/full/list")
            .success(function(response) {
                $scope.lang_full_list = response;
            });*/


        if(! $scope.disable_recieve){
            $('#recieveModal').modal('show');
            // loading the datepicker for the nominated fields
            $(".datepick").datepicker().datepicker("setDate", new Date());
        }
    }

    // display the modal form
    $scope.loadRechargePage = function() {

        //retrieve levels list from API

        // It is done in the reload time of the page
        /*$http.get(constants.API_URL + "ACRbookdis/vendor/list")
            .success(function(response) {
                $scope.vendor_list = response;
            });

        //retrieve levels list from API
        $http.get(constants.API_URL + "ACRbookdis/grade/list")
            .success(function(response) {
                $scope.grade_list = response;
            });

        $http.get(constants.API_URL + "ACRbookdis/language/list")
            .success(function(response) {
                $scope.language_list = response;
            });*/

        $http.get(constants.API_URL + "ACRbookdis/record/balance/list")
            .success(function(response) {
                $scope.balance_list = response;
            });


        $('#rechargeModal').modal('show');
        // loading the datepicker for the nominated fields
        $(".datepick").datepicker().datepicker("setDate", new Date());
    }


    // display the modal form
    $scope.update_title_list = function() {

        //retrieve levels list from API
        $http.get(constants.API_URL + "ACRbookdis/grade/"+$scope.send_shipment.grade+"/title")
            .success(function(response) {
                $scope.grade_titles_list = response;
            });
        //retrieve levels list from API
        $http.get(constants.API_URL + "ACRbookdis/language/list")
            .success(function(response) {
                $scope.language_list = response;
            });

    }
    // display the modal form
    $scope.grade_list_update = function() {

        //retrieve levels list from API
        $http.get(constants.API_URL + "ACRbookdis/grade/list")
            .success(function(response) {
                $scope.grade_list = response;
            });
    }

    // adding a node
    $scope.sendShipment = function() {

        $scope.errorMessages = {};
        $http.post(constants.API_URL + "ACRbookdis/shipment/send", $scope.send_shipment)
            .success(function(response) {
                console.log(response);
                //$scope.reloadNodes();
                $http.get(constants.API_URL + "ACRbookdis/record/balance/list")
                    .success(function(response) {
                        $scope.balance_list = response;
                    });

                if($scope.is_parent == 1){

                    $scope.receipt_list = {};
                    $http.get(constants.API_URL + "ACRbookdis/record/receipt/list")
                        .success(function(response) {
                            $scope.receipt_list = response;
                        });
                }

                // close the modal
                //$scope.closeSendModal();

            })
            .error(function(response, status, headers, config) {
                if(status == 401){
                    if(response.hasOwnProperty("internal_code")){
                        if(response.internal_code == 502){
                            $scope.send_error = true;
                            $scope.errorMessages = response.messages;
                            console.log($scope.errorMessages);
                        }
                    }else{
                        alert("response.message");
                    }
                }



            });

    }
    // adding a node
    $scope.sendToBeneficiaryShipment = function() {

        $http.post(constants.API_URL + "ACRbookdis/shipment/send/to_beneficiary", $scope.toBeneficiary)
            .success(function(response) {
                console.log(response);
                $scope.to_beneficiary_list = {};
                $scope.from_beneficiary_list = {};
                //$scope.reloadNodes();
                $http.get(constants.API_URL + "ACRbookdis/record/balance/list")
                    .success(function(response) {
                        $scope.balance_list = response;
                    });
                $http.get(constants.API_URL + "ACRbookdis/record/to_benefici/list")
                    .success(function(response) {
                        $scope.to_beneficiary_list = response;
                    });

                $http.get(constants.API_URL + "ACRbookdis/record/from_benefici/list")
                    .success(function(response) {
                        $scope.from_beneficiary_list = response;
                    });
                // close the modal
                $scope.closeToBeneficiaryModal();

            })
            .error(function(response, status, headers, config) {
                if(status == 401){
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
    // adding a node
    $scope.sendFromBeneficiaryShipment = function() {

        $http.post(constants.API_URL + "ACRbookdis/shipment/send/from_beneficiary", $scope.fromBeneficiary)
            .success(function(response) {
                console.log(response);
                $scope.to_beneficiary_list = {};
                $scope.from_beneficiary_list = {};
                //$scope.reloadNodes();
                $http.get(constants.API_URL + "ACRbookdis/record/balance/list")
                    .success(function(response) {
                        $scope.balance_list = response;
                    });
                $http.get(constants.API_URL + "ACRbookdis/record/to_benefici/list")
                    .success(function(response) {
                        $scope.to_beneficiary_list = response;
                    });

                $http.get(constants.API_URL + "ACRbookdis/record/from_benefici/list")
                    .success(function(response) {
                        $scope.from_beneficiary_list = response;
                    });
                // close the modal
                $scope.closeFromBeneficiaryModal();

            })
            .error(function(response, status, headers, config) {
                if(status == 401){
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

    // adding a node
    $scope.submitReceipt = function() {


        $http.post(constants.API_URL + "ACRbookdis/receipt/submit", $scope.recharge)
            .success(function(response) {
                console.log(response);

                //$scope.reloadNodes();

                $http.get(constants.API_URL + "ACRbookdis/record/balance/list")
                    .success(function(response) {
                        $scope.balance_list = response;
                    });

                if($scope.is_parent == 1){

                    $scope.receipt_list = {};
                    $http.get(constants.API_URL + "ACRbookdis/record/receipt/list")
                        .success(function(response) {
                            $scope.receipt_list = response;
                        });
                }
                // close the modal
                $scope.closeRechargeModal();

            })
            .error(function(response, status, headers, config) {
                if(status == 401){
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
    $scope.checkBoxchanged = function(grade_id , status_check_box){
        $scope.grade_selected[grade_id] = status_check_box;
        console.log("grade_changed");
        console.log($scope.grade_selected);
        console.log("grade_changed");
    }
    // adding a node
    $scope.submitReceive = function() {

        $scope.errorReceiveMessages = {};
        $scope.receive.recieve_id = $scope.shipment_receive.id;
        $scope.receive.received_grades = $scope.grade_selected;

        console.log("START");
        console.log($scope.receive);
        console.log("END");

        if(! $scope.frmRecieve.$invalid){
            $http.post(constants.API_URL + "ACRbookdis/receive/submit", $scope.receive)
                .success(function(response) {
                    console.log(response);

                    $http.get(constants.API_URL + "ACRbookdis/record/balance/list")
                        .success(function(response) {
                            $scope.balance_list = response;
                        });
                    // close the modal
                    $scope.closeReceiveModal();

                })
                .error(function(response, status, headers, config) {
                    if(status == 401){
                        if(response.hasOwnProperty("internal_code")){
                            if(response.internal_code == 502){
                                $scope.receive_error = true;
                                $scope.errorReceiveMessages = response.messages;
                                console.log($scope.errorMessages);
                            }
                        }else{
                            alert("response.message");
                        }
                    }

                });
        }
    }


    // display the modal form
    $scope.controlTable = function(id) {
        var name = "controltable_"+id;
        //alert(name);
        alert($scope[name]);
    }

    $scope.setSelectedTab = function(tabId) {

        $scope.animation = "scale-fade";
        $timeout(function () {
            $scope.selectedTab = tabId;
        }, 150 );

    }
    $scope.setSelectedTabReceiveSend = function(tabId) {

        $scope.animation = "scale-fade";
        $timeout(function () {
            $scope.selectedTabReceiveSend = tabId;
        }, 150 );

    }
    $scope.setSelectedTabDetail = function(tabId) {

        $scope.animation = "scale-fade";
        $timeout(function () {
            $scope.selectedTabDetail = tabId;
        }, 150 );

    }
    $scope.setSelectedTabReceive = function(tabId) {

        $scope.animation = "scale-fade";
        $timeout(function () {
            $scope.selectedTabReceive = tabId;
        }, 150 );

    }
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



    // display the modal form
    $scope.openSendModal = function() {
        $('#sendModal').modal('show');
    }
    // display the modal form
    $scope.closeSendModal = function() {
        $('#sendModal').modal('hide');
    }
    // display the modal form
    $scope.closeToBeneficiaryModal = function() {
        $('#toBeneficiaryModal').modal('hide');
    }
    // display the modal form
    $scope.closeFromBeneficiaryModal = function() {
        $('#fromBeneficiaryModal').modal('hide');
    }
    // display the modal form
    $scope.openRechargeModal = function() {
        $('#rechargeModal').modal('show');
    }
    // display the modal form
    $scope.closeRechargeModal = function() {
        $('#rechargeModal').modal('hide');
    }
    $scope.closeReceiveModal = function() {
        $('#recieveModal').modal('hide');
    }

	});



