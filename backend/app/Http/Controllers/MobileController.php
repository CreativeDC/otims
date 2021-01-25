<?php

namespace App\Http\Controllers;

use App\inventory_RM\BookDisRM;
use App\Models\book_dis\book_dis_node;
use App\Models\book_dis\book_dis_node_level;
use App\Models\book_dis\book_dis_shipment_file;
use App\Models\book_dis\book_request;
use App\Models\book_dis\book_request_file;
use App\Models\book_dis\district;
use App\Models\book_dis\project;
use App\Models\book_dis\province;
use App\User;
use Auth;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Response;

class MobileController extends Controller
{
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|string|min:3',
        ]);

        if (User::where('email', $request->get('email'))->exists()) {
            $user = User::where('email', $request->get('email'))->first();
            $auth = Hash::check($request->get('password'), $user->password);

            if ($user && $auth) {

                $user->rollApiKey(); //Model Function

                $isAdmin = $user->canRead('book_dis_admin');
                $user['admin'] = $isAdmin;

                return response(array(
                    'user' => $user,
                    'message' => 'Authentication successful.',
                    'code' => 200,
                ));
            }
        }
        return response(array(
            'message' => 'Unauthenticated, check your credentials.',
            'code' => 401,
        ), 401);
    }

    // web: not available or nested somewhere
    public function getUserNode()
    {
        $nodes = auth()->guard('api')->user()->work_node();

        if (count($nodes) > 0) {
            $node = $nodes->first();
            return $node;
        }

        return $nodes;

        // return response()->json(['message' => 'No node information.'], 401);

    }

    // web: not available or nested somewhere
    public function getUserAndParentNodes()
    {
        $nodes = auth()->guard('api')->user()->work_node();
        $node = null;

        if (count($nodes) > 0) {
            $node = $nodes->first();
        }

        $node->parent = book_dis_node::where('id', $node->parent_id)->first();

        return $node;
    }

    // web: not available or nested somewhere
    public function getUserRoles()
    {
        $roles = auth()->guard('api')->user()->roles;

        return $roles;
    }

    // web: BookController admin_level_list_all
    public function getLevels()
    {
        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {
            return book_dis_node_level::all();
        }
        return response()->json(['message' => 'Insufficient permissions.'], 401);
    }

    // web: N/A
    public function getProjects()
    {
        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {
            return project::all();
        }
        return response()->json(['message' => 'Insufficient permissions.'], 401);
    }

    // web: BookController province_list_all
    public function getProvinces()
    {
        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {
            return province::all();
        }
        return response()->json(['message' => 'Insufficient permissions.'], 401);
    }

    // web: BookController district_list_province
    public function getDistricts($provinceId)
    {
        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {
            return district::where('province_id', $provinceId)->get();
        }
        return response()->json(['message' => 'Insufficient permissions.'], 401);
    }

    // web: BookController record_child_node
    public function getLevelChildNode($levelId, $provinceId, $districtId)
    {
        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {
            $db_result = BookDisRM::getChildNodes($levelId, $provinceId, $districtId);
            if ($db_result != null) {
                return $db_result;
            } else {
                return array();
            }

            return $db_result;
        }
        return response()->json(['message' => 'Insufficient permissions.'], 401);
    }

    // web: BookController grade_list
    public function getGrades()
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff')) {

            $db_result = BookDisRM::getGradeList();
            if ($db_result != null) {
                return $db_result;
            } else {
                return array();
            }

            return $db_result;
        }
        return response()->json(['message' => 'Insufficient permissions.'], 401);
    }

    // web: BookController sent_list
    public function getSentShipmentsByPage()
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff')) {

            if (request()->has("page")) {
                if (request()->page != 0) {
                    return BookDisRM::sentShipmentList(request()->page, request()->url, auth()->guard('api')->user()->id);
                } else {
                    return response()->json(['message' => 'something went wrong here!'], 401);
                }
            } else {
                return response()->json(['message' => 'something went wrong here!'], 401);
            }
        } else {
            return response()->json(['message' => 'Insufficient permissions.'], 401);
        }
    }

    // web: BookController receive_list
    public function getReceivedShipmentsByPage()
    {
        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {
            $DB_balance_result = BookDisRM::getReceives(request()->page, request()->url, auth()->guard('api')->user());
            if ($DB_balance_result != null) {
                return $DB_balance_result;
            } else {
                return array();
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    // web: N/A. Added by Zabeeh.
    public function getPendingReceivedShipments()
    {
        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {
            $DB_balance_result = BookDisRM::getPendingReceives(request()->page, request()->url, auth()->guard('api')->user());
            if ($DB_balance_result != null) {
                return $DB_balance_result;
            } else {
                return array();
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    // web: BookController sent_list_all
    public function getAllSentShipments()
    {
        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {
            return BookDisRM::sentShipmentList_all(auth()->guard('api')->user()->id);
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    // web: BookController balance_list
    public function getRecentTransactions()
    {
        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {
            $DB_balance_result = BookDisRM::getBalances(auth()->guard('api')->user());
            if ($DB_balance_result != null) {
                return $DB_balance_result;
            } else {
                return array();
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    // web: BookController balance_d_list
    public function getBalanceList()
    {
        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {
            $DB_balance_result = BookDisRM::getBalancesDetail(auth()->guard('api')->user());
            if ($DB_balance_result != null) {
                return $DB_balance_result;
            } else {
                return array();
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    // web: BookController sent_record
    public function getSentPackageSentDetails($shipmentId)
    {
        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {
            if ($shipmentId != 0) {
                $db_result = BookDisRM::getSentShipmentItem($shipmentId);

                return $db_result;
            } else {
                return response()->json(['message' => "Incorrect shipment ID."], 401);
            }
        }
        return response()->json(['message' => "Insufficient permissions."], 401);
    }

    // web: BookController receive_record
    public function getReceivedPackageSentDetails($shipmentId)
    {
        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {
            if ($shipmentId != 0) {
                $db_result = BookDisRM::getReceiveShipmentItem($shipmentId);

                return $db_result;
            } else {
                return response()->json(['message' => "Incorrect shipment ID."], 401);
            }
        }
        return response()->json(['message' => "Insufficient permissions."], 401);
    }

    // web: BookController sent_recieve_shipment
    public function getReceivedPackageAllDetails($shipmentId)
    {
        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {
            if ($shipmentId != 0) {
                $db_result = BookDisRM::getSentRecieveShipment($shipmentId);
                if ($db_result != null) {
                    return $db_result;
                } else {
                    return response()->json(['message' => "Something went wrong."], 401);
                }
            } else {
                return response()->json(['message' => "Incorrent shipment ID."], 401);
            }
        }
        return response()->json(['message' => "Insufficient permissions."], 401);
    }

    // web: BookController sent_shipment
    public function getSentPackageAllDetails($shipmentId)
    {
        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {
            if ($shipmentId != 0) {
                $db_result = BookDisRM::getSentShipment($shipmentId);
                if ($db_result != null) {
                    return $db_result;
                } else {
                    return response()->json(['message' => "Something went wrong."], 401);
                }
            } else {
                return response()->json(['message' => "Incorrent shipment ID."], 401);
            }
        }

        return response()->json(['message' => "Insufficient permissions."], 401);
    }

    // web: BookController receive_shipment
    public function getReceiveRecord($id)
    {
        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {
            if ($id != 0) {
                $db_result = BookDisRM::getReceiveShipment($id);
                if ($db_result != null) {
                    return $db_result;
                } else {
                    return response()->json(['message' => "Something went wrong."], 401);
                }
            } else {
                return response()->json(['message' => "Incorrent shipment ID."], 401);
            }
        }

        return response()->json(['message' => "Insufficient permissions."], 401);
    }

    // web: BookController shipment_receive
    public function getShipmentRecord($id)
    {
        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {
            if ($id != 0) {
                $db_result = BookDisRM::getShipmentReceive($id);
                if ($db_result != null) {
                    return $db_result;
                } else {
                    return response()->json(['message' => "Something went wrong."], 401);
                }
            } else {
                return response()->json(['message' => "Incorrent shipment ID."], 401);
            }
        }

        return response()->json(['message' => "Insufficient permissions."], 401);
    }

    // web: BookController send_shipment
    public function sendShipment(Request $request)
    {

        // $da = Input::all();
        // ob_flush();
        // ob_start();
        // var_export($da);
        // file_put_contents("dump-send-mobile-province_to_vendor.txt", ob_get_flush());
        // die();

        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {

            //check if the user_node is the vendor or starter
            $is_starter = BookDisRM::isStarter(auth()->guard('api')->user());

            if ($is_starter == 1) {
                $DB_transantion_result = BookDisRM::createStarterSendShipment(Input::all(), auth()->guard('api')->user());
                if ($DB_transantion_result == 1) {
                    return response()->json(['message' => 'Shipment has been successfully sent.'], 200);
                } else {
                    // == 2 means the records are already exist in database, en error message will be passed to client
                    if ($DB_transantion_result == 2) {
                        //internal code is used just for passing the error messages to client. 501 means that the data is duplicated
                        return response()->json(['internal_code' => "501", 'message' => 'The data provided already exists in the database. The title and other data for this action must be unique.'], 401);
                    }
                    return response()->json(['message' => 'Something went wrong.'], 401);
                }
            } else {
                $is_valid_to_send = BookDisRM::isValidToSend(Input::all(), auth()->guard('api')->user());
                if ($is_valid_to_send == 1) {
                    $DB_transantion_result = BookDisRM::createSendShipment(Input::all(), auth()->guard('api')->user());
                    if ($DB_transantion_result == 1) {
                        return response()->json(['message' => 'Shipment has been successfully sent.'], 200);
                    } else {
                        // == 2 means the records are already exist in database, en error message will be passed to client
                        if ($DB_transantion_result == 2) {
                            //internal code is used just for passing the error messages to client. 501 means that the data is duplicated
                            return response()->json(['internal_code' => "501", 'message' => 'The data provided already exists in the database. The title and other data for this action must be unique.'], 401);
                        }
                        return response()->json(['message' => 'Something went wrong here: ' . $DB_transantion_result], 401);
                    }
                } else {
                    $messages = BookDisRM::messagesToSend(Input::all(), auth()->guard('api')->user());
                    return response()->json(['internal_code' => "502", 'messages' => $messages], 401);
                }
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    // web: BookController submit_receive
    public function receiveShipment(Request $request)
    {
        // $da = Input::all();
        // ob_flush();
        // ob_start();
        // var_export($da);
        // file_put_contents("dump-receive-for-school-mobile.txt", ob_get_flush());
        // die();

        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {

            $is_valid_to_receive = BookDisRM::isValidToReceive(Input::all(), auth()->guard('api')->user());
            if ($is_valid_to_receive == 1) {

                $DB_transantion_result = BookDisRM::createReceive(Input::all(), auth()->guard('api')->user());
                if ($DB_transantion_result == 1) {
                    return response()->json(['message' => 'Shipment has been successfully received.'], 200);
                } else {
                    // == 2 means the records are already exist in database, en error message will be passed to client
                    if ($DB_transantion_result == 2) {
                        //internal code is used just for passing the error messages to client. 501 means that the data is duplicated
                        return response()->json(['internal_code' => "501", 'message' => 'The data provided already exists in the database. The title and other data for this action must be unique.'], 401);
                    }
                    return response()->json(['message' => 'Something went wrong.'], 401);
                }
            } else {
                $messages = BookDisRM::messagesToSendForReceive(Input::all(), auth()->guard('api')->user());
                return response()->json(['internal_code' => "502", 'messages' => $messages], 401);
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    // web: BookController group_node_list
    public function getGroupNodes()
    {
        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {
            $DB_balance_result = BookDisRM::getGroupNode(auth()->guard('api')->user());
            if ($DB_balance_result != null) {
                return $DB_balance_result;
            } else {
                return array();
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    // web: BookController sent_node_list
    public function getNodeAllSentShipments($node_id)
    {
        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {
            return BookDisRM::sentNodeShipmentList($node_id, request()->url, auth()->guard('api')->user()->id);
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    // web: BookController receive_node_list
    public function getNodeAllReceivedShipments($node_id)
    {
        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {
            $DB_balance_result = BookDisRM::getNodeRecieveReceives($node_id, request()->url, auth()->guard('api')->user());
            if ($DB_balance_result != null) {
                return $DB_balance_result;
            } else {
                return array();
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    // web: BookController benefic_node_id
    public function getUserBeneficiaryNodeId(Request $request)
    {
        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {
            $has_beneficiry = BookDisRM::hasBeneficiary(auth()->guard('api')->user());
            if ($has_beneficiry == 1) {
                $node_id = BookDisRM::getNodeId(auth()->guard('api')->user());
                return response()->json(['node_id' => $node_id], 200);
            } else {
                return response()->json(['message' => 'No beneficiary node for user.'], 401);
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    // web: BookController to_benefic_list
    public function getUserBeneficiarySentShipments()
    {
        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {
            $DB_to_beneficiary = BookDisRM::getToBenefic(auth()->guard('api')->user());
            if ($DB_to_beneficiary != null) {
                return $DB_to_beneficiary;
            } else {
                return array();
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    // web: BookController to_benefic_list
    public function getUserBeneficiaryReceivedShipments()
    {
        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {
            $DB_to_beneficiary = BookDisRM::getFromBenefic(auth()->guard('api')->user());
            if ($DB_to_beneficiary != null) {
                return $DB_to_beneficiary;
            } else {
                return array();
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    // web: BookController to_benefic_list_id
    public function getNodeBeneficiarySentShipments($node_id)
    {
        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {
            $DB_to_beneficiary = BookDisRM::getToBeneficId($node_id);
            if ($DB_to_beneficiary != null) {
                return $DB_to_beneficiary;
            } else {
                return array();
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    // web: BookController from_benefic_list_id
    public function getNodeBeneficiaryReceivedShipments($node_id)
    {
        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {
            $DB_to_beneficiary = BookDisRM::getFromBeneficId($node_id);
            if ($DB_to_beneficiary != null) {
                return $DB_to_beneficiary;
            } else {
                return array();
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    // web: BookController send_toBeneficiary_shipment
    public function sendShipmentToBeneficiary(Request $request)
    {
        // $da = Input::all();
        // ob_flush();
        // ob_start();
        // var_export($da);
        // file_put_contents("dump-to-beneficiary-mobile.txt", ob_get_flush());
        // die();

        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {

            //check if the user_node is the vendor or starter

            $is_valid_to_send_to_benif = BookDisRM::isValidToSendToBeneficiary(Input::all(), auth()->guard('api')->user());
            if ($is_valid_to_send_to_benif) {

                $DB_transantion_result = BookDisRM::createSendToBeneficiShipment(Input::all(), auth()->guard('api')->user());
                //dd($DB_transantion_result);
                if ($DB_transantion_result == 1) {
                    return response()->json(['message' => 'Shipment has been successfully sent.'], 200);
                } else {
                    // == 2 means the records are already exist in database, en error message will be passed to client
                    if ($DB_transantion_result == 2) {
                        //internal code is used just for passing the error messages to client. 501 means that the data is duplicated
                        return response()->json(['internal_code' => "501", 'message' => 'The data provided already exists in the database. The title and other data for this action must be unique.'], 401);
                    }
                    return response()->json(['message' => 'Something went wrong: ' . $DB_transantion_result], 401);
                }
            } else {
                $messages = BookDisRM::messagesToSendToBeneficiary(Input::all(), auth()->guard('api')->user());
                return response()->json(['internal_code' => "502", 'messages' => $messages], 401);
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    // web: BookController send_fromBeneficiary_shipment
    public function receiveShipmentFromBeneficiary(Request $request)
    {
        // $da = Input::all();
        // ob_flush();
        // ob_start();
        // var_export($da);
        // file_put_contents("dump-from-beneficiary-mobile.txt", ob_get_flush());
        // die();

        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {

            $DB_transantion_result = BookDisRM::createRecieveFromBeneficiShipment(Input::all(), auth()->guard('api')->user());
            if ($DB_transantion_result == 1) {
                return response()->json(['message' => 'Shipment has been successfully received.'], 200);
            } else {
                // == 2 means the records are already exist in database, en error message will be passed to client
                if ($DB_transantion_result == 2) {
                    //internal code is used just for passing the error messages to client. 501 means that the data is duplicated
                    return response()->json(['internal_code' => "501", 'message' => 'The data provided already exists in the database. The title and other data for this action must be unique.'], 401);
                }
                return response()->json(['message' => 'Something went wrong.'], 401);
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    // web: BookController language_list
    public function getFirstLanguages()
    {
        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {

            $db_result = BookDisRM::getLanguageList();
            if ($db_result != null) {
                return $db_result;
            } else {
                return array();
            }

            return $db_result;
        }

        return response()->json(['message' => "Insufficient permissions."], 401);
    }

    // web: BookController language_full_list
    public function getThirdLanguages()
    {
        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {
            $db_result = BookDisRM::getLanguageFullList();
            if ($db_result != null) {
                return $db_result;
            } else {
                return array();
            }

            return $db_result;
        }

        return response()->json(['message' => "Insufficient permissions."], 401);
    }

    // web: BookController shipment_documents
    public function getShipmentDocuments($shipmentId)
    {
        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {
            if ($shipmentId != 0) {
                $db_result = BookDisRM::getShipmentDocuments($shipmentId);

                return $db_result;
            } else {
                return response()->json(['message' => 'Something went wrong.'], 401);
            }
        }

        return response()->json(['message' => "Insufficient permissions."], 401);
    }

    // web: BookController DownloadShipmentFile
    public function downloadShipmentDocument($fileId)
    {
        $files = book_dis_shipment_file::where('id', $fileId)->get();
        if (count($files) > 0) {
            $file = $files->first();
            // $path=base_path().'/public/'.$file->file_url;
            $path = base_path() . '/' . $file->file_url;
            $file_name = explode('/', $file->file_url);
            $file_name1 = strtolower(end($file_name));

            ob_end_clean();

            $headers = array(
                'Content-Type: image/png',
            );
            return Response::download($path, $file_name1, $headers);

            // return response()->json(['path' => $path], 200);

        }
    }

    // web: BookController shipment_doc_upload
    public function uploadSentDocument(Request $request)
    {
        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {
            $DB_transantion_result = BookDisRM::uploadDocShipment(Input::all(), auth()->guard('api')->user()->id);
            if ($DB_transantion_result == 1) {
                return response()->json(['message' => 'The document has been uploaded successfully.'], 200);
            } else {
                return response()->json(['message' => 'Something went wrong.'], 401);
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    // web: BookController rec_shipment_doc_upload
    public function uploadReceivedDocument(Request $request)
    {
        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {
            $DB_transantion_result = BookDisRM::uploadDocRecShipment(Input::all(), auth()->guard('api')->user()->id);
            if ($DB_transantion_result == 1) {
                return response()->json(['message' => 'The document has been uploaded successfully.'], 200);
            } else {
                return response()->json(['message' => 'Something went wrong.'], 401);
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    // Admin Panel
    public function clearReceiveRecords($recvId)
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin')) {
            $result = BookDisRM::adminClearReceiveRecords($recvId);

            if ($result == 1) {
                return response()->json(['message' => "SRID " . $recvId . ": successfully cleared the receive records."], 200);
            } else {
                return response()->json(['message' => $result], 500);
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    // Admin Panel
    public function deleteShipment($shipmentId)
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin')) {
            $result = BookDisRM::adminDeleteShipment($shipmentId);

            if ($result == 1) {
                return response()->json(['message' => "SID " . $shipmentId . ": successfully deleted the shipment."], 200);
            } else {
                return response()->json(['message' => $result], 500);
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    // Admin Panel
    public function getNodeBalanceList($nodeId)
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin')) {
            // create function
            $result = BookDisRM::adminGetNodeBalance($nodeId);
            if ($result != null) {
                return $result;
            } else {
                return array();
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    public function deleteBalanceRecord(Request $request)
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin')) {
            $result = BookDisRM::adminDeleteBalanceRecord(Input::all());

            if ($result == 1) {
                return response()->json(['message' => 'The record has been successfully deleted.'], 200);
            } else {
                return response()->json(['message' => 'Something went wrong.', 'error' => $result], 500);
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    public function updateBalanceRecord(Request $request)
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin')) {
            $result = BookDisRM::adminUpdateBalanceRecord(Input::all());

            if ($result == 1) {
                return response()->json(['message' => 'The record has been successfully updated.'], 200);
            } else {
                return response()->json(['message' => 'Something went wrong.', 'error' => $result], 500);
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    public function updateShipmentGeneralInfo(Request $request)
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin')) {
            $result = BookDisRM::adminUpdateShipmentGeneralInfo(Input::all());

            if ($result == 1) {
                return response()->json(['message' => 'The information has been successfully updated.'], 200);
            } else {
                return response()->json(['message' => 'Something went wrong.', 'error' => $result], 500);
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    public function updateShipmentRecipient(Request $request)
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin')) {
            $result = BookDisRM::adminUpdateShipmentRecipient(Input::all());

            if ($result == 1) {
                return response()->json(['message' => 'The recipient has been successfully changed.'], 200);
            } else {
                return response()->json(['message' => 'Something went wrong.', 'error' => $result], 500);
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    // web: BookController admin_node_store
    public function createNode(Request $request)
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin')) {
            $result = BookDisRM::createNode(Input::all(), auth()->guard('api')->user()->id);

            if ($result == 1) {
                return response()->json(['message' => 'The node has been successfully created.'], 200);
            } else {
                return response()->json(['message' => 'Something went wrong.'], 500);
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    public function getAllSchoolsForDistrict($provinceId, $districtId)
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin')) {
            $result = BookDisRM::getAllSchoolsForDistrict($provinceId, $districtId);

            if ($result != null) {
                return $result;
            } else {
                return array();
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    public function getAllUsersForDistrict($provinceId, $districtId)
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin')) {
            $result = BookDisRM::getAllUsersForDistrict($provinceId, $districtId);

            if ($result != null) {
                return $result;
            } else {
                return array();
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    public function getAllUsersForNode($nodeId)
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin')) {
            $result = BookDisRM::getAllUsersForNode($nodeId);

            if ($result != null) {
                return $result;
            } else {
                return array();
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    public function getAllNodesForUser($userId)
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin')) {
            $result = BookDisRM::getAllNodesForUser($userId);

            if ($result != null) {
                return $result;
            } else {
                return array();
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    public function getAllSchoolNodesForUser($userId)
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin')) {
            $result = BookDisRM::getAllSchoolNodesForUser($userId);

            if ($result != null) {
                return $result;
            } else {
                return array();
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    public function getRolesForUser($userId)
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin')) {
            $roles = User::find($userId)->roles;

            return $roles;
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    public function changePasswordForUser(Request $request)
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin')) {
            $user = User::find($request->userId);
            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json(['message' => 'The password for the selected user has been successfully changed.', 'user' => $user], 200);
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    public function searchUsersByTerm($searchTerm)
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin')) {
            $result = BookDisRM::searchUsersByTerm($searchTerm);

            if ($result != null) {
                return $result;
            } else {
                return array();
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    public function unassignNodeFromUser($nodeId, $userId)
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin')) {
            $result = BookDisRM::unassignNodeFromUser($nodeId, $userId);

            if ($result == 1) {
                return response()->json(['message' => 'The node has been successfully unassigned from user.'], 200);
            } else {
                return response()->json(['message' => 'Something went wrong. Please try again.', 'error' => $result], 500);
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    public function assignNodeToUser($nodeId, $userId)
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin')) {
            $result = BookDisRM::assignNodeToUser($nodeId, $userId);

            if ($result == 1) {
                return response()->json(['message' => 'The node has been successfully assigned to user.'], 200);
            } else {
                return response()->json(['message' => 'Something went wrong. Please try again.', 'error' => $result], 500);
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    public function getAssignedSchoolsForDistrictUser($userId)
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin')) {
            $result = BookDisRM::getAssignedSchoolsForDistrictUser($userId);

            if ($result != null) {
                return $result;
            } else {
                return array();
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    public function assignSchoolsToDistrictUser(Request $request)
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin')) {
            $result = BookDisRM::assignSchoolsToDistrictUser(Input::all());

            if ($result == 1) {
                return response()->json(['message' => 'The schools have been successfully assigned to user.'], 200);
            } else {
                return response()->json(['message' => 'Something went wrong. Please try again.', 'error' => $result], 500);
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    public function unassignSchoolsFromDistrictUser(Request $request)
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin')) {
            $result = BookDisRM::unassignSchoolsFromDistrictUser(Input::all());

            if ($result == 1) {
                return response()->json(['message' => 'The schools have been successfully unassigned from user.'], 200);
            } else {
                return response()->json(['message' => 'Something went wrong. Please try again.', 'error' => $result], 500);
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    // Reporting Module
    public function getStudentDistByDistricts(Request $request)
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin')) {

            $result = BookDisRM::getStudentDistByDistricts($request);

            if ($result != null) {
                return $result;
            } else {
                return array();
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    public function getStudentDistByLanguages(Request $request)
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin')) {

            $result = BookDisRM::getStudentDistByLanguages($request);

            if ($result != null) {
                return $result;
            } else {
                return array();
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    public function getStudentDistByGrades(Request $request)
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin')) {

            $result = BookDisRM::getStudentDistByGrades($request);

            if ($result != null) {
                return $result;
            } else {
                return array();
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    public function getStudentDistByTitles(Request $request)
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin')) {

            $result = BookDisRM::getStudentDistByTitles($request);

            if ($result != null) {
                return $result;
            } else {
                return array();
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    public function getProvinceStats(Request $request)
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin')) {

            $result = BookDisRM::getProvinceStats($request);

            if ($result != null) {
                return $result;
            } else {
                return array();
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    public function studentDistExportExcel($provinceId)
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin')) {
            BookDisRM::studentDistExportExcel($provinceId);
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    public function sentReceivedExportExcel($provinceId)
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin')) {
            BookDisRM::sentReceivedExportExcel($provinceId);
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    public function getAllProvincesTabularRecords(Request $request)
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin')) {
            if ($request->projectId > 0) {
                $result = BookDisRM::getAllProvincesTabularRecordsByProject($request);

            } else {
                $result = BookDisRM::getAllProvincesTabularRecords($request);
            }

            if ($result != null) {
                return $result;
            } else {
                return array();
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    public function getProvinceTabularRecords(Request $request)
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin')) {

            if ($request->projectId > 0) {
                $result = BookDisRM::getProvinceTabularRecordsByProject($request);

            } else {
                $result = BookDisRM::getProvinceTabularRecords($request);

            }

            if ($result != null) {
                return $result;
            } else {
                return array();
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    public function getProvinceDistrictsTabularRecords(Request $request)
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin')) {
            if ($request->projectId > 0) {
                $result = BookDisRM::getProvinceDistrictsTabularRecordsByProject($request);
            } else {
                $result = BookDisRM::getProvinceDistrictsTabularRecords($request);
            }

            if ($result != null) {
                return $result;
            } else {
                return array();
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    public function getSelectedDistrictTabularRecords(Request $request)
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin')) {
            if ($request->projectId > 0) {
                $result = BookDisRM::getSelectedDistrictTabularRecordsByProject($request);

            } else {
                $result = BookDisRM::getSelectedDistrictTabularRecords($request);

            }

            if ($result != null) {
                return $result;
            } else {
                return array();
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    public function getDistrictSchoolsTabularRecords(Request $request)
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin')) {

            if ($request->projectId > 0) {
                $result = BookDisRM::getDistrictSchoolsTabularRecordsByProject($request);
            } else {
                $result = BookDisRM::getDistrictSchoolsTabularRecords($request);
            }

            if ($result != null) {
                return $result;
            } else {
                return array();
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    public function getSentRequestsByPage()
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff')) {

            if (request()->has("page")) {
                if (request()->page != 0) {
                    return BookDisRM::getSentRequestsByPage(request()->page, request()->url, auth()->guard('api')->user()->id);
                } else {
                    return response()->json(['message' => 'something went wrong here!'], 401);
                }
            } else {
                return response()->json(['message' => 'something went wrong here!'], 401);
            }
        } else {
            return response()->json(['message' => 'Insufficient permissions.'], 401);
        }
    }

    public function getReceivedRequestsByPage()
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff')) {

            if (request()->has("page")) {
                if (request()->page != 0) {
                    return BookDisRM::getReceivedRequestsByPage(request()->page, request()->url, auth()->guard('api')->user()->id);
                } else {
                    return response()->json(['message' => 'something went wrong here!'], 401);
                }
            } else {
                return response()->json(['message' => 'something went wrong here!'], 401);
            }
        } else {
            return response()->json(['message' => 'Insufficient permissions.'], 401);
        }
    }

    public function getApprovedReceivedRequests()
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff')) {

            return BookDisRM::getApprovedReceivedRequests(auth()->guard('api')->user()->id);
        } else {
            return response()->json(['message' => 'Insufficient permissions.'], 401);
        }
    }

    public function sendRequest(Request $request)
    {
        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {
            $transaction_result = BookDisRM::sendRequest($request, auth()->guard('api')->user());

            if ($transaction_result == 1) {
                return response()->json(['message' => 'Request has been successfully sent.'], 200);
            } else {
                return response()->json(['message' => 'Something went wrong.'], 401);
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    public function approveRequest(Request $request)
    {
        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {

            $book_request = book_request::findOrFail($request->request_id);

            if ($book_request != null) {
                $book_request->approved = 1;
                $book_request->approved_description = $request->approved_description;
                $book_request->save();

                return response()->json(['message' => 'Request has been successfully approved.'], 200);
            } else {
                return response()->json(['message' => 'Something went wrong.'], 401);
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    public function getRequestDetails($requestId)
    {
        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {
            if ($requestId != 0) {
                $db_result = BookDisRM::getRequestDetails($requestId);

                return $db_result;
            } else {
                return response()->json(['message' => "Incorrect request ID."], 401);
            }
        }
        return response()->json(['message' => "Insufficient permissions."], 401);
    }

    public function getRequestDocuments($requestId)
    {
        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {
            if ($requestId != 0) {
                $db_result = BookDisRM::getRequestDocuments($requestId);

                return $db_result;
            } else {
                return response()->json(['message' => 'Something went wrong.'], 401);
            }
        }

        return response()->json(['message' => "Insufficient permissions."], 401);
    }

    // web: BookController DownloadShipmentFile
    public function downloadRequestDocument($fileId)
    {
        $files = book_request_file::where('id', $fileId)->get();
        if (count($files) > 0) {
            $file = $files->first();
            $path = base_path() . '/' . $file->file_url;
            $file_name = explode('/', $file->file_url);
            $file_name1 = strtolower(end($file_name));

            ob_end_clean();

            $headers = array(
                'Content-Type: image/png',
            );
            return Response::download($path, $file_name1, $headers);
        }
    }

    // web: BookController shipment_doc_upload
    public function uploadRequestDocument(Request $request)
    {
        if (auth()->guard('api')->user() && (auth()->guard('api')->user()->canRead('book_dis_admin') || auth()->guard('api')->user()->canRead('book_distribution_staff'))) {
            $DB_transantion_result = BookDisRM::uploadRequestDocument(Input::all(), auth()->guard('api')->user()->id);
            if ($DB_transantion_result == 1) {
                return response()->json(['message' => 'The document has been uploaded successfully.'], 200);
            } else {
                return response()->json(['message' => 'Something went wrong.'], 401);
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    public function syncDistrictSchools(Request $request)
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin')) {
            $resultUpdate = BookDisRM::syncUpdateDistrictSchools(Input::all());
            $resultDelete = BookDisRM::syncDeleteDistrictSchools(Input::all());
            $resultCreate = BookDisRM::syncCreateDistrictSchools(Input::all(), auth()->guard('api')->user()->id);

            if ($resultUpdate == 1 && $resultDelete == 1 && $resultCreate == 1) {
                return response()->json(['message' => 'The district schools have been successfully synced with EMIS.'], 200);
            } else {
                return response()->json(['message' => 'Something went wrong and the sync process could not be completed.', 'error' => $resultDelete], 500);
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    public function syncProvinces(Request $request)
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin')) {
            $resultUpdate = BookDisRM::syncUpdateProvinces(Input::all());
            $resultCreate = BookDisRM::syncCreateProvinces(Input::all(), auth()->guard('api')->user()->id);
            $resultDelete = BookDisRM::syncDeleteProvinces(Input::all());

            if ($resultUpdate == 1 && $resultDelete == 1 && $resultCreate == 1) {
                return response()->json(['message' => 'The provinces have been successfully synced with EMIS.'], 200);
            } else {
                return response()->json(['message' => 'Something went wrong and the sync process could not be completed.', 'error' => $resultDelete], 500);
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }

    public function syncDistricts(Request $request)
    {
        if (auth()->guard('api')->user() && auth()->guard('api')->user()->canRead('book_dis_admin')) {
            $resultUpdate = BookDisRM::syncUpdateDistricts(Input::all());
            $resultCreate = BookDisRM::syncCreateDistricts(Input::all(), auth()->guard('api')->user()->id);
            $resultDelete = BookDisRM::syncDeleteDistricts(Input::all());

            if ($resultCreate == 1 && $resultUpdate == 1 && $resultDelete == 1) {
                return response()->json(['message' => 'The districts have been successfully synced with EMIS.'], 200);
            } else {
                return response()->json(['message' => 'Something went wrong and the sync process could not be completed.', 'error' => $resultDelete], 500);
            }
        } else {
            return response()->json(['message' => "Insufficient permissions."], 401);
        }
    }
}
