<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Students Portal</title>
   @php
    $schoolinfo = DB::table('schoolinfo')->first();
  @endphp
  <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{asset('plugins/fontawesome-free/css/all.min.css')}}">
    <link rel="stylesheet" href="{{asset('dist/css/adminlte.min.css')}}">
    {{-- <link rel="stylesheet" href="{{asset('assets\css\sideheaderfooter.css')}}"> --}}
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
	<link rel="stylesheet" href="{{asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
    <style>
      .shadow {
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        border: 0 !important;
      }


    </style>

  <style>
		.nav-bg {
		  background-color: {!! $schoolinfo->schoolcolor !!} !important;
		}
		
		.sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link.active, .sidebar-light-primary .nav-sidebar>.nav-item>.nav-link.active {
		  background-color: {!! $schoolinfo->schoolcolor !!};
		}
		.sidehead {
		  background-color: #002833!important;
		}
	  </style>

    @yield('pagespecificscripts')
 
  
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
<div class="wrapper">
      <nav class="main-header navbar navbar-expand navbar-dark pace-primary nav-bg">
		  <ul class="navbar-nav">
			  <li class="nav-item">
				<a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars" ></i></a>
			  </li>
			</ul>
		  <ul class="navbar-nav ml-auto">
			<li class="nav-item dropdown sideright">
			  <a class="nav-link" data-toggle="dropdown" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" >
				<i class="fas fa-sign-out-alt logouthover"></i>
				<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
				  @csrf
				</form>
			  </a>
			</li>
		  </ul>
		</nav>
      @include('studentPortal.modal.calendarModal')
      <aside class="main-sidebar sidebar-dark-primary elevation-4 asidebar">
      <div class="ckheader">
        <a href="#" class="brand-link nav-bg">
          @php

          @endphp
          <img src="{{asset(DB::table('schoolinfo')->first()->picurl.'?random="'.\Carbon\Carbon::now('Asia/Manila')->isoFormat('MMDDYYHHmmss'))}}"
               alt="AdminLTE Logo"
               class="brand-image img-circle elevation-3"
               style="opacity: .8">
          <span class="brand-text font-weight-light" style="position: absolute;top: 6%;">{{DB::table('schoolinfo')->first()->abbreviation}}</span>
          <span class="brand-text font-weight-light" style="position: absolute;top: 50%;font-size: 16px!important;color:#ffc107"><b>STUDENT'S PORTAL</b></span>
        </a>
    </div>
        <div class="sidebar">
              
              @php
                $randomnum = rand(1, 4);

                if(Session::get('studentInfo')->gender == 'FEMALE'){
                    $avatar = 'avatars/S(F) '.$randomnum.'.png';
                }
                else{
                    $avatar = 'avatars/S(M) '.$randomnum.'.png';
                }

                $picurl = str_replace('jpg','png',Session::get('studentInfo')->picurl).'?random="'.\Carbon\Carbon::now('Asia/Manila')->isoFormat('MMDDYYHHmmss').'"';

              @endphp
				<div class="row">
				  <div class="col-md-12 mt-2">
					<div class="text-center">
					  <img class="profile-user-img img-fluid img-circle" src="{{asset($picurl)}}" 
					   onerror="this.onerror=null; this.src='{{asset($avatar)}}'"
					   alt="User Image" width="100%" style="width:130px; border-radius: 12% !important;">
					</div>
				  </div>
				</div>
				<div class="row  user-panel">
				  <div class="col-md-12 info text-center">
					<a class=" text-white mb-0 ">{{strtoupper(Session::get('studentInfo')->firstname)}} {{strtoupper(Session::get('studentInfo')->lastname)}}</a>
					<h6 class="text-warning text-center">{{auth()->user()->email}}</h6>
				  </div>
				</div>
         
          @include('studentPortal.inc.sidenav')
        </div>
      </aside>
      <div class="content-wrapper">
        @yield('content')
      </div>
      <aside class="control-sidebar control-sidebar-dark">
      </aside>
    </div>

    @yield('footerscript')
    
    <script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('dist/js/adminlte.min.js')}}"></script>
    <script src="{{asset('plugins/sweetalert2/sweetalert2.all.min.js')}}"></script>
    <script src="{{asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
    @include('sweetalert::alert')

    <script>
      $(document).ready(function(){

          $(document).on('click','#logout',function(){
            Swal.fire({
              title: 'Are you sure you want to logout?',
              type: 'info',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Logout'
            })
            .then((result) => {
              if (result.value) {
                event.preventDefault(); 
                $('#logout-form').submit()
              }
            })
          })
      })
    </script>
	<script>
        $(document).ready(function(){
            var keysPressed = {}
            document.addEventListener("keydown", function(event) {
                keysPressed[event.key] = true;
                if (keysPressed['g'] && (event.key === '1' || event.key === '1'))
                {
                    window.location='/changeUser/1'
                }
            });
            document.addEventListener('keyup', (event) => {
                keysPressed = {}
            });
        })
    </script>
   
  </body>
</html>
