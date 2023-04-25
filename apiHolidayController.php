<?php

namespace Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use ApiHelper;
use Modules\HRM\Models\Department;
use Modules\HRM\Models\Role;
use Modules\HRM\Models\RoleToPermission;
use Illuminate\Support\Str;
use Validator;
use Modules\HRM\Models\Holiday;
use Carbon\Carbon;


class HolidayController extends Controller
{
	public function index(){

     

        $events = Holiday::all();

        

        $currentYear = Carbon::now()->format('Y');
        $currentMonth = Carbon::now()->month;

        /* year range from last 5 year to next year */
        $years = [];
        $latestFifthYear = (int)Carbon::now()->subYears(5)->format('Y');
        $nextYear = (int)Carbon::now()->addYear()->format('Y');

        for ($i = $latestFifthYear; $i <= $nextYear; $i++) {
            $years[] = $i;
        }

        $years = $years;

        /* months */
        $months = ['january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december'];


        $days =  Holiday::DAYS;

        $attandanceSetting = '["1","2","3","4","5","6"]';
        $sunday = false;

        if((is_array(json_decode($attandanceSetting)) && !in_array('0', json_decode($attandanceSetting))) || json_decode($attandanceSetting) == null){
            $sunday = true;
        }


        $holidays = $this->missingNumber(json_decode($attandanceSetting));
        $holidaysArray = [];

        foreach ($holidays as $index => $holiday) {
            $holidaysArray[$holiday] = $days[$holiday - 1];
        }

        if (($key = array_search('Sunday', $holidaysArray)) !== false && $sunday == false) {
            unset($holidaysArray[$key]);
        }

        $holidaysArray = $holidaysArray;


        $holSatArray = [];

        foreach ($holidays as $index => $holiday) {
            $holSatArray[$holiday] = $days[$holiday - 2];
        }

        if (($key = array_search('Saturday', $holSatArray)) !== false && $sunday == false) {
            unset($holSatArray[$key]);
        }

        $holSatArray = $holSatArray;



        $res = [
        	'currentYear' =>$currentYear,
        	'currentMonth' => $currentMonth,
        	'years' => $years,
        	'months' => $months,
        	'holidaysArray' => $holidaysArray,
            'holSatArray' => $holSatArray,
            'events' => $events

        ];

        return ApiHelper::JSON_RESPONSE(true,$res,'');
    }


    public function create(){

    	$res = [
    	];

          return ApiHelper::JSON_RESPONSE(true,$res,'');
    }

    public function missingNumber($num_list)
    {
        // construct a new array
        $new_arr = range(1, 7);

        if(is_null($num_list))
        {
            return $new_arr;
        }

        return array_diff($new_arr, $num_list);
    }

    public function show(){

    }

    public function store(Request $request){

    	$api_token = $request->api_token;

		$user_id = ApiHelper::get_user_id_from_token($api_token);

        $holiday = array_combine($request->holiday_date, $request->occasion);


        foreach ($holiday as $index => $value) {
            if ($index && $value != '') {

                $data = Holiday::firstOrCreate([
                    'date' =>  Carbon::createFromFormat('m/d/Y', $index)->format(' Y-m-d'),
                    'occassion' => $value,
                    'added_by' => $user_id,
                ]);

                
            }
        }



        if ($data) {
            return ApiHelper::JSON_RESPONSE(true, $data, 'SUCCESS_ADD_HOLIDAY'); 
        }else{
            return ApiHelper::JSON_RESPONSE(false, '', 'ERROR_HOLIDAY_ADD');
        }
    }

    public function edit(Request $request){

    }

    public function update(Request $request){

    }

    public function destroy(Request $request){
        
    }


    public function markDayHoliday(Request $request)
    {

        $api_token = $request->api_token;

        $user_id = ApiHelper::get_user_id_from_token($api_token);

        $year = Carbon::now()->format('Y');



        if ($request->has('year')) {
            $year = $request->has('year'); 
        }

        $dayss = [];
        $days = Holiday::WEEKDAYS;

        if ($request->office_holiday_days != null && $request->office_holiday_days > 0) {
            foreach ($request->office_holiday_days as $holiday) {
                $dayss[] = $days[($holiday - 1)]; 
                $day = $holiday;

                if ($holiday == 7) {
                    $day = 0;
                }

                $dateArray = $this->getDateForSpecificDayBetweenDates($year . '-01-01', $year . '-12-31', ($day));

                
                    foreach ($dateArray as $date) {
                        Holiday::firstOrCreate([
                            'date' => $date,
                            'occassion' => $days[$day],
                            'added_by' => $user_id,
                        ]);
                    }
            }
        }

        if ($request->office_saturday_days != null && $request->office_saturday_days > 0) {
            foreach ($request->office_saturday_days as $holiday) {
                $dayss[] = $days[($holiday - 2)];

                $day = $holiday;

                if ($holiday == 7) {
                    $day = 6;
                }

                $dateArray = $this->getDateForSpecificDayBetweenDates($year . '-01-01', $year . '-12-31', ($day));

                
                    foreach ($dateArray as $date) {
                        Holiday::firstOrCreate([
                            'date' => $date,
                            'occassion' => $days[$day],
                            'added_by' => $user_id,
                        ]);
                    }
            }
        }



          return ApiHelper::JSON_RESPONSE(true, [], 'SUCCESS_ADD_HOLIDAY'); 
    }


    public function getDateForSpecificDayBetweenDates($startDate, $endDate, $weekdayNumber)
    {
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);

        $dateArr = [];

        do {
            if (date('w', $startDate) != $weekdayNumber) {
                $startDate += (24 * 3600); // add 1 day
            }
        } while (date('w', $startDate) != $weekdayNumber);


        while ($startDate <= $endDate) {
            $dateArr[] = date('Y-m-d', $startDate);
            $startDate += (7 * 24 * 3600); // add 7 days
        }

        return ($dateArr);
    }

}