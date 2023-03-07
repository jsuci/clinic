<?php

namespace App\Http\Controllers\TeacherControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use PDF;
use App\Models\Grading\IndividualGrading;
use App\Models\Grading\GradingSystem;
use App\Models\Grading\GradeSchool;
use App\Models\Principal\SPP_Subject;
use App\Models\Grading\GradeStatus;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class MasterSheetController extends Controller
{

    public static function consolidated_pdf(Request $request){

        $activesem = $request->get('semid');
        $activesy = $request->get('sy');
        $gradelevel = $request->get('gradelevel');
        $section = $request->get('section');
        $quarter = $request->get('quarter');
        $strandid = $request->get('strand');
        $subjid = $request->get('subjid');

        if($gradelevel == 14 || $gradelevel == 15){
            $activesem = $request->get('semid');
            $subjects = \App\Http\Controllers\SuperAdminController\StudentGradeEvaluation::sf9_subjects_sh($gradelevel,$strandid,$activesem,$activesy);
            
        }else{
            $subjects = \App\Http\Controllers\SuperAdminController\StudentGradeEvaluation::sf9_subjects($gradelevel,$activesy);
        }

        $students = \App\Http\Controllers\TeacherControllers\TeacherGradingV4::get_student_data($request);
        $subjects = collect($subjects)->values();

        // return $students;

        $version = "V5";
        unset($students[0]);
        if($gradelevel == 14 || $gradelevel == 15){
            $students = collect($students)->where('strand',$strandid)->values();
        }
       
        $section_detail = DB::table('sections')
                        ->leftJoin('teacher',function($join){
                            $join->on('sections.teacherid','=','teacher.id');
                        })
                        ->join('gradelevel',function($join){
                            $join->on('sections.levelid','=','gradelevel.id');
                        })
                        ->where('sections.id',$section)
                        ->where('sections.deleted',0)
                        ->select('lastname','firstname','sectionname','levelname','levelid')
                        ->first();
                    
        $schoolyear_detail = DB::table('sy')->where('id',$activesy)->first();

        $schoolinfo = DB::table('schoolinfo')
                        ->leftJoin('refregion','schoolinfo.region','=','refregion.regCode')
                        ->leftJoin('refcitymun','schoolinfo.division','=','refcitymun.citymunCode')
                        ->select('schoolinfo.*','refregion.regDesc','refcitymun.citymunDesc')
                        ->get();

        // return $students;

        $pdf = PDF::loadView('teacher.grading.grade_summary.grade_summary_printable.grade_summary_con',compact('subjid','schoolinfo','students','subjects','quarter','section_detail','schoolyear_detail','activesem'))->setPaper('legal');
        $pdf->getDomPDF()->set_option("enable_php", true)->set_option("DOMPDF_ENABLE_CSS_FLOAT", true);
      
        return $pdf->stream('MSQ'.$quarter.str_replace("GRADE ","_G",$section_detail->levelname).str_replace(" ","_",$section_detail->sectionname).'_SY'.$schoolyear_detail->sydesc.'_'.\Carbon\Carbon::now('Asia/Manila')->isoFormat('YYYYMMDDThhMMSS').$version.'.pdf');
     

    }


    public static function excel_composite(Request $request){

        $activesem = $request->get('semid');
        $activesy = $request->get('sy');
        $gradelevel = $request->get('gradelevel');
        $section = $request->get('section');
        $quarter = $request->get('quarter');
        $strandid = $request->get('strand');

        

        if($gradelevel == 14 || $gradelevel == 15){
            $activesem = $request->get('semid');
            $subjects = \App\Http\Controllers\SuperAdminController\StudentGradeEvaluation::sf9_subjects_sh($gradelevel,$strandid,$activesem);
            
        }else{
            $subjects = \App\Http\Controllers\SuperAdminController\StudentGradeEvaluation::sf9_subjects($gradelevel);
        }
        $students = \App\Http\Controllers\TeacherControllers\TeacherGradingV4::get_student_data($request);

      

        $subjects = collect($subjects)->where('isVisible',1)->values();
        foreach($students as $item){
            $item->grades = collect($item->grades)->where('isVisible',1)->values();
        }

        unset($students[0]);
        if($gradelevel == 14 || $gradelevel == 15){
            $students = collect($students)->where('strand',$strandid)->values();
        }

        $section_detail = DB::table('sections')
                            ->leftJoin('teacher',function($join){
                                $join->on('sections.teacherid','=','teacher.id');
                            })
                            ->join('gradelevel',function($join){
                                $join->on('sections.levelid','=','gradelevel.id');
                            })
                            ->where('sections.id',$section)
                            ->where('sections.deleted',0)
                            ->select('lastname','firstname','sectionname','levelname','levelid')
                            ->first();
    
        $schoolyear_detail = DB::table('sy')->where('id',$activesy)->first();

        $schoolinfo = DB::table('schoolinfo')
                ->leftJoin('refregion','schoolinfo.region','=','refregion.regCode')
                ->leftJoin('refcitymun','schoolinfo.division','=','refcitymun.citymunCode')
                ->select('schoolinfo.*','refregion.regDesc','refcitymun.citymunDesc')
                ->first();

        $teacherinfo = Db::table('sectiondetail')
            ->select('teacher.*')
            ->leftJoin('teacher','sectiondetail.teacherid','=','teacher.id')
            ->where('sectiondetail.deleted', 0)
            ->where('sectionid', $section)
            ->where('syid', $activesy)
            ->first();
            
        if($teacherinfo)
        {
            $teachername = $teacherinfo->lastname.', '.$teacherinfo->firstname.' '.$teacherinfo->middlename[0].' '.$teacherinfo->suffix;
        }else{
            $teachername = "";
        }
        
        if($request->get('quarter') == 1)
        {
            $quartername = '1st Quarter';
        }
        elseif($request->get('quarter') == 2)
        {
            $quartername = '2nd Quarter';
        }
        elseif($request->get('quarter') == 3)
        {
            $quartername = '3rd Quarter';
        }
        elseif($request->get('quarter') == 4)
        {
            $quartername = '4th Quarter';
        }
        elseif($request->get('quarter') == 5)
        {
            $quartername = 'Final Grade';
        }

        $gradelevelinfo = Db::table('gradelevel')
            ->select('levelname','acadprogcode')
            ->join('academicprogram', 'gradelevel.acadprogid','=','academicprogram.id')
            ->where('gradelevel.id', $gradelevel)
            ->first();
        
        if($gradelevelinfo)
        {
            if(strtolower($gradelevelinfo->acadprogcode) == 'college')
            {
                $sectioninfo = Db::table('college_sections')
                    ->select('sectionDesc as sectionname')
                    ->where('id', $section)
                    ->first();
                    
            }else{
                $sectioninfo = Db::table('sections')
                    ->select('sectionname')
                    ->where('id', $section)
                    ->first();
            }
        }else{
            $sectioninfo = (object)array();
        }

        $schoolyearinfo = DB::table('sy')
            ->where('id', $activesy)
            ->first();
            

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();;
        
        $sheet = $spreadsheet->getActiveSheet();
        $borderstyle = [
            'borders' => [
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
                'left' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
                'right' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ]
        ];

        $border_1 = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];

        $font_bold = [
            'font' => [
                'bold' => true,
            ]
        ];

        $component = [
            'font' => [
                'bold' => false,
            ],
        ];

        $letterval = 65; // A
        $contanstantcellcount = 6;

        $numofsubjects = 0;
        
        foreach(range('C','Z') as $columnID) {
            $sheet->getColumnDimension('D')->setWidth(6);
        }

        foreach(range('AA','AZ') as $columnID) {
            $sheet->getColumnDimension('D')->setWidth(6);
        }

        // $last_column = chr(67 + count($subjects) + 2);
        // $quarter_holder = chr( ( 67 + count($subjects) + 2 ) - 5);

        // $sheet->mergeCells('A1:'.$last_column.'1');
        // $sheet->setCellValue('A1',$schoolinfo->schoolname);
        // $sheet->getStyle('A1')->applyFromArray($font_bold);

        // $sheet->mergeCells('A2:'.$last_column.'2');
        // $sheet->setCellValue('A2',$schoolinfo->address);

        // $sheet->mergeCells('A4:'.$last_column.'4');
        // $sheet->setCellValue('A4','M A S T E R   S H E E T S');
        // $sheet->getStyle('A4')->applyFromArray($font_bold);

        // $sheet->mergeCells('A5:'.$last_column.'5');
        // $sheet->setCellValue('A5',strtoupper($gradelevelinfo->levelname).' - '.strtoupper($sectioninfo->sectionname));
        // $sheet->getStyle('A5')->applyFromArray($font_bold);

        // $sheet->mergeCells('A6:'.$last_column.'6');
        // $sheet->setCellValue('A6',strtoupper('SCHOOL YEAR').' - '.$schoolyearinfo->sydesc);
            
        // $sheet->getStyle('A1:A6')->getAlignment()->setHorizontal('center');

        // $sheet->mergeCells('A8:B8');
        // $sheet->setCellValue('A8','TEACHER: '.$teachername);
 
        // $sheet->mergeCells($quarter_holder.'8:'.$last_column.'8');

        

        $cell_number = 10;
        $sheet->getStyle('C'.$cell_number.':'.'AZ'.$cell_number)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('C'.($cell_number+1).':'.'BZ'.($cell_number+1))->getAlignment()->setHorizontal('center');
        $sheet->getStyle('C'.($cell_number+2).':'.'BZ'.($cell_number+2))->getAlignment()->setHorizontal('center');

        if(count($subjects)>0)
        {
            $subjectletterval = 67; // F
            $lettervalmax = 64;
            $sheet->getStyle('A9')->applyFromArray($borderstyle);
            $sheet->getStyle('B9')->applyFromArray($borderstyle);
            $letter = '';

            foreach(collect($subjects)->sortBy('sortid')->values() as $subject)
            {
              
                if($subject->isVisible == 1){
                    if($gradelevel == 14 || $gradelevel == 15){
                        
                    }else{

                        if($request->get('quarter') == 5){
                            if($subject->subjCom == null){
                               
                            }
                        }else{
                            if( $subjectletterval < 90){
                                
                                $sheet->mergeCells($letter.chr($subjectletterval).$cell_number.':'.$letter.chr($subjectletterval+3).$cell_number);

                                $sheet->setCellValue($letter.chr($subjectletterval).$cell_number,$subject->subjcode);
                                
                                $sheet->mergeCells($letter.chr($subjectletterval).($cell_number+1).':'.$letter.chr($subjectletterval+3).($cell_number+1));
                                $sheet->setCellValue($letter.chr($subjectletterval).($cell_number+1),'Quarter');
                                $sheet->getStyle($letter.chr($subjectletterval).($cell_number+1).':'.$letter.chr($subjectletterval+3).($cell_number+1))->getAlignment()->setHorizontal('center');

                                $sheet->setCellValue($letter.chr($subjectletterval).($cell_number+2),'1');
                                $sheet->setCellValue($letter.chr($subjectletterval+1).($cell_number+2),'2');
                                $sheet->setCellValue($letter.chr($subjectletterval+2).($cell_number+2),'3');
                                $sheet->setCellValue($letter.chr($subjectletterval+3).($cell_number+2),'4');

                                if($subjectletterval+4 <= 90){
                                    $sheet->setCellValue($letter.chr($subjectletterval+4).($cell_number+2),'Grade');
                                    $sheet->setCellValue($letter.chr($subjectletterval+4).($cell_number+1),'Final');
                                    $sheet->getColumnDimension($letter.chr($subjectletterval+4))->setWidth(6);
                                }else{
                                    $sheet->setCellValue(chr($lettervalmax+1).chr($lettervalmax+1).($cell_number+2),'Grade');
                                    $sheet->setCellValue(chr($lettervalmax+1).chr($lettervalmax+1).($cell_number+1),'Final');
                                    $sheet->getColumnDimension(chr($lettervalmax+1).chr($lettervalmax+1))->setWidth(6);
                                }
                                $sheet->getColumnDimension($letter.chr($subjectletterval))->setWidth(4);
                                $sheet->getColumnDimension($letter.chr($subjectletterval+1))->setWidth(4);
                                $sheet->getColumnDimension($letter.chr($subjectletterval+2))->setWidth(4);
                                $sheet->getColumnDimension($letter.chr($subjectletterval+3))->setWidth(4);
                            }
                        }
                    }
                    
                }

                if( $subjectletterval + 3 >= 90){
                    $subjectletterval += 6;
                    $subjectletterval = 66;
                    $lettervalmax += 1;
                    $letter = chr($lettervalmax);
                    // return $letter;
                }else{
                    $subjectletterval += 5;
                }
                
            }

        }

        $last_column = 'C';
        $cell_number += 4;
        $male = 0;
        $female = 0;
        $count = 1;

        $sheet->getColumnDimension('A')->setWidth(4);
        $sheet->getColumnDimension('B')->setAutoSize(true);


        foreach(collect($students)->where('student','!=','SUBJECTS')->values() as $student)
        {
            if($male == 0 && strtoupper($student->gender) == 'MALE'){
                $sheet->getStyle('A'.$cell_number)->applyFromArray($borderstyle);
                $sheet->getStyle('B'.$cell_number.':AZ'.$cell_number)->applyFromArray($borderstyle);
                $sheet->mergeCells('B'.$cell_number.':AZ'.$cell_number);
                $sheet->setCellValue('A'.$cell_number,'#');
                $sheet->getStyle('A'.$cell_number)->getAlignment()->setHorizontal('center');
                $sheet->setCellValue('B'.$cell_number,'MALE');
                $cell_number += 1;
                $male = 1;
            }

            if($female == 0 && strtoupper($student->gender) == 'FEMALE'){
                $sheet->getStyle('A'.$cell_number)->applyFromArray($borderstyle);
                $sheet->mergeCells('B'.$cell_number.':AZ'.$cell_number);
                $sheet->getStyle('B'.$cell_number.':AZ'.$cell_number)->applyFromArray($borderstyle);
                $sheet->setCellValue('A'.$cell_number,'#');
                $sheet->getStyle('A'.$cell_number)->getAlignment()->setHorizontal('center');
                $sheet->setCellValue('B'.$cell_number,'FEMALE');
                $cell_number += 1;
                $female = 1;
                $count = 1;
            }

            $sheet->setCellValue('A'.$cell_number,$count);
            $sheet->setCellValue('B'.$cell_number,$student->student);
            $sheet->getStyle('A'.$cell_number)->applyFromArray($border_1);
            $sheet->getStyle('B'.$cell_number)->applyFromArray($border_1);
            $subjectletterval = 67; // F
            $letter = '';

            foreach(collect($student->grades)->where('id','!=','G1')->sortBy('sortid')->values() as $stud_grades){

                $sheet->getStyle('C'.$cell_number.':AZ'.$cell_number)->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A'.$cell_number.':AZ'.$cell_number)->applyFromArray($border_1);

                $sheet->getStyle('A'.$cell_number)->getAlignment()->setHorizontal('center');

                if($stud_grades->subjid != null){
                    if($gradelevel == 14 || $gradelevel == 15){
                        $sheet->setCellValue($letter.chr($subjectletterval).$cell_number,$stud_grades->q1);
                        $sheet->setCellValue($letter.chr($subjectletterval+1).$cell_number,$stud_grades->q2);
                        $sheet->setCellValue($letter.chr($subjectletterval+2).$cell_number,$stud_grades->q3);
                        $sheet->setCellValue($letter.chr($subjectletterval+3).$cell_number,$stud_grades->q4);

                        // $sheet->getStyle($letter.chr($subjectletterval).$cell_number)->getAlignment()->setHorizontal('center');
                        // $sheet->getStyle($letter.chr($subjectletterval+1).$cell_number)->getAlignment()->setHorizontal('center');
                        // $sheet->getStyle($letter.chr($subjectletterval+2).$cell_number)->getAlignment()->setHorizontal('center');
                        // $sheet->getStyle($letter.chr($subjectletterval+3).$cell_number)->getAlignment()->setHorizontal('center');

                        // $sheet->getStyle($letter.chr($subjectletterval).$cell_number)->applyFromArray($border_1);
                        // $sheet->getStyle($letter.chr($subjectletterval+1).$cell_number)->applyFromArray($border_1);
                        // $sheet->getStyle($letter.chr($subjectletterval+2).$cell_number)->applyFromArray($border_1);
                        // $sheet->getStyle($letter.chr($subjectletterval+3).$cell_number)->applyFromArray($border_1);

                        if($subjectletterval+4 <= 90){
                            $sheet->getColumnDimension($letter.chr($subjectletterval+4))->setWidth(6);
                            $sheet->setCellValue($letter.chr($subjectletterval+4).($cell_number),$stud_grades->qg);
                            // $sheet->getStyle($letter.chr($subjectletterval+4).($cell_number))->applyFromArray($border_1);
                        }else{
                            $sheet->setCellValue('AA'.$cell_number,$stud_grades->qg);
                            // $sheet->getStyle('AA')->applyFromArray($border_1);
                        }
                    }else{
                        if($request->get('quarter') == 5){
                            $sheet->setCellValue($letter.chr($subjectletterval).$cell_number,$stud_grades->q1);
                            $sheet->setCellValue($letter.chr($subjectletterval+1).$cell_number,$stud_grades->q2);
                            $sheet->setCellValue($letter.chr($subjectletterval+2).$cell_number,$stud_grades->q3);
                            $sheet->setCellValue($letter.chr($subjectletterval+3).$cell_number,$stud_grades->q4);

                            // $sheet->getStyle($letter.chr($subjectletterval).$cell_number)->getAlignment()->setHorizontal('center');
                            // $sheet->getStyle($letter.chr($subjectletterval+1).$cell_number)->getAlignment()->setHorizontal('center');
                            // $sheet->getStyle($letter.chr($subjectletterval+2).$cell_number)->getAlignment()->setHorizontal('center');
                            // $sheet->getStyle($letter.chr($subjectletterval+3).$cell_number)->getAlignment()->setHorizontal('center');

                            // $sheet->getStyle($letter.chr($subjectletterval).$cell_number)->applyFromArray($border_1);
                            // $sheet->getStyle($letter.chr($subjectletterval+1).$cell_number)->applyFromArray($border_1);
                            // $sheet->getStyle($letter.chr($subjectletterval+2).$cell_number)->applyFromArray($border_1);
                            // $sheet->getStyle($letter.chr($subjectletterval+3).$cell_number)->applyFromArray($border_1);

                            if($subjectletterval+4 <= 90){
                                $sheet->getColumnDimension($letter.chr($subjectletterval+4))->setWidth(6);
                                $sheet->setCellValue($letter.chr($subjectletterval+4).($cell_number),$stud_grades->qg);
                                // $sheet->getStyle($letter.chr($subjectletterval+4).($cell_number))->applyFromArray($border_1);
                            }else{
                                $sheet->setCellValue('AA'.$cell_number,$stud_grades->qg);
                                $sheet->getStyle('AA')->applyFromArray($border_1);
                            }
                        }else{
                           if( $subjectletterval < 90){
                                $sheet->setCellValue($letter.chr($subjectletterval).$cell_number,$stud_grades->q1);
                                $sheet->setCellValue($letter.chr($subjectletterval+1).$cell_number,$stud_grades->q2);
                                $sheet->setCellValue($letter.chr($subjectletterval+2).$cell_number,$stud_grades->q3);
                                $sheet->setCellValue($letter.chr($subjectletterval+3).$cell_number,$stud_grades->q4);

                                // $sheet->getStyle($letter.chr($subjectletterval).$cell_number)->getAlignment()->setHorizontal('center');
                                // $sheet->getStyle($letter.chr($subjectletterval+1).$cell_number)->getAlignment()->setHorizontal('center');
                                // $sheet->getStyle($letter.chr($subjectletterval+2).$cell_number)->getAlignment()->setHorizontal('center');
                                // $sheet->getStyle($letter.chr($subjectletterval+3).$cell_number)->getAlignment()->setHorizontal('center');

                                // $sheet->getStyle($letter.chr($subjectletterval).$cell_number)->applyFromArray($border_1);
                                // $sheet->getStyle($letter.chr($subjectletterval+1).$cell_number)->applyFromArray($border_1);
                                // $sheet->getStyle($letter.chr($subjectletterval+2).$cell_number)->applyFromArray($border_1);
                                // $sheet->getStyle($letter.chr($subjectletterval+3).$cell_number)->applyFromArray($border_1);

                                if($subjectletterval+4 <= 90){
                                    $sheet->getColumnDimension($letter.chr($subjectletterval+4))->setWidth(6);
                                    $sheet->setCellValue($letter.chr($subjectletterval+4).($cell_number),$stud_grades->qg);
                                    // $sheet->getStyle($letter.chr($subjectletterval+4).($cell_number))->applyFromArray($border_1);
                                }else{
                                    $sheet->setCellValue('AA'.$cell_number,$stud_grades->qg);
                                    // $sheet->getStyle('AA')->applyFromArray($border_1);
                                }
                            }
                        }
                    }
                }

                if( $subjectletterval + 3 >= 90){
                    $subjectletterval += 6;
                    $subjectletterval = 66;
                    $letter = 'A';
                }else{
                    $subjectletterval += 5;
                }
            }
            
            // $sheet->getStyle(chr($subjectletterval).$cell_number)->applyFromArray($borderstyle);
            
            // if($quarter != 5){
            //     $subjectletterval+=1;
            // }else{
            //     foreach(collect($subjects)->where('subjCom','!=',null)->values() as $item){
            //         $sheet->getStyle(chr($subjectletterval).$cell_number)->applyFromArray($borderstyle);
            //         $subjectletterval+=1;
            //     }
            //     $sheet->getStyle(chr($subjectletterval).$cell_number)->applyFromArray($borderstyle);
            //     $subjectletterval+=1;
            // }
            
            // if($quarter != 5){
            //     $temp_qg = 'q'.$quarter;
            //     $sheet->setCellValue(chr($subjectletterval).$cell_number,collect($student->grades)->where('id','G1')->first()->$temp_qg);
            // }else{
            //     $sheet->setCellValue(chr($subjectletterval).$cell_number,collect($student->grades)->where('id','G1')->first()->finalrating);
            // }
            
            // $sheet->getColumnDimension(chr($subjectletterval))->setAutoSize(false);
            // $sheet->getStyle(chr($subjectletterval).$cell_number)->applyFromArray($borderstyle);
            // $sheet->getStyle(chr($subjectletterval).$cell_number)->getAlignment()->setHorizontal('center');
            // $subjectletterval+=1;
            
            // if($quarter != 5){
            //     $temp_qgcomp = 'q'.$quarter.'comp';
            //     $sheet->setCellValue(chr($subjectletterval).$cell_number,collect($student->grades)->where('id','G1')->first()->$temp_qgcomp);
            // }else{
            //     $sheet->setCellValue(chr($subjectletterval).$cell_number,collect($student->grades)->where('id','G1')->first()->fcomp);
            // }
            
            // $sheet->getColumnDimension(chr($subjectletterval))->setAutoSize(false);
            // $sheet->getStyle(chr($subjectletterval).$cell_number)->applyFromArray($borderstyle);
            // $sheet->getStyle(chr($subjectletterval).$cell_number)->getAlignment()->setHorizontal('center');
        
            $subjectletterval += 5;
        
            $cell_number+=1;
            $count += 1;
        }
        

        $version = "V5";

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="MSQ"'.$quarter.str_replace("GRADE ","_G",$section_detail->levelname).str_replace(" ","_",$section_detail->sectionname).'_SY'.$schoolyear_detail->sydesc.'_'.\Carbon\Carbon::now('Asia/Manila')->isoFormat('YYYYMMDDThhMMSS').$version.'.xlsx"');
        $writer->save("php://output");


    }

    public static function excel_mastersheet(Request $request){

        $activesem = $request->get('semid');
        $activesy = $request->get('sy');
        $gradelevel = $request->get('gradelevel');
        $section = $request->get('section');
        $quarter = $request->get('quarter');
        $strandid = $request->get('strand');
        $strandcode = '';

        if($gradelevel == 14 || $gradelevel == 15){
            $activesem = $request->get('semid');
            $subjects = \App\Http\Controllers\SuperAdminController\StudentGradeEvaluation::sf9_subjects_sh($gradelevel,$strandid,$activesem,$activesy);
            
            $strandinfo = DB::table('sh_strand')
                            ->where('id',$strandid)
                            ->select('strandcode')
                            ->first();

            if(isset($strandinfo->strandcode)){
                $strandcode = ' ( '.$strandinfo->strandcode.' )';
            }

        }else{
            $activesem = 1;
            $subjects = \App\Http\Controllers\SuperAdminController\StudentGradeEvaluation::sf9_subjects($gradelevel,$activesy);
        }
        $students = \App\Http\Controllers\TeacherControllers\TeacherGradingV4::get_student_data($request);
      

        unset($students[0]);
        if($gradelevel == 14 || $gradelevel == 15){
            $students = collect($students)->where('strand',$strandid)->values();
        }

        $section_detail = DB::table('sections')
                            ->leftJoin('teacher',function($join){
                                $join->on('sections.teacherid','=','teacher.id');
                            })
                            ->join('gradelevel',function($join){
                                $join->on('sections.levelid','=','gradelevel.id');
                            })
                            ->where('sections.id',$section)
                            ->where('sections.deleted',0)
                            ->select('lastname','firstname','sectionname','levelname','levelid')
                            ->first();
    
        $schoolyear_detail = DB::table('sy')->where('id',$activesy)->first();

        $schoolinfo = DB::table('schoolinfo')
                ->leftJoin('refregion','schoolinfo.region','=','refregion.regCode')
                ->leftJoin('refcitymun','schoolinfo.division','=','refcitymun.citymunCode')
                ->select('schoolinfo.*','refregion.regDesc','refcitymun.citymunDesc')
                ->first();

        $teacherinfo = Db::table('sectiondetail')
            ->select('teacher.*')
            ->leftJoin('teacher','sectiondetail.teacherid','=','teacher.id')
            ->where('sectiondetail.deleted', 0)
            ->where('sectionid', $section)
            ->where('syid', $activesy)
            ->first();
            
        if($teacherinfo)
        {
            $teachername = $teacherinfo->lastname.', '.$teacherinfo->firstname.' '.$teacherinfo->middlename[0].' '.$teacherinfo->suffix;
        }else{
            $teachername = "";
        }
        
        if($request->get('quarter') == 1)
        {
            $quartername = '1st Quarter';
        }
        elseif($request->get('quarter') == 2)
        {
            $quartername = '2nd Quarter';
        }
        elseif($request->get('quarter') == 3)
        {
            $quartername = '3rd Quarter';
        }
        elseif($request->get('quarter') == 4)
        {
            $quartername = '4th Quarter';
        }
        elseif($request->get('quarter') == 5)
        {
            $quartername = 'Final Grade';
        }

        $gradelevelinfo = Db::table('gradelevel')
            ->select('levelname','acadprogcode')
            ->join('academicprogram', 'gradelevel.acadprogid','=','academicprogram.id')
            ->where('gradelevel.id', $gradelevel)
            ->first();
        
        if($gradelevelinfo)
        {
            if(strtolower($gradelevelinfo->acadprogcode) == 'college')
            {
                $sectioninfo = Db::table('college_sections')
                    ->select('sectionDesc as sectionname')
                    ->where('id', $section)
                    ->first();
                    
            }else{
                $sectioninfo = Db::table('sections')
                    ->select('sectionname')
                    ->where('id', $section)
                    ->first();
            }
        }else{
            $sectioninfo = (object)array();
        }

        $schoolyearinfo = DB::table('sy')
            ->where('id', $activesy)
            ->first();
            
			 $format = $request->get('format');

        if($format == 2){

            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $spreadsheet = $reader->load("GRADE MASTER SHEET/GRADE_MASTER_SHEET_HCHS.xlsx");

            $sheet = $spreadsheet->setActiveSheetIndex(0);

            $first_char = 72;
            foreach(collect($subjects)->sortBy('sortid')->values() as $subject)
            {
                $sheet->setCellValue(chr($first_char).'9',$subject->subjdesc);
                $sheet->setCellValue(chr($first_char).'59',$subject->subjdesc);
                $first_char += 1;
            }

            $quarterlabel = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
            $quarterlabel->createText('Quarter: ');
            $quartertext = $quarterlabel->createTextRun($quartername);
            $quartertext->getFont()->setBold(true);
            $quartertext->getFont()->setUnderline(true);
            $quartertext->getFont()->setName('Times New Roman');

            $sheet->setCellValue('F6',$quarterlabel);
            $sheet->setCellValue('F56',$quarterlabel);

            $sectionlabel = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
            $sectionlabel->createText('Grade & Section: ');
            $sectiontext = $sectionlabel->createTextRun($section_detail->levelname.' - '.$section_detail->sectionname);
            $sectiontext->getFont()->setBold(true);
            $sectiontext->getFont()->setUnderline(true);
            $sectiontext->getFont()->setName('Times New Roman');

            $sheet->setCellValue('L6',$sectionlabel);
            $sheet->setCellValue('L56',$sectionlabel);

            $teachernamelabel = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
            $teachernamelabel->createText('Adviser: ');
            $teachernametext = $teachernamelabel->createTextRun($teachername);
            $teachernametext->getFont()->setBold(true);
            $teachernametext->getFont()->setUnderline(true);
            $teachernametext->getFont()->setName('Times New Roman');

            $sheet->setCellValue('Q6',$teachernamelabel);
            $sheet->setCellValue('Q56',$teachernamelabel);

            $sydesclabel = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
            $sydesclabel->createText('School Year: ');
            $sydesctext = $sydesclabel->createTextRun($schoolyearinfo->sydesc);
            $sydesctext->getFont()->setBold(true);
            $sydesctext->getFont()->setUnderline(true);
            $sydesctext->getFont()->setName('Times New Roman');

            $sheet->setCellValue('Q5',$sydesclabel);
            $sheet->setCellValue('Q55',$sydesclabel);

            $male_row = 9;
            $female_row = 59;

            foreach(collect($students)->where('student','!=','SUBJECTS')->values() as $student)
            {
                $row = 0;
                if($student->gender == 'MALE' ){
                    $male_row += 1;
                    $row = $male_row;
                }else{
                    $female_row += 1;
                    $row = $female_row;
                }

                $studgrades = $student->grades;
                $temp_qg = 'q'.$quarter;
                $first_char = 72;
                $formula = '= (';


                foreach($subjects as $subjitem){

                    $subjgrades = collect($studgrades)->where('subjid',$subjitem->subjid)->first();
                 
                    if(isset($subjgrades)){
                        $sheet->setCellValue(chr($first_char).$row,$subjgrades->$temp_qg);
                    }

                   

                    if($subjitem->inSF9 == 1 && $subjitem->subjCom == null){
                        $formula .= chr($first_char).$row.' + ';
                    }

                    $first_char += 1;

                }

                $formula = substr($formula, 0, -2) .')/'.collect($subjects)->where('inSF9',1)->where('subjCom',null)->count();

                $sheet->setCellValue('C'.$row,$student->student);
                $sheet->setCellValue('U'.$row,$formula);
               
            }

            $version = "V5";
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="MSQ"'.$quarter.str_replace("GRADE ","_G",$section_detail->levelname).str_replace(" ","_",$section_detail->sectionname).'_SY'.$schoolyear_detail->sydesc.'_'.\Carbon\Carbon::now('Asia/Manila')->isoFormat('YYYYMMDDThhMMSS').$version.'.xlsx"');
            $writer->save("php://output");
            exit();
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();;
        
        $sheet = $spreadsheet->getActiveSheet();
        $borderstyle = [
            'borders' => [
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
                'left' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
                'right' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ]
        ];
        $font_bold = [
            'font' => [
                'bold' => true,
            ]
        ];

        $letterval = 65; // A
        $contanstantcellcount = 6;

        $numofsubjects = 0;
        
        foreach(range('A',chr($letterval+$contanstantcellcount)) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $last_column = chr(67 + count($subjects) + 2);
        if(count($subjects) > 0){
            $quarter_holder = chr( ( 67 + count($subjects) + 2 ) - 5);
        }else{
            $quarter_holder = 'C';
        }
        

        $sheet->mergeCells('A1:'.$last_column.'1');
        $sheet->setCellValue('A1',$schoolinfo->schoolname);
        $sheet->getStyle('A1')->applyFromArray($font_bold);

        $sheet->mergeCells('A2:'.$last_column.'2');
        $sheet->setCellValue('A2',$schoolinfo->address);

        $sheet->mergeCells('A4:'.$last_column.'4');
        $sheet->setCellValue('A4','M A S T E R   S H E E T S');
        $sheet->getStyle('A4')->applyFromArray($font_bold);

        $sheet->mergeCells('A5:'.$last_column.'5');
        $sheet->setCellValue('A5',strtoupper($gradelevelinfo->levelname).' - '.strtoupper($sectioninfo->sectionname).$strandcode);
        $sheet->getStyle('A5')->applyFromArray($font_bold);

        $sheet->mergeCells('A6:'.$last_column.'6');
        $sheet->setCellValue('A6',strtoupper('SCHOOL YEAR').' - '.$schoolyearinfo->sydesc);
            
        $sheet->getStyle('A1:A6')->getAlignment()->setHorizontal('center');

        $sheet->mergeCells('A8:B8');
        $sheet->setCellValue('A8','TEACHER: '.$teachername);

        $sheet->mergeCells($quarter_holder.'8:'.$last_column.'8');
       
        $sheet->setCellValue($quarter_holder.'8','GRADING PERIOD: '.$quartername);
    
            $countstudmale = 1;
            $startcellno = 10;
            $numofsubjectswithgrades= 0;
           
            if(count($subjects)>0)
            {
                $subjectletterval = 67; // F
                $sheet->getStyle('A9')->applyFromArray($borderstyle);
                $sheet->getStyle('B9')->applyFromArray($borderstyle);

                foreach(collect($subjects)->sortBy('sortid')->values() as $subject)
                {

                    //if($subject->isVisible == 1){
                        if($gradelevel == 14 || $gradelevel == 15){
                            $sheet->getStyle(chr($subjectletterval).'9')->applyFromArray($font_bold);
                            $sheet->getStyle(chr($subjectletterval).'9')->getAlignment()->setTextRotation(90);
                            $sheet->setCellValue(chr($subjectletterval).'9',$subject->subjdesc);
                            $sheet->getStyle(chr($subjectletterval).'9')->applyFromArray($borderstyle);
                            $sheet->getColumnDimension(chr($subjectletterval))->setAutoSize(false);
                            $sheet->getColumnDimension(chr($subjectletterval))->setWidth(8);
                            $sheet->getStyle(chr($subjectletterval).'9')->getAlignment()->setWrapText(true);
                            $sheet->getStyle(chr($subjectletterval).'9')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                            $sheet->getRowDimension(9)->setRowHeight(200);
                            $subjectletterval+=1;
                        }else{
                            if($request->get('quarter') == 5){
                                if($subject->subjCom == null){
                                     $sheet->getStyle(chr($subjectletterval).'9')->applyFromArray($font_bold);
                                    $sheet->getStyle(chr($subjectletterval).'9')->getAlignment()->setTextRotation(90);
                                    $sheet->setCellValue(chr($subjectletterval).'9',$subject->subjdesc);
                                    $sheet->getStyle(chr($subjectletterval).'9')->applyFromArray($borderstyle);
                                    $sheet->getColumnDimension(chr($subjectletterval))->setAutoSize(false);
                                    $sheet->getColumnDimension(chr($subjectletterval))->setWidth(5);
                                    $sheet->getColumnDimension('E')->setWidth(5);
                                    $subjectletterval+=1;
                                }
                            }else{
                                if($subject->subjCom == null){
                                    $sheet->getStyle(chr($subjectletterval).'9')->applyFromArray($font_bold);
                                    $sheet->setCellValue(chr($subjectletterval).'9',$subject->subjdesc);
                                }else{
                                    $sheet->setCellValue(chr($subjectletterval).'9','     '.$subject->subjdesc);
                                }
                                $sheet->getStyle(chr($subjectletterval).'9')->getAlignment()->setTextRotation(90);
                                
                                $sheet->getStyle(chr($subjectletterval).'9')->applyFromArray($borderstyle);
                                $sheet->getColumnDimension(chr($subjectletterval))->setAutoSize(false);
                                $sheet->getColumnDimension(chr($subjectletterval))->setWidth(5);
                                $sheet->getColumnDimension('E')->setWidth(5);
                                $subjectletterval+=1;
                            }
                        }
                     
                    //}
                    
                }
                
                if($quarter != 5){
                    $sheet->getStyle(chr($subjectletterval).'9')->applyFromArray($borderstyle);
                    $sheet->getColumnDimension(chr($subjectletterval))->setAutoSize(false);
                    $sheet->getColumnDimension(chr($subjectletterval))->setWidth(5);
                    $subjectletterval+=1;
                }else{
                    foreach(collect($subjects)->where('subjCom','!=',null)->values() as $item){
                        $sheet->getStyle(chr($subjectletterval).'9')->applyFromArray($borderstyle);
                        $sheet->getColumnDimension(chr($subjectletterval))->setAutoSize(false);
                        $sheet->getColumnDimension(chr($subjectletterval))->setWidth(5);
                        $subjectletterval+=1;
                    }
                    $sheet->getStyle(chr($subjectletterval).'9')->applyFromArray($borderstyle);
                    $sheet->getColumnDimension(chr($subjectletterval))->setAutoSize(false);
                    $sheet->getColumnDimension(chr($subjectletterval))->setWidth(5);
                    $subjectletterval+=1;
                }
                
                $sheet->setCellValue(chr($subjectletterval).'9','AVERAGE');
                $sheet->getStyle(chr($subjectletterval).'9')->applyFromArray($font_bold);
                $sheet->getStyle(chr($subjectletterval).'9')->applyFromArray($borderstyle);
                $sheet->getStyle(chr($subjectletterval).'9')->getAlignment()->setTextRotation(90);
                $sheet->getColumnDimension(chr($subjectletterval))->setAutoSize(false);
                $sheet->getColumnDimension(chr($subjectletterval))->setWidth(5);
                $subjectletterval+=1;
                $sheet->setCellValue(chr($subjectletterval).'9','COMPOSITE');
                $sheet->getStyle(chr($subjectletterval).'9')->applyFromArray($font_bold);
                $sheet->getStyle(chr($subjectletterval).'9')->applyFromArray($borderstyle);
                $sheet->getStyle(chr($subjectletterval).'9')->getAlignment()->setTextRotation(90);
                $sheet->getColumnDimension(chr($subjectletterval))->setAutoSize(false);
                $sheet->getColumnDimension(chr($subjectletterval))->setWidth(5);
            }

            $cell_number = 10;
            $male = 0;
            $female = 0;
          
            $count = 1;
            foreach(collect($students)->where('student','!=','SUBJECTS')->values() as $student)
            {
                if($male == 0 && strtoupper($student->gender) == 'MALE'){
                    $sheet->getStyle('A'.$cell_number)->applyFromArray($borderstyle);
                    $sheet->getStyle('B'.$cell_number)->applyFromArray($borderstyle);
                    $sheet->getStyle('C'.$cell_number.':'.$last_column.$cell_number)->applyFromArray($borderstyle);
                    $sheet->setCellValue('A'.$cell_number,'#');
                    $sheet->getStyle('A'.$cell_number)->getAlignment()->setHorizontal('center');
                    $sheet->setCellValue('B'.$cell_number,'MALE');
                    $cell_number += 1;
                    $male = 1;
                }

                if($female == 0 && strtoupper($student->gender) == 'FEMALE'){
                    $sheet->getStyle('A'.$cell_number)->applyFromArray($borderstyle);
                    $sheet->getStyle('B'.$cell_number)->applyFromArray($borderstyle);
                    $sheet->getStyle('C'.$cell_number.':'.$last_column.$cell_number)->applyFromArray($borderstyle);
                    $sheet->getStyle('A'.$cell_number)->getAlignment()->setHorizontal('center');
                    $sheet->setCellValue('A'.$cell_number,'#');
                    $sheet->setCellValue('B'.$cell_number,'FEMALE');
                    $cell_number += 1;
                    $female = 1;
                    $count = 1;
                }

                $sheet->setCellValue('A'.$cell_number,$count);
                $sheet->setCellValue('B'.$cell_number,$student->student);
                $sheet->getStyle('A'.$cell_number)->applyFromArray($borderstyle);
                $sheet->getStyle('B'.$cell_number)->applyFromArray($borderstyle);
                $subjectletterval = 67; // F
                foreach(collect($student->grades)->where('id','!=','G1')->sortBy('sortid')->values() as $stud_grades){
                    $sheet->getStyle(chr($subjectletterval).$cell_number)->applyFromArray($borderstyle);
                    if($stud_grades->subjid != null){
                        if($gradelevel == 14 || $gradelevel == 15){
                            if(isset($stud_grades->qg)){
                                $sheet->setCellValue(chr($subjectletterval).$cell_number,$stud_grades->qg);
                            }
                            $sheet->getColumnDimension(chr($subjectletterval))->setAutoSize(false);
                            $sheet->getStyle(chr($subjectletterval).$cell_number)->getAlignment()->setHorizontal('center');
                            // $sheet->getColumnDimension('E')->setWidth(5);
                            $subjectletterval+=1;
                        }else{
                            if($request->get('quarter') == 5){
                                if($stud_grades->subjCom == null){
                                    if(isset($stud_grades->qg)){
                                        $sheet->setCellValue(chr($subjectletterval).$cell_number,$stud_grades->qg);
                                    }
                                    $sheet->getColumnDimension(chr($subjectletterval))->setAutoSize(false);
                                    $sheet->getStyle(chr($subjectletterval).$cell_number)->getAlignment()->setHorizontal('center');
                                    $sheet->getColumnDimension('E')->setWidth(5);
                                    $subjectletterval+=1;
                                }
                            }else{
                                if(isset($stud_grades->qg)){
                                    $sheet->setCellValue(chr($subjectletterval).$cell_number,$stud_grades->qg);
                                }
                                $sheet->getColumnDimension(chr($subjectletterval))->setAutoSize(false);
                                $sheet->getStyle(chr($subjectletterval).$cell_number)->getAlignment()->setHorizontal('center');
                                $sheet->getColumnDimension('E')->setWidth(5);
                                $subjectletterval+=1;
                            }
                        }
                       
                            
                    }
                }
                
                $sheet->getStyle(chr($subjectletterval).$cell_number)->applyFromArray($borderstyle);
                
                if($quarter != 5){
                    $subjectletterval+=1;
                }else{
                    foreach(collect($subjects)->where('subjCom','!=',null)->values() as $item){
                        $sheet->getStyle(chr($subjectletterval).$cell_number)->applyFromArray($borderstyle);
                        $subjectletterval+=1;
                    }
                    $sheet->getStyle(chr($subjectletterval).$cell_number)->applyFromArray($borderstyle);
                    $subjectletterval+=1;
                }
                
                if($quarter != 5){
                    $temp_qg = 'q'.$quarter;
                    $sheet->setCellValue(chr($subjectletterval).$cell_number,collect($student->grades)->where('id','G1')->first()->$temp_qg);
                }else{
                    $sheet->setCellValue(chr($subjectletterval-1).$cell_number,collect($student->grades)->where('id','G1')->first()->finalrating);
                    $sheet->setCellValue(chr($subjectletterval).$cell_number,collect($student->grades)->where('id','G1')->first()->fcomp);
                }
                
                $sheet->getColumnDimension(chr($subjectletterval))->setAutoSize(false);
                $sheet->getStyle(chr($subjectletterval).$cell_number)->applyFromArray($borderstyle);
                $sheet->getStyle(chr($subjectletterval).$cell_number)->getAlignment()->setHorizontal('center');
                $subjectletterval+=1;
                
                if($quarter != 5){
                   
                    $temp_qgcomp = 'q'.$quarter.'comp';
                    $temp_qg = 'q'.$quarter;
                    
                      if($gradelevel == 14 || $gradelevel == 15){
                            $sheet->setCellValue(chr($subjectletterval-1).$cell_number,collect($student->grades)->where('id','G1')->where('semid',$activesem)->first()->$temp_qg);
                            $sheet->setCellValue(chr($subjectletterval).$cell_number,collect($student->grades)->where('id','G1')->where('semid',$activesem)->first()->$temp_qgcomp);
                      }else{
                          $sheet->setCellValue(chr($subjectletterval-1).$cell_number,collect($student->grades)->where('id','G1')->first()->$temp_qg);
                          $sheet->setCellValue(chr($subjectletterval).$cell_number,collect($student->grades)->where('id','G1')->first()->$temp_qgcomp);
                      }
                  
                }else{
                     if($gradelevel == 14 || $gradelevel == 15){
                            $sheet->setCellValue(chr($subjectletterval-1).$cell_number,collect($student->grades)->where('id','G1')->first()->finalrating);
                            $sheet->setCellValue(chr($subjectletterval).$cell_number,collect($student->grades)->where('id','G1')->first()->fcomp);
                      }else{
                            $sheet->setCellValue(chr($subjectletterval-1).$cell_number,collect($student->grades)->where('id','G1')->first()->finalrating);
                            $sheet->setCellValue(chr($subjectletterval).$cell_number,collect($student->grades)->where('id','G1')->first()->fcomp);
                      }
                  
                }
                
                $sheet->getColumnDimension(chr($subjectletterval))->setAutoSize(false);
                $sheet->getStyle(chr($subjectletterval).$cell_number)->applyFromArray($borderstyle);
                $sheet->getStyle(chr($subjectletterval).$cell_number)->getAlignment()->setHorizontal('center');
            
                $subjectletterval += 5;
            
                $cell_number+=1;
                $count += 1;
            }

            $version = "V5";
            
            if(count($students) == 0){
                $spreadsheet->getActiveSheet()->getPageSetup()->setPrintArea('A1:'.'E'.$cell_number);
            }else{
                $spreadsheet->getActiveSheet()->getPageSetup()->setPrintArea('A1:'.chr($subjectletterval-5).$cell_number);
            }
           

            $spreadsheet->getActiveSheet()->getPageMargins()->setTop(0);
            $spreadsheet->getActiveSheet()->getPageMargins()->setRight(0);
            $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0);
            $spreadsheet->getActiveSheet()->getPageMargins()->setBottom(0);

            $spreadsheet->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
            $spreadsheet->getActiveSheet()->getPageSetup()->setVerticalCentered(false);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToPage(true);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToHeight(0);
            $spreadsheet->getActiveSheet()->getSheetView()->setZoomScale(85);
            
             
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="MSQ"'.$quarter.str_replace("GRADE ","_G",$section_detail->levelname).str_replace(" ","_",$section_detail->sectionname).'_SY'.$schoolyear_detail->sydesc.'_'.\Carbon\Carbon::now('Asia/Manila')->isoFormat('YYYYMMDDThhMMSS').$version.'.xlsx"');
            $writer->save("php://output");
    
    }

    public function mastersheet(Request $request)
    {

        $activesem = $request->get('semid');
        $activesy = $request->get('sy');
        $gradelevel = $request->get('gradelevel');
        $section = $request->get('section');
        $quarter = $request->get('quarter');
        $strandid = $request->get('strand');

        if($gradelevel == 14 || $gradelevel == 15){
            $activesem = $request->get('semid');
            $subjects = \App\Http\Controllers\SuperAdminController\StudentGradeEvaluation::sf9_subjects_sh($gradelevel,$strandid,$activesem,$activesy);
            
        }else{
            $subjects = \App\Http\Controllers\SuperAdminController\StudentGradeEvaluation::sf9_subjects($gradelevel,$activesy);
        }

        $students = \App\Http\Controllers\TeacherControllers\TeacherGradingV4::get_student_data($request);
        $subjects = collect($subjects)->values();

        // return $students;

        $version = "V5";
        unset($students[0]);
        if($gradelevel == 14 || $gradelevel == 15){
            $students = collect($students)->where('strand',$strandid)->values();
        }
       
        $section_detail = DB::table('sections')
                        ->leftJoin('teacher',function($join){
                            $join->on('sections.teacherid','=','teacher.id');
                        })
                        ->join('gradelevel',function($join){
                            $join->on('sections.levelid','=','gradelevel.id');
                        })
                        ->where('sections.id',$section)
                        ->where('sections.deleted',0)
                        ->select('lastname','firstname','sectionname','levelname','levelid')
                        ->first();
                    
        $schoolyear_detail = DB::table('sy')->where('id',$activesy)->first();

        $schoolinfo = DB::table('schoolinfo')
                        ->leftJoin('refregion','schoolinfo.region','=','refregion.regCode')
                        ->leftJoin('refcitymun','schoolinfo.division','=','refcitymun.citymunCode')
                        ->select('schoolinfo.*','refregion.regDesc','refcitymun.citymunDesc')
                        ->get();

        $pdf = PDF::loadView('teacher.grading.grade_summary.grade_summary_printable.grade_summary_sf9',compact('schoolinfo','students','subjects','quarter','section_detail','schoolyear_detail','activesem'))->setPaper('legal');
        $pdf->getDomPDF()->set_option("enable_php", true)->set_option("DOMPDF_ENABLE_CSS_FLOAT", true);
      
        return $pdf->stream('MSQ'.$quarter.str_replace("GRADE ","_G",$section_detail->levelname).str_replace(" ","_",$section_detail->sectionname).'_SY'.$schoolyear_detail->sydesc.'_'.\Carbon\Carbon::now('Asia/Manila')->isoFormat('YYYYMMDDThhMMSS').$version.'.pdf');
     
    }
    public function bysubject(Request $request)
    {

        $gradelevel = $request->get('gradelevel');
        $section = $request->get('section');

        if($request->has('sy'))
        {
            $syid = $request->get('sy');
        }else{
            $syid = $request->get('syid');
        }

        $semid = $request->get('semid');
        $subjid = $request->get('subjid');
        $status = $request->get('status');
        $strandid = $request->get('strand');

        $subjid = $request->get('subjid');
        $request['quarter'] = null;
        
        if($gradelevel == 14 || $gradelevel == 15){
            $activesem = $request->get('semid');
            $subjects = \App\Http\Controllers\SuperAdminController\StudentGradeEvaluation::sf9_subjects_sh($gradelevel,$strandid,$activesem);
        }else{
            $subjects = \App\Http\Controllers\SuperAdminController\StudentGradeEvaluation::sf9_subjects($gradelevel);
        }

        $students = \App\Http\Controllers\TeacherControllers\TeacherGradingV4::get_student_data($request);
        $version = "V5";
        unset($students[0]);


        if($gradelevel == 14 || $gradelevel == 15){
            $students = collect($students)->where('strand',$strandid)->values();
        }

        $data = $students;


        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();;
        $sheet = $spreadsheet->getActiveSheet();
        $borderstyle = [
            'borders' => [
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
                'left' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
                'right' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ]
        ];
        $font_bold = [
                'font' => [
                    'bold' => true,
                ]
            ];

        $schoolinfo = Db::table('schoolinfo')->first();

        $gradelevelinfo = DB::table('gradelevel')
            ->select('gradelevel.*','academicprogram.acadprogcode')
            ->join('academicprogram','gradelevel.acadprogid','=','academicprogram.id')
            ->where('gradelevel.id', $gradelevel)
            ->first();
            
        if(strtolower($gradelevelinfo->acadprogcode) == 'college')
        {
            
        }else{
            $sectioninfo = Db::table('sections')
                ->where('id', $section)
                ->first();
        }

        $schoolyearinfo = DB::table('sy')
            ->where('id', $syid)
            ->first();

        foreach(range('A','H') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        $sheet->getColumnDimension('B')->setAutoSize(false);
        $sheet->getColumnDimension('B')->setWidth(15);
        
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(base_path().'/public/assets/images/department_of_Education.png');
        // $drawing->setPath(base_path().'/public/'.$schoolinfo->picurl);
        $drawing->setHeight(80);
        $drawing->setWorksheet($sheet);
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(20);
        $drawing->setOffsetY(20);

        $drawing->getShadow()->setVisible(true);
        $drawing->getShadow()->setDirection(45);

        
        $sheet->mergeCells('D1:G2');
        $sheet->setCellValue('D1','Summary of Quarterly Grades');
        $sheet->getStyle('D1')->applyFromArray($font_bold);
        $sheet->getStyle('D1')->getAlignment()->setHorizontal('center');

        $sheet->setCellValue('C4','REGION');
        $sheet->setCellValue('C5','SCHOOL NAME');

        $sheet->getStyle('C4:C5')->getAlignment()->setHorizontal('right');
        $sheet->setCellValue('D4',$schoolinfo->regiontext);
        $sheet->getStyle('D4')->applyFromArray($borderstyle);
        $sheet->getStyle('D4')->getAlignment()->setHorizontal('center');
        $sheet->setCellValue('E4','DIVISION');
        $sheet->getStyle('E4')->getAlignment()->setHorizontal('right');
        $sheet->setCellValue('F4',$schoolinfo->divisiontext);
        $sheet->getStyle('F4')->applyFromArray($borderstyle);
        $sheet->getStyle('F4')->getAlignment()->setHorizontal('center');

        $sheet->mergeCells('H1:I1');
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(base_path().'/public/assets/images/deped_logo.png');
        $drawing->setWidth(110);
        $drawing->setWorksheet($sheet);
        $drawing->setCoordinates('H1');
        $drawing->setOffsetX(20);
        $drawing->setOffsetY(15);

        $drawing->getShadow()->setVisible(true);
        $drawing->getShadow()->setDirection(45);
        


        $sheet->mergeCells('D5:F5');
        $sheet->setCellValue('D5',$schoolinfo->schoolname);
        $sheet->getStyle('D5:F5')->applyFromArray($borderstyle);
        $sheet->getStyle('D5')->getAlignment()->setHorizontal('center');
        $sheet->setCellValue('G5','SCHOOL ID');
        $sheet->getStyle('G5')->getAlignment()->setHorizontal('right');
        $sheet->mergeCells('H5:I5');
        $sheet->setCellValue('H5',$schoolinfo->schoolid);
        $sheet->getStyle('H5:I5')->applyFromArray($borderstyle);
        $sheet->getStyle('H5')->getAlignment()->setHorizontal('center');

        
        $sheet->mergeCells('A7:A9');
        $sheet->getStyle('A7:A9')->applyFromArray($borderstyle);

        
        $sheet->mergeCells('B7:C9');
        $sheet->getStyle('B7:C9')->applyFromArray($borderstyle);
        $sheet->getStyle('B7:C9')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('B7:C9')->getAlignment()->setVertical('center');
        $sheet->setCellValue('B7','LEARNERS\' NAMES');

        $studentgrades = array();

        if($gradelevel == 14 || $gradelevel == 15 ){

            $teachername = '';

        }else{
            
            $teachername= '';

            $teacher = DB::table('assignsubj')
                        ->where('sectionid',$section)
                        ->where('syid',$syid)
                        ->where('glevelid',$gradelevel)
                        ->join('assignsubjdetail',function($join) use($subjid){
                            $join->on('assignsubj.id','=','assignsubjdetail.headerid');
                            $join->where('assignsubjdetail.deleted',0);
                            $join->where('assignsubjdetail.subjid',$subjid);
                        })
                        ->join('teacher',function($join){
                            $join->on('assignsubjdetail.teacherid','=','teacher.id');
                            $join->where('teacher.deleted',0);
                        })
                        ->select('firstname','lastname','middlename','suffix')
                        ->where('assignsubj.deleted',0)
                        ->first();

       

            if(isset($teacher->firstname)){
                if($teacher->middlename == null)
                {
                    $teachername = $teacher->lastname.', '.$teacher->firstname.' '.$teacher->suffix;
                }else{
                    $teachername = $teacher->lastname.', '.$teacher->firstname.' '.$teacher->middlename[0].'. '.$teacher->suffix ;
                }
            }
        }
       

        if(count($data)>0)
        {
            foreach($data as $eachdata)
            {
                if(strtolower($eachdata->student) != 'subjects')
                {
                    $gender = Db::table('studinfo')
                        ->where('id', $eachdata->studid)
                        ->first()->gender;
                    $eachdata->gender = $gender;
                    $filteredsubject = collect($eachdata->grades)->filter(function ($value, $key) use($subjid) {
                        if($value->subjid == $subjid)
                        {
                            return $value;
                        }
                    })->values()->all();
                    $eachdata->subjectgrades = $filteredsubject;
                    $eachdata->subjectgrades[0]->teachername = $teachername;
                    $eachdata->subjectgrades = $filteredsubject;
                    array_push($studentgrades, $eachdata);
                }
            }
        }
        // return $studentgrades;
        $sheet->mergeCells('D7:F7');
        $sheet->getStyle('D7:F7')->applyFromArray($borderstyle);
        $sheet->setCellValue('D7','GRADE & SECTION: '.$gradelevelinfo->levelname.' - '.$sectioninfo->sectionname);

        $sheet->mergeCells('G7:I7');
        $sheet->getStyle('G7:I7')->applyFromArray($borderstyle);
        $sheet->setCellValue('G7','SCHOOL YEAR: '.$schoolyearinfo->sydesc);

        $sheet->mergeCells('D8:F8');
        $sheet->getStyle('D8:F8')->applyFromArray($borderstyle);
        
        if(count($studentgrades)>0)
        {
            if(count($studentgrades[0]->subjectgrades)> 0)
            {
                $sheet->setCellValue('D8','TEACHER: '.$studentgrades[0]->subjectgrades[0]->teachername);
            }else{
                $sheet->setCellValue('D8','TEACHER:');
            }

        }else{
            $sheet->setCellValue('D8','TEACHER:');
        }
        $sheet->mergeCells('G8:I8');
        $sheet->getStyle('G8:I8')->applyFromArray($borderstyle);
        if(count($studentgrades)>0)
        {
            if(count($studentgrades[0]->subjectgrades)> 0)
            {
                $subjectname = $studentgrades[0]->subjectgrades[0]->subjdesc;
                $sheet->setCellValue('G8','SUBJECT: '.$studentgrades[0]->subjectgrades[0]->subjdesc);
    
            }else{
                $subjectname = "";
                $sheet->setCellValue('G8','SUBJECT: ');
    
            }
        }else{
            $subjectname = "";
            $sheet->setCellValue('G8','SUBJECT: ');
        }

        if(strtolower($gradelevelinfo->acadprogcode) == 'shs')
        {

            if($semid == 1){
                $sheet->mergeCells('D9:E9');
                $sheet->setCellValue('D9','1st Quarter');
                $sheet->getStyle('D9')->applyFromArray($borderstyle);
                $sheet->mergeCells('F9:G9');
                $sheet->setCellValue('F9','2nd Quarter');
                $sheet->getStyle('F9')->applyFromArray($borderstyle);
            }else if($semid == 2){
                $sheet->mergeCells('D9:E9');
                $sheet->setCellValue('D9','3rd Quarter');
                $sheet->getStyle('D9')->applyFromArray($borderstyle);
                $sheet->mergeCells('F9:G9');
                $sheet->setCellValue('F9','4th Quarter');
                $sheet->getStyle('F9')->applyFromArray($borderstyle);
            }
            
    
        }else{
            $sheet->setCellValue('D9','1st Quarter');
            $sheet->getStyle('D9')->applyFromArray($borderstyle);
            
            $sheet->setCellValue('E9','2nd Quarter');
            $sheet->getStyle('E9')->applyFromArray($borderstyle);
    
            $sheet->setCellValue('F9','3rd Quarter');
            $sheet->getStyle('F9')->applyFromArray($borderstyle);
    
            $sheet->setCellValue('G9','4th Quarter');
            $sheet->getStyle('G9')->applyFromArray($borderstyle);
        }

        $sheet->setCellValue('H9','FINAL GRADE');
        $sheet->getStyle('H9')->applyFromArray($borderstyle);

        $sheet->setCellValue('I9','REMARKS');
        $sheet->getStyle('I9')->applyFromArray($borderstyle);

        $sheet->getStyle('D9:I9')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('D7:I9')->getAlignment()->setVertical('center');
        
        
        $sheet->getStyle('A10:I10')->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setARGB('DDDDDD');

        $studentgrades = collect($studentgrades)->sortBy('student')->values()->all();
        // $sheet->setReadDataOnly(false);
        $startcellno = 11;
        $malecount = 1;
        $femalecount = 1;



        $sheet->mergeCells('B10:C10');
        $sheet->getStyle('B10:C10')->applyFromArray($borderstyle);
        $sheet->setCellValue('B10','MALE');

        if(strtolower($gradelevelinfo->acadprogcode) == 'shs')
        {
            $sheet->mergeCells('D10:E10');
            $sheet->mergeCells('F10:G10');
        }

        foreach(range('D','I') as $columnID) {
            $sheet->getStyle($columnID.'10')->applyFromArray($borderstyle);
        }
        
        if(count($studentgrades)>0)
        {
            foreach($studentgrades as $studentgrade)
            {
                if(strtolower($studentgrade->gender) == 'male')
                {
                    $sheet->mergeCells('B'.$startcellno.':C'.$startcellno);
                    $sheet->setCellValue('A'.$startcellno,$malecount);
                    $sheet->getStyle('A'.$startcellno)->applyFromArray($borderstyle);
                    $sheet->setCellValue('B'.$startcellno,$studentgrade->student);
                    $sheet->getStyle('B'.$startcellno.':C'.$startcellno)->applyFromArray($borderstyle);
                    
                    foreach(range('D','I') as $columnID) {
                        $sheet->getStyle($columnID.$startcellno)->applyFromArray($borderstyle);
                    }
                    $complete = 0;
                    $totalgrade = 0;
                    if(count($studentgrade->subjectgrades) > 0)
                    {
                        if(strtolower($gradelevelinfo->acadprogcode) == 'shs')
                        {
                            if($request->get('semid') == 1){
                                $sheet->mergeCells('D'.$startcellno.':E'.$startcellno);
                                $sheet->mergeCells('F'.$startcellno.':G'.$startcellno);
                                $sheet->setCellValue('D'.$startcellno,$studentgrade->subjectgrades[0]->q1); 
                                $sheet->setCellValue('F'.$startcellno,$studentgrade->subjectgrades[0]->q2);
                            }else{
                                $sheet->mergeCells('D'.$startcellno.':E'.$startcellno);
                                $sheet->mergeCells('F'.$startcellno.':G'.$startcellno);
                                $sheet->setCellValue('D'.$startcellno,$studentgrade->subjectgrades[0]->q3); 
                                $sheet->setCellValue('F'.$startcellno,$studentgrade->subjectgrades[0]->q4);
                            }
                        }else{
                            $sheet->setCellValue('D'.$startcellno,$studentgrade->subjectgrades[0]->q1);
                            $sheet->setCellValue('E'.$startcellno,$studentgrade->subjectgrades[0]->q2);
                            $sheet->setCellValue('F'.$startcellno,$studentgrade->subjectgrades[0]->q3);
                            $sheet->setCellValue('G'.$startcellno,$studentgrade->subjectgrades[0]->q4);
                        }
                    }
                    $sheet->setCellValue('H'.$startcellno,$studentgrade->subjectgrades[0]->finalrating);
                    $sheet->setCellValue('I'.$startcellno,$studentgrade->subjectgrades[0]->actiontaken);
                    $sheet->getStyle('D'.$startcellno.':I'.$startcellno)->getAlignment()->setHorizontal('center');
                    $startcellno+=1;
                    $malecount+=1;
                }
            }
        }
        $sheet->mergeCells('B'.$startcellno.':C'.$startcellno);
        $sheet->getStyle('B'.$startcellno.':C'.$startcellno)->applyFromArray($borderstyle);
        $sheet->setCellValue('B'.$startcellno,'FEMALE');
        if(strtolower($gradelevelinfo->acadprogcode) == 'shs')
        {
            $sheet->mergeCells('D'.$startcellno.':E'.$startcellno);
            $sheet->mergeCells('F'.$startcellno.':G'.$startcellno);
        }
        

        $sheet->getStyle('A'.$startcellno.':I'.$startcellno)->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setARGB('DDDDDD');
        foreach(range('D','I') as $columnID) {
            $sheet->getStyle($columnID.$startcellno)->applyFromArray($borderstyle);
        }
        
        $startcellno+=1;
        if(count($studentgrades)>0)
        {
            foreach($studentgrades as $studentgrade)
            {
                if(strtolower($studentgrade->gender) == 'female')
                {
                    $sheet->mergeCells('B'.$startcellno.':C'.$startcellno);
                    $sheet->setCellValue('A'.$startcellno,$femalecount);
                    $sheet->getStyle('A'.$startcellno)->applyFromArray($borderstyle);
                    $sheet->setCellValue('B'.$startcellno,$studentgrade->student);
                    $sheet->getStyle('B'.$startcellno.':C'.$startcellno)->applyFromArray($borderstyle);
                    
                    foreach(range('D','I') as $columnID) {
                        $sheet->getStyle($columnID.$startcellno)->applyFromArray($borderstyle);
                    }

                    $complete = 0;
                    $totalgrade = 0;

                    if(count($studentgrade->subjectgrades) > 0)
                    {
                        if(strtolower($gradelevelinfo->acadprogcode) == 'shs')
                        {
                            if($request->get('semid') == 1){
                                $sheet->mergeCells('D'.$startcellno.':E'.$startcellno);
                                $sheet->mergeCells('F'.$startcellno.':G'.$startcellno);
                                $sheet->setCellValue('D'.$startcellno,$studentgrade->subjectgrades[0]->q1); 
                                $sheet->setCellValue('F'.$startcellno,$studentgrade->subjectgrades[0]->q2);
                            }else{
                                $sheet->mergeCells('D'.$startcellno.':E'.$startcellno);
                                $sheet->mergeCells('F'.$startcellno.':G'.$startcellno);
                                $sheet->setCellValue('D'.$startcellno,$studentgrade->subjectgrades[0]->q3); 
                                $sheet->setCellValue('F'.$startcellno,$studentgrade->subjectgrades[0]->q4);
                            }
                        }else{
                            $sheet->setCellValue('D'.$startcellno,$studentgrade->subjectgrades[0]->q1);
                            $sheet->setCellValue('E'.$startcellno,$studentgrade->subjectgrades[0]->q2);
                            $sheet->setCellValue('F'.$startcellno,$studentgrade->subjectgrades[0]->q3);
                            $sheet->setCellValue('G'.$startcellno,$studentgrade->subjectgrades[0]->q4);
                        }
                    }
                    $sheet->setCellValue('H'.$startcellno,$studentgrade->subjectgrades[0]->finalrating);
                    $sheet->setCellValue('I'.$startcellno,$studentgrade->subjectgrades[0]->actiontaken);
                    $sheet->getStyle('D'.$startcellno.':I'.$startcellno)->getAlignment()->setHorizontal('center');
                    $startcellno+=1;
                    $femalecount+=1;
                }
            }
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        header('Content-Type: application/vnd.ms-excel');
        $schoolyear_detail = DB::table('sy')->where('id',$syid)->first();
        header('Content-Disposition: attachment; filename="GS_'.$subjectname.'_'.str_replace("GRADE ","_G",$gradelevelinfo->levelname).str_replace(" ","_",$sectioninfo->sectionname).'_SY'.$schoolyear_detail->sydesc.'_'.\Carbon\Carbon::now('Asia/Manila')->isoFormat('YYYYMMDDThhMMSS').$version.'.xlsx"');
        $writer->save("php://output");
    }


    public function grade_status(Request $request)
    {
        $gradelevel = $request->get('gradelevel');
        $section = $request->get('section');
        $status = $request->get('status');

        if($request->has('sy'))
        {
            $syid = $request->get('sy');
        }else{
            $syid = $request->get('syid');
        }

        $semid = $request->get('semid');


        $data = \App\Models\Grading\GradingReport::grade_report_gradelevel($syid, $semid, $gradelevel);

        $temp_data = array();

        foreach($data as $item){

            $q1_grade = 0;
            $q2_grade = 0;
            $q3_grade = 0;
            $q4_grade = 0;
            $final = 60;
            $remark = 0;

            foreach($item->grades as $grade_item){

                
                $q1_grade += $grade_item->q1;
                $q2_grade += $grade_item->q2;

                if($gradelevel != 14 && $gradelevel != 15){
                    $q3_grade += $grade_item->q3;
                    $q4_grade += $grade_item->q4;
                }
               
              

            }
            
            $q1_grade =  $q1_grade / count($item->grades);
            $q2_grade =  $q2_grade/ count($item->grades);

            if($gradelevel != 14 && $gradelevel != 15){
                $final = ( number_format($q1_grade) + number_format($q2_grade) + number_format($q3_grade) + number_format($q4_grade) ) / 4;
                if(number_format($final) >= 75){
                    $remark = 1;
                }
            }else{
                $final = ( number_format($q1_grade) + number_format($q2_grade) ) / 2;
                if(number_format($final) >= 75){
                    $remark = 1;
                }
            }


            if($status == 3 && $remark == 0){

                array_push($temp_data,(object)[
                        'student'=>$item->student,
                        'studid'=>$item->studid,
                        'q1'=>number_format($q1_grade),
                        'q2'=>number_format($q2_grade),
                        'q3'=>number_format($q3_grade),
                        'q4'=>number_format($q4_grade),
                        'final'=>number_format($final),
                        'remark'=>$remark
                ]);
            }
            else if($status == 2 && $remark == 1){
                array_push($temp_data,(object)[
                    'student'=>$item->student,
                    'studid'=>$item->studid,
                    'q1'=>number_format($q1_grade),
                    'q2'=>number_format($q2_grade),
                    'q3'=>number_format($q3_grade),
                    'q4'=>number_format($q4_grade),
                    'final'=>number_format($final),
                    'remark'=>$remark
                ]);
            }else  if($status == 2){
                array_push($temp_data,(object)[
                    'student'=>$item->student,
                    'studid'=>$item->studid,
                    'q1'=>number_format($q1_grade),
                    'q2'=>number_format($q2_grade),
                    'q3'=>number_format($q3_grade),
                    'q4'=>number_format($q4_grade),
                    'final'=>number_format($final),
                    'remark'=>$remark
                ]);
            }

        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();;
        $sheet = $spreadsheet->getActiveSheet();
        $borderstyle = [
            'borders' => [
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
                'left' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
                'right' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ]
        ];
        $font_bold = [
                'font' => [
                    'bold' => true,
                ]
            ];

        $schoolinfo = Db::table('schoolinfo')
            ->select(
                'schoolinfo.schoolid',
                'schoolinfo.schoolname',
                'schoolinfo.authorized',
                'schoolinfo.picurl',
                'refcitymun.citymunDesc as division',
                'schoolinfo.district',
                'schoolinfo.address',
                'refregion.regDesc as region'
            )
            ->leftJoin('refregion','schoolinfo.region','=','refregion.regCode')
            ->leftJoin('refcitymun','schoolinfo.division','=','refcitymun.citymunCode')
            ->first();

        $gradelevelinfo = DB::table('gradelevel')
            ->select('gradelevel.*','academicprogram.acadprogcode')
            ->join('academicprogram','gradelevel.acadprogid','=','academicprogram.id')
            ->where('gradelevel.id', $gradelevel)
            ->first();
            
        if(strtolower($gradelevelinfo->acadprogcode) == 'college')
        {
            
        }else{
            $sectioninfo = Db::table('sections')
                ->where('id', $section)
                ->first();
        }

        $schoolyearinfo = DB::table('sy')
            ->where('id', $syid)
            ->first();

        foreach(range('A','H') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        $sheet->getColumnDimension('B')->setAutoSize(false);
        $sheet->getColumnDimension('B')->setWidth(15);
        
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(base_path().'/public/assets/images/department_of_Education.png');
        // $drawing->setPath(base_path().'/public/'.$schoolinfo->picurl);
        $drawing->setHeight(80);
        $drawing->setWorksheet($sheet);
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(20);
        $drawing->setOffsetY(20);

        $drawing->getShadow()->setVisible(true);
        $drawing->getShadow()->setDirection(45);

        
        $sheet->mergeCells('D1:G2');
        $sheet->setCellValue('D1','Summary of Quarterly Grades');
        $sheet->getStyle('D1')->applyFromArray($font_bold);
        $sheet->getStyle('D1')->getAlignment()->setHorizontal('center');

        $sheet->setCellValue('C4','REGION');
        $sheet->setCellValue('C5','SCHOOL NAME');

        $sheet->getStyle('C4:C5')->getAlignment()->setHorizontal('right');
        $sheet->setCellValue('D4',$schoolinfo->region);
        $sheet->getStyle('D4')->applyFromArray($borderstyle);
        $sheet->getStyle('D4')->getAlignment()->setHorizontal('center');
        $sheet->setCellValue('E4','DIVISION');
        $sheet->getStyle('E4')->getAlignment()->setHorizontal('right');
        $sheet->setCellValue('F4',$schoolinfo->division);
        $sheet->getStyle('F4')->applyFromArray($borderstyle);
        $sheet->getStyle('F4')->getAlignment()->setHorizontal('center');

        $sheet->mergeCells('H1:I1');
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(base_path().'/public/assets/images/deped_logo.png');
        // $drawing->setPath(base_path().'/public/'.$schoolinfo->picurl);
        // $drawing->setHeight(50);
        $drawing->setWidth(110);
        $drawing->setWorksheet($sheet);
        $drawing->setCoordinates('H1');
        $drawing->setOffsetX(20);
        $drawing->setOffsetY(15);

        $drawing->getShadow()->setVisible(true);
        $drawing->getShadow()->setDirection(45);
        


        $sheet->mergeCells('D5:F5');
        $sheet->setCellValue('D5',$schoolinfo->schoolname);
        $sheet->getStyle('D5:F5')->applyFromArray($borderstyle);
        $sheet->getStyle('D5')->getAlignment()->setHorizontal('center');
        $sheet->setCellValue('G5','SCHOOL ID');
        $sheet->getStyle('G5')->getAlignment()->setHorizontal('right');
        $sheet->mergeCells('H5:I5');
        $sheet->setCellValue('H5',$schoolinfo->schoolid);
        $sheet->getStyle('H5:I5')->applyFromArray($borderstyle);
        $sheet->getStyle('H5')->getAlignment()->setHorizontal('center');

        
        $sheet->mergeCells('A7:A9');
        $sheet->getStyle('A7:A9')->applyFromArray($borderstyle);

        
        $sheet->mergeCells('B7:C9');
        $sheet->getStyle('B7:C9')->applyFromArray($borderstyle);
        $sheet->getStyle('B7:C9')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('B7:C9')->getAlignment()->setVertical('center');
        $sheet->setCellValue('B7','LEARNERS\' NAMES');



        $studentgrades = array();

        // if(count($data)>0)
        // {
        //     foreach($data as $eachdata)
        //     {
        //         if(strtolower($eachdata->student) != 'subjects')
        //         {
        //             $gender = Db::table('studinfo')
        //                 ->where('id', $eachdata->studid)
        //                 ->first()->gender;

        //             $eachdata->gender = $gender;
        //             // return $eachdata->grades;
        //             $filteredsubject = collect($eachdata->grades)->filter(function ($value, $key) use($subjid) {
        //                 if($value->subjid == $subjid)
        //                 {
        //                     return $value;
        //                 }
        //             })->values()->all();

        //             $eachdata->subjectgrades = $filteredsubject;

        //             if(count($eachdata->subjectgrades)>0)
        //             {
        //                 if($eachdata->subjectgrades[0]->teacherid != "")
        //                 {
        //                     $teachername = Db::table('teacher')
        //                         ->select('teacher.lastname','teacher.firstname','teacher.middlename','teacher.suffix')
        //                         ->where('id', $eachdata->subjectgrades[0]->teacherid)
        //                         ->first();
                            
        //                     if($teachername->middlename == null)
        //                     {
        //                         $teachername = $teachername->lastname.', '.$teachername->firstname.' '.$teachername->suffix;
        //                     }else{
        //                         $teachername = $teachername->lastname.', '.$teachername->firstname.' '.$teachername->middlename[0].'. '.$teachername->suffix ;
        //                     }
        //                 }else{
        //                     $teachername = null;
        //                 }
        //                     $eachdata->subjectgrades[0]->teachername = $teachername;
        //             }
                    
        //             $eachdata->subjectgrades = $filteredsubject;

        //             array_push($studentgrades, $eachdata);
        //         }
        //     }
        // }
        // return $studentgrades;

        foreach($temp_data as $eachdata)
        {
            $gender = Db::table('studinfo')
                        ->where('id', $eachdata->studid)
                        ->select('gender')
                        ->first()->gender;

            $eachdata->gender =  $gender;

        }

        $sheet->mergeCells('D7:F7');
        $sheet->getStyle('D7:F7')->applyFromArray($borderstyle);
        $sheet->setCellValue('D7','GRADE LEVEL: '.$gradelevelinfo->levelname);

        $sheet->mergeCells('G7:I7');
        $sheet->getStyle('G7:I7')->applyFromArray($borderstyle);
        $sheet->setCellValue('G7','SCHOOL YEAR: '.$schoolyearinfo->sydesc);

        $sheet->mergeCells('D8:F8');
        $sheet->getStyle('D8:F8')->applyFromArray($borderstyle);
        
        if(count($studentgrades)>0)
        {
            if(count($studentgrades[0]->subjectgrades)> 0)
            {
                $sheet->setCellValue('D8','');
            }else{
                $sheet->setCellValue('D8','');
            }

        }else{
            $sheet->setCellValue('D8','');
        }
        $sheet->mergeCells('G8:I8');
        $sheet->getStyle('G8:I8')->applyFromArray($borderstyle);

        if(count($studentgrades)>0)
        {
            if(count($studentgrades[0]->subjectgrades)> 0)
            {
                $subjectname = $studentgrades[0]->subjectgrades[0]->subjdesc;
                $sheet->setCellValue('G8','');
    
            }else{
                $subjectname = "";
                $sheet->setCellValue('G8','');
    
            }
        }else{
            $subjectname = "";
            $sheet->setCellValue('G8','');
        }

        if(strtolower($gradelevelinfo->acadprogcode) == 'shs')
        {
            $sheet->mergeCells('D9:E9');
            $sheet->setCellValue('D9','1st Quarter');
            $sheet->getStyle('D9')->applyFromArray($borderstyle);
            
    
            $sheet->mergeCells('F9:G9');
            $sheet->setCellValue('F9','2nd Quarter');
            $sheet->getStyle('F9')->applyFromArray($borderstyle);
    
        }else{
            $sheet->setCellValue('D9','1st Quarter');
            $sheet->getStyle('D9')->applyFromArray($borderstyle);
            
            $sheet->setCellValue('E9','2nd Quarter');
            $sheet->getStyle('E9')->applyFromArray($borderstyle);
    
            $sheet->setCellValue('F9','3rd Quarter');
            $sheet->getStyle('F9')->applyFromArray($borderstyle);
    
            $sheet->setCellValue('G9','4th Quarter');
            $sheet->getStyle('G9')->applyFromArray($borderstyle);
        }

        $sheet->setCellValue('H9','FINAL GRADE');
        $sheet->getStyle('H9')->applyFromArray($borderstyle);

        $sheet->setCellValue('I9','REMARKS');
        $sheet->getStyle('I9')->applyFromArray($borderstyle);

        $sheet->getStyle('D9:I9')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('D7:I9')->getAlignment()->setVertical('center');
        
        
        $sheet->getStyle('A10:I10')->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setARGB('DDDDDD');

        $studentgrades = collect($temp_data)->sortBy('student')->values()->all();
        // $sheet->setReadDataOnly(false);

        
        $startcellno = 11;
        $malecount = 1;
        $femalecount = 1;



        $sheet->mergeCells('B10:C10');
        $sheet->getStyle('B10:C10')->applyFromArray($borderstyle);
        $sheet->setCellValue('B10','MALE');

        if(strtolower($gradelevelinfo->acadprogcode) == 'shs')
        {
            $sheet->mergeCells('D10:E10');
            $sheet->mergeCells('F10:G10');
        }

        foreach(range('D','I') as $columnID) {
            $sheet->getStyle($columnID.'10')->applyFromArray($borderstyle);
        }
        
        if(count($studentgrades)>0)
        {
            foreach($temp_data as $studentgrade)
            {
                if(strtolower($studentgrade->gender) == 'male')
                {
                    $sheet->mergeCells('B'.$startcellno.':C'.$startcellno);
                    $sheet->setCellValue('A'.$startcellno,$malecount);
                    $sheet->getStyle('A'.$startcellno)->applyFromArray($borderstyle);
                    $sheet->setCellValue('B'.$startcellno,$studentgrade->student);
                    $sheet->getStyle('B'.$startcellno.':C'.$startcellno)->applyFromArray($borderstyle);
                    
                    foreach(range('D','I') as $columnID) {
                        $sheet->getStyle($columnID.$startcellno)->applyFromArray($borderstyle);
                    }
                    $complete = 0;
                    $totalgrade = 0;
                    if(isset($studentgrade->q1))
                    {
                        
                        if(strtolower($gradelevelinfo->acadprogcode) == 'shs')
                        {
                            
                            $sheet->mergeCells('D'.$startcellno.':E'.$startcellno);
                            if(isset($studentgrade->q1))
                            {
                                $sheet->setCellValue('D'.$startcellno,$studentgrade->q1); 
                            }
                            $sheet->mergeCells('F'.$startcellno.':G'.$startcellno);
                            if(isset($studentgrade->q1))
                            {
                                $sheet->setCellValue('F'.$startcellno,$studentgrade->q2);
                            }
                        }else{
                            $sheet->setCellValue('D'.$startcellno,$studentgrade->q1);
                            $sheet->setCellValue('E'.$startcellno,$studentgrade->q2);
                            $sheet->setCellValue('F'.$startcellno,$studentgrade->q3);
                            $sheet->setCellValue('G'.$startcellno,$studentgrade->q4);
                        }

                        if(isset($studentgrade->q1))
                        {
                            if($studentgrade->q1 != null)
                            {
                                $totalgrade += $studentgrade->q1;
                                $complete+=1;
                            }
                        }
                        if(isset($studentgrade->q2))
                                {
                            if($studentgrade->q2 != null)
                            {
                                $totalgrade += $studentgrade->q2;
                                $complete+=1;
                            }
                        }
                        if(isset($studentgrade->q3))
                        {
                            if($studentgrade->q3 != null)
                            {
                                $totalgrade += $studentgrade->q3;
                                $complete+=1;
                            }
                        }
                        if(isset($studentgrade->q4))
                        {
                            if($studentgrade->q4 != null)
                            {
                                $totalgrade += $studentgrade->q4;
                                $complete+=1;
                            }
                        }
                    }

                    if(strtolower($gradelevelinfo->acadprogcode) == 'shs')
                    {
                        // if($complete == 2)
                        // {
                            $sheet->setCellValue('H'.$startcellno,number_format($totalgrade/2));
                            if(number_format($totalgrade/2,2) < 75)
                            {
                                $remarks = 'FAILED';
                            }else{
                                $remarks = 'PASSED';
                            }
                            $sheet->setCellValue('I'.$startcellno,$remarks);
                        // }
                    }else{
                        // if($complete == 4)
                        // {
                            $sheet->setCellValue('H'.$startcellno,number_format($totalgrade/4));
                            if(number_format($totalgrade/4,2) < 75)
                            {
                                $remarks = 'FAILED';
                            }else{
                                $remarks = 'PASSED';
                            }
                            $sheet->setCellValue('I'.$startcellno,$remarks);
                        // }
                    }
                    $sheet->getStyle('D'.$startcellno.':I'.$startcellno)->getAlignment()->setHorizontal('center');
                    $startcellno+=1;
                    $malecount+=1;
                }
            }
        }
        $sheet->mergeCells('B'.$startcellno.':C'.$startcellno);
        $sheet->getStyle('B'.$startcellno.':C'.$startcellno)->applyFromArray($borderstyle);
        $sheet->setCellValue('B'.$startcellno,'FEMALE');
        if(strtolower($gradelevelinfo->acadprogcode) == 'shs')
        {
            $sheet->mergeCells('D'.$startcellno.':E'.$startcellno);
            $sheet->mergeCells('F'.$startcellno.':G'.$startcellno);
        }
        

        $sheet->getStyle('A'.$startcellno.':I'.$startcellno)->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setARGB('DDDDDD');
        foreach(range('D','I') as $columnID) {
            $sheet->getStyle($columnID.$startcellno)->applyFromArray($borderstyle);
        }
        
        $startcellno+=1;
        if(count($studentgrades)>0)
        {
            foreach($studentgrades as $studentgrade)
            {
                if(strtolower($studentgrade->gender) == 'female')
                {
                    $sheet->mergeCells('B'.$startcellno.':C'.$startcellno);
                    $sheet->setCellValue('A'.$startcellno,$femalecount);
                    $sheet->getStyle('A'.$startcellno)->applyFromArray($borderstyle);
                    $sheet->setCellValue('B'.$startcellno,$studentgrade->student);
                    $sheet->getStyle('B'.$startcellno.':C'.$startcellno)->applyFromArray($borderstyle);
                    
                    foreach(range('D','I') as $columnID) {
                        $sheet->getStyle($columnID.$startcellno)->applyFromArray($borderstyle);
                    }

                    $complete = 0;
                    $totalgrade = 0;

                    if(isset($studentgrade->q1))
                    {
                        
                        if(strtolower($gradelevelinfo->acadprogcode) == 'shs')
                        {
                            $sheet->mergeCells('D'.$startcellno.':E'.$startcellno);
                            if(isset($studentgrade->q1))
                            {
                                $sheet->setCellValue('D'.$startcellno,$studentgrade->q1); 
                            }
                            $sheet->mergeCells('F'.$startcellno.':G'.$startcellno);
                            if(isset($studentgrade->q1))
                            {
                                $sheet->setCellValue('F'.$startcellno,$studentgrade->q2);
                            }
                        }else{
                            $sheet->setCellValue('D'.$startcellno,$studentgrade->q1);
                            $sheet->setCellValue('E'.$startcellno,$studentgrade->q2);
                            $sheet->setCellValue('F'.$startcellno,$studentgrade->q3);
                            $sheet->setCellValue('G'.$startcellno,$studentgrade->q4);
                        }
                        if(isset($studentgrade->q1))
                        {
                            if($studentgrade->q1 != null)
                            {
                                $totalgrade += $studentgrade->q1;
                                $complete+=1;
                            }
                        }
                        if(isset($studentgrade->q2))
                                {
                            if($studentgrade->q2 != null)
                            {
                                $totalgrade += $studentgrade->q2;
                                $complete+=1;
                            }
                        }
                        if(isset($studentgrade->q3))
                        {
                            if($studentgrade->q3 != null)
                            {
                                $totalgrade += $studentgrade->q3;
                                $complete+=1;
                            }
                        }
                        if(isset($studentgrade->q4))
                        {
                            if($studentgrade->q4 != null)
                            {
                                $totalgrade += $studentgrade->q4;
                                $complete+=1;
                            }
                        }
                    }

                    if(strtolower($gradelevelinfo->acadprogcode) == 'shs')
                    {
                        // if($complete == 2)
                        // {
                            $sheet->setCellValue('H'.$startcellno,number_format($totalgrade/2));
                            if(number_format($totalgrade/2,2) < 75)
                            {
                                $remarks = 'FAILED';
                            }else{
                                $remarks = 'PASSED';
                            }
                            $sheet->setCellValue('I'.$startcellno,$remarks);
                        // }
                    }else{
                        // if($complete == 4)
                        // {
                            $sheet->setCellValue('H'.$startcellno,number_format($totalgrade/4));
                            if(number_format($totalgrade/4,2) < 75)
                            {
                                $remarks = 'FAILED';
                            }else{
                                $remarks = 'PASSED';
                            }
                            $sheet->setCellValue('I'.$startcellno,$remarks);
                        // }
                    }
                    $sheet->getStyle('D'.$startcellno.':I'.$startcellno)->getAlignment()->setHorizontal('center');
                    $startcellno+=1;
                    $femalecount+=1;
                }
            }
        }

        $excel_status = 'ALL';

        if($status == 2){
            $excel_status = 'PASSED';
        }else if($status == 3){
            $excel_status = 'FAILED';
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="'.'GRADE STATUS - '.$gradelevelinfo->levelname.' - '.$excel_status.'.xlsx"');
        $writer->save("php://output");
    }

    function studentawards(Request $request)
    {
        
        
        $gradelevel     = $request->get('gradelevel');
        $section        = $request->get('section');
        $syid           = $request->get('sy');
        $semid          = $request->get('semid');
        $strandid       = $request->get('strand');

        $request->request->remove('semid');
       
        if($gradelevel == 14 || $gradelevel == 15){
            $activesem = $request->get('semid');
            $subjects = \App\Http\Controllers\SuperAdminController\StudentGradeEvaluation::sf9_subjects_sh($gradelevel,$strandid,$activesem);
            
        }else{
            $subjects = \App\Http\Controllers\SuperAdminController\StudentGradeEvaluation::sf9_subjects($gradelevel);
        }

        if($section == null){
            $sections = DB::table('sections')
                            ->where('levelid',$gradelevel)
                            ->where('deleted',0)
                            ->select(
                                'id',
                                'sectionname'
                            )
                            ->get();
            $gradelevel_students = array();
            foreach($sections as $section_item){
                $request->request->add(['section' => $section_item->id]);
                $temp_students = \App\Http\Controllers\TeacherControllers\TeacherGradingV4::get_student_data($request);
                foreach($temp_students as $student){
                    $student->sectionname = $section_item->sectionname;
                    array_push($gradelevel_students,$student);
                }
            }
            $students = collect($gradelevel_students);
        }else{
            $sections = DB::table('sections')
                            ->where('id',$section)
                            ->where('deleted',0)
                            ->select(
                                'id',
                                'sectionname'
                            )
                            ->first();

            $students = \App\Http\Controllers\TeacherControllers\TeacherGradingV4::get_student_data($request);
            if($strandid != null){
                $gradelevel_students = array();
                $temp_students = array();
                foreach($students as $student){
                    if(isset($student->strand)){
                        if($strandid == $student->strand){
                            $student->sectionname = $sections->sectionname;
                            array_push($gradelevel_students,$student);
                            array_push($temp_students,$student);
                        }
                    }
                }
                $students = $temp_students;
            }else{
                foreach($students as $student){
                    $student->sectionname = $sections->sectionname;
                }
            }
        }

        
        $quarter        = $request->get('quarter');
        $exclude        = explode(",",$request->get('exclude'));

        $schoolinfo     = Db::table('schoolinfo')
                        ->select(
                            'schoolinfo.*',
                            'schoolinfo.schoolid',
                            'schoolinfo.schoolname',
                            'schoolinfo.authorized',
                            'schoolinfo.picurl',
                            'refcitymun.citymunDesc as division',
                            'schoolinfo.district',
                            'schoolinfo.address',
                            'refregion.regDesc as region'
                        )
                        ->leftJoin('refregion','schoolinfo.region','=','refregion.regCode')
                        ->leftJoin('refcitymun','schoolinfo.division','=','refcitymun.citymunCode')
                        ->first();

        $spreadsheet    = new \PhpOffice\PhpSpreadsheet\Spreadsheet();


        $sheet          = $spreadsheet->getActiveSheet();
        $borderstyle    = [
                            'borders' => [
                                'top' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                ],
                                'bottom' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                ],
                                'left' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                ],
                                'right' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                ],
                            ]
                        ];
        $font_bold      = [
                            'font' => [
                                'bold' => true,
                            ]
                        ];

        $gradelevelinfo = DB::table('gradelevel')
                        ->select('gradelevel.*','academicprogram.acadprogcode')
                        ->join('academicprogram','gradelevel.acadprogid','=','academicprogram.id')
                        ->where('gradelevel.id', $gradelevel)
                        ->first();
            
        if(strtolower($gradelevelinfo->acadprogcode) == 'college')
        {
            
        }else{
            
            if($section == null){
                $sectioninfo = (object)[
                    'sectionname'=>null
                ];
                
            }else{
                $sectioninfo = Db::table('sections')
                ->where('id', $section)
                ->first();
            }
           
        }

        $schoolyearinfo = DB::table('sy')
            ->where('id', $syid)
            ->first();

        foreach(range('A','H') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        
        
        $sheet->getColumnDimension('B')->setAutoSize(false);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->mergeCells('D1:F2');
        $sheet->setCellValue('D1','Student Ranking List');
        $sheet->getStyle('D1')->applyFromArray($font_bold);
        $sheet->getStyle('D1')->getAlignment()->setHorizontal('center');

        $sheet->setCellValue('C4','REGION');
        $sheet->setCellValue('C5','SCHOOL NAME');

        $sheet->getStyle('C4:C5')->getAlignment()->setHorizontal('right');
        $sheet->setCellValue('D4',$schoolinfo->regiontext);
        $sheet->getStyle('D4')->applyFromArray($borderstyle);
        $sheet->getStyle('D4')->getAlignment()->setHorizontal('center');
        $sheet->setCellValue('E4','DIVISION');
        $sheet->getStyle('E4')->getAlignment()->setHorizontal('right');
        $sheet->setCellValue('F4',$schoolinfo->divisiontext);
        $sheet->getStyle('F4')->applyFromArray($borderstyle);
        $sheet->getStyle('F4')->getAlignment()->setHorizontal('center');
        // $drawing->setPath(base_path().'/public/'.$schoolinfo->picurl);
        // $drawing->setHeight(50);
        $sheet->getColumnDimension('H')->setAutoSize(false);
        $sheet->getColumnDimension('H')->setWidth(15);

        $sheet->mergeCells('D5:F5');
        $sheet->setCellValue('D5',$schoolinfo->schoolname);
        $sheet->getStyle('D5:F5')->applyFromArray($borderstyle);
        $sheet->getStyle('D5')->getAlignment()->setHorizontal('center');
        $sheet->setCellValue('G5','SCHOOL ID');
        $sheet->getStyle('G5')->getAlignment()->setHorizontal('right');
        // $sheet->mergeCells('H5:I5');
        $sheet->setCellValue('H5',$schoolinfo->schoolid);
        $sheet->getStyle('H5')->applyFromArray($borderstyle);
        $sheet->getStyle('H5')->getAlignment()->setHorizontal('center');

      
        
        $sheet->mergeCells('A7:A8');
        $sheet->getStyle('A7:A8')->applyFromArray($borderstyle);

        
        $sheet->mergeCells('B7:C8');
        $sheet->getStyle('B7:C8')->applyFromArray($borderstyle);
        $sheet->getStyle('B7:C8')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('B7:C8')->getAlignment()->setVertical('center');
        $sheet->setCellValue('B7','LEARNERS\' NAMES');

        $setup_with_distinct = true;
        $setup_with_sp = true;

        // return $studentgrades;
        $sheet->mergeCells('D7:F7');
        $sheet->getStyle('D7:F7')->applyFromArray($borderstyle);
        $sheet->setCellValue('D7','GRADE LEVEL: '.$gradelevelinfo->levelname);

        $sheet->mergeCells('G7:H7');
        $sheet->getStyle('G7:H7')->applyFromArray($borderstyle);
        $sheet->setCellValue('G7','SCHOOL YEAR: '.$schoolyearinfo->sydesc);

        
        // $sheet->mergeCells('D9:E9');
        if($gradelevel == 14 || $gradelevel == 15){
            $sheet->setCellValue('D8','Section');
        }
        $sheet->getStyle('D8')->applyFromArray($borderstyle);
        
        if($quarter == 6){
         
            $sheet->getStyle('E8')->applyFromArray($borderstyle);
            $sheet->getStyle('F8')->applyFromArray($borderstyle);
            $sheet->getStyle('G8')->applyFromArray($borderstyle);
            $sheet->getStyle('H8')->applyFromArray($borderstyle);
            $sheet->getStyle('I8')->applyFromArray($borderstyle);
            $sheet->getStyle('J8')->applyFromArray($borderstyle);

            $sheet->getStyle('D8:I8')->getAlignment()->setHorizontal('center');

            $sheet->getColumnDimension('I')->setAutoSize(false);
            $sheet->getColumnDimension('J')->setWidth(15);

            $sheet->setCellValue('E8','1st Sem');
            $sheet->setCellValue('F8','2nd Sem');
            $sheet->setCellValue('G8','Gen. Ave.');
            $sheet->setCellValue('H8','Composite');
            $sheet->mergeCells('I8:J8');
            $sheet->setCellValue('I8','Award');

        }else{

            // $sheet->mergeCells('F9:G9');
            $sheet->setCellValue('E8','Gen. Ave');
            $sheet->getStyle('E8')->applyFromArray($borderstyle);

            $sheet->setCellValue('F8','Composite');
            $sheet->getStyle('F8')->applyFromArray($borderstyle);

            $sheet->mergeCells('G8:H8');
            $sheet->setCellValue('G8','Award');
            $sheet->getStyle('G8:H8')->applyFromArray($borderstyle);
            
            $sheet->mergeCells('J8:K8');
            $sheet->setCellValue('J8','Lowest Grade');
            $sheet->getStyle('J8:K8')->applyFromArray($borderstyle);
            $sheet->getStyle('J8')->getAlignment()->setHorizontal('center');
            
            $sheet->getStyle('D8:H8')->getAlignment()->setHorizontal('center');
        }

       
        
        $malecount = 1;
        $startcellno = 10;

        // return $students;
     
        if(count($students)>0)
        {

            $students = collect($students)->where('student','!=','SUBJECTS')->values();


            if($gradelevel == 14 || $gradelevel == 15){
                $students = collect($students)->where('student','!=','SUBJECTS')->where('semid',$semid)->values();

            }else{
                $students = collect($students)->where('student','!=','SUBJECTS')->values();
            }

            foreach($students as $studentgrade){

                $temp_data =  collect($studentgrade->grades)->where('subjid','G1')->first();


                if($gradelevel == 14 || $gradelevel == 15){
                    if($quarter == 6){
                        $temp_data =  collect($studentgrade->grades)->where('sortid','ZZGENAVE')->first();
                    }else{
                        $temp_data =  collect($studentgrade->grades)->where('subjid','G1')->where('semid',$semid)->first();
                    }
                }else{
                    $temp_data =  collect($studentgrade->grades)->where('subjid','G1')->first();
                }

                $gen_ave = null;
                $composite = null;
                $award = null;
                $lg = null;

                if($quarter == 1){
                    $gen_ave = $temp_data->q1;
                    $composite = $temp_data->q1comp;
                    $award = $temp_data->q1award;
                    $lg = $temp_data->lq1;
                }
                if($quarter == 2){
                    $gen_ave = $temp_data->q2;
                    $composite = $temp_data->q2comp;
                    $award = $temp_data->q2award;
                    $lg = $temp_data->lq2;
                }
                if($quarter == 3){
                    $gen_ave = $temp_data->q3;
                    $composite = $temp_data->q3comp;
                    $award = $temp_data->q3award;
                    $lg = $temp_data->lq3;
                }
                if($quarter == 4){
                    $gen_ave = $temp_data->q4;
                    $composite = $temp_data->q4comp;
                    $award = $temp_data->q4award;
                    $lg = $temp_data->lq4;
                }
                if($quarter == 5){
                    $gen_ave = $temp_data->finalrating;
                    $composite = $temp_data->fcomp;
                    $award = $temp_data->fraward;
                    $lg = $temp_data->lfr;
                }
                if($quarter == 6){
                    $gen_ave = $temp_data->finalrating;
                    $composite = $temp_data->fcomp;
                    $award = $temp_data->fraward;
                    $lg = $temp_data->lfr;
                }
                $studentgrade->temp_gen_ave = $gen_ave;
                $studentgrade->temp_composite = $composite;
                $studentgrade->temp_award = $award;
                $studentgrade->lg = $lg;
            }

            if($quarter == 6){
                foreach(collect($students)->sortByDesc('temp_composite')->values() as $studentgrade)
                {

                    $sheet->getStyle('A'.$startcellno)->getAlignment()->setHorizontal('center');
                    $sheet->getStyle('E'.$startcellno.':M'.$startcellno)->getAlignment()->setHorizontal('center');
                    $sheet->getStyle('D'.$startcellno.':M'.$startcellno)->getAlignment()->setHorizontal('center');

                    $sheet->setCellValue('A'.$startcellno,$malecount);
                    $sheet->mergeCells('B'.$startcellno.':C'.$startcellno);
                    $sheet->setCellValue('B'.$startcellno,$studentgrade->student);
                    $sheet->setCellValue('D'.$startcellno,$studentgrade->sectionname);
                   
                    $sheet->setCellValue('E'.$startcellno,collect($studentgrade->grades)->where('subjid','G1')->where('semid',1)->first()->finalrating);
                    $sheet->setCellValue('F'.$startcellno,collect($studentgrade->grades)->where('subjid','G1')->where('semid',2)->first()->finalrating);

                    $sheet->setCellValue('G'.$startcellno,$studentgrade->temp_gen_ave);
                    $sheet->setCellValue('H'.$startcellno,$studentgrade->temp_composite);


                    $sheet->mergeCells('I'.$startcellno.':J'.$startcellno);
                    $sheet->getStyle('I'.$startcellno)->getNumberFormat()->setFormatCode('0.000'); 
                    $sheet->setCellValue('I'.$startcellno,$studentgrade->temp_award);
                    
                    $sheet->getStyle('A'.$startcellno)->applyFromArray($borderstyle);
                    $sheet->getStyle('B'.$startcellno)->applyFromArray($borderstyle);
                    $sheet->getStyle('C'.$startcellno)->applyFromArray($borderstyle);
                    $sheet->getStyle('D'.$startcellno)->applyFromArray($borderstyle);
                    $sheet->getStyle('E'.$startcellno)->applyFromArray($borderstyle);
                    $sheet->getStyle('F'.$startcellno)->applyFromArray($borderstyle);
                    $sheet->getStyle('G'.$startcellno)->applyFromArray($borderstyle);
                    $sheet->getStyle('H'.$startcellno)->applyFromArray($borderstyle);

                    $sheet->getStyle('I'.$startcellno.':J'.$startcellno)->applyFromArray($borderstyle);
                    
                    $sheet->getStyle('N'.$startcellno)->getAlignment()->setHorizontal('center');
                    $sheet->mergeCells('N'.$startcellno.':O'.$startcellno);
                    $sheet->setCellValue('N'.$startcellno,$studentgrade->lg);
                    $sheet->getStyle('N'.$startcellno.':O'.$startcellno)->applyFromArray($borderstyle);
                   
                    $startcellno+=1;
                    $malecount+=1;
                }
            }else{
                foreach(collect($students)->sortByDesc('temp_composite')->values() as $studentgrade)
                {
                    $sheet->getStyle('A'.$startcellno)->getAlignment()->setHorizontal('center');
                    $sheet->getStyle('E'.$startcellno.':H'.$startcellno)->getAlignment()->setHorizontal('center');
                    $sheet->setCellValue('A'.$startcellno,$malecount);
                    $sheet->mergeCells('B'.$startcellno.':C'.$startcellno);
                    $sheet->setCellValue('B'.$startcellno,$studentgrade->student);
                    $sheet->setCellValue('D'.$startcellno,$studentgrade->sectionname);
                        
                    $sheet->getStyle('D'.$startcellno)->getAlignment()->setHorizontal('center');
                    $sheet->setCellValue('E'.$startcellno,$studentgrade->temp_gen_ave);
                    $sheet->setCellValue('F'.$startcellno,$studentgrade->temp_composite);
                    $sheet->mergeCells('G'.$startcellno.':H'.$startcellno);
                    $sheet->getStyle('F'.$startcellno)->getNumberFormat()->setFormatCode('0.000'); 
                    $sheet->setCellValue('G'.$startcellno,$studentgrade->temp_award);
                    
                    $sheet->getStyle('A'.$startcellno)->applyFromArray($borderstyle);
                    $sheet->getStyle('B'.$startcellno)->applyFromArray($borderstyle);
                    $sheet->getStyle('C'.$startcellno)->applyFromArray($borderstyle);
                    $sheet->getStyle('D'.$startcellno)->applyFromArray($borderstyle);
                    $sheet->getStyle('E'.$startcellno)->applyFromArray($borderstyle);
                    $sheet->getStyle('F'.$startcellno)->applyFromArray($borderstyle);
                    $sheet->getStyle('G'.$startcellno.':H'.$startcellno)->applyFromArray($borderstyle);
                    
                    $sheet->getStyle('J'.$startcellno)->getAlignment()->setHorizontal('center');
                    $sheet->mergeCells('J'.$startcellno.':K'.$startcellno);
                    $sheet->setCellValue('J'.$startcellno,$studentgrade->lg);
                    $sheet->getStyle('J'.$startcellno.':K'.$startcellno)->applyFromArray($borderstyle);
                   
                    $startcellno+=1;
                    $malecount+=1;
                }
            }

            
        }

        $section_detail = DB::table('sections')
                ->leftJoin('teacher',function($join){
                    $join->on('sections.teacherid','=','teacher.id');
                })
                ->join('gradelevel',function($join){
                    $join->on('sections.levelid','=','gradelevel.id');
                })
                ->where('sections.id',$section)
                ->where('sections.deleted',0)
                ->select('lastname','firstname','sectionname','levelname','levelid')
                ->first();
            
        $schoolyear_detail = DB::table('sy')->where('id',$syid)->first();
        $version = "V5";
        
        $spreadsheet->getActiveSheet()->getPageSetup()->setPrintArea('A1:H'.$startcellno);
        $spreadsheet->getActiveSheet()->getPageMargins()->setTop(0);
        $spreadsheet->getActiveSheet()->getPageMargins()->setRight(0);
        $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0);
        $spreadsheet->getActiveSheet()->getPageMargins()->setBottom(0);

        $spreadsheet->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
        $spreadsheet->getActiveSheet()->getPageSetup()->setVerticalCentered(false);
        $spreadsheet->getActiveSheet()->getPageSetup()->setFitToPage(true);
        
        $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
        $spreadsheet->getActiveSheet()->getPageSetup()->setFitToHeight(0);
        $spreadsheet->getActiveSheet()->getSheetView()->setZoomScale(85);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="'.'SR '.str_replace("GRADE ","_G",$gradelevelinfo->levelname).str_replace(" ","_",$sectioninfo->sectionname).'_SY'.$schoolyear_detail->sydesc.'_'.\Carbon\Carbon::now('Asia/Manila')->isoFormat('YYYYMMDDThhMMSS').$version.'.xlsx');
        $writer->save("php://output");
    }

    public function finalcomposite(Request $request)
    {
        
        // return "This page is not yet available";
        
        $semid = $request->get('semid');
        $syid = $request->get('sy');
        $gradelevel = $request->get('gradelevel');
        $section = $request->get('section');
        $quarter = $request->get('quarter');
        $strandid = $request->get('strand');
        
     
        $students = DB::table('sh_enrolledstud')
                        ->where('sh_enrolledstud.levelid',$gradelevel)
                        ->where('sh_enrolledstud.sectionid',$section)
                        ->where('sh_enrolledstud.syid',$syid)
                        ->where('sh_enrolledstud.deleted',0)
                        ->join('studinfo',function($join){
                            $join->on('sh_enrolledstud.studid','=','studinfo.id');
                            $join->where('studinfo.deleted',0);
                        })
                         ->orderBy('gender','desc')
                          ->orderBy('lastname')
                          ->orderBy('studentname','asc')
                        ->select(
                            'sh_enrolledstud.sectionid as ensectid',
                            'sh_enrolledstud.studid as id',
                            'sh_enrolledstud.strandid',
                            'sh_enrolledstud.semid',
                            'lastname',
                            'firstname',
                            'middlename',
                            'lrn',
                            'dob',
                            'gender',
                            'studid',
                            'sh_enrolledstud.levelid',
                            'sh_enrolledstud.syid',
                            'sh_enrolledstud.strandid',
                            'sh_enrolledstud.sectionid',
                             DB::raw("CONCAT(studinfo.lastname,' ',studinfo.firstname) as student"),
                               DB::raw("CONCAT(studinfo.lastname,' ',studinfo.firstname) as studentname")
                        )
                        ->get();
                        
        $students = collect($students)->unique('studid')->values();
        

        foreach($students as $item){
            
            $enrollment = DB::table('sh_enrolledstud')
                               
                                ->join('gradelevel',function($join){
                                    $join->on('sh_enrolledstud.levelid','=','gradelevel.id');
                                    $join->where('gradelevel.deleted',0);
                                })
                                ->join('sections',function($join){
                                    $join->on('sh_enrolledstud.sectionid','=','sections.id');
                                    $join->where('sections.deleted',0);
                                })
                                 ->join('studinfo',function($join){
                                    $join->on('sh_enrolledstud.studid','=','studinfo.id');
                                    $join->where('gradelevel.deleted',0);
                                })
                                 ->where('sh_enrolledstud.studid',$item->studid)
                                ->where('sh_enrolledstud.deleted',0)
                                ->select(
                                    'sh_enrolledstud.sectionid as ensectid',
                                    'acadprogid',
                                    'sh_enrolledstud.studid as id',
                                    'sh_enrolledstud.strandid',
                                    'sh_enrolledstud.semid',
                                    'lastname',
                                    'firstname',
                                    'middlename',
                                    'lrn',
                                    'dob',
                                    'gender',
                                    'levelname',
                                    'sh_enrolledstud.levelid',
                                    'sh_enrolledstud.syid',
                                    'sh_enrolledstud.strandid',
                                    'sh_enrolledstud.sectionid',
                                    'sections.sectionname as ensectname'
                                )
                                // ->distinct('sh_enrolledstud.syid')
                                ->get();
                                
            $enrollment =  collect($enrollment)->unique('syid')->values();
            
            foreach($enrollment as $enitem){
                
                
                $enlevel = $enitem->levelid;
                $studid = $item->studid;
                $ensyid = $enitem->syid;
                $enstrand = $enitem->strandid;
                $ensection = $enitem->sectionid;
                
                // if($ensyid == 2){
                    
                    
                //     $gradesv4 = \App\Models\Principal\GenerateGrade::reportCardV5($enitem, true, 'sf9',$ensyid);    
                       
                //     $grades = $gradesv4;
                //     $temp_grades = array();
            
                  
                //     foreach($grades as $key=>$gradeitem){
                //         $checkStrand = DB::table('sh_subjstrand')
                //                             ->where('subjid',$gradeitem->subjid)
                //                             ->where('deleted',0)
                //                             ->get();
                //         if( count($checkStrand) > 0 ){
                //             $check_same_strand = collect($checkStrand)->where('strandid',$enstrand)->count();
                //             if( $check_same_strand == 0){
                //                 unset($grades[$key]); 
                //             }
                //         }
                //     }
                    
                //     $semid = 1;
                //     $gen_ave_for_sem = 0;
                //     $grades =  collect($grades)->unique('subjectcode')->values();
                
                //     foreach(collect($grades)->where('semid',1)->values() as $gradeitem){
                //         $with_final_rating = $gradeitem->quarter1 != null && $gradeitem->quarter2 != null ? true : false;
                //         $average = $with_final_rating ? ($gradeitem->quarter1 + $gradeitem->quarter2 ) / 2 : '';
                //         $gen_ave_for_sem += $average != '' ? number_format($average) : 0;
                //     }
                
                //     $gen_ave = number_format($gen_ave_for_sem / collect($grades)->where('semid',1)->count() );
                  
                //     array_push($temp_grades,(object)[
                //         'semid'=>1,
                //         'finalrating'=>$gen_ave
                //     ]);
                    
                //     $semid = 2;
                //     $gen_ave_for_sem = 0;
                //     $grades =  collect($grades)->values();
       
                //     foreach(collect($grades)->where('semid',2)->values() as $gradeitem){
                //         $average = ( $gradeitem->quarter1 + $gradeitem->quarter2 )  / 2;
                //         $gen_ave_for_sem += $average != '' ? number_format($average) : 0;
                //     }
                //     $gen_ave = number_format($gen_ave_for_sem / collect($grades)->where('semid',2)->count() );
                    
                //     array_push($temp_grades,(object)[
                //         'semid'=>2,
                //         'finalrating'=>$gen_ave
                //     ]);
                    
                //     $enitem->grades = $temp_grades;
                   
                // }else{
                     $studgrades = \App\Http\Controllers\SuperAdminController\StudentGradeEvaluation::sf9_grades( $enlevel,$studid,$ensyid,$enstrand,null,$ensection);
                     $enitem->grades = collect($studgrades)->where('subjid','G1')->values();
                // }
               
                
                
            }
            
            $item->enrollment = $enrollment;
          
                                
        }
        
      
        //$students = \App\Http\Controllers\TeacherControllers\TeacherGradingV4::get_student_data($request);
      
  
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();;
        $sheet = $spreadsheet->getActiveSheet();
        $borderstyle = [
            'borders' => [
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
                'left' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
                'right' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ]
        ];
        $font_bold = [
                'font' => [
                    'bold' => true,
                ]
            ];

        $schoolinfo = Db::table('schoolinfo')->first();

        $gradelevelinfo = DB::table('gradelevel')
            ->select('gradelevel.*','academicprogram.acadprogcode')
            ->join('academicprogram','gradelevel.acadprogid','=','academicprogram.id')
            ->where('gradelevel.id', $gradelevel)
            ->first();
            
        if(strtolower($gradelevelinfo->acadprogcode) == 'college')
        {
            
        }else{
            $sectioninfo = Db::table('sections')
                ->where('id', $section)
                ->first();
        }

        $schoolyearinfo = DB::table('sy')
            ->where('id', $syid)
            ->first();

        foreach(range('A','H') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        $sheet->getColumnDimension('B')->setAutoSize(false);
        $sheet->getColumnDimension('B')->setWidth(15);
        
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(base_path().'/public/assets/images/department_of_Education.png');
        // $drawing->setPath(base_path().'/public/'.$schoolinfo->picurl);
        $drawing->setHeight(80);
        $drawing->setWorksheet($sheet);
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(20);
        $drawing->setOffsetY(20);

        $drawing->getShadow()->setVisible(true);
        $drawing->getShadow()->setDirection(45);

        
        $sheet->mergeCells('D1:G2');
        $sheet->setCellValue('D1','Grading Sheet');
        $sheet->getStyle('D1')->applyFromArray($font_bold);
        $sheet->getStyle('D1')->getAlignment()->setHorizontal('center');

        $sheet->setCellValue('C4','REGION');
        $sheet->setCellValue('C5','SCHOOL NAME');

        $sheet->getStyle('C4:C5')->getAlignment()->setHorizontal('right');
        $sheet->setCellValue('D4',$schoolinfo->regiontext);
        $sheet->getStyle('D4')->applyFromArray($borderstyle);
        $sheet->getStyle('D4')->getAlignment()->setHorizontal('center');
        $sheet->setCellValue('E4','DIVISION');
        $sheet->getStyle('E4')->getAlignment()->setHorizontal('right');
        $sheet->setCellValue('F4',$schoolinfo->divisiontext);
        $sheet->getStyle('F4')->applyFromArray($borderstyle);
        $sheet->getStyle('F4')->getAlignment()->setHorizontal('center');

        $sheet->mergeCells('H1:I1');
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(base_path().'/public/assets/images/deped_logo.png');
  
        $drawing->setWidth(110);
        $drawing->setWorksheet($sheet);
        $drawing->setCoordinates('H1');
        $drawing->setOffsetX(20);
        $drawing->setOffsetY(15);

        $drawing->getShadow()->setVisible(true);
        $drawing->getShadow()->setDirection(45);
        


        $sheet->mergeCells('D5:F5');
        $sheet->setCellValue('D5',$schoolinfo->schoolname);
        $sheet->getStyle('D5:F5')->applyFromArray($borderstyle);
        $sheet->getStyle('D5')->getAlignment()->setHorizontal('center');
        $sheet->setCellValue('G5','SCHOOL ID');
        $sheet->getStyle('G5')->getAlignment()->setHorizontal('right');
        $sheet->mergeCells('H5:I5');
        $sheet->setCellValue('H5',$schoolinfo->schoolid);
        $sheet->getStyle('H5:I5')->applyFromArray($borderstyle);
        $sheet->getStyle('H5')->getAlignment()->setHorizontal('center');

        
        $sheet->mergeCells('A7:A9');
        $sheet->getStyle('A7:A9')->applyFromArray($borderstyle);

        
        $sheet->mergeCells('B7:C9');
        $sheet->getStyle('B7:C9')->applyFromArray($borderstyle);
        $sheet->getStyle('B7:C9')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('B7:C9')->getAlignment()->setVertical('center');
        $sheet->setCellValue('B7','LEARNERS\' NAMES');

        $sheet->mergeCells('D7:F7');
        $sheet->getStyle('D7:F7')->applyFromArray($borderstyle);
        $sheet->setCellValue('D7','GRADE & SECTION: '.$gradelevelinfo->levelname.' - '.$sectioninfo->sectionname);

        $sheet->mergeCells('G7:I7');
        $sheet->getStyle('G7:I7')->applyFromArray($borderstyle);
        $sheet->setCellValue('G7','SCHOOL YEAR: '.$schoolyearinfo->sydesc);

        $sheet->mergeCells('D8:F8');
        $sheet->getStyle('D8:F8')->applyFromArray($borderstyle);
        
        $studentgrades = array();
        
        if(count($studentgrades)>0)
        {
            if(count($studentgrades[0]->subjectgrades)> 0)
            {
                $sheet->setCellValue('D8','TEACHER: '.$studentgrades[0]->subjectgrades[0]->teachername);
            }else{
                $sheet->setCellValue('D8','TEACHER:');
            }

        }else{
            $sheet->setCellValue('D8','TEACHER:');
        }
        $sheet->mergeCells('G8:I8');
        $sheet->getStyle('G8:I8')->applyFromArray($borderstyle);
        if(count($studentgrades)>0)
        {
            if(count($studentgrades[0]->subjectgrades)> 0)
            {
                $subjectname = "";
                $sheet->setCellValue('G8','SUBJECT: '."");
    
            }else{
                $subjectname = "";
                $sheet->setCellValue('G8','SUBJECT: ');
    
            }
        }else{
            $subjectname = "";
            $sheet->setCellValue('G8','SUBJECT: ');
        }

        if(strtolower($gradelevelinfo->acadprogcode) == 'shs')
        {
            $sheet->setCellValue('D9','First Semester');
            $sheet->getStyle('D9')->applyFromArray($borderstyle);
            
            $sheet->setCellValue('E9','Second Semester');
            $sheet->getStyle('E9')->applyFromArray($borderstyle);
    
            $sheet->setCellValue('F9','First Semester');
            $sheet->getStyle('F9')->applyFromArray($borderstyle);
    
            $sheet->setCellValue('G9','Second Semester');
            $sheet->getStyle('G9')->applyFromArray($borderstyle);
    
        }else{
            $sheet->setCellValue('D9','1st Quarter');
            $sheet->getStyle('D9')->applyFromArray($borderstyle);
            
            $sheet->setCellValue('E9','2nd Quarter');
            $sheet->getStyle('E9')->applyFromArray($borderstyle);
    
            $sheet->setCellValue('F9','3rd Quarter');
            $sheet->getStyle('F9')->applyFromArray($borderstyle);
    
            $sheet->setCellValue('G9','4th Quarter');
            $sheet->getStyle('G9')->applyFromArray($borderstyle);
        }

        $sheet->setCellValue('H9','FINAL GRADE');
        $sheet->getStyle('H9')->applyFromArray($borderstyle);

        $sheet->setCellValue('I9','COMPOSITE');
        $sheet->getStyle('I9')->applyFromArray($borderstyle);

        $sheet->getStyle('D9:I9')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('D7:I9')->getAlignment()->setVertical('center');
        
        
        $sheet->getStyle('A10:I10')->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setARGB('DDDDDD');

        $studentgrades = collect($studentgrades)->sortBy('student')->values()->all();
        $startcellno = 11;
        $malecount = 1;
        $femalecount = 1;


        $sheet->mergeCells('B10:C10');
        $sheet->getStyle('B10:C10')->applyFromArray($borderstyle);
        $sheet->setCellValue('B10','MALE');

        if(strtolower($gradelevelinfo->acadprogcode) == 'shs')
        {
            $sheet->mergeCells('D10:E10');
            $sheet->mergeCells('F10:G10');
        }

        foreach(range('D','I') as $columnID) {
            $sheet->getStyle($columnID.'10')->applyFromArray($borderstyle);
        }
        
        $students = collect($students)->where('student','!=','SUBJECTS')->values();
        
        
        if(count($students)>0)
        {
            foreach($students as $studentgrade)
            {
            
                if(strtolower($studentgrade->gender) == 'male')
                {
                    
               
                    $sheet->mergeCells('B'.$startcellno.':C'.$startcellno);
                    $sheet->setCellValue('A'.$startcellno,$malecount);
                    $sheet->getStyle('A'.$startcellno)->applyFromArray($borderstyle);
                    $sheet->setCellValue('B'.$startcellno,$studentgrade->student);
                    $sheet->getStyle('B'.$startcellno.':C'.$startcellno)->applyFromArray($borderstyle);
                    
                    foreach(range('D','I') as $columnID) {
                        $sheet->getStyle($columnID.$startcellno)->applyFromArray($borderstyle);
                    }
                    
                    $complete = 0;
                    $totalgrade = 0;
                    
 
                    $student_grade = DB::table('sf10')
                                        ->join('sf10grades_senior',function($join){
                                            $join->on('sf10.id','=','sf10grades_senior.headerid');
                                            $join->where('sf10grades_senior.deleted',0);
                                        })
                                        ->where('sf10.studid',$studentgrade->studid)
                                        ->where('sf10grades_senior.deleted',0)
                                        ->select('semid','q1','q2','q3','q4')
                                        ->get();
                                        
                    
                           
                    $q1 = collect($students)->where('studid',$studentgrade->studid)->values();
                    $q2 = collect($students)->where('studid',$studentgrade->studid)->values();
                    
                    $compq1 = 0;
                    $compq2 = 0;
                    $compq3 = 0;
                    $compq4 = 0;
                    
                    $fincomp_grade = 0;
                    foreach($studentgrade->enrollment as $enitem){
                        foreach($enitem->grades as $entimgrade){
                             $fincomp_grade += $entimgrade->finalrating ;
                        }
                    }

                    //grade 11 enrollment
                    $g11_enrollment = collect($studentgrade->enrollment)->where('levelid',14)->values();
                 
                    foreach($g11_enrollment as $en_item){
                        foreach($en_item->grades as $grade_item){
                            
                            if($grade_item->semid == 1){
                                $sheet->setCellValue('D'.$startcellno,$grade_item->finalrating);
                            }else{
                                $sheet->setCellValue('E'.$startcellno,$grade_item->finalrating);
                            }
                        }
                    }
                    
                    //grade 12 enrollment
                    $g12_enrollment = collect($studentgrade->enrollment)->where('levelid',15)->values();
                 
                    foreach($g12_enrollment as $en_item){
                        foreach($en_item->grades as $grade_item){
                            
                            if($grade_item->semid == 1){
                                $sheet->setCellValue('F'.$startcellno,$grade_item->finalrating);
                            }else{
                                $sheet->setCellValue('G'.$startcellno,$grade_item->finalrating);
                            }
                        }
                    }
                    
                    $sheet->setCellValue('H'.$startcellno,number_format( ( $fincomp_grade ) / 4 ));
                    $sheet->setCellValue('I'.$startcellno,number_format( ( ( $fincomp_grade ) / 4 ) , 3));
                    
                    $sheet->getStyle('D'.$startcellno.':I'.$startcellno)->getAlignment()->setHorizontal('center');
                    $startcellno+=1;
                    $malecount+=1;
                    
                }
            }
        }
        
        $sheet->mergeCells('B'.$startcellno.':C'.$startcellno);
        $sheet->getStyle('B'.$startcellno.':C'.$startcellno)->applyFromArray($borderstyle);
        $sheet->setCellValue('B'.$startcellno,'FEMALE');
        
        if(strtolower($gradelevelinfo->acadprogcode) == 'shs')
        {
            $sheet->mergeCells('D'.$startcellno.':E'.$startcellno);
            $sheet->mergeCells('F'.$startcellno.':G'.$startcellno);
        }
        


        $sheet->getStyle('A'.$startcellno.':I'.$startcellno)->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setARGB('DDDDDD');
        foreach(range('D','I') as $columnID) {
            $sheet->getStyle($columnID.$startcellno)->applyFromArray($borderstyle);
        }
        
        $startcellno+=1;
        
         $students = collect($students)->where('student','!=','SUBJECTS')->values();
         
        if(count($students)>0)
        {
            foreach($students as $studentgrade)
            {
                
         
                if(strtolower($studentgrade->gender) == 'female')
                {
                    
                    $sheet->mergeCells('B'.$startcellno.':C'.$startcellno);
                    $sheet->setCellValue('A'.$startcellno,$malecount);
                    $sheet->getStyle('A'.$startcellno)->applyFromArray($borderstyle);
                    $sheet->setCellValue('B'.$startcellno,$studentgrade->student);
                    $sheet->getStyle('B'.$startcellno.':C'.$startcellno)->applyFromArray($borderstyle);
                    
                    foreach(range('D','I') as $columnID) {
                        $sheet->getStyle($columnID.$startcellno)->applyFromArray($borderstyle);
                    }
                    
                    $complete = 0;
                    $totalgrade = 0;
                    
                    $student_grade = DB::table('sf10')
                                        ->join('sf10grades_senior',function($join){
                                            $join->on('sf10.id','=','sf10grades_senior.headerid');
                                            $join->where('sf10grades_senior.deleted',0);
                                        })
                                        ->where('sf10.studid',$studentgrade->studid)
                                        ->where('sf10grades_senior.deleted',0)
                                        ->select('semid','q1','q2','q3','q4')
                                        ->get();
                                        
                    
                           
                    $q1 = collect($students)->where('studid',$studentgrade->studid)->values();
                    $q2 = collect($students)->where('studid',$studentgrade->studid)->values();
                    
                    $compq1 = 0;
                    $compq2 = 0;
                    $compq3 = 0;
                    $compq4 = 0;
                   
                    $fincomp_grade = 0;
                    foreach($studentgrade->enrollment as $enitem){
                        foreach($enitem->grades as $entimgrade){
                             $fincomp_grade += $entimgrade->finalrating ;
                        }
                    }
                    //  $sheet->setCellValue('D'.$startcellno,$studentgrade->enrollment[0]->grades[0]->finalrating);
                    // $sheet->setCellValue('E'.$startcellno,$studentgrade->enrollment[0]->grades[1]->finalrating);
                    // if(count($studentgrade->enrollment) == 2){
                    //     $sheet->setCellValue('F'.$startcellno,$studentgrade->enrollment[1]->grades[0]->finalrating);
                    //     $sheet->setCellValue('G'.$startcellno,$studentgrade->enrollment[1]->grades[1]->finalrating);
                    // }
                    
                    //grade 11 enrollment
                    $g11_enrollment = collect($studentgrade->enrollment)->where('levelid',14)->values();
                 
                    foreach($g11_enrollment as $en_item){
                        foreach($en_item->grades as $grade_item){
                            
                            if($grade_item->semid == 1){
                                $sheet->setCellValue('D'.$startcellno,$grade_item->finalrating);
                            }else{
                                $sheet->setCellValue('E'.$startcellno,$grade_item->finalrating);
                            }
                        }
                    }
                    
                    //grade 12 enrollment
                    $g12_enrollment = collect($studentgrade->enrollment)->where('levelid',15)->values();
                 
                    foreach($g12_enrollment as $en_item){
                        foreach($en_item->grades as $grade_item){
                            
                            if($grade_item->semid == 1){
                                $sheet->setCellValue('F'.$startcellno,$grade_item->finalrating);
                            }else{
                                $sheet->setCellValue('G'.$startcellno,$grade_item->finalrating);
                            }
                        }
                    }
                    
                    $sheet->setCellValue('H'.$startcellno,number_format( ( $fincomp_grade ) / 4 ));
                    $sheet->setCellValue('I'.$startcellno,number_format( ( ( $fincomp_grade ) / 4 ) , 3));
                    
                    $sheet->getStyle('D'.$startcellno.':I'.$startcellno)->getAlignment()->setHorizontal('center');
                    $startcellno+=1;
                    $malecount+=1;
                }
            }
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="'.$gradelevelinfo->levelname.' '.$sectioninfo->sectionname.' - '.$subjectname.'.xlsx"');
        $writer->save("php://output");
    }

}
