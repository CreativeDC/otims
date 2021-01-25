<?php

namespace App\Models\book_dis;

use Illuminate\Database\Eloquent\Model;

class book_dis_meta_title extends Model
{
    public function shipment_details(){
        return $this->hasMany('App\Models\book_dis\book_dis_shipment_detail','title_id','id');
    }
    public function parent_grade(){
        return $this->belongsTo('App\Models\book_dis\book_dis_meta_grade','grade_id','id');
    }
}
