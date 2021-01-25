<?php

namespace App\Models\book_dis;

use Illuminate\Database\Eloquent\Model;

class province extends Model
{
    protected $fillable = ['dr_name', 'en_name', 'ps_name'];

    public function districts()
    {
        return $this->hasMany('App\Models\book_dis\district', 'province_id', 'id');
    }
}
