<?php

namespace App\Http\Controllers\StudentControllers;

use Illuminate\Http\Request;
use DB;

class StudentGradeEvaluation extends \App\Http\Controllers\Controller
{

    public static function student_grade_evaluation(Request $request){

        $studid = $request->get('studid');

        $course = DB::table('studinfo')
                        ->join('college_courses',function($join){
                            $join->on('studinfo.courseid','=','college_courses.id');
                            $join->where('college_courses.deleted',0);
                        })
                        ->where('studinfo.id',$studid)
                        ->select(
                            'courseid',
                            'courseDesc'
                        )
                        ->first();

        $student_curriculum = DB::table('college_studentcurriculum')
                                ->join('college_curriculum',function($join){
                                    $join->on('college_studentcurriculum.curriculumid','=','college_curriculum.id');
                                    $join->where('college_studentcurriculum.deleted',0);
                                })
                                ->where('studid',$studid)
                                ->where('college_studentcurriculum.deleted',0)
                                ->select(
                                    'curriculumid',
                                    'curriculumname'
                                )
                                ->first();

        $student_prospectus = DB::table('college_prospectus')
                                ->where('deleted',0)
                                ->where('courseID',$course->courseid)
                                ->where('curriculumID',$student_curriculum->curriculumid)
                                ->select(
                                    'id',
                                    'subjectID',
                                    'semesterID',
                                    'yearId',
                                    'lecunits',
                                    'labunits',
                                    'subjDesc',
                                    'subjCode',
                                    'psubjsort'
                                )
                                ->get();


        $student_grades_teacher = DB::table('college_studentprospectus')
                                        ->join('college_sections',function($join){
                                            $join->on('college_studentprospectus.sectionid','=','college_sections.id');
                                            $join->where('college_sections.deleted',0);
                                        })
                                        ->join('college_prospectus',function($join){
                                            $join->on('college_studentprospectus.prospectusID','=','college_prospectus.id');
                                            $join->where('college_prospectus.deleted',0);
                                        })
                                        ->where('studid',$studid)
                                        ->select(
                                            'subjectID',
                                            'college_studentprospectus.prospectusID',
                                            'college_studentprospectus.prelemgrade',
                                            'midtermgrade',
                                            'college_studentprospectus.prefigrade',
                                            'college_studentprospectus.finalgrade',
                                            'college_studentprospectus.prelemstatus',
                                            'college_studentprospectus.midtermstatus',
                                            'college_studentprospectus.prefistatus',
                                            'college_studentprospectus.finalstatus',
                                            'college_sections.yearID',
                                            'college_sections.semesterID',
                                            'college_studentprospectus.syid',
                                            'college_studentprospectus.semid',
                                            'college_prospectus.subjCode',
                                            'college_prospectus.subjDesc',
                                            'fg',
                                            'fgremarks'
                                        )
                                        ->get();

        // return $student_grades_teacher;

        $student_enrollment = DB::table('college_enrolledstud')
                                    ->where('studid',$studid)
                                    ->where('deleted',0)
                                    ->select(
                                        'yearLevel',
                                        'semid',
                                        'syid'
                                    )
                                    ->get();

        $student_prospectus = collect($student_prospectus)->toArray();

        foreach($student_prospectus as $item){

            $check = collect($student_grades_teacher)
                        ->where('prospectusID',$item->id)
                        ->values();

            if(count($check) == 1){
                $item->prelemgrade = $check[0]->prelemgrade;
                $item->midtermgrade = $check[0]->midtermgrade;
                $item->prefigrade = $check[0]->prefigrade;
                $item->finalgrade = $check[0]->finalgrade;
				$item->fg = $check[0]->fg;
                $item->fgremarks = $check[0]->fgremarks;
            }else if(count($check) > 1){
				
				
                foreach($check as $check_item){
                    if($item->semesterID == $check_item->semesterID && $item->yearId == $check_item->yearID){

                        //if regular subject

                        $item->prelemgrade = $check_item->prelemgrade;
                        $item->midtermgrade = $check_item->midtermgrade;
                        $item->prefigrade = $check_item->prefigrade;
                        $item->finalgrade = $check_item->finalgrade;
                        $item->fg = $check_item->fg;
                        $item->fgremarks = $check_item->fgremarks;
                    }else{

                        $check_enrollment = collect($student_enrollment)
                                                ->where('syid',$check_item->syid)
                                                ->where('semid',$check_item->semid)
                                                ->first();

                        if(isset($check_enrollment->semid)){
                       
                            array_push($student_prospectus, (object)[
                                "id"=> $item->id,
                                "subjectID"=>  $item->subjectID,
                                "semesterID"=> $check_item->semid,
                                "yearId"=> $check_enrollment->yearLevel,
                                "lecunits"=> 3,
                                "labunits"=> 0,
                                "subjDesc"=> $item->subjDesc,
                                "subjCode"=> $item->subjCode,
                                "psubjsort"=> 'Z',
                                "prelemgrade"=> $check_item->prelemgrade,
                                "midtermgrade"=> $check_item->midtermgrade,
                                "prefigrade"=> $check_item->prefigrade,
                                "finalgrade"=> $check_item->finalgrade,
                                'fg'=>$check_item->fg,
                                'fgremarks'=>$check_item->fgremarks
                            ]);

                        }
                       
                    }
                }
            }else{

                $check = collect($student_grades_teacher)->where('subjectID',$item->subjectID)->values();
                $with_record = 0;

                if(count( $check) > 0){
					$with_record = 1;
				}

				if($with_record == 0){
					$check = collect($student_grades_teacher)->where('subjCode',$item->subjCode)->values();
				}
				
				if(count( $check) > 0){
					$with_record = 1;
				}
				
				if($with_record == 0){
					$check = collect($student_grades_teacher)->where('subjDesc',$item->subjDesc)->values();
				}
				
				if(count( $check) > 0){
					$with_record = 1;
				}

                if(count($check) != 0){
                    $item->prelemgrade = $check[0]->prelemgrade;
                    $item->midtermgrade = $check[0]->midtermgrade;
                    $item->prefigrade = $check[0]->prefigrade;
                    $item->finalgrade = $check[0]->finalgrade;
                    $item->fg = $check[0]->fg;
                    $item->fgremarks = $check[0]->fgremarks;
                }else{
                    $item->prelemgrade = null;
                    $item->midtermgrade = null;
                    $item->prefigrade = null;
                    $item->finalgrade = null;
                    $item->fg = null;
                    $item->fgremarks = null;
                }
            }

        }

        
        $yearLevel = DB::table('gradelevel')
                        ->where('acadprogid',6)
                        ->where('deleted',0)
                        ->select(
                            'id',
                            'levelname'
                        )
                        ->get();

      
        $semester = DB::table('semester')
                        ->where('deleted',0)
                        ->select(
                              'id',
                              'semester'
                        )
                        ->get();

        return array((object)[
            'curriculum'=>$student_curriculum,
            'evaluation'=>$student_prospectus,
            'yearLevel'=>$yearLevel,
            'semester'=>$semester,
            'course'=>$course
        ]);




    }


}
