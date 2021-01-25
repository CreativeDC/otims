<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Auth;
//app model
use App\Models\Application\goal;
use App\Models\Application\ir;
use App\Models\Application\result;
use App\Models\Application\activity;

use Excel;
use App\ResourceManager\ChartRM;



class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        EmailRM::sendEmailTrainingLoopAction("" , "d","s","acr.email.training_loop.index");

       /* $beautymail = app()->make(\Snowfire\Beautymail\Beautymail::class);
        $beautymail->send('acr.overallview.list2', [], function($message)
        {
            $message
                ->from('ACRM&EIS@acrmeis.com')
                ->to('ahmadr@readafghanistan.com', 'Ahmad Rezae')
                ->subject('This is the test mail from online server!');
        });*/

    }

    /**
     * Show the application overall view on PMEP .
     *
     * @return \Illuminate\Http\Response
     */
    public function showAll()
    {

        $goals = Goal::all();
        return view('acr.overallview.list')
            ->with('goals', $goals);
    }
    /**
     * Show the activities on Activity Sheet table .
     *
     * @return \Illuminate\Http\Response
     */
    public function showActivities_according_to_user_role()
    {
        return redirect()->back()->withInput();

    }
	/**
     * Show the application overall view on Dashboard .
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        session(['menu'=>'dashboard']);
		$goals = Goal::all();
        return view('acr.dashboard')
            ->with('goals', $goals);
    }
}
