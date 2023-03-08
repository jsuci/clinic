<?php

namespace App\Http\Controllers\AdministratorControllers;

use Illuminate\Http\Request;
use DB;
use Session;

class BuildingController extends \App\Http\Controllers\Controller
{

    public static function getBuildings(Request $request){

        try{
            
            $id = $request->get('id');
            $description = $request->get('description');

            $buildings = DB::table('building')
                            ->where('deleted',0);

            if($id !== "" && $id !== null){
                $buildings = $buildings->where('id',$id);
            }

            if($description !== "" && $description !== null){
                $buildings = $buildings->where('description',$description);
            }
                          
            $buildings = $buildings->select(
                                'description',
                                'capacity',
                                'id'
                            )
                            ->get();

            return array((object)[
                'status'=>1,
                'data'=>$buildings
            ]);
        
        }catch(\Exception $e){
            return self::store_error($e);
        }

    }

    public static function getBuildingsDatatable(Request $request){

        try{

            $search = $request->get('search');
            $search = $search['value'];
            
            $buildings = DB::table('building')
                            ->where('deleted',0)
                            ->where(function($query) use($search){
                                if($search != null){
                                    $query->where('description','like','%'.$search.'%');
                                }
                            })
                            ->take($request->get('length'))
                            ->skip($request->get('start'))
                            ->select(
                                'description',
                                'capacity',
                                'id'
                            )
                            ->get();

            $building_count = DB::table('building')
                            ->where('deleted',0)
                            ->where(function($query) use($search){
                                if($search != null){
                                    $query->where('description','like','%'.$search.'%');
                                }
                            })
                            ->count();

            if(count($buildings) < 10){
                $buildings = collect($buildings)->toArray();
                $lacking = 10 - count( $buildings);
                for($x=0;$x <= $lacking; $x++){
                    array_push( $buildings , (object)[
                        'description'=>null,
                        'capacity'=>null,
                        'id'=>null
                    ]);
                }
            }


            return @json_encode((object)[
                'data'=>$buildings,
                'recordsTotal'=>$building_count,
                'recordsFiltered'=>$building_count
            ]);
        
        }catch(\Exception $e){
            return self::store_error($e);
        }

    }

    public static function getBuildingRooms(Request $request) {
        try {

            $search = $request->get('search');
            $search = $search['value'];

            $buildingid = $request->get('buildingid');

            $rooms = DB::table('rooms')
                            ->where('deleted',0)
                            ->where('buildingid', $buildingid)
                            ->where(function($query) use($search){
                                if($search != null){
                                    $query->where('roomname','like','%'.$search.'%');
                                }
                            })
                            ->take($request->get('length'))
                            ->skip($request->get('start'))
                            ->select(
                                'id',
                                'roomname',
                                'capacity',
                                'buildingid'
                            )
                            ->get();

            $room_count = DB::table('rooms')
            ->where('deleted',0)
            ->where(function($query) use($search){
                if($search != null){
                    $query->where('roomname','like','%'.$search.'%');
                }
            })
            ->count();

            if(count($rooms) < 10){
                $rooms = collect($rooms)->toArray();
                $lacking = 10 - count($rooms);
                for($x=0;$x <= $lacking; $x++){
                    array_push( $rooms , (object)[
                        'roomname'=>null,
                        'capacity'=>null,
                        'buildingid'=>null,
                        'id'=>null
                    ]);
                }
            }

            return @json_encode((object)[
                'data'=>$rooms,
                'recordsTotal'=>$room_count,
                'recordsFiltered'=>$room_count
            ]);


            // $id = $request->get('id');

            // $results = DB::table('building')
            // ->join('rooms', 'building.id', '=', 'rooms.buildingid')
            // ->where('building.id', $id)
            // ->where('building.deleted', 0)
            // ->where('rooms.deleted', 0)
            // ->select(
            //     'building.id',
            //     'building.description',
            //     'building.capacity',
            //     'rooms.id',
            //     'rooms.roomname',
            //     'rooms.capacity'
            // )
            
            // ->get();

            // return @json_encode((object)[
            //     'data'=>$results
            // ]);

            // return @json_encode((object)[
            //     'data'=>$results,
            //     'recordsTotal'=>$building_count,
            //     'recordsFiltered'=>$building_count
            // ]);
            
        } catch(\Exception $e) {
            return self::store_error($e);
        }

    }

    public static function getBuildingsSelect(Request $request){

        try{

            $search = $request->get('search');
    
            $buildings = DB::table('building')
                        ->where('deleted',0)
                        ->where(function($query) use($search){
                            if($search != null && $search != ""){
                                $query->orWhere('description','like','%'.$search.'%');
                            }
                        })
                        ->select(
                            'description as text',
                            'capacity',
                            'id'
                        )
                        ->take(10)
                        ->skip($request->get('page')*10)
                        ->get();
    
            $buildings_count = DB::table('building')
                        ->where('deleted',0)
                        ->where(function($query) use($search){
                            if($search != null && $search != ""){
                                $query->orWhere('description','like','%'.$search.'%');
                            }
                        })
                        ->count();
    
            return @json_encode((object)[
                  "results"=>$buildings,
                  "pagination"=>(object)[
                        "more"=>$buildings_count > 10  ? true :false
                  ],
                  "count_filtered"=>$buildings_count
            ]);
        
        }catch(\Exception $e){
            return self::store_error($e);
        }

    }

    public static function buildingCreate(Request $request){

        $description = $request->get('description');
        $capacity = $request->get('capacity');
        
        try{

            $check = DB::table('building')
                        ->where('deleted',0)
                        ->where('description',$description)
                        ->count();

            if($check > 0){
                return array((object)[
                    'status'=>0,
                    'message'=>'Building Exist',
                    'icon'=>'error',
                ]);
            }

            DB::table('building')
                ->insert([
                    'bldgsyncstat'=>0,
                    'description'=>$description,
                    'capacity'=>$capacity,
                    'createdby'=>auth()->user()->id,
                    'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                ]);
           
            return array((object)[
                'status'=>1,
                'message'=>'Building Created',
                'icon'=>'success',
            ]);
    
        
        }catch(\Exception $e){
            return self::store_error($e);
        }

    }


    public static function buildingUpdate(Request $request){

        $description = $request->get('description');
        $capacity = $request->get('capacity');
        $id = $request->get('id');
        
        try{

            $check = DB::table('building')
                        ->where('id','!=',$id)
                        ->where('deleted',0)
                        ->where('description',$description)
                        ->count();

            if($check > 0){
                return array((object)[
                    'status'=>0,
                    'message'=>'Building Exist',
                    'icon'=>'error'
                ]);
            }

            DB::table('building')
                ->where('id',$id)
                ->take(1)
                ->update([
                    'bldgsyncstat'=>2,
                    'description'=>$description,
                    'capacity'=>$capacity,
                    'updatedby'=>auth()->user()->id,
                    'updateddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                ]);
           
            return array((object)[
                'status'=>1,
                'message'=>'Building Updated',
                'icon'=>'success',
            ]);
    
        
        }catch(\Exception $e){
            return self::store_error($e);
        }

    }

    public static function buildingDelete(Request $request){

        $description = $request->get('description');
        $capacity = $request->get('capacity');
        $id = $request->get('id');
        
        try{

            $check = DB::table('rooms')
                        ->where('buildingid',$id)
                        ->where('deleted',0)
                        ->count();

            if($check > 0){
                return array((object)[
                    'status'=>0,
                    'message'=>'Already Used',
                    'icon'=>'error'
                ]);
            }

            DB::table('building')
                ->where('id',$id)
                ->take(1)
                ->update([
                    'bldgsyncstat'=>3,
                    'deleted'=>1,
                    'deletedby'=>auth()->user()->id,
                    'deleteddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                ]);
           
            return array((object)[
                'status'=>1,
                'message'=>'Building Deleted',
                'icon'=>'success',
            ]);
    
        
        }catch(\Exception $e){
            return self::store_error($e);
        }

    }

    public static function syncNew(Request $request){
        try{

            $tablename = $request->get('tablename');
            $data = $request->get('data');

            DB::table($tablename)   
                ->insert($data);

        }catch(\Exception $e){
            return self::store_error($e);
        }
    }

    public static function syncUpdate(Request $request){
        try{

            $tablename = $request->get('tablename');
            $data = $request->get('data');
            $dataid = $data['id'];

            DB::table($tablename)
                ->take(1)
                ->where('id',$dataid)
                ->update($data);

        }catch(\Exception $e){
            return self::store_error($e);
        }
    }

    public static function syncDelete(Request $request){
        try{

            $tablename = $request->get('tablename');
            $data = $request->get('data');
            $dataid = $data['id'];

            DB::table($tablename)
                ->take(1)
                ->where('id',$dataid)
                ->update([
                    'deleted'=>1,
                    'deleteddatetime'=>$data['deleteddatetime']
                ]);

        }catch(\Exception $e){
            return self::store_error($e);
        }
    }

    public static function getNewInfo(Request $request){
        $tablename = $request->get('tablename');

        $table_date = DB::table($tablename)
                        ->where('bldgsyncstat',0)
                        ->get();

        return $table_date;
    }

    public static function getUpdateInfo(Request $request){
        $tablename = $request->get('tablename');    

        $table_date = DB::table($tablename)
                        ->where('bldgsyncstat',2)
                        ->get();

        return $table_date;
    }

    public static function getDeleteInfo(Request $request){
        $tablename = $request->get('tablename');

        $table_date = DB::table($tablename)
                        ->where('bldgsyncstat',3)
                        ->get();

        return $table_date;
    }

    public static function getUpdateStat(Request $request){
        $tablename = $request->get('tablename');
        $data = $request->get('data');

        DB::table($tablename)
                        ->where('id', $data['id'])
                        ->take(1)
                        ->update([
                            'bldgsyncstat'=>1,
                            'bldgsyncstatdate'=>\Carbon\Carbon::now('Asia/Manila')
                        ]);
    }
    
    
    public static function store_error($e){
        DB::table('zerrorlogs')
        ->insert([
                    'error'=>$e,
                    // 'createdby'=>auth()->user()->id,
                    'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                    ]);
        return array((object)[
              'status'=>0,
              'icon'=>'error',
              'message'=>'Something went wrong!'
        ]);
    }


}
