<?php

namespace App\Http\Controllers\FinanceControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\FinanceModel;
use PDF;
use Dompdf\Dompdf;
use Session;
use Auth;
use Hash;

class ExpensesController extends Controller
{
    public function expenses()
    {
        return view('finance.expenses');
    }
    

    public function saveexpense(Request $request)
    {
        if($request->ajax())
        {
            $description = $request->get('description');
            $transdate = $request->get('transdate');
            $requestby = $request->get('requestby');
            $remarks = $request->get('remarks');
            $paidby = $request->get('paidby');
            $trans = $request->get('trans');
            $dataid = $request->get('dataid');


            if($trans == 1)
            {
                $delete = 0;
            }
            else
            {
                $delete = 1;
            }


            
            if($dataid == '')
            {
                $ins = db::table('expense')
                        ->insertGetId([
                            'description' => $description,
                            'requestedbyid' => $requestby,
                            'amount' => 0,
                            'transdate' => $transdate,
                            'paidby' => $paidby,
                            'status' => 'SUBMITTED',
                            'remarks' => $remarks,
                            'createdby' => auth()->user()->id,
                            'deleted' => $delete
                        ]);
                
                $refnum = 'EXP-' . sprintf('%06d', $ins);

                $saveRef = db::table('expense')
                        ->where('id', $ins)
                        ->update([
                            'description' => $description,
                            'requestedbyid' => $requestby,
                            'amount' => 0,
                            'transdate' => $transdate,
                            'paidby' => $paidby,
                            'status' => 'SUBMITTED',
                            'refnum' => $refnum,
                            'remarks' => $remarks,
                            'createdby' => auth()->user()->id,
                            'deleted' => $delete
                        ]);
            }
            else
            {


                $expense = db::table('expense')
                    ->where('id', $dataid)
                    ->first();

                if($expense->status == 'SUBMITTED')
                {
                    $gt = db::table('expensedetail')
                        ->where('headerid', $dataid)
                        ->where('deleted', 0)
                        ->sum('total');

                    // return $gt;


                    $ins = db::table('expense')
                        ->where('id', $dataid)
                        ->update([
                            'description' => $description,
                            'requestedbyid' => $requestby,
                            'amount' => $gt,
                            'transdate' => $transdate,
                            'paidby' => $paidby,
                            'status' => 'SUBMITTED',
                            // 'refnum' => $refnum . $ins,
                            'remarks' => $remarks,
                            'createdby' => auth()->user()->id,
                            'deleted' => $delete
                        ]);
                }

            }

            return $ins;

        }
    }

    public function searchexpenses(Request $request)
    {
        if($request->ajax())
        {
            $filter = $request->get('filter');
            $datefrom = $request->get('datefrom');
            $dateto = $request->get('dateto');
            $status = $request->get('status');

            $datefrom = date_create($datefrom);
            $datefrom = date_format($datefrom, 'Y-m-d 00:00');

            $dateto = date_create($dateto);
            $dateto = date_format($dateto, 'Y-m-d 23:59');

            // return $filter;

            $expenses = db::table('expense')
                    ->select('expense.id as expenseid', 'transdate', 'description', 'companyname as name', 'amount', 'status', 'refnum')
                    ->join('expense_company', 'expense.requestedbyid', '=', 'expense_company.id')
                    ->where(function($q) use($filter){
                        $q->where('description', 'like', '%'.$filter.'%')
                            ->orWhere('refnum', $filter);
                    })
                    ->where(function($q) use($status){
                        if($status != 'ALL')
                        {
                            $q->where('status', $status);
                        }
                    })
                    ->where('expense.deleted', 0)
                    ->whereBetween('transdate', [$datefrom, $dateto])
                    ->orderBy('status', 'DESC')
                    ->orderBy('transdate', 'DESC')
                    ->get();

            // echo ' ' . $datefrom . ' - ' . $dateto . ' ' . $filter;

            // return $expenses;
            $total = 0;
            if(count($expenses) > 0)
            {
                $list = '';
                foreach($expenses as $expense)
                {
                    $total += $expense->amount;
                    $tdate = date_create($expense->transdate);
                    $tdate = date_format($tdate, 'm-d-Y');
                    
                    if($expense->status == 'SUBMITTED')
                    {
                        $list .='
                            <tr class="expense-tr" data-id="'.$expense->expenseid.'">
                            <td>'.$expense->refnum.'</td>
                                <td>'.$expense->description.'</td>
                                <td>'.$tdate.'</td>
                                <td>'.$expense->name.'</td>
                                <td class="text-right">'.number_format($expense->amount, 2).'</td>
                                <td>'.$expense->status.'</td>
                            </tr>
                        ';
                    }
                    else if($expense->status == 'APPROVED')
                    {
                        $list .='
                            <tr class="expense-tr text-success" data-id="'.$expense->expenseid.'">
                            <td>'.$expense->refnum.'</td>
                                <td>'.$expense->description.'</td>
                                <td>'.$tdate.'</td>
                                <td>'.$expense->name.'</td>
                                <td class="text-right">'.number_format($expense->amount, 2).'</td>
                                <td>'.$expense->status.'</td>
                            </tr>
                        ';  
                    }
                    else if($expense->status == 'DISAPPROVED')
                    {
                        $list .='
                            <tr class="expense-tr text-danger" data-id="'.$expense->expenseid.'">
                            <td>'.$expense->refnum.'</td>
                                <td>'.$expense->description.'</td>
                                <td>'.$tdate.'</td>
                                <td>'.$expense->name.'</td>
                                <td class="text-right">'.number_format($expense->amount, 2).'</td>
                                <td>'.$expense->status.'</td>
                            </tr>
                        ';  
                    }
                }

                $data = array(
                    'list' => $list,
                    'gtotal' => '<td colspan="5" class="text-right text-bold">TOTAL: <span class="text-success">'.number_format($total, 2).'</span></td>'
                );

                echo json_encode($data);
            }
        }
    }

    public function saveexpensedetail(Request $request)
    {
        if($request->ajax())
        {
            $headerid = $request->get('headerid');
            $itemid = $request->get('itemid');
            $itemprice = str_replace(',', '', $request->get('itemprice'));
            $qty = $request->get('qty');
            $total = str_replace(',', '', $request->get('total'));
            $detailid = $request->get('detailid');


            if($detailid == 0)
            {
                $insitem = db::table('expensedetail')
                    ->insert([
                        'headerid' => $headerid,
                        'itemid' =>$itemid,
                        'itemprice' => $itemprice,
                        'qty' => $qty,
                        'total' => $total,
                        'deleted' => 0,
                        'createdby' => auth()->user()->id,
                        'createddatetime' => FinanceModel::getServerDateTime()
                    ]);
            }
            else
            {
                $upditem = db::table('expensedetail')
                    ->where('id', $detailid)
                    ->update([
                        'headerid' => $headerid,
                        'itemid' =>$itemid,
                        'itemprice' => $itemprice,
                        'qty' => $qty,
                        'total' => $total,
                        'deleted' => 0,
                        'updatedby' => auth()->user()->id,
                        'updateddatetime' => FinanceModel::getServerDateTime()
                    ]); 
            }
        }
    }

    public function loadexpensedetail(Request $request)
    {
        $headerid = $request->get('headerid');

        $details = db::table('expensedetail')
                ->select('expensedetail.id', 'items.description', 'itemprice', 'qty', 'total')
                ->leftjoin('items', 'expensedetail.itemid', '=', 'items.id')
                ->where('headerid', $headerid)
                ->where('expensedetail.deleted', 0)
                ->get();

        $list = '';

        $gtotal = 0;

        foreach($details as $detail)
        {
            $gtotal += $detail->total;
            $list .='
                <tr data-id="'.$detail->id.'">
                  <td>'.$detail->description.'</td>
                  <td class="text-right">'.number_format($detail->itemprice, 2).'</td>
                  <td class="text-center">'.$detail->qty.'</td>
                  <td class="text-right">'.number_format($detail->total, 2).'</td>
                </tr>
            ';
        }

        if($gtotal > 0)
        {
            $grandtotal = '
                <td colspan="4" class="text-right text-bold">TOTAL: <span id="gt" class="text-success text-lg">'.number_format($gtotal, 2).'</span></td>
            ';
        }
        else
        {
            $grandtotal = '';
        }
        $data = array(
            'list' => $list,
            'gtotal' => $grandtotal
        );

        echo json_encode($data);
    }

    public function loadexpense(Request $request)
    {
        if($request->ajax())
        {
            $headerid = $request->get('headerid');

            $expense = db::table('expense')
                    ->where('id', $headerid)
                    ->first();

            $transdate = date_create($expense->transdate);
            $transdate = date_format($transdate, 'Y-m-d');

            $data = array(
                'description' => $expense->description,
                'requestedbyid' => $expense->requestedbyid,
                'transdate' => $transdate,
                'paidby' => $expense->paidby,
                'status' => $expense->status,
                'refnum' => $expense->refnum,
                'remarks' => $expense->remarks
            );

            echo json_encode($data);

        }
    }

    public function loadexpenseitems(Request $request)
    {
        if($request->ajax())
        {
            $itemid = $request->get('itemid');
            $items = FinanceModel::expenseitems();

            $list = '';
            foreach($items as $item)
            {
                if($itemid == $item->id)
                {
                    $list .= '
                        <option value="'.$item->id.'" selected>'.$item->description.'</option>
                    ';
                }
                else
                {
                    $list .= '
                        <option value="'.$item->id.'">'.$item->description.'</option>
                    ';
                }
            }

            $data = array(
                'list' => $list
            );

            echo json_encode($data);
        }
    }

    public function approveexpense(Request $request)
    {
        if($request->ajax())
        {
            $dataid = $request->get('dataid');

            $chkstatus = db::table('expense')
                ->where('id', $dataid)
                ->first();



            if($chkstatus->status == 'SUBMITTED')
            {
                $chkelevate = db::table('chrngpermission')
                    ->where('userid', auth()->user()->id)
                    ->count();

                

                if($chkelevate > 0)
                {
                    $expense = db::table('expense')
                        ->where('id', $dataid)
                        ->update([
                            'status' => 'APPROVED',
                            'approveby' => auth()->user()->id,
                            'approveddatetime' => FinanceModel::getServerDateTime(),
                            'updatedby' => auth()->user()->id,
                            'updateddatetime' => FinanceModel::getServerDateTime()
                        ]); 

                    return 1;
                }
                else
                {
                    return 0;
                }
            }
            else
            {
                return 2;
            }
        }
    }

    public function disapproveexpense(Request $request)
    {
        if($request->ajax())
        {
            $dataid = $request->get('dataid');

            $chkstatus = db::table('expense')
                ->where('id', $dataid)
                ->first();



            if($chkstatus->status == 'SUBMITTED')
            {
                $chkelevate = db::table('chrngpermission')
                    ->where('userid', auth()->user()->id)
                    ->count();

                

                if($chkelevate > 0)
                {
                    $expense = db::table('expense')
                        ->where('id', $dataid)
                        ->update([
                            'status' => 'DISAPPROVED',
                            'approveby' => auth()->user()->id,
                            'approveddatetime' => FinanceModel::getServerDateTime(),
                            'updatedby' => auth()->user()->id,
                            'updateddatetime' => FinanceModel::getServerDateTime()
                        ]); 

                    return 1;
                }
                else
                {
                    return 0;
                }
            }
            else
            {
                return 2;
            }
        }
    }

    public function expenseItemInfo(Request $request)
    {
        if($request->ajax())
        {
            $detailid = $request->get('detailid');

            $detail = db::table('expensedetail')
                ->where('id', $detailid)
                ->first();

            $data = array(
                'itemid' => $detail->itemid,
                'itemprice' => $detail->itemprice,
                'qty' => $detail->qty,
                'total' => $detail->total,
            );

            echo json_encode($data);
        }
    }


    public function saveNewItem(Request $request)
    {
        if($request->ajax())
        {
            $itemcode = $request->get('itemcode');
            $itemdesc = $request->get('itemdesc');
            $classid = $request->get('classid');
            $amount = $request->get('amount');
            $isexpense = $request->get('isexpense');

            $itemid = db::table('items')
                    ->insertGetId([
                        'itemcode' => $itemcode,
                        'description' => $itemdesc,
                        'classid' => $classid,
                        'amount' => $amount,
                        'isdp' => 0,
                        'isreceivable' => 0,
                        'isexpense' => 1,
                        'deleted' => 0,
                        'createdby' => auth()->user()->id,
                        'createddatetime' => FinanceModel::getServerDateTime()
                    ]);

            return $itemid;

        }
    }

    public function company_create(Request $request)
    {
        $company = $request->get('company');
        $address = $request->get('address');
        $department = $request->get('department');

        $check = db::table('expense_company')
            ->where('companyname', $company)
            ->where('deleted', 0)
            ->count();

        if($check == 0)
        {
            db::table('expense_company')
                ->insert([
                    'companyname' => $company,
                    'address' => $address,
                    'department' => $department,
                    'createdby' => auth()->user()->id,
                    'createddatetime' => FinanceModel::getServerDateTime()
                ]);

            return 'done';
        }
        else
        {
            return 'exist';
        }

    }

    public function company_load(Request $request)
    {
        $list = '';
        $company = db::table('expense_company')
            ->where('deleted', 0)
            ->orderBy('companyname')
            ->get();

        foreach($company as $comp)
        {
            $list .= '
                <option value="'.$comp->id.'" comp-dept="'.$comp->department.'">
                    '.strtoUpper($comp->companyname).'
                </option>
            ';    
        }

        $data = array(
            'list' => $list
        );

        echo json_encode($data);
    }

    public function expese_deletedetail(Request $request)
    {
        $dataid = $request->get('dataid');

        db::table('expensedetail')
            ->where('id', $dataid)
            ->update([
                'deleted' => 1,
                'deletedby' => auth()->user()->id
            ]);
    }

}
