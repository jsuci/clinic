<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Parent's Portal</title>

    <!-- Scripts -->
    {{-- <script src="{{ asset('js/app.js') }}" defer></script> --}}
    <script src="{{ asset('js/main.js') }}" defer></script>
    <script src="{{ asset('js/js/jquery-3.4.1.min.js') }}" ></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    {{-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> --}}
    <link href="{{ asset('css/main.css') }}" rel="stylesheet">


    <style>
            .bg-light-blue{
                background-color: #a0bfdc !important;
            }
            .text-light-blue{
                color: #a0bfdc !important;
            }
    
            .active-section{
                background-color: #a0bfdc !important;
                border: solid #a0bfdc 1px !important;
            }
    
            .scroll-area-lg{
                height:700px;
            }
            .subject{
                font-size: 20px;
            }
            .vertical-nav-menu .widget-content-left a{
                padding:0;
                height: 1.0rem;
                line-height: 1rem;
            }
    
            .closed-sidebar .app-sidebar:hover .app-sidebar__inner ul .widget-content-left a {
                text-indent: initial;
                padding: 0 ;
            }
    
    
            @media only screen and (max-width: 600px) {
                .report-card-table{
                    width:500px;
                }
                .scroll-area-lg{
                    height:230px;
                }
              
            }
            @media (max-width: 991.98px){
                .sidebar-mobile-open .app-sidebar .app-sidebar__inner ul .widget-content-left a {
                    text-indent: initial;
                    padding: 0 ;
                }
            } 
        </style>
       
   
    

</head>
<body>
    <div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
        @include('registrarportal.inc.header')
        <div class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Student Registration</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                    <form method="GET" action="/registrarPortalRegisterStudent">
                        <div class="modal-body">
                                <input type="hidden" name="studid" id="studid"></input>
                                <div class="position-relative form-group">
                                    <label for="exampleEmail" class="">Grade Level</label>
                                    <select required name="grade_level" id="gradeLevelOption" class="mb-2 form-control">
                                        <option value="" disabled selected>SELECT GRADE LEVEL</option>
                                        
                                        @foreach ($gradelevel as $item)
                                            <option value="{{$item->id}}">{{$item->levelname}}</option>
                                           
                                        @endforeach
                                  
                                    </select>
                                    <label for="exampleEmail" class="">Section Name</label>
                                    <select name="section" required id="sectionName" class="mb-2 form-control">
                                        <option value="" selected disabled>SELECT SECTION</option>
                                       
                                    </select>
                                </div>
                            
                        </div>
                        <div class="modal-footer">
                            <button  class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Register Student</button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        <div class="app-main">
            @include('registrarportal.inc.sidenav')
            
            <div class="app-main__outer">
                <div class="app-main__inner">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
    
<script>
    $(document).ready(function(){
        $("#gradeLevelOption").change(function(){
            $('#sectionName').empty();
            $('#sectionName').append('<option  value="" selected disabled>SELECT SECTION</option>');
            @foreach($sections as $value)
                if('{{$value->levelid}}'==$(this).val()){
                    $('#sectionName').append('<option value="{{$value->id}}">{{$value->sectionname}}</option>');
                }
            @endforeach
        });
        $(".regBut").on('click',function(){
            $('#studid').val($(this).attr('id'))
        })
    });
</script>
</body>
</html>
