<?php

namespace App\Http\Controllers\SuperAdminController\College;

use Illuminate\Http\Request;
use File;
use DB;
use Image;

class InputPeriodController extends \App\Http\Controllers\Controller
{
    public static function getActiveSetup(Request $request){


        $syid = $request->get('syid');
        $semid = $request->get('semid');

        $active_inputperiod = DB::table('college_grade_inputperiod')
                                ->where('startstatus',1)
                                ->where('endstatus',0)
                                ->where('syid',$syid)
                                ->where('semid',$semid)
                                ->get();


        foreach($active_inputperiod as $item){
            $item->startformat2 = \Carbon\Carbon::create($item->datestart)->isoFormat('MMM DD, YYYY hh:mm A');
            $item->endformat2 = \Carbon\Carbon::create($item->dateend)->isoFormat('MMM DD, YYYY hh:mm A');

            $item->startdatetimeformat2 = \Carbon\Carbon::create($item->startdatetime)->isoFormat('MMM DD, YYYY hh:mm A');
            $item->enddatetimeformat2 = \Carbon\Carbon::create($item->dateended)->isoFormat('MMM DD, YYYY hh:mm A');
        }

        return $active_inputperiod;

    }


    public static function inputperiodslist(Request $request){

        $syid  = $request->get('syid');
        $semid = $request->get('semid');

        $schedule_classifiation = DB::table('college_grade_inputperiod')
                                    ->where('syid',$syid)
                                    ->where('semid',$semid)
                                    ->where('deleted',0)
                                    ->orderBy('datestart','desc')
                                    ->get();

        foreach($schedule_classifiation as $item){
            $item->startformat2 = \Carbon\Carbon::create($item->datestart)->isoFormat('MMM DD, YYYY hh:mm A');
            $item->endformat2 = \Carbon\Carbon::create($item->dateend)->isoFormat('MMM DD, YYYY hh:mm A');

            $item->startdatetimeformat2 = \Carbon\Carbon::create($item->startdatetime)->isoFormat('MMM DD, YYYY hh:mm A');
            $item->enddatetimeformat2 = \Carbon\Carbon::create($item->dateended)->isoFormat('MMM DD, YYYY hh:mm A');
        }
                    
        return $schedule_classifiation;
        

    }

    public static function inputperiodscreate(Request $request){

        try{

            $datetime = explode(' - ',$request->get('date'));

            $datetimestart = substr($datetime[0],0,-3);
            $datetimeend = substr($datetime[1],0,-3);

            $start = $request->get('start');

            $inputperiod_starteddatettime = null;
            if($start == 1){
                $inputperiod_starteddatettime = \Carbon\Carbon::now('Asia/Manila');
            }

            if($start == 1){
                DB::tabLe('college_grade_inputperiod')
                    ->where('startstatus',1)
                    ->where('deleted',0)
                    ->where('endstatus',0)
                    ->update([
                        'endstatus'=>1,
                        'dateended'=>\Carbon\Carbon::now('Asia/Manila'),
                        'endedby'=>auth()->user()->id
                    ]);
            }

            DB::table('college_grade_inputperiod')
                    ->insert([
                        'syid'=>$request->get('syid'),
                        'semid'=>$request->get('semid'),
                        'datestart'=>str_replace('/','-',$datetimestart),
                        'dateend'=>str_replace('/','-',$datetimeend),
                        'startstatus'=>$start,
                        'startdatetime'=>$inputperiod_starteddatettime,
                        'startby'=>auth()->user()->id,
                        'createddatetime'=>\Carbon\Carbon::now('Asia/Manila'),
                        'createdby'=>auth()->user()->id,
                        'deleted'=>0
                    ]);

            return array((object)[
                        'status'=>1,
                        'icon'=>'success',
                        'message'=>'Input Period Created!',
                        'data'=>self::inputperiodslist($request)
                    ]);
            
        }catch(\Exception $e){
            return self::store_error($e);
        }

    }

    public static function inputperiodsupdate(Request $request){
        try{

            $datetime = explode(' - ',$request->get('date'));

            $datetimestart = substr($datetime[0],0,-3);
            $datetimeend = substr($datetime[1],0,-3);

            $start = $request->get('start');
            $end = $request->get('end');
            $id = $request->get('id');
            $syid = $request->get('syid');
            $semid = $request->get('semid');

            $info = DB::table('college_grade_inputperiod')
                                        ->where('id',$id)
                                        ->where('deleted',0)
                                        ->first();


            
            $inputperiod_starteddatettime = null;
            $startby = null;
            if($start == 1){
                if($info->startstatus == 1){
                    $inputperiod_starteddatettime = $info->startdatetime;
                    $startby = $info->startby;
                }else{
                    $startby = auth()->user()->id;
                    $inputperiod_starteddatettime = \Carbon\Carbon::now('Asia/Manila');
                }

                DB::tabLe('college_grade_inputperiod')
                    ->where('id','!=',$id)
                    ->where('startstatus',1)
                    ->where('deleted',0)
                    ->where('endstatus',0)
                    ->update([
                        'endstatus'=>1,
                        'dateended'=>\Carbon\Carbon::now('Asia/Manila'),
                        'endedby'=>auth()->user()->id
                    ]);
            }

            $inputperiod_endeddatettime = null;
            $endby = null;

            if($end == 1){
                $inputperiod_endeddatettime = \Carbon\Carbon::now('Asia/Manila');
                $endby = auth()->user()->id;
            }

            DB::table('college_grade_inputperiod')
                ->take(1)
                ->where('id',$id)
                ->update([
                    'startstatus'=>$start,
                    'endstatus'=>$end,

                    'startdatetime'=>$inputperiod_starteddatettime,
                    'startby'=>$startby,
                    
                    'dateended'=>$inputperiod_endeddatettime,
                    'endedby'=>$endby,

                    'datestart'=>str_replace('/','-',$datetimestart),
                    'dateend'=>str_replace('/','-',$datetimeend),
                    'updateddatetime'=>\Carbon\Carbon::now('Asia/Manila'),
                    'updatedby'=>auth()->user()->id,
                ]);

            return array((object)[
                        'status'=>1,
                        'icon'=>'success',
                        'message'=>'Input Period Updated!',
                        'data'=>self::inputperiodslist($request)
                    ]);

        }catch(\Exception $e){
            return self::store_error($e);
        }
    }

    public static function inputperiodsdelete(Request $request){
        try{

            $id = $request->get('id');

            $check = DB::table('college_grade_inputperiod')
                        ->where('id',$id)
                        ->where('deleted',0)
                        ->first();

            if($check->startstatus == 1){
                return array((object)[
                    'status'=>0,
                    'icon'=>'warning',
                    'message'=>'Period Already Started!'
                ]);
            }

            DB::table('college_grade_inputperiod')
                ->take(1)
                ->where('id',$id)
                ->update([
                    'deleted'=>1,
                    'deleteddatetime'=>\Carbon\Carbon::now('Asia/Manila'),
                    'deletedby'=>auth()->user()->id,
                ]);

            return array((object)[
                        'status'=>1,
                        'icon'=>'success',
                        'message'=>'Input Period Deleted!',
                        'data'=>self::inputperiodslist($request)
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
              'icon'=>'error',
              'message'=>'Something went wrong!'
        ]);
    }
      
}
