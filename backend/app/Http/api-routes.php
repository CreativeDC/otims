<?php

// Created by Zabeeh
// API Routes

// login route, no token required
Route::post('/mobile/login', 'MobileController@login');

Route::group(['middleware' => ['auth:api']], function () {

    // test route
    Route::get('/mobile/test', function () {
        return "works fine";
    });

    // start user routes
    Route::get('/mobile/user', function () {
        return auth()->guard('api')->user();
    });

    Route::get('/mobile/user/roles', 'MobileController@getUserRoles');

    Route::get('/mobile/user/node', 'MobileController@getUserNode');

    Route::get('/mobile/user/nodeandparent', 'MobileController@getUserAndParentNodes');

    // end user routes

    // start shipment list routes
    Route::get('/mobile/shipments/sent', 'MobileController@getSentShipmentsByPage');
    Route::get('/mobile/shipments/sent/all', 'MobileController@getAllSentShipments');
    Route::get('/mobile/shipments/received', 'MobileController@getReceivedShipmentsByPage');

    Route::get('/mobile/shipments/received/pending', 'MobileController@getPendingReceivedShipments');
    // end shipment list routes

    // start meta / lookup routes
    Route::get('/mobile/meta/grades', 'MobileController@getGrades');
    Route::get('/mobile/meta/levels', 'MobileController@getLevels');
    Route::get('/mobile/meta/provinces', 'MobileController@getProvinces');
    Route::get('/mobile/meta/projects', 'MobileController@getProjects');

    Route::get('/mobile/meta/provinces/{provinceId}/districts', 'MobileController@getDistricts');
    Route::get('/mobile/meta/levels/{levelId}/{provinceId}/{districtId}/nodes', 'MobileController@getLevelChildNode');

    Route::get('/mobile/meta/languages/first', 'MobileController@getFirstLanguages');
    Route::get('/mobile/meta/languages/third', 'MobileController@getThirdLanguages');
    // end meta / lookup routes

    // start home page routes
    Route::get('/mobile/transactions/recent', 'MobileController@getRecentTransactions');
    Route::get('/mobile/balance/list', 'MobileController@getBalanceList');
    // end home page routes

    // start shipment routes
    Route::get('/mobile/shipments/sent/{shipmentId}/sent', 'MobileController@getSentPackageSentDetails');
    Route::get('/mobile/shipments/sent/{shipmentId}/all', 'MobileController@getSentPackageAllDetails');

    Route::get('/mobile/shipments/received/{shipmentId}/sent', 'MobileController@getReceivedPackageSentDetails');
    Route::get('/mobile/shipments/received/{shipmentId}/all', 'MobileController@getReceivedPackageAllDetails');

    Route::get('/mobile/records/receive/{id}', 'MobileController@getReceiveRecord');
    Route::get('/mobile/records/shipment/{id}', 'MobileController@getShipmentRecord');

    Route::post('/mobile/shipments/send', 'MobileController@sendShipment');
    Route::post('/mobile/shipments/receive', 'MobileController@receiveShipment');
    // end shipment routes

    // start distribution to students routes
    Route::get('/mobile/user/group/nodes', 'MobileController@getGroupNodes');
    Route::get('/mobile/shipments/{node_id}/sent/all', ['uses' => 'MobileController@getNodeAllSentShipments']);
    Route::get('/mobile/shipments/{node_id}/received/all', ['uses' => 'MobileController@getNodeAllReceivedShipments']);

    Route::get('/mobile/user/beneficiary/node', ['uses' => 'MobileController@getUserBeneficiaryNodeId']);

    Route::get('/mobile/user/beneficiary/sent', ['uses' => 'MobileController@getUserBeneficiarySentShipments']);
    Route::get('/mobile/user/beneficiary/received', ['uses' => 'MobileController@getUserBeneficiaryReceivedShipments']);

    Route::get('/mobile/node/{nodeId}/beneficiary/sent', ['uses' => 'MobileController@getNodeBeneficiarySentShipments']);
    Route::get('/mobile/node/{nodeId}/beneficiary/received', ['uses' => 'MobileController@getNodeBeneficiaryReceivedShipments']);

    Route::post('/mobile/beneficiary/send', ['uses' => 'MobileController@sendShipmentToBeneficiary']);
    Route::post('/mobile/beneficiary/receive', ['uses' => 'MobileController@receiveShipmentFromBeneficiary']);
    // end distribution to students routes

    // start documents routes
    Route::get('/mobile/shipments/{shipmentId}/documents', ['uses' => 'MobileController@getShipmentDocuments']);
    Route::get('/mobile/shipments/documents/{fileId}', ['uses' => 'MobileController@downloadShipmentDocument']);

    Route::post('/mobile/shipments/documents/sent/store', ['uses' => 'MobileController@uploadSentDocument']);
    Route::post('/mobile/shipments/documents/receive/store', ['uses' => 'MobileController@uploadReceivedDocument']);
    // end documents routes

    // start admin panel routes
    Route::get('/mobile/admin/shipments/{recvId}/clear', ['uses' => 'MobileController@clearReceiveRecords']);
    Route::get('/mobile/admin/shipments/{shipmentId}/delete', ['uses' => 'MobileController@deleteShipment']);
    Route::get('/mobile/admin/node/{nodeId}/balance', 'MobileController@getNodeBalanceList');

    Route::post('/mobile/admin/shipments/general/update', 'MobileController@updateShipmentGeneralInfo');
    Route::post('/mobile/admin/shipments/recipient/update', 'MobileController@updateShipmentRecipient');

    Route::post('/mobile/admin/balance/record/delete', 'MobileController@deleteBalanceRecord');
    Route::post('/mobile/admin/balance/record/update', 'MobileController@updateBalanceRecord');

    Route::post('/mobile/admin/nodes/create', 'MobileController@createNode');

    Route::get('/mobile/admin/nodes/{provinceId}/{districtId}/schools', 'MobileController@getAllSchoolsForDistrict');
    Route::get('/mobile/admin/nodes/{provinceId}/{districtId}/users', 'MobileController@getAllUsersForDistrict');
    Route::get('/mobile/admin/nodes/{userId}/schools', 'MobileController@getAssignedSchoolsForDistrictUser');

    Route::post('/mobile/admin/nodes/user/schools/assign', 'MobileController@assignSchoolsToDistrictUser');
    Route::post('/mobile/admin/nodes/user/schools/unassign', 'MobileController@unassignSchoolsFromDistrictUser');

    Route::get('/mobile/admin/nodes/{nodeId}/users', 'MobileController@getAllUsersForNode');
    Route::get('/mobile/admin/nodes/unassign/{nodeId}/{userId}', 'MobileController@unassignNodeFromUser');
    Route::get('/mobile/admin/users/search/{searchTerm}', 'MobileController@searchUsersByTerm');
    Route::get('/mobile/admin/nodes/assign/{nodeId}/{userId}', 'MobileController@assignNodeToUser');

    Route::get('/mobile/admin/users/{userId}/nodes', 'MobileController@getAllNodesForUser');
    Route::get('/mobile/admin/users/{userId}/nodes/schools', 'MobileController@getAllSchoolNodesForUser');

    Route::get('/mobile/admin/users/{userId}/roles', 'MobileController@getRolesForUser');
    Route::post('/mobile/admin/users/password', 'MobileController@changePasswordForUser');
    // end admin panel routes


    // start integration routes
    Route::post('/mobile/admin/sync/schools', 'MobileController@syncDistrictSchools');
    Route::post('/mobile/admin/sync/provinces', 'MobileController@syncProvinces');
    Route::post('/mobile/admin/sync/districts', 'MobileController@syncDistricts');
    // end integration routes


    // reporting module routes

    Route::post('/mobile/reports/students/province/titles', ['uses' => 'MobileController@getStudentDistByTitles']);
    Route::post('/mobile/reports/students/province/grades', ['uses' => 'MobileController@getStudentDistByGrades']);
    Route::post('/mobile/reports/students/province/districts', ['uses' => 'MobileController@getStudentDistByDistricts']);
    Route::post('/mobile/reports/students/province/languages', ['uses' => 'MobileController@getStudentDistByLanguages']);
    Route::post('/mobile/reports/province/stats', ['uses' => 'MobileController@getProvinceStats']);

    Route::get('/mobile/reports/students/{provinceId}/excel', ['uses' => 'MobileController@studentDistExportExcel']);
    Route::get('/mobile/reports/sent/{provinceId}/excel', ['uses' => 'MobileController@sentReceivedExportExcel']);

    Route::post('/mobile/reports/provinces/tabular', ['uses' => 'MobileController@getAllProvincesTabularRecords']);
    Route::post('/mobile/reports/province/tabular', ['uses' => 'MobileController@getProvinceTabularRecords']);
    Route::post('/mobile/reports/province/districts/tabular', ['uses' => 'MobileController@getProvinceDistrictsTabularRecords']);
    Route::post('/mobile/reports/province/district/tabular', ['uses' => 'MobileController@getSelectedDistrictTabularRecords']);
    Route::post('/mobile/reports/province/district/schools/tabular', ['uses' => 'MobileController@getDistrictSchoolsTabularRecords']);

    // reporting module routes

    // requisition module routes

    Route::get('/mobile/requests/sent', 'MobileController@getSentRequestsByPage');
    Route::get('/mobile/requests/received', 'MobileController@getReceivedRequestsByPage');
    Route::get('/mobile/requests/received/approved', 'MobileController@getApprovedReceivedRequests');
    Route::post('/mobile/requests/send', 'MobileController@sendRequest');
    Route::get('/mobile/requests/{requestId}', 'MobileController@getRequestDetails');

    Route::post('/mobile/requests/approve', 'MobileController@approveRequest');

    Route::get('/mobile/requests/{requestId}/documents', ['uses' => 'MobileController@getRequestDocuments']);
    Route::get('/mobile/requests/documents/{fileId}', ['uses' => 'MobileController@downloadRequestDocument']);
    Route::post('/mobile/requests/documents/store', ['uses' => 'MobileController@uploadRequestDocument']);

    // requisition module routes

});
