<?php

namespace App\Http\Controllers\SuperAdminController;

use Illuminate\Http\Request;
use App\Models\Principal\SPP_Truncanator;
use DB;
use File;
use Mail;
use Image;
use Illuminate\Support\Facades\Validator;
use Hash;
use Crypt;
use Auth;
use Session;
use App\Models\Principal\LoadData;
use App\Models\Principal\SPP_Student;
use App\Models\Principal\SPP_EnrolledStudent;
use App\Models\Principal\SPP_Teacher;
use App\Models\Principal\SPP_Subject;
use App\Models\Principal\SPP_GradeSetup;
use App\Models\Principal\SPP_Blocks;
use App\Models\Principal\Section;
use App\Models\Principal\SPP_SchoolYear;

use App\Models\Grading\GradingSystem;
use App\Models\Grading\RatingValue;
use App\Models\Grading\GradingSystemDetail;
use App\Models\Grading\GradeCalculation;
use App\Models\Grading\GradeStudentPreschool;

use App\Models\Section\SectionDetail;
use App\Models\Student\Student;

use App\Models\Teacher\TeacherSubjectAssignment;



class SuperAdminController extends \App\Http\Controllers\Controller
{
    
      public function schoolMonitoring(){

            $listSum = array();

            $client = new \GuzzleHttp\Client();
            
            foreach(DB::table('schoollist')->get() as $item){

                  try{
                        $result = $client->request('GET',$item->eslink.'/studentmasterlist?enrolled=enrolled&count=count&withacadprog=withacadprog&withgradelevel=withgradelevel');
                        $students = $result->getBody()->getContents();
                        $students =  (object)json_decode($students, true);
                        
                  }catch (\Exception $e){
                  }

                  if(!isset($students->totalenrolledstudents)){
                        $students = (object)[
                              'enrrolledseniorhigh'=>0,
                              'nursery'=>0,
                              'gradeschool'=>0,
                              'juniorhigh'=>0,
                              'nursery'=>0,
                              'k1'=>0,
                              'k2'=>0,
                              'g1'=>0,
                              'g2'=>0,
                              'g3'=>0,
                              'g4'=>0,
                              'g5'=>0,
                              'g6'=>0,
                              'g7'=>0,
                              'g8'=>0,
                              'g9'=>0,
                              'g10'=>0,
                              'g11'=>0,
                              'g12'=>0,
                              'totalenrolledstudents'=>0
                        ];
                  }
          
                  array_push($listSum, (object)[
                        'schoolname'=>$item->schoolname,
                        'enrollmentreport'=>$students
                  ]);
          
            }

            return view('superadmin.pages.summary.enrolledstudents')->with('enrollmentsum',$listSum);

      }

      
      //cashreport
      public static function cashreport(){

            $listSum = array();

            $client = new \GuzzleHttp\Client();
            
            foreach(DB::table('schoollist')->get() as $item){

                  $trasactiondetails = (object)['scalar'=>'0'];

                  try{
                        $result = $client->request('GET',$item->eslink.'/cashtransaction?total=total');
                        $trasactiondetails = $result->getBody()->getContents();
                        $trasactiondetails =  (object)json_decode($trasactiondetails, true);
                        
                  }catch (\Exception $e){
                        
                  }


                  array_push($listSum, (object)[
                        'schoolname'=>$item->schoolname,
                        'totalpaidamount'=>$trasactiondetails
                  ]);
          
            }

            return view('superadmin.pages.summary.cashtransactions')->with('cashtransactions',$listSum);

      }

      //target collection
      public static function targetcollection(){

            return DB::table('studpayscheddetail')->get();

            // $listSum = array();

            // $client = new \GuzzleHttp\Client();
            
            // foreach(DB::table('schoollist')->get() as $item){

            //       $trasactiondetails = (object)['scalar'=>'0'];

            //       try{
            //             $result = $client->request('GET',$item->eslink.'/cashtransaction?total=total');
            //             $trasactiondetails = $result->getBody()->getContents();
            //             $trasactiondetails =  (object)json_decode($trasactiondetails, true);
                        
            //       }catch (\Exception $e){
                        
            //       }


            //       array_push($listSum, (object)[
            //             'schoolname'=>$item->schoolname,
            //             'totalpaidamount'=>$trasactiondetails
            //       ]);
          
            // }

            // return view('superadmin.pages.summary.cashtransactions')->with('cashtransactions',$listSum);

      }

      


      public function truncanator(Request $request){

            return view('superadmin.pages.truncanator');
    
      }
    
      public function truncate(Request $request){

            return SPP_Truncanator::truncate(
                  $request->has('fas'),
                  $request->has('sections'),
                  $request->has('grades'),
                  $request->has('enstud'),
                  $request->has('fortesting'),
                  $request->has('sched'),
                  $request->has('humanresource'),
                  $request->has('finance'),
                  $request->has('admin'),
                  $request->has('cashier'),
                  $request->has('teacher'),
                  $request->has('room')
            );

      }

      public function studentaccounts(Request $request){

            return back();

      }

      public function tunclevel1(){

            return view('superadmin.pages.truncate.level1');

      }

      public function tunclevel2(){

            return view('superadmin.pages.truncate.level2');

      }

      public function tunclevel3(){

            return view('superadmin.pages.truncate.level3');

      }

      public function tunclevel4(){

            $notcleared = self::checkprincipalstatus();

            $hrclearStatus = self::checkHumanResource();

            $cashierclearStatus = self::checkCashier();

            $registrarclearStatus = self::checkRegistrar();

            $financeclearStatus = self::checkFinance();

            $adminclearStatus = self::checkAdmin();

            return view('superadmin.pages.truncate.level4')
                        ->with('principalstatus',$notcleared)
                        ->with('cashierclearStatus',$cashierclearStatus)
                        ->with('registrarclearStatus',$registrarclearStatus)
                        ->with('financeclearStatus',$financeclearStatus)
                        ->with('adminNotCleared',$adminclearStatus)
                        ->with('hrStatus',$hrclearStatus);

      }

      public function truncatelevel4(Request $request){

            return SPP_Truncanator::trunclevel4(
                  $request->has('principal'),
                  $request->has('admin'),
                  $request->has('registrar'),
                  $request->has('finance'),
                  $request->has('cashier'),
                  $request->has('humanresource'),
                  $request

            );

      }

      public static function checkprincipalstatus(){

            
            
            $a1 = DB::table('subjects')->count();
            
            $a2 = DB::table('sh_subjects')->count();
            $a3 = DB::table('sh_prerequisite')->count();
            $a4 = DB::table('sh_sh_corequisite')->count();

            $a5 = DB::table('college_subjects')->count();
            $a6 = DB::table('college_subjprereq')->count();
            $a7 = DB::table('college_teachersubjects')->count();

            $a8 = DB::table('grades')->count();
            $a9 = DB::table('gradesspclass')->count();
            $a10 = DB::table('gradesdetail')->count();
            $a11 = DB::table('gradelogs')->count();
            $a12 = DB::table('tempgradesum')->count();
            $a13 = DB::table('notifications')->whereIn('type',['2','3'])->count();

            $a14 = DB::table('gradessetup')->count();
            $a15 = DB::table('sections')->count();
            $a16 = DB::table('sh_block')->count();
            $a17 = DB::table('sectiondetail')->count();
            $a18 = DB::table('college_sections')->count();
            $a19 = DB::table('sh_sectionblockassignment')->count();
    

    
            $a20 = DB::table('assignsubj')->count();
            $a21 = DB::table('assignsubjdetail')->count();
    
            $a22 = DB::table('classsched')->count();
            $a23 = DB::table('classscheddetail')->count();
    
            $a24 = DB::table('sh_classsched')->count();
            $a25 = DB::table('sh_classscheddetail')->count();
    
            $a26 = DB::table('sh_blocksched')->count();
            $a27 = DB::table('sh_blockscheddetail')->count();
    
            $a28 = DB::table('college_classsched')->count();
            $a29 = DB::table('college_scheddetail')->count();
            $a30 = DB::table('college_studsched')->count();
    
            $a31 = DB::table('classsubj')->count();

            $notcleared = false;

            for($x = 1 ; $x <= 31; $x++){

                  if(${'a'.$x} > 0){

                        $notcleared = true;

                  }

            }

            return $notcleared;

      }


      public static function checkHumanResource(){


            $a1 = DB::table('employee_allowance')->count();
            $a2 = DB::table('employee_allowanceinfo')->count();
            $a3 = DB::table('employee_allowanceinfodetail')->count();
            $a4 = DB::table('employee_allowanceother')->count();
            $a5 = DB::table('employee_allowanceotherdetail')->count();
            $a6 = DB::table('employee_allowancestandard')->count();
            $a7 = DB::table('employee_basicsalaryinfo')->count();
            $a8 = DB::table('employee_benefits')->count();
            $a9 = DB::table('employee_cashadvanceinfo')->count();
            $a10 = DB::table('employee_cashadvanceinfo')->count();
            $a11 = DB::table('employee_customtimesched')->count();
            $a12 = DB::table('employee_deductioninfo')->count();
            $a13 = DB::table('employee_deductionother')->count();
            $a14 = DB::table('employee_deductionotherdetail')->count();
            $a15 = DB::table('employee_deductionstandard')->count();
            $a16 = DB::table('employee_educationinfo')->count();
            $a17 = DB::table('employee_experience')->count();
            $a18 = DB::table('employee_familyinfo')->count();
            $a19 = DB::table('employee_leaves')->count();
            $a20 = DB::table('employee_overtime')->count();
            $a21 = DB::table('employee_overtimeattachments')->count();
            $a22 = DB::table('employee_overtimedetail')->count();
            $a23 = DB::table('employee_personalinfo')->count();
            $a24 = DB::table('employee_salary')->count();
            $a25 = DB::table('employee_salaryhistory')->count();
            $a26 = DB::table('employee_salaryhistorydetail')->count();

            $a27 = DB::table('deduction_standard')->count();
            $a28 = DB::table('deduction_standarddetail')->count();
            $a29 = DB::table('deduction_tardinessapplication')->count();
            $a30 = DB::table('deduction_tardinessdetail')->count();
            $a31 = DB::table('deduction_type')->count();
            $a32 = DB::table('deduction_typedetail')->count();

            $a33 = DB::table('job_deduction')->count();
            $a34 = DB::table('job_leavesdetail')->count();
            $a35 = DB::table('job_deductiondetail')->count();
            $a36 = DB::table('job_description')->count();
            $a37 = DB::table('job_overtime')->count();
            $a38 = DB::table('job_payroll')->count();
            $a39 = DB::table('job_payroll_history')->count();
            

            $a40 = DB::table('payroll')->count();
            $a41 = DB::table('payroll_history')->count();
            $a42 = DB::table('payroll_historydetail')->count();
            $a43 = DB::table('payrolldeductiondetail')->count();
            $a44 = DB::table('payrolldetail')->count();
            $a45 = DB::table('payrollearnings')->count();
            $a46 = DB::table('payrollleavesdetail')->count();

            // $a47 = DB::table('hr_school_department')->count();
            $a47 = 0;
            

            $notcleared = false;

            for($x = 1 ; $x <= 47; $x++){

                  if(${'a'.$x} > 0){

                        $notcleared = true;

                  }

            }

            return $notcleared;
      
      }


      public static function checkCashier(){


            $a1 = DB::table('orcounter')->count();
            $a2 = DB::table('chrngcashtrans')->count();
            $a3 = DB::table('chrngcrs')->count();
            $a4 = DB::table('chrngday')->count();
            $a5 = DB::table('chrngpermission')->count();
            $a6 = DB::table('chrngshift')->count();
            $a7 = DB::table('chrngtrans')->count();
            $a8 = DB::table('chrngtransdetail')->count();
            $a9 = DB::table('chrngvoidtrans')->count();
            $a10 = DB::table('announcements')->count();
            $a11 = DB::table('allowance_standard')->count();
            $a12 =  DB::table('studpaysched')->count();
            $a13 =  DB::table('studpayscheddetail')->count();
            $a14 = DB::table('chrngterminals')->where('owner','!=',null)->count();
            $a15 = DB::table('onlinepayments')->count();
            $a16 =  DB::table('onlinepaymentdetails')->count();

            
            $notcleared = false;

            for($x = 1 ; $x <= 16; $x++){

                  if(${'a'.$x} > 0){
                       
                        $notcleared = true;

                  }

            }

            return $notcleared;

      }

      public static function checkRegistrar(){

            $a1 = DB::table('observedvalues')->count();
            $a2 = DB::table('observedvaluesdetail')->count();
            $a3 = DB::table('enrolledstud')->count();
            $a4 = DB::table('sh_enrolledstud')->count();
            $a5 = DB::table('sh_studentsched')->count();
            $a6 = DB::table('studattendance')->count();
            $a7 = DB::table('studdiscounts')->count();
            $a8 = DB::table('studentsubjectattendance')->count();
            $a9 = DB::table('studledger')->count();
            $a10 = DB::table('studledgeritemized')->count();
            $a11 = DB::table('studpaysched')->count();
            $a12 = DB::table('studpayscheddetail')->count();
            $a13 = DB::table('notifications')->count();
    
            $a14 = DB::table('studinfo')->count();
            $a15 = DB::table('preregistration')->count();
           
            $a16 = DB::table('users')
                        ->whereIn('type',['7',9])
                        ->count();
    
            $a17 = DB::table('sf10childattendance')->count();
            $a18 = DB::table('sf10childgrades')->count();
            $a19 = DB::table('sf10eligibility')->count();
            $a20 = DB::table('sf10parent')->count();
            $a21 = DB::table('sf10remedial')->count();
            $a22 = DB::table('sf10schoolinfo')->count();
            $a23 = DB::table('sf10_grades_sh')->count();
            $a24 = DB::table('sf10_schoollist')->count();
            // $a25 = DB::table('sf10_student_js')->count();
            $a25 = 0;
            $a26 = DB::table('sf10_student_sh')->count();

            $a27 = DB::table('preregistration_answers')->count();
            $a28 = DB::table('preregistration_examination')->count();
            $a29 = DB::table('preregistration_questions')->count();
            $a30 = DB::table('preregistration_questions')->count();
            $a31 = DB::table('preregmedicalinfo')->count();
            $a32 = DB::table('preregreligiousinfo')->count();
            $a33 = DB::table('preregscholasticinfo')->count();

            $notcleared = false;

            for($x = 1 ; $x <= 33; $x++){

                  if(${'a'.$x} > 0){

                        $notcleared = true;

                  }

            }

            return $notcleared;

      }

      public static function checkFinance(){

            $a1 = DB::table('itemclassification')->count();
            $a2 = DB::table('items')->count();

            $a3 = DB::table('tuitiondetail')->count();
            $a4 = DB::table('tuitionheader')->count();
            $a5 = DB::table('tuitionitems')->count();
            $a6 = DB::table('tuitionitems')->count();

            // $a7 = DB::table('acc_coa')->count();
            $a7 = 0;
            // $a8 = DB::table('balforwardsetup')->count();

            $a8 = 0;
            $a9 = DB::table('paymentdiscount')->count();
            $a10 = DB::table('paymentpenalty')->count();
            $a11 = DB::table('paymentsched')->count();
            $a12 = DB::table('paymentsetup')->count();
            $a13 = DB::table('paymentsetupdetail')->count();
            // $a14 = DB::table('paymenttype')->count();
            $a14 = 0;

            $notcleared = false;

            for($x = 1 ; $x <= 14; $x++){

                  if(${'a'.$x} > 0){

                        $notcleared = true;

                  }

            }

            return $notcleared;

      }

      public static function checkAdmin(){

            $a1 = DB::table('schoolcal')->count();
            $a2 = DB::table('smsbunker')->count();
            $a3 = DB::table('building')->count();
      
            // $a4 = DB::table('users')->count();
            $a4 = 0;
      
            $a5 = DB::table('schoolcal')->count();
      
            $a6 = DB::table('perreq')->count();
            $a7 = DB::table('perreqdetail')->count();

            $a8 = DB::table('sy')->count();
            $a9 = DB::table('unpostrequest')->count();
            $a10 = 0;
            $a11 = DB::table('semester')->where('id','!=','1')->where('isactive',1)->count();

            $a12 = DB::table('academicprogram')->where('principalid','!=','0')->count();

            $a13 = DB::table('notifications')->count();
            $a14 = DB::table('teacher')->count();
            $a15 = DB::table('announcements')->count();
            $a16 = DB::table('teacheracadprog')->count();
            $a17 = DB::table('faspriv')->count();
            $a18 = DB::table('teacherattendance')->count();

            $notcleared = false;

            for($x = 1 ; $x <= 18; $x++){

                  if(${'a'.$x} > 0){
                  
                        $notcleared = true;

                  }

            }

            return $notcleared;

      }

      public function viewschoolinfo(){

            $schoolinfo = DB::table('schoolinfo')
                        ->first();

            $admin_pass = DB::table('users')
                              ->where('type',6)
                              ->where('deleted',0)
                              ->first();

           $adminadmin_pass = DB::table('users')
                              ->where('type',12)
                              ->where('deleted',0)
                              ->first();

            $databaseName = \DB::connection()->getDatabaseName();


            return view('superadmin.pages.schoolinfo.viewschoolinfo')
                        ->with('schoolinfo',$schoolinfo)
                        ->with('admin_pass',$admin_pass)
                        ->with('adminadmin_pass',$adminadmin_pass)
                        ->with('databasename',$databaseName);

      }


      public function updateschooolcolor(Request $request){

            DB::enableQueryLog();

            DB::table('schoolinfo')->update(['schoolcolor'=>$request->get('schoolcolor')]);

            $logs = json_encode(DB::getQueryLog());

            DB::table('updatelogs')
            ->insert([
                  'type'=>1,
                  'sql'=> $logs,
                  'createdby'=>auth()->user()->id,
                  'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
            ]);

            DB::disableQueryLog();

          
            return back();

      }

      public function updateschooolwebsitelink(Request $request ){
           DB::table('schoolinfo')->update(['websitelink'=>$request->get('websitelink')]);
           return back();
      }

      public function updateschooolessentiellink(Request $request ){
            DB::table('schoolinfo')->update([
                              'essentiellink'=>$request->get('essentiellink'),
                        ]);
            return back();
      }

      public function updateschooolessentielcloudlink(Request $request ){
            DB::table('schoolinfo')->update([
                              'es_cloudurl'=>$request->get('essentielcloudlink')
                        ]);
            return back();
      }

      public function updateterms(Request $request ){
            DB::table('schoolinfo')->update(['terms'=>$request->get('terms')]);
            return back();
       }

      public function viewpaymentoptions(){

            $paymentoptions = DB::table('onlinepaymentoptions')
                                    ->where('deleted','0')
                                    ->take(10)
                                    ->orderBy('createddatetime','desc')
                                    ->get();

            $data = array((object)[
                  'data'=>$paymentoptions,
                  'count'=>DB::table('onlinepaymentoptions') ->where('deleted','0')->count()
            ]);


       
            return view('superadmin.pages.paymentoptions.viewpaymentoptions')
                        ->with('data',$data);


      }

      public function createpaymentoptions(Request $request){

            $message = [
                  'paymentLogo.required'=>'Payment logo is required',
                  'paymenttype.required'=>'Payment type is required'
              ];
      
              $validator = Validator::make($request->all(), [
                  'paymentLogo' => ['required'],
                  'paymenttype' => ['required']
              
              ], $message);


            if ($validator->fails()) {

                  $data = array(
                        (object)
                      [
                        'status'=>'0',
                        'message'=>'Error',
                        'errors'=>$validator->errors(),
                        'inputs'=>$request->all()
                    ]);

                  return $data;

            }else{

                  $urlFolder = str_replace('http://','',$request->root());

                  if (! File::exists(public_path().'paymentoptions/')) {

                        $path = public_path('paymentoptions');

                        if(!File::isDirectory($path)){

                              File::makeDirectory($path, 0777, true, true);
                        }
                  }

                  if (! File::exists(dirname(base_path(), 1).'/'.$urlFolder.'/paymentoptions/')) {

                        $cloudpath = dirname(base_path(), 1).'/'.$urlFolder.'/paymentoptions/';

                        if(!File::isDirectory($cloudpath)){

                              File::makeDirectory($cloudpath, 0777, true, true);

                        }
                  
                  }

                  $file = $request->file('paymentLogo');
            
                  // $extension = $file->getClientOriginalExtension();

                  $extension = 'png';
                  
                  $img = Image::make($file->path());


                  if($request->get('poid') == null){

                        $countPaymentOptions = DB::table('onlinepaymentoptions')->count() + 1;

                  }else{

                        $countPaymentOptions = $request->get('poid');

                  }


                  $destinationPath = public_path('paymentoptions/paymentoptions'.$countPaymentOptions.'.'.$extension);

                  $clouddestinationPath = dirname(base_path(), 1).'/'.$urlFolder.'/'.'paymentoptions/paymentoptions'.$countPaymentOptions.'.'.$extension;

                  $img->resize(600, 600, function ($constraint) {
                        $constraint->aspectRatio();
                  })->save($destinationPath);

                  $img->resize(500, 500, function ($constraint) {
                              $constraint->aspectRatio();
                              })->resizeCanvas(500, 500,'center')->save($clouddestinationPath);


                  if($request->get('poid') == null){

                        DB::table('onlinepaymentoptions')
                                    ->insert([
                                          'picurl'=>'paymentoptions/paymentoptions'.$countPaymentOptions.'.'.$extension,
                                          'paymenttype'=>$request->get('paymenttype'),
                                          'optionDescription'=>$request->get('paymentDesc'),
                                          'accountName'=>$request->get('accName'),
                                          'accountNum'=>$request->get('accNum'),
                                          'mobileNum'=>$request->get('mobileNum'),
                                          'createddatetime'=> \Carbon\Carbon::now('Asia/Manila')->isoFormat('YYYY-MM-DD HH:mm:ss')
                                    ]);
                  }
                  else{

                        DB::table('onlinepaymentoptions')
                                    ->where('id',$request->get('poid'))
                                    ->update([
                                          'picurl'=>'paymentoptions/paymentoptions'.$countPaymentOptions.'.'.$extension,
                                          'paymenttype'=>$request->get('paymenttype'),
                                          'optionDescription'=>$request->get('paymentDesc'),
                                          'accountName'=>$request->get('accName'),
                                          'accountNum'=>$request->get('accNum'),
                                          'mobileNum'=>$request->get('mobileNum'),
                                    ]);

                  }

                  $data = array(
                        (object)
                      [
                        'status'=>'1',
                        // 'message'=>'Error',
                        // 'errors'=>$validator->errors(),
                        // 'inputs'=>$request->all()
                    ]);

                  return $data;
            }

      }

      public function filterpaymentoptions(Request $request){

            $paymentOptions = DB::table('onlinepaymentoptions')
                                          ->where('deleted','0');

            $paymentOptionsCount =  $paymentOptions->count();

            $paymentOptions->skip( ( $request->get('pagenum') - 1 )  * 10);
            $paymentOptions->take(10);
           

            $data = $paymentOptions
                        ->orderBy('createddatetime','desc')
                        ->get();

            $data = array((object)[
                  'data'=>$data,
                  'count'=>$paymentOptionsCount
            ]);

            return view('superadmin.pages.paymentoptions.paymentoptionstable')->with('data',$data);

      }

      public function removepaymentoptions($id){

            DB::table('onlinepaymentoptions')->where('id',$id)->update(['deleted'=>'1']);

      }

      public function setactivepaymentoptions($id){

            DB::table('onlinepaymentoptions')->where('id',$id)->update(['isActive'=>'1']);

      }

      
      public function setasinactivepaymentoptions($id){

            DB::table('onlinepaymentoptions')->where('id',$id)->update(['isActive'=>'0']);

      }


      public static function getUsers(Request $request){


            if($request->has('reset') && $request->get('reset') != null){

                  if(auth()->user()->type == 6 || auth()->user()->type == 17){

                        DB::table('users')
                                          ->where('id',$request->get('id'))
                                          ->update([
                                                'password'=>Hash::make('123456'),
                                                'isDefault'=>'1'
                                          ]);

                        DB::enableQueryLog();

                        DB::table('users')
                                    ->where('id',$request->get('id'))
                                    ->update([
                                          'password'=>Hash::make('123456'),
                                          'isDefault'=>'1'
                                    ]);

                        $logs = json_encode(DB::getQueryLog());

                        DB::table('updatelogs')
                              ->insert([
                                    'type'=>1,
                                    'sql'=> $logs,
                                    'createdby'=>auth()->user()->id,
                                    'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                              ]);
                        
                        DB::disableQueryLog();
                        return '1';
                        
                  }

                  


              
                       
            }

            $users = DB::table('users')
                        ->join('usertype',function($join){
                              $join->on('users.type','=','usertype.id');
                              $join->where('usertype.deleted','0');
                        })
                        ->where('users.deleted','0');
         
                      
                        
            if($request->has('search') && $request->get('search') != 'null'){

                  $search = $request->get('search');

                  if($search == 'defaultpass'){
                        $users->where(function($query) use( $search ) {
                              $query->where('isDefault','1');
                        });
                  }
                  else{
                      
                        $users->where(function($query) use( $search ) {

                                    $query->where('users.name','like','%'.$search.'%');
                                    $query->orWhere('usertype.utype','like','%'.$search.'%');
                                    $query->orWhere('users.email','like','%'.$search.'%');
                              
                        });

                  }

                  
            }

            if(auth()->user()->type == 6){

                  $users =  $users->whereIn('type',[7,9]);
 
             }
 

            $count = $users->count();
          
            if($request->has('take') && $request->get('take') != 'null'){

                  $users->take($request->get('take'));

            }

        

            if($request->has('skip') && $request->get('skip') != 'null'){


                  if($request->has('take')){

                        $users->skip( ( $request->get('skip')-1 ) * $request->get('take'));

                  }
                  else{

                        $users->take($count)->skip($request->get('skip'));

                  }

            }

            if($request->has('count')){
             
                  return $users->get()->count();

            }

            
            $users = $users->select('name','utype','isDefault','users.id','email')
            ->get();

            if($request->has('table')){
                 
                  $users = array((object)['data'=>$users,'count'=>$count]);

                  return view('superadmin.pages.passreseter.userstable')->with('data',$users);

            }
         
            return redirect('/home');

      }

      public function usersblade(){

            return view('superadmin.pages.passreseter.users');

      }

      public function updateModeOfLearning(Request $request){

            DB::table('schoolinfo')           
                  ->update([
                        'withMOL'=>$request->get('withMOL')
                  ]);
            
            return back();

      }

      public function updateESC(Request $request){

            DB::table('schoolinfo')           
                  ->update([
                        'withESC'=>$request->get('withESC')
                  ]);
            
            return back();

      }

      public function updatesetup(Request $request){

            $setup = $request->get('setup');
            $user_setup = 'all';

            if($setup == 0){
                  $setup = 'offline';
            }else{
                  $user_setup = 'hybrid1';
                  $setup = 'online';
            }

            DB::table('schoolinfo')           
                  ->update([
                        'setup'=>$request->get('setup'),
                        'user_setup'=>$user_setup
                  ]);

            if($setup == 'all'){

                  DB::table('usertype')
                        ->update([
                              'type_active'=>1
                        ]);

            }else{

                  DB::table('usertype')
                        ->update([
                              'type_active'=>0
                        ]);

                  DB::table('usertype')
                        ->whereIn('id',[3,4,14,15,16,17,10,6])
                        ->update([
                              'type_active'=>1
                        ]);

            }

            return back();

      }



      public function getaccessibility(Request $request){

            $accessibility = DB::table('useraccessibility')
                              ->join('useraccesslist',function($join){
                                    $join->on('useraccessibility.access','=','useraccesslist.id');
                              })
                              ->join('useraccesslistdetail',function($join){
                                    $join->on('useraccesslist.id','=','useraccesslistdetail.headerid');
                              });

            if($request->get('blade') != null && $request->get('blade') == 'blade'){


                  return view('superadmin.pages.useraccessibility.accessibility');

            }
            else if($request->get('table') != null && $request->get('table') == 'table'){

                  $useraccesslist = DB::table('useraccesslist')->get();

                  return view('superadmin.pages.useraccessibility.accessibilitytable')
                                    ->with('useraccesslist',$useraccesslist);

            }

            return $accessibility->get();

      }


      public function getusertype(Request $request){

            $usertype = DB::table('usertype');

         

            if($request->get('table') != null && $request->get('table') == 'table'){
                  
                  return view('superadmin.pages.usertype.usertypetable')->with('usertype',$usertype->get());

            }


      }

      public function studentinfofixer(){

            $students =  DB::table('studinfo')
                        ->where('lrn',null)->select('id')->get();

            foreach( $students as $item){

                  $studentOriginalInfo = DB::table('studinfoold')->where('id',$item->id)->first();
           
                  try{

                        DB::table('studinfo')
                                    ->where('id',$item->id)
                                    ->update([
                                          'lastname'                      =>  $studentOriginalInfo->lastname,
                                          'firstname'                     =>  $studentOriginalInfo->firstname ,
                                          'middlename'                    =>  $studentOriginalInfo->middlename ,
                                          'suffix'                        => $studentOriginalInfo->suffix ,
                                          'gender'                        => $studentOriginalInfo->gender,
                                          'dob'                           => $studentOriginalInfo->dob ,
                                          'contactno'                     => $studentOriginalInfo->contactno ,
                                          'mothername'                    => $studentOriginalInfo->mothername ,
                                          'moccupation'                   => $studentOriginalInfo->moccupation ,
                                          'mcontactno'                    => $studentOriginalInfo->mcontactno ,
                                          'fathername'                    => $studentOriginalInfo->fathername ,
                                          'foccupation'                   => $studentOriginalInfo->foccupation ,
                                          'fcontactno'                    => $studentOriginalInfo->fcontactno ,
                                          'guardianname'                  => $studentOriginalInfo->guardianname ,
                                          'gcontactno'                    => $studentOriginalInfo->gcontactno ,
                                          'guardianrelation'              => $studentOriginalInfo->guardianrelation ,
                                          'semail'                         => $studentOriginalInfo->semail ,
                                          'ismothernum'                   => $studentOriginalInfo->ismothernum,
                                          'isfathernum'                   => $studentOriginalInfo->isfathernum,
                                          'isguardannum'                  => $studentOriginalInfo->isguardannum,
                                          'preEnrolled'                   => '0',
                                          'strandid'                      =>$studentOriginalInfo->strandid,
                                          'courseid'                      =>$studentOriginalInfo->courseid
                                    ]);
                  }
                  catch (\Exception $e) {
                        
                  }
              

            }

      }


      public function preregrequirements(Request $request){

            $requirementlist = DB::table('preregistrationreqlist')
                                    ->leftJoin('academicprogram',function($join){
                                          $join->on('preregistrationreqlist.acadprogid','=','academicprogram.id');
                                    })
                                    ->where('deleted','0');
                                    

            if($request->has('table') && $request->get('table') == 'table'){

                  $requirementlist = $requirementlist->select(
                        'preregistrationreqlist.*',
                        'academicprogram.acadprogcode'
                        )->get();

                  return view('superadmin.pages.schoolinfo.requirementlisttable')
                              ->with('requirementlist' , $requirementlist);

            }

            else if($request->has('settoactive') && $request->get('settoactive') == 'settoactive'){

                  $requirementlist->where('preregistrationreqlist.id',$request->get('reqid'))
                                    ->where('createdby',auth()->user()->id)
                                    ->update([
                                          'isActive'=>'1',
                                          'updatedby'=>auth()->user()->id,
                                          'updateddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                                    ]);

            }

            else if($request->has('settoinactive') && $request->get('settoinactive') == 'settoinactive'){

                  $requirementlist->where('createdby',auth()->user()->id)
                                  ->where('preregistrationreqlist.id',$request->get('reqid'))
                                  ->update([
                                        'isActive'=>'0',
                                        'updatedby'=>auth()->user()->id,
                                        'updateddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                                  ]);

            }
            else if($request->has('info') && $request->get('info') == 'info'){

                  if($request->has('isactive') && $request->get('isactive') != null){
                        
                        $requirementlist->where('preregistrationreqlist.isactive',1);

                  }

                  if($request->has('levelid') && $request->get('levelid') != null){
                        
                        $levelAcadprog = DB::table('gradelevel')
                                          ->where('id',$request->get('levelid'))
                                          ->first();

                        $requirementlist->where('preregistrationreqlist.acadprogid',$levelAcadprog->acadprogid);

                  }


                  if($request->has('reqid') && $request->get('reqid') != null){

                        return $requirementlist->where('preregistrationreqlist.id',$request->get('reqid'))
                              ->select(
                                    'preregistrationreqlist.*',
                                    'academicprogram.acadprogcode'
                              )->get();

                  }

                  return $requirementlist->select(
                        'preregistrationreqlist.*',
                        'academicprogram.acadprogcode'
                        )->get();

                  

                                  

            }
            else if($request->has('remove') && $request->get('remove') == 'remove'){

                  $requirementlist->where('preregistrationreqlist.id',$request->get('reqid'))
                                    ->where('createdby',auth()->user()->id)
                                    ->update([
                                          'isActive'=>'0',
                                          'deleted'=>'1',
                                          'deletedby'=>auth()->user()->id,
                                          'deleteddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                                    ]);

            }
            else if($request->has('create') && $request->get('create') == 'create'){

                  DB::table('preregistrationreqlist')
                        ->updateOrInsert(
                              [ 
                                    'id'=>$request->get('reqid'),
                                    'createdby'=>auth()->user()->id
                              ],
                              [
                                    'description'=>$request->get('description'),
                                    'acadprogid'=>$request->get('acadprog'),
                                    'isActive'=>$request->get('isactive'),
                                    'isRequired'=>$request->get('isrequired'),
                                    'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                              ]);

            }




           

            

      }

      //for old students with existing users after truncate
      public function fixStudentCredentials(){

            $students = DB::table('studinfo')
                ->where('studstatus','1')
                ->join('users',function($join){
                    $join->on('studinfo.userid','=','users.id');
                })
                ->select(
                    'userid','sid',
                    'email','studinfo.id',
                    'firstname','lastname',
                    'mothername','fathername','guardianname',
                    'ismothernum','isfathernum','isguardannum',
                    'contactno','fcontactno','mcontactno','gcontactno'
                    )
                ->get();

            $errorCount = 0;

            foreach($students as $key=>$item){

                  $studentname = $item->firstname.', '.$item->lastname;
                  $pname = null;
                  $pcontactno = null;

                  if($item->sid != str_replace('S','',$item->email)){

                  $errorCount += 1;

                  DB::table('studinfo')->where('id',$item->id)->update(['userid'=>null]);

                  $studentID = DB::table('users')
                        ->insertGetID([
                              'name'=>$studentname,
                              'email'=>'S'.$item->sid,
                              'type'=>'7',
                              'password'=>Hash::make('123456')
                        ]);

                  if($item->ismothernum == 1){

                        $pcontactno = $item->mcontactno;
                        $pname = $item->mothername;

                        if($item->mothername == ''){

                              $pname = $studentname;

                        }

                        $id = DB::table('users')
                              ->insertGetID([
                              'name'=>$pname,
                              'email'=>'P'.$item->sid,
                              'type'=>'9',
                              'password'=>Hash::make('123456')
                              ]);

                  }
                  elseif($item->isfathernum == 1){

                        $pname = $item->fathername;
                        $pcontactno = $item->fcontactno;

                        if($item->mothername == ''){

                              $pname = $studentname;
                              
                        }

                        $id = DB::table('users')
                              ->insertGetID([
                              'name'=>$pname,
                              'email'=>'P'.$item->sid,
                              'type'=>'9',
                              'password'=>Hash::make('123456')
                              ]);

                  }
                  elseif($item->isguardannum == 1){

                        $pname = $item->guardianname;
                        $pcontactno = $item->gcontactno;

                        if($item->guardianname == ''){

                              $pname = $studentname;
                              
                        }

                        $id = DB::table('users')
                              ->insertGetID([
                              'name'=>$pname,
                              'email'=>'P'.$item->sid,
                              'type'=>'9',
                              'password'=>Hash::make('123456')
                              ]);

                  }

                  DB::table('studinfo')->where('id',$item->id)->update(['userid'=>$studentID]);

                  if(substr($item->contactno, 0,1)=='0')
                              {
                                    $contactno = '+63' . substr($item->contactno, 1);
                              }

                  $smsStud = db::table('smsbunker')
                                                ->insert([
                                                      'message' => $item->firstname .' you are already enrolled. Portal Credential - Username:S'.$item->sid . ' Default Password: 123456',
                                                      'receiver' => $contactno,
                                                      'smsstatus' => 0
                              ]);
                              
                  if(substr($pcontactno, 0,1)=='0')
                  {
                        $pcontactno = '+63' . substr($pcontactno, 1);
                  }
            

                  $smsParent = db::table('smsbunker')
                              ->insert([
                              'message' => 'Your student '. $item->firstname .' is already enrolled. Portal Credential - Username:P'.$item->sid . ' Default Password: 123456',
                              'receiver' => $pcontactno,
                              'smsstatus' => 0
                              ]);
                  
                  }
            }
            
            return $errorCount;

      }

      public function checkIfSmsIsSent(){

            $students = DB::table('studinfo')
                  ->where('studstatus','1')
                  ->join('users',function($join){
                  $join->on('studinfo.userid','=','users.id');
                  })
                  ->select(
                  'userid','sid',
                  'email','studinfo.id',
                  'firstname','lastname',
                  'mothername','fathername','guardianname',
                  'ismothernum','isfathernum','isguardannum',
                  'contactno','fcontactno','mcontactno','gcontactno'
                  )
                  ->get();

            $countUnsent = 0;
            $idList = array();

            foreach($students as $item){

                  $checkIfSent = DB::table('smsbunker')
                                    ->where('message', 'like', '%'.$item->sid.'%')
                                    ->count();

                  if($checkIfSent == 0){

                        $studentname = $item->firstname.', '.$item->lastname;
                        $pname = null;
                        $pcontactno = null;
                        $countUnsent += 1;
      
                        if($item->ismothernum == 1){
      
                              $pcontactno = $item->mcontactno;
                              $pname = $item->mothername;
      
                              if($item->mothername == ''){
      
                                    $pname = $studentname;
      
                              }
      
                        }
                        elseif($item->isfathernum == 1){
      
                              $pname = $item->fathername;
                              $pcontactno = $item->fcontactno;
      
                              if($item->mothername == ''){
      
                                    $pname = $studentname;
                                    
                              }
      
                        }
                        elseif($item->isguardannum == 1){
      
                              $pname = $item->guardianname;
                              $pcontactno = $item->gcontactno;
      
                              if($item->guardianname == ''){
      
                                    $pname = $studentname;
                                    
                              }
      
                        }
      
                        if(substr($item->contactno, 0,1)=='0')
                        {
                              $contactno = '+63' . substr($item->contactno, 1);
                        }
      
                        DB::table('smsbunker')
                                          ->insert([
                                                'message' => $item->firstname .' you are already enrolled. Portal Credential - Username:S'.$item->sid . ' Default Password: 123456',
                                                'receiver' => $contactno,
                                                'smsstatus' => 0
                                    ]);
                                    
                        if(substr($pcontactno, 0,1)=='0')
                        {
                              $pcontactno = '+63' . substr($pcontactno, 1);
                        }
                  
      
                        DB::table('smsbunker')
                                    ->insert([
                                    'message' => 'Your student '. $item->firstname .' is already enrolled. Portal Credential - Username:P'.$item->sid . ' Default Password: 123456',
                                    'receiver' => $pcontactno,
                                    'smsstatus' => 0
                                    ]);
                        
                  }

            }

            return $countUnsent;

      }
      


      public function matchPassword(Request $request){

            if(Hash::check(base64_decode($request->get('pass')), auth()->user()->password)){
                  return 1;
            }
            else{
                  return 0;
            }

      }


      public function adminsetupgradesetupblade(){

            return view('superadmin.pages.gradetermsetup.gradetermsetup');
            

      }

      public function gradetermsetup(Request $request){


            if($request->has('create') && $request->get('create') == 'create'){

                  if($request->get('withprelim') == null){
                        $request->merge(['withprelim'=>0 ]);
                  }
                  if($request->get('withmid') == null){
                        $request->merge(['withmid'=>0 ]);
                  }
                  if($request->get('withsemi') == null){
                        $request->merge(['withsemi'=>0 ]);
                  }
                  if($request->get('withfinal') == null){
                        $request->merge(['withfinal'=>0 ]);
                  }
                  if($request->get('isactive') == null){
                        $request->merge(['isactive'=>0 ]);
                  }

                  // $prelimcomp = '';
                  // $midcomp = '';
                  // $semicomp = '';
                  // $finalcomp = '';

                  // if($request->has('prelimcomp')){
                  //       foreach($request->get('prelimcomp') as $item){
                  //             $prelimcomp .= $item;        
                  //       }
                  // }
                  // if($request->has('midcomp')){
                  //       foreach($request->get('midcomp') as $item){
                  //             $midcomp .= $item;        
                  //       }
                  // }
                  // if($request->has('semicomp')){
                  //       foreach($request->get('semicomp') as $item){
                  //             $semicomp .= $item;        
                  //       }
                  // }
                  // if($request->has('finalcomp')){
                  //       foreach($request->get('finalcomp') as $item){
                  //             $finalcomp .= $item;        
                  //       }
                  // }

                  DB::table('college_gradestermsetup')
                        ->insert([
                              'description'=>$request->get('description'),
                              'withpre'=>$request->get('withpre'),
                              'withmid'=>$request->get('withmid'),
                              'withsemi'=>$request->get('withsemi'),
                              'withfinal'=>$request->get('withfinal'),
                              'pttype'=>$request->get('pttype'),
                              'mttype'=>$request->get('mttype'),
                              'sttype'=>$request->get('sttype'),
                              'fttype'=>$request->get('fttype'),
                              'fgtype'=>$request->get('fgtype'),

                              'fgptper'=>$request->get('fgptper'),
                              'fgmtper'=>$request->get('fgmtper'),
                              'fgstper'=>$request->get('fgstper'),
                              'fgftper'=>$request->get('fgftper'),

                              'ptptper'=>$request->get('ptptper'),

                              'mtptper'=>$request->get('mtptper'),
                              'mtmtper'=>$request->get('mtmtper'),

                              'stptper'=>$request->get('stptper'),
                              'stmtper'=>$request->get('stmtper'),
                              'ststper'=>$request->get('ststper'),

                              'ftptper'=>$request->get('ftptper'),
                              'ftmtper'=>$request->get('ftmtper'),
                              'ftstper'=>$request->get('ftstper'),
                              'ftftper'=>$request->get('ftftper'),

                              'isactive'=>$request->get('isactive'),
                        ]);

            }
            else if($request->has('table') && $request->get('table') == 'table'){

                  $gradetermsetup = DB::table('college_gradestermsetup')->get();

                  return view('superadmin.pages.gradetermsetup.gradetermsetuptable')
                              ->with('gradetermsetup',$gradetermsetup);
                        
            }
            elseif($request->has('info') && $request->get('info') == 'info'){

                  $gradetermsetup = DB::table('college_gradestermsetup')
                                          ->where('deleted','0')
                                          ->where('isactive','1')
                                          ->select('id','description')
                                          ->get();

                  return $gradetermsetup;
            }
            elseif($request->has('detail') && $request->get('detail') == 'detail'){

                  $gradetermsetup = DB::table('college_gradestermsetup')
                                          ->where('deleted','0')
                                          ->where('id',$request->get('id'))
                                          ->where('isactive','1')
                                          ->get();

                  return $gradetermsetup;
            }
           

      }

      public function synctest(){

            $tables = DB::select('SHOW TABLES');

            return count($tables);

            $res = DB::select(DB::raw('SHOW CREATE TABLE '.'studinfo'));

            return $res;

      }

      public function syncmodules(Request $request){

            try{

                  // explode('=', explode("/",url()->previous() )[3] )[3];

                  // if( ( $request->get('pass') == 'ckgroupSync2019' && $request->has('pass') ) 
                  // || explode('=', explode("/",url()->previous() )[3] )[3] == 'ckgroupSync2019'
                  // ){

                        $syncmodules = DB::table('syncmodules');

                        if($request->get('tablename') != null && $request->has('tablename')){

                              $syncmodules = $syncmodules->where('tablename', $request->get('tablename') );
                        }

                        if($request->get('table') == 'table' && $request->has('table')){

                              

                              if($request->get('synctype') == 'ltc'){

                                    $enabled = false;

                                    if($request->get('enabled') == 'enabled' && $request->has('enabled') ){

                                          $syncmodules = $syncmodules->where('deleted',0);
                                          $enabled = true;

                                    }

                                    if( ( $request->get('search') != 'null' && $request->get('search') != null ) && $request->has('search') ){

                                          $syncmodules = $syncmodules->where('tablename','like','%'.$request->get('search').'%');

                                    }

                                    $count = $syncmodules->count();

                                    if($request->get('skip') != null && $request->has('skip') ){

                                          $syncmodules = $syncmodules->skip($request->get('take') * ( $request->get('skip') - 1));

                                    }

                                  


                                    if($request->get('take') != null && $request->has('take') ){

                                          $syncmodules = $syncmodules->take($request->get('take'));

                                    }

                                 
                                    $syncmodules = $syncmodules->orderBy('tablename')->get();

                                    $data = array((object)[
                                          'data'=> $syncmodules,
                                          'count'=> $count
                                    ]);

                                    return view('superadmin.pages.synchronization.syncmodulestable')
                                                ->with('enabled',$enabled)
                                                ->with('data',$data);

                              }
                              elseif($request->get('synctype') == 'ctl'){

                                    $syncsetup = DB::table('syncsetup')->first();

                                    $client = new \GuzzleHttp\Client();
                                    $response = $client->request('GET', $syncsetup->url. '/syncmodules?table=table&synctype=ltc'.'&take='.$request->get('take').'&skip='.$request->get('skip').'&search='.$request->get('search').'&enabled='.$request->get('enabled'));

                                    $result = json_decode($response->getBody()->getContents()); 

                                    return $response->getBody();

                              }

                             

                        }
                        if($request->get('forsync') == 'forsync' && $request->has('forsync')){

                             return $syncmodules->where('deleted',0)->get();
                             

                        }
                        else if($request->get('blade') == 'blade' && $request->has('blade')){

                              if($request->get('synctype') == 'ltc'){

                                    return view('superadmin.pages.synchronization.syncmodules');

                              }
                              elseif($request->get('synctype') == 'ctl'){

                                    return view('superadmin.pages.synchronization.syncmodulesctl');

                              }

                              

                        }
                        else 
                              if($request->get('update') == 'update' && $request->has('update'))
                              // if(explode('=', explode("/",url()->previous() )[3] )[2] == 'ckgroupSync2019')
                        {


                              if($request->get('synctype') == 'ltc'){

                                    if( ( $request->has('type') && $request->get('type') == 'all' ) && (  $request->has('status') && $request->get('status') != null ) ){

                                          $syncmodules->update([
                                                'create'=>$request->get('status'),
                                                'update'=>$request->get('status'),
                                                'delete'=>$request->get('status')
                                          ]);
                                    }
                                    else if($request->has('type') && $request->get('type') != null && (  $request->has('status') && $request->get('status') != null ) ){
      
                                          $syncmodules->update([
                                                $request->get('type')=>$request->get('status'),
                                          ]);
      
                                    }

                              }
                              elseif($request->get('synctype') == 'ctl'){

                                    $syncsetup = DB::table('syncsetup')->first();
                                    
                                    $client = new \GuzzleHttp\Client();
                                    $response = $client->request('GET', $syncsetup->url. '/syncmodules?update=update&type='.$request->get('type').'&tablename='.$request->get('tablename').'&status='.$request->get('status').'&synctype=ltc');

                              }


                              
                        
                        }

                  // }
                  // else{

                  //       return "Unable to access";

                  // }

            }catch (\Exception $e){


                  return $e;
                  return "A PASSWORD IS REQUIRED";

            }

      }
      
      public function syncsetup(Request $request){


            $syncsetup = DB::table('syncsetup');

            if($request->has('blade') && $request->get('blade') == 'blade'){

                  return view('superadmin.pages.synchronization.syncsetup');

            }
            else if($request->has('info') && $request->get('info') == 'info'){

                  if($request->has('synctype') && $request->get('synctype') == 'ltc'){

                        return $syncsetup->get();

                  }
                  else if($request->has('synctype') && $request->get('synctype') == 'ctl'){

                        try{

                              $client = new \GuzzleHttp\Client();
                              $response = $client->request('GET', $syncsetup->first()->url.'/syncsetup?info=info&synctype=ltc');

                              $result = json_decode($response->getBody()->getContents()); 

                              return  $result;

                        }catch (\Exception $e){

                              return array((object)[
                                    'url'=>'Not Found'
                              ]);

                        }


                  }

            }
            else if($request->has('update') && $request->get('update') == 'update'){

                  if($request->has('synctype') && $request->get('synctype') == 'ltc'){

                        DB::table('syncsetup')->update(['url'=>$request->get('url')]);

                        return "1";

                  }
                  else if($request->has('synctype') && $request->get('synctype') == 'ctl'){


                        // return $syncsetup->first()->url.'/syncsetup?update=update&synctype=ltc&url='.$request->get('url');

                        $client = new \GuzzleHttp\Client();
                        $response = $client->request('GET', $syncsetup->first()->url.'/syncsetup?update=update&synctype=ltc&url='.$request->get('url'));

                        $result = json_decode($response->getBody()->getContents()); 

                        return  $result;

                  }


            }
            
      }


      public function teacherevalquestions(Request $request){

            $evaluationQuestion = DB::table('teacherevalquestions')->where('deleted',0);
            

            if($request->has('question_id') && $request->get('question_id') != null){

                  $evaluationQuestion = $evaluationQuestion->where('id',$request->get('question_id'));
                  
            }

            if($request->has('blade') && $request->get('blade') == 'blade'){

                  return view('superadmin.pages.teacherevaluation.questionsetup.questionsetup');


            }
            else if($request->has('table') && $request->get('table') == 'table'){

                  $evaluationQuestion = $evaluationQuestion->get();

                  return view('superadmin.pages.teacherevaluation.questionsetup.questionsetuptable')->with( 'evaluationQuestion', $evaluationQuestion);

            }
            else if($request->has('insert') && $request->get('insert') == 'insert'){

                  $evaluationQuestion->insert([
                        'question'=>$request->get('question'),
                        'maxrating'=>$request->get('maxrate'),
                        'createdby'=>auth()->user()->id,
                        'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                  ]);

            }
            else if($request->has('info') && $request->get('info') == 'info'){

                return $evaluationQuestion->where('id',$request->get('id'))->get();

            }
            else if($request->has('update') && $request->get('update') == 'update'){

                  $evaluationQuestion->update([
                              'question'=>$request->get('question'),
                              'maxrating'=>$request->get('maxrate'),
                              'updatedby'=>auth()->user()->id,
                              'updateddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                        ]);
  
            }
            else if($request->has('delete') && $request->get('delete') == 'delete'){

                  $evaluationQuestion->update([
                              'deleted'=>1,
                              'deletedby'=>auth()->user()->id,
                              'deleteddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                        ]);
  
            }

      }

      public function teacherevalsetup(Request $request){

            $setup = DB::table('teacherevalsetup');
            
            if($request->has('blade') && $request->get('blade') == 'blade'){

                  $setup = $setup->first();

                  if(!isset($setup->id)){

                        $setup = (object)[
                              'instruction'=>null,
                              'q1'=>0,
                              'q1'=>0,
                              'q1'=>0,
                              'q1'=>0
                        ];
                  }

                  return view('superadmin.pages.teacherevaluation.evaluationsetup')->with('setup',$setup);

            }

            else if($request->has('update') && $request->get('update') == 'update'){

                  $setup->updateOrInsert(
                        ['id'=>1],

                        [
                        'instruction'=>$request->get('instruction'),
                        'q1'=>$request->get('q1'),
                        'q2'=>$request->get('q2'),
                        'q3'=>$request->get('q3'),
                        'q4'=>$request->get('q4')
                  ]);

            }


      }




      public function preschoolquestionsetup(Request $request){

            $preschoolquestions = DB::table('preschoolquestion')->where('deleted',0);

            

            if($request->has('question_id') && $request->get('question_id') != null){

                  $preschoolquestions = $preschoolquestions->where('id',$request->get('question_id'));
                  
            }

            if($request->has('blade') && $request->get('blade') == 'blade'){

                  return view('superadmin.pages.pre-schoolgrading.questionsetup');


            }
            else if($request->has('table') && $request->get('table') == 'table'){

                  $preschoolquestions = $preschoolquestions->get();

                  return view('superadmin.pages.pre-schoolgrading.questionsetuptable')->with('preschoolquestions',$preschoolquestions);

            }
            else if($request->has('insert') && $request->get('insert') == 'insert'){

                  $preschoolquestions->insert([
                        'question'=>$request->get('question'),
                        'createdby'=>auth()->user()->id,
                        'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                  ]);

            }
            else if($request->has('info') && $request->get('info') == 'info'){

                return $preschoolquestions->where('id',$request->get('id'))->get();

            }
            else if($request->has('update') && $request->get('update') == 'update'){

                  $preschoolquestions->update([
                              'question'=>$request->get('question'),
                              'updatedby'=>auth()->user()->id,
                              'updateddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                        ]);
  
            }
            else if($request->has('delete') && $request->get('delete') == 'delete'){

                  $preschoolquestions->update([
                              'deleted'=>1,
                              'deletedby'=>auth()->user()->id,
                              'deleteddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                        ]);
  
            }

      }


      public function passwordresseter(Request $request){

            // return Request::fullUrl();

            if($request->has('studentnparent') && $request->get('studentnparent') == 'studentnparent'){

                  $users = DB::table('users')->where('users.deleted',0)->whereIn('type',['7','9']);

                  $users->join('usertype',function($join){
                        $join->on('users.type','=','usertype.id');
                  })
                  ->select('users.*','usertype.utype');   

            }
            else if($request->has('facultynstaff') && $request->get('facultynstaff') == 'facultynstaff'){

                  $users = DB::table('users')->whereNotIn('type',['7','9','6','12','17']);

                  $users->join('usertype',function($join){
                        $join->on('users.type','=','usertype.id');
                  })
                  ->select('users.*','usertype.utype');   

            }


            if($request->has('search') && $request->get('search') != 'null'){

                  $search = $request->get('search');
                      
                  $users->where(function($query) use( $search ) {
                        $query->where('users.name','like','%'.$search.'%');
                        $query->orWhere('usertype.utype','like','%'.$search.'%');
                        $query->orWhere('users.email','like','%'.$search.'%');
                  });
                  
            }

            $userCount = $users->count();

            if($request->has('take') && $request->get('take') != 'null'){

                  $users->take($request->get('take'));

            }

            if($request->has('skip') && $request->get('skip') != 'null'){

                  if($request->has('take')){

                        $users->skip( ( $request->get('skip')-1 ) * $request->get('take'));

                  }
                  else{

                        $users->take($count)->skip($request->get('skip'));

                  }

            }

            if($request->has('blade') && $request->get('blade') == 'blade'){

                  // return auth()->user()->type;

                  if($request->has('studentnparent') && $request->get('studentnparent') == 'studentnparent'){

                        
                        if(auth()->user()->type == 6){

                              return view('adminPortal.pages.passreseter.parentnstudent');

                        }
                        else if(auth()->user()->type == 17){

                              return view('superadmin.pages.passreseter.parentnstudent');

                        }
      
                  }
                  else if($request->has('facultynstaff') && $request->get('facultynstaff') == 'facultynstaff'){

                        if(auth()->user()->type == 6){

                              return view('adminPortal.pages.passreseter.facultynstaff');

                        }
                        else if(auth()->user()->type == 17){

                              return view('superadmin.pages.passreseter.facultynstaff');
                        }
      
                      
      
                  }

            }
            if($request->has('table') && $request->get('table') == 'table'){

                  $data = array((object)[
                        'data'=>$users->get(),
                        'count'=>$userCount
                  ]);

                  return view('superadmin.pages.passreseter.userstable')->with('data',$data);

            }

      }

      public function unenrollstudent(Request $request){


            if($request->get('blade') == 'blade' && $request->has('blade')){


                  $gradelevel = DB::table('gradelevel')->where('deleted',0)->orderBy('sortid')->select('levelname','id')->get();
                  $sections = DB::table('sections')->where('deleted',0)->select('levelid','sectionname','id')->orderBy('sectionname')->get();
                  $students = DB::table('studinfo')
                                    ->join('gradelevel',function($join){
                                          $join->on('studinfo.levelid','=','gradelevel.id');
                                          $join->where('gradelevel.deleted',0);
                                    })
                                    ->join('studentstatus',function($join){
                                          $join->on('studinfo.studstatus','=','studentstatus.id');
                                    })
                                    ->select('lastname','firstname','sid','studstatus','levelname','sectionname','description','studinfo.id')
                                    ->get();

                  return view('superadmin.pages.unenrollstudent.unenrollestudent')
                              ->with('gradelevel',$gradelevel)
                              ->with('students',$students)
                              ->with('sections',$sections);

            }
            elseif($request->get('student') == 'student' && $request->has('student')){

                  $acadprog = DB::table('gradelevel')->where('id',$request->get('gradelevel'))->select('acadprogid')->first()->acadprogid;
                  $activeSy = DB::table('sy')->where('isactive',1)->first();
                  $activeSem = DB::table('semester')->where('isactive',1)->first();

                  if($acadprog == 5){

                        $students = DB::table('sh_enrolledstud')
                                          ->join('studinfo',function($join){
                                                $join->on('sh_enrolledstud.studid','=','studinfo.id');
                                                $join->where('sh_enrolledstud.deleted',0);
                                          })
                                          ->join('gradelevel',function($join){
                                                $join->on('sh_enrolledstud.levelid','=','gradelevel.id');
                                                $join->where('gradelevel.deleted',0);
                                          })
                                          ->join('studentstatus',function($join){
                                                $join->on('sh_enrolledstud.studstatus','=','studentstatus.id');
                                          })
                                          ->where('syid',$activeSy->id)
                                          ->where('sh_enrolledstud.sectionid',$request->get('section'))
                                          ->select('lastname','firstname','sid','sh_enrolledstud.studstatus','levelname','sectionname','description','studid')
                                          ->get();


                  }elseif($acadprog == 6){


                        $students = DB::table('college_enrolledstud')
                                          ->join('studinfo',function($join){
                                                $join->on('college_enrolledstud.studid','=','studinfo.id');
                                                $join->where('college_enrolledstud.deleted',0);
                                          })
                                          ->join('gradelevel',function($join){
                                                $join->on('enrolledstud.levelid','=','gradelevel.id');
                                                $join->where('gradelevel.deleted',0);
                                          })
                                          ->join('studentstatus',function($join){
                                                $join->on('college_enrolledstud.studstatus','=','studentstatus.id');
                                          })
                                          ->where('syid',$activeSy->id)
                                          ->where('college_enrolledstud.sectionid',$request->get('section'))
                                          ->select('lastname','firstname','sid','college_enrolledstud.studstatus','levelname','sectionname','description','studid')
                                          ->get();

                  
                  }else{
                  
                        $students = DB::table('enrolledstud')
                                          ->join('studinfo',function($join){
                                                $join->on('enrolledstud.studid','=','studinfo.id');
                                                $join->where('enrolledstud.deleted',0);
                                          })
                                          ->join('gradelevel',function($join){
                                                $join->on('enrolledstud.levelid','=','gradelevel.id');
                                                $join->where('gradelevel.deleted',0);
                                          })
                                          ->join('studentstatus',function($join){
                                                $join->on('enrolledstud.studstatus','=','studentstatus.id');
                                          })
                                          ->where('syid',$activeSy->id)
                                          ->where('enrolledstud.sectionid',$request->get('section'))
                                          ->select('lastname','firstname','sid','enrolledstud.studstatus','levelname','sectionname','description','studid')
                                          ->get();
                                         
                  
                  }


                  return $students;

                 



            }
            else if($request->get('table') == 'table' && $request->has('table')){

                  $students = DB::table('studinfo')
                                    ->where('deleted',0);

                  if($request->has('search') && $request->get('search') != 'null'){

                        $search = $request->get('search');
                              
                        $students = $students->where(function($query) use( $search ) {
                              $query->where('studinfo.firstname','like','%'.$search.'%');
                              $query->orWhere('studinfo.sid','like','%'.$search.'%');
                              $query->orWhere('studinfo.lastname','like','%'.$search.'%');
                        });
                        
                  }
      
                  $studentCount = $students->count();
      
                  if($request->has('take') && $request->get('take') != 'null'){
      
                        $students = $students->take($request->get('take'));
      
                  }
      
                  if($request->has('skip') && $request->get('skip') != 'null'){
      
                        if($request->has('take')){
      
                              $students = $students->skip( ( $request->get('skip')-1 ) * $request->get('take'));
      
                        }
                        else{
      
                              $students = $students->take($count)->skip($request->get('skip'));
      
                        }
      
                  }


                  $students = $students->select('firstname','lastname','id','sid','studstatus')->get();

                  $data = array((object)[
                        'data'=>   $students,
                        'count'=>   $studentCount
                  ]);

                  return view('superadmin.pages.unenrollstudent.unenrollestudenttable')
                                    ->with('data',$data);

            }

            else if($request->get('unenroll') == 'unenroll' && $request->has('unenroll')){

                  
                  $studentID = DB::table('studinfo')
                                    ->where('firstname',$request->get('fname'))
                                    ->where('lastname',$request->get('lname'))
                                    ->where('sid',$request->get('sid'))
                                    ->where('studinfo.id',$request->get('studid'))
                                    ->join('gradelevel',function($join){
                                          $join->on('studinfo.levelid','=','gradelevel.id');
                                          $join->where('gradelevel.deleted',0);
                                    })
                                    ->where('studinfo.deleted',0)
                                    ->select('studinfo.id','gradelevel.acadprogid','studinfo.levelid')
                                    ->first();

                  if(isset($studentID->id)){

                       

                        $activeSy = DB::table('sy')
                                          ->where('isactive',1)
                                          ->select('id')
                                          ->first();

                        $activeSem = DB::table('semester')
                                          ->where('isactive',1)
                                          ->select('id')
                                          ->first();

                        $studentLedger = DB::table('studledger')
                                                ->where('studledger.studid',$studentID->id)
                                                ->where('studledger.deleted',0)
                                                ->join('tuitiondetail',function($join){
                                                      $join->on('studledger.classid','=','tuitiondetail.classificationid');
                                                      $join->where('tuitiondetail.deleted',0);
                                                })
                                                ->where('studledger.syid',$activeSy->id);

                        if($studentID->acadprogid == 5 || $studentID->acadprogid == 6){

                              $studentLedger = $studentLedger->where('studledger.semid',$activeSem->id);
                        }

                        $studentLedger = $studentLedger->update([
                                                      'studledger.deleted'=>1,
                                                      'studledger.deletedby'=>auth()->user()->id,
                                                      'studledger.deleteddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                                                ]);

                        $studpayscheddetail = DB::table('studpayscheddetail')
                                                ->where('studpayscheddetail.studid',$studentID->id)
                                                ->where('studpayscheddetail.deleted',0)
                                                ->where('studpayscheddetail.syid',$activeSy->id);

                        if($studentID->acadprogid == 5 || $studentID->acadprogid == 6){

                              $studpayscheddetail = $studpayscheddetail->where('studpayscheddetail.semid',$activeSem->id);
                        }

                        $studpayscheddetail->update([
                                    'studpayscheddetail.deleted'=>1,
                              ]);


                        if($studentID->acadprogid == 5){

                              DB::table('sh_enrolledstud')
                                          ->where('studid',$studentID->id)
                                          ->where('syid',$activeSy->id)
                                          ->where('semid',$activeSem->id)
                                          ->where('deleted',0)
                                          ->take(1)
                                          ->update([
                                                'deleted'=>1,
                                                'deletedby'=>auth()->user()->id,
                                                'deleteddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                                          ]);


                        }elseif($studentID->acadprogid == 6){

                              DB::table('college_enrolledstud')
                                          ->where('studid',$studentID->id)
                                          ->where('syid',$activeSy->id)
                                          ->where('semid',$activeSem->id)
                                          ->where('deleted',0)
                                          ->take(1)
                                          ->update([
                                                'deleted'=>1,
                                                'deletedby'=>auth()->user()->id,
                                                'deleteddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                                          ]);

                              DB::table('college_studsched')
                                          ->where('studid',$studentID->id)
                                          ->where('college_studsched.deleted',0)
                                          ->join('college_classsched',function($join)use($activeSy, $activeSem){
                                                $join->on('college_studsched.schedid','=','college_classsched.id');
                                                $join->where('college_classsched.syid',$activeSy->id);
                                                $join->where('college_classsched.semesterID',$activeSem->id);
                                          })
                                          ->update([
                                                'college_studsched.deleted'=>1,
                                                'college_studsched.deletedby'=>auth()->user()->id,
                                                'college_studsched.deleteddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                                          ]);


                              // DB::table('college_studentcurriculum')
                              //       ->where('studid',$studentID->id)
                              //       ->update([
                              //             'deleted'=>1,
                              //             'deletedby'=>auth()->user()->id,
                              //             'deleteddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                              //       ]);

                        }
                        else{

                              DB::table('enrolledstud')
                                    ->where('studid',$studentID->id)
                                    ->where('syid',$activeSy->id)
                                    ->where('deleted',0)
                                    ->take(1)
                                    ->update([
                                          'deleted'=>1,
                                          'deletedby'=>auth()->user()->id,
                                          'deleteddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                                    ]);

                        }

                        $studentID = DB::table('studinfo')
                                    ->where('id',$studentID->id)
                                    ->where('firstname',$request->get('fname'))
                                    ->where('lastname',$request->get('lname'))
                                    ->where('sid',$request->get('sid'))
                                    ->where('deleted',0)
                                    ->take(1)
                                    ->update([
                                          'sectionid'=>null,
                                          'sectionname'=>null,
                                          'blockid'=>null,
                                          'studstatus'=>0,
                                          'deletedby'=>auth()->user()->id,
                                          'deleteddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                                    ]);

                  }
            
                  return "done";
                  
            }

      }

      public function changeUser($id){

            if (!Auth::check()) {
                  Auth::logout();
                  Session::flush();
                  return redirect('/login');
            }

            if(auth()->user()->type == 17 ){

                  Auth::loginUsingId($id);
                  $userInfo = DB::table('users')->where('id',$id)->first();
                  Session::put('currentPortal',$userInfo->type);
                  Session::put('imSuperAdmin',true);
                  self::checkUserType();
                  return redirect('home');


              


            }
            else if(Session::get('imSuperAdmin')){

                  Auth::loginUsingId($id);
                  $userInfo = DB::table('users')->where('id',$id)->first();
                  Session::put('currentPortal',$userInfo->type);
                  self::checkUserType();
                  return redirect('home');
                  
            }
            else{
                  return  back();
            }


      }

      public static function checkUserType(){

            if(auth()->user()->type=='2'){

                  $req = DB::table('perreq')
                          ->join('perreqdetail',function($join){
                              $join->on('perreq.id','=','perreqdetail.headerid');
                          })
                          ->join('users','perreqdetail.approvedby','=','users.id')
                          ->leftJoin('users as senderinfo','perreq.createdby','=','senderinfo.id')
                          ->join('sy','perreq.reqid','=','sy.id')
                          ->select(
                              'status',
                              'users.name',
                              'senderinfo.name as sendername',
                              'perreqtype',
                              'perreq.id',
                              'perreqdetail.id as perreqdetialid',
                              'perreqdetail.response',
                              'sy.sydesc'
                          )
                          ->where('perreqdetail.approvedby',auth()->user()->id)
                          ->where('status','0')
                          ->count();
  
                  
                  $principalInfo = DB::table('teacher')
                                  ->join('academicprogram','teacher.id','=','academicprogram.principalid')
                                  ->where('userid',auth()->user()->id)
                                  ->select( 'teacher.*',
                                              'academicprogram.progname',
                                              'academicprogram.id as acadid'
                                              )
                                  ->get();
  
                  
  
                  $prinInfo = DB::table('teacher')
                                  ->join('usertype',function($join){
                                      $join->on('teacher.usertypeid','=','usertype.id');
                                      $join->where('teacher.deleted',0);
                                  })
                                  ->select(
                                      'teacher.id',
                                      'teacher.lastname',
                                      'teacher.firstname',
                                      'usertype.refid'
                                  )
                                  ->where('userid',auth()->user()->id)
                                  ->where('teacher.deleted','0')
                                  ->first();
  
                  $isSeniorHighPrincipal = false;
                  $isJuniorHighPrinicpal = false;
                  $isPreSchoolPrinicpal = false;
                  $isGradeSchoolPrinicpal = false;
  
                  foreach($principalInfo as $item){
  
                      if($item->acadid=='5'){
  
                          $isSeniorHighPrincipal = true;
                      
                      }
                      else if($item->acadid=='2'){
  
                          $isPreSchoolPrinicpal = true;
  
                      }
                      else if($item->acadid=='3'){
  
                          $isGradeSchoolPrinicpal = true;
  
                      }
                      else if($item->acadid=='4'){
  
                          $isJuniorHighPrinicpal = true;
  
                      }
                      
  
                  }

                  $totalStudent = 0;
  
                  Session::put('isSeniorHighPrincipal',$isSeniorHighPrincipal);
                  Session::put('isPreSchoolPrinicpal', $isPreSchoolPrinicpal);
                  Session::put('isGradeSchoolPrinicpal', $isGradeSchoolPrinicpal);
                  Session::put('isJuniorHighPrinicpal', $isJuniorHighPrinicpal);
  
                  Session::put('principalInfo', $principalInfo);
                  Session::put('prinInfo', $prinInfo);
                  Session::put('requestCount', $req);
  
                  $teacherCount = count(SPP_Teacher::filterTeacherFaculty()[0]->data);
                  
                  Session::put('teachercount', $teacherCount);
  
                  if($isSeniorHighPrincipal){
  
                      $shStudents = SPP_EnrolledStudent::getStudent(null,1,null,null,5)[0]->count;
                      Session::put('shstudentcount', $shStudents);
  
                      $shSubjects = SPP_Subject::getAllSubject(null,1,null,null,Crypt::encrypt(5))[0]->count;
                      Session::put('shsubjectcount', $shSubjects);
  
                      $shGradeSetup = SPP_GradeSetup::getAllGradeSetup(null,10,null,null,5)[0]->count;
                      Session::put('shgradesetup', $shGradeSetup);

                      $totalStudent += $shStudents;
  
                  }
                  if($isJuniorHighPrinicpal){
  
                      $jhStudents = SPP_EnrolledStudent::getStudent(null,1,null,null,4)[0]->count;
                      Session::put('jhstudentcount', $jhStudents);
  
                      $jsSubjects = SPP_Subject::getAllSubject(null,1,null,null,Crypt::encrypt(4))[0]->count;
                      Session::put('jhsubjectcount', $jsSubjects);
  
                      $jhGradeSetup = SPP_GradeSetup::getAllGradeSetup(null,10,null,null,4)[0]->count;
                      Session::put('jhgradesetup', $jhGradeSetup);

                      $totalStudent += $jhStudents;
  
  
                  }
                  if($isPreSchoolPrinicpal){
  
                      $psStudents = SPP_EnrolledStudent::getStudent(null,1,null,null,2)[0]->count;
                      Session::put('psstudentcount', $psStudents);
  
                      $psSubjects = SPP_Subject::getAllSubject(null,1,null,null,Crypt::encrypt(2))[0]->count;
                      Session::put('pssubjectcount', $psSubjects);
  
                      $psGradeSetup = SPP_GradeSetup::getAllGradeSetup(null,10,null,null,2)[0]->count;
                      Session::put('psgradesetup', $psGradeSetup);

                      $totalStudent += $psStudents;
  
                  }
                  if($isGradeSchoolPrinicpal){
  
                      $gsStudents = SPP_EnrolledStudent::getStudent(null,1,null,null,3)[0]->count;
                      Session::put('gsstudentcount', $gsStudents);
  
                      $gsSubject = SPP_Subject::getAllSubject(null,1,null,null,Crypt::encrypt(3))[0]->count;
                      Session::put('gssubjectcount', $gsSubject);
  
                      $gsGradeSetup = SPP_GradeSetup::getAllGradeSetup(null,10,null,null,3)[0]->count;
                      Session::put('gsgradesetup', $gsGradeSetup);

                      $totalStudent += $gsStudents;
  
                  }
  
                  $sections = Section::getSections(null,1,null,null,null, $prinInfo->id)[0]->count;
                  Session::put('sectionCount', $sections);
                  Session::put('totalStudent', $totalStudent);
  
                  $blocks = SPP_Blocks::getBlock(null,6,null,null)[0]->count;
                  Session::put('blockCount', $blocks);
  
  
              }
  
  
              
  
              else if(auth()->user()->type=='7'){

                  $studentInfo = DB::table('studinfo')
                                          ->where('sid',str_replace("S", "", auth()->user()->email))
                                          ->where('studinfo.deleted',0)
                                          ->join('gradelevel','studinfo.levelid','=','gradelevel.id')
                                          ->select(
                                              'firstname',
                                              'lastname',
                                              'studinfo.id',
                                              'studinfo.gender',
                                              'studinfo.levelid',
                                              'studinfo.picurl',
                                              'gradelevel.acadprogid'
                                             )
                                          ->first();
                                          
                Session::put('studentInfo', $studentInfo);
  
              }
  
              else if(auth()->user()->type=='9'){
  
                  $studendinfo = DB::table('studinfo')
                                          ->where('sid',str_replace("P", "", auth()->user()->email))
                                          ->where('studinfo.deleted',0)
                                          ->join('gradelevel','studinfo.levelid','=','gradelevel.id')
                                          ->select(
                                              'studinfo.id',
                                              'studinfo.gender',
                                              'studinfo.levelid',
                                              'studinfo.picurl',
                                              'gradelevel.acadprogid'
                                             )
                                          ->first();
                      
                   Session::put('studentInfo', $studendinfo);
    
              }
              else{
  
                  $usertype = DB::table('usertype')->where('id',auth()->user()->type)->first();
  
                  if(isset($usertype->refid) && $usertype->refid == 20){
  
                      
                      $principalInfo = DB::table('teacher')
                                      ->join('teacheracadprog','teacher.id','=','teacheracadprog.teacherid')
                                      ->where('userid',auth()->user()->id)
                                      ->select( 'teacher.*',
                                                  'teacheracadprog.acadprogid as acadid'
                                                  )
                                      ->get();
  
                      foreach($principalInfo as $item){
  
                          if($item->acadid=='5'){
  
                              $isSeniorHighPrincipal = true;
                          
                          }
                          else if($item->acadid=='2'){
  
                              $isPreSchoolPrinicpal = true;
  
                          }
                          else if($item->acadid=='3'){
  
                              $isGradeSchoolPrinicpal = true;
  
                          }
                          else if($item->acadid=='4'){
  
                              $isJuniorHighPrinicpal = true;
  
                          }
                          
  
                      }
  
                      Session::put('isSeniorHighPrincipal',$isSeniorHighPrincipal);
                      Session::put('isPreSchoolPrinicpal', $isPreSchoolPrinicpal);
                      Session::put('isGradeSchoolPrinicpal', $isGradeSchoolPrinicpal);
                      Session::put('isJuniorHighPrinicpal', $isJuniorHighPrinicpal);
  
                      
                      $prinInfo = DB::table('teacher')
                                          ->join('usertype',function($join){
                                                $join->on('teacher.usertypeid','=','usertype.id');
                                                $join->where('teacher.deleted',0);
                                          })
                                          ->select(
                                                'teacher.id',
                                                'teacher.lastname',
                                                'teacher.firstname',
                                                'usertype.refid'
                                          )
                                          ->where('userid',auth()->user()->id)
                                          ->where('teacher.deleted','0')
                                          ->first();
  
                      Session::put('principalInfo', $principalInfo);
                      Session::put('prinInfo', $prinInfo);
                      Session::put('isAssistantPrin',true);
                     
  
                      $teacherCount = count(SPP_Teacher::filterTeacherFaculty()[0]->data);
                      
                      Session::put('teachercount', $teacherCount);
                      $totalStudent = 0;
  
                      if($isSeniorHighPrincipal){
  
                          $shStudents = SPP_EnrolledStudent::getStudent(null,1,null,null,5)[0]->count;
                          Session::put('shstudentcount', $shStudents);
  
                          $shSubjects = SPP_Subject::getAllSubject(null,1,null,null,Crypt::encrypt(5))[0]->count;
                          Session::put('shsubjectcount', $shSubjects);
  
                          $shGradeSetup = SPP_GradeSetup::getAllGradeSetup(null,10,null,null,5)[0]->count;
                          Session::put('shgradesetup', $shGradeSetup);

                          $totalStudent += $shStudents;
  
                      }
                      if($isJuniorHighPrinicpal){
  
                          $jhStudents = SPP_EnrolledStudent::getStudent(null,1,null,null,4)[0]->count;
                          Session::put('jhstudentcount', $jhStudents);
  
                          $jsSubjects = SPP_Subject::getAllSubject(null,1,null,null,Crypt::encrypt(4))[0]->count;
                          Session::put('jhsubjectcount', $jsSubjects);
  
                          $jhGradeSetup = SPP_GradeSetup::getAllGradeSetup(null,10,null,null,4)[0]->count;
                          Session::put('jhgradesetup', $jhGradeSetup);

                          $totalStudent += $jhStudents;
  
  
                      }
                      if($isPreSchoolPrinicpal){
  
                          $psStudents = SPP_EnrolledStudent::getStudent(null,1,null,null,2)[0]->count;
                          Session::put('psstudentcount', $psStudents);
  
                          $psSubjects = SPP_Subject::getAllSubject(null,1,null,null,Crypt::encrypt(2))[0]->count;
                          Session::put('pssubjectcount', $psSubjects);
  
                          $psGradeSetup = SPP_GradeSetup::getAllGradeSetup(null,10,null,null,2)[0]->count;
                          Session::put('psgradesetup', $psGradeSetup);

                          $totalStudent += $psStudents;
  
                      }
                      if($isGradeSchoolPrinicpal){
  
                          $gsStudents = SPP_EnrolledStudent::getStudent(null,1,null,null,3)[0]->count;
                          Session::put('gsstudentcount', $gsStudents);
  
                          $gsSubject = SPP_Subject::getAllSubject(null,1,null,null,Crypt::encrypt(3))[0]->count;
                          Session::put('gssubjectcount', $gsSubject);
  
                          $gsGradeSetup = SPP_GradeSetup::getAllGradeSetup(null,10,null,null,3)[0]->count;
                          Session::put('gsgradesetup', $gsGradeSetup);

                          $totalStudent += $gsStudents;

  
                      }
  
                      $sections = Section::getSections(null,1,null,null,null, $prinInfo->id)[0]->count;
                      Session::put('sectionCount', $sections);
  
                      $blocks = SPP_Blocks::getBlock(null,6,null,null)[0]->count;
                      Session::put('blockCount', $blocks);

                      Session::put('totalStudent', $totalStudent);
  
                  }
  
              }



      }


      public function nocrestudents(Request $request){




            if($request->get('blade') == 'blade' && $request->has('blade')){

                  $students = \App\Models\Student\StudentFixer::no_credential_student_get();

                  return view('superadmin.pages.nocreds.nocreds')
                              ->with('students',$students);

            }
            else if($request->get('count') == 'count' && $request->has('count')){

                  return DB::table('studinfo')
                                    ->where('studinfo.studstatus',1)
                                    ->whereNull('studinfo.userid')
                                    ->count();
            }
            else if($request->get('table') == 'table' && $request->has('table')){

                  $students = DB::table('studinfo')
                              ->where('studinfo.studstatus',1)
                              ->whereNull('studinfo.userid');

                  if($request->has('search') && $request->get('search') != 'null'){

                        $search = $request->get('search');
                            
                        $students->where(function($query) use( $search ) {
                              $query->where('studinfo.firstname','like','%'.$search.'%');
                              $query->orWhere('studinfo.lastname','like','%'.$search.'%');
                              $query->orWhere('studinfo.sid','like','%'.$search.'%');
                        });
                        
                  }
      
                  $studentCount = $students->count();
      
                  if($request->has('take') && $request->get('take') != 'null'){
      
                        $students->take($request->get('take'));
      
                  }
      
                  if($request->has('skip') && $request->get('skip') != 'null'){
      
                        if($request->has('take')){
      
                              $students->skip( ( $request->get('skip')-1 ) * $request->get('take'));
      
                        }
                        else{
      
                              $students->take($count)->skip($request->get('skip'));
      
                        }
      
                  }
                  
                  
                  $data = array((object)[
                        'count'=>$studentCount ,
                        'data'=>$students->select('studinfo.firstname','studinfo.lastname','studinfo.sid','studinfo.id')->get() 
                  ]);

                  return view('superadmin.pages.nocreds.nocredstable')
                              ->with('data',$data);

            }
            else if($request->get('fix') == 'fix' && $request->has('fix')){


                  $school_abbreviation = DB::table('schoolinfo')->select('abbreviation','websitelink')->first();
                  

                  if($school_abbreviation->abbreviation == null){

                        return array((object)[
                              'status'=>0,
                              'data'=>'School abbreviation is not configured.'
                        ]);

                  }
                  if($school_abbreviation->websitelink == null){

                        return  array((object)[
                              'status'=>0,
                              'data'=>'School website link is not configured.'
                        ]);

                  }

                  $abbreviation = $school_abbreviation->abbreviation;
                  $websitelink = $school_abbreviation->websitelink;

                  $item = DB::table('studinfo')
                                          ->where('studinfo.id',$request->get('id'))
                                          ->where('firstname',$request->get('firstname'))
                                          ->where('firstname',$request->get('firstname'))
                                          ->where('sid',$request->get('sid'))
                                          ->join('gradelevel',function($join){
                                                $join->on('studinfo.levelid','=','gradelevel.id');
                                                $join->where('gradelevel.deleted',0);
                                          })
                                          ->select(
                                                'studinfo.id','studinfo.sid',
                                                'firstname','lastname',
                                                'mothername','fathername','guardianname',
                                                'ismothernum','isfathernum','isguardannum',
                                                'contactno','fcontactno','mcontactno','gcontactno',
                                                'levelname'
                                          )
                                          ->first();

                  if(isset($item->id)){

                        $studentname = $item->firstname.', '.$item->lastname;
                        $pname = null;
                        $pcontactno = null;

                        DB::table('studinfo')->where('id',$item->id)->update(['userid'=>null]);

                        $studentID = DB::table('users')
                              ->insertGetID([
                                    'name'=>$studentname,
                                    'email'=>'S'.$item->sid,
                                    'type'=>'7',
                                    'password'=>Hash::make('123456')
                              ]);

                        if($item->ismothernum == 1){

                              $pcontactno = $item->mcontactno;
                              $pname = $item->mothername;

                              if($item->mothername == ''){

                                    $pname = $studentname;

                              }

                              $id = DB::table('users')
                                    ->insertGetID([
                                    'name'=>$pname,
                                    'email'=>'P'.$item->sid,
                                    'type'=>'9',
                                    'password'=>Hash::make('123456')
                                    ]);

                        }
                        elseif($item->isfathernum == 1){

                              $pname = $item->fathername;
                              $pcontactno = $item->fcontactno;

                              if($item->mothername == ''){

                                    $pname = $studentname;
                                    
                              }

                              $id = DB::table('users')
                                    ->insertGetID([
                                    'name'=>$pname,
                                    'email'=>'P'.$item->sid,
                                    'type'=>'9',
                                    'password'=>Hash::make('123456')
                                    ]);

                        }
                        elseif($item->isguardannum == 1){

                              $pname = $item->guardianname;
                              $pcontactno = $item->gcontactno;

                              if($item->guardianname == ''){

                                    $pname = $studentname;
                                    
                              }

                              $id = DB::table('users')
                                    ->insertGetID([
                                    'name'=>$pname,
                                    'email'=>'P'.$item->sid,
                                    'type'=>'9',
                                    'password'=>Hash::make('123456')
                                    ]);

                        }
                        else{

                              $pname = $item->guardianname;
                              $pcontactno = $item->gcontactno;

                              if($item->guardianname == ''){

                                    $pname = $studentname;
                                    
                              }

                              $id = DB::table('users')
                                    ->insertGetID([
                                    'name'=>$pname,
                                    'email'=>'P'.$item->sid,
                                    'type'=>'9',
                                    'password'=>Hash::make('123456')
                                    ]);

                        }

                        DB::table('studinfo')->where('id',$item->id)->update(['userid'=>$studentID]);

                        if(substr($item->contactno, 0,1)=='0')
                        {
                              $contactno = '+63' . substr($item->contactno, 1);
                        }else{
                              $contactno = null;
                        }

                     



                        $studentMessage = $abbreviation.' message: '.$item->firstname.' '.$item->lastname.'. ,'.$item->levelname.
                        'Please login to you portals
                        Student Login: S'.$item->sid.'
                        Default Password: 123456
                        Visit: '.$websitelink;


                        $parentMessage = $abbreviation.' message: '.$item->firstname.' '.$item->lastname.'. ,'.$item->levelname.
                        'Please login to you portals
                        Parent Login: P'.$item->sid.'
                        Default Password: 123456
                        Visit: '.$websitelink;

                        $smsStud = db::table('smsbunker')
                                                      ->insert([
                                                            'message' => $studentMessage,
                                                            'receiver' => $contactno,
                                                            'smsstatus' => 0
                                    ]);
                                    
                        if(substr($pcontactno, 0,1)=='0')
                        {
                              $pcontactno = '+63' . substr($pcontactno, 1);
                        }else{
                              $contactno = null;
                        }
                  

                        $smsParent = db::table('smsbunker')
                                    ->insert([
                                    'message' => $parentMessage,
                                    'receiver' => $pcontactno,
                                    'smsstatus' => 0
                                    ]);
            
                        return  array((object)[
                              'status'=>1,
                              'data'=>'School website link is not configured.'
                        ]);

                  }

            }


      }

      public function smsbunkerEnrollment(Request $request){

            if($request->get('blade') == 'blade' && $request->has('blade')){

                  return view('superadmin.pages.smsbunker.textblast.smsbunkerblade');

            }
            elseif($request->get('evaluate') == 'evaluate' && $request->has('evaluate')){

                  $parentSent = DB::table('smsbunker')
                                    ->where('message','like','%P'.$request->get('sid').'%')
                                    ->get();
                  $studentSent = DB::table('smsbunker')
                                    ->where('message','like','%S'.$request->get('sid').'%')
                                    ->get();

                  $studentSentEval = 0;
                  $parentSentEval = 0;

                  foreach($parentSent as $item){

                        if(isset($item->receiver)){

                              if(strlen($item->receiver) == 13){
      
                                   $parentSentEval = 1;
      
                              }
      
                        }

                  }

                  foreach($studentSent as $item){ 

                        if(isset($item->receiver)){

                              if(strlen($item->receiver) == 13){

                                    $studentSentEval = 1;
      
                              }
                              
                        }

                  }

                  $data = array((object)[
                        'parentSent'=>$parentSentEval,
                        'studentSent'=>$studentSentEval
                  ]);

                  return $data;

            }
            elseif($request->get('send') == 'send' && $request->has('send')){

                  if($request->get('date_enrolled') != null && $request->has('date_enrolled')){

                        $activeSy = DB::table('sy')
                                          ->where('isactive',1)
                                          ->select('id')
                                          ->first();

                        $activeSem = DB::table('semester')
                                          ->where('isactive',1)
                                          ->select('id')
                                          ->first();

                        if($request->get('acadprogid') == 5){

                              $dateEnrolled = DB::table('sh_enrolledstud')
                                                ->where('studid',$request->get('studid'))
                                                ->where('deleted',0)
                                                ->where('syid',$activeSy->id)
                                                ->where('semid',$activeSem->id)
                                                ->select('dateenrolled')
                                                ->first();

                        }else if($request->get('acadprogid') == 6){

                              // $isEnrolled = DB::table('sh_enrolledstud')
                              //             ->where('studid',$request->get('studid'))
                              //             ->where('deletd',0)
                              //             ->where('syid',$activeSy->id)
                              //             ->where('semid',$activeSem->id)
                              //             ->select('dateenrolled')
                              //             ->first();

                        }else{

                              $dateEnrolled =  DB::table('enrolledstud')
                                          ->where('studid',$request->get('studid'))
                                          ->where('deleted',0)
                                          ->where('syid',$activeSy->id)
                                          ->select('dateenrolled')
                                          ->first();


                        }

                        if($request->get('date_enrolled') <= $dateEnrolled->dateenrolled){

                              if( ( $request->get('pcontactno') != null && $request->get('pcontactno') != 'null' ) && $request->has('pcontactno')){
      
                                    $contactno = '+63' . substr($request->get('pcontactno'), 1);

                                    $smsParent = db::table('smsbunker')
                                                      ->insert([
                                                      'message' => 'Your student '. $request->get('firstname') .' is already enrolled. Portal Credential - Username:P'.$request->get('sid') . ' Default Password: 123456',
                                                      'receiver' => $contactno,
                                                      'smsstatus' => 0
                                                      ]);
            
                              }
            
                              if( ( $request->get('scontactno') != null && $request->get('scontactno') != 'null' ) && $request->has('scontactno')){
            
                                    $contactno = '+63' . substr($request->get('scontactno'), 1);
            
                                    DB::table('smsbunker')
                                          ->insert([
                                                'message' => $request->get('firstname') .' you are already enrolled. Portal Credential - Username:S'.$request->get('sid'). ' Default Password: 123456',
                                                'receiver' => $contactno,
                                                'smsstatus' => 0
                                    ]);
            
                              }
      
                              return 1;
                        }
                        else{
      
                              return 0;
                        }
                  
                  }
                  elseif($request->get('student') != null && $request->has('student')){

                        $studentInfo = DB::table('studinfo')
                                                ->where('sid',$request->get('sid'))
                                                ->join('gradelevel',function($join){
                                                      $join->on('studinfo.levelid','=','gradelevel.id');
                                                      $join->where('gradelevel.deleted','0');
                                                })
                                                ->select('levelname','lastname','firstname')
                                                ->first();

                        $contactno = '+63' . substr($request->get('contact'), 1);

                        $studentMessage = 'CSL message: '.$studentInfo->firstname.' - '.$studentInfo->levelname.
                        '. Please login to you portal. Student Username: S'.$request->get('sid').'. Default Password: 123456. Visit: http://csl-essentiel.ckgroup.ph/';

                        DB::table('smsbunker')
                              ->insert([
                                    'message' => $studentMessage,
                                    'receiver' => $contactno,
                                    'smsstatus' => 0
                        ]);



                  }
                  elseif($request->get('parent') == 'parent' && $request->has('parent')){
                        
                        $studentInfo = DB::table('studinfo')
                                                ->where('sid',$request->get('sid'))
                                                ->join('gradelevel',function($join){
                                                      $join->on('studinfo.levelid','=','gradelevel.id');
                                                      $join->where('gradelevel.deleted','0');
                                                })
                                                ->select('levelname','lastname','firstname')
                                                ->first();

                        $parentMessage = 'CSL message: '.$studentInfo->firstname.' - '.$studentInfo->levelname.
                        '. Please login to you portal. Parent Username: S'.$request->get('sid').'. Default Password: 123456. Visit: http://csl-essentiel.ckgroup.ph/';

                        $contactno = '+63' . substr($request->get('contact'), 1);
      
                        $smsParent = db::table('smsbunker')
                                          ->insert([
                                          'message' => $parentMessage,
                                          'receiver' => $contactno,
                                          'smsstatus' => 0
                                          ]);

                  }
                  else{

                        if($request->get('pcontactno') != null && $request->has('pcontactno')){
      
                              $contactno = '+63' . substr($request->get('pcontactno'), 1);
      
                              $smsParent = db::table('smsbunker')
                                                ->insert([
                                                'message' => 'Your student '. $request->get('firstname') .' is already enrolled. Portal Credential - Username:P'.$request->get('sid') . ' Default Password: 123456',
                                                'receiver' => $contactno,
                                                'smsstatus' => 0
                                                ]);
      
                        }
      
                        if($request->get('scontactno') != null && $request->has('scontactno')){
      
                              $contactno = '+63' . substr($request->get('scontactno'), 1);
      
                              DB::table('smsbunker')
                                    ->insert([
                                          'message' => $request->get('firstname') .' you are already enrolled. Portal Credential - Username:S'.$request->get('sid'). ' Default Password: 123456',
                                          'receiver' => $contactno,
                                          'smsstatus' => 0
                              ]);
      
                        }

                        return 1;

                  }

                  


            }
            elseif($request->get('students') == 'students' && $request->has('students')){

                  $studentlist = DB::table('studinfo')
                                    ->where('deleted',0)
                                    ->whereIn('studstatus',[1,2,4])
                                    ->select(
                                          'lastname',
                                          'firstname',
                                          'sid',
                                          'mcontactno',
                                          'fcontactno',
                                          'gcontactno',
                                          'mothername',
                                          'fathername',
                                          'guardianname',
                                          'ismothernum',
                                          'isfathernum',
                                          'isguardannum',
                                          'contactno'
                                    );


                  // return "sdfsdf";

                  if($request->get('specified') != null && $request->has('specified')){

                      if($request->get('specified') == 1){

                        $studentcount = $studentlist->where('ismothernum',1);

                      }
                      else  if($request->get('specified') == 2){

                        $studentcount = $studentlist->where('isfathernum',1);

                      }
                      else if($request->get('specified') == 3){
                            
                        $studentcount = $studentlist->where('isguardannum',1);

                      }
                      else if($request->get('specified') == 4){

                        $studentcount = $studentlist  
                                          ->where(function($query){
                                                $query->where('ismothernum',0);
                                                $query->orWhere('ismothernum','=',null);
                                          })
                                          ->where(function($query){
                                                $query->where('isfathernum',0);
                                                $query->orWhere('isfathernum','=',null);
                                          })
                                          ->where(function($query){
                                                $query->where('isguardannum',0);
                                                $query->orWhere('isguardannum','=',null);
                                          });
                                       

                      }

                  }

                  $studentcount = $studentlist->count();

                  $ismothernum =  DB::table('studinfo')
                                    ->where('deleted',0)
                                    ->where('ismothernum',1)
                                    ->whereNotNull('mcontactno')
                                    ->whereIn('studstatus',[1,2,4])
                                    ->count();

                  $isfathernum =  DB::table('studinfo')
                                    ->where('deleted',0)
                                    ->where('isfathernum',1)
                                    ->whereNotNull('fcontactno')
                                    ->whereIn('studstatus',[1,2,4])
                                    ->count();

                  $isguardannum =  DB::table('studinfo')
                                    ->where('deleted',0)
                                    ->where('isguardannum',1)
                                    ->whereNotNull('gcontactno')
                                    ->whereIn('studstatus',[1,2,4])
                                    ->count();

                  $notspecified =  DB::table('studinfo')
                                    ->whereIn('studstatus',[1,2,4])
                                    ->where('deleted',0)
                                    ->where('ismothernum',0)
                                    ->where('isfathernum',0)
                                    ->where('isguardannum',0)
                                   
                                    ->count();
                  
                
                  if($request->get('skip') != null && $request->has('skip')){

                        $studentlist = $studentlist->skip( ( $request->get('skip') - 1 ) * 10);

                  }

                  if($request->get('take') != null && $request->has('take')){

                        $studentlist = $studentlist->take(10);

                  }

                  $studentlist = $studentlist->get();

                  $data = array((object)[
                        'count'=>$studentcount,
                        'data'=>$studentlist,
                        'ismothernum'=>$ismothernum,
                        'isfathernum'=>$isfathernum,
                        'isguardannum'=>$isguardannum,
                        'notspecified'=>$notspecified
                  ]);
                                 

                  return view('superadmin.pages.smsbunker.textblast.smsbunkertable')
                              ->with('data',$data);

            }
            if($request->get('receivers') == 'receivers' && $request->has('receivers')){

                  $studentlist = DB::table('studinfo')
                                    ->join('gradelevel',function($join){
                                          $join->on('studinfo.levelid','=','gradelevel.id');
                                          $join->where('gradelevel.deleted',0);
                                    })
                                    ->where('studinfo.deleted',0)
                                    ->whereIn('studstatus',[1,2,4])
                                    ->select(
                                          'studinfo.id',
                                          'lastname',
                                          'firstname',
                                          'sid',
                                          'mcontactno',
                                          'fcontactno',
                                          'gcontactno',
                                          'mothername',
                                          'fathername',
                                          'guardianname',
                                          'ismothernum',
                                          'isfathernum',
                                          'isguardannum',
                                          'contactno',
                                          'gradelevel.acadprogid'
                                    )->get();
                        
                  return view('superadmin.pages.smsbunker.textblast.receiverstable')
                                    ->with('students',$studentlist);
            }



      }



      public function generateAdminAdminPass(){

            do{
 
                  $lowcaps = 'abcdefghijklmnopqrstuvwxyz';

                  $permitted_chars = '0123456789'.$lowcaps;
                  
                  $input_length = strlen($permitted_chars);
      
                  $random_string = '';
      
                  for($i = 0; $i < 10; $i++) {
      
                        $random_character = $permitted_chars[mt_rand(0, $input_length - 1)];
                        $random_string .= $random_character;
      
                  }

                  $hashed = Hash::make($random_string);

            }while(strpos($hashed,'/') || strpos($hashed,'\'') );

            DB::table('users')->updateOrInsert([
                        'name'=>'adminadmin',
                        'email'=>'adminadmin',
                        'type'=>'12',
                  ],
                  [
                        'password'=>$hashed,
                        'passwordstr'=>$random_string
                  ]);

            $data = array((object)[
                  'code'=>$random_string,
                  'hash'=>$hashed
            ]);

            return $data;

      }

      public function generateAdminPass(){

            do{
 
                  $lowcaps = 'abcdefghijklmnopqrstuvwxyz';

                  $permitted_chars = '0123456789'.$lowcaps;
                  
                  $input_length = strlen($permitted_chars);
      
                  $random_string = '';
      
                  for($i = 0; $i < 10; $i++) {
      
                        $random_character = $permitted_chars[mt_rand(0, $input_length - 1)];
                        $random_string .= $random_character;
      
                  }

                  $hashed = Hash::make($random_string);

            }while(strpos($hashed,'/') || strpos($hashed,'\'') );

            DB::table('users')->updateOrInsert([
                        'name'=>'admin',
                        'email'=>'admin',
                        'type'=>'6',
                  ],
                  [
                        'password'=>$hashed,
                        'passwordstr'=>$random_string
                  ]);

            $data = array((object)[
                  'code'=>$random_string,
                  'hash'=>$hashed
            ]);

            return $data;

      }

      public function generateckpass(){

            do{
 
                  $hashed = Hash::make('CK_publishin6');

            }while(strpos($hashed,'/') || strpos($hashed,'\'') );

            DB::table('users')
                  ->where('type','17')
                  ->update([
                        'password'=>$hashed
                  ]);

            return 1;

      }


      //grading system v2
      public function gradingsystem(Request $request){

            
            if($request->get('blade') == 'blade' && $request->has('blade')){

                  return view('superadmin.pages.gradingsystem.gradingsetup');

            }    
            else if($request->get('gradesysdetail') == 'gradesysdetail' && $request->has('gradesysdetail')){

                  $sysDetail = DB::table('grading_system_detail')
                                    ->where('headerid',$request->get('gradesysid'))
                                    ->where('deleted',0)
                                    ->orderBy('sort')
                                    ->orderBy('group')
                                    ->get();


                  $gs = DB::table('grading_system')
                              ->where('id',$request->get('gradesysid'))
                              ->where('deleted',0)
                              ->first();


                  return view('superadmin.pages.gradingsystem.gradingsysdetailtable')
                                    ->with('gs',$gs)
                                    ->with('sysDetail',$sysDetail);

            }   
            else if($request->get('ratingvaluetable') == 'ratingvaluetable' && $request->has('ratingvaluetable')){

                  $ratingValue = DB::table('grading_system_ratingvalue')
                                    ->where('gsid',$request->get('gradesysid'))
                                    ->where('deleted',0)
                                    ->orderBy('sort')
                                    ->get();

              
                  return view('superadmin.pages.gradingsystem.ratingvaluetable')
                                    ->with('ratingValue',$ratingValue);

            }   
            else if($request->get('action') == 'create' && $request->has('action')){

                  if($request->get('detail') == 'detail' && $request->has('detail')){

                        return GradingSystemDetail::create_grading_system_detail(
                              $request->get('headerid'),
                              $request->get('value'),
                              $request->get('sort'),
                              $request->get('description'),
                              $request->get('items'),
                              $request->get('group'),
                              $request->get('sf9val')
                        );

                  }
                  else if($request->get('ratingvalue') == 'ratingvalue' && $request->has('ratingvalue')){

                        return RatingValue::create_rating_value(
                             $request->get('headerid'),
                             $request->get('value'),
                             $request->get('sort'),
                             $request->get('description')
                        );

                  }
                  else{

                        return GradingSystem::create_grading_system(
                              $request->get('type'),
                              $request->get('description'),
                              $request->get('acadprog'),
                              $request->get('isactive'),
                              $request->get('specification'),
                              $request->get('trackid')
                        );
                     
                  }


            }  
            else if($request->get('action') == 'update' && $request->has('action')){

                  if($request->get('detail') == 'detail' && $request->has('detail')){

                       

                        return GradingSystemDetail::update_grading_system_detail(
                              $request->get('id'),
                              $request->get('value'),
                              $request->get('sort'),
                              $request->get('description'),
                              $request->get('items'),
                              $request->get('group'),
                              $request->get('sf9val')
                        );

                  }
                  else if($request->get('ratingvalue') == 'ratingvalue' && $request->has('ratingvalue')){

                        return GradingSystem::update_rating_value(
                              $request->get('id'),
                              $request->get('sort'),
                              $request->get('description'),
                              $request->get('value')
                        );

                  }
                  else{

                        return GradingSystem::update_grading_system(
                              $request->get('id'),
                              $request->get('type'),
                              $request->get('description'),
                              $request->get('acadprog'),
                              $request->get('isactive'),
                              $request->get('specification'),
                              $request->get('trackid')
                        );

                  }

            }
            else if($request->get('action') == 'delete' && $request->has('action')){

                  if($request->get('detail') == 'detail' && $request->has('detail')){

                        return GradingSystemDetail::delete_grading_system_detail(
                              $request->get('id')
                        );

                  }
                  if($request->get('ratingvalue') == 'ratingvalue' && $request->has('ratingvalue')){
                       
                        return RatingValue::delete_grading_system(
                              $request->get('id')
                        );

                  }
                  else{

                        return GradingSystem::delete_grading_system(
                              $request->get('id')
                        );

                  }
            
            }
            else if($request->get('table') == 'table' && $request->has('table')){

                  $gradingsystem = DB::table('grading_system')
                                          ->where('deleted',0)
                                          ->join('grading_system_type',function($join){
                                                $join->on('grading_system.type','=','grading_system_type.id');
                                          })
                                          ->leftJoin('grading_system_tspecification',function($join){
                                                $join->on('grading_system.specification','=','grading_system_tspecification.id');
                                          })   
                                          ->leftJoin('academicprogram',function($join){
                                                $join->on('grading_system.acadprogid','=','academicprogram.id');
                                          })   
                                          ->select(
                                                'grading_system.acadprogid',
                                                'academicprogram.progname',
                                                'grading_system.isactive',
                                                'grading_system.id',
                                                'grading_system.trackid',
                                                'grading_system.description',
                                                'grading_system_tspecification.description as specification',
                                                'grading_system_tspecification.id as spid',
                                                'grading_system.type as gstype',
                                                'grading_system_type.description as type'
                                          )->get();

                  $gradingsystemCount = DB::table('grading_system')
                                                ->where('deleted',0)
                                                ->count();

                  $data = array((object)[
                        'data'=>$gradingsystem,
                        'count'=>$gradingsystemCount
                  ]);


                  return view('superadmin.pages.gradingsystem.gradingsystemtable')
                        ->with('data',$data);

            }   


      }

     

      public function testgrading(Request $request){

            if($request->get('test') == 'test' && $request->has('test')){

                  if($request->get('acadprog') == '2'){

                        // $gsid = $request->get('gsid');
                        // $teacher = $request->get('teacherid');

                        // return GradeStudentPreschool::grade_student_preschool($gsid,$teacher);

                  }
                  else if($request->get('acadprog') == '3'){


                  }
                  else if($request->get('acadprog') == '4'){

                        $grading_system = DB::table('grading_system')
                                                ->where('acadprogid',4)
                                                ->where('id',$request->get('gsid'))
                                                ->where('deleted',0)
                                                ->get();

                        $student = DB::table('studinfo')
                                          ->join('gradelevel',function($join) use($request){
                                                $join->on('studinfo.levelid','=','gradelevel.id');
                                                $join->where('gradelevel.deleted',0);
                                                $join->where('gradelevel.acadprogid',$request->get('acadprog'));
                                          })
                                          ->where('studstatus',1)
                                          ->select('studinfo.firstname','studinfo.lastname','studinfo.id','sectionid')
                                          ->get();


                        return view('superadmin.pages.gradingsystem.gsgrading')
                                          ->with('students',$student)
                                          ->with('acadprog',$request->get('acadprog'))
                                          ->with('grading_system',$grading_system);


                  }


            }
            else if($request->get('eval') == 'eval' && $request->has('eval')){

                  $activeSy = DB::table('sy')->where('isactive',1)->first();

                  if($request->get('acadprog') == '2'){

                        // $student = $request->get('studid');
                        // $gsid = $request->get('gsid');
                        
                        // return GradeStudentPreschool::evaluate_student_grade_preschool($gsid,$student);

                  }
                  else if($request->get('acadprog') == '3'){

                        $activeSy = DB::table('sy')->where('isactive',1)->first();

                        $gs = DB::table('grading_system')
                                    ->where('grading_system.id',$request->get('gsid'))
                                    ->where('grading_system.deleted',0)
                                    ->first();

                        if($gs->specification  == 1){


                        }elseif($gs->specification == 2){
                          
                              $grading_system = DB::table('grading_system')
                                                ->where('acadprogid',3)
                                                ->where('id',$request->get('gsid'))
                                                ->where('grading_system.deleted',0)
                                                ->get();

                              $gsdetail = DB::table('grading_system_grades_cv')
                                                ->join('grading_system_detail',function($join){
                                                      $join->on('grading_system_grades_cv.gsdid','=','grading_system_detail.id');
                                                      $join->where('grading_system_detail.deleted',0);
                                                })
                                                ->join('grading_system',function($join) use($grading_system){
                                                      $join->on('grading_system_detail.headerid','=','grading_system.id');
                                                      $join->where('grading_system.deleted',0);
                                                      $join->where('grading_system.id',$grading_system[0]->id);
                                                })
                                                ->where('grading_system_grades_cv.deleted',0)
                                                ->where('studid',$request->get('studid'))
                                                ->where('syid',$activeSy->id)
                                                ->select(
                                                      'grading_system_grades_cv.id',
                                                      'grading_system_grades_cv.q1eval',
                                                      'grading_system_grades_cv.q2eval',
                                                      'grading_system_grades_cv.q3eval',
                                                      'grading_system_grades_cv.q4eval',
                                                      'grading_system_detail.description',
                                                      'value',
                                                      'sort',
                                                      'type',
                                                      'group'
                                                )
                                                ->orderBy('sort')
                                                ->get();

                              if($grading_system[0]->type == 3 ){

                                    $rv = DB::table('grading_system_ratingvalue')
                                                      ->where('deleted',0)
                                                      ->where('gsid',$grading_system[0]->id)
                                                      ->orderBy('sort')
                                                      ->get();

                              }


                              if(count($gsdetail) > 0){

                                    $lackinggsd = DB::table('grading_system_detail')
                                                      ->where('headerid',$grading_system[0]->id)
                                                      ->where('grading_system_detail.deleted',0)
                                                      ->count();
      
                                    $widthAdditionalgs = false;
      
                                    if($lackinggsd != count($gsdetail)){
      
                                          $widthAdditionalgs = true;
      
                                    }
      
                              }


                              $nogscount = 0;

                              if(count($gsdetail) == 0){

                                    $nogscount += 1;

                              }

                              return view('superadmin.pages.gradingsystem.coregradingtable')
                                                ->with('nogscount',$nogscount)
                                                ->with('lackinggsd',$lackinggsd)
                                                ->with('ratingValue',$rv)
                                                ->with('widthAdditionalgs',$widthAdditionalgs)
                                                ->with('gsdetail',$gsdetail);
                                                
                        }
                  }

            }
            
            else if($request->get('generate') == 'generate' && $request->has('generate')){
                
                  $activeSy = DB::table('sy')->where('isactive',1)->first();
                  $proccesCount = 0;

                  if($request->get('acadprog') == '2'){

                  }
                  elseif($request->get('acadprog') == '3'){

                        try{

                              $gs = DB::table('grading_system')
                                          ->where('grading_system.id',$request->get('gsid'))
                                          ->where('grading_system.deleted',0)
                                          ->first();

                              if($gs->specification  == 1){


                                    // $grading_system_detail = DB::table('grading_system')
                                    //                               ->join('grading_system_detail',function($join){
                                    //                                     $join->on('grading_system.id','=','grading_system_detail.headerid');
                                    //                                     $join->where('grading_system_detail.deleted',0);
                                    //                               })
                                    //                               ->where('acadprogid',3)
                                    //                               ->where('grading_system.id',$request->get('gsid'))
                                    //                               ->where('grading_system.deleted',0)
                                    //                               ->select('grading_system_detail.id','grading_system_detail.headerid')
                                    //                               ->get();

                                    // foreach($grading_system_detail as $item){

                                    //       $gsgradescount = DB::table('grading_system_gsgrades')
                                    //                               ->join('grading_system_detail',function($join) use($item){
                                    //                                     $join->on('grading_system_gsgrades.gsdid','=','grading_system_detail.id');
                                    //                                     $join->where('grading_system_detail.deleted',0);
                                    //                                     $join->where('grading_system_detail.headerid',$item->headerid);
                                    //                               })
                                    //                               ->where('studid',$request->get('studid'))
                                    //                               ->where('syid',$activeSy->id)
                                    //                               ->where('grading_system_gsgrades.deleted',0)
                                    //                               ->where('subjid',$request->get('subjid'))
                                    //                               ->count();

                                    //       if($gsgradescount == 0){

                                    //             $proccesCount +=1;

                                    //             DB::table('grading_system_gsgrades')
                                    //                         ->insert([
                                    //                               'studid'=>$request->get('studid'),
                                    //                               'syid'=>$activeSy->id,
                                    //                               'gsdid'=>$item->id,
                                    //                               'sectionid'=>$request->get('section'),
                                    //                               'subjid'=>$request->get('subject'),
                                    //                               'createdby'=>auth()->user()->id,
                                    //                               'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                                    //                         ]);
                                                      
                                    //       }

                                    // }

                                    // $gsheader = DB::table('grading_system_gsgrades')
                                    //                         ->where('studid',0)
                                    //                         ->where('syid',$activeSy->id)
                                    //                         ->where('sectionid',$request->get('section'))
                                    //                         ->where('subjid',$request->get('subject'))
                                    //                         ->count();

                                    // if($gsheader == 0){

                                    //       DB::table('grading_system_gsgrades')
                                    //                         ->insert([
                                    //                               'studid'=>0,
                                    //                               'syid'=>$activeSy->id,
                                    //                               'gsdid'=>$item->id,
                                    //                               'sectionid'=>$request->get('section'),
                                    //                               'subjid'=>$request->get('subject'),
                                    //                               'createdby'=>auth()->user()->id,
                                    //                               'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                                    //                         ]);
                                    // }

                                    // return $proccesCount;

                              }elseif($gs->specification  == 2){

                                    
                                    $grading_system_detail = DB::table('grading_system')
                                                                  ->join('grading_system_detail',function($join){
                                                                        $join->on('grading_system.id','=','grading_system_detail.headerid');
                                                                        $join->where('grading_system_detail.deleted',0);
                                                                  })
                                                                  ->where('acadprogid',3)
                                                                  ->where('grading_system.id',$request->get('gsid'))
                                                                  ->where('grading_system.deleted',0)
                                                                  ->select('grading_system_detail.id')
                                                                  ->get();

                              

                                    foreach($grading_system_detail as $item){

                                          $vsgradescount = DB::table('grading_system_grades_cv')
                                                                  ->where('studid',$request->get('studid'))
                                                                  ->where('gsdid',$item->id)
                                                                  ->where('syid',$activeSy->id)
                                                                  ->where('sectionid',$request->get('section'))
                                                                  ->where('grading_system_grades_cv.deleted',0)
                                                                  ->count();

                                          if($vsgradescount == 0){

                                                $proccesCount +=1;

                                                DB::table('grading_system_grades_cv')
                                                            ->insert([
                                                                  'studid'=>$request->get('studid'),
                                                                  'syid'=>$activeSy->id,
                                                                  'gsdid'=>$item->id,
                                                                  'sectionid'=>$request->get('section'),
                                                                  'createdby'=>auth()->user()->id,
                                                                  'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                                                            ]);
                                                      
                                          }

                                    }

                                    return $proccesCount;

                              }

                        }catch(\Exception $e){

                              DB::table('zerrorlogs')
                                          ->insert([
                                          'error'=>$e,
                                          'createdby'=>auth()->user()->id,
                                          'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                                          ]);

                              return 0;

                        }

                  }

                 
            }
            else if($request->get('submit') == 'submit' && $request->has('submit')){
                  
                  if($request->get('acadprog') == '2'){

                        // try{

                        //       DB::table('grading_system_pgrades')
                        //                   ->where('studid',$request->get('studid'))
                        //                   ->where('id',$request->get('gradeid'))
                        //                   ->update([
                        //                         $request->get('gardequarter')=>$request->get('value'),
                        //                         'updatedby'=>auth()->user()->id,
                        //                         'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                        //                   ]);

                        //       return 1;

                        // }catch(\Exception $e){

                        //       DB::table('zerrorlogs')
                        //                   ->insert([
                        //                   'error'=>$e,
                        //                   'createdby'=>auth()->user()->id,
                        //                   'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                        //                   ]);

                        //       return 0;

                        // }

                  }


                  if($request->get('acadprog') == '3'){

                        try{

                              $gs = DB::table('grading_system')
                                          ->where('grading_system.id',$request->get('gsid'))
                                          ->where('grading_system.deleted',0)
                                          ->first();

                              if($gs->specification  == 1){

                                    $gradeDetail = DB::table('grading_system_gsgrades')
                                                      ->where('studid',$request->get('studid'))
                                                      ->where('id',$request->get('gradeid'));

                                    $forHPS = $gradeDetail->select('syid','sectionid','subjid','gsdid')->first();

                                    $hps = DB::table('grading_system_gsgrades')
                                                            ->where('studid',0)
                                                            ->where('syid',$forHPS->syid)
                                                            ->where('sectionid',$forHPS->sectionid)
                                                            ->where('gsdid',$forHPS->gsdid)
                                                            ->where('subjid',$forHPS->subjid);

                                    $gradeDetail->update([
                                                      $request->get('field')=>$request->get('value'),
                                                      'updatedby'=>auth()->user()->id,
                                                      'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                                                ]);


                                    $studentgsd =   DB::table('grading_system_gsgrades')
                                                            ->where('studid',$request->get('studid'))
                                                            ->where('id',$request->get('gradeid'));

                                    if(strpos($request->get('field') , 'q1')){

                                          for($x = 1; $x <= 10; $x++){

                                                $studentgsd =  $studentgsd->addSelect('g'.$x.'q1');
                                                $hps =  $hps->addSelect('g'.$x.'q1');

                                          }

                                          $quarter = 1;

                                    }
                                    elseif(strpos($request->get('field') , 'q2')){

                                          for($x = 1; $x <= 10; $x++){

                                                $studentgsd =  $studentgsd->addSelect('g'.$x.'q2');
                                                $hps =  $hps->addSelect('g'.$x.'q2');

                                          }

                                          $quarter = 2;

                                    }
                                    elseif(strpos($request->get('field') , 'q3')){

                                          for($x = 1; $x <= 10; $x++){

                                                $studentgsd =  $studentgsd->addSelect('g'.$x.'q3');
                                                $hps =  $hps->addSelect('g'.$x.'q3');

                                          }

                                          $quarter = 3;



                                    }

                                    elseif(strpos($request->get('field') , 'q4')){

                                          for($x = 1; $x <= 10; $x++){

                                                $studentgsd =  $studentgsd->addSelect('g'.$x.'q4');
                                                $hps =  $hps->addSelect('g'.$x.'q4');

                                          }

                                          $quarter = 4;


                                    }

                                    $qtotal = collect(  $studentgsd->first())->sum();

                                    $gsdid = $studentgsd->select('gsdid')->first();

                                    $gsdetail = DB::table('grading_system_detail')     
                                                            ->where('deleted',0)
                                                            ->where('id',$gsdid->gsdid)
                                                            ->select('value')
                                                            ->first();

                                    $hpssum = collect($hps->first())->sum();

                                    if($request->get('studid') != 0){

                                          if($hpssum == 0){

                                                $ps = 0;
                                                $ws = 0;

                                          }
                                          else{

                                                $ps = ( $qtotal /  $hpssum ) * 100;
                                                $ws = $ps * ( $gsdetail->value / 100 );

                                          }

                                    }else{

                                          $ps = 0;
                                          $ws = 0;
                                          $ig = 0;
                                    }

                                    DB::table('grading_system_gsgrades')
                                                ->where('studid',$request->get('studid'))
                                                ->where('id',$request->get('gradeid'))
                                                ->update([
                                                      'q'.$quarter.'total'=>$qtotal,
                                                      'psq'.$quarter=>$ps,
                                                      'wsq'.$quarter=>$ws,
                                                      'updatedby'=>auth()->user()->id,
                                                      'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                                                ]);

                                    if($request->get('studid') != 0){
                                          
                                          $ig = self::calcig(
                                                $request->get('studid'),
                                                $forHPS->syid,
                                                $forHPS->subjid,
                                                $forHPS->sectionid,
                                                $quarter
                                          );

                                          $qg = GradeCalculation::grade_transmutation($ig);
                                          
                                          if($request->get('studid') != null && $forHPS->syid != null  && $forHPS->sectionid != null && $forHPS->subjid != null ){

                                                DB::table('grading_system_gsgrades')
                                                            ->where('studid',$request->get('studid'))
                                                            ->where('syid',$forHPS->syid)
                                                            ->where('sectionid',$forHPS->sectionid)
                                                            ->where('subjid',$forHPS->subjid)
                                                            ->update([
                                                                  'igq'.$quarter=>$ig,
                                                                  'updatedby'=>auth()->user()->id,
                                                                  'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                                                            ]);
                                                
                                                DB::table('grading_system_gsgrades')
                                                            ->where('studid',$request->get('studid'))
                                                            ->where('syid',$forHPS->syid)
                                                            ->where('sectionid',$forHPS->sectionid)
                                                            ->where('subjid',$forHPS->subjid)
                                                            ->update([
                                                                  'qgq'.$quarter=>$qg,
                                                                  'updatedby'=>auth()->user()->id,
                                                                  'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                                                            ]);
                                    
                                          }


                                    }

                                    return 1;

                              }elseif($gs->specification  == 2){

                                    DB::table('grading_system_grades_cv')
                                                ->where('studid',$request->get('studid'))
                                                ->where('id',$request->get('gradeid'))
                                                ->update([
                                                      $request->get('gardequarter')=>$request->get('value'),
                                                      'updatedby'=>auth()->user()->id,
                                                      'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                                                ]);

                                    return 1;

                              }

                        }catch(\Exception $e){

                              DB::table('zerrorlogs')
                                          ->insert([
                                          'error'=>$e,
                                          'createdby'=>auth()->user()->id,
                                          'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                                          ]);

                              return 0;

                        }

                  }

            }

      }

      public static function calcig($student = null, $syid = null, $subject = null, $section = null, $quarter = null){

            $studgd = DB::table('grading_system_gsgrades')
                                    ->where('studid',$student)
                                    ->where('syid',$syid)
                                    ->where('subjid',$subject)
                                    ->where('sectionid',$section)
                                    ->where('deleted',0)
                                    ->select('wsq'.$quarter)
                                    ->sum('wsq'.$quarter);

            return  $studgd;
            

      }

      public function generate_student_credentail(Request $request){

 
            if($request->get('blade') == 'blade' && $request->has('blade')){

                  $students = DB::table('studinfo')
                                    ->whereNull('userid')
                                    ->whereIn('studstatus',[1,2,4])
                                    ->where('studinfo.deleted',0)
                                    ->join('gradelevel',function($join){
                                          $join->on('studinfo.levelid','=','gradelevel.id');
                                          $join->where('gradelevel.deleted',0);
                                    })
                                    ->select('lastname','firstname','sid','levelname','studinfo.id')
                                    ->get();

                  return view('superadmin.pages.student_credetials.gen_stud_credentials')->with('students',$students);
                           

            }
            else if($request->get('generate') == 'generate' && $request->has('generate')){

                  $checkIfExist = DB::table('users')
                                          ->where('type',7)
                                          ->where('deleted',0)
                                          ->where('email','S'.$request->get('sid'))
                                          ->get();


                  if(count($checkIfExist) == 0){


                        $studinfo = DB::table('studinfo')
                                          ->where('id',$request->get('studid'))
                                          ->where('deleted',0)
                                          ->select(
                                                'mothername',
                                                'fathername',
                                                'guardianname',
                                                'ismothernum',
                                                'isfathernum',
                                                'isguardannum',
                                                'lastname',
                                                'firstname'
                                          )
                                          ->first();



                        $guardian_name = '';

                        if($studinfo->ismothernum == 1 && $studinfo->ismothernum != null && $studinfo->mothername != null){

                              $guardian_name = $studinfo->mothername;

                        }
                        else if($studinfo->isfathernum == 1 && $studinfo->isfathernum != null && $studinfo->fathername != null){

                              $guardian_name = $studinfo->fathername;

                        }
                        else if($studinfo->isguardannum == 1 && $studinfo->isguardannum != null && $studinfo->guardianname != null){

                              $guardian_name = $studinfo->guardianname;

                        }
                        else{

                              if($studinfo->guardianname != null){

                                    $guardian_name = $studinfo->guardianname;

                              }
                              else if($studinfo->mothername != null){

                                    $guardian_name = $studinfo->mothername;

                              }
                              else if($studinfo->fathername != null){

                                    $guardian_name = $studinfo->fathername;

                              }else{

                                    $guardian_name = $studinfo->lastname.' '.$studinfo->firstname;

                              }

                        }

                        $userid = DB::table('users')
                                    ->insertGetId([
                                          'name'=>$studinfo->lastname.' '.$studinfo->firstname,
                                          'email'=>'S'.$request->get('sid'),
                                          'type'=>7,
                                          'password' => Hash::make('123456'),
                                    ]);

                        DB::table('users')
                              ->insert([
                                    'name'=>$guardian_name,
                                    'email'=>'P'.$request->get('sid'),
                                    'type'=>9,
                                    'password' => Hash::make('123456'),
                              ]);


                        DB::table('studinfo')
                              ->where('id',$request->get('studid'))
                              ->where('deleted',0)
                              ->take(1)
                              ->update([
                                    'userid'=>$userid
                              ]);


                        return $data = array((object)[
                              'status'=>1,
                              'message'=>'done'
                        ]);

                  }


            
            }


      }

      //grading system v2

      public static function studentInformation(Request $request){

        
            if($request->get('blade') == 'blade'){

                  $students = DB::table('studinfo')->where('deleted',0)->select('firstname','lastname','sid','id')->get();

                 
                  return view('superadmin.pages.studentinformation.student')
                              ->with('students',$students);

            }

      }

      public static function section_detial_fixer(Request $request){

            if($request->get('blade') == 'blade'){

                  $section_detail = SectionDetail::section_detail();

                  return view('superadmin.pages.fixer.section_detail')
                               ->with('section_detail',$section_detail);

            }
            else if($request->get('fix') == 'fix'){

                  $detail_id = $request->get('detail_id');

                  $section_detail = SectionDetail::fix_sectiondetail_sectionname($detail_id);

                  return $section_detail;

            }

      }


      public static function student_grade_fixer(Request $request){

            if($request->get('blade') == 'blade'){

                  $students = DB::table('studinfo')
                                    ->select('firstname','lastname','sid','studinfo.id','levelname','sectionname','modeoflearning.description','mol')
                                    ->where('studinfo.deleted',0)
                                    ->whereIn('studinfo.studstatus',[1,2,4])
                                    ->leftJoin('gradelevel',function($join){
                                          $join->on('studinfo.levelid','=','gradelevel.id');
                                          $join->where('gradelevel.deleted',0);
                                    })
                                    ->leftJoin('modeoflearning',function($join){
                                          $join->on('studinfo.mol','=','modeoflearning.id');
                                          $join->where('modeoflearning.deleted',0);
                                    })
                                    ->get();

                  return view('superadmin.pages.fixer.student_grade')
                                    ->with('students',$students);

            }
            else if($request->get('final_grade') == 'final_grade'){

                  $studid = $request->get('studid');

                  return Student::student_final_grade( $studid);

            }

      }


      public static function version_control(Request $request){

            if($request->get('blade') == 'blade'){

                  $versions = \App\Models\VersionControl\VersionControl::get_all_versions();

                  return view('superadmin.pages.version_control.version_control')
                                    ->with('versions',$versions);

            }

      }

      //student_promotion
      public static function student_promotion_blade(Request $request){
            return view('superadmin.pages.student_promotion');
      }

      public static function student_promotion(Request $request){
            $syid = $request->get('syid');
            $semid = $request->get('semid');
            $levelid = $request->get('levelid');
            return \App\Models\SuperAdmin\SuperAdminData::student_promotion($syid, $semid, $levelid);
      }

      public static function student_promote(Request $request){
            $syid = $request->get('syid');
            $semid = $request->get('semid');
            $studid = $request->get('studid');
            return \App\Models\SuperAdmin\SuperAdminProccess::student_promote($syid, $semid, $studid);
      }
      //student_promotion


      public static function college_students(Request $request){
            return \App\Models\SuperAdmin\SuperAdminData::all_college_students();
      }

      public function college_schedule(){
            return view('superadmin.pages.college_schedule.college_schedule');
      }

      public function college_sections(Request $request){

            $syid = $request->get('syid');
            $semid = $request->get('semid');
            $courseid = $request->get('courseid');

            return  \App\Models\SuperAdmin\SuperAdminData::college_sections($syid, $semid, $courseid);

      }

      public function curriculum_propectus(Request $request){

            $syid = $request->get('syid');
            $semid = $request->get('semid');
            $curriculumid = $request->get('curriculumid');
            
            return  \App\Models\SuperAdmin\SuperAdminData::curriculum_propectus($syid, $semid, $curriculumid);

      }

      public function enrollment_record(Request $request){
            $syid = $request->get('syid');
            $semid = $request->get('semid');
            $studid = $request->get('studid');
            return  \App\Models\SuperAdmin\SuperAdminData::student_college_enrollment($syid, $semid, $studid);

      }


      public function subject_enrollment_records(Request $request){
            $syid = $request->get('syid');
            $semid = $request->get('semid');
            $studid = $request->get('studid');
            return  \App\Models\SuperAdmin\SuperAdminData::subject_enrollment_records($syid, $semid, $studid);

      }

      public function section_schedule(Request $request){
            $sectionid = $request->get('sectionid');
            return  \App\Models\SuperAdmin\SuperAdminData::section_schedule($sectionid);

      }

      public function add_student_sched(Request $request){
            $studid = $request->get('studid');
            $schedid = $request->get('schedid');
            return  \App\Models\SuperAdmin\SuperAdminProccess::add_student_sched($studid,$schedid);
      }

      public function remove_student_sched(Request $request){
            $studid = $request->get('studid');
            $schedid = $request->get('schedid');
            return  \App\Models\SuperAdmin\SuperAdminProccess::remove_student_sched($studid,$schedid);
      }


      public function grades_fixer(){
            return view('superadmin.pages.grade_fixer');
      }

      
      //grade
      public function grade(){
            return view('superadmin.pages.grade.grade');
      }

      //grade
      public function grade_detail(){
            return  \App\Models\Grades\GradesData::all_grades_v1(2,1,1,10);
      }


      //attendance setup start
      public static function attendance_setup(Request $request){
            return view('superadmin.pages.attendancesetup.attendance_setup');
      }

      public static function attendance_setup_list(Request $request){
            $syid = $request->get('schoolyear');
            return \App\Models\AttendanceSetup\AttendanceSetupData::attendance_setup_list($syid);
      }

      public static function attendance_setup_create(Request $request){
            $month = $request->get('month');
            $days = $request->get('days');
            $syid = $request->get('syid');
            $sort = $request->get('sort');
            $year = $request->get('year');
            return \App\Models\AttendanceSetup\AttendanceSetupProccess::attendance_setup_create($month, $days, $syid, $sort, $year);
      }
      public static function attendance_setup_update(Request $request){
            $attsetupid = $request->get('attsetupid');
            $month = $request->get('month');
            $days = $request->get('days');
            $syid = $request->get('syid');
            $sort = $request->get('sort');
            $year = $request->get('year');
            return \App\Models\AttendanceSetup\AttendanceSetupProccess::attendance_setup_update($attsetupid, $month, $days, $syid, $sort, $year);
      }
      public static function attendance_setup_delete(Request $request){
            $attsetupid = $request->get('attsetupid');
            return \App\Models\AttendanceSetup\AttendanceSetupProccess::attendance_setup_delete($attsetupid);
      }
      //attendance setup end


      //validate student firstname and lastname
      public static function validate_student_name(Request $request){
            $firstname = $request->get('firstname');
            $lastname = $request->get('lastname');
            $check_studentinfo = DB::table('studinfo')->where('lastname',$lastname)->where('firstname',$firstname)->count();
            $check_prereg = DB::table('preregistration')->where('last_name',$lastname)->where('first_name',$firstname)->count();

            if($check_studentinfo > 0){
                  return array((object)[
                        'status'=>1,
                        'message'=>'*Student name information already exist. Please contact the school registrar.'
                  ]);
            }
            if($check_prereg > 0){
                  return array((object)[
                        'status'=>1,
                        'message'=>'*Student name information already exist. Please contact the school registrar.'
                  ]);
            }
            return array((object)[
                        'status'=>0,
                        'message'=>'No duplication'
                  ]);

      }

      //fixer studentschedulecoding

      public static function studentschedulecoding(Request $request){
            $syid = $request->get('syid');
            $semid = $request->get('semid');
            $studid = $request->get('studid');
            return \App\Models\StudentScheduleCode\StudentScheduleCodeData::student_schedule_code($syid,$semid,$studid);
      }

      
      public static function fixer_studentschedulecoding(){
            return view('superadmin.pages.fixer.student_schedulecode');
      }

      public static function no_code_sched(){
            return \App\Models\StudentScheduleCode\StudentScheduleCodeData::no_code_sched();
      }

      public static function available_code_sched(){
            return \App\Models\StudentScheduleCode\StudentScheduleCodeData::available_sched_code();
      }

      public static function update_studentschedcode(Request $request){
            
            $studschedid = $request->get('studschedid');
            $schedcodeid = $request->get('schedcodeid');
            return \App\Models\StudentScheduleCode\StudentScheduleCodeProcess::update_studentschedcode($studschedid,$schedcodeid);
      }
      //fixer studentschedulecoding

      //fixer studentschedulecoding
      public static function fixer_sectionchedulecode(){
            return view('superadmin.pages.fixer.collegesection_schedulecode');
      }

      public static function collegesection_no_schedcode(){
            return \App\Models\CollegeSectionSchedCode\CollegeSectionSchedCodeData::collegesection_no_schedcode();
      }

      public static function collegesection_available_schedcode(){
            return \App\Models\CollegeSectionSchedCode\CollegeSectionSchedCodeData::collegesection_available_schedcode();
      }

      public static function create_collegesection_schedcode(Request $request){
            $sectionid = $request->get('sectionid');
            $schedulecodingid = $request->get('schedulecodingid');
            return \App\Models\CollegeSectionSchedCode\CollegeSectionSchedCodeProcess::section_schedcode_create($sectionid,$schedulecodingid);
      }
      //fixer studentschedulecoding

      //setup Document Requirements
      public static function prereg_requirements(Request $request){
            return view('superadmin.pages.setup_preregquirement');
      }

      public static function prereg_requirements_list(Request $request){
            return  \App\Models\SuperAdmin\Setup\PreregRequirements\PreregRequirementsData::preregrequirements_list();
      }

      public static function prereg_requirements_create(Request $request){
            
      }

      public static function prereg_requirements_update(Request $request){
            
      }

      public static function prereg_requirements_delete(Request $request){
            
      }

      public static function prereg_requirements_update_active(Request $request){
            
      }
      //setup Document Requirements
}
