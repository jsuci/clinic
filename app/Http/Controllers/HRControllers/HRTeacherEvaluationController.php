<?php

namespace App\Http\Controllers\HRControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Grading\GradeSchool;
use App\Models\Student\TeacherEvaluation;
use DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
class HRTeacherEvaluationController extends Controller
{
    
    public function admin_view_results(Request $request){
        
        $teachers = DB::table('teacheracadprog')
                        // ->where('syid',$syid)
                        ->join('teacher',function($join){
                            $join->on('teacher.id','teacheracadprog.teacherid');
                            $join->where('teacher.deleted','0');
                            $join->where('teacher.isactive','1');
                        })
                        ->where('teacheracadprog.deleted',0)
                        ->select(
                            'lastname',
                            'firstname',
                            'middlename',
                            'teacher.id',
                            'userid',
                            'usertypeid',
                            'tid'
                        )
                        ->distinct()
                        ->get();

        foreach($teachers as $key=>$item){
            
            $middlename = '';
            if($item->middlename != null){
                $middlename = ' '.$item->middlename[0].'.';
            }
            
            $item->teacher = $item->firstname.$middlename.' '.$item->lastname;

            if($item->usertypeid != 1){

                $checkWithPriv = DB::table('faspriv')
                                    ->where('userid',$item->userid)
                                    ->where('usertype',1)
                                    ->count();

                if($checkWithPriv == 0){

                    unset($teachers[$key]);

                }

            }
            
            $item->search = $item->teacher.' '.$item->tid;

        }

        $teachers = collect($teachers)->values();

        return view('hr.summaries.teachersevaluation')->with('teachers',$teachers);

    

    }
    // public static function teacher_schedule(Request $request){
    //     return $request->all();
        
    //     $subjects = GradeSchool::teacher_assign_subjects($request->get('teacherid'));

    //     $teacherEvaluation = TeacherEvaluation::getTeacherEvaluationSetup();

    //     if($teacherEvaluation[0]->status == 0){

    //         return $teacherEvaluation;

    //     }
    //     else{

    //         $teacherEvaluation =  $teacherEvaluation[0]->data;

    //     }

    //     $evaluationDetail = TeacherEvaluation::evaluate_teacher_evaluation_setup($teacherEvaluation[0]->id);

    //     if($evaluationDetail[0]->status == 0){

    //         return    $evaluationDetail;

    //     }
    //     else{

    //         $evaluationDetail =  $evaluationDetail[0]->data;

    //     }

    //     $ratingValue = TeacherEvaluation::evaluate_teacher_evaluation_rating_value($teacherEvaluation[0]->id);

    //     if($ratingValue[0]->status == 0){

    //         return    $ratingValue;

    //     }
    //     else{

    //         $ratingValue =  $ratingValue[0]->data;

    //     }

    //     foreach( $subjects as $item){

    //         $studentCount = DB::table('studinfo')
    //                           ->whereIn('studstatus',[1,2,4])
    //                           ->where('sectionid',$item->id)
    //                           ->where('studinfo.deleted',0)
    //                           ->count();

    //         $item->studcount = $studentCount;

    //     }

    //     if($request->GET('exporttype') == 'excel')
    //     {

    //     }else{

    //     }

    //     // return $evaluationDetail;

    //     // return view('superadmin.pages.gradingsystem.teacherEvaluation.assignment_table')
    //     //         ->with('evaluationDetail',$evaluationDetail)
    //     //         ->with('ratingvalue',$ratingValue)
    //     //         ->with('subjects',$subjects);


        
    // }
    
    public function viewcomment(Request $request)
    {
        $quarter = Db::table('quarter_setup')
            ->where('isactive','1')
            ->where('deleted','0')
            ->first()->id;
        // return $request->all();
        $syid = Db::table('sy')
            ->where('isactive','1')
            ->first()->id;
        $semid = Db::table('semester')
            ->where('isactive','1')
            ->first()->id;

        $quarter = 1;
        $syid = 2;
        $semid = 2;

        $evaluations = DB::table('grading_system_student_header')
            ->select('q'.$quarter.'com')
            ->join('grading_system_student_evalcom','grading_system_student_header.id','=','grading_system_student_evalcom.evalcom_studheader')
            ->where('syid', $syid)
            ->where('grading_system_student_evalcom.deleted',0)
            ->where('semid',$semid)
            ->where('grading_system_student_header.teacherid',$request->get('teacherid'))
            ->get();

        return collect($evaluations)->pluck('q'.$quarter.'com');
    }
    public function check_evaluation(Request $request){
        
        $quarter = Db::table('quarter_setup')
            ->where('isactive','1')
            ->where('deleted','0')
            ->first()->id;
            
        $quarter = $request->get('yearfilter');
            
        $teacherEvaluation = TeacherEvaluation::getTeacherEvaluationSetup();

        if($teacherEvaluation[0]->status == 0){

            return $teacherEvaluation;

        }
        else{

            $teacherEvaluation =  $teacherEvaluation[0]->data;

        }

      
        $evaluationDetail = TeacherEvaluation::evaluate_teacher_evaluation_setup($teacherEvaluation[0]->id);

        if($evaluationDetail[0]->status == 0){

            return    $evaluationDetail;

        }
        else{

            $evaluationDetail =  $evaluationDetail[0]->data;

        }

        $ratingValue = TeacherEvaluation::evaluate_teacher_evaluation_rating_value($teacherEvaluation[0]->id);

        if($ratingValue[0]->status == 0){

            return    $ratingValue;

        }
        else{

            $ratingValue =  $ratingValue[0]->data;

        }
        
        $syid = $request->get('syid');

        $overall = array();
        // return $evaluationDetail;
        foreach($request->get('ddvalues') as $value)
        {

            $data = array((object)[
                'respondents'=>null,
                'responses'=>null
            ]);
    
            $field = 'q'.$quarter.'val';
            $detail = array();
            $newRv = array();

            foreach($evaluationDetail as $item){
                
                if($item->id == $value)
                {
                    $responses = DB::table('grading_system_student_header')
                                    ->join('grading_system_student_evaluation',function($join){
                                        $join->on('grading_system_student_header.id','=','grading_system_student_evaluation.studheader');
                                        $join->where('grading_system_student_evaluation.deleted',0);
                                    })
                                    ->where('grading_system_student_header.syid',$syid)
                                    ->where('teacherid',$request->get('teacherid'))
                                    ->where('grading_system_student_header.deleted',0)
                                    ->where('grading_system_student_evaluation.gsid',$item->id)
                                    ->select($field)
                                    ->get();
        
                    foreach($ratingValue as $rtvalue){
        
                        array_push($newRv,(object)[
                            'rtid'=>$rtvalue->id,
                            'ratingCount'=>collect($responses)->where($field, $rtvalue->value)->count(),
                            'ratingvalue'=> $rtvalue->description
                        ]);
        
                    }
        
                    array_push($detail, (object)[
                        'detail'=>$item,
                        'responses'=>$newRv
                    ]);

                    $respondents = DB::table('grading_system_student_header')
                                    ->join('grading_system_student_evaluation',function($join){
                                        $join->on('grading_system_student_header.id','=','grading_system_student_evaluation.studheader');
                                        $join->where('grading_system_student_evaluation.deleted',0);
                                    })
                                    ->where('grading_system_student_header.syid',$syid)
                                    ->where($field, '!=' ,null)
                                    ->where('teacherid',$request->get('teacherid'))
                                    ->where('grading_system_student_header.deleted',0)
                                    ->where('grading_system_student_evaluation.gsid',$item->id)
                                    ->select('studid')
                                    ->count();
        
                    $data[0]->respondents  = $respondents;
                    $data[0]->responses  = $detail;
                }
                
    
            }

            array_push($overall, $data);
        }

        $overalldetails = collect($overall)->flatten();
        
        $teacherinfo = DB::table('teacher')
            ->select(
                'teacher.lastname',
                'teacher.firstname',
                'teacher.middlename',
                'teacher.suffix',
                'teacher.tid'
            )
            ->where('id', $request->get('teacherid'))
            ->first();
            
        if($request->get('exporttype') == 'pdf')
        {
            return 'Not yet supported';

        }elseif($request->get('exporttype') == 'excel')
        {

           
            $groupresult = array();
            foreach ($overalldetails as $element) {
                $groupresult[$element->responses[0]->detail->group][] = $element;
            }
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $spreadsheet->getProperties()
                    ->setTitle('Work Sheet 1')
                    ->setSubject('Office 2007 XLSX Test Document')
                    ->setDescription('PhpOffice')
                    ->setKeywords('PhpOffice')
                    ->setCategory('PhpOffice');
            
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Work Sheet 1');
            // foreach(range('A','Z') as $columnID) {
            //     $sheet->getColumnDimension($columnID)
            //         ->setAutoSize(true);
            // }
            $sheet
                ->setCellValue('A1', 'Name')
                ->setCellValue('B1', ':')
                ->setCellValue('C1', strtoupper($teacherinfo->lastname).', '.strtoupper($teacherinfo->firstname).' '.strtoupper($teacherinfo->middlename).' '.strtoupper($teacherinfo->suffix))
                ->setCellValue('A2','Date')
                ->setCellValue('B2',':')
                ->setCellValue('C2',date('F d, Y'))
                ->setCellValue('A3','Submitted answers')
                ->setCellValue('B3',':')
                ->setCellValue('C3',$overalldetails[0]->respondents.' ')
                ->setCellValue('A4','Questions')
                ->setCellValue('B4',':')
                ->setCellValue('C4',count($overalldetails).' ')
                ->setCellValue('C6','Question')
                ->setCellValue('D6','Responses')
                ->setCellValue('D1',$overalldetails[0]->respondents);


            
            $alphabet = range('D', 'Z');
            $starcellno = 7;
            
            
            $sheet->getColumnDimension('A')->setWidth(18);
            $sheet->getColumnDimension('B')->setWidth(3);
            $sheet->getColumnDimension('C')->setWidth(80);
            $sheet->getColumnDimension('D')->setWidth(12);
            $sheet->getColumnDimension('E')->setWidth(12);
            $sheet->getColumnDimension('F')->setWidth(12);
            $sheet->getColumnDimension('G')->setWidth(12);
            $sheet->getColumnDimension('H')->setWidth(12);
            $sheet->getColumnDimension('I')->setWidth(12);
 
            $ratingvalues = array();
            // return $overalldetails;
            if(count($overalldetails)>0)
            {
                $count = 1;


                foreach($overalldetails[0]->responses[0]->responses as $rateval)
                {
                    array_push($ratingvalues,$rateval->ratingvalue);
                }
                $ratingvalues = collect($ratingvalues)->reverse()->values();

                foreach($overalldetails as $overalldetail)
                {
                    $sheet
                        ->setCellValue('B'.$starcellno, $count.'. ')
                        ->setCellValue('C'.$starcellno, $overalldetail->responses[0]->detail->description);

                    $responseshead          = $overalldetail->responses[0]->responses;
                
                    $responsesheadcount     = count($overalldetail->responses[0]->responses);

                    $letterrangehead = range('D',$alphabet[$responsesheadcount]);

                    
                    if($responsesheadcount>0)
                    {
                        
                        for($y = 0; $y<count($responseshead) ; $y++)
                        {
                                $sheet
                                    ->setCellValue($letterrangehead[$y].$starcellno, $ratingvalues[$y]);
                           
                                $responsesheadcount-=1;
                        }
                        
                    }
                    
                    $count+=1;

                    $starcellno+=1;

                    $responses          = collect($overalldetail->responses[0]->responses)->reverse()->values()->all();
                    
                    $responsescount     = count($overalldetail->responses[0]->responses) - 1;

                    $letterrange = range('D',$alphabet[$responsescount]);
                    
                    if($responsescount>0)
                    {
                        for($x = 0; $x<=$responsescount ; $x++)
                        {

                                $sheet
                                    ->setCellValue($letterrange[$x].$starcellno, $responses[$x]->ratingCount);
                                    
                           
                        }
                        $starcellno+=1;

                    
                        $responsesfootcounter     = count($overalldetail->responses[0]->responses);

                        $responsesfootcount     = count($overalldetail->responses[0]->responses) - 1;

                        $letterrangefoot = range('D',$alphabet[$responsesfootcount]);

                        for($z = 0; $z<=$responsesfootcount ; $z++)
                        {

                            if($responses[$z]->ratingCount == 0)
                            {
                                $sheet
                                    ->setCellValue($letterrangefoot[$z].$starcellno, $responses[$z]->ratingCount);
                            }else{

                                #example
                                #[ 6 (rate) * (numberofresponses selected 6 as a rate) / number of respondents] / number of rating selection
                                $sheet
                                    ->setCellValue($letterrangefoot[$z].$starcellno, (($responsesfootcounter*$responses[$z]->ratingCount)/$overalldetail->respondents)/count($overalldetail->responses[0]->responses));
                            }
                            $responsesfootcounter-=1;
                        }

                        $starcellno+=1;


                    }
                    $starcellno+=1;
                }
            }
            

            $syid = $request->get('syid');
                
            $semid = Db::table('semester')
                ->where('isactive','1')
                ->first()->id;

            $evaluations = DB::table('grading_system_student_header')
                ->select('q'.$quarter.'com')
                ->join('grading_system_student_evalcom','grading_system_student_header.id','=','grading_system_student_evalcom.evalcom_studheader')
                ->where('syid', $syid)
                ->where('grading_system_student_header.deleted',0)
                ->where('grading_system_student_evalcom.deleted',0)
                // ->where('semid',$semid)
                ->where('grading_system_student_header.teacherid',$request->get('teacherid'))
                ->get();

            // return $evaluationssds;
            $starcellno+=4;
            $sheet->setCellValue('A'.$starcellno, 'COMMENTS');

            $commentcount = 1;
            if(count($evaluations)>0)
            {
                foreach($evaluations as $comment)
                {
                    $field = 'q'.$quarter.'com';
                    if($comment->$field != null){
                        
                        $sheet->setCellValue('B'.$starcellno, $commentcount.'. ');
                        $sheet->setCellValue('C'.$starcellno, $comment->$field);
    
                        $starcellno+=1;
                        $commentcount+=1;
                    }
                    
                }
            }
            //WorkSheet 2
            $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex(1);

            $spreadsheet->getActiveSheet()->setTitle('Result');

            $sheet2 = $spreadsheet->getActiveSheet();

            foreach(range('A','Z') as $columnID) {
                $sheet2->getColumnDimension($columnID)
                    ->setAutoSize(true);
            }
           
            $sheet2startcell = 8;
            
            $groupaverage = array();

            if(count($groupresult)>0)
            {
                $count = 1;

                // return $ratingvalues;
                foreach($groupresult as $groupkey => $group)
                {
                    // return $group;
                    $start = 0;
                    $end = 0;
                    foreach($group as $overalldetailkey => $overalldetail)
                    {
                        if($overalldetailkey == 0)
                        {
                            $start = $count;
                            $end = $count+count($group)-1;
                        }
                        
                        $letterval = 68;
                        $stringvalue = "";

                        foreach($ratingvalues as $ratingval)
                        {
                            $stringvalue.="('Work Sheet 1'!".chr($letterval).$sheet2startcell."*".$ratingval.")+";
                            $letterval+=1;
                        }

                        $stringvalue = substr($stringvalue,0,-1);
                        $sheet2->setCellValue('A'.$count, $count.'. '.$overalldetail->responses[0]->detail->description);
                        $sheet2->setCellValue('B'.$count, "=(".$stringvalue.")/'Work Sheet 1'!D1");

                        $count+=1;
                        $sheet2startcell+=4;
                    }
                    array_push($groupaverage,(object)array(
                        'desc'      => $groupkey,
                        'start'     => $start,
                        'end'     => $end
                    ));
                }
            }
            $count+=4;

            $colorfill = array('ecec13','b6ec13','49ec13','3bbd0f');
            $colorindex=0;
            foreach($groupaverage as $average)
            {
                
                $sheet2->getStyle('B'.$average->start.':B'.$average->end)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB($colorfill[$colorindex]);
                   
                // return collect($average);
                $sheet2->setCellValue('A'.$count, $average->desc);
                $sheet2->setCellValue('B'.$count, "=AVERAGE(B".$average->start.":B".$average->end.")");

                $count+=1;
                if($colorindex == 3)
                {
                    $colorindex = 0;
                }else{
                    $colorindex+=1;
                }
            }

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="Teaching Evalutation - '.$teacherinfo->lastname.', '.$teacherinfo->firstname.'.xlsx"');
            $writer->save("php://output");
        }


    }

    public static function evaluation_monitoring(Request $request){
        
        $syid = $request->get('syid');
        $semid = $request->get('semid');

        $sections = DB::table('sectiondetail')
                        ->join('sections',function($join){
                            $join->on('sectiondetail.sectionid','=','sections.id');
                            $join->where('sections.deleted',0);
                        })
                        ->join('gradelevel',function($join){
                            $join->on('sections.levelid','=','gradelevel.id');
                            $join->where('gradelevel.deleted',0);
                        })
                        ->where('sectiondetail.syid',$syid)
                        ->where('sectiondetail.deleted',0)
                        ->select(
                            'sortid',
                            'levelname',
                            'levelid',
                            'sectionid',
                            'sectionname'
                        )
                        ->get();

        $sectionstrand = DB::table('sh_sectionblockassignment')
                            ->join('sh_block',function($join){
                                $join->on('sh_sectionblockassignment.blockid','=','sh_block.id');
                                $join->where('sh_block.deleted',0);
                            })
                            ->where('syid',$syid)
                            ->where('sh_sectionblockassignment.deleted',0)
                            ->select(
                                'sectionid',
                                'strandid'
                            )
                            ->get();
                        
        // $subject_plot = DB::table('subject_plot')
        //                     ->where('syid',$syid)
        //                     ->where('deleted',0)
        //                     ->select(
        //                         'strandid',
        //                         'semid',
        //                         'subjid',
        //                         'levelid'
        //                     )
        //                     ->get();

        $sh_subjteachers = DB::table('sh_classsched')
                            ->where('deleted',0)
                            ->where('syid',$syid)
                            ->where('semid',$semid)
                            ->select(
                                'subjid',
                                'teacherid',
                                'sectionid'
                            )
                            ->get();

        $gshs_subjteachers = DB::table('assignsubj')
                    ->join('assignsubjdetail',function($join){
                        $join->on('assignsubj.id','=','assignsubjdetail.headerid');
                        $join->where('assignsubjdetail.deleted',0);
                    })
                    ->where('syid',$syid)
                    ->where('assignsubj.deleted',0)
                    ->select(
                        'sectionid',
                        'glevelid as levelid',
                        'teacherid'
                    )->get();


        $enrolledstudents = DB::table('enrolledstud')
                                ->where('syid',$syid)
                                ->where('deleted',0)
                                ->whereIn('studstatus',[1,2,4])
                                ->select(
                                    'sectionid',
                                    'levelid'
                                )
                                ->get();

        $sh_enrolledstudents = DB::table('sh_enrolledstud')
                                ->where('syid',$syid)
                                ->where('semid',$semid)
                                ->where('deleted',0)
                                ->whereIn('studstatus',[1,2,4])
                                ->select(
                                    'semid',
                                    'strandid',
                                    'sectionid',
                                    'levelid'
                                )
                                ->get();

        $student_header = DB::table('grading_system_student_header')
                            ->join('grading_system_student_evaluation',function($join){
                                $join->on('grading_system_student_header.id','=','grading_system_student_evaluation.studheader');
                                $join->where('grading_system_student_evaluation.deleted',0);
                            })
                            ->where('grading_system_student_header.syid',$syid)
                            ->where('grading_system_student_header.deleted',0)
                            ->groupBy('studheader')
                            ->select(
                                'grading_system_student_header.id',
                                'grading_system_student_header.sectionid',
                                'grading_system_student_header.subjid',
                                'grading_system_student_header.levelid',
                                'grading_system_student_header.studid',
                                'grading_system_student_header.teacherid',
                                'grading_system_student_evaluation.studheader',
                                'grading_system_student_evaluation.q1val',
                                'grading_system_student_evaluation.q2val'
                            )
                            ->get();

        $sh_teachers = DB::table('sh_classsched')
                            ->where('deleted',0)
                            ->where('syid',$syid)
                            ->where('semid',$semid)
                            ->select('teacherid')
                            ->distinct('teacherid')
                            ->get();

        foreach($sections as $item){

            if($item->levelid == 14 || $item->levelid == 15){
                $check_strand = collect($sectionstrand)->where('sectionid',$item->sectionid)->values();

                if(count($check_strand) == 1){
                    $item->multiple_strand = false;

                    if($semid == 1){
                        $item->evaluationcount = collect($student_header)->whereNotNull('q1val')
                                                    ->whereIn('teacherid',collect($sh_teachers)->pluck('teacherid'))
                                                    ->where('sectionid',$item->sectionid)
                                                    ->count();
                        
                    }else{
                        $item->evaluationcount = collect($student_header)
                                                        ->whereNotNull('q2val')
                                                        ->where('sectionid',$item->sectionid)
                                                        ->whereIn('teacherid',collect($sh_teachers)->pluck('teacherid'))
                                                        ->count();
                    }

                    $item->enrolledcount = collect($sh_enrolledstudents)->where('sectionid',$item->sectionid)->count();
                    $item->subjcount =  collect($sh_subjteachers)
                                            ->where('sectionid',$item->sectionid)
                                            ->count();

                   
                }else{

                    $item->multiple_strand = true;
                    $subjcount_holder = array();

                    foreach($check_strand as $schek_strand_item){

                        $temp_subjcount = collect($sh_subjteachers)
                                            ->where('sectionid',$item->sectionid)
                                            ->count();

                        $temp_enrolled = collect($sh_enrolledstudents)
                                            ->where('sectionid',$item->sectionid)
                                            ->count();

                        array_push($subjcount_holder,(object)[
                            'strand'=>$schek_strand_item->strandid,
                            'subjcount'=>$temp_subjcount,
                            'enrolledcount'=>$temp_enrolled
                        ]);

                        $item->subjcount = $subjcount_holder;
                    }
                }

            }else{

                if($semid == 1){
                    $item->evaluationcount = collect($student_header)->whereNotNull('q1val')->where('sectionid',$item->sectionid)->count();
                }else{
                    $item->evaluationcount = collect($student_header)->whereNotNull('q2val')->where('sectionid',$item->sectionid)->count();
                }
               
                $item->enrolledcount = collect($enrolledstudents)->where('sectionid',$item->sectionid)->count();
                $item->multiple_strand = false;

                $item->subjcount = collect($gshs_subjteachers)->where('sectionid',$item->sectionid)->unique('teacherid')->count();

            }


        }

        return $sections;
                    
    }

    //teacher evaluation setup

    public static function getTeacherEvlStp(Request $request){

        $teacherEvlStp = DB::table('grading_system_student_evlstp')
                            ->get();


        return $teacherEvlStp;

    }

    public static function updateTeacherEvlStp(Request $request){

        $evlStup = $request->get('evlStup');
        $evlStupStatus = $request->get('evlStupStatus');

        if($evlStupStatus == 0){
            DB::table('grading_system_student_evlstp')
                ->where('id',$evlStup)
                ->update([
                    'status'=>0,
                    'updatedby'=>auth()->user()->id,
                    'updateddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                ]);

        }else{

            DB::table('grading_system_student_evlstp')
                ->update([
                    'status'=>0,
                    'updatedby'=>auth()->user()->id,
                    'updateddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                ]);

            DB::table('grading_system_student_evlstp')
                ->where('id',$evlStup)
                ->update([
                    'status'=>1,
                    'updatedby'=>auth()->user()->id,
                    'updateddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                ]);
            
        }

        return array((object)[
            'status'=>1,
            'message'=>'Setup Updated'
        ]);


    }



}
