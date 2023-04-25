<?php

namespace Modules\Hrm\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use App\Helper\Helper;

class AttendenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
         $month = date('m');
         $now = Carbon::now();
        $year = $now->format('Y');
        $parameters = array(
            "search" => "",
            "sortBy" => "",
            "user_id" => "all",
            "month" =>$month,
            "year" =>$year,

        );

        $apiurl = config('apipath.attendance-list');
        $responseData = Helper::ApiServiceResponse($apiurl, $parameters);
//  dd($responseData);
        return view('Hrm::attendence.index', collect($responseData->data));
    }



    public function attendenc_data(Request $request)
    {
        $parameters = array(
            "search" => "",
            "sortBy" => "",
            "user_id" =>$request->user_id,
            "month" =>$request->month,
            "year" =>$request->year,

        );

        $apiurl = config('apipath.attendance-list');
        $responseData = Helper::ApiServiceResponse($apiurl, $parameters);
//  dd($responseData);
       return response()->json(collect($responseData->data));
       // return view('Hrm::attendence.index', collect($responseData->data));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $parameters = array(
            "language" => "1",
            "date" => $request->date,
        );

        $apiurl = config('apipath.attendance-create');
        $responseData = Helper::ApiServiceResponse($apiurl, $parameters);
        // dd($responseData);

        return view('Hrm::attendence.create', collect($responseData->data));
    }


    public function select_dept(Request $request)
    {
        $parameters = array(
            "department_id" => $request->dept_id,
        );

        $apiurl = config('apipath.attendance-dept');
        $responseData = Helper::ApiServiceResponse($apiurl, $parameters);

        return response()->json([$responseData->data]);
   
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $parameters = array(
            "user_id" => $request->user_id,
            "working_from" => $request->working_from,
            "attendence_date" => $request->attendence_date,
            "year" => $request->year,
            "month" => $request->month,
            "clock_in_time" => $request->clock_in_time,
            "clock_out_time" => $request->clock_out_time,
            "mark_attendence" => $request->mark_attendence,
            "late" => $request->late,
            "half_day" => $request->half_day,
        );


        $apiurl = config('apipath.attendance-mark');
        $responseData = Helper::ApiServiceResponse($apiurl, $parameters);
       

        if ($responseData->message) {
            return redirect()->route('hrm.attendance')->with('success', $responseData->message);
        } else {
            return redirect()->route('hrm.attendance')->with('error', $responseData->message);
        }

    }
}