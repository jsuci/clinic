<?php

namespace App\Http\Controllers\ClinicControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Session;
class ClinicAppointmentController extends Controller
{
    public function index()
    {
        $experiences = DB::table('clinic_experiences')
            ->where('deleted','0')
            ->orderBy('description')
            ->get();
        $refid = DB::table('usertype')
            ->where('id', Session::get('currentPortal'))
            ->first();
            
        if($refid->refid == '23')
        {
            $extends = 'clinic';
        }elseif($refid->refid == '24'){

            $extends = 'clinic_nurse';
        }elseif($refid->refid == '25'){

            $extends = 'clinic_doctor';
        }
        return view('clinic.appointment.index')
            ->with('experiences', $experiences)
            ->with('extends', $extends);
    }
    public function getappointments(Request $request)
    {
        $daterange = explode(' - ', $request->get('selecteddaterange'));
        $datefrom  = date('Y-m-d', strtotime($daterange[0]));
        $dateto    = date('Y-m-d', strtotime($daterange[1]));
        
        $usertypeid = DB::table('usertype')
            ->where('refid','25')
            ->where('deleted','0')
            ->first();

        $doctors = array();
        if($usertypeid)
        {
            $doctors = DB::table('teacher')
                ->where('usertypeid', $usertypeid->id)
                ->where('deleted','0')
                ->where('isactive','1')
                ->get();

            if(count($doctors)>0)
            {
                foreach($doctors as $doctor)
                {
                    
                }
            }
        }
        $appointments = DB::table('clinic_appointments')
            ->select('clinic_appointments.*','users.type','usertype.utype')
            ->join('users','clinic_appointments.userid','=','users.id')
            ->join('usertype','users.type','=','usertype.id')
            ->whereBetween('clinic_appointments.adate', [$datefrom, $dateto])
            ->where('clinic_appointments.deleted','0')
            ->get();

        if(count($appointments)>0)
        {
            foreach($appointments as $appointment)
            {
                // return collect($appointment);
                if($appointment->type == 7)
                {
                    $info = DB::table('studinfo')
                        ->where('userid', $appointment->userid)
                        ->first();
                    $info->title = null;
                }else{
                    $info = DB::table('teacher')
                        ->where('userid', $appointment->userid)
                        ->first();
                }
                $name_showfirst = "";

                if($info->title != null)
                {
                    $name_showfirst.=$info->title.' ';
                }
                $name_showfirst.=$info->firstname.' ';

                if($info->middlename != null)
                {
                    $name_showfirst.=$info->middlename[0].'. ';
                }
                $name_showfirst.=$info->lastname.' ';
                $name_showfirst.=$info->suffix.' ';

                $appointment->name_showfirst = $name_showfirst;

                $name_showlast = "";

                if($info->title != null)
                {
                    $name_showlast.=$info->title.' ';
                }
                $name_showlast.=$info->lastname.', ';
                $name_showlast.=$info->firstname.' ';

                if($info->middlename != null)
                {
                    $name_showlast.=$info->middlename[0].'. ';
                }
                $name_showlast.=$info->suffix.' ';

                $appointment->name_showlast = $name_showlast;
                $appointedname = '';
                if($appointment->admitted == 1)
                {
                    $appointed = DB::table('teacher')
                        ->where('userid', $appointment->admittedby)
                        ->first();


                    if($appointed)
                    {
                        if($appointed->title != null)
                        {
                            $appointedname.=$appointed->title.' ';
                        }
                        $appointedname.=$appointed->firstname.' ';
        
                        if($appointed->middlename != null)
                        {
                            $appointedname.=$appointed->middlename[0].'. ';
                        }
                        $appointedname.=$appointed->lastname.' ';
                        $appointedname.=$appointed->suffix.' ';
                    }
    
                    $appointment->appointedname = $appointedname;
                }
                if(count($doctors)>0)
                {
                    foreach($doctors as $doctor)
                    {
                        $available = 0;
                        $timeid = 0;

                        $collectavailabilities = DB::table('clinic_schedavailability')
                            ->select('id','timefrom','timeto')
                            ->where('docid', $doctor->id)
                            ->where('scheddate', $appointment->adate)
                            ->where('deleted','0')
                            ->get();

                        if(count($collectavailabilities)>0)
                        {
                            foreach($collectavailabilities as $collectavailability)
                            {
                                if($appointment->atime >= $collectavailability->timefrom && $appointment->atime <= $collectavailability->timeto)
                                {
                                    $available = 1;
                                    $timeid = $collectavailability->id;
                                }
                            }
                        }
                        
                        $doctor->available = $available;
                        $doctor->timeid = $timeid;

                    }
                }
                $appointment->doctors = $doctors;
            }
        }
        
        $refid = DB::table('usertype')
            ->where('id', Session::get('currentPortal'))
            ->first();
            
        if($refid->refid == '23')
        {
            $extends = 'clinic';
        }elseif($refid->refid == '24'){

            $extends = 'clinic_nurse';
        }elseif($refid->refid == '25'){

            $extends = 'clinic_doctor';
        }

        // return $extends;
        return view('clinic.appointment.appointment_filter')
            ->with('appointments', $appointments)
            ->with('extends', $extends);
    }
    public function create()
    {
        $experiences = DB::table('clinic_experiences')
            ->where('deleted','0')
            ->orderBy('description')
            ->get();
        return view('clinic.appointment.appointment_create')
            ->with('experiences', $experiences);
    }
    public function getexperiences()
    {
        $experiences = DB::table('clinic_experiences')
            ->where('deleted','0')
            ->orderBy('description')
            ->get();

        return collect($experiences);
    }
    public function createexperience(Request $request)
    {
        // return $request->all();
        $checkifexists = DB::table('clinic_experiences')
            ->where('description','like','%'.$request->get('newoption').'%')
            ->where('deleted','0')
            ->first();

        if($checkifexists)
        {
            return 0;
        }else{

            $experienceid = DB::table('clinic_experiences')
                ->insertGetId([
                    'description'       => $request->get('newoption'),
                    'createdby'         => auth()->user()->id,
                    'createddatetime'   => date('Y-m-d H:i:s')
                ]);
            return $experienceid;
        }
    }
    public function deleteexperience(Request $request)
    {
        DB::table('clinic_experiences')
            ->where('id', $request->get('id'))
            ->update([
                'deleted'           => 1,
                'deletedby'         => auth()->user()->id,
                'deleteddatetime'   => date('Y-m-d H:i:s')
            ]);
    }
    public function admitaccept(Request $request)
    {
        date_default_timezone_set('Asia/Manila');

        $doctorschedid = explode('-',$request->get('doctorschedid'));
        if(count($doctorschedid)>1)
        {
            $doctorid = $doctorschedid[0];
            $schedid = $doctorschedid[1];
    
            $checkifadmitted = DB::table('clinic_appointments')
                ->where('id', $request->get('id'))
                ->first()->admitted;
    
            if($checkifadmitted == 0 || $checkifadmitted == null)
            {
                try{
                    DB::table('clinic_appointments')
                        ->where('id', $request->get('id'))
                        ->update([
                            'admitted'          =>  1,
                            'docid'          =>  $doctorid,
                            'docavailabilityid'          =>  $schedid,
                            'admittedby'        => auth()->user()->id,
                            'admitteddatetime'  => date('Y-m-d H:i:s')
                        ]);
        
                    return 1;
                }catch(\Exception $error)
                {
                    return 0;
                }
            }else{
                return 2;
            }
        }else{
            return 0;
        }
    }
    public function admitcancel(Request $request)
    {
        // return $request->all();
        date_default_timezone_set('Asia/Manila');

        try{
            DB::table('clinic_appointments')
                ->where('id', $request->get('id'))
                ->update([
                    'admitted'          =>  0,
                    'admittedby'        => null,
                    'admitteddatetime'  => null
                ]);

            return 1;
        }catch(\Exception $error)
        {
            return 0;
        }
    }
    public function markdone(Request $request)
    {
        // return $request->all();
        date_default_timezone_set('Asia/Manila');

        try{
            DB::table('clinic_appointments')
                ->where('id', $request->get('id'))
                ->update([
                    'label'          =>  $request->get('applabel'),
                    'labeldatetime'  =>  date('Y-m-d H:i:s')
                ]);

            return 1;
        }catch(\Exception $error)
        {
            return 0;
        }
    }
}
