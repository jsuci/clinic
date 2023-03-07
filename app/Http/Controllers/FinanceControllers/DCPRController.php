<?php

namespace App\Http\Controllers\FinanceControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use TCPDF;
use App\FinanceModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class DCPRController extends Controller
{
    public function dcpr(Request $request)
    {
        return view('finance.reports.dcpr');
    }

    public function dcpr_generate(Request $request)
    {
        $date = date_format(date_create($request->get('date')), 'Y-m-d');
        $orfrom = $request->get('orfrom');
        $orto = $request->get('orto');
        $type = $request->get('type');

        $items = array();
        $headerlist = '<tr>';
        $bodylist = '';
        $arraygtotal = array();
        
        array_push($items, 'OR NUMBER');
        array_push($items, 'NAME');
        array_push($items, 'TOTAL AMOUNT');

        if($type == 'date')
        {
            $chrngtrans = db::table('chrngtrans')
                ->select('chrngcashtrans.particulars')
                ->join('chrngcashtrans', 'chrngtrans.transno', '=', 'chrngcashtrans.transno')
                ->whereBetween('transdate', [$date . ' 00:00', $date . ' 23:59'])
                ->where('cancelled', 0)
                ->where('deleted', 0)
                ->groupBy('particulars')
                ->orderBy('particulars')
                ->get();
        }
        else
        {
            $chrngtrans = db::table('chrngtrans')
                ->select('chrngcashtrans.particulars')
                ->join('chrngcashtrans', 'chrngtrans.transno', '=', 'chrngcashtrans.transno')
                ->whereBetween('ornum', [$orfrom, $orto])
                ->where('cancelled', 0)
                ->where('deleted', 0)
                ->groupBy('particulars')
                ->orderBy('particulars')
                ->get();   
        }

        $genitem = '';
        $itemcount = count($chrngtrans);

        foreach($chrngtrans as $trans)
        {
            $item = '';

            if(strpos($trans->particulars, 'TUITION') !== false)
            {
                $item = 'TUITION';
            }
            else
            {
                $item = $trans->particulars;
            }

            if(!in_array($item, $items))
            {
                array_push($items, $item);
            }            
        }

        array_push($items, 'TOTAL');

        // print_r($items);

        foreach($items as $_item)
        {
            // echo $_item . '<br>';
            if(strpos($_item, 'Balance forwarded from SY') !== false)
            {
                $_item = 'BALANCE FORWARDED';
            }

            if(strpos($_item, 'TUITION') !== false)
            {
                $_item = 'TUITION';
            }

            if($_item == 'NAME')
            {
                $headerlist .='
                        <th class="text-center bg-gray-dark" style="width:300px !important">'.$_item.'</th>
                ';
            }
            else
            {
                $headerlist .='
                        <th class="text-center bg-gray-dark" style="width:120px">'.$_item.'</th>
                ';
            }
        }

        $headerlist .='</tr>';
        $array_bodylist = array();
        $grandtotal = 0;

        
        if($type == 'date')
        {
            $chrngtransaction = db::table('chrngtrans')
                // ->select('ornum', 'studname', 'amountpaid as totalamount')
                ->select(db::raw('ornum, studname, sum(amountpaid) as totalamount'))
                ->whereBetween('transdate', [$date . ' 00:00', $date . ' 23:59'])
                ->where('cancelled', 0)
                ->groupBy('ornum')
                ->orderBy('ornum')
                ->get();
        }
        else
        {
            $chrngtransaction = db::table('chrngtrans')
                // ->select('ornum', 'studname', 'amountpaid as totalamount')
                ->select(db::raw('ornum, studname, sum(amountpaid) as totalamount'))
                ->whereBetween('ornum', [$orfrom, $orto])
                ->where('cancelled', 0)
                ->groupBy('ornum')
                ->orderBy('ornum')
                ->get();
        }

        $sumtotal = array();

        // return $items;

        foreach($chrngtransaction as $_trans)
        {
            $bodylist .='
                <tr>
                    <td style="width:60px">'.$_trans->ornum.'</td>
                    <td style="width:300">'.$_trans->studname.'</td>
                    <td style="width:70px" class="text-right">'.number_format($_trans->totalamount, 2).'</td>
            ';  

            $grandtotal += $_trans->totalamount;

            foreach($items as $_item)
            {
                if($_item != 'OR NUMBER' && $_item != 'NAME' && $_item != 'TOTAL AMOUNT' && $_item != 'TOTAL')
                {
                    // echo $_trans->ornum . ' - ' . $_item . '<br>';
                    $trx = db::table('chrngtrans')
                        ->select(db::raw('ornum, studname, sum(chrngtrans.amountpaid) as totalamount, chrngcashtrans.particulars, sum(chrngcashtrans.amount) as amount'))
                        ->join('chrngcashtrans', 'chrngtrans.transno', '=', 'chrngcashtrans.transno')
                        ->where('ornum', $_trans->ornum)
                        ->where(function($q) use($_item){
                            if(strpos($_item, 'TUITION') !== false)
                            {
                                $q->where('chrngcashtrans.particulars', 'like', '%TUITION%');
                            }
                            else
                            {
                                $q->where('chrngcashtrans.particulars', $_item);
                            }
                        })
                        // ->where('chrngcashtrans.particulars', 'like', '%'.$_item.'%')
                        // ->where(function($q) use($_item){
                        //     if($_item == 'PTA FEE')
                        //     {
                        //          $q->where('chrngcashtrans.particulars', 'like', '%PTA%');
                        //     }
                        //     else
                        //     {
                        //         $q->where('chrngcashtrans.particulars', 'like', '%'.$_item.'%');   
                        //     }


                        // })
                        ->where('chrngcashtrans.deleted', 0)
                        ->where('cancelled', 0)
                        ->first();

                    if($trx->ornum != null)
                    {   
                        // echo 'ornum: ' . $trx->ornum . ' particulars: ' . $trx->particulars . ' amount: ' . $trx->amount . '<br>';
                        $arrayamount = 0;

                        array_push($arraygtotal, (object)[
                            $_item => $trx->amount
                        ]);

                        $bodylist .='
                            <td style="width:70px" class="text-right">'.number_format($trx->amount, 2).'</td>
                        ';
                    }
                    else
                    {
                        $bodylist .='<td style="width:70px"></td>';
                    }
                }
            }

            $bodylist .='<td style="width:70ox" class="text-right">'.number_format($_trans->totalamount, 2).'</td>';
            $bodylist .='</tr>';
        }

        // return $arraygtotal;

        $bodylist .='
            <tr>
                <td colspan="2" class="text-right text-bold">TOTAL:</td>
                <td class="text-right text-bold">'.number_format($grandtotal, 2).'</td>
            
        ';

        $arraygtotal = collect($arraygtotal);

        

        foreach($items as $_item)
        {
            if($type == 'date')
            {
                $sum = db::table('chrngtrans')
                    ->select(db::raw('sum(chrngcashtrans.amount) as amount'))
                    ->join('chrngcashtrans', 'chrngtrans.transno', '=', 'chrngcashtrans.transno')
                    ->whereBetween('transdate', [$date . ' 00:00', $date . ' 23:59'])
                    ->where('cancelled', 0)
                    ->where('deleted', 0)
                    // ->where('chrngcashtrans.particulars', 'like', '%'.$_item.'%')
                    ->where(function($q) use($_item){
                        if(strpos($_item, 'TUITION') !== false)
                        {
                            $q->where('chrngcashtrans.particulars', 'like', '%TUITION%');
                        }
                        else
                        {
                            $q->where('chrngcashtrans.particulars', $_item);
                        }
                    })
                    ->first();
            }
            else
            {
                $sum = db::table('chrngtrans')
                    ->select(db::raw('sum(chrngcashtrans.amount) as amount'))
                    ->join('chrngcashtrans', 'chrngtrans.transno', '=', 'chrngcashtrans.transno')
                    ->whereBetween('ornum', [$orfrom, $orto])
                    ->where('cancelled', 0)
                    ->where('deleted', 0)
                    ->where(function($q) use($_item){
                        if($_item != 'OR')
                        {
                            $q->where('chrngcashtrans.particulars', 'like', '%'.$_item.'%');
                        }
                        else
                        {
                            $q->where('chrngcashtrans.particulars', 'like', '%aaaaaaaa%');
                        }
                    })
                    ->first();   
            }

            if($sum->amount > 0)
            {
                array_push($sumtotal, $sum->amount);
            }

        }

        // return $sumtotal;

        foreach($sumtotal as $_sum)
        {
            $bodylist .='
                <td class="text-right text-bold">'.number_format($_sum, 2).'</td>
            ';
        }

        $bodylist .='
            <td class="text-right text-bold">'.number_format($grandtotal, 2).'</td>
        ';

        // return $sumtotal;

        $data = array(
            'headerlist' => $headerlist,
            'bodylist' => $bodylist,
            'itemcount' => $itemcount
        );

        echo json_encode($data);

    }
	
    public function dcpr_export(Request $request)
    {
        $date = $request->get('dcpr_date');
        $orfrom = $request->get('orfrom');
        $orto = $request->get('orto');
        $type = $request->get('type');

        $returndate = date_format(date_create($date), 'mdY');
        $displaydate = date_format(date_create($date), 'M d, Y');

        // return $displaydate;

        if($type == 'date')
        {
            $orlist = db::table('chrngtrans')
                ->select('ornum')
                ->join('chrngcashtrans', 'chrngtrans.transno', '=', 'chrngcashtrans.transno')
                ->whereBetween('transdate', [$date . ' 00:00', $date . ' 23:59'])
                ->where('cancelled', 0)
                // ->where('chrngcashtrans.particulars', '!=', 'PTA')
                // ->where('chrngcashtrans.particulars', '!=', 'BACK ACCOUNTS')
                ->where('deleted', 0)
                ->groupBy('ornum')
                ->orderBy('ornum')
                ->first();
        }
        else
        {
            $orlist = db::table('chrngtrans')
                ->select('ornum')
                ->join('chrngcashtrans', 'chrngtrans.transno', '=', 'chrngcashtrans.transno')
                ->whereBetween('ornum', [$orfrom, $orto])
                ->where('cancelled', 0)
                // ->where('chrngcashtrans.particulars', '!=', 'PTA')
                // ->where('chrngcashtrans.particulars', '!=', 'BACK ACCOUNTS')
                ->where('deleted', 0)
                ->groupBy('ornum')
                ->orderBy('ornum')
                ->first();   
        }

        $orfirst = $orlist->ornum;

        if($type == 'date')
        {
            $orlist = db::table('chrngtrans')
                ->select('ornum')
                ->join('chrngcashtrans', 'chrngtrans.transno', '=', 'chrngcashtrans.transno')
                ->whereBetween('transdate', [$date . ' 00:00', $date . ' 23:59'])
                ->where('cancelled', 0)
                // ->where('chrngcashtrans.particulars', '!=', 'PTA')
                // ->where('chrngcashtrans.particulars', '!=', 'BACK ACCOUNTS')
                ->where('deleted', 0)
                ->groupBy('ornum')
                ->orderBy('ornum', 'DESC')
                ->first();
        }
        else
        {
            $orlist = db::table('chrngtrans')
                ->select('ornum')
                ->join('chrngcashtrans', 'chrngtrans.transno', '=', 'chrngcashtrans.transno')
                ->whereBetween('ornum', [$orfrom, $orto])
                ->where('cancelled', 0)
                // ->where('chrngcashtrans.particulars', '!=', 'PTA')
                // ->where('chrngcashtrans.particulars', '!=', 'BACK ACCOUNTS')
                ->where('deleted', 0)
                ->groupBy('ornum')
                ->orderBy('ornum', 'DESC')
                ->first();
        }

        $orlast = $orlist->ornum;

        $items = array();
        $headerlist = '<tr>';
        $bodylist = '';
        $arraygtotal = array();

        array_push($items, 'OR Number');
        array_push($items, 'NAME');
        array_push($items, 'TOTAL AMOUNT');

        if($type == 'date')
        {
            $chrngtrans = db::table('chrngtrans')
                ->select('ornum', 'studname', 'chrngcashtrans.particulars')
                ->join('chrngcashtrans', 'chrngtrans.transno', '=', 'chrngcashtrans.transno')
                ->whereBetween('transdate', [$date . ' 00:00', $date . ' 23:59'])
                ->where('cancelled', 0)
                // ->where('chrngcashtrans.particulars', '!=', 'PTA')
                // ->where('chrngcashtrans.particulars', '!=', 'BACK ACCOUNTS')
                ->where('deleted', 0)
                ->groupBy('particulars')
                ->orderBy('particulars')
                ->get();
        }
        else
        {
            $chrngtrans = db::table('chrngtrans')
                ->select('ornum', 'studname', 'chrngcashtrans.particulars')
                ->join('chrngcashtrans', 'chrngtrans.transno', '=', 'chrngcashtrans.transno')
                ->whereBetween('ornum', [$orfrom, $orto])
                ->where('cancelled', 0)
                // ->where('chrngcashtrans.particulars', '!=', 'PTA')
                // ->where('chrngcashtrans.particulars', '!=', 'BACK ACCOUNTS')
                ->where('deleted', 0)
                ->groupBy('particulars')
                ->orderBy('particulars')
                ->get();
        }

        $genitem = '';

        $itemcount = count($chrngtrans);

        foreach($chrngtrans as $trans)
        {
            // $item = explode(' GRADE ', $trans->particulars);
            // if(!in_array($item[0], $items))
            // {
            //     // $genitem = expload(' ', $item[0]);
            //     // if(strpos($genitem[0], $))
            //         array_push($items, $item[0]);
            // }

            // echo $item[0] . '<br>';

            $item = '';

            if(strpos($trans->particulars, 'TUITION') !== false)
            {
                $item = 'TUITION';
            }
            else
            {
                $item = $trans->particulars;
            }

            if(!in_array($item, $items))
            {
                array_push($items, $item);
            }     
        }

        array_push($items, 'TOTAL');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getStyle('B1')->getFont()->setSize(14);
        $sheet->getStyle('B1')->getFont()->setName('Arial');
        $sheet->getStyle('B1')->getFont()->setBold(true);

        $sheet->getStyle('B2')->getFont()->setSize(12);
        $sheet->getStyle('B2')->getFont()->setName('Arial');
        $sheet->getStyle('B2')->getFont()->setBold(false);

        $sheet->getStyle('B4')->getFont()->setSize(12);
        $sheet->getStyle('B4')->getFont()->setName('Arial');
        $sheet->getStyle('B4')->getFont()->setBold(true);

        $sheet->getStyle('B5')->getFont()->setSize(12);
        $sheet->getStyle('B5')->getFont()->setName('Arial');
        $sheet->getStyle('B5')->getFont()->setBold(true);

        $sheet->getStyle('B6')->getFont()->setSize(12);
        $sheet->getStyle('B6')->getFont()->setName('Arial');
        $sheet->getStyle('B6')->getFont()->setBold(true);

        $sheet->setCellValue('B1', db::table('schoolinfo')->first()->schoolname);
        $sheet->setCellValue('B2', db::table('schoolinfo')->first()->address);
        $sheet->setCellValue('B4', 'DAILY CASH SCHEDULE');
        $sheet->setCellValue('B5', $displaydate);
        $sheet->setCellValue('B6', 'OR # ' . $orfirst . ' - ' . $orlast);

        $sheet->getStyle('B9')->getAlignment()->setTextRotation(60);
        $sheet->getStyle('C9')->getAlignment()->setTextRotation(60);
        $sheet->getStyle('D9')->getAlignment()->setTextRotation(60);

        $sheet->getColumnDimension('A')->setWidth('3', 'pt');
        $sheet->getColumnDimension('B')->setWidth('8', 'pt');
        $sheet->getColumnDimension('C')->setWidth('40', 'pt');
        $sheet->getColumnDimension('D')->setWidth('12', 'pt');

        $hcol = 'B';
        $sheet->getStyle('A9')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        foreach($items as $_item)
        {
            // echo $_item . '<br>';
            if(strpos($_item, 'Balance forwarded from SY') !== false)
            {
                $_item = 'BALANCE FORWARDED';
            }

            $sheet->getStyle($hcol . '9')->getAlignment()->setTextRotation(60);
            $sheet->setCellValue($hcol . '9', $_item);
            $sheet->getStyle($hcol . '9')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            
            $hcol ++;

            // if($_item == 'NAME')
            // {



            //     $headerlist .='
            //             <th class="text-center bg-gray-dark" style="width:300px !important">'.$_item.'</th>
            //     ';
            // }
            // else
            // {
            //     $headerlist .='
            //             <th class="text-center bg-gray-dark" style="width:120px">'.$_item.'</th>
            //     ';
            // }
        }

        $array_bodylist = array();
        $grandtotal = 0;
        $row = 10;

        if($type == 'date')
        {
            $chrngtransaction = db::table('chrngtrans')
                ->select(db::raw('ornum, studname, sum(amountpaid) as totalamount'))
                ->whereBetween('transdate', [$date . ' 00:00', $date . ' 23:59'])
                ->where('cancelled', 0)
                ->groupBy('ornum')
                ->orderBy('ornum')
                ->get();
        }
        else
        {
            $chrngtransaction = db::table('chrngtrans')
                ->select(db::raw('ornum, studname, sum(amountpaid) as totalamount'))
                ->whereBetween('ornum', [$orfrom, $orto])
                ->where('cancelled', 0)
                ->groupBy('ornum')
                ->orderBy('ornum')
                ->get();   
        }

        $rcount = 1;

        foreach($chrngtransaction as $_trans)
        {
            
            $sheet->setCellValue('A' .$row, $rcount);
            $sheet->setCellValue('B' .$row, $_trans->ornum);
            $sheet->setCellValue('C' .$row, $_trans->studname);
            $sheet->setCellValue('D' .$row, $_trans->totalamount);
            $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

            $sheet->getStyle('A' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->getStyle('B' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->getStyle('C' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->getStyle('D' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            $grandtotal += $_trans->totalamount;

            $hcol = 'E';


            foreach($items as $_item)
            {
                if($_item != 'OR Number' && $_item != 'NAME' && $_item != 'TOTAL AMOUNT' && $_item != 'TOTAL')
                {
                    // echo $_trans->ornum . ' - ' . $_item .  '<br>';
                    $trx = db::table('chrngtrans')
                        ->select(db::raw('ornum, studname, sum(chrngtrans.amountpaid) as totalamount, chrngcashtrans.particulars, sum(chrngcashtrans.amount) as amount'))
                        ->join('chrngcashtrans', 'chrngtrans.transno', '=', 'chrngcashtrans.transno')
                        ->where('ornum', $_trans->ornum)
                        ->where(function($q) use($_item){
                            if(strpos($_item, 'TUITION') !== false)
                            {
                                $q->where('chrngcashtrans.particulars', 'like', '%TUITION%');
                            }
                            else
                            {
                                $q->where('chrngcashtrans.particulars', $_item);
                            }
                        })
                        // ->where('chrngcashtrans.particulars', $_item)
                        // ->where('chrngcashtrans.particulars', 'like', '%'.$_item.'%')
                        // ->where(function($q) use($_item){
                        //     if($_item == 'PTA FEE')
                        //     {
                        //          $q->where('chrngcashtrans.particulars', 'like', '%PTA%');
                        //     }
                        //     else
                        //     {
                        //         $q->where('chrngcashtrans.particulars', 'like', '%'.$_item.'%');   
                        //     }


                        // })
                        ->where('chrngcashtrans.deleted', 0)
                        ->where('cancelled', 0)
                        ->first();
                    
                    $sheet->getColumnDimension($hcol)->setWidth('10', 'pt');

                    if($trx->ornum != null)
                    {   
                        // echo 'ornum: ' . $trx->ornum . ' particulars: ' . $trx->particulars . ' amount: ' . $trx->amount . '<br>';
                        $arrayamount = 0;

                        array_push($arraygtotal, (object)[
                            $_item => $trx->amount
                        ]);

                        // $bodylist .='
                        //     <td style="width:70px" class="text-right">'.number_format($trx->amount, 2).'</td>
                        // ';

                        $sheet->setCellValue($hcol .$row, $trx->amount);
                        
                        $sheet->getStyle($hcol . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                        // $sheet->getColumnDimension($hcol)->setWidth('15', 'pt');
                        $sheet->getStyle($hcol . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    }
                    else
                    {
                        $sheet->setCellValue($hcol .$row, '');
                        $sheet->getStyle($hcol . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                        // $sheet->getColumnDimension($hcol)->setWidth('15', 'pt');
                        $sheet->getStyle($hcol . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    } 
                    $hcol++;
                }
            }

            $sheet->setCellValue($hcol .$row, $_trans->totalamount);
            $sheet->getStyle($hcol . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getColumnDimension($hcol)->setWidth('12', 'pt');
            $sheet->getStyle($hcol . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            $row++;
            $rcount++;
        }

        $arraygtotal = collect($arraygtotal);

        $sumtotal = array();

        foreach($items as $_item)
        {
            // if($arraygtotal->sum($_item) > 0)
            // {
            //     array_push($sumtotal, $arraygtotal->sum($_item));    
            // }

            if($type == 'date')
            {
                $sum = db::table('chrngtrans')
                    ->select(db::raw('sum(chrngcashtrans.amount) as amount'))
                    ->join('chrngcashtrans', 'chrngtrans.transno', '=', 'chrngcashtrans.transno')
                    ->whereBetween('transdate', [$date . ' 00:00', $date . ' 23:59'])
                    ->where('cancelled', 0)
                    ->where('deleted', 0)
                    // ->where('chrngcashtrans.particulars', $_item)
                    ->where(function($q) use($_item){
                        if(strpos($_item, 'TUITION') !== false)
                        {
                            $q->where('chrngcashtrans.particulars', 'like', '%TUITION%');
                        }
                        else
                        {
                            $q->where('chrngcashtrans.particulars', $_item);
                        }
                    })
                    ->first();
            }
            else
            {
                $sum = db::table('chrngtrans')
                    ->select(db::raw('sum(chrngcashtrans.amount) as amount'))
                    ->join('chrngcashtrans', 'chrngtrans.transno', '=', 'chrngcashtrans.transno')
                    ->whereBetween('ornum', [$orfrom, $orto])
                    ->where('cancelled', 0)
                    ->where('deleted', 0)
                    ->where('chrngcashtrans.particulars', $_item)
                    ->first();
            }

            if($sum->amount > 0)
            {
                array_push($sumtotal, $sum->amount);
            }
        }

        $sheet->getStyle($hcol . $row)->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $hcol = 'D';
        $sheet->setCellValue('B' .$row, 'TOTAL');
        $sheet->getStyle('B' .$row)->getFont()->setBold(true);
        $sheet->getStyle('B' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->getStyle('B' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->getStyle('C' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $sheet->setCellValue($hcol .$row, $grandtotal);
        $sheet->getStyle($hcol . $row)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle($hcol . $row)->getFont()->setBold(true);
        $sheet->getStyle($hcol . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $hcol ++;

        foreach($sumtotal as $_sum)
        {
            $sheet->setCellValue($hcol .$row, $_sum);
            $sheet->getStyle($hcol . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle($hcol . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->getStyle($hcol . $row)->getFont()->setBold(true);
            $hcol ++;
        }

        $sheet->setCellValue($hcol .$row, $grandtotal);
        $sheet->getStyle($hcol . $row)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle($hcol . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->getStyle($hcol . $row)->getFont()->setBold(true);

        $sig = db::table('finance_sigs')
            ->where('id', 1)
            ->first();

        $row ++;
        $row ++;
        $row ++;
        $row ++;
        $sheet->setCellValue('B' .$row, $sig->title_1 . ':');
        $sheet->setCellValue('D' .$row, $sig->title_2 . ':');

        $row ++;

        $sheet->setCellValue('B' .$row, $sig->sig_1);
        $sheet->setCellValue('D' .$row, $sig->sig_2);

        $sheet->getStyle('B' . $row)->getFont()->setBold(true);
        $sheet->getStyle('D' . $row)->getFont()->setBold(true);

        $row ++;
        $sheet->setCellValue('B' .$row, $sig->designation_1);
        $sheet->setCellValue('D' .$row, $sig->designation_2);

        $row++;
        $row++;
        $row++;
        $row++;

        $sheet->setCellValue('C' .$row, $sig->title_3 .':');
        $row++;
        $sheet->setCellValue('C' .$row, $sig->sig_3);
        $sheet->getStyle('C' . $row)->getFont()->setBold(true);
        $row++;
        $sheet->setCellValue('C' .$row, $sig->designation_3);


        $datenow = date_format(date_create(FinanceModel::getServerDateTime()), 'mdyhis');

        $dcprloc = 'dcpr/DCPR' . $orfirst . '_' . $orlast . '_' . $datenow.'.xlsx';

        // $writer = new Xlsx($spreadsheet);
        // $writer->save($dcprloc);

        // return $dcprloc;

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="'.$dcprloc.'"');
        $writer->save("php://output");
        exit();
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
