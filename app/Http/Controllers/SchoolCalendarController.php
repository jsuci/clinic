<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;
class SchoolCalendarController extends Controller
{
    public function show(){


        $acad_prog = DB::table('academicprogram')
            ->select(
                'id',
                'progname as text'
            )
            ->get();

        $users= DB::table('users')
            ->select(
                'id',
                'name as text'
            )
            ->get();

        $college_courses = DB::table('college_courses')
            ->where('deleted', 0)
            ->select(
                'id',
                'courseabrv as text'
            )
            ->get(); 


        $gradelevel = DB::table('gradelevel')
            ->where('deleted', 0)
            ->select(
                'id',
                'levelname as text'
            )
            ->get();   

        $college_colleges = DB::table('college_colleges')
            ->where('deleted', 0)
            ->select(
                'id',
                'collegeabrv as text'
            )
            ->get(); 

        $faculty = DB::table('school_calendarfaculty')
            ->where('deleted', 0)
            ->select(
                'id',
                'name as text'
            )
            ->get();

        $activeSY = DB::table('sy')
            ->where('isactive', 1)
            ->first();
        $sy = DB::table('sy')
            ->get();

        $sem = DB::table('semester')
            ->where('isactive', 1)
            ->first();
            
        return view('superadmin.pages.school-calendar.school-calendar',[

            'acad_prog'=>$acad_prog,
            'gradelevel'=>$gradelevel,
            'courses'=>$college_courses,
            'colleges'=>$college_colleges,
            'faculty'=>$faculty,
            'activeSY'=>$activeSY,
            'sy'=>$sy,
            'sem'=>$sem
        ]);
        
    }

    public function getall_event($type, $syid){


        if($type == 7 || $type == 9){

            $events = DB::table('school_calendar')
            ->where('deleted', 0)
            ->where('syid', $syid)
            ->whereIn('type', [0, 1])
            ->select(
                'id',
                'title',
                'start',
                'end',
                'allDay'
            )
            ->get();


            foreach ($events as $value) {
                
                if($value->allDay == 1){

                    $value->allDay = true;

                }else{

                    $value->allDay = false;
                }

                return response()->json($events);

            }
        }else{


            $events = DB::table('school_calendar')
            ->where('deleted', 0)
            ->where('syid', $syid)
            // ->where('type', '!=', 7)
            // ->where('type', '!=', 9)
            ->select(
                'id',
                'title',
                'start',
                'end',
                'allDay'
            )
            ->get();


            foreach ($events as $value) {
                
                if($value->allDay == 1){

                    $value->allDay = true;

                }else{

                    $value->allDay = false;
                }

                return response()->json($events);

            }
            
        }
                

    }

    public function get_event(Request $request){

        $event = DB::table('school_calendar')
            ->where('id', $request->id)
            ->where('deleted', 0)
            ->get();

        return $event;

    }

    public function add_event(Request $request){

        $colleges = null;
        $courses = null;

        if($request->collegeid != null){

            $colleges =  implode(" ",$request->collegeid);
        }
        if($request->courseid != null){

            $courses =  implode(" ",$request->courseid);
        }
        


        DB::table('school_calendar')
            ->insert([

                "start" => $request->start, 
                "end" => $request->end,
                "title" => $request->event_desc, 
                "venue"=> $request->act_venue,
                "involve"=> $request->involve,
                'isnoclass'=> $request->isNoClass,
                'gradelevelid'=> $request->gradelevelid,
                'acadprogid'=> $request->acadprogid,
                'courseid'=> $courses,
                'collegeid'=> $colleges,
                'type'=> $request->type,
                'syid'=> $request->syid
                
        ]);
    
        return array (
            (object)[

            'status'=>200,
            'statusCode'=>"success",
            'message'=>'Event Added Successfully!'

        ]); 
 
    }
    
    public function update_event(Request $request){

        if(strlen($request->start) == 10){

            DB::table('school_calendar')
            ->where('id', $request->id)
            ->update([

                "start" => $request->start, 
                "end" => $request->end, 
                "allDay" => 1
            ]);

        }else{

            DB::table('school_calendar')
            ->where('id', $request->id)
            ->update([

                "start" => $request->start, 
                "end" => $request->end, 
                "allDay" => 0
            ]);
        }

        
    }

    public function update_event_details(Request $request){

        $colleges = null;
        $courses = null;

        if($request->collegeid != null){

            $colleges =  implode(" ",$request->collegeid);
        }
        if($request->courseid != null){

            $courses =  implode(" ",$request->courseid);
        }

        DB::table('school_calendar')
            ->where('id', $request->id)
            ->update([

                "title" => $request->event_desc, 
                "venue"=> $request->act_venue,
                "involve"=> $request->involve,
                'isnoclass'=> $request->isNoClass,
                'gradelevelid'=> $request->gradelevelid,
                'acadprogid'=> $request->acadprogid,
                'courseid'=> $courses,
                'collegeid'=> $colleges,
                'type'=> $request->type
            ]);
        
        $event = DB::table('school_calendar')
            ->where('id', $request->id)
            ->where('deleted', 0)
            ->get();
        
        return array (
            (object)[

            'status'=>200,
            'statusCode'=>"success",
            'message'=>'Event Updated Successfully!',
            'event'=>$event

        ]); 
    }


    public function delete_event(Request $request){

        $event = DB::table('school_calendar')
            ->where('id', $request->id)
            ->update([

                "deleted" => 1
            ]);

        return array (
            (object)[

            'status'=>200,
            'statusCode'=>"success",
            'message'=>'Event Removed Successfully!'

        ]); 
    }

    public function get_select2_gradelvl(Request $request){

        if($request->acad_prog == 2){

            $gradelevel = $this->gradelevelQuery(2);

        }

        else if($request->acad_prog == 3){

            $gradelevel = $this->gradelevelQuery(3);

        }

        else if($request->acad_prog == 4){

            $gradelevel = $this->gradelevelQuery(4);

        }

        else if($request->acad_prog == 5){

            $gradelevel= $this->gradelevelQuery(5);
        }

        else if($request->acad_prog == 6){

            $gradelevel = $this->gradelevelQuery(6);
        }

        else{

            $gradelevel = $this->gradelevelQuery(7);
        }

        return response()->Json([

            'gradelevel'=> $gradelevel

        ]);
          
    }

    public function get_select2_faculty(Request $request){

        $faculty = DB::table('school_calendarfaculty')
        ->where('deleted', 0)
        ->select(
            'id',
            'name as text'
        )
        ->get();

        return response()->Json([

            'faculty'=>$faculty

        ]);
          
    }
    
    public function add_faculty(Request $request){

        DB::table('school_calendarfaculty')
            ->insert([

                "name" => $request->name,  
        ]);
    
        return array (
            (object)[

            'status'=>200,
            'statusCode'=>"success",
            'message'=>'Succesfully Added Faculty!'

        ]); 
 
    }

    public function generatePDF($syid){

        $events = DB::table('school_calendar')
            ->where('deleted', 0)
            ->where('syid', $syid)
            ->get();

        $schoolinfo = DB::table('schoolinfo')
            ->first();
            
        $schoolyear = DB::table('sy')
            ->where('id', $syid)
            ->first();


        $pdf = PDF::loadView('superadmin.pages.printable.schoolcalendar-pdf', compact('events', 'schoolinfo', 'schoolyear'))->setPaper('legal', 'portrait');
        
        $pdf->getDomPDF()->set_option("enable_php", true)->set_option("DOMPDF_ENABLE_CSS_FLOAT", true);

        return $pdf->stream();

    }

    public function generateExcel($syid){

        //initialize

        $events = DB::table('school_calendar')
            ->where('deleted', 0)
            ->where('syid', $syid)
            ->get();

        $schoolinfo = DB::table('schoolinfo')
            ->first();
            
        $schoolyear = DB::table('sy')
            ->where('id', $syid)
            ->first();

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load("schoolcalendar/school-calendar-temp.xlsx");


        //

        
        $sheet = $spreadsheet->setActiveSheetIndex(0);
        $sheet->setCellValue('A3', $schoolinfo->schoolname);
        $sheet->setCellValue('A4', $schoolinfo->address);
        $sheet->setCellValue('F7', "SY ".$schoolyear->sydesc);

        $months = [
            "January",
            "Febuary",
            "March",
            "April",
            "May",
            "June",
            "July",
            "August",
            "September",
            "October",
            "November",
            "December",
        ];

        $count = 11;
        $itemCounter = 0;
        $monthly = 12;
        for ($i=0; $i < count($months); $i++) {
            
            $sheet->setCellValue('C'.($count+$itemCounter), $months[$i]);
            $sheet->getStyle('C'.($count+$itemCounter))->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('1c1d1f');

            $sheet->getStyle('C'.($count+$itemCounter))->getFont()->getColor()->setARGB('ffffff');
            $sheet->mergeCells('C'.($count+$itemCounter).':H'.($count+$itemCounter));


            foreach ($events as $event) {
            
                // $count++;
    
                $month = date_create($event->start); 
                $date = date_create($event->start); 
                $dayS = date_create($event->start); 
                $dayE = date_create($event->end); 
                    
                $startday = date_format(date_create($event->start),"d"); 
                $endday = date_format(date_create($event->end),"d"); 
                $themonth = date_format(date_create($event->start),"M"); 

                if( date_format($month,"m") == ($i+1)){
    
                    if($monthly >= 11){


                        if($startday == $endday){

                            $sheet->setCellValue('C'.($monthly+$itemCounter), date_format($dayS,"d"));
                            $sheet->setCellValue('D'.($monthly+$itemCounter), date_format($dayS,"D"));
        
                        }else{
                            $sheet->setCellValue('C'.($monthly+$itemCounter), $startday.'-'.$endday);
                            $sheet->setCellValue('D'.($monthly+$itemCounter), date_format($dayS,"D")."-".date_format($dayE,"D"));
                        }
                        
                        $sheet->setCellValue('E'.($monthly+$itemCounter), $event->title);
                        $sheet->setCellValue('F'.($monthly+$itemCounter), $event->venue);
                        $sheet->setCellValue('G'.($monthly+$itemCounter), $event->involve);
                        // $sheet->setCellValue('H'.($monthly+$itemCounter), $themonth);
    
                    }

                    $itemCounter++;
                }

                            
            }

            $monthly++;
            $count++;


        }
        

        //


        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="calendar-'.$schoolyear->sydesc.'.xlsx"');
        $writer->save("php://output");
        exit();
        
    }

    public function edit_faculty(Request $request){

        DB::table('school_calendarfaculty')
            ->where('id', $request->id)
            ->update([
                'name' => $request->name, 
            ]);
    
        return array (
            (object)[

            'status'=>200,
            'statusCode'=>"success",
            'message'=>'Succesfully Saved!',
            'id'=>$request->id

        ]); 
 
    }

    public function delete_faculty(Request $request){

        DB::table('school_calendarfaculty')
            ->update([

                "name" => $request->name, 
                
        ]);
    
        return array (
            (object)[

            'status'=>200,
            'statusCode'=>"success",
            'message'=>'Succesfully Deleted Faculty!'

        ]); 
 
    }

    public static function gradelevelQuery($id){

        $gradelevel = DB::table('gradelevel')
        ->where('acadprogid', $id)
        ->where('deleted', 0)
        ->select(
            'id',
            'levelname as text'
        )
        ->get();


        return $gradelevel;
    }

    

}
