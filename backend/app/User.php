<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Hamedmehryar\Laracancan\Traits\LaracancanUserTrait;

class User extends Authenticatable
{
    use LaracancanUserTrait;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /*********   Book Distribution inventory parts start   *********/
    public function level(){
        return $this->hasMany('App\Models\book_dis\book_dis_node_level','user_id','id');
    }
    public function shipments(){
        return $this->hasMany('App\Models\book_dis\book_dis_shipment','creator_id','id');
    }
    public function receipts(){
        return $this->hasMany('App\Models\book_dis\book_dis_receipt','creator_id','id');
    }
    public function received_shipments(){
        return $this->hasMany('App\Models\book_dis\book_dis_shipment_recieve','receiver_id','id');
    }
    public function work_node()
    {
        return $this->belongsToMany('App\Models\book_dis\book_dis_node',"book_dis_node_staffs",'user_id','node_id')
            ->withTimestamps();
    }
    public function transaction_balance()
    {
        return $this->hasMany('App\Models\book_dis\book_dis_node_balance','creator_id','id');
    }
    public function transaction_record()
    {
        return $this->hasMany('App\Models\book_dis\book_dis_node_transaction','creator_id','id');
    }
    public function group_node()
    {
        return $this->belongsToMany('App\Models\book_dis\book_dis_node',"book_dis_node_staffs_groups",'user_id','node_id')
            ->withPivot('active')
            ->withTimestamps();
    }
    /*********   Book Distribution inventory parts end   *********/


    // added by Zabeeh
    public function rollApiKey(){
        do {
            $this->api_token = str_random(60);
        } while($this->where('api_token', $this->api_token)->exists());

        $this->save();
    }
}
