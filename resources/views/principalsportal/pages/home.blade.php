@extends('principalsportal.layouts.app2')

@section('pagespecificscripts')

    <link rel="stylesheet" href="{{ asset('plugins/fullcalendar/main.min.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('plugins/fullcalendar-interaction/main.min.css') }}"> --}}
    <link rel="stylesheet" href="{{ asset('plugins/fullcalendar-daygrid/main.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/fullcalendar-timegrid/main.min.css') }}">
    
    <link rel="stylesheet" href="{{ asset('plugins/fullcalendar-bootstrap/main.min.css') }}">
    <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('plugins/fullcalendar/main.min.js') }}"></script>
    <script src="{{ asset('plugins/fullcalendar-daygrid/main.min.js') }}"></script>
    <script src="{{ asset('plugins/fullcalendar-timegrid/main.min.js') }}"></script>
    <script src="{{ asset('plugins/fullcalendar-interaction/main.min.js') }}"></script>
    <script src="{{ asset('plugins/fullcalendar-bootstrap/main.min.js') }}"></script>
    <script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>
    <link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">

    <style>
        .fc-event-container:hover{
            cursor: pointer;
        }
    </style>

@endsection

@section('content')
<style>
    
    .principalfstaff .card-body{
        height: 200px;
        overflow-y: scroll;
    }
    .principalcalendar {
        font-size: 12px;
    }
    /* .principalcalendar .card-body{
        height: 340px;
        overflow-y: scroll; */
    }
    .principald ul li a{
    color: #fff;
    -webkit-transition: .3s;
    }
    .principald li ul li{
        -webkit-transition: .3s;
        border-radius: 5px;
        background: rgba(173, 177, 173, 0.3);
        margin-left: 2px;
    }
    .principald li ul li a:hover {
        transition: .3s;
        border-radius: 5px;
        color: #17a2b8;
    }
    .small-box {
        box-shadow: 1px 2px 2px #001831c9;
        overflow-y: auto scroll;
    }
    .small-box h5 i{
        /* -webkit-text-stroke: .2px #000; */
    }
    .small-box h5 {
        text-shadow: 1px 1px 2px gray;
    }
    .has-treeview ul {
        background-color: rgba(255,255,255,0)!important;
    }
    .info-box {
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    border-radius: .25rem;
    background: #fff;
    line-height:
    display: -ms-flexbox;
    display: flex;
    margin-bottom: 1rem;
    min-height: none!important;
    height: 50px!important;
    padding: .5rem;
    position: relative;
    }
</style>
<section class="content-header">
</section>
<div class="card collapsed-card col-md-12 shadow container-fluid bg-warning">
	<div class="card-header bg-warning" data-card-widget="collapse" style="cursor: pointer;">
		<h3 class="card-title"><i class="fa fa-exclamation-triangle"></i> MSTeams Credentials</h3>
	</div>
	<div class="card-body">
      @php
        try
        {
            $msteamsaccount = DB::table('msteams_creds')
                ->where('studid', DB::table('teacher')->where('userid', auth()->user()->id)->first()->id)
                ->where('department', 'TEACHER')
                ->first();
        }catch(\Exception $error)
        {
            $msteamsaccount = false;
        }
      @endphp
      
		<div class="row">
			<div class="col-6">
				<label>Username</label><br/>
				@if($msteamsaccount)
				  <em>{{$msteamsaccount->username}}</em>
				@endif
			</div>
			<div class="col-6">
				<label>Password</label><br/>
				  <div class="input-group" data-target-input="nearest">
					  @if($msteamsaccount)
						  <input type="password" value="{{$msteamsaccount->password}}" id="msteamspassword" class="form-control form-control-sm" readonly/>
						  <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
							  <div class="input-group-text" id="msteamsvisible"><i class="fa fa-eye"></i></div>
						  </div>
					  @endif
				  </div>
			</div>
		</div>
	</div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-footer">
                <div class="row">
                    <div class="col-sm-3 col-6">
                        <div class="description-block border-right">
                            <h5 class="description-header">{{$present}} / {{count($teacheratt)}}</h5>
                            <span class="description-text text-success">Present Faculty</span>
                        </div>
                    </div>
                    <div class="col-sm-3 col-6">
                        <div class="description-block border-right">
                            <h5 class="description-header">{{$absent}} / {{count($teacheratt)}}</h5>
                            <span class="description-text text-danger">Absent Faculty</span>
                        </div>
                    </div>
                    <div class="col-sm-3 col-6">
                        <div class="description-block border-right">
                            <h5 class="description-header">{{$ontime}} / {{count($teacheratt)}}</h5>
                            <span class="description-text text-success">Ontime Faculty</span>
                        </div>
                    </div>
                    {{-- <div class="col-sm-3 col-6">
                        <div class="description-block">
                            <h5 class="description-header">{{$late}} / {{count($teacheratt)}}</h5>
                            <span class="description-text">Late Faculty</span>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card card-primary card-outline h-100 shadow">
            <div class="card-header bg-info">
                <h3 class="card-title">
                    <i style="color: #ffc107" class="fas fa-th-list"></i>
                        Faculty and Staff Attendance 
                </h3>
            </div>
            <div class="card-body table-responsive p-0" style="height: 150px;">
                <table class="table table-head-fixed table-sm">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Time In</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    @foreach($teacheratt as $item)
                        <tr>
                            <td>
                                {{$item->teacher}}
                            </td>
                            @if($item->time!='00:00')
                                <td>
                                    {{$item->time}}
                                </td>
                            @else
                                <td></td>
                            @endif
                            @if($item->time=='00:00')
                                <td>
                                    <span class="badge bg-danger">ABSENT</span>
                                </td>
                            @elseif(\Carbon\Carbon::createFromTimeString($item->time,'Asia/Manila')->isoFormat('HH:mm')<=\Carbon\Carbon::createFromTimeString($item->customamin,'Asia/Manila')->isoFormat('HH:mm'))
                                <td>
                                    <span class="badge bg-success">PRESENT</span>
                                </td>
                            @else
                                <td>
                                    <span class="badge bg-warning">LATE</span>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6" style="height: 250px;">
	{{--<di v class="card h-100 shadow">
            <div class="card-header bg-primary">
                <h3 class="card-title">
                    <i style="color: #ffc107" class="fas fa-th-list"></i>
                        Student Attendance
                </h3>
            </div>
            <div class="card-body">
                <div class="col-12 col-sm-12 col-md-12" style="height: 60px">
                    <div class="info-box" style="min-height: 0px">
                        <span style="width: 250px;"  class="info-box-icon bg-success elevation-1 pt-1"><h3>0 /  {{Session::get('gsstudentcount')+Session::get('psstudentcount')+Session::get('shstudentcount')+Session::get('jhstudentcount')}}</h3></span>

                        <div class="info-box-content">
                            <span class="info-box-text"><i class="fas fa-user"></i> <b>PRESENT</b></span>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-12 col-md-12" style="height: 60px">
                    <div class="info-box" style="min-height: 0px">
                        <span style="width: 250px;"  class="info-box-icon bg-warning elevation-1 pt-1"><h3>0 /  {{Session::get('gsstudentcount')+Session::get('psstudentcount')+Session::get('shstudentcount')+Session::get('jhstudentcount')}}</h3></span>

                        <div class="info-box-content">
                            <span class="info-box-text"><i class="fas fa-user"></i> <b>LATES</b></span>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-12 col-md-12" style="height: 50px">
                    <div class="info-box" style="min-height: 0px">
                        <span style="width: 250px;"  class="info-box-icon bg-danger elevation-1 pt-1"><h3>0 / {{Session::get('gsstudentcount')+Session::get('psstudentcount')+Session::get('shstudentcount')+Session::get('jhstudentcount')}}</h3></span>

                        <div class="info-box-content">
                            <span class="info-box-text"><i class="fas fa-user"></i> <b>ABSENT</b></span>
                        </div>
                    </div>
                </div>
            </div>
	</div> --}}
	<div class="card principald shadow">
            <div class="card-header bg-primary">
                <span style="font-size: 20px"><i style="color: #ffc107;" class="fas fa-users"></i> <b>STUDENTS LIST</b></span>
            </div>
            <div class="card-body pt-0"  style="overflow-y:scroll;">
                <table class="table table-head-fixed table-sm">
                    <thead>
                        <tr>
                            <th>Total Numbers</th>
                            <th>Academic Program</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (App\Models\Principal\SPP_AcademicProg::getPrincipalAcadProg(Session::get('prinInfo')->id) as $item)
                            <tr>
                                <td>
                                    @if($item->id == 2)
                                        {{$studcount = Session::get('psstudentcount')}} 
                                    @elseif($item->id == 3)
                                        {{$studcount = Session::get('gsstudentcount')}}
                                    @elseif($item->id == 4)
                                        {{$studcount = Session::get('jhstudentcount')}}
                                    @elseif($item->id == 5)
                                        {{$studcount = Session::get('shstudentcount')}}
                                    @endif
                                </td>
                                <td>
                                    <option class=" {{ $studcount > 0 ? 'text-success':'text-danger'}}" value="{{Crypt::encrypt($item->id)}}">{{$item->progname}} <p style="background-color:#FF0055;">( {{$studcount}} )</p></option>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row mt-3">
    <div class="col-md-6">
		<div class="col-row principalcalendar">
			<div class="card h-100 shadow">
				<div class="card-header bg-info">
					<h3 class="card-title">
						<i style="color: #ffc107" class="fas fa-calendar-day"></i>
						School Calendar 
					</h3>
				</div>
				<div class="card-body  p-0">
					<div id="newcal" ></div>
				</div>
			</div>
		</div>
    </div>
    <div class="col-md-6 col-12">
        

        {{-- <div class="card bg-primary principald">
            <div class="card-header">
            <h5><i style="color: #ffc107" class="fas fa-align-justify"></i> <b>SCHOOL FORMS (SF)</b></h5>
            </div>
            <div class="card-body">
            <li class="nav-item has-treeview {{Request::url() == url('/principalSF9')  || Request::url() == url('/principalSF6') || Request::url() == url('/principalPortalForms')? 'menu-open':''}}">
                <ul class="nav nav-treeview udernavs ">
                    <li class="nav-item">
                        <a href="/principalPortalForms" class="nav-link {{Request::url() == url('/principalPortalForms') ? 'active':''}}">School Form 4
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/principalSF6" class="nav-link {{Request::url() == url('/principalSF6') ? 'active':''}}">School Form 6
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/principalSF9" class="nav-link {{Request::url() == url('/principalSF9') ? 'active':''}}">School Form 9
                        </a>
                    </li>
                </ul>
            </li>
            </div>
        </div> --}}
    </div>
</div>

{{-- <div class="row mb-3">
    <div class="col-md-6">
    <div class="card h-100">
            <div class="card-header bg-success">
                <h3 class="card-title">
                    <i style="color: #ffc107" class="fas fa-th-list"></i>
                    TOTAL NUMBER OF STUDENTS
                </h3>
            </div>
            <div class="card-body">
                <div class="col-12 col-sm-12 col-md-12" style="height: 60px">
                    <div class="info-box" style="min-height: 0px">
                        <span style="height: 38px;width: 57px;"  class="info-box-icon bg-success elevation-1"><h3>{{$totalnumberofescstudents}}</h3></span>

                        <div class="info-box-content">
                            <span class="info-box-text"><i class="fas fa-user"></i> <b>ESC GRANTEE</b></span>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-12 col-md-12" style="height: 60px">
                    <div class="info-box" style="min-height: 0px">
                        <span style="height: 38px;width: 57px;"  class="info-box-icon bg-warning elevation-1"><h3>{{$totalnumberofvoucherstudents}}</h3></span>

                        <div class="info-box-content">
                            <span class="info-box-text"><i class="fas fa-user"></i> <b>VOUCHER</b></span>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-12 col-md-12" style="height: 50px">
                    <div class="info-box" style="min-height: 0px">
                        <span style="height: 38px;width: 57px;"  class="info-box-icon bg-danger elevation-1"><h3>{{$totalnumberofregularstudents}}</h3></span>

                        <div class="info-box-content">
                            <span class="info-box-text"><i class="fas fa-user"></i> <b>REGULAR</b></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="/summarytotalnumberofstudents/view" class="nav-link {{Request::url() == url('/summarytotalnumberofstudents/view') ? 'active':''}} btn btn-primary">See List
                </a>
        </div>
    </div>
</div>
</div> --}}



@endsection

@section('footerjavascript')

<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>

<script>

    $( document ).ready(function() {
		
		var syid = '<?php echo DB::table('sy')->where('isactive',1)->first()->id; ?>';
        var currentportal = @json(Session::get('currentPortal'));
		
		
        $(document).on('click','#msteamsvisible', function(){
            if ($('#msteamspassword').attr('type') === "password") {
                $('#msteamspassword').attr('type','text');
                $('#msteamspassword').val($.trim($('#msteamspassword').val()))
            } else {
                $('#msteamspassword').attr('type','password');
            }
        })

        function showTime(){

            var datetime = new Date().toLocaleString("en-US", {timeZone: "Asia/Manila"})
           
            // $('#toDate')[0].innerHTML = moment(datetime).format('MMM DD, YYYY')+'<span class="float-right">'+moment(datetime).format('hh:mm:ss a')+'</span>'

        }

        setInterval(showTime,1000);

        var datefrom = new Date().toLocaleString("en-US", {timeZone: "Asia/Manila"})


        

        console.log('sdfsdf');

        $(function () {
            $('#day').daterangepicker({
                locale: {
                    format: 'YYYY/MM/DD'
                }
            })
        })

        var date = new Date()
        var d    = date.getDate(),
        m    = date.getMonth(),
        y    = date.getFullYear()

        var schedule = [];

        @foreach($schoolcalendar as $item)

            @if($item->noclass == 1)
                var backgroundcolor = '#dc3545';
            @else
                var backgroundcolor = '#00a65a';
            @endif

            schedule.push({
                title          : '{{$item->description}}',
                start          : '{{$item->datefrom}}',
                end            : '{{$item->dateto}}',
                backgroundColor: backgroundcolor,
                borderColor    : backgroundcolor,
                allDay         : true,
                id: '{{$item->id}}'
            })

        @endforeach


        var Calendar = FullCalendar.Calendar;

        var calendarEl = document.getElementById('newcal');

        var calendar = new Calendar(calendarEl, {
            plugins: [ 'bootstrap', 'interaction', 'dayGrid'],
            header    : {
                left:   'title',
                center: '',
                right:  'today prev,next'
            },
            // events    : schedule,
            events    : '/school-calendar/getall-event/'+currentportal+'/'+syid,
            themeSystem: 'bootstrap',
            eventStartEditable: false,
            timeZone: 'UTC',
            height : 'auto',
            eventClick: function(info) {
            
                $('#modal-primary').modal('show')
                $.ajax({
                type:'GET',
                url:'/principalgetevent',
                data:{
                id:info.event.id
                },
                
                success:function(data) {

                    $('#des').empty()
                    $('#date').empty()
                    $('#des').empty()
                    $('#type').empty()

                    if(data[0].data[0].annual == 1){
                    
                    $('#annual').prop('checked','checked')
                    }

                    if(data[0].data[0].noclass == 1){
                    $('#noclass').prop('checked','checked')
                    }
        
                    // $('#clas').val(data[0].data[0].eventtype)
            
                    var typeid = data[0].data[0].eventtype
                    var scholcaltypeid = data[0].data[0].typeid

            
                
                    $.ajax({
                    type:'GET',
                    url:'/principalgeteventtype',
                    data:{
                        id:typeid
                    },
                    success:function(data) {
            
                        $('#type').empty();

                        $.each(data,function(key,value){

                        if(scholcaltypeid == value.id){
                            $('#type').append(value.typename)
                        }
                        
                        
                        })
                        
                    }
                    })
                    var datefrom = new Date(data[0].data[0].datefrom)
                    var dateto = new Date(data[0].data[0].dateto)
                    $('#des').append(data[0].data[0].description)
                    $('#date').append(moment(datefrom).format('MMM DD, YYYY'))
                        
                    if(moment(datefrom).format('MMM DD, YYYY') != moment(dateto).format('MMM DD, YYYY')){
                        $('#date').append(' - '+ moment(dateto).format('MMM DD, YYYY'))
                    }

                }
            })

            },

        });

        calendar.render();

        // $('.fc-prev-button').addClass('btn-sm')
        // $('.fc-next-button').addClass('btn-sm')
        $('.fc-today-button').addClass('btn-sm')
        $('.fc-left').css('font-size','13px')
        $('.fc-toolbar').css('margin','0')
        $('.fc-toolbar').css('padding-top','0')
        
        });

</script>



    
@endsection
