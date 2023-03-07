

@extends('hr.layouts.app')
@section('content')
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <!-- <h1>Employees</h1> -->
          <h4 class="text-warning" style="text-shadow: 1px 1px 1px #000000">
          <!-- <i class="fa fa-chart-line nav-icon"></i>  -->
          EMPLOYEEES </h4>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="/home">Home</a></li>
            <li class="breadcrumb-item active">Employees</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>
  @php
  $refid = DB::table('usertype')
      ->where('id', Session::get('currentPortal'))
      ->first()->refid;
  @endphp
  <div class="card shadow" style="border: none;">
      <div class="card-header">
          <div class="row">
            <div class="col-md-6">
              <input class="filter form-control" placeholder="Search employee" />
            </div>
            <div class="col-md-6 text-right">
              @if($refid != 26)
              <a href="/hr/employees/addnewemployee/index" class="btn btn-primary" style="color: white;"><i class="fa fa-plus"></i> Add New Employee</a>
              @endif
        
            </div>
          </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-5 text-left">
            <a href="/hr/employees/index?action=export&exporttype=pdf" class="btn btn-primary btn-sm" style="color: white;" target="_blank"><i class="fa fa-file-pdf"></i> Export to PDF</a>
            <a href="/hr/employees/index?action=export&exporttype=excel" class="btn btn-primary btn-sm" style="color: white;" target="_blank"><i class="fa fa-file-excel"></i> Export to Excel</a>
          </div>
            <div class="col-md-7 text-right">
                <button type="button" class="btn btn-sm btn-default">Total No. of Employees <span class="right badge badge-warning">{{count($employees)}}</span></button>
                <button type="button" class="btn btn-sm btn-default">Active <span class="right badge badge-warning">{{collect($employees)->where('isactive','1')->count()}}</span></button>
                <button type="button" class="btn btn-sm btn-default" @if(collect($employees)->where('isactive','0')->count()>0) data-toggle="modal" data-target="#modal-inactive" @endif>Inactive <span class="right badge badge-warning">{{collect($employees)->where('isactive','0')->count()}}</span></button>
            </div> 
        </div>
      </div>
  </div>
    <div class="row d-flex align-items-stretch">
        @foreach(collect($employees)->where('isactive','1')->values() as $employee)
            <div class="card col-md-4 col-6 text-center eachemployee" style="border: none !important;box-shadow: none !important;" data-string="{{$employee->lastname}}, {{$employee->firstname}} {{$employee->suffix}} {{$employee->utype}}<">
                <div class="card-body p-0" style="border: 1px solid#ddd;background: #e8e8e8;">
                    <small>{{$employee->utype}}</small>
                    <div class="card-text text-center m-0">
                        <div class="widget-user-image">
                
                              @php
                                $number = rand(1,3);
                                if(strtoupper($employee->gender) == 'FEMALE'){
                                    $avatar = 'avatar/T(F) '.$number.'.png';
                                }
                                else{
                                    $avatar = 'avatar/T(M) '.$number.'.png';
                                }
                              @endphp
                
                            <img class="img-circle elevation-2" src="{{asset($employee->picurl.'?random="'.\Carbon\Carbon::now('Asia/Manila')->isoFormat('MMDDYYHHmmss'))}}" 
                                onerror="this.onerror = null, this.src='{{asset($avatar)}}'"
                                alt="User Avatar" style="width: 30% !important"
                                >
                    
                                {{-- <a href="/hr/employees/profile/index?employeeid={{$employee->id}}"> --}}
                                    <h6>{{ucwords(strtolower($employee->lastname))}}, {{ucwords(strtolower($employee->firstname))}} {{ucwords(strtolower($employee->suffix))}}</h6>
                                {{-- </a> --}}
                        </div>
                        <div class="row p-2">
                          @if($refid == 26)
                            <div class="col-md-12 p-1">
                                <a type="button" href="/hr/employees/profile/index?employeeid={{$employee->id}}" class="btn btn-block btn-sm text-center btn-default">View Profile</a>
                            </div>
                            @else
                            <div class="col-md-3 p-1">
                                <button type="button" class="btn btn-block btn-sm text-center btn-default text-success p-1" data-toggle="modal" data-target="#modal-status-{{$employee->id}}" ><i class="fa fa-check-circle m-0"></i></button>
                            </div>
                            <div class="col-md-9 p-1">
                                <a type="button" href="/hr/employees/profile/index?employeeid={{$employee->id}}" class="btn btn-block btn-sm text-center btn-default">View Profile</a>
                            </div>
                            @endif

                        </div>
                        {{-- <div class="row p-2">
                            <div class="col-md-2 p-1">
                                <button type="button" class="btn btn-block btn-sm text-center btn-default text-success p-1" data-toggle="modal" data-target="#modal-status-{{$employee->id}}" ><i class="fa fa-check-circle m-0"></i></button>
                            </div>
                            <div class="col-md-4 p-1">
                                <button type="button" class="btn btn-block btn-sm text-center btn-default text-secondary" data-toggle="modal" data-target="#modal-status-{{$employee->id}}" ><i class="fa fa-file-pdf"></i> PDF</button>
                            </div>
                            <div class="col-md-6 p-1">
                                <a type="button" href="/hr/employees/profile/index?employeeid={{$employee->id}}" class="btn btn-block btn-sm text-center btn-default text-bold">View Profile</a>
                            </div>
                        </div> --}}
                        <div class="modal fade" id="modal-status-{{$employee->id}}">
                            <div class="modal-dialog">
                              <div class="modal-content">
                                  <form action="/hr/employees/profile/changestatus" method="GET">
                                    @csrf
                                    <div class="modal-header">
                                      <h4 class="modal-title">Update Status</h4>
                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                      </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p>Do you want to mark this employee - <strong class="text-danger">Inactive</strong>?</p>
                                            </div>
                                        </div>
                                        <input type="hidden" name="status" value="0"/>
                                        <input type="hidden" name="id" value="{{$employee->id}}"/>
                                    </div>
                                    <div class="modal-footer justify-content-between">
                                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                      <button type="submit" class="btn btn-danger">Inactive</button>
                                    </div>
                                  </form>
                              </div>
                              <!-- /.modal-content -->
                            </div>
                            <!-- /.modal-dialog -->
                          </div>
                          <!-- /.modal -->
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="modal fade" id="modal-inactive">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Inactive Employees</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <div class="row">                    
                    @foreach(collect($employees)->where('isactive','0')->values() as $inactiveemp)
                    <div class="col-12">
                        <div class="info-box">
                          <span class="info-box-icon bg-info">
                            <img class="img-circle elevation-2" src="{{asset($inactiveemp->picurl)}}" 
                            onerror="this.onerror = null, this.src='{{asset($avatar)}}'"
                            alt="User Avatar">
                          </span>
            
                          <div class="info-box-content">
                            <form action="/hr/employees/profile/changestatus" method="GET">
                              @csrf
                            <input type="hidden" name="status" value="1"/>
                            <input type="hidden" name="id" value="{{$inactiveemp->id}}"/>
                            <span class="info-box-text"><h6>{{ucwords(strtolower($inactiveemp->lastname))}}, {{ucwords(strtolower($inactiveemp->firstname))}} {{ucwords(strtolower($inactiveemp->suffix))}}</h6></span>
                            <span class="info-box-number">{{$inactiveemp->utype}}</span>
                            <span class="info-box-number text-right"><button type="submit" class="btn btn-success btn-sm">Mark Active</button></span>
                            </form>
                          </div>
                          <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                      </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->
{{-- @foreach($employees as $employee)
<div class="col-md-4 col-12 col-lg-4 col-xl-4 ">
    <a href="/hr/employeeprofile?employeeid={{$employee->id}}">
    <div class="card card-widget widget-user">
        @if($employee->isactive == '1')
            <div class="widget-user-header bg-success">
                <h5 class="widget-user-desc">{{$employee->utype}}</h5>
            </div>
        @elseif($employee->isactive == '0')
            <div class="widget-user-header bg-secondary">
                <h5 class="widget-user-desc">{{$employee->utype}}</h5>
            </div>
        @endif
        <div class="widget-user-image">

            @php
                    $number = rand(1,3);
                    if(strtoupper($employee->gender) == 'FEMALE'){
                        $avatar = 'avatar/T(F) '.$number.'.png';
                    }
                    else{
                        $avatar = 'avatar/T(M) '.$number.'.png';
                    }
                @endphp

            <img class="img-circle elevation-2" src="{{asset($employee->picurl)}}" 
                onerror="this.onerror = null, this.src='{{asset($avatar)}}'"
                alt="User Avatar" style="width: 65% !important"
                >
      
        </div>
        <div class="card-footer" style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">
            <small style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">{{$employee->lastname}}, {{$employee->firstname}} {{$employee->suffix}}</small>
        </div>
    </div>
    </a>
</div>
@endforeach --}}
<!-- Bootstrap 4 -->
@endsection
@section('footerjavascript')
<script>
    
    $(document).ready(function(){
        $(".filter").on("keyup", function() {
            var input = $(this).val().toUpperCase();
            var visibleCards = 0;
            var hiddenCards = 0;

            $(".container").append($("<div class='card-group card-group-filter'></div>"));


            $(".eachemployee").each(function() {
                if ($(this).data("string").toUpperCase().indexOf(input) < 0) {

                $(".card-group.card-group-filter:first-of-type").append($(this));
                $(this).hide();
                hiddenCards++;

                } else {

                $(".card-group.card-group-filter:last-of-type").prepend($(this));
                $(this).show();
                visibleCards++;

                if (((visibleCards % 4) == 0)) {
                    $(".container").append($("<div class='card-group card-group-filter'></div>"));
                }
                }
            });

        });
    })

</script>

@endsection
