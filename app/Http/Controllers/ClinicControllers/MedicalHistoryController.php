<?php

namespace App\Http\Controllers\ClinicControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Session;
use App\Models\SchoolClinic\SchoolClinic;
class MedicalHistoryController extends Controller
{
    public function index(Request $request)
    {
        
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
        $users  = SchoolClinic::users();
        
        return view('clinic.medicalhistory.index')
            ->with('extends', $extends)
            ->with('users', $users);
    }
    public function gethistory(Request $request)
    {
        // return $request->all();

        $appointments = DB::table('clinic_appointments')
            ->where('userid',$request->get('userid'))
            ->where('deleted','0')
            ->where('label','1')
            ->where('admitted','1')
            ->get();

        return $appointments;

        
    }
}
