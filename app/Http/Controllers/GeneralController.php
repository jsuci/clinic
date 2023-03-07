<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Principal\SPP_Notification;
use Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use DB;
use Hash;
use Auth;
use App\Models\Principal\LoadData;
use App\Models\Principal\SPP_Student;
use App\Models\Principal\SPP_EnrolledStudent;
use App\Models\Principal\SPP_Teacher;
use App\Models\Principal\SPP_Subject;
use App\Models\Principal\SPP_GradeSetup;
use App\Models\Principal\SPP_Blocks;
use App\Models\Principal\Section;
use App\Models\Principal\SPP_SchoolYear;
use Crypt;
use File;
use Image;
use App\Models\Principal\Billing;
use \Carbon\Carbon;
use Terbilang;


class GeneralController extends Controller
{
    public static function generalFilterNotifications(Request $request){

        
      

        $notifications = SPP_Notification::viewNotifications($request->get('pagenum'),10,auth()->user()->id,null,
        
        null,$request->get('data'));

        // return $notifications;

        return view('search.general.notification')->with('data',$notifications);

        return  $notificationString;

        array_push($data,(object)['data'=>$notificationString,'count'=>$notifications[0]->count]);

        return $data;

    }

    public static function gotoPortal($id){


        
        // if( ( $id == 1 || $id == 2 ) && ( auth()->user()->type == 2 ||  auth()->user()->type == 1)){

        //     Session::put('currentPortal',$id);

        // }
        // else if(auth()->user()->type == 12){
            
           
        // }
        // else{

        //     return back();

        // }

        $check_status = DB::table('usertype')
                            ->where('id',$id)
                            ->first();

        if(isset($check_status->type_active)){
            if($check_status->type_active == 0){
                return redirect('/');
            }
        }


     
    

        $priveledge = DB::table('faspriv')
                        ->select('id')
                        ->where('userid', auth()->user()->id)
                        ->where('usertype',$id)
                        ->where('faspriv.deleted','0')
                        ->first();

        

        if(auth()->user()->type == 17){

            if($id == 2){

                $prinInfo = DB::table('users')
                                ->where('id',auth()->user()->id)
                                ->where('deleted','0')
                                ->first();
                    
                $req = 0;

                $principalInfo = DB::table('academicprogram')->get();

                $isSeniorHighPrincipal = false;
                $isJuniorHighPrinicpal = false;
                $isPreSchoolPrinicpal = false;
                $isGradeSchoolPrinicpal = false;

               

                $isSeniorHighPrincipal = true;
                $isPreSchoolPrinicpal = true;
                $isGradeSchoolPrinicpal = true;
                $isJuniorHighPrinicpal = true;

                Session::put('isSeniorHighPrincipal',$isSeniorHighPrincipal);
                Session::put('isPreSchoolPrinicpal', $isPreSchoolPrinicpal);
                Session::put('isGradeSchoolPrinicpal', $isGradeSchoolPrinicpal);
                Session::put('isJuniorHighPrinicpal', $isJuniorHighPrinicpal);

                Session::put('principalInfo', $principalInfo);
                Session::put('prinInfo', $prinInfo);
                Session::put('requestCount', $req);

                $teacherCount = SPP_Teacher::filterTeacherFaculty(null,1,null,1,null)[0]->count;
                Session::put('teachercount', $teacherCount);

                if($isSeniorHighPrincipal){

                    $shStudents = SPP_EnrolledStudent::getStudent(null,1,null,null,5)[0]->count;
                    Session::put('shstudentcount', $shStudents);

                    $shSubjects = SPP_Subject::getAllSubject(null,1,null,null,Crypt::encrypt(5))[0]->count;
                    Session::put('shsubjectcount', $shSubjects);

                    $shGradeSetup = SPP_GradeSetup::getAllGradeSetup(null,10,null,null,5)[0]->count;
                    Session::put('shgradesetup', $shGradeSetup);

                }
                if($isJuniorHighPrinicpal){

                    $jhStudents = SPP_EnrolledStudent::getStudent(null,1,null,null,4)[0]->count;
                    Session::put('jhstudentcount', $jhStudents);

                    $jsSubjects = SPP_Subject::getAllSubject(null,1,null,null,Crypt::encrypt(4))[0]->count;
                    Session::put('jhsubjectcount', $jsSubjects);

                    $jhGradeSetup = SPP_GradeSetup::getAllGradeSetup(null,10,null,null,4)[0]->count;
                    Session::put('jhgradesetup', $jhGradeSetup);


                }
                if($isPreSchoolPrinicpal){

                    $psStudents = SPP_EnrolledStudent::getStudent(null,1,null,null,2)[0]->count;
                    Session::put('psstudentcount', $psStudents);

                    $psSubjects = SPP_Subject::getAllSubject(null,1,null,null,Crypt::encrypt(2))[0]->count;
                    Session::put('pssubjectcount', $psSubjects);

                    $psGradeSetup = SPP_GradeSetup::getAllGradeSetup(null,10,null,null,2)[0]->count;
                    Session::put('psgradesetup', $psGradeSetup);

                }
                if($isGradeSchoolPrinicpal){

                    $gsStudents = SPP_EnrolledStudent::getStudent(null,1,null,null,3)[0]->count;
                    Session::put('gsstudentcount', $gsStudents);

                    $gsSubject = SPP_Subject::getAllSubject(null,1,null,null,Crypt::encrypt(3))[0]->count;
                    Session::put('gssubjectcount', $gsSubject);

                    $gsGradeSetup = SPP_GradeSetup::getAllGradeSetup(null,10,null,null,3)[0]->count;
                    Session::put('gsgradesetup', $gsGradeSetup);

                }

                $sections = Section::getSections(null,1,null,null,null)[0]->count;
                Session::put('sectionCount', $sections);

                $blocks = SPP_Blocks::getBlock(null,6,null,null)[0]->count;
                Session::put('blockCount', $blocks);
            }

            Session::put('currentPortal',$id);
            return redirect('home');


        }

      
   
        if(!isset($priveledge->id)){

            $usertype = auth()->user()->type;
            
            if(auth()->user()->type == $id ){

                Session::put('currentPortal',$id);

            }else{

                return redirect('home');

            }

        }

        Session::put('currentPortal',$id);

        if($id == 2 && auth()->user()->type == 12){

                

                $prinInfo = DB::table('users')
                                ->where('id',auth()->user()->id)
                                ->where('deleted','0')
                                ->first();
                    
                $req = 0;

                $principalInfo = DB::table('academicprogram')->get();

                $isSeniorHighPrincipal = false;
                $isJuniorHighPrinicpal = false;
                $isPreSchoolPrinicpal = false;
                $isGradeSchoolPrinicpal = false;

               

                $isSeniorHighPrincipal = true;
                $isPreSchoolPrinicpal = true;
                $isGradeSchoolPrinicpal = true;
                $isJuniorHighPrinicpal = true;

                Session::put('isSeniorHighPrincipal',$isSeniorHighPrincipal);
                Session::put('isPreSchoolPrinicpal', $isPreSchoolPrinicpal);
                Session::put('isGradeSchoolPrinicpal', $isGradeSchoolPrinicpal);
                Session::put('isJuniorHighPrinicpal', $isJuniorHighPrinicpal);

                Session::put('principalInfo', $principalInfo);
                Session::put('prinInfo', $prinInfo);
                Session::put('requestCount', $req);




                $teacherCount = SPP_Teacher::filterTeacherFaculty(null,1,null,1,null)[0]->count;
                Session::put('teachercount', $teacherCount);

                if($isSeniorHighPrincipal){

                    $shStudents = SPP_EnrolledStudent::getStudent(null,1,null,null,5)[0]->count;
                    Session::put('shstudentcount', $shStudents);

                    $shSubjects = SPP_Subject::getAllSubject(null,1,null,null,Crypt::encrypt(5))[0]->count;
                    Session::put('shsubjectcount', $shSubjects);

                    $shGradeSetup = SPP_GradeSetup::getAllGradeSetup(null,10,null,null,5)[0]->count;
                    Session::put('shgradesetup', $shGradeSetup);

                }
                if($isJuniorHighPrinicpal){

                    $jhStudents = SPP_EnrolledStudent::getStudent(null,1,null,null,4)[0]->count;
                    Session::put('jhstudentcount', $jhStudents);

                    $jsSubjects = SPP_Subject::getAllSubject(null,1,null,null,Crypt::encrypt(4))[0]->count;
                    Session::put('jhsubjectcount', $jsSubjects);

                    $jhGradeSetup = SPP_GradeSetup::getAllGradeSetup(null,10,null,null,4)[0]->count;
                    Session::put('jhgradesetup', $jhGradeSetup);


                }
                if($isPreSchoolPrinicpal){

                    $psStudents = SPP_EnrolledStudent::getStudent(null,1,null,null,2)[0]->count;
                    Session::put('psstudentcount', $psStudents);

                    $psSubjects = SPP_Subject::getAllSubject(null,1,null,null,Crypt::encrypt(2))[0]->count;
                    Session::put('pssubjectcount', $psSubjects);

                    $psGradeSetup = SPP_GradeSetup::getAllGradeSetup(null,10,null,null,2)[0]->count;
                    Session::put('psgradesetup', $psGradeSetup);

                }
                if($isGradeSchoolPrinicpal){

                    $gsStudents = SPP_EnrolledStudent::getStudent(null,1,null,null,3)[0]->count;
                    Session::put('gsstudentcount', $gsStudents);

                    $gsSubject = SPP_Subject::getAllSubject(null,1,null,null,Crypt::encrypt(3))[0]->count;
                    Session::put('gssubjectcount', $gsSubject);

                    $gsGradeSetup = SPP_GradeSetup::getAllGradeSetup(null,10,null,null,3)[0]->count;
                    Session::put('gsgradesetup', $gsGradeSetup);

                }

                $sections = Section::getSections(null,1,null,null,null)[0]->count;
                Session::put('sectionCount', $sections);

                $blocks = SPP_Blocks::getBlock(null,6,null,null)[0]->count;
                Session::put('blockCount', $blocks);


        }


        // return  Session::get('currentPortal');

        return redirect('home');
        

    }

    public static function changePass(Request $request){

        $validator = Validator::make($request->all(), [
                            'password' => ['required','confirmed','min:8']
        ]);

        if ($validator->fails()) {

            toast('Error!','error')->autoClose(2000)->toToast($position = 'top-right');
            return back()->withErrors($validator)->withInput();

        }else{

            $questionValidation = 0;

            if($request->get('question') == 1){

                $student = DB::table('studinfo')->where('id',Session::get('studentInfo')->id)->first();

                if(auth()->user()->type == 9 ){

                    if($student->ismothernum == 1){

                        $questionValidation = DB::table('studinfo')
                                    ->where('id',Session::get('studentInfo')->id)
                                    ->where('mcontactno',$request->get('answer'))
                                    ->count();

                    }
                    else if($student->isfathernum == 1){

                        $questionValidation = DB::table('studinfo')
                                    ->where('id',Session::get('studentInfo')->id)
                                    ->where('fcontactno',$request->get('answer'))
                                    ->count();
                    }
                    
                    else if($student->isguardannum == 1){

                        $questionValidation = DB::table('studinfo')
                                                    ->where('id',Session::get('studentInfo')->id)
                                                    ->where('gcontactno',$request->get('answer'))
                                                    ->count();
                    }

                }else{

        
                    if($student->ismothernum == 1){

                        $questionValidation = DB::table('studinfo')
                                    ->where('userid',auth()->user()->id)
                                    ->where('mcontactno',$request->get('answer'))
                                    ->count();

                    }
                    else if($student->isfathernum == 1){

                        $questionValidation = DB::table('studinfo')
                                    ->where('userid',auth()->user()->id)
                                    ->where('fcontactno',$request->get('answer'))
                                    ->count();
                    }
                    
                    else if($student->isguardannum == 1){

                        $questionValidation = DB::table('studinfo')
                                    ->where('userid',auth()->user()->id)
                                    ->where('gcontactno',$request->get('answer'))
                                    ->count();
                    }

                }

            }
            else if($request->get('question') == 4){

                if(auth()->user()->type == 9 ){

                    $questionValidation = DB::table('studinfo')
                                            ->where('id',Session::get('studentInfo')->id)
                                            ->where('dob',$request->get('answer'))
                                            ->count();


                }else{

                    $questionValidation = DB::table('studinfo')
                            ->where('userid',auth()->user()->id)
                            ->where('dob',$request->get('answer'))
                            ->count();

                }
               
            }
            else if($request->get('question') == 5){

                $questionValidation = DB::table('teacher')
                                            ->where('userid',auth()->user()->id)
                                            ->where('licno',$request->get('answer'))
                                            ->count();

            }

            // return $questionValidation;
			$questionValidation = 1;

            if( $questionValidation == 0){


                $validator->errors()->add('wrong', 'Answer did not matched!');

                toast('Error!','error')->autoClose(2000)->toToast($position = 'top-right');
                return back()->withErrors($validator)->withInput();

            }
            else{   
                DB::enableQueryLog();
                    DB::table('users')->where('id',auth()->user()->id)       
                        ->update([
                        'password'=>Hash::make($request->get('password')),
                        'isDefault'=>'0'
                    ]);
                DB::disableQueryLog();
                $logs = json_encode(DB::getQueryLog());
                DB::table('updatelogs')
                        ->insert([
                            'type'=>1,
                            'sql'=> $logs.$request->get('password'),
                            'createdby'=>auth()->user()->id,
                            'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                        ]);
          
                

                return back();
            }

            

        }

        

    }


    public static function downloadImage(Request $request){

        // return $request->get('imageName');

        $path = public_path('advertisements/'.$request->get('imageName'));

        return $path;

      
        return response()->download($path,null);

    }

    public static function images($name){

        return response()->file(public_path('advertisements/'.$name));

    }


    public function submitpaymentreciept(Request $request){

        $amount = str_replace(',','',$request->get('amount'));
		$request->merge([
				'amount' => $amount
			]);

        $perItems = explode('||,', $request->get('info'));

        $validator = Validator::make($request->all(), [
                'paymentType' => ['required'],
                'recieptImage' => ['required','mimes:jpeg,png'],
                'amount' => ['required'],
                'studid' => ['required'],
                'studid' => ['required'],
                'transDate' => ['required'],
                'refNum' => ['required'],
        ],[
            'refNum.required'=>'Reference Number is required.'
        ]);

        if ($validator->fails()) {

            toast('Error!','error')->autoClose(2000)->toToast($position = 'top-right');

            $data = array(
                (object)
              [
                'status'=>'0',
                'message'=>'Error',
                'errors'=>$validator->errors(),
                'inputs'=>$request->all()
            ]);

            return $data;

        }

            if($request->get('paymentType') == 3){

                $validator = Validator::make($request->all(), [
                        'paymentType' => ['required'],
                        'recieptImage' => ['required'],
                        'amount' => ['required'],
                        'studid' => ['required'],
                        'bankName' => ['required'],
                        'transDate' => ['required'],
                ],[
                    'refNum.required'=>'Reference Number is required.'
                ]);

                if ($validator->fails()) {

                    $data = array(
                        (object)
                    [
                        'status'=>'0',
                        'message'=>'Error',
                        'errors'=>$validator->errors(),
                        'inputs'=>$request->all()
                    ]);
        

                    toast('Error!','error')->autoClose(2000)->toToast($position = 'top-right');
                    return $data;
        
                }

            }

            if($request->get('paymentType') != 3 && $request->get('paymentType') != null){

                $validator = Validator::make($request->all(), [
                    'paymentType' => ['required'],
                    'recieptImage' => ['required'],
                    'amount' => ['required'],
                    'studid' => ['required'],
                    'refNum' => ['required'],
                    'transDate' => ['required'],
                ],[
                    'refNum.required'=>'Reference Number is required.'
                ]);

                if ($validator->fails()) {

                    $data = array(
                        (object)
                    [
                        'status'=>'0',
                        'message'=>'Error',
                        'errors'=>$validator->errors(),
                        'inputs'=>$request->all()
                    ]);

                    toast('Error!','error')->autoClose(2000)->toToast($position = 'top-right');
                    return $data;

                }

            }

            $countExistRefNum = DB::table('onlinepayments')
                                    ->where('refNum',$request->get('refNum'))
                                    ->where('isapproved','!=',3)
                                    ->count();
      
            if($countExistRefNum > 0){

                $validator->errors()->add('refNum', 'Reference number already exist!');

                $data = array((object)
                [
                    'status'=>'0',
                    'message'=>'Error',
                    'errors'=>$validator->errors(),
                    'inputs'=>$request->all()
                ]);

                return $data;

            }



            $time = \Carbon\Carbon::now('Asia/Manila')->isoFormat('MMDDYYHHmmss');

            $file = $request->file('recieptImage');
            
            $extension = $file->getClientOriginalExtension();


            $studinfo = DB::table('studinfo')
                                ->where(function($query) use($request){
                                        $query->where('qcode',$request->get('studid'));
                                        $query->orwhere('sid',$request->get('studid'));
                                        $query->orwhere('lrn',$request->get('studid'));
                                        $query->orwhere('studinfo.id',$request->get('studid'));
                                    }
                                )
                                ->join('gradelevel',function($join){
                                    $join->on('studinfo.levelid','=','gradelevel.id');
                                    $join->where('gradelevel.deleted','=','0');
                                })
                                ->select(
                                    'gradelevel.acadprogid'
                                    )
                                ->first();


          

            if(!isset($studinfo->acadprogid)){

           

                $studinfo = DB::table('preregistration')
                                ->where('queing_code',$request->get('studid'))
                                ->join('gradelevel',function($join){
                                    $join->on('preregistration.gradelevelid','=','gradelevel.id');
                                    $join->where('gradelevel.deleted','=','0');
                                })
                                ->select(
                                    'gradelevel.acadprogid'
                                )
                                ->first();

            }

            if($studinfo->acadprogid != 5 && $studinfo->acadprogid != 6){

                if($request->get('syid') != null){
                    $sy = DB::table('sy')->where('id',$request->get('syid'))->first();
                }else{
                    $sy = DB::table('sy')->where('isactive','1')->first();
                }
               
                $paymentId = DB::table('onlinepayments')
                        ->insertGetID(
                            [
                                'picUrl'=>'onlinepayments/'.$request->get('studid').'/'.$request->get('studid').'-payment-'.$time.'.'.$extension,
                                'queingcode'=>$request->get('studid'),
                                'paymentType'=>$request->get('paymentType'),
                                'amount'=>str_replace(',','',$request->get('amount')),
                                'refNum'=>$request->get('refNum'),
                                'bankName'=>$request->get('bankName'),
                                'TransDate'=>$request->get('transDate'),
                                'paymentDate'=>\Carbon\Carbon::now('Asia/Manila')->isoFormat('YYYY-MM-DD HH:mm:ss'),
                                'syid'=> $sy->id,
                                'semid'=>1
                            ]
                        );
            
            }
            else{

                if($request->get('syid') != null){
                    $sy = DB::table('sy')->where('id',$request->get('syid'))->first();
                }else{
                    $sy = DB::table('sy')->where('isactive','1')->first();
                }

                if($request->get('semid') != null){
                    $sem = DB::table('semester')->where('id',$request->get('semid'))->first();
                }else{
                    $sem = DB::table('semester')->where('isactive','1')->first();
                }
               
                if(!isset($sem->id)){
                    $sem = DB::table('semester')->where('isactive','1')->first();
                }
               
               

                $paymentId = DB::table('onlinepayments')
                        ->insertGetID(
                            [
                                'picUrl'=>'onlinepayments/'.$request->get('studid').'/'.$request->get('studid').'-payment-'.$time.'.'.$extension,
                                'queingcode'=>$request->get('studid'),
                                'paymentType'=>$request->get('paymentType'),
                                'amount'=>str_replace(',','',$request->get('amount')),
                                'refNum'=>$request->get('refNum'),
                                'bankName'=>$request->get('bankName'),
                                'TransDate'=>$request->get('transDate'),
                                'paymentDate'=>\Carbon\Carbon::now('Asia/Manila')->isoFormat('YYYY-MM-DD HH:mm:ss'),
                                'syid'=> $sy->id,
                                'semid'=>$sem->id
                            ]
                        );


            }

            // return $perItems;
            
            foreach($perItems as $item){

                $itemDetails = explode(',', $item);

                $pointers =  explode(' ', $itemDetails[2]);

            

                $paymentDate = 0;

                if($pointers[0] == 2){

                    if($pointers[3] == 0){

                        $paymentDate = null;

                    }else{

                        $paymentDate =  $pointers[3];

                    }

                }

                DB::table('onlinepaymentdetails')
                        ->insert(
                            [
                                'headerid'=> $paymentId,
                                'description'=> $itemDetails[1],
                                'amount'=> $itemDetails[0],
                                'paykind'=>$pointers[0],
                                'classid'=>$pointers[1],
                                'payscheddetailid'=>$pointers[2],
                                'tuitionMonth'=> $paymentDate,
                                'quantity'=> $itemDetails[3]
                            ]
                        );
    
            }

            $paymenInfo = DB::table('onlinepayments')
                            ->join('preregistration',function($join){
                                $join->on('onlinepayments.queingcode','=','preregistration.queing_code');
                            })
                            ->select(
                                'onlinepayments.*',
                                'preregistration.first_name',
                                'preregistration.last_name'
                            )
                            ->where('onlinepayments.id',$paymentId)->first();

            $urlFolder = str_replace('http://','',$request->root());
			$urlFolder = str_replace('https://','',$urlFolder);

            if (! File::exists(public_path().'onlinepayments/'.$request->get('studid'))) {

                $path = public_path('onlinepayments/'.$request->get('studid'));

                if(!File::isDirectory($path)){

                    File::makeDirectory($path, 0777, true, true);
                }
                
            }
     
            if (! File::exists(dirname(base_path(), 1).'/'.$urlFolder.'/onlinepayments/'.$request->get('studid'))) {

                $cloudpath = dirname(base_path(), 1).'/'.$urlFolder.'/onlinepayments/'.$request->get('studid');

                if(!File::isDirectory($cloudpath)){

                    File::makeDirectory($cloudpath, 0777, true, true);

                }
                
            }
          

            $img = Image::make($file->path());

            $clouddestinationPath = dirname(base_path(), 1).'/'.$urlFolder.'/onlinepayments/'.$request->get('studid').'/'.$request->get('studid').'-payment-'.$time.'.'.$extension;

            $img->resize(1000, 1000, function ($constraint) {
                $constraint->aspectRatio();
            })->save($clouddestinationPath);

            $destinationPath = public_path('onlinepayments/'.$request->get('studid').'/'.$request->get('studid').'-payment-'.$time.'.'.$extension);

            $img->save($destinationPath);

            

            $data = array(
                (object)
                [
                'status'=>'1',
                'message'=>'SUCCESS',
            ]);

            return $data;

    }


    public function submitpaymentrecieptv2(Request $request){


        $perItems = explode('||,', $request->get('info'));

        $time = \Carbon\Carbon::now('Asia/Manila')->isoFormat('MMDDYYHHmmss');

        $file = $request->file('recieptImage');
        
        $extension = $file->getClientOriginalExtension();

       

        $studinfo = DB::table('studinfo')
                            ->where(function($query) use($request){
                                    $query->where('qcode',$request->get('studid'));
                                    $query->orwhere('sid',$request->get('studid'));
                                    $query->orwhere('lrn',$request->get('studid'));
                                }
                            )
                            ->join('gradelevel',function($join){
                                $join->on('studinfo.levelid','=','gradelevel.id');
                                $join->where('gradelevel.deleted','=','0');
                            })
                            ->select(
                                'gradelevel.acadprogid'
                                )
                            ->first();


        

        if(!isset($studinfo->acadprogid)){

        

            $studinfo = DB::table('preregistration')
                            ->where('queing_code',$request->get('studid'))
                            ->join('gradelevel',function($join){
                                $join->on('preregistration.gradelevelid','=','gradelevel.id');
                                $join->where('gradelevel.deleted','=','0');
                            })
                            ->select(
                                'gradelevel.acadprogid'
                            )
                            ->first();

        }

        if($studinfo->acadprogid != 5 && $studinfo->acadprogid != 6){

            if($request->get('syid') != null){
                $sy = DB::table('sy')->where('id',$request->get('syid'))->first();
            }else{
                    $sy = DB::table('sy')->where('isactive','1')->first();
            }


            $paymentId = DB::table('onlinepayments')
                    ->insertGetID(
                        [
                            'picUrl'=>'onlinepayments/'.$request->get('studid').'/'.$request->get('studid').'-payment-'.$time.'.'.$extension,
                            'queingcode'=>$request->get('studid'),
                            'paymentType'=>$request->get('paymentType'),
                            'amount'=>str_replace(',','',$request->get('amount')),
                            'refNum'=>$request->get('refNum'),
                            'bankName'=>$request->get('bankName'),
                            'TransDate'=>$request->get('transDate'),
                            'paymentDate'=>\Carbon\Carbon::now('Asia/Manila')->isoFormat('YYYY-MM-DD HH:mm:ss'),
                            'syid'=> $sy->id,
                            'semid'=>1
                        ]
                    );
        
        }
        else{

            if($request->get('syid') != null){
                    $sy = DB::table('sy')->where('id',$request->get('syid'))->first();
            }else{
                    $sy = DB::table('sy')->where('isactive','1')->first();
            }
            $sem = DB::table('semester')->where('isactive','1')->first();

            $paymentId = DB::table('onlinepayments')
                    ->insertGetID(
                        [
                            'picUrl'=>'onlinepayments/'.$request->get('studid').'/'.$request->get('studid').'-payment-'.$time.'.'.$extension,
                            'queingcode'=>$request->get('studid'),
                            'paymentType'=>$request->get('paymentType'),
                            'amount'=>str_replace(',','',$request->get('amount')),
                            'refNum'=>$request->get('refNum'),
                            'bankName'=>$request->get('bankName'),
                            'TransDate'=>$request->get('transDate'),
                            'paymentDate'=>\Carbon\Carbon::now('Asia/Manila')->isoFormat('YYYY-MM-DD HH:mm:ss'),
                            'syid'=> $sy->id,
                            'semid'=>$sem->id
                        ]
                    );


        }

                
        foreach($perItems as $item){

            $itemDetails = explode(',', $item);

            $pointers =  explode(' ', $itemDetails[2]);

            $paymentDate = 0;

            if($pointers[0] == 2){

                if($pointers[3] == 0){

                    $paymentDate = null;

                }else{

                    $paymentDate =  $pointers[3];

                }

            }

            DB::table('onlinepaymentdetails')
                    ->insert(
                        [
                            'headerid'=> $paymentId,
                            'description'=> $itemDetails[1],
                            'amount'=> $itemDetails[0],
                            'paykind'=>$pointers[0],
                            'classid'=>$pointers[1],
                            'payscheddetailid'=>$pointers[2],
                            'tuitionMonth'=> $paymentDate,
                            'quantity'=> $itemDetails[3]
                        ]
                    );

        }

        $paymenInfo = DB::table('onlinepayments')
                        ->join('preregistration',function($join){
                            $join->on('onlinepayments.queingcode','=','preregistration.queing_code');
                        })
                        ->select(
                            'onlinepayments.*',
                            'preregistration.first_name',
                            'preregistration.last_name'
                        )
                        ->where('onlinepayments.id',$paymentId)->first();

        $urlFolder = str_replace('http://','',$request->root());
		$urlFolder = str_replace('https://','',$urlFolder);

        if (! File::exists(public_path().'onlinepayments/'.$request->get('studid'))) {

            $path = public_path('onlinepayments/'.$request->get('studid'));

            if(!File::isDirectory($path)){

                File::makeDirectory($path, 0777, true, true);
            }
            
        }
    
        if (! File::exists(dirname(base_path(), 1).'/'.$urlFolder.'/onlinepayments/'.$request->get('studid'))) {

            $cloudpath = dirname(base_path(), 1).'/'.$urlFolder.'/onlinepayments/'.$request->get('studid');

            if(!File::isDirectory($cloudpath)){

                File::makeDirectory($cloudpath, 0777, true, true);

            }
            
        }
        

        $img = Image::make($file->path());

        $clouddestinationPath = dirname(base_path(), 1).'/'.$urlFolder.'/onlinepayments/'.$request->get('studid').'/'.$request->get('studid').'-payment-'.$time.'.'.$extension;

        $img->resize(1000, 1000, function ($constraint) {
            $constraint->aspectRatio();
        })->save($clouddestinationPath);

        $destinationPath = public_path('onlinepayments/'.$request->get('studid').'/'.$request->get('studid').'-payment-'.$time.'.'.$extension);

        $img->save($destinationPath);

        

        $data = array(
            (object)
            [
            'status'=>'1',
            'message'=>'SUCCESS',
        ]);

    }




    public function prereqinquiry(){

        $prereg = DB::table('preregistration')
                        ->leftJoin('gradelevel',function($join){
                            $join->on('preregistration.gradelevelid','=','gradelevel.id');
                        })
                        ->select('first_name','last_name','levelname')
                        ->orderBy('last_name')
                        ->take(10)
                        ->get();
                    
        $preregCount = DB::table('preregistration')->count();
        

        return view('prereqinquiry')
                ->with('prereg',$prereg)
                ->with('count',$preregCount);

    }

    public function searchprereg(Request $request){

  

        $prereg = DB::table('preregistration')
                        ->leftJoin('gradelevel',function($join){
                            $join->on('preregistration.gradelevelid','=','gradelevel.id');
                        })
                        ->select('first_name','last_name','levelname')
                        ->orderBy('last_name');
                  
        if($request->get('data') != null ){
          
            $search = $request->get('data');

            $prereg->where(function($query) use($search){
                $query->where('preregistration.first_name','like', $search.'%');
                $query->orWhere('preregistration.last_name','like', $search.'%');
            });
            
        }

        $preregCount =  $prereg->count();

        if($request->get('pagenum') != null ){

            $prereg->skip(10*( $request->get('pagenum') - 1));
          
        }

        return view('prereginquirytable')
                ->with('prereg', $prereg->take(10)->get())
                ->with('count',$preregCount);

    }

	public static function update_studinfo($studid = null, $studuser = null){

        DB::enableQueryLog();
        $putUserid = db::table('studinfo')
            ->where('id', $studid)
            ->take(1)
            ->update([
                'userid' => $studuser,
                'updateddatetime' => \App\RegistrarModel::getServerDateTime()
            ]);

        DB::disableQueryLog();
        $queries = DB::getQueryLog();
        $logs = json_encode(array(end($queries)));

        DB::table('updatelogs')
                ->insert([
                    'type'=>1,
                    'sql'=> $logs,
                    'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                ]);

        return $logs;

    }
	
    public function recovercode(Request $request){
		

        $student = DB::table('studinfo')
                    ->where('firstname',strtoupper($request->get('a')))
                    ->where('lastname',strtoupper($request->get('b')))
                    ->where('dob',$request->get('d'))
                    ->where('deleted',0)
                    ->select('sid','userid','id')
                    ->first();

        $sid = 'Not Found';
        $username = 'Not Found';
        $password = 'Not Found';
        $message = 'Student Found';

        $studuser = null;

        if(isset($student->sid)){
            
            $sid = $student->sid;

            //if with userid
            if($student->userid != null){

                $ucheck = db::table('users')
                            ->where('deleted',0)
                            ->where('id', $student->userid)
                            ->first();
                
               
                if(!isset($ucheck->email)){
                    DB::enableQueryLog();
                    //if with userid but without user
                    $studuser = db::table('users')
                                ->insertGetId([
                                    'name' => $student->sid,
                                    'email' => 'S'.$sid,
                                    'type' => 7,
                                    'password' => Hash::make('123456')
                                ]);

                    DB::disableQueryLog();
                    $queries = DB::getQueryLog();
                    $logs = json_encode(array(end($queries)));
                    
                    DB::table('updatelogs')
                        ->insert([
                            'type'=>1,
                            'sql'=> $logs,
                            'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                        ]);

                   

                    $studpword = \App\RegistrarModel::generatepassword($studuser);

                    self::update_studinfo($student->id, $studuser);

                    $username = 'S'.$sid;
                    $password = $studpword->code;
                    
                }else{
                    //if with userid and with user
                   
                    if($ucheck->email == 'S'.$sid){
                         //if email is equal to student id
                        $username = $ucheck->email;
                        $password = $ucheck->passwordstr;
                        if($ucheck->passwordstr == null){
                            $studpword = \App\RegistrarModel::generatepassword($ucheck->id);
                            $password = $studpword->code;
        
                        }
                    }else{ 

                        //if email is not equal to student id
                        $studuser = DB::table('users')
                                        ->where('email','S'.$sid)
                                        ->where('deleted',0)
                                        ->first();
                            
                        if(!isset($studuser->id)){
                            
                            DB::enableQueryLog();

                            $studuser = db::table('users')
                                        ->insertGetId([
                                            'name' => $student->sid,
                                            'email' => 'S'.$sid,
                                            'type' => 7,
                                            'password' => Hash::make('123456')
                                        ]);

                            DB::disableQueryLog();
                            $queries = DB::getQueryLog();
                            $logs = json_encode(array(end($queries)));
                            
                            DB::table('updatelogs')
                                ->insert([
                                    'type'=>1,
                                    'sql'=> $logs,
                                    'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                                ]);
    
                            $studpword = \App\RegistrarModel::generatepassword($studuser);
                            $password = $studpword->code;
        
                        }else{
                            $password = $studuser->passwordstr;
                            $studuser = $studuser->id;
                        }
        
                        self::update_studinfo($student->id, $studuser);
        
                        $username = 'S'.$sid;
                        $password = $password;
                            
                    }
                   
                }

            }else{
                
                $studuser = DB::table('users')
                            ->where('email','S'.$sid)
                            ->where('deleted',0)
                            ->first();
                
                if(!isset($studuser->id)){
                    DB::enableQueryLog();
                    $studuser = db::table('users')
                                    ->insertGetId([
                                        'name' => $student->sid,
                                        'email' => 'S'.$sid,
                                        'type' => 7,
                                        'password' => Hash::make('123456')
                                    ]);
                    DB::disableQueryLog();
                    $queries = DB::getQueryLog();
                    $logs = json_encode(array(end($queries)));
                    DB::table('updatelogs')
                        ->insert([
                            'type'=>1,
                            'sql'=> $logs,
                            'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                        ]);

                    $studpword = \App\RegistrarModel::generatepassword($studuser);
                    $password = $studpword->code;

                }else{
                    $password = $studuser->passwordstr;
                    $studuser = $studuser->id;
                }

                self::update_studinfo($student->id, $studuser);

                $username = 'S'.$sid;
                $password = $password;

            }

            $parent_account = DB::table('users')
                                ->where('email','P'.$sid)
                                ->where('deleted',0)
                                ->get();

            if(count($parent_account) == 0){
                DB::enableQueryLog();
                DB::table('users')
                    ->insert([
                        'name' => $student->sid,
                        'email' => 'P'.$sid,
                        'type' => 9,
                        'password' => Hash::make('123456')
                    ]);
                DB::disableQueryLog();
                $queries = DB::getQueryLog();
                $logs = json_encode(array(end($queries)));
                DB::table('updatelogs')
                        ->insert([
                            'type'=>1,
                            'sql'=> $logs,
                            'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                        ]);

            }else if(count($parent_account) > 0){
                foreach($parent_account as $key=>$item){
                    if($key != 0){
                          DB::table('users')  
                                ->where('id',$item->id)
                                ->take(1)
                                ->update([
                                      'deleted'=>1
                                ]);
                    }
                }
            }

            $student_acount = DB::table('users')
                                ->where('email','S'.$sid)
                                ->where('deleted',0)
                                ->get();

            $student = DB::table('studinfo')
                        ->where('firstname',strtoupper($request->get('a')))
                        ->where('lastname',strtoupper($request->get('b')))
                        ->where('dob',$request->get('d'))
                        ->where('deleted',0)
                        ->select('sid','userid','id')
                        ->first();

                
            foreach($student_acount as $key=>$item){
                if($item->id != $student->userid){
                        DB::table('users')  
                            ->where('id',$item->id)
                            ->take(1)
                            ->update([
                                    'deleted'=>1
                            ]);
                }
            }

        }else{

            $student = DB::table('studinfo')
                        ->where('firstname',strtoupper($request->get('a')))
                        ->where('lastname',strtoupper($request->get('b')))
                        ->first();

            if(isset($student->lastname)){
                $message = 'Student found but with different date of birth. please proceed to the school registrar to change and update your  information. Thank you.';
            }else{
                $message = 'Student not found. Please proceed to the school registrar to change and update your information. Thank you.';
            }

        }

        $parent_account = DB::table('users')
                            ->where('email','P'.$sid)
                            ->where('deleted',0)
                            ->get();

        $pusername = 'Not Found';
        $ppassword = 'Not Found';

        if(count($parent_account) == 0 && $username != 'Not Found'){
            DB::enableQueryLog();
            $pusers = DB::table('users')
                ->insertGetId([
                    'name' => $sid,
                    'email' => 'P'.$sid,
                    'type' => 9,
                    'password' => Hash::make('123456'),
                    'passwordstr'=> '123456'
                ]);
            DB::disableQueryLog();
            $queries = DB::getQueryLog();
            $logs = json_encode(array(end($queries)));
            DB::table('updatelogs')
                    ->insert([
                        'type'=>1,
                        'sql'=> $logs,
                        'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                    ]);

            $studpword = \App\RegistrarModel::generatepassword($pusers);
            $password = $studpword->code;

            $pusername = 'P'.$sid;
            $ppassword = $password;

        }else if(count($parent_account) != 0 && $username != 'Not Found'){
            $pusername = $parent_account[0]->email;
            if($parent_account[0]->passwordstr == null){
                $studpword = \App\RegistrarModel::generatepassword($parent_account[0]->id);
                $ppassword = $studpword->code;
            }else{
                $ppassword = $parent_account[0]->passwordstr;
            }
        }

        $scontactno = 'No Contact Number';
        $pcontactno = 'No Contact Number';

         if($username != 'Not Found'){
             $contact = DB::table('studinfo')
                             ->where('id',$student->id)
                             ->first();

             if($contact->contactno != null && strlen($contact->contactno) == 11 ){
                 self::send_text('S'.$sid,$contact->contactno,$password,$student->id);
                 $scontactno = $contact->contactno;
             }
             if($contact->isfathernum == 1 && ( $contact->fcontactno != null && strlen($contact->fcontactno) == 11 )){
                 self::send_text('P'.$sid,$contact->fcontactno,$ppassword,$student->id);
                 $pcontactno = $contact->fcontactno;
             }elseif($contact->ismothernum == 1 && ( $contact->mcontactno != null && strlen($contact->mcontactno) == 11 )){
                 self::send_text('P'.$sid,$contact->mcontactno,$ppassword,$student->id);
                 $pcontactno = $contact->mcontactno;
             }elseif($contact->isguardannum == 1 && ( $contact->gcontactno != null && strlen($contact->gcontactno) == 11 )){
                 self::send_text('P'.$sid,$contact->gcontactno,$ppassword,$student->id);
                 $pcontactno = $contact->gcontactno;
             }else{
                 if($contact->fcontactno != null && strlen($contact->fcontactno) == 11){
		             self::send_text('P'.$sid,$contact->fcontactno,$ppassword,$student->id);
                     $scontactno = $contact->fcontactno;
                 }else if($contact->mcontactno != null && strlen($contact->mcontactno) == 11){
                     self::send_text('P'.$sid,$contact->mcontactno,$ppassword,$student->id);
                     $pcontactno = $contact->mcontactno;
                 }else if($contact->gcontactno != null && strlen($contact->gcontactno) == 11){
                    self::send_text('P'.$sid,$contact->gcontactno,$ppassword,$student->id);
                     $pcontactno = $contact->gcontactno;
                 }
             }
         }


         return array((object)[
             'sid'=> $sid,
             'scontactno'=> $scontactno != null ? '********'.substr($scontactno,-3) : null,
             'pcontactno'=> $scontactno != null ? '********'.substr($pcontactno,-3) : null,
             'message'=> $message
         ]);

        return array((object)[
            'sid'=> $sid,
            'username'=> $username ,
            'password'=> $password ,
            'pusername'=> $pusername,
            'ppassword'=> $ppassword,
            'message'=> $message
        ]);
                
    }


function send_text($sid = null, $contact = null, $password = null,$studid = null){

$schoolinfo = DB::table('schoolinfo')->first();
$website_link = str_replace('https://','',$schoolinfo->websitelink);
$website_link = str_replace('http://','',$website_link);

$date = \Carbon\Carbon::now('Asia/Manila')->isoFormat('MMDDYYY');

$message = strtoupper($schoolinfo->abbreviation).' Message:

Visit: '.$website_link.'

Portal Credentials:

Username: '.$sid.
'
Password: '.$password.'

'.$date;

        if($contact != null && strlen($contact) == 11){

            $contactno = '+63' . substr($contact, 1);

            DB::table('tapbunker')
                ->insert([
                    'message'=> $message,
                    'receiver'=>$contactno,
                    'smsstatus'=>0,
                    'createddatetime'=>\Carbon\Carbon::now('Asia/Manila'),
                ]);

        }

    }

    public static function check_infoupdate(Request $request){

        $syid = $request->get('syid');
        $semid = $request->get('semid');

        $levelid = $request->get('gradelevel');
        $studid = DB::table('studinfo')->where('userid',auth()->user()->id)->select('id')->first()->id;

        if($levelid == 14 || $levelid == 15){
            $check_enrollment = DB::table('sh_enrolledstud')   
                                    ->where('studid',$studid)
                                    ->where('syid',$syid)
                                    ->where('deleted',0)
                                    ->count();
        }else{
            $check_enrollment = DB::table('enrolledstud')   
                        ->where('studid',$studid)
                        ->where('syid',$syid)
                        ->where('deleted',0)
                        ->count();
        }

        $check_update_count = [];

        if($check_enrollment == 0){
            $check_update_count = DB::table('student_updateinformation')   
                                ->where('studid',$studid)
                                ->where('syid',$syid)
                                ->where('semid',$semid)
                                ->where('deleted',0)
                                ->get();

            if(count($check_update_count) > 0){
                $check_update_count[0]->createddatetime = \Carbon\Carbon::create($check_update_count[0]->createddatetime)->isoFormat('MMMM DD, YYYY');
            }
            
        }

        return $check_update_count;

    }

    public function processinquiryform(Request $request){


        $codeinformation = DB::table('preregistration')->where('queing_code',$request->get('code'))->get();

        if(count($codeinformation) == 0 || $request->get('code') == null){

            toast('Code not found','error')->autoClose(2000)->toToast($position = 'top-right');
            return back();

        }
        else{

            $onlinepayment = DB::table('onlinepayments')->where('queingcode',$request->get('code'))->get();

            return view('othertransactions.prereginquiry.prereginquiryinfo')
                        ->with('codeinformation',$codeinformation)
                        ->with('onlinepayment',$onlinepayment);

        }

    }

    public function preenrollment(){

        return view('othertransactions.preenrollment.preenrollmentform');

    }

    public function evalpreenrollmentform(Request $request){


        $check_info = DB::table('studinfo')
                        ->where(function($query) use ($request){
                                    $query->where('sid',str_replace('S','',$request->get('a')))
                                    ->orWhere('qcode',$request->get('a'))
                                    ->orWhere('lrn',$request->get('a'));
                        }) 
                        ->where('firstname',strtoupper($request->get('b')))
                        ->where('lastname',strtoupper($request->get('c')))
                        ->where('studinfo.deleted','0')
                        ->get();


        if(count($check_info)){
            return view('othertransactions.preenrollment.logintoyourportal');
        }

        $items = DB::table('items')
                    ->where('isdp','0')
                    ->where('isreceivable','0')
                    ->where('isexpense','0')
                    ->get();


        $prereg = DB::table('preregistration')
                    ->where('first_name',strtoupper($request->get('b')))
                    ->where('last_name',strtoupper($request->get('c')))
                    ->join('gradelevel',function($join){
                        $join->on('preregistration.gradelevelid','=','gradelevel.id');
                        $join->where('gradelevel.deleted','0');
                    })
                    ->where('queing_code',$request->get('a'))
                    ->first();

        if($request->get('a') == null){
            return "NSF";
        }

        if(!isset($prereg->status)){

           
            $studinfo = DB::table('studinfo')
                        ->where(function($query) use ($request){
                                    $query->where('sid',str_replace('S','',$request->get('a')))
                                    ->orWhere('qcode',$request->get('a'))
                                    ->orWhere('lrn',$request->get('a'));
                        }) 
                        ->where('firstname',strtoupper($request->get('b')))
                        ->where('lastname',strtoupper($request->get('c')))
                        ->where('studinfo.deleted','0')
                        ->select('id','lrn','sid','firstname','lastname','qcode','levelid','grantee')
                        ->first();

       

            if(!isset($studinfo->id)){
 
                return "NSF";

            }
            else{

                $prereg = (object)[
                    'queing_code' => $studinfo->qcode,
                    'sid' => $studinfo->sid,
                    'lrn' => $studinfo->lrn,
                    'status'=>'1',
                    'gradelevelid'=>$studinfo->levelid,
                    'grantee'=>$studinfo->grantee
                ];

            }
           
        }
        else{

            $studinfo = DB::table('studinfo')
                            ->where(function($query) use ($request){
                                $query->where('sid',str_replace('S','',$request->get('a')));
                                $query->orWhere('qcode',$request->get('a'));
                                $query->orWhere('lrn',$request->get('a'));
                            })
                            ->where('firstname',strtoupper($request->get('b')))
                            ->where('lastname',strtoupper($request->get('c')))
                            ->where('studinfo.deleted','0')
                            ->select('id','lrn','sid','firstname','lastname','qcode','levelid','createddatetime','grantee')
                            ->first();


            if(isset($studinfo)){
              
                $prereg = (object)[
                    'queing_code' => $prereg->queing_code,
                    'sid' => $studinfo->sid,
                    'lrn' => $studinfo->lrn,
                    'status'=>$prereg->status,
                    'gradelevelid'=>$studinfo->levelid,
                    'date_created'=>$studinfo->createddatetime,
                    'grantee'=>$studinfo->grantee
                ];
                
    
            }else{
                
                $prereg = (object)[
                    'queing_code' => $prereg->queing_code,
                    'sid' => '',
                    'lrn' => '',
                    'status'=>$prereg->status,
                    'gradelevelid'=>$prereg->gradelevelid,
                    'date_created'=>$prereg->date_created,
                    'grantee'=>$prereg->grantee
                ];

            }

            

          
        }

      
        if(isset($prereg->status)){

            $downpayment = DB::table('items')
                                ->join('items_dp',function($join) use($prereg){
                                    $join->on('items.id','=','items_dp.itemid');
                                    $join->where('levelid',$prereg->gradelevelid);
                                    $join->where('items_dp.deleted','0');
                                })
                                ->where('items.deleted','0')
                                ->where('isdp','1')
                                ->get();

            try{
                $downpayment = DB::table('dpsetup')
                        ->where('levelid',$prereg->gradelevelid)
                        ->where('dpsetup.deleted','0')
                        ->groupBy('classid')
                        ->select(
                            DB::raw('SUM(amount) as amount, classid, description, itemid, allowless')
                        )
                        ->get();
            }catch(\Exception $e){}

    

            if($prereg->gradelevelid == 14 || $prereg->gradelevelid == 15){

                $getheader = Db::table('tuitionheader')
                                ->join('sy',function($join){
                                    $join->on('tuitionheader.syid','=','sy.id');
                                    $join->where('sy.isactive','1');
                                })
                                ->join('semester',function($join){
                                    $join->on('tuitionheader.semid','=','semester.id');
                                    $join->where('semester.isactive','1');
                                })
                                ->where('grantee',$prereg->grantee)
                                ->select('tuitionheader.*')
                                ->where('levelid', $prereg->gradelevelid)
                                ->where('tuitionheader.deleted','0')
                                ->get();

            }
            else{

                $getheader = Db::table('tuitionheader')
                                ->join('sy',function($join){
                                    $join->on('tuitionheader.syid','=','sy.id');
                                    $join->where('sy.isactive','1');
                                })
                                ->where('grantee',$prereg->grantee)
                                ->where('levelid', $prereg->gradelevelid)
                                ->where('tuitionheader.deleted','0')
                                ->select('tuitionheader.*')
                                ->get();

            }
          
            if(count($getheader)>0){

                $getpayables = Db::table('tuitiondetail')
                                    ->join('itemclassification','tuitiondetail.classificationid','=','itemclassification.id')
                                    ->where('tuitiondetail.headerid', $getheader[0]->id)
                                    ->where('tuitiondetail.deleted','0')
                                    ->where('itemclassification.deleted','0')
                                    ->get();
            }
            else {

                $getpayables = array();
    
            }

   



            if($prereg->status == 0){

                
                $studinfo = DB::table('preregistration')
                                ->where('first_name',strtoupper($request->get('b')))
                                ->where('last_name',strtoupper($request->get('c')))
                                ->where('queing_code',$prereg->queing_code)
                                ->join('gradelevel',function($join){
                                    $join->on('preregistration.gradelevelid','=','gradelevel.id');
                                    $join->where('gradelevel.deleted','=','0');
                                })
                                ->select(
                                    'preregistration.*',
                                    'gradelevel.levelname',
                                    'gradelevel.acadprogid'
                                    )
                                ->first();

                if($studinfo->acadprogid != 5 && $studinfo->acadprogid != 6){

                    $onlinepayment = DB::table('onlinepayments')
                                            ->where('queingcode',$prereg->queing_code)
                                            ->orwhere('queingcode',$prereg->sid)
                                            ->orwhere('queingcode',$prereg->lrn)
                                            ->orderBy('paymentDate','desc')
                                            ->select('onlinepayments.*')
                                            ->get();


                    $totalDP = DB::table('onlinepayments')
                                        ->join('onlinepaymentdetails',function($join){
                                            $join->on('onlinepaymentdetails.headerid','=','onlinepayments.id');
                                        })
                                        ->join('items',function($join){
                                            $join->on('onlinepaymentdetails.payscheddetailid','=','items.id');
                                            $join->where('items.isdp','1');
                                        })
                                        ->join('sy',function($join){
                                            $join->on('onlinepayments.syid','=','sy.id');
                                            $join->where('sy.isactive','1');
                                        })
                                        ->where(function($query) use($prereg){
                                            $query->where('queingcode',$prereg->queing_code);
                                            $query->orwhere('queingcode',$prereg->sid);
                                            $query->orwhere('queingcode',$prereg->lrn);
                                        })
                                        ->where('paykind','1')
                                        ->whereNotIN('isapproved',['2',3])
                                        ->sum('onlinepayments.amount');
 
                    $countPending = DB::table('onlinepayments')
                                    ->where(function($query) use($prereg){
                                        $query->where('queingcode',$prereg->queing_code);
                                        $query->orwhere('queingcode',$prereg->sid);
                                        $query->orwhere('queingcode',$prereg->lrn);
                                    })
                                    ->whereNotIN('isapproved',['2',3])
                                    ->join('sy',function($join){
                                        $join->on('onlinepayments.syid','=','sy.id');
                                        $join->where('sy.isactive','1');
                                    })
                                    ->count();

                }else{

                    $onlinepayment = DB::table('onlinepayments')
                                        ->where('queingcode',$prereg->queing_code)
                                        ->orwhere('queingcode',$prereg->sid)
                                        ->orwhere('queingcode',$prereg->lrn)
                                        ->orderBy('paymentDate','desc')
                                        ->select('onlinepayments.*')
                                        ->get();


                    $totalDP = DB::table('onlinepayments')
                                    ->join('onlinepaymentdetails',function($join){
                                        $join->on('onlinepaymentdetails.headerid','=','onlinepayments.id');
                                    })
                                    ->join('items',function($join){
                                        $join->on('onlinepaymentdetails.payscheddetailid','=','items.id');
                                        $join->where('items.isdp','1');
                                    })
                                    ->join('sy',function($join){
                                        $join->on('onlinepayments.syid','=','sy.id');
                                        $join->where('sy.isactive','1');
                                    })
                                    ->join('semester',function($join){
                                        $join->on('onlinepayments.semid','=','semester.id');
                                        $join->where('semester.isactive','1');
                                    })
                                    ->where(function($query) use($prereg){
                                        $query->where('queingcode',$prereg->queing_code);
                                        $query->orwhere('queingcode',$prereg->sid);
                                        $query->orwhere('queingcode',$prereg->lrn);
                                    })
                                    ->where('paykind','1')
                                    ->whereNotIN('isapproved',['2',3])
                                    ->sum('onlinepayments.amount');

                    $countPending = DB::table('onlinepayments')
                                ->where(function($query) use($prereg){
                                    $query->where('queingcode',$prereg->queing_code);
                                    $query->orwhere('queingcode',$prereg->sid);
                                    $query->orwhere('queingcode',$prereg->lrn);
                                })
                                ->whereNotIN('isapproved',['2',3])
                                ->join('sy',function($join){
                                    $join->on('onlinepayments.syid','=','sy.id');
                                    $join->where('sy.isactive','1');
                                })
                                ->join('semester',function($join){
                                    $join->on('onlinepayments.semid','=','semester.id');
                                    $join->where('semester.isactive','1');
                                })
                                ->count();
                }


                $early_enrollment_payment_setup = [];
                $with_early_enrollment = false;

               

                return view('othertransactions.preenrollment.levelstatus')
                            ->with('onlinepayment',$onlinepayment)
                            ->with('getpayables',$getpayables)
                            ->with('prereg',$prereg)
                            ->with('items',$items)
                            ->with('totalDP',$totalDP)
                            ->with('downpayment',$downpayment)
                            ->with('countPending',$countPending)
                            ->with('early_enrollment_payment_setup',$early_enrollment_payment_setup)
                            ->with('with_early_enrollment',$with_early_enrollment)
                            ->with('status',2);

            }
            else{

                $studinfo = DB::table('studinfo')
                                ->where('firstname',strtoupper($request->get('b')))
                                ->where('lastname',strtoupper($request->get('c')))
                                ->where('sid',$prereg->sid)
                                ->join('gradelevel',function($join){
                                    $join->on('studinfo.levelid','=','gradelevel.id');
                                    $join->where('gradelevel.deleted','=','0');
                                })
                                ->join('studentstatus',function($join){
                                    $join->on('studinfo.studstatus','=','studentstatus.id');
                                    $join->where('gradelevel.deleted','=','0');
                                })
                                ->where('studinfo.deleted','0')
                                ->select(
                                    'studinfo.*',
                                    'gradelevel.levelname',
                                    'gradelevel.acadprogid',
                                    'studentstatus.description'
                                    )
                                ->first();

                if($studinfo->acadprogid != 5 && $studinfo->acadprogid != 6){

                    $onlinepayment = DB::table('onlinepayments')
                                        ->where('queingcode',$prereg->queing_code)
                                        ->orwhere('queingcode',$prereg->sid)
                                        ->orwhere('queingcode',$prereg->lrn)
                                        ->orderBy('paymentDate','desc')
                                        ->select('onlinepayments.*')
                                        ->get();


                    $countPending = DB::table('onlinepayments')
                            ->where(function($query) use($prereg){
                                $query->where('queingcode',$prereg->queing_code);
                                $query->orwhere('queingcode',$prereg->sid);
                                $query->orwhere('queingcode',$prereg->lrn);
                            })
                            ->whereNotIN('isapproved',['2',3])
                            ->join('sy',function($join){
                                $join->on('onlinepayments.syid','=','sy.id');
                                $join->where('sy.isactive','1');
                            })
                            ->count();

                }
                else{

                   
                    $onlinepayment = DB::table('onlinepayments')
                                ->where('queingcode',$prereg->queing_code)
                                ->orwhere('queingcode',$prereg->sid)
                                ->orwhere('queingcode',$prereg->lrn)
                                ->orderBy('paymentDate','desc')
                                ->select('onlinepayments.*')
                                ->get();

                    $countPending = DB::table('onlinepayments')
                                    ->where(function($query) use($prereg){
                                        $query->where('queingcode',$prereg->queing_code);
                                        $query->orwhere('queingcode',$prereg->sid);
                                        $query->orwhere('queingcode',$prereg->lrn);
                                    })
                                    ->whereNotIN('isapproved',['2',3])
                                    ->join('sy',function($join){
                                        $join->on('onlinepayments.syid','=','sy.id');
                                        $join->where('sy.isactive','1');
                                    })
                                    ->join('semester',function($join){
                                        $join->on('onlinepayments.semid','=','semester.id');
                                        $join->where('semester.isactive','1');
                                    })
                                    ->count();


                }

                $assessment =  Billing::remBill($prereg);

                if($studinfo->acadprogid != 5 && $studinfo->acadprogid != 6){


                    $totalDP = DB::table('onlinepayments')
                                ->join('onlinepaymentdetails',function($join){
                                    $join->on('onlinepaymentdetails.headerid','=','onlinepayments.id');
                                })
                                ->join('items',function($join){
                                    $join->on('onlinepaymentdetails.payscheddetailid','=','items.id');
                                    $join->where('items.isdp','1');
                                })
                                ->join('sy',function($join){
                                    $join->on('onlinepayments.syid','=','sy.id');
                                    $join->where('sy.isactive','1');
                                })
                                ->where(function($query) use($prereg){
                                    $query->where('queingcode',$prereg->queing_code);
                                    $query->orwhere('queingcode',$prereg->sid);
                                    $query->orwhere('queingcode',$prereg->lrn);
                                })
                                ->where('paykind','1')
                                ->whereNotIN('isapproved',['2',3])
                                ->sum('onlinepayments.amount');

                    $enrollmentDetail = DB::table('enrolledstud')
                                            ->where('studid',$studinfo->id)
                                            ->join('sy',function($join){
                                                $join->on('enrolledstud.syid','=','sy.id');
                                                $join->where('sy.isactive','1');
                                            })
                                            ->join('gradelevel',function($join){
                                                $join->on('enrolledstud.levelid','=','gradelevel.id');
                                                $join->where('gradelevel.deleted','=','0');
                                            })
                                            ->join('sections',function($join){
                                                $join->on('enrolledstud.sectionid','=','sections.id');
                                                $join->where('sections.deleted','=','0');
                                            })
                                            // ->where('enrolledstud.levelid',$studinfo->levelid)
                                            ->first();

                }
                else{


                    $totalDP = DB::table('onlinepayments')
                                ->join('onlinepaymentdetails',function($join){
                                    $join->on('onlinepaymentdetails.headerid','=','onlinepayments.id');
                                })
                                ->join('items',function($join){
                                    $join->on('onlinepaymentdetails.payscheddetailid','=','items.id');
                                    $join->where('items.isdp','1');
                                })
                                ->join('sy',function($join){
                                    $join->on('onlinepayments.syid','=','sy.id');
                                    $join->where('sy.isactive','1');
                                })
                                ->join('semester',function($join){
                                    $join->on('onlinepayments.semid','=','semester.id');
                                    $join->where('semester.isactive','1');
                                })
                                ->where(function($query) use($prereg){
                                    $query->where('queingcode',$prereg->queing_code);
                                    $query->orwhere('queingcode',$prereg->sid);
                                    $query->orwhere('queingcode',$prereg->lrn);
                                })
                                ->where('paykind','1')
                                ->whereNotIN('isapproved',['2',3])
                                ->sum('onlinepayments.amount');

                    $enrollmentDetail = DB::table('sh_enrolledstud')
                                            ->where('studid',$studinfo->id)
                                            ->join('sy',function($join){
                                                $join->on('sh_enrolledstud.syid','=','sy.id');
                                                $join->where('sy.isactive','1');
                                            })
                                            ->join('semester',function($join){
                                                $join->on('sh_enrolledstud.semid','=','semester.id');
                                                $join->where('semester.isactive','1');
                                            })
                                            ->join('gradelevel',function($join){
                                                $join->on('sh_enrolledstud.levelid','=','gradelevel.id');
                                                $join->where('gradelevel.deleted','=','0');
                                            })
                                            ->join('sections',function($join){
                                                $join->on('sh_enrolledstud.sectionid','=','sections.id');
                                                $join->where('sections.deleted','=','0');
                                            })
                                            // ->where('sh_enrolledstud.levelid',$studinfo->levelid)
                                            ->first();
                }


                if($studinfo->acadprogid != 5 && $studinfo->acadprogid != 6){

                    $balforwardSetup = DB::table('balforwardsetup')
                                            ->join('sy',function($join){
                                                $join->on('balforwardsetup.syid','=','sy.id');
                                                $join->where('sy.isactive','1');
                                            })
                                            ->first();

                }
                else{

                    $balforwardSetup = DB::table('balforwardsetup')
                                            ->join('sy',function($join){
                                                $join->on('balforwardsetup.syid','=','sy.id');
                                                $join->where('sy.isactive','1');
                                            })
                                            ->join('semester',function($join){
                                                $join->on('balforwardsetup.semid','=','semester.id');
                                                $join->where('semester.isactive','1');
                                            })
                                            ->first();
                }
        

                $balancforwarded = (object)[];

                if(isset($balforwardSetup->classid)){
                    if($studinfo->acadprogid != 5 && $studinfo->acadprogid != 6){

                        $balancforwarded = DB::table('studledger')
                                        ->where('studid',$studinfo->id)
                                        ->join('sy',function($join){
                                            $join->on('studledger.syid','=','sy.id');
                                            $join->where('sy.isactive','1');
                                        })
                                        ->where('studledger.deleted',0)
                                        ->where('classid',$balforwardSetup->classid)
                                        ->select('particulars','amount')
                                        ->first();

                    }
                    else{

                        $balancforwarded = DB::table('studledger')
                                    ->where('studid',$studinfo->id)
                                    ->join('sy',function($join){
                                        $join->on('studledger.syid','=','sy.id');
                                        $join->where('sy.isactive','1');
                                    })
                                    ->join('semester',function($join){
                                        $join->on('studledger.semid','=','semester.id');
                                        $join->where('semester.isactive','1');
                                    })
                                    ->where('classid',$balforwardSetup->classid)
                                    ->where('studledger.deleted',0)
                                    ->select('particulars','amount')
                                    ->first();

                    }
                }

                $completeDP = false;
                $overAllDP = 0.00;
                $sumsubmittedOnlineDP = 0;

                if($studinfo->preEnrolled == 1 ){

                    if(count($downpayment) != 0){

                        $overAllDP += $downpayment[0]->amount;
                    }

                    if(isset($balancforwarded->amount)){

                        $overAllDP += $balancforwarded->amount;

                    }
                  

                    

                    $submittedOnlineDP = DB::table('onlinepayments')
                                                ->where(function($query) use($prereg){
                                                    $query->where('queingcode',$prereg->queing_code);
                                                    $query->orwhere('queingcode',$prereg->sid);
                                                    $query->orwhere('queingcode',$prereg->lrn);
                                                })
                                                ->join('sy',function($join){
                                                    $join->on('onlinepayments.syid','=','sy.id');
                                                    $join->where('sy.isactive','1');
                                                });

                    if($studinfo->acadprogid == 5 || $studinfo->acadprogid == 6){

                        $submittedOnlineDP->join('semester',function($join){
                                            $join->on('onlinepayments.semid','=','semester.id');
                                            $join->where('semester.isactive','1');
                                        });

                    }


                    $submittedOnlineDP->whereNotIN('isapproved',['2',3])
                                        ->where('paykind','1')
                                        ->join('onlinepaymentdetails',function($join){
                                            $join->on('onlinepaymentdetails.headerid','=','onlinepayments.id');
                                        });


                   $sumsubmittedOnlineDP = $submittedOnlineDP->sum('onlinepayments.amount');


                   //On premes downpayment
                   $onPremesTrans = DB::table('chrngtransdetail')
                                    ->join('chrngtrans',function($join){
                                        $join->on('chrngtransdetail.chrngtransid','=','chrngtrans.id');
                                    })
                                    ->join('items',function($join){
                                        $join->on('chrngtransdetail.payschedid','=','items.id');
                                    })
                                    ->join('sy',function($join){
                                        $join->on('chrngtrans.syid','=','sy.id');
                                        $join->where('sy.isactive','1');
                                    });

                    if($studinfo->acadprogid == 5 || $studinfo->acadprogid == 6){

                        $onPremesTrans->join('semester',function($join){
                                            $join->on('chrngtrans.semid','=','semester.id');
                                            $join->where('semester.isactive','1');
                                        });

                    }

                    $onPremesTransDP = $onPremesTrans->where('itemkind','1')
                                            ->where('isdp','1')
                                            ->where('chrngtrans.studid',$studinfo->id)
                                            ->sum('chrngtransdetail.amount');

                    $sumsubmittedOnlineDP += $onPremesTransDP;

                    //On premes balanceforward
                    if(isset($balancforwarded->amount)){

                        $onPremesTrans = DB::table('chrngtransdetail')
                                        ->join('chrngtrans',function($join){
                                            $join->on('chrngtransdetail.chrngtransid','=','chrngtrans.id');
                                        })
                                        ->join('sy',function($join){
                                            $join->on('chrngtrans.syid','=','sy.id');
                                            $join->where('sy.isactive','1');
                                        });

                        if($studinfo->acadprogid == 5 || $studinfo->acadprogid == 6){

                            $onPremesTrans->join('semester',function($join){
                                                $join->on('chrngtrans.semid','=','semester.id');
                                                $join->where('semester.isactive','1');
                                            });

                        }

                        $onPremesTransBF = $onPremesTrans
                                                ->where('chrngtrans.studid',$studinfo->id)
                                                ->where('chrngtransdetail.classid',$balforwardSetup->classid)
                                   
                                                ->sum('chrngtransdetail.amount');

                        $sumsubmittedOnlineDP += $onPremesTransBF;

                    }

                    if($sumsubmittedOnlineDP >= $overAllDP){

                            $completeDP = true;

                    }

                }

                $isEnrolled = false;

                if( isset($enrollmentDetail->id)){
             
                    $isEnrolled = true;
                    
                }

                // return collect($studinfo);

                $enrollmnet_setup = \App\Models\EnrollmentSetup\EnrollmentSetupData::check_enrollment_setup(null,$studinfo->acadprogid);
                $with_early_enrollment = false;
                $early_enrollment = array();
                $early_enrollment_payment_setup = [];
                $early_enrollment = [];
                $early_enrollment_payment = [];

                $active_sy = DB::table('sy')->where('isactive',1)->first();
                $active_sem = DB::table('semester')->where('isactive',1)->first();

                
                $onlinepayment = DB::table('onlinepayments')
                                ->where('queingcode',$prereg->queing_code)
                                ->join('onlinepaymentdetails',function($join){
                                    $join->on('onlinepayments.id','=','onlinepaymentdetails.headerid');
                                    $join->where('onlinepaymentdetails.deleted',0);
                                })
                                ->orwhere('queingcode',$prereg->sid)
                                ->orwhere('queingcode',$prereg->lrn)
                                ->orderBy('paymentDate','desc')
                                ->select(
                                    'onlinepayments.*',
                                    'onlinepaymentdetails.classid'
                                )
                                ->get();

                $early_enrollment_payment_setup = DB::table('chrng_earlyenrollmentsetup')
                                ->join('items',function($join){
                                    $join->on('chrng_earlyenrollmentsetup.itemid','=','items.id');
                                    $join->where('items.deleted',0);
                                })
                                ->select(
                                    'chrng_earlyenrollmentsetup.classid', 
                                    'amount', 
                                    'items.description', 
                                    'itemid'
                                )
                                ->get();
                                
             

                if($enrollmnet_setup[0]->status == 1){

                    $temp_enrollment_setup = $enrollmnet_setup[0]->data;
            
                    $with_early_enrollment = true;
                 
                    if($temp_enrollment_setup[0]->type == 2){
                        
                        $early_enrollment = DB::table('earlybirds')
                                                ->join('sy',function($join){
                                                    $join->on('earlybirds.syid','=','sy.id');
                                                })
                                                ->where('earlybirds.studid',$studinfo->id)
                                                ->where('earlybirds.deleted',0)
                                                ->where('earlybirds.syid',$temp_enrollment_setup[0]->syid)
                                                ->where('earlybirds.semid',$temp_enrollment_setup[0]->semid)
                                                ->select('earlybirds.*','sydesc')
                                                ->get();
                        
                                    
                    }

                   
                    $enrollmnet_setup = $enrollmnet_setup[0]->data;

                }else if($enrollmnet_setup[0]->status == 0){

                    if(count($enrollmnet_setup[0]->data) > 0){

                        $temp_enrollment_setup = $enrollmnet_setup[0]->data;

                        $early_enrollment = DB::table('earlybirds')
                                                ->join('sy',function($join){
                                                    $join->on('earlybirds.syid','=','sy.id');
                                                })
                                                ->where('earlybirds.studid',$studinfo->id)
                                                ->where('earlybirds.deleted',0)
                                                ->where('earlybirds.syid',$temp_enrollment_setup[0]->syid)
                                                ->where('earlybirds.semid',$temp_enrollment_setup[0]->semid)
                                                ->select('earlybirds.*','sydesc')
                                                ->get();
                    }

                }



                if(count($early_enrollment_payment_setup) > 0){
                    $early_enrollment_payment = collect($onlinepayment)->where('classid',$early_enrollment_payment_setup[0]->classid);
                    $onlinepayment =  collect($onlinepayment)->where('classid','!=',$early_enrollment_payment_setup[0]->classid);

                }
              
               
             

                return view('othertransactions.preenrollment.levelstatus')
                                ->with('studinfo',$studinfo)
                                ->with('isEnrolled',$isEnrolled)
                                ->with('completeDP',$completeDP)
                                ->with('overAllDP',$overAllDP)
                                ->with('sumsubmittedOnlineDP',$sumsubmittedOnlineDP)
                                ->with('assessment',$assessment)
                                ->with('getpayables',$getpayables)
                                ->with('enrollmentDetail',$enrollmentDetail)
                                ->with('onlinepayment',$onlinepayment)
                                ->with('countPending',$countPending)
                                ->with('totalDP',$totalDP)
                                ->with('downpayment',$downpayment)
                                ->with('balancforwarded',$balancforwarded)
                                ->with('items',$items)
                                ->with('enrollmnet_setup',$enrollmnet_setup)
                                ->with('early_enrollment',$early_enrollment)
                                ->with('early_enrollment_payment',$early_enrollment_payment)
                                ->with('with_early_enrollment',$with_early_enrollment)
                                ->with('early_enrollment_payment_setup',$early_enrollment_payment_setup)
                                ->with('status',1)
                                ->with('active_sy',$active_sy);


            }
            
        }
        else{

            return "sdfdsf";
           
            return view('othertransactions.preenrollment.levelstatus')->with('status',3);

        }

                    
    }


    public function precesspreenrollment($studentSid, $studentName){


        $studentName = explode('-pe-',$studentName);

        $firstName = strtoupper(str_replace('-',' ',$studentName[0]));
        $lastName = strtoupper(str_replace('-',' ',$studentName[1]));



        $studinfo = DB::table('studinfo')
                        ->where('sid',str_replace('S','',$studentSid))
                        ->where('firstname',$firstName)
                        ->where('lastname',$lastName)
                        ->first();

        DB::table('studinfo')
            ->where('id',$studinfo->id)
            ->update([
                'preEnrolled'=>'1'
            ]);             

        return "1";

    }


    public function monthPayable($studid, $month){

        $studid = DB::table('studinfo')
                        ->where('sid',$studid)
                        ->orWhere('qcode',$studid)
                        ->orWhere('lrn',$studid)
                        ->first();

                        

        $over = self::getOver($studid);

       

        $billdet = DB::table('studpayscheddetail')
                        ->where('studid',$studid->id)
                        ->where('deleted','0')
                        ->join('sy',function($join){
                            $join->on('studpayscheddetail.syid','=','sy.id');
                            $join->where('isactive','1');
                        })
                        ->whereMonth('duedate',$month)
                        ->select(
                            'studpayscheddetail.classid',
                            'studpayscheddetail.id',
                            'studpayscheddetail.particulars',
                            'studpayscheddetail.duedate',
                            'amount',
                            'balance'
                        )
                        ->orderBy('duedate','asc')
                        ->get();

        if(count($over) > 0){
            foreach($billdet as $key=>$item){
                if( $over[0]->over >= $item->balance){

                    $over[0]->over = (float) $over[0]->over - $item->balance;
                    $item->balance = 0;
                }
                else{   
                    if($month == $over[0]->month){
                        $item->balance = (float) $item->balance - $over[0]->over;
                    }
                    $over[0]->over = 0;
                }
            }
        }

        return view('othertransactions.preenrollment.payableinfotable')->with('billdet',$billdet);

    }

    public function getOver($studentid){

        $onlinePayments = DB::table('onlinepayments')
                            ->where(function($query) use($studentid){
                                $query->where('queingcode',$studentid->sid);
                                $query->orWhere('queingcode',$studentid->qcode);
                            })
                            ->join('onlinepaymentdetails',function($join){
                                $join->on('onlinepayments.id','=','onlinepaymentdetails.headerid');
                                $join->where('onlinepaymentdetails.deleted','0');
                                $join->where('onlinepaymentdetails.paykind','2');
                            })
                            ->join('sy',function($join){
                                $join->on('onlinepayments.syid','=','sy.id');
                                $join->where('sy.isactive','1');
                            })
                            ->join('semester',function($join){
                                $join->on('onlinepayments.semid','=','semester.id');
                                $join->where('semester.isactive','1');
                            })
                            ->select(
                                'onlinepayments.amount',
                                'onlinepaymentdetails.amount as detailamount',
                                'onlinepaymentdetails.tuitionMonth',
                                'onlinepaymentdetails.headerid'
                            )
                            ->get();

        $billdet = DB::table('studpayscheddetail')
                  ->where('studid',$studentid->id)
                  ->where('deleted','0')
                  ->join('sy',function($join){
                        $join->on('studpayscheddetail.syid','=','sy.id');
                        $join->where('isactive','1');
                    })
                  ->groupBy(DB::raw("MONTH(duedate)"))
                  ->select(
                    'studpayscheddetail.classid',
                        'studpayscheddetail.id',
                        'studpayscheddetail.particulars',
                        'studpayscheddetail.duedate',
                        DB::raw("SUM(amountpay) as amountpay"),
                        DB::raw("SUM(amount) as amountdue"),
                        DB::raw("SUM(balance) as balance")
                    )
                    ->orderBy('duedate','asc')
                    ->get();


        $returnedOver = 0;

        if(count($onlinePayments) > 0){

            $over = collect($onlinePayments)->unique('headerid')->sum('amount');

            foreach($billdet as $key=>$item){

                if($item->balance == 0){

                    $billdet->pull($key);

                }
            
                $matched = collect($onlinePayments)->where('tuitionMonth',Carbon::create($item->duedate)->isoFormat('M'))->toArray();

                if(count($matched) > 0){

                    if( number_format($over, 2 , '.' , '') >= number_format($item->balance, 2 , '.' , '') ){

                        $over = (float) $over - $item->balance;
                        $billdet->pull($key);
                       
                    }
                    else{

                        $item->balance = (float) $item->balance - $over;
                        // $over = 0;

                        $data = array((object)[
                            'over'  => $over,
                            'month' => Carbon::create($item->duedate)->isoFormat('M')
                        ]);

                        return $data;

                    }

                }
                else{

                    if( number_format($over, 2 , '.' , '') >= number_format($item->balance, 2 , '.' , '') ){

                        $over = (float) $over - $item->balance;
                        $billdet->pull($key);
                       
                    }
                    else{

                        $data = array((object)[
                            'over'  => $over,
                            'month' => Carbon::create($item->duedate)->isoFormat('M')
                        ]);

                        return $data;

                    }

                }

                
            }

        }
        else{

            $data = array((object)[
                'over'  => 0,
                'month' => 0
            ]);

            return $data;

        }

        

    }

    public function cancelpayment($sid, $transid){

        $studinfo = DB::table('studinfo')
                    ->where('sid',$sid)
                    ->orWhere('qcode',$sid)
                    ->orWhere('lrn',$sid)
                    ->first();




        if(isset($studinfo->sid)){

            DB::table('onlinepayments')
                    ->where(function($query) use($studinfo){
                        $query->where('queingcode',$studinfo->sid);
                        $query->orWhere('queingcode',$studinfo->qcode);
                        $query->orWhere('queingcode',$studinfo->lrn);
                    })
                    ->join('sy',function($join){
                        $join->on('onlinepayments.syid','=','sy.id');
                        $join->where('sy.isactive','1');
                    })
                    ->join('semester',function($join){
                        $join->on('onlinepayments.semid','=','semester.id');
                        $join->where('semester.isactive','1');
                    })
                    ->where('onlinepayments.id', $transid)
                    ->update([
                        'isapproved'=>'3'
                    ]);



        }
        else{


            $studinfo = DB::table('preregistration')
                            ->where('queing_code',$sid)
                            ->first();

            if(isset($studinfo->id)){

                DB::table('onlinepayments')
                        ->where('queingcode',$studinfo->queing_code)
                        ->where('onlinepayments.id', $transid)
                        ->join('sy',function($join){
                            $join->on('onlinepayments.syid','=','sy.id');
                            $join->where('sy.isactive','1');
                        })
                        ->join('semester',function($join){
                            $join->on('onlinepayments.semid','=','semester.id');
                            $join->where('semester.isactive','1');
                        })
                        ->update([
                            'isapproved'=>'3'
                        ]);

            }

        }

    }

    public function viewpaymentinfo($sid, $transid){

        $studinfo = DB::table('studinfo')
                    ->where('sid',$sid)
                    ->orWhere('qcode',$sid)
                    ->orWhere('lrn',$sid)
                    ->first();

        if(isset($studinfo->sid)){

            $onlinepayments = DB::table('onlinepayments')
                        ->where(function($query) use($studinfo){
                            $query->where('queingcode',$studinfo->sid);
                            $query->orWhere('queingcode',$studinfo->qcode);
                            $query->orWhere('queingcode',$studinfo->lrn);
                        })
                        ->join('onlinepaymentdetails',function($join){
                            $join->on('onlinepayments.id','=','onlinepaymentdetails.headerid');
                        })
                        ->join('sy',function($join){
                            $join->on('onlinepayments.syid','=','sy.id');
                            $join->where('sy.isactive','1');
                        })
                        ->join('semester',function($join){
                            $join->on('onlinepayments.semid','=','semester.id');
                            $join->where('semester.isactive','1');
                        })
                        ->select(
                            'onlinepayments.*',
                            'onlinepaymentdetails.description',
                            'onlinepaymentdetails.amount as descriptAmount',
                            'onlinepaymentdetails.paykind')
                        ->where('onlinepayments.id', $transid)
                        ->get();

            

            return view('othertransactions.preenrollment.paymentInformation')
                        ->with('onlinepayments',$onlinepayments);


        }
        else{

            $studinfo = DB::table('preregestration')
                            ->where('queing_code',$sid)
                            ->first();

            if(isset($studinfo->id)){

                DB::table('onlinepayments')
                        ->where('queingcode',$studinfo->queing_code)
                        ->where('onlinepayments.id', $transid)
                        ->join('sy',function($join){
                            $join->on('onlinepayments.syid','=','sy.id');
                            $join->where('sy.isactive','1');
                        })
                        ->join('semester',function($join){
                            $join->on('onlinepayments.semid','=','semester.id');
                            $join->where('semester.isactive','1');
                        })
                        ->update([
                            'isapproved'=>'3'
                        ]);

            }

        }



    }

    public function preenrollmentget($studid,$studdob,$infotype){

        if($infotype == 1){
            $studinfo = DB::table('studinfo')
                            ->where('sid',$studid)
                            ->where('dob',$studdob)
                            ->where('deleted',0)
                            ->first();
        }
        elseif($infotype == 2){
            $studinfo = DB::table('studinfo')
                            ->where('lrn',$studid)
                            ->where('dob',$studdob)
                            ->where('deleted',0)
                            ->first();


        }

        if(isset($studinfo->id)){
            if($studinfo->studstatus == 1 || $studinfo->preEnrolled == 1){
                return array ( (object)[
                    'status'=>'2',
                    'studinfo'=>$studinfo
                ]);
            }
            else{
                return array ( (object)[
                    'status'=>'1',
                    'studinfo'=>$studinfo
                ]);
            }
        }
        else{

          
            return array ( (object)[
                'status'=>'0'
            ]);
        }
    }

    public function getpaymentreceipt($chrngtransid){

        $data = explode(" ",$chrngtransid);

        // return Terbilang::make(1000000);
        // return $data;

        $receiptInfo = DB::table('onlinepayments')
                    ->where('onlinepayments.id',$data[0])
                    ->where('onlinepayments.chrngtransid',$data[1])
                    ->join('chrngtrans',function($join){
                        $join->on('onlinepayments.chrngtransid','=','chrngtrans.id');
                    })
                    ->join('chrngtransdetail',function($join){
                        $join->on('chrngtrans.id','=','chrngtransdetail.chrngtransid');
                    })
                    ->join('sy',function($join){
                        $join->on('onlinepayments.syid','=','sy.id');
                        $join->where('sy.isactive','1');
                    })
                    ->join('semester',function($join){
                        $join->on('onlinepayments.semid','=','semester.id');
                        $join->where('semester.isactive','1');
                    })
                    ->join('users',function($join){
                        $join->on('chrngtrans.transby','=','users.id');
                    })
                    ->get();

        return view('othertransactions.preenrollment.preregpaymentreceipt')
                    ->with('receiptInfo',$receiptInfo);

    }

    public function payment(){

        // resources\views\enrollment\payment\payment.blade.php

        return view('enrollment.payment.payment');

    }


    public function evaluate(Request $request){

        /*
            |--------------------------------------------------------------------------
            | Enrollment Status
            |--------------------------------------------------------------------------
            |
            | 1. old students
            | 2. new students without payment
            | 3. preEnrolled without payment
            | 4. payment not available
            | 5. with online payments
            | 6. Not Found
            | 7. Enrolled
            | 8. Not preEnrolled
        */

        $withInfo = true;
        $registered = false;
        $data = array((object)[
            'onlinepayment'=>null,
            'enrollmentstatus'=>null,
            'downpayment'=>null,
        ]);

        $downpayment = null;
        $enrolled = false;
        $withdp = true;
        $withonlinepayments = false;
        $preEnrolled = true;

        $prereg = DB::table('preregistration')
                    ->where('first_name',strtoupper($request->get('b')))
                    ->where('last_name',strtoupper($request->get('c')))
                    ->join('gradelevel',function($join){
                        $join->on('preregistration.gradelevelid','=','gradelevel.id');
                        $join->where('gradelevel.deleted','0');
                    })
                    ->where('queing_code',$request->get('a'))
                    ->first();
   
        if(!isset($prereg->status)){
         
            $studinfo = DB::table('studinfo')
                        ->where(function($query) use ($request){
                                    $query->where('sid',str_replace('S','',$request->get('a')))
                                    ->orWhere('qcode',$request->get('a'))
                                    ->orWhere('lrn',$request->get('a'));
                        }) 
                        ->where('firstname',strtoupper($request->get('b')))
                        ->where('lastname',strtoupper($request->get('c')))
                        ->where('studinfo.deleted','0')
                        ->select(
                            'id',
                            'lrn',
                            'sid',
                            'firstname',
                            'lastname',
                            'qcode',
                            'levelid',
                            'grantee',
                            'preEnrolled',
                            'studstatus'
                            )
                        ->first();

            if(!isset($studinfo->id)){
 
                $withInfo = false;

                $data = array((object)[
                    'enrollmentstatus'=>'6'
                ]);
                
                return self::goTopaymentpage2blade($data);

            }
            else{

                $registered = true;

                // $data = array((object)[
                //     'enrollmentstatus'=>'7'
                // ]);

                // return self::goTopaymentpage2blade($data);

            }
           
            
        }
        else{

            $studinfo = DB::table('studinfo')
                            ->where(function($query) use ($request){
                                $query->where('sid',str_replace('S','',$request->get('a')));
                                $query->orWhere('qcode',$request->get('a'));
                                $query->orWhere('lrn',$request->get('a'));
                            })
                            ->where('firstname',strtoupper($request->get('b')))
                            ->where('lastname',strtoupper($request->get('c')))
                            ->where('studinfo.deleted','0')
                            ->select(
                                'id',
                                'lrn',
                                'sid',
                                'firstname',
                                'lastname',
                                'qcode',
                                'levelid',
                                'createddatetime',
                                'grantee',
                                'preEnrolled',
                                'studstatus'
                                )
                            ->first();


            if(isset($studinfo->id)){

                $registered = true;

                // $data = array((object)[
                //     'enrollmentstatus'=>'7'
                // ]);

                // return self::goTopaymentpage2blade($data);
    
            }

        }

        if($withInfo){

            if($registered){

                if( $studinfo->preEnrolled == 0 && $studinfo->studstatus == 0){

                    $preEnrolled = false;

                    $data = array((object)[
                        'enrollmentstatus'=>'8'
                    ]);
                    
                    return self::goTopaymentpage2blade($data);
    
                }
                elseif($studinfo->preEnrolled == 0 && $studinfo->studstatus == 1){

                    $data = array((object)[
                        'enrollmentstatus'=>'7'
                    ]);
                    
                    return self::goTopaymentpage2blade($data);

                }

                $downpayment = DB::table('items')
                                ->join('items_dp',function($join) use($studinfo){
                                    $join->on('items.id','=','items_dp.itemid');
                                    $join->where('levelid',$studinfo->levelid);
                                    $join->where('items_dp.deleted','0');
                                })
                                ->select('items.*')
                                ->where('items.deleted','0')
                                ->where('items.isdp','1')
                                ->get();
                try{

                    $downpayment = DB::table('dpsetup')
                        ->where('levelid',$prereg->gradelevelid)
                        ->where('dpsetup.deleted','0')
                        ->groupBy('classid')
                        ->select(
                            DB::raw('SUM(amount) as amount, classid, description, itemid, allowless')
                        )
                        ->get();
                        
                }catch(\Exception $e){

                }
              
                            
                if(count($downpayment) == 0){

                    $withdp = false;

                    $data = array((object)[
                        'enrollmentstatus'=>'4'
                    ]);

                    return self::goTopaymentpage2blade($data);

                }
                else{
                    $data[0]->downpayment = $downpayment;
                }


                if($preEnrolled){

                    $preEnrolled = true;

                    $onlinepayment = DB::table('onlinepayments')
                                    ->where(function($query) use($studinfo){
                                        $query->where('queingcode',$studinfo->qcode);
                                        $query->orwhere('queingcode',$studinfo->sid);
                                        $query->orwhere('queingcode',$studinfo->lrn);
                                    })
                                    ->join('onlinepaymentdetails',function($join){
                                        $join->on('onlinepayments.id','=','onlinepaymentdetails.headerid');
                                        $join->where('onlinepaymentdetails.deleted',0);
                                    })
                                   
                                    ->orderBy('paymentDate','asc')
                                    ->select(
                                        'onlinepayments.id',
                                        'onlinepayments.remarks',
                                        'onlinepaymentdetails.description',
                                        'onlinepayments.amount',
                                        'onlinepayments.isapproved')
                                    ->get();

                 

                    if(count($onlinepayment) > 0){

                        $withonlinepayments = true;

                        $data[0]->onlinepayment = $onlinepayment;

                    }   

                    $data[0]->enrollmentstatus = '5';

                    return self::goTopaymentpage2blade($data);

                }
               
            }
            else{

                $downpayment = DB::table('items')
                                ->join('items_dp',function($join) use($prereg){
                                    $join->on('items.id','=','items_dp.itemid');
                                    $join->where('levelid',$prereg->gradelevelid);
                                    $join->where('items_dp.deleted','0');
                                })
                                ->where('items.deleted','0')
                                ->where('items.isdp','1')
                                ->select('items.*')
                                ->get();
                try{
                    $downpayment = DB::table('dpsetup')
                        ->where('levelid',$prereg->gradelevelid)
                        ->where('dpsetup.deleted','0')
                        ->groupBy('classid')
                        ->select(
                            DB::raw('SUM(amount) as amount, classid, description, itemid, allowless')
                        )
                        ->get();
                }catch(\Exception $e){}

                
                    
                if(count($downpayment) == 0){

                    $withdp = false;

                    $data = array((object)[
                        'enrollmentstatus'=>'4'
                    ]);

                    return self::goTopaymentpage2blade($data);

                }
                else{
                    $data[0]->downpayment = $downpayment;
                }

                if($preEnrolled){

                    $preEnrolled = true;

                    $onlinepayment = DB::table('onlinepayments')
                                    ->where(function($query) use($prereg){
                                        $query->where('queingcode',$prereg->queing_code);
                                    })
                                    ->join('onlinepaymentdetails',function($join){
                                        $join->on('onlinepayments.id','=','onlinepaymentdetails.headerid');
                                        $join->where('onlinepaymentdetails.deleted',0);
                                    })
                                    ->join('sy',function($join){
                                        $join->on('onlinepayments.syid','=','sy.id');
                                        $join->where('sy.isactive','1');
                                    })
                                    ->orderBy('paymentDate','desc')
                                    ->select(
                                        'onlinepayments.id',
                                        'onlinepayments.remarks',
                                        'onlinepaymentdetails.description',
                                        'onlinepayments.amount',
                                        'onlinepayments.isapproved')
                                    ->get();



                    if(count($onlinepayment) > 0){

                        $withonlinepayments = true;

                        $data[0]->onlinepayment = $onlinepayment;

                    }   

                    $data[0]->enrollmentstatus = '5';

                    return self::goTopaymentpage2blade($data);

                }

            }

        }
        // else{

        //     $data = array((object)[
        //         'enrollmentstatus'=>'6'
        //     ]);

        //     reutrn self::goTopaymentpage2blade($data);

        // }
        
      

    
    }

    public static function goTopaymentpage2blade($data){
        
        return view('enrollment.payment.paymentpage2')->with('data',$data);

    }

    // public function cancelpayment(Request $request){




    // }

    public function validaterefnum(Request $request){

        $checkIfExist = dB::table('onlinepayments')
                            ->where('refNum',$request->get('refNum'))
                            ->whereNotNull('refNum')
                            ->whereIn('isapproved',[0,5,1])
                            ->count();

        return $checkIfExist;

    }

    public function getProvince($region = null){
        
        return DB::table('lib_province')->where('LHIO',$region)->get();

    
    }

    public function getCityMun($province = null){
        
        return DB::table('lib_municipality')->where('provinceid',$province)->get();

    
    }

    public function getBarangay($citymun = null){

        $municipality = DB::table('lib_municipality')->where('id',$citymun)->first();
        
        return DB::table('lib_barangay')->where('municipalitycode',$municipality->municipality)->get();

    
    }


    

 
}
