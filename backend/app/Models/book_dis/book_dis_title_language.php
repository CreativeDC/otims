<?php

namespace App\Models\book_dis;

use Illuminate\Database\Eloquent\Model;

class book_dis_title_language extends Model
{
    public function shipment_details(){
        return $this->hasMany('App\Models\book_dis\book_dis_shipment_detail','language_id','id');
    }
}
