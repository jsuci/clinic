<?php

namespace App\Http\Controllers\FinanceControllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\FinanceModel;
use App\DisplayModel;
use App\Models\Finance\FinanceUtilityModel;
use DB;
use NumConvert;
use PDF;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
// use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class DiscountController extends Controller
{
    public function discounts()
    {
        return view('finance/discount_v2');
    }

    public function discount_setup()
    {
        $setup = db::table('discounts')
            ->where('deleted', 0)
            ->get();
        
        return $setup;
    }

    public function discount_setup_create(Request $request)
    {
        $particulars = $request->get('particulars');
        $amount = $request->get('amount');
        $percent = $request->get('percent');
        $dataid = $request->get('dataid');

        $check = db::table('discounts')
            ->where('particulars', $particulars)
            ->first();

        if($check)
        {
            return 'exist';
        }
        else{
            db::table('discounts')
                ->insert([
                    'particulars' => $particulars,
                    'amount' => $amount,
                    'percent' => $percent,
                    'deleted' => 0,
                    'createddatetime' => FinanceModel::getServerDateTime(),
                    'createdby' => auth()->user()->id
                ]);

            return 'done';
        }
    }

    public function discount_setup_read(Request $request)
    {
        $dataid = $request->get('dataid');

        $setup = db::table('discounts')
            ->where('id', $dataid)
            ->first();
        
        if($setup)
        {
            return collect($setup);
        }
    }

    public function discount_setup_update(Request $request)
    {
        $dataid = $request->get('dataid');
        $particulars = $request->get('particulars');
        $amount = $request->get('amount');
        $percent = $request->get('percent');

        $check = db::table('discounts')
            ->where('particulars', $particulars)
            ->where('id', '!=', $dataid)
            ->first();
        
        if($check)
        {
            return 'exist';
        }
        else{
            db::table('discounts')
                ->where('id', $dataid)
                ->update([
                    'particulars' => $particulars,
                    'amount' => $amount,
                    'percent' => $percent,
                    'updateddatetime' => FinanceModel::getServerDateTime(),
                    'updatedby' => auth()->user()->id
                ]);

            return 'done';
        }

    }

    public function discount_setup_delete(Request $request)
    {
        $dataid = $request->get('dataid');

        db::table('discounts')
            ->where('id', $dataid)
            ->update([
                'deleted' => 1,
                'deleteddatetime' => FinanceModel::getServerDateTime(),
                'deletedby' => auth()->user()->id
            ]);
    }

    public function discount_getstudents(Request $request)
    {
        $syid = $request->get('syid');
        $semid = $request->get('semid');
        $studarray = array();

        $enrolledstud = db::table('enrolledstud')
            ->select(db::raw('CONCAT(lastname, ", ", firstname, " - ", levelname) AS text, studid as id'))
            ->join('studinfo', 'enrolledstud.studid', '=', 'studinfo.id')
            ->join('gradelevel', 'enrolledstud.levelid', '=', 'gradelevel.id')
            ->where('enrolledstud.deleted', 0)
            ->where('enrolledstud.studstatus', '>', 0)
            ->where('syid', $syid)
            ->where(function($q) use($semid){
                if($semid == 3)
                {
                    $q->where('ghssemid', 3);
                }
                else{
                    $q->where('ghssemid', '!=', 3);
                }
            })
            ->get();

        // foreach($enrolledstud as $stud)
        // {
        //     array_push($studarray, (object)[
        //         'text' => $stud->text,
        //         'id' => $stud->id
        //     ]);
        // }

        $sh_enrolledstud = db::table('sh_enrolledstud')
            ->select(db::raw('CONCAT(lastname, ", ", firstname, " - ", levelname) AS text, studid as id'))
            ->join('studinfo', 'sh_enrolledstud.studid', '=', 'studinfo.id')
            ->join('gradelevel', 'sh_enrolledstud.levelid', '=', 'gradelevel.id')
            ->where('sh_enrolledstud.deleted', 0)
            ->where('sh_enrolledstud.studstatus', '>', 0)
            ->where('syid', $syid)
            ->where(function($q) use($semid){
                if($semid == 3)
                {
                    $q->where('sh_enrolledstud.semid', 3);
                }
                else{
                    if(db::table('schoolinfo')->first()->shssetup == 0)
                    {
                        $q->where('sh_enrolledstud.semid', $semid);
                    }
                    else{
                        $q->where('sh_enrolledstud.semid', '!=', 3);
                    }
                }
            })
			->groupBy('studid')
            ->get();
        
        $college_enrolledstud = db::table('college_enrolledstud')
            ->select(db::raw('CONCAT(lastname, ", ", firstname, " - ", levelname) AS text, studid as id'))
            ->join('studinfo', 'college_enrolledstud.studid', '=', 'studinfo.id')
            ->join('gradelevel', 'college_enrolledstud.yearLevel', '=', 'gradelevel.id')
            ->where('college_enrolledstud.deleted', 0)
            ->where('college_enrolledstud.studstatus', '>', 0)
            ->where('college_enrolledstud.syid', $syid)
            ->where('college_enrolledstud.semid', $semid)
            ->get();
			
        $students = collect();

        $students = $students->merge($enrolledstud);
        $students = $students->merge($sh_enrolledstud);
        $students = $students->merge($college_enrolledstud);
        // $stud = collect($students)->toArray();

        return $students->sortBy('text')->values();
        // return $students->values()->all();
        

        // $students = $studentssort();
        
        // return $students;
    }

    public function discount_charges(Request $request)
    {
        $studid = $request->get('studid');
        $syid = $request->get('syid');
        $semid = $request->get('semid');
        $charges = array();

        $stud = db::table('studinfo')
            ->select('levelid')
            ->where('id', $studid)
            ->first();

        $paysched = db::table('studpayscheddetail')
            ->select(db::raw('id, particulars, SUM(amount) AS amount, SUM(amountpay) AS amountpay, SUM(balance) AS balance, classid'))
            ->where('deleted', 0)
            ->where('studid', $studid)
            ->where('syid', $syid)
            ->where(function($q) use($semid, $stud){
                if($stud->levelid == 14 || $stud->levelid == 15)
                {
                    if($semid == 3)
                    {
                        $q->where('semid', 3);
                    }
                    else{
                        if(db::table('schoolinfo')->first()->shssetup == 0)
                        {
                            $q->where('semid', $semid);
                        }
                        else{
                            $q->where('semid', '!=', 3);
                        }
                    }
                }
                elseif($stud->levelid >=  17 && $stud->levelid <= 21)
                {
                    $q->where('semid', $semid);
                }
            })
            ->groupBy('classid')
            ->get();

        foreach($paysched as $ledger)
        {
            array_push($charges, (object)[
                'id' => $ledger->id,
                'classid' => $ledger->classid,
                'particulars' => $ledger->particulars,
                'balance' => number_format($ledger->amount, 2)
            ]);
        }

        return $charges;
    }

    public function discount_getsetup(Request $request)
    {
        $discountid = $request->get('discountid');

        $discounts = db::table('discounts')
            ->select('id', 'particulars', 'amount', 'percent')
            ->where('id', $discountid)
            // ->where('deleted', 0)
            ->get();

        return $discounts;
    }

    public function discount_post(Request $request)
    {
        $studid = $request->get('studid');
        $discinfo = $request->get('info');
        $syid = $request->get('syid');
        $semid = $request->get('semid');
        $discountid = $request->get('discountid');

        // return 'aaa';

        // return FinanceModel::getServerDateTime();
        $groupid = date_format(date_create(FinanceModel::getServerDateTime()), 'mdYHis');

        $levelid = 0;

        $einfo = db::table('enrolledstud')
            ->where('syid', $syid)
            ->where('studid', $studid)
            ->where('deleted', 0)
            ->first();
        
        if($einfo)
        {
            $levelid = $einfo->levelid;
        }
        else{
            $einfo = db::table('sh_enrolledstud')
                ->where('syid', $syid)
                ->where(function($q) use($semid){
                    if($semid == 3)
                    {
                        $q->where('semid', 3);
                    }
                    else{
                        if(db::table('schoolinfo')->first()->shssetup == 0)
                        {
                            $q->where('semid', $semid);
                        }
                        else{
                            $q->where('semid', '!=', 3);
                        }
                    }
                })
                ->where('studid', $studid)
                ->where('deleted', 0)
                ->first();

            if($einfo)
            {
                $levelid = $einfo->levelid;
            }
            else{
                $einfo = db::table('college_enrolledstud')
                    ->where('syid', $syid)
                    ->where('semid', $semid)
                    ->where('studid', $studid)
                    ->where('deleted', 0)
                    ->first();

                if($einfo)
                {
                    $levelid = $einfo->yearLevel;
                }
                else{
                    $levelid = db::table('studinfo')
                        ->where('id', $studid)
                        ->first()
                        ->levelid;
                }
            }
        }


        foreach($discinfo as $info)
        {
            $discount = str_replace('%', '', $info['discount']);
            $discount = str_replace(',', '', $info['discount']);
            $percent = 0;
            $discamount = str_replace(',', '', $info['discountamount']);

            if(strpos($info['discount'], '%') !== false)
            {
                $percent = 1;
            }
            else{
                $percent = 0;
            }
            
            // echo 'studid: ' . $studid .'<br>';
            // echo 'discountid ' . $discountid .'<br>';
            // echo 'syid ' . $syid <br>;
            // echo 'semid ' . $semid .'<br>';
            // echo 'classid ' . $info['classid'] .'<br>';
            // echo 'discount ' . $discount .'<br>';
            // echo 'percent ' . $percent .'<br>';
            // echo 'discamount ' . $info['discountamount'] .'<br> <br>'; 

            $studdiscountid = DB::table('studdiscounts')
                ->insertGetId([
                    'studid' => $studid,
                    'discountid' => $discountid,
                    'syid' => $syid,
                    'semid' => $semid,
                    'classid' => $info['classid'],
                    'discount' => $discount,
                    'percent' => $percent,
                    'discamount' => $discamount,
                    'groupid' => $groupid,
                    'posted' => 1,
                    'deleted' => 0,
                    'createdby' => auth()->user()->id,
                    'createddatetime' => FinanceModel::getServerDateTime()
                ]);
        }

        FinanceUtilityModel::resetv3_generatediscounts($studid, $levelid, $syid, $semid, $studdiscountid);

        return 'done';
        
    }
	
    public function discount_getdiscount(Request $request)
    {
        $syid = $request->get('syid');
        $semid = $request->get('semid');
        $filter = $request->get('filter');
        $discount_list = array();

        $discounts = db::table('studdiscounts')
            ->select(db::raw('sid, lastname, firstname, middlename, discounts.percent, particulars, classid, groupid, discounts.amount as discount, 
                discamount, discounts.id, studid, studdiscounts.syid, studdiscounts.semid, studdiscounts.id as studdiscountid'))
            ->join('discounts', 'studdiscounts.discountid', '=', 'discounts.id')
            ->join('studinfo', 'studdiscounts.studid', '=', 'studinfo.id')
            ->where('studdiscounts.deleted', 0)
            ->where('studdiscounts.syid', $syid)
            ->where(function($q) use($semid){
                if($semid != 0)
                {
                    $q->where('studdiscounts.semid', $semid);
                }
            })
            ->where(function($q) use($filter){
                if($filter != '')
                {
                    $q->where('lastname', 'like', '%'.$filter.'%')
                        ->orWhere('firstname', 'like', '%'.$filter.'%')
                        ->orWhere('particulars', 'like', '%'.$filter.'%')
						->orWhere('sid', 'like', '%'.$filter.'%');
                }
            })
            ->groupBy('discounts.id', 'sid')
            ->get();
        
        // return $discounts;
        
        foreach($discounts as $discount)
        {
            $discamount = '';

            if($discount->percent == 1)
            {
                $discamount = $discount->discount . '%';
            }
            else{
                $discamount = number_format($discount->discount, 2);
            }

            array_push($discount_list, (object)[
                'fullname' => $discount->sid . ' - ' . $discount->lastname . ', ' . $discount->firstname . ' ' . $discount->middlename,
                'particulars' => $discount->particulars,
                'discount' => $discamount,
                'groupid' => $discount->groupid,
                'discid' => $discount->id,
                'studid' => $discount->studid,
                'studdiscountid' => $discount->studdiscountid,
                'syid' => $discount->syid,
                'semid' => $discount->semid
            ]);
        }

        return $discount_list;
    }

    public function discount_read(Request $request)
    {
        $dataid = $request->get('dataid');

        $studdiscount = db::table('studdiscounts')
            ->where('id', $dataid)
			->where('deleted', 0)
            ->first();

        $grouparray = array();
        $discountinfo = array();
        $studid = $studdiscount->studid;
        $discountid = $studdiscount->discountid;
        // return $discountid;
        
        if($studdiscount->groupid != null && $studdiscount->groupid != '')
        {
            $groups = db::table('studdiscounts')
                ->where('groupid', $studdiscount->groupid)
				->where('deleted', 0)
                ->get();
            
            foreach($groups as $group)
            {
                $class = db::table('itemclassification')
                    ->where('id', $group->classid)
                    ->first();

                $sign = '';

                if($group->percent == 1)
                {
                    $sign = '%';
                }
                else{
                    $sign = '';
                }
                
                $classname = $class->description;

                array_push($grouparray, (object)[
                    'classid' => $group->classid,
                    'classname' => $classname,
                    'discount' => $group->discount . $sign,
                    'discamount' => number_format($group->discamount, 2)
                ]);
            }
        }
        else{

        }

        $data = array(
            'studid' => $studid,
            'discountid' => $discountid,
            'groups' => $grouparray,
            'syid' => $studdiscount->syid,
            'semid' => $studdiscount->semid
        );

        return $data;
    }
    

    
}