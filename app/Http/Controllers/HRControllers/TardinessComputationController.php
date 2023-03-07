<?php

namespace App\Http\Controllers\HRControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
class TardinessComputationController extends Controller
{
    public function index(Request $request)
    {
        $computations = DB::table('hr_tardinesscomp')
            ->where('deleted','0')
            ->get();

        $departments = DB::table('hr_departments')
            ->where('deleted','0')
            ->get();
            
        return view('hr.settings.tardinesscomp.index')
            ->with('computations',$computations)
            ->with('departments',$departments);
    }
    public function getbrackets(Request $request)
    {
        $computations = DB::table('hr_tardinesscomp')
            ->where('departmentid',$request->get('deptid'))
            ->where('deleted','0')
            ->get();
            
        return view('hr.settings.tardinesscomp.brackets')
            ->with('deptid',$request->get('deptid'))
            ->with('computations',$computations);
        
    }
    public function addbrackets(Request $request)
    {
        $brackets = json_decode($request->get('brackets'));
        // return $bracket;
        if(count($brackets)>0)
        {
            foreach($brackets as $bracket)
            {
                DB::table('hr_tardinesscomp')
                    ->insert([
                        'departmentid'      => $bracket->deptid,
                        'latefrom'          => $bracket->latefrom,
                        'lateto'            => $bracket->lateto,
                        'latetimetype'      => $bracket->timetype,
                        'deducttype'        => $bracket->deducttype,
                        'amount'            => $bracket->amount,
                        'createdby'         => auth()->user()->id,
                        'createddatetime'   => date('Y-m-d H:i:s')
                    ]);
            }
        }
        return 1;
    }
    public function updatebracket(Request $request)
    {
        // return $request->all();
        DB::table('hr_tardinesscomp')
            ->where('id', $request->get('dataid'))
            ->update([
                'latefrom'          =>  $request->get('latefrom'),
                'lateto'            =>  $request->get('lateto'),
                'latetimetype'      =>  $request->get('timetype'),
                'deducttype'        =>  $request->get('deducttype'),
                'amount'            =>  $request->get('amount'),
                'updatedby'         => auth()->user()->id,
                'updateddatetime'   => date('Y-m-d H:i:s')
            ]);

        return 1;
    }
    public function deletebracket(Request $request)
    {
        DB::table('hr_tardinesscomp')
            ->where('id', $request->get('id'))
            ->update([
                'deleted'           =>  1,
                'deletedby'         => auth()->user()->id,
                'deleteddatetime'   => date('Y-m-d H:i:s')
            ]);

        return 1;
    }
    public function activation(Request $request)
    {
        $countcomputations = DB::table('hr_tardinesscomp')
        ->where('departmentid', $request->get('deptid'))
        ->where('deleted','0')
        ->count();
        
        if($request->get('deptid') == 0)
        {
            DB::table('hr_tardinesscomp')
                ->where('deleted','0')
                ->update([
                    'isactive'      => 0
                ]);

        }else{

            DB::table('hr_tardinesscomp')
                ->where('departmentid', $request->get('deptid'))
                ->where('deleted','0')
                ->update([
                    'isactive'      => $request->get('isactive')
                ]);
        }

        if($countcomputations == 0)
        {
            return 3;
        }else{
            return 1;
        }
    }
}
