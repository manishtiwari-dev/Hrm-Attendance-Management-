<?php

namespace Modules\Hrm\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Hrm\Models\Designation;
use App\Helper\Helper;

class HolidayController extends Controller
{
    public function index(){
        $parameters = array(
            
        );
        
        $apiurl = config('apipath.holiday');
        $responseData = Helper::ApiServiceResponse($apiurl, $parameters); 

// dd($responseData);       
       return view('Hrm::holiday.index', collect($responseData->data));
    }


    public function create(){
        $parameters = array(
            
        );
        
        $apiurl = config('apipath.holiday-create');
        $responseData = Helper::ApiServiceResponse($apiurl, $parameters); 

       
       return view('Hrm::holiday.create', collect($responseData->data));
    }

    public function show(){

    }

    public function store(Request $request){
        $parameters =array( 
            "holiday_date" => $request->holiday_date,
            'occasion' => $request->occasion
        );


        $apiurl = config('apipath.holiday-store');
        $responseData = Helper::ApiServiceResponse($apiurl, $parameters);  
    //    dd($responseData);

        if($responseData->message){
            return redirect()->route('hrm.holiday.index')->with('success', $responseData->message);
           
        }
        else{
            return redirect()->route('hrm.holiday,index')->with('error', $responseData->message);
        }
    }

    public function edit($id){

    }

    public function update(Request $request , $id){

    }

    public function destroy($id){

    }

    public function markDayHoliday(Request $request)
    { 

        $parameters =array( 
            "office_holiday_days" => $request->office_holiday_days,
            'office_saturday_days' => $request->office_saturday_days
        );
        
        // dd($parameters);
 
        $apiurl = config('apipath.holiday-markDayHoliday');

        $responseData = Helper::ApiServiceResponse($apiurl, $parameters); 

        // dd($responseData);
        
        return response()->json(['success' => $responseData->message]);
    }
}