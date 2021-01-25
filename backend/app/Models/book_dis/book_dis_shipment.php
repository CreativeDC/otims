<?php

namespace App\Models\book_dis;

use Illuminate\Database\Eloquent\Model;

class book_dis_shipment extends Model
{
    public function creator_user(){
        return $this->belongsTo('App\User','creator_id','id');
    }
    public function details(){
        return $this->hasMany('App\Models\book_dis\book_dis_shipment_detail','book_dis_shipments_id','id');
    }
    public function receive(){
        return $this->hasMany('App\Models\book_dis\book_dis_shipment_recieve','book_dis_shipments_id','id');
    }
    public function from_node(){
        return $this->belongsTo('App\Models\book_dis\book_dis_node','from','id');
    }
    public function to_node(){
        return $this->belongsTo('App\Models\book_dis\book_dis_node','to','id');
    }

    public function docs(){
        return $this->hasMany('App\Models\book_dis\book_dis_shipment_file','book_dis_shipments_id','id');
    }
}
