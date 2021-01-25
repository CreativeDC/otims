<?php

namespace App\Models\book_dis;

use Illuminate\Database\Eloquent\Model;

class book_dis_node_transaction extends Model
{
    public function creator_user(){
        return $this->belongsTo('App\User','creator_id','id');
    }
    public function balance(){
        return $this->hasMany('App\Models\book_dis\book_dis_node_balance','transaction_id','id');
    }
}
