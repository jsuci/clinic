<?php

namespace App\Http\Controllers\SuperAdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class RegisterRFIDController extends Controller
{

    public static function rfidcard_list(){

        $rfid_list = Db::table('rfidcard')
                        ->where('deleted',0)
                        ->get();

        foreach($rfid_list as $item){
            $item->createddatetime = \Carbon\Carbon::create($item->createddatetime)->isoFormat('MMM DD, YYYY hh:mm A');
        }

        return $rfid_list;
    }
   
    public function storerfid($id,$schoolid){

        try{
            $rfidCount = DB::table('rfidcard')
                        ->where('rfidcode',$id)
                        ->count();

            if(  $rfidCount == 0){

                DB::table('rfidcard')
                    ->insert([
                        'rfidcode'=>$id,
                        'rfidschoolid'=>$schoolid,
                        'createddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                    ]);

                return array((object)[
                    'status'=>1,
                    'message'=>'RFID Registered!'
                ]);

            }{
                return array((object)[
                    'status'=>0,
                    'message'=>'Already Exist!'
                ]);
            }
        }catch(\Exception $e){
            return array((object)[
                'status'=>0,
                'message'=>'Something went wrong!'
            ]);

        }
       

    }

}
