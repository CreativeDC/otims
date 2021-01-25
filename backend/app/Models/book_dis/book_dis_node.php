<?php

namespace App\Models\book_dis;

use Illuminate\Database\Eloquent\Model;

class book_dis_node extends Model
{
    public function sub_nodes()
    {
        return $this->hasMany('App\Models\book_dis\book_dis_node', 'parent_id', 'id');
    }
    public function parent_node()
    {
        return $this->belongsTo('App\Models\book_dis\book_dis_node', 'parent_id', 'id');
    }
    public function level()
    {
        return $this->belongsTo('App\Models\book_dis\book_dis_node_level', 'level_id', 'id');
    }
    public function province_loc()
    {
        return $this->belongsTo('App\Models\book_dis\province', 'province', 'id');
    }
    public function district_loc()
    {
        return $this->belongsTo('App\Models\book_dis\district', 'district', 'id');
    }
    public function staffs()
    {
        return $this->belongsToMany('App\User', "book_dis_node_staffs", 'node_id', 'user_id')
            ->withTimestamps();
    }
    public function group_staffs()
    {
        return $this->belongsToMany('App\User', "book_dis_node_staffs_groups", 'node_id', 'user_id')
            ->withPivot('active')
            ->withTimestamps();
    }
    public function shipment_froms()
    {
        return $this->hasMany('App\Models\book_dis\book_dis_shipment', 'from', 'id');
    }
    public function shipment_tos()
    {
        return $this->hasMany('App\Models\book_dis\book_dis_shipment', 'to', 'id');
    }

    public function receipt_tos()
    {
        return $this->hasMany('App\Models\book_dis\book_dis_receipt', 'to', 'id');
    }
    public function balance()
    {
        return $this->hasMany('App\Models\book_dis\book_dis_node_balance', 'node_id', 'id');
    }

    public function creator_user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function requests_by()
    {
        return $this->hasMany('App\Models\book_dis\book_request', 'request_by_node', 'id');
    }

    public function requests_to()
    {
        return $this->hasMany('App\Models\book_dis\book_request', 'request_to_node', 'id');
    }

}
