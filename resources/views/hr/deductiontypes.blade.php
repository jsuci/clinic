

<!-- DataTables -->
<link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css')}}">
<link rel="stylesheet" href="{{asset('plugins/summernote/summernote-bs4.css')}}">
@extends('hr.layouts.app')
@section('content')
<style>
    .mobile{
        display: none;
    }
    @media only screen and (max-width: 600px) {
        .mobile {
            display: block;
        }
        .web {
            display: none;
        }
    }

</style>
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <!-- <h1>Standard Deductions Setup</h1> -->
        <h4 class="text-warning" style="text-shadow: 1px 1px 1px #000000">
        <!-- <i class="fa fa-chart-line nav-icon"></i>  -->
        MANDATORY DEDUCTION SETUP</h4>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="/home">Home</a></li>
            <li class="breadcrumb-item active">Deductions Setup</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>
  <section class="content">
    <div class="container-fluid">
        <!-- START ALERTS AND CALLOUTS -->
        <div class="row">
            <div class="col-md-4">
              <div class="card">
                  <div class="card-header">
                      <i class="fa fa-plus"></i> Particulars
                  </div>
                  <div class="card-footer">
                      <form action="/updatedeductions/{{Crypt::encrypt('adddeduction')}}" method="get">
                        <label>Title</label>
                        <input type="text" name="deductiontype" class="form-control form-control-sm" required/>
                        <br/>
                        {{-- <label>Bracket</label>
                        <select name="brackettype" class="form-control form-control-sm">
                            <option value="">None</option>
                            <option value="1">PAG-IBIG</option>
                            <option value="2">PHILHEALTH</option>
                            <option value="3">SSS</option>
                        </select>
                        <br/> --}}
                        <button type="submit" class="btn btn-sm btn-primary btn-block">
                            <i class="fa fa-edit"></i> Update
                        </button>
                      </form>
                  </div>
                </div>
              <!-- /.card -->
            </div>
          <div class="col-md-8">
            <div class="card h-100">
                <div class="card-body">
                    @foreach($deductiontypes as $deductiontype)
                        @if($deductiontype->constant == 0)
                            <form action="/updatedeductions/{{Crypt::encrypt('editdeduction')}}" method="get">
                                <a href="#" class="nav-link text-uppercase m-0 p-0">
                                    <input type="hidden" class="form-control form-control-sm col-md-9 col-12" name="deductiontype" value="{{$deductiontype->id}}" style="display: inline-block; position:relative" readonly required/>
                                    <input type="text" class="form-control form-control-sm col-md-9 col-12 text-uppercase" name="editeddeductiontype" value="{{$deductiontype->description}}" style="display: inline-block; position:relative" readonly required/>
                                    <span class=" web buttonscontainer pt-2 col-3">
                                        <span class="btn btn-warning btn-sm editdeductiontypebutton">Edit</span>&nbsp;<span class="btn btn-sm btn-danger deletedeductiontypebutton" >Delete</span>
                                    </span>
                                    <span class="mobile buttonscontainer pt-2">
                                        <div class="row mobilecontainer">
                                        <div class="col-6">
                                        <span class="btn btn-warning btn-sm btn-block editdeductiontypebutton">Edit</span>
                                        </div>
                                        <div class="col-6">
                                            <span class="btn btn-sm btn-block btn-danger deletedeductiontypebutton" >Delete</span>
                                        </div>
                                    </div>
                                    </span>
                                </a>
                            </form>
                        @else
                            <div class="row">
                                <div class="col-11">
                                    <input type="text" class="form-control form-control-sm" value="{{$deductiontype->description}}" disabled/>
                                </div>
                                <div class="col-1">
                                    <form action="/bracketing" method="get">
                                        <input type="hidden" class="form-control form-control-sm" name="type" value="{{$deductiontype->description}}"/>
                                        <input type="hidden" class="form-control form-control-sm" name="id" value="{{$deductiontype->id}}"/>
                                        <button type="submit" class="btn btn-default">
                                            <i class="fa fa-cogs text-muted"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                        <br>
                    @endforeach
                </div>
              </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
        <!-- END ALERTS AND CALLOUTS -->
        <!-- END CUSTOM TABS -->
        <!-- START PROGRESS BARS -->
        <!-- /.row -->
        <!-- END PROGRESS BARS -->

        <!-- START ACCORDION & CAROUSEL-->
        <!-- END ACCORDION & CAROUSEL-->

        <!-- START TYPOGRAPHY -->
        <!-- END TYPOGRAPHY -->
      </div>
  </section>
  <input type="hidden" name="deleteid" value="{{Crypt::encrypt('deletedeductiontype')}}"/>
  <input type="hidden" name="adddeductiondetail" value="{{Crypt::encrypt('adddeductiondetail')}}"/>
  <hr>
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <!-- <h1>Tardiness Deduction Setup</h1> -->
          <h4 class="text-warning" style="text-shadow: 1px 1px 1px #000000">
            <!-- <i class="fa fa-chart-line nav-icon"></i>  -->
            TARDINESS DEDUCTION SETUP</h4>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>
  <section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card p-2">
                    <div class="row p-2">
                        <div class="col-md-8">
                            {{-- {{$tardinesstype}} --}}
                            <form action="/tardinessdeduction/changecomputationtype" method="get" name="changecomputationtype">
                                <input type="hidden" name="computationtypeid" />
                            </form>
                            <div class="form-group clearfix">
                                @if($tardinesstype[0]->status == 0)
                                    <div class="icheck-primary d-inline">
                                        <input type="radio" id="radioPrimary1" name="tardinesssetuptype" value="{{$tardinesstype[0]->id}}">
                                        <label for="radioPrimary1">
                                            Standard Computation
                                        </label>
                                    </div>
                                @else
                                    <div class="icheck-primary d-inline">
                                        <input type="radio" id="radioPrimary1" name="tardinesssetuptype" value="{{$tardinesstype[0]->id}}" checked>
                                        <label for="radioPrimary1">
                                            Standard Computation
                                        </label>
                                    </div>
                                @endif
                                <br>
                                <br>
                                @if($tardinesstype[1]->status == 0)
                                    <div class="icheck-primary d-inline">
                                        <input type="radio" id="radioPrimary2" name="tardinesssetuptype" value="{{$tardinesstype[1]->id}}">
                                        <label for="radioPrimary2">
                                            Custom Computation
                                        </label>
                                    </div>
                                @else
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="icheck-primary d-inline">
                                            <input type="radio" id="radioPrimary2" name="tardinesssetuptype" value="{{$tardinesstype[1]->id}}" checked>
                                            <label for="radioPrimary2">
                                                Custom Computation
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 addcomputationbutton">
                                            @if($tardinesstype[1]->status == 1)

                                            <a data-toggle="modal" data-target="#addcomputation" class="float-left">
                                                <i class="fa fa-plus text-success"></i> Add new custom computation
                                            </a>
                                            <div id="addcomputation" class="modal custom-modal fade" role="dialog" style="display: none;" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                                <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title employeename">New Custom computation</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">×</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form action="/addtardinesscomputation" method="get">
                                                                <label>Late duration</label>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <input type="number" class="form-control form-control-sm" name="timeduration" placeholder="Time duration" required/>
                                                                        <br>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <div class="form-group clearfix">
                                                                            <div class="icheck-primary d-inline mr-3">
                                                                            <input type="radio" id="radioPrimary3" name="durationtype" value="minutes" checked>
                                                                            <label for="radioPrimary3">
                                                                                Minute/s
                                                                            </label>
                                                                            </div>
                                                                            <div class="icheck-primary d-inline">
                                                                            <input type="radio" id="radioPrimary4" name="durationtype" value="hours">
                                                                            <label for="radioPrimary4">
                                                                                    Hour/s
                                                                            </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <br>
                                                                <label>Deduction Basis</label>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="form-group clearfix">
                                                                            <div class="icheck-primary d-inline mr-3">
                                                                            <input type="radio" id="radioPrimary5" name="deductionbasis" value="fixedamount" checked>
                                                                            <label for="radioPrimary5">
                                                                                Fixed Amount
                                                                            </label>
                                                                            </div>
                                                                            <div class="icheck-primary d-inline">
                                                                            <input type="radio" id="radioPrimary6" name="deductionbasis" value="dailyratepercentage">
                                                                            <label for="radioPrimary6">
                                                                                    Daily rate percentage
                                                                            </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <br>
                                                                <div class="basiscontainer">
                                                                    <label>Amount</label>
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <input type="text" class="form-control form-control-sm groupOfTexbox" name="amountdeducted" placeholder="Amount" required/>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <br>
                                                                <div class="row">
                                                                    <div class="col-md-3">
                                                                        Applicable to:
                                                                    </div>
                                                                    <div class="col-md-9">
                                                                        <select class="form-control form-control-sm" name="applicationtype">
                                                                            <option value="all">All</option>
                                                                            <option value="specific">Specific Departments</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <br>
                                                                <div class="row">
                                                                    <div class="specificdepartmentscontainer"></div>
                                                                </div>
                                                                <div class="submit-section">
                                                                    <button type="submit" class="btn btn-success submit-btn float-right addnewcomputation">Add</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            
                            
                        </div>
                    </div>
                    {{-- {{$tardinesscomputations}} --}}
                    @if($tardinesstype[1]->status == 1)
                    @if(count($tardinesscomputations) == 0)
                    @else
                        <div class="row text-uppercase">
                            <div class="col-md-12">
                                <table class="table" style="table-layout: fixed;font-size:13px;">
                                    <thead class="text-center">
                                        <tr>
                                            <th style="width: 15%;">Late Duration</th>
                                            <th style="width: 15%;">Amount Deducted</th>
                                            <th style="width: 15%;">Daily rate deduction (%)</th>
                                            <th style="width: 35%;">Department/s</th>
                                            <th style="width: 10%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center">

                                        @foreach($tardinesscomputations as $tardinesscomputation)
                                            <tr>
                                                <td>
                                                    {{$tardinesscomputation->computationinfo->lateduration}}
                                                    @if($tardinesscomputation->computationinfo->minutes == '1')
                                                        Minute/s
                                                    @endif
                                                    @if($tardinesscomputation->computationinfo->hours == '1')
                                                        Hour/s
                                                    @endif
                                                </td>
                                                    {{-- <select class="form-control form-control-sm" name="durationtype">
                                                        <option value="minutes" {{$tardinesscomputation->minutes == '1' ? 'selected' : ''}}>Minute/s</option>
                                                        <option value="hours" {{$tardinesscomputation->hours == '1' ? 'selected' : ''}}>Hour/s</option>
                                                    </select> --}}
                                                <td>
                                                    @if($tardinesscomputation->computationinfo->basisfixedamount == '1')
                                                    &#8369; {{$tardinesscomputation->computationinfo->modifiedamount}}
                                                    @else
                                                    ------------
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($tardinesscomputation->computationinfo->basispercentage == '1')
                                                        {{$tardinesscomputation->computationinfo->modifiedpercentage}}
                                                    @else
                                                    ------------
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($tardinesscomputation->computationinfo->specific == '1')
                                                        @foreach($tardinesscomputation->computationdepartments as $computationdepartment)
                                                            {{$computationdepartment->department}}<br>
                                                        @endforeach
                                                    @else
                                                    All
                                                    @endif
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editcomputation{{$tardinesscomputation->computationinfo->id}}">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    <div id="editcomputation{{$tardinesscomputation->computationinfo->id}}" class="modal custom-modal fade" role="dialog" style="display: none;" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                                        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title employeename">Edit Custom computation</h4>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">×</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form action="/edittardinesscomputation" method="get" style="text-align: left !important;">
                                                                        <input type="hidden" class="form-control form-control-sm" name="computationid" value="{{$tardinesscomputation->computationinfo->id}}" placeholder="Time duration" required/>
                                                                        <label>Late duration</label>
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <input type="number" class="form-control form-control-sm" name="timeduration" value="{{$tardinesscomputation->computationinfo->lateduration}}" placeholder="Time duration" required/>
                                                                                <br>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                @if($tardinesscomputation->computationinfo->minutes == '1')
                                                                                    <div class="form-group clearfix">
                                                                                        <div class="icheck-primary d-inline mr-3">
                                                                                        <input type="radio" id="radioPrimary1{{$tardinesscomputation->computationinfo->lateduration}}" name="durationtype" value="minutes" checked>
                                                                                        <label for="radioPrimary1{{$tardinesscomputation->computationinfo->lateduration}}">
                                                                                            Minute/s
                                                                                        </label>
                                                                                        </div>
                                                                                        <div class="icheck-primary d-inline">
                                                                                        <input type="radio" id="radioPrimary2{{$tardinesscomputation->computationinfo->lateduration}}" name="durationtype" value="hours">
                                                                                        <label for="radioPrimary2{{$tardinesscomputation->computationinfo->lateduration}}">
                                                                                                Hour/s
                                                                                        </label>
                                                                                        </div>
                                                                                    </div>
                                                                                @endif
                                                                                @if($tardinesscomputation->computationinfo->hours == '1')
                                                                                    <div class="form-group clearfix">
                                                                                        <div class="icheck-primary d-inline mr-3">
                                                                                        <input type="radio" id="radioPrimary1{{$tardinesscomputation->computationinfo->lateduration}}" name="durationtype" value="minutes">
                                                                                        <label for="radioPrimary1{{$tardinesscomputation->computationinfo->lateduration}}">
                                                                                            Minute/s
                                                                                        </label>
                                                                                        </div>
                                                                                        <div class="icheck-primary d-inline">
                                                                                        <input type="radio" id="radioPrimary2{{$tardinesscomputation->computationinfo->lateduration}}" name="durationtype" value="hours" checked>
                                                                                        <label for="radioPrimary2{{$tardinesscomputation->computationinfo->lateduration}}">
                                                                                                Hour/s
                                                                                        </label>
                                                                                        </div>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <br>
                                                                        <label>Deduction Basis</label>
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                @if($tardinesscomputation->computationinfo->basisfixedamount == '1')
                                                                                    <div class="form-group clearfix">
                                                                                        <div class="icheck-primary d-inline mr-3">
                                                                                        <input type="radio" id="radioPrimary3{{$tardinesscomputation->computationinfo->lateduration}}" name="deductionbasis" value="fixedamount" checked>
                                                                                        <label for="radioPrimary3{{$tardinesscomputation->computationinfo->lateduration}}">
                                                                                            Fixed Amount
                                                                                        </label>
                                                                                        </div>
                                                                                        <div class="icheck-primary d-inline">
                                                                                        <input type="radio" id="radioPrimary4{{$tardinesscomputation->computationinfo->lateduration}}" name="deductionbasis" value="dailyratepercentage">
                                                                                        <label for="radioPrimary4{{$tardinesscomputation->computationinfo->lateduration}}">
                                                                                                Daily rate percentage
                                                                                        </label>
                                                                                        </div>
                                                                                    </div>
                                                                                @else
                                                                                    <div class="form-group clearfix">
                                                                                        <div class="icheck-primary d-inline mr-3">
                                                                                        <input type="radio" id="radioPrimary3{{$tardinesscomputation->computationinfo->lateduration}}" name="deductionbasis" value="fixedamount" >
                                                                                        <label for="radioPrimary3{{$tardinesscomputation->computationinfo->lateduration}}">
                                                                                            Fixed Amount
                                                                                        </label>
                                                                                        </div>
                                                                                        <div class="icheck-primary d-inline">
                                                                                        <input type="radio" id="radioPrimary4{{$tardinesscomputation->computationinfo->lateduration}}" name="deductionbasis" value="dailyratepercentage" checked>
                                                                                        <label for="radioPrimary4{{$tardinesscomputation->computationinfo->lateduration}}">
                                                                                                Daily rate percentage
                                                                                        </label>
                                                                                        </div>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <br>
                                                                        <div class="basiscontainer">
                                                                            @if($tardinesscomputation->computationinfo->basisfixedamount == '1')
                                                                                <label>Amount</label>
                                                                                <div class="row">
                                                                                    <div class="col-md-12">
                                                                                        <input type="text" class="form-control form-control-sm groupOfTexbox" name="amountdeducted" value="{{$tardinesscomputation->computationinfo->amount}}" placeholder="Amount" required/>
                                                                                    </div>
                                                                                </div>
                                                                            @else
                                                                                <label>Percentage</label>
                                                                                <div class="row">
                                                                                    <div class="col-md-12">
                                                                                        <input type="text" class="form-control form-control-sm groupOfTexbox" name="percentage" value="{{$tardinesscomputation->computationinfo->dailyratepercentage}}" placeholder="Amount" required/>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                        <br>
                                                                        <div class="row">
                                                                            <div class="col-md-3">
                                                                                Applicable to:
                                                                            </div>
                                                                            @if($tardinesscomputation->computationinfo->specific == '1')
                                                                                <div class="col-md-9">
                                                                                    <select class="form-control form-control-sm" name="editapplicationtype">
                                                                                        <option value="all">All</option>
                                                                                        <option value="specific" selected="selected">Specific Departments</option>
                                                                                    </select>
                                                                                </div>
                                                                            @else
                                                                                <div class="col-md-9">
                                                                                    <select class="form-control form-control-sm" name="editapplicationtype">
                                                                                        <option value="all" selected="selected">All</option>
                                                                                        <option value="specific">Specific Departments</option>
                                                                                    </select>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                        <br>
                                                                        <div class="row">
                                                                            <div class="editspecificdepartmentscontainer">
                                                                                @if($tardinesscomputation->computationinfo->specific == 1)
                                                                                @foreach($departments as $department)
                                                                                    @php
                                                                                        $match = 0;   
                                                                                    @endphp
                                                                                    @foreach($tardinesscomputation->computationdepartments as $computationdepartment)
                                                                                        @if($department->id == $computationdepartment->departmentid)
                                                                                            @php
                                                                                                $match = 1;   
                                                                                                $deptid = $computationdepartment->departmentid;   
                                                                                            @endphp
                                                                                        @endif
                                                                                        
                                                                                    @endforeach
                                                                                    @if($match == 1)
                                                                                        <div class="col-md-12 mb-3">
                                                                                            <div class="icheck-primary d-inline">
                                                                                                <input type="checkbox" id="{{$department->id}}" checked="" value="{{$department->id}}" name="departments[]">
                                                                                                <label for="{{$department->id}}">
                                                                                                    {{$department->department}}
                                                                                                </label>
                                                                                            </div>
                                                                                        </div>
                                                                                    @else
                                                                                        <div class="col-md-12 mb-3">
                                                                                            <div class="icheck-primary d-inline">
                                                                                                <input type="checkbox" id="{{$department->id}}" value="{{$department->id}}" name="departments[]">
                                                                                                <label for="{{$department->id}}">
                                                                                                    {{$department->department}}
                                                                                                </label>
                                                                                            </div>
                                                                                        </div>
                                                                                    @endif
                                                                                @endforeach
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <div class="submit-section">
                                                                            <button type="submit" class="btn btn-warning submit-btn float-right addnewcomputation">Update</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#addcomputation{{$tardinesscomputation->computationinfo->id}}">
                                                        <i class="fa fa-trash-alt"></i>
                                                    </button>
                                                    <div id="addcomputation{{$tardinesscomputation->computationinfo->id}}" class="modal custom-modal fade" role="dialog" style="display: none;" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                                        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title employeename">Delete selected computation</h4>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">×</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body" style="text-align: none !important">
                                                                    <form action="/deletetardinesscomputation" method="get">
                                                                        <input type="hidden" name="tardinesscomputationid" value="{{$tardinesscomputation->computationinfo->id}}"/>
                                                                        <label>Late duration</label>: <br>
                                                                        {{$tardinesscomputation->computationinfo->lateduration}}
                                                                        @if($tardinesscomputation->computationinfo->minutes == '1')
                                                                            Minute/s
                                                                        @endif
                                                                        @if($tardinesscomputation->computationinfo->hours == '1')
                                                                            Hour/s
                                                                        @endif
                                                                        <br>
                                                                        <br>
                                                                        @if($tardinesscomputation->computationinfo->basisfixedamount == '1')
                                                                        <label>Amount</label>:<br>
                                                                        &#8369; {{$tardinesscomputation->computationinfo->modifiedamount}}
                                                                        <br>
                                                                        <br>
                                                                        @else
                                                                        @endif
                                                                        @if($tardinesscomputation->computationinfo->basispercentage == '1')
                                                                        <label>Daily rate deduction </label>:<br>
                                                                        {{$tardinesscomputation->computationinfo->modifiedpercentage}}
                                                                        <br>
                                                                        <br>
                                                                        @else
                                                                        @endif
                                                                        <label>Department/s:</label><br>
                                                                        @if($tardinesscomputation->computationinfo->specific == '1')
                                                                            @foreach($tardinesscomputation->computationdepartments as $computationdepartment)
                                                                                {{$computationdepartment->department}}<br>
                                                                            @endforeach
                                                                        @else
                                                                        All
                                                                        @endif
                                                                        <div class="submit-section">
                                                                            <button type="submit" class="btn btn-danger submit-btn float-right addnewcomputation">Delete</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap 4 -->
<script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- ChartJS -->
<script src="{{asset('plugins/chart.js/Chart.min.js')}}"></script>
<!-- DataTables -->
<script src="{{asset('plugins/datatables/jquery.dataTables.js')}}"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}"></script>
<script src="{{asset('plugins/summernote/summernote-bs4.min.js')}}"></script>
<script>
    $(document).ready(function(){
        $(document).on('click','.editdeductiontypebutton', function(){
            console.log($(this).closest('.buttonscontainer'))
            $(this).closest('.nav-link')[0].children[1].removeAttribute('readonly')
            // console.log($(this).closest('.nav-link')[0].children[0])
            $(this).closest('.buttonscontainer').append(
                '<button type="submit" class="btn btn-sm btn-success savebutton">Update</button>'
            );
            $(this).closest('.mobilecontainer').remove();
            $(this).find('.deletedeductiontypebutton').remove();
            $(this).next('.deletedeductiontypebutton').remove();
            $(this).remove();

        })
        $('.deletedeductiontypebutton').click(function() {
            var deductiontypeid = $(this).closest('.nav-link')[0].children[0].value;
            var deductiontypedescription = $(this).closest('.nav-link')[0].children[1].value;
            var action = $('input[name=deleteid]').val();
            // console.log()
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: '/updatedeductions/'+action,
                        type:"GET",
                        dataType:"json",
                        data:{
                            deductiontypeid: deductiontypeid,
                            deductiontypedescription: deductiontypedescription
                        },
                        // headers: { 'X-CSRF-TOKEN': token },,
                        complete: function(){
                            Swal.fire({
                                title: 'Deleted!',
                                text: "Your file has been deleted.",
                                type: 'success',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK!'
                            }).then((confirm) => {
                                if (confirm.value) {
                                    window.location.reload();
                                }
                            })
                        }
                    })
                }
            })
        });
        // ===============================================================================================
        // ===============================================================================================
        // ===============================================================================================

        $('#adddeductiondetail').on('click', function(){
            var action = $('input[name=adddeductiondetail]').val();
           Swal.fire({
            title: 'Add new deduction detail',
            html:
                '<label style="text-align:none;">Select deduction type</label>' +
                '<select id="deductiontypeid" name="deductiontypeid" class="form-control form-control-sm">' +
                    @foreach($deductiontypes as $deductiontype)
                        '<option value="{{$deductiontype->id}}">{{$deductiontype->description}}</option>'+
                    @endforeach
                '</select>' +
                '<br>' +
                '<label style="text-align:none;">Deduction detail description</label>' +
                '<input id="deductiondetaildescription" name="deductiondetaildescription" type="text" class="form-control form-control-sm" required>'+
                '<br>' +
                '<div class="row">'+
                '<div class="col-md-6">'+
                '<label style="text-align:none;">Monthly amount</label>' +
                '<input id="deductiondetailmonthlyamount" name="deductiondetailmonthlyamount" type="number" class="form-control form-control-sm" required>'+
                '</div>'+
                '<div class="col-md-6">'+
                '<label style="text-align:none;">Percentage (%)</label>' +
                '<input id="deductiondetailpercentage" name="deductiondetailpercentage" type="number" class="form-control form-control-sm" required>'+
                '</div>'+
                '</div>',
            focusConfirm: false
            }).then((formValues) => {
                if (formValues.value) {
                    var deductiontypeid = $('#deductiontypeid').val();
                    var deductiondetaildescription = $('#deductiondetaildescription').val();
                    var deductiondetailmonthlyamount = $('#deductiondetailmonthlyamount').val();
                    var deductiondetailpercentage = $('#deductiondetailpercentage').val();
                    $.ajax({
                        url: '/deductiondetails/'+action,
                        type:"GET",
                        dataType:"json",
                        data:{
                            deductiontypeid: deductiontypeid,
                            deductiondetaildescription: deductiondetaildescription,
                            deductiondetailmonthlyamount: deductiondetailmonthlyamount,
                            deductiondetailpercentage: deductiondetailpercentage
                        },
                        // headers: { 'X-CSRF-TOKEN': token },,
                        complete: function(data){
                            Swal.fire({
                                title: 'Added Successfully!',
                                // text: "Your file has been deleted.",
                                type: 'success',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK!'
                            }).then((confirm) => {
                                if (confirm.value) {
                                    window.location.reload();
                                }
                            })
                        }
                    })
                }
            })
        });
        $(document).on('click','.editdeductiondetail', function(){
            console.log($(this).closest('tr'));
            $('select[name=deductiontypeid]').attr('disabled','disabled')
            $('input[name=deductiondetaildescription]').attr('readonly',true)
            $('input[name=monthlyamount]').attr('readonly',true)
            $('input[name=percentage]').attr('readonly',true)
            $('.editdeductiondetail').prop('type','button');
            $('.editdeductiondetail').removeClass('btn-success');
            $('.editdeductiondetail').addClass('btn-warning');
            $('.editdeductiondetail').text('Save');
            var detailid = $(this).attr('detailid');
            $('select#deductiontypeid'+detailid).removeAttr('disabled')
            $('input#deductiondetaildescription'+detailid).removeAttr('readonly')
            $('input#monthlyamount'+detailid).removeAttr('readonly')
            $('input#percentage'+detailid).removeAttr('readonly')
            $(this).prop('type','submit');
            $(this).removeClass('btn-warning');
            $(this).addClass('btn-success');
            $(this).text('Save');
            // var deductiontypeid = $(this).closest('tr')[0].cells[0].children[0].value;
            // var bracketname = $(this).closest('tr')[0].cells[1].children[0].value;
            // var monthlyamount = $(this).closest('tr')[0].cells[2].children[0].value;
            // var percentage = $(this).closest('tr')[0].cells[3].children[0].value;
        })
    })
    $(document).on('click','input[name=deductionbasis]', function(){
        $('.basiscontainer').empty();
        if($(this).val() == 'fixedamount'){
            $('.basiscontainer').append(
                '<label>Amount</label>'+
                '<div class="row">'+
                    '<div class="col-md-12">'+
                        '<input type="text" class="form-control form-control-sm groupOfTexbox" name="amountdeducted" placeholder="Amount" required/>'+
                    '</div>'+
                '</div>'
            )
        }else{
            $('.basiscontainer').append(
                '<label>Daily Rate Deduction (%)</label>'+
                '<div class="row">'+
                    '<div class="col-md-12">'+
                        '<input type="text" class="form-control form-control-sm groupOfTexbox" name="percentage" placeholder="Percentage " required/>'+
                    '</div>'+
                '</div>'
            )
        }
    });
        $(document).ready(function() {
        $('.groupOfTexbox').keypress(function (event) {
            return isNumber(event, this)
        });
    });
    // THE SCRIPT THAT CHECKS IF THE KEY PRESSED IS A NUMERIC OR DECIMAL VALUE.
    function isNumber(evt, element) {

        var charCode = (evt.which) ? evt.which : event.keyCode

        if (
            (charCode != 45 || $(element).val().indexOf('-') != -1) &&      // “-” CHECK MINUS, AND ONLY ONE.
            (charCode != 46 || $(element).val().indexOf('.') != -1) &&      // “.” CHECK DOT, AND ONLY ONE.
            (charCode < 48 || charCode > 57))
            return false;

        return true;
    }  
    $(document).on('change','select[name=applicationtype]', function(){
        if($(this).val() == 'specific'){

            @foreach($departments as $department)
                $('.specificdepartmentscontainer').append(
                    '<div class="col-md-12 mb-3">'+
                        '<div class="icheck-primary d-inline">'+
                            '<input type="checkbox" id="a{{$department->id}}" checked="" value="{{$department->id}}" name="departments[]">'+
                            '<label for="a{{$department->id}}">'+
                                '{{$department->department}}'+
                            '</label>'+
                        '</div>'+
                    '</div>'
                )
            @endforeach
        }else{
            $('.specificdepartmentscontainer').empty();
        }
    });
    $(document).on('change','select[name=editapplicationtype]', function(){
        if($(this).val() == 'specific'){

            @foreach($departments as $department)
                $('.editspecificdepartmentscontainer').append(
                    '<div class="col-md-12 mb-3">'+
                        '<div class="icheck-primary d-inline">'+
                            '<input type="checkbox" id="a{{$department->id}}" checked="" value="{{$department->id}}" name="departments[]">'+
                            '<label for="a{{$department->id}}">'+
                                '{{$department->department}}'+
                            '</label>'+
                        '</div>'+
                    '</div>'
                )
            @endforeach
        }else{
            $('.editspecificdepartmentscontainer').empty();
        }
    });
    $(document).on('click','input[name=tardinesssetuptype]', function(){
        $('input[name=computationtypeid]').val($(this).val());
        $('form[name=changecomputationtype]').submit();
    });
    
  </script>
@endsection

