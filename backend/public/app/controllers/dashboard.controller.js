BookDistribution.controller('dashboardController', function dashboardController($scope, $http, Upload, $location, constants, $timeout) {
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


    $scope.node_id = 0;


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
    $scope.balance_d_list = {};
    $scope.receipt_list = {};
    $scope.receive_list = {};
    $scope.to_beneficiary_list = {};
    $scope.from_beneficiary_list = {};
    $scope.errorMessages = {};
    $scope.successMessage = {};
    $scope.successMessageShow = false;
    $scope.successRecieveMessage = {};
    $scope.successRecieveMessageShow = false;

    $scope.grade_selected = [];
    $scope.grade_selected_send = [];
    $scope.grade_selected_send_ben = [];
    $scope.grade_selected_receive_ben = [];



    $scope.send_error = false;
    $scope.receive_error = false;
    $scope.to_ben_error = false;
    $scope.errorReceiveMessages = {};
    $scope.errorToBenMessages = {};
    $scope.sent_receive_status = "Pending";
    $scope.sent_receive_status_cntrl = false;
    $scope.sent_received_shipment_detail = {};
    $scope.sent_received_dmg_shipment_detail = {};
    $scope.sent_received_lost_shipment_detail = {};
    $scope.shipment_files = {};


    $scope.sent_shipment_id_upload = 0;




    //retrieve levels list from API
    $http.get(constants.API_URL + "ACRbookdis/record/sent/list", { params: { page: $scope.sentCurrentPage } })
        .success(function (response) {
            $scope.sent_list = response.data;
            $scope.sentCurrentPage = response.current_page;
            $scope.sentLastPage = response.last_page;

            if ($scope.sentCurrentPage >= $scope.sentLastPage) {
                $scope.loadMoreSent = 'All sent shipments Loaded!';
            }
        });

    $http.get(constants.API_URL + "ACRbookdis/record/balance/list")
        .success(function (response) {
            $scope.balance_list = response;
        });


    $http.get(constants.API_URL + "ACRbookdis/record/balance/detail/list")
        .success(function (response) {
            $scope.balance_d_list = response;
        });


    $http.get(constants.API_URL + "ACRbookdis/record/group/node/list")
        .success(function (response) {
            $scope.group_node_list = response;
        });




    /*  loading the send and receive list*/
    $http.get(constants.API_URL + "ACRbookdis/grade/list")
        .success(function (response) {
            $scope.grade_list[0] = response[0];
            console.log("yes");
            console.log($scope.grade_list);
            console.log("yesNo");
            $scope.branch_grade_list = response;
            angular.forEach($scope.branch_grade_list, function (value, key) {
                $scope.grade_selected[value.grade.id] = false;
                $scope.grade_selected_send[value.grade.id] = false;
                $scope.grade_selected_send_ben[value.grade.id] = false;
                $scope.grade_selected_receive_ben[value.grade.id] = false;
            });

            console.log("grade_seleceted");
            console.log($scope.grade_selected);
            console.log("grade_seleceted");
        });

    $http.get(constants.API_URL + "ACRbookdis/language/list")
        .success(function (response) {
            $scope.language_list = response;
        });

    $http.get(constants.API_URL + "ACRbookdis/language/full/list")
        .success(function (response) {
            $scope.lang_full_list = response;
        });
    /*  loading the send and receive list*/

    if ($scope.is_parent == 1) {

        $scope.receipt_list = {};
        $http.get(constants.API_URL + "ACRbookdis/record/receipt/list")
            .success(function (response) {
                $scope.receipt_list = response;
            });
    }

    if ($scope.has_beneficiary == 1) {

        $scope.to_beneficiary_list = {};
        $scope.from_beneficiary_list = {};
        $http.get(constants.API_URL + "ACRbookdis/record/to_benefici/list")
            .success(function (response) {
                $scope.to_beneficiary_list = response;
            });

        $http.get(constants.API_URL + "ACRbookdis/record/from_benefici/list")
            .success(function (response) {
                $scope.from_beneficiary_list = response;
            });



        $http.get(constants.API_URL + "ACRbookdis/record/benefici/node_id")
            .success(function (response) {
                $scope.node_id = response.node_id;
            });




    }





    $http.get(constants.API_URL + "ACRbookdis/record/received/list", { params: { page: $scope.recieveCurrentPage } })
        .success(function (response) {
            $scope.receive_list = response.data;
            $scope.recieveCurrentPage = response.current_page;
            $scope.recieveLastPage = response.last_page;

        });



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

    // display the modal form
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

    // display the modal form
    $scope.loadSentPage = function () {

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
            .success(function (response) {
                $scope.shipment_receive_level = response;
            });

        $http.get(constants.API_URL + "ACRbookdis/province/list/all")
            .success(function (response) {
                $scope.shipment_to_ptovince = response;
            });

        $('#sendModal').modal('show');
        // loading the datepicker for the nominated fields
        $(".datepick").datepicker().datepicker("setDate", new Date());
    }
    // display the modal form
    $scope.loadToBeneficiaryPage = function () {

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
    $scope.loadFromBenficiaryPage = function () {

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
    $scope.loadReceivePage = function () {

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


        $scope.errorReceiveMessages = {};
        if (!$scope.disable_recieve) {
            $('#recieveModal').modal('show');
            // loading the datepicker for the nominated fields
            $(".datepick").datepicker().datepicker("setDate", new Date());
        }
    }

    // display the modal form
    $scope.loadRechargePage = function () {

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
            .success(function (response) {
                $scope.balance_list = response;
            });


        $('#rechargeModal').modal('show');
        // loading the datepicker for the nominated fields
        $(".datepick").datepicker().datepicker("setDate", new Date());
    }


    // display the modal form
    $scope.update_title_list = function () {

        //retrieve levels list from API
        $http.get(constants.API_URL + "ACRbookdis/grade/" + $scope.send_shipment.grade + "/title")
            .success(function (response) {
                $scope.grade_titles_list = response;
            });
        //retrieve levels list from API
        $http.get(constants.API_URL + "ACRbookdis/language/list")
            .success(function (response) {
                $scope.language_list = response;
            });

    }
    // display the modal form
    $scope.grade_list_update = function () {

        //retrieve levels list from API
        $http.get(constants.API_URL + "ACRbookdis/grade/list")
            .success(function (response) {
                $scope.grade_list = response;
            });
    }

    // adding a node
    $scope.sendShipment = function () {

        $scope.errorMessages = {};
        $scope.send_shipment.sent_grades = $scope.grade_selected_send;
        $http.post(constants.API_URL + "ACRbookdis/shipment/send", $scope.send_shipment)
            .success(function (response) {

                $scope.successMessageShow = true;
                $scope.successMessage = response.message;
                $('#send_success_msg').focus();

                alert(response.message);

                console.log(response);
                //$scope.reloadNodes();
                $http.get(constants.API_URL + "ACRbookdis/record/balance/list")
                    .success(function (response) {
                        $scope.balance_list = response;

                    });
                $http.get(constants.API_URL + "ACRbookdis/record/sent/list", { params: { page: 1 } })
                    .success(function (response) {
                        $scope.sent_list = response.data;
                    });

                if ($scope.is_parent == 1) {

                    $scope.receipt_list = {};
                    $http.get(constants.API_URL + "ACRbookdis/record/receipt/list")
                        .success(function (response) {
                            $scope.receipt_list = response;
                        });
                }


                $timeout(function () {
                    // close the modal
                    $scope.closeSendModal();
                    $scope.successMessage = {};
                    $scope.successMessageShow = false;
                }, 2000);



            })
            .error(function (response, status, headers, config) {
                if (status == 401) {
                    if (response.hasOwnProperty("internal_code")) {
                        if (response.internal_code == 502) {
                            $scope.send_error = true;
                            $scope.errorMessages = response.messages;
                            console.log($scope.errorMessages);
                            $('#sendModal').scrollTop(0);
                        }
                    } else {
                        alert("response.message");
                    }
                }



            });

    }
    // adding a node
    $scope.sendToBeneficiaryShipment = function () {

        $scope.toBeneficiary.sent_grades = $scope.grade_selected_send_ben;
        $scope.toBeneficiary.node_id = $scope.node_id;
        $http.post(constants.API_URL + "ACRbookdis/shipment/send/to_beneficiary", $scope.toBeneficiary)
            .success(function (response) {
                console.log(response);
                $scope.to_beneficiary_list = {};
                $scope.from_beneficiary_list = {};
                //$scope.reloadNodes();
                $http.get(constants.API_URL + "ACRbookdis/record/balance/list")
                    .success(function (response) {
                        $scope.balance_list = response;
                    });
                $http.get(constants.API_URL + "ACRbookdis/record/to_benefici/list")
                    .success(function (response) {
                        $scope.to_beneficiary_list = response;
                    });

                $http.get(constants.API_URL + "ACRbookdis/record/from_benefici/list")
                    .success(function (response) {
                        $scope.from_beneficiary_list = response;
                    });
                // close the modal
                $scope.closeToBeneficiaryModal();

            })
            .error(function (response, status, headers, config) {
                if (status == 401) {
                    if (response.hasOwnProperty("internal_code")) {
                        if (response.internal_code == 502) {
                            $scope.to_ben_error = true;
                            $scope.errorToBenMessages = response.messages;
                            console.log($scope.errorToBenMessages);
                        }
                        if (response.internal_code == 501) {
                            $scope.to_ben_error = true;
                            $scope.errorToBenMessages = response.message;
                            alert(response.message);
                            console.log($scope.errorToBenMessages);
                        }
                    } else {
                        alert("You got an error ! please reach out system admin to solve it!");
                    }
                }

                console.log(response);

            });

    }
    // adding a node
    $scope.sendFromBeneficiaryShipment = function () {

        $scope.fromBeneficiary.receive_grades = $scope.grade_selected_receive_ben;
        $scope.fromBeneficiary.node_id = $scope.node_id;
        $http.post(constants.API_URL + "ACRbookdis/shipment/send/from_beneficiary", $scope.fromBeneficiary)
            .success(function (response) {
                console.log(response);
                $scope.to_beneficiary_list = {};
                $scope.from_beneficiary_list = {};
                //$scope.reloadNodes();
                $http.get(constants.API_URL + "ACRbookdis/record/balance/list")
                    .success(function (response) {
                        $scope.balance_list = response;
                    });
                $http.get(constants.API_URL + "ACRbookdis/record/to_benefici/list")
                    .success(function (response) {
                        $scope.to_beneficiary_list = response;
                    });

                $http.get(constants.API_URL + "ACRbookdis/record/from_benefici/list")
                    .success(function (response) {
                        $scope.from_beneficiary_list = response;
                    });
                // close the modal
                $scope.closeFromBeneficiaryModal();

            })
            .error(function (response, status, headers, config) {
                if (status == 401) {
                    if (response.hasOwnProperty("internal_code")) {
                        if (response.internal_code == 502) {
                            $scope.to_ben_error = true;
                            $scope.errorToBenMessages = response.messages;
                            console.log($scope.errorToBenMessages);
                        }

                    } else {
                        alert("response.message");
                    }
                }

                console.log(response);

            });

    }

    // adding a node
    $scope.submitReceipt = function () {


        $http.post(constants.API_URL + "ACRbookdis/receipt/submit", $scope.recharge)
            .success(function (response) {
                console.log(response);

                //$scope.reloadNodes();

                $http.get(constants.API_URL + "ACRbookdis/record/balance/list")
                    .success(function (response) {
                        $scope.balance_list = response;
                    });

                if ($scope.is_parent == 1) {

                    $scope.receipt_list = {};
                    $http.get(constants.API_URL + "ACRbookdis/record/receipt/list")
                        .success(function (response) {
                            $scope.receipt_list = response;
                        });
                }
                // close the modal
                $scope.closeRechargeModal();

            })
            .error(function (response, status, headers, config) {
                if (status == 401) {
                    if (response.hasOwnProperty("internal_code")) {
                        if (response.internal_code == 501) {
                            alert(response.message);
                        }
                    } else {
                        alert("response.message");
                    }
                }

                console.log(response);

            });

    }
    $scope.checkBoxchanged = function (grade_id, status_check_box) {
        $scope.grade_selected[grade_id] = status_check_box;
        console.log("grade_changed");
        console.log($scope.grade_selected);
        console.log("grade_changed");
    }


    $scope.checkBoxchanged_send = function (grade_id, status_check_box) {
        $scope.grade_selected_send[grade_id] = status_check_box;
        console.log("grade_changed Send");
        console.log($scope.grade_selected_send);
        console.log("grade_changed Send");
    }
    $scope.checkBoxchanged_send_ben = function (grade_id, status_check_box) {
        $scope.grade_selected_send_ben[grade_id] = status_check_box;
        console.log("grade_changed Send Ben");
        console.log($scope.grade_selected_send_ben);
        console.log("grade_changed Send Ben");
    }
    $scope.checkBoxchanged_receive_ben = function (grade_id, status_check_box) {
        $scope.grade_selected_receive_ben[grade_id] = status_check_box;
        console.log("grade_changed Receive Ben");
        console.log($scope.grade_selected_send_ben);
        console.log("grade_changed Receive Ben");
    }


    // adding a node
    $scope.submitReceive = function () {

        $scope.errorReceiveMessages = {};
        $scope.receive.recieve_id = $scope.shipment_receive.id;
        $scope.receive.received_grades = $scope.grade_selected;

        console.log("START");
        console.log($scope.receive);
        console.log("END");

        if (!$scope.frmRecieve.$invalid) {
            $http.post(constants.API_URL + "ACRbookdis/receive/submit", $scope.receive)
                .success(function (response) {
                    console.log(response);

                    $scope.successRecieveMessageShow = true;
                    $scope.successRecieveMessage = response.message;
                    alert(response.message);

                    $http.get(constants.API_URL + "ACRbookdis/record/balance/list")
                        .success(function (response) {
                            $scope.balance_list = response;
                        });
                    $http.get(constants.API_URL + "ACRbookdis/record/received/list", { params: { page: $scope.recieveCurrentPage } })
                        .success(function (response) {
                            $scope.receive_list = response.data;
                            $scope.recieveCurrentPage = response.current_page;
                            $scope.recieveLastPage = response.last_page;

                        });

                    if ($scope.has_group == 1) {
                        $http.get(constants.API_URL + "ACRbookdis/record/received/" + $scope.node_receive + "/list")
                            .success(function (response) {
                                $scope.group_receive_list = response.data;
                            });
                    }

                    $timeout(function () {
                        // close the modal
                        $scope.closeReceiveModal();
                        $scope.successRecieveMessage = {};
                        $scope.successRecieveMessageShow = false;
                    }, 2000);


                })
                .error(function (response, status, headers, config) {
                    if (status == 401) {
                        if (response.hasOwnProperty("internal_code")) {
                            if (response.internal_code == 502) {
                                $scope.receive_error = true;
                                //$scope.errorReceiveMessages = response.messages;
                                angular.forEach(response.messages, function (value, key) {
                                    $scope.errorReceiveMessages[key] = value;
                                });
                                console.log("error messages");
                                console.log($scope.errorReceiveMessages);
                                console.log("End error messages");
                            }
                        } else {
                            alert("response.message");
                        }
                    }

                });
        }
    }


    // display the modal form
    $scope.controlTable = function (id) {
        var name = "controltable_" + id;
        //alert(name);
        alert($scope[name]);
    }

    $scope.setSelectedTab = function (tabId) {

        $scope.animation = "scale-fade";
        $timeout(function () {
            $scope.selectedTab = tabId;
        }, 1);

    }
    $scope.setSelectedTab_send = function (tabId, index) {


        if ($scope.has_index(index) == false) {
            $scope.grade_list[index] = $scope.branch_grade_list[index];
        }

        $scope.animation = "scale-fade";
        $timeout(function () {
            $scope.selectedTab = tabId;
        }, 1);

    }


    $scope.has_index = function (index) {

        var result = false;
        angular.forEach($scope.grade_list, function (value, key) {
            if (key == index) {
                result = true;
            }
        });
        return result;
    }

    $scope.loadMoreSent = function () {

        $scope.sentCurrentPage++;
        $http.get(constants.API_URL + "ACRbookdis/record/sent/list", { params: { page: $scope.sentCurrentPage } })
            .success(function (response) {
                angular.forEach(response.data, function (value, key) {
                    $scope.sent_list = $scope.sent_list.concat(value);
                });

                $scope.sentCurrentPage = response.current_page;
                $scope.sentLastPage = response.last_page;

                if ($scope.sentCurrentPage >= $scope.sentLastPage) {
                    $scope.loadMoreSent = 'All sent shipments Loaded!';
                }
            });
    }
    $scope.loadMoreRecieve = function () {

        $scope.recieveCurrentPage++;
        $http.get(constants.API_URL + "ACRbookdis/record/received/list", { params: { page: $scope.recieveCurrentPage } })
            .success(function (response) {

                angular.forEach(response.data, function (value, key) {
                    $scope.receive_list = $scope.receive_list.concat(value);
                });
                $scope.recieveCurrentPage = response.current_page;
                $scope.recieveLastPage = response.last_page;

            });
    }
    $scope.setSelectedTabReceiveSend = function (tabId) {

        $scope.animation = "scale-fade";
        $timeout(function () {
            $scope.selectedTabReceiveSend = tabId;
        }, 1);

    }
    $scope.setSelectedTabReceiveSend_clean = function (tabId, index) {

        if ($scope.has_index(index) == false) {
            $scope.grade_list[index] = $scope.branch_grade_list[index];
        }

        $scope.animation = "scale-fade";
        $timeout(function () {
            $scope.selectedTabReceiveSend = tabId;
        }, 1);

    }
    $scope.setSelectedTabDetail = function (tabId) {

        $scope.animation = "scale-fade";
        $timeout(function () {
            $scope.selectedTabDetail = tabId;
        }, 1);

    }

    $scope.setSelectedTabDoc = function (tabId) {

        $scope.animation = "scale-fade";
        $timeout(function () {
            $scope.selectedTabSentDoc = tabId;
        }, 1);

    }
    $scope.setSelectedTabReceive = function (tabId) {

        $scope.animation = "scale-fade";
        $timeout(function () {
            $scope.selectedTabReceive = tabId;
        }, 1);

    }
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
        $scope.node_id = nodeId;

        $http.get(constants.API_URL + "ACRbookdis/record/received/" + nodeId + "/list")
            .success(function (response) {
                angular.forEach(response, function (value, key) {
                    $scope.group_receive_list = $scope.group_receive_list.concat(value);
                });
            });


        $http.get(constants.API_URL + "ACRbookdis/record/to_benefici/" + nodeId + "/list")
            .success(function (response) {
                $scope.to_beneficiary_list = response;
            });

        $http.get(constants.API_URL + "ACRbookdis/record/from_benefici/" + nodeId + "/list")
            .success(function (response) {
                $scope.from_beneficiary_list = response;
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



    $scope.uploadSentDocument = function (file, shipment_id) {

        $scope.sent_doc.shipment_id = shipment_id;
        file.upload = Upload.upload({
            url: constants.API_URL + "ACRbookdis/shipment/document/store",
            data: { sent_doc: $scope.sent_doc, file: file },
        });

        file.upload.then(function (response) {
            $timeout(function () {
                $scope.sentUploadPageClose();
            });
        }, function (response) {
            if (response.status == 200) {

                alert(response.message);
                $scope.recieveUploadPageClose();

            } else if (response.status == 401) {
                alert(response.message);
            }
        }, function (evt) {
            // Math.min is to fix IE which reports 200% sometimes
            file.progress = Math.min(100, parseInt(100.0 * evt.loaded / evt.total));
        });

    }


    $scope.uploadRecieveDocument = function (file, shipment_id) {


        $scope.recieve_doc.shipment_id = shipment_id;
        file.upload = Upload.upload({
            url: constants.API_URL + "ACRbookdis/shipment/document/rec/store",
            data: { recieve_doc: $scope.recieve_doc, file: file },
        });

        file.upload.then(function (response) {
            $timeout(function () {
                $scope.recieveUploadPageClose();
            });
        }, function (response) {
            if (response.status == 200) {

                alert(response.message);
                $scope.recieveUploadPageClose();

            } else if (response.status == 401) {
                alert(response.message);
            }

        }, function (evt) {
            // Math.min is to fix IE which reports 200% sometimes
            file.progress = Math.min(100, parseInt(100.0 * evt.loaded / evt.total));
        });

    }




    // display the modal form
    $scope.openSendModal = function () {
        $('#sendModal').modal('show');
    }
    $scope.closeSendModal = function () {
        $('#sendModal').modal('hide');
    }

    // display the modal form
    $scope.sentUploadPage = function (shipment_id) {
        $scope.sent_shipment_id_upload = shipment_id;
        $('#sendUploadModal').modal('show');
    }
    // display the modal form
    $scope.sentUploadPageClose = function () {
        $('#sendUploadModal').modal('hide');
    }


    // display the modal form
    $scope.recieveUploadPage = function (shipment_id) {
        $scope.sent_shipment_id_upload = shipment_id;
        $('#showReceiveDetailModal').modal('hide');
        $('#recieveUploadModal').modal('show');
    }
    // display the modal form
    $scope.recieveUploadPageClose = function () {
        $('#recieveUploadModal').modal('hide');
    }


    // display the modal form
    $scope.closeSendModal = function () {
        $('#sendModal').modal('hide');
    }
    // display the modal form
    $scope.closeToBeneficiaryModal = function () {
        $('#toBeneficiaryModal').modal('hide');
    }
    // display the modal form
    $scope.closeFromBeneficiaryModal = function () {
        $('#fromBeneficiaryModal').modal('hide');
    }
    // display the modal form
    $scope.openRechargeModal = function () {
        $('#rechargeModal').modal('show');
    }
    // display the modal form
    $scope.closeRechargeModal = function () {
        $('#rechargeModal').modal('hide');
    }
    $scope.closeReceiveModal = function () {
        $('#recieveModal').modal('hide');
    }

});







BookDistribution.directive('focusMe', ['$timeout', '$parse', function ($timeout, $parse) {
    return {
        //scope: true,   // optionally create a child scope
        link: function (scope, element, attrs) {
            var model = $parse(attrs.focusMe);
            scope.$watch(model, function (value) {
                console.log('value=', value);
                if (value === true) {
                    $timeout(function () {
                        element[0].focus();
                    });
                }
            });
            // to address @blesh's comment, set attribute value to 'false'
            // on blur event:
            element.bind('blur', function () {
                console.log('blur');
                scope.$apply(model.assign(scope, false));
            });
        }
    };
}]);



