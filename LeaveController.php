<?php
namespace Modules\Hrm\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Helper\Helper;

class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(isset($_GET["page"]))
        {
            $page=$_GET["page"];
        }
        else
        {
            $page=1;
        }

        $parameters = array(
            "page" => $page,
            "perPage" => "",
            "sortBy" => "",
            "orderBY" => "",
        );
        
        $apiurl = config('apipath.leave-type');
        $responseData = Helper::ApiServiceResponse($apiurl, $parameters); 

       
       return view('Hrm::leave.index', collect($responseData->data)); 
      
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    { 
        $parameters =array(
            "language" => "1",
        );

        $apiurl = config('apipath.leave-type-create');
      
        $responseData = Helper::ApiServiceResponse($apiurl, $parameters);
        

        return view('Hrm::leave.create', collect($responseData->data)); 
          
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {       
       
        $parameters =array( 
            "leave_date" => $request->leave_date, 
            "leave_type_id" => $request->leave_type_id, 
            "reason" => $request->reason, 
            "user_id"=>$request->user_id
        );


        $apiurl = config('apipath.leave-type-store');
        $responseData = Helper::ApiServiceResponse($apiurl, $parameters);  
      // dd($responseData);
        if($responseData->message){
            return redirect()->route('hrm.leave')->with('success', $responseData->message);
           
        }
        else{
            return redirect()->route('hrm.leave')->with('error', $responseData->message);
        } 

    }

   
    public function edit($id)
    {   
        $parameters =array(
            "leave_id" => $id
           );
    
        $apiurl = config('apipath.leave-type-edit');
        $responseData = Helper::ApiServiceResponse($apiurl, $parameters);

        return view('Hrm::leave.edit', collect($responseData->data)); 
      
    }


    public function update(Request $request,$id)
    {
    
   
        $parameters =array( 
            "leave_id"=>$id,
            "leave_date" => $request->leave_date,
            "leave_type_id" => $request->leave_type_id, 
            "reason" => $request->reason, 
            "user_id"=>$request->user_id,
            "status"=>$request->status
        );

        $apiurl = config('apipath.leave-type-update');
        $responseData = Helper::ApiServiceResponse($apiurl, $parameters);  
        
          

        if($responseData->message){
            return redirect()->route('hrm.leave')->with('success', $responseData->message);
           
        }
        else{
            return redirect()->route('hrm.leave')->with('error', $responseData->message);
        } 

    }


    
    public function changeStatus(Request $request)
    {
        $parameters =array(
                "leave_id" => $request->leave_id,
                "status" => $request->status,
            );

        $apiurl = config('apipath.leave-type-status');
        $responseData = Helper::ApiServiceResponse($apiurl, $parameters);

        return response()->json(['status' => 1, 'message' => $responseData->message]);
    }

 
 
}
