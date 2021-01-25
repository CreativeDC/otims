<?php

namespace App\Models\book_dis;

use Illuminate\Database\Eloquent\Model;

class book_dis_node_balance_detail extends Model
{
    public function node(){
        return $this->belongsTo('App\Models\book_dis\book_dis_node','node_id','id');
    }

    public function grade(){
        return $this->belongsTo('App\Models\book_dis\book_dis_meta_grade','grade_id','id');
    }

    public function title(){
        return $this->belongsTo('App\Models\book_dis\book_dis_meta_title','title_id','id');
    }

    public function lang(){
        return $this->belongsTo('App\Models\book_dis\book_dis_title_language','language_id','id');
    }
}
