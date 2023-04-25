<?php

namespace Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use ApiHelper;
use Modules\HRM\Models\Department;
use Modules\HRM\Models\Leave;
use Modules\HRM\Models\Role;
use Modules\HRM\Models\RoleToPermission;
use Illuminate\Support\Str;
use Validator;
use Carbon\Carbon;
use App\Models\User;
use Modules\HRM\Models\LeaveType;

use DateTime;

class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public $page = 'leave';
    public $pageview = 'view';
    public $pageadd = 'add';
    public $pagestatus = 'remove';
    public $pageupdate = 'update';

    
    public function index(Request $request)
    {

        // Validate user page access
        $api_token = $request->api_token;
        if(!ApiHelper::is_page_access($api_token, $this->page, $this->pageview))
        return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
    
        // get all request val
        $current_page = !empty($request->page)?$request->page:1;
        $perPage = !empty($request->perPage)?$request->perPage:10;
        $search = $request->search;
        $sortBY = $request->sortBy;
        $ASCTYPE = $request->orderBY;

        $role_name = ApiHelper::get_role_from_token($api_token);
        if($role_name == 'admin' || $role_name == 'superadmin')
           $data_query = Leave::with('leave_type','user');
        else
           $data_query = Leave::with('leave_type','user')->where('user_id',ApiHelper::get_user_id_from_token($api_token));


        // search
        if(!empty($search))
            $data_query = $data_query->where("reason","LIKE", "%{$search}%");

        /* order by sorting */
        if(!empty($sortBY) && !empty($ASCTYPE)){
            $data_query = $data_query->orderBy($sortBY,$ASCTYPE);
        }else{
            $data_query = $data_query->orderBy('id','ASC');
        }

        $skip = ($current_page == 1)?0:(int)($current_page-1)*$perPage;     // apply page logic

        $data_count = $data_query->count(); // get total count

         $data_list = $data_query->skip($skip)->take($perPage)->get();  
             // get pagination data
        // if (!empty($data_list)) { 
        //     $data_list->map(function($data){
        //         $data->status = ($data->status == "0")?'Pending':(
        //             ($data->status == "1")?'Approved':'Rejected'
        //         );
        //         return $data;
        //     });
        // }

        $res = [
            'leave_list'=>$data_list,
            'current_page'=>$current_page,
            'total_records'=>$data_count,
            'total_page'=>ceil((int)$data_count/(int)$perPage),
            'per_page'=>$perPage
        ];
        return ApiHelper::JSON_RESPONSE(true,$res,'');

    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function create(Request $request)
    {
        $api_token = $request->api_token;

        $list = LeaveType::all();
        $user_list = User::all();
        $res = [
            'leave_type' => $list,
            'user_list' => $user_list,
         
        ];
        return ApiHelper::JSON_RESPONSE(true, $res, '');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $api_token = $request->api_token;
        if(!ApiHelper::is_page_access($api_token, $this->page, $this->pageadd))
        return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
    


        $validator = Validator::make($request->all(),[
            'leave_type_id' => 'required',
            //'subject' => 'required',
            'reason' => 'required',
            'leave_date'=>'required',
          
        ],[
            'leave_type_id.required'=>'LEAVE_TYPE_REQUIRED',
           // 'subject.unique'=>'LEAVE_SUBJECT_REQUIRED',
            'reason.required'=>'LEAVE_REASON_REQUIRED',
            'leave_date.required'=>'LEAVE_FROMDATE_REQUIRED',
           
        ]);
        if ($validator->fails())
            return ApiHelper::JSON_RESPONSE(false,[],$validator->messages());

          $leave_data='';
          $select_date = explode(',', $request->leave_date);
            if($request->has('leave_date') ){
                foreach ($select_date as $key => $value) {
                    // return ApiHelper::JSON_RESPONSE(true, Carbon::createFromFormat('d/m/y',$value)->format('Y-m-d'),'SUCCESS_LEAVE_SUBMIITE');

                 $leave_data=    Leave::create([
                         'leave_date'=>Carbon::createFromFormat('m/d/Y', $value)->format(' Y-m-d'), 
                         'user_id' =>$request->user_id, 
                         'created_by'=>ApiHelper::get_adminid_from_token($request->api_token),
                         'leave_type_id' =>$request->leave_type_id,  
                         'reason' =>$request->reason,  
                
                      ]);
                  }
      
             } 
      


    //     $Insert = $request->only('leave_type_id','reason');
    //     $from_date = Carbon::createFromFormat('d/m/Y',$request->from_date)->format('Y-m-d');
    //     $to_date = Carbon::createFromFormat('d/m/Y',$request->to_date)->format('Y-m-d');

    //      $from_date_select = new DateTime($from_date);
    //      $to_date_select = new DateTime($to_date);
    //      $interval = $to_date_select->diff($from_date_select);
               
    //   //    return ApiHelper::JSON_RESPONSE(true, (int)$interval->format('%a') +1  ,'SUCCESS_LEAVE_SUBMIITE');



    //     $Insert['total_leave_days'] = (int)$interval->format('%a') +1;
    //     $Insert['from_date'] = $from_date;
    //     $Insert['to_date'] = $to_date;
    //     $Insert['user_id'] = ApiHelper::get_user_id_from_token($api_token);
    //     $res = Leave::create($Insert);



        if($leave_data)
            return ApiHelper::JSON_RESPONSE(true,$leave_data,'SUCCESS_LEAVE_SUBMIITE');
        else
            return ApiHelper::JSON_RESPONSE(false,[],'ERROR_LEAVE_SUBMIITE');

    





    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $api_token = $request->api_token;
     
      

        $data_list = Leave::where('id',$request->leave_id)->first();
    
        $list = LeaveType::all();
        $user_list = User::all();
        $res =[
            'leave_type'=> $list ,
            'user_list'=>$user_list,
            'data_list'=>$data_list
        ];
        return ApiHelper::JSON_RESPONSE(true,$res,'');

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $api_token = $request->api_token;
      

        if(!ApiHelper::is_page_access($api_token, $this->page, $this->pageupdate))
        return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');

        // $user_id = ApiHelper::get_adminid_from_token($api_token);
        // $Insert = $request->only('dept_name','dept_details','status');
        // $res = Department::where('id',$updateId)->update($Insert);



        // $leave_data='';
        // $select_date = explode(',', $request->leave_date);
        //   if($request->has('leave_date') ){
        //       foreach ($select_date as $key => $value) {
        //           // return ApiHelper::JSON_RESPONSE(true, Carbon::createFromFormat('d/m/y',$value)->format('Y-m-d'),'SUCCESS_LEAVE_SUBMIITE');


               $leave_data=Leave::where('id',$request->leave_id)->update(
                   [
                       'leave_date'=>Carbon::createFromFormat('m/d/Y', $request->leave_date)->format(' Y-m-d'),
                       'user_id' =>$request->user_id, 
                       'created_by'=>ApiHelper::get_adminid_from_token($request->api_token),
                       'leave_type_id' =>$request->leave_type_id,  
                       'reason' =>$request->reason, 
                       'status'=>$request->status 
              
                    ]);

                  
        //         }
    
        //    } 
    

        if($leave_data)
            return ApiHelper::JSON_RESPONSE(true,$leave_data,'SUCCESS_LEAVE_UPDATE');
        else
            return ApiHelper::JSON_RESPONSE(false,[],'ERROR_LEAVE_UPDATE');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $api_token = $request->api_token;
        $id = $request->deleteId;

        if(!ApiHelper::is_page_access($api_token,$this->DepartmentDelete)){
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        }
        $status = Department::where('leave_id',$id)->delete();
        if($status) {
            return ApiHelper::JSON_RESPONSE(true,[],'SUCCESS_DEPARTMENT_DELETE');
        }else{
            return ApiHelper::JSON_RESPONSE(false,[],'ERROR_DEPARTMENT_DELETE');
        }
    }
    public function statusChange(Request $request){
        $api_token = $request->api_token;
        $id = $request->leave_id;
        $status = $request->status;
        $res = Leave::where('leave_id', $id)->update(['status'=>$status]);
        $msg = ($status == '1')? 'APPROVED' : 'REJECTED';
        
        // generate notification
        $leave = Leave::where('leave_id',$id)->first();
        $c_msg = "Your Leave $msg from ".$leave->from_date.' To '. $leave->to_date;
        $title = 'LEAVE_'.$msg;
        $gendata = [ "user_id" => $leave->user_id, "type" => 1, "title" =>$title , "msg" =>$c_msg  ];
        ApiHelper::generate_notification($gendata);

        return ApiHelper::JSON_RESPONSE(true,$res,'LEAVE_'.$msg);
    }
}
