<?php

namespace App\Http\Controllers\AdministratorControllers;

use Illuminate\Http\Request;
use DB;
use Session;

class RoomsController extends \App\Http\Controllers\Controller
{

    public static function getsubjects(Request $request){

        try{
            
            $levelid = $request->get('levelid');
            $sectionid = $request->get('sectionid');
            $sections = $request->get('sections');
            $syid = $request->get('syid');
            $semid = $request->get('semid');

            if($levelid == 14 || $levelid == 15){

                $sectionblockass = DB::table('sh_sectionblockassignment')
                                        ->join('sh_block',function($join){
                                            $join->on('sh_sectionblockassignment.blockid','=','sh_block.id');
                                            $join->where('sh_block.deleted',0);
                                        })
                                        ->where('sh_sectionblockassignment.syid',$syid)
                                        ->where('sh_sectionblockassignment.deleted',0)
                                        ->where('sh_sectionblockassignment.sectionid',$sectionid)
                                        ->select('strandid')
                                        ->get();

                $subjects = DB::table('subject_plot')
                                ->where('subject_plot.deleted',0)
                                ->where('subject_plot.syid',$syid)
                                ->where('subject_plot.levelid',$levelid)
                                ->where('subject_plot.strandid',collect($sectionblockass)->pluck('strandid'))
                                ->where('subject_plot.semid',$semid)
                                ->join('sh_subjects',function($join){
                                    $join->on('subject_plot.subjid','=','sh_subjects.id');
                                    $join->where('sh_subjects.deleted',0);
                                })
                                ->distinct('subjid')
                                ->select(
                                    'subjtitle as text',
                                    'subjid as id'
                                )
                                ->get();
                foreach($subjects as $item){
                    $item->subjCom = null;
                }

            }else{

                $subjects = DB::table('subject_plot')
                                ->where('subject_plot.deleted',0)
                                ->where('subject_plot.syid',$syid)
                                ->where('subject_plot.levelid',$levelid)
                                ->join('subjects',function($join){
                                    $join->on('subject_plot.subjid','=','subjects.id');
                                    $join->where('subjects.deleted',0);
                                    $join->where('subjects.isCon',0);
                                })
                                ->select(
                                    'subjdesc as text',
                                    'subjid as id',
                                    'subjCom'
                                )
                                ->get();
            }


            return array((object)[
                'status'=>1,
                'data'=>$subjects
            ]);
        
        }catch(\Exception $e){

            return self::store_error($e);


        }

    }

    public static function getsections(Request $request){

        try{
            
            $sections = DB::table('sectiondetail')
                            ->where('sectiondetail.deleted',0)
                            ->where('syid',$request->get('syid'))
                            ->join('sections',function($join){
                                $join->on('sectiondetail.sectionid','=','sections.id');
                                $join->where('sections.deleted',0);
                            })
                            ->join('gradelevel',function($join){
                                $join->on('sections.levelid','=','gradelevel.id');
                                $join->where('gradelevel.deleted',0);
                            })
                            ->select(
                                'sectiondetail.sd_roomid as roomid',
                                'sectiondetail.teacherid',
                                'sectionname as text',
                                'sectionid as id',
                                'levelid',
                                'acadprogid'
                            )
                            ->get();

            return array((object)[
                'status'=>1,
                'data'=>$sections
            ]);
        
        }catch(\Exception $e){

            return self::store_error($e);


        }

    }

    public static function roomsDatatable(Request $request){

        $search = $request->get('search');
        $search = $search['value'];
        
        $order = $request->get('order')[0];
        $order_col = $order['column'];
        $order_dir = $order['dir'];

        $columns = ['rooms.roomname', 'description', 'rooms.capacity'];

        $rooms = DB::table('rooms')
            ->orderBy($columns[$order_col], $order_dir)
            ->leftJoin('building',function($join){
                $join->on('rooms.buildingid','=','building.id');
                $join->where('building.deleted',0);
            })  
            ->where(function($query) use($search){
                if($search != null){
                    $query->orWhere('roomname','like','%'.$search.'%');
                    $query->orWhere('description','like','%'.$search.'%');
                }
            })
            ->take($request->get('length'))
            ->skip($request->get('start'))
            ->where('rooms.deleted',0)
            ->select(
                'rooms.id',
                'rooms.roomname',
                'rooms.capacity',
                'buildingid',
                'description'
            )
            ->get();

        $room_count = DB::table('rooms')
            ->orderBy($columns[$order_col], $order_dir)
            ->leftJoin('building',function($join){
                $join->on('rooms.buildingid','=','building.id');
                $join->where('building.deleted',0);
            })  
            ->where('rooms.deleted',0)
            ->select(
                'rooms.id',
                'rooms.roomname',
                'rooms.capacity',
                'buildingid',
                'description'
            )
            ->count();

        return @json_encode((object)[
            'data'=>$rooms,
            'recordsTotal'=>$room_count,
            'recordsFiltered'=>$room_count
        ]);

        // return $rooms;

    }

    public static function create_room(Request $request){

        $roomname = $request->get('roomname');
        $capacity = $request->get('capacity');
        $building = $request->get('building');

        try{

            $check = DB::table('rooms')
                        ->where('roomname',$roomname)
                        ->where('deleted',0)
                        ->count();

            if($check == 0){
                DB::table('rooms')
                    ->insert([
                        'deleted'=>0,
                        'roomname'=>$roomname,
                        'buildingid'=>$building,
                        'capacity'=>$capacity,
                        'createdby'=>auth()->user()->id,
                        'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                    ]);

                return  array((object)[
                    'status'=>1,
                    'message'=>'Room Created',
                    'icon'=>'success',
                ]);
            }else{
                return  array((object)[
                    'status'=>0,
                    'message'=>'Already Exist',
                    'icon'=>'error',
                ]);
            }

        }catch(\Exception $e){
            return $e;
            return  array((object)[
                'status'=>0,
                'message'=>'Something went wrong!'
            ]);

        }

    }

    public static function udpate_room(Request $request){

        $roomname = $request->get('roomname');
        $capacity = $request->get('capacity');
        $building = $request->get('building');
        $id = $request->get('id');

        try{

            // // check usage in 'rooms'
            // $check_usage = DB::table('rooms')
            //             ->where('id',$id)
            //             ->where('deleted',0)
            //             ->where('buildingid', '!=', null)
            //             ->get();

            // if($check_usage->count() > 0){
            //     $room = $check_usage[0];

            //     $room_blg_assigned = DB::table('building')
            //     ->where('deleted',0)
            //     ->where('id', $room->buildingid)
            //     ->get();

            //     return  array((object)[
            //         'status'=>2,
            //         'message'=>'<p class="text-left" style="margin-bottom: 0;"><b>Update Error:</b><br>Room already assigned to ' .$room_blg_assigned[0]->description. ' building.</p>',
            //         'icon'=>'error'
            //     ]);
            // }

            // check roomname exists
            $check_usage = DB::table('rooms')
                        ->where('roomname',$roomname)
                        ->where('id','!=',$id)
                        ->where('deleted',0)
                        ->count();

            if($check_usage > 0){
                return  array((object)[
                    'status'=>0,
                    'message'=>'Room already exist',
                    'icon'=>'error'
                ]);
            }

            DB::table('rooms')
            ->where('id',$id)
            ->take(1)
            ->update([
                'roomname'=>$roomname,
                'buildingid'=>$building,
                'capacity'=>$capacity,
                'updatedby'=>auth()->user()->id,
                'updateddatetime'=>\Carbon\Carbon::now('Asia/Manila')
            ]);

            return  array((object)[
                'status'=>1,
                'message'=>'Room Updated',
                'icon'=>'success'
            ]);

        }catch(\Exception $e){
            return  array((object)[
                'status'=>0,
                'message'=>'Something went wrong!'
            ]);

        }

    }

    public static function delete_room(Request $request){

        $id = $request->get('id');

        try{

            // check usage in 'classscheddetail'
            $check_usage = DB::table('classscheddetail')
                            ->where('roomid',$id)
                            ->where('deleted',0)
                            ->count();

            if($check_usage > 0){
                return  array((object)[
                    'status'=>2,
                    'message'=>'Room is in used!',
                    'icon'=>'warning'
                ]);
            }

            // check usage in 'sh_classscheddetail'
            $check_usage = DB::table('sh_classscheddetail')
                        ->where('roomid',$id)
                        ->where('deleted',0)
                        ->count();

            if($check_usage > 0){
                return  array((object)[
                    'status'=>2,
                    'message'=>'Room is in used!',
                    'icon'=>'warning'
                ]);
            }

            // // check usage in 'rooms'
            // $check_usage = DB::table('rooms')
            //             ->where('id',$id)
            //             ->where('deleted',0)
            //             ->where('buildingid', '!=', null)
            //             ->get();

            // if($check_usage->count() > 0){
            //     $room = $check_usage[0];

            //     $room_blg_assigned = DB::table('building')
            //     ->where('deleted',0)
            //     ->where('id', $room->buildingid)
            //     ->get();

            //     return  array((object)[
            //         'status'=>2,
            //         'message'=>'<p class="text-left" style="margin-bottom: 0;">Delete Error:<br>Room already assigned to ' .$room_blg_assigned[0]->description. ' building.</p>',
            //         'icon'=>'error'
            //     ]);
            // }

            // deleted column not found
            // $check_usage = DB::table('scheddetail')
            //             ->where('roomid',$id)
            //             ->where('deleted',0)
            //             ->count();

            // if($check_usage > 0){
            //     return  array((object)[
            //         'status'=>2,
            //         'message'=>'Room is in used!',
            //         'icon'=>'warning'
            //     ]);
            // }

            DB::table('rooms')
                ->where('id',$id)
                ->update([
                    'deleted'=>1,
                    'updatedby'=>auth()->user()->id,
                    'updateddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                ]);
            
            return  array((object)[
                'status'=>1,
                'message'=>'Room Deleted!',
                'icon'=>'success'
            ]);
    
        }catch(\Exception $e){
            return $e;
            return  array((object)[
                'status'=>0,
                'message'=>'Something went wrong!',
                'icon'=>'error'
            ]);

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
              'message'=>'Something went wrong!'
        ]);
    }


}
