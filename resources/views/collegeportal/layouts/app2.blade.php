<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>College Admin Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{asset('plugins/fontawesome-free/css/all.min.css')}}">
    <link rel="stylesheet" href="{{asset('dist/css/adminlte.min.css')}}">
    <script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('assets\css\sideheaderfooter.css')}}">
   
    @yield('pagespecificscripts')
  
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
<div class="wrapper">
      <nav class="main-header navbar navbar-expand navbar-white navbar-light navss">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
            </li>
          </ul>
          <!-- <div class="form-inline ml-3">
            <div class="input-group input-group-sm">
              Students Portal
            </div>
          </div> -->
        <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown notification">
          <a class="nav-link notnum" data-toggle="dropdown" href="#">
          </a>
          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right notificationholder">
          </div>
        </li>
        
        
          
      <li class="nav-item dropdown sideright">
        <a class="nav-link" data-toggle="dropdown" href="{{ route('logout') }}" onclick="event.preventDefault(); $('#logout-form').submit();" >
          <i class="fas fa-sign-out-alt logouthover" style="margin-right: 7px; color: #fff"></i>
          <!-- <span class="logoutshow" id="logoutshow"> Logout</span> -->
          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
          </form>
        </a>
        
       
      </li>
        </ul>
      </nav>
      <aside class="main-sidebar sidebar-dark-primary elevation-4 asidebar">
      <div class="ckheader">
        <a href="#" class="brand-link sidehead">
          <img src="{{asset(DB::table('schoolinfo')->first()->picurl)}}"
               alt="AdminLTE Logo"
               class="brand-image img-circle elevation-3"
               style="opacity: .8">
          <span class="brand-text font-weight-light" style="position: absolute;top: 6%;">{{DB::table('schoolinfo')->first()->abbreviation}}</span>
          <span class="brand-text font-weight-light" style="position: absolute;top: 50%;font-size: 16px!important;color:#ffc107"><b>ADMIN'S PORTAL</b></span>
        </a>
    </div>
        <div class="sidebar">
              <div class="user-panel mt-3 pb-3 mb-3 d-flex">
              <div class="image">
                <img src="{{asset('avatar/T(M) 3.png')}}" 
                  onerror="this.onerror=null; this.src=''"
                  class="img-circle elevation-2" alt="User Image">
              </div>
              <div class="info pt-0" style="    margin-top: -7px;">
                  <a href="#" class="d-block">{{auth()->user()->name}}</a>
                  <h6 class="text-white m-0 text-warning">{{auth()->user()->email}}</h6>
              </div>
            </div>
          @include('collegeportal.inc.sidenav')
        </div>
      </aside>
      <div class="content-wrapper">
        @yield('content')
       
           
       
      </div>
      
    </div>

   
    @include('sweetalert::alert') 

   

    <script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('dist/js/adminlte.min.js')}}"></script>
    <script src="{{asset('plugins/sweetalert2/sweetalert2.all.min.js')}}"></script>
    
    
    <script>


      $(document).ready(function(){

          @if ($errors->any())
            
            @if($errors->has('modalName'))
               
                  $('#'+'{{ $errors->first('modalName') }}').modal('show');
                 
            @else
                $('#'+'{{ $modalInfo->modalName }}').modal('show');
            @endif
         
          @endif
      })
      
    </script>
     @yield('footerscript')
   
  </body>
</html>
