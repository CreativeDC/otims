<?php

namespace App\Models\book_dis;

use Illuminate\Database\Eloquent\Model;

class book_request extends Model
{
    public function creator_user()
    {
        return $this->belongsTo('App\User', 'created_by', 'id');
    }

    public function details()
    {
        return $this->hasMany('App\Models\book_dis\book_request_detail', 'book_request_id', 'id');
    }

    public function request_by()
    {
        return $this->belongsTo('App\Models\book_dis\book_dis_node', 'request_by_node', 'id');
    }
    public function request_to()
    {
        return $this->belongsTo('App\Models\book_dis\book_dis_node', 'request_to_node', 'id');
    }

    public function docs()
    {
        return $this->hasMany('App\Models\book_dis\book_request_file', 'book_request_id', 'id');
    }
}
