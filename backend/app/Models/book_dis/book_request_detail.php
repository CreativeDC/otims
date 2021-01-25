<?php

namespace App\Models\book_dis;

use Illuminate\Database\Eloquent\Model;

class book_request_detail extends Model
{
    public function request()
    {
        return $this->belongsTo('App\Models\book_dis\book_request', 'book_request_id', 'id');
    }
    public function language()
    {
        return $this->belongsTo('App\Models\book_dis\book_dis_title_language', 'language_id', 'id');
    }
    public function title()
    {
        return $this->belongsTo('App\Models\book_dis\book_dis_meta_title', 'title_id', 'id');
    }
    public function grade()
    {
        return $this->belongsTo('App\Models\book_dis\book_dis_meta_grade', 'grade_id', 'id');
    }
}
