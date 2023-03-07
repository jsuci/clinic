<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use DB;
use Crypt;
use \Carbon\Carbon;
use \Carbon\CarbonPeriod;
use App\MoneyCurrency;
use PDF;
use DateTime;
use DateInterval;
use DatePeriod;
use Conversion;
class HRAllowances extends Model
{

    public static function getstandardallowances($employeeid,$payrollinfo, $employeeallowanceid)
    {
        $monthlypayroll = DB::table('hr_payrollv2')
            ->select('hr_payrollv2history.*','hr_payrollv2.datefrom','hr_payrollv2.dateto')
            ->join('hr_payrollv2history','hr_payrollv2.id','=','hr_payrollv2history.payrollid')
            ->whereYear('hr_payrollv2.datefrom',date('Y', strtotime($payrollinfo->datefrom)))
            ->whereMonth('hr_payrollv2.datefrom',date('m', strtotime($payrollinfo->datefrom)))
            ->where('hr_payrollv2.deleted','0')
            ->where('hr_payrollv2history.deleted','0')
            ->where('hr_payrollv2history.employeeid',$employeeid)
            ->get();

        $allowanceinfo = DB::table('employee_allowancestandard')
            ->where('id', $employeeallowanceid)
            ->first();
            
        $historyinfo = DB::table('hr_payrollv2history')
            ->select('id','configured','released')
            ->where('hr_payrollv2history.employeeid', $employeeid)
            ->where('payrollid', $payrollinfo->id)
            ->first();
        $paidforthismonth = 0;
        $lock = 0;
        $totalamount = 0;
        $amounttopay = 0;
        $paymenttype = 0; // 0 = full; 1 = half;
        
        if(count($monthlypayroll) == 0)
        {
            if($allowanceinfo)
            {
                $amounttopay = $allowanceinfo->amount;
                $totalamount = $allowanceinfo->amount;
            }
        }
        elseif(count($monthlypayroll) == 1)
        {            
            if($historyinfo)
            {
                if($historyinfo->id == $monthlypayroll[0]->id)
                {
                    $alloweinfo = DB::table('hr_payrollv2historydetail')
                        ->where('headerid', $payrollinfo->id)
                        ->where('particulartype',3)
                        ->where('deleted','0')
                        ->where('particularid',$employeeallowanceid)
                        ->first();
                        
                    if($alloweinfo)
                    {
                        $paymenttype = $alloweinfo->paymenttype;
                        $amounttopay = $alloweinfo->amountpaid;
                        $totalamount = $alloweinfo->totalamount;
                        if($payrollinfo->dateto > date('Y-m-15', strtotime($payrollinfo->dateto)))
                        {
                            $lock = 1;
                        }
                    }else{
                        $paymenttype = 3;
                        $amounttopay = $allowanceinfo->amount;
                        $totalamount = $allowanceinfo->amount;
                    }
                }else{
                    if(date('Y-m', strtotime($monthlypayroll[0]->datefrom)) == date('Y-m', strtotime($payrollinfo->datefrom)))
                    {
                        $alloweinfo = DB::table('hr_payrollv2historydetail')
                        ->where('headerid', $monthlypayroll[0]->id)
                            ->where('particulartype',3)
                            ->where('deleted','0')
                            ->where('particularid',$employeeallowanceid)
                            ->first();
            
                        if($alloweinfo)
                        {
                            if($alloweinfo->paymenttype == 0)
                            {
                                $paidforthismonth = 1;
                            }else{
                                $lock        = 1;
                            }
                            $paymenttype = $alloweinfo->paymenttype;
                            $amounttopay = $alloweinfo->amountpaid;
                            $totalamount = $alloweinfo->totalamount;
                        }else{
                            if($allowanceinfo)
                            {
                                $amounttopay = $allowanceinfo->amount;
                                $totalamount = $allowanceinfo->amount;
                                $lock        = 1;
                            }

                        }
                        
                    }else{
                        if($allowanceinfo)
                        {
                            $amounttopay = $allowanceinfo->amount;
                            $totalamount = $allowanceinfo->amount;
                        }
                    }
                }
            }else{
                if(date('Y-m', strtotime($monthlypayroll[0]->datefrom)) == date('Y-m', strtotime($payrollinfo->datefrom)))
                {
                    $alloweinfo = DB::table('hr_payrollv2historydetail')
                        ->where('headerid', $monthlypayroll[0]->id)
                        ->where('particulartype',3)
                        ->where('deleted','0')
                        ->where('particularid',$employeeallowanceid)
                        ->first();
        
                    if($alloweinfo)
                    {
                        if($alloweinfo->paymenttype == 1)
                        {
                            $paymenttype = 1;
                        }else{
                            $paidforthismonth = 1;
                        }
                        $amounttopay = $alloweinfo->amountpaid;
                        $totalamount = $alloweinfo->totalamount;
                        $lock        = 1;
                    }else{
                        if($allowanceinfo)
                        {
                            $amounttopay = $allowanceinfo->amount;
                            $totalamount = $allowanceinfo->amount;
                            $lock        = 1;
                        }

                    }
                    
                }else{
                    if($allowanceinfo)
                    {
                        $amounttopay = $allowanceinfo->amount;
                        $totalamount = $allowanceinfo->amount;
                    }
                }
            }

        }elseif(count($monthlypayroll) == 2)
        {
            $alloweinfo = DB::table('hr_payrollv2historydetail')
                ->where('headerid', collect($monthlypayroll)->where('payrollid', $payrollinfo->id)->first()->id)
                ->where('particulartype',3)
                ->where('deleted','0')
                ->where('particularid',$allowanceinfo->id)
                ->first();
                
            if($alloweinfo)
            {
                if(collect($monthlypayroll)->where('payrollid', $payrollinfo->id)->first()->released == 1)
                {
                    if($alloweinfo)
                    {
                        if($alloweinfo->paymenttype == 1)
                        {
                            $paymenttype = 1;
                        }else{
                            $paidforthismonth = 1;
                        }
                        $amounttopay = $alloweinfo->amountpaid;
                        $totalamount = $alloweinfo->totalamount;
                        $lock        = 1;
                    }
    
                }else{
                    if($alloweinfo)
                    {
                        if($alloweinfo->paymenttype == 1)
                        {
                            $paymenttype = 1;
                        }else{
                            $paidforthismonth = 1;
                        }
                        $amounttopay = $alloweinfo->amountpaid;
                        $totalamount = $alloweinfo->totalamount;
                        $lock        = 1;
                    }else{
                        if($allowanceinfo)
                        {
                            $amounttopay = $allowanceinfo->amount;
                            $totalamount = $allowanceinfo->amount;
                            $lock        = 1;
                        }
    
                    }
                }
            }else{
                $alloweinfo = DB::table('hr_payrollv2historydetail')
                    ->where('headerid', collect($monthlypayroll)->where('payrollid', '!=',$payrollinfo->id)->first()->id)
                    ->where('particulartype',3)
                    ->where('deleted','0')
                    ->where('particularid',$allowanceinfo->id)
                    ->first();
                    
                if($alloweinfo)
                {
                    if($alloweinfo->paymenttype == 0)
                    {
                        $paidforthismonth = 1;
                        $amounttopay = $alloweinfo->amountpaid;
                        $totalamount = $alloweinfo->totalamount;
                        $lock        = 1;
                    }
                }
            }

        }


        // if(count($balance) == 0)
        // {
        //     if($deductioninfo)
        //     {
        //         $amounttopay = $deductioninfo->eesamount;
        //     }
        // }else{
        //     $amounttopay = collect($balance)->whereMonth('createdatetime',date('m', strtotime($payrollinfo->datefrom)))->sum('balance');
        // }
        
        $allowanceinfo = (object)array(
            'lock'    => $lock,
            'paidforthismonth'    => $paidforthismonth,
            'totalamount'    => $totalamount,
            'amount'    => $amounttopay,
            'paymenttype'    => $paymenttype
        );

        return $allowanceinfo;
    }
    public static function standardallowances($payrolldatefrom, $payrollid, $employeeid){
        
        $standardallowances = array();

        $standardallowancesfullamount = 0;

        $getallowancestandard = Db::table('allowance_standard')
            ->select(
                'allowance_standard.id',
                'allowance_standard.description',
                'employee_allowancestandard.amount as eesamount'
            )
            ->join('employee_allowancestandard','allowance_standard.id','=','employee_allowancestandard.allowance_standardid')
            ->where('employee_allowancestandard.employeeid',$employeeid)
            ->where('employee_allowancestandard.status','1')
            ->where('allowance_standard.deleted','0')
            ->get();

        $row = DB::table('payroll')
            ->orderBy('id','asc')
            ->count();

        $newsid = $row -2;
        $lastpayroll = DB::table('payroll')
        ->orderBy('id','asc')
        ->skip($newsid)->take($newsid)->first();
        
        $currentpayrollmonth = strtolower(date('M', strtotime($payrolldatefrom)));
                
        $lastpayrollmonth = strtolower(date('M', strtotime($lastpayroll->dateto)));
        
        if(count($getallowancestandard) > 0){
        
            $checkifreleased = DB::table('payroll_history')
                            ->where('payroll_history.payrollid', $payrollid)
                            ->where('payroll_history.employeeid', $employeeid)
                            ->distinct()
                            ->get();
                            
            $standardallowancescontainer = array();
            if(count($checkifreleased) == 0){

                $payrollstatus = 0;
                
                if($currentpayrollmonth == $lastpayrollmonth && $row != 1)
                {
                    foreach($getallowancestandard as $allowancestandard){
    
                        $checkifsaved =  DB::table('payroll_historydetail')
                            ->where('payroll_historydetail.payrollid', $lastpayroll->id)
                            ->where('payroll_historydetail.employeeid', $employeeid)
                            ->where('payroll_historydetail.allowanceid', $allowancestandard->id)
                            ->where('payroll_historydetail.type', 'standard')
                            ->where('payroll_historydetail.deleted', '0')
                            ->distinct()
                            ->get();

                        if(count($checkifsaved) == 0)
                        {
    
                            $standardallowancesfullamount+=($allowancestandard->eesamount);
        
                            $allowancestandard->paymentoption = '2';
                            $allowancestandard->saved = '0';
                            $allowancestandard->forcefull = '1';
                        }else{
                            
                            if($checkifsaved[0]->paymentoption == 1)
                            {
                                $standardallowancesfullamount+=($checkifsaved[0]->amount);
            
                                $allowancestandard->eesamount = $checkifsaved[0]->amount;
                                $allowancestandard->paymentoption = $checkifsaved[0]->paymentoption;
                                $allowancestandard->saved = '1';
                                $allowancestandard->forcefull = '1';
                            }
                            elseif($checkifsaved[0]->paymentoption == 2)
                            {
                                // $standarddeductionsfullamount+=($checkifsaved[0]->amount);
                                // $standarddeductionsfullamount+=0;
            
                                $allowancestandard->eesamount = 0.00; //$checkifsaved[0]->amount;
                                $allowancestandard->paymentoption = $checkifsaved[0]->paymentoption;
                                $allowancestandard->saved = '1';
                                $allowancestandard->forcefull = '1';

                                $payrollstatus = 1;
                            }
                        }
    
                        array_push($standardallowancescontainer,$allowancestandard);
    
                    }
                    
                    array_push($standardallowances,(object)array(
                        'standardallowances' => $standardallowancescontainer,
                        'payrollstatus'      => $payrollstatus,
                        'fullamount'      => $standardallowancesfullamount
                    ));
                }else{
                    
                    foreach($getallowancestandard as $allowancestandard){
    
                        $checkifsaved =  DB::table('payroll_historydetail')
                            ->where('payroll_historydetail.payrollid', $payrollid)
                            ->where('payroll_historydetail.employeeid', $employeeid)
                            ->where('payroll_historydetail.allowanceid', $allowancestandard->id)
                            ->where('payroll_historydetail.type', 'standard')
                            ->where('payroll_historydetail.deleted', '0')
                            ->distinct()
                            ->get();
                            
                        if(count($checkifsaved) == 0)
                        {
    
                            $standardallowancesfullamount+=($allowancestandard->eesamount);
        
                            $allowancestandard->paymentoption = '2';
                            $allowancestandard->saved = '0';
                            $allowancestandard->forcefull = '0';
                        }else{
                            $standardallowancesfullamount+=($checkifsaved[0]->amount);
        
                            $allowancestandard->eesamount = $checkifsaved[0]->amount;
                            $allowancestandard->paymentoption = $checkifsaved[0]->paymentoption;
                            $allowancestandard->saved = '1';
                            $allowancestandard->forcefull = '0';
                        }
    
                        array_push($standardallowancescontainer,$allowancestandard);
    
                    }
                    
                    array_push($standardallowances,(object)array(
                        'standardallowances' => $standardallowancescontainer,
                        'payrollstatus'      => $payrollstatus,
                        'fullamount'      => $standardallowancesfullamount
                    ));
                }

            }else{
                if($currentpayrollmonth == $lastpayrollmonth && $row != 1)
                {
                    foreach($getallowancestandard as $allowancestandard){
    
                        $checkifsaved =  DB::table('payroll_historydetail')
                            ->where('payroll_historydetail.payrollid', $lastpayroll->id)
                            ->where('payroll_historydetail.employeeid', $employeeid)
                            ->where('payroll_historydetail.allowanceid', $allowancestandard->id)
                            ->where('payroll_historydetail.type', 'standard')
                            ->where('payroll_historydetail.deleted', '0')
                            ->distinct()
                            ->get();
                        if(count($checkifsaved) == 0)
                        {
    
                            $standardallowancesfullamount+=($allowancestandard->eesamount);
        
                            $allowancestandard->paymentoption = '2';
                            $allowancestandard->saved = '0';
                            $allowancestandard->forcefull = '1';
                        }else{
                            
                            if($checkifsaved[0]->paymentoption == 1)
                            {
                                $standardallowancesfullamount+=($checkifsaved[0]->amount);
            
                                $allowancestandard->eesamount = $checkifsaved[0]->amount;
                                $allowancestandard->paymentoption = $checkifsaved[0]->paymentoption;
                                $allowancestandard->saved = '1';
                                $allowancestandard->forcefull = '1';
                            }
                            elseif($checkifsaved[0]->paymentoption == 2)
                            {
                                // $standarddeductionsfullamount+=($checkifsaved[0]->amount);
                                // $standarddeductionsfullamount+=0;
            
                                $allowancestandard->eesamount = $checkifsaved[0]->amount;
                                $allowancestandard->paymentoption = $checkifsaved[0]->paymentoption;
                                $allowancestandard->saved = '1';
                                $allowancestandard->forcefull = '1';
                            }
                        }
    
                        array_push($standardallowancescontainer,$allowancestandard);
    
                    }
                    
                    array_push($standardallowances,(object)array(
                        'standardallowances' => $standardallowancescontainer,
                        'payrollstatus'      => 0,
                        'fullamount'      => $standardallowancesfullamount
                    ));
                }else{
                    foreach($getallowancestandard as $allowancestandard){
    
                        $checkifsaved =  DB::table('payroll_historydetail')
                            ->where('payroll_historydetail.payrollid', $payrollid)
                            ->where('payroll_historydetail.employeeid', $employeeid)
                            ->where('payroll_historydetail.allowanceid', $allowancestandard->id)
                            ->where('payroll_historydetail.type', 'standard')
                            ->where('payroll_historydetail.deleted', '0')
                            ->distinct()
                            ->get();
                        if(count($checkifsaved) == 0)
                        {
    
                            $standardallowancesfullamount+=($allowancestandard->eesamount);
        
                            $allowancestandard->paymentoption = '2';
                            $allowancestandard->saved = '0';
                            $allowancestandard->forcefull = '0';
                        }else{
                            $standardallowancesfullamount+=($checkifsaved[0]->amount);
        
                            $allowancestandard->eesamount = $checkifsaved[0]->amount;
                            $allowancestandard->paymentoption = $checkifsaved[0]->paymentoption;
                            $allowancestandard->saved = '1';
                            $allowancestandard->forcefull = '0';
                        }
    
                        array_push($standardallowancescontainer,$allowancestandard);
    
                    }
                    
                    array_push($standardallowances,(object)array(
                        'standardallowances' => $standardallowancescontainer,
                        'payrollstatus'      => 0,
                        'fullamount'      => $standardallowancesfullamount
                    ));
                }
            }
        }

        return $standardallowances;

    }

    
    public static function otherallowances($payrolldatefrom, $payrollid, $employeeid){

    
        $otherallowances = array();

        $otherallowancesfullamount = 0;

        $getallowanceothers = Db::table('employee_allowanceother')
            ->select(
                'employee_allowanceother.id',
                'employee_allowanceother.description',
                'employee_allowanceother.amount',
                'employee_allowanceother.term'
            )
            ->where('employee_allowanceother.employeeid',$employeeid)
            ->where('employee_allowanceother.paid','0')
            ->where('employee_allowanceother.deleted','0')
            ->get();
            
        $row = DB::table('payroll')
        ->orderBy('id','asc')
        ->count();

        $newsid = $row -2;
        $lastpayroll = DB::table('payroll')
        ->orderBy('id','asc')
        ->skip($newsid)->take($newsid)->first();
        
        $currentpayrollmonth = strtolower(date('M', strtotime($payrolldatefrom)));
                
        $lastpayrollmonth = strtolower(date('M', strtotime($lastpayroll->dateto)));
        
        if(count($getallowanceothers) > 0){

            $checkifreleased = DB::table('payroll_history')
                            ->where('payroll_history.payrollid', $payrollid)
                            ->where('payroll_history.employeeid', $employeeid)
                            ->distinct()
                            ->get();

            $otherallowancescontainer = array();
            if(count($checkifreleased) == 0){

                $payrollstatus = 0;
                
                if($currentpayrollmonth == $lastpayrollmonth && $row != 1)
                {
                    foreach($getallowanceothers as $allowanceother)
                    {
            
                            $checkifsaved =  DB::table('payroll_historydetail')
                                ->where('payroll_historydetail.payrollid', $lastpayroll->id)
                                ->where('payroll_historydetail.employeeid', $employeeid)
                                ->where('payroll_historydetail.allowanceid', $allowanceother->id)
                                ->where('payroll_historydetail.type', 'other')
                                ->where('payroll_historydetail.deleted', '0')
                                ->distinct()
                                ->get();
        
                            if(count($checkifsaved) == 0)
                            {
        
                                $otherallowancesfullamount+=($allowanceother->amount/$allowanceother->term);
            
                                $allowanceother->amount = ($allowanceother->amount/$allowanceother->term);
                                $allowanceother->paymentoption = '2';
                                $allowanceother->saved = '0';
                                $allowanceother->forcefull = '1';
                            }else{
                            
                                if($checkifsaved[0]->paymentoption == 1)
                                {
                                    $otherallowancesfullamount+=($checkifsaved[0]->amount);
                
                                    $allowanceother->amount = $checkifsaved[0]->amount;
                                    $allowanceother->paymentoption = $checkifsaved[0]->paymentoption;
                                    $allowanceother->saved = '1';
                                    $allowanceother->forcefull = '1';
                                }
                                elseif($checkifsaved[0]->paymentoption == 2)
                                {
                                    // $standarddeductionsfullamount+=($checkifsaved[0]->amount);
                                    // $standarddeductionsfullamount+=0;
                
                                    $allowanceother->amount = 0.00; //$checkifsaved[0]->amount;
                                    $allowanceother->paymentoption = $checkifsaved[0]->paymentoption;
                                    $allowanceother->saved = '1';
                                    $allowanceother->forcefull = '1';
    
                                    $payrollstatus = 1;
                                }
                                // $otherallowancesfullamount+=($checkifsaved[0]->amount);
            
                                // $allowanceother->amount = $checkifsaved[0]->amount;
                                // $allowanceother->paymentoption = $checkifsaved[0]->paymentoption;
                                // $allowanceother->saved = '1';
                            }
        
                            array_push($otherallowancescontainer,$allowanceother);
            
                        }
                        
                        array_push($otherallowances,(object)array(
                            'otherallowances' => $otherallowancescontainer,
                            'payrollstatus'      => $payrollstatus,
                            'fullamount'      => $otherallowancesfullamount
                        ));
                }else{
                    foreach($getallowanceothers as $allowanceother)
                    {
            
                        $checkifsaved =  DB::table('payroll_historydetail')
                            ->where('payroll_historydetail.payrollid', $payrollid)
                            ->where('payroll_historydetail.employeeid', $employeeid)
                            ->where('payroll_historydetail.allowanceid', $allowanceother->id)
                            ->where('payroll_historydetail.type', 'other')
                            ->where('payroll_historydetail.deleted', '0')
                            ->distinct()
                            ->get();
    
                        if(count($checkifsaved) == 0)
                        {
    
                            if($allowanceother->term == 0)
                            {
                                $otherallowancesfullamount+=$allowanceother->amount;
            
                                $allowanceother->amount = $allowanceother->amount;
                                $allowanceother->paymentoption = '2';
                                $allowanceother->saved = '0';
                                $allowanceother->forcefull = '1';
                            }else{
                                $otherallowancesfullamount+=($allowanceother->amount/$allowanceother->term);
            
                                $allowanceother->amount = ($allowanceother->amount/$allowanceother->term);
                                $allowanceother->paymentoption = '2';
                                $allowanceother->saved = '0';
                                $allowanceother->forcefull = '0';
                            }

                                
                        }else{
                            $otherallowancesfullamount+=($checkifsaved[0]->amount);
        
                            $allowanceother->amount = $checkifsaved[0]->amount;
                            $allowanceother->paymentoption = $checkifsaved[0]->paymentoption;
                            $allowanceother->saved = '1';
                            $allowanceother->forcefull = '0';
                        }
    
                        array_push($otherallowancescontainer,$allowanceother);
        
                    }
                    
                    array_push($otherallowances,(object)array(
                        'otherallowances' => $otherallowancescontainer,
                        'payrollstatus'      => $payrollstatus,
                        'fullamount'      => $otherallowancesfullamount
                    ));
                }
            }else{
                if($currentpayrollmonth == $lastpayrollmonth && $row != 1)
                {
                    foreach($getallowanceothers as $allowanceother)
                    {
            
                            $checkifsaved =  DB::table('payroll_historydetail')
                                ->where('payroll_historydetail.payrollid', $lastpayroll->id)
                                ->where('payroll_historydetail.employeeid', $employeeid)
                                ->where('payroll_historydetail.allowanceid', $allowanceother->id)
                                ->where('payroll_historydetail.type', 'other')
                                ->where('payroll_historydetail.deleted', '0')
                                ->distinct()
                                ->get();
        
                            if(count($checkifsaved) == 0)
                            {
        
                                $otherallowancesfullamount+=($allowanceother->amount/$allowanceother->term);
            
                                $allowanceother->amount = ($allowanceother->amount/$allowanceother->term);
                                $allowanceother->paymentoption = '2';
                                $allowanceother->saved = '0';
                                $allowanceother->forcefull = '1';
                            }else{
                            
                                if($checkifsaved[0]->paymentoption == 1)
                                {
                                    $otherallowancesfullamount+=($checkifsaved[0]->amount);
                
                                    $allowanceother->amount = $checkifsaved[0]->amount;
                                    $allowanceother->paymentoption = $checkifsaved[0]->paymentoption;
                                    $allowanceother->saved = '1';
                                    $allowanceother->forcefull = '1';
                                }
                                elseif($checkifsaved[0]->paymentoption == 2)
                                {
                                    // $standarddeductionsfullamount+=($checkifsaved[0]->amount);
                                    // $standarddeductionsfullamount+=0;
                
                                    $allowanceother->amount = 0.00; //$checkifsaved[0]->amount;
                                    $allowanceother->paymentoption = $checkifsaved[0]->paymentoption;
                                    $allowanceother->saved = '1';
                                    $allowanceother->forcefull = '1';
                                }
                                // $otherallowancesfullamount+=($checkifsaved[0]->amount);
            
                                // $allowanceother->amount = $checkifsaved[0]->amount;
                                // $allowanceother->paymentoption = $checkifsaved[0]->paymentoption;
                                // $allowanceother->saved = '1';
                            }
        
                            array_push($otherallowancescontainer,$allowanceother);
            
                        }
                        
                        array_push($otherallowances,(object)array(
                            'otherallowances' => $otherallowancescontainer,
                            'payrollstatus'      => 0,
                            'fullamount'      => $otherallowancesfullamount
                        ));
                }else{
                    foreach($getallowanceothers as $allowanceother)
                    {
            
                        $checkifsaved =  DB::table('payroll_historydetail')
                            ->where('payroll_historydetail.payrollid', $payrollid)
                            ->where('payroll_historydetail.employeeid', $employeeid)
                            ->where('payroll_historydetail.allowanceid', $allowanceother->id)
                            ->where('payroll_historydetail.type', 'other')
                            ->where('payroll_historydetail.deleted', '0')
                            ->distinct()
                            ->get();
    
                        if(count($checkifsaved) == 0)
                        {
    
                            $otherallowancesfullamount+=($allowanceother->amount/$allowanceother->term);
        
                            $allowanceother->amount = ($allowanceother->amount/$allowanceother->term);
                            $allowanceother->paymentoption = '2';
                            $allowanceother->saved = '0';
                            $allowanceother->forcefull = '0';
                        }else{
                            $otherallowancesfullamount+=($checkifsaved[0]->amount);
        
                            $allowanceother->amount = $checkifsaved[0]->amount;
                            $allowanceother->paymentoption = $checkifsaved[0]->paymentoption;
                            $allowanceother->saved = '1';
                            $allowanceother->forcefull = '0';
                        }
    
                        array_push($otherallowancescontainer,$allowanceother);
        
                    }
                    
                    array_push($otherallowances,(object)array(
                        'otherallowances' => $otherallowancescontainer,
                        'payrollstatus'      => 0,
                        'fullamount'      => $otherallowancesfullamount
                    ));
                }
            }
        }

                // $paidsofar = 0;

                // $checkpaid = Db::table('payroll_historydetail')
                //         ->where('payrollid', '!=',$payrollid)
                //         ->where('allowanceid', $allowanceother->id)
                //         ->where('type', 'otherallowance')
                //         ->get();

                // if(count($checkpaid) == 0)
                // {
                //     $allowanceother->allowancepermonth = $allowanceother->amount/$allowanceother->term;

                // }else{

                // }

                // return collect($allowanceother);

                // if(count($checkpaid)>0)
                // {
                //     foreach($checkpaid as $paid)
                //     {
                //         $paidsofar+=$paid->amount;
                //     }
                // }
                // if($paidsofar >= $allowanceother->amount)
                // {
                //     Db::table('employee_allowanceother')
                //         ->where('id', $allowanceother->id)
                //         ->update([
                //             'paid' => 1
                //         ]);
                //     // Db::table('employee_allowanceotherdetail')
                //     //     ->where('headerid', $allowanceother->id)
                //     //     ->update([
                //     //         'paid' => 1
                //     //     ]);
                // }
                // else{
                //     array_push($otherallowancesarray, $allowanceother);

                // }
       
        return $otherallowances;
    }

}
