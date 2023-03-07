<?php

namespace App\Http\Controllers\SuperAdminController;

use Illuminate\Http\Request;
use DB;
use Auth;
use Session;

class SchoolDaysController extends \App\Http\Controllers\Controller
{

      public static function get_gradelevel(Request $request){

            $syid = $request->get('syid');

            $acad = self::get_acad($syid);

            if(Session::get('currentPortal') == 2){
                  
                  $teacherid = DB::table('teacher')
                                    ->where('deleted',0)
                                    ->where('tid',auth()->user()->email)
                                    ->first();
      
                  $gradelevel = DB::table('gradelevel')
                              ->where('deleted',0)
                              ->whereIn('acadprogid',$acad)
                              ->where('gradelevel.acadprogid','!=',6)
                              ->orderBy('sortid')
                              ->select(
                                    'gradelevel.levelname as text',
                                    'gradelevel.id',
                                    'acadprogid'
                              )
                              ->get(); 
      
            }else{
      
                  $gradelevel = DB::table('gradelevel')
                              ->where('deleted',0)
                              ->where('gradelevel.acadprogid','!=',6)
                              ->whereIn('gradelevel.acadprogid',$acad)
                              ->orderBy('sortid')
                              ->select(
                                    'gradelevel.levelname as text',
                                    'gradelevel.id',
                                    'acadprogid'
                              )
                              ->get(); 
            }


            return $gradelevel;

      }


      public static function get_acad($syid = null){

            if(auth()->user()->type == 17){
                  $acadprog = DB::table('academicprogram')
                                          ->select('id')
                                          ->get();
            }
            else{

                  $teacherid = DB::table('teacher')
                                    ->where('tid',auth()->user()->email)
                                    ->select('id')
                                    ->first()
                                    ->id;

                  if(auth()->user()->type == 2 || Session::get('currentPortal') == 2){

                        $acadprog = DB::table('academicprogram')
                                          ->where('principalid',$teacherid)
                                          ->get();

                  }else{

                        $acadprog = DB::table('teacheracadprog')
                                    ->where('teacherid',$teacherid)
                                    ->where('acadprogutype',Session::get('currentPortal'))
                                    ->where('deleted',0)
                                    ->where('syid',$syid)
                                    ->select('acadprogid as id')
                                    ->distinct('acadprogid')
                                    ->get();
                  }
            }


            $acadprog_list = array();
            foreach($acadprog as $item){
                  array_push($acadprog_list,$item->id);
            }

            return $acadprog_list;

      }

      //attendance setup start
      public static function list(Request $request){
            $syid = $request->get('schoolyear');
            $levelid = $request->get('levelid');
            return self::attendance_setup_list($syid,$levelid);
      }

      public static function create(Request $request){
            $month = $request->get('month');
            $days = $request->get('days');
            $syid = $request->get('syid');
            $sort = $request->get('sort');
            $year = $request->get('year');
            $semid = $request->get('semid');
            $levelid = $request->get('levelid');
            $userid = $request->get('userid');
            return self::attendance_setup_create($month, $days, $syid, $sort, $year,$levelid,$semid,$userid);
      }
      public static function update(Request $request){
            $attsetupid = $request->get('attsetupid');
            $month = $request->get('month');
            $days = $request->get('days');
            $syid = $request->get('syid');
            $sort = $request->get('sort');
            $year = $request->get('year');
            $semid = $request->get('semid');
            $levelid = $request->get('levelid');
            $userid = $request->get('userid');
            return self::attendance_setup_update($attsetupid, $month, $days, $syid, $sort, $year,$levelid,$semid,$userid);
      }
      public static function delete(Request $request){
            $attsetupid = $request->get('attsetupid');
            $syid = $request->get('syid');
            $userid = $request->get('userid');
            return self::attendance_setup_delete($attsetupid, $syid,$userid);
      }
      //attendance setup end

      //proccess
      public static function attendance_setup_create(
            $month = null, 
            $days = null,
            $syid  = null,
            $sort = null,
            $year = null,
            $levelid = null,
            $semid = null,
            $userid = null
      ){

            if(Auth::check()){
                  $userid = auth()->user()->id;
                  $username = auth()->user()->name;
            }else{
                  $username = DB::table('users')->where('id',$userid)->first()->name;
            }

            try{
                 
                  $check_sy = DB::table('sy')
                                    ->where('id',$syid)
                                    ->first();

                  if($check_sy->ended == 1){
                        return array((object)[
                              'status'=>2,
                              'data'=>'S.Y. Ended!'
                        ]);
                  }

                  $check = DB::table('studattendance_setup')
                              ->where('syid',$syid)
                              ->where('levelid',$levelid)
                              ->where('month',$month)
                              ->where('deleted',0)
                              ->count();

                  if($check > 0){
                        return array((object)[
                              'status'=>2,
                              'data'=>'Already exist!',
                        ]);
                  }

                  $attendance_setup_id = DB::table('studattendance_setup')
                        ->insertGetId([
                              'syid'=>$syid,
                              'month'=>$month,
                              'days'=>$days,
                              'sort'=>$sort,
                              'year'=>$year,
                              'levelid'=>$levelid,
                              'semid'=>$semid,
                              'createdby'=>$userid,
                              'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                        ]);

                  $year = DB::table('sy')->where('id',$syid)->first()->sydesc;

                  $message = $username.' added month of '.\Carbon\Carbon::create(null, $month)->isoFormat('MMMM').' for school year '. $year;
                  
                  self::create_logs($message,$attendance_setup_id,$userid);

                  return array((object)[
                        'status'=>1,
                        'data'=>'Created Successfully!',
                        'id'=> $attendance_setup_id
                  ]);

            }catch(\Exception $e){
                  return self::store_error($e,$userid);
            }
      }

      public static function attendance_setup_update(
            $attsetupid = null,
            $month = null, 
            $days = null,
            $syid  = null,
            $sort = null,
            $year = null,
            $levelid = null,
            $semid = null,
            $userid = null
      ){

            if(Auth::check()){
                  $userid = auth()->user()->id;
                  $username = auth()->user()->name;
            }else{
                  $username = DB::table('users')->where('id',$userid)->first()->name;
            }

            try{

                  $check_sy = DB::table('sy')
                                    ->where('id',$syid)
                                    ->first();

                  if($check_sy->ended == 1){
                        return array((object)[
                              'status'=>2,
                              'data'=>'S.Y. Ended!'
                        ]);
                  }

                  DB::table('studattendance_setup')
                        ->take(1)
                        ->where('id',$attsetupid)
                        ->where('deleted',0)
                        ->update([
                              'syid'=>$syid,
                              'month'=>$month,
                              'days'=>$days,
                              'sort'=>$sort,
                              'sort'=>$sort,
                              'year'=>$year,
                              'semid'=>$semid,
                              'levelid'=>$levelid,
                              'updateddatetime'=>\Carbon\Carbon::now('Asia/Manila'),
                              'updatedby'=>$userid
                        ]);

                  $year = DB::table('sy')->where('id',$syid)->first()->sydesc;

                  $message = $username.' updated month of '.\Carbon\Carbon::create(null, $month)->isoFormat('MMMM').' school year '. $year;
                  
                  self::create_logs($message,$attsetupid,$userid);

                  return array((object)[
                        'status'=>1,
                        'data'=>'Updated Successfully!'
                  ]);

            }catch(\Exception $e){
                  return self::store_error($e,$userid);
            }
      }

      
      public static function attendance_setup_delete(
            $attsetupid = null,
            $syid = null,
            $userid = null
      ){

            if(Auth::check()){
                  $userid = auth()->user()->id;
                  $username = auth()->user()->name;
            }else{
                  $username = DB::table('users')->where('id',$userid)->first()->name;
            }

            try{
                  DB::table('studattendance_setup')
                        ->take(1)
                        ->where('id',$attsetupid)
                        ->where('deleted',0)
                        ->update([
                              'deleted'=>1,
                              'deleteddatetime'=>\Carbon\Carbon::now('Asia/Manila'),
                              'deletedby'=>$userid
                        ]);

                        
                  $month = DB::table('studattendance_setup')->where('id',$attsetupid)->first()->month;
                  $year = DB::table('sy')->where('id',$syid)->first()->sydesc;

                  $message = $username.' remove month of '.\Carbon\Carbon::create(null, $month)->isoFormat('MMMM').' for school year '. $year;

                  self::create_logs($message,$attsetupid,$userid);

                  return array((object)[
                        'status'=>1,
                        'data'=>'Updated Successfully!'
                  ]);
            }catch(\Exception $e){
                  return self::store_error($e,$userid);
            }
      }

      
      //data
      public static function attendance_setup_list($syid = null, $levelid = null){
            $attendance_setup = DB::table('studattendance_setup')
                              ->where('deleted',0);
            if($syid != null){
                  $attendance_setup = $attendance_setup->where('syid',$syid);
            }
            if($levelid != null){
                  $attendance_setup = $attendance_setup->where('levelid',$levelid);
            }else{
                  $attendance_setup = $attendance_setup->whereNull('levelid');
            }
            $attendance_setup = $attendance_setup
                                    ->join('sy',function($join){
                                          $join->on('studattendance_setup.syid','=','sy.id');
                                    })
                                    ->select(
                                          'studattendance_setup.id',
                                          'studattendance_setup.syid',
                                          'studattendance_setup.month',
                                          'studattendance_setup.days',
                                          'studattendance_setup.year',
                                          'studattendance_setup.semid',
                                          'studattendance_setup.levelid',
                                          'sydesc',
                                          'sort'
                                    )
                                    ->get();
            foreach( $attendance_setup as $item){
                  $item->monthdesc = \Carbon\Carbon::create(null, $item->month)->isoFormat('MMMM');
            }
            return $attendance_setup;
      }

      public static function schooldayscopy(Request $request){

            try{
                  $gradelevelto = $request->get('gradelevel_to');
                  $gradefrom = $request->get('gradelevel_from');

                  $syidto = $request->get('syid_to');
                  $syidfrom = $request->get('syid_from');


                  $copied = 0;

                  if($gradefrom != null){
                        $list = self::attendance_setup_list($syidto,$gradefrom);
                        foreach($list as $item){
                              $new_request = new Request([
                                    'month'   => $item->month,
                                    'syid'   => $item->syid,
                                    'sort'   => $item->sort,
                                    'days'   => $item->days,
                                    'year'   => $item->year,
                                    'semid'   => $item->semid,
                                    'levelid'   => $gradelevelto
                              ]);
                              $status = self::create($new_request);
                              if($status[0]->status == 1){
                                    $copied += 1;
                              }
                        }
                        
                  
                  }else if($syidfrom != null){

                        $list = self::attendance_setup_list($syidfrom,$gradelevelto);
                     
                        foreach($list as $item){
                              $new_request = new Request([
                                    'month'   => $item->month,
                                    'syid'   => $syidto,
                                    'sort'   => $item->sort,
                                    'days'   => $item->days,
                                    'year'   => $item->year,
                                    'semid'   => $item->semid,
                                    'levelid'   => $item->levelid
                              ]);
                              $status = self::create($new_request);
                              if($status[0]->status == 1){
                                    $copied += 1;
                              }
                        }

                  }

                  return array((object)[
                        'status'=>1,
                        'data'=>'School Days Copied!',
                        'copied'=>$copied
                  ]);
            }catch(\Exception $e){
                  return self::store_error($e);
            }


      }



      public static function logs($syid = null){
            return DB::table('logs')->where('module',1)->get();
      }

      public static function store_error($e,$userid = null){
            DB::table('zerrorlogs')
            ->insert([
                        'error'=>$e,
                        'createdby'=>$userid,
                        'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                        ]);
            return array((object)[
                  'status'=>0,
                  'data'=>'Something went wrong!'
            ]);
      }

      public static function create_logs($message = null, $id = null,$userid = null){
           DB::table('logs') 
             ->insert([
                  'dataid'=>$id,
                  'module'=>1,
                  'message'=>$message,
                  'createdby'=>$userid,
                  'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
            ]);
      }


      
      
     
      

}
