<?php

namespace App\Models\book_dis;

use Illuminate\Database\Eloquent\Model;

class district extends Model
{
    protected $fillable = ['dr_name', 'en_name', 'ps_name'];

    public function province()
    {
        return $this->belongsTo('App\Models\book_dis\province', 'province_id', 'id');
    }
}
