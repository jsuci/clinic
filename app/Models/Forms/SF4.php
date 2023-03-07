<?php

namespace App\Models\Forms;

use Illuminate\Database\Eloquent\Model;
use DB;

class SF4 extends Model
{
    
    public static function generate($year = null, $month = null, $days = null, $syid = null){

        $month = \Carbon\Carbon::create(null , $month)->isoFormat('MM');

        $sections = DB::table('sections')
                        ->join('gradelevel',function($join){
                            $join->on('sections.levelid','=','gradelevel.id');
                            $join->where('gradelevel.deleted',0);
                        })
                        ->leftJoin('sectiondetail',function($join) use($syid){
                            $join->on('sections.id','=','sectiondetail.sectionid');
                            $join->where('sectiondetail.deleted','0');
                            $join->where('syid',$syid);
                        })
                        ->leftJoin('teacher',function($join){
                            $join->on('sectiondetail.teacherid','=','teacher.id');
                            $join->where('teacher.deleted','0');
                            $join->where('teacher.isactive','1');
                        })
                        ->where('sections.deleted',0)
                        ->select('sections.id as sectionid','sectionname','acadprogid','levelname','levelid','sortid','lastname','firstname')
                        ->orderBy('sortid')
                        ->get();

        foreach($sections as $item){
            if($item->acadprogid == 5){
                $item->registered = self::sh_count($year, $month, $item->sectionid , [1,2,4], $syid);
                // return $item->registered;
                $item->dropped_out_a = self::sh_count($year, $month, $item->sectionid , [3], $syid);
                $item->dropped_out_b = self::sh_count($year, $month - 1, $item->sectionid , [3], $syid);
                $item->transferred_out_a = self::sh_count($year, $month, $item->sectionid , [5], $syid);
                $item->transferred_out_b = self::sh_count($year, $month - 1, $item->sectionid ,[5], $syid);
                $item->transferred_in_a = self::sh_count($year, $month, $item->sectionid ,[4], $syid);
                $item->transferred_in_b = self::sh_count($year, $month - 1, $item->sectionid , [4], $syid);
                $item->attendance = self::sh_attendance($year, $month - 1, $item->sectionid , [1,2,4] , $days, $item->registered, $syid);
            }else{
                $item->registered = self::count($year, $month, $item->sectionid, [1,2,4], $syid);
                $item->dropped_out_a = self::count($year, $month, $item->sectionid , [3], $syid);
                $item->dropped_out_b = self::count($year, $month,$item->sectionid , [3], $syid);
                $item->transferred_out_a = self::count($year, $month, $item->sectionid , [5], $syid);
                $item->transferred_out_b = self::count($year, $month - 1, $item->sectionid , [5], $syid);
                $item->transferred_in_a = self::count($year, $month, $item->sectionid , [4], $syid);
                $item->transferred_in_b = self::count($year, $month - 1, $item->sectionid , [4], $syid);

                $item->attendance = self::attendance($year, $month - 1, $item->sectionid , [1,2,4] , $days, $item->registered, $syid);
               
            }
        }

        return $sections;
    }

    public static function count($year = null, $month = null, $sectionid = null, $status = null, $syid = null){

        $month = \Carbon\Carbon::create(null , $month)->isoFormat('MM');

        $male_reg = DB::table('enrolledstud')
                            ->where('enrolledstud.deleted',0)
                            // ->where('enrolledstud.dateenrolled','like','%'.$year.'-'. $month.'%')
                            ->whereIn('enrolledstud.studstatus',$status)
                            ->where('enrolledstud.sectionid',$sectionid)
                            ->where('enrolledstud.syid',$syid)
                            ->join('studinfo',function($join){
                                $join->on('enrolledstud.studid','=','studinfo.id');
                                $join->where('studinfo.deleted',0);
                            })
                            ->where(function($query){
                                $query->where('gender','Male');
                                $query->where('gender','MALE');
                            })
                            ->count();
                            
        $female_reg = DB::table('enrolledstud')
                            ->where('enrolledstud.deleted',0)
                            // ->where('enrolledstud.dateenrolled','like','%'.$year.'-'. $month.'%')
                            ->whereIn('enrolledstud.studstatus',$status)
                            ->where('enrolledstud.sectionid',$sectionid)
                            ->where('enrolledstud.syid',$syid)
                            ->join('studinfo',function($join){
                                $join->on('enrolledstud.studid','=','studinfo.id');
                                $join->where('studinfo.deleted',0);
                            })
                            ->where(function($query){
                                $query->where('gender','Female');
                                $query->where('gender','FEMALE');
                            })
                            ->count();

        return (object)[
                        'male'=>$male_reg,
                        'female'=>$female_reg,
                        'total'=>$male_reg+$female_reg
                    ];


    }

    public static function sh_count($year = null, $month = null, $sectionid = null, $status = null, $syid = null ){

        $month = \Carbon\Carbon::create(null , $month)->isoFormat('MM');
        $male_reg = DB::table('sh_enrolledstud')
                        // ->select('dateenrolled')
                        ->where('sh_enrolledstud.deleted',0)
                        // ->where('sh_enrolledstud.dateenrolled','like','%'.$year.'-'. $month.'%')
                        // ->where('sh_enrolledstud.dateenrolled','<=',$year.'-'. $month.'-'.date('t', strtotime($year.'-'. $month.'-'.date('t'))))
                        ->whereIn('sh_enrolledstud.studstatus',$status)
                        ->where('sh_enrolledstud.sectionid',$sectionid)
                        ->where('sh_enrolledstud.syid',$syid)
                        ->join('studinfo',function($join){
                            $join->on('sh_enrolledstud.studid','=','studinfo.id');
                            $join->where('studinfo.deleted',0);
                        })
                        ->where(function($query){
                            $query->where('gender','Male');
                            $query->where('gender','MALE');
                        })
                        ->count();
                        // ->get();

        $female_reg = DB::table('sh_enrolledstud')
                        ->where('sh_enrolledstud.deleted',0)
                        // ->where('sh_enrolledstud.dateenrolled','like','%'.$year.'-'. $month.'%')
                        // ->where('sh_enrolledstud.dateenrolled','<=',$year.'-'. $month.'-'.date('t', strtotime($year.'-'. $month.'-'.date('t'))))
                        ->whereIn('sh_enrolledstud.studstatus',$status)
                        ->where('sh_enrolledstud.sectionid',$sectionid)
                        ->where('sh_enrolledstud.syid',$syid)
                        ->join('studinfo',function($join){
                            $join->on('sh_enrolledstud.studid','=','studinfo.id');
                            $join->where('studinfo.deleted',0);
                        })
                        ->where(function($query){
                            $query->where('gender','Female');
                            $query->where('gender','FEMALE');
                        })
                        ->count();
        
        return (object)[
            'male'=>$male_reg,
            'female'=>$female_reg,
            'total'=>$male_reg+$female_reg
        ];

    }

    public static function attendance($year = null, $month = null, $sectionid = null, $status = null, $days, $registered, $syid = null){

        $average_male_attendance = 0;
        $average_female_attendance = 0;

        if( $registered->total != 0){

            foreach($days as $day){

                $month = \Carbon\Carbon::create(null , $day)->isoFormat('DD');
                $day_male_attendance =  0;
                $day_female_attendance =  0;

                if($registered->male != 0 ){

                    $male_attendance = DB::table('enrolledstud')
                                    ->where('enrolledstud.deleted',0)
                                    // ->where('enrolledstud.dateenrolled','like','%'.$year.'-'. $month.'%')
                                    ->whereIn('enrolledstud.studstatus',$status)
                                    ->where('enrolledstud.sectionid',$sectionid)
                                    ->where('enrolledstud.syid',$syid)
                                    ->join('studinfo',function($join){
                                        $join->on('enrolledstud.studid','=','studinfo.id');
                                        $join->where('studinfo.deleted',0);
                                    })
                                    ->join('studattendance',function($join){
                                        $join->on('enrolledstud.studid','=','studattendance.studid');
                                        $join->where('studattendance.deleted',0);
                                    })
                                    ->where(function($query){
                                        $query->where('gender','Male');
                                        $query->where('gender','MALE');
                                    })
                                    ->count();

                    $day_male_attendance =  round($male_attendance / $registered->male);

                }
                            
                if($registered->female != 0 ){

                    $female_attendance = DB::table('enrolledstud')
                            ->where('enrolledstud.deleted',0)
                            // ->where('enrolledstud.dateenrolled','like','%'.$year.'-'. $month.'%')
                            ->whereIn('enrolledstud.studstatus',$status)
                            ->where('enrolledstud.sectionid',$sectionid)
                            ->where('enrolledstud.syid',$syid)
                            ->join('studinfo',function($join){
                                $join->on('enrolledstud.studid','=','studinfo.id');
                                $join->where('studinfo.deleted',0);
                            })
                            ->join('studattendance',function($join){
                                $join->on('enrolledstud.studid','=','studattendance.studid');
                                $join->where('studattendance.deleted',0);
                            })
                            ->where(function($query){
                                $query->where('gender','Female');
                                $query->where('gender','FEMALE');
                            })
                            ->count();

                    $day_female_attendance =  round($female_attendance / $registered->female);
                    
                }


                $average_male_attendance +=  $day_male_attendance;
                $average_female_attendance +=  $day_female_attendance;

            }
        }

        return (object)[
            'male'=>round($average_male_attendance / count($days)),
            'female'=>round($average_female_attendance / count($days)),
            'total'=>round($average_male_attendance / count($days)) + round($average_female_attendance / count($days))
        ];

    }


    public static function sh_attendance($year = null, $month = null, $sectionid = null, $status = null, $days, $registered, $syid = null){

        $average_male_attendance = 0;
        $average_female_attendance = 0;

        if( $registered->total != 0){

            foreach($days as $day){

                $month = \Carbon\Carbon::create(null , $day)->isoFormat('DD');

                $day_male_attendance =  0;
                $day_female_attendance = 0;


                if($registered->male != 0 ){

                    $male_attendance = DB::table('sh_enrolledstud')
                                    ->where('sh_enrolledstud.deleted',0)
                                    // ->where('sh_enrolledstud.dateenrolled','like','%'.$year.'-'. $month.'%')
                                    ->whereIn('sh_enrolledstud.studstatus',$status)
                                    ->where('sh_enrolledstud.sectionid',$sectionid)
                                    ->where('sh_enrolledstud.syid',$syid)
                                    ->join('studinfo',function($join){
                                        $join->on('sh_enrolledstud.studid','=','studinfo.id');
                                        $join->where('studinfo.deleted',0);
                                    })
                                    ->join('studattendance',function($join){
                                        $join->on('sh_enrolledstud.studid','=','studattendance.studid');
                                        $join->where('studattendance.deleted',0);
                                    })
                                    ->where(function($query){
                                        $query->where('gender','Male');
                                        $query->where('gender','MALE');
                                    })
                                    ->count();

                    $day_male_attendance =  round($male_attendance / $registered->male);

                }

                if($registered->female != 0 ){

                    $female_attendance = DB::table('sh_enrolledstud')
                        ->where('sh_enrolledstud.deleted',0)
                        // ->where('sh_enrolledstud.dateenrolled','like','%'.$year.'-'. $month.'%')
                        ->whereIn('sh_enrolledstud.studstatus',$status)
                        ->where('sh_enrolledstud.sectionid',$sectionid)
                        ->where('sh_enrolledstud.syid',$syid)
                        ->join('studinfo',function($join){
                            $join->on('sh_enrolledstud.studid','=','studinfo.id');
                            $join->where('studinfo.deleted',0);
                        })
                        ->join('studattendance',function($join){
                            $join->on('sh_enrolledstud.studid','=','studattendance.studid');
                            $join->where('studattendance.deleted',0);
                        })
                        ->where(function($query){
                            $query->where('gender','Female');
                            $query->where('gender','FEMALE');
                        })
                        ->count();

                    $day_female_attendance = round($female_attendance / $registered->female);

                }
                
                   
                    

                $average_male_attendance +=  $day_male_attendance;
                $average_female_attendance +=  $day_female_attendance;

            }
        }

        return (object)[
            'male'=> round($average_male_attendance / count($days)),
            'female'=>round($average_female_attendance / count($days)),
            'total'=>round($average_male_attendance / count($days)) + round($average_female_attendance / count($days))
        ];
        

    }
    
    public static function get_calendar($month = null,$year = null){

        $list=array();
        $today = date("d"); // Current day

        function draw_calendar($month,$year){

            /* draw table */
            $calendar = '<table class="table-bordered" style="width: 100%;">';
        
            /* table headings */
            $headings = array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');
            $calendar.= '<tr class="calendar-row"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr>';
        
            /* days and weeks vars now ... */
            $running_day = date('w',mktime(0,0,0,$month,1,$year));
            $days_in_month = date('t',mktime(0,0,0,$month,1,$year));
            $days_in_this_week = 1;
            $day_counter = 0;
            $dates_array = array();
        
            /* row for week one */
            $calendar.= '<tr class="calendar-row">';
        
            /* print "blank" days until the first of the current week */
            for($x = 0; $x < $running_day; $x++):
                $calendar.= '<td class="calendar-day-np"> </td>';
                $days_in_this_week++;
            endfor;
        
            /* keep going with days.... */
            for($list_day = 1; $list_day <= $days_in_month; $list_day++):
                $calendar.= '<td class="calendar-day active-date align-middle" data-id="'.$list_day.'">';
                    /* add in the day number */
                    $calendar.= '<div class="day-number"><a class="btn btn-block "  data-id="'.$list_day.'">'.$list_day.'</a></div>';
        
                    /** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! **/
                    $calendar.= str_repeat('<p> </p>',2);
                    
                $calendar.= '</td>';
                if($running_day == 6):
                    $calendar.= '</tr>';
                    if(($day_counter+1) != $days_in_month):
                        $calendar.= '<tr class="calendar-row">';
                    endif;
                    $running_day = -1;
                    $days_in_this_week = 0;
                endif;
                $days_in_this_week++; $running_day++; $day_counter++;
            endfor;
        
            /* finish the rest of the days in the week */
            if($days_in_this_week < 8):
                for($x = 1; $x <= (8 - $days_in_this_week); $x++):
                    $calendar.= '<td class="calendar-day-np"> </td>';
                endfor;
            endif;
        
            /* final row */
            $calendar.= '</tr>';
        
            /* end the table */
            $calendar.= '</table>';
            
            /* all done, return result */
            return $calendar;
        }
        
        /* sample usages */
        echo '<h2><center>' . date('F Y', strtotime($year.'-'.$month)) . '</center></h2>';
        return draw_calendar($month,$year);


    }



}
