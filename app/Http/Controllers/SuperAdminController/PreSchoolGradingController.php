<?php

namespace App\Http\Controllers\SuperAdminController;

use Illuminate\Http\Request;
use DB;
use PDF;

class PreSchoolGradingController extends \App\Http\Controllers\Controller
{

      public static function pdf_format(Request $request){

            $studid = $request->get('studid');
            $syid = $request->get('syid');

            if($studid == null){
                  return "No student selected.";
            }

            $student = DB::table('studinfo')
                           ->where('id',$studid)
                           ->select(
                                 'id',
                                 'gender',
                                 'firstname',
                                 'lastname',
                                 'suffix',
                                 'middlename',
                                 'dob'
                           )
                           ->first();   

            $grades = self::get_student_grades_data($studid,$syid);
            $setup = self::get_preschool_setup($syid);
            $age_setup = self::get_preschool_setup_age_ajax($syid,$request);
            $remarks_setup = self::get_preschool_setup_remarks_ajax($syid,$request);
            
            $section = Db::table('enrolledstud')
                              ->where('studid',$studid)
                              ->where('syid',$syid)
                              ->join('sections',function($join){
                                    $join->on('enrolledstud.sectionid','=','sections.id');
                                    $join->where('sections.deleted',0);
                              })
                              ->where('enrolledstud.deleted',0)
                              ->select(
                                    'sectionname',
                                    'sections.id'
                                    )
                              ->first();

            foreach($setup as $item){
                  $grade = array((object)[
                        'q1grade'=>null,
                        'q2grade'=>null,
                        'q3grade'=>null,
                        'q4grade'=>null,
                  ]);
                  $row_grade = collect($grades)->where('gsdid',$item->id)->values();
                  if(count($row_grade) > 0){
                        $grade = array((object)[
                              'q1grade'=>$row_grade[0]->q1evaltext,
                              'q2grade'=>$row_grade[0]->q2evaltext,
                              'q3grade'=>$row_grade[0]->q3evaltext,
                              'q4grade'=>$row_grade[0]->q4evaltext,
                        ]);
                  }
                  $item->grade = $grade;
            }

            foreach($age_setup as $item){
                  $item->q1grade=null;
                  $item->q2grade=null;
                  $item->q3grade=null;
                  $item->q4grade=null;
                  $row_grade = collect($grades)->where('gsdid',$item->id)->values();
                  if(count($row_grade) > 0){
                        $item->q1grade= $row_grade[0]->q1evaltext == null ? null : $row_grade[0]->q1evaltext;
                        $item->q2grade= $row_grade[0]->q2evaltext == null ? null : $row_grade[0]->q2evaltext;
                        $item->q3grade= $row_grade[0]->q3evaltext == null ? null : $row_grade[0]->q3evaltext;
                        $item->q4grade= $row_grade[0]->q4evaltext == null ? null : $row_grade[0]->q4evaltext;
                  }
            }

            foreach($remarks_setup as $item){
                  $item->q1grade=null;
                  $item->q2grade=null;
                  $item->q3grade=null;
                  $item->q4grade=null;
                  $row_grade = collect($grades)->where('gsdid',$item->id)->values();
                  if(count($row_grade) > 0){
                        $item->q1grade= $row_grade[0]->q1evaltext == null ? null : $row_grade[0]->q1evaltext;
                        $item->q2grade= $row_grade[0]->q2evaltext == null ? null : $row_grade[0]->q2evaltext;
                        $item->q3grade= $row_grade[0]->q3evaltext == null ? null : $row_grade[0]->q3evaltext;
                        $item->q4grade= $row_grade[0]->q4evaltext == null ? null : $row_grade[0]->q4evaltext;
                  }
            }

            $sectioninfo = DB::table('sectiondetail')
                            ->where('sectionid',$section->id)
                            ->where('syid',$syid)
                            ->join('teacher',function($join){
                                $join->on('sectiondetail.teacherid','=','teacher.id');
                                $join->where('teacher.deleted',0);
                            })
                            ->select(
                                'lastname',
                                'firstname',
                                'middlename',
                                'suffix',
                                'teacherid'
                            )
                            ->get();

            $adviser = '';
            $teacherid = null;

            foreach($sectioninfo as $item){
                  $temp_middle = '';
                  if($item->middlename != null){
                        $temp_middle = $item->middlename[0].'.';
                  }
                  $adviser = $item->firstname.' '.$temp_middle.' '.$item->lastname.' '.$item->suffix;
                  $teacherid = $item->teacherid;
                  $item->checked = 0;

            }

            $schoolyear = Db::table('sy')
                              ->where('id',$syid)
                              ->first();

            if($syid == 3){
                  $all_ages = array((object)[
                        'b_y'=>\Carbon\Carbon::parse($student->dob)->diff(\Carbon\Carbon::now())->format('%y'),
                        'b_m'=>\Carbon\Carbon::parse($student->dob)->diff(\Carbon\Carbon::now())->format('%m'),
                        'e_y'=>\Carbon\Carbon::parse($student->dob)->diff(\Carbon\Carbon::now())->format('%y'),
                        'e_m'=>\Carbon\Carbon::parse($student->dob)->diff(\Carbon\Carbon::now())->format('%m')
                  ]);
                  $principal = "";
            }else {
                  $all_ages = array((object)[
                        'b_y'=>"",
                        'b_m'=>"",
                        'e_y'=>"",
                        'e_m'=>""
                  ]);
                  $principal = "ALFIE P. BILLION";
            }


            //Attendance
            $attendance_setup = \App\Models\AttendanceSetup\AttendanceSetupData::attendance_setup_list($syid);

            foreach( $attendance_setup as $item){

                  $sf2_setup = DB::table('sf2_setup')
                              ->where('month',$item->month)
                              ->where('year',$item->year)
                              ->where('sectionid',$section->id)
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
                              ->where('teacherid',$teacherid)
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
                                          ->select([
                                          'present',
                                          'absent',
                                          'tardy',
                                          'cc'
                                          ])
                                          ->get();

                  $item->present = collect($student_attendance)->where('present',1)->count() + collect($student_attendance)->where('tardy',1)->count() + collect($student_attendance)->where('cc',1)->count();
                  $item->absent = collect($student_attendance)->where('absent',1)->count();
            
            }

            $pdf = PDF::loadView('principalsportal.forms.sf9layout.spct.preschool',compact('syid','student','grades','setup','section','all_ages','adviser','principal','age_setup','remarks_setup','attendance_setup'))->setPaper('legal','landscape');
            $pdf->getDomPDF()->set_option("enable_php", true)->set_option("DOMPDF_ENABLE_CSS_FLOAT", true);
    
            return $pdf->stream();
      }

      public static function get_student_grades_data($studid = null, $syid = null){

            $student_grade = DB::table('grading_system_pgrades')
                              ->where('studid',$studid)
                              ->where('syid',$syid)
                              ->where('deleted',0)
                              ->get();

            return $student_grade;


      }

      public static function get_student_grades(Request $request){

            $studid = $request->get('studid');
            $syid = $request->get('syid');

            $student_grade = DB::table('grading_system_pgrades')
                              ->where('studid',$studid)
                              ->where('syid',$syid)
                              ->where('deleted',0)
                              ->get();

            return $student_grade;


      }

      public static function store_grades(Request $request){

            $gsdid = $request->get('gsdid');
            $studid = $request->get('studid');
            $quarter = $request->get('quarter');
            $value = $request->get('value');
            $syid = $request->get('syid');

            try{

                  $check_if_exist = DB::table('grading_system_pgrades')
                              ->where('studid',$studid)
                              ->where('syid',$syid)
                              ->where('gsdid',$gsdid)
                              ->where('deleted',0)
                              ->first();
               
                  if(isset($check_if_exist->id)){
                        $quarter_val = 'q'.$quarter.'evaltext';
                        DB::table('grading_system_pgrades')
                                    ->where('id',$check_if_exist->id)
                                    ->update([
                                          'updatedby'=>auth()->user()->id,
                                          'updateddatetime'=>\Carbon\Carbon::now('Asia/Manila'),
                                          $quarter_val=>$value
                                    ]);
                        
                  }else{
                        $quarter_val = 'q'.$quarter.'evaltext';
                        DB::table('grading_system_pgrades')
                                    ->insert([
                                          'syid'=>$syid,
                                          'gsdid'=>$gsdid,
                                          'studid'=>$studid,
                                          'createdby'=>auth()->user()->id,
                                          'createddatetime'=>\Carbon\Carbon::now('Asia/Manila'),
                                          $quarter_val=>$value
                                    ]);
                  }

                  return array((object)[
                        'status'=>1,
                        'data'=>'Updated Successfully!',
                  ]);

            }catch(\Exception $e){
                  return self::store_error($e);
            }
            
      }

      public static function teacher_class(){

            $syid = 3;

            $teacherid = DB::table('teacher')
                              ->where('userid',auth()->user()->id)
                              ->where('deleted',0)
                              ->select('id')
                              ->first()
                              ->id;


            if(auth()->user()->type == 2){
                  $sections = DB::table('sectiondetail')
                        ->join('sections',function($join){
                              $join->on('sectiondetail.sectionid','=','sections.id');
                              $join->where('sections.levelid',3);
                              $join->where('sections.deleted',0);
                        })
                        ->where('sectiondetail.syid',$syid)
                        ->select(
                              'sectionname',
                              'sectionname as text',
                              'sections.id'
                        )
                        ->get();
            }else{
                  $sections = DB::table('sectiondetail')
                        ->join('sections',function($join){
                              $join->on('sectiondetail.sectionid','=','sections.id');
                              $join->where('sections.levelid',3);
                              $join->where('sections.deleted',0);
                        })
                        ->where('sectiondetail.syid',$syid)
                        ->where('sectiondetail.teacherid', $teacherid)
                        ->select(
                              'sectionname',
                              'sectionname as text',
                              'sections.id'
                        )
                        ->get();
            }

         

            $all_info = array();

            foreach($sections as $item){

                  $students = DB::table('enrolledstud')
                                    ->join('studinfo',function($join){
                                          $join->on('enrolledstud.studid','=','studinfo.id');
                                          $join->where('studinfo.deleted',0);
                                    })
                                    ->where('enrolledstud.sectionid',$item->id)
                                    ->where('enrolledstud.syid',$syid)
                                    ->where('enrolledstud.deleted',0)
                                    ->where('enrolledstud.levelid',3)
                                    ->select(
                                          'sid',
                                          'studid as id',
                                          'lastname',
                                          'firstname',
                                          'middlename',
                                          'suffix'
                                    )
                                    ->get();

                  foreach($students as $student_item){
                        $middlename = explode(" ",$student_item->middlename);
                        $temp_middle = '';
                        if($middlename != null){
                              foreach ($middlename as $middlename_item) {
                                    if(strlen($middlename_item) > 0){
                                    $temp_middle .= $middlename_item[0].'.';
                                    } 
                              }
                        }
                        $student_item->student=$student_item->lastname.', '.$student_item->firstname.' '.$student_item->suffix.' '.$temp_middle;
                        $student_item->text = $student_item->sid.' - '.$student_item->student;
                  }

                  array_push($all_info,(object)[
                        'id'=>$item->id,
                        'sectionname'=>$item->sectionname,
                        'text'=>$item->sectionname,
                        'students'=>$students
                  ]);

            }

            return $all_info;

      }


      public static function get_preschool_setup_ajax(Request $request){
            $syid = $request->get('syid');
            return self::get_preschool_setup($syid);

      }

      public static function get_preschool_setup(
            $syid = null
      ){

            $preschool_setup = DB::table('grading_system')
                                    ->join('grading_system_detail',function($join){
                                          $join->on('grading_system.id','=','grading_system_detail.headerid');
                                          $join->where('grading_system_detail.deleted',0);
                                    })
                                    ->where('acadprogid',2)
                                    ->where('levelid',3)
                                    ->where('syid',$syid)
                                    ->where('grading_system.deleted',0)
                                    ->where('grading_system.description','Pre-school Compentencies')
                                    ->select(
                                          'grading_system_detail.*'
                                    )
                                    ->orderBy('sort')
                                    ->orderBy('group')
                                    ->get();

            return $preschool_setup;

      }

      public static function get_preschool_setup_age_ajax(
            $syid = null,
            Request $request
      ){

            $syid = $request->get('syid');
            $preschool_setup = DB::table('grading_system')
                                    ->join('grading_system_detail',function($join){
                                          $join->on('grading_system.id','=','grading_system_detail.headerid');
                                          $join->where('grading_system_detail.deleted',0);
                                    })
                                    ->where('acadprogid',2)
                                    ->where('levelid',3)
                                    ->where('syid',$syid)
                                    ->where('grading_system.description','Kinder Age')
                                    ->where('grading_system.deleted',0)
                                    ->select(
                                          'grading_system_detail.*'
                                    )
                                    ->orderBy('sort')
                                    ->orderBy('group')
                                    ->get();


            return $preschool_setup;

      }

      public static function get_preschool_setup_remarks_ajax(
            $syid = null,
            Request $request
      ){

            $syid = $request->get('syid');
            $preschool_setup = DB::table('grading_system')
                                    ->join('grading_system_detail',function($join){
                                          $join->on('grading_system.id','=','grading_system_detail.headerid');
                                          $join->where('grading_system_detail.deleted',0);
                                    })
                                    ->where('acadprogid',2)
                                    ->where('levelid',3)
                                    ->where('syid',$syid)
                                    ->where('grading_system.description','Kinder Comments')
                                    ->where('grading_system.deleted',0)
                                    ->select(
                                          'grading_system_detail.*'
                                    )
                                    ->orderBy('sort')
                                    ->orderBy('group')
                                    ->get();


            return $preschool_setup;

      }

      
      public static function create_setup_ajax(Request $request){
            $decription = $request->get('decription');
            $sort = $request->get('sort');
            $syid = $request->get('syid');
            $type = $request->get('type');
            $dataid = $request->get('dataid');
            return self::create_setup($decription,$sort,$syid,$type,$dataid);
      }

      public static function create_setup(
            $decription = null,
            $sort = null,
            $syid = null,
            $type = null,
            $dataid = null
      ){

            try{

                  $value = 5;

                  $header = DB::table('grading_system')
                              ->where('syid',$syid)
                              ->where('acadprogid',2)
                              ->where('levelid',3)
                              ->first();

                  if(isset($header->id)){
                        $header = $header->id;
                  }else{
                        $header = self::create_setup_header($syid);

                        if($header[0]->status == 1){
                              $header = $header[0]->new_id;
                        }else{
                              return $header;
                        }
                  }

                  $group = strtoupper($sort);

                  if($type == "header"){

                        $value = 0;
                        $sort_count = DB::table('grading_system_detail')
                                          ->where('value',0)
                                          ->where('deleted',0)
                                          ->where('headerid',$header)
                                          ->select(
                                                'grading_system_detail.id',
                                                'grading_system_detail.sort')
                                          ->get();
                     
                        $count = 1;
                        foreach($sort_count as $item){
                              if(strlen($item->sort) == 1){
                                    $count += 1;
                              }
                        }

                        $sort =  chr(64+$count);
                        $group = NULL;
                      
                  }

                  if($dataid != null){

                        $headersort = DB::table('grading_system_detail')
                                   ->where('id',$dataid)
                                   ->where('deleted',0)
                                   ->select('sort','group')
                                   ->first();

                        if(isset($headersort->sort)){

                              $sort_count = DB::table('grading_system_detail')
                                                ->where('group',$headersort->sort)
                                                ->where('headerid',$header)
                                                ->where('deleted',0)
                                                ->count();
                              
                              $sort = $headersort->sort . chr(65+$sort_count);
                              $group = strtoupper($headersort->sort);

                        }

                        
                  }

                  DB::table('grading_system_detail')
                        ->insert([
                              'headerid'=>$header,
                              'description'=>$decription,
                              'value'=>$value,
                              'sort'=>strtoupper($sort),
                              'group'=>$group
                        ]);
                       
                  return array((object)[
                        'status'=>1,
                        'data'=>'Created Successfully!',
                  ]);

            }catch(\Exception $e){
                  return self::store_error($e);
            }

      }

      public static function delete_setup_ajax(Request $request){
            $dataid = $request->get('dataid');
            $syid = $request->get('syid');
            return self::delete_setup($dataid,$syid);
            
      }

      public static function delete_setup(
            $dataid = null,
            $syid = null
      ){
            try{

                  $check_info = DB::table('grading_system_detail')
                                    ->where('id',$dataid)
                                    ->where('deleted',0)
                                    ->first();

                  if(isset($check_info->id)){
                        if($check_info->value == 0){

                              $check_info = DB::table('grading_system_detail')
                                                ->where('group',$check_info->sort)
                                                ->where('deleted',0)
                                                ->where('value','!=',0)
                                                ->count();

                              if($check_info > 0){
                                    return array((object)[
                                          'status'=>2,
                                          'message'=>'Please remove header items!',
                                    ]);
                              }
                        }
                  }


                  DB::table('grading_system_detail')
                        ->where('id',$dataid)
                        ->where('deleted',0)
                        ->update([
                              'deleted'=>1,
                              'deletedby'=>auth()->user()->id,
                              'deleteddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                        ]);

                  return array((object)[
                        'status'=>1,
                        'message'=>'Deleted Successfully!',
                        'info'=>self::get_preschool_setup($syid)
                  ]);
            }catch(\Exception $e){
                  return self::store_error($e);
            }
      }

      public static function update_setup_ajax(Request $request){
            $dataid = $request->get('dataid');
            $description = $request->get('description');
            $type = $request->get('type');
            $syid = $request->get('syid');
            return self::update_setup($dataid,$description,$type,$syid);
            
      }

      public static function update_setup(
            $dataid = null,
            $description = null,
            $type = null,
            $syid
      ){
            try{

                  DB::table('grading_system_detail')
                        ->where('id',$dataid)
                        ->where('deleted',0)
                        ->update([
                              'description'=>$description,
                              'updatedby'=>auth()->user()->id,
                              'updateddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                        ]);

                  return array((object)[
                        'status'=>1,
                        'message'=>'Deleted Successfully!',
                        'info'=>self::get_preschool_setup($syid)
                  ]);
            }catch(\Exception $e){
                  return self::store_error($e);
            }
      }

      public static function create_setup_header_ajax(Request $request){

            
      }

      public static function create_setup_header(
            $syid = null
      ){
            try{

                  $header = DB::table('grading_system')
                        ->insertGetId([
                              'description'=>'Pre-school Compentencies',
                              'syid'=>$syid,
                              'acadprogid'=>2,
                              'levelid'=>3,
                              'type'=>3,
                              'specification'=>1,
                              'deleted'=>0,
                              'createdby'=>auth()->user()->id,
                              'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                        ]);

                  return array((object)[
                        'status'=>0,
                        'message'=>'Deleted Successfully!',
                        'new_id'=>$headerid
                        // 'info'=>$header
                  ]);

            }catch(\Exception $e){
                  return self::store_error($e);
            }

      }

      public static function store_error($e){
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
