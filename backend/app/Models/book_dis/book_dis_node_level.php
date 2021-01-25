<?php

namespace App\Models\book_dis;

use Illuminate\Database\Eloquent\Model;

class book_dis_node_level extends Model
{

    public function sub_levels(){
        return $this->hasMany('App\Models\book_dis\book_dis_node_level','parent_id','id');
    }
    public function parent(){
        return $this->belongsTo('App\Models\book_dis\book_dis_node_level','parent_id','id');
    }
    public function nodes(){
        return $this->hasMany('App\Models\book_dis\book_dis_node','level_id','id');
    }

    public function creator_user(){
        return $this->belongsTo('App\User','user_id','id');
    }

}