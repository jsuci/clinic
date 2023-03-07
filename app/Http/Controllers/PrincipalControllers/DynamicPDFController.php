<?php

namespace App\Http\Controllers\PrincipalControllers;

use Illuminate\Http\Request;
use DB;
use PDF;
use \Carbon\Carbon;
use App\Models\Principal\AttendanceReport;
use \Carbon\CarbonPeriod;
use App\Models\Principal\Section;
use App\Models\Principal\SPP_Attendance;
use App\Models\Principal\SPP_EnrolledStudent;
use App\Models\Principal\SPP_Gradelevel;
use App\Models\Principal\SPP_Subject;
use Crypt;
use Session;
use App\Models\Principal\GenerateGrade;
use App\Models\Grading\GradingSystem;


class DynamicPDFController extends \App\Http\Controllers\Controller
{
    function pdf($month)
    {
 
        $schoolinfo = DB::table('schoolinfo')
                        ->join('refregion','schoolinfo.region','=','refregion.regCode')
                        ->join('refcitymun','schoolinfo.division','=','refcitymun.citymunCode')
                        ->select('schoolinfo.*','refregion.regDesc','refcitymun.citymunDesc')
                        ->get();

        $sections = Section::getSections(null,null,null,null,null,Session::get('prinInfo')->id);

        date_default_timezone_set('Asia/Manila');

        $month = $month;

        $prevmonth =  $month-1;

        $schooldays = SPP_Attendance::schoolDays(Session::get('schoolYear')->id);

        if($month == 1){

            $prevmonth = 12;

        }

        $studenattendance = DB::table('studattendance')
                    ->join('sy',function($join){
                        $join->on('studattendance.syid','=','sy.id');
                        $join->where('sy.id',Session::get('schoolYear')->id);
                    })
                    ->select('studid','tdate')
                    ->get();

        foreach( $sections[0]->data  as $key=>$item){

            $item->femaleAtt = 0;
            $item->maleAtt = 0;

            $male = 0;
            $female =0;
            $maleid = null;
            $femaleid = null;

            $students = SPP_EnrolledStudent::getStudent(
                null,
                null,
                null,
                null,
                $item->acadprogid,
                $item->id,
                null,
                null,
                null,
                true,
                true
            );



            foreach($students[0]->data as $student){

                if($student->updateddatetime != null){

                    $student->curmonth = date('m',strtotime($student->updateddatetime));

                }
                else{

                    $student->curmonth = date('m',strtotime($student->dateenrolled));

                }

            }

          

            $gender = collect($students[0]->data)->countBy('gender');

            if(isset($gender['FEMALE'])){

                $female = $gender['FEMALE']; 

                $femaleid = collect($students[0]->data)->map(function($value){
                    if($value->gender == 'FEMALE'){
                        return $value->id;
                    }
                });

                foreach($schooldays as $days){

                    $item->femaleAtt = collect($days->days)->map(function($value) use ($studenattendance,$femaleid){
                                    $attCount = collect($studenattendance)
                                                    ->whereIn('studid',$femaleid)
                                                    ->where('tdate',$value->day)
                                                    ->count();
                                    return $attCount;
                                })->avg();

                }

            }
            if(isset($gender['MALE'])){

                    $male = $gender['MALE']; 

                    $maleid = collect($students[0]->data)->map(function($value){
                        if($value->gender == 'MALE'){
                            return $value->id;
                        }
                    });

                    foreach($schooldays as $days){
                        $item->maleAtt = collect($days->days)->map(function($value) use ($studenattendance,$maleid){
                                        $attCount = collect($studenattendance)
                                                        ->whereIn('studid',$maleid)
                                                        ->where('tdate',$value->day)
                                                        ->count();
                                        return $attCount;
                                    })->avg();
                        
                    }

            }

            if($male == 0 && $female == 0){

                unset($sections[0]->data[$key]);

            }
            else{

                $item->prevtransinmale =   collect($students)->where('curmonth', $prevmonth)->where('studstatus','4')->where('gender','MALE')->count();
                $item->prevtransinfemale =   collect($students)->where('prevemonth', $prevmonth)->where('studstatus','4')->where('gender','FEMALE')->count();

                $item->prevtransoutmale =   collect($students)->where('curmonth', $prevmonth)->where('studstatus','5')->where('gender','MALE')->count();
                $item->prevtransoutfemale =   collect($students)->where('curmonth', $prevmonth)->where('studstatus','5')->where('gender','FEMALE')->count();

                $item->prevdropoutmale =   collect($students)->where('curmonth', $prevmonth)->where('studstatus','3')->where('gender','MALE')->count();
                $item->prevdropoutfemale =   collect($students)->where('curmonth', $prevmonth)->where('studstatus','3')->where('gender','FEMALE')->count();
               

                $item->transinmale =   collect($students)->where('curmonth',$month)->where('studstatus','4')->where('gender','MALE')->count();
                $item->transinfemale =   collect($students)->where('curmonth',$month)->where('studstatus','4')->where('gender','FEMALE')->count();

                $item->transoutmale =   collect($students)->where('curmonth',$month)->where('studstatus','5')->where('gender','MALE')->count();
                $item->transoutfemale =   collect($students)->where('curmonth',$month)->where('studstatus','5')->where('gender','FEMALE')->count();

                $item->dropoutmale =   collect($students)->where('curmonth',$month)->where('studstatus','3')->where('gender','MALE')->count();
                $item->dropoutfemale =   collect($students)->where('curmonth',$month)->where('studstatus','3')->where('gender','FEMALE')->count();
             
                $item->male = $male;
                $item->female = $female;
             
            }

        }

        $sections = collect($sections)->sortBy('levelname');

        $pdf = PDF::loadView('principalsportal.forms.showschoolform4',compact('sections','month','schoolinfo'))->setPaper('legal', 'landscape');
        
        $pdf->getDomPDF()->set_option("enable_php", true);

        return $pdf->stream();
    
    }

    public static function sf6pdf(){

      

        $acadid = collect(Session::get('principalInfo'))->map(function($value){
            return $value->acadid;
        });

    

        $data = array();

        foreach($acadid as $acaditem){

            $student = SPP_EnrolledStudent::getStudent(
                null,
                null,
                null,
                null,
                $acaditem,
                null,
                null,
                null,
                null,
                true,
                false,
                'sf6'
            );
            
            


            $gradelevel = SPP_Gradelevel::getGradeLevel(null,null,null,null,Crypt::encrypt($acaditem));

            foreach($gradelevel[0]->data as $levelitem){

              

                $devmale = 0;
                $begmale = 0;
                $approfmale = 0;
                $profmale = 0;
                $addmale = 0;

                $devfemale = 0;
                $begfemale = 0;
                $approffemale = 0;
                $proffemale = 0;
                $addfemale = 0;

            

                if(collect($student)->where('enlevelid',$levelitem->id)->count() != 0){

                    $studidmale = collect($student)->where('enlevelid',$levelitem->id)->where('gender','MALE')->map(function($value){
                        return $value->id;
                    });

                    $studidfemale = collect($student)->where('enlevelid',$levelitem->id)->where('gender','FEMALE')->map(function($value){
                        return $value->id;
                    });

                    $gradesmale = collect(DB::table('tempgradesum')
                            ->whereIn('studid',$studidmale)
                            ->join('sy',function($join){
                                $join->on('tempgradesum.syid','=','sy.id');
                                $join->where('sy.id',Session::get('schoolYear')->id);
                            })
                            ->select()
                            ->get())
                            ->groupBy('studid')
                            ->map(function($value){

                                $q1 = collect($value)->avg('q1');
                                $q2 = collect($value)->avg('q2');
                                $q3 = collect($value)->avg('q3');
                                $q4 = collect($value)->avg('q4');
                                return ['ave' => round( ( $q1 + $q2 + $q3 + $q4 ) / 4 ) ];
                            });


                    $gradesfemale = collect(DB::table('tempgradesum')
                        ->whereIn('studid',$studidfemale)
                        ->join('sy',function($join){
                            $join->on('tempgradesum.syid','=','sy.id');
                            $join->where('sy.id',Session::get('schoolYear')->id);
                        })
                        ->select()
                        ->get())
                        ->groupBy('studid')
                        ->map(function($value){

                            $q1 = collect($value)->avg('q1');
                            $q2 = collect($value)->avg('q2');
                            $q3 = collect($value)->avg('q3');
                            $q4 = collect($value)->avg('q4');
                            return ['ave' => round( ( $q1 + $q2 + $q3 + $q4 ) / 4 ) ];
                        });

                  
                    
                    $begmale = $gradesmale->where('ave','<=','74')->count();
                    $devmale = $gradesmale->where('ave','>=','75')->where('ave','<=','79')->count();
                    $approfmale = $gradesmale->where('ave','>=','80')->where('ave','<=','84')->count();
                    $profmale = $gradesmale->where('ave','>=','85')->where('ave','<=','89')->count();
                    $addmale = $gradesmale->where('ave','>=','90')->count();

                    $begfemale = $gradesfemale->where('ave','<=','74')->count();
                    $devfemale = $gradesfemale->where('ave','>=','75')->where('ave','<=','79')->count();
                    $approffemale = $gradesfemale->where('ave','>=','80')->where('ave','<=','84')->count();
                    $proffemale = $gradesfemale->where('ave','>=','85')->where('ave','<=','89')->count();
                    $addfemale = $gradesfemale->where('ave','>=','90')->count();

                 

                }

                $malepromtstud = $devmale + $approfmale + $profmale + $addmale;

                $fempromtstud = $devfemale + $approffemale + $proffemale + $addfemale;

                $maleretstud = collect($student)->where('enlevelid',$levelitem->id)->where('gender','MALE')->where('studstatus','1')->count() - $malepromtstud;

                $femretstud = collect($student)->where('enlevelid',$levelitem->id)->where('gender','FEMALE')->where('studstatus','1')->count() -  $fempromtstud;

                $maleconstud = collect($student)->where('enlevelid',$levelitem->id)->where('gender','MALE')->where('studstatus','2')->count();

                $femconstud = collect($student)->where('enlevelid',$levelitem->id)->where('gender','FEMALE')->where('studstatus','2')->count();


                array_push($data,(object)[

                    'sortid'=> $levelitem->sortid,

                    'malepromtstud'=>$malepromtstud,
                    'fempromtstud'=>$fempromtstud,

                    'maleretstud'=>$maleretstud,
                    'femretstud'=>$femretstud,

                    'maleconstud'=>$maleconstud,
                    'femconstud'=>$femconstud,

                    'begmale'=>$begmale,
                    'devmale'=>$devmale,
                    'approfmale'=>$approfmale,
                    'profmale'=>$profmale,
                    'addmale'=>$addmale,

                    'begfemale'=>$begfemale,
                    'devfemale'=>$devfemale,
                    'approffemale'=>$approffemale,
                    'proffemale'=>$proffemale,
                    'addfemale'=>$addfemale
                    

                ]);


            }

        }

        $allGradeLevel = SPP_Gradelevel::getGradeLevel();

        foreach($allGradeLevel[0]->data as $item){
            if(count(collect($data)->where('sortid',$item->sortid)) == 0 && $item->acadprogid != 2){
                array_push($data,(object)[
                    'sortid'=> $item->sortid,
                    'malepromtstud'=>0,
                    'fempromtstud'=>0,
                    'maleretstud'=>0,
                    'femretstud'=>0,
                    'maleconstud'=>0,
                    'femconstud'=>0,
                    'begmale'=>0,
                    'devmale'=>0,
                    'approfmale'=>0,
                    'profmale'=>0,
                    'addmale'=>0,
                    'begfemale'=>0,
                    'devfemale'=>0,
                    'approffemale'=>0,
                    'proffemale'=>0,
                    'addfemale'=>0
                ]);
            }
        }


        // return collect($data)->where('sortid','!=','1')->where('sortid','!=','2')->sum('addmale');

        $schoolinfo = DB::table('schoolinfo')
                        ->join('refregion','schoolinfo.region','=','refregion.regCode')
                        ->join('refcitymun','schoolinfo.division','=','refcitymun.citymunCode')
                        ->select('schoolinfo.*','refregion.regDesc','refcitymun.citymunDesc')
                        ->get();

        $pdf = PDF::loadView('principalsportal.forms.showschoolform6',compact('data','schoolinfo'))->setPaper('legal', 'landscape');
        $pdf->getDomPDF()->set_option("enable_php", true);

        return $pdf->stream();

    }

    public static function sf9pdf($id, Request $request){

        $syid = $request->get('syid');
        $strand = null;
        $studid = $id;

        $schoolyear = DB::table('sy')->where('id',$syid)->first();

        $student = DB::table('enrolledstud')
                        ->where('enrolledstud.studid',$studid)
                        ->where('enrolledstud.deleted',0)
                        ->where('enrolledstud.syid',$syid)
                        ->join('studinfo',function($join){
                            $join->on('studinfo.id','=','enrolledstud.studid');
                            $join->where('studinfo.deleted',0);
                        })
                        ->join('sections',function($join){
                            $join->on('enrolledstud.sectionid','=','sections.id');
                            $join->where('sections.deleted',0);
                        })
                        ->join('gradelevel',function($join){
                            $join->on('enrolledstud.levelid','=','gradelevel.id');
                            $join->where('gradelevel.deleted',0);
                        })
                        ->select(
                            'lastname',
                            'firstname',
                            'middlename',
                            'suffix',
                            'acadprogid',
                            'enrolledstud.levelid',
                            'enrolledstud.sectionid',
                            'dob',
                            'gender',
                            'levelname',
                            'sections.sectionname',
                            'lrn'
                        )
                        ->first();

        if(!isset($student->levelid)){

            $student = DB::table('sh_enrolledstud')
                        ->where('sh_enrolledstud.studid',$studid)
                        ->where('sh_enrolledstud.deleted',0)
                        ->where('sh_enrolledstud.syid',$syid)
                        ->join('studinfo',function($join){
                            $join->on('sh_enrolledstud.studid','=','studinfo.id');
                            $join->where('studinfo.deleted',0);
                        })
                        ->join('sections',function($join){
                            $join->on('sh_enrolledstud.sectionid','=','sections.id');
                            $join->where('sections.deleted',0);
                        })
                        ->join('gradelevel',function($join){
                            $join->on('sh_enrolledstud.levelid','=','gradelevel.id');
                            $join->where('gradelevel.deleted',0);
                        })
                        ->select(
                            'lastname',
                            'firstname',
                            'middlename',
                            'suffix',
                            'acadprogid',
                            'sh_enrolledstud.strandid',
                            'sh_enrolledstud.levelid',
                            'sh_enrolledstud.sectionid',
                            'dob',
                            'gender',
                            'levelname',
                            'sections.sectionname',
                            'lrn'
                        )
                        ->first();

            $strand = $student->strandid;

        }

       

        if(!isset($student->levelid)){
            return "Student not Found!";    
        }

        
        $acad = $student->acadprogid;
        $gradelevel = $student->levelid;
        $sectionid = $student->sectionid;


        $birthDate = $student->dob; // Your birthdate
        $currentYear = explode("-",$schoolyear->sydesc)[0]; // Current Year
        $birthYear = date('Y', strtotime($birthDate)); // Extracted Birth Year using strtotime and date() function
        $age = $currentYear - $birthYear; // Current year minus birthyear
        $student->age = $age;
        
        $middlename = explode(" ",$student->middlename);
        $temp_middle = '';
        if($middlename != null){
            foreach ($middlename as $middlename_item) {
                if(strlen($middlename_item) > 0){
                    $temp_middle .= $middlename_item[0].'.';
                } 
            }
        }

        $student->student = $student->lastname.', '.$student->firstname.' '.$student->suffix.' '.$temp_middle;

        $sectioninfo = DB::table('sectiondetail')
                            ->where('sectionid',$sectionid)
                            ->join('teacher',function($join){
                                $join->on('sectiondetail.teacherid','=','teacher.id');
                                $join->where('teacher.deleted',0);
                            })
                            ->select(
                                'lastname',
                                'firstname',
                                'middlename',
                                'suffix',
                                'teacherid',
                                'title'
                            )
                            ->get();

        $adviser = '';
		$teacherid = null;

		foreach($sectioninfo as $item){
            $temp_middle = '';
            $temp_suffix = '';
            $temp_title = '';
            if(isset($item->middlename)){
                $temp_middle = $item->middlename[0].'.';
            }
            if(isset($item->title)){
                $temp_title = $item->title.'. ';
            }
            if(isset($item->suffix)){
                $temp_suffix = ', '.$item->suffix;
            }
            $adviser = $temp_title.$item->firstname.' '.$temp_middle.' '.$item->lastname.$temp_suffix;
            $teacherid = $item->teacherid;
            $item->checked = 0;

        }

        //Attendance
        $attendance_setup = \App\Models\AttendanceSetup\AttendanceSetupData::attendance_setup_list($syid,$gradelevel);
		
	

        foreach( $attendance_setup as $item){

            $sf2_setup = DB::table('sf2_setup')
                            ->where('month',$item->month)
                            ->where('year',$item->year)
                            ->where('sectionid',$sectionid)
                            ->where('sf2_setup.deleted',0)
                            ->join('sf2_setupdates',function($join){
                                $join->on('sf2_setup.id','=','sf2_setupdates.setupid');
                                $join->where('sf2_setupdates.deleted',0);
                            })
                            ->select('dates')
                            ->get();

            if(count($sf2_setup) == 0){

                $sf2_setup = DB::table('sf2_setup')
                            ->where('month',$item->month)
                            ->where('year',$item->year)
                            ->where('sectionid',$sectionid)
                            ->where('sf2_setup.deleted',0)
                            ->join('sf2_setupdates',function($join){
                                $join->on('sf2_setup.id','=','sf2_setupdates.setupid');
                                $join->where('sf2_setupdates.deleted',0);
                            })
                            ->select('dates')
                            ->get();

            }

            $temp_days = array();

            foreach($sf2_setup as $sf2_setup_item){
                array_push($temp_days,$sf2_setup_item->dates);
            }

            $student_attendance = DB::table('studattendance')
                                    ->where('studid',$studid)
                                    ->where('deleted',0)
                                    ->whereIn('tdate',$temp_days)
                                    // ->where('syid',$syid)
                                    ->distinct('tdate')
                                    ->distinct()
                                    ->select([
                                        'present',
                                        'absent',
                                        'tardy',
                                        'cc',
                                        'tdate'
                                    ])
                                    ->get();

            $student_attendance = collect($student_attendance)->unique('tdate')->values();

            $item->present = collect($student_attendance)->where('present',1)->count() + collect($student_attendance)->where('tardy',1)->count() + collect($student_attendance)->where('cc',1)->count();
            $item->absent = collect($student_attendance)->where('absent',1)->count();
         
        }

        $schoolinfo = DB::table('schoolinfo')
                        ->leftJoin('refregion','schoolinfo.region','=','refregion.regCode')
                        ->leftJoin('refcitymun','schoolinfo.division','=','refcitymun.citymunCode')
                        ->select('schoolinfo.*','refregion.regDesc','refcitymun.citymunDesc')
                        ->get();

       
        //core value
        $checkGrades = [];
        $rv = [];
        
        $ob = \App\Http\Controllers\SuperAdminController\ObservedValuesController::observedvalues_list( null,null,null,$syid,$gradelevel);

        if(count($ob) > 0){

                $checkGrades = DB::table('grading_system_grades_cv')
                                ->leftJoin('grading_system_detail',function($join){
                                    $join->on('grading_system_grades_cv.gsdid','=','grading_system_detail.id');
                                    $join->where('grading_system_detail.deleted',0);
                                })
                                ->leftJoin('grading_system',function($join) use($ob){
                                    $join->on('grading_system_detail.headerid','=','grading_system.id');
                                    $join->where('grading_system.deleted',0);
                                    $join->where('grading_system.id',$ob[0]->headerid);
                                })
                                ->where('grading_system_grades_cv.deleted',0)
                                ->where('grading_system_grades_cv.studid',$studid)
                                ->where('grading_system_grades_cv.syid',$syid)
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
                if(count($checkGrades) == 0){
                    $checkGrades = DB::table('grading_system')
                                ->leftJoin('grading_system_detail',function($join){
                                    $join->on('grading_system.id','=','grading_system_detail.headerid');
                                    $join->where('grading_system_detail.deleted',0);
                                })
                                ->where('grading_system.deleted',0)
                                ->where('grading_system.id',$ob[0]->headerid)
                                ->select(
                                    'grading_system_detail.description',
                                    'value',
                                    'sort',
                                    'type',
                                    'group'
                                )
                                ->orderBy('sort')
                                ->get();

                    foreach($checkGrades as $item){
                        $item->q1eval = null;
                        $item->q2eval = null;
                        $item->q3eval = null;
                        $item->q4eval = null;
                    }
                }
                $rv = DB::table('grading_system_ratingvalue')
                                ->where('deleted',0)
                                ->where('gsid',$ob[0]->headerid)
                                ->orderBy('sort')
                                ->get();
        }
        //core value
        
        $grading_version = DB::table('zversion_control')->where('module',1)->where('isactive',1)->first();

		$subjects = [];
		
        if($student->levelid == 14 || $student->levelid == 15){
            if($grading_version->version == 'v2'){
                $studgrades = \App\Http\Controllers\SuperAdminController\StudentGradeEvaluation::sf9_grades_gv2( $gradelevel,$studid,$syid,$strand,null,$sectionid);
            }else{
                $studgrades = \App\Http\Controllers\SuperAdminController\StudentGradeEvaluation::sf9_grades( $gradelevel,$studid,$syid,$strand,null,$sectionid,true);
            }
			
			
			//return $studgrades;
			
            $temp_grades = array();
            $finalgrade = array();
            foreach($studgrades as $item){
                if($item->id == 'G1'){
                    array_push($finalgrade,$item);
                }else{
                    if($item->strandid == $strand){
                        array_push($temp_grades,$item);
                    }
                    if($item->strandid == null){
                        array_push($temp_grades,$item);
                    }
                }
            }
           
            $studgrades = $temp_grades;
            $studgrades = collect($studgrades)->sortBy('sortid')->values();
			
        }else{
            if($grading_version->version == 'v2'){
                $studgrades = \App\Http\Controllers\SuperAdminController\StudentGradeEvaluation::sf9_grades_gv2( $gradelevel,studid,$syid,null,null,$sectionid);
            }else{
                $studgrades = \App\Http\Controllers\SuperAdminController\StudentGradeEvaluation::sf9_grades( $gradelevel,$studid,$syid,null,null,$sectionid,true);
            }

            $subjects = \App\Http\Controllers\SuperAdminController\StudentGradeEvaluation::sf9_subjects($gradelevel);
            $grades = $studgrades;
            $grades = collect($grades)->sortBy('sortid')->values();
            $finalgrade = collect($grades)->where('id','G1')->values();
            unset($grades[count($grades)-1]);
            $studgrades = collect($grades)->where('isVisible','1')->values();
        }   

		$principal_info = array((object)[
            'name'=>null,
            'title'=>null
        ]);

		
        $signatory = DB::table('signatory')
                        ->where('form','report_card')
                        ->where('syid',$syid)
                        ->where('acadprogid',$acad)
                        ->where('deleted',0)
                        ->select(
                            'name',
                            'title'
                        )
                        ->first();

        if(isset($signatory->name)){
            $principal_info[0]->name = $signatory->name;
            $principal_info[0]->title = $signatory->title;
        }

		if($request->get('type') == 'excel'){
			$templateid = 9;
			
			$templateid = DB::table('sf9template')
							->join('sf9template_gradelvl',function($join) use($student){
								$join->on('sf9template.id','=','sf9template_gradelvl.headerid');
								$join->where('levelid',$student->levelid);
							})
							->where('sf9template.syid',$syid)
							->select('sf9template.id')
							->first();
							
			if(isset($templateid)){
				$templateid = $templateid->id;
			}else{
				return "No tempalte available";
			}
			
			
			$firstname = $student->firstname;
			$lastname = $student->lastname;
			$middlename = $student->middlename;
			$sy = $schoolyear->sydesc;
			$address  = $address;
			$adviser = $adviser;
			$age = $age;
			$gender = $student->gender;
			$lrn = $student->lrn;
			$section = $student->sectionname;
			$gradelevel = $student->levelname;
			$levelid = $student->levelid;
			$dob = $student->dob;
			$address = $address;
		 
		   
			$sf9template = DB::table('sf9template')
							->where('id',$templateid)
							->first();

			$sf9templatedetails = DB::table('sf9templatedetail')
							->where('headerid',$templateid)
							->get();

			$sf9templatestudinfo = DB::table('sf9templateinfo')
							->get();

			$studentinformation = collect($sf9templatedetails)->where('type','information')->values();
			$gradescellvalue = collect($sf9templatedetails)->where('type','grades')->values();

			$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
			$spreadsheet = $reader->load($sf9template->filelocation);


			$sheet = $spreadsheet->setActiveSheetIndex(0);

			foreach($studentinformation as $item){
				$tempvariable = collect($sf9templatestudinfo)
									->where('id',$item->dataid)
									->first();
				try{
					$cells = explode(',',$item->cellvalue);
					foreach($cells as $cellitem){
						$sheet->setCellValue($cellitem,eval('return '.$tempvariable->formula.';'));
					}
				}catch(\Exception $e){
					if($item->dataid == 3){
						return $e;
					}
				}
			}

			
			//insert grades to excel  
			foreach($studgrades as $item){
				$cellvalue = collect($gradescellvalue)
								->where('dataid',$item->subjid)
								->where('cellvalue','!=',null)
								->values();
				
					foreach($cellvalue as $cellitem){
						
						if($cellitem->quarter == "quarter1"){
							$sheet->setCellValue($cellitem->cellvalue,$item->quarter1);
						}else if($cellitem->quarter == "quarter2"){
							$sheet->setCellValue($cellitem->cellvalue,$item->quarter2);
						}else if($cellitem->quarter == "quarter3"){
							$sheet->setCellValue($cellitem->cellvalue,$item->quarter3);
						}else if($cellitem->quarter == "quarter4"){
							$sheet->setCellValue($cellitem->cellvalue,$item->quarter4);
						} else if($cellitem->quarter == "finalgrade"){
							$sheet->setCellValue($cellitem->cellvalue,$item->finalrating);
						}else if($cellitem->quarter == "remarks"){
							$sheet->setCellValue($cellitem->cellvalue,$item->actiontaken);
						}
					}
				
			}
			
			foreach($finalgrade as $item){
				$cellvalue = collect($sf9templatedetails)
								->where('type','genave')
								->values();
							   
				foreach($cellvalue as $cellitem){

					$cells = explode(',',$cellitem->cellvalue);

					foreach($cells as $cell){
						if($cellitem->quarter == "quarter1"){
							$sheet->setCellValue($cell,$item->quarter1);
						}else if($cellitem->quarter == "quarter2"){
							$sheet->setCellValue($cell,$item->quarter2);
						}else if($cellitem->quarter == "quarter3"){
							$sheet->setCellValue($cell,$item->quarter3);
						}else if($cellitem->quarter == "quarter4"){
							$sheet->setCellValue($cell,$item->quarter4);
						} else if($cellitem->quarter == "finalgrade"){
							$sheet->setCellValue($cell,$item->finalrating);
						}else if($cellitem->quarter == "remarks"){
							$sheet->setCellValue($cell,$item->actiontaken);
						}
					}
				}
			}
			
			$cellvalue = collect($sf9templatedetails)
                        ->whereIn('type',['schooldays','daysabsent','dayspresent'])
                        ->values();

			$attendancecell = DB::table('sf9templatedetail')
							->whereIn('sf9templatedetail.id',collect($cellvalue)->pluck('id'))
							->join('studattendance_setup',function($join){
								$join->on('sf9templatedetail.dataid','=','studattendance_setup.id');
								$join->where('studattendance_setup.deleted',0);
							})
							->select(
								'sf9templatedetail.*',
								'month'
							)
							->get();

			foreach($attendance_setup as $item){

				$cellvalue = collect($attendancecell)
								->where('month',$item->month)
								->where('type','schooldays')
								->first();

				if(isset($cellvalue->cellvalue)){
					$sheet->setCellValue($cellvalue->cellvalue,$item->days);
				}

				

				$cellvalue = collect($attendancecell)
								->where('month',$item->month)
								->where('type','dayspresent')
								->first();

				if(isset($cellvalue->cellvalue)){
					$sheet->setCellValue($cellvalue->cellvalue,$item->present);
				}

				$cellvalue = collect($attendancecell)
								->where('month',$item->month)
								->where('type','daysabsent')
								->first();

				if(isset($cellvalue->cellvalue)){
					$sheet->setCellValue($cellvalue->cellvalue,$item->absent);
				}
				
			}
			

			$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
			
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment; filename="SF9template.xlsx"');
			$writer->save("php://output");
			exit();
		}
		
		
            if($acad == 5){
				
				$strandInfo = DB::table('sh_strand')
                                    ->join('sh_track',function($join){
                                        $join->on('sh_strand.trackid','=','sh_track.id');
                                        $join->where('sh_strand.deleted',0);
                                    })
                                    ->where('sh_strand.id',$strand)
                                    ->select('trackname','strandcode','strandname')
                                    ->first();
									
				$semid = $request->get('semid');
				if($request->get('semid') == ""){
					$semid = DB::table('semester')
								->where('isactive',1)
								->first()
								->id;
				}
									
                $pdf = PDF::loadView('principalsportal.forms.sf9layout.ica.shs',compact('semid','strandInfo','principal_info','student','subjects','studgrades','finalgrade','attendance_setup','schoolyear','schoolinfo','checkGrades','rv','adviser'))->setPaper('legal');
                $pdf->getDomPDF()->set_option("enable_php", true)->set_option("DOMPDF_ENABLE_CSS_FLOAT", true);
                return $pdf->stream();
            }else{
			
                $pdf = PDF::loadView('principalsportal.forms.sf9layout.ica.jhs',compact('principal_info','student','subjects','studgrades','finalgrade','attendance_setup','schoolyear','schoolinfo','checkGrades','rv','adviser'))->setPaper('legal');
                $pdf->getDomPDF()->set_option("enable_php", true)->set_option("DOMPDF_ENABLE_CSS_FLOAT", true);
                return $pdf->stream();
            }
        
              


    }

	 public static function section_reportcard(Request $request){

        $levelid = $request->get('levelid');
        $syid = $request->get('syid');
        $sectionid = $request->get('sectionid');

        $section_info = DB::table('sections')
                            ->where('id',$sectionid)
                            ->select('sectionname')
                            ->first();

        $gradelevel = DB::table('gradelevel') 
                    ->where('id',$levelid)
                    ->select(
                        'levelname',
                        'acadprogid'
                    )
                    ->first();

        $schoolinfo = DB::table('schoolinfo')->first();

        $schoolyear = DB::table('sy')
                        ->where('id',$syid)
                        ->first();

        $sectioninfo = DB::table('sectiondetail')
                        ->where('sectionid',$sectionid)
                        ->where('sectiondetail.deleted',0)
                        ->where('syid',$syid)
                        ->whereNotNull('teacherid')
                        ->join('teacher',function($join){
                            $join->on('sectiondetail.teacherid','=','teacher.id');
                            $join->where('teacher.deleted',0);
                        })
                        ->select(
                            'lastname',
                            'firstname',
                            'middlename',
                            'suffix',
                            'teacherid',
                            'title'
                        )
                        ->first();

        $adviser = '';
        $teacherid = null;

        if(isset($sectioninfo->lastname)){
            $temp_middle = '';
            $temp_suffix = '';
            $temp_title = '';
            if(isset($sectioninfo->middlename)){
                $temp_middle = $sectioninfo->middlename[0].'.';
            }
            if(isset($sectioninfo->title)){
                $temp_title = $sectioninfo->title.'. ';
            }
            if(isset($sectioninfo->suffix)){
                $temp_suffix = ', '.$sectioninfo->suffix;
            }
            $adviser = $temp_title.$sectioninfo->firstname.' '.$temp_middle.' '.$sectioninfo->lastname.$temp_suffix;
            $teacherid = $sectioninfo->teacherid;
        }

        $principal_info = array((object)[
            'name'=>null,
            'title'=>null
        ]);

        $signatory = DB::table('signatory')
                        ->where('form','report_card')
                        ->where('syid',$syid)
                        ->where('acadprogid',$gradelevel->acadprogid)
                        ->where('deleted',0)
                        ->select(
                            'name',
                            'title'
                        )
                        ->first();

        if(isset($signatory->name)){
            $principal_info[0]->name = $signatory->name;
            $principal_info[0]->title = $signatory->title;
        }

        $setup = $request->get('setup');
        $type = $request->get('type');
        $principal = '';

        if( $setup == 1){
            if($syid == 3){
                if($gradelevel->acadprogid == 5 || $gradelevel->acadprogid == 4){
                    $principal = 'MR. RUFINO T. EFONDO, JR., LPT, MA ';
                }else{
                    $principal = 'MR. ALFIE P. BILLION';
                }
            }
            if($gradelevel->acadprogid == 5){
                $enrolledstud = self::shs_reportcard_info($request, $schoolinfo->abbreviation, $teacherid);
                $pdf = PDF::loadView('principalsportal.forms.sf9layout.section.spct.shs',compact('enrolledstud','section_info','schoolinfo','gradelevel','schoolyear','adviser','principal_info','principal'))->setPaper('legal');
                $pdf->getDomPDF()->set_option("enable_php", true)->set_option("DOMPDF_ENABLE_CSS_FLOAT", true);
                return $pdf->stream($gradelevel->levelname.' - '.$section_info->sectionname.' '.$schoolyear->sydesc);
            }
            else if($gradelevel->acadprogid == 4){
                $enrolledstud = self::gshs_reportcard_info($request, $schoolinfo->abbreviation, $teacherid, $section_info);
                if($type == 'excel'){
                    return self::plot_excel_sait($enrolledstud);
                }else{
                    $pdf = PDF::loadView('principalsportal.forms.sf9layout.section.spct.jhs',compact('enrolledstud','section_info','schoolinfo','gradelevel','schoolyear','adviser','principal_info','principal'))->setPaper('legal');
                    $pdf->getDomPDF()->set_option("enable_php", true)->set_option("DOMPDF_ENABLE_CSS_FLOAT", true);
                    return $pdf->stream($gradelevel->levelname.' - '.$section_info->sectionname.' '.$schoolyear->sydesc);
                }
            }
            else{
                $enrolledstud = self::gshs_reportcard_info($request, $schoolinfo->abbreviation, $teacherid, $section_info);
                if($type == 'excel'){
                    return self::plot_excel_sait($enrolledstud);
                }else{
                    $pdf = PDF::loadView('principalsportal.forms.sf9layout.section.spct.gs',compact('enrolledstud','section_info','schoolinfo','gradelevel','schoolyear','adviser','principal_info','principal'))->setPaper('legal');
                    $pdf->getDomPDF()->set_option("enable_php", true)->set_option("DOMPDF_ENABLE_CSS_FLOAT", true);
                    return $pdf->stream($gradelevel->levelname.' - '.$section_info->sectionname.' '.$schoolyear->sydesc);
                }
            }
        }else if( $setup == 2){
            if($gradelevel->acadprogid == 5){
                $enrolledstud = self::shs_reportcard_info($request, $schoolinfo->abbreviation, $teacherid);
                $pdf = PDF::loadView('principalsportal.forms.sf9layout.section.spct.shs',compact('enrolledstud','section_info','schoolinfo','gradelevel','schoolyear','adviser','principal_info','principal'))->setPaper('legal');
                $pdf->getDomPDF()->set_option("enable_php", true)->set_option("DOMPDF_ENABLE_CSS_FLOAT", true);
                return $pdf->stream($gradelevel->levelname.' - '.$section_info->sectionname.' '.$schoolyear->sydesc);
                // $pdf = PDF::loadView('principalsportal.forms.sf9layout.section.hccsi.hccsi_shs',compact('enrolledstud','section_info','schoolinfo','gradelevel','schoolyear','adviser','principal_info','principal'))->setPaper('legal');
                // $pdf->getDomPDF()->set_option("enable_php", true)->set_option("DOMPDF_ENABLE_CSS_FLOAT", true);
                // return $pdf->stream($gradelevel->levelname.' - '.$section_info->sectionname.' '.$schoolyear->sydesc);
            }
            else if($gradelevel->acadprogid == 4){
                $enrolledstud = self::gshs_reportcard_info($request, $schoolinfo->abbreviation, $teacherid);
                if($type == 'excel'){
                    return self::plot_excel_setup_2_gshs($enrolledstud, $schoolyear, $gradelevel, $adviser, $principal, $section_info);
                }else{
                    $pdf = PDF::loadView('principalsportal.forms.sf9layout.section.spct.jhs',compact('enrolledstud','section_info','schoolinfo','gradelevel','schoolyear','adviser','principal_info','principal'))->setPaper('legal');
                    $pdf->getDomPDF()->set_option("enable_php", true)->set_option("DOMPDF_ENABLE_CSS_FLOAT", true);
                    return $pdf->stream($gradelevel->levelname.' - '.$section_info->sectionname.' '.$schoolyear->sydesc);

                    // $pdf = PDF::loadView('principalsportal.forms.sf9layout.section.hccsi.hccsi_jhs',compact('enrolledstud','section_info','schoolinfo','gradelevel','schoolyear','adviser','principal_info','principal'))->setPaper('legal');
                    // $pdf->getDomPDF()->set_option("enable_php", true)->set_option("DOMPDF_ENABLE_CSS_FLOAT", true);
                    // return $pdf->stream($gradelevel->levelname.' - '.$section_info->sectionname.' '.$schoolyear->sydesc);
                }
            }
            else{
                $enrolledstud = self::gshs_reportcard_info($request, $schoolinfo->abbreviation, $teacherid);
                if($type == 'excel'){
                    return self::plot_excel_setup_2_gshs($enrolledstud, $schoolyear, $gradelevel, $adviser, $principal, $section_info);
                }else{

                    $pdf = PDF::loadView('principalsportal.forms.sf9layout.section.spct.gs',compact('enrolledstud','section_info','schoolinfo','gradelevel','schoolyear','adviser','principal_info','principal'))->setPaper('legal');
                    $pdf->getDomPDF()->set_option("enable_php", true)->set_option("DOMPDF_ENABLE_CSS_FLOAT", true);
                    return $pdf->stream($gradelevel->levelname.' - '.$section_info->sectionname.' '.$schoolyear->sydesc);

                    // $pdf = PDF::loadView('principalsportal.forms.sf9layout.section.hccsi.hccsi_jhs',compact('enrolledstud','section_info','schoolinfo','gradelevel','schoolyear','adviser','principal_info','principal'))->setPaper('legal');
                    // $pdf->getDomPDF()->set_option("enable_php", true)->set_option("DOMPDF_ENABLE_CSS_FLOAT", true);
                    // return $pdf->stream($gradelevel->levelname.' - '.$section_info->sectionname.' '.$schoolyear->sydesc);
                }
            }
        }else{
            if($gradelevel->acadprogid == 5){

            }else{
                $enrolledstud = self::gshs_reportcard_info($request);
                $pdf = PDF::loadView('principalsportal.forms.sf9layout.section.sf9_gshs',compact('enrolledstud','section_info','schoolinfo','gradelevel','schoolyear','adviser','principal_info'))->setPaper('legal');
                $pdf->getDomPDF()->set_option("enable_php", true)->set_option("DOMPDF_ENABLE_CSS_FLOAT", true);
                return $pdf->stream();
            }
        }
       
        return $enrolledstud;

    }
	
	public static function plot_excel_setup_2_gshs($enrolledstud, $schoolyear, $gradelevel, $adviser, $principal, $section_info){

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $spreadsheet = $reader->load("ReportCard/JHS-Report-Card.xlsx");

        foreach($enrolledstud as $item){

            $birthDate = $item->dob; // Your birthdate
            $currentYear = explode("-",$schoolyear->sydesc)[0]; // Current Year
            $birthYear = date('Y', strtotime($birthDate)); // Extracted Birth Year using strtotime and date() function
            $age = $currentYear - $birthYear; // Current year minus birthyear

            $clonedWorksheet = clone $spreadsheet->getSheetByName('Template');
            $clonedWorksheet->setTitle($item->lastname.' '.$item->firstname[0].'.');
            $spreadsheet->addSheet($clonedWorksheet);

            $spreadsheet->setActiveSheetIndexByName($item->lastname.' '.$item->firstname[0].'.');
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('S12',$item->fullname);
            $sheet->setCellValue('S13',$age);
            $sheet->setCellValue('Y13',$item->gender);
            $sheet->setCellValue('S14',$gradelevel->levelname);
            $sheet->setCellValue('Y14',$section_info->sectionname);
            $sheet->setCellValue('Y15',$item->lrn);
            $sheet->setCellValue('T15',$schoolyear->sydesc);
            $sheet->setCellValue('R23',$adviser);
            $sheet->setCellValue('Y23',$principal);
            $grades = $item->grades;
            $finalgrade = $item->finalgrade;
            $row = 29;
            foreach($grades as $item){
                $sheet->setCellValue('T'.$row,$item->quarter1);
                $sheet->setCellValue('V'.$row,$item->quarter2);
                $sheet->setCellValue('X'.$row,$item->quarter3);
                $sheet->setCellValue('Z'.$row,$item->quarter4);
                if(isset($item->finalrating)){
                    $sheet->setCellValue('AB'.$row,$item->finalrating);
                    $sheet->setCellValue('AD'.$row,$item->actiontaken);
                }
                $row += 1;
            }

            if(isset($finalgrade->finalrating)){
                $sheet->setCellValue('AB'.$row,$finalgrade->finalrating);
                $sheet->setCellValue('AD'.$row,$finalgrade->actiontaken);
            }

            $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $sheet->getPageSetup()->setPrintArea('A1:AE50,A60:AE104');
            $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A3);

            $sheet->getPageMargins()->setTop(0);
            $sheet->getPageMargins()->setRight(.25);
            $sheet->getPageMargins()->setLeft();
            $sheet->getPageMargins()->setBottom(0);

            $spreadsheet->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
            $spreadsheet->getActiveSheet()->getPageSetup()->setVerticalCentered(false);
            // $spreadsheet->getActiveSheet()->getPageSetup()->setFitToPage(true);
            
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToHeight(0);
            $spreadsheet->getActiveSheet()->getSheetView()->setZoomScale(85);

        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="Report Card.xlsx"');
        $writer->save("php://output");
        exit ;

    }
	
	 public static function shs_reportcard_info(Request $request, $school, $teacherid){

        $syid = $request->get('syid');
        $semid = $request->get('semid');
        $sectionid = $request->get('sectionid');
        $levelid = $request->get('levelid');

        $enrolledstud = DB::table('sh_enrolledstud')
                            ->join('studinfo',function($join){
                                $join->on('sh_enrolledstud.studid','=','studinfo.id');
                                $join->where('studinfo.deleted',0);
                            })
                            ->where('sh_enrolledstud.sectionid',$sectionid)
                            ->where('sh_enrolledstud.levelid',$levelid)
                            ->where('sh_enrolledstud.syid',$syid)
                            ->where('sh_enrolledstud.deleted',0)
                            ->whereIn('sh_enrolledstud.studstatus',[1,2,4])
                            ->orderBy('gender','desc')
                            ->orderBy('lastname')
                            ->select(
                                'dob',
                                'sh_enrolledstud.strandid',
                                'lrn',
                                'studid',
                                'firstname',
                                'lastname',
                                'middlename',
                                'suffix',
                                'gender',
                                DB::raw("CONCAT(studinfo.lastname,' ',studinfo.firstname) as studentname")
                            )
                            ->orderBy('gender','desc')
                            ->orderBy('studentname','asc')
                            ->get();

        $attendance_setup = \App\Models\AttendanceSetup\AttendanceSetupData::attendance_setup_list($syid);

        foreach($enrolledstud as $item){

            $studid = $item->studid;
            $strand = $item->strandid;

            $studgrades = \App\Http\Controllers\SuperAdminController\StudentGradeEvaluation::sf9_grades( $levelid,$studid,$syid,$strand,null,$sectionid,true);
            $temp_grades = array();
            $finalgrade = array();
            foreach($studgrades as $grade_item){
                if($grade_item->id == 'G1'){
                    array_push($finalgrade,$grade_item);
                }else{
                    if($item->strandid == $strand){
                        array_push($temp_grades,$grade_item);
                    }
                    if($item->strandid == null){
                        array_push($temp_grades,$grade_item);
                    }
                }
            }
           
            $studgrades = $temp_grades;
            $studgrades = collect($studgrades)->where('isVisible','1')->values();
            $studgrades = collect($studgrades)->sortBy('sortid')->values();

            $item->grades = $studgrades;
            $item->finalgrade = $finalgrade;

            $middlename = '';
            $suffix = '';

            if($school == 'SPCT'){
                if(isset($item->middlename)){
                    $middlename =  $item->middlename;
                }
            }else{
                if(isset($item->middlename)){
                    $middlename =  ', '.$item->middlename[0];
                }
            }
           
            if(isset($item->suffix)){
                $suffix =  ' '.$item->suffix;
            }

            $item->fullname = $item->lastname.', '.$item->firstname.$middlename.$suffix;
            
            $student_attendance_setup = $attendance_setup;
            $studid = $item->studid;
            $student_attendance_setup = self::student_attendance($student_attendance_setup, $studid, $syid, $sectionid);
            
            $strandInfo = DB::table('sh_strand')
                                    ->join('sh_track',function($join){
                                        $join->on('sh_strand.trackid','=','sh_track.id');
                                        $join->where('sh_strand.deleted',0);
                                    })
                                    ->where('sh_strand.id',$strand)
                                    ->select('trackname','strandcode')
                                    ->first();

            $item->trackname = $strandInfo->trackname;
            $item->strandcode = $strandInfo->strandcode;

            $item->attendance = $student_attendance_setup;
            $item->observedvalues = self::student_observedvalues($studid, $syid, $levelid);
        }

        return $enrolledstud;

    }
   
    public static function gshs_reportcard_info(Request $request, $school, $teacherid){

        $syid = $request->get('syid');
        $semid = $request->get('semid');
        $sectionid = $request->get('sectionid');
        $levelid = $request->get('levelid');

        $enrolledstud = DB::table('enrolledstud')
                            ->join('studinfo',function($join){
                                $join->on('enrolledstud.studid','=','studinfo.id');
                                $join->where('studinfo.deleted',0);
                            })
                            ->where('enrolledstud.sectionid',$sectionid)
                            ->where('enrolledstud.levelid',$levelid)
                            ->where('enrolledstud.syid',$syid)
                            ->where('enrolledstud.deleted',0)
                            ->whereIn('enrolledstud.studstatus',[1,2,4])
                            ->orderBy('gender','desc')
                            ->orderBy('lastname')
                            ->select(
                                'dob',
                                'lrn',
                                'studid',
                                'firstname',
                                'lastname',
                                'middlename',
                                'suffix',
                                'gender',
                                DB::raw("CONCAT(studinfo.lastname,' ',studinfo.firstname) as studentname")
                            )
                            ->orderBy('gender','desc')
                            ->orderBy('studentname','asc')
                            ->get();

        $attendance_setup = \App\Models\AttendanceSetup\AttendanceSetupData::attendance_setup_list($syid);

        foreach($enrolledstud as $item){

            $studgrades = \App\Http\Controllers\SuperAdminController\StudentGradeEvaluation::sf9_grades( $levelid,$item->studid,$syid,null,null,$sectionid,true);
            $grades = $studgrades;
            $grades = collect($grades)->sortBy('sortid')->values();
            $finalgrade = collect($grades)->where('id','G1')->values();
            unset($grades[count($grades)-1]);
            $studgrades = collect($grades)->where('isVisible','1')->values();
            $item->grades = $studgrades;
            $item->finalgrade = $finalgrade;

            $middlename = '';
            $suffix = '';

            if($school == 'SPCT'){
                if(isset($item->middlename)){
                    $middlename =  $item->middlename;
                }
            }else{
                if(isset($item->middlename)){
                    $middlename =  ', '.$item->middlename[0];
                }
            }
           
            if(isset($item->suffix)){
                $suffix =  ' '.$item->suffix;
            }

            $item->fullname = $item->lastname.', '.$item->firstname.$middlename.$suffix;
            
            $student_attendance_setup = $attendance_setup;
            $studid = $item->studid;
            $student_attendance_setup = self::student_attendance($student_attendance_setup, $studid, $syid, $sectionid);
            

            $item->attendance = $student_attendance_setup;
            $item->observedvalues = self::student_observedvalues($studid, $syid, $levelid);


        }

        return $enrolledstud;
            
    }
	
	    public static function student_observedvalues($studid = null, $syid = null, $gradelevel = null){

        $checkGrades = [];
        $rv = [];
        
        $ob = \App\Http\Controllers\SuperAdminController\ObservedValuesController::observedvalues_list( null,null,null,$syid,$gradelevel);

        if(count($ob) > 0){

                $checkGrades = DB::table('grading_system_grades_cv')
                                ->leftJoin('grading_system_detail',function($join){
                                    $join->on('grading_system_grades_cv.gsdid','=','grading_system_detail.id');
                                    $join->where('grading_system_detail.deleted',0);
                                })
                                ->leftJoin('grading_system',function($join) use($ob){
                                    $join->on('grading_system_detail.headerid','=','grading_system.id');
                                    $join->where('grading_system.deleted',0);
                                    $join->where('grading_system.id',$ob[0]->headerid);
                                })
                                ->where('grading_system_grades_cv.deleted',0)
                                ->where('grading_system_grades_cv.studid',$studid)
                                ->where('grading_system_grades_cv.syid',$syid)
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
                if(count($checkGrades) == 0){
                    $checkGrades = DB::table('grading_system')
                                ->leftJoin('grading_system_detail',function($join){
                                    $join->on('grading_system.id','=','grading_system_detail.headerid');
                                    $join->where('grading_system_detail.deleted',0);
                                })
                                ->where('grading_system.deleted',0)
                                ->where('grading_system.id',$ob[0]->headerid)
                                ->select(
                                    'grading_system_detail.description',
                                    'value',
                                    'sort',
                                    'type',
                                    'group'
                                )
                                ->orderBy('sort')
                                ->get();

                    foreach($checkGrades as $item){
                        $item->q1eval = null;
                        $item->q2eval = null;
                        $item->q3eval = null;
                        $item->q4eval = null;
                    }
                }
                $rv = DB::table('grading_system_ratingvalue')
                                ->where('deleted',0)
                                ->where('gsid',$ob[0]->headerid)
                                ->orderBy('sort')
                                ->get();
        }

        return array((object)[
            'checkGrades'=>$checkGrades,
            'rv'=>$rv
        ]);

    }
    
	public static function student_attendance($student_attendance_setup, $studid = null, $syid = null, $sectionid = null){

        foreach( $student_attendance_setup as $att_item){

            $sf2_setup = DB::table('sf2_lact')
                        ->where('month',$att_item->month)
                        ->where('year',$att_item->year)
                        ->where('lact',3)
                        ->where('sectionid',$sectionid)
                        ->where('sf2_lact.deleted',0)
                        ->join('sf2_lact3detail',function($join) use($studid){
                            $join->on('sf2_lact.id','=','sf2_lact3detail.headerid');
                            $join->where('sf2_lact3detail.deleted',0);
                            $join->where('sf2_lact3detail.studid',$studid);
                        })
                        ->select(
                            'dayspresent',
                            'daysabsent'
                        )
                        ->get();
                        
                        
            if(count($sf2_setup) > 0){
                $att_item->present = $sf2_setup[0]->dayspresent >= $att_item->days ? $att_item->days :  $sf2_setup[0]->dayspresent;
                $att_item->absent = $sf2_setup[0]->daysabsent ;
            }else{
                
                $sf2_setup = DB::table('sf2_setup')
                        ->where('month',$att_item->month)
                        ->where('year',$att_item->year)
                        ->where('sectionid',$sectionid)
                        ->where('sf2_setup.deleted',0)
                        ->join('sf2_setupdates',function($join){
                            $join->on('sf2_setup.id','=','sf2_setupdates.setupid');
                            $join->where('sf2_setupdates.deleted',0);
                        })
                        ->groupBy('dates')
                        ->select('dates')
                        ->get();
    
                $temp_days = array();
    
                foreach($sf2_setup as $sf2_setup_item){
                    array_push($temp_days,$sf2_setup_item->dates);
                }
                
                $student_attendance = DB::table('studattendance')
                                     ->where('studid',$studid)
                                    ->where('deleted',0)
                                    ->whereIn('tdate',$temp_days)
                                    // ->where('syid',$syid)
                                    ->distinct()
                                    ->select([
                                        'present',
                                        'absent',
                                        'tardy',
                                        'cc',
                                        'tdate',
                                        'syid'
                                    ])
                                    ->get();
    
                $att_item->present = collect($student_attendance)->where('present',1)->count() + collect($student_attendance)->where('tardy',1)->count() + collect($student_attendance)->where('cc',1)->count();
                $att_item->absent = collect($student_attendance)->where('absent',1)->count();
                
            }
         
        }

        return $student_attendance_setup;

    }



}
