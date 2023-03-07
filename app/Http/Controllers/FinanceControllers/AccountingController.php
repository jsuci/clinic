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

class AccountingController extends Controller
{
    Public function journalentries()
    {
    	return view('finance/accounting/journalentries');
    }

    public function jeloadcoa(Request $request)
    {
    	if($request->ajax())
    	{
    		$coalist = FinanceModel::loadCOA();

    		$list = '<option value="0">Select Account</option>';

    		foreach($coalist as $coa)
    		{
    			$list .='
                    <option value="'.$coa->id.'">' .$coa->code. ' - ' .$coa->account.'</option>
    			';
    		}

    		$data = array(
    			'coalist' => $list
    		);

    		echo json_encode($data);
    	}
    }

    public function saveje(Request $request)
    {
        if($request->ajax())
        {
            $accid = $request->get('accid');
            $debit = str_replace(',', '', $request->get('isdebit'));
            $credit = str_replace(',', '', $request->get('iscredit'));
            $datetrans = $request->get('transdate');
            $refid = $request->get('refid');
            $action = $request->get('action');
            $refnum = '';

            // echo $refid;
            
                if($action == 'create')
                {
                    if($refid == '')
                    {
                        $jeid = db::table('acc_je')
                            ->insertGetId([
                                'transdate' => $datetrans,
                                'jestatus' => 'Draft',
                                'createdby' => auth()->user()->id,
                                'createddatetime' => FinanceModel::getServerDateTime()
                            ]);

                        db::table('acc_jedetails')
                            ->insert([
                                'headerid' => $jeid,
                                'accid' => $accid,
                                'debit' => $debit,
                                'credit' => $credit,
                                'createdby' => auth()->user()->id,
                                'createddatetime' => FinanceModel::getServerDateTime()
                            ]);

                        $refnum = 'JE'. date('Y') . sprintf('%06d', $jeid);

                        db::table('acc_je')
                            ->where('id', $jeid)
                            ->update([
                                'refid' => $refnum
                            ]);

                        $data = array(
                            'refid' => $jeid,
                            'refnum' => $refnum
                        );

                        return $data;

                        // echo json_encode($data);
                    }
                    else
                    {
                        db::table('acc_jedetails')
                            ->insert([
                                'headerid' => $refid,
                                'accid' => $accid,
                                'debit' => $debit,
                                'credit' => $credit,
                                'createdby' => auth()->user()->id,
                                'createddatetime' => FinanceModel::getServerDateTime()
                            ]);                    
                    }
                }
                else
                {
                    $detailid = $request->get('detailid');
                    db::table('acc_je')
                        ->where('id', $refid)
                        ->update([
                            'transdate' => $datetrans,
                            'updatedby' => auth()->user()->id,
                            'updateddatetime' => FinanceModel::getServerDateTime()
                        ]);

                    if($detailid != '')
                    {
                        db::table('acc_jedetails')
                            ->where('id', $detailid)
                            ->update([
                                'accid' => $accid,
                                'debit' => $debit,
                                'credit' => $credit,
                                'updatedby' => auth()->user()->id,
                                'updateddatetime' => FinanceModel::getServerDateTime()
                            ]);
                    }
                    else
                    {
                        db::table('acc_jedetails')
                            ->insert([
                                'headerid' => $refid,
                                'accid' => $accid,
                                'debit' => $debit,
                                'credit' => $credit,
                                'createdby' => auth()->user()->id,
                                'createddatetime' => FinanceModel::getServerDateTime()
                            ]);
                    }

                }
            // return 0;
        }
    }

    public function loadje(Request $request)
    {
        if($request->ajax())
        {
            $daterange = $request->get('daterange');

            // return $daterange;

            $daterange = explode(" - ",$daterange);

            $dateArray = array();

            $totalamount = 0;

            // return $daterange[0];

            if($daterange[0] != '')
            {
                $d1 = date_create($daterange[0]);
                $d1 = date_format($d1, 'Y-m-d 00:00');
                array_push($dateArray, $d1);
            }
            else
            {
                $d1 = date_create($FinanceModel::getServerDateTime());
                $d1 = date_format($d1, 'Y-m-d 00:00');
                array_push($dateArray, $d1);
            }

            $d2 = date_create($daterange[1]);
            $d2 = date_format($d2, 'Y-m-d 23:59');
            array_push($dateArray, $d2);

            $entries = db::table('acc_je')
                ->select(DB::raw('acc_je.id, transdate, refid, SUM(debit) as damount, jestatus'))
                ->join('acc_jedetails', 'acc_je.id', '=', 'acc_jedetails.headerid')
                ->whereBetween('transdate', $dateArray)
                ->groupBy('headerid')
                ->orderBy('acc_je.id', 'DESC')
                ->get();

            $jelist = '';

            foreach($entries as $je)
            {
                $date = date_create($je->transdate);
                $date = date_format($date, 'm-d-Y');
                $jelist .='
                    <tr data-id="'.$je->id.'">
                        <td>'.$date.'</td>
                        <td>'.$je->refid.'</td>
                        <td class="text-right" style="width:180px">'.number_format($je->damount, 2).'</td>
                        <td>'.$je->jestatus.'</td>
                    </tr>
                ';

                $totalamount += $je->damount;
            }

            $data = array(
                'jelist' => $jelist,
                'totalamount' => number_format($totalamount, 2)
            );

            echo json_encode($data);

        }

    }

    public function editje(Request $request)
    {
        if($request->ajax())
        {
            $jeid = $request->get('jeid');

            $debit = 0;
            $credit = 0;

            $acc_array = array();

            $acc_je = db::table('acc_je')
                ->select('id', 'transdate', 'refid', 'jestatus')
                ->where('id', $jeid)
                ->first();

            $detail = db::table('acc_jedetails')
                ->select('acc_jedetails.*', 'account', 'acc_coa.id as accid')
                ->join('acc_coa', 'acc_jedetails.accid', '=', 'acc_coa.id')
                ->where('headerid', $jeid)
                ->where('acc_jedetails.deleted', 0)
                ->get();

            $jelist = '';
            $transdate = date_create($acc_je->transdate);
            $transdate = date_format($transdate, 'Y-m-d');

            $count = 0;
            foreach($detail as $je)
            {
                $count += 1;

                $debit += $je->debit;
                $credit += $je->credit;

                $coalist = FinanceModel::loadCOA();

                $chart = '<option value="0">Select Account</option>';

                foreach($coalist as $coa)
                {
                    if($coa->id == $je->accid)
                    {
                        $chart .='
                            <option value="'.$coa->id.'" selected>' .$coa->code. ' - ' .$coa->account.'</option>
                        ';
                    }
                    else
                    {
                        $chart .='
                            <option value="'.$coa->id.'">' .$coa->code. ' - ' .$coa->account.'</option>
                        ';
                    }
                }

                // return $chart;

                $jelist ='
                    <div class="row je-body p-1" data-line="'.$count.'" data-id="'.$je->id.'">

                        <div class="col-md-7">
                            <select id="je-coa" class="select2bs4 je-coa form-control">
                              '.$chart.'
                            </select>
                        </div>

                        <div class="col-md-2 text-right">
                            <input id="txtdebit" type="text" class="text-right isdebit form-control" pattern="^\\$\\d{1,3}(,\\d{3})*(\\.\\d+)?$" name="currency-field" data-type="currency" autocomplete="off" value="'.number_format($je->debit, 2).'">
                        </div>

                        <div class="col-md-2 text-right">
                            <input id="txtcredit" class="text-right iscredit form-control" pattern="^\\$\\d{1,3}(,\\d{3})*(\\.\\d+)?$" name="currency-field" data-type="currency" autocomplete="off" value="'.number_format($je->credit, 2).'">
                        </div>

                        <div class="col-md-1">
                            <button class="btn btn-danger btn-sm btn-linedel" data-id="'.$je->id.'">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>

                    </div>
                ';

                array_push($acc_array, $jelist);

            }

            // return $acc_array;

            $data = array(
                'refid' => $acc_je->refid,
                'transdate' => $transdate,
                'jeid' => $acc_je->id,
                'jestatus' => $acc_je->jestatus,
                'jearray' => $acc_array,

                'iscredit' => number_format($credit, 2),
                'isdebit' => number_format($debit, 2)
            );

            echo json_encode($data);
        }
    }

    public function deletejedetail(Request $request)
    {
        if($request->ajax())
        {
            $detailid = $request->get('detailid');

            db::table('acc_jedetails')
                ->where('id', $detailid)
                ->update([
                    'deleted' => 1,
                    'deletedby' => auth()->user()->id,
                    'deleteddatetime' => FinanceModel::getServerDateTime()
                ]);

            return 1;
        }
    }

    public function appendeditdetail(Request $request)
    {
        if($request->ajax())
        {
            $headerid = $request->get('headerid');

            $detailid = db::table('acc_jedetails')
                ->insertGetId([
                    'headerid' => $headerid,
                    'deleted' => 0,
                    'createdby' => auth()->user()->id,
                    'createddatetime' => FinanceModel::getServerDateTime()
                ]);


            $detail = db::table('acc_jedetails')
                ->where('id', $detailid)
                ->first();

            $data = array(
                'detailid' => $detailid
            );

            echo json_encode($data);
        }
    }

    public function postje(Request $request)
    {
        if($request->ajax())
        {
            $refid = $request->get('refid');

            db::table('acc_je')
                ->where('id', $refid)
                ->update([
                    'jestatus' => 'Posted',
                    'posteddatetime' => FinanceModel::getServerDateTime(),
                    'postedby' => auth()->user()->id,
                    'updatedby' => auth()->user()->id,
                    'updateddatetime' => FinanceModel::getServerDateTime()
                ]);

        }
    }
}
