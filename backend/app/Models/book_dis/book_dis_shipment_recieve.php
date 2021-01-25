<?php

namespace App\Models\book_dis;

use Illuminate\Database\Eloquent\Model;

class book_dis_shipment_recieve extends Model
{
    //
    public function shipment(){
        return $this->belongsTo('App\Models\book_dis\book_dis_shipment','book_dis_shipments_id','id');
    }
    public function receiver_user(){
        return $this->belongsTo('App\User','receiver_id','id');
    }
}