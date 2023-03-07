<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-15">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
    <link href="{{asset('assets/css/gijgo.min.css')}}" rel="stylesheet" />
    <link rel="stylesheet" href="{{asset('plugins/fontawesome-free/css/all.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/jqvmap/jqvmap.min.css')}}">
    <link rel="stylesheet" href="{{asset('dist/css/adminlte.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/summernote/summernote-bs4.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/pace-progress/themes/black/pace-theme-flat-top.css')}}">
    <link rel="stylesheet" href="{{asset('assets\css\sideheaderfooter.css')}}">
    <!-- dropzonejs -->
    <link rel="stylesheet" href="{{asset('plugins/dropzone/min/dropzone.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/toastr/toastr.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
    <style>
            .table-responsive {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            .profile-img-wrap.edit-img {
                border-radius: 50%;
                margin: 0 auto 30px;
                position: relative;
            }
            .profile-img-wrap {
                height: 120px;
                position: absolute;
                width: 120px;
                background: #fff;
                overflow: hidden;
            }.profile-img-wrap.edit-img .fileupload.btn {
                left: 0;
            }
            .fileupload.btn {
                position: absolute;
                right: 0;
                bottom: 0;
                background: rgba(33, 33, 33, 0.5);
                border-radius: 0;
                padding: 3px 10px;
                border: none;
            }
            .fileupload input.upload {
                cursor: pointer;
                filter: alpha(opacity=0);
                font-size: 20px;
                margin: 0;
                opacity: 0;
                padding: 0;
                position: absolute;
                right: -3px;
                top: -3px;
                padding: 5px;
            }

            input[type="file"] {
                height: auto;
            }
            img {
                vertical-align: middle;
                border-style: none;
            } */
    </style>
    @yield('headerjavascript')
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed accent-info pace-primary">
        <div class="wrapper" style="min-height: 1000px;">
          <!-- Navbar -->
          @include('hr.inc.header')
          <!-- /.navbar -->
        
          <!-- Main Sidebar Container -->
          @include('hr.inc.sidenav')
        
          <!-- Content Wrapper. Contains page content -->
          
          <!-- /.content-wrapper -->
        
          <!-- Control Sidebar -->
          <div class="content-wrapper" >
                <!-- Content Header (Page header) -->
                
            
                <!-- Main content -->
                <section class="content">
                        <div class="container-fluid">
							@include('general.queuingactionbutton.qab')
                            {{-- <br> --}}
                            @yield('content')
                        </div>
                </section>

                <!-- /.content -->
              </div>
          <!-- /.control-sidebar -->
        
          <!-- Main Footer -->
          
        </div>
        <!-- ./wrapper -->
        
        <!-- REQUIRED SCRIPTS -->
        
    <script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('plugins/inputmask/min/jquery.inputmask.bundle.min.js')}}"></script>
    <script src="{{asset('assets/scripts/gijgo.min.js')}}" ></script>
    <script src="{{asset('plugins/moment/moment.min.js')}}"></script>
    <script src="{{asset('plugins/sweetalert2/sweetalert2.min.js')}}"></script>
    <script src="{{asset('plugins/croppie/croppie.js')}}"></script>
    <link rel="stylesheet" href="{{asset('plugins/croppie/croppie.css')}}">
    <script src="{{asset('plugins/toastr/toastr.min.js')}}"></script>
    <script src="{{asset('plugins/bootstrap-switch/js/bootstrap-switch.min.js')}}"></script>
    <script src="{{asset('plugins/select2/js/select2.full.min.js')}}"></script> 
    <script src="{{asset('dist/js/adminlte.js')}}"></script>
    <script src="{{asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
    <script src="{{asset('plugins/chart.js/Chart.min.js')}}"></script>
    <script src="{{asset('dist/js/demo.js')}}"></script>
    <script src="{{asset('dist/js/pages/dashboard3.js')}}"></script>
    <script src="{{asset('plugins/pace-progress/pace.min.js')}}"></script>
    <script src="{{asset('plugins/sweetalert2/sweetalert2.all.min.js')}}"></script>
    <!-- DataTables -->
    <script src="{{asset('plugins/datatables/jquery.dataTables.js')}}"></script>
    <script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}"></script>
    <script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
    <script src="{{asset('plugins/summernote/summernote-bs4.min.js')}}"></script>
    <!-- dropzonejs -->
    <script src="{{asset('plugins/dropzone/min/dropzone.min.js')}}"></script>

    
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
        
        </body>
        </html>