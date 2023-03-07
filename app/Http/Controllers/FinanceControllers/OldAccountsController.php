<?php

namespace App\Http\Controllers\FinanceControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use TCPDF;
use App\FinanceModel;
use App\Models\Finance\FinanceUtilityModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class OldAccountsController extends Controller
{
    public function oldaccounts(Request $request)
    {
        return view('finance..oldaccounts');
    }

    // public function dcpr_generate(Request $request)
    // {
    //     $date = date_format(date_create($request->get('date')), 'Y-m-d');
    //     $orfrom = $request->get('orfrom');
    //     $orto = $request->get('orto');
    //     $type = $request->get('type');

    //     $items = array();
    //     $headerlist = '<tr>';
    //     $bodylist = '';
    //     $arraygtotal = array();
        
    //     array_push($items, 'OR NUMBER');
    //     array_push($items, 'NAME');
    //     array_push($items, 'TOTAL AMOUNT');

    //     if($type == 'date')
    //     {
    //         $chrngtrans = db::table('chrngtrans')
    //             ->select('chrngcashtrans.particulars')
    //             ->join('chrngcashtrans', 'chrngtrans.transno', '=', 'chrngcashtrans.transno')
    //             ->whereBetween('transdate', [$date . ' 00:00', $date . ' 23:59'])
    //             ->where('cancelled', 0)
    //             ->where('deleted', 0)
    //             ->groupBy('particulars')
    //             ->orderBy('particulars')
    //             ->get();
    //     }
    //     else
    //     {
    //         $chrngtrans = db::table('chrngtrans')
    //             ->select('chrngcashtrans.particulars')
    //             ->join('chrngcashtrans', 'chrngtrans.transno', '=', 'chrngcashtrans.transno')
    //             ->whereBetween('ornum', [$orfrom, $orto])
    //             ->where('cancelled', 0)
    //             ->where('deleted', 0)
    //             ->groupBy('particulars')
    //             ->orderBy('particulars')
    //             ->get();   
    //     }

    //     $genitem = '';
    //     $itemcount = count($chrngtrans);

    //     foreach($chrngtrans as $trans)
    //     {
    //         $item = '';

    //         if(strpos($trans->particulars, 'TUITION') !== false)
    //         {
    //             $item = 'TUITION';
    //         }
    //         else
    //         {
    //             $item = $trans->particulars;
    //         }

    //         if(!in_array($item, $items))
    //         {
    //             array_push($items, $item);
    //         }            
    //     }

    //     array_push($items, 'TOTAL');

    //     // print_r($items);

    //     foreach($items as $_item)
    //     {
    //         // echo $_item . '<br>';
    //         if(strpos($_item, 'Balance forwarded from SY') !== false)
    //         {
    //             $_item = 'BALANCE FORWARDED';
    //         }

    //         if(strpos($_item, 'TUITION') !== false)
    //         {
    //             $_item = 'TUITION';
    //         }

    //         if($_item == 'NAME')
    //         {
    //             $headerlist .='
    //                     <th class="text-center bg-gray-dark" style="width:300px !important">'.$_item.'</th>
    //             ';
    //         }
    //         else
    //         {
    //             $headerlist .='
    //                     <th class="text-center bg-gray-dark" style="width:120px">'.$_item.'</th>
    //             ';
    //         }
    //     }

    //     $headerlist .='</tr>';
    //     $array_bodylist = array();
    //     $grandtotal = 0;

        
    //     if($type == 'date')
    //     {
    //         $chrngtransaction = db::table('chrngtrans')
    //             // ->select('ornum', 'studname', 'amountpaid as totalamount')
    //             ->select(db::raw('ornum, studname, sum(amountpaid) as totalamount'))
    //             ->whereBetween('transdate', [$date . ' 00:00', $date . ' 23:59'])
    //             ->where('cancelled', 0)
    //             ->groupBy('ornum')
    //             ->orderBy('ornum')
    //             ->get();
    //     }
    //     else
    //     {
    //         $chrngtransaction = db::table('chrngtrans')
    //             // ->select('ornum', 'studname', 'amountpaid as totalamount')
    //             ->select(db::raw('ornum, studname, sum(amountpaid) as totalamount'))
    //             ->whereBetween('ornum', [$orfrom, $orto])
    //             ->where('cancelled', 0)
    //             ->groupBy('ornum')
    //             ->orderBy('ornum')
    //             ->get();
    //     }

    //     $sumtotal = array();

    //     // return $items;

    //     foreach($chrngtransaction as $_trans)
    //     {
    //         $bodylist .='
    //             <tr>
    //                 <td style="width:60px">'.$_trans->ornum.'</td>
    //                 <td style="width:300">'.$_trans->studname.'</td>
    //                 <td style="width:70px" class="text-right">'.number_format($_trans->totalamount, 2).'</td>
    //         ';  

    //         $grandtotal += $_trans->totalamount;

    //         foreach($items as $_item)
    //         {
    //             if($_item != 'OR NUMBER' && $_item != 'NAME' && $_item != 'TOTAL AMOUNT' && $_item != 'TOTAL')
    //             {
    //                 // echo $_trans->ornum . ' - ' . $_item . '<br>';
    //                 $trx = db::table('chrngtrans')
    //                     ->select(db::raw('ornum, studname, sum(chrngtrans.amountpaid) as totalamount, chrngcashtrans.particulars, sum(chrngcashtrans.amount) as amount'))
    //                     ->join('chrngcashtrans', 'chrngtrans.transno', '=', 'chrngcashtrans.transno')
    //                     ->where('ornum', $_trans->ornum)
    //                     ->where(function($q) use($_item){
    //                         if(strpos($_item, 'TUITION') !== false)
    //                         {
    //                             $q->where('chrngcashtrans.particulars', 'like', '%TUITION%');
    //                         }
    //                         else
    //                         {
    //                             $q->where('chrngcashtrans.particulars', $_item);
    //                         }
    //                     })
    //                     // ->where('chrngcashtrans.particulars', 'like', '%'.$_item.'%')
    //                     // ->where(function($q) use($_item){
    //                     //     if($_item == 'PTA FEE')
    //                     //     {
    //                     //          $q->where('chrngcashtrans.particulars', 'like', '%PTA%');
    //                     //     }
    //                     //     else
    //                     //     {
    //                     //         $q->where('chrngcashtrans.particulars', 'like', '%'.$_item.'%');   
    //                     //     }


    //                     // })
    //                     ->where('chrngcashtrans.deleted', 0)
    //                     ->where('cancelled', 0)
    //                     ->first();

    //                 if($trx->ornum != null)
    //                 {   
    //                     // echo 'ornum: ' . $trx->ornum . ' particulars: ' . $trx->particulars . ' amount: ' . $trx->amount . '<br>';
    //                     $arrayamount = 0;

    //                     array_push($arraygtotal, (object)[
    //                         $_item => $trx->amount
    //                     ]);

    //                     $bodylist .='
    //                         <td style="width:70px" class="text-right">'.number_format($trx->amount, 2).'</td>
    //                     ';
    //                 }
    //                 else
    //                 {
    //                     $bodylist .='<td style="width:70px"></td>';
    //                 }
    //             }
    //         }

    //         $bodylist .='<td style="width:70ox" class="text-right">'.number_format($_trans->totalamount, 2).'</td>';
    //         $bodylist .='</tr>';
    //     }

    //     // return $arraygtotal;

    //     $bodylist .='
    //         <tr>
    //             <td colspan="2" class="text-right text-bold">TOTAL:</td>
    //             <td class="text-right text-bold">'.number_format($grandtotal, 2).'</td>
            
    //     ';

    //     $arraygtotal = collect($arraygtotal);

        

    //     foreach($items as $_item)
    //     {
    //         if($type == 'date')
    //         {
    //             $sum = db::table('chrngtrans')
    //                 ->select(db::raw('sum(chrngcashtrans.amount) as amount'))
    //                 ->join('chrngcashtrans', 'chrngtrans.transno', '=', 'chrngcashtrans.transno')
    //                 ->whereBetween('transdate', [$date . ' 00:00', $date . ' 23:59'])
    //                 ->where('cancelled', 0)
    //                 ->where('deleted', 0)
    //                 // ->where('chrngcashtrans.particulars', 'like', '%'.$_item.'%')
    //                 ->where(function($q) use($_item){
    //                     if(strpos($_item, 'TUITION') !== false)
    //                     {
    //                         $q->where('chrngcashtrans.particulars', 'like', '%TUITION%');
    //                     }
    //                     else
    //                     {
    //                         $q->where('chrngcashtrans.particulars', $_item);
    //                     }
    //                 })
    //                 ->first();
    //         }
    //         else
    //         {
    //             $sum = db::table('chrngtrans')
    //                 ->select(db::raw('sum(chrngcashtrans.amount) as amount'))
    //                 ->join('chrngcashtrans', 'chrngtrans.transno', '=', 'chrngcashtrans.transno')
    //                 ->whereBetween('ornum', [$orfrom, $orto])
    //                 ->where('cancelled', 0)
    //                 ->where('deleted', 0)
    //                 ->where(function($q) use($_item){
    //                     if($_item != 'OR')
    //                     {
    //                         $q->where('chrngcashtrans.particulars', 'like', '%'.$_item.'%');
    //                     }
    //                     else
    //                     {
    //                         $q->where('chrngcashtrans.particulars', 'like', '%aaaaaaaa%');
    //                     }
    //                 })
    //                 ->first();   
    //         }

    //         if($sum->amount > 0)
    //         {
    //             array_push($sumtotal, $sum->amount);
    //         }

    //     }

    //     // return $sumtotal;

    //     foreach($sumtotal as $_sum)
    //     {
    //         $bodylist .='
    //             <td class="text-right text-bold">'.number_format($_sum, 2).'</td>
    //         ';
    //     }

    //     $bodylist .='
    //         <td class="text-right text-bold">'.number_format($grandtotal, 2).'</td>
    //     ';

    //     // return $sumtotal;

    //     $data = array(
    //         'headerlist' => $headerlist,
    //         'bodylist' => $bodylist,
    //         'itemcount' => $itemcount
    //     );

    //     echo json_encode($data);

    // }

    public function oa_loadsy(Request $request)
    {
        $levelid = $request->get('levelid');
        $sylist ='';
        $semlist ='';

        if($levelid >= 17 && $levelid <= 20)
        {
            $schoolyear = db::table('sy')
                ->orderBy('sydesc')
                ->get();

            if(FinanceModel::getSemID() == 2)
            {
                foreach($schoolyear as $sy)
                {
                    if($sy->isactive == 1)
                    {
                        $sylist .='
                            <option value="'.$sy->id.'">'.$sy->sydesc.'</option>
                        ';    
                        break;
                    }
                    else
                    {
                        $sylist .='
                            <option value="'.$sy->id.'">'.$sy->sydesc.'</option>
                        ';    
                    }
                    
                }
            }
            else
            {
                foreach($schoolyear as $sy)
                {
                    // echo $sy->isactive . ' ' . $sy->sydesc . '<br>';
                    if($sy->isactive == 1)
                    {
                        break;
                    }
                    else
                    {
                        $sylist .='
                            <option value="'.$sy->id.'">'.$sy->sydesc.'</option>
                        ';
                    }
                }   
            }
        }
        elseif($levelid == 14 || $levelid == 15)
        {
            $schoolyear = db::table('sy')
                ->orderBy('sydesc')
                ->get();

            if(db::table('schoolinfo')->first()->shssetup == 0)
            {
                if(FinanceModel::getSemID() == 2)
                {
                    foreach($schoolyear as $sy)
                    {
                        if($sy->isactive == 1)
                        {
                            $sylist .='
                                <option value="'.$sy->id.'">'.$sy->sydesc.'</option>
                            ';    
                            break;
                        }
                        else
                        {
                            $sylist .='
                                <option value="'.$sy->id.'">'.$sy->sydesc.'</option>
                            ';    
                        }
                        
                    }
                }
                else
                {
                    foreach($schoolyear as $sy)
                    {
                        // echo $sy->isactive . ' ' . $sy->sydesc . '<br>';
                        if($sy->isactive == 1)
                        {
                            break;
                        }
                        else
                        {
                            $sylist .='
                                <option value="'.$sy->id.'">'.$sy->sydesc.'</option>
                            ';
                        }
                    }   
                }                
            }
            else
            {
                foreach($schoolyear as $sy)
                {
                    if($sy->isactive == 1)
                    {
                        break;
                    }
                    else
                    {
                        $sylist .='
                            <option value="'.$sy->id.'">'.$sy->sydesc.'</option>
                        ';
                    }
                }
            }
        }
        else
        {
            $schoolyear = db::table('sy')
                ->orderBy('sydesc')
                ->get();

                foreach($schoolyear as $sy)
                {
                    if($sy->isactive == 1)
                    {
                        break;
                    }
                    else
                    {
                        $sylist .='
                            <option value="'.$sy->id.'">'.$sy->sydesc.'</option>
                        ';
                    }
                }
        }

        $shssetup = db::table('schoolinfo')->first()->shssetup;

        $data = array(
            'shssetup' => $shssetup,
            'sylist'=> $sylist,
            'semactive' => FinanceModel::getSemID(),
            'syactive' => FinanceModel::getSYID()
        );

        echo json_encode($data);
    }

    public function oa_load(Request $request)
    {
        $levelid = $request->get('levelid');
        $syid = $request->get('syid');
        $semid = $request->get('semid');
        $filter = $request->get('filter');

        $oaclassid = db::table('balforwardsetup')->first()->classid;
        $old_list = '';

        $oldaccounts = db::table('studledger')
            ->select(db::raw('CONCAT(lastname, ", ", firstname) AS fullname, SUM(amount) AS amount, SUM(payment) AS payment, SUM(amount) - SUM(payment) AS balance, studid, sid'))
            ->join('studinfo', 'studledger.studid', '=', 'studinfo.id')
            ->where('studledger.deleted', 0)
            ->where('syid', $syid)
            ->where('levelid', $levelid)
            ->where(function($q) use($levelid, $semid){
                if($levelid == 14 || $levelid ==15)
                {
                    if(db::table('schoolinfo')->first()->shssetup == 0)
                    {
                        $q->where('semid', $semid);
                    }
                }
                if($levelid >= 17 && $levelid <= 20)
                {
                    $q->where('studledger.semid', $semid);
                }
            })
            ->groupBy('studid')
            ->having('balance', '>', 0)
            ->having('fullname', 'like', '%'.$filter.'%')
            ->get();

        foreach($oldaccounts as $old)
        {
            $old_list .='
                <tr>
                    <td class="fullname">'.$old->sid . ' - ' . $old->fullname.'</td>
                    <td class="text-right">'.number_format($old->amount, 2).'</td>
                    <td class="text-right">'.number_format($old->payment, 2).'</td>
                    <td class="text-right">'.number_format($old->balance, 2).'</td>
                    <td class="text-center">
                        <button id="" class="btn btn-primary btn-sm oa_forward" data-toggle="tooltip" title="Fordward Old Account" data-id="'.$old->studid.'" data-amount="'.$old->balance.'">
                            <i class="fas fa-external-link-alt"></i>
                        </button>
                        <button class="btn btn-success btn-sm v-ledger" data-toggle="tooltip" title="View Ledger" data-id="'.$old->studid.'">
                            <i class="fas fa-file-invoice"></i>
                        </button>
                    </td>
                </t>
            ';
        }

        // return $old_list;

        $data = array(
            'list' => $old_list
        );

        echo json_encode($data);
    }

    public function oa_forward(Request $request)
    {
        $studid = $request->get('studid');
        $syfrom = $request->get('syfrom');
        $semfrom = $request->get('semfrom');
        $amount = $request->get('amount');
        $action = $request->get('action');
        $tempamount = 0;

        if($semfrom == null)
        {
            $semfrom = 1;
        }

        $sy = db::table('sy')
            ->where('id', $syfrom)
            ->first();

        $sem = db::table('semester')
            ->where('id', $semfrom)
            ->first();

        $syid = FinanceModel::getSYID();
        $semid = FinanceModel::getSemID();

        $studinfo = db::table('studinfo')
            ->select('id', 'levelid')
            ->where('id', $studid)
            ->first();

        $levelid = $studinfo->levelid;

        if($syfrom == $syid)
        {
            if($semfrom == $semid)
            {
                return 'error';
            }
        }

        $balclassid = db::table('balforwardsetup')->first()->classid;

        $particulars = 'Balance forwarded from SY ' . $sy->sydesc . ' ' . $sem->semester;
        $reverse_particulars = 'Balance forwarded to SY ' . $sy->sydesc . ' ' . $sem->semester;

        $studledger = db::table('studledger')
            ->where('studid', $studid)
            ->where('syid', FinanceModel::getSYID())
            ->where('semid', FinanceModel::getSemID())
            ->where('particulars', 'like',  '%'.$particulars.'%')
            ->where('deleted', 0)
            ->first();

        if(!$studledger)
        {
            $oldledger = db::table('studledger')
                ->select(db::raw('SUM(amount) - SUM(payment) AS balance'))
                ->where('studid', $studid)
                ->where('syid', $syfrom)
                ->where(function($q)use($levelid, $semfrom){
                    if($levelid == 14 || $levelid == 15)
                    {
                        if(db::table('schoolinfo')->first()->shssetup == 0)
                        {
                            $q->where('semid', $semfrom);
                        }
                    }
                    if($levelid >= 17 && $levelid <= 20)
                    {
                        $q->where('semid', $semfrom);
                    }
                })
                ->where('deleted', 0)
                ->where('void', 0)
                ->first();

            if($oldledger)
            {
                if($oldledger->balance > 0)
                {
                    if($action == 'create')
                    {
                        $tempamount = $amount;
                        $reverse_particulars = $reverse_particulars . ' - Amount: ' . $amount;
                        $amount = $oldledger->balance;
                    }
                    else{
                        $tempamount = $amount;
                    }

                    db::table('studledger')
                        ->insert([
                            'studid' => $studid,
                            'syid' => $syfrom,
                            'semid' => $semfrom,
                            'classid' => $balclassid,
                            'particulars' =>$reverse_particulars,
                            'payment' => $amount,
                            'createddatetime' => FinanceModel::getServerDateTime(),
                            'deleted' => 0,
                            'void' => 0
                        ]);

                    $itemized = db::table('studledgeritemized')
                        ->where('studid', $studid)
                        ->where('syid', $syfrom)
                        ->where(function($q)use($levelid, $semfrom){
                            if($levelid == 14 || $levelid == 15)
                            {
                                if(db::table('schoolinfo')->first()->shssetup == 0)
                                {
                                    $q->where('semid', $semfrom);
                                }
                            }
                            if($levelid >= 17 && $levelid <= 20)
                            {
                                $q->where('semid', $semfrom);
                            }
                        })
                        ->where('deleted', 0)
                        ->whereColumn('totalamount', '!=', 'itemamount')
                        ->get();

                    foreach($itemized as $item)
                    {
                        db::table('studledgeritemized')   
                            ->where('id', $item->id)
                            ->update([
                                'totalamount' => $item->itemamount
                            ]);
                    }

                    $payscheddetail = db::table('studpayscheddetail')
                        ->where('studid', $studid)
                        ->where('syid', $syfrom)
                        ->where(function($q)use($levelid, $semfrom){
                            if($levelid == 14 || $levelid == 15)
                            {
                                if(db::table('schoolinfo')->first()->shssetup == 0)
                                {
                                    $q->where('semid', $semfrom);
                                }
                            }
                            if($levelid >= 17 && $levelid <= 20)
                            {
                                $q->where('semid', $semfrom);
                            }
                        })
                        ->where('deleted', 0)
                        ->where('balance', '>', 0)
                        ->get();

                    foreach($payscheddetail as $detail)
                    {
                        db::table('studpayscheddetail')
                            ->where('id', $detail->id)
                            ->update([
                                'amountpay' => $detail->amountpay + $detail->balance,
                                'balance' => 0,
                                'updateddatetime' => FinanceModel::getServerDateTime()
                            ]);
                    }

                }
				else{
                    $tempamount = $amount;
                }
            }

            db::table('studledger')
                ->insert([
                    'studid' => $studid,
                    'syid' => FinanceModel::getSYID(),
                    'semid' => FinanceModel::getSemID(),
                    'classid' => $balclassid,
                    'particulars' =>$particulars,
                    'amount' => $tempamount,
                    'createddatetime' => FinanceModel::getServerDateTime(),
                    'deleted' => 0,
                    'void' => 0
                ]);

            FinanceUtilityModel::resetv3_generateoldaccounts($studid, $levelid, $syid, $semid);

            return 'done';
        }
        else
        {
            return 'exist';
        }
    }
	
    public function oa_setup(Request $request)
    {
        $setup = db::table('balforwardsetup')
            ->first();



        if($setup)
        {
            $data = array(
                'classid' => $setup->classid,
                'mopid' => $setup->mopid
            );

            return $data;
        }
    }

    public function oa_setupsave(Request $request)
    {
        $classid = $request->get('classid');
        $mop = $request->get('mop');

        db::table('balforwardsetup')
            ->where('id', 1)
            ->update([
                'classid' => $classid,
                'mopid' => $mop
            ]);
    }

    public function old_add_studlist(Request $request)
    {
        if($request->ajax())
        {
            $studid = $request->get('studid');
            $studlist = '<option value="0">NAME</option>';
            $sylist = '<option value="0">School Year</option>';
            $semlist = '<option value="0">Semester</option>';
            if($studid > 0)
            {
                $stud = db::table('studinfo')
                    ->select('studinfo.id', 'levelname', 'levelid', 'sectionid', 'courseid', 'grantee.description as grantee')
                    ->join('gradelevel', 'studinfo.levelid', '=', 'gradelevel.id')
                    ->join('grantee', 'studinfo.grantee', '=', 'grantee.id')
                    ->where('studinfo.id', $studid)
                    ->first();

                if($stud)
                {
                    $section = '';

                    if($stud->levelid >= 17 && $stud->levelid <= 21)
                    {
                        $collegecourse = db::table('college_courses')
                            ->where('id', $stud->courseid)
                            ->first();

                        if($collegecourse)
                        {
                            $section = $collegecourse->courseabrv;
                        }

                        $sem = db::table('semester')
                            ->where('isactive', 1)
                            ->first();

                        if($sem->id == 1)
                        {
                            $schoolyear = db::table('sy')
                                ->where('sydesc', '<', FinanceModel::getSYDesc())
                                ->get();

                            foreach($schoolyear as $sy)
                            {
                                $sylist .='
                                    <option value="'.$sy->id.'">'.$sy->sydesc.'</option>
                                ';    
                            }

                            $_semester = db::table('semester')
                                ->get();

                            foreach($_semester as $_sem)
                            {
                                $semlist .='
                                    <option value="'.$_sem->id.'">'.$_sem->semester.'</option>
                                ';       
                            }
                        }
                        elseif($sem->id == 2)
                        {
                            $schoolyear = db::table('sy')
                                ->where('sydesc', '<=', FinanceModel::getSYDesc())
                                ->get();

                            foreach($schoolyear as $sy)
                            {
                                $sylist .='
                                    <option value="'.$sy->id.'">'.$sy->sydesc.'</option>
                                ';    
                            }
                        }

                        
                    }
                    else
                    {
                        $_section = db::table('sections')
                            ->where('id', $stud->sectionid)
                            ->first();

                        if($_section)
                        {
                            $section = $_section->sectionname;
                        }

                        $schoolyear = db::table('sy')
                            ->where('sydesc', '<', FinanceModel::getSYDesc())
                            ->get();

                        foreach($schoolyear as $sy)
                        {
                            $sylist .='
                                <option value="'.$sy->id.'">'.$sy->sydesc.'</option>
                            ';    
                        }
                    }

                    // return $section;

                    $data = array(
                        'levelname' => $stud->levelname,
                        'grantee' => $stud->grantee,
                        'section' => $section,
                        'levelid' => $stud->levelid,
                        'semlist' => $semlist,
                        'sylist' => $sylist
                    );
                }
            }
            else
            {
                $studinfo = db::table('studinfo')
                    ->select('id', 'sid', 'lastname', 'firstname')
                    ->where('deleted', 0)
                    ->orderBy('lastname')
                    ->orderBy('firstname')
                    ->get();

                foreach($studinfo as $stud)
                {
                    $studlist .='
                        <option value="'.$stud->id.'">'.$stud->sid.' - '.$stud->lastname.', '.$stud->firstname.' </option>
                    ';
                }

                $data = array(
                    'studlist' => $studlist
                );
            }

            echo json_encode($data);
        }
    }

    public function old_getsem(Request $request)
    {
        $syid = $request->get('syid');
        $semlist = '';

        if($syid == FinanceModel::getSYID())
        {
            $semlist .='
                <option value="0">Semester</option>
                <option value="1">1st Semester</option>
            ';
        }
        else
        {
            $semlist .='
                <option value="0">Semester</option>
                <option value="1">1st Semester</option>
                <option value="2">2nd Semester</option>
            ';   
        }

        return $semlist;
    }

    
}
class DCPR extends TCPDF {

    // //Page header
    // public function Header() {
    //     // Logo
    //     // $this->Image('@'.file_get_contents('/home/xxxxxx/public_html/xxxxxxxx/uploads/logo/logo.png'),10,6,0,13);
    //     $schoollogo = DB::table('schoolinfo')->first();
    //     $image_file = public_path().'/'.$schoollogo->picurl;
    //     $extension = explode('.', $schoollogo->picurl);
    //     $this->Image('@'.file_get_contents($image_file),20,9,17,17);

    //     if(strtolower($schoollogo->abbreviation) == 'msmi')
    //     {
    //         $this->Cell(0, 15, 'Page '.$this->getAliasNumPage(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    //         $this->Cell(0, 25, date('m/d/Y'), 0, false, 'R', 0, '', 0, false, 'T', 'M');   
    //     }
        
    //     $schoolname = $this->writeHTMLCell(false, 50, 40, 10, '<span style="font-weight: bold">'.$schoollogo->schoolname.'</span>', false, false, false, $reseth=true, $align='L', $autopadding=true);
    //     $schooladdress = $this->writeHTMLCell(false, 50, 40, 15, '<span style="font-weight: bold; font-size: 10px;">'.$schoollogo->address.'</span>', false, false, false, $reseth=true, $align='L', $autopadding=true);
    //     $title = $this->writeHTMLCell(false, 50, 40, 20, 'Cash Receipt Summary', false, false, false, $reseth=true, $align='L', $autopadding=true);
    //     // Ln();
    // }

    // Page footer
    public function Footer() {
        $schoollogo = DB::table('schoolinfo')->first();
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        // $this->Cell(0, 15, 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
        // $this->Cell(0, 5, date('m/d/Y'), 0, false, 'C', 0, '', 0, false, 'T', 'M');
        
        if(strtolower($schoollogo->abbreviation) != 'msmi')
        {
            $this->Cell(0, 10, date('l, F d, Y'), 0, false, 'L', 0, '', 0, false, 'T', 'M');
            $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
            // $this->Cell(0, 15, date('m/d/Y'), 0, false, 'R', 0, '', 0, false, 'T', 'M');   
        }
    }
}
