{{-- <div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                
            </div>
        </div>
    </div>
</div> --}}
@if(count($histories) == 0)
<div class="col-12">
    <div class="alert alert-danger" role="alert">
        No payroll history!
    </div>
</div>
@else
    <div class="col-md-12">
        <div class="form-group">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">Search employee</span>
                </div>
                <input type="text" class="form-control" placeholder="Employee">
            </div>        
         </div>
    </div>
    @foreach($histories as $history)
    <div class="col-md-12 d-flex align-items-stretch flex-column">
        <div class="card card-primary collapsed-card" style="border: 1px solid #ddd; box-shadow: 0 0 1px rgb(0 0 0 / 13%) !important;">
            <div class="card-header">
                <div class="row">
                    <div class="col-2">
                            @php
                            $number = rand(1,3);
                            if(strtoupper($history->gender) == 'FEMALE'){
                                $avatar = 'avatar/T(F) '.$number.'.png';
                            }
                            else{
                                $avatar = 'avatar/T(M) '.$number.'.png';
                            }
                        @endphp
                        <img src="{{ asset($history->picurl) }}" alt="" onerror="this.onerror = null, this.src='{{asset($avatar)}}'"  class="img-circle img-fluid" style="width: 100px;">
                    </div>
                    <div class="col-10">
                        <div class="row">
                            <div class="col-6">
                                <p class="text-bold text-muted m-0">{{$history->lastname}}, {{$history->firstname}}</p>
                            </div>
                            <div class="col-6 text-right">
                                <p class="text-muted m-0"><small>{{$history->utype}} / {{$history->tid}}</small></p>
                            </div>
                            <div class="col-4 text-right" style="font-size: 20px !important;">
                                <small class="text-muted">Total Earning: <span class="text-bold">{{number_format($history->totalearning,2,'.',',')}}</span></small>
                            </div>
                            <div class="col-4 text-right" style="font-size: 20px !important;">
                                <small class="text-muted">Total Deduction:  <span class="text-bold">{{number_format($history->totaldeduction,2,'.',',')}}</span></small>
                            </div>
                            <div class="col-4 text-right" style="font-size: 20px !important;">
                                <small class="text-muted">Net Pay:  <span class="text-bold">{{number_format($history->netsalary,2,'.',',')}}</span></small>
                            </div>
                            <div class="col-4 text-muted text-bold text-right"><small>Pay Released: </b>{{date('M d, Y', strtotime($history->releaseddatetime))}}</small></div>
                            <div class="col-8 text-right mt-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary btn-print-slip" data-id="{{$history->id}}"><i class="fas fa-print"></i> Export Payslip
                                </button>

                                <button type="button" class="btn btn-tool text-secondary btn-outline-warning btn-getdetails" data-id="{{$history->id}}"  data-card-widget="collapse"><i class="fas fa-plus"></i> View details
                                </button>
                            </div>
                        </div>
                    </div>
                        {{-- <div class="card-tools">
                            <button type="button" class="btn btn-tool text-secondary" data-card-widget="collapse"><i class="fas fa-plus"></i>
                            </button>
                        </div> --}}
                </div>            
            </div>
            
            <div class="card-body pt-0" style="font-size: 12.5px;" id="container-id-{{$history->id}}">
                
            </div>
            
        </div>
        
        
        {{-- <div class="card bg-light d-flex flex-fill" style="border: 1px solid #ddd; box-shadow: 0 0 1px rgb(0 0 0 / 13%) !important;">
            <div class="card-header text-muted border-bottom-0 p-2">
                {{$history->utype}}
            </div>
            <div class="card-body pt-0 pb-0 pr-2 pl-2">
                <div class="row">
                    <div class="col-12">
                        <h2 class="lead"><b>{{$history->lastname}}, {{$history->firstname}}</b></h2>
                        <p class="text-muted text-sm m-1" style="font-size: 12px !important;"><b>Pay Released: </b><br/>{{date('M d, Y h:i A', strtotime($history->releaseddatetime))}} </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <p class="text-muted text-sm m-1"><b>Total Earnings: </b> {{number_format($history->totalearning,2,'.',',')}} </p>
                        <p class="text-muted text-sm m-1"><b>Total Deductions: </b>  {{number_format($history->totaldeduction,2,'.',',')}} </p>
                        <p class="text-muted text-sm m-1"><b>Net Pay: </b>  {{number_format($history->netsalary,2,'.',',')}} </p>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="text-right">
                    <button type="button" class="btn btn-sm btn-primary">View Payroll Details</button>
                </div>
            </div>
        </div> --}}
    </div>
    @endforeach
@endif