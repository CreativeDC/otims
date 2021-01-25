<?php

/**
 * Created by PhpStorm.
 * User: ahmadz
 * Date: 11/30/16 AD
 * Time: 11:14 AM
 */

namespace App\inventory_RM;

use App\Models\book_dis\book_dis_meta_grade;
use App\Models\book_dis\book_dis_meta_title;
use App\Models\book_dis\book_dis_node;
use App\Models\book_dis\book_dis_node_balance;
use App\Models\book_dis\book_dis_node_balance_detail;
use App\Models\book_dis\book_dis_node_level;
use App\Models\book_dis\book_dis_node_transaction;
use App\Models\book_dis\book_dis_shipment;
use App\Models\book_dis\book_dis_shipment_detail;
use App\Models\book_dis\book_dis_shipment_file;
use App\Models\book_dis\book_dis_shipment_recieve;
use App\Models\book_dis\book_dis_shipment_recieve_detail;
use App\Models\book_dis\book_dis_shipment_recieve_dmg;
use App\Models\book_dis\book_dis_shipment_recieve_lost;
use App\Models\book_dis\book_dis_title_language;
use App\Models\book_dis\book_request;
use App\Models\book_dis\book_request_detail;
use App\Models\book_dis\book_request_file;
use App\Models\book_dis\district;
use App\Models\book_dis\province;
use App\user;
use Carbon\Carbon;
use DB;
use Excel;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Input;

//This is the part to include all needed classes for API
//use Dingo\Api\Routing\Helpers;

class BookDisRM
{

    public static function isFromRootLevel($user)
    {

        /*** If the level code == 1 and the node of user doesnt have the parent then the node can be considered as root one ***/
        $node_users = $user->work_node();
        if (count($node_users) > 0) {
            $user_node = $node_users->first();
            $user_level = $user_node->level()->get();
            if (count($user_level) > 0) {
                /*  code === 1 means the level is the root level */
                if ($user_level->first()->code == "1") {
                    $parent_node = $user_node->parent_node()->get();
                    if (count($parent_node) > 0) {
                        return 0;
                    } else {
                        return 1;
                    }
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public static function isStarter($user)
    {

        /*** If the level code == 1 and the node of user doesnt have the parent then the node can be considered as root one ***/
        $node_users = $user->work_node();
        if (count($node_users) > 0) {
            $user_node = $node_users->first();
            $user_level = $user_node->level()->get();
            if (count($user_level) > 0) {
                /*  code === 1 means the level is the root level */
                if ($user_level->first()->is_starter == "1") {
                    return 1;
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public static function hasParent($user)
    {

        /*** If the level code == 1 and the node of user doesnt have the parent then the node can be considered as root one ***/
        $node_users = $user->work_node();
        if (count($node_users) > 0) {
            $user_node = $node_users->first();
            $level = $user_node->level()->get();
            if (count($level) > 0) {
                $parent = $level->first()->parent()->get();
                if (count($parent) > 0) {
                    return 1;
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public static function hasGroup($user)
    {

        /*** If the level code == 1 and the node of user doesnt have the parent then the node can be considered as root one ***/
        $node_users = $user->group_node()->get();
        if (count($node_users) > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function hasNode($user)
    {

        /*** If the level code == 1 and the node of user doesnt have the parent then the node can be considered as root one ***/
        $node_users = $user->work_node()->get();
        if (count($node_users) > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function hasChild($user)
    {

        /*** If the level code == 1 and the node of user doesnt have the parent then the node can be considered as root one ***/
        $node_users = $user->work_node();
        if (count($node_users) > 0) {
            $user_node = $node_users->first();
            $child_node = $user_node->sub_nodes()->get();
            if (count($child_node) > 0) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public static function hasBeneficiary($user)
    {

        /*** If the level code == 1 and the node of user doesnt have the parent then the node can be considered as root one ***/
        $node_users = $user->work_node();
        if (count($node_users) > 0) {
            $user_node = $node_users->first();
            $level = $user_node->level()->get();
            if (count($level) > 0) {
                $has_beneficiary = $level->first()->sub_levels()->where("is_beneficiary", "1")->get();
                if (count($has_beneficiary) > 0) {
                    return 1;
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public static function getNodeId($user)
    {

        /*** If the level code == 1 and the node of user doesnt have the parent then the node can be considered as root one ***/
        $node_users = $user->work_node();
        if (count($node_users) > 0) {
            $user_node = $node_users->first();
            return $user_node->id;
        } else {
            return 0;
        }
    }

    public static function createLevel($input_data, $user_id)
    {
        if ($input_data != null) {

            $data = $input_data;
            //book level creation
            $level = new book_dis_node_level();
            $level->title = $data['title'];
            $level->code = $data['code'];
            $level->description = $data['description'];
            if (isset($data['parent_id'])) {
                if ($data['parent_id'] != 0) {
                    $level->parent()->associate($data['parent_id']);
                }
            }
            $level->creator_user()->associate($user_id);
            $level->save();

            if ($level->id != 0) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public static function getNodes($level_id)
    {
        if ($level_id != null) {
            //book level creation
            $level = book_dis_node_level::findOrFail($level_id);
            $main_level = $level->parent()->get();
            if (count($main_level) > 0) {
                $main_level = $main_level->first();
            } else {
                return null;
            }
            if ($main_level != null) {
                return $main_level->nodes()->get();
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public static function getGradeList()
    {

        $grades = book_dis_meta_grade::all();
        $result = array();
        if ($grades != null) {
            foreach ($grades as $grade) {
                $titles = $grade->titles()->get();
                if ($titles != null) {
                } else {
                    $titles = array();
                }
                array_push($result, ["grade" => $grade, "titles" => $titles]);
            }
            return $result;
        } else {
            return null;
        }
    }

    public static function receipt_level_list()
    {

        $levels = book_dis_node_level::all();
        return $levels;
    }

    public static function getBalances($user)
    {

        if ($user != null) {

            $nodes = $user->work_node();
            if (count($nodes) > 0) {
                $node = $nodes->first();
                $query_string = "
                select bbn.*,
                    ( select node.`title` from `book_dis_nodes` as node
                            where node.id = (select bds.to from `book_dis_shipments` as bds where bds.id = (select bdt.`source_id` from `book_dis_node_transactions` as bdt where bdt.id = bbn.`transaction_id`))

                    ) as to_title,
                    ( select node.`title` from `book_dis_nodes` as node
                            where node.id = (select bds.from from `book_dis_shipments` as bds where bds.id = (select bdt.`source_id` from `book_dis_node_transactions` as bdt where bdt.id = bbn.`transaction_id`))

                    ) as from_title

                    from `book_dis_node_balances` as bbn where bbn.`node_id`= " . $node->id . "
                    ORDER BY bbn.`created_at` DESC
                    LIMIT 5";

                $balance_result = DB::select(DB::raw($query_string));
                if (count($balance_result) > 0) {
                    return $balance_result;
                } else {
                    return array();
                }
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    public static function getBalancesDetail($user)
    {

        if ($user != null) {

            $nodes = $user->work_node();
            if (count($nodes) > 0) {
                $node = $nodes->first();
                $query_string = "

                            select

                            (select bdmt.`title` from `book_dis_meta_titles` as bdmt where bdmt.id = bdnbd.`title_id`) as title,
                            (select bdmg.`title` from `book_dis_meta_grades` as bdmg where bdmg.id = bdnbd.`grade_id`) as grade,
                            (select bdtl.title from `book_dis_title_languages` as bdtl where bdtl.id = bdnbd.`language_id`) as lang,
                            bdnbd.`total`

                            from `book_dis_node_balance_details` as bdnbd where bdnbd.`node_id` = " . $node->id . "

                            ORDER by bdnbd.`grade_id` ASC

                   ";

                $balance_result = DB::select(DB::raw($query_string));
                if (count($balance_result) > 0) {
                    return $balance_result;
                } else {
                    return array();
                }
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    public static function getGroupNode($user)
    {

        if ($user != null) {

            $nodes = $user->group_node()->get();
            if (count($nodes) > 0) {
                return $nodes;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    public static function getReceives($page, $url, $user)
    {

        if ($user != null) {

            $nodes = $user->work_node();
            if (count($nodes) > 0) {
                $node = $nodes->first();
                $query_string = "
                select bds.`title`,bds.`description`, bds.`send_date` as send_date,
                    (select bn.`title` from `book_dis_nodes` as bn where bn.id = bds.to) as To_title,
                    (select bn.`title` from `book_dis_nodes` as bn where bn.id = bds.from) as From_title,
                    (select usr.name from users as usr where usr.id = bds.`creator_id`) as sender_name,
                    (select bdsr.`received` from `book_dis_shipment_recieves` as bdsr where bdsr.`book_dis_shipments_id` = bds.id LIMIT 1) as recieved,
                    (select bdsr.id from `book_dis_shipment_recieves` as bdsr where bdsr.`book_dis_shipments_id` = bds.id LIMIT 1) as recieved_id,
                    (select count(bdsfiles.id) from `book_dis_shipment_files` as bdsfiles where bdsfiles.`book_dis_shipments_id` = bds.id) as docs
                     from `book_dis_shipments` as bds
                     where bds.to = " . $node->id . "
                     and bds.`to_beneficiary` = 0
                    ORDER BY bds.`created_at` DESC";

                $balance_result = DB::select(DB::raw($query_string));
                $perPage = 3;
                $offset = ($page * $perPage) - $perPage;

                $balance_result = new LengthAwarePaginator(
                    array_slice($balance_result, $offset, $perPage, true),
                    count($balance_result),
                    $perPage,
                    $page
                );
                return $balance_result;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    // added by zabeeh

    public static function getAllSchoolsForDistrict($provinceId, $districtId)
    {
        if ($provinceId != null && $districtId != null) {
            $query = "SELECT * FROM book_dis_nodes WHERE level_id = 15 AND province = " . $provinceId . " AND district = " . $districtId . ";";

            $data = DB::select(DB::raw($query));

            if (count($data) > 0) {
                return $data;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    public static function getAllUsersForDistrict($provinceId, $districtId)
    {
        if ($provinceId != null && $districtId != null) {
            $query = "select bdnstaffs.node_id, bdnstaffs.user_id, bdn.title as node_name, users.name as user_name, users.email as user_email from book_dis_node_staffs bdnstaffs
                JOIN users on bdnstaffs.user_id = users.id
                JOIN book_dis_nodes bdn on bdnstaffs.node_id = bdn.id
                where bdnstaffs.node_id = (select id from book_dis_nodes where level_id = 14 and province = " . $provinceId . " and district = " . $districtId . ");";

            $data = DB::select(DB::raw($query));

            if (count($data) > 0) {
                return $data;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    public static function getAllUsersForNode($nodeId)
    {
        if ($nodeId != null) {
            $query = "select bdnstaffs.node_id, bdnstaffs.user_id, bdn.title as node_name, users.name as user_name, users.email as user_email from book_dis_node_staffs bdnstaffs
                JOIN users on bdnstaffs.user_id = users.id
                JOIN book_dis_nodes bdn on bdnstaffs.node_id = bdn.id
                where bdnstaffs.node_id = " . $nodeId . ";";

            $data = DB::select(DB::raw($query));

            if (count($data) > 0) {
                return $data;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    public static function getAllNodesForUser($userId)
    {
        if ($userId != null) {
            $query = "select bdnstaffs.node_id, bdn.title as node_title, bdn.level_id, bdnl.title as level_title, bdnstaffs.user_id from book_dis_node_staffs bdnstaffs
                    JOIN book_dis_nodes bdn on bdnstaffs.node_id = bdn.id
                    JOIN book_dis_node_levels bdnl on bdn.level_id = bdnl.id
                    where bdnstaffs.user_id = " . $userId . ";";

            $data = DB::select(DB::raw($query));

            if (count($data) > 0) {
                return $data;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    public static function getAllSchoolNodesForUser($userId)
    {
        if ($userId != null) {
            $query = "select bdnsg.node_id, bdn.title as node_title, bdn.level_id, bdnl.title as level_title, bdnsg.user_id from book_dis_node_staffs_groups bdnsg
                        JOIN book_dis_nodes bdn on bdnsg.node_id = bdn.id
                        JOIN book_dis_node_levels bdnl on bdn.level_id = bdnl.id
                        where bdnsg.user_id = " . $userId . ";";

            $data = DB::select(DB::raw($query));

            if (count($data) > 0) {
                return $data;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    public static function searchUsersByTerm($searchTerm)
    {
        if ($searchTerm != null) {
            $query = "SELECT id, name, email FROM users WHERE email LIKE '%" . $searchTerm . "%'
                OR name LIKE '%" . $searchTerm . "%' LIMIT 5;";

            $data = DB::select(DB::raw($query));

            if (count($data) > 0) {
                return $data;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    public static function getAssignedSchoolsForDistrictUser($userId)
    {
        if ($userId != null) {
            $query = "SELECT bdnsg.node_id, bdn.title as node_title, bdnsg.user_id from book_dis_node_staffs_groups bdnsg
                JOIN book_dis_nodes bdn on bdnsg.node_id = bdn.id
                WHERE bdnsg.user_id = " . $userId . ";";

            $data = DB::select(DB::raw($query));

            if (count($data) > 0) {
                return $data;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    public static function assignSchoolsToDistrictUser($input)
    {
        if ($input['assignSchools'] != null) {
            try {

                foreach ($input['assignSchools'] as $value) {
                    $record = DB::table('book_dis_node_staffs_groups')
                        ->where('node_id', $value)
                        ->where('user_id', $input['user_id'])
                        ->first();

                    if (empty($record)) {
                        DB::table('book_dis_node_staffs_groups')->insert(
                            ['node_id' => $value, 'user_id' => $input['user_id'], 'active' => 1]
                        );
                    }
                }

                return 1;
            } catch (\Illuminate\Database\QueryException $ex) {
                return $ex->getMessage();
            }
        }
    }

    public static function unassignNodeFromUser($nodeId, $userId)
    {
        if ($nodeId != null && $userId != null) {
            try {
                $record = DB::table('book_dis_node_staffs')
                    ->where('node_id', $nodeId)
                    ->where('user_id', $userId)
                    ->delete();

                return 1;
            } catch (\Illuminate\Database\QueryException $ex) {
                return $ex->getMessage();
            }
        }
    }

    public static function assignNodeToUser($nodeId, $userId)
    {
        if ($nodeId != null && $userId != null) {
            try {
                $record = DB::table('book_dis_node_staffs')
                    ->where('node_id', $nodeId)
                    ->where('user_id', $userId)
                    ->first();

                if (empty($record)) {
                    DB::table('book_dis_node_staffs')->insert(
                        ['node_id' => $nodeId, 'user_id' => $userId]
                    );
                }

                return 1;
            } catch (\Illuminate\Database\QueryException $ex) {
                return $ex->getMessage();
            }
        }
    }

    public static function unassignSchoolsFromDistrictUser($input)
    {
        if ($input['unassignSchools'] != null) {
            try {

                foreach ($input['unassignSchools'] as $value) {
                    $record = DB::table('book_dis_node_staffs_groups')
                        ->where('node_id', $value)
                        ->where('user_id', $input['user_id'])
                        ->delete();
                }

                return 1;
            } catch (\Illuminate\Database\QueryException $ex) {
                return $ex->getMessage();
            }
        }
    }

    public static function getSelectedDistrictTabularRecords($request)
    {
        if ($request->provinceId != null) {
            DB::statement('SET @levelId := 14');
            DB::statement('SET @provinceId := ' . $request->provinceId);
            DB::statement('SET @districtId := ' . $request->districtId);
            DB::statement('SET @startDate := "' . $request->startDate . '"');
            DB::statement('SET @endDate := "' . $request->endDate . '"');

            $query = "
            SELECT SentTable.Province as Province, SentTable.District as District, SentTable.Grade as Grade, SentTable.Lang as Lang, SentTable.Title as Title,
            SentTable.TotalSent, RcvdTable.TotalReceived, DmgsTable.TotalDamages, LostsTable.TotalLosts, DistributedTable.TotalDistributed, BalancesTable.RemainingBalance
            FROM

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,
                (SELECT en_name from districts where id = bdnto.district) as District,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsd.total) as TotalSent

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_details bdsd on bds.id = bdsd.book_dis_shipments_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsd.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsd.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsd.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId AND bdn.district = @districtId) AND (bds.to_beneficiary = 0 AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, Province, District) as SentTable

                LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,
                (SELECT en_name from districts where id = bdnto.district) as District,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsrd.total) as TotalReceived

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_recieves bdsr on bds.id = bdsr.book_dis_shipments_id
                JOIN book_dis_shipment_recieve_details bdsrd on bdsr.id = bdsrd.book_dis_receive_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsrd.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsrd.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsrd.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId AND bdn.district = @districtId) AND (bds.to_beneficiary = 0 AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, Province, District) as RcvdTable

                ON (SentTable.District = RcvdTable.District) AND (SentTable.Grade = RcvdTable.Grade) AND (SentTable.Lang = RcvdTable.Lang) AND (SentTable.Title = RcvdTable.Title)


            LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,
                (SELECT en_name from districts where id = bdnto.district) as District,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsrdmgs.total) as TotalDamages

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_recieves bdsr on bds.id = bdsr.book_dis_shipments_id
                JOIN book_dis_shipment_recieve_dmgs bdsrdmgs on bdsr.id = bdsrdmgs.book_dis_receive_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsrdmgs.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsrdmgs.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsrdmgs.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId AND bdn.district = @districtId) AND (bds.to_beneficiary = 0 AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, Province, District) as DmgsTable


                ON (SentTable.District = DmgsTable.District) AND (SentTable.Grade = DmgsTable.Grade) AND (SentTable.Lang = DmgsTable.Lang) AND (SentTable.Title = DmgsTable.Title)


            LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,
                (SELECT en_name from districts where id = bdnto.district) as District,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsrlosts.total) as TotalLosts

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_recieves bdsr on bds.id = bdsr.book_dis_shipments_id
                JOIN book_dis_shipment_recieve_losts bdsrlosts on bdsr.id = bdsrlosts.book_dis_receive_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsrlosts.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsrlosts.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsrlosts.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId AND bdn.district = @districtId) AND (bds.to_beneficiary = 0 AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, Province, District) as LostsTable


                ON (SentTable.District = LostsTable.District) AND (SentTable.Grade = LostsTable.Grade) AND (SentTable.Lang = LostsTable.Lang) AND (SentTable.Title = LostsTable.Title)


            LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,
                (SELECT en_name from districts where id = bdnto.district) as District,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsd.total) as TotalDistributed

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_details bdsd on bds.id = bdsd.book_dis_shipments_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsd.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsd.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsd.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = 15 AND bdn.province = @provinceId AND bdn.district = @districtId) AND (bds.to_beneficiary = 1 AND bds.to_beneficiary_type = 1 AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, Province, District) as DistributedTable


                ON (SentTable.District = DistributedTable.District) AND (SentTable.Grade = DistributedTable.Grade) AND (SentTable.Lang = DistributedTable.Lang) AND (SentTable.Title = DistributedTable.Title)



                LEFT JOIN

                (SELECT
                (SELECT en_name from provinces where id = bdn.province) as Province,
                (SELECT en_name from districts where id = bdn.district) as District,
                grades.title as Grade, langs.title as Lang, titles.title as Title, blnc.total as RemainingBalance

            FROM book_dis_node_balance_details blnc
                JOIN book_dis_nodes bdn on blnc.node_id = bdn.id
                JOIN book_dis_meta_grades grades on blnc.grade_id = grades.id
                JOIN book_dis_meta_titles titles on blnc.title_id = titles.id
                JOIN book_dis_title_languages langs on blnc.language_id = langs.id
            WHERE
                blnc.node_id IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId AND bdn.district = @districtId)) as BalancesTable

                ON (SentTable.District = BalancesTable.District) AND (SentTable.Grade = BalancesTable.Grade) AND (SentTable.Lang = BalancesTable.Lang) AND (SentTable.Title = BalancesTable.Title)

                GROUP BY Title, Grade, Lang, Province, District
                ORDER By District, CASE
                    WHEN SentTable.Grade='Grade One' THEN 1
                    WHEN SentTable.Grade='Grade Two' THEN 2
                    WHEN SentTable.Grade='Grade Three' THEN 3
                    WHEN SentTable.Grade='Grade Four' THEN 4
                    WHEN SentTable.Grade='Grade Five' THEN 5
                    WHEN SentTable.Grade='Grade Six' THEN 6
                    WHEN SentTable.Grade='Grade Seven' THEN 7
                    WHEN SentTable.Grade='Grade Eight' THEN 8
                    WHEN SentTable.Grade='Grade Nine' THEN 9
                    WHEN SentTable.Grade='Grade Ten' THEN 10
                    WHEN SentTable.Grade='Grade Eleven' THEN 11
                    WHEN SentTable.Grade='Grade Twelve' THEN 12
                END ASC;
            ";

            $data = DB::select(DB::raw($query));

            if (count($data) > 0) {
                return $data;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    public static function getSelectedDistrictTabularRecordsByProject($request)
    {
        if ($request->provinceId != null) {
            DB::statement('SET @projectId := ' . $request->projectId);
            DB::statement('SET @levelId := 14');
            DB::statement('SET @provinceId := ' . $request->provinceId);
            DB::statement('SET @districtId := ' . $request->districtId);
            DB::statement('SET @startDate := "' . $request->startDate . '"');
            DB::statement('SET @endDate := "' . $request->endDate . '"');

            $query = "
            SELECT SentTable.Province as Province, SentTable.District as District, SentTable.Grade as Grade, SentTable.Lang as Lang, SentTable.Title as Title,
            SentTable.TotalSent, RcvdTable.TotalReceived, DmgsTable.TotalDamages, LostsTable.TotalLosts, DistributedTable.TotalDistributed, BalancesTable.RemainingBalance
            FROM

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,
                (SELECT en_name from districts where id = bdnto.district) as District,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsd.total) as TotalSent

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_details bdsd on bds.id = bdsd.book_dis_shipments_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsd.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsd.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsd.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId AND bdn.district = @districtId) AND (bds.to_beneficiary = 0 AND bds.project_id=@projectId AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, Province, District) as SentTable

                LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,
                (SELECT en_name from districts where id = bdnto.district) as District,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsrd.total) as TotalReceived

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_recieves bdsr on bds.id = bdsr.book_dis_shipments_id
                JOIN book_dis_shipment_recieve_details bdsrd on bdsr.id = bdsrd.book_dis_receive_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsrd.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsrd.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsrd.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId AND bdn.district = @districtId) AND (bds.to_beneficiary = 0 AND bds.project_id=@projectId AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, Province, District) as RcvdTable

                ON (SentTable.District = RcvdTable.District) AND (SentTable.Grade = RcvdTable.Grade) AND (SentTable.Lang = RcvdTable.Lang) AND (SentTable.Title = RcvdTable.Title)


            LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,
                (SELECT en_name from districts where id = bdnto.district) as District,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsrdmgs.total) as TotalDamages

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_recieves bdsr on bds.id = bdsr.book_dis_shipments_id
                JOIN book_dis_shipment_recieve_dmgs bdsrdmgs on bdsr.id = bdsrdmgs.book_dis_receive_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsrdmgs.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsrdmgs.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsrdmgs.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId AND bdn.district = @districtId) AND (bds.to_beneficiary = 0 AND bds.project_id=@projectId AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, Province, District) as DmgsTable


                ON (SentTable.District = DmgsTable.District) AND (SentTable.Grade = DmgsTable.Grade) AND (SentTable.Lang = DmgsTable.Lang) AND (SentTable.Title = DmgsTable.Title)


            LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,
                (SELECT en_name from districts where id = bdnto.district) as District,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsrlosts.total) as TotalLosts

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_recieves bdsr on bds.id = bdsr.book_dis_shipments_id
                JOIN book_dis_shipment_recieve_losts bdsrlosts on bdsr.id = bdsrlosts.book_dis_receive_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsrlosts.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsrlosts.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsrlosts.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId AND bdn.district = @districtId) AND (bds.to_beneficiary = 0 AND bds.project_id=@projectId AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, Province, District) as LostsTable


                ON (SentTable.District = LostsTable.District) AND (SentTable.Grade = LostsTable.Grade) AND (SentTable.Lang = LostsTable.Lang) AND (SentTable.Title = LostsTable.Title)


            LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,
                (SELECT en_name from districts where id = bdnto.district) as District,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsd.total) as TotalDistributed

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_details bdsd on bds.id = bdsd.book_dis_shipments_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsd.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsd.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsd.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = 15 AND bdn.province = @provinceId AND bdn.district = @districtId) AND (bds.to_beneficiary = 1 AND bds.to_beneficiary_type = 1 AND bds.project_id=@projectId AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, Province, District) as DistributedTable


                ON (SentTable.District = DistributedTable.District) AND (SentTable.Grade = DistributedTable.Grade) AND (SentTable.Lang = DistributedTable.Lang) AND (SentTable.Title = DistributedTable.Title)



                LEFT JOIN

                (SELECT
                (SELECT en_name from provinces where id = bdn.province) as Province,
                (SELECT en_name from districts where id = bdn.district) as District,
                grades.title as Grade, langs.title as Lang, titles.title as Title, blnc.total as RemainingBalance

            FROM book_dis_node_balance_details blnc
                JOIN book_dis_nodes bdn on blnc.node_id = bdn.id
                JOIN book_dis_meta_grades grades on blnc.grade_id = grades.id
                JOIN book_dis_meta_titles titles on blnc.title_id = titles.id
                JOIN book_dis_title_languages langs on blnc.language_id = langs.id
            WHERE
                blnc.node_id IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId AND bdn.district = @districtId)) as BalancesTable

                ON (SentTable.District = BalancesTable.District) AND (SentTable.Grade = BalancesTable.Grade) AND (SentTable.Lang = BalancesTable.Lang) AND (SentTable.Title = BalancesTable.Title)

                GROUP BY Title, Grade, Lang, Province, District
                ORDER By District, CASE
                    WHEN SentTable.Grade='Grade One' THEN 1
                    WHEN SentTable.Grade='Grade Two' THEN 2
                    WHEN SentTable.Grade='Grade Three' THEN 3
                    WHEN SentTable.Grade='Grade Four' THEN 4
                    WHEN SentTable.Grade='Grade Five' THEN 5
                    WHEN SentTable.Grade='Grade Six' THEN 6
                    WHEN SentTable.Grade='Grade Seven' THEN 7
                    WHEN SentTable.Grade='Grade Eight' THEN 8
                    WHEN SentTable.Grade='Grade Nine' THEN 9
                    WHEN SentTable.Grade='Grade Ten' THEN 10
                    WHEN SentTable.Grade='Grade Eleven' THEN 11
                    WHEN SentTable.Grade='Grade Twelve' THEN 12
                END ASC;
            ";

            $data = DB::select(DB::raw($query));

            if (count($data) > 0) {
                return $data;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    public static function getDistrictSchoolsTabularRecords($request)
    {
        if ($request->provinceId != null) {
            DB::statement('SET @levelId := 15');
            DB::statement('SET @provinceId := ' . $request->provinceId);
            DB::statement('SET @districtId := ' . $request->districtId);
            DB::statement('SET @startDate := "' . $request->startDate . '"');
            DB::statement('SET @endDate := "' . $request->endDate . '"');

            $query = "
            SELECT SentTable.Province as Province, SentTable.District as District, SentTable.School as School, SentTable.Grade as Grade, SentTable.Lang as Lang, SentTable.Title as Title,
            SentTable.TotalSent, RcvdTable.TotalReceived, DmgsTable.TotalDamages, LostsTable.TotalLosts, DistributedTable.TotalDistributed, BalancesTable.RemainingBalance
            FROM

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,
                (SELECT en_name from districts where id = bdnto.district) as District,
                bdnto.title as School,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsd.total) as TotalSent

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_details bdsd on bds.id = bdsd.book_dis_shipments_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsd.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsd.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsd.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId AND bdn.district = @districtId) AND (bds.to_beneficiary = 0 AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, School, Province, District) as SentTable

                LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,
                (SELECT en_name from districts where id = bdnto.district) as District,
                bdnto.title as School,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsrd.total) as TotalReceived

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_recieves bdsr on bds.id = bdsr.book_dis_shipments_id
                JOIN book_dis_shipment_recieve_details bdsrd on bdsr.id = bdsrd.book_dis_receive_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsrd.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsrd.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsrd.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId AND bdn.district = @districtId) AND (bds.to_beneficiary = 0 AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, School, Province, District) as RcvdTable

                ON (SentTable.School = RcvdTable.School) AND  (SentTable.District = RcvdTable.District) AND (SentTable.Grade = RcvdTable.Grade) AND (SentTable.Lang = RcvdTable.Lang) AND (SentTable.Title = RcvdTable.Title)


            LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,
                (SELECT en_name from districts where id = bdnto.district) as District,
                bdnto.title as School,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsrdmgs.total) as TotalDamages

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_recieves bdsr on bds.id = bdsr.book_dis_shipments_id
                JOIN book_dis_shipment_recieve_dmgs bdsrdmgs on bdsr.id = bdsrdmgs.book_dis_receive_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsrdmgs.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsrdmgs.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsrdmgs.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId AND bdn.district = @districtId) AND (bds.to_beneficiary = 0 AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, School, Province, District) as DmgsTable


                ON (SentTable.School = DmgsTable.School) AND (SentTable.District = DmgsTable.District) AND (SentTable.Grade = DmgsTable.Grade) AND (SentTable.Lang = DmgsTable.Lang) AND (SentTable.Title = DmgsTable.Title)


            LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,
                (SELECT en_name from districts where id = bdnto.district) as District,
                bdnto.title as School,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsrlosts.total) as TotalLosts

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_recieves bdsr on bds.id = bdsr.book_dis_shipments_id
                JOIN book_dis_shipment_recieve_losts bdsrlosts on bdsr.id = bdsrlosts.book_dis_receive_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsrlosts.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsrlosts.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsrlosts.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId AND bdn.district = @districtId) AND (bds.to_beneficiary = 0 AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, School, Province, District) as LostsTable


                ON (SentTable.School = LostsTable.School) AND (SentTable.District = LostsTable.District) AND (SentTable.Grade = LostsTable.Grade) AND (SentTable.Lang = LostsTable.Lang) AND (SentTable.Title = LostsTable.Title)


            LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,
                (SELECT en_name from districts where id = bdnto.district) as District,
                bdnto.title as School,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsd.total) as TotalDistributed

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_details bdsd on bds.id = bdsd.book_dis_shipments_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsd.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsd.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsd.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = 15 AND bdn.province = @provinceId AND bdn.district = @districtId) AND (bds.to_beneficiary = 1 AND bds.to_beneficiary_type = 1 AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, School, Province, District) as DistributedTable


                ON (SentTable.School = DistributedTable.School) AND (SentTable.District = DistributedTable.District) AND (SentTable.Grade = DistributedTable.Grade) AND (SentTable.Lang = DistributedTable.Lang) AND (SentTable.Title = DistributedTable.Title)



                LEFT JOIN

                (SELECT
                (SELECT en_name from provinces where id = bdn.province) as Province,
                (SELECT en_name from districts where id = bdn.district) as District,
                bdn.title as School,
                grades.title as Grade, langs.title as Lang, titles.title as Title, blnc.total as RemainingBalance

            FROM book_dis_node_balance_details blnc
                JOIN book_dis_nodes bdn on blnc.node_id = bdn.id
                JOIN book_dis_meta_grades grades on blnc.grade_id = grades.id
                JOIN book_dis_meta_titles titles on blnc.title_id = titles.id
                JOIN book_dis_title_languages langs on blnc.language_id = langs.id
            WHERE
                blnc.node_id IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId AND bdn.district = @districtId)) as BalancesTable

                ON (SentTable.School = BalancesTable.School) AND (SentTable.District = BalancesTable.District) AND (SentTable.Grade = BalancesTable.Grade) AND (SentTable.Lang = BalancesTable.Lang) AND (SentTable.Title = BalancesTable.Title)

                GROUP BY Title, Grade, Lang, School, Province, District
                ORDER By District, School, CASE
                    WHEN SentTable.Grade='Grade One' THEN 1
                    WHEN SentTable.Grade='Grade Two' THEN 2
                    WHEN SentTable.Grade='Grade Three' THEN 3
                    WHEN SentTable.Grade='Grade Four' THEN 4
                    WHEN SentTable.Grade='Grade Five' THEN 5
                    WHEN SentTable.Grade='Grade Six' THEN 6
                    WHEN SentTable.Grade='Grade Seven' THEN 7
                    WHEN SentTable.Grade='Grade Eight' THEN 8
                    WHEN SentTable.Grade='Grade Nine' THEN 9
                    WHEN SentTable.Grade='Grade Ten' THEN 10
                    WHEN SentTable.Grade='Grade Eleven' THEN 11
                    WHEN SentTable.Grade='Grade Twelve' THEN 12
                END ASC;
            ";

            $data = DB::select(DB::raw($query));

            if (count($data) > 0) {
                return $data;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    public static function getDistrictSchoolsTabularRecordsByProject($request)
    {
        if ($request->provinceId != null) {
            DB::statement('SET @projectId := ' . $request->projectId);
            DB::statement('SET @levelId := 15');
            DB::statement('SET @provinceId := ' . $request->provinceId);
            DB::statement('SET @districtId := ' . $request->districtId);
            DB::statement('SET @startDate := "' . $request->startDate . '"');
            DB::statement('SET @endDate := "' . $request->endDate . '"');

            $query = "
            SELECT SentTable.Province as Province, SentTable.District as District, SentTable.School as School, SentTable.Grade as Grade, SentTable.Lang as Lang, SentTable.Title as Title,
            SentTable.TotalSent, RcvdTable.TotalReceived, DmgsTable.TotalDamages, LostsTable.TotalLosts, DistributedTable.TotalDistributed, BalancesTable.RemainingBalance
            FROM

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,
                (SELECT en_name from districts where id = bdnto.district) as District,
                bdnto.title as School,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsd.total) as TotalSent

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_details bdsd on bds.id = bdsd.book_dis_shipments_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsd.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsd.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsd.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId AND bdn.district = @districtId) AND (bds.to_beneficiary = 0 AND bds.project_id=@projectId AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, School, Province, District) as SentTable

                LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,
                (SELECT en_name from districts where id = bdnto.district) as District,
                bdnto.title as School,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsrd.total) as TotalReceived

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_recieves bdsr on bds.id = bdsr.book_dis_shipments_id
                JOIN book_dis_shipment_recieve_details bdsrd on bdsr.id = bdsrd.book_dis_receive_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsrd.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsrd.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsrd.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId AND bdn.district = @districtId) AND (bds.to_beneficiary = 0 AND bds.project_id=@projectId AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, School, Province, District) as RcvdTable

                ON (SentTable.School = RcvdTable.School) AND  (SentTable.District = RcvdTable.District) AND (SentTable.Grade = RcvdTable.Grade) AND (SentTable.Lang = RcvdTable.Lang) AND (SentTable.Title = RcvdTable.Title)


            LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,
                (SELECT en_name from districts where id = bdnto.district) as District,
                bdnto.title as School,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsrdmgs.total) as TotalDamages

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_recieves bdsr on bds.id = bdsr.book_dis_shipments_id
                JOIN book_dis_shipment_recieve_dmgs bdsrdmgs on bdsr.id = bdsrdmgs.book_dis_receive_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsrdmgs.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsrdmgs.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsrdmgs.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId AND bdn.district = @districtId) AND (bds.to_beneficiary = 0 AND bds.project_id=@projectId AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, School, Province, District) as DmgsTable


                ON (SentTable.School = DmgsTable.School) AND (SentTable.District = DmgsTable.District) AND (SentTable.Grade = DmgsTable.Grade) AND (SentTable.Lang = DmgsTable.Lang) AND (SentTable.Title = DmgsTable.Title)


            LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,
                (SELECT en_name from districts where id = bdnto.district) as District,
                bdnto.title as School,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsrlosts.total) as TotalLosts

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_recieves bdsr on bds.id = bdsr.book_dis_shipments_id
                JOIN book_dis_shipment_recieve_losts bdsrlosts on bdsr.id = bdsrlosts.book_dis_receive_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsrlosts.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsrlosts.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsrlosts.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId AND bdn.district = @districtId) AND (bds.to_beneficiary = 0 AND bds.project_id=@projectId AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, School, Province, District) as LostsTable


                ON (SentTable.School = LostsTable.School) AND (SentTable.District = LostsTable.District) AND (SentTable.Grade = LostsTable.Grade) AND (SentTable.Lang = LostsTable.Lang) AND (SentTable.Title = LostsTable.Title)


            LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,
                (SELECT en_name from districts where id = bdnto.district) as District,
                bdnto.title as School,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsd.total) as TotalDistributed

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_details bdsd on bds.id = bdsd.book_dis_shipments_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsd.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsd.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsd.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = 15 AND bdn.province = @provinceId AND bdn.district = @districtId) AND (bds.to_beneficiary = 1 AND bds.to_beneficiary_type = 1 AND bds.project_id=@projectId AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, School, Province, District) as DistributedTable


                ON (SentTable.School = DistributedTable.School) AND (SentTable.District = DistributedTable.District) AND (SentTable.Grade = DistributedTable.Grade) AND (SentTable.Lang = DistributedTable.Lang) AND (SentTable.Title = DistributedTable.Title)



                LEFT JOIN

                (SELECT
                (SELECT en_name from provinces where id = bdn.province) as Province,
                (SELECT en_name from districts where id = bdn.district) as District,
                bdn.title as School,
                grades.title as Grade, langs.title as Lang, titles.title as Title, blnc.total as RemainingBalance

            FROM book_dis_node_balance_details blnc
                JOIN book_dis_nodes bdn on blnc.node_id = bdn.id
                JOIN book_dis_meta_grades grades on blnc.grade_id = grades.id
                JOIN book_dis_meta_titles titles on blnc.title_id = titles.id
                JOIN book_dis_title_languages langs on blnc.language_id = langs.id
            WHERE
                blnc.node_id IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId AND bdn.district = @districtId)) as BalancesTable

                ON (SentTable.School = BalancesTable.School) AND (SentTable.District = BalancesTable.District) AND (SentTable.Grade = BalancesTable.Grade) AND (SentTable.Lang = BalancesTable.Lang) AND (SentTable.Title = BalancesTable.Title)

                GROUP BY Title, Grade, Lang, School, Province, District
                ORDER By District, School, CASE
                    WHEN SentTable.Grade='Grade One' THEN 1
                    WHEN SentTable.Grade='Grade Two' THEN 2
                    WHEN SentTable.Grade='Grade Three' THEN 3
                    WHEN SentTable.Grade='Grade Four' THEN 4
                    WHEN SentTable.Grade='Grade Five' THEN 5
                    WHEN SentTable.Grade='Grade Six' THEN 6
                    WHEN SentTable.Grade='Grade Seven' THEN 7
                    WHEN SentTable.Grade='Grade Eight' THEN 8
                    WHEN SentTable.Grade='Grade Nine' THEN 9
                    WHEN SentTable.Grade='Grade Ten' THEN 10
                    WHEN SentTable.Grade='Grade Eleven' THEN 11
                    WHEN SentTable.Grade='Grade Twelve' THEN 12
                END ASC;
            ";

            $data = DB::select(DB::raw($query));

            if (count($data) > 0) {
                return $data;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    public static function getProvinceDistrictsTabularRecords($request)
    {
        if ($request->provinceId != null) {
            DB::statement('SET @levelId := 14');
            DB::statement('SET @provinceId := ' . $request->provinceId);
            DB::statement('SET @startDate := "' . $request->startDate . '"');
            DB::statement('SET @endDate := "' . $request->endDate . '"');

            $query = "
            SELECT SentTable.Province as Province, SentTable.District as District, SentTable.Grade as Grade, SentTable.Lang as Lang, SentTable.Title as Title,
            SentTable.TotalSent, RcvdTable.TotalReceived, DmgsTable.TotalDamages, LostsTable.TotalLosts, DistributedTable.TotalDistributed, BalancesTable.RemainingBalance
            FROM

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,
                (SELECT en_name from districts where id = bdnto.district) as District,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsd.total) as TotalSent

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_details bdsd on bds.id = bdsd.book_dis_shipments_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsd.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsd.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsd.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId) AND (bds.to_beneficiary = 0 AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, Province, District) as SentTable

                LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,
                (SELECT en_name from districts where id = bdnto.district) as District,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsrd.total) as TotalReceived

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_recieves bdsr on bds.id = bdsr.book_dis_shipments_id
                JOIN book_dis_shipment_recieve_details bdsrd on bdsr.id = bdsrd.book_dis_receive_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsrd.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsrd.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsrd.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId) AND (bds.to_beneficiary = 0 AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, Province, District) as RcvdTable

                ON (SentTable.District = RcvdTable.District) AND (SentTable.Grade = RcvdTable.Grade) AND (SentTable.Lang = RcvdTable.Lang) AND (SentTable.Title = RcvdTable.Title)


            LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,
                (SELECT en_name from districts where id = bdnto.district) as District,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsrdmgs.total) as TotalDamages

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_recieves bdsr on bds.id = bdsr.book_dis_shipments_id
                JOIN book_dis_shipment_recieve_dmgs bdsrdmgs on bdsr.id = bdsrdmgs.book_dis_receive_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsrdmgs.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsrdmgs.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsrdmgs.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId) AND (bds.to_beneficiary = 0 AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, Province, District) as DmgsTable


                ON (SentTable.District = DmgsTable.District) AND (SentTable.Grade = DmgsTable.Grade) AND (SentTable.Lang = DmgsTable.Lang) AND (SentTable.Title = DmgsTable.Title)


            LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,
                (SELECT en_name from districts where id = bdnto.district) as District,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsrlosts.total) as TotalLosts

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_recieves bdsr on bds.id = bdsr.book_dis_shipments_id
                JOIN book_dis_shipment_recieve_losts bdsrlosts on bdsr.id = bdsrlosts.book_dis_receive_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsrlosts.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsrlosts.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsrlosts.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId) AND (bds.to_beneficiary = 0 AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, Province, District) as LostsTable


                ON (SentTable.District = LostsTable.District) AND (SentTable.Grade = LostsTable.Grade) AND (SentTable.Lang = LostsTable.Lang) AND (SentTable.Title = LostsTable.Title)


            LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,
                (SELECT en_name from districts where id = bdnto.district) as District,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsd.total) as TotalDistributed

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_details bdsd on bds.id = bdsd.book_dis_shipments_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsd.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsd.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsd.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = 15 AND bdn.province = @provinceId) AND (bds.to_beneficiary = 1 AND bds.to_beneficiary_type = 1 AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, Province, District) as DistributedTable


                ON (SentTable.District = DistributedTable.District) AND (SentTable.Grade = DistributedTable.Grade) AND (SentTable.Lang = DistributedTable.Lang) AND (SentTable.Title = DistributedTable.Title)



                LEFT JOIN

                (SELECT
                (SELECT en_name from provinces where id = bdn.province) as Province,
                (SELECT en_name from districts where id = bdn.district) as District,
                grades.title as Grade, langs.title as Lang, titles.title as Title, blnc.total as RemainingBalance

            FROM book_dis_node_balance_details blnc
                JOIN book_dis_nodes bdn on blnc.node_id = bdn.id
                JOIN book_dis_meta_grades grades on blnc.grade_id = grades.id
                JOIN book_dis_meta_titles titles on blnc.title_id = titles.id
                JOIN book_dis_title_languages langs on blnc.language_id = langs.id
            WHERE
                blnc.node_id IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId)) as BalancesTable

                ON (SentTable.District = BalancesTable.District) AND (SentTable.Grade = BalancesTable.Grade) AND (SentTable.Lang = BalancesTable.Lang) AND (SentTable.Title = BalancesTable.Title)

                GROUP BY Title, Grade, Lang, Province, District
                ORDER By District, CASE
                    WHEN SentTable.Grade='Grade One' THEN 1
                    WHEN SentTable.Grade='Grade Two' THEN 2
                    WHEN SentTable.Grade='Grade Three' THEN 3
                    WHEN SentTable.Grade='Grade Four' THEN 4
                    WHEN SentTable.Grade='Grade Five' THEN 5
                    WHEN SentTable.Grade='Grade Six' THEN 6
                    WHEN SentTable.Grade='Grade Seven' THEN 7
                    WHEN SentTable.Grade='Grade Eight' THEN 8
                    WHEN SentTable.Grade='Grade Nine' THEN 9
                    WHEN SentTable.Grade='Grade Ten' THEN 10
                    WHEN SentTable.Grade='Grade Eleven' THEN 11
                    WHEN SentTable.Grade='Grade Twelve' THEN 12
                END ASC;
            ";

            $data = DB::select(DB::raw($query));

            if (count($data) > 0) {
                return $data;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    public static function getProvinceDistrictsTabularRecordsByProject($request)
    {
        if ($request->provinceId != null) {
            DB::statement('SET @projectId := ' . $request->projectId);
            DB::statement('SET @levelId := 14');
            DB::statement('SET @provinceId := ' . $request->provinceId);
            DB::statement('SET @startDate := "' . $request->startDate . '"');
            DB::statement('SET @endDate := "' . $request->endDate . '"');

            $query = "
            SELECT SentTable.Province as Province, SentTable.District as District, SentTable.Grade as Grade, SentTable.Lang as Lang, SentTable.Title as Title,
            SentTable.TotalSent, RcvdTable.TotalReceived, DmgsTable.TotalDamages, LostsTable.TotalLosts, DistributedTable.TotalDistributed, BalancesTable.RemainingBalance
            FROM

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,
                (SELECT en_name from districts where id = bdnto.district) as District,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsd.total) as TotalSent

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_details bdsd on bds.id = bdsd.book_dis_shipments_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsd.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsd.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsd.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId) AND (bds.to_beneficiary = 0 AND bds.project_id=@projectId AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, Province, District) as SentTable

                LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,
                (SELECT en_name from districts where id = bdnto.district) as District,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsrd.total) as TotalReceived

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_recieves bdsr on bds.id = bdsr.book_dis_shipments_id
                JOIN book_dis_shipment_recieve_details bdsrd on bdsr.id = bdsrd.book_dis_receive_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsrd.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsrd.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsrd.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId) AND (bds.to_beneficiary = 0 AND bds.project_id=@projectId AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, Province, District) as RcvdTable

                ON (SentTable.District = RcvdTable.District) AND (SentTable.Grade = RcvdTable.Grade) AND (SentTable.Lang = RcvdTable.Lang) AND (SentTable.Title = RcvdTable.Title)


            LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,
                (SELECT en_name from districts where id = bdnto.district) as District,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsrdmgs.total) as TotalDamages

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_recieves bdsr on bds.id = bdsr.book_dis_shipments_id
                JOIN book_dis_shipment_recieve_dmgs bdsrdmgs on bdsr.id = bdsrdmgs.book_dis_receive_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsrdmgs.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsrdmgs.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsrdmgs.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId) AND (bds.to_beneficiary = 0 AND bds.project_id=@projectId AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, Province, District) as DmgsTable


                ON (SentTable.District = DmgsTable.District) AND (SentTable.Grade = DmgsTable.Grade) AND (SentTable.Lang = DmgsTable.Lang) AND (SentTable.Title = DmgsTable.Title)


            LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,
                (SELECT en_name from districts where id = bdnto.district) as District,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsrlosts.total) as TotalLosts

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_recieves bdsr on bds.id = bdsr.book_dis_shipments_id
                JOIN book_dis_shipment_recieve_losts bdsrlosts on bdsr.id = bdsrlosts.book_dis_receive_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsrlosts.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsrlosts.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsrlosts.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId) AND (bds.to_beneficiary = 0 AND bds.project_id=@projectId AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, Province, District) as LostsTable


                ON (SentTable.District = LostsTable.District) AND (SentTable.Grade = LostsTable.Grade) AND (SentTable.Lang = LostsTable.Lang) AND (SentTable.Title = LostsTable.Title)


            LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,
                (SELECT en_name from districts where id = bdnto.district) as District,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsd.total) as TotalDistributed

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_details bdsd on bds.id = bdsd.book_dis_shipments_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsd.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsd.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsd.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = 15 AND bdn.province = @provinceId) AND (bds.to_beneficiary = 1 AND bds.to_beneficiary_type = 1 AND bds.project_id=@projectId AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, Province, District) as DistributedTable


                ON (SentTable.District = DistributedTable.District) AND (SentTable.Grade = DistributedTable.Grade) AND (SentTable.Lang = DistributedTable.Lang) AND (SentTable.Title = DistributedTable.Title)



                LEFT JOIN

                (SELECT
                (SELECT en_name from provinces where id = bdn.province) as Province,
                (SELECT en_name from districts where id = bdn.district) as District,
                grades.title as Grade, langs.title as Lang, titles.title as Title, blnc.total as RemainingBalance

            FROM book_dis_node_balance_details blnc
                JOIN book_dis_nodes bdn on blnc.node_id = bdn.id
                JOIN book_dis_meta_grades grades on blnc.grade_id = grades.id
                JOIN book_dis_meta_titles titles on blnc.title_id = titles.id
                JOIN book_dis_title_languages langs on blnc.language_id = langs.id
            WHERE
                blnc.node_id IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId)) as BalancesTable

                ON (SentTable.District = BalancesTable.District) AND (SentTable.Grade = BalancesTable.Grade) AND (SentTable.Lang = BalancesTable.Lang) AND (SentTable.Title = BalancesTable.Title)

                GROUP BY Title, Grade, Lang, Province, District
                ORDER By District, CASE
                    WHEN SentTable.Grade='Grade One' THEN 1
                    WHEN SentTable.Grade='Grade Two' THEN 2
                    WHEN SentTable.Grade='Grade Three' THEN 3
                    WHEN SentTable.Grade='Grade Four' THEN 4
                    WHEN SentTable.Grade='Grade Five' THEN 5
                    WHEN SentTable.Grade='Grade Six' THEN 6
                    WHEN SentTable.Grade='Grade Seven' THEN 7
                    WHEN SentTable.Grade='Grade Eight' THEN 8
                    WHEN SentTable.Grade='Grade Nine' THEN 9
                    WHEN SentTable.Grade='Grade Ten' THEN 10
                    WHEN SentTable.Grade='Grade Eleven' THEN 11
                    WHEN SentTable.Grade='Grade Twelve' THEN 12
                END ASC;
            ";

            $data = DB::select(DB::raw($query));

            if (count($data) > 0) {
                return $data;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    public static function getAllProvincesTabularRecords($request)
    {

        DB::statement('SET @levelId := 13');
        DB::statement('SET @startDate := "' . $request->startDate . '"');
        DB::statement('SET @endDate := "' . $request->endDate . '"');

        $query = "
            SELECT SentTable.Province as Province, SentTable.Grade as Grade, SentTable.Lang as Lang,
            SentTable.TotalSent, RcvdTable.TotalReceived, DmgsTable.TotalDamages, LostsTable.TotalLosts, DistributedTable.TotalDistributed, BalancesTable.RemainingBalance
            FROM

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,

                grades.title as Grade, langs.title as Lang, sum(bdsd.total) as TotalSent

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_details bdsd on bds.id = bdsd.book_dis_shipments_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsd.grade_id = grades.id
                JOIN book_dis_title_languages langs on bdsd.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId) AND (bds.to_beneficiary = 0 AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY grades.title, langs.title, Province)
                AS SentTable

                LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,

                grades.title as Grade, langs.title as Lang, sum(bdsrd.total) as TotalReceived

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_recieves bdsr on bds.id = bdsr.book_dis_shipments_id
                JOIN book_dis_shipment_recieve_details bdsrd on bdsr.id = bdsrd.book_dis_receive_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsrd.grade_id = grades.id
                JOIN book_dis_title_languages langs on bdsrd.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId) AND (bds.to_beneficiary = 0 AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY grades.title, langs.title, Province)
                AS RcvdTable

                ON (SentTable.Grade = RcvdTable.Grade) AND (SentTable.Lang = RcvdTable.Lang) AND (SentTable.Province = RcvdTable.Province)


                LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,

                grades.title as Grade, langs.title as Lang, sum(bdsrdmgs.total) as TotalDamages

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_recieves bdsr on bds.id = bdsr.book_dis_shipments_id
                JOIN book_dis_shipment_recieve_dmgs bdsrdmgs on bdsr.id = bdsrdmgs.book_dis_receive_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsrdmgs.grade_id = grades.id
                JOIN book_dis_title_languages langs on bdsrdmgs.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId) AND (bds.to_beneficiary = 0 AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY grades.title, langs.title, Province) as DmgsTable


                ON (SentTable.Grade = DmgsTable.Grade) AND (SentTable.Lang = DmgsTable.Lang) AND (SentTable.Province = DmgsTable.Province)


            LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,

                grades.title as Grade, langs.title as Lang, sum(bdsrlosts.total) as TotalLosts

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_recieves bdsr on bds.id = bdsr.book_dis_shipments_id
                JOIN book_dis_shipment_recieve_losts bdsrlosts on bdsr.id = bdsrlosts.book_dis_receive_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsrlosts.grade_id = grades.id
                JOIN book_dis_title_languages langs on bdsrlosts.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId) AND (bds.to_beneficiary = 0 AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY grades.title, langs.title, Province) as LostsTable


                ON (SentTable.Grade = LostsTable.Grade) AND (SentTable.Lang = LostsTable.Lang) AND (SentTable.Province = LostsTable.Province)


            LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,

                grades.title as Grade, langs.title as Lang, sum(bdsd.total) as TotalDistributed

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_details bdsd on bds.id = bdsd.book_dis_shipments_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsd.grade_id = grades.id
                JOIN book_dis_title_languages langs on bdsd.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = 15) AND (bds.to_beneficiary = 1 AND bds.to_beneficiary_type = 1 AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY grades.title, langs.title, Province) as DistributedTable


                ON (SentTable.Grade = DistributedTable.Grade) AND (SentTable.Lang = DistributedTable.Lang) AND (SentTable.Province = DistributedTable.Province)


                LEFT JOIN

                (SELECT
                (SELECT en_name from provinces where id = bdn.province) as Province,
                grades.title as Grade, langs.title as Lang, blnc.total as RemainingBalance

            FROM book_dis_node_balance_details blnc
                JOIN book_dis_nodes bdn on blnc.node_id = bdn.id
                JOIN book_dis_meta_grades grades on blnc.grade_id = grades.id
                JOIN book_dis_title_languages langs on blnc.language_id = langs.id
            WHERE
                blnc.node_id IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId)) as BalancesTable

                ON (SentTable.Grade = BalancesTable.Grade) AND (SentTable.Lang = BalancesTable.Lang) AND (SentTable.Province = BalancesTable.Province)

                GROUP BY Grade, Lang, Province
            ORDER BY Province, Lang,
                CASE
                    WHEN SentTable.Grade='Grade One' THEN 1
                    WHEN SentTable.Grade='Grade Two' THEN 2
                    WHEN SentTable.Grade='Grade Three' THEN 3
                    WHEN SentTable.Grade='Grade Four' THEN 4
                    WHEN SentTable.Grade='Grade Five' THEN 5
                    WHEN SentTable.Grade='Grade Six' THEN 6
                    WHEN SentTable.Grade='Grade Seven' THEN 7
                    WHEN SentTable.Grade='Grade Eight' THEN 8
                    WHEN SentTable.Grade='Grade Nine' THEN 9
                    WHEN SentTable.Grade='Grade Ten' THEN 10
                    WHEN SentTable.Grade='Grade Eleven' THEN 11
                    WHEN SentTable.Grade='Grade Twelve' THEN 12
                END ASC
            ";

        $data = DB::select(DB::raw($query));

        if (count($data) > 0) {
            return $data;
        } else {
            return array();
        }
    }

    public static function getAllProvincesTabularRecordsByProject($request)
    {

        DB::statement('SET @projectId := ' . $request->projectId);
        DB::statement('SET @levelId := 13');
        DB::statement('SET @startDate := "' . $request->startDate . '"');
        DB::statement('SET @endDate := "' . $request->endDate . '"');

        $query = "
            SELECT SentTable.Province as Province, SentTable.Grade as Grade, SentTable.Lang as Lang,
            SentTable.TotalSent, RcvdTable.TotalReceived, DmgsTable.TotalDamages, LostsTable.TotalLosts, DistributedTable.TotalDistributed, BalancesTable.RemainingBalance
            FROM

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,

                grades.title as Grade, langs.title as Lang, sum(bdsd.total) as TotalSent

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_details bdsd on bds.id = bdsd.book_dis_shipments_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsd.grade_id = grades.id
                JOIN book_dis_title_languages langs on bdsd.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId) AND (bds.to_beneficiary = 0 AND bds.project_id=@projectId AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY grades.title, langs.title, Province)
                AS SentTable

                LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,

                grades.title as Grade, langs.title as Lang, sum(bdsrd.total) as TotalReceived

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_recieves bdsr on bds.id = bdsr.book_dis_shipments_id
                JOIN book_dis_shipment_recieve_details bdsrd on bdsr.id = bdsrd.book_dis_receive_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsrd.grade_id = grades.id
                JOIN book_dis_title_languages langs on bdsrd.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId) AND (bds.to_beneficiary = 0 AND bds.project_id=@projectId AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY grades.title, langs.title, Province)
                AS RcvdTable

                ON (SentTable.Grade = RcvdTable.Grade) AND (SentTable.Lang = RcvdTable.Lang) AND (SentTable.Province = RcvdTable.Province)


                LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,

                grades.title as Grade, langs.title as Lang, sum(bdsrdmgs.total) as TotalDamages

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_recieves bdsr on bds.id = bdsr.book_dis_shipments_id
                JOIN book_dis_shipment_recieve_dmgs bdsrdmgs on bdsr.id = bdsrdmgs.book_dis_receive_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsrdmgs.grade_id = grades.id
                JOIN book_dis_title_languages langs on bdsrdmgs.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId) AND (bds.to_beneficiary = 0 AND bds.project_id=@projectId AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY grades.title, langs.title, Province) as DmgsTable


                ON (SentTable.Grade = DmgsTable.Grade) AND (SentTable.Lang = DmgsTable.Lang) AND (SentTable.Province = DmgsTable.Province)


            LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,

                grades.title as Grade, langs.title as Lang, sum(bdsrlosts.total) as TotalLosts

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_recieves bdsr on bds.id = bdsr.book_dis_shipments_id
                JOIN book_dis_shipment_recieve_losts bdsrlosts on bdsr.id = bdsrlosts.book_dis_receive_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsrlosts.grade_id = grades.id
                JOIN book_dis_title_languages langs on bdsrlosts.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId) AND (bds.to_beneficiary = 0  AND bds.project_id=@projectId AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY grades.title, langs.title, Province) as LostsTable


                ON (SentTable.Grade = LostsTable.Grade) AND (SentTable.Lang = LostsTable.Lang) AND (SentTable.Province = LostsTable.Province)


            LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,

                grades.title as Grade, langs.title as Lang, sum(bdsd.total) as TotalDistributed

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_details bdsd on bds.id = bdsd.book_dis_shipments_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsd.grade_id = grades.id
                JOIN book_dis_title_languages langs on bdsd.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = 15) AND (bds.to_beneficiary = 1 AND bds.to_beneficiary_type = 1  AND bds.project_id=@projectId AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY grades.title, langs.title, Province) as DistributedTable


                ON (SentTable.Grade = DistributedTable.Grade) AND (SentTable.Lang = DistributedTable.Lang) AND (SentTable.Province = DistributedTable.Province)


                LEFT JOIN

                (SELECT
                (SELECT en_name from provinces where id = bdn.province) as Province,
                grades.title as Grade, langs.title as Lang, blnc.total as RemainingBalance

            FROM book_dis_node_balance_details blnc
                JOIN book_dis_nodes bdn on blnc.node_id = bdn.id
                JOIN book_dis_meta_grades grades on blnc.grade_id = grades.id
                JOIN book_dis_title_languages langs on blnc.language_id = langs.id
            WHERE
                blnc.node_id IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId)) as BalancesTable

                ON (SentTable.Grade = BalancesTable.Grade) AND (SentTable.Lang = BalancesTable.Lang) AND (SentTable.Province = BalancesTable.Province)

                GROUP BY Grade, Lang, Province
            ORDER BY Province, Lang,
                CASE
                    WHEN SentTable.Grade='Grade One' THEN 1
                    WHEN SentTable.Grade='Grade Two' THEN 2
                    WHEN SentTable.Grade='Grade Three' THEN 3
                    WHEN SentTable.Grade='Grade Four' THEN 4
                    WHEN SentTable.Grade='Grade Five' THEN 5
                    WHEN SentTable.Grade='Grade Six' THEN 6
                    WHEN SentTable.Grade='Grade Seven' THEN 7
                    WHEN SentTable.Grade='Grade Eight' THEN 8
                    WHEN SentTable.Grade='Grade Nine' THEN 9
                    WHEN SentTable.Grade='Grade Ten' THEN 10
                    WHEN SentTable.Grade='Grade Eleven' THEN 11
                    WHEN SentTable.Grade='Grade Twelve' THEN 12
                END ASC
            ";

        $data = DB::select(DB::raw($query));

        if (count($data) > 0) {
            return $data;
        } else {
            return array();
        }
    }

    public static function getProvinceTabularRecords($request)
    {
        if ($request->provinceId != null) {
            DB::statement('SET @levelId := 13');
            DB::statement('SET @provinceId := ' . $request->provinceId);
            DB::statement('SET @startDate := "' . $request->startDate . '"');
            DB::statement('SET @endDate := "' . $request->endDate . '"');

            $query = "
            SELECT SentTable.Province as Province, SentTable.Grade as Grade, SentTable.Lang as Lang, SentTable.Title as Title,
            SentTable.TotalSent, RcvdTable.TotalReceived, DmgsTable.TotalDamages, LostsTable.TotalLosts, DistributedTable.TotalDistributed, BalancesTable.RemainingBalance
            FROM

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsd.total) as TotalSent

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_details bdsd on bds.id = bdsd.book_dis_shipments_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsd.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsd.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsd.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId) AND (bds.to_beneficiary = 0 AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, Province)
                AS SentTable

                LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsrd.total) as TotalReceived

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_recieves bdsr on bds.id = bdsr.book_dis_shipments_id
                JOIN book_dis_shipment_recieve_details bdsrd on bdsr.id = bdsrd.book_dis_receive_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsrd.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsrd.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsrd.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId) AND (bds.to_beneficiary = 0 AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, Province)
                AS RcvdTable

                ON (SentTable.Grade = RcvdTable.Grade) AND (SentTable.Lang = RcvdTable.Lang) AND (SentTable.Title = RcvdTable.Title)


                LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,
                (SELECT en_name from districts where id = bdnto.district) as District,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsrdmgs.total) as TotalDamages

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_recieves bdsr on bds.id = bdsr.book_dis_shipments_id
                JOIN book_dis_shipment_recieve_dmgs bdsrdmgs on bdsr.id = bdsrdmgs.book_dis_receive_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsrdmgs.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsrdmgs.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsrdmgs.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId) AND (bds.to_beneficiary = 0 AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, Province) as DmgsTable


                ON (SentTable.Grade = DmgsTable.Grade) AND (SentTable.Lang = DmgsTable.Lang) AND (SentTable.Title = DmgsTable.Title)


            LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,
                (SELECT en_name from districts where id = bdnto.district) as District,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsrlosts.total) as TotalLosts

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_recieves bdsr on bds.id = bdsr.book_dis_shipments_id
                JOIN book_dis_shipment_recieve_losts bdsrlosts on bdsr.id = bdsrlosts.book_dis_receive_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsrlosts.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsrlosts.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsrlosts.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId) AND (bds.to_beneficiary = 0 AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, Province) as LostsTable


                ON (SentTable.Grade = LostsTable.Grade) AND (SentTable.Lang = LostsTable.Lang) AND (SentTable.Title = LostsTable.Title)


            LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,
                (SELECT en_name from districts where id = bdnto.district) as District,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsd.total) as TotalDistributed

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_details bdsd on bds.id = bdsd.book_dis_shipments_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsd.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsd.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsd.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = 15 AND bdn.province = @provinceId) AND (bds.to_beneficiary = 1 AND bds.to_beneficiary_type = 1 AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, Province) as DistributedTable


                ON (SentTable.Grade = DistributedTable.Grade) AND (SentTable.Lang = DistributedTable.Lang) AND (SentTable.Title = DistributedTable.Title)


                LEFT JOIN

                (SELECT
                (SELECT en_name from provinces where id = bdn.province) as Province,
                grades.title as Grade, langs.title as Lang, titles.title as Title, blnc.total as RemainingBalance

            FROM book_dis_node_balance_details blnc
                JOIN book_dis_nodes bdn on blnc.node_id = bdn.id
                JOIN book_dis_meta_grades grades on blnc.grade_id = grades.id
                JOIN book_dis_meta_titles titles on blnc.title_id = titles.id
                JOIN book_dis_title_languages langs on blnc.language_id = langs.id
            WHERE
                blnc.node_id IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId)) as BalancesTable

                ON (SentTable.Grade = BalancesTable.Grade) AND (SentTable.Lang = BalancesTable.Lang) AND (SentTable.Title = BalancesTable.Title)

                GROUP BY Title, Grade, Lang, Province
            ORDER BY
                CASE
                    WHEN SentTable.Grade='Grade One' THEN 1
                    WHEN SentTable.Grade='Grade Two' THEN 2
                    WHEN SentTable.Grade='Grade Three' THEN 3
                    WHEN SentTable.Grade='Grade Four' THEN 4
                    WHEN SentTable.Grade='Grade Five' THEN 5
                    WHEN SentTable.Grade='Grade Six' THEN 6
                    WHEN SentTable.Grade='Grade Seven' THEN 7
                    WHEN SentTable.Grade='Grade Eight' THEN 8
                    WHEN SentTable.Grade='Grade Nine' THEN 9
                    WHEN SentTable.Grade='Grade Ten' THEN 10
                    WHEN SentTable.Grade='Grade Eleven' THEN 11
                    WHEN SentTable.Grade='Grade Twelve' THEN 12
                END ASC
            ";

            $data = DB::select(DB::raw($query));

            if (count($data) > 0) {
                return $data;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    public static function getProvinceTabularRecordsByProject($request)
    {
        if ($request->provinceId != null) {
            DB::statement('SET @projectId := ' . $request->projectId);
            DB::statement('SET @levelId := 13');
            DB::statement('SET @provinceId := ' . $request->provinceId);
            DB::statement('SET @startDate := "' . $request->startDate . '"');
            DB::statement('SET @endDate := "' . $request->endDate . '"');

            $query = "
            SELECT SentTable.Province as Province, SentTable.Grade as Grade, SentTable.Lang as Lang, SentTable.Title as Title,
            SentTable.TotalSent, RcvdTable.TotalReceived, DmgsTable.TotalDamages, LostsTable.TotalLosts, DistributedTable.TotalDistributed, BalancesTable.RemainingBalance
            FROM

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsd.total) as TotalSent

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_details bdsd on bds.id = bdsd.book_dis_shipments_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsd.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsd.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsd.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId) AND (bds.to_beneficiary = 0 AND bds.project_id=@projectId  AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, Province)
                AS SentTable

                LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsrd.total) as TotalReceived

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_recieves bdsr on bds.id = bdsr.book_dis_shipments_id
                JOIN book_dis_shipment_recieve_details bdsrd on bdsr.id = bdsrd.book_dis_receive_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsrd.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsrd.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsrd.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId) AND (bds.to_beneficiary = 0  AND bds.project_id=@projectId AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, Province)
                AS RcvdTable

                ON (SentTable.Grade = RcvdTable.Grade) AND (SentTable.Lang = RcvdTable.Lang) AND (SentTable.Title = RcvdTable.Title)


                LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,
                (SELECT en_name from districts where id = bdnto.district) as District,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsrdmgs.total) as TotalDamages

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_recieves bdsr on bds.id = bdsr.book_dis_shipments_id
                JOIN book_dis_shipment_recieve_dmgs bdsrdmgs on bdsr.id = bdsrdmgs.book_dis_receive_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsrdmgs.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsrdmgs.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsrdmgs.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId) AND (bds.to_beneficiary = 0 AND bds.project_id=@projectId  AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, Province) as DmgsTable


                ON (SentTable.Grade = DmgsTable.Grade) AND (SentTable.Lang = DmgsTable.Lang) AND (SentTable.Title = DmgsTable.Title)


            LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,
                (SELECT en_name from districts where id = bdnto.district) as District,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsrlosts.total) as TotalLosts

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_recieves bdsr on bds.id = bdsr.book_dis_shipments_id
                JOIN book_dis_shipment_recieve_losts bdsrlosts on bdsr.id = bdsrlosts.book_dis_receive_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsrlosts.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsrlosts.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsrlosts.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId) AND (bds.to_beneficiary = 0 AND bds.project_id=@projectId AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, Province) as LostsTable


                ON (SentTable.Grade = LostsTable.Grade) AND (SentTable.Lang = LostsTable.Lang) AND (SentTable.Title = LostsTable.Title)


            LEFT JOIN

            (SELECT
                (SELECT en_name from provinces where id = bdnto.province) as Province,
                (SELECT en_name from districts where id = bdnto.district) as District,

                grades.title as Grade, langs.title as Lang, titles.title as Title, sum(bdsd.total) as TotalDistributed

                FROM book_dis_shipments bds
                JOIN book_dis_shipment_details bdsd on bds.id = bdsd.book_dis_shipments_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                JOIN book_dis_meta_grades grades on bdsd.grade_id = grades.id
                JOIN book_dis_meta_titles titles on bdsd.title_id = titles.id
                JOIN book_dis_title_languages langs on bdsd.language_id = langs.id

                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = 15 AND bdn.province = @provinceId) AND (bds.to_beneficiary = 1 AND bds.to_beneficiary_type = 1  AND bds.project_id=@projectId AND bds.`send_date` BETWEEN CAST(@startDate AS DATE) AND CAST(@endDate AS DATE))

                GROUP BY titles.title, grades.title, langs.title, Province) as DistributedTable


                ON (SentTable.Grade = DistributedTable.Grade) AND (SentTable.Lang = DistributedTable.Lang) AND (SentTable.Title = DistributedTable.Title)


                LEFT JOIN

                (SELECT
                (SELECT en_name from provinces where id = bdn.province) as Province,
                grades.title as Grade, langs.title as Lang, titles.title as Title, blnc.total as RemainingBalance

            FROM book_dis_node_balance_details blnc
                JOIN book_dis_nodes bdn on blnc.node_id = bdn.id
                JOIN book_dis_meta_grades grades on blnc.grade_id = grades.id
                JOIN book_dis_meta_titles titles on blnc.title_id = titles.id
                JOIN book_dis_title_languages langs on blnc.language_id = langs.id
            WHERE
                blnc.node_id IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = @levelId AND bdn.province = @provinceId)) as BalancesTable

                ON (SentTable.Grade = BalancesTable.Grade) AND (SentTable.Lang = BalancesTable.Lang) AND (SentTable.Title = BalancesTable.Title)

                GROUP BY Title, Grade, Lang, Province
            ORDER BY
                CASE
                    WHEN SentTable.Grade='Grade One' THEN 1
                    WHEN SentTable.Grade='Grade Two' THEN 2
                    WHEN SentTable.Grade='Grade Three' THEN 3
                    WHEN SentTable.Grade='Grade Four' THEN 4
                    WHEN SentTable.Grade='Grade Five' THEN 5
                    WHEN SentTable.Grade='Grade Six' THEN 6
                    WHEN SentTable.Grade='Grade Seven' THEN 7
                    WHEN SentTable.Grade='Grade Eight' THEN 8
                    WHEN SentTable.Grade='Grade Nine' THEN 9
                    WHEN SentTable.Grade='Grade Ten' THEN 10
                    WHEN SentTable.Grade='Grade Eleven' THEN 11
                    WHEN SentTable.Grade='Grade Twelve' THEN 12
                END ASC
            ";

            $data = DB::select(DB::raw($query));

            if (count($data) > 0) {
                return $data;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    public static function sentReceivedExportExcel($provinceId)
    {

        $query = "
        SELECT bds.id as BDSID, bdsr.id as BDSRID, bds.title as ShipmentTitle, bds.send_date as SendDate, bds.receive_date as ReceiveDate, bdnfrom.title as FromNode, bdnto.title ToNode, grades.title as Grade, titles.title as Title, langs.title as Lang, bdsd.total as TotalSent,
        (SELECT en_name from provinces where id = bdnto.province) as Province,
        (SELECT en_name from districts where id = bdnto.district) as District,
        (SELECT total from book_dis_shipment_recieve_details bdsrd where bdsrd.book_dis_receive_id = bdsr.id AND bdsrd.grade_id = bdsd.grade_id AND bdsrd.language_id = bdsd.language_id AND bdsrd.title_id = bdsd.title_id) as TotalRceived,
        (SELECT total from book_dis_shipment_recieve_dmgs bdsrdm where bdsrdm.book_dis_receive_id = bdsr.id AND bdsrdm.grade_id = bdsd.grade_id AND bdsrdm.language_id = bdsd.language_id AND bdsrdm.title_id = bdsd.title_id) as TotalDamaged,
        (SELECT total from book_dis_shipment_recieve_losts bdsrl where bdsrl.book_dis_receive_id = bdsr.id AND bdsrl.grade_id = bdsd.grade_id AND bdsrl.language_id = bdsd.language_id AND bdsrl.title_id = bdsd.title_id) as TotalLost
        FROM book_dis_shipments bds
        JOIN book_dis_shipment_details bdsd on bds.id = bdsd.book_dis_shipments_id
        JOIN book_dis_shipment_recieves bdsr on bds.id = bdsr.book_dis_shipments_id
        JOIN book_dis_nodes bdnfrom on bds.`from` = bdnfrom.id
        JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
        JOIN book_dis_meta_grades grades on bdsd.grade_id = grades.id
        JOIN book_dis_meta_titles titles on bdsd.title_id = titles.id
        JOIN book_dis_title_languages langs on bdsd.language_id = langs.id

        WHERE

        bds.`from` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
        bdn.level_id = 17) AND

        bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
        bdn.level_id = 15 AND bdn.province = " . $provinceId . ")
        ";

        $records = DB::select(DB::raw($query));

        $records_array[] = array('BDSID', 'BDSRID', 'ShipmentTitle', 'SendDate', 'ReceiveDate', 'FromNode', 'ToNode', 'Grade', 'Title', 'Lang', 'TotalSent', 'Province', 'District', 'TotalRceived', 'TotalDamaged', 'TotalLost');

        foreach ($records as $record) {
            $records_array[] = array(
                'BDSID' => $record->BDSID,
                'BDSRID' => $record->BDSRID,
                'ShipmentTitle' => $record->ShipmentTitle,
                'SendDate' => $record->SendDate,
                'ReceiveDate' => $record->ReceiveDate,
                'FromNode' => $record->FromNode,
                'ToNode' => $record->ToNode,
                'Grade' => $record->Grade,
                'Title' => $record->Title,
                'Lang' => $record->Lang,
                'TotalSent' => $record->TotalSent,
                'Province' => $record->Province,
                'District' => $record->District,
                'TotalRceived' => $record->TotalRceived,
                'TotalDamaged' => $record->TotalDamaged,
                'TotalLost' => $record->TotalLost,
            );
        }

        Excel::create('VendorsToSchools', function ($excel) use ($records_array) {
            $excel->setTitle('Vendors To Schools');
            $excel->sheet('Vendors To Schools', function ($sheet) use ($records_array) {
                $sheet->fromArray($records_array, null, 'A1', false, false);
            });
        })->download('csv');
    }

    public static function studentDistExportExcel($provinceId)
    {
        $records = DB::select(DB::raw("
        SELECT bds.id as BDSID, bdsr.id as BDSRID, bds.title as ShipmentTitle, bds.send_date as SendDate, bds.receive_date as ReceiveDate, bdnfrom.title as FromNode, bdnto.title ToNode, grades.title as Grade, titles.title as Title, langs.title as Lang, bdsd.total as TotalSent,
        (SELECT en_name from provinces where id = bdnto.province) as Province,
        (SELECT en_name from districts where id = bdnto.district) as District,
        (SELECT total from book_dis_shipment_recieve_details bdsrd where bdsrd.book_dis_receive_id = bdsr.id AND bdsrd.grade_id = bdsd.grade_id AND bdsrd.language_id = bdsd.language_id AND bdsrd.title_id = bdsd.title_id) as TotalRceived,
        (SELECT total from book_dis_shipment_recieve_dmgs bdsrdm where bdsrdm.book_dis_receive_id = bdsr.id AND bdsrdm.grade_id = bdsd.grade_id AND bdsrdm.language_id = bdsd.language_id AND bdsrdm.title_id = bdsd.title_id) as TotalDamaged,
        (SELECT total from book_dis_shipment_recieve_losts bdsrl where bdsrl.book_dis_receive_id = bdsr.id AND bdsrl.grade_id = bdsd.grade_id AND bdsrl.language_id = bdsd.language_id AND bdsrl.title_id = bdsd.title_id) as TotalLost
        FROM book_dis_shipments bds
        JOIN book_dis_shipment_details bdsd on bds.id = bdsd.book_dis_shipments_id
        JOIN book_dis_shipment_recieves bdsr on bds.id = bdsr.book_dis_shipments_id
        JOIN book_dis_nodes bdnfrom on bds.`from` = bdnfrom.id
        JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
        JOIN book_dis_meta_grades grades on bdsd.grade_id = grades.id
        JOIN book_dis_meta_titles titles on bdsd.title_id = titles.id
        JOIN book_dis_title_languages langs on bdsd.language_id = langs.id

        WHERE

        bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
        bdn.level_id = 15 AND bdn.province = " . $provinceId . ") AND (bds.to_beneficiary = 1 AND bds.to_beneficiary_type = 1)
        "));

        $records_array[] = array('BDSID', 'BDSRID', 'ShipmentTitle', 'SendDate', 'ReceiveDate', 'FromNode', 'ToNode', 'Grade', 'Title', 'Lang', 'TotalSent', 'Province', 'District', 'TotalRceived', 'TotalDamaged', 'TotalLost');

        foreach ($records as $record) {
            $records_array[] = array(
                'BDSID' => $record->BDSID,
                'BDSRID' => $record->BDSRID,
                'ShipmentTitle' => $record->ShipmentTitle,
                'SendDate' => $record->SendDate,
                'ReceiveDate' => $record->ReceiveDate,
                'FromNode' => $record->FromNode,
                'ToNode' => $record->ToNode,
                'Grade' => $record->Grade,
                'Title' => $record->Title,
                'Lang' => $record->Lang,
                'TotalSent' => $record->TotalSent,
                'Province' => $record->Province,
                'District' => $record->District,
                'TotalRceived' => $record->TotalRceived,
                'TotalDamaged' => $record->TotalDamaged,
                'TotalLost' => $record->TotalLost,
            );
        }

        Excel::create('StudentDist', function ($excel) use ($records_array) {
            $excel->setTitle('Student Dist');
            $excel->sheet('Student Dist', function ($sheet) use ($records_array) {
                $sheet->fromArray($records_array, null, 'A1', false, false);
            });
        })->download('csv');
    }

    public static function getProvinceStats($request)
    {
        if ($request->provinceId != null) {
            $query = "SELECT sum(bdsd.total) as TotalSent,
                    sum((SELECT total from book_dis_shipment_recieve_details bdsrd where bdsrd.book_dis_receive_id = bdsr.id AND bdsrd.grade_id = bdsd.grade_id AND bdsrd.language_id = bdsd.language_id AND bdsrd.title_id = bdsd.title_id)) as TotalRceived,
                    sum((SELECT total from book_dis_shipment_recieve_dmgs bdsrdm where bdsrdm.book_dis_receive_id = bdsr.id AND bdsrdm.grade_id = bdsd.grade_id AND bdsrdm.language_id = bdsd.language_id AND bdsrdm.title_id = bdsd.title_id)) as TotalDamaged,
                    sum((SELECT total from book_dis_shipment_recieve_losts bdsrl where bdsrl.book_dis_receive_id = bdsr.id AND bdsrl.grade_id = bdsd.grade_id AND bdsrl.language_id = bdsd.language_id AND bdsrl.title_id = bdsd.title_id)) as TotalLost,

                    (SELECT sum(bdsd.total)
                    FROM book_dis_shipments bds
                    JOIN book_dis_shipment_details bdsd on bds.id = bdsd.book_dis_shipments_id
                    WHERE
                    bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                    bdn.level_id = 15 AND bdn.province =" . $request->provinceId . ") AND (bds.to_beneficiary = 1 AND bds.to_beneficiary_type = 1
                    AND bds.`send_date` BETWEEN CAST('" . $request->startDate . "' AS DATE) AND CAST('" . $request->endDate . "' AS DATE))) as TotalDistributed

                    FROM book_dis_shipments bds
                    JOIN book_dis_shipment_details bdsd on bds.id = bdsd.book_dis_shipments_id
                    JOIN book_dis_shipment_recieves bdsr on bds.id = bdsr.book_dis_shipments_id
                    WHERE
                    bds.`from` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                    bdn.level_id = 17) AND
                    bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                    bdn.level_id = 15 AND bdn.province = " . $request->provinceId . ") AND
                    bds.`send_date` BETWEEN CAST('" . $request->startDate . "' AS DATE) AND CAST('" . $request->endDate . "' AS DATE);";

            $data = DB::select(DB::raw($query));

            if (count($data) > 0) {
                return $data;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    public static function getStudentDistByTitles($request)
    {
        if ($request->provinceId != null) {
            $query = "SELECT  titles.title as Title, sum(bdsd.total) as TotalDistributed
                    FROM book_dis_shipments bds
                    JOIN book_dis_shipment_details bdsd on bds.id = bdsd.book_dis_shipments_id
                    JOIN book_dis_meta_titles titles on bdsd.title_id = titles.id
                    WHERE
                    bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                    bdn.level_id = 15 AND bdn.province =" . $request->provinceId . ") AND (bds.to_beneficiary = 1 AND bds.to_beneficiary_type = 1 AND bds.`send_date` BETWEEN CAST('" . $request->startDate . "' AS DATE) AND CAST('" . $request->endDate . "' AS DATE))
                    GROUP BY Title
                    ORDER BY TotalDistributed DESC;";

            $data = DB::select(DB::raw($query));

            if (count($data) > 0) {
                return $data;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    public static function getStudentDistByGrades($request)
    {
        if ($request->provinceId != null) {
            $query = "SELECT grades.title as Grade, sum(bdsd.total) as TotalDistributed
                    FROM book_dis_shipments bds
                    JOIN book_dis_shipment_details bdsd on bds.id = bdsd.book_dis_shipments_id
                    JOIN book_dis_meta_grades grades on bdsd.grade_id = grades.id
                    WHERE
                    bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                    bdn.level_id = 15 AND bdn.province =" . $request->provinceId . ") AND (bds.to_beneficiary = 1 AND bds.to_beneficiary_type = 1 AND bds.`send_date` BETWEEN CAST('" . $request->startDate . "' AS DATE) AND CAST('" . $request->endDate . "' AS DATE))
                    GROUP BY Grade
                    ORDER By Grade DESC;";

            $data = DB::select(DB::raw($query));

            if (count($data) > 0) {
                return $data;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    public static function getStudentDistByLanguages($request)
    {
        if ($request->provinceId != null) {
            $query = "SELECT sum(bdsd.total) as TotalDistributed, langs.title as Lang
                    FROM book_dis_shipments bds
                    JOIN book_dis_shipment_details bdsd on bds.id = bdsd.book_dis_shipments_id
                    JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                    JOIN book_dis_title_languages langs on bdsd.language_id = langs.id
                    WHERE
                    bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                    bdn.level_id = 15 AND bdn.province =" . $request->provinceId . ") AND (bds.to_beneficiary = 1 AND bds.to_beneficiary_type = 1 AND bds.`send_date` BETWEEN CAST('" . $request->startDate . "' AS DATE) AND CAST('" . $request->endDate . "' AS DATE))
                    GROUP BY Lang;";

            $data = DB::select(DB::raw($query));

            if (count($data) > 0) {
                return $data;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    public static function getStudentDistByDistricts($request)
    {
        if ($request->provinceId != null) {
            $query = "SELECT sum(bdsd.total) as TotalDistributed, (SELECT en_name from districts where id = bdnto.district) as District
                FROM book_dis_shipments bds
                JOIN book_dis_shipment_details bdsd on bds.id = bdsd.book_dis_shipments_id
                JOIN book_dis_nodes bdnto on bds.`to` = bdnto.id
                WHERE
                bds.`to` IN (SELECT bdn.id FROM book_dis_nodes AS bdn WHERE
                bdn.level_id = 15 AND bdn.province =" . $request->provinceId . ") AND (bds.to_beneficiary = 1 AND bds.to_beneficiary_type = 1 AND bds.`send_date` BETWEEN CAST('" . $request->startDate . "' AS DATE) AND CAST('" . $request->endDate . "' AS DATE))
                GROUP BY District;";

            $data = DB::select(DB::raw($query));

            if (count($data) > 0) {
                return $data;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    public static function syncCreateDistricts($input, $user_id)
    {
        if ($input['missingDistricts'] != null) {
            try {
                $missingDistricts = $input['missingDistricts'];

                foreach ($missingDistricts as $dis) {

                    $district = new district();
                    $district->en_name = $dis['NameEng'];
                    $district->dr_name = $dis['NameDr'];
                    $district->ps_name = $dis['NamePs'];
                    $district->emis_id = $dis['DistrictId'];
                    $district->province_id = $input['province_id'];
                    $district->save();

                    $parentNode = book_dis_node::where("province", $input['province_id'])->where("level_id", 13)->first();

                    $node = new book_dis_node();
                    $node->title = $district->en_name;
                    $node->description = $district->en_name;
                    $node->code = 0;
                    $node->level()->associate(14);
                    $node->province_loc()->associate($input['province_id']);
                    $node->district_loc()->associate($district->id);
                    $node->creator_user()->associate($user_id);
                    $node->parent_node()->associate($parentNode->id);
                    $node->save();
                }
                return 1;
            } catch (\Illuminate\Database\QueryException $ex) {
                return $ex->getMessage();
            }
        }

        return 1;
    }

    public static function syncUpdateDistricts($input)
    {
        if ($input['updateableDistricts'] != null) {
            try {
                $updateableDistricts = $input['updateableDistricts'];

                foreach ($updateableDistricts as $dis) {
                    $district = district::where("emis_id", $dis['DistrictId'])->first();
                    $district->en_name = $dis['NameEng'];
                    $district->dr_name = $dis['NameDr'];
                    $district->ps_name = $dis['NamePs'];
                    $district->save();

                    $node = book_dis_node::where("district", $district->id)->where("level_id", 14)->first();
                    $node->title = $dis['NameEng'];
                    $node->description = $dis['NameEng'];
                    $node->save();
                }
                return 1;
            } catch (\Illuminate\Database\QueryException $ex) {
                return $ex->getMessage();
            }
        }

        return 1;
    }

    public static function syncDeleteDistricts($input)
    {
        if ($input['deleteableDistricts'] != null) {
            try {
                $deleteableDistricts = $input['deleteableDistricts'];
                foreach ($deleteableDistricts as $dis) {
                    $district = district::findOrFail($dis['id']);
                    $node = book_dis_node::where("district", $district->id)->where("level_id", 14)->first();

                    if (isset($node)) {
                        $node->delete();
                    }

                    $district->delete();
                }
                return 1;
            } catch (\Illuminate\Database\QueryException $ex) {
                return $ex->getMessage();
            }
        }

        return 1;
    }

    public static function syncCreateProvinces($input, $user_id)
    {
        if ($input['missingProvinces'] != null) {
            try {
                $missingProvinces = $input['missingProvinces'];

                foreach ($missingProvinces as $pro) {

                    $province = new province();
                    $province->en_name = $pro['NameEng'];
                    $province->dr_name = $pro['NameDr'];
                    $province->ps_name = $pro['NamePs'];
                    $province->emis_id = $pro['ProvinceId'];
                    $province->save();

                    $node = new book_dis_node();
                    $node->title = $province->en_name;
                    $node->description = $province->en_name;
                    $node->code = 0;
                    $node->level()->associate(13);
                    $node->parent_node()->associate(10);
                    $node->province_loc()->associate($province->id);
                    $node->creator_user()->associate($user_id);
                    $node->save();
                }
                return 1;
            } catch (\Illuminate\Database\QueryException $ex) {
                return $ex->getMessage();
            }
        }

        return 1;
    }

    public static function syncUpdateProvinces($input)
    {
        if ($input['updateableProvinces'] != null) {
            try {
                $updateableProvinces = $input['updateableProvinces'];

                foreach ($updateableProvinces as $pro) {
                    $province = province::where("emis_id", $pro['ProvinceId'])->first();
                    $province->en_name = $pro['NameEng'];
                    $province->dr_name = $pro['NameDr'];
                    $province->ps_name = $pro['NamePs'];
                    $province->save();

                    $node = book_dis_node::where("province", $province->id)->where("level_id", 13)->first();
                    $node->title = $pro['NameEng'];
                    $node->description = $pro['NameEng'];
                    $node->save();
                }
                return 1;
            } catch (\Illuminate\Database\QueryException $ex) {
                return $ex->getMessage();
            }
        }

        return 1;
    }

    public static function syncDeleteProvinces($input)
    {
        if ($input['deleteableProvinces'] != null) {
            try {
                $deleteableProvinces = $input['deleteableProvinces'];
                foreach ($deleteableProvinces as $pro) {
                    $province = province::findOrFail($pro['id']);
                    $node = book_dis_node::where("province", $province->id)->where("level_id", 13)->first();

                    if (isset($node)) {
                        $node->delete();
                    }

                    $province->delete();
                }
                return 1;
            } catch (\Illuminate\Database\QueryException $ex) {
                return $ex->getMessage();
            }
        }

        return 1;
    }

    public static function syncCreateDistrictSchools($input, $user_id)
    {
        if ($input['missingSchools'] != null) {
            try {
                $missingSchools = $input['missingSchools'];

                foreach ($missingSchools as $school) {
                    $node = new book_dis_node();
                    $node->title = $school['NameDr'];
                    $node->code = $input['province'] . "_" . $input['district'];
                    $node->description = $school['SchoolCode'];

                    $node->level()->associate(15);
                    $parent = book_dis_node::where("level_id", 14)->where("province", $input['province'])->where("district", $input['district'])->first();
                    $node->parent_node()->associate($parent->id);

                    $node->province_loc()->associate($input['province']);
                    $node->district_loc()->associate($input['district']);
                    $node->creator_user()->associate($user_id);
                    $node->save();
                }
                return 1;
            } catch (\Illuminate\Database\QueryException $ex) {
                return $ex->getMessage();
            }
        }

        return 1;
    }

    public static function syncUpdateDistrictSchools($input)
    {
        if ($input['updateableSchools'] != null) {
            try {
                $updateableSchools = $input['updateableSchools'];
                foreach ($updateableSchools as $school) {
                    $node = book_dis_node::where("description", $school['SchoolCode'])->first();
                    $node->title = $school['NameDr'];
                    $node->save();
                }
                return 1;
            } catch (\Illuminate\Database\QueryException $ex) {
                return $ex->getMessage();
            }
        }

        return 1;
    }

    public static function syncDeleteDistrictSchools($input)
    {
        if ($input['deleteableSchools'] != null) {
            try {
                $deleteableSchools = $input['deleteableSchools'];
                foreach ($deleteableSchools as $school) {
                    $node = book_dis_node::findOrFail($school['id']);
                    $node->delete();
                }
                return 1;
            } catch (\Illuminate\Database\QueryException $ex) {
                return $ex->getMessage();
            }
        }

        return 1;
    }

    public static function adminUpdateShipmentRecipient($input)
    {
        if ($input != null) {
            try {
                $shipment = book_dis_shipment::find($input['id']);
                $shipment->to = $input['node'];
                $shipment->updated_at = Carbon::now();
                $shipment->save();

                return 1;
            } catch (\Illuminate\Database\QueryException $ex) {
                return $ex->getMessage();
            }
        }
    }

    public static function adminUpdateShipmentGeneralInfo($input)
    {
        if ($input != null) {
            try {
                $shipment = book_dis_shipment::find($input['id']);
                $shipment->title = $input['title'];
                $shipment->description = $input['description'];
                $shipment->send_date = $input['sendDate'];
                $shipment->receive_date = $input['receiveDate'];

                if (isset($input['project']) && $input['project'] != 0) {
                    $shipment->project_id = $input['project'];
                }

                $shipment->updated_at = Carbon::now();
                $shipment->save();

                return 1;
            } catch (\Illuminate\Database\QueryException $ex) {
                return $ex->getMessage();
            }
        }
    }

    public static function adminUpdateBalanceRecord($input)
    {
        if ($input != null) {
            try {
                $balanceRecord = book_dis_node_balance_detail::find($input['recordId']);
                $balanceRecord->total = $input['newTotal'];
                $balanceRecord->save();

                return 1;
            } catch (\Illuminate\Database\QueryException $ex) {
                return $ex->getMessage();
            }
        }
    }

    public static function adminDeleteBalanceRecord($input)
    {
        if ($input != null) {
            try {
                $result = DB::delete("DELETE FROM book_dis_node_balance_details WHERE id = " . $input['recordId']);
                return 1;
            } catch (\Illuminate\Database\QueryException $ex) {
                return $ex->getMessage();
            }
        }
    }

    public static function adminGetNodeBalance($nodeId)
    {
        if ($nodeId != null) {
            $query = "
                            select
                            (select bdmt.`title` from `book_dis_meta_titles` as bdmt where bdmt.id = bdnbd.`title_id`) as title,
                            (select bdmg.`title` from `book_dis_meta_grades` as bdmg where bdmg.id = bdnbd.`grade_id`) as grade,
                            (select bdtl.title from `book_dis_title_languages` as bdtl where bdtl.id = bdnbd.`language_id`) as lang,
                            bdnbd.*
                            from `book_dis_node_balance_details` as bdnbd where bdnbd.`node_id` = " . $nodeId . "
                            ORDER by bdnbd.`grade_id` ASC";

            $balance = DB::select(DB::raw($query));
            if (count($balance) > 0) {
                return $balance;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    public static function adminDeleteShipment($shipmentId)
    {
        try {
            $bdsrd = DB::delete("DELETE FROM book_dis_shipments WHERE id = " . $shipmentId);
            return 1;
        } catch (\Illuminate\Database\QueryException $ex) {
            return $ex->getMessage();
        }
    }

    public static function adminClearReceiveRecords($recvId)
    {
        try {
            $bdsrd = DB::delete("DELETE FROM book_dis_shipment_recieve_details WHERE book_dis_receive_id = " . $recvId);
            $bdsrl = DB::delete("DELETE FROM book_dis_shipment_recieve_losts WHERE book_dis_receive_id = " . $recvId);
            $bdsrdmgs = DB::delete("DELETE FROM book_dis_shipment_recieve_dmgs WHERE book_dis_receive_id =" . $recvId);

            $update = DB::update("UPDATE book_dis_shipment_recieves SET total_safe = 0, total_general = 0, lost = 0, damaged = 0, received = 0, receive_date = NULL WHERE id = ?", [$recvId]);

            return 1;
        } catch (\Illuminate\Database\QueryException $ex) {
            return $ex->getMessage();
        }
    }

    public static function getPendingReceives($page, $url, $user)
    {

        if ($user != null) {

            $nodes = $user->work_node();
            if (count($nodes) > 0) {
                $node = $nodes->first();
                $query_string = "
                select bds.`title`,bds.`description`, bds.`send_date` as send_date,
                    (select bn.`title` from `book_dis_nodes` as bn where bn.id = bds.to) as To_title,
                    (select bn.`title` from `book_dis_nodes` as bn where bn.id = bds.from) as From_title,
                    (select bdsr.`received` from `book_dis_shipment_recieves` as bdsr where bdsr.`book_dis_shipments_id` = bds.id LIMIT 1) as recieved,
                    (select bdsr.id from `book_dis_shipment_recieves` as bdsr where bdsr.`book_dis_shipments_id` = bds.id LIMIT 1) as recieved_id,
                    (select count(bdsfiles.id) from `book_dis_shipment_files` as bdsfiles where bdsfiles.`book_dis_shipments_id` = bds.id) as docs
                     from `book_dis_shipments` as bds
                     where bds.to = " . $node->id . "
                     and bds.`to_beneficiary` = 0
                     and (select bdsr.`received` from `book_dis_shipment_recieves` as bdsr where bdsr.`book_dis_shipments_id` = bds.id LIMIT 1) = 0
                    ORDER BY bds.`created_at` DESC
                    LIMIT 150";

                $balance_result = DB::select(DB::raw($query_string));
                $perPage = 150;
                $offset = ($page * $perPage) - $perPage;

                $balance_result = new LengthAwarePaginator(
                    array_slice($balance_result, $offset, $perPage, true),
                    count($balance_result),
                    $perPage,
                    $page
                );
                return $balance_result;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }
    // added by zabeeh

    public static function getNodeRecieveReceives($node_id, $url, $user)
    {

        if ($user != null) {

            $query_string = "
                select bds.`title`,bds.`description`, bds.`send_date` as send_date,
                    (select bn.`title` from `book_dis_nodes` as bn where bn.id = bds.to) as To_title,
                    (select bn.`title` from `book_dis_nodes` as bn where bn.id = bds.from) as From_title,
                    (select usr.name from users as usr where usr.id = bds.`creator_id`) as sender_name,
                    (select bdsr.`received` from `book_dis_shipment_recieves` as bdsr where bdsr.`book_dis_shipments_id` = bds.id LIMIT 1) as recieved,
                    (select bdsr.id from `book_dis_shipment_recieves` as bdsr where bdsr.`book_dis_shipments_id` = bds.id LIMIT 1) as recieved_id
                     from `book_dis_shipments` as bds
                    where bds.to = " . $node_id . "
                     and bds.`to_beneficiary` = 0
                    ORDER BY bds.`created_at` DESC
                 ";

            $balance_result = DB::select(DB::raw($query_string));

            return $balance_result;
        } else {
            return array();
        }
    }

    public static function getNodeReceives($page, $url, $node_id, $user)
    {

        if ($user != null) {

            $nodes = book_dis_node::where("id", $node_id)->get();
            if (count($nodes) > 0) {
                $node = $nodes->first();
                $query_string = "
                select bds.`title`,bds.`description`, bds.`send_date` as send_date,
                    (select bn.`title` from `book_dis_nodes` as bn where bn.id = bds.to) as To_title,
                    (select bn.`title` from `book_dis_nodes` as bn where bn.id = bds.from) as From_title,
                    (select bdsr.`received` from `book_dis_shipment_recieves` as bdsr where bdsr.`book_dis_shipments_id` = bds.id LIMIT 1) as recieved,
                    (select bdsr.id from `book_dis_shipment_recieves` as bdsr where bdsr.`book_dis_shipments_id` = bds.id LIMIT 1) as recieved_id
                     from `book_dis_shipments` as bds
                    where bds.to = " . $node->id . "
                     and bds.`to_beneficiary` = 0
                    ORDER BY bds.`created_at` DESC
                    LIMIT 15";

                $balance_result = DB::select(DB::raw($query_string));
                /*$perPage = 3;
                $offset = ($page * $perPage) - $perPage;

                $balance_result = new LengthAwarePaginator(
                array_slice($balance_result, $offset, $perPage, true),
                count($balance_result),
                $perPage,
                $page
                );*/
                return $balance_result;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    public static function getToBenefic($user)
    {

        if ($user != null) {

            // the both node should be the same to be indentifiabel as a transaction between the last node and baneficiary
            $nodes = $user->work_node();
            if (count($nodes) > 0) {
                $node = $nodes->first();
                $query_string = "
                select
                    bds.id as id, bds.`title`,bds.`description`, bds.`send_date` as send_date,
                    (select bn.`title` from `book_dis_nodes` as bn where bn.id = bds.to) as To_title,
                    (select bn.`title` from `book_dis_nodes` as bn where bn.id = bds.from) as From_title,
                    (select bdsr.`received` from `book_dis_shipment_recieves` as bdsr where bdsr.`book_dis_shipments_id` = bds.id LIMIT 1) as recieved,
                    (select bdsr.id from `book_dis_shipment_recieves` as bdsr where bdsr.`book_dis_shipments_id` = bds.id LIMIT 1) as recieved_id
                     from `book_dis_shipments` as bds
                    where bds.to = " . $node->id . "
                    and bds.from = " . $node->id . "
                    and bds.`to_beneficiary` = 1
                    and bds.`to_beneficiary_type` = 1
                    ORDER BY bds.`created_at` DESC
                    ";

                $balance_result = DB::select(DB::raw($query_string));
                if (count($balance_result) > 0) {
                    return $balance_result;
                } else {
                    return array();
                }
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    public static function getToBeneficId($node_id)
    {

        if ($node_id != null) {

            // the both node should be the same to be indentifiabel as a transaction between the last node and baneficiary
            $query_string = "
                select
                    bds.id as id, bds.`title`,bds.`description`, bds.`send_date` as send_date,
                    (select bn.`title` from `book_dis_nodes` as bn where bn.id = bds.to) as To_title,
                    (select bn.`title` from `book_dis_nodes` as bn where bn.id = bds.from) as From_title,
                    (select bdsr.`received` from `book_dis_shipment_recieves` as bdsr where bdsr.`book_dis_shipments_id` = bds.id LIMIT 1) as recieved,
                    (select bdsr.id from `book_dis_shipment_recieves` as bdsr where bdsr.`book_dis_shipments_id` = bds.id LIMIT 1) as recieved_id
                     from `book_dis_shipments` as bds
                    where bds.to = " . $node_id . "
                    and bds.from = " . $node_id . "
                    and bds.`to_beneficiary` = 1
                    and bds.`to_beneficiary_type` = 1
                    ORDER BY bds.`created_at` DESC
                    ";

            $balance_result = DB::select(DB::raw($query_string));
            if (count($balance_result) > 0) {
                return $balance_result;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    public static function getFromBenefic($user)
    {

        if ($user != null) {

            // the both node should be the same to be indentifiabel as a transaction between the last node and baneficiary
            $nodes = $user->work_node();
            if (count($nodes) > 0) {
                $node = $nodes->first();
                $query_string = "
                select bds.id, bds.`title`,bds.`description`, bds.`send_date` as send_date,
                    (select bn.`title` from `book_dis_nodes` as bn where bn.id = bds.to) as To_title,
                    (select bn.`title` from `book_dis_nodes` as bn where bn.id = bds.from) as From_title,
                    (select bdsr.`received` from `book_dis_shipment_recieves` as bdsr where bdsr.`book_dis_shipments_id` = bds.id LIMIT 1) as recieved,
                    (select bdsr.id from `book_dis_shipment_recieves` as bdsr where bdsr.`book_dis_shipments_id` = bds.id LIMIT 1) as recieved_id
                     from `book_dis_shipments` as bds
                    where bds.to = " . $node->id . "
                    and bds.from = " . $node->id . "
                    and bds.`to_beneficiary` = 1
                    and bds.`to_beneficiary_type` = 2
                    ORDER BY bds.`created_at` DESC
                    ";

                $balance_result = DB::select(DB::raw($query_string));
                if (count($balance_result) > 0) {
                    return $balance_result;
                } else {
                    return array();
                }
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    public static function getFromBeneficId($node_id)
    {

        if ($node_id != null) {

            // the both node should be the same to be indentifiabel as a transaction between the last node and baneficiary
            $query_string = "
                select bds.id, bds.`title`,bds.`description`, bds.`send_date` as send_date,
                    (select bn.`title` from `book_dis_nodes` as bn where bn.id = bds.to) as To_title,
                    (select bn.`title` from `book_dis_nodes` as bn where bn.id = bds.from) as From_title,
                    (select bdsr.`received` from `book_dis_shipment_recieves` as bdsr where bdsr.`book_dis_shipments_id` = bds.id LIMIT 1) as recieved,
                    (select bdsr.id from `book_dis_shipment_recieves` as bdsr where bdsr.`book_dis_shipments_id` = bds.id LIMIT 1) as recieved_id
                     from `book_dis_shipments` as bds
                    where bds.to = " . $node_id . "
                    and bds.from = " . $node_id . "
                    and bds.`to_beneficiary` = 1
                    and bds.`to_beneficiary_type` = 2
                    ORDER BY bds.`created_at` DESC
                    ";

            $balance_result = DB::select(DB::raw($query_string));
            if (count($balance_result) > 0) {
                return $balance_result;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    public static function searchSendReport($node_id)
    {

        $query_string = "
                select *, sum(bdsd.total) as total_all , (	select bdst.to from `book_dis_shipments` as bdst where bdst.`id` = bdsd.`book_dis_shipments_id`) as To_node,
                        (select grades.`title` from `book_dis_meta_grades` as grades where grades.`id` = bdsd.`grade_id`) as grade_title,
                        (select titles.`title` from `book_dis_meta_titles` as titles where titles.`id` = bdsd.`title_id`) as title_title,
                        (select langs.title from `book_dis_title_languages` as langs where langs.`id` = bdsd.`language_id`) as lang_title,
                        (
                        select bdn.`title` from `book_dis_nodes` as bdn where bdn.id = (select bdst.to from `book_dis_shipments` as bdst where bdst.`id` = bdsd.`book_dis_shipments_id`)

                        ) as to_title

                        from `book_dis_shipment_details` as bdsd where bdsd.`book_dis_shipments_id` in
                        (
                            select bds.id from `book_dis_shipments` as bds where bds.`from` = " . $node_id . "
                        )
                        GROUP BY bdsd.`grade_id` , bdsd.`title_id`, bdsd.`language_id`,To_node
                        order by lang_title,grade_title
                    ";

        $balance_result = DB::select(DB::raw($query_string));
        if (count($balance_result) > 0) {
            return $balance_result;
        } else {
            return array();
        }
    }

    public static function searchReceiveReport($node_id)
    {

        $query_string = "
                select *, sum(bdsd.total) as total_all , (	select bdst.to from `book_dis_shipments` as bdst where bdst.`id` = bdsd.`book_dis_shipments_id`) as To_node,
                        (select grades.`title` from `book_dis_meta_grades` as grades where grades.`id` = bdsd.`grade_id`) as grade_title,
                        (select titles.`title` from `book_dis_meta_titles` as titles where titles.`id` = bdsd.`title_id`) as title_title,
                        (select langs.title from `book_dis_title_languages` as langs where langs.`id` = bdsd.`language_id`) as lang_title,
                        (
                        select bdn.`title` from `book_dis_nodes` as bdn where bdn.id = (select bdst.to from `book_dis_shipments` as bdst where bdst.`id` = bdsd.`book_dis_shipments_id`)

                        ) as to_title

                        from `book_dis_shipment_details` as bdsd where bdsd.`book_dis_shipments_id` in
                        (
                            select bds.id from `book_dis_shipments` as bds where bds.`to` = " . $node_id . "
                        )
                        GROUP BY bdsd.`grade_id` , bdsd.`title_id`, bdsd.`language_id`,To_node
                        order by lang_title,grade_title
                    ";

        $balance_result = DB::select(DB::raw($query_string));
        if (count($balance_result) > 0) {
            return $balance_result;
        } else {
            return array();
        }
    }

    public static function searchReport($node_id)
    {

        $query_string = "
               select bdbd.total as avalaible_total ,


						(select grades.`title` from `book_dis_meta_grades` as grades where grades.`id` = bdbd.`grade_id`) as grade_title,
                        (select titles.`title` from `book_dis_meta_titles` as titles where titles.`id` = bdbd.`title_id`) as title_title,
                        (select langs.title from `book_dis_title_languages` as langs where langs.`id` = bdbd.`language_id`) as lang_title, (select sum(bdsrd.total) as received_total from `book_dis_shipment_recieve_details` as bdsrd where bdsrd.`book_dis_receive_id` in (
select bdsr.id from `book_dis_shipment_recieves` as bdsr where bdsr.`book_dis_shipments_id` in (select bds.id  from `book_dis_shipments` as bds where bds.to = " . $node_id . ") and received = 1
)

and
bdsrd.`grade_id` = bdbd.`grade_id`
and
bdsrd.`title_id` = bdbd.`title_id`
and
bdsrd.`language_id` = bdbd.`language_id`) as received_total , (select sum(bdsd.total) as issued_total from `book_dis_shipment_details` as bdsd  where bdsd.`book_dis_shipments_id` in (select bds.id  from `book_dis_shipments` as bds where bds.from = " . $node_id . ")
and
bdsd.`grade_id` = bdbd.`grade_id`
and
bdsd.`title_id` = bdbd.`title_id`
and
bdsd.`language_id` = bdbd.`language_id`) as issued_total  from `book_dis_node_balance_details` as bdbd where bdbd.`node_id` = " . $node_id . "
                    ";

        $balance_result = DB::select(DB::raw($query_string));
        if (count($balance_result) > 0) {
            return $balance_result;
        } else {
            return array();
        }
    }

    public static function getLanguageList()
    {

        $langs = book_dis_title_language::where("title", "like", "%Dari%")->orWhere("title", "like", "%Pashto%")->get();
        if ($langs != null) {
            return $langs;
        } else {
            return null;
        }
    }

    public static function getLanguageFullList()
    {

        $langs = book_dis_title_language::where("title", "not like", "%Dari%")->Where("title", "not like", "%Pashto%")->get();
        if ($langs != null) {
            return $langs;
        } else {
            return null;
        }
    }

    public static function getGradeTitleList($grade_id)
    {

        $grade = book_dis_meta_grade::findOrFail($grade_id);
        if ($grade != null) {
            $titles = $grade->titles()->get();
            return $titles;
        } else {
            return null;
        }
    }

    public static function getChildNodes($level_id, $province_id, $district_id)
    {

        $nodes = book_dis_node::where("level_id", $level_id)->where("province", $province_id)->where("district", $district_id)->get();
        if (count($nodes) > 0) {
            return $nodes;
        } else {
            return array();
        }
    }

    public static function getNodeList($level_id)
    {
        if ($level_id != null) {
            //book level creation
            $level = book_dis_node_level::findOrFail($level_id);
            $main_level = $level->parent()->get();
            if (count($main_level) > 0) {
                $main_level = $main_level->first();
            } else {
                return null;
            }
            if ($main_level != null) {
                return $main_level->nodes()->get();
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public static function getNodeUserList($Node_id)
    {
        if ($Node_id != null) {
            //book level creation
            $node = book_dis_node::findOrFail($Node_id);
            if ($node != null) {
                return $node->staffs()->get();
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public static function createNode($input_data, $user_id)
    {
        if ($input_data != null) {

            $data = $input_data;
            //book level creation
            $node = new book_dis_node();
            $node->title = $data['title'];
            $node->code = $data['code'];
            $node->description = $data['description'];
            if ($data['level_id'] == 0) {
                return 0;
            } else {
                $level = book_dis_node_level::findOrFail($data['level_id']);
                if ($level != null) {
                    if ($level->code == 1) {
                        $node->level()->associate($data['level_id']);
                    } elseif ($level->code > 1) {
                        if ($data['parent_id'] == 0) {
                            return 0;
                        } else {
                            $node->level()->associate($data['level_id']);
                            $node->parent_node()->associate($data['parent_id']);
                        }
                    } else {
                        return 0;
                    }
                } else {
                    return 0;
                }
            }
            $node->province_loc()->associate($data['province']);
            $node->district_loc()->associate($data['district']);
            $node->creator_user()->associate($user_id);
            $node->save();

            if ($node->id != 0) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public static function createSendShipment($input_data, $user)
    {
        if ($input_data != null) {

            $data = $input_data;
            $success = false;

            $_this = new BookDisRM();
            $node_users_for_search = $user->work_node();
            $search_result = $_this::searchShipment($input_data, $node_users_for_search->first()->id, $data['to']);
            if (count($search_result) > 0) {
                //returning 2 means that there are some records at data base with the same data provided, It is not allowed
                return 2;
            } else {

                DB::beginTransaction();
                try {

                    $shipment = new book_dis_shipment();
                    $shipment->title = $data['title'];
                    $shipment->description = (isset($data['description']) ? $data['description'] : "");

                    $shipment->project_id = null;
                    if (isset($data['project_id']) && $data['project_id'] != 0) {
                        $shipment->project_id = $data['project_id'];
                    }

                    $node_users = $user->work_node();
                    if (count($node_users) > 0) {
                        $shipment->from_node()->associate($node_users->first()->id);
                        $shipment->to_node()->associate($data['to']);
                    }
                    $shipment->creator_user()->associate($user->id);
                    $shipment->send_date = date('Y-m-d', strtotime($data['send_date']));
                    $shipment->save();

                    $receive_record = new book_dis_shipment_recieve();
                    $receive_record->total_safe = 0;
                    $receive_record->total_general = 0;
                    $receive_record->damaged = 0;
                    $receive_record->lost = 0;
                    $receive_record->received = 0;
                    $receive_record->shipment()->associate($shipment->id);
                    $receive_record->save();

                    $total_amount = 0;
                    foreach ($data['lang'] as $key => $val) {
                        if ($val != null) {
                            $key_string = explode("_", $key);
                            $title_id = $key_string[0];
                            $language_id = $key_string[1];
                            $grade_id = $key_string[2];

                            if ($language_id != null && $language_id != 0) {
                                if ($data["sent_grades"][$grade_id] == true) {

                                    $grade = book_dis_meta_grade::findOrFail($grade_id);
                                    if ($grade != null) {
                                        $shipment_detail = new book_dis_shipment_detail();
                                        $shipment_detail->grade()->associate($grade->id);
                                        $shipment_detail->title()->associate($title_id);
                                        $shipment_detail->language()->associate($language_id);
                                        $shipment_detail->shipment()->associate($shipment->id);
                                        $shipment_detail->total = $val;
                                        $shipment_detail->save();

                                        /*    Making the balance detail for the node */
                                        $db_result_blance_detail = book_dis_node_balance_detail::where("node_id", $node_users->first()->id)
                                            ->where("grade_id", $grade_id)
                                            ->where("title_id", $title_id)
                                            ->where("language_id", $language_id)->get();
                                        if (count($db_result_blance_detail) > 0) {
                                            $db_record_blance_detail = $db_result_blance_detail->first();
                                            $db_record_blance_detail->total = $db_record_blance_detail->total - $val;
                                            $db_record_blance_detail->save();
                                        } else {

                                            if ($val >= 0) {
                                            } else {

                                                $success = false;
                                                DB::rollback();
                                            }
                                        }
                                        /*    Making the balance detail for the node */

                                        $total_amount += $val;
                                    }
                                }
                            }
                        }
                    }

                    $transaction = new book_dis_node_transaction();
                    $transaction->amount = $total_amount;
                    // type has two value  1  for out, 2 for in
                    $transaction->type = 1;
                    $transaction->source_id = $shipment->id;
                    $transaction->creator_user()->associate($user->id);
                    $transaction->save();

                    $node_balance = book_dis_node_balance::where('node_id', $node_users->first()->id)
                        ->where('active', 1)->get();
                    if (count($node_balance) > 0) {
                        $node_balance_old = $node_balance->first();
                        $node_balance_old->update(['active' => 0]);
                        $node_balance_old->save();

                        $node_balance_new = new book_dis_node_balance();

                        //check if the amount requested to send is more than what is in balance
                        if ($node_balance_old->total >= $total_amount) {
                            $node_balance_new->total = $node_balance_old->total - $total_amount;
                        } else {
                            $parent_node = $node_users->first()->parent_node()->get();
                            if (count($parent_node) > 0) {
                                //this condition means that user is not from root node so can not send the shipment without recieving somthing
                                /*$success = false;
                            DB::rollback();
                            return 0;*/
                            } else {
                                $node_balance_new->total = $node_balance_old->total - $total_amount;
                            }
                        }

                        $node_balance_new->previous_total = $node_balance_old->total;
                        $node_balance_new->amount = $total_amount;
                        // active field has two value 1 for active 0 for unactive
                        $node_balance_new->active = 1;
                        // type has two value  1  for out, 2 for in
                        $node_balance_new->type = 1;
                        $node_balance_new->creator_user()->associate($user->id);
                        $node_balance_new->transaction()->associate($transaction->id);
                        $node_balance_new->node()->associate($node_users->first()->id);
                        $node_balance_new->save();
                    } else {
                        $node_balance_new = new book_dis_node_balance();
                        $node_balance_new->total = 0 - $total_amount;
                        $node_balance_new->previous_total = 0;
                        $node_balance_new->amount = $total_amount;
                        // active field has two value 1 for active 0 for unactive
                        $node_balance_new->active = 1;
                        // type has two value  1  for out, 2 for in
                        $node_balance_new->type = 1;
                        $node_balance_new->creator_user()->associate($user->id);
                        $node_balance_new->transaction()->associate($transaction->id);
                        $node_balance_new->node()->associate($node_users->first()->id);
                        $node_balance_new->save();
                    }

                    DB::commit();
                    $success = true;
                } catch (\Exception $e) {
                    $success = false;
                    DB::rollback();
                }
            }
            if ($success) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public static function uploadDocShipment($input_data, $user)
    {
        if ($input_data != null) {

            $data = $input_data;
            $_this = new BookDisRM();
            if (isset($data['sent_doc']['shipment_id'])) {
                $book_dis_shipment_result = book_dis_shipment::where("id", $data['sent_doc']['shipment_id'])->get();
                if (count($book_dis_shipment_result) > 0) {
                    $book_dis_shipment = $book_dis_shipment_result->first();

                    if (Input::hasFile('sent_doc.file.0')) {

                        $index = 0;
                        $book_dis_ship_file_result = $book_dis_shipment->docs()->get();
                        if (count($book_dis_ship_file_result)) {
                            if ($book_dis_ship_file_result->last() != null) {
                                //dd($book_dis_ship_file_result->last()->id);
                                $index = $book_dis_ship_file_result->last()->id + 1;
                            }
                        }

                        $newfile = new book_dis_shipment_file();
                        $filename = Input::file('sent_doc.file.0')->getClientOriginalExtension();
                        $path = "files/" . $book_dis_shipment->id . '_' . $index . '_shipment_doc.' . $filename;
                        $upload_success = Input::file('sent_doc.file.0')->move(base_path() . "/files/", $book_dis_shipment->id . '_' . $index . '_shipment_doc.' . $filename);

                        if ($upload_success) {
                            $newfile->title = (isset($data['sent_doc']['title']) ? $data['sent_doc']['title'] : "");
                            $newfile->file_url = $path;
                            //type 0 = sent 1 = receive
                            $newfile->type = 0;
                            $newfile->book_dis_shipments_id = $book_dis_shipment->id;
                            $newfile->save();

                            return 1;
                        }
                    } else {
                        return 0;
                    }
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public static function uploadDocRecShipment($input_data, $user)
    {
        if ($input_data != null) {

            $data = $input_data;
            $_this = new BookDisRM();
            if (isset($data['recieve_doc']['shipment_id'])) {
                $book_dis_shipment_result = book_dis_shipment::where("id", $data['recieve_doc']['shipment_id'])->get();
                if (count($book_dis_shipment_result) > 0) {
                    $book_dis_shipment = $book_dis_shipment_result->first();

                    if (Input::hasFile('recieve_doc.file.0')) {

                        $index = 0;
                        $book_dis_ship_file_result = $book_dis_shipment->docs()->get();
                        if (count($book_dis_ship_file_result)) {
                            if ($book_dis_ship_file_result->last() != null) {
                                //dd($book_dis_ship_file_result->last()->id);
                                $index = $book_dis_ship_file_result->last()->id + 1;
                            }
                        }

                        $newfile = new book_dis_shipment_file();
                        $filename = Input::file('recieve_doc.file.0')->getClientOriginalExtension();
                        $path = "files/" . $book_dis_shipment->id . '_' . $index . '_shipment_doc.' . $filename;
                        $upload_success = Input::file('recieve_doc.file.0')->move(base_path() . "/files/", $book_dis_shipment->id . '_' . $index . '_shipment_doc.' . $filename);

                        if ($upload_success) {
                            $newfile->title = (isset($data['recieve_doc']['title']) ? $data['recieve_doc']['title'] : "");
                            $newfile->file_url = $path;
                            //type 0 = sent 1 = receive
                            $newfile->type = 1;
                            $newfile->book_dis_shipments_id = $book_dis_shipment->id;
                            $newfile->save();

                            return 1;
                        }
                    } else {
                        return 0;
                    }
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public static function createStarterSendShipment($input_data, $user)
    {
        if ($input_data != null) {

            $data = $input_data;
            $success = false;

            $_this = new BookDisRM();
            $node_users_for_search = $user->work_node();
            $search_result = $_this::searchShipment($input_data, $node_users_for_search->first()->id, $data['to']);
            if (count($search_result) > 0) {
                //returning 2 means that there are some records at data base with the same data provided, It is not allowed
                return 2;
            } else {

                DB::beginTransaction();
                try {

                    $shipment = new book_dis_shipment();
                    $shipment->title = $data['title'];
                    $shipment->description = (isset($data['description']) ? $data['description'] : "");

                    $shipment->project_id = null;
                    if (isset($data['project_id']) && $data['project_id'] != 0) {
                        $shipment->project_id = $data['project_id'];
                    }

                    $node_users = $user->work_node();
                    if (count($node_users) > 0) {
                        $shipment->from_node()->associate($node_users->first()->id);
                        $shipment->to_node()->associate($data['to']);
                    }
                    $shipment->creator_user()->associate($user->id);
                    $shipment->send_date = date('Y-m-d', strtotime($data['send_date']));
                    $shipment->save();

                    $receive_record = new book_dis_shipment_recieve();
                    $receive_record->total_safe = 0;
                    $receive_record->total_general = 0;
                    $receive_record->damaged = 0;
                    $receive_record->lost = 0;
                    $receive_record->received = 0;
                    $receive_record->shipment()->associate($shipment->id);
                    $receive_record->save();

                    $total_amount = 0;
                    foreach ($data['lang'] as $key => $val) {
                        $key_string = explode("_", $key);
                        $title_id = $key_string[0];
                        $language_id = $key_string[1];
                        $grade_id = $key_string[2];

                        $grade = book_dis_meta_grade::findOrFail($grade_id);
                        if ($grade != null) {
                            $shipment_detail = new book_dis_shipment_detail();
                            $shipment_detail->grade()->associate($grade->id);
                            $shipment_detail->title()->associate($title_id);
                            $shipment_detail->language()->associate($language_id);
                            $shipment_detail->shipment()->associate($shipment->id);
                            $shipment_detail->total = $val;
                            $shipment_detail->save();

                            /*    Making the balance detail for the node */
                            $db_result_blance_detail = book_dis_node_balance_detail::where("node_id", $node_users_for_search->first()->id)
                                ->where("grade_id", $grade_id)
                                ->where("title_id", $title_id)
                                ->where("language_id", $language_id)->get();
                            if (count($db_result_blance_detail) > 0) {
                                $db_record_blance_detail = $db_result_blance_detail->first();
                                $db_record_blance_detail->total = $db_record_blance_detail->total - $val;
                                $db_record_blance_detail->save();
                            } else {
                                $node_balance_detail = new book_dis_node_balance_detail();
                                $node_balance_detail->node_id = $node_users_for_search->first()->id;
                                $node_balance_detail->grade_id = $grade_id;
                                $node_balance_detail->title_id = $title_id;
                                $node_balance_detail->language_id = $language_id;
                                $node_balance_detail->total = $val;
                                $node_balance_detail->save();
                            }
                            /*    Making the balance detail for the node */

                            $total_amount += $val;
                        }
                    }

                    $transaction = new book_dis_node_transaction();
                    $transaction->amount = $total_amount;
                    // type has two value  1  for out, 2 for in
                    $transaction->type = 1;
                    $transaction->source_id = $shipment->id;
                    $transaction->creator_user()->associate($user->id);
                    $transaction->save();

                    $node_balance = book_dis_node_balance::where('node_id', $node_users->first()->id)
                        ->where('active', 1)->get();
                    if (count($node_balance) > 0) {
                        $node_balance_old = $node_balance->first();
                        $node_balance_old->update(['active' => 0]);
                        $node_balance_old->save();

                        $node_balance_new = new book_dis_node_balance();

                        //check if the amount requested to send is more than what is in balance
                        if ($node_balance_old->total > $total_amount) {
                            $node_balance_new->total = $node_balance_old->total - $total_amount;
                        } else {
                            $parent_node = $node_users->first()->parent_node()->get();
                            if (count($parent_node) > 0) {
                                //this condition means that user is not from root node so can not send the shipment without recieving somthing
                                $success = false;
                                DB::rollback();
                                return 0;
                            } else {
                                $node_balance_new->total = $node_balance_old->total - $total_amount;
                            }
                        }

                        $node_balance_new->previous_total = $node_balance_old->total;
                        $node_balance_new->amount = $total_amount;
                        // active field has two value 1 for active 0 for unactive
                        $node_balance_new->active = 1;
                        // type has two value  1  for out, 2 for in
                        $node_balance_new->type = 1;
                        $node_balance_new->creator_user()->associate($user->id);
                        $node_balance_new->transaction()->associate($transaction->id);
                        $node_balance_new->node()->associate($node_users->first()->id);
                        $node_balance_new->save();
                    } else {
                        $node_balance_new = new book_dis_node_balance();
                        $node_balance_new->total = 0 - $total_amount;
                        $node_balance_new->previous_total = 0;
                        $node_balance_new->amount = $total_amount;
                        // active field has two value 1 for active 0 for unactive
                        $node_balance_new->active = 1;
                        // type has two value  1  for out, 2 for in
                        $node_balance_new->type = 1;
                        $node_balance_new->creator_user()->associate($user->id);
                        $node_balance_new->transaction()->associate($transaction->id);
                        $node_balance_new->node()->associate($node_users->first()->id);
                        $node_balance_new->save();
                    }

                    DB::commit();
                    $success = true;
                } catch (\Exception $e) {
                    $success = false;
                    DB::rollback();
                }
            }
            if ($success) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public static function isValidToSend($input_data, $user)
    {
        if ($input_data != null) {

            $data = $input_data;
            foreach ($data['lang'] as $key => $val) {
                $key_string = explode("_", $key);
                $title_id = $key_string[0];
                $language_id = $key_string[1];
                $grade_id = $key_string[2];
                $total = $val;

                $node_users = $user->work_node();
                if (count($node_users) > 0) {
                    $user_node = $node_users->first()->id;

                    if ($language_id != null && $language_id != 0) {
                        if ($total > 0) {
                            if ($data["sent_grades"][$grade_id] == true) {
                                $db_result = book_dis_node_balance_detail::where("grade_id", $grade_id)->where("title_id", $title_id)
                                    ->where("language_id", $language_id)->where("node_id", $user_node)->where("total", ">=", $total)->get();

                                if (count($db_result) > 0) {

                                    if ($total < 0) {
                                        return 0;
                                    } else {
                                    }
                                } else {

                                    return 0;
                                }
                            } else {
                            }
                        }
                    } else {
                    }
                }
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function isValidToSendToBeneficiary($input_data, $user)
    {
        if ($input_data != null) {

            $data = $input_data;
            foreach ($data['lang'] as $key => $val) {
                $key_string = explode("_", $key);
                $title_id = $key_string[0];
                $language_id = $key_string[1];
                $grade_id = $key_string[2];
                $total = $val;
                if ($total != null) {
                    $node_users = $user->work_node();
                    if (count($node_users) > 0) {
                        $user_node = $node_users->first()->id;

                        if ($language_id != null && $language_id != 0) {
                            if ($total > 0 && $total != null) {
                                if ($data["sent_grades"][$grade_id] == true) {
                                    $db_result = book_dis_node_balance_detail::where("grade_id", $grade_id)->where("title_id", $title_id)
                                        ->where("language_id", $language_id)->where("node_id", (isset($data['node_id']) ? $data['node_id'] : 0))->where("total", ">=", $total)->get();

                                    if (count($db_result) > 0) {

                                        if ($total < 0) {
                                            return 0;
                                        } else {
                                        }
                                    } else {

                                        return 0;
                                    }
                                } else {
                                }
                            }
                        } else {
                        }
                    }
                }
            }
            return 1;
        } else {
            return 0;
        }
    }

    public static function isValidToReceive($input_data, $user)
    {
        if ($input_data != null) {

            $data = $input_data;

            foreach ((isset($data['lang']) ? $data['lang'] : array()) as $key => $val) {
                if ($val != null) {
                    if ($val != 0) {
                        $key_string = explode("_", $key);
                        $title_id = $key_string[0];
                        $language_id = $key_string[1];
                        $grade_id = $key_string[2];
                        $damaged = 0;
                        if (isset($data['lang_dmg'][$key])) {
                            $damaged = $data['lang_dmg'][$key];
                        }
                        $total = $val + $damaged;
                        $shipment_id = $data['recieve_id'];

                        if ($language_id != null && $language_id != 0) {
                            if ($data["received_grades"][$grade_id] == true) {
                                $db_result = book_dis_shipment_detail::where("book_dis_shipments_id", $shipment_id)->where("grade_id", $grade_id)->where("title_id", $title_id)
                                    ->where("language_id", $language_id)->where("total", ">=", $total)->get();

                                if (count($db_result) > 0) {

                                    if ($total < 0) {
                                        return 4;
                                    } else {
                                    }
                                } else {
                                    return 3;
                                }
                            } else {
                            }
                        } else {
                        }
                    }
                }
            }

            foreach ((isset($data['lang_dmg']) ? $data['lang_dmg'] : array()) as $key_dmg => $val_dmg) {
                if ($val_dmg != null) {
                    if ($val_dmg != 0) {
                        $key_string_dmg = explode("_", $key_dmg);
                        $title_id_dmg = $key_string_dmg[0];
                        $language_id_dmg = $key_string_dmg[1];
                        $grade_id_dmg = $key_string_dmg[2];
                        $fine_dmg = 0;
                        if (isset($data['lang'][$key_dmg])) {
                            $fine_dmg = $data['lang'][$key_dmg];
                        }
                        $total_dmg = $val_dmg + $fine_dmg;
                        $shipment_id = $data['recieve_id'];

                        if ($language_id_dmg != null && $language_id_dmg != 0) {
                            if ($data["received_grades"][$grade_id_dmg] == true) {
                                $db_result_dmg = book_dis_shipment_detail::where("book_dis_shipments_id", $shipment_id)->where("grade_id", $grade_id_dmg)->where("title_id", $title_id_dmg)
                                    ->where("language_id", $language_id_dmg)->where("total", ">=", $total_dmg)->get();

                                if (count($db_result_dmg) > 0) {

                                    if ($total_dmg < 0) {
                                        return 4;
                                    } else {
                                    }
                                } else {
                                    return 3;
                                }
                            } else {
                            }
                        } else {
                        }
                    }
                }
            }
            return 1;
        } else {
            return 2;
        }
    }

    public static function messagesToSend($input_data, $user)
    {
        if ($input_data != null) {
            $messages = [];
            $grade_title = "";
            $title_title = "";
            $lang_title = "";
            $data = $input_data;
            foreach ($data['lang'] as $key => $val) {
                $key_string = explode("_", $key);
                $title_id = $key_string[0];
                $language_id = $key_string[1];
                $grade_id = $key_string[2];
                $total = $val;

                $node_users = $user->work_node();
                if (count($node_users) > 0) {

                    if ($language_id != null && $language_id != 0) {
                        if ($data["sent_grades"][$grade_id] == true) {
                            $user_node = $node_users->first()->id;

                            if ($total > 0) {
                                $db_result = book_dis_node_balance_detail::where("grade_id", $grade_id)->where("title_id", $title_id)
                                    ->where("language_id", $language_id)->where("node_id", $user_node)->where("total", ">=", $total)->get();

                                if (count($db_result) > 0) {

                                    if ($total < 0) {
                                        array_push($messages, "The provided numbers are invalid. Please correct them and try again.");
                                    } else {
                                    }
                                } else {

                                    if (count(book_dis_meta_grade::where('id', $grade_id)->get()) > 0) {
                                        $grade_title = book_dis_meta_grade::where('id', $grade_id)->first()->title;
                                    }
                                    if (count(book_dis_meta_title::where('id', $title_id)) > 0) {
                                        $title_title = book_dis_meta_title::where('id', $title_id)->first()->title;
                                    }
                                    if (count(book_dis_title_language::where('id', $language_id)) > 0) {
                                        $lang_title = book_dis_title_language::where('id', $language_id)->first();
                                        if ($lang_title != null) {
                                            $lang_title = $lang_title->title;
                                        }
                                    }

                                    $avalaible = $db_result = book_dis_node_balance_detail::where("grade_id", $grade_id)->where("title_id", $title_id)
                                        ->where("language_id", $language_id)->where("node_id", $user_node)->get();
                                    $avalible_total = 0;
                                    if (count($avalaible) > 0) {
                                        $avalible_total = $avalaible->first()->total;
                                    }

                                    array_push($messages, "The available balance of " . $title_title . " for language " . $lang_title . " for " . $grade_title . " is " . $avalible_total . ", but you have provided " . $total . ".");
                                }
                            }
                        }
                    }
                }
            }
            return $messages;
        } else {
        }
    }

    public static function messagesToSendToBeneficiary($input_data, $user)
    {
        if ($input_data != null) {
            $messages = [];
            $grade_title = "";
            $title_title = "";
            $lang_title = "";
            $data = $input_data;
            foreach ($data['lang'] as $key => $val) {
                $key_string = explode("_", $key);
                $title_id = $key_string[0];
                $language_id = $key_string[1];
                $grade_id = $key_string[2];
                $total = $val;

                if ($total != null) {
                    $node_users = $user->work_node();
                    if (count($node_users) > 0) {

                        if ($language_id != null && $language_id != 0) {
                            if ($total > 0) {
                                if ($data["sent_grades"][$grade_id] == true) {
                                    $user_node = $node_users->first()->id;
                                    $db_result = book_dis_node_balance_detail::where("grade_id", $grade_id)->where("title_id", $title_id)
                                        ->where("language_id", $language_id)->where("node_id", (isset($data['node_id']) ? $data['node_id'] : 0))->where("total", ">=", $total)->get();

                                    if (count($db_result) > 0) {

                                        if ($total < 0) {
                                            array_push($messages, "The provided numbers are invalid. Please correct them and try again.");
                                        } else {
                                        }
                                    } else {

                                        if (count(book_dis_meta_grade::where('id', $grade_id)->get()) > 0) {
                                            $grade_title = book_dis_meta_grade::where('id', $grade_id)->first()->title;
                                        }
                                        if (count(book_dis_meta_title::where('id', $title_id)) > 0) {
                                            $title_title = book_dis_meta_title::where('id', $title_id)->first()->title;
                                        }
                                        if (count(book_dis_title_language::where('id', $language_id)) > 0) {
                                            $lang_title = book_dis_title_language::where('id', $language_id)->first();
                                            if ($lang_title != null) {
                                                $lang_title = $lang_title->title;
                                            }
                                        }

                                        $avalaible = $db_result = book_dis_node_balance_detail::where("grade_id", $grade_id)->where("title_id", $title_id)
                                            ->where("language_id", $language_id)->where("node_id", (isset($data['node_id']) ? $data['node_id'] : 0))->get();
                                        $avalible_total = 0;
                                        if (count($avalaible) > 0) {
                                            $avalible_total = $avalaible->first()->total;
                                        }

                                        array_push($messages, "The available balance of " . $title_title . " for language " . $lang_title . " for " . $grade_title . " is " . $avalible_total . ", but you have provided " . $total . ".");
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return $messages;
        } else {
        }
    }

    public static function messagesToSendForReceive($input_data, $user)
    {
        if ($input_data != null) {
            $messages = [];
            $grade_title = "";
            $title_title = "";
            $lang_title = "";
            $data = $input_data;
            foreach ((isset($data['lang']) ? $data['lang'] : array()) as $key => $val) {

                if ($val != null) {
                    if ($val != 0) {
                        $key_string = explode("_", $key);
                        $title_id = $key_string[0];
                        $language_id = $key_string[1];
                        $grade_id = $key_string[2];
                        $damaged = 0;
                        if (isset($data['lang_dmg'][$key])) {
                            $damaged = $data['lang_dmg'][$key];
                        }
                        $total = $val + $damaged;

                        $node_users = $user->work_node();
                        $group_node_users = $user->group_node()->get();
                        if (count($node_users) > 0 || count($group_node_users) > 0) {
                            $shipment_id = $data['recieve_id'];

                            if ($language_id != null && $language_id != 0) {
                                if ($data["received_grades"][$grade_id] == true) {
                                    $db_result = book_dis_shipment_detail::where("book_dis_shipments_id", $shipment_id)->where("grade_id", $grade_id)->where("title_id", $title_id)
                                        ->where("language_id", $language_id)->where("total", ">=", $total)->get();

                                    if (count($db_result) > 0) {

                                        if ($total < 0) {
                                            array_push($messages, "The provided numbers are invalid. Please correct them and try again.");
                                        } else {
                                        }
                                    } else {

                                        if (count(book_dis_meta_grade::where('id', $grade_id)->get()) > 0) {
                                            $grade_title = book_dis_meta_grade::where('id', $grade_id)->first()->title;
                                        }
                                        if (count(book_dis_meta_title::where('id', $title_id)) > 0) {
                                            $title_title = book_dis_meta_title::where('id', $title_id)->first()->title;
                                        }
                                        if (count(book_dis_title_language::where('id', $language_id)) > 0) {
                                            $lang_title = book_dis_title_language::where('id', $language_id)->first();
                                            if ($lang_title != null) {
                                                $lang_title = $lang_title->title;
                                            }
                                        }

                                        $avalaible = book_dis_shipment_detail::where("book_dis_shipments_id", $shipment_id)->where("grade_id", $grade_id)->where("title_id", $title_id)
                                            ->where("language_id", $language_id)->get();
                                        $avalible_total = 0;
                                        if (count($avalaible) > 0) {
                                            $avalible_total = $avalaible->first()->total;
                                        }

                                        array_push($messages, "The available balance of " . $title_title . " for language " . $lang_title . " for " . $grade_title . " is " . $avalible_total . ", but you have provided " . $total . ".");
                                    }
                                }
                            }
                        }
                    }
                }
            }

            /*for reverse checking of data entry, Lang_dmg*/
            foreach ((isset($data['lang_dmg']) ? $data['lang_dmg'] : array()) as $key_dmg => $val_dmg) {

                if ($val_dmg != null) {
                    if ($val_dmg != 0) {
                        $key_string_dmg = explode("_", $key_dmg);
                        $title_id_dmg = $key_string_dmg[0];
                        $language_id_dmg = $key_string_dmg[1];
                        $grade_id_dmg = $key_string_dmg[2];
                        $fine = 0;
                        if (isset($data['lang'][$key_dmg])) {
                            $fine = $data['lang'][$key_dmg];
                        }
                        $total_dmg = $val_dmg + $fine;

                        $node_users = $user->work_node();
                        $group_node_users = $user->group_node()->get();
                        if (count($node_users) > 0 || count($group_node_users) > 0) {
                            $shipment_id = $data['recieve_id'];

                            if ($language_id_dmg != null && $language_id_dmg != 0) {
                                if ($data["received_grades"][$grade_id_dmg] == true) {
                                    $db_result_dmg = book_dis_shipment_detail::where("book_dis_shipments_id", $shipment_id)->where("grade_id", $grade_id_dmg)->where("title_id", $title_id_dmg)
                                        ->where("language_id", $language_id_dmg)->where("total", ">=", $total_dmg)->get();

                                    if (count($db_result_dmg) > 0) {

                                        if ($total_dmg < 0) {
                                            array_push($messages, "The provided numbers are invalid. Please correct them and try again.");
                                        } else {
                                        }
                                    } else {

                                        if (count(book_dis_meta_grade::where('id', $grade_id_dmg)->get()) > 0) {
                                            $grade_title_dmg = book_dis_meta_grade::where('id', $grade_id_dmg)->first()->title;
                                        }
                                        if (count(book_dis_meta_title::where('id', $title_id_dmg)) > 0) {
                                            $title_title_dmg = book_dis_meta_title::where('id', $title_id_dmg)->first()->title;
                                        }
                                        if (count(book_dis_title_language::where('id', $language_id_dmg)) > 0) {
                                            $lang_title_dmg = book_dis_title_language::where('id', $language_id_dmg)->first();
                                            if ($lang_title_dmg != null) {
                                                $lang_title_dmg = $lang_title_dmg->title;
                                            }
                                        }

                                        $avalaible_dmg = book_dis_shipment_detail::where("book_dis_shipments_id", $shipment_id)->where("grade_id", $grade_id_dmg)->where("title_id", $title_id_dmg)
                                            ->where("language_id", $language_id_dmg)->get();
                                        $avalible_total_dmg = 0;
                                        if (count($avalaible_dmg) > 0) {
                                            $avalible_total_dmg = $avalaible_dmg->first()->total;
                                        }

                                        array_push($messages, "The available balance of " . $title_title_dmg . " for language " . $lang_title_dmg . " for " . $grade_title_dmg . " is " . $avalible_total_dmg . ", but you have provided " . $total_dmg . ".");
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return $messages;
        } else {
        }
    }

    public static function createSendToBeneficiShipment($input_data, $user)
    {
        if ($input_data != null) {

            $data = $input_data;
            $success = false;

            $_this = new BookDisRM();
            $node_users_for_search = $user->work_node();
            $search_result = $_this::searchToBanaficShipment($input_data, (isset($data['node_id']) ? $data['node_id'] : 0), (isset($data['node_id']) ? $data['node_id'] : 0));
            if (count($search_result) > 0) {
                //returning 2 means that there are some records at data base with the same data provided, It is not allowed
                return 2;
            } else {

                DB::beginTransaction();
                try {

                    $shipment = new book_dis_shipment();
                    $shipment->title = $data['title'];
                    $shipment->description = (isset($data['description']) ? $data['description'] : "");

                    $shipment->project_id = null;
                    if (isset($data['project_id']) && $data['project_id'] != 0) {
                        $shipment->project_id = $data['project_id'];
                    }

                    $node_users = $user->work_node();
                    if (count($node_users) > 0) {
                        $shipment->from_node()->associate((isset($data['node_id']) ? $data['node_id'] : 0));
                        $shipment->to_node()->associate((isset($data['node_id']) ? $data['node_id'] : 0));
                    }
                    $shipment->creator_user()->associate($user->id);
                    $shipment->send_date = date('Y-m-d', strtotime($data['send_date']));
                    $shipment->to_beneficiary = 1;
                    $shipment->to_beneficiary_type = 1;
                    $shipment->save();

                    $receive_record = new book_dis_shipment_recieve();
                    $receive_record->total_safe = 0;
                    $receive_record->total_general = 0;
                    $receive_record->damaged = 0;
                    $receive_record->lost = 0;
                    $receive_record->received = 1;
                    $receive_record->shipment()->associate($shipment->id);
                    $receive_record->save();

                    $total_amount = 0;
                    foreach ($data['lang'] as $key => $val) {
                        if ($val != null) {
                            $key_string = explode("_", $key);
                            $title_id = $key_string[0];
                            $language_id = $key_string[1];
                            $grade_id = $key_string[2];

                            if ($language_id != null && $language_id != 0) {
                                if ($data["sent_grades"][$grade_id] == true) {

                                    $grade = book_dis_meta_grade::findOrFail($grade_id);
                                    if ($grade != null) {
                                        $shipment_detail = new book_dis_shipment_detail();
                                        $shipment_detail->grade()->associate($grade->id);
                                        $shipment_detail->title()->associate($title_id);
                                        $shipment_detail->language()->associate($language_id);
                                        $shipment_detail->shipment()->associate($shipment->id);
                                        $shipment_detail->total = $val;
                                        $shipment_detail->save();

                                        /*    Making the balance detail for the node */
                                        $db_result_blance_detail = book_dis_node_balance_detail::where("node_id", (isset($data['node_id']) ? $data['node_id'] : 0))
                                            ->where("grade_id", $grade_id)
                                            ->where("title_id", $title_id)
                                            ->where("language_id", $language_id)->get();
                                        if (count($db_result_blance_detail) > 0) {
                                            $db_record_blance_detail = $db_result_blance_detail->first();
                                            $db_record_blance_detail->total = $db_record_blance_detail->total - $val;
                                            $db_record_blance_detail->save();
                                        } else {
                                            if ($val >= 0) {
                                            } else {

                                                $success = false;
                                                DB::rollback();
                                            }
                                        }
                                        /*    Making the balance detail for the node */

                                        $total_amount += $val;
                                    }
                                }
                            }
                        }
                    }

                    $transaction = new book_dis_node_transaction();
                    $transaction->amount = $total_amount;
                    // type has two value  1  for out, 2 for in
                    $transaction->type = 1;
                    $transaction->source_id = $shipment->id;
                    $transaction->creator_user()->associate($user->id);
                    $transaction->save();

                    $node_balance = book_dis_node_balance::where('node_id', (isset($data['node_id']) ? $data['node_id'] : 0))
                        ->where('active', 1)->get();
                    if (count($node_balance) > 0) {
                        $node_balance_old = $node_balance->first();
                        $node_balance_old->update(['active' => 0]);
                        $node_balance_old->save();

                        $node_balance_new = new book_dis_node_balance();
                        //dd("total amount : ".$total_amount . " and the fucking balance amount is = ".$node_balance_old->total );
                        //check if the amount requested to send is more than what is in balance
                        if ($node_balance_old->total >= $total_amount) {
                            $node_balance_new->total = $node_balance_old->total - $total_amount;
                        } else {
                            $parent_node = $node_users->first()->parent_node()->get();
                            if (count($parent_node) > 0) {
                                //this condition means that user is not from root node so can not send the shipment without recieving somthing
                                /*$success = false;
                            DB::rollback();
                            return 0;*/
                            } else {
                                $node_balance_new->total = $node_balance_old->total - $total_amount;
                            }
                        }

                        $node_balance_new->previous_total = $node_balance_old->total;
                        $node_balance_new->amount = $total_amount;
                        // active field has two value 1 for active 0 for unactive
                        $node_balance_new->active = 1;
                        // type has two value  1  for out, 2 for in
                        $node_balance_new->type = 1;
                        $node_balance_new->creator_user()->associate($user->id);
                        $node_balance_new->transaction()->associate($transaction->id);
                        $node_balance_new->node()->associate((isset($data['node_id']) ? $data['node_id'] : 0));
                        $node_balance_new->save();
                    } else {
                        $node_balance_new = new book_dis_node_balance();
                        $node_balance_new->total = 0 - $total_amount;
                        $node_balance_new->previous_total = 0;
                        $node_balance_new->amount = $total_amount;
                        // active field has two value 1 for active 0 for unactive
                        $node_balance_new->active = 1;
                        // type has two value  1  for out, 2 for in
                        $node_balance_new->type = 1;
                        $node_balance_new->creator_user()->associate($user->id);
                        $node_balance_new->transaction()->associate($transaction->id);
                        $node_balance_new->node()->associate((isset($data['node_id']) ? $data['node_id'] : 0));
                        $node_balance_new->save();
                    }

                    DB::commit();
                    $success = true;
                } catch (\Exception $e) {
                    $success = false;
                    DB::rollback();
                }
            }
            if ($success) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public static function createRecieveFromBeneficiShipment($input_data, $user)
    {
        if ($input_data != null) {
            $_this = new BookDisRM();
            $data = $input_data;

            $node_users_for_search = $user->work_node();
            $search_result = $_this::searchToBanaficShipment($input_data, (isset($data['node_id']) ? $data['node_id'] : 0), (isset($data['node_id']) ? $data['node_id'] : 0));
            if (count($search_result) > 0) {
                //returning 2 means that there are some records at data base with the same data provided, It is not allowed
                return 2;
            } else {

                DB::beginTransaction();
                try {

                    $shipment = new book_dis_shipment();
                    $shipment->title = $data['title'];
                    $shipment->description = (isset($data['description']) ? $data['description'] : "");

                    $shipment->project_id = null;
                    if (isset($data['project_id']) && $data['project_id'] != 0) {
                        $shipment->project_id = $data['project_id'];
                    }

                    $node_users = $user->work_node();
                    if (count($node_users) > 0) {
                        $shipment->from_node()->associate((isset($data['node_id']) ? $data['node_id'] : 0));
                        $shipment->to_node()->associate((isset($data['node_id']) ? $data['node_id'] : 0));
                    }
                    $shipment->creator_user()->associate($user->id);
                    $shipment->send_date = date('Y-m-d', strtotime($data['send_date']));
                    $shipment->to_beneficiary = 1;
                    // 2 means retrieve
                    $shipment->to_beneficiary_type = 2;
                    $shipment->save();

                    $receive_record = new book_dis_shipment_recieve();
                    $receive_record->total_safe = 0;
                    $receive_record->total_general = 0;
                    $receive_record->damaged = 0;
                    $receive_record->lost = 0;
                    $receive_record->received = 0;
                    $receive_record->shipment()->associate($shipment->id);
                    $receive_record->save();

                    $total_amount = 0;
                    foreach ($data['lang'] as $key => $val) {
                        $key_string = explode("_", $key);
                        $title_id = $key_string[0];
                        $language_id = $key_string[1];
                        $grade_id = $key_string[2];

                        if ($val != null) {
                            $grade = book_dis_meta_grade::findOrFail($grade_id);
                            if ($grade != null) {
                                $total_amount += $val;
                            }
                        }
                    }

                    $receive_record->received = 1;
                    $receive_record->total_safe = $total_amount;
                    $receive_record->total_general = $total_amount;
                    $receive_record->damaged = 0;
                    $receive_record->lost = 0;
                    $receive_record->save();

                    foreach ($data['lang'] as $key => $val) {
                        $key_string = explode("_", $key);
                        $title_id = $key_string[0];
                        $language_id = $key_string[1];
                        $grade_id = $key_string[2];

                        if ($val != null) {
                            $grade = book_dis_meta_grade::findOrFail($grade_id);
                            if ($grade != null) {
                                $db_result_bok_dis = book_dis_shipment_recieve_detail::where("book_dis_receive_id", $receive_record->id)->where("grade_id", $grade_id)->where("title_id", $title_id)
                                    ->where("language_id", $language_id)->get();
                                if (count($db_result_bok_dis) > 0) {
                                } else {
                                    $shipment_detail = new book_dis_shipment_recieve_detail();
                                    $shipment_detail->grade()->associate($grade_id);
                                    $shipment_detail->title()->associate($title_id);
                                    $shipment_detail->language()->associate($language_id);
                                    $shipment_detail->receive()->associate($receive_record->id);
                                    $shipment_detail->total = $val;
                                    $shipment_detail->save();
                                }

                                /*    Making the balance detail for the node */
                                $db_result_blance_detail = book_dis_node_balance_detail::where("node_id", (isset($data['node_id']) ? $data['node_id'] : 0))
                                    ->where("grade_id", $grade_id)
                                    ->where("title_id", $title_id)
                                    ->where("language_id", $language_id)->get();
                                if (count($db_result_blance_detail) > 0) {
                                    $db_record_blance_detail = $db_result_blance_detail->first();
                                    $db_record_blance_detail->total = $db_record_blance_detail->total + $val;
                                    $db_record_blance_detail->save();
                                } else {
                                    $node_balance_detail = new book_dis_node_balance_detail();
                                    $node_balance_detail->node_id = (isset($data['node_id']) ? $data['node_id'] : 0);
                                    $node_balance_detail->grade_id = $grade_id;
                                    $node_balance_detail->title_id = $title_id;
                                    $node_balance_detail->language_id = $language_id;
                                    $node_balance_detail->total = $val;
                                    $node_balance_detail->save();
                                }
                                /*    Making the balance detail for the node */
                            }
                        }
                    }

                    $transaction = new book_dis_node_transaction();
                    $transaction->amount = $total_amount;
                    // type has two value  1  for out, 2 for in
                    $transaction->type = 2;
                    $transaction->source_id = $shipment->id;
                    $transaction->creator_user()->associate($user->id);
                    $transaction->save();

                    $node_balance = book_dis_node_balance::where('node_id', (isset($data['node_id']) ? $data['node_id'] : 0))
                        ->where('active', 1)->get();
                    if (count($node_balance) > 0) {
                        $node_balance_old = $node_balance->first();
                        $node_balance_old->update(['active' => 0]);
                        $node_balance_old->save();

                        $node_balance_new = new book_dis_node_balance();
                        $node_balance_new->total = $node_balance_old->total + $total_amount;

                        $node_balance_new->previous_total = $node_balance_old->total;
                        $node_balance_new->amount = $total_amount;
                        // active field has two value 1 for active 0 for unactive
                        $node_balance_new->active = 1;
                        // type has two value  1  for out, 2 for in
                        $node_balance_new->type = 2;
                        $node_balance_new->creator_user()->associate($user->id);
                        $node_balance_new->transaction()->associate($transaction->id);
                        $node_balance_new->node()->associate((isset($data['node_id']) ? $data['node_id'] : 0));
                        $node_balance_new->save();
                    } else {
                        $node_balance_new = new book_dis_node_balance();
                        $node_balance_new->total = 0 + $total_amount;
                        $node_balance_new->previous_total = 0;
                        $node_balance_new->amount = $total_amount;
                        // active field has two value 1 for active 0 for inactive
                        $node_balance_new->active = 1;
                        // type has two value  1  for out, 2 for in
                        $node_balance_new->type = 2;
                        $node_balance_new->creator_user()->associate($user->id);
                        $node_balance_new->transaction()->associate($transaction->id);
                        $node_balance_new->node()->associate((isset($data['node_id']) ? $data['node_id'] : 0));
                        $node_balance_new->save();
                    }

                    DB::commit();
                    $success = true;
                } catch (\Exception $e) {
                    $success = false;
                    DB::rollback();
                }
            }
            if ($success) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public static function createReceive($input_data, $user)
    {
        if ($input_data != null) {
            $_this = new BookDisRM();
            $data = $input_data;

            try {
                $shipment = book_dis_shipment::findOrFail($data['recieve_id']);
                $shipment_finding_result = true;
            } catch (\Exception $e) {
                $shipment_finding_result = false;
            }
            if ($shipment_finding_result) {

                $node_users_for_search = $user->work_node();

                /*check if the distination of shipment is at group of nodes for user*/
                $node_group_user = $user->group_node()->get();
                $exist_in_group = false;
                foreach ($node_group_user as $struct) {
                    if ($shipment->to == $struct->id) {
                        $exist_in_group = true;
                        break;
                    }
                }
                /*check if the distination of shipment is at group of nodes for user*/

                if ($shipment->to == $node_users_for_search->first()->id || $exist_in_group) {
                    $shipment_recieve_check = $shipment->receive()->where("received", 0)->get();
                    if (count($shipment_recieve_check) > 0) {

                        $shipment_recieve_record = $shipment_recieve_check->first();

                        $total_amount = 0;
                        $receive_transaction_result = false;
                        DB::beginTransaction();
                        try {

                            // $shipment->receive_date = Carbon::today();
                            $shipment->receive_date = date('Y-m-d', strtotime($data['recieve_date']));
                            $shipment->save();

                            $total_safe = 0;
                            $total_lost = 0;

                            /*checking the main reference of shipment detail and check the lost and dmg based on that -- START*/
                            $total_dmg = 0;
                            $total_safe = 0;
                            $total_lost = 0;
                            $total_sent = 0;
                            $detail_list = book_dis_shipment_detail::where("book_dis_shipments_id", $data['recieve_id'])->get();
                            if (count($detail_list) > 0) {
                                foreach ($detail_list as $item) {
                                    $key_string_search = $item->title_id . "_" . $item->language_id . "_" . $item->grade_id;
                                    $total_dmg += (isset($data['lang_dmg'][$key_string_search]) ? $data['lang_dmg'][$key_string_search] : 0);
                                    $total_safe += (isset($data['lang'][$key_string_search]) ? $data['lang'][$key_string_search] : 0);
                                    $total_sent += $item->total;
                                }
                                $total_lost = $total_sent - ($total_safe + $total_dmg);
                                $shipment_recieve_record->received = 1;
                                $shipment_recieve_record->total_safe = $total_safe;
                                $shipment_recieve_record->total_general = $total_safe + $total_dmg;
                                $shipment_recieve_record->damaged = $total_dmg;
                                $shipment_recieve_record->lost = $total_lost;
                                $shipment_recieve_record->save();

                                foreach ($detail_list as $item) {
                                    $key_string_search = $item->title_id . "_" . $item->language_id . "_" . $item->grade_id;
                                    if (isset($data['lang'][$key_string_search])) {

                                        $db_result_bok_dis = book_dis_shipment_recieve_detail::where("book_dis_receive_id", $shipment_recieve_record->id)->where("grade_id", $item->grade_id)->where("title_id", $item->title_id)
                                            ->where("language_id", $item->language_id)->get();
                                        if (count($db_result_bok_dis) > 0) {
                                        } else {
                                            $shipment_detail = new book_dis_shipment_recieve_detail();
                                            $shipment_detail->grade()->associate($item->grade_id);
                                            $shipment_detail->title()->associate($item->title_id);
                                            $shipment_detail->language()->associate($item->language_id);
                                            $shipment_detail->receive()->associate($shipment_recieve_record->id);
                                            $shipment_detail->total = $data['lang'][$key_string_search];
                                            $shipment_detail->save();
                                        }

                                        /*    Making the balance detail for the node */
                                        $db_result_blance_detail = book_dis_node_balance_detail::where("node_id", $shipment->to)
                                            ->where("grade_id", $item->grade_id)
                                            ->where("title_id", $item->title_id)
                                            ->where("language_id", $item->language_id)->get();
                                        if (count($db_result_blance_detail) > 0) {
                                            $db_record_blance_detail = $db_result_blance_detail->first();
                                            $db_record_blance_detail->total = $db_record_blance_detail->total + $data['lang'][$key_string_search];
                                            $db_record_blance_detail->save();
                                        } else {
                                            $node_balance_detail = new book_dis_node_balance_detail();
                                            $node_balance_detail->node_id = $shipment->to;
                                            $node_balance_detail->grade_id = $item->grade_id;
                                            $node_balance_detail->title_id = $item->title_id;
                                            $node_balance_detail->language_id = $item->language_id;
                                            $node_balance_detail->total = $data['lang'][$key_string_search];
                                            $node_balance_detail->save();
                                        }
                                        /*    Making the balance detail for the node */
                                    }

                                    if (isset($data['lang_dmg'][$key_string_search])) {

                                        $db_result_bok_dis_dmg = book_dis_shipment_recieve_dmg::where("book_dis_receive_id", $shipment_recieve_record->id)->where("grade_id", $item->grade_id)->where("title_id", $item->title_id)
                                            ->where("language_id", $item->language_id)->get();
                                        if (count($db_result_bok_dis_dmg) > 0) {
                                        } else {
                                            $shipment_dmg_detail = new book_dis_shipment_recieve_dmg();
                                            $shipment_dmg_detail->grade()->associate($item->grade_id);
                                            $shipment_dmg_detail->title()->associate($item->title_id);
                                            $shipment_dmg_detail->language()->associate($item->language_id);
                                            $shipment_dmg_detail->receive()->associate($shipment_recieve_record->id);
                                            $shipment_dmg_detail->total = $data['lang_dmg'][$key_string_search];
                                            $shipment_dmg_detail->save();
                                        }
                                    }

                                    /* check if the lost exist in the calculation */
                                    if ($item->total > ((isset($data['lang_dmg'][$key_string_search]) ? $data['lang_dmg'][$key_string_search] : 0) + (isset($data['lang'][$key_string_search]) ? $data['lang'][$key_string_search] : 0))) {

                                        $db_result_bok_dis_lost = book_dis_shipment_recieve_lost::where("book_dis_receive_id", $shipment_recieve_record->id)->where("grade_id", $item->grade_id)->where("title_id", $item->title_id)
                                            ->where("language_id", $item->language_id)->get();
                                        if (count($db_result_bok_dis_lost) > 0) {
                                        } else {
                                            $shipment_lost_detail = new book_dis_shipment_recieve_lost();
                                            $shipment_lost_detail->grade()->associate($item->grade_id);
                                            $shipment_lost_detail->title()->associate($item->title_id);
                                            $shipment_lost_detail->language()->associate($item->language_id);
                                            $shipment_lost_detail->receive()->associate($shipment_recieve_record->id);
                                            $shipment_lost_detail->total = $item->total - ((isset($data['lang_dmg'][$key_string_search]) ? $data['lang_dmg'][$key_string_search] : 0) + (isset($data['lang'][$key_string_search]) ? $data['lang'][$key_string_search] : 0));
                                            $shipment_lost_detail->save();
                                        }
                                    }
                                }
                            }
                            /*checking the main reference of shipment detail and check the lost and dmg based on that -- END*/

                            $transaction = new book_dis_node_transaction();
                            $transaction->amount = $total_safe;
                            // type has two value  1  for out, 2 for in
                            $transaction->type = 2;
                            // source_id indicate the record of data that might be either book_dis_receipt or book_dis_shipment_recieve
                            $transaction->source_id = $shipment_recieve_record->id;
                            $transaction->creator_user()->associate($user->id);
                            $transaction->save();

                            $node_balance = book_dis_node_balance::where('node_id', $shipment->to)
                                ->where('active', 1)->get();
                            if (count($node_balance) > 0) {
                                $node_balance_old = $node_balance->first();
                                $node_balance_old->update(['active' => 0]);
                                $node_balance_old->save();

                                $node_balance_new = new book_dis_node_balance();
                                $node_balance_new->total = $node_balance_old->total + $total_safe;

                                $node_balance_new->previous_total = $node_balance_old->total;
                                $node_balance_new->amount = $total_safe;
                                // active field has two value 1 for active 0 for unactive
                                $node_balance_new->active = 1;
                                // type has two value  1  for out, 2 for in
                                $node_balance_new->type = 2;
                                $node_balance_new->creator_user()->associate($user->id);
                                $node_balance_new->transaction()->associate($transaction->id);
                                $node_balance_new->node()->associate($shipment->to);
                                $node_balance_new->save();
                            } else {
                                $node_balance_new = new book_dis_node_balance();
                                $node_balance_new->total = 0 + $total_safe;
                                $node_balance_new->previous_total = 0;
                                $node_balance_new->amount = $total_safe;
                                // active field has two value 1 for active 0 for inactive
                                $node_balance_new->active = 1;
                                // type has two value  1  for out, 2 for in
                                $node_balance_new->type = 2;
                                $node_balance_new->creator_user()->associate($user->id);
                                $node_balance_new->transaction()->associate($transaction->id);
                                $node_balance_new->node()->associate($shipment->to);
                                $node_balance_new->save();
                            }

                            /*}*/
                            $receive_transaction_result = true;
                            DB::commit();
                            return 1;
                        } catch (\Exception $e) {
                            $receive_transaction_result = false;
                            DB::rollback();
                        }
                    } else {
                        return 0;
                    }
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public static function createReceiveBACKUPSecond10Sep($input_data, $user)
    {
        if ($input_data != null) {
            $_this = new BookDisRM();
            $data = $input_data;

            try {
                $shipment = book_dis_shipment::findOrFail($data['recieve_id']);
                $shipment_finding_result = true;
            } catch (\Exception $e) {
                $shipment_finding_result = false;
            }
            if ($shipment_finding_result) {

                $node_users_for_search = $user->work_node();

                /*check if the distination of shipment is at group of nodes for user*/
                $node_group_user = $user->group_node()->get();
                $exist_in_group = false;
                foreach ($node_group_user as $struct) {
                    if ($shipment->to == $struct->id) {
                        $exist_in_group = true;
                        break;
                    }
                }
                /*check if the distination of shipment is at group of nodes for user*/

                if ($shipment->to == $node_users_for_search->first()->id || $exist_in_group) {
                    $shipment_recieve_check = $shipment->receive()->where("received", 0)->get();
                    if (count($shipment_recieve_check) > 0) {

                        $shipment_recieve_record = $shipment_recieve_check->first();

                        $total_amount = 0;
                        $receive_transaction_result = false;
                        DB::beginTransaction();
                        try {

                            $shipment->receive_date = Carbon::today();
                            $shipment->save();

                            $total_safe = 0;
                            $total_lost = 0;
                            /*foreach((isset($data['lang']) ? $data['lang'] : array()) as $key => $val){
                            if( $val != null){
                            $key_string = explode("_",$key);
                            $title_id = $key_string[0];
                            $language_id = $key_string[1];
                            $grade_id = $key_string[2];

                            if($language_id != null && $language_id != 0) {
                            if ($data["received_grades"][$grade_id] == true) {

                            $total_safe += $val;
                            $db_result = book_dis_shipment_detail::where("book_dis_shipments_id",$data['recieve_id'])->where("grade_id",$grade_id)->where("title_id",$title_id)
                            ->where("language_id",$language_id)->get();
                            if(count($db_result) > 0){
                            $result_record = $db_result->first();
                            $total_lost += $result_record->total - ($val + (isset($data['lang_dmg']) ? (isset($data['lang_dmg'][$key]) ? $data['lang_dmg'][$key] : 0) : 0) );
                            }

                            }
                            }
                            }
                            }

                            $total_dmg = 0;
                            foreach((isset($data['lang_dmg']) ? $data['lang_dmg'] : array()) as $key => $val){

                            if( $val != null){
                            $key_string = explode("_",$key);
                            $title_id = $key_string[0];
                            $language_id = $key_string[1];
                            $grade_id = $key_string[2];
                            if($language_id != null && $language_id != 0) {
                            if ($data["received_grades"][$grade_id] == true) {
                            $total_dmg += $val;
                            }
                            }
                            }
                            }*/

                            /*checking the main reference of shipment detail and check the lost and dmg based on that -- START*/
                            $total_dmg = 0;
                            $total_safe = 0;
                            $total_lost = 0;
                            $total_sent = 0;
                            $detail_list = book_dis_shipment_detail::where("book_dis_shipments_id", $data['recieve_id'])->get();
                            if (count($detail_list) > 0) {
                                foreach ($detail_list as $item) {
                                    $key_string_search = $item->title_id . "_" . $item->language_id . "_" . $item->grade_id;
                                    $total_dmg += (isset($data['lang_dmg'][$key_string_search]) ? $data['lang_dmg'][$key_string_search] : 0);
                                    $total_safe += (isset($data['lang'][$key_string_search]) ? $data['lang'][$key_string_search] : 0);
                                    $total_sent += $item->total;
                                }
                                $total_lost = $total_sent - ($total_safe + $total_dmg);
                            }
                            /*checking the main reference of shipment detail and check the lost and dmg based on that -- END*/

                            //dd("total ".$total_safe."_".$total_dmg."_".$total_lost."_".$total_amount);
                            $shipment_recieve_record->received = 1;
                            $shipment_recieve_record->total_safe = $total_safe;
                            $shipment_recieve_record->total_general = $total_safe + $total_dmg;
                            $shipment_recieve_record->damaged = $total_dmg;
                            $shipment_recieve_record->lost = $total_lost;
                            $shipment_recieve_record->save();

                            $total_amount = 0;
                            foreach ((isset($data['lang']) ? $data['lang'] : array()) as $key => $val) {

                                if ($val != null) {
                                    $key_string = explode("_", $key);
                                    $title_id = $key_string[0];
                                    $language_id = $key_string[1];
                                    $grade_id = $key_string[2];

                                    if ($language_id != null && $language_id != 0) {
                                        if ($data["received_grades"][$grade_id] == true) {

                                            $grade = book_dis_meta_grade::findOrFail($grade_id);
                                            if ($grade != null) {

                                                $db_result_bok_dis_recv_dtal = book_dis_shipment_recieve_detail::where("book_dis_receive_id", $shipment_recieve_record->id)->where("grade_id", $grade_id)->where("title_id", $title_id)
                                                    ->where("language_id", $language_id)->get();
                                                if (count($db_result_bok_dis_recv_dtal) > 0) {
                                                } else {
                                                    $shipment_detail = new book_dis_shipment_recieve_detail();
                                                    $shipment_detail->grade()->associate($grade->id);
                                                    $shipment_detail->title()->associate($title_id);
                                                    $shipment_detail->language()->associate($language_id);
                                                    $shipment_detail->receive()->associate($shipment_recieve_record->id);
                                                    $shipment_detail->total = $val;
                                                    $shipment_detail->save();
                                                }

                                                $temp_total = $val;
                                                if (isset($data['lang_dmg'][$key])) {

                                                    $db_result_bok_dis_dmg = book_dis_shipment_recieve_dmg::where("book_dis_receive_id", $shipment_recieve_record->id)->where("grade_id", $grade_id)->where("title_id", $title_id)
                                                        ->where("language_id", $language_id)->get();
                                                    if (count($db_result_bok_dis_dmg) > 0) {
                                                    } else {
                                                        $shipment_dmg_detail = new book_dis_shipment_recieve_dmg();
                                                        $shipment_dmg_detail->grade()->associate($grade->id);
                                                        $shipment_dmg_detail->title()->associate($title_id);
                                                        $shipment_dmg_detail->language()->associate($language_id);
                                                        $shipment_dmg_detail->receive()->associate($shipment_recieve_record->id);
                                                        $shipment_dmg_detail->total = $data['lang_dmg'][$key];
                                                        $shipment_dmg_detail->save();
                                                        $temp_total = $temp_total + $data['lang_dmg'][$key];
                                                    }
                                                }

                                                $temp_total_lost = 0;
                                                $db_result = book_dis_shipment_detail::where("book_dis_shipments_id", $data['recieve_id'])->where("grade_id", $grade_id)->where("title_id", $title_id)
                                                    ->where("language_id", $language_id)->get();
                                                if (count($db_result) > 0) {
                                                    $result_record = $db_result->first();
                                                    $temp_total_lost = $result_record->total - $temp_total;

                                                    $db_result_bok_dis_lost = book_dis_shipment_recieve_lost::where("book_dis_receive_id", $shipment_recieve_record->id)->where("grade_id", $grade_id)->where("title_id", $title_id)
                                                        ->where("language_id", $language_id)->get();
                                                    if (count($db_result_bok_dis_lost) > 0) {
                                                    } else {
                                                        $shipment_lost_detail = new book_dis_shipment_recieve_lost();
                                                        $shipment_lost_detail->grade()->associate($grade->id);
                                                        $shipment_lost_detail->title()->associate($title_id);
                                                        $shipment_lost_detail->language()->associate($language_id);
                                                        $shipment_lost_detail->receive()->associate($shipment_recieve_record->id);
                                                        $shipment_lost_detail->total = $temp_total_lost;
                                                        $shipment_lost_detail->save();
                                                    }
                                                }

                                                /*    Making the balance detail for the node */
                                                $db_result_blance_detail = book_dis_node_balance_detail::where("node_id", $shipment->to)
                                                    ->where("grade_id", $grade_id)
                                                    ->where("title_id", $title_id)
                                                    ->where("language_id", $language_id)->get();
                                                if (count($db_result_blance_detail) > 0) {
                                                    $db_record_blance_detail = $db_result_blance_detail->first();
                                                    $db_record_blance_detail->total = $db_record_blance_detail->total + $val;
                                                    $db_record_blance_detail->save();
                                                } else {
                                                    $node_balance_detail = new book_dis_node_balance_detail();
                                                    $node_balance_detail->node_id = $shipment->to;
                                                    $node_balance_detail->grade_id = $grade_id;
                                                    $node_balance_detail->title_id = $title_id;
                                                    $node_balance_detail->language_id = $language_id;
                                                    $node_balance_detail->total = $val;
                                                    $node_balance_detail->save();
                                                }
                                                /*    Making the balance detail for the node */

                                                /*$total_amount += $val;*/
                                            }
                                        }
                                    }
                                }
                            }

                            /*to check if all value for lang is null*/
                            $lang_is_null = true;
                            if (isset($data['lang'])) {

                                foreach ((isset($data['lang']) ? $data['lang'] : array()) as $key => $val) {
                                    if ($val != null) {
                                        $lang_is_null = false;
                                    }
                                }
                            }
                            /*to check if all value for lang is null*/

                            /*if the user leave the lang fields blank*/
                            if (isset($data['lang']) == false || $lang_is_null) {

                                foreach ((isset($data['lang_dmg']) ? $data['lang_dmg'] : array()) as $key_dmg => $val_dmg) {

                                    if ($val_dmg != null) {

                                        $key_string_dmg = explode("_", $key_dmg);
                                        $title_id_dmg = $key_string_dmg[0];
                                        $language_id_dmg = $key_string_dmg[1];
                                        $grade_id_dmg = $key_string_dmg[2];

                                        if ($language_id_dmg != null && $language_id_dmg != 0) {
                                            if ($data["received_grades"][$grade_id_dmg] == true) {

                                                $grade_dmg = book_dis_meta_grade::findOrFail($grade_id_dmg);
                                                if ($grade_dmg != null) {

                                                    $db_result_bok_dis_recv_dtal_dmg = book_dis_shipment_recieve_detail::where("book_dis_receive_id", $shipment_recieve_record->id)->where("grade_id", $grade_id_dmg)->where("title_id", $title_id_dmg)
                                                        ->where("language_id", $language_id_dmg)->get();
                                                    if (count($db_result_bok_dis_recv_dtal_dmg) > 0) {
                                                    } else {
                                                        $shipment_detail_dmg = new book_dis_shipment_recieve_detail();
                                                        $shipment_detail_dmg->grade()->associate($grade_dmg->id);
                                                        $shipment_detail_dmg->title()->associate($title_id_dmg);
                                                        $shipment_detail_dmg->language()->associate($language_id_dmg);
                                                        $shipment_detail_dmg->receive()->associate($shipment_recieve_record->id);
                                                        $shipment_detail_dmg->total = $val_dmg;
                                                        $shipment_detail_dmg->save();
                                                    }

                                                    $temp_total_dmg = $val_dmg;

                                                    $db_result_bok_dis = book_dis_shipment_recieve_dmg::where("book_dis_receive_id", $shipment_recieve_record->id)->where("grade_id", $grade_id_dmg)->where("title_id", $title_id_dmg)
                                                        ->where("language_id", $language_id_dmg)->get();
                                                    if (count($db_result_bok_dis) > 0) {
                                                    } else {
                                                        $shipment_dmg_detail = new book_dis_shipment_recieve_dmg();
                                                        $shipment_dmg_detail->grade()->associate($grade_dmg->id);
                                                        $shipment_dmg_detail->title()->associate($title_id_dmg);
                                                        $shipment_dmg_detail->language()->associate($language_id_dmg);
                                                        $shipment_dmg_detail->receive()->associate($shipment_recieve_record->id);
                                                        $shipment_dmg_detail->total = $data['lang_dmg'][$key_dmg];
                                                        $shipment_dmg_detail->save();
                                                    }

                                                    $temp_total_lost_dmg = 0;
                                                    $db_result_dmg = book_dis_shipment_detail::where("book_dis_shipments_id", $data['recieve_id'])->where("grade_id", $grade_id_dmg)->where("title_id", $title_id_dmg)
                                                        ->where("language_id", $language_id_dmg)->get();
                                                    if (count($db_result_dmg) > 0) {
                                                        $result_record_dmg = $db_result_dmg->first();
                                                        $temp_total_lost_dmg = $result_record_dmg->total - $temp_total_dmg;

                                                        $db_result_bok_dis_lost_dmg = book_dis_shipment_recieve_lost::where("book_dis_receive_id", $shipment_recieve_record->id)->where("grade_id", $grade_id_dmg)->where("title_id", $title_id_dmg)
                                                            ->where("language_id", $language_id_dmg)->get();
                                                        if (count($db_result_bok_dis_lost_dmg) > 0) {
                                                        } else {
                                                            $shipment_lost_detail_dmg = new book_dis_shipment_recieve_lost();
                                                            $shipment_lost_detail_dmg->grade()->associate($grade_dmg->id);
                                                            $shipment_lost_detail_dmg->title()->associate($title_id_dmg);
                                                            $shipment_lost_detail_dmg->language()->associate($language_id_dmg);
                                                            $shipment_lost_detail_dmg->receive()->associate($shipment_recieve_record->id);
                                                            $shipment_lost_detail_dmg->total = $temp_total_lost_dmg;
                                                            $shipment_lost_detail_dmg->save();
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            /*if(($data['total_general'] >  $total_amount) || (  $data['lost'] > $total_amount )  || ($data['damaged'] > $total_amount)  || ($data['total_safe'] > $total_amount)){
                            return 0;
                            }else{*/

                            $transaction = new book_dis_node_transaction();
                            $transaction->amount = $total_safe;
                            // type has two value  1  for out, 2 for in
                            $transaction->type = 2;
                            // source_id indicate the record of data that might be either book_dis_receipt or book_dis_shipment_recieve
                            $transaction->source_id = $shipment_recieve_record->id;
                            $transaction->creator_user()->associate($user->id);
                            $transaction->save();

                            $node_balance = book_dis_node_balance::where('node_id', $shipment->to)
                                ->where('active', 1)->get();
                            if (count($node_balance) > 0) {
                                $node_balance_old = $node_balance->first();
                                $node_balance_old->update(['active' => 0]);
                                $node_balance_old->save();

                                $node_balance_new = new book_dis_node_balance();
                                $node_balance_new->total = $node_balance_old->total + $total_safe;

                                $node_balance_new->previous_total = $node_balance_old->total;
                                $node_balance_new->amount = $total_safe;
                                // active field has two value 1 for active 0 for unactive
                                $node_balance_new->active = 1;
                                // type has two value  1  for out, 2 for in
                                $node_balance_new->type = 2;
                                $node_balance_new->creator_user()->associate($user->id);
                                $node_balance_new->transaction()->associate($transaction->id);
                                $node_balance_new->node()->associate($shipment->to);
                                $node_balance_new->save();
                            } else {
                                $node_balance_new = new book_dis_node_balance();
                                $node_balance_new->total = 0 + $total_safe;
                                $node_balance_new->previous_total = 0;
                                $node_balance_new->amount = $total_safe;
                                // active field has two value 1 for active 0 for inactive
                                $node_balance_new->active = 1;
                                // type has two value  1  for out, 2 for in
                                $node_balance_new->type = 2;
                                $node_balance_new->creator_user()->associate($user->id);
                                $node_balance_new->transaction()->associate($transaction->id);
                                $node_balance_new->node()->associate($shipment->to);
                                $node_balance_new->save();
                            }

                            /*}*/

                            $receive_transaction_result = true;
                            DB::commit();
                            return 1;
                        } catch (\Exception $e) {
                            $receive_transaction_result = false;
                            DB::rollback();
                        }
                    } else {
                        return 0;
                    }
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public static function createCOPYBACKUPReceive($input_data, $user)
    {
        if ($input_data != null) {
            $_this = new BookDisRM();
            $data = $input_data;

            try {
                $shipment = book_dis_shipment::findOrFail($data['recieve_id']);
                $shipment_finding_result = true;
            } catch (\Exception $e) {
                $shipment_finding_result = false;
            }
            if ($shipment_finding_result) {

                $node_users_for_search = $user->work_node();
                if ($shipment->to == $node_users_for_search->first()->id) {
                    $shipment_recieve_check = $shipment->receive()->where("received", 0)->get();
                    if (count($shipment_recieve_check) > 0) {

                        $shipment_recieve_record = $shipment_recieve_check->first();

                        $total_amount = 0;
                        $receive_transaction_result = false;
                        DB::beginTransaction();
                        try {

                            $shipment->receive_date = Carbon::today();
                            $shipment->save();

                            $total_safe = 0;
                            $total_lost = 0;
                            foreach ($data['lang'] as $key => $val) {
                                if ($val != null) {
                                    $key_string = explode("_", $key);
                                    $title_id = $key_string[0];
                                    $language_id = $key_string[1];
                                    $grade_id = $key_string[2];

                                    if ($language_id != null && $language_id != 0) {
                                        if ($data["received_grades"][$grade_id] == true) {

                                            $total_safe += $val;
                                            $db_result = book_dis_shipment_detail::where("book_dis_shipments_id", $data['recieve_id'])->where("grade_id", $grade_id)->where("title_id", $title_id)
                                                ->where("language_id", $language_id)->get();
                                            if (count($db_result) > 0) {
                                                $result_record = $db_result->first();
                                                $total_lost += $result_record->total - ($val + $data['lang_dmg'][$key]);
                                            }
                                        }
                                    }
                                }
                            }

                            $total_dmg = 0;
                            foreach ($data['lang_dmg'] as $key => $val) {

                                if ($val != null) {
                                    if ($language_id != null && $language_id != 0) {
                                        if ($data["received_grades"][$grade_id] == true) {
                                            $total_dmg += $val;
                                        }
                                    }
                                }
                            }

                            //dd("total ".$total_safe."_".$total_dmg."_".$total_lost."_".$total_amount);
                            $shipment_recieve_record->received = 1;
                            $shipment_recieve_record->total_safe = $total_safe;
                            $shipment_recieve_record->total_general = $total_safe + $total_dmg;
                            $shipment_recieve_record->damaged = $total_dmg;
                            $shipment_recieve_record->lost = $total_lost;
                            $shipment_recieve_record->save();

                            $total_amount = 0;
                            foreach ($data['lang'] as $key => $val) {

                                if ($val != null) {
                                    $key_string = explode("_", $key);
                                    $title_id = $key_string[0];
                                    $language_id = $key_string[1];
                                    $grade_id = $key_string[2];

                                    if ($language_id != null && $language_id != 0) {
                                        if ($data["received_grades"][$grade_id] == true) {

                                            $grade = book_dis_meta_grade::findOrFail($grade_id);
                                            if ($grade != null) {

                                                $db_result_bok_dis_recv_dtal = book_dis_shipment_recieve_detail::where("book_dis_receive_id", $shipment_recieve_record->id)->where("grade_id", $grade_id)->where("title_id", $title_id)
                                                    ->where("language_id", $language_id)->get();
                                                if (count($db_result_bok_dis_recv_dtal) > 0) {
                                                } else {
                                                    $shipment_detail = new book_dis_shipment_recieve_detail();
                                                    $shipment_detail->grade()->associate($grade->id);
                                                    $shipment_detail->title()->associate($title_id);
                                                    $shipment_detail->language()->associate($language_id);
                                                    $shipment_detail->receive()->associate($shipment_recieve_record->id);
                                                    $shipment_detail->total = $val;
                                                    $shipment_detail->save();
                                                }

                                                $temp_total = $val;
                                                if (isset($data['lang_dmg'][$key])) {

                                                    $db_result_bok_dis_dmg = book_dis_shipment_recieve_dmg::where("book_dis_receive_id", $shipment_recieve_record->id)->where("grade_id", $grade_id)->where("title_id", $title_id)
                                                        ->where("language_id", $language_id)->get();
                                                    if (count($db_result_bok_dis_dmg) > 0) {
                                                    } else {
                                                        $shipment_dmg_detail = new book_dis_shipment_recieve_dmg();
                                                        $shipment_dmg_detail->grade()->associate($grade->id);
                                                        $shipment_dmg_detail->title()->associate($title_id);
                                                        $shipment_dmg_detail->language()->associate($language_id);
                                                        $shipment_dmg_detail->receive()->associate($shipment_recieve_record->id);
                                                        $shipment_dmg_detail->total = $data['lang_dmg'][$key];
                                                        $shipment_dmg_detail->save();
                                                        $temp_total = $temp_total + $data['lang_dmg'][$key];
                                                    }
                                                }

                                                $temp_total_lost = 0;
                                                $db_result = book_dis_shipment_detail::where("book_dis_shipments_id", $data['recieve_id'])->where("grade_id", $grade_id)->where("title_id", $title_id)
                                                    ->where("language_id", $language_id)->get();
                                                if (count($db_result) > 0) {
                                                    $result_record = $db_result->first();
                                                    $temp_total_lost = $result_record->total - $temp_total;

                                                    $db_result_bok_dis_lost = book_dis_shipment_recieve_lost::where("book_dis_receive_id", $shipment_recieve_record->id)->where("grade_id", $grade_id)->where("title_id", $title_id)
                                                        ->where("language_id", $language_id)->get();
                                                    if (count($db_result_bok_dis_lost) > 0) {
                                                    } else {
                                                        $shipment_lost_detail = new book_dis_shipment_recieve_lost();
                                                        $shipment_lost_detail->grade()->associate($grade->id);
                                                        $shipment_lost_detail->title()->associate($title_id);
                                                        $shipment_lost_detail->language()->associate($language_id);
                                                        $shipment_lost_detail->receive()->associate($shipment_recieve_record->id);
                                                        $shipment_lost_detail->total = $temp_total_lost;
                                                        $shipment_lost_detail->save();
                                                    }
                                                }

                                                /*    Making the balance detail for the node */
                                                $db_result_blance_detail = book_dis_node_balance_detail::where("node_id", $node_users_for_search->first()->id)
                                                    ->where("grade_id", $grade_id)
                                                    ->where("title_id", $title_id)
                                                    ->where("language_id", $language_id)->get();
                                                if (count($db_result_blance_detail) > 0) {
                                                    $db_record_blance_detail = $db_result_blance_detail->first();
                                                    $db_record_blance_detail->total = $db_record_blance_detail->total + $val;
                                                    $db_record_blance_detail->save();
                                                } else {
                                                    $node_balance_detail = new book_dis_node_balance_detail();
                                                    $node_balance_detail->node_id = $node_users_for_search->first()->id;
                                                    $node_balance_detail->grade_id = $grade_id;
                                                    $node_balance_detail->title_id = $title_id;
                                                    $node_balance_detail->language_id = $language_id;
                                                    $node_balance_detail->total = $val;
                                                    $node_balance_detail->save();
                                                }
                                                /*    Making the balance detail for the node */

                                                /*$total_amount += $val;*/
                                            }
                                        }
                                    }
                                }
                            }

                            /*if(($data['total_general'] >  $total_amount) || (  $data['lost'] > $total_amount )  || ($data['damaged'] > $total_amount)  || ($data['total_safe'] > $total_amount)){
                            return 0;
                            }else{*/

                            $transaction = new book_dis_node_transaction();
                            $transaction->amount = $total_safe;
                            // type has two value  1  for out, 2 for in
                            $transaction->type = 2;
                            // source_id indicate the record of data that might be either book_dis_receipt or book_dis_shipment_recieve
                            $transaction->source_id = $shipment_recieve_record->id;
                            $transaction->creator_user()->associate($user->id);
                            $transaction->save();

                            $node_balance = book_dis_node_balance::where('node_id', $node_users_for_search->first()->id)
                                ->where('active', 1)->get();
                            if (count($node_balance) > 0) {
                                $node_balance_old = $node_balance->first();
                                $node_balance_old->update(['active' => 0]);
                                $node_balance_old->save();

                                $node_balance_new = new book_dis_node_balance();
                                $node_balance_new->total = $node_balance_old->total + $total_safe;

                                $node_balance_new->previous_total = $node_balance_old->total;
                                $node_balance_new->amount = $total_safe;
                                // active field has two value 1 for active 0 for unactive
                                $node_balance_new->active = 1;
                                // type has two value  1  for out, 2 for in
                                $node_balance_new->type = 2;
                                $node_balance_new->creator_user()->associate($user->id);
                                $node_balance_new->transaction()->associate($transaction->id);
                                $node_balance_new->node()->associate($node_users_for_search->first()->id);
                                $node_balance_new->save();
                            } else {
                                $node_balance_new = new book_dis_node_balance();
                                $node_balance_new->total = 0 + $total_safe;
                                $node_balance_new->previous_total = 0;
                                $node_balance_new->amount = $total_safe;
                                // active field has two value 1 for active 0 for inactive
                                $node_balance_new->active = 1;
                                // type has two value  1  for out, 2 for in
                                $node_balance_new->type = 2;
                                $node_balance_new->creator_user()->associate($user->id);
                                $node_balance_new->transaction()->associate($transaction->id);
                                $node_balance_new->node()->associate($node_users_for_search->first()->id);
                                $node_balance_new->save();
                            }

                            /*}*/

                            $receive_transaction_result = true;
                            DB::commit();
                            return 1;
                        } catch (\Exception $e) {
                            $receive_transaction_result = false;
                            DB::rollback();
                        }
                    } else {
                        return 0;
                    }
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public static function searchShipment($input_data, $from, $to)
    {
        if ($input_data != null) {

            $data = $input_data;
            if (isset($data['title'])) {

                $sub_query = (isset($data['description']) ? " and bdsh.`description` = '" . $data['description'] . "'" : " ");
                $query_string = "
                 select * from `book_dis_shipments` as bdsh
						 where
						 (bdsh.title = '" . $data['title'] . "' " . $sub_query . ")
						 and
                         (bdsh.from = " . $from . " and bdsh.to = " . $to . ")
                         and
                          bdsh.send_date ='" . date('Y-m-d', strtotime($data['send_date'])) . "'";

                $shipment_result = DB::select(DB::raw($query_string));
                if (count($shipment_result) > 0) {
                    return $shipment_result;
                } else {
                    return array();
                }
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public static function searchToBanaficShipment($input_data, $from, $to)
    {
        if ($input_data != null) {

            $data = $input_data;
            if (isset($data['title'])) {
                $query_string = "
                 select * from `book_dis_shipments` as bdsh
						 where
						 (bdsh.title = '" . $data['title'] . "' and bdsh.`description` = '" . (isset($data['description']) ? $data['description'] : "") . "')
						 and
                         (bdsh.from = " . $from . " and bdsh.to = " . $to . ")
                         and
                          bdsh.send_date ='" . date('Y-m-d', strtotime($data['send_date'])) . "'";

                $shipment_result = DB::select(DB::raw($query_string));
                if (count($shipment_result) > 0) {
                    return $shipment_result;
                } else {
                    return array();
                }
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public static function searchUser($input_data)
    {
        if ($input_data != null) {

            $data = $input_data;
            $name_parm = "";

            if (isset($data['name'])) {
                $name_parm = $data['name'];
            }
            /*
             * select  users who are in this "book_distribution_staff" and are not assinged to any node
             * */
            $query_string = "
                      select usr.id,usr.name,usr.email from users as usr where
                        (usr.name like '%" . $name_parm . "%' OR usr.email = '" . $data['email'] . "')
                        and
                        (
                            (usr.id Not In
                                (select usr_nod.`user_id` from `book_dis_node_staffs` as usr_nod GROUP BY usr_nod.`user_id`))
                            and
                            (usr.id In
                                (select usr_role.`user_id` from `lcc_role_user` as usr_role where usr_role.`role_id` = (select role.id from lcc_roles as role where role.`name` = 'book_distribution_staff')))
                        )";

            //book level creation
            $user_result = DB::select(DB::raw($query_string));

            if (count($user_result) > 0) {
                return $user_result;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    public static function attachUserToNode($node_id, $user_id)
    {

        $node = book_dis_node::findOrFail($node_id);
        $user = user::findOrFail($user_id);
        if ($node != null && $user != null) {
            //$user->work_node()->associate($node_id);
            $user->work_node()->attach($node_id);
            // to make it deatach just use $user->work_node()->detach();
            return 1;
        } else {
            return 0;
        }
    }

    public static function sentShipmentList($page, $url, $user_id)
    {
        $user = user::findOrFail($user_id);
        if ($user != null) {
            $nodes = $user->work_node();
            if (count($nodes) > 0) {
                $node = $nodes->first();
                $query_string = "select bdsh.id,bdsh.title,bdsh.`description`,bdsh.send_date,bdsh.receive_date,
                         (select bdn.title from book_dis_nodes as bdn where bdn.id = bdsh.from) as from_node,
                         (select bdn.title from book_dis_nodes as bdn where bdn.id = bdsh.to) as to_node,
                         (select usr.name from users as usr where usr.id = bdsh.`creator_id`) as sender_name,
                         (select bdsr.`received` from `book_dis_shipment_recieves` as bdsr where bdsr.`book_dis_shipments_id` = bdsh.id limit 1) as recieved_status,
                         (select count(bdsfiles.id) from `book_dis_shipment_files` as bdsfiles where bdsfiles.`book_dis_shipments_id` = bdsh.id) as docs
                         from `book_dis_shipments` as bdsh where bdsh.`from` = " . $node->id . " and bdsh.to_beneficiary != 1
                         ORDER BY bdsh.`created_at` DESC

                                  ";
                //book level creation
                $user_result = DB::select(DB::raw($query_string));
                $perPage = 3;
                $offset = ($page * $perPage) - $perPage;

                $user_result = new LengthAwarePaginator(
                    array_slice($user_result, $offset, $perPage, true),
                    count($user_result),
                    $perPage,
                    $page
                );
                return $user_result;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public static function sentNodeShipmentList($node_id, $url, $user_id)
    {

        $user = user::findOrFail($user_id);
        if ($user != null) {
            $query_string = "select bdsh.id,bdsh.title,bdsh.`description`,bdsh.send_date,bdsh.receive_date,
                         (select bdn.title from book_dis_nodes as bdn where bdn.id = bdsh.from) as from_node,
                         (select bdn.title from book_dis_nodes as bdn where bdn.id = bdsh.to) as to_node,
                         (select usr.name from users as usr where usr.id = bdsh.`creator_id`) as sender_name,
                         (select bdsr.`received` from `book_dis_shipment_recieves` as bdsr where bdsr.`book_dis_shipments_id` = bdsh.id limit 1) as recieved_status,
                         (select bdsr.id from `book_dis_shipment_recieves` as bdsr where bdsr.`book_dis_shipments_id` = bdsh.id LIMIT 1) as recieved_id
                         from `book_dis_shipments` as bdsh where bdsh.`from` = " . $node_id . "
                         and bdsh.`to_beneficiary` = 0
                         ORDER BY bdsh.`created_at` DESC

                                  ";
            //book level creation
            $user_result = DB::select(DB::raw($query_string));
            return $user_result;
        } else {
            return array();
        }
    }

    public static function sentShipmentList_all($user_id)
    {
        $user = user::findOrFail($user_id);
        if ($user != null) {
            $nodes = $user->work_node();
            if (count($nodes) > 0) {
                $node = $nodes->first();
                $query_string = "select bdsh.id,bdsh.title,bdsh.`description`,bdsh.send_date,bdsh.receive_date,
                         (select bdn.title from book_dis_nodes as bdn where bdn.id = bdsh.from) as from_node,
                         (select bdn.title from book_dis_nodes as bdn where bdn.id = bdsh.to) as to_node,
                         (select usr.name from users as usr where usr.id = bdsh.`creator_id`) as sender_name,
                         (select bdsr.`received` from `book_dis_shipment_recieves` as bdsr where bdsr.`book_dis_shipments_id` = bdsh.id limit 1) as recieved_status,
                         (select count(bdsfiles.id) from `book_dis_shipment_files` as bdsfiles where bdsfiles.`book_dis_shipments_id` = bdsh.id) as docs
                         from `book_dis_shipments` as bdsh where bdsh.`from` = " . $node->id . "
                         ORDER BY bdsh.`created_at` DESC
                                  ";
                //book level creation
                $user_result = DB::select(DB::raw($query_string));
                return $user_result;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public static function getSentShipmentItem($record_id)
    {
        $shipment = book_dis_shipment::findOrFail($record_id);
        if ($shipment != null) {
            $query_string = "select bdshd.id,bdshd.total,bdshd.`created_at`,bdshd.`updated_at`,bdshd.`book_dis_shipments_id`,
						(select grade.`title` from `book_dis_meta_grades` as grade where grade.id = bdshd.`grade_id`) as grade,
					    (select title.`title` from `book_dis_meta_titles` as title where title.id = bdshd.`title_id`) as title,
					    (select lang.`title` from `book_dis_title_languages` as lang where lang.id = bdshd.`language_id`) as language,
					    bdshd.`language_id`

						 from `book_dis_shipment_details` as bdshd where bdshd.`book_dis_shipments_id` = " . $record_id . "

                                  ";
            //book level creation
            $user_result = DB::select(DB::raw($query_string));
            return $user_result;
        } else {
            return null;
        }
    }

    public static function getShipmentDocuments($shipment_id)
    {
        $shipment = book_dis_shipment::findOrFail($shipment_id);
        $sent_docs = array();
        $reciept_docs = array();
        $result = array();
        if ($shipment != null) {
            $sent_docs = $shipment->docs()->where('type', 0)->get();
            $reciept_docs = $shipment->docs()->where('type', 1)->get();
            $result = ["sent_docs" => $sent_docs, "reciept_docs" => $reciept_docs];

            return $result;
        } else {
            return null;
        }
    }

    public static function getSentShipmentItemGroup($record_id)
    {
        $shipment = book_dis_shipment::findOrFail($record_id);
        if ($shipment != null) {
            $query_string = "select bdshd.id,bdshd.total,bdshd.`created_at`,bdshd.`updated_at`,bdshd.`book_dis_shipments_id`,
						(select grade.`title` from `book_dis_meta_grades` as grade where grade.id = bdshd.`grade_id`) as grade,
					    (select title.`title` from `book_dis_meta_titles` as title where title.id = bdshd.`title_id`) as title,
					    (select lang.`title` from `book_dis_title_languages` as lang where lang.id = bdshd.`language_id`) as language,
					 	bdshd.`language_id`

						 from `book_dis_shipment_details` as bdshd where bdshd.`book_dis_shipments_id` = " . $record_id . "
						 GROUP BY bdshd.`language_id`
                                  ";
            //book level creation
            $user_result = DB::select(DB::raw($query_string));
            return $user_result;
        } else {
            return null;
        }
    }

    public static function getReceiveShipmentItemGroup($record_id)
    {
        $shipment = book_dis_shipment_recieve::findOrFail($record_id);
        if ($shipment != null) {
            $query_string = " select bdshd.id,bdshd.total,bdshd.`created_at`,bdshd.`updated_at`,bdshd.`book_dis_shipments_id`,
						(select grade.`title` from `book_dis_meta_grades` as grade where grade.id = bdshd.`grade_id`) as grade,
					    (select title.`title` from `book_dis_meta_titles` as title where title.id = bdshd.`title_id`) as title,
					    (select lang.`title` from `book_dis_title_languages` as lang where lang.id = bdshd.`language_id`) as language,
					 	bdshd.`language_id`

						 from `book_dis_shipment_details` as bdshd where bdshd.`book_dis_shipments_id` = (select bdsr.`book_dis_shipments_id` from `book_dis_shipment_recieves` as bdsr where bdsr.id =  " . $record_id . ")

						 GROUP BY bdshd.`language_id`
                                  ";
            //book level creation
            $user_result = DB::select(DB::raw($query_string));
            return $user_result;
        } else {
            return null;
        }
    }

    public static function getReceiveShipmentItem($record_id)
    {

        $query_string = "select bdshd.id,bdshd.total,bdshd.`created_at`,bdshd.`updated_at`,bdshd.`book_dis_shipments_id`,
						(select grade.`title` from `book_dis_meta_grades` as grade where grade.id = bdshd.`grade_id`) as grade,
					    (select title.`title` from `book_dis_meta_titles` as title where title.id = bdshd.`title_id`) as title,
					    (select lang.`title` from `book_dis_title_languages` as lang where lang.id = bdshd.`language_id`) as language,
					    bdshd.`language_id`

						 from `book_dis_shipment_details` as bdshd where bdshd.`book_dis_shipments_id` = (select bdsr.`book_dis_shipments_id` from `book_dis_shipment_recieves` as bdsr where bdsr.id =  " . $record_id . ")

                                  ";
        //book level creation
        $user_result = DB::select(DB::raw($query_string));
        return $user_result;
    }

    public static function getSentShipment($record_id)
    {
        $shipment = book_dis_shipment::findOrFail($record_id);
        if ($shipment != null) {
            $sent_grades_detail = [];
            $query_string = "select bdsd.`grade_id`, (select bdmg.`title` from `book_dis_meta_grades` as bdmg where bdmg.id = bdsd.`grade_id` ) as grade_title
                             from `book_dis_shipment_details` as bdsd where bdsd.`book_dis_shipments_id` = " . $shipment->id . "
                             GROUP BY bdsd.`grade_id`";
            $detail_result = DB::select(DB::raw($query_string));
            if (count($detail_result) > 0) {
                foreach ($detail_result as $key => $value) {
                    $query_string_sent_detail = " select bdsd.total as total,
                                                  (select title.`title` from `book_dis_meta_titles` as title where title.id = bdsd.`title_id`) as title,
                                                  (select lang.`title` from `book_dis_title_languages` as lang where lang.id = bdsd.`language_id`) as language
                                                  from `book_dis_shipment_details` as bdsd where bdsd.`book_dis_shipments_id` = " . $shipment->id . " and bdsd.`grade_id` = " . $value->grade_id . "  ORDER BY bdsd.`title_id`";
                    $detail_result_sent = DB::select(DB::raw($query_string_sent_detail));
                    $sent_grades_detail[$value->grade_title] = $detail_result_sent;
                }
            }

            $recieve_record = book_dis_shipment_recieve::where("book_dis_shipments_id", $shipment->id)->limit(1)->get();
            if (count($recieve_record) > 0) {

                if ($recieve_record[0]->received == 1) {

                    $receive_grades_detail = [];
                    $query_string_receive = "select bdsrd.`grade_id`, (select bdmg.`title` from `book_dis_meta_grades` as bdmg where bdmg.id = bdsrd.`grade_id` ) as grade_title
                                    from `book_dis_shipment_recieve_details` as bdsrd where bdsrd.`book_dis_receive_id` =
                                        (
                                            select bdsr.id from `book_dis_shipment_recieves` as bdsr where bdsr.`book_dis_shipments_id` = " . $shipment->id . " limit 1
                                        )
                                    GROUP BY bdsrd.`grade_id`";
                    $detail_result_receive = DB::select(DB::raw($query_string_receive));
                    if (count($detail_result_receive) > 0) {
                        foreach ($detail_result_receive as $key => $value) {
                            $query_string_receive_detail = " select bdsrd.`total` as total,
                                                            (select title.`title` from `book_dis_meta_titles` as title where title.id = bdsrd.`title_id`) as title,
                                                            (select lang.`title` from `book_dis_title_languages` as lang where lang.id = bdsrd.`language_id`) as language
                                                            from `book_dis_shipment_recieve_details` as bdsrd where bdsrd.`book_dis_receive_id` =
                                                                (
                                                                    select bdsr.id from `book_dis_shipment_recieves` as bdsr where bdsr.`book_dis_shipments_id` = " . $shipment->id . " limit 1
                                                                )
                                                            and bdsrd.`grade_id` = " . $value->grade_id . "
                                                            ORDER BY bdsrd.`title_id`";
                            $detail_result_receive = DB::select(DB::raw($query_string_receive_detail));
                            $receive_grades_detail[$value->grade_title] = $detail_result_receive;
                        }
                    }

                    $receive_dmg_grades_detail = [];
                    $query_string_receive_dmg = "select bdsrd.`grade_id`, (select bdmg.`title` from `book_dis_meta_grades` as bdmg where bdmg.id = bdsrd.`grade_id` ) as grade_title
                                    from `book_dis_shipment_recieve_dmgs` as bdsrd where bdsrd.`book_dis_receive_id` =
                                        (
                                            select bdsr.id from `book_dis_shipment_recieves` as bdsr where bdsr.`book_dis_shipments_id` = " . $shipment->id . " limit 1
                                        )
                                    GROUP BY bdsrd.`grade_id`";
                    $detail_result_receive_dmg = DB::select(DB::raw($query_string_receive_dmg));
                    if (count($detail_result_receive_dmg) > 0) {
                        foreach ($detail_result_receive_dmg as $key => $value) {
                            $query_string_receive_detail_dmg = " select bdsrd.`total` as total,
                                                            (select title.`title` from `book_dis_meta_titles` as title where title.id = bdsrd.`title_id`) as title,
                                                            (select lang.`title` from `book_dis_title_languages` as lang where lang.id = bdsrd.`language_id`) as language
                                                            from `book_dis_shipment_recieve_dmgs` as bdsrd where bdsrd.`book_dis_receive_id` =
                                                                (
                                                                    select bdsr.id from `book_dis_shipment_recieves` as bdsr where bdsr.`book_dis_shipments_id` = " . $shipment->id . " limit 1
                                                                )
                                                            and bdsrd.`grade_id` = " . $value->grade_id . "
                                                            ORDER BY bdsrd.`title_id`";
                            $detail_result_receive_dmg = DB::select(DB::raw($query_string_receive_detail_dmg));
                            $receive_dmg_grades_detail[$value->grade_title] = $detail_result_receive_dmg;
                        }
                    }

                    $receive_lost_grades_detail = [];
                    $query_string_receive_lost = "select bdsrd.`grade_id`, (select bdmg.`title` from `book_dis_meta_grades` as bdmg where bdmg.id = bdsrd.`grade_id` ) as grade_title
                                    from `book_dis_shipment_recieve_losts` as bdsrd where bdsrd.`book_dis_receive_id` =
                                        (
                                            select bdsr.id from `book_dis_shipment_recieves` as bdsr where bdsr.`book_dis_shipments_id` = " . $shipment->id . " limit 1
                                        )
                                    GROUP BY bdsrd.`grade_id`";
                    $detail_result_receive_lost = DB::select(DB::raw($query_string_receive_lost));
                    if (count($detail_result_receive_lost) > 0) {
                        foreach ($detail_result_receive_lost as $key => $value) {
                            $query_string_receive_detail_lost = " select bdsrd.`total` as total,
                                                            (select title.`title` from `book_dis_meta_titles` as title where title.id = bdsrd.`title_id`) as title,
                                                            (select lang.`title` from `book_dis_title_languages` as lang where lang.id = bdsrd.`language_id`) as language
                                                            from `book_dis_shipment_recieve_losts` as bdsrd where bdsrd.`book_dis_receive_id` =
                                                                (
                                                                    select bdsr.id from `book_dis_shipment_recieves` as bdsr where bdsr.`book_dis_shipments_id` = " . $shipment->id . " limit 1
                                                                )
                                                            and bdsrd.`grade_id` = " . $value->grade_id . "
                                                            ORDER BY bdsrd.`title_id`";
                            $detail_result_receive_lost = DB::select(DB::raw($query_string_receive_detail_lost));
                            $receive_lost_grades_detail[$value->grade_title] = $detail_result_receive_lost;
                        }
                    }

                    return array(
                        "sent_shipment" => $shipment, "receive_record" => $recieve_record[0],
                        'sent_detail' => $sent_grades_detail,
                        'sent_received_detail' => $receive_grades_detail,
                        'receive_dmg_grades_detail' => $receive_dmg_grades_detail,
                        'receive_lost_grades_detail' => $receive_lost_grades_detail,
                    );
                } else {
                    return array(
                        "sent_shipment" => $shipment, "receive_record" => $recieve_record[0],
                        'sent_detail' => $sent_grades_detail,
                    );
                }
            } else {
                return array(
                    "sent_shipment" => $shipment, "receive_record" => array(),
                    'sent_detail' => $sent_grades_detail,
                );
            }
        } else {
            return null;
        }
    }

    public static function getSentRecieveShipment($record_id)
    {

        $recieve_shipment = book_dis_shipment_recieve::where("id", $record_id)->get();
        if (count($recieve_shipment) > 0) {
            $recieve_shipment = $recieve_shipment->first();

            $shipment = book_dis_shipment::where("id", $recieve_shipment->book_dis_shipments_id)->get();
            if (count($shipment) > 0) {
                $shipment = $shipment->first();
                $sent_grades_detail = [];
                $query_string = "select bdsd.`grade_id`, (select bdmg.`title` from `book_dis_meta_grades` as bdmg where bdmg.id = bdsd.`grade_id` ) as grade_title
                             from `book_dis_shipment_details` as bdsd where bdsd.`book_dis_shipments_id` = " . $shipment->id . "
                             GROUP BY bdsd.`grade_id`";
                $detail_result = DB::select(DB::raw($query_string));
                if (count($detail_result) > 0) {
                    foreach ($detail_result as $key => $value) {
                        $query_string_sent_detail = " select bdsd.total as total,
                                                  (select title.`title` from `book_dis_meta_titles` as title where title.id = bdsd.`title_id`) as title,
                                                  (select lang.`title` from `book_dis_title_languages` as lang where lang.id = bdsd.`language_id`) as language
                                                  from `book_dis_shipment_details` as bdsd where bdsd.`book_dis_shipments_id` = " . $shipment->id . " and bdsd.`grade_id` = " . $value->grade_id . "  ORDER BY bdsd.`title_id`";
                        $detail_result_sent = DB::select(DB::raw($query_string_sent_detail));
                        $sent_grades_detail[$value->grade_title] = $detail_result_sent;
                    }
                }

                $recieve_record = book_dis_shipment_recieve::where("book_dis_shipments_id", $shipment->id)->limit(1)->get();
                if (count($recieve_record) > 0) {

                    if ($recieve_record[0]->received == 1) {

                        $receive_grades_detail = [];
                        $query_string_receive = "select bdsrd.`grade_id`, (select bdmg.`title` from `book_dis_meta_grades` as bdmg where bdmg.id = bdsrd.`grade_id` ) as grade_title
                                    from `book_dis_shipment_recieve_details` as bdsrd where bdsrd.`book_dis_receive_id` =
                                        (
                                            select bdsr.id from `book_dis_shipment_recieves` as bdsr where bdsr.`book_dis_shipments_id` = " . $shipment->id . " limit 1
                                        )
                                    GROUP BY bdsrd.`grade_id`";
                        $detail_result_receive = DB::select(DB::raw($query_string_receive));
                        if (count($detail_result_receive) > 0) {
                            foreach ($detail_result_receive as $key => $value) {
                                $query_string_receive_detail = " select bdsrd.`total` as total,
                                                            (select title.`title` from `book_dis_meta_titles` as title where title.id = bdsrd.`title_id`) as title,
                                                            (select lang.`title` from `book_dis_title_languages` as lang where lang.id = bdsrd.`language_id`) as language
                                                            from `book_dis_shipment_recieve_details` as bdsrd where bdsrd.`book_dis_receive_id` =
                                                                (
                                                                    select bdsr.id from `book_dis_shipment_recieves` as bdsr where bdsr.`book_dis_shipments_id` = " . $shipment->id . " limit 1
                                                                )
                                                            and bdsrd.`grade_id` = " . $value->grade_id . "
                                                            ORDER BY bdsrd.`title_id`";
                                $detail_result_receive = DB::select(DB::raw($query_string_receive_detail));
                                $receive_grades_detail[$value->grade_title] = $detail_result_receive;
                            }
                        }

                        $receive_dmg_grades_detail = [];
                        $query_string_receive_dmg = "select bdsrd.`grade_id`, (select bdmg.`title` from `book_dis_meta_grades` as bdmg where bdmg.id = bdsrd.`grade_id` ) as grade_title
                                    from `book_dis_shipment_recieve_dmgs` as bdsrd where bdsrd.`book_dis_receive_id` =
                                        (
                                            select bdsr.id from `book_dis_shipment_recieves` as bdsr where bdsr.`book_dis_shipments_id` = " . $shipment->id . " limit 1
                                        )
                                    GROUP BY bdsrd.`grade_id`";
                        $detail_result_receive_dmg = DB::select(DB::raw($query_string_receive_dmg));
                        if (count($detail_result_receive_dmg) > 0) {
                            foreach ($detail_result_receive_dmg as $key => $value) {
                                $query_string_receive_detail_dmg = " select bdsrd.`total` as total,
                                                            (select title.`title` from `book_dis_meta_titles` as title where title.id = bdsrd.`title_id`) as title,
                                                            (select lang.`title` from `book_dis_title_languages` as lang where lang.id = bdsrd.`language_id`) as language
                                                            from `book_dis_shipment_recieve_dmgs` as bdsrd where bdsrd.`book_dis_receive_id` =
                                                                (
                                                                    select bdsr.id from `book_dis_shipment_recieves` as bdsr where bdsr.`book_dis_shipments_id` = " . $shipment->id . " limit 1
                                                                )
                                                            and bdsrd.`grade_id` = " . $value->grade_id . "
                                                            ORDER BY bdsrd.`title_id`";
                                $detail_result_receive_dmg = DB::select(DB::raw($query_string_receive_detail_dmg));
                                $receive_dmg_grades_detail[$value->grade_title] = $detail_result_receive_dmg;
                            }
                        }

                        $receive_lost_grades_detail = [];
                        $query_string_receive_lost = "select bdsrd.`grade_id`, (select bdmg.`title` from `book_dis_meta_grades` as bdmg where bdmg.id = bdsrd.`grade_id` ) as grade_title
                                    from `book_dis_shipment_recieve_losts` as bdsrd where bdsrd.`book_dis_receive_id` =
                                        (
                                            select bdsr.id from `book_dis_shipment_recieves` as bdsr where bdsr.`book_dis_shipments_id` = " . $shipment->id . " limit 1
                                        )
                                    GROUP BY bdsrd.`grade_id`";
                        $detail_result_receive_lost = DB::select(DB::raw($query_string_receive_lost));
                        if (count($detail_result_receive_lost) > 0) {
                            foreach ($detail_result_receive_lost as $key => $value) {
                                $query_string_receive_detail_lost = " select bdsrd.`total` as total,
                                                            (select title.`title` from `book_dis_meta_titles` as title where title.id = bdsrd.`title_id`) as title,
                                                            (select lang.`title` from `book_dis_title_languages` as lang where lang.id = bdsrd.`language_id`) as language
                                                            from `book_dis_shipment_recieve_losts` as bdsrd where bdsrd.`book_dis_receive_id` =
                                                                (
                                                                    select bdsr.id from `book_dis_shipment_recieves` as bdsr where bdsr.`book_dis_shipments_id` = " . $shipment->id . " limit 1
                                                                )
                                                            and bdsrd.`grade_id` = " . $value->grade_id . "
                                                            ORDER BY bdsrd.`title_id`";
                                $detail_result_receive_lost = DB::select(DB::raw($query_string_receive_detail_lost));
                                $receive_lost_grades_detail[$value->grade_title] = $detail_result_receive_lost;
                            }
                        }

                        return array(
                            "sent_shipment" => $shipment, "receive_record" => $recieve_record[0],
                            'sent_detail' => $sent_grades_detail,
                            'sent_received_detail' => $receive_grades_detail,
                            'receive_dmg_grades_detail' => $receive_dmg_grades_detail,
                            'receive_lost_grades_detail' => $receive_lost_grades_detail,
                        );
                    } else {
                        return array(
                            "sent_shipment" => $shipment, "receive_record" => $recieve_record[0],
                            'sent_detail' => $sent_grades_detail,
                        );
                    }
                } else {
                    return array(
                        "sent_shipment" => $shipment, "receive_record" => array(),
                        'sent_detail' => $sent_grades_detail,
                    );
                }
            } else {
                return null;
            }
        } else {
        }
    }

    public static function getReceiveShipment($record_id)
    {
        $shipment = book_dis_shipment_recieve::findOrFail($record_id);
        if ($shipment != null) {
            return $shipment;
        } else {
            return null;
        }
    }

    public static function getShipmentReceive($record_id)
    {
        $shipment = book_dis_shipment_recieve::findOrFail($record_id);
        if ($shipment != null) {
            $root_shipment = $shipment->shipment()->get();
            if (count($root_shipment) > 0) {
                return $root_shipment->first();
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public static function validateNode($Node_id)
    {
        if ($Node_id != null && $Node_id != 0) {
            //book level creation
            $node = book_dis_node::findOrFail($Node_id);
            if ($node != null) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public static function validateUser($User_id)
    {
        if ($User_id != null && $User_id != 0) {

            $user = user::findOrFail($User_id);
            if ($user != null) {
                $query_string = "
                                              select * from `book_dis_node_staffs` as usr_nod where usr_nod.`user_id` = " . $User_id . "
                                  ";
                //book level creation
                $user_result = DB::select(DB::raw($query_string));
                /*   if the user has been assinged to another node before, then it can not be assign again so return 0 if else the return 1*/
                if (count($user_result) > 0) {
                    return 0;
                } else {
                    return 1;
                }
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public static function getReportTypes($q_types)
    {
        if ($q_types != null) {
            $result_categories = array();

            foreach ($q_types as $key => $value) {
                array_push($result_categories, ["id" => $key, "title" => $value]);
            }
            return $result_categories;
        } else {
            return null;
        }
    }

    /*
     * This is the first version of report desinged based on the
     * sheet Naeem jan sent
     *
     * public static function createReportProvinceWise( $input_data ){

    $data= $input_data;
    $provinces_reports = [];
    $provinces  = province::get();

    if($provinces != null){
    if(count($provinces) > 0){
    foreach($provinces as $province){
    $query_string_province = "select * ,
    (select grade.`title` from `book_dis_meta_grades` as grade where grade.id = bdsrd.`grade_id`) as grade,
    (select title.`title` from `book_dis_meta_titles` as title where title.id = bdsrd.`title_id`) as title,
    (select lang.`title` from `book_dis_title_languages` as lang where lang.id = bdsrd.`language_id`) as language,
    (select prov.`en_name` from `provinces` as prov where prov.id = ".$province->id.") as province_name,

    (
    select sum(bdsddd.total) from `book_dis_shipment_details` as bdsddd
    where bdsddd.`book_dis_shipments_id` in
    (
    select bds.id from `book_dis_shipments` as bds
    where bds.to =
    (
    select bdn.id from `book_dis_nodes` as bdn
    where bdn.`province` = ".$province->id."
    and bdn.`level_id` =
    (select bdl.id from `book_dis_node_levels` as bdl where bdl.`title` = 'Provincial')
    limit 1
    )

    and bds.`receive_date` BETWEEN Date('".date('Y-m-d', strtotime($data['from_date']))."') and Date('".date('Y-m-d', strtotime($data['to_date']))."')
    )
    and
    bdsddd.`grade_id` = bdsrd.`grade_id`
    and
    bdsddd.`title_id` = bdsrd.`title_id`
    and
    bdsddd.`language_id` = bdsrd.`language_id`

    ) as total_sent_to ,

    sum(bdsrd.total) as main_total ,
    (select bdnbd.`total` from `book_dis_node_balance_details` as bdnbd where bdnbd.`node_id` =

    (
    select bdn.id from `book_dis_nodes` as bdn
    where bdn.`province` = ".$province->id."
    and bdn.`level_id` =
    (select bdl.id from `book_dis_node_levels` as bdl where bdl.`title` = 'Provincial')
    limit 1
    )

    and bdnbd.`grade_id` = bdsrd.`grade_id` and bdnbd.`title_id` = bdsrd.`title_id` and bdnbd.`language_id` = bdsrd.`language_id`) as avalaible_blance ,

    (
    select sum(bdsrdg.total) from `book_dis_shipment_recieve_dmgs` as bdsrdg
    where bdsrdg.`book_dis_receive_id` in
    (
    select bdsr.id from `book_dis_shipment_recieves` as bdsr where
    bdsr.`book_dis_shipments_id` in
    (
    select bds.id from `book_dis_shipments` as bds
    where bds.to =
    (
    select bdn.id from `book_dis_nodes` as bdn
    where bdn.`province` = ".$province->id."
    and bdn.`level_id` =
    (select bdl.id from `book_dis_node_levels` as bdl where bdl.`title` = 'Provincial')
    limit 1
    )

    and bds.`receive_date` BETWEEN Date('".date('Y-m-d', strtotime($data['from_date']))."') and Date('".date('Y-m-d', strtotime($data['to_date']))."')
    )
    and
    bdsr.`received` = 1
    )
    and
    bdsrdg.`grade_id` = bdsrd.`grade_id`
    and
    bdsrdg.`title_id` = bdsrd.`title_id`
    and
    bdsrdg.`language_id` = bdsrd.`language_id`

    ) as total_dmg ,

    (
    select sum(bdsrls.total) from `book_dis_shipment_recieve_losts` as bdsrls
    where bdsrls.`book_dis_receive_id` in
    (
    select bdsr.id from `book_dis_shipment_recieves` as bdsr where
    bdsr.`book_dis_shipments_id` in
    (
    select bds.id from `book_dis_shipments` as bds
    where bds.to =

    (
    select bdn.id from `book_dis_nodes` as bdn
    where bdn.`province` = ".$province->id."
    and bdn.`level_id` =
    (select bdl.id from `book_dis_node_levels` as bdl where bdl.`title` = 'Provincial')
    limit 1
    )

    and bds.`receive_date` BETWEEN Date('".date('Y-m-d', strtotime($data['from_date']))."') and Date('".date('Y-m-d', strtotime($data['to_date']))."')
    )
    and
    bdsr.`received` = 1
    )
    and
    bdsrls.`grade_id` = bdsrd.`grade_id`
    and
    bdsrls.`title_id` = bdsrd.`title_id`
    and
    bdsrls.`language_id` = bdsrd.`language_id`

    ) as total_lost,

    (
    select sum(bdsdd.total) from `book_dis_shipment_details` as bdsdd
    where bdsdd.`book_dis_shipments_id` in
    (
    select bds.id from `book_dis_shipments` as bds
    where bds.from =

    (
    select bdn.id from `book_dis_nodes` as bdn
    where bdn.`province` = ".$province->id."
    and bdn.`level_id` =
    (select bdl.id from `book_dis_node_levels` as bdl where bdl.`title` = 'Provincial')
    limit 1
    )

    and bds.`send_date` BETWEEN Date('".date('Y-m-d', strtotime($data['from_date']))."') and Date('".date('Y-m-d', strtotime($data['to_date']))."')
    )
    and
    bdsdd.`grade_id` = bdsrd.`grade_id`
    and
    bdsdd.`title_id` = bdsrd.`title_id`
    and
    bdsdd.`language_id` = bdsrd.`language_id`

    ) as total_sent

    from `book_dis_shipment_recieve_details` as bdsrd where
    bdsrd.`book_dis_receive_id` in
    (
    select bdsr.id from `book_dis_shipment_recieves` as bdsr where
    bdsr.`book_dis_shipments_id` in
    (
    select bds.id from `book_dis_shipments` as bds
    where bds.to =

    (
    select bdn.id from `book_dis_nodes` as bdn
    where bdn.`province` = ".$province->id."
    and bdn.`level_id` =
    (select bdl.id from `book_dis_node_levels` as bdl where bdl.`title` = 'Provincial')
    limit 1
    )

    and bds.`receive_date` BETWEEN Date('".date('Y-m-d', strtotime($data['from_date']))."') and Date('".date('Y-m-d', strtotime($data['to_date']))."')
    )
    and
    bdsr.`received` = 1
    )
    group by bdsrd.`grade_id` , bdsrd.`title_id` , bdsrd.`language_id`";
    $province_result =DB::select( DB::raw( $query_string_province) );
    $provinces_reports[$province->id] = $province_result;

    }
    return array("reports" => $provinces_reports
    );
    }else{
    return array("reports" => array()
    );
    }
    }else{
    return array("reports" => array()
    );
    }

    }*/

    public static function createReportProvinceWise($input_data)
    {

        $data = $input_data;
        $provinces_reports = [];
        $query_string_school = "select bdn.id
                                        from `book_dis_nodes` as bdn
                                        where bdn.`level_id` =
                                        (select bdl.id from `book_dis_node_levels` as bdl where bdl.`title` = 'School') ";
        $school_result = DB::select(DB::raw($query_string_school));

        foreach ($school_result as $item) {
            $query_string_province = "select * ,

                                        (select grade.`title` from `book_dis_meta_grades` as grade where grade.id = bdsd.`grade_id`) as grade,
                                        (select title.`title` from `book_dis_meta_titles` as title where title.id = bdsd.`title_id`) as title,
                                        (select lang.`title` from `book_dis_title_languages` as lang where lang.id = bdsd.`language_id`) as language,

                                        (select prov.`en_name` from `provinces` as prov where prov.id =  (select bdn.`province` from `book_dis_nodes` as bdn where bdn.id = " . $item->id . ")
                                                                                    ) as province_name ,
                                        (select dis.`en_name` from `districts` as dis where dis.id = (select bdn.`district` from `book_dis_nodes` as bdn where bdn.id = " . $item->id . ")) as district_name  ,
                                        (select bdnt.`title` from `book_dis_nodes` as bdnt where bdnt.id = " . $item->id . " ) as School_name,
                                        sum(bdsd.total) as Total_sent,


                                        (
                                            select sum(bdsrd.total) as Total_recieve from `book_dis_shipment_recieve_details` as bdsrd
                                                                                        where bdsrd.`book_dis_receive_id` in
                                                                                            (
                                                                                                select bdsr.id from `book_dis_shipment_recieves` as bdsr
                                                                                                    where bdsr.`book_dis_shipments_id` in
                                                                                                        (
                                                                                                            select bds.id from `book_dis_shipments` as bds
                                                                                                                where bds.to = " . $item->id . "
                                                                                                                and bds.from != " . $item->id . "
                                                                                                                and bds.`receive_date` BETWEEN Date('" . date('Y-m-d', strtotime($data['from_date'])) . "') and Date('" . date('Y-m-d', strtotime($data['to_date'])) . "')

                                                                                                        )
                                                                                            )
                                                                                            and
                                                                                            bdsrd.`grade_id` = bdsd.`grade_id`  and bdsrd.`title_id` = bdsd.`title_id` and bdsrd.`language_id` =  bdsd.`language_id`
                                                                                            group by bdsrd.`grade_id` , bdsrd.`title_id` , bdsrd.`language_id`
                                        ) as Total_recieved,

                                        (
                                            select sum(bdsrd.total) as Total_recieve from `book_dis_shipment_recieve_dmgs` as bdsrd
                                                                                        where bdsrd.`book_dis_receive_id` in
                                                                                            (
                                                                                                select bdsr.id from `book_dis_shipment_recieves` as bdsr
                                                                                                    where bdsr.`book_dis_shipments_id` in
                                                                                                        (
                                                                                                            select bds.id from `book_dis_shipments` as bds
                                                                                                                where bds.to = " . $item->id . "
                                                                                                                and bds.from != " . $item->id . "
                                                                                                                and bds.`receive_date` BETWEEN Date('" . date('Y-m-d', strtotime($data['from_date'])) . "') and Date('" . date('Y-m-d', strtotime($data['to_date'])) . "')

                                                                                                        )
                                                                                            )
                                                                                            and
                                                                                            bdsrd.`grade_id` = bdsd.`grade_id`  and bdsrd.`title_id` = bdsd.`title_id` and bdsrd.`language_id` =  bdsd.`language_id`
                                                                                            group by bdsrd.`grade_id` , bdsrd.`title_id` , bdsrd.`language_id`
                                        ) as Total_damaged,

                                        (
                                            select sum(bdsrd.total) as Total_recieve from `book_dis_shipment_recieve_losts` as bdsrd
                                                                                        where bdsrd.`book_dis_receive_id` in
                                                                                            (
                                                                                                select bdsr.id from `book_dis_shipment_recieves` as bdsr
                                                                                                    where bdsr.`book_dis_shipments_id` in
                                                                                                        (
                                                                                                            select bds.id from `book_dis_shipments` as bds
                                                                                                                where bds.to = " . $item->id . "
                                                                                                                and bds.from != " . $item->id . "
                                                                                                                and bds.`receive_date` BETWEEN Date('" . date('Y-m-d', strtotime($data['from_date'])) . "') and Date('" . date('Y-m-d', strtotime($data['to_date'])) . "')

                                                                                                        )
                                                                                            )
                                                                                            and
                                                                                            bdsrd.`grade_id` = bdsd.`grade_id`  and bdsrd.`title_id` = bdsd.`title_id` and bdsrd.`language_id` =  bdsd.`language_id`
                                                                                            group by bdsrd.`grade_id` , bdsrd.`title_id` , bdsrd.`language_id`
                                        ) as Total_lost,

                                        (
                                            select sum(bdsdd.total) from `book_dis_shipment_details` as bdsdd
                                                                                                where bdsdd.`book_dis_shipments_id` in
                                                                                                    (
                                                                                                        select bds.id from `book_dis_shipments` as bds
                                                                                                                where bds.to = " . $item->id . "
                                                                                                                and bds.from = " . $item->id . "
                                                                                                                and bds.`to_beneficiary` = 1
                                                                                                                and bds.`to_beneficiary_type`= 1
                                                                                                                and bds.`send_date` BETWEEN Date('" . date('Y-m-d', strtotime($data['from_date'])) . "') and Date('" . date('Y-m-d', strtotime($data['to_date'])) . "')
                                                                                                    )
                                                                                                    and bdsdd.`grade_id` = bdsd.`grade_id` and  bdsdd.`title_id` = bdsd.`title_id` and  bdsdd.`language_id` = bdsd.`language_id`
                                                                                                    group by bdsdd.`grade_id` , bdsdd.`title_id` , bdsdd.`language_id`
                                        ) as Total_to_student,


                                        (
                                            select bdnbd.`total`
                                            from `book_dis_node_balance_details` as bdnbd
                                            where bdnbd.`node_id` = " . $item->id . "
                                            and bdnbd.`grade_id` = bdsd.`grade_id`
                                            and bdnbd.`title_id` = bdsd.`title_id`
                                            and bdnbd.`language_id` = bdsd.`language_id`
                                         ) as avalaible_blance



                                        from `book_dis_shipment_details` as bdsd
                                        where bdsd.`book_dis_shipments_id` in
                                        (
                                            select bds.id from `book_dis_shipments` as bds
                                            where bds.to = " . $item->id . "
                                            and bds.from != " . $item->id . "
                                            and bds.`receive_date` BETWEEN Date('" . date('Y-m-d', strtotime($data['from_date'])) . "') and Date('" . date('Y-m-d', strtotime($data['to_date'])) . "')
                                        )
                                        group by bdsd.`grade_id` , bdsd.`title_id` , bdsd.`language_id`   ";
            $district_result = DB::select(DB::raw($query_string_province));
            $provinces_reports[$item->id] = $district_result;
        }

        return array("reports" => $provinces_reports);
    }

    /* public static function createReportProvinceWise( $input_data ){

    $data= $input_data;
    $provinces_reports = [];
    $provinces  = province::get();

    if($provinces != null){
    if(count($provinces) > 0){
    foreach($provinces as $province){
    $query_string_province = "select *,
    sum(bdsrd.total) as Total_recieved ,

    (
    select sum(bdsrdg.total)
    from `book_dis_shipment_recieve_dmgs` as bdsrdg
    where bdsrdg.`book_dis_receive_id` in
    (
    select bdsr.id
    from `book_dis_shipment_recieves` as bdsr
    where bdsr.`book_dis_shipments_id` in
    (
    select bds.id
    from `book_dis_shipments` as bds
    where bds.to =
    (
    select bdn.id
    from `book_dis_nodes` as bdn
    where bdn.`province` = ".$province->id."
    and bdn.`level_id` =
    (select bdl.id from `book_dis_node_levels` as bdl where bdl.`title` = 'Provincial')
    limit 1
    )

    and bds.`receive_date` BETWEEN Date('".date('Y-m-d', strtotime($data['from_date']))."') and Date('".date('Y-m-d', strtotime($data['to_date']))."')
    )
    and
    bdsr.`received` = 1
    )
    and
    bdsrdg.`grade_id` = bdsrd.`grade_id`
    and
    bdsrdg.`title_id` = bdsrd.`title_id`
    and
    bdsrdg.`language_id` = bdsrd.`language_id`

    ) as total_dmg ,

    (
    select sum(bdsrls.total) from `book_dis_shipment_recieve_losts` as bdsrls
    where bdsrls.`book_dis_receive_id` in
    (
    select bdsr.id from `book_dis_shipment_recieves` as bdsr where
    bdsr.`book_dis_shipments_id` in
    (
    select bds.id from `book_dis_shipments` as bds
    where bds.to =

    (
    select bdn.id from `book_dis_nodes` as bdn
    where bdn.`province` = ".$province->id."
    and bdn.`level_id` =
    (select bdl.id from `book_dis_node_levels` as bdl where bdl.`title` = 'Provincial')
    limit 1
    )

    and bds.`receive_date` BETWEEN Date('".date('Y-m-d', strtotime($data['from_date']))."') and Date('".date('Y-m-d', strtotime($data['to_date']))."')
    )
    and
    bdsr.`received` = 1
    )
    and
    bdsrls.`grade_id` = bdsrd.`grade_id`
    and
    bdsrls.`title_id` = bdsrd.`title_id`
    and
    bdsrls.`language_id` = bdsrd.`language_id`

    ) as total_lost,

    (
    select sum(bdsdd.total)
    from `book_dis_shipment_details` as bdsdd
    where bdsdd.`book_dis_shipments_id` in
    (
    select bds.id
    from `book_dis_shipments` as bds
    where bds.from =
    (
    select bdn.id
    from `book_dis_nodes` as bdn
    where bdn.`province` = ".$province->id."
    and bdn.`level_id` =
    (select bdl.id from `book_dis_node_levels` as bdl where bdl.`title` = 'Provincial')
    limit 1
    )

    and bds.`send_date` BETWEEN Date('".date('Y-m-d', strtotime($data['from_date']))."') and Date('".date('Y-m-d', strtotime($data['to_date']))."')
    )
    and
    bdsdd.`grade_id` = bdsrd.`grade_id`
    and
    bdsdd.`title_id` = bdsrd.`title_id`
    and
    bdsdd.`language_id` = bdsrd.`language_id`

    ) as total_sent ,

    (
    select bdnbd.`total`
    from `book_dis_node_balance_details` as bdnbd
    where bdnbd.`node_id` =
    (
    select bdn.id
    from `book_dis_nodes` as bdn
    where bdn.`province` = ".$province->id."
    and bdn.`level_id` =
    (select bdl.id from `book_dis_node_levels` as bdl where bdl.`title` = 'Provincial')
    limit 1
    )
    and bdnbd.`grade_id` = bdsrd.`grade_id`
    and bdnbd.`title_id` = bdsrd.`title_id`
    and bdnbd.`language_id` = bdsrd.`language_id`
    ) as avalaible_blance ,
    (select grade.`title` from `book_dis_meta_grades` as grade where grade.id = bdsrd.`grade_id`) as grade,
    (select title.`title` from `book_dis_meta_titles` as title where title.id = bdsrd.`title_id`) as title,
    (select lang.`title` from `book_dis_title_languages` as lang where lang.id = bdsrd.`language_id`) as language,
    (select prov.`en_name` from `provinces` as prov where prov.id = ".$province->id.") as province_name

    from `book_dis_shipment_recieve_details` as bdsrd
    where bdsrd.`book_dis_receive_id` in
    (
    select bdsr.id from `book_dis_shipment_recieves` as bdsr where
    bdsr.`book_dis_shipments_id` in
    (
    select bds.id from `book_dis_shipments` as bds
    where bds.to =

    (
    select bdn.id from `book_dis_nodes` as bdn
    where bdn.`province` = ".$province->id."
    and bdn.`level_id` =
    (select bdl.id from `book_dis_node_levels` as bdl where bdl.`title` = 'Provincial')
    limit 1
    )

    and bds.`receive_date` BETWEEN Date('".date('Y-m-d', strtotime($data['from_date']))."') and Date('".date('Y-m-d', strtotime($data['to_date']))."')
    )
    and
    bdsr.`received` = 1
    )
    group by bdsrd.`grade_id` , bdsrd.`title_id` , bdsrd.`language_id`";
    $province_result =DB::select( DB::raw( $query_string_province) );
    $provinces_reports[$province->id] = $province_result;

    }
    return array("reports" => $provinces_reports
    );
    }else{
    return array("reports" => array()
    );
    }
    }else{
    return array("reports" => array()
    );
    }

    }*/
    public static function createReportProvinceWiseSent($input_data)
    {

        $data = $input_data;
        $district_reports = [];

        if (isset($data['province'])) {

            $query_string_school = "select bdn.id
                                        from `book_dis_nodes` as bdn
                                        where bdn.`district` in (select d.id from `districts` as d where d.`province_id` = " . $data['province'] . ")
                                        and bdn.`level_id` =
                                        (select bdl.id from `book_dis_node_levels` as bdl where bdl.`title` = 'School') ";
            $school_result = DB::select(DB::raw($query_string_school));
        } else {
            $query_string_school = "select bdn.id
                                        from `book_dis_nodes` as bdn
                                        where bdn.`district` in (select d.id from `districts` as d where d.`province_id` = " . $data['province'] . ")
                                        and bdn.`level_id` =
                                        (select bdl.id from `book_dis_node_levels` as bdl where bdl.`title` = 'School') ";
            $school_result = DB::select(DB::raw($query_string_school));
        }

        foreach ($school_result as $item) {
            $query_string_province = "select * ,

                                        (select grade.`title` from `book_dis_meta_grades` as grade where grade.id = bdsd.`grade_id`) as grade,
                                        (select title.`title` from `book_dis_meta_titles` as title where title.id = bdsd.`title_id`) as title,
                                        (select lang.`title` from `book_dis_title_languages` as lang where lang.id = bdsd.`language_id`) as language,

                                        (select prov.`en_name` from `provinces` as prov where prov.id =  (select bdn.`province` from `book_dis_nodes` as bdn where bdn.id = " . $item->id . ")
                                                                                    ) as province_name ,
                                        (select dis.`en_name` from `districts` as dis where dis.id = (select bdn.`district` from `book_dis_nodes` as bdn where bdn.id = " . $item->id . ")) as district_name  ,
                                        (select bdnt.`title` from `book_dis_nodes` as bdnt where bdnt.id = " . $item->id . " ) as School_name,
                                        sum(bdsd.total) as Total_sent,


                                        (
                                            select sum(bdsrd.total) as Total_recieve from `book_dis_shipment_recieve_details` as bdsrd
                                                                                        where bdsrd.`book_dis_receive_id` in
                                                                                            (
                                                                                                select bdsr.id from `book_dis_shipment_recieves` as bdsr
                                                                                                    where bdsr.`book_dis_shipments_id` in
                                                                                                        (
                                                                                                            select bds.id from `book_dis_shipments` as bds
                                                                                                                where bds.to = " . $item->id . "
                                                                                                                and bds.from != " . $item->id . "
                                                                                                                and bds.`receive_date` BETWEEN Date('" . date('Y-m-d', strtotime($data['from_date'])) . "') and Date('" . date('Y-m-d', strtotime($data['to_date'])) . "')

                                                                                                        )
                                                                                            )
                                                                                            and
                                                                                            bdsrd.`grade_id` = bdsd.`grade_id`  and bdsrd.`title_id` = bdsd.`title_id` and bdsrd.`language_id` =  bdsd.`language_id`
                                                                                            group by bdsrd.`grade_id` , bdsrd.`title_id` , bdsrd.`language_id`
                                        ) as Total_recieved,

                                        (
                                            select sum(bdsrd.total) as Total_recieve from `book_dis_shipment_recieve_dmgs` as bdsrd
                                                                                        where bdsrd.`book_dis_receive_id` in
                                                                                            (
                                                                                                select bdsr.id from `book_dis_shipment_recieves` as bdsr
                                                                                                    where bdsr.`book_dis_shipments_id` in
                                                                                                        (
                                                                                                            select bds.id from `book_dis_shipments` as bds
                                                                                                                where bds.to = " . $item->id . "
                                                                                                                and bds.from != " . $item->id . "
                                                                                                                and bds.`receive_date` BETWEEN Date('" . date('Y-m-d', strtotime($data['from_date'])) . "') and Date('" . date('Y-m-d', strtotime($data['to_date'])) . "')

                                                                                                        )
                                                                                            )
                                                                                            and
                                                                                            bdsrd.`grade_id` = bdsd.`grade_id`  and bdsrd.`title_id` = bdsd.`title_id` and bdsrd.`language_id` =  bdsd.`language_id`
                                                                                            group by bdsrd.`grade_id` , bdsrd.`title_id` , bdsrd.`language_id`
                                        ) as Total_damaged,

                                        (
                                            select sum(bdsrd.total) as Total_recieve from `book_dis_shipment_recieve_losts` as bdsrd
                                                                                        where bdsrd.`book_dis_receive_id` in
                                                                                            (
                                                                                                select bdsr.id from `book_dis_shipment_recieves` as bdsr
                                                                                                    where bdsr.`book_dis_shipments_id` in
                                                                                                        (
                                                                                                            select bds.id from `book_dis_shipments` as bds
                                                                                                                where bds.to = " . $item->id . "
                                                                                                                and bds.from != " . $item->id . "
                                                                                                                and bds.`receive_date` BETWEEN Date('" . date('Y-m-d', strtotime($data['from_date'])) . "') and Date('" . date('Y-m-d', strtotime($data['to_date'])) . "')

                                                                                                        )
                                                                                            )
                                                                                            and
                                                                                            bdsrd.`grade_id` = bdsd.`grade_id`  and bdsrd.`title_id` = bdsd.`title_id` and bdsrd.`language_id` =  bdsd.`language_id`
                                                                                            group by bdsrd.`grade_id` , bdsrd.`title_id` , bdsrd.`language_id`
                                        ) as Total_lost,

                                        (
                                            select sum(bdsdd.total) from `book_dis_shipment_details` as bdsdd
                                                                                                where bdsdd.`book_dis_shipments_id` in
                                                                                                    (
                                                                                                        select bds.id from `book_dis_shipments` as bds
                                                                                                                where bds.to = " . $item->id . "
                                                                                                                and bds.from = " . $item->id . "
                                                                                                                and bds.`to_beneficiary` = 1
                                                                                                                and bds.`to_beneficiary_type`= 1
                                                                                                                and bds.`send_date` BETWEEN Date('" . date('Y-m-d', strtotime($data['from_date'])) . "') and Date('" . date('Y-m-d', strtotime($data['to_date'])) . "')
                                                                                                    )
                                                                                                    and bdsdd.`grade_id` = bdsd.`grade_id` and  bdsdd.`title_id` = bdsd.`title_id` and  bdsdd.`language_id` = bdsd.`language_id`
                                                                                                    group by bdsdd.`grade_id` , bdsdd.`title_id` , bdsdd.`language_id`
                                        ) as Total_to_student,


                                        (
                                            select bdnbd.`total`
                                            from `book_dis_node_balance_details` as bdnbd
                                            where bdnbd.`node_id` = " . $item->id . "
                                            and bdnbd.`grade_id` = bdsd.`grade_id`
                                            and bdnbd.`title_id` = bdsd.`title_id`
                                            and bdnbd.`language_id` = bdsd.`language_id`
                                         ) as avalaible_blance



                                        from `book_dis_shipment_details` as bdsd
                                        where bdsd.`book_dis_shipments_id` in
                                        (
                                            select bds.id from `book_dis_shipments` as bds
                                            where bds.to = " . $item->id . "
                                            and bds.from != " . $item->id . "
                                            and bds.`receive_date` BETWEEN Date('" . date('Y-m-d', strtotime($data['from_date'])) . "') and Date('" . date('Y-m-d', strtotime($data['to_date'])) . "')
                                        )
                                        group by bdsd.`grade_id` , bdsd.`title_id` , bdsd.`language_id`   ";
            $district_result = DB::select(DB::raw($query_string_province));
            $district_reports[$item->id] = $district_result;
        }

        return array("reports" => $district_reports);
    }

    public static function createReportDistrictWise($input_data)
    {

        $data = $input_data;
        $district_reports = [];

        if (isset($data['district']['description'])) {

            $districts = district::where("id", $data['district']['description']['id'])->get();
            if (count($districts) > 0) {
                $district = $districts->first();
                $query_string_school = "select bdn.id
                                        from `book_dis_nodes` as bdn
                                        where bdn.`district` = " . $district->id . "
                                        and bdn.`level_id` =
                                        (select bdl.id from `book_dis_node_levels` as bdl where bdl.`title` = 'School') ";
                $school_result = DB::select(DB::raw($query_string_school));
            }
        } else {

            $query_string_school = "select bdn.id
                                        from `book_dis_nodes` as bdn
                                        where bdn.`district` in (select d.id from `districts` as d where d.`province_id` = " . $data['province'] . ")
                                        and bdn.`level_id` =
                                        (select bdl.id from `book_dis_node_levels` as bdl where bdl.`title` = 'School') ";
            $school_result = DB::select(DB::raw($query_string_school));
        }

        foreach ($school_result as $item) {
            $query_string_province = "select *,
                                                 sum(bdsrd.total) as Total_recieved ,


                                                (
                                                    select sum(bdsrdg.total)
                                                    from `book_dis_shipment_recieve_dmgs` as bdsrdg
                                                    where bdsrdg.`book_dis_receive_id` in
                                                    (
                                                        select bdsr.id
                                                        from `book_dis_shipment_recieves` as bdsr
                                                        where bdsr.`book_dis_shipments_id` in
                                                        (
                                                            select bds.id
                                                            from `book_dis_shipments` as bds
                                                            where bds.to = " . $item->id . "

                                                            and bds.`receive_date` BETWEEN Date('" . date('Y-m-d', strtotime($data['from_date'])) . "') and Date('" . date('Y-m-d', strtotime($data['to_date'])) . "')
                                                        )
                                                        and
                                                        bdsr.`received` = 1
                                                    )
                                                    and
                                                    bdsrdg.`grade_id` = bdsrd.`grade_id`
                                                    and
                                                    bdsrdg.`title_id` = bdsrd.`title_id`
                                                    and
                                                    bdsrdg.`language_id` = bdsrd.`language_id`


                                                ) as total_dmg ,

                                                (
                                                    select sum(bdsrls.total) from `book_dis_shipment_recieve_losts` as bdsrls
                                                    where bdsrls.`book_dis_receive_id` in
                                                    (
                                                        select bdsr.id from `book_dis_shipment_recieves` as bdsr where
                                                        bdsr.`book_dis_shipments_id` in
                                                        (
                                                            select bds.id from `book_dis_shipments` as bds
                                                            where bds.to = " . $item->id . "
                                                            and bds.`receive_date` BETWEEN Date('" . date('Y-m-d', strtotime($data['from_date'])) . "') and Date('" . date('Y-m-d', strtotime($data['to_date'])) . "')
                                                        )
                                                        and
                                                        bdsr.`received` = 1
                                                    )
                                                    and
                                                    bdsrls.`grade_id` = bdsrd.`grade_id`
                                                    and
                                                    bdsrls.`title_id` = bdsrd.`title_id`
                                                    and
                                                    bdsrls.`language_id` = bdsrd.`language_id`


                                                ) as total_lost,


                                                 (
                                                    select sum(bdsdd.total)
                                                    from `book_dis_shipment_details` as bdsdd
                                                    where bdsdd.`book_dis_shipments_id` in
                                                    (
                                                        select bds.id
                                                        from `book_dis_shipments` as bds
                                                        where bds.from = " . $item->id . "
                                                        and bds.`send_date` BETWEEN Date('" . date('Y-m-d', strtotime($data['from_date'])) . "') and Date('" . date('Y-m-d', strtotime($data['to_date'])) . "')
                                                    )
                                                    and
                                                    bdsdd.`grade_id` = bdsrd.`grade_id`
                                                    and
                                                    bdsdd.`title_id` = bdsrd.`title_id`
                                                    and
                                                    bdsdd.`language_id` = bdsrd.`language_id`


                                                ) as total_sent ,


                                                (
                                                    select bdnbd.`total`
                                                    from `book_dis_node_balance_details` as bdnbd
                                                    where bdnbd.`node_id` = " . $item->id . "
                                                    and bdnbd.`grade_id` = bdsrd.`grade_id`
                                                    and bdnbd.`title_id` = bdsrd.`title_id`
                                                    and bdnbd.`language_id` = bdsrd.`language_id`
                                                 ) as avalaible_blance ,
                                                (select grade.`title` from `book_dis_meta_grades` as grade where grade.id = bdsrd.`grade_id`) as grade,
                                                (select title.`title` from `book_dis_meta_titles` as title where title.id = bdsrd.`title_id`) as title,
                                                (select lang.`title` from `book_dis_title_languages` as lang where lang.id = bdsrd.`language_id`) as language,
                                                (select prov.`en_name` from `provinces` as prov where prov.id =  (select bdn.`province` from `book_dis_nodes` as bdn where bdn.id = " . $item->id . ")
                                                ) as province_name ,
                                                (select dis.`en_name` from `districts` as dis where dis.id = (select bdn.`district` from `book_dis_nodes` as bdn where bdn.id = " . $item->id . ")) as district_name  ,

                                                (select bdnt.`title` from `book_dis_nodes` as bdnt where bdnt.id = " . $item->id . " ) as School_name


                                                from `book_dis_shipment_recieve_details` as bdsrd
                                                where bdsrd.`book_dis_receive_id` in
                                                (
                                                    select bdsr.id from `book_dis_shipment_recieves` as bdsr where
                                                    bdsr.`book_dis_shipments_id` in
                                                    (
                                                        select bds.id from `book_dis_shipments` as bds
                                                        where bds.to = " . $item->id . "

                                                        and bds.`receive_date` BETWEEN Date('" . date('Y-m-d', strtotime($data['from_date'])) . "') and Date('" . date('Y-m-d', strtotime($data['to_date'])) . "')
                                                    )
                                                    and
                                                    bdsr.`received` = 1
                                                )
                                                group by bdsrd.`grade_id` , bdsrd.`title_id` , bdsrd.`language_id`";
            $district_result = DB::select(DB::raw($query_string_province));
            $district_reports[$item->id] = $district_result;
        }

        return array("reports" => $district_reports);
    }

    public static function createReportDistrictSchoolWise($input_data)
    {

        $data = $input_data;
        $district_reports = [];

        if (isset($data['province'])) {

            $districts = province::where("id", $data['province'])->get();
            if (count($districts) > 0) {

                $query_string_school = "select d.id from districts as d where d.`province_id` = " . $data['province'] . " ";
                $school_result = DB::select(DB::raw($query_string_school));
                foreach ($school_result as $district) {
                    $query_string_province = "
                                                select *,
                                                 sum(bdsrd.total) as Total_recieved ,


                                                (
                                                    select  sum(bdsrdg.total)
                                                    from `book_dis_shipment_recieve_dmgs` as bdsrdg
                                                    where bdsrdg.`book_dis_receive_id` in
                                                    (
                                                        select bdsr.id
                                                        from `book_dis_shipment_recieves` as bdsr
                                                        where bdsr.`book_dis_shipments_id` in
                                                        (
                                                            select bds.id
                                                            from `book_dis_shipments` as bds
                                                            where bds.to in
                                                            (select bdn.id from `book_dis_nodes` as bdn where bdn.`district`= " . $district->id . ")
                                                            and bds.`receive_date` BETWEEN Date('" . date('Y-m-d', strtotime($data['from_date'])) . "') and Date('" . date('Y-m-d', strtotime($data['to_date'])) . "')


                                                        )
                                                        and
                                                        bdsr.`received` = 1
                                                    )
                                                    and
                                                    bdsrdg.`grade_id` = bdsrd.`grade_id`
                                                    and
                                                    bdsrdg.`title_id` = bdsrd.`title_id`
                                                    and
                                                    bdsrdg.`language_id` = bdsrd.`language_id`


                                                ) as total_dmg ,

                                                (
                                                    select sum(bdsrls.total) from `book_dis_shipment_recieve_losts` as bdsrls
                                                    where bdsrls.`book_dis_receive_id` in
                                                    (
                                                        select bdsr.id from `book_dis_shipment_recieves` as bdsr where
                                                        bdsr.`book_dis_shipments_id` in
                                                        (
                                                            select bds.id from `book_dis_shipments` as bds
                                                            where bds.to in
                                                            (select bdn.id from `book_dis_nodes` as bdn where bdn.`district`= " . $district->id . ")
                                                          and bds.`receive_date` BETWEEN Date('" . date('Y-m-d', strtotime($data['from_date'])) . "') and Date('" . date('Y-m-d', strtotime($data['to_date'])) . "')
                                                        )
                                                        and
                                                        bdsr.`received` = 1
                                                    )
                                                    and
                                                    bdsrls.`grade_id` = bdsrd.`grade_id`
                                                    and
                                                    bdsrls.`title_id` = bdsrd.`title_id`
                                                    and
                                                    bdsrls.`language_id` = bdsrd.`language_id`


                                                ) as total_lost,
(
                                                    select sum(bdsdd.total)
                                                    from `book_dis_shipment_details` as bdsdd
                                                    where bdsdd.`book_dis_shipments_id` in
                                                    (
                                                        select bds.id
                                                        from `book_dis_shipments` as bds
                                                        where bds.to in
                                                        (select bdn.id from `book_dis_nodes` as bdn where bdn.`district`= " . $district->id . ")
                                                        and bds.`receive_date` BETWEEN Date('" . date('Y-m-d', strtotime($data['from_date'])) . "') and Date('" . date('Y-m-d', strtotime($data['to_date'])) . "')

                                                    )
                                                    and
                                                    bdsdd.`grade_id` = bdsrd.`grade_id`
                                                    and
                                                    bdsdd.`title_id` = bdsrd.`title_id`
                                                    and
                                                    bdsdd.`language_id` = bdsrd.`language_id`


                                                ) as total_sent_to ,



                                                 (
                                                    select sum(bdsdd.total)
                                                    from `book_dis_shipment_details` as bdsdd
                                                    where bdsdd.`book_dis_shipments_id` in
                                                    (
                                                        select bds.id
                                                        from `book_dis_shipments` as bds
                                                        where bds.from in
                                                        (select bdn.id from `book_dis_nodes` as bdn where bdn.`district`= " . $district->id . ")
                                                        and bds.`receive_date` BETWEEN Date('" . date('Y-m-d', strtotime($data['from_date'])) . "') and Date('" . date('Y-m-d', strtotime($data['to_date'])) . "')

                                                    )
                                                    and
                                                    bdsdd.`grade_id` = bdsrd.`grade_id`
                                                    and
                                                    bdsdd.`title_id` = bdsrd.`title_id`
                                                    and
                                                    bdsdd.`language_id` = bdsrd.`language_id`


                                                ) as total_sent ,


                                                (
                                                    select  sum(bdnbd.`total`)
                                                    from `book_dis_node_balance_details` as bdnbd
                                                    where bdnbd.`node_id` in
                                                    (select bdn.id from `book_dis_nodes` as bdn where bdn.`district`= " . $district->id . ")


                                                    and bdnbd.`grade_id` = bdsrd.`grade_id`
                                                    and bdnbd.`title_id` = bdsrd.`title_id`
                                                    and bdnbd.`language_id` = bdsrd.`language_id`
                                                 ) as avalaible_blance ,



                                                (select grade.`title` from `book_dis_meta_grades` as grade where grade.id = bdsrd.`grade_id`) as grade,
                                                (select title.`title` from `book_dis_meta_titles` as title where title.id = bdsrd.`title_id`) as title,
                                                (select lang.`title` from `book_dis_title_languages` as lang where lang.id = bdsrd.`language_id`) as language,
                                                (select prov.`en_name` from `provinces` as prov where prov.id =
                                                (select dis.`province_id` from `districts` as dis where dis.id = " . $district->id . " LIMIT 1)
                                                ) as province_name ,
                                                (select dis.`en_name` from `districts` as dis where dis.id = " . $district->id . ") as district_name


                                                from `book_dis_shipment_recieve_details` as bdsrd
                                                where bdsrd.`book_dis_receive_id` in
                                                (
                                                    select bdsr.id from `book_dis_shipment_recieves` as bdsr where
                                                    bdsr.`book_dis_shipments_id` in
                                                    (
                                                        select bds.id from `book_dis_shipments` as bds
                                                        where bds.to in
                                                        (select bdn.id from `book_dis_nodes` as bdn where bdn.`district`= " . $district->id . ")
                                                        and bds.`receive_date` BETWEEN Date('" . date('Y-m-d', strtotime($data['from_date'])) . "') and Date('" . date('Y-m-d', strtotime($data['to_date'])) . "')

                                                    )
                                                    and
                                                    bdsr.`received` = 1
                                                )
                                                group by bdsrd.`grade_id` , bdsrd.`title_id` , bdsrd.`language_id`

                                                ";
                    $district_result = DB::select(DB::raw($query_string_province));
                    $district_reports[$district->id] = $district_result;
                }

                return array("reports" => $district_reports);
            } else {
                return array("reports" => array());
            }
        } else {
            return array("reports" => array());
        }
    }

    public static function createReportCentralWise($input_data)
    {

        $data = $input_data;
        $district_reports = [];
        $query_string_school = "select bdn.id
                                        from `book_dis_nodes` as bdn
                                        where bdn.`level_id` =
                                        (select bdl.id from `book_dis_node_levels` as bdl where bdl.`title` like '%central%') ";
        $school_result = DB::select(DB::raw($query_string_school));

        foreach ($school_result as $item) {
            $query_string_province = "
                                        select *,
                                         sum(bdsrd.total) as Total_recieved ,


                                        (
                                            select sum(bdsrdg.total)
                                            from `book_dis_shipment_recieve_dmgs` as bdsrdg
                                            where bdsrdg.`book_dis_receive_id` in
                                            (
                                                select bdsr.id
                                                from `book_dis_shipment_recieves` as bdsr
                                                where bdsr.`book_dis_shipments_id` in
                                                (
                                                    select bds.id
                                                    from `book_dis_shipments` as bds
                                                    where bds.to =  " . $item->id . "

                                                    and bds.`receive_date` BETWEEN Date('" . date('Y-m-d', strtotime($data['from_date'])) . "') and Date('" . date('Y-m-d', strtotime($data['to_date'])) . "')
                                                )
                                                and
                                                bdsr.`received` = 1
                                            )
                                            and
                                            bdsrdg.`grade_id` = bdsrd.`grade_id`
                                            and
                                            bdsrdg.`title_id` = bdsrd.`title_id`
                                            and
                                            bdsrdg.`language_id` = bdsrd.`language_id`


                                        ) as total_dmg ,

                                        (
                                            select sum(bdsrls.total) from `book_dis_shipment_recieve_losts` as bdsrls
                                            where bdsrls.`book_dis_receive_id` in
                                            (
                                                select bdsr.id from `book_dis_shipment_recieves` as bdsr where
                                                bdsr.`book_dis_shipments_id` in
                                                (
                                                    select bds.id from `book_dis_shipments` as bds
                                                    where bds.to =  " . $item->id . "

                                                    and bds.`receive_date` BETWEEN Date('" . date('Y-m-d', strtotime($data['from_date'])) . "') and Date('" . date('Y-m-d', strtotime($data['to_date'])) . "')
                                                )
                                                and
                                                bdsr.`received` = 1
                                            )
                                            and
                                            bdsrls.`grade_id` = bdsrd.`grade_id`
                                            and
                                            bdsrls.`title_id` = bdsrd.`title_id`
                                            and
                                            bdsrls.`language_id` = bdsrd.`language_id`


                                        ) as total_lost,


                                         (
                                            select sum(bdsdd.total)
                                            from `book_dis_shipment_details` as bdsdd
                                            where bdsdd.`book_dis_shipments_id` in
                                            (
                                                select bds.id
                                                from `book_dis_shipments` as bds
                                                where bds.from =  " . $item->id . "

                                                and bds.`send_date` BETWEEN Date('" . date('Y-m-d', strtotime($data['from_date'])) . "') and Date('" . date('Y-m-d', strtotime($data['to_date'])) . "')
                                            )
                                            and
                                            bdsdd.`grade_id` = bdsrd.`grade_id`
                                            and
                                            bdsdd.`title_id` = bdsrd.`title_id`
                                            and
                                            bdsdd.`language_id` = bdsrd.`language_id`


                                        ) as total_sent ,


                                        (
                                            select bdnbd.`total`
                                            from `book_dis_node_balance_details` as bdnbd
                                            where bdnbd.`node_id` =  " . $item->id . "
                                            and bdnbd.`grade_id` = bdsrd.`grade_id`
                                            and bdnbd.`title_id` = bdsrd.`title_id`
                                            and bdnbd.`language_id` = bdsrd.`language_id`
                                         ) as avalaible_blance ,
                                        (select grade.`title` from `book_dis_meta_grades` as grade where grade.id = bdsrd.`grade_id`) as grade,
                                        (select title.`title` from `book_dis_meta_titles` as title where title.id = bdsrd.`title_id`) as title,
                                        (select lang.`title` from `book_dis_title_languages` as lang where lang.id = bdsrd.`language_id`) as language


                                        from `book_dis_shipment_recieve_details` as bdsrd
                                        where bdsrd.`book_dis_receive_id` in
                                        (
                                            select bdsr.id from `book_dis_shipment_recieves` as bdsr where
                                            bdsr.`book_dis_shipments_id` in
                                            (
                                                select bds.id from `book_dis_shipments` as bds
                                                where bds.to =  " . $item->id . "

                                                and bds.`receive_date` BETWEEN Date('" . date('Y-m-d', strtotime($data['from_date'])) . "') and Date('" . date('Y-m-d', strtotime($data['to_date'])) . "')
                                            )
                                            and
                                            bdsr.`received` = 1
                                        )
                                        group by bdsrd.`grade_id` , bdsrd.`title_id` , bdsrd.`language_id`";
            $district_result = DB::select(DB::raw($query_string_province));
            $district_reports[$item->id] = $district_result;
        }

        return array("reports" => $district_reports);
    }

    public static function getSentRequestsByPage($page, $url, $user_id)
    {
        $user = user::findOrFail($user_id);
        if ($user != null) {
            $nodes = $user->work_node();
            if (count($nodes) > 0) {
                $node = $nodes->first();
                $query_string = "SELECT br.id, br.title, br.description, br.requested_at, br.expected_at, br.delivered, br.approved, br.merged, bdnby.title AS request_by_node, bdnto.title AS request_to_node
                                FROM book_requests br
                                JOIN book_dis_nodes bdnby ON br.request_by_node = bdnby.id
                                JOIN book_dis_nodes bdnto ON br.request_to_node = bdnto.id
                                WHERE br.request_by_node = " . $node->id . "
                                order by br.created_at desc;
                                ";
                //book level creation
                $user_result = DB::select(DB::raw($query_string));
                $perPage = 3;
                $offset = ($page * $perPage) - $perPage;

                $user_result = new LengthAwarePaginator(
                    array_slice($user_result, $offset, $perPage, true),
                    count($user_result),
                    $perPage,
                    $page
                );
                return $user_result;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public static function getReceivedRequestsByPage($page, $url, $user_id)
    {
        $user = user::findOrFail($user_id);
        if ($user != null) {
            $nodes = $user->work_node();
            if (count($nodes) > 0) {
                $node = $nodes->first();
                $query_string = "SELECT br.id, br.title, br.description, br.requested_at, br.expected_at, br.delivered, br.approved, br.merged, bdnby.title AS request_by_node, bdnto.title AS request_to_node
                                FROM book_requests br
                                JOIN book_dis_nodes bdnby ON br.request_by_node = bdnby.id
                                JOIN book_dis_nodes bdnto ON br.request_to_node = bdnto.id
                                WHERE br.request_to_node = " . $node->id . "
                                order by br.created_at desc;
                                ";
                //book level creation
                $user_result = DB::select(DB::raw($query_string));
                $perPage = 3;
                $offset = ($page * $perPage) - $perPage;

                $user_result = new LengthAwarePaginator(
                    array_slice($user_result, $offset, $perPage, true),
                    count($user_result),
                    $perPage,
                    $page
                );
                return $user_result;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public static function getApprovedReceivedRequests($user_id)
    {
        $user = user::findOrFail($user_id);
        if ($user != null) {
            $nodes = $user->work_node();
            if (count($nodes) > 0) {
                $node = $nodes->first();
                $query_string = "SELECT br.id, br.title, br.description, br.requested_at, br.expected_at, br.delivered, br.approved, br.merged, bdnby.title AS request_by_node, bdnto.title AS request_to_node
                                FROM book_requests br
                                JOIN book_dis_nodes bdnby ON br.request_by_node = bdnby.id
                                JOIN book_dis_nodes bdnto ON br.request_to_node = bdnto.id
                                WHERE br.request_to_node = " . $node->id . " AND br.approved = 1 AND br.merged = 0 AND br.delivered = 0
                                order by br.created_at desc;
                                ";

                $user_result = DB::select(DB::raw($query_string));

                return $user_result;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public static function sendRequest($input_data, $user)
    {
        if ($input_data != null) {
            $success = false;
            $data = $input_data;

            DB::beginTransaction();

            try {
                $request = new book_request();
                $request->title = $data['title'];
                $request->description = (isset($data['description']) ? $data['description'] : "");
                $request->requested_at = date('Y-m-d', strtotime($data['requested_at']));
                $request->expected_at = date('Y-m-d', strtotime($data['expected_at']));

                $request->request_by()->associate($data['request_by_node']);
                $request->request_to()->associate($data['request_to_node']);
                $request->creator_user()->associate($user->id);

                $request->save();

                foreach ($data['lang'] as $key => $val) {
                    $key_string = explode("_", $key);
                    $title_id = $key_string[0];
                    $language_id = $key_string[1];
                    $grade_id = $key_string[2];

                    $request_detail = new book_request_detail();
                    $request_detail->grade()->associate($grade_id);
                    $request_detail->title()->associate($title_id);
                    $request_detail->language()->associate($language_id);
                    $request_detail->request()->associate($request->id);
                    $request_detail->total = $val;
                    $request_detail->save();
                }

                if (isset($data['merged_requests'])) {
                    foreach ($data['merged_requests'] as $id) {
                        $merged_request = book_request::findOrFail($id);
                        $merged_request->merged = 1;
                        $merged_request->merged_description = "Request merged and forwarded with " . $request->id;
                        $merged_request->save();
                    }
                }

                DB::commit();
                $success = true;

                if ($success) {
                    return 1;
                } else {
                    return 0;
                }
            } catch (\Exception $e) {
                $success = false;
                DB::rollback();
            }
        } else {
            return 0;
        }
    }

    public static function getRequestDetails($requestId)
    {
        $request = book_request::findOrFail($requestId);

        if ($request != null) {
            $grades_detail = [];

            $query_string = "
            SELECT brd.grade_id, (select bdmg.title from book_dis_meta_grades as bdmg where bdmg.id = brd.grade_id) as grade_title
            FROM book_request_details as brd where brd.book_request_id = " . $request->id . "
            GROUP BY brd .grade_id
            ";

            $detail_result = DB::select(DB::raw($query_string));
            if (count($detail_result) > 0) {
                foreach ($detail_result as $key => $value) {
                    $query_string_titles = "
                    SELECT brd.total as total,
                    (select title.title from book_dis_meta_titles as title where title.id = brd.title_id) as title,
                    (select lang.title from book_dis_title_languages as lang where lang.id = brd.language_id) as language
                    FROM book_request_details as brd where brd.book_request_id = " . $request->id . " and brd.grade_id = " . $value->grade_id . "
                    ORDER BY brd.title_id";

                    $detail_result_titles = DB::select(DB::raw($query_string_titles));
                    $grades_detail[$value->grade_title] = $detail_result_titles;
                }
            }

            return array(
                "request" => $request,
                'details' => $grades_detail,
                'sender' => $request->request_by,
            );
        } else {
            return null;
        }
    }

    public static function uploadRequestDocument($input_data, $user)
    {
        if ($input_data != null) {

            $data = $input_data;

            if (isset($data['request_doc']['request_id'])) {
                $book_request_result = book_request::where("id", $data['request_doc']['request_id'])->get();
                if (count($book_request_result) > 0) {
                    $book_request = $book_request_result->first();

                    if (Input::hasFile('request_doc.file.0')) {

                        $index = 0;
                        $book_request_file_result = $book_request->docs()->get();
                        if (count($book_request_file_result)) {
                            if ($book_request_file_result->last() != null) {
                                $index = $book_request_file_result->last()->id + 1;
                            }
                        }

                        $newfile = new book_request_file();
                        $filename = Input::file('request_doc.file.0')->getClientOriginalExtension();
                        $path = "files/" . $book_request->id . '_' . $index . '_request_doc.' . $filename;
                        $upload_success = Input::file('request_doc.file.0')->move(base_path() . "/files/", $book_request->id . '_' . $index . '_request_doc.' . $filename);

                        if ($upload_success) {
                            $newfile->title = (isset($data['request_doc']['title']) ? $data['request_doc']['title'] : "");
                            $newfile->file_url = $path;
                            $newfile->book_request_id = $book_request->id;
                            $newfile->save();

                            return 1;
                        }
                    } else {
                        return 0;
                    }
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public static function getRequestDocuments($request_id)
    {
        $request = book_request::findOrFail($request_id);
        $request_docs = array();
        $result = array();

        if ($request != null) {
            $request_docs = $request->docs()->get();
            $result = ["request_docs" => $request_docs];

            return $result;
        } else {
            return null;
        }
    }
}
