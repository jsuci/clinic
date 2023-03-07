
<link rel="stylesheet" href="{{asset('plugins/fontawesome-free/css/all.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css')}}">
<link rel="stylesheet" href="{{asset('dist/css/adminlte.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/fullcalendar/main.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/fullcalendar-daygrid/main.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/fullcalendar-timegrid/main.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/fullcalendar-bootstrap/main.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/fullcalendar-interaction/main.min.css')}}">

@extends('clinic_nurse.layouts.app')

<style>
    .dataTable                  { font-size:80%; }
    .tschoolschedule .card-body { height:250px; }
    .tschoolcalendar            { font-size: 12px; }
    .tschoolcalendar .card-body { height: 250px; overflow-x: scroll; }
    .teacherd ul li a           { color: #fff; -webkit-transition: .3s; }
    .teacherd ul li             { -webkit-transition: .3s; border-radius: 5px; background: rgba(173, 177, 173, 0.3); margin-left: 2px; }
    .sf5                        { background: rgba(173, 177, 173, 0.3)!important; border: none!important; }
    .sf5menu a:hover            { background-color: rgba(173, 177, 173, 0.3)!important; }
    .teacherd ul li:hover       { transition: .3s; border-radius: 5px; padding: none; margin: none; }

    .small-box                  { box-shadow: 1px 2px 2px #001831c9; overflow-y: auto scroll; }

    .small-box h5               { text-shadow: 1px 1px 2px gray; }

    img{
        border-radius: unset !important;
    }

</style>
@section('content')
@php
    use \Carbon\Carbon;
    $now = Carbon::now();
    $comparedDate = $now->toDateString();

    $appointments = DB::table('clinic_appointments')
            ->select('clinic_appointments.*','users.type','usertype.utype')
            ->join('users','clinic_appointments.userid','=','users.id')
            ->join('usertype','users.type','=','usertype.id')
            ->where('clinic_appointments.adate', date('Y-m-d'))
            ->where('clinic_appointments.admitted','1')
            ->where('clinic_appointments.admittedby',auth()->user()->id)
            ->get();

      if(count($appointments)>0)
      {
          foreach($appointments as $appointment)
          {
              if($appointment->type == 7)
              {
                  $info = DB::table('studinfo')
                      ->where('userid', $appointment->userid)
                      ->first();
                  $info->title = null;
              }else{
                  $info = DB::table('teacher')
                      ->where('userid', $appointment->userid)
                      ->first();
              }
              $name_showfirst = "";

              if($info->title != null)
              {
                  $name_showfirst.=$info->title.' ';
              }
              $name_showfirst.=$info->firstname.' ';

              if($info->middlename != null)
              {
                  $name_showfirst.=$info->middlename[0].'. ';
              }
              $name_showfirst.=$info->lastname.' ';
              $name_showfirst.=$info->suffix.' ';

              $appointment->name_showfirst = $name_showfirst;

              $name_showlast = "";

              if($info->title != null)
              {
                  $name_showlast.=$info->title.' ';
              }
              $name_showlast.=$info->lastname.', ';
              $name_showlast.=$info->firstname.' ';

              if($info->middlename != null)
              {
                  $name_showlast.=$info->middlename[0].'. ';
              }
              $name_showlast.=$info->suffix.' ';

              $appointment->name_showlast = $name_showlast;
              $appointedname = '';
          }
      }
        

@endphp
<div class="row">
  <div class="col-md-4">
      <div class="info-box mb-3 bg-warning">
        <span class="info-box-icon">
            <i class="fa fa-laptop-medical"></i>
          </span>

        <div class="info-box-content">
          <span class="info-box-text">Medical</span>
          <span class="info-box-number">History</span>
        </div>
        <!-- /.info-box-content -->
      </div>
  </div>
</div>
<div class="row">
  <div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
            <i class="ion ion-clipboard mr-1"></i>
                <strong>Complaints for today</strong>
            </h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <ul class="todo-list" data-widget="todo-list">
                <li>
                    <div  class="icheck-primary d-inline ml-2">
                        <input type="checkbox" value="" name="todo1" id="todoCheck1">
                        <label for="todoCheck1"></label>
                    </div>
                    <!-- todo text -->
                    <span class="text">Design a nice theme</span>
                    <!-- Emphasis label -->
                    <small class="badge badge-danger"><i class="far fa-clock"></i> 2 mins</small>
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                        <i class="fas fa-edit"></i>
                        <i class="fas fa-trash-o"></i>
                    </div>
                </li>
            </ul>
        </div>
        <!-- /.card-body -->
        {{-- <div class="card-footer clearfix">
            <button type="button" class="btn btn-info float-right"><i class="fas fa-plus"></i> Add item</button>
        </div> --}}
    </div>
  </div>
  <div class="col-md-6">
      <div class="card">
          <div class="card-header">
              <h3 class="card-title">
              <i class="ion ion-clipboard mr-1"></i>
                  <strong>Appointments admitted for today</strong>
              </h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            @if(count($appointments)>0)
              <ul class="todo-list" data-widget="todo-list">
                @foreach($appointments as $appointment)
                  <li>
                      <div  class="icheck-primary d-inline ml-2">
                          <input type="checkbox" value="{{$appointment->id}}" name="appointment" class="check-appointment" id="todoCheckAppointment{{$appointment->id}}" @if($appointment->label == 1) checked @endif>
                          <label for="todoCheckAppointment{{$appointment->id}}"></label>
                      </div>
                      <!-- todo text -->
                      <span class="text text-gray">{{$appointment->name_showlast}}</span>
                      <!-- Emphasis label -->
                      @if($appointment->atime == null)
                      @else
                      <small class="badge badge-info"><i class="far fa-clock"></i> {{date('M d, Y h:i A', strtotime($appointment->atime))}}</small>
                      @endif
                      <!-- General tools such as edit or delete-->
                      <div class="tools">
                          <i class="fas fa-edit"></i>
                          <i class="fas fa-trash-o"></i>
                      </div>
                  </li>
                @endforeach
              </ul>
            @else
            <div class="row">
              <div class="col-md-12 text-center">No Appointments for today</div>
            </div>
            @endif
          </div>
          <!-- /.card-body -->
          {{-- <div class="card-footer clearfix">
              <button type="button" class="btn btn-info float-right"><i class="fas fa-plus"></i> Add item</button>
          </div> --}}
      </div>
  </div>
    {{-- <div class="col-md-8">
        <div class="row">
            <div class="col-md-4">
                <div class="info-box mb-3 bg-success">
                  <span class="info-box-icon">
                      <i class="fas fa-teeth"></i>
                    </span>
        
                  <div class="info-box-content">
                    <span class="info-box-text">Dental</span>
                    <span class="info-box-number">Clinic</span>
                  </div>
                  <!-- /.info-box-content -->
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box mb-3 bg-info">
                  <span class="info-box-icon">
                      <i class="fa fa-id-card-alt"></i>
                    </span>
        
                  <div class="info-box-content">
                    <span class="info-box-text">Patients</span>
                     <span class="info-box-number">Dental</span> 
                  </div>
                  <!-- /.info-box-content -->
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box mb-3 bg-warning">
                  <span class="info-box-icon">
                      <i class="fa fa-laptop-medical"></i>
                    </span>
        
                  <div class="info-box-content">
                    <span class="info-box-text">Medical</span>
                    <span class="info-box-number">History</span>
                  </div>
                  <!-- /.info-box-content -->
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                        <i class="ion ion-clipboard mr-1"></i>
                            <strong>Appointments</strong>
                        </h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <ul class="todo-list" data-widget="todo-list">
                            <li>
                                <div  class="icheck-primary d-inline ml-2">
                                    <input type="checkbox" value="" name="todo1" id="todoCheck1">
                                    <label for="todoCheck1"></label>
                                </div>
                                <!-- todo text -->
                                <span class="text">Design a nice theme</span>
                                <!-- Emphasis label -->
                                <small class="badge badge-danger"><i class="far fa-clock"></i> 2 mins</small>
                                <!-- General tools such as edit or delete-->
                                <div class="tools">
                                    <i class="fas fa-edit"></i>
                                    <i class="fas fa-trash-o"></i>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer clearfix">
                        <button type="button" class="btn btn-info float-right"><i class="fas fa-plus"></i> Add item</button>
                    </div> 
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="row">
            <div class="col-md-12 mb-2">
                <button type="button" class="btn btn-block btn-success" id="btn-createapp">Create Appointment</button>
            </div>
            <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Now Serving</h3>
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body p-0">
                    <ul class="products-list product-list-in-card pl-2 pr-2">
                        <!-- /.item -->
                        <li class="item">
                          <div class="product-img">
                            <img src="dist/img/default-150x150.png" alt="Product Image" class="img-size-50">
                          </div>
                          <div class="product-info">
                            <a href="javascript:void(0)" class="product-title">
                              Check Ups for today <span class="badge badge-danger float-right">
                              $350
                            </span>
                            </a>
                            <span class="product-description">
                              Xbox One Console Bundle with Halo Master Chief Collection.
                            </span>
                          </div>
                        </li>
                    </ul>
                  </div>
                </div>
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Recently Added Products</h3>
                  </div> 
                  <!-- /.card-header -->
                  <div class="card-body p-0">
                    <ul class="products-list product-list-in-card pl-2 pr-2">
                        <!-- /.item -->
                        <li class="item">
                          <div class="product-img">
                            <img src="{{asset('assets/icons/medical/png/32/patient_room.png')}}" alt="Product Image" class="img-size-50">
                          </div>
                          <div class="product-info">
                            <a href="javascript:void(0)" class="product-title">
                              Check Ups for today <span class="badge badge-danger float-right">
                              $350
                            </span>
                            </a>
                            <span class="product-description">
                              Xbox One Console Bundle with Halo Master Chief Collection.
                            </span>
                          </div>
                        </li>
                      <li class="item">
                        <div class="product-img">
                            <img src="{{asset('assets/icons/medical/png/32/appointment.png')}}" alt="Product Image" class="img-size-50">
                        </div>
                        <div class="product-info">
                          <a href="javascript:void(0)" class="product-title">Applied Today
                            <span class="badge badge-warning float-right">$1800</span></a>
                          <span class="product-description">
                            Samsung 32" 1080p 60Hz LED Smart HDTV.
                          </span>
                        </div>
                      </li>
                      <!-- /.item -->
                      <li class="item">
                        <div class="product-img">
                            <img src="{{asset('assets/icons/medical/png/32/treatment.png')}}" alt="Product Image" class="img-size-50">
                        </div>
                        <div class="product-info">
                          <a href="javascript:void(0)" class="product-title">Applied Total
                            <span class="badge badge-info float-right">$700</span></a>
                          <span class="product-description">
                            26" Mongoose Dolomite Men's 7-speed, Navy Blue.
                          </span>
                        </div>
                      </li>
                    </ul>
                  </div>
                </div>
            </div>
        </div> --}}
</div>
{{-- <div class="row">
</div> --}}
<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
<script src="{{asset('plugins/moment/moment.min.js')}}"></script>
<script src="{{asset('plugins/fullcalendar/main.min.js')}}"></script>
<script src="{{asset('plugins/fullcalendar-daygrid/main.min.js')}}"></script>
<script src="{{asset('plugins/fullcalendar-timegrid/main.min.js')}}"></script>
<script src="{{asset('plugins/fullcalendar-interaction/main.min.js')}}"></script>
<script src="{{asset('plugins/fullcalendar-bootstrap/main.min.js')}}"></script>
<script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.js')}}"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}"></script>
<script>
    $(document).ready(function(){
        $('#btn-createapp').on('click', function(){
            window.open("/clinic/appointment/index");
        })
    })
</script>
@endsection
