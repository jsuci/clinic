<?php

namespace App\Http\Controllers\RegistrarControllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use File;
use Image;
use \Carbon\Carbon;
use Hash;


class PreRegistrationControllerV2 extends \App\Http\Controllers\Controller
{
   
    public function submitPrereg(Request $request){

        $isGuardian = 0;
        $isFather = 0;
        $isMother = 0;

        if($request->get('incase') == 1){
            $isFather = 1;
        }else if($request->get('incase') == 2){
            $isMother = 1;
        }else if($request->get('incase') == 3){
            $isGuardian = 1;
        }

        if($request->get('studtype') == 2){
            $studtype = ' transferee';
        }
        else{
            $studtype = 'new';
        }

        $grantee = 1;

        if($request->has('withESC') && $request->get('withESC') != null){
            $grantee = $request->get('withESC');
        }

        $schoolinfo = DB::table('schoolinfo')->first();
        $request->request->add(['syid'=>$request->get('input_syid')]);
   


        if($request->get('studtype') == 3){
            
            $getStudentInformation = DB::table('studinfo')
                            ->where('sid',$request->get('studid'))
                            ->where('studinfo.deleted','0')
                            ->first();

            if(!isset($getStudentInformation->id)){
                return "Student record not found! Please contact your school registrar";
            }

            self::upload_requirements($request->get('studid'), $request);

            DB::table('earlybirds')
                ->insert([
                    'studid'=>$getStudentInformation->id,
                    'syid'=>$request->get('input_syid'),
                    'semid'=>$request->get('input_semid'),
                    'levelid'=>$request->get('gradelevelid'),
                    'strandid'=>$request->get('studstrand'),
                    'courseid'=>$request->get('courseid'),
                    'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                ]);
            
            return redirect('/preregistration/get/qcode/'.$request->get('studid').'/'.$request->get('last_name').', '.$request->get('first_name').'/'.'Pre-enrolled');


        }else if($request->get('studtype') == 2 || $request->get('studtype') == 1){

            $studid = DB::table('studinfo')->insertGetId([
                'lrn'                      => strtoupper($request->get('lrn')),
                'lastname'                      => strtoupper($request->get('last_name')),
                'firstname'                     => strtoupper($request->get('first_name')),
                'middlename'                    => strtoupper($request->get('middle_name')),
                'street'                        => strtoupper($request->get('street')),
                'barangay'                      => strtoupper($request->get('barangay')),
                'city'                          => strtoupper($request->get('city')),
                'province'                      => strtoupper($request->get('province')),
                'userid'                        => 0,
                'suffix'                        => strtoupper($request->get('suffix')),
                'gender'                        => strtoupper($request->get('gender')),
                'dob'                           => $request->get('dob'),
                'contactno'                     => str_replace('-','',$request->get('contact_number')),
                'mothername'                    => $request->get('mother_name'),
                'moccupation'                   => $request->get('mother_occupation'),
                'mcontactno'                    => str_replace('-','',$request->get('mother_contact_number')),
                'fathername'                    => $request->get('father_name'),
                'foccupation'                   => $request->get('father_occupation'),
                'fcontactno'                    => str_replace('-','',$request->get('father_contact_number')),
                'guardianname'                  => $request->get('guardian_name'),
                'gcontactno'                    => str_replace('-','',$request->get('guardian_contact_number')),
                'guardianrelation'              => $request->get('guardian_relation'),
                'semail'                        => $request->get('email'),
                'studtype'                      => $studtype,
                'levelid'                       => $request->get('gradelevelid'),
                'createddatetime'               => \Carbon\Carbon::now('Asia/Manila')->isoFormat('YYYY-MM-DD HH:mm:ss'),
                'deleted'                       => '0',
                'ismothernum'                   => $isMother,
                'isfathernum'                   => $isFather,
                'isguardannum'                  => $isGuardian, 
                'strandid'                      =>$request->get('studstrand'),
                'courseid'                      =>$request->get('courseid'),
                'lastschoolatt'                 =>$request->get('lastschoolatt'),
                'mol'                           =>$request->get('withMOL'),
                'grantee'                       =>$grantee,
                'nationality'                   =>$request->get('nationality'),
                'preEnrolled'                   =>1
            ]);


            $request->request->add(['studid'=>$studid]);

            // \App\Http\Controllers\SuperAdminController\StudentMedInfoController::create($request);

            $vaccine = DB::table('vaccine_type')
                                            ->where('deleted',0)
                                            ->select(
                                                'id',
                                                'vaccinename',
                                                'vaccinename as text'
                                            )
                                            ->orderBy('vaccinename')
                                            ->get();

            $vacc_type_1st = collect($vaccine)->where('id',$request->get('vacc_type_1st'))->first();
            $vacc_type_2nd = collect($vaccine)->where('id',$request->get('vacc_type_2nd'))->first();
            $vacc_type_booster = collect($vaccine)->where('id',$request->get('vacc_type_booster'))->first();

            DB::table('apmc_midinfo')
                ->insert([
                    'studid'=>$studid,
                    'vacc'=>$request->get('vacc'),

                    'vacc_type_id'=>$request->get('vacc_type_1st'),
                    'vacc_type_2nd_id'=>$request->get('vacc_type_2nd'),
                    'booster_type_id'=>$request->get('vacc_type_booster'),
                    
                    'dose_date_booster'=>$request->get('dose_date_booster'),
                    
                

                    'vacc_type'=>isset($vacc_type_1st->text) ? $vacc_type_1st->text : null,
                    'vacc_type_2nd'=>isset($vacc_type_2nd->text) ? $vacc_type_2nd->text : null,
                    'vacc_type_booster'=>isset($vacc_type_booster->text) ? $vacc_type_booster->text : null,

                    'vacc_card_id'=>$request->get('vacc_card_id'),
                    'dose_date_1st'=>$request->get('dose_date_1st'),
                    'dose_date_2nd'=>$request->get('dose_date_2nd'),
                    'philhealth'=>$request->get('philhealth'),
                    'bloodtype'=>$request->get('bloodtype'),
                    'allergy_to_med'=>$request->get('allergy_to_med'),
                    'med_his'=>$request->get('med_his'),
                    'other_med_info'=>$request->get('other_med_info'),
                    'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                ]);
           
            $acadprog = DB::table('gradelevel')
                            ->where('id',$request->get('gradelevelid'))
                            ->where('deleted',0)
                            ->first()
                            ->acadprogid;


            $sid = \App\RegistrarModel::idprefix($acadprog,$studid);

            $upd = db::table('studinfo')
                    ->where('id', $studid)
                    ->take(1)
                    ->update([
                        'sid' => $sid
                    ]);

            $message = strtoupper($request->get('first_name')) . ' ' .  strtoupper($request->get('last_name')) . ' submitted a pre-enrollment / pre-registration.';
            $link = '/registrar/preenrolled';
            $createdby = 0;
            $registrar = DB::table('teacher')->where('usertypeid',3)->select('userid')->get();
    
            foreach($registrar as $item){
                \App\Models\Notification\NotificationProccess::notification_create($message, $link, $item->userid, $createdby);
            }
    
            $syid = $request->get('input_syid');
            $semid = $request->get('input_semid');

            $gradelevel =  $request->get('gradelevelid');
            $admission_type =  $request->get('input_acadprog');
            \App\Models\Student\PreRegistration\PreRegistrationProccess::submit_preregistration($studid, $syid, $semid, $gradelevel, $admission_type);
            self::upload_requirements($sid, $request, $syid, $semid);
            
            $sname = $request->get('first_name') . ' ' . $request->get('middle_name') . ' ' . $request->get('last_name') . ' ' . $request->get('suffix');
		
            $studuser = db::table('users')
                ->insertGetId([
                    'name' => $sname,
                    'email' => 'S'.$sid,
                    'type' => 7,
                    'password' => Hash::make('123456')
                ]);

            $studpword = \App\RegistrarModel::generatepassword($studuser);

            $putUserid = db::table('studinfo')
                ->where('id', $studid)
                ->update([
                    'userid' => $studuser,
                    'updateddatetime' => \App\RegistrarModel::getServerDateTime(),
                ]);

                
            if($request->get('input_setup_type') == 2){

                DB::table('earlybirds')
                    ->insert([
                        'studid'=>$studid,
                        'syid'=>$request->get('input_syid'),
                        'semid'=>$request->get('input_semid'),
                        'levelid'=>$request->get('gradelevelid'),
                        'strandid'=>$request->get('studstrand'),
                        'courseid'=>$request->get('courseid'),
                        'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                    ]);

            }

            if($schoolinfo->withMOL == 1){
                DB::table('modeoflearning_student')
                    ->insert([
                        'studid'=>$studid,
                        'mol'=>$request->get('withMOL'),
                        'syid'=>$request->get('input_syid'),
                        'createddatetime'=>\Carbon\Carbon::now('Asia/Manila'),
                        'createdby'=>$studuser,
                    ]);
            }

            DB::table('student_updateinformation')
                ->insert([
                    'studid'=>$studid,
                    'createddatetime'=>\Carbon\Carbon::now('Asia/Manila'),
                    'createdby'=>$studuser,
                    'syid'=>$request->get('input_syid'),
                    'semid'=>$request->get('input_semid')
                ]);
            
            return redirect('/preregistration/get/qcode/'.$sid.'/'.$request->get('last_name').', '.$request->get('first_name').'/'.'Pre-registered');

        }

    }

    public static function upload_requirements($queuecode = null, $request = null, $syid = null, $semid = null){

        $extension = 'png';

        foreach(DB::table('preregistrationreqlist')->get() as $item){
            if($request->has('req'.$item->id) != null){
                $urlFolder = str_replace('http://','',$request->root());
                $urlFolder = str_replace('https://','',$urlFolder);
                if (! File::exists(public_path().'preregrequirements/'.$queuecode.'/')) {
                    $path = public_path('preregrequirements/'.$queuecode.'');
                    if(!File::isDirectory($path)){
                        File::makeDirectory($path, 0777, true, true);
                    }
                }
                if (! File::exists(dirname(base_path(), 1).'/'.$urlFolder.'/preregrequirements/'.$queuecode.'/')) {
                    $cloudpath = dirname(base_path(), 1).'/'.$urlFolder.'/preregrequirements/'.$queuecode.'/';
                    if(!File::isDirectory($cloudpath)){
                        File::makeDirectory($cloudpath, 0777, true, true);
                    }
                }
            }
            if($request->has('req'.$item->id)){
                $img = Image::make($request->file('req'.$item->id)->path());
                $time = \Carbon\Carbon::now('Asia/Manila')->isoFormat('MMDDYYHHmmss');
                $destinationPath = public_path('preregrequirements/'.$queuecode.'/'.'requirement'.$item->id.'-'.$time.'.'.$extension);
                $clouddestinationPath = dirname(base_path(), 1).'/'.$urlFolder.'/'.'preregrequirements/'.$queuecode.'/'.'requirement'.$item->id.'-'.$time.'.'.$extension;
                $img->resize(1000, 1000, function ($constraint) {
                                $constraint->aspectRatio();
                            })->save($destinationPath);
                $img->save($clouddestinationPath);
                $datetime = Carbon::now('Asia/Manila');
                DB::table('preregistrationrequirements')    
                            ->insert([
                                'picurl'=>'preregrequirements/requirement'.$item->id.'-'.$time.'.'.$extension,
                                'qcode'=>$queuecode,
                                'preregreqtype'=>$item->id,
                                'createddatetime'=>$datetime,
                                'syid'=>$syid,
                                'semid'=>$semid,
                            ]);

            }
        }


    }

    public function getqcode($qcode, $name){

        $queuecode = array((object)['queing_code'=>$qcode]);

        return view('registrar.preregistrationgetcode')
                        ->with('fullname',$name)
                        ->with('code',$queuecode);

    }


    public function preenrollmentinfo($name, $levelname, $status = null){

        return view('othertransactions.preenrollment.prerenrollmentsubmitted')
                            ->with('fullname',$name)
                            ->with('status',$status)
                            ->with('levelname',$levelname);

    }

    public function early_enrollment_submit(Request $request){

        $studid = $request->get('studid');
        $syid = $request->get('syid');
        $semid = $request->get('semid');
        $levelid = $request->get('levelid');

        $gradelevel_to_enroll = DB::table('gradelevel')
                            ->where('id',$levelid)
                            ->select('sortid')
                            ->where('deleted',0)
                            ->first()
                            ->sortid;

        $studinfo = DB::table('studinfo')
                        ->where('id',$studid)
                        ->where('deleted',0)
                        ->first();

        if($studinfo->studstatus == 1){
            
            $gradelevel_to_enroll = DB::table('gradelevel')
                                        ->where('deleted',0)
                                        ->select('sortid','id')
                                        ->where('sortid','>',$gradelevel_to_enroll)
                                        ->first()
                                        ->id;
        }

        try{

            $check_if_exist =  DB::table('earlybirds')
                                ->where('studid',$studid)
                                ->where('syid',$syid)
                                ->where('semid',$semid)
                                ->where('deleted',0)
                                ->where('levelid',$levelid)
                                ->count();

            if($check_if_exist == 0){
                
                DB::table('earlybirds')
                    ->insert([
                        'studid'=>$studid,
                        'syid'=>$syid,
                        'semid'=>$semid,
                        'levelid'=>$gradelevel_to_enroll,
                        'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                    ]);

            }

            return array((object)[
                'status'=>1,
                'data'=>'Submitted Successfully'
            ]);
         
        }catch(\Exception $e){

            DB::table('zerrorlogs')
            ->insert([
                        'error'=>$e,
                        'createdby'=>auth()->user()->id,
                        'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                        ]);

            return array((object)[
                'status'=>0,
                'data'=>'Something went wrong!'
            ]);
        }

    }


    public function pre_enrollment_submit(Request $request){

        $studid = $request->get('studid');

        try{

            DB::table('studinfo')
                ->where('id',$studid)
                ->take(1)
                ->update([
                    'preEnrolled'=>1
                ]);

            return array((object)[
                'status'=>1,
                'data'=>'Submitted Successfully!'
            ]);

        }catch(\Exception $e){

            DB::table('zerrorlogs')
            ->insert([
                        'error'=>$e,
                        'createdby'=>auth()->user()->id,
                        'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                        ]);

            return array((object)[
                'status'=>0,
                'data'=>'Something went wrong!'
            ]);

        }
        

        

    }
   
}
