<?php

namespace Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use ApiHelper;
use Modules\HRM\Models\Attendance;
use Illuminate\Support\Str;
use Validator;
use Modules\HRM\Models\AttendenceReport;
use Modules\HRM\Models\Department;
use Modules\CRM\Models\CRMCustomer;
use Carbon\Carbon;
use Modules\HRM\Models\Staff;
use App\Models\User;
use Modules\HRM\Models\Holiday;
use Modules\HRM\Models\LeaveType;


class AttendanceController extends Controller
{
    public $timezone = 'UTC';



    public function index(Request $request)
    {
        $employees = User::with(
            [
                'attendence' => function ($query) use ($request) {
                    $query->whereRaw('MONTH(hrm_attendances.clock_in_time) = ?', [$request->month])
                        ->whereRaw('YEAR(hrm_attendances.clock_in_time) = ?', [$request->year])
                        ->whereRaw('status=?', 'present');
                    },

                'leaves' => function ($query) use ($request) {
                    $query->whereRaw('MONTH(hrm_leaves.leave_date) = ?', [$request->month])
                        ->whereRaw('YEAR(hrm_leaves.leave_date) = ?', [$request->year])
                        ->where('status', 'approved');
                    }
            ]
     
        );

        if ($request->user_id != 'all') {
            $employees = $employees->where('usr_users.id', $request->user_id);
        }

        $employees = $employees->get();


        $holidays = Holiday::whereRaw('MONTH(hrm_holidays.date) = ?', [$request->month])->whereRaw('YEAR(hrm_holidays.date) = ?', [$request->year])->get();


        $final = [];
        $holidayOccasions = [];

        $daysInMonth = Carbon::parse('01-' . $request->month . '-' . $request->year)->daysInMonth;
        $now = Carbon::now();
        $requestedDate = Carbon::parse(Carbon::parse('01-' . $request->month . '-' . $request->year))->endOfMonth();


        foreach ($employees as $employee) {

        $employees_name = $employee->first_name;
            
            $dataBeforeJoin = null;

            $dataTillToday = array_fill(1, $now->copy()->format('d'), 'Absent');

            if (($now->copy()->format('d') != $daysInMonth) && !$requestedDate->isPast()) {
                $dataFromTomorrow = array_fill($now->copy()->addDay()->format('d'), ((int)$daysInMonth - (int)$now->copy()->format('d')), '-');
            }
            else {
                $dataFromTomorrow = array_fill($now->copy()->addDay()->format('d'), ((int)$daysInMonth - (int)$now->copy()->format('d')), 'Absent');
            } 

            $final[$employee->id . '#' . $employee->first_name] = array_replace($dataTillToday, $dataFromTomorrow);

            //return ApiHelper::JSON_RESPONSE(true, $employee, '');
         
            if(!empty($employee->attendence)){
                foreach ($employee->attendence as $attendance) {
                    $final[$employee->id . '#' . $employee->first_name][Carbon::parse($attendance->clock_in_time)->day] = 'Present';

                    
                }
            }

            if(Carbon::parse('01-' . $request->month . '-' . $request->year)->isFuture()){
                $dataBeforeJoin = array_fill(1, $daysInMonth, '-');
            }

            if(!is_null($dataBeforeJoin)){
                $final[$employee->id . '#' . $employee->first_name] = array_replace($final[$employee->id . '#' . $employee->first_name], $dataBeforeJoin);
            }

            foreach ($employee->leaves as $leave) { 
 
                $final[$employee->id . '#' . $employee->first_name][Carbon::parse($leave->leave_date)->day] = 'Leave';
            }

            
            
            foreach ($holidays as $holiday) {
                if ($final[$employee->id . '#' . $employee->first_name][Carbon::parse($holiday->date)->day] == 'Absent' || $final[$employee->id . '#' . $employee->first_name][Carbon::parse($holiday->date)->day] == '-') {
                    $final[$employee->id . '#' . $employee->first_name][Carbon::parse($holiday->date)->day] = 'Holiday';
                    $holidayOccasions[Carbon::parse($holiday->date)->day] = $holiday->occassion;
                }
            }
        }

         $month = date('m');
         $now = Carbon::now();
         $year = $now->format('Y');
         $days = Carbon::now()->month($month)->daysInMonth;
         $user_data = User::all();

        $res = [
            'employeeAttendence' => $final,
            'daysInMonth' => $daysInMonth,
            'month' => $month,
            'year' => $year,
            'days' => $days,
            'user_data' => $user_data
        ];
        return ApiHelper::JSON_RESPONSE(true, $res, '');
        // $holidayOccasions = $holidayOccasions;
        // return ApiHelper::JSON_RESPONSE(true, $employeeAttendence, '');
    }

    public function attendData(Request $request){
             // Validate user page access
        // $api_token = $request->api_token;

        // $user_id = $request->user_id;
        $this->index();
        $month = date('m');

        $data_list = User::all();

        //   $daysInMonth = Carbon::now()->daysInMonth; 



        // if (!empty($data_list)) {
        //     $data_list = $data_list->map(function ($data) {

        //         $reportList = AttendenceReport::where('user_id', $data->id)->get();

        //         // return ApiHelper::JSON_RESPONSE(true,  $reportList, '');


        //         $data->attd_data =
        //             $reportList->map(function ($attdStatus) use ($data) {

        //                 return $attdStatus;
        //             });


        //         return $data;
        //     });
        // }



        // $daysInMonth = Carbon::parse('01-' . '02' . '-' . '2023')->daysInMonth;
        // $now = Carbon::now();
        // $dataBeforeJoin = [];
        // $requestedDate = Carbon::parse(Carbon::parse('01-' . '02' . '-' . '2023'))->endOfMonth();
      
        $now = Carbon::now();
        $year = $now->format('Y');
        $days = Carbon::now()->month($month)->daysInMonth;
        $user_data = User::all();


        $res = [
            'data_list' => $data_list,
            'year' => $year,
            'month' => $month,
            'days' => $days,
            'user_data' => $user_data,
        ];

        return ApiHelper::JSON_RESPONSE(true, $res, '');
    }

    public function checkIn(Request $request)
    {

        $api_token = $request->api_token;
        $clientIP = $request->ip();
        $user_id = $request->user_id;
        $date = $request->attendence_date;
        $year = $request->year;
        $month = $request->month;

        $clock_In = ($date . ' ' . $request->clock_in_time);
        $clock_Out = ($date . ' ' . $request->clock_out_time);

        $dayCount = date('t', strtotime('01-' . $month . '-' . $year));
        // in case of july 2022 it will be 31

        $selected_clock = [];
        for ($i = 1; $i <= $dayCount; $i++) {
            $days = sprintf('%04d-%02d-%02d', $year, $month, $i);
            $clockIn = ($days . ' ' . $request->clock_in_time);
            $clockOut = ($days . ' ' . $request->clock_out_time);

            array_push($selected_clock, [
                "clockIn" => $clockIn,
                "clockOut" => $clockOut
            ]);
        }


        //   return ApiHelper::JSON_RESPONSE(true,$clockOut,'SUCCESS_TODAY_ATTENDANCE_FOUND');

        //    if($request->has("mark_attendence" == '1') ){
        $monthlyAttend = '';

        foreach ($user_id as  $key => $value) {

            //  if($request->has('mark_attendence' == '1') ){
            foreach ($selected_clock as $clockKey) {

                $clock_in_time = $clockKey['clockIn'];
                $clock_out_time = $clockKey['clockOut'];
                $monthlyAttend = AttendenceReport::create(

                    [
                        'user_id' => $value,
                        'clock_in_time' => $clock_in_time,
                        'clock_out_time' => $clock_out_time,
                        'working_from' => $request->working_from,
                        'late' => $request->late,
                        'half_day' => $request->half_day,
                        'clock_in_ip' => $clientIP,
                        'clock_out_ip' => $clientIP
                    ]
                );
            }
            //}
        }

        if ($request->has('mark_attendence' == '0')) {
            foreach ($user_id as $key => $value) {
                AttendenceReport::create([
                    'user_id' => $value,
                    'clock_in_time' => $clock_In,
                    'clock_out_time' => $clock_Out,
                    'working_from' => $request->working_from,
                    'late' => $request->late,
                    'half_day' => $request->half_day,
                    'clock_in_ip' => $clientIP,
                    'clock_out_ip' => $clientIP
                ]);
            }
        }


        if ($monthlyAttend) {
            return ApiHelper::JSON_RESPONSE(true, $monthlyAttend, 'SUCCESS_TODAY_ATTENDANCE_FOUND');
        } else {
            return ApiHelper::JSON_RESPONSE(false, [], 'ERROR_TODAY_ATTENDANCE_FOUND');
        }
    }

    public function create(Request $request)
    {
        $api_token = $request->api_token;
        $department_id = $request->department_id;

        $dept_list = Department::all();


        // $user_list = Staff::where('department_id', $department_id)->get();
        $now = Carbon::now();
        $year = $now->format('Y');
        $list = LeaveType::all();
        $user_list = Staff::all();



        $res = [

            'dept_list' => $dept_list,
            'user_list' => $user_list,
            'year' => $year,
            'leave_type'=> $list ,
        ];

        return ApiHelper::JSON_RESPONSE(true, $res, '');
    }


    public function select_dept(Request $request)
    {
        $api_token = $request->api_token;
        $department_id = $request->department_id;

        $dept_list = Department::all();


        $user_list = Staff::where('department_id', $department_id)->get();

        $res = [


            'user_list' => $user_list,

        ];

        return ApiHelper::JSON_RESPONSE(true, $res, '');
    }
}
