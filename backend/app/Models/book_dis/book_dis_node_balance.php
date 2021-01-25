<?php

namespace App\Models\book_dis;

use Illuminate\Database\Eloquent\Model;

class book_dis_node_balance extends Model
{

    protected $fillable = ['active'];

    public function creator_user(){
        return $this->belongsTo('App\User','creator_id','id');
    }
    public function node(){
        return $this->belongsTo('App\Models\book_dis\book_dis_node','node_id','id');
    }
    public function transaction(){
        return $this->belongsTo('App\Models\book_dis\book_dis_node_transaction','transaction_id','id');
    }

}
