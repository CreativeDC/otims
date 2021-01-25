<?php

namespace App\Models\book_dis;

use Illuminate\Database\Eloquent\Model;

class book_request_file extends Model
{
    public function request()
    {
        return $this->belongsTo('App\Models\book_dis\book_request', 'book_request_id', 'id');
    }
}
