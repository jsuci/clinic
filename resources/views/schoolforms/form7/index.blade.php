
@extends($extends)

@section('headerjavascript')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
@endsection
@section('content')
<style>
    
    .select2-container--default .select2-selection--single .select2-selection__choice {
        background-color: #007bff;
        border-color: #006fe6;
        color: #fff;
        padding: 0 10px;
        margin-top: .31rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__choice__remove {
        color: #fff;
    }

</style>
<!-- DataTables -->

    <section class="content">
        <div class="container-fluid">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">School Form 7</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="/home">Home</a></li>
                                <li class="breadcrumb-item active">School Form 7</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div>
            </section>
        </div>
    </section>
    <div class="card shadow" style="box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; border: none;">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <label>School Year</label>
                    <select class="form-control select2" id="select-syid">
                        @foreach(DB::table('sy')->get() as $eachsy)
                            <option value="{{$eachsy->id}}" @if($eachsy->isactive == 1) selected @endif>{{$eachsy->sydesc}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Month</label>
                    <select class="form-control select2" id="select-month">
                        @for ($m=1; $m<=12; $m++) 
                            @php
                            $month = date('F', mktime(0,0,0,$m, 1, date('Y')));
                            @endphp
                            <option value="{{$month}}">{{$month}}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4 align-self-end text-right">
                    <button type="button" class="btn btn-primary" id="btn-show-results"><i class="fa fa-sync"></i> Show results</button>
                </div>
            </div>
        </div>
    </div>  
    <div id="container-results"></div>  
@endsection
@section('footerjavascript')     
    <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-buttons/js/buttons.html5.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-buttons/js/buttons.print.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-buttons/js/buttons.colVis.min.js')}}"></script>
    <script>
        $(document).ready(function(){
            $('.select2').select2({
                theme: 'bootstrap4'
            })
            $('#btn-show-results').on('click', function(){
                var syid = $('#select-syid').val()
                var month   = $('#select-month').val()
                Swal.fire({
                    title: 'Fetching data...',
                    onBeforeOpen: () => {
                        Swal.showLoading()
                    },
                    allowOutsideClick: false
                })
                $.ajax({
                    url: '/schoolform/sf7',
                    type:'GET',
                    data: {
                        action      :  'filter',
                        syid     :  syid,
                        month       :  month
                    },
                    success:function(data) {
                        $('#container-results').empty()
                        $('#container-results').append(data)
                        $(".swal2-container").remove();
                        $('body').removeClass('swal2-shown')
                        $('body').removeClass('swal2-height-auto')                
                    }
                })
            })
            $(document).on('click','#btn-exporttopdf', function(){  
                var syid = $('#select-syid').val()
                var month   = $('#select-month').val()              
                window.open("/schoolform/sf7?action=export&month="+month+"&syid="+syid,'_blank');
            })
        })
    </script>
@endsection

                                        

                                        
                                        