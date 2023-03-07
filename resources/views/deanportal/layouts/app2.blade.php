<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Dean's Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

    @php
		$schoolinfo = DB::table('schoolinfo')->first();
	@endphp

  <link rel="stylesheet" href="{{asset('plugins/fontawesome-free/css/all.min.css')}}">
  <link rel="stylesheet" href="{{asset('dist/css/adminlte.min.css')}}">
  <link rel="stylesheet" href="{{asset('plugins/pace-progress/themes/black/pace-theme-flat-top.css')}}">
  <script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
	<link rel="stylesheet" href="{{asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">

  <style>
      .table-head-fixed thead th  { 
              position: sticky; left: 0; 
              background-color: #fff; 
              outline: 2px solid #fff;
              outline-offset: -1px;
              border-bottom: 2px solid #dee2e6 !important;
             
          }

        .table.table-head-fixed thead tr:nth-child(1) th {
            box-shadow: none !important;
            
        }
		
		.nav-bg {
			background-color: {{$schoolinfo->schoolcolor}} !important;
		}
		
		.sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link.active, .sidebar-light-primary .nav-sidebar>.nav-item>.nav-link.active {
			background-color: {{$schoolinfo->schoolcolor}} !important;
			color: #fff;
		}
  </style>

    @yield('headerjavascript')
  @yield('pagespecificscripts')
  
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed ">


<div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-dark pace-primary nav-bg">
      <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars" style="color: #fff"></i></a>
          </li>
        </ul>
      <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown sideright logout">
          <a href="#" id="logout" class="nav-link">
            <i class="fas fa-sign-out-alt logouthover" style="margin-right: 6px; color: #fff"></i>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
              @csrf
            </form>
          </a>
        </li>
      </ul>
    </nav>

    @include('deanportal.inc.sidenav')

    <div class="content-wrapper">
		@include('general.queuingactionbutton.qab')
        @yield('content')
    </div>

  </div>

 

    @include('sweetalert::alert') 

    <script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('dist/js/adminlte.min.js')}}"></script>
    <script src="{{asset('plugins/pace-progress/pace.min.js') }}"></script>
    <script src="{{asset('plugins/sweetalert2/sweetalert2.all.min.js')}}"></script>

    <script src="{{asset('plugins/select2/js/select2.full.min.js')}}"></script> 
    <!-- DataTables -->
    <script src="{{asset('plugins/datatables/jquery.dataTables.js')}}"></script>
    <script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}"></script>
    <script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
	<script src="{{asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
	
    @yield('footerscripts')
    @yield('footerjavascript')
	@yield('qab_sript')
	
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
