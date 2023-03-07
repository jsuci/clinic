<?php

namespace App\Http\Controllers\ClinicControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\SchoolClinic\SchoolClinic;
class PersonnelController extends Controller
{
    public function index()
    {
        // $usertypeids = DB::table('usertype')
        //     ->select('id')
        //     ->whereIn('refid',[23,24,25])
        //     ->where('deleted','0')
        //     ->get();

        // $usertypeids = collect($usertypeids)->pluck('id');
        
        $personnel = SchoolClinic::personnel();
        // return $personnel;
        return view('clinic.personnel.index')
            ->with('personnel', $personnel);
    }
    // public function getemployees()
    // {
    //     $users = DB::table('teacher')
    //         ->join('usertype','teacher.usertypeid','=','usertype.id')
    //         ->where('teacher.deleted','0')
    //         ->get();

        
    //         return $users;
    // }
}
