<div class="row">
    <div class="col-md-6">
        <table class="table">
            <tr>
                <th colspan="3">Earnings</th>
            </tr>
            <tr>
                <td style="width: 5%;"></td>
                <td>BASIC PAY</td>                    
                <td style="width: 25%; text-align: right;">{{number_format($history->basicsalaryamount,2,'.',',')}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            </tr>
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
            <tr>
                <th colspan="2">Total Earning</th>
                <th style="text-align: right;">{{number_format($history->totalearning,2,'.',',')}}</th>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <table class="table">
            <tr>
                <th colspan="3">Deductions</th>
            </tr>
            @if($history->daysabsentamount > 0)
            <tr style=" text-transform: uppercase;">
                <td style="width: 5%;">&nbsp;</td>
                <td>Absent
                </td>
                <td style="width: 25%; text-align: right;">
                    {{number_format($history->daysabsentamount,2,'.',',')}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
            @endif
            @if($history->lateamount > 0)
            <tr style=" text-transform: uppercase;">
                <td style="width: 5%;">&nbsp;</td>
                <td>Late
                </td>
                <td style="width: 25%; text-align: right;">
                    {{number_format($history->lateamount,2,'.',',')}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
            @endif
            @foreach($particulars as $particular)
                @if($particular->particulartype == '1')
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
                @if($particular->particulartype == '2')
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
            <tr>
                <th colspan="2">Total Deduction</th>
                <th style="text-align: right;">{{number_format($history->totaldeduction,2,'.',',')}}</th>
            </tr>
        </table>
    </div>
    <div class="col-md-6">&nbsp;</div>
    <div class="col-md-3"><h5>Net Pay</h5></div>
    <div class="col-md-3 text-right"><h5>{{number_format($history->netsalary,2,'.',',')}}</h5></div>
</div>