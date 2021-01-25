<?php

namespace App\Modules\Laracancan\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Auth;



class ShowController extends \App\Http\Controllers\Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        dd();
    }
}
