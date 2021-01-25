<?php

namespace App\Models\book_dis;

use Illuminate\Database\Eloquent\Model;

class book_dis_meta_grade extends Model
{
    public function shipment_details(){
        return $this->hasMany('App\Models\book_dis\book_dis_shipment_detail','grade_id','id');
    }

    public function titles(){
        return $this->hasMany('App\Models\book_dis\book_dis_meta_title','grade_id','id');
    }
}