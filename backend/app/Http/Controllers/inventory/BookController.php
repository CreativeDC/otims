<?php

namespace App\Http\Controllers\inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Auth;
use Config,Response;
use App\inventory_RM\BookDisRM;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;


use App\Models\book_dis\book_dis_node_level;
use App\Models\book_dis\book_dis_node;
use App\Models\book_dis\province;
use App\Models\book_dis\district;
use App\Models\book_dis\book_dis_shipment_file;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('inventory.index');
    }

    /**
     * Display a setting page for admin of book sys.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin_index()
    {
        if(Auth::user() && Auth::user()->canRead('book_dis_admin')){
            return view('inventory.admin.index');
        }
        return response(view('layouts.401'), 401);

    }
    /**
     * Display a setting page for admin of book sys.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin_report_index()
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){
            $is_parent = false;
            $has_parent = false;
            $has_child = false;
            $has_beneficiary = false;
            //this is the last commmit
            $user = Auth::user();
            if(BookDisRM::hasNode($user) == 1){
                $is_parent = BookDisRM::isFromRootLevel($user);
                $has_parent = BookDisRM::hasParent($user);
                $has_child = BookDisRM::hasChild($user);
                $has_beneficiary = BookDisRM::hasBeneficiary($user);

                return view('inventory.admin.report.index', compact("is_parent" ,$is_parent , "has_parent" , $has_parent , "has_child" , $has_child , "has_beneficiary" , $has_beneficiary));
            }else{
                return response(view('layouts.401'), 401);
            }
        }
        return response(view('layouts.401'), 401);
    }
    /**
     * Display a setting page for admin of book sys.
     *
     * @return \Illuminate\Http\Response
     */
    public function record_index()
    {

        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){


            $is_parent = false;
            $has_parent = false;
            $has_child = false;
            $has_beneficiary = false;
            //this is the last commmit
            $user = Auth::user();

            if(BookDisRM::hasNode($user) == 1){
                $is_parent = BookDisRM::isFromRootLevel($user);
                $has_parent = BookDisRM::hasParent($user);
                $has_group = BookDisRM::hasGroup($user);
                $has_child = BookDisRM::hasChild($user);
                $has_beneficiary = BookDisRM::hasBeneficiary($user);
                return view('inventory.dataentry.index', compact("is_parent" ,$is_parent , "has_parent" , $has_parent , "has_child" , $has_child, "has_beneficiary" , $has_beneficiary, "has_group" , $has_group));
            }else{
                return response(view('layouts.401'), 401);
            }
        }
    }

    /**
     * Display a setting page for admin of book sys.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin_level_index()
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){
            return view('inventory.admin.level.index');
        }
        return response(view('layouts.401'), 401);
    }
    /**
     * Display a setting page for admin of book sys.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin_level_list()
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){
            return book_dis_node_level::paginate();
        }
        return response(view('layouts.401'), 401);
    }
    /**
     * Display a setting page for admin of book sys.
     *
     * @return \Illuminate\Http\Response
     */
    public function receipt_level_list()
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){

            return BookDisRM::receipt_level_list();
        }
        return response(view('layouts.401'), 401);
    }
    /**
     * Display a setting page for admin of book sys.
     *
     * @return \Illuminate\Http\Response
     */
    public function sent_list()
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){
            if(request()->has("page")){
                if(request()->page != 0 ){
                    return BookDisRM::sentShipmentList(request()->page , request()->url, Auth::user()->id);
                }else{
                    return response()->json(['message' => 'something went wrong! here '],401);
                }
            }else{
                return response()->json(['message' => 'something went wrong! here '],401);
            }
        }
        return response(view('layouts.401'), 401);
    }
    /**
     * Display a setting page for admin of book sys.
     *
     * @return \Illuminate\Http\Response
     */
    public function sent_node_list($node_id)
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){
            return BookDisRM::sentNodeShipmentList($node_id, request()->url, Auth::user()->id);
        }
        return response(view('layouts.401'), 401);
    }

    public function sent_list_all()
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){
            return BookDisRM::sentShipmentList_all( Auth::user()->id);
        }
        return response(view('layouts.401'), 401);
    }
    /**
     * get back list of district.
     *
     * @return \Illuminate\Http\Response
     */
    public function balance_list()
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){
            $DB_balance_result = BookDisRM::getBalances(Auth::user());
            if($DB_balance_result != null){
                return $DB_balance_result;
            }else{
                return array();
            }
        }else{
            return response()->json(['message' => "You don't have permission "],401);
        }

    }

    /**
     * get back list of district.
     *
     * @return \Illuminate\Http\Response
     */
    public function balance_d_list()
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){
            $DB_balance_result = BookDisRM::getBalancesDetail(Auth::user());
            if($DB_balance_result != null){
                return $DB_balance_result;
            }else{
                return array();
            }
        }else{
            return response()->json(['message' => "You don't have permission "],401);
        }

    }

    /**
     * get back list of district.
     *
     * @return \Illuminate\Http\Response
     */
    public function group_node_list()
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){
            $DB_balance_result = BookDisRM::getGroupNode(Auth::user());
            if($DB_balance_result != null){
                return $DB_balance_result;
            }else{
                return array();
            }
        }else{
            return response()->json(['message' => "You don't have permission "],401);
        }

    }
    /**
     * get back list of district.
     *
     * @return \Illuminate\Http\Response
     */
    public function receipt_list()
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){
            $DB_balance_result = BookDisRM::getReceipts(Auth::user());
            if($DB_balance_result != null){
                return $DB_balance_result;
            }else{
                return array();
            }
        }else{
            return response()->json(['message' => "You don't have permission "],401);
        }

    }
    /**
     * get back list of district.
     *
     * @return \Illuminate\Http\Response
     */
    public function to_benefic_list()
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){
            $DB_to_beneficiary = BookDisRM::getToBenefic(Auth::user());
            if($DB_to_beneficiary != null){
                return $DB_to_beneficiary;
            }else{
                return array();
            }
        }else{
            return response()->json(['message' => "You don't have permission "],401);
        }

    }

    public function to_benefic_list_id($node_id)
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){
            $DB_to_beneficiary = BookDisRM::getToBeneficId($node_id);
            if($DB_to_beneficiary != null){
                return $DB_to_beneficiary;
            }else{
                return array();
            }
        }else{
            return response()->json(['message' => "You don't have permission "],401);
        }

    }
    /**
     * get back list of district.
     *
     * @return \Illuminate\Http\Response
     */
    public function from_benefic_list()
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){
            $DB_to_beneficiary = BookDisRM::getFromBenefic(Auth::user());
            if($DB_to_beneficiary != null){
                return $DB_to_beneficiary;
            }else{
                return array();
            }
        }else{
            return response()->json(['message' => "You don't have permission "],401);
        }

    }

    public function from_benefic_list_id($node_id)
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){
            $DB_to_beneficiary = BookDisRM::getFromBeneficId($node_id);
            if($DB_to_beneficiary != null){
                return $DB_to_beneficiary;
            }else{
                return array();
            }
        }else{
            return response()->json(['message' => "You don't have permission "],401);
        }

    }
    /**
     * get back list of district.
     *
     * @return \Illuminate\Http\Response
     */
    public function receive_list()
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){
            $DB_balance_result = BookDisRM::getReceives(request()->page , request()->url , Auth::user());
            if($DB_balance_result != null){
                return $DB_balance_result;
            }else{
                return array();
            }
        }else{
            return response()->json(['message' => "You don't have permission "],401);
        }

    }
    /**
     * get back list of district.
     *
     * @return \Illuminate\Http\Response
     */
    public function receive_node_list($node_id)
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){
            $DB_balance_result = BookDisRM::getNodeRecieveReceives($node_id , request()->url , Auth::user());
            if($DB_balance_result != null){
                return $DB_balance_result;
            }else{
                return array();
            }
        }else{
            return response()->json(['message' => "You don't have permission "],401);
        }

    }
    /**
     * get back list of district.
     *
     * @return \Illuminate\Http\Response
     */
    public function node_receive_list($node_id)
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){
            $DB_balance_result = BookDisRM::getNodeReceives(request()->page , request()->url , $node_id, Auth::user());

            if($DB_balance_result != null){
                return $DB_balance_result;
            }else{
                return array();
            }
        }else{
            return response()->json(['message' => "You don't have permission "],401);
        }

    }
    /**
     * Display a setting page for admin of book sys.
     *
     * @return \Illuminate\Http\Response
     */
    public function sent_record($record_id)
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){
            if($record_id != 0 ){
                $db_result = BookDisRM::getSentShipmentItem($record_id);

                return $db_result;
            }else{
                return response()->json(['message' => 'something went wrong! here '],401);
            }
        }
        return response(view('layouts.401'), 401);
    }
    /**
     * Display a setting page for admin of book sys.
     *
     * @return \Illuminate\Http\Response
     */
    public function shipment_documents($shipment_id)
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){
            if($shipment_id != 0 ){
                $db_result = BookDisRM::getShipmentDocuments($shipment_id);

                return $db_result;
            }else{
                return response()->json(['message' => 'something went wrong! here '],401);
            }
        }
        return response(view('layouts.401'), 401);
    }


    public function DownloadShipmentFile($file_id)
    {
        $files = book_dis_shipment_file::where('id',$file_id)->get();
        if(count($files) > 0){
            $file = $files->first();
            $path=base_path().'/'.$file->file_url;
            $file_name=explode('/', $file->file_url);
            $file_name1=strtolower(end($file_name));
            ob_end_clean();
            $headers = array(
                'Content-Type: image/png',
            );

            return Response::download($path,$file_name1 , $headers);

            // // return response(array(
            // // 'path' => $path,
            // // 'file_name' => $file_name1,
            
            // // ));


            // // return response()->json(['path' => $path], 401);

            // return response()->json(['path' => $path], 200);
        }

    }


    /**
     * Display a setting page for admin of book sys.
     *
     * @return \Illuminate\Http\Response
     */
    public function sent_record_group($record_id)
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){
            if($record_id != 0 ){
                $db_result = BookDisRM::getSentShipmentItemGroup($record_id);

                return $db_result;
            }else{
                return response()->json(['message' => 'something went wrong! here '],401);
            }
        }
        return response(view('layouts.401'), 401);
    }
    /**
     * Display a setting page for admin of book sys.
     *
     * @return \Illuminate\Http\Response
     */
    public function receive_record_group($record_id)
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){
            if($record_id != 0 ){
                $db_result = BookDisRM::getReceiveShipmentItemGroup($record_id);

                return $db_result;
            }else{
                return response()->json(['message' => 'something went wrong! here '],401);
            }
        }
        return response(view('layouts.401'), 401);
    }
    /**
     * Display a setting page for admin of book sys.
     *
     * @return \Illuminate\Http\Response
     */
    public function receive_record($record_id)
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){
            if($record_id != 0 ){
                $db_result = BookDisRM::getReceiveShipmentItem($record_id);

                return $db_result;
            }else{
                return response()->json(['message' => 'something went wrong! here '],401);
            }
        }
        return response(view('layouts.401'), 401);
    }

    /**
     * Display a setting page for admin of book sys.
     *
     * @return \Illuminate\Http\Response
     */
    public function record_child_node($level_id , $province_id , $district_id)
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){

            $db_result = BookDisRM::getChildNodes($level_id , $province_id , $district_id);
            if($db_result != null){
                return $db_result;
            }else{
                return array();
            }

            return $db_result;

        }
        return response(view('layouts.401'), 401);
    }

    /**
     * Display a setting page for admin of book sys.
     *
     * @return \Illuminate\Http\Response
     */
    public function grade_list()
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){

            $db_result = BookDisRM::getGradeList();
            if($db_result != null){
                return $db_result;
            }else{
                return array();
            }

            return $db_result;

        }
        return response(view('layouts.401'), 401);
    }
    /**
     * Display a setting page for admin of book sys.
     *
     * @return \Illuminate\Http\Response
     */
    public function vendor_list()
    {
        if(Auth::user() && (Auth::user()->canRead('data_entry_book_dis')  || Auth::user()->canRead('book_distribution_staff'))){

            $db_result = BookDisRM::getVendorList();
            if($db_result != null){
                return $db_result;
            }else{
                return array();
            }

            return $db_result;

        }
        return response(view('layouts.401'), 401);
    }

    /**
     * Display a setting page for admin of book sys.
     *
     * @return \Illuminate\Http\Response
     */
    public function grade_title_list($grade_id)
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){

            $db_result = BookDisRM::getGradeTitleList($grade_id);
            if($db_result != null){
                return $db_result;
            }else{
                return array();
            }

            return $db_result;

        }
        return response(view('layouts.401'), 401);
    }
    /**
     * Display a setting page for admin of book sys.
     *
     * @return \Illuminate\Http\Response
     */
    public function language_list()
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){

            $db_result = BookDisRM::getLanguageList();
            if($db_result != null){
                return $db_result;
            }else{
                return array();
            }

            return $db_result;

        }
        return response(view('layouts.401'), 401);
    }
    /**
     * Display a setting page for admin of book sys.
     *
     * @return \Illuminate\Http\Response
     */
    public function language_full_list()
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){

            $db_result = BookDisRM::getLanguageFullList();
            if($db_result != null){
                return $db_result;
            }else{
                return array();
            }

            return $db_result;

        }
        return response(view('layouts.401'), 401);
    }

    /**
     * Display a setting page for admin of book sys.
     *
     * @return \Illuminate\Http\Response
     */
    public function sent_shipment($shipment_id)
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){
            if($shipment_id != 0 ){
                $db_result = BookDisRM::getSentShipment($shipment_id);
                if($db_result != null){
                    return $db_result;
                }else{
                    return response()->json(['message' => 'something went wrong! here '],401);
                }
            }else{
                return response()->json(['message' => 'something went wrong! here '],401);
            }
        }
        return response(view('layouts.401'), 401);
    }

    /**
     * Display a setting page for admin of book sys.
     *
     * @return \Illuminate\Http\Response
     */
    public function sent_recieve_shipment($shipment_id)
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){
            if($shipment_id != 0 ){
                $db_result = BookDisRM::getSentRecieveShipment($shipment_id);
                if($db_result != null){
                    return $db_result;
                }else{
                    return response()->json(['message' => 'something went wrong! here '],401);
                }
            }else{
                return response()->json(['message' => 'something went wrong! here '],401);
            }
        }
        return response(view('layouts.401'), 401);
    }
    /**
     * Display a setting page for admin of book sys.
     *
     * @return \Illuminate\Http\Response
     */
    public function receive_shipment($shipment_id)
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){
            if($shipment_id != 0 ){
                $db_result = BookDisRM::getReceiveShipment($shipment_id);
                if($db_result != null){
                    return $db_result;
                }else{
                    return response()->json(['message' => 'something went wrong! here '],401);
                }
            }else{
                return response()->json(['message' => 'something went wrong! here '],401);
            }
        }
        return response(view('layouts.401'), 401);
    }
    /**
     * Display a setting page for admin of book sys.
     *
     * @return \Illuminate\Http\Response
     */
    public function shipment_receive($shipment_id)
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){
            if($shipment_id != 0 ){
                $db_result = BookDisRM::getShipmentReceive($shipment_id);
                if($db_result != null){
                    return $db_result;
                }else{
                    return response()->json(['message' => 'something went wrong! here '],401);
                }
            }else{
                return response()->json(['message' => 'something went wrong! here '],401);
            }
        }
        return response(view('layouts.401'), 401);
    }
    /**
     * get back list of level.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin_level_list_all()
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){
            return book_dis_node_level::all();
        }
        return response(view('layouts.401'), 401);
    }
    /**
     * get back list of level.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin_report_type()
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){
            return BookDisRM::getReportTypes(Config::get('ACR_Book_dis_meta.report_types'));
        }
        return response(view('layouts.401'), 401);
    }

    /**
     * get back list of district.
     *
     * @return \Illuminate\Http\Response
     */
    public function adminlevel_list_nodes($level_id)
    {
        if (Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){
            $DB_transantion_result = BookDisRM::getNodes($level_id);
            if ($DB_transantion_result != null) {
                return $DB_transantion_result;
            } else {
                return array();
            }
        } else {
            return response()->json(['message' => "You don't have permission "], 401);
        }

    }

    /**
     * Store level into database
     *
     * @return \Illuminate\Http\Response
     */
    public function admin_node_store(Request $request)
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){

            $DB_transantion_result = BookDisRM::createNode(Input::all(), Auth::user()->id);
            if($DB_transantion_result == 1){
                return response()->json(['message' => 'Node has been successfully saved'],200);
            }else{
                return response()->json(['message' => 'something went wrong! here '],401);
            }
        }else{
            return response()->json(['message' => 'You dont have the permission to access this part!'],401);
        }
    }


    /**
     * Store level into database
     *
     * @return \Illuminate\Http\Response
     */
    public function shipment_doc_upload(Request $request)
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){

            $DB_transantion_result = BookDisRM::uploadDocShipment(Input::all(), Auth::user()->id);
            if($DB_transantion_result == 1){
                return response()->json(['message' => 'Document has been uploaded successfully'],200);
            }else{
                return response()->json(['message' => 'something went wrong! here '],401);
            }
        }else{
            return response()->json(['message' => 'You dont have the permission to access this part!'],401);
        }
    }


    /**
     * Store level into database
     *
     * @return \Illuminate\Http\Response
     */
    public function rec_shipment_doc_upload(Request $request)
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){

            $DB_transantion_result = BookDisRM::uploadDocRecShipment(Input::all(), Auth::user()->id);
            if($DB_transantion_result == 1){
                return response()->json(['message' => 'Document has been uploaded successfully',"status" => 200],200);
            }else{
                return response()->json(['message' => 'something went wrong! here ' ,"status" => 401],401);
            }
        }else{
            return response()->json(['message' => 'You dont have the permission to access this part!'],401);
        }
    }


    /**
     * Store level into database
     *
     * @return \Illuminate\Http\Response
     */
    public function send_shipment(Request $request)
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){

            //check if the user_node is the vendor or starter
            $is_starter = BookDisRM::isStarter( Auth::user());

            if($is_starter == 1){
                $DB_transantion_result = BookDisRM::createStarterSendShipment(Input::all(), Auth::user());
                if($DB_transantion_result == 1){
                    return response()->json(['message' => 'Shipment has been successfully sent'],200);
                }else{
                    // == 2 means the records are already exist in database, en error message will be passed to client
                    if($DB_transantion_result == 2){
                        //internal code is used just for passing the error messages to client. 501 means that the data is duplicated
                        return response()->json(['internal_code' => "501",'message' => 'The data provided has been already exist in database, please make correction and try later. The tile and other data for this action must be unique'],401);
                    }
                    return response()->json(['message' => 'something went wrong! here '],401);
                }
            }else{
                $is_valid_to_send = BookDisRM::isValidToSend(Input::all(), Auth::user());
                if($is_valid_to_send == 1){
                    $DB_transantion_result = BookDisRM::createSendShipment(Input::all(), Auth::user());
                    if($DB_transantion_result == 1){
                        return response()->json(['message' => 'Shipment has been successfully sent'],200);
                    }else{
                        // == 2 means the records are already exist in database, en error message will be passed to client
                        if($DB_transantion_result == 2){
                            //internal code is used just for passing the error messages to client. 501 means that the data is duplicated
                            return response()->json(['internal_code' => "501",'message' => 'The data provided has been already exist in database, please make correction and try later. The tile and other data for this action must be unique'],401);
                        }
                        return response()->json(['message' => 'something went wrong! here Here i meant '.$DB_transantion_result],401);
                    }
                }else{
                    $messages = BookDisRM::messagesToSend(Input::all(), Auth::user());
                    return response()->json(['internal_code' => "502",'messages' => $messages],401);
                }

            }

        }else{
            return response()->json(['message' => 'You dont have the permission to access this part!'],401);
        }
    }
    /**
     * Store level into database
     *
     * @return \Illuminate\Http\Response
     */
    public function send_toBeneficiary_shipment(Request $request)
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){

            //check if the user_node is the vendor or starter

            $is_valid_to_send_to_benif = BookDisRM::isValidToSendToBeneficiary(Input::all(), Auth::user());
            if($is_valid_to_send_to_benif){


                $DB_transantion_result = BookDisRM::createSendToBeneficiShipment(Input::all(), Auth::user());
                //dd($DB_transantion_result);
                if($DB_transantion_result == 1){
                    return response()->json(['message' => 'Shipment has been successfully sent'],200);
                }else{
                    // == 2 means the records are already exist in database, en error message will be passed to client
                    if($DB_transantion_result == 2){
                        //internal code is used just for passing the error messages to client. 501 means that the data is duplicated
                        return response()->json(['internal_code' => "501",'message' => 'The data provided has been already exist in database, please make correction and try later. The tile and other data for this action must be unique'],401);
                    }
                    return response()->json(['message' => 'something went wrong! here #1 '. $DB_transantion_result],401);
                }


            }else{
                $messages = BookDisRM::messagesToSendToBeneficiary(Input::all(), Auth::user());
                return response()->json(['internal_code' => "502",'messages' => $messages],401);
            }


        }else{
            return response()->json(['message' => 'You dont have the permission to access this part!'],401);
        }
    }
    /**
     * Store level into database
     *
     * @return \Illuminate\Http\Response
     */
    public function send_fromBeneficiary_shipment(Request $request)
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){

            $DB_transantion_result = BookDisRM::createRecieveFromBeneficiShipment(Input::all(), Auth::user());
            if($DB_transantion_result == 1){
                return response()->json(['message' => 'Shipment has been successfully sent'],200);
            }else{
                // == 2 means the records are already exist in database, en error message will be passed to client
                if($DB_transantion_result == 2){
                    //internal code is used just for passing the error messages to client. 501 means that the data is duplicated
                    return response()->json(['internal_code' => "501",'message' => 'The data provided has been already exist in database, please make correction and try later. The tile and other data for this action must be unique'],401);
                }
                return response()->json(['message' => 'something went wrong! here '],401);
            }
        }else{
            return response()->json(['message' => 'You dont have the permission to access this part!'],401);
        }
    }



    /**
     * Store level into database
     *
     * @return \Illuminate\Http\Response
     */
    public function benefic_node_id(Request $request)
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){



            $has_beneficiry = BookDisRM::hasBeneficiary( Auth::user());
            if($has_beneficiry == 1){
                $node_id = BookDisRM::getNodeId( Auth::user());
                return response()->json(['node_id' => $node_id],200);
            }else{
                return response()->json(['message' => 'something went wrong! here '],401);
            }

        }else{
            return response()->json(['message' => 'You dont have the permission to access this part!'],401);
        }
    }

    /**
     * Store level into database
     *
     * @return \Illuminate\Http\Response
     */
    public function submit_receipt(Request $request)
    {
        if(Auth::user() && Auth::user()->canRead('book_dis_admin')){

            $DB_transantion_result = BookDisRM::createReceipt(Input::all(), Auth::user());
            if($DB_transantion_result == 1){
                return response()->json(['message' => 'Shipment has been successfully sent'],200);
            }else{
                // == 2 means the records are already exist in database, en error message will be passed to client
                if($DB_transantion_result == 2){
                    //internal code is used just for passing the error messages to client. 501 means that the data is duplicated
                    return response()->json(['internal_code' => "501",'message' => 'The data provided has been already exist in database, please make correction and try later. The tile and other data for this action must be unique'],401);
                }
                return response()->json(['message' => 'something went wrong! here '],401);
            }
        }else{
            return response()->json(['message' => 'You dont have the permission to access this part!'],401);
        }
    }
    /**
     * Store level into database
     *
     * @return \Illuminate\Http\Response
     */
    public function submit_receive(Request $request)
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){

            $is_valid_to_receive = BookDisRM::isValidToReceive(Input::all(), Auth::user());
            if($is_valid_to_receive == 1){

                $DB_transantion_result = BookDisRM::createReceive(Input::all(), Auth::user());
                if($DB_transantion_result == 1){
                    return response()->json(['message' => 'Shipment has been successfully received'],200);
                }else{
                    // == 2 means the records are already exist in database, en error message will be passed to client
                    if($DB_transantion_result == 2){
                        //internal code is used just for passing the error messages to client. 501 means that the data is duplicated
                        return response()->json(['internal_code' => "501",'message' => 'The data provided has been already exist in database, please make correction and try later. The tile and other data for this action must be unique'],401);
                    }
                    return response()->json(['message' => 'something went wrong! here '],401);
                }

            }else{
                $messages = BookDisRM::messagesToSendForReceive(Input::all(), Auth::user());
                return response()->json(['internal_code' => "502",'messages' => $messages],401);
            }

        }else{
            return response()->json(['message' => 'You dont have the permission to access this part!'],401);
        }
    }


    /**
     * get back list of provinces.
     *
     * @return \Illuminate\Http\Response
     */
    public function province_list_all()
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){
            return province::all();
        }
        return response(view('layouts.401'), 401);
    }

    /**
     * get back list of district.
     *
     * @return \Illuminate\Http\Response
     */
    public function district_list_province($prov_id)
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){
            return district::where('province_id',$prov_id)->get();
        }
        return response(view('layouts.401'), 401);
    }
    /**
     * get back list of district.
     *
     * @return \Illuminate\Http\Response
     */
    public function send_report_search($node_id)
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){
            /*$DB_transantion_result = BookDisRM::searchSendReport($node_id);*/
            $DB_transantion_result = BookDisRM::searchReport($node_id);
            return $DB_transantion_result;
        }
        return response(view('layouts.401'), 401);
    }
    /**
     * get back list of district.
     *
     * @return \Illuminate\Http\Response
     */
    public function receive_report_search($node_id)
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){
            $DB_transantion_result = BookDisRM::searchReceiveReport($node_id);
            return $DB_transantion_result;
        }
        return response(view('layouts.401'), 401);
    }


    /**
     * Store level into database
     *
     * @return \Illuminate\Http\Response
     */
    public function admin_level_store(Request $request)
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){

            $DB_transantion_result = BookDisRM::createLevel(Input::all(), Auth::user()->id);
            if($DB_transantion_result == 1){
                return response()->json(['message' => 'level has been successfully saved'],200);
            }else{
                return response()->json(['message' => 'something went wrong! here '],401);
            }
        }else{
            return response()->json(['message' => 'You dont have the permission to access this part!'],401);
        }
    }

    /**
     * Display a node page for admin of book sys.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin_node_index()
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){
            return view('inventory.admin.node.index');
        }
        return response(view('layouts.401'), 401);
    }
    /**
     * Display a node page for admin of book sys.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard_index()
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin'))){
            $is_parent = false;
            $has_parent = false;
            $has_child = false;
            $has_beneficiary = false;
            //this is the last commmit
            $user = Auth::user();
            if(BookDisRM::hasNode($user) == 1){
                $is_parent = BookDisRM::isFromRootLevel($user);
                $has_parent = BookDisRM::hasParent($user);
                $has_child = BookDisRM::hasChild($user);
                $has_beneficiary = BookDisRM::hasBeneficiary($user);

                return view('inventory.admin.dashboard.index', compact("is_parent" ,$is_parent , "has_parent" , $has_parent , "has_child" , $has_child , "has_beneficiary" , $has_beneficiary));
            }else{
                return response(view('layouts.401'), 401);
            }
        }
        return response(view('layouts.401'), 401);
    }
    /**
     * Display a setting page for admin of book sys.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin_node_list()
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){

            return book_dis_node::paginate();
        }
        return response(view('layouts.401'), 401);
    }

    /**
     * Display a setting page for admin of book sys.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin_get_node($node_id)
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){

            $result_db_node = book_dis_node::findOrFail($node_id);
            if($result_db_node != null){
                return $result_db_node;

            }else{
                return response()->json(['message' => 'something went wrong! here, Id was not correct '],401);
            }
        }
        return response(view('layouts.401'), 401);
    }
    /**
     * Display a setting page for admin of book sys.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin_get_node_users($node_id)
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){

            $result_db_node = BookDisRM::getNodeUserList($node_id);
            if($result_db_node != null){
                return $result_db_node;

            }else{
                return response()->json(['message' => 'something went wrong! here, Id was not correct '],401);
            }
        }
        return response(view('layouts.401'), 401);
    }
    /**
     * Display a setting page for admin of book sys.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin_search_node_users(Request $request)
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){

            $result_db_node = BookDisRM::searchUser(Input::all());
            return $result_db_node;
        }
        return response(view('layouts.401'), 401);
    }

    /**
     * Display a setting page for admin of book sys.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin_attach_node_user($node_id,$user_id)
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){

            if(BookDisRM::validateNode($node_id) == 0){
                return response()->json(['message' => 'something went wrong! here, Id was not correct '],401);
            }
            if(BookDisRM::validateUser($user_id) == 0){
                return response()->json(['message' => 'something went wrong! here, Id was not correct '],401);
            }

            $result_db_node = BookDisRM::attachUserToNode($node_id,$user_id);
            if($result_db_node != 0){
                return response()->json(['message' => 'level has been successfully saved'],200);
            }else{
                return response()->json(['message' => 'something went wrong! here, Id was not correct '],401);
            }
        }
        return response(view('layouts.401'), 401);
    }


    public function report_province_wise_search(Request $request)
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){

            $DB_transantion_result = BookDisRM::createReportProvinceWise(Input::all());
            if($DB_transantion_result != null){
                return $DB_transantion_result;
            }else{
                // == 2 means the records are already exist in database, en error message will be passed to client
                if($DB_transantion_result == 2){
                    //internal code is used just for passing the error messages to client. 501 means that the data is duplicated
                    return response()->json(['internal_code' => "501",'message' => 'The data provided has been already exist in database, please make correction and try later. The tile and other data for this action must be unique'],401);
                }
                return response()->json(['message' => 'something went wrong! here '],401);
            }

        }else{
            return response()->json(['message' => 'You dont have the permission to access this part!'],401);
        }
    }
    public function report_province_wise_sent_search(Request $request)
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){

            $DB_transantion_result = BookDisRM::createReportProvinceWiseSent(Input::all());
            if($DB_transantion_result != null){
                return $DB_transantion_result;
            }else{
                // == 2 means the records are already exist in database, en error message will be passed to client
                if($DB_transantion_result == 2){
                    //internal code is used just for passing the error messages to client. 501 means that the data is duplicated
                    return response()->json(['internal_code' => "501",'message' => 'The data provided has been already exist in database, please make correction and try later. The tile and other data for this action must be unique'],401);
                }
                return response()->json(['message' => 'something went wrong! here '],401);
            }

        }else{
            return response()->json(['message' => 'You dont have the permission to access this part!'],401);
        }
    }
    public function report_district_wise_search(Request $request)
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){

            $DB_transantion_result = BookDisRM::createReportDistrictWise(Input::all());
            if($DB_transantion_result != null){
                return $DB_transantion_result;
            }else{
                // == 2 means the records are already exist in database, en error message will be passed to client
                if($DB_transantion_result == 2){
                    //internal code is used just for passing the error messages to client. 501 means that the data is duplicated
                    return response()->json(['internal_code' => "501",'message' => 'The data provided has been already exist in database, please make correction and try later. The tile and other data for this action must be unique'],401);
                }
                return response()->json(['message' => 'something went wrong! here '],401);
            }

        }else{
            return response()->json(['message' => 'You dont have the permission to access this part!'],401);
        }
    }

    public function report_school_district_wise_search(Request $request)
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){

            $DB_transantion_result = BookDisRM::createReportDistrictSchoolWise(Input::all());
            if($DB_transantion_result != null){
                return $DB_transantion_result;
            }else{
                // == 2 means the records are already exist in database, en error message will be passed to client
                if($DB_transantion_result == 2){
                    //internal code is used just for passing the error messages to client. 501 means that the data is duplicated
                    return response()->json(['internal_code' => "501",'message' => 'The data provided has been already exist in database, please make correction and try later. The tile and other data for this action must be unique'],401);
                }
                return response()->json(['message' => 'something went wrong! here '],401);
            }

        }else{
            return response()->json(['message' => 'You dont have the permission to access this part!'],401);
        }
    }

    public function report_central_wise_search(Request $request)
    {
        if(Auth::user() && (Auth::user()->canRead('book_dis_admin') || Auth::user()->canRead('book_distribution_staff'))){

            $DB_transantion_result = BookDisRM::createReportCentralWise(Input::all());
            if($DB_transantion_result != null){
                return $DB_transantion_result;
            }else{
                // == 2 means the records are already exist in database, en error message will be passed to client
                if($DB_transantion_result == 2){
                    //internal code is used just for passing the error messages to client. 501 means that the data is duplicated
                    return response()->json(['internal_code' => "501",'message' => 'The data provided has been already exist in database, please make correction and try later. The tile and other data for this action must be unique'],401);
                }
                return response()->json(['message' => 'something went wrong! here '],401);
            }

        }else{
            return response()->json(['message' => 'You dont have the permission to access this part!'],401);
        }
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function BookList()
    {
        return partner::paginate();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function add()
    {
        if(Auth::user() && Auth::user()->canCreate('data_entry_book_dis')){
            $provinces = province::all();
            return $provinces;
        }
        return array();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(Auth::user() && Auth::user()->canCreate('data_entry_book_dis')){

            $data= Input::all();
            return $data;
        }
        return array();
    }

    /**
     * Update the record of the data
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        if(Auth::user() && Auth::user()->canCreate('data_entry_book_dis')){

            $data= Input::all();
            return $data;
        }
        return array();
    }

    /**
     * Destroy the record of the data
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Auth::user() && Auth::user()->canCreate('data_entry_book_dis')){

            $data= Input::all();
            return $data;
        }
        return array();
    }


    /**
     * This is just for test the functionality of the application
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function test()
    {

        //return array();
        return view('inventory.test.burgers');
    }
    public function show($id)
    {

        $book = book_dis_rec::findOrFail($id);

        return $book;
    }


}
