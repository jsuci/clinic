<?php

namespace App\Http\Controllers\ClinicControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use DB;
class ClinicPatientController extends Controller
{
    public function index()
    {
        if(Session::get('currentPortal') == '1')
        {
            $extends = 'teacher.layouts.app';
        }
        elseif(Session::get('currentPortal') == 2)
        {
            $extends = 'principalsportal.layouts.app';
        }
        elseif(Session::get('currentPortal') == 3)
        {
            $extends = 'registrar.layouts.app';
        }
        elseif(Session::get('currentPortal') == 4)
        {
            $extends = 'finance.layouts.app';
        }
        elseif(Session::get('currentPortal') == 6)
        {
            $extends = 'admin.layouts.app';
        }
        elseif(Session::get('currentPortal') == 7)
        {
            $extends = 'studentPortal.layouts.app';
        }
        elseif(Session::get('currentPortal') == 8)
        {
            $extends = 'general.defaultportal.layouts.app';
        }
        elseif(Session::get('currentPortal') == 9)
        {
            $extends = 'parentsportal.layouts.app';
        }
        elseif(Session::get('currentPortal') == 10)
        {
            $extends = 'hr.layouts.app';
        }else{
            $extends = 'general.defaultportal.layouts.app';
        }
        // return $extends;
        $experiences = DB::table('clinic_experiences')
            ->where('deleted','0')
            ->get();

        $appointmentsquery = DB::table('clinic_appointments')
            ->select('id','userid','adate','admitted')
            ->where('deleted','0')
            ->distinct('adate')
            ->get();

        $appointments = array();
        if(count($appointmentsquery)>0)
        {
            foreach($appointmentsquery as $appointment)
            {
                $day = date('d', strtotime($appointment->adate));
                if(date('d', strtotime($appointment->adate))[0] == 0)
                {
                    $day = date('d', strtotime($appointment->adate))[1];
                }
                $month = date('m', strtotime($appointment->adate));
                if(date('m', strtotime($appointment->adate))[0] == 0)
                {
                    $month = date('m', strtotime($appointment->adate))[1];
                }
                if($appointment->admitted == 1)
                {
                    array_push($appointments, (object)array(
                        'id'                => $appointment->id,
                        // 'feelingtoday'      => $appointment->description,
                        // 'experienced'       => '',
                        'year'              => date('Y', strtotime($appointment->adate)),
                        'month'             => $month,
                        'day'               => $day,
                        'cancelled'         => false,
                    ));
                }else{
                    if($appointment->userid == auth()->user()->id)
                    {
                        array_push($appointments, (object)array(
                            'id'                => $appointment->id,
                            // 'feelingtoday'      => $appointment->description,
                            // 'experienced'       => '',
                            'year'              => date('Y', strtotime($appointment->adate)),
                            'month'             => $month,
                            'day'               => $day,
                            'cancelled'         => false,
                        ));
                    }
                }
            }
        }
        
        $myappointments = DB::table('clinic_appointments')
            ->where('userid', auth()->user()->id)
            // ->orderByDesc('createddatetime')
            ->where('deleted','0')
            ->get();

        if(count($myappointments)>0)
        {
            foreach($myappointments as $myappointment)
            {
                if($myappointment->updateddatetime == null)
                {
                    $myappointment->updateddatetime = $myappointment->createddatetime;
                }
            }
        }
        // return $appointments;
        // return collect($myappointments)->sortByDesc('updateddatetime')->values();


        return view('general.clinicportal.index')
            ->with('extends', $extends)
            ->with('experiences', $experiences)
            ->with('appointmentdates', $appointments)
            ->with('myappointments', collect($myappointments)->sortByDesc('updateddatetime')->values());
    }
    public function getappointment(Request $request)
    {
        $apointmentinfo = DB::table('clinic_appointments')
            ->where('id',$request->get('id'))
            ->where('deleted','0')
            ->first();
        return collect($apointmentinfo);
    }
    public function getappointments(Request $request)
    {
        // return $request->all();
        $appointments = DB::table('clinic_appointments')
            ->select('atime','createdby','createddatetime')
            ->whereDate('adate', $request->get('year').'-'.$request->get('month').'-'.$request->get('day'))
            ->where('deleted','0')
            ->get();

        if(count($appointments)>0)
        {
            foreach($appointments as $appointment)
            {
                if($appointment->atime == null)
                {
                    $appointment->atime = '';
                }else{
                    $appointment->atime = date('d M. Y h:i A', strtotime($appointment->atime));
                }
            }
        }
        return collect($appointments);
    }
    public function createapp(Request $request)
    {
        date_default_timezone_set('Asia/Manila');
        // return $request->all();
        $month = date('m', strtotime($request->get('month')));

        $checkifexists = DB::table('clinic_appointments')
            ->where('userid', auth()->user()->id)
            ->where('adate', $request->get('year').'-'.$month.'-'.$request->get('day'))
            ->where('deleted','0')
            ->count();

        if($checkifexists==0)
        {
            $timeslot = null;
            if($request->get('timeslot') != null)
            {
                $timeslot = $request->get('timeslot');
            }

            $appid = DB::table('clinic_appointments')
                ->insertGetId([
                    'userid'            => auth()->user()->id,
                    'description'       => $request->get('feelingtoday'),
                    'adate'             => $request->get('year').'-'.$month.'-'.$request->get('day'),
                    'atime'             => $timeslot,
                    'createdby'         => auth()->user()->id,
                    'createddatetime'   => date('Y-m-d H:i:s')
                ]);

            if($request->has('experienced'))
            {
                foreach($request->get('experienced') as $expid)
                {
                    DB::table('clinic_appointmentsdetail')
                        ->insert([
                            'headerid'          => $appid,
                            'experienceid'      => $expid,
                            'createdby'         => auth()->user()->id,
                            'createddatetime'   => date('Y-m-d H:i:s')
                        ]);
                }
            }
            
            return $appid;
        }else{
            return 0;
        }
    }
    public function updateappointment(Request $request)
    {
        try{
            DB::table('clinic_appointments')
                ->where('id', $request->get('id'))
                ->update([
                    'description'           =>  $request->get('description'),
                    'updatedby'         => auth()->user()->id,
                    'updateddatetime'   => date('Y-m-d H:i:s')
                ]);

            return 1;
        }catch(\Exception $error)
        {
            return 0;
        }
        
    }
    public function deleteappointment(Request $request)
    {
        try{
            DB::table('clinic_appointments')
                ->where('id', $request->get('id'))
                ->update([
                    'deleted'           => 1,
                    'deletedby'         => auth()->user()->id,
                    'deleteddatetime'   => date('Y-m-d H:i:s')
                ]);

            return 1;
        }catch(\Exception $error)
        {
            return 0;
        }
        
    }
}
