<?php

namespace App\Http\Controllers\SuperAdminController\Setup;

use Illuminate\Http\Request;
use File;
use DB;
use Image;

class SubjectSchedule extends \App\Http\Controllers\Controller
{

      // public static function scheduledetail(Request $request){

      //       $sectionid = $request->get('sectionid');
      //       $schedid = $request->get('schedid');


      //       $schedule = DB::table('college_sections')
      //                         ->join('college_classsched',function($join) use($schedid){
      //                               $join->on('college_sections.id','=','college_classsched.sectionID');
      //                               $join->where('college_classsched.deleted',0);
      //                               $join->where('college_classsched.id',$schedid);
      //                         })
      //                         ->leftJoin('teacher',function($join) use($schedid){
      //                               $join->on('college_classsched.teacherID','=','teacher.id');
      //                               $join->where('teacher.deleted',0);
      //                         })
      //                         ->where('college_sections.id',$sectionid)
      //                         ->select(
      //                               'college_classsched.id',
      //                               'yearID',
      //                               'capacity',
      //                               'teacherID',
      //                               'lastname',
      //                               'firstname',
      //                               'capacity',
      //                               'section_specification'
      //                         )
      //                         ->first();

      //       $schedule_more = DB::table('college_scheddetail')
      //                               ->where('headerID',$schedule->id)
      //                               ->where('college_scheddetail.deleted',0)
      //                               ->leftJoin('rooms',function($join) use($schedid){
      //                                     $join->on('college_scheddetail.roomid','=','rooms.id');
      //                                     $join->where('teacher.deleted',0);
      //                               })
      //                               ->get();

      //       return $schedule;

      // }


      public static function addsched(Request $request){

            try{

                  $subjid = $request->get('headerid');
                  $syid = $request->get('syid');
                  $semid = $request->get('semid');
                  $days = $request->get('days');
                  $room = $request->get('room');
                  $term = $request->get('term');
                  $letter_count = 65;

                  $subject = DB::table('college_prospectus')         
                                    ->where('subjectID',$subjid)
                                    ->where('deleted',0)
                                    ->first();

                  if(!isset($subject)){
                        return "Subject is not added in the prospecuts";
                  }

                  //check if subject schedule exist 
                  $section_count = DB::table('college_classsched')
                                    ->join('college_sections',function($join){
                                          $join->on('college_sections.id','=','college_classsched.sectionid');
                                          $join->where('college_sections.deleted',0);
                                          $join->where('issubjsched',1);
                                    })
                                    ->where('college_classsched.subjectID',$subject->id)
                                    ->where('college_classsched.syiD',$syid)
                                    ->where('college_classsched.semesterID',$semid)
                                    ->where('college_classsched.deleted',0)
                                    ->count();

                  //insert section
                  $sectionid = DB::table('college_sections')
                                    ->insertGetID([
                                          'sectionDesc'=>$subject->subjCode.' '.chr($letter_count + $section_count),
                                          'yearID'=>$subject->yearID,
                                          'semesterID'=>$semid,
                                          'syID'=>$syid,
                                          'courseID'=>$subject->courseID,
                                          'curriculumid'=>$subject->curriculumID,
                                          'section_specification'=>$request->get('classtype'),
                                          'issubjsched'=>1,
                                          'createdby'=>auth()->user()->id,
                                          'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                                    ]);

                  $classschedid = DB::table('college_classsched')
                                    ->insertGetId([
                                          'syID'=>$syid,
                                          'semesterID'=>$semid,
                                          'sectionID'=>$sectionid,
                                          // 'schedgroup'=>$request->get('schedgroup'),
                                          'subjectID'=>$subject->id,
                                          'teacherID'=>$request->get('teacherid'),
                                          'capacity'=>$request->get('capacity'),
                                          'createdby'=>auth()->user()->id,
                                          'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                                    ]);


                  $time = explode(" - ", $request->get('time'));
                  $stime = \Carbon\Carbon::create($time[0])->isoFormat('HH:mm:ss');
                  $etime = \Carbon\Carbon::create($time[1])->isoFormat('HH:mm:ss');

                  foreach($days as $item){
                        DB::table('college_scheddetail')
                              ->insert([
                                    'headerid'=> $classschedid,
                                    'day'=>$item,
                                    'stime'=>$stime,
                                    'etime'=>$etime,
                                    'roomid'=>$room,
                                    'schedotherclass'=>$term,
                                    'deleted'=>'0',
                                    'createdby'=>auth()->user()->id,
                                    'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                              ]);
                  }

                  $schedgroup = $request->get('schedgroup');

                  if($schedgroup != ""){
                        foreach($schedgroup as $item){
                              $check = DB::table('college_schedgroup_detail')
                                          ->where('schedid',$classschedid)
                                          ->where('groupid',$item)
                                          ->where('deleted',0)
                                          ->count();

                              if($check == 0){
                                    DB::table('college_schedgroup_detail')
                                          ->insert([
                                                'schedid'=>$classschedid,
                                                'groupid'=>$item,
                                                'createdby'=>auth()->user()->id,
                                                'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                                          ]);
                              }
                        }
                  
                        $check = DB::table('college_schedgroup_detail')
                                    ->where('schedid',$request->get('id'))
                                    ->whereNotIn('groupid',$schedgroup)
                                    ->update([
                                          'deleted'=>1,
                                          'deletedby'=>auth()->user()->id,
                                          'deleteddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                                    ]);
                  }else{
                        $check = DB::table('college_schedgroup_detail')
                                    ->where('schedid',$request->get('id'))
                                    ->update([
                                          'deleted'=>1,
                                          'deletedby'=>auth()->user()->id,
                                          'deleteddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                                    ]);
                  }


                  return array((object)[
                        'status'=>1,
                        'message'=>'Schedule Created',
                  ]);

            }catch(\Exception $e){
                  return self::store_error($e);
            }
            
      }

    
      public static function sy(){

            $sy = DB::table('sy')
                        ->select(
                              'id',
                              'sydesc',
                              'sydesc as text',
                              'isactive'
                        )
                        ->get();

            foreach($sy as $item){
                  $item->selected = $item->isactive == 1 ? true : false;
            }

            return $sy;
            
      }

      public static function semester(){

            $semester = DB::table('semester')
                        ->select(
                              'id',
                              'semester',
                              'semester as text',
                              'isactive'
                        )
                        ->get();

            foreach($semester as $item){
                  $item->selected = $item->isactive == 1 ? true : false;
            }

            return $semester;

      }

      public static function schedgroup(Request $request){

            $search = $request->get('search');
    
            $schedgroup = DB::table('college_schedgroup')
                        ->where('deleted',0)
                        ->where(function($query) use($search){
                              if($search != null && $search != ""){
                                    $query->orWhere('schedgroupdesc','like','%'.$search.'%');
                              }
                        })
                        ->select(
                              'id',
                              'schedgroupdesc',
                              'schedgroupdesc as text'
                        )
                        ->take(10)
                        ->skip($request->get('page')*10)
                        ->get();
    
            $schedgroup_count = DB::table('college_schedgroup')
                        ->where('deleted',0)
                        ->where(function($query) use($search){
                              if($search != null && $search != ""){
                                    $query->orWhere('schedgroupdesc','like','%'.$search.'%');
                              }
                        })
                        ->count();
    
            return @json_encode((object)[
                  "results"=>$schedgroup,
                  "pagination"=>(object)[
                        "more"=>$schedgroup_count > 10  ? true :false
                  ],
                  "count_filtered"=>$schedgroup_count
            ]);
            
      }
      


      public static function rooms(Request $request){

        $search = $request->get('search');

        $rooms = DB::table('rooms')
                    ->where('deleted',0)
                    ->where(function($query) use($search){
                          if($search != null && $search != ""){
                                $query->orWhere('roomname','like','%'.$search.'%');
                          }
                    })
                    ->select(
                          'id',
                          'roomname',
                          'roomname as text'
                    )
                    ->take(10)
                    ->skip($request->get('page')*10)
                    ->orderBy('roomname')
                    ->get();

        $rooms_count = DB::table('rooms')
                    ->where('deleted',0)
                    ->where(function($query) use($search){
                          if($search != null && $search != ""){
                                $query->orWhere('roomname','like','%'.$search.'%');
                          }
                    })
                    ->count();

        return @json_encode((object)[
              "results"=>$rooms,
              "pagination"=>(object)[
                    "more"=>$rooms_count > 10  ? true :false
              ],
              "count_filtered"=>$rooms_count
        ]);
        
    }


    public static function subjects(Request $request){

        $search = $request->get('search');

        $all_subjects = DB::table('college_subjects')
                          ->join('college_prospectus',function($join){
                              $join->on('college_subjects.id','=','college_prospectus.subjectID');
                              $join->where('college_prospectus.deleted',0);
                          })
                          ->where('college_subjects.deleted',0)
                          ->where(function($query) use($search){
                                if($search != null && $search != ""){
                                      $query->orWhere('college_subjects.subjCode','like','%'.$search.'%');
                                      $query->orWhere('college_subjects.subjDesc','like','%'.$search.'%');
                                }
                          })
                          ->take(10)
                          ->skip($request->get('page')*10)
                          ->select(
                                'college_subjects.id',
                                'college_subjects.subjCode',
                                'college_subjects.subjDesc',
                                'college_subjects.lecunits',
                                'college_subjects.labunits',
                                DB::raw("CONCAT(college_subjects.subjCode,' - ',college_subjects.subjDesc) as text")

                          )
                          ->groupBy
                          (
                              [
                                  'subjCode',
                                  'subjDesc',
                                  'lecunits',
                                  'labunits'
                              ]
                          )
                          ->get();


        $all_subjects_count = DB::table('college_subjects')
                  ->join('college_prospectus',function($join){
                        $join->on('college_subjects.id','=','college_prospectus.subjectID');
                        $join->where('college_prospectus.deleted',0);
                  })
                  ->where('college_subjects.deleted',0)
                  ->where(function($query) use($search){
                        if($search != null && $search != ""){
                              $query->orWhere('college_subjects.subjCode','like','%'.$search.'%');
                              $query->orWhere('college_subjects.subjDesc','like','%'.$search.'%');
                        }
                  })
                  ->select(
                        'college_subjects.id',
                        'college_subjects.subjCode',
                        'college_subjectssubjDesc',
                        DB::raw("CONCAT(college_subjects.subjCode,' - ',college_subjects.subjDesc) as text")
                  )
                  ->count();       
           
        return @json_encode((object)[
              "results"=>$all_subjects,
              "pagination"=>(object)[
                    "more"=>($request->get('page')*10) < $all_subjects_count ? true : false
              ],
              "count_filtered"=>$all_subjects_count
        ]);
    

    }


    public static function teachers(Request $request){

        $syid = $request->get('syid');

        $teacher_array = array();

        $search = $request->get('search');



      //   $teachers_faspriv = DB::table('teacher')
      //                   ->where('teacher.deleted',0)
      //                   ->join('faspriv',function($join){
      //                           $join->on('teacher.userid','=','faspriv.userid');
      //                           $join->where('faspriv.deleted',0);
      //                           $join->where('usertype',18);
      //                   })
      //                   ->where(function($query) use($search){
      //                           if($search != null && $search != ""){
      //                               $query->orWhere('lastname','like','%'.$search.'%');
      //                               $query->orWhere('firstname','like','%'.$search.'%');
      //                           }
      //                   })
      //                   ->take(10)
      //                   ->skip($request->get('page')*10)
      //                   ->select(
      //                           'teacher.id',
      //                           'firstname',
      //                           'lastname',
      //                           'middlename',
      //                           'title',
      //                           'tid',
      //                           'suffix',
      //                           DB::raw("CONCAT(teacher.lastname,', ',teacher.firstname) as text")
                                
      //                   ) 
      //                   ->distinct();
                        // ->orderBy('lastname')

       $teachers_faspriv = DB::table('faspriv')
                              ->join('teacher',function($join){
                                    $join->on('faspriv.userid','=','teacher.id');
                                    $join->where('teacher.deleted',0);
                              })
                              ->where('faspriv.deleted',0)
                              ->where('faspriv.usertype',18)
                              ->select('teacher.id')
                              ->get();

       $ci_teachers = DB::table('teacher')
                        ->where('teacher.usertypeid',18)
                        ->where('deleted',0)
                        ->select('id')
                        ->get();

        $ci_teachers = collect($ci_teachers)->toArray();

        foreach($teachers_faspriv as $item){
            array_push($ci_teachers,$item);
        }
        

        $teachers = DB::table('teacher')
                        // ->where('teacher.usertypeid',18)
                        ->where('teacher.deleted',0)
                        ->whereIn('id',collect($ci_teachers)->pluck('id'))
                        // ->orWhereIn ('userid',collect($teachers_faspriv)->pluck('userid'))
                        ->where(function($query) use($search){
                                if($search != null && $search != ""){
                                    $query->orWhere('lastname','like','%'.$search.'%');
                                    $query->orWhere('firstname','like','%'.$search.'%');
                                }
                              //  
                        })
                        ->take(10)
                        ->skip($request->get('page')*10)
                        // ->union($teachers_faspriv)
                        ->select(
                                'teacher.id',
                                'firstname',
                                'lastname',
                                'middlename',
                                'title',
                                'tid',
                                'suffix',
                                DB::raw("CONCAT(teacher.lastname,', ',teacher.firstname) as text")
                        )
                        ->distinct()
                        ->orderBy('lastname')
                        ->get();

        $teachers_count = DB::table('teacher')
                        // ->where('teacher.usertypeid',18)
                        ->where('teacher.deleted',0)
                        ->whereIn('id',collect($ci_teachers)->pluck('id'))
                        // ->orWhereIn ('userid',collect($teachers_faspriv)->pluck('userid'))
                        ->where(function($query) use($search){
                                if($search != null && $search != ""){
                                    $query->orWhere('lastname','like','%'.$search.'%');
                                    $query->orWhere('firstname','like','%'.$search.'%');
                                }
                        })
                        // ->union($teachers_faspriv)
                        ->select(
                                'teacher.id',
                                'firstname',
                                'lastname',
                                'middlename',
                                'title',
                                'tid',
                                'suffix',
                                DB::raw("CONCAT(teacher.lastname,', ',teacher.firstname) as text")
                        )
                        ->distinct()
                        ->count();

        return @json_encode((object)[
            "results"=>$teachers,
            "pagination"=>(object)[
                  "more"=>($request->get('page')*10) < $teachers_count  ? true :false
            ],
            "count_filtered"=>$teachers_count
        ]);

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
              'icon'=>'error',
              'message'=>'Something went wrong!'
        ]);
    }
      
}
