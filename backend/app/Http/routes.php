<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
 */

Route::get('/', function () {
    return view('welcome');
});

Route::get('/language/{locale}', function ($locale) {
    App::setLocale($locale);
    Session::put('locale', $locale);

    return Redirect::back();

});

Route::get('/language', function () {
    return Session::get('locale');
});

/*
 * start of Auth Routes
 * */
//Route::auth();
Route::get('login', ['as' => 'auth.login', 'uses' => 'Auth\AuthController@showLoginForm']);
Route::post('login', ['as' => 'auth.login', 'uses' => 'Auth\AuthController@login']);
Route::get('logout', ['as' => 'auth.logout', 'uses' => 'Auth\AuthController@logout']);

// Registration Routes...
Route::get('register', ['as' => 'auth.register', 'uses' => 'Auth\AuthController@showRegistrationForm']);
Route::post('register', ['as' => 'auth.register', 'uses' => 'Auth\AuthController@register']);

// Password Reset Routes...
Route::get('password/reset/{token?}', ['as' => 'auth.password.reset', 'uses' => 'Auth\PasswordController@showResetForm']);
Route::post('password/email', ['as' => 'auth.password.email', 'uses' => 'Auth\PasswordController@sendResetLinkEmail']);
Route::post('password/reset', ['as' => 'auth.password.reset', 'uses' => 'Auth\PasswordController@reset']);
/*
 * end of Auth Routes
 * */
Route::get('/home', 'HomeController@index');

/* routes for inventory management system
 *  12 - Feb - 2018
 *
 */
Route::get('ACRbookdis/admin', ['uses' => 'inventory\BookController@admin_index']);
Route::get('ACRbookdis/admin/level', ['uses' => 'inventory\BookController@admin_level_index']);
Route::get('ACRbookdis/admin/level/list', ['uses' => 'inventory\BookController@admin_level_list']);
Route::get('ACRbookdis/admin/level/list/all', ['uses' => 'inventory\BookController@admin_level_list_all']);
Route::post('ACRbookdis/admin/level/store', ['uses' => 'inventory\BookController@admin_level_store']);
Route::get('ACRbookdis/admin/node', ['uses' => 'inventory\BookController@admin_node_index']);
Route::get('ACRbookdis/admin/node/{node_id}/id', ['uses' => 'inventory\BookController@admin_get_node']);
Route::get('ACRbookdis/admin/node/{node_id}/users', ['uses' => 'inventory\BookController@admin_get_node_users']);
Route::post('ACRbookdis/admin/node/search/users', ['uses' => 'inventory\BookController@admin_search_node_users']);
Route::get('ACRbookdis/admin/node/{node_id}/attach/{user_id}/user', ['uses' => 'inventory\BookController@admin_attach_node_user']);
Route::get('ACRbookdis/admin/node/list', ['uses' => 'inventory\BookController@admin_node_list']);
Route::get('ACRbookdis/level/{level_id}/list/node', ['uses' => 'inventory\BookController@adminlevel_list_nodes']);
Route::post('ACRbookdis/admin/node/store', ['uses' => 'inventory\BookController@admin_node_store']);

Route::get('ACRbookdis/admin/report', ['uses' => 'inventory\BookController@admin_report_index']);

Route::post('ACRbookdis/shipment/send', ['uses' => 'inventory\BookController@send_shipment']);
Route::post('ACRbookdis/shipment/send/to_beneficiary', ['uses' => 'inventory\BookController@send_toBeneficiary_shipment']);
Route::post('ACRbookdis/shipment/send/from_beneficiary', ['uses' => 'inventory\BookController@send_fromBeneficiary_shipment']);
Route::post('ACRbookdis/receipt/submit', ['uses' => 'inventory\BookController@submit_receipt']);
Route::post('ACRbookdis/receive/submit', ['uses' => 'inventory\BookController@submit_receive']);

Route::get('ACRbookdis/child/{level_id}/{province_id}/{district_id}/node', ['uses' => 'inventory\BookController@record_child_node']);
Route::get('ACRbookdis/report/{node_id}/send/search', ['uses' => 'inventory\BookController@send_report_search']);
Route::get('ACRbookdis/report/{node_id}/receive/search', ['uses' => 'inventory\BookController@receive_report_search']);

Route::get('ACRbookdis/record', ['uses' => 'inventory\BookController@record_index']);
Route::get('ACRbookdis/record/sent/list', ['uses' => 'inventory\BookController@sent_list']);
Route::get('ACRbookdis/record/sent/all_list', ['uses' => 'inventory\BookController@sent_list_all']);
Route::get('ACRbookdis/record/balance/list', ['uses' => 'inventory\BookController@balance_list']);
Route::get('ACRbookdis/record/balance/detail/list', ['uses' => 'inventory\BookController@balance_d_list']);
Route::get('ACRbookdis/record/group/node/list', ['uses' => 'inventory\BookController@group_node_list']);
Route::get('ACRbookdis/record/receipt/list', ['uses' => 'inventory\BookController@receipt_list']);
Route::get('ACRbookdis/record/to_benefici/list', ['uses' => 'inventory\BookController@to_benefic_list']);
Route::get('ACRbookdis/record/to_benefici/{node_id}/list', ['uses' => 'inventory\BookController@to_benefic_list_id']);
Route::get('ACRbookdis/record/from_benefici/list', ['uses' => 'inventory\BookController@from_benefic_list']);
Route::get('ACRbookdis/record/from_benefici/{node_id}/list', ['uses' => 'inventory\BookController@from_benefic_list_id']);
Route::get('ACRbookdis/record/benefici/node_id', ['uses' => 'inventory\BookController@benefic_node_id']);
Route::get('ACRbookdis/record/received/list', ['uses' => 'inventory\BookController@receive_list']);
Route::get('ACRbookdis/record/received/{node_id}/list', ['uses' => 'inventory\BookController@node_receive_list']);
Route::get('ACRbookdis/record/sent/{record_id}/get', ['uses' => 'inventory\BookController@sent_record']);
Route::get('ACRbookdis/record/sent/{record_id}/get/group', ['uses' => 'inventory\BookController@sent_record_group']);
Route::get('ACRbookdis/record/sent/{shipment_id}/shipment/get', ['uses' => 'inventory\BookController@sent_shipment']);
Route::get('ACRbookdis/record/receive/{record_id}/get', ['uses' => 'inventory\BookController@receive_record']);
Route::get('ACRbookdis/record/receive/{record_id}/get/group', ['uses' => 'inventory\BookController@receive_record_group']);
Route::get('ACRbookdis/record/receive/{shipment_id}/shipment/get', ['uses' => 'inventory\BookController@receive_shipment']);
Route::get('ACRbookdis/record/shipment/{shipment_id}/receive/get', ['uses' => 'inventory\BookController@shipment_receive']);
Route::get('ACRbookdis/record/sent/{shipment_id}/shipment/receive/get', ['uses' => 'inventory\BookController@sent_recieve_shipment']);

Route::get('ACRbookdis/province/list/all', ['uses' => 'inventory\BookController@province_list_all']);
Route::get('ACRbookdis/district/{prov_id}/list/all', ['uses' => 'inventory\BookController@district_list_province']);
Route::get('ACRbookdis/grade/list', ['uses' => 'inventory\BookController@grade_list']);
Route::get('ACRbookdis/vendor/list', ['uses' => 'inventory\BookController@vendor_list']);
Route::get('ACRbookdis/grade/{grade_id}/title', ['uses' => 'inventory\BookController@grade_title_list']);
Route::get('ACRbookdis/language/list', ['uses' => 'inventory\BookController@language_list']);
Route::get('ACRbookdis/language/full/list', ['uses' => 'inventory\BookController@language_full_list']);

Route::get('ACRbookdis/receipt/level/list', ['uses' => 'inventory\BookController@receipt_level_list']);
Route::get('ACRbookdis/admin/report/types', ['uses' => 'inventory\BookController@admin_report_type']);
Route::get('ACRbookdis/shipment/documents/{shipment_id}/get', ['uses' => 'inventory\BookController@shipment_documents']);
Route::get('ACRbookdis/shipment/documents/{file_id}/download', ['uses' => 'inventory\BookController@DownloadShipmentFile']);

Route::get('ACRbookdis/dashboard', ['uses' => 'inventory\BookController@dashboard_index']);
Route::get('ACRbookdis/record/sent/{node_id}/list', ['uses' => 'inventory\BookController@sent_node_list']);
Route::get('ACRbookdis/record/received/{node_id}/list', ['uses' => 'inventory\BookController@receive_node_list']);

//Province-wise Textbooks Reciept Report
Route::post('ACRbookdis/report/1/search', ['uses' => 'inventory\BookController@report_province_wise_search']);
Route::post('ACRbookdis/report/2/search', ['uses' => 'inventory\BookController@report_province_wise_sent_search']);
Route::post('ACRbookdis/report/3/search', ['uses' => 'inventory\BookController@report_district_wise_search']);
Route::post('ACRbookdis/report/4/search', ['uses' => 'inventory\BookController@report_central_wise_search']);
Route::post('ACRbookdis/report/5/search', ['uses' => 'inventory\BookController@report_school_district_wise_search']);

Route::post('ACRbookdis/shipment/document/store', ['uses' => 'inventory\BookController@shipment_doc_upload']);
Route::post('ACRbookdis/shipment/document/rec/store', ['uses' => 'inventory\BookController@rec_shipment_doc_upload']);

Route::get('book', ['uses' => 'inventory\BookController@index']);
Route::get('book/list', ['uses' => 'inventory\BookController@BookList']);

Route::resource('api/todos', 'inventory\BookController');
Route::get('ahmad', ['uses' => 'inventory\BookController@test']);
Route::get('inventory/index', ['uses' => 'inventory\BookController@list_pro']);
