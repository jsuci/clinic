<?php

namespace App\Http\Controllers\HRControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use \Carbon\Carbon;
use Carbon\CarbonPeriod;
use Crypt;
use File;
use DateTime;
use DateInterval;
use DatePeriod;
use PDF;
use Session;
use App\Models\HR\HREmployeeAttendance;
class HRAttendanceController extends Controller
{
    
    public function index(Request $request)
    {
        date_default_timezone_set('Asia/Manila');
        
        if($request->get('changedate') == true){
            
            $date = $request->get('changedate');

        }else{

            $date = date('Y-m-d');

        }
        
        $getMyid = DB::table('teacher')
            ->select('id')
            ->where('userid', auth()->user()->id)
            ->first();
        
        $employees = DB::table('teacher')
            ->select(
                'teacher.id',
                'teacher.firstname',
                'teacher.middlename',
                'teacher.lastname',
                'teacher.suffix',
                'teacher.picurl',
                'employee_personalinfo.gender',
                'usertype.id as usertypeid',
                'usertype.utype'
                )
            ->join('usertype','teacher.usertypeid','=','usertype.id')
            ->leftJoin('employee_personalinfo','teacher.id','=','employee_personalinfo.employeeid')
            ->where('teacher.deleted','0')
            ->where('teacher.isactive','1')
            // ->take(20)
            ->orderBy('lastname','asc')
            ->get();
          
    
        $detecttimeschedsetup = DB::table('deduction_tardinesssetup')
            ->where('status','1')
            ->first();

        $attendancearray = array();
        
        foreach($employees as $employee){

            $attendance = HREmployeeAttendance::getattendance($date, $employee);
            // return $attendance;
            array_push($attendancearray,(object)array(
                'employeeinfo'      => $employee,
                'attendance'        => (object)array(
                                            'in_am'             =>     $attendance->amin,
                                            'out_am'            =>     $attendance->amout,
                                            'in_pm'             =>     $attendance->pmin,
                                            'out_pm'            =>     $attendance->pmout,
                                            'taphistorystatus'  =>     $attendance->status
                                        )
            ));

        }
        if($request->get('changedate') == true){
            
            $attendance = array();

            return view('hr.attendance.changedate')
                ->with('currentdate',$date)
                ->with('attendance',$attendancearray);

        }else{
            // return $attendancearray;
            return view('hr.attendance.index')
                ->with('currentdate',$date)
                ->with('attendance',$attendancearray);

        }
    }
    public function indexv2(Request $request)
    {
        if($request->has('action'))
        {
            $search = $request->get('search');
            $search = $search['value'];

            $employees = DB::table('teacher')
            ->select(
                'teacher.id',
                'teacher.firstname',
                'teacher.middlename',
                'teacher.lastname',
                'teacher.suffix',
                'teacher.picurl',
                'teacher.tid',
                'employee_personalinfo.gender',
                'usertype.id as usertypeid',
                'usertype.utype'
                )
            ->leftJoin('usertype','teacher.usertypeid','=','usertype.id')
            ->leftJoin('employee_personalinfo','teacher.id','=','employee_personalinfo.employeeid')
            ->where('teacher.deleted','0')
            ->where('teacher.isactive','1');
            
            if($search != null){
                    $employees = $employees->where(function($query) use($search){
                                        $query->orWhere('firstname','like','%'.$search.'%');
                                        $query->orWhere('lastname','like','%'.$search.'%');
                                });
            }
            
            $employees = $employees->take($request->get('length'))
                ->skip($request->get('start'))
                ->orderBy('lastname','asc')
                // ->whereIn('studinfo.studstatus',[1,2,4])
                ->get();
                
            $employeescount = DB::table('teacher')
            ->select(
                'teacher.id',
                'teacher.firstname',
                'teacher.middlename',
                'teacher.lastname',
                'teacher.suffix',
                'teacher.picurl',
                'employee_personalinfo.gender',
                'usertype.id as usertypeid',
                'usertype.utype'
                )
            ->leftJoin('usertype','teacher.usertypeid','=','usertype.id')
            ->leftJoin('employee_personalinfo','teacher.id','=','employee_personalinfo.employeeid')
            ->where('teacher.deleted','0')
            ->where('teacher.isactive','1');
                
            if($search != null){
                    $employeescount = $employeescount->where(function($query) use($search){
                                    $query->orWhere('firstname','like','%'.$search.'%');
                                    $query->orWhere('lastname','like','%'.$search.'%');
                                });
            }
            
            
            $employeescount = $employeescount
                ->orderBy('lastname','asc')
                // ->whereIn('studinfo.studstatus',[1,2,4])
                ->count();
                
                
            if($request->has('changedate')){
                
                $date = $request->get('changedate');

            }else{

                $date = date('Y-m-d');

            }
            foreach($employees as $employee)
            {
                $employee->lastname = strtoupper($employee->lastname);
                $employee->firstname = strtoupper($employee->firstname);
    
                $attendance = HREmployeeAttendance::getattendance($date, $employee);
                // return $attendance;
                $employee->amin = $attendance->amin != '00:00:00' ? $attendance->amin : '';
                $employee->amout = $attendance->amout != '00:00:00' ? $attendance->amout : '';
                $employee->pmin = $attendance->pmin != '00:00:00' ? $attendance->pmin : '';
                $employee->pmout = $attendance->pmout != '00:00:00' ? $attendance->pmout : '';
                $employee->attstatus = $attendance->status;

                $remarks = DB::table('hr_attendanceremarks')
                    ->where('tdate',$date)
                    ->where('employeeid', $employee->id)
                    ->where('deleted','0')
                    ->first();

                    
                $employee->remarks = $remarks->remarks ?? null;
    
            }
            return @json_encode((object)[
                'data'=>$employees,
                'recordsTotal'=>$employeescount,
                'recordsFiltered'=>$employeescount
            ]);
        }else{
            return view('hr.attendance.indexv2');
        }
          
    }
    public function gettimelogs(Request $request)
    {
        date_default_timezone_set('Asia/Manila');

        $customtimesched = DB::table('employee_customtimesched')
            ->where('employeeid',$request->get('employeeid'))
            ->where('deleted','0')
            ->get();
        if(count($customtimesched) > 0)
        {
            if(strtolower(date('A', strtotime($customtimesched[0]->pmin))) == 'pm')
            {
                $customtimesched[0]->pmin = date('h:i:s', strtotime($customtimesched[0]->pmin));
            }
            if(strtolower(date('A', strtotime($customtimesched[0]->pmout))) == 'pm')
            {
                $customtimesched[0]->pmout = date('h:i:s', strtotime($customtimesched[0]->pmout));
            }
        }else{
            $customtimesched = array((object)[
                'amin'  => '08:00:00',
                'amout' => '12:00:00',
                'pmin'  => '01:00:00',
                'pmout' => '05:00:00'
            ]);
        }
        
        // $changedate = explode('-',$request->get('selecteddate'));

        // $date = $changedate[2].'-'.$changedate[0].'-'.$changedate[1];
        $date = $request->get('selecteddate');

        $employeeinfo = DB::table('teacher')
            ->where('id', $request->get('employeeid'))
            ->first();

        $taphistory = DB::table('taphistory')
            ->where('tdate', $date)
            ->where('studid', $request->get('employeeid'))
            ->where('utype', '!=','7')
            ->where('deleted','0')
            ->get();

        if(count($taphistory)>0)
        {
            foreach($taphistory as $tapatt)
            {
                $tapatt->mode = 0;
            }
        }

        $hr_attendance = DB::table('hr_attendance')
            ->where('tdate', $date)
            ->where('studid', $request->get('employeeid'))
			->where('deleted',0)
            ->get();

        if(count($hr_attendance)>0)
        {
            foreach($hr_attendance as $hratt)
            {
                $hratt->mode = 1;
            }
        }


        $checkifexists = collect();
        $checkifexists = $checkifexists->merge($taphistory);
        $checkifexists = $checkifexists->merge($hr_attendance);
        $checkifexists = $checkifexists->sortBy('ttime');
        $checkifexists = $checkifexists->unique('ttime');
        
        if(count($checkifexists)>0)
        {
            foreach($checkifexists as $log)
            {
                $log->ttime = date('h:i:s A',strtotime($log->ttime));
            }
        }

        $remarks = DB::table('hr_attendanceremarks')
            ->where('tdate',$date)
            ->where('employeeid', $request->get('employeeid'))
            ->where('deleted','0')
            ->first();
        // return $taphistory;
        return view('hr.attendance.timelogs')
            ->with('customtimesched', $customtimesched)
            ->with('employeeinfo', $employeeinfo)
            ->with('remarks', $remarks)
            ->with('logs', $checkifexists);
    }
    public function updateremarks(Request $request)
    {
        date_default_timezone_set('Asia/Manila');
        $checkifexists = DB::table('hr_attendanceremarks')
            ->where('tdate',$request->get('selecteddate'))
            ->where('employeeid', $request->get('id'))
            ->where('deleted','0')
            ->first();
        if($checkifexists)
        {
            DB::table('hr_attendanceremarks')
                ->where('id', $checkifexists->id)
                ->update([
                    'remarks'           => $request->get('remarks'),
                    'updatedby'         => auth()->user()->id,
                    'updateddatetime'   => date('Y-m-d H:i:s')
                ]);

            return 1;
        }else{
            DB::table('hr_attendanceremarks')
                ->insert([
                    'employeeid'        => $request->get('id'),
                    'tdate'             => $request->get('selecteddate'),
                    'remarks'           => $request->get('remarks'),
                    'createdby'         => auth()->user()->id,
                    'createddatetime'   => date('Y-m-d H:i:s')
                ]);
            return 1;
        }
    }
    public function addtimelog(Request $request)
    {
        date_default_timezone_set('Asia/Manila');

        $date = $request->get('selecteddate');

        $taphistory = DB::table('taphistory')
            ->where('tdate',$date)
            ->where('ttime',$request->get('timelog'))
            ->where('studid',$request->get('employeeid'))
			->where('tapstate',strtoupper($request->get('tapstate')))
			->where('deleted',0)
            ->where('utype', '!=','7')
            ->get();

        $hr_attendance = DB::table('hr_attendance')
            ->where('tdate',$date)
            ->where('ttime',$request->get('timelog'))
            ->where('studid',$request->get('employeeid'))
			->where('tapstate',strtoupper($request->get('tapstate')))
			->where('deleted',0)
            ->get();


        $checkifexists = collect();
        $checkifexists = $checkifexists->merge($taphistory);
        $checkifexists = $checkifexists->merge($hr_attendance);
        $checkifexists = $checkifexists->sortBy('ttime');
        $checkifexists = $checkifexists->unique('ttime');

        if(count($checkifexists) == 0)
        {
            DB::table('hr_attendance')
                ->insert([
                    'tdate'                 => $date,
                    'ttime'                 =>  $request->get('timelog'),
                    'tapstate'              => strtoupper($request->get('tapstate')),
                    'timeshift'             => strtoupper(date('A',strtotime($request->get('timelog')))),
                    'studid'                => $request->get('employeeid'),
                    'utype'                 => $request->get('usertypeid'),
                    // 'mode'                  => 1,
                    'createdby'             => auth()->user()->id,
                    'createddatetime'       => date('Y-m-d H:i:s')
                ]);

            return '1';
        }else{
            return '0';
        }
    }
    public function deletetimelog(Request $request)
    {
        date_default_timezone_set('Asia/Manila');
        DB::table('hr_attendance')
            ->where('id', $request->get('id'))
            ->update([
                'deleted'   => '1',
                'deletedby' => auth()->user()->id,
                'deleteddatetime'   => date('Y-m-d H:i:s')
            ]);
    }
    public function summaryindex(Request $request)
    {
        $employees = DB::table('teacher')
            ->select(
                'teacher.id',
                'teacher.firstname',
                'teacher.middlename',
                'teacher.lastname',
                'teacher.suffix',
                'teacher.picurl',
                'employee_personalinfo.gender',
                'usertype.id as usertypeid',
                'usertype.utype'
                )
            ->join('usertype','teacher.usertypeid','=','usertype.id')
            ->leftJoin('employee_personalinfo','teacher.id','=','employee_personalinfo.employeeid')
            ->where('teacher.deleted','0')
            ->where('teacher.isactive','1')
            ->where('teacher.isactive','1')
            // ->take(20)
            ->orderBy('lastname','asc')
            ->get();

        if(Session::get('currentPortal') == 10)
        {
            $extends = 'hr.layouts.app';
        }else{
            $extends = 'principalsportal.layouts.app2';
        }
        $departments = Db::table('hr_departments')
            ->where('deleted','0')
            ->get();
        return view('hr.attendance.summaryindex')
            ->with('departments', $departments)
            ->with('employees', $employees)
            ->with('extends', $extends);
    }
    public function summarygenerate(Request $request)
    {
        if(is_string($request->get('id')))
        {
            $request->merge([
                'id'    => json_decode($request->get('id'))
            ]);
        }
        try{
            if(count($request->get('id')) == 0 )
            {                
                $request->request->remove('id');
            }
        }catch(\Exception $error)
        {
            if($request->get('id') == null)
            {
                $request->request->remove('id');
            }
        }
        
        // return $request->all();
        $dates = explode(' - ',$request->get('dates'));
        $datefrom   = $dates[0];
        $dateto   = $dates[1];

        $alldays        = array();

        $beginmonth             = new DateTime($datefrom);
    
        $endmonth               = new DateTime($dateto);

        $endmonth               = $endmonth->modify( '+1 day' ); 
        
        $intervalmonth          = new DateInterval('P1D');

        $daterangemonth         = new DatePeriod($beginmonth, $intervalmonth ,$endmonth);

        foreach($daterangemonth as $datemonth){

                array_push($alldays,$datemonth->format("Y-m-d"));

        }
        
        if(!$request->has('id') || count($request->get('id')) > 1)
        {
            if($request->has('id'))
            {
                $employees = DB::table('teacher')
                    ->select(
                        'teacher.id',
                        'teacher.firstname',
                        'teacher.middlename',
                        'teacher.lastname',
                        'teacher.suffix',
                        'teacher.picurl',
                        'employee_personalinfo.gender',
                        'employee_personalinfo.departmentid',
                        'teacher.schooldeptid',
                        'usertype.id as usertypeid',
                        'usertype.utype'
                        )
                    ->leftJoin('usertype','teacher.usertypeid','=','usertype.id')
                    ->leftJoin('employee_personalinfo','teacher.id','=','employee_personalinfo.employeeid')
                    ->where('teacher.deleted','0')
                    ->where('teacher.isactive','1')
                    ->whereIn('teacher.id',$request->get('id'))
                    // ->take(20)
                    ->orderBy('lastname','asc')
                    // ->take(5)
                    ->get();
                    
            }else{
                $employees = DB::table('teacher')
                    ->select(
                        'teacher.id',
                        'teacher.firstname',
                        'teacher.middlename',
                        'teacher.lastname',
                        'teacher.suffix',
                        'teacher.picurl',
                        'employee_personalinfo.gender',
                        'employee_personalinfo.departmentid',
                        'teacher.schooldeptid',
                        'usertype.id as usertypeid',
                        'usertype.utype'
                        )
                    ->leftJoin('usertype','teacher.usertypeid','=','usertype.id')
                    ->leftJoin('employee_personalinfo','teacher.id','=','employee_personalinfo.employeeid')
                    ->where('teacher.deleted','0')
                    ->where('teacher.isactive','1')
                    // ->take(20)
                    ->orderBy('lastname','asc')
                    // ->take(5)
                    ->get();
            }

            if(count($employees)>0)
            {
                foreach($employees as $employee)
                {
                    if($employee->departmentid == null)
                    {
                        $employee->departmentid = $employee->schooldeptid;
                    }
                }
            }

            if($request->get('departmentid')>0)
            {
                $employees = collect($employees)->where('departmentid',$request->get('departmentid'))->all();
            }
            
            if(count($employees)>0)
            {
                foreach($employees as $employee)
                {
                    // $employee->id = 21;
                        
                    $summarylogs = array();

                    $customamtimein = DB::table('employee_customtimesched')
                        ->where('employeeid', $employee->id)
                        ->where('deleted', 0)
                        ->first();

                    if(!$customamtimein)
                    {
                        $customamtimein = (object)array(
                            'amin'      => '08:00:00',
                            'amout'      => '12:00:00',
                            'pmin'      => '13:00:00',
                            'pmout'      => '17:00:00',
                        );
                    }

                    foreach($alldays as $day)
                    {
                        $taphistory = DB::table('taphistory')
                            ->where('studid', $employee->id)
                            ->where('deleted','0')
                            ->where('tdate', $day)
                            ->where('utype', '!=', 7)
                            ->orderBy('ttime','asc')
                            ->get();

                        if(count($taphistory)>0)
                        {
                            foreach($taphistory as $tapatt)
                            {
                                $tapatt->mode = 0;
                            }
                        }

                        $hr_attendance = DB::table('hr_attendance')
                            ->where('studid', $employee->id)
                            ->where('tdate', $day)
                            ->where('deleted',0)
                            ->orderBy('ttime','asc')
                            ->get();

                        if(count($hr_attendance)>0)
                        {
                            foreach($hr_attendance as $hratt)
                            {
                                $hratt->mode = 1;
                            }
                        }


                        $logs = collect();
                        $logs = $logs->merge($taphistory);
                        $logs = $logs->merge($hr_attendance);
                        $logs = $logs->sortBy('ttime');
                        $logs = $logs->unique('ttime');
                        
                        $hours = 0;
                        $minutes = 0; 
                        $latehours = 0;
                        $lateminutes = 0;
                        if(count($logs)>0)
                        {
                            
                            if(collect($logs)->where('ttime','<','12:00:00')->count() > 0)
                            {
                                if(collect($logs)->where('tapstate','IN')->where('ttime','<','12:00:00')->first())
                                {
                                    $amttimeamin = collect($logs)->where('tapstate','IN')->where('ttime','<','12:00:00')->first()->ttime;
                                    $amtimein = $day.' '.$amttimeamin;
                                }else{
                                    
                                    if($customamtimein)
                                    {
                                        $amttimeamin = $customamtimein->amin;
                                        $amtimein = $day.' '.$customamtimein->amin;
                                    }else{
                                        $amttimeamin = '08:00:00';
                                        $amtimein = $day.' 08:00:00';
                                    }
                                }

                                $am_customtimein = new DateTime($amtimein);
                                $am_latetimein = new DateTime($day.' '.$customamtimein->amin);
                                $amlateinterval = $am_customtimein->diff($am_latetimein);
                                $lateh = $amlateinterval->format('%h');
                                $latem = $amlateinterval->format('%i');
                                
                                if($amttimeamin>$customamtimein->amin)
                                {
                                    $latehours+=$lateh;
                                    $lateminutes+=$latem;
                                }

                                if(collect($logs)->where('tapstate','OUT')->where('ttime','<','12:00:00')->last())
                                {
                                    $amtimeout = $day.' '.collect($logs)->where('tapstate','OUT')->where('ttime','<','12:00:00')->last()->ttime;
                                }else{
                                    
                                    $customamtimeout = DB::table('employee_customtimesched')
                                        ->where('employeeid', $employee->id)
                                        ->where('deleted', 0)
                                        ->first();
                
                                    if($customamtimeout)
                                    {
                                        $amtimeout = $day.' '.$customamtimeout->amout;
                                    }else{
                                        $amtimeout = $day.' 12:00:00';
                                    }
                                }
                                
                                $am_timein = new DateTime($amtimein);
                                $am_timeout = new DateTime($amtimeout);
                                $aminterval = $am_timein->diff($am_timeout);
                                 
                                $hours += $aminterval->format('%h');
                                $minutes += $aminterval->format('%i');
                            }
                            
                            /////
                            // return collect($logs)->where('ttime','>=','12:00:00')->count();
                            if(collect($logs)->where('ttime','>=','12:00:00')->count() > 0)
                            {
                                if(collect($logs)->where('tapstate','IN')->where('ttime','>=','12:00:00')->first())
                                {
                                    $pmtimein = $day.' '.collect($logs)->where('tapstate','IN')->where('ttime','>=','12:00:00')->first()->ttime;
                                }else{
                                    
                                    $custompmtimein = DB::table('employee_customtimesched')
                                        ->where('employeeid', $employee->id)
                                        ->where('deleted', 0)
                                        ->first();
                
                                    if($custompmtimein)
                                    {
                                        $pmtimein = $day.' '.$custompmtimein->pmin;
                                    }else{
                                        $pmtimein = $day.' 13:00:00';
                                    }
                                }
                                if(collect($logs)->where('tapstate','OUT')->where('ttime','>=','12:00:00')->last())
                                {
                                    $pmtimeout = $day.' '.collect($logs)->where('tapstate','OUT')->where('ttime','>=','12:00:00')->last()->ttime;
                                }else{
                                    
                                    $custompmtimeout = DB::table('employee_customtimesched')
                                        ->where('employeeid', $employee->id)
                                        ->where('deleted', 0)
                                        ->first();
                
                                    if($custompmtimeout)
                                    {
                                        $pmtimeout = $day.' '.$custompmtimeout->pmout;
                                    }else{
                                        $pmtimeout = $day.' 17:00:00';
                                    }
                                }
                                
                                $pm_timein = new DateTime($pmtimein);
                                $pm_timeout = new DateTime($pmtimeout);
                                $pminterval = $pm_timein->diff($pm_timeout);
                                 
                                $hours += $pminterval->format('%h');
                                $minutes += $pminterval->format('%i');
                            }
            
                        }
            
                        while($minutes>=60)
                        {
                            $hours+=1;
                            $minutes-=60;
                        }
                        while($lateminutes>=60)
                        {
                            $latehours+=1;
                            $lateminutes-=60;
                        }
                        $employee->amin = $customamtimein->amin;
                        $employee->amout = $customamtimein->amout;
                        $employee->pmin = $customamtimein->pmin;
                        $employee->pmout = $customamtimein->pmout;
                        
                        $remarks = DB::table('hr_attendanceremarks')
                            ->where('tdate',$day)
                            ->where('employeeid',  $employee->id)
                            ->where('deleted','0')
                            ->first();
                        array_push($summarylogs, (object)array(
                            'remarks'      => $remarks,
                            'date'      => $day,
                            'logs'      => $logs,
                            'latehours'     => $latehours,
                            'lateminutes'     => $lateminutes,
                            'hours'     => $hours,
                            'minutes'   => $minutes,
                        ));
                    }

                    $employee->logs = $summarylogs;
                    // return collect($employee);
                }
            }

        }elseif(count($request->get('id')) == 1){

            $customamtimein = DB::table('employee_customtimesched')
                ->where('employeeid', $request->get('id')[0])
                ->where('deleted', 0)
                ->first();

            if(!$customamtimein)
            {
                $customamtimein = (object)array(
                    'amin'      => '08:00:00',
                    'amout'      => '12:00:00',
                    'pmin'      => '13:00:00',
                    'pmout'      => '17:00:00',
                );
            }
            $summarylogs = array();
    
            foreach($alldays as $day)
            {
    
            
                $taphistory = DB::table('taphistory')
                    ->where('studid', $request->get('id'))
                    ->where('deleted','0')
                    ->where('tdate', $day)
                    ->where('utype', '!=', 7)
                    ->orderBy('ttime','asc')
                    ->get();

                if(count($taphistory)>0)
                {
                    foreach($taphistory as $tapatt)
                    {
                        $tapatt->mode = 0;
                    }
                }

                $hr_attendance = DB::table('hr_attendance')
                    ->where('studid', $request->get('id'))
                    ->where('tdate', $day)
                    ->where('deleted',0)
                    ->orderBy('ttime','asc')
                    ->get();

                if(count($hr_attendance)>0)
                {
                    foreach($hr_attendance as $hratt)
                    {
                        $hratt->mode = 1;
                    }
                }


                $logs = collect();
                $logs = $logs->merge($taphistory);
                $logs = $logs->merge($hr_attendance);
                $logs = $logs->sortBy('ttime');
                $logs = $logs->unique('ttime');

                // if($day == '2021-06-19')
                // {
                //     return $logs;
                // }
                // $logs = DB::table('taphistory')
                //     ->where('studid', $request->get('id'))
                //     ->where('utype', '!=', 7)
                //     ->where('deleted','0')
                //     ->where('tdate', $day)
                //     ->get();
    
                $hours = 0;
                $minutes = 0; 
                $latehours = 0;
                $lateminutes = 0;
    
                if(count($logs)>0)
                {
                    
                    if(collect($logs)->where('ttime','<','12:00:00')->count() > 0)
                    {
                        if(collect($logs)->where('tapstate','IN')->where('ttime','<','12:00:00')->first())
                        {
                            $amttimeamin = collect($logs)->where('tapstate','IN')->where('ttime','<','12:00:00')->first()->ttime;
                            $amtimein = $day.' '.$amttimeamin;
                        }else{
                            
                            if($customamtimein)
                            {
                                $amttimeamin = $customamtimein->amin;
                                $amtimein = $day.' '.$customamtimein->amin;
                            }else{
                                $amttimeamin = '08:00:00';
                                $amtimein = $day.' 08:00:00';
                            }
                        }

                        $am_customtimein = new DateTime($amtimein);
                        $am_latetimein = new DateTime($day.' '.$customamtimein->amin);
                        $amlateinterval = $am_customtimein->diff($am_latetimein);
                        $lateh = $amlateinterval->format('%h');
                        $latem = $amlateinterval->format('%i');
                        
                        if($amttimeamin>$customamtimein->amin)
                        {
                            $latehours+=$lateh;
                            $lateminutes+=$latem;
                        }
                        // if(collect($logs)->where('tapstate','IN')->where('ttime','<','12:00:00')->first())
                        // {
                        //     $amtimein = $day.' '.collect($logs)->where('tapstate','IN')->where('ttime','<','12:00:00')->first()->ttime;
                        // }else{
                            
        
                        //     if($customamtimein)
                        //     {
                        //         $amtimein = $day.' '.$customamtimein->amin;
                        //     }else{
                        //         $amtimein = $day.' 08:00:00';
                        //     }
                        // }
                        if(collect($logs)->where('tapstate','OUT')->where('ttime','<','12:00:00')->last())
                        {
                            $amtimeout = $day.' '.collect($logs)->where('tapstate','OUT')->where('ttime','<','12:00:00')->last()->ttime;
                        }else{
                            
                            $customamtimeout = DB::table('employee_customtimesched')
                                ->where('employeeid', $request->get('id'))
                                ->where('deleted', 0)
                                ->first();
        
                            if($customamtimeout)
                            {
                                $amtimeout = $day.' '.$customamtimeout->amout;
                            }else{
                                $amtimeout = $day.' 12:00:00';
                            }
                        }
                        $am_timein = new DateTime($amtimein);
                        $am_timeout = new DateTime($amtimeout);
                        $aminterval = $am_timein->diff($am_timeout);
                         
                        $hours = $aminterval->format('%h');
                        $minutes = $aminterval->format('%i');
                    }
                    
                    /////
                    // return collect($logs)->where('ttime','>=','12:00:00')->count();
                    if(collect($logs)->where('ttime','>=','12:00:00')->count() > 0)
                    {
                        if(collect($logs)->where('tapstate','IN')->where('ttime','>=','12:00:00')->first())
                        {
                            $pmtimein = $day.' '.collect($logs)->where('tapstate','IN')->where('ttime','>=','12:00:00')->first()->ttime;
                        }else{
                            
                            $custompmtimein = DB::table('employee_customtimesched')
                                ->where('employeeid', $request->get('id'))
                                ->where('deleted', 0)
                                ->first();
        
                            if($custompmtimein)
                            {
                                $pmtimein = $day.' '.$custompmtimein->pmin;
                            }else{
                                $pmtimein = $day.' 13:00:00';
                            }
                        }
                        if(collect($logs)->where('tapstate','OUT')->where('ttime','>=','12:00:00')->last())
                        {
                            $pmtimeout = $day.' '.collect($logs)->where('tapstate','OUT')->where('ttime','>=','12:00:00')->last()->ttime;
                        }else{
                            
                            $custompmtimeout = DB::table('employee_customtimesched')
                                ->where('employeeid', $request->get('id'))
                                ->where('deleted', 0)
                                ->first();
        
                            if($custompmtimeout)
                            {
                                $pmtimeout = $day.' '.$custompmtimeout->pmout;
                            }else{
                                $pmtimeout = $day.' 17:00:00';
                            }
                        }
                        $pm_timein = new DateTime($pmtimein);
                        $pm_timeout = new DateTime($pmtimeout);
                        $pminterval = $pm_timein->diff($pm_timeout);
                         
                        $hours += $pminterval->format('%h');
                        $minutes += $pminterval->format('%i');
                    }
    
                }
    
                while($minutes>=60)
                {
                    $hours+=1;
                    $minutes-=60;
                }

                $remarks = DB::table('hr_attendanceremarks')
                ->where('tdate',$day)
                ->where('employeeid',  $request->get('id'))
                ->where('deleted','0')
                ->first();

                array_push($summarylogs, (object)array(
                    'remarks'      => $remarks,
                    'date'      => $day,
                    'logs'      => $logs,
                    'latehours'     => $latehours,
                    'lateminutes'     => $lateminutes,
                    'hours'     => $hours,
                    'minutes'   => $minutes
                ));
            }
        }
        
        // return $summarylogs;

        if(!$request->has('exporttype'))
        {
            if(!$request->has('id') || count($request->get('id')) > 1)
            {
                return view('hr.attendance.summarylogs')
                    ->with('dates', $alldays)
                    ->with('employees', $employees);
            }elseif( count($request->get('id')) == 1){
                return view('hr.attendance.summaryemplogs')
                    ->with('logs', $summarylogs);
            }
        }else{
            
            $info = DB::table('teacher')
                    ->select('teacher.*','usertype.utype')
                    ->where('teacher.id', $request->get('id'))
                    ->join('usertype','teacher.usertypeid','=','usertype.id')
                    ->first();

            if(!$request->has('id') || count($request->get('id')) > 1)
            {
                $alldays = collect($alldays)->toArray();
                $alldays = array_chunk($alldays, 5);
                
                // if(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'ck')
                // {
                    // return $employees;
                    if(strtolower(DB::table('schoolinfo')->first()->abbreviation) != 'hchs')
                    {
                        $employees = collect($employees)->toArray();
                        $employees = array_chunk($employees, 3);
                    }

                    //     $pdf = PDF::loadview('hr/pdf/summaryattendance',compact('alldays','datefrom','dateto','employees'))->setPaper('portrait');
                    // }else{
                        $pdf = PDF::loadview('hr/pdf/summaryattendance',compact('alldays','datefrom','dateto','employees'))->setPaper('portrait');
                    // }
                // }else{
                //     $pdf = PDF::loadview('hr/pdf/summaryattendance',compact('alldays','datefrom','dateto','employees'))->setPaper('landscape');
                // }
    
                return $pdf->stream('Summary of Attendance');
            }elseif( count($request->get('id')) == 1){
                // return $summarylogs[0]->remarks->remarks;
                $pdf = PDF::loadview('hr/pdf/summaryattendanceemp',compact('summarylogs','datefrom','dateto','info'))->setPaper('portrait');
    
                return $pdf->stream('Summary of Attendance');
            }
        }
    }
    public function absencesindex(Request $request)
    {
        $offenses = DB::table('hr_offenses')
            ->where('deleted','0')
            ->where('type','0')
            ->get();

        if($request->get('action') == 'getoffenses')
        {
            return view('hr.attendance.absences.resultsoffenses')
                ->with('offenses', $offenses);
        }else{
            return view('hr.attendance.absences.index')
                ->with('offenses', $offenses);
        }
    }
    public function absencesoffense(Request $request)
    {
        $checkifexists = DB::table('hr_offenses')
            ->where('title','like','%'.$request->get('title').'%')
            ->where('deleted','0')
            ->where('type','0')
            ->first();

        if($request->get('action') == 'addoffense')
        {
            if($checkifexists)
            {
                return 0;
            }else{
                DB::table('hr_offenses')
                    ->insert([
                        'title'                 => $request->get('title'),
                        'description'           => $request->get('description'),
                        'createdby'             => auth()->user()->id,
                        'createddatetime'       => date('Y-m-d H:i:s')
                    ]);
    
                return 1;
            }
        }
        elseif($request->get('action') == 'editoffense')
        {
            try{
                // return $request->get('offenseid');
                DB::table('hr_offenses')
                    ->where('id', $request->get('offenseid'))
                    ->update([
                        'title'           => $request->get('title'),
                        'description'     => $request->get('description'),
                        'updatedby'       => auth()->user()->id,
                        'updateddatetime' => date('Y-m-d H:i:s')
                    ]);
                return 1;
            }catch(\Exception $error)
            {
                return 0;
            }
        }
        elseif($request->get('action') == 'deleteoffense')
        {
            try{
                // return $request->get('offenseid');
                DB::table('hr_offenses')
                    ->where('id', $request->get('offenseid'))
                    ->update([
                        'deleted'         => 1,
                        'deletedby'       => auth()->user()->id,
                        'deleteddatetime' => date('Y-m-d H:i:s')
                    ]);
                return 1;
            }catch(\Exception $error)
            {
                return 0;
            }
        }
    }
    public function absencesgenerate(Request $request)
    {
        // return date("Y-m-d", strtotime($request->get('week')));
        // return $request->all();
        $employees = DB::table('teacher')
            ->select('title','id','firstname','middlename','lastname','suffix')
            ->where('deleted','0')
            ->orderBy('lastname','asc')
            ->get();

        if($request->has('employeeid'))
        {
            $employees = collect($employees)->where('id', $request->get('employeeid'))->values();
        }

        $days = array();
        $monthstart = date('Y-m-d', strtotime($request->get('week')));
        $monthend = date('Y-m-t', strtotime($request->get('week')));
        
        array_push($days,$monthstart);
        $initdate = $monthstart;
        for($x=0; $x<6; $x++)
        {
            array_push($days,date('Y-m-d', strtotime($initdate . ' +1 day')));
            $initdate = date('Y-m-d', strtotime($initdate . ' +1 day'));
        }
        // return $days;
        if(count($employees)>0)
        {
            foreach($employees as $employee)
            {
                $employee->firstname = ucwords(strtolower($employee->firstname));
                $employee->middlename = ucwords(strtolower($employee->middlename));
                $attrecords = collect();

                $atttap = DB::table('taphistory')
                    ->select('tdate')
                    ->where('studid', $employee->id)
                    ->where('deleted', 0)
                    ->whereIn('tdate', $days)
                    ->get();

                $atthr = DB::table('hr_attendance')
                    ->select('tdate')
                    ->where('studid', $employee->id)
                    ->where('deleted', 0)
                    ->whereIn('tdate', $days)
                    ->get();

                $attrecords = $attrecords->merge($atttap);
                $attrecords = $attrecords->merge($atthr);
                    
                $attremarks = DB::table('hr_attendanceremarks')
                    ->select('remarks','tdate')
                    ->where('employeeid', $employee->id)
                    ->where('deleted', 0)
                    ->whereIn('tdate', $days)
                    ->get();
                
                $daysabsent = array();

                foreach($days as $day)
                {
                    if(strtolower(date('l', strtotime($day))) != 'sunday' && strtolower(date('l', strtotime($day))) != 'saturday')
                    {
                        if(collect($attrecords)->where('tdate', $day)->count() == 0)
                        {
                            if(collect($attremarks)->where('tdate', $day)->count() == 0)
                            {
                                $remarks = "";
                            }else{
                                $remarks = collect($attremarks)->where('tdate', $day)->first()->remarks;
                            }
                            array_push($daysabsent, (object)array(
                                'date'  => $day,
                                'remarks' => $remarks
                            ));
                        }
                    }
                }
                $employee->daysabsent = $daysabsent;
                $employee->offenses   = DB::table('hr_offenseslist')
                    ->select('hr_offenseslist.*')
                    ->join('hr_offenses','hr_offenseslist.offenseid','=','hr_offenses.id')
                    ->where('employeeid',$employee->id)
                    ->where('weekid',$request->get('week'))
                    ->where('hr_offenseslist.deleted','0')
                    ->where('hr_offenses.deleted','0')
                    ->where('hr_offenses.type','0')
                    ->get();
            }
        }
        $offenses = DB::table('hr_offenses')
            ->where('deleted','0')
            ->where('type','0')
            ->get();
            
        if(!$request->has('export'))
        {
            return view('hr.attendance.absences.resultsemployees')
                ->with('employees', $employees)
                ->with('offenses', $offenses);
        }else{
            $weekid = $request->get('week');
            // pdf_tardiness.blade.php
            if($request->get('exportclass') == 1)
            {
                $employee = collect($employees)->where('id', $request->get('employeeid'))->first();
                $pdf = PDF::loadview('hr/attendance/absences/pdf_employeeabsences',compact('offenses','employee','weekid','days'))->setPaper('portrait');
    
                return $pdf->stream($weekid.' Absences - '.$employee->lastname.'_'.$employee->firstname.'.pdf');
            }else{
                $pdf = PDF::loadview('hr/attendance/absences/pdf_absences',compact('offenses','employees','weekid','days'))->setPaper('portrait');
    
                return $pdf->stream($weekid.' Absences.pdf');
            }
        }
    }
    public function absencesmarkoffense(Request $request)
    {
        $checkifexists = DB::table('hr_offenseslist')
            ->where('employeeid', $request->get('empid'))
            ->where('weekid', $request->get('weekid'))
            ->where('offenseid', $request->get('offenseid'))
            ->first();

        if($checkifexists)
        {
            if($request->get('status') == 0 && $checkifexists->deleted == 0)
            {
                DB::table('hr_offenseslist')
                    ->where('id', $checkifexists->id)
                    ->update([
                        'deleted'         => 1,
                        'deletedby'       => auth()->user()->id,
                        'deleteddatetime' => date('Y-m-d H:i:s')
                    ]);
            }
            if($request->get('status') == 1 && $checkifexists->deleted == 1)
            {
                DB::table('hr_offenseslist')
                    ->where('id', $checkifexists->id)
                    ->update([
                        'deleted'         => 0,
                        'updatedby'       => auth()->user()->id,
                        'updateddatetime' => date('Y-m-d H:i:s')
                    ]);
            }
        }else{
            if($request->get('status') == 1)
            {
                DB::table('hr_offenseslist')
                    ->insert([
                        'employeeid'          => $request->get('empid'),
                        'weekid'              => $request->get('weekid'),
                        'offenseid'           => $request->get('offenseid'),
                        'createdby'           => auth()->user()->id,
                        'createddatetime'     => date('Y-m-d H:i:s')
                    ]);
            }
        }
        
    }
    public function absencesexport(Request $request)
    {
        // return $request->all();
        $employeeinfo = DB::table('teacher')
            ->select(
                'teacher.id',
                'teacher.firstname',
                'teacher.middlename',
                'teacher.lastname',
                'teacher.suffix',
                'teacher.picurl',
                'employee_personalinfo.gender',
                'usertype.id as usertypeid',
                'usertype.utype'
                )
            ->join('usertype','teacher.usertypeid','=','usertype.id')
            ->leftJoin('employee_personalinfo','teacher.id','=','employee_personalinfo.employeeid')
            ->where('teacher.deleted','0')
            ->where('teacher.isactive','1')
            ->where('teacher.id',$request->get('employeeid'))
            ->first();

        return collect($employeeinfo);
            
    }
    public function tardinessindex(Request $request)
    {
        $offenses = DB::table('hr_offenses')
            ->where('deleted','0')
            ->where('type','1')
            ->get();

        if($request->get('action') == 'getoffenses')
        {
            return view('hr.attendance.tardiness.resultsoffenses')
                ->with('offenses', $offenses);
        }else{
            return view('hr.attendance.tardiness.index')
                ->with('offenses', $offenses);
        }

    }
    public function tardinessoffense(Request $request)
    {
        $checkifexists = DB::table('hr_offenses')
            ->where('title','like','%'.$request->get('title').'%')
            ->where('deleted','0')
            ->where('type','1')
            ->first();

        if($request->get('action') == 'addoffense')
        {
            if($checkifexists)
            {
                return 0;
            }else{
                DB::table('hr_offenses')
                    ->insert([
                        'title'                 => $request->get('title'),
                        'description'           => $request->get('description'),
                        'type'                  => 1,
                        'createdby'             => auth()->user()->id,
                        'createddatetime'       => date('Y-m-d H:i:s')
                    ]);
    
                return 1;
            }
        }
        elseif($request->get('action') == 'editoffense')
        {
            try{
                // return $request->get('offenseid');
                DB::table('hr_offenses')
                    ->where('id', $request->get('offenseid'))
                    ->update([
                        'title'           => $request->get('title'),
                        'description'     => $request->get('description'),
                        'updatedby'       => auth()->user()->id,
                        'updateddatetime' => date('Y-m-d H:i:s')
                    ]);
                return 1;
            }catch(\Exception $error)
            {
                return 0;
            }
        }
        elseif($request->get('action') == 'deleteoffense')
        {
            try{
                // return $request->get('offenseid');
                DB::table('hr_offenses')
                    ->where('id', $request->get('offenseid'))
                    ->update([
                        'deleted'         => 1,
                        'deletedby'       => auth()->user()->id,
                        'deleteddatetime' => date('Y-m-d H:i:s')
                    ]);
                return 1;
            }catch(\Exception $error)
            {
                return 0;
            }
        }
    }
    public function tardinessgenerate(Request $request)
    {
        // return date("Y-m-d", strtotime($request->get('week')));
        // return $request->all();
        $employees = DB::table('teacher')
            ->select('title','id','firstname','middlename','lastname','suffix')
            ->where('deleted','0')
            ->orderBy('lastname','asc')
            ->get();

        if($request->has('employeeid'))
        {
            $employees = collect($employees)->where('id', $request->get('employeeid'))->values();
        }
        $days = array();
        $monthstart = date('Y-m-d', strtotime($request->get('week')));
        $monthend = date('Y-m-t', strtotime($request->get('week')));
        
        array_push($days,$monthstart);
        $initdate = $monthstart;
        for($x=0; $x<6; $x++)
        {
            array_push($days,date('Y-m-d', strtotime($initdate . ' +1 day')));
            $initdate = date('Y-m-d', strtotime($initdate . ' +1 day'));
        }
        // return $days;
        if(count($employees)>0)
        {
            foreach($employees as $employee)
            {
                $employee->firstname = ucwords(strtolower($employee->firstname));
                $employee->middlename = ucwords(strtolower($employee->middlename));

                // try{
                $dates = \App\Models\HR\HREmployeeAttendance::gethours($days, $employee->id);
                    // $employee->records = $days;
                    // return $days;
                // }catch(\Exception $error)
                // {
                //     // return $days;
                //     $dates = \App\Models\HR\HREmployeeAttendance::gethours($days, $employee->id);
                //     // return $dates;
                // }
                $employee->records = collect($dates)->filter(function ($value, $key) {
                    return $value->latehours > 0  ||  $value->undertimehours > 0;
                })->values();
                // if($employee->id == 21)
                // {
                //     return $employee->records;
                // }
                $employee->offenses   = DB::table('hr_offenseslist')
                    ->select('hr_offenseslist.*')
                    ->join('hr_offenses','hr_offenseslist.offenseid','=','hr_offenses.id')
                    ->where('employeeid',$employee->id)
                    ->where('weekid',$request->get('week'))
                    ->where('hr_offenseslist.deleted','0')
                    ->where('hr_offenses.deleted','0')
                    ->where('hr_offenses.type','1')
                    ->get();
                
            }
        }
        // // $employees = collect($employees)->where('latehours','>',0)->orWhere('undertimehours','>',0)->values();
        // $employees = collect($employees)->filter(function ($value, $key) {
        //     return collect($value->records)->sum('latehours') > 0  ||  collect($value->records)->sum('undertimehours') > 0;
        // })->values();
        $offenses = DB::table('hr_offenses')
            ->where('deleted','0')
            ->where('type','1')
            ->get();

        if(!$request->has('export'))
        {
            return view('hr.attendance.tardiness.resultsemployees')
                ->with('employees', $employees)
                ->with('offenses', $offenses);
        }else{
            $weekid = $request->get('week');
            // pdf_tardiness.blade.php
            if($request->get('exportclass') == 1)
            {
                $employee = collect($employees)->where('id', $request->get('employeeid'))->first();
                $pdf = PDF::loadview('hr/attendance/tardiness/pdf_employeetardiness',compact('offenses','employee','weekid','days'))->setPaper('portrait');
    
                return $pdf->stream($weekid.' Tardiness - '.$employee->lastname.'_'.$employee->firstname.'.pdf');
            }else{
                $pdf = PDF::loadview('hr/attendance/tardiness/pdf_tardiness',compact('offenses','employees','weekid','days'))->setPaper('portrait');
    
                return $pdf->stream($weekid.' Tardiness.pdf');
            }
        }
    }
    public function tardinessmarkoffense(Request $request)
    {
        $checkifexists = DB::table('hr_offenseslist')
            ->select('hr_offenseslist.*')
            ->join('hr_offenses','hr_offenseslist.offenseid','=','hr_offenses.id')
            ->where('employeeid', $request->get('empid'))
            ->where('weekid', $request->get('weekid'))
            ->where('offenseid', $request->get('offenseid'))
            ->where('hr_offenses.type','1')
            ->where('hr_offenses.deleted','0')
            ->first();

        if($checkifexists)
        {
            if($request->get('status') == 0 && $checkifexists->deleted == 0)
            {
                DB::table('hr_offenseslist')
                    ->where('id', $checkifexists->id)
                    ->update([
                        'deleted'         => 1,
                        'deletedby'       => auth()->user()->id,
                        'deleteddatetime' => date('Y-m-d H:i:s')
                    ]);
            }
            if($request->get('status') == 1 && $checkifexists->deleted == 1)
            {
                DB::table('hr_offenseslist')
                    ->where('id', $checkifexists->id)
                    ->update([
                        'deleted'         => 0,
                        'updatedby'       => auth()->user()->id,
                        'updateddatetime' => date('Y-m-d H:i:s')
                    ]);
            }
        }else{
            if($request->get('status') == 1)
            {
                DB::table('hr_offenseslist')
                    ->insert([
                        'employeeid'          => $request->get('empid'),
                        'weekid'              => $request->get('weekid'),
                        'offenseid'           => $request->get('offenseid'),
                        'createdby'           => auth()->user()->id,
                        'createddatetime'     => date('Y-m-d H:i:s')
                    ]);
            }
        }
        
    }
}
