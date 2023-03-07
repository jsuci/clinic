
<style>
    * {
        /* text-transform: uppercase; */
        font-family: Arial, Helvetica, sans-serif;
    }
    table {
        border-collapse: collapse;
    }
    @page{
        margin: 30px 50px;
    }
    h5{
        margin: 0px;
    }
</style>


<table style="width: 100%; table-layout: fixed;" border="1">
    <tr>
        <td colspan="2">
            <div style="font-size: 11px; ">Payroll Period : {{date('M d, Y', strtotime($payrollinfo->datefrom))}} - {{date('M d, Y', strtotime($payrollinfo->dateto))}}</div>
            <h5 style="text-transform: uppercase;">{{$header->title}}. {{$header->firstname}} {{$header->middlename}} {{$header->lastname}} {{$header->suffix}}</h5>
            <div style="font-size: 11px;">{{$header->utype}}</div>
            <div style="font-size: 13px; font-weight: bold;">{{$header->tid}}</div>
            <table style="width: 100%; font-size: 11px; ">
                <tr>
                    <td>Daily Rate: {{number_format($header->dailyrate,2,'.',',')}}</td>
                    <td>No. of Present Days: {{$header->presentdays}}</td>
                </tr>
            </table>
            {{-- <div style="font-size: 11px; width: 45%; float: left; padding-top: 15px;">Daily Rate: {{number_format($header->dailyrate,2,'.',',')}}</div>
            <div style="font-size: 11px; width: 45%; float: right; padding-top: 15px;">No. of Present Days: {{$header->presentdays}}</div> --}}
        </td>
        <td colspan="2" rowspan="2" style="vertical-align: top !important; border-bottom: none;">
            <h5 style="text-align: center;">DEDUCTIONS</h5>
            <table style="width: 100%; font-size: 11px; ">
                @if($payrolldetail->daysabsentamount > 0)
                <tr style=" text-transform: uppercase;">
                    <td style="width: 5%;">&nbsp;</td>
                    <td>Absent
                    </td>
                    <td style="width: 25%; text-align: right;">
                        {{number_format($payrolldetail->daysabsentamount,2,'.',',')}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </td>
                </tr>
                @endif
                @if($payrolldetail->lateamount > 0)
                <tr style=" text-transform: uppercase;">
                    <td style="width: 5%;">&nbsp;</td>
                    <td>Late
                    </td>
                    <td style="width: 25%; text-align: right;">
                        {{number_format($payrolldetail->lateamount,2,'.',',')}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </td>
                </tr>
                @endif
                @foreach($particulars as $particular)
                    @if($particular->particulartype == '1' && $particular->amountpaid > 0)
                        <tr style=" text-transform: uppercase;">
                            <td style="width: 5%;">&nbsp;</td>
                            <td>{{$particular->description}}
                            </td>
                            <td style="width: 25%; text-align: right;">
                                {{number_format($particular->amountpaid,2,'.',',')}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            </td>
                        </tr>
                    @endif
                @endforeach
                @foreach($particulars as $particular)
                    @if($particular->particulartype == '2' && $particular->amountpaid > 0)
                        <tr style=" text-transform: uppercase;">
                            <td style="width: 5%;">&nbsp;</td>
                            <td>{{$particular->description}}
                            </td>
                            <td style="width: 25%; text-align: right;">
                                {{number_format($particular->amountpaid,2,'.',',')}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            </td>
                        </tr>
                    @endif
                @endforeach
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="vertical-align: top; border-bottom: none;">
            <h5 style="text-align: center;">EARNINGS</h5>
            <table style="width: 100%; font-size: 11px; ">
                <tr>
                    <td style="width: 5%;"></td>
                    <td>BASIC PAY</td>
                    <td style="width: 25%; text-align: right;">{{number_format($header->basicsalaryamount,2,'.',',')}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                </tr>
                @if(count($leavedetails)>0)
                    @foreach($leavedetails as $leavedetail)
                        <tr>
                            <td style="width: 5%;"></td>
                            <td>{{$leavedetail->leave_type}} {{date('m/d/Y',strtotime($leavedetail->ldate))}}</td>
                            <td style="width: 25%; text-align: right;">{{number_format($leavedetail->amount,2,'.',',')}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        </tr>
                    @endforeach
                @endif
                @foreach($particulars as $particular)
                    @if($particular->particulartype == '3')
                        <tr style=" text-transform: uppercase;">
                            <td></td>
                            <td>{{$particular->description}}
                            </td>
                            <td style="text-align: right;">
                                {{number_format($particular->amountpaid,2,'.',',')}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            </td>
                        </tr>
                    @endif
                @endforeach
                @foreach($particulars as $particular)
                    @if($particular->particulartype == '4')
                        <tr style=" text-transform: uppercase;">
                            <td></td>
                            <td>{{$particular->description}}
                            </td>
                            <td style="text-align: right;">
                                {{number_format($particular->amountpaid,2,'.',',')}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            </td>
                        </tr>
                    @endif
                @endforeach
            </table>
        </td>
    </tr>
    <tr>
        <td style="border-right: none; border-top: none; border-bottom: none;">&nbsp;</td>
        <td style="border-left: none; border-top: none; border-bottom: none;">&nbsp;</td>
        <td style="border-right: none; border-top: none; border-bottom: none;">&nbsp;</td>
        <td style="border-left: none; border-top: none; border-bottom: none;">&nbsp;</td>
    </tr>
    <tr style="font-size: 11px;">
        <td style="border-right: none; border-top: none;"><label>&nbsp;&nbsp;&nbsp;&nbsp;TOTAL EARNINGS</label></td>
        <td style="border-left: none; text-align: right; font-weight: bold; border-top: none;">{{number_format($payrolldetail->totalearning,2,'.',',')}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td style="border-right: none; border-top: none;"><label>&nbsp;&nbsp;&nbsp;&nbsp;TOTAL DEDUCTIONS</label></td>
        <td style="border-left: none; text-align: right; font-weight: bold; border-top: none;">{{number_format($payrolldetail->totaldeduction,2,'.',',')}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2" style="font-size: 12px; text-align: center; border-bottom: none;"><h5>EMPLOYEES CONTRIBUTION</h5></td>
        <td rowspan="2" style="vertical-align: top; text-align: center; border-right: none; border-bottom: none;">NET PAY</td>
        <td rowspan="2" style="vertical-align: top; text-align: right; font-weight: bold; border-left: none; border-bottom: none;">{{number_format($payrolldetail->netsalary,2,'.',',')}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2" style="vertical-align: top; border-top: none;">
            @if(collect($particulars)->where('particulartype','1')->count()>0)
            <table style="width: 100%; font-size: 11px; ">
                <tr>
                    <th style=" text-align: left; padding-left: 15px;">Active</th>
                </tr>
                @foreach($particulars as $particular)
                    @if($particular->particulartype == '1')
                        <tr style=" text-transform: uppercase; text-align: left ;">
                            {{-- <td>&nbsp;</td> --}}
                            <td style=" padding-left: 15px;">{{$particular->description}}
                            </td>
                        </tr>
                    @endif
                @endforeach
            </table>
            @endif
        </td>
    </tr>
</table>