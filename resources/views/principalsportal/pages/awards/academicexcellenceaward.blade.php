@php
    $refid = DB::table('usertype')->where('id',auth()->user()->type)->where('deleted',0)->select('refid')->first();
    $teacherid = DB::table('teacher')->where('userid',auth()->user()->id)->select('id')->first()->id;
    if(auth()->user()->type == 2 || Session::get('currentPortal') == 2){
        $acadprogid = DB::table('academicprogram')
                        ->where('principalid',$teacherid)
                        ->select('id as acadprogid')
                        ->get();

        $xtend = 'principalsportal.layouts.app2';
    }
    elseif(auth()->user()->type == 3 || Session::get('currentPortal') == 3){
        $acadprogid = DB::table('academicprogram')
                        ->select('id as acadprogid')
                        ->get();

        $xtend = 'registrar.layouts.app';
    }
    else{
        if( $refid->refid == 20){
            $xtend = 'principalassistant.layouts.app2';
        }elseif( $refid->refid == 22){
            $xtend = 'principalcoor.layouts.app2';
        }

        $syid = DB::table('sy')->where('isactive',1)->select('id')->first()->id;

        $acadprogid = DB::table('teacheracadprog')
                        ->where('teacherid',$teacherid)
                        ->where('syid',$syid)
                        ->select('acadprogid')
                        ->where('deleted',0)
                        ->get();
    }

    $all_acad = array();

    foreach( $acadprogid as $item){
        if($item->acadprogid != 6){
            array_push($all_acad,$item->acadprogid);
        }
    }
@endphp

@extends($xtend)

@section('pagespecificscripts')
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
    <style>
        .select2-container--default .select2-selection--single .select2-selection__rendered {
              margin-top: -9px;
        }
        .shadow {
              box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
              border: 0 !important;
        }
  </style>
@endsection

@section('content')
    @php
        $subj_strand = DB::table('sh_sectionblockassignment')
                    ->join('sh_block',function($join){
                        $join->on('sh_sectionblockassignment.blockid','=','sh_block.id');
                        $join->where('sh_block.deleted',0);
                    })
                    ->join('sh_strand',function($join){
                        $join->on('sh_block.strandid','=','sh_strand.id');
                        $join->where('sh_strand.deleted',0);
                    })
                    ->where('sh_sectionblockassignment.deleted',0)
                    ->select(
                        'syid',
                        'sectionid',
                        'strandid',
                        'strandcode'
                    )->get();
    @endphp
    <div class="modal fade" id="award_setup_form_modal" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
              <div class="modal-content">
                    <div class="modal-header pb-2 pt-2 border-0">
                          <h4 class="modal-title">Award Setup Form</h4>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                          <div class="row">
                                <div class="col-md-12 form-group">
                                      <label for="">Award</label>
                                      <input id="input_award" class="form-control form-control-sm">
                                </div>
                          </div>
                          <div class="row">
                                <div class="col-md-12 form-group">
                                      <label for="">Range (From)</label>
                                      <input id="input_gfrom" class="form-control form-control-sm" oninput="this.value=this.value.replace(/[^0-9\.]/g,'');" >
                                </div>
                          </div>
                          <div class="row">
                                <div class="col-md-12 form-group">
                                      <label for="">Range (To)</label>
                                      <input id="input_gto" class="form-control form-control-sm" oninput="this.value=this.value.replace(/[^0-9\.]/g,'');" >
                                </div>
                          </div>
                          <div class="row">
                                <div class="col-md-12">
                                      <button class="btn btn-sm btn-primary" id="award_setup_form_button">Create</button>
                                </div>
                          </div>
                    </div>
              </div>
        </div>
   </div>   
    <section class="content-header">
        <div class="container-fluid">
              <div class="row mb-2">
                    <div class="col-sm-6">
                          <h1>Student Ranking</h1>
                    </div>
                    <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                          <li class="breadcrumb-item"><a href="/home">Home</a></li>
                          <li class="breadcrumb-item active">Student Ranking</li>
                    </ol>
                    </div>
              </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row ">
                <div class="col-md-12">
                    <div class="card shadow">
                        <div class="card-body" >
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="">Grade Level</label>
                                        @php
                                            $allsections = array();
                                        @endphp
                                        <select class="form-control select2" id="gradelevel">
                                            <option selected value="" >Select Grade Level</option>
                                            @foreach ($all_acad as $item)
                                                @php
                                                    $gradelevel = DB::table('gradelevel')
                                                            ->where('acadprogid',$item)->orderBy('sortid')->where('deleted',0)->select('id','levelname')->get();
                                                @endphp
                                                @foreach ($gradelevel as $levelitem)
                                                    @php
                                                        $newsections = DB::table('sections')->where('levelid',$levelitem->id)->where('deleted',0)->where('levelid',$levelitem->id)->select('sectionname','id','levelid')->get();
                                                        foreach ($newsections as $newsectionitem){
                                                            array_push($allsections, $newsectionitem);
                                                        }
                                                    @endphp
                                                    <option value="{{$levelitem->id}}">{{$levelitem->levelname}}</option>
                                                @endforeach
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Section</label>
                                        <select name="section" id="section" class="form-control select2">
                                            <option selected value="" >Select Section</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2" >
                                    <div class="form-group strand_holder" hidden id="starnd_holder">
                                        <label for="">Strand</label>
                                        <select name="strand" id="strand" class="form-control select2">
                                            
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-1"></div>
                                <div class="col-md-2">
                                    <label for="">SCHOOL YEAR</label>
                                    <select name="syid" id="syid" class="form-control select2">
                                        @foreach(DB::table('sy')->select('id','sydesc','isactive')->orderBy('sydesc')->get() as $item)
                                            @if($item->isactive == 1)
                                                <option value="{{$item->id}}" selected="selected">{{$item->sydesc}}</option>
                                            @else
                                                <option value="{{$item->id}}">{{$item->sydesc}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="">SEMESTER</label>
                                    <select name="semester" id="semester" class="form-control select2">
                                        @foreach(DB::table('semester')->select('id','semester','isactive')->get() as $item)
                                            @if($item->isactive == 1)
                                                <option value="{{$item->id}}" selected="selected">{{$item->semester}}</option>
                                            @else
                                                <option value="{{$item->id}}">{{$item->semester}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <hr class="mt-0">
                            <div class="row">
                                <div class="col-md-2">
                                    <button class="btn btn-primary btn-block btn-sm" id="filter"> <i class="fas fa-filter"></i> FILTER</button>
                                </div>
                                <div class="col-md-2">
                                </div>
                                <div class="col-md-6"></div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <select name="quarter" id="quarter" class="form-control select2">
                                            <option value="">SELECT QUARTER</option>
                                            <option value="1">Quarter 1</option>
                                            <option value="2">Quarter 2</option>
                                            <option value="3">Quarter 3</option>
                                            <option value="4">Quarter 4</option>
                                            <option value="5">FINAL</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            {{-- <h3 class="text-center">STUDENT RANKING LIST</h3> --}}
                            
                            <div class="row mt-3">
                                <div class="col-md-12" style="font-size:.7rem">
                                    <table class="table table-bordered table-sm" id="student_list">
                                        <thead>
                                            <tr>
                                                    <th width="25%">Student Name</th>
                                                    <th width="15%" class="text-center strand_holder" hidden>Strand</th>
                                                    <th width="15%" class="text-center">Gen. Ave (Rounded)</th>
                                                    <th width="15%" class="text-center">Gen. Ave (Decimal)</th>
                                                    <th width="20%" class="text-center">Award</th>
                                                    <th width="10%" class="text-center">Lowest</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="5" class="text-center">PLEASE SELECT FILTER</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <button class="btn btn-default float-right btn-sm" id="print_student_ranking" disabled>
                                        <i class="fas fa-print" ></i> PRINT STUDENT RANKING
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 group-group">
                                    <div class="row">
                                        <div class="col-md-12 form-group">
                                            <label for="">Minimum grade requirement by subject  </label>
                                            <input class="form-control form-control-sm" id="input_lowest_grade">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">Base Grade</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        {{-- <div class="col-md-12 form-group">
                                            <label for="">Minimum grade requirement by subject  </label>
                                            <input class="form-control form-control-sm" id="input_lowest_grade">
                                        </div> --}}
                                        <div class="col-md-6 form-group clearfix">
                                                <div class="icheck-primary d-inline">
                                                    <input type="radio" id="base_rounded" name="base_grade" class="base_grade" value="1">
                                                    <label for="base_rounded">
                                                            Rounded
                                                    </label>
                                                </div>
                                        </div>
                                        <div class="col-md-6 form-group clearfix">
                                                <div class="icheck-primary d-inline">
                                                    <input type="radio" id="base_decimal" name="base_grade" class="base_grade" value="2">
                                                    <label for="base_decimal">
                                                            Decimal
                                                    </label>
                                                </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <button class="btn btn-primary btn-sm" id="update_button_1">Update</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <button class="btn btn-sm btn-primary float-right" id="to_award_setup_form_modal">Add Setup</button>
                                        </div>
                                        
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-12" style="font-size:.7rem">
                                            <table class="table table-bordered table-sm" id="award_setup">
                                                <thead>
                                                    <tr>
                                                            <th width="60%">Award</th>
                                                            <th width="15%" class="text-center">From</th>
                                                            <th width="15%" class="text-center">To</th>
                                                            <th width="5%" class="text-center"></th>
                                                            <th width="5%" class="text-center"></th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('footerjavascript')
    <script src="{{asset('js/pagination.js')}}"></script>
    <script src="{{asset('plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{asset('plugins/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
    @include('principalsportal.pages.awards.awardsjs')

    <script>
        $(document).ready(function(){

            $('#print_student_ranking').click(function(){
                var gradelevel = $('#gradelevel').val();
                var section = $('#section').val();
                var quarter  = $('#quarter').val(); 
                var syid  = $('#syid').val(); 
                var semid  = $('#semester').val(); 

                var valid_filter = true

                if(gradelevel == ''){
                    Swal.fire({
                        type: 'info',
                        text: 'Please select a gradelevel!',
                        timer: 1500
                    });
                    valid_filter = false
                    return false;
                }

                var excluded = []

                $('.subj_list').each(function(a,b){
                    if($(b).prop('checked') == false){
                        excluded.push($(b).attr('data-id'));
                    }
                })

                if(section == null){
                    Swal.fire({
                            type: 'info',
                            title: 'Something went wrong!',
                            text: 'Please reload the page',
                            timer: 1500
                    });
                }
                else{
                    window.open("/grades/report/studentawards?gradelevel="+gradelevel+"&section="+section+"&quarter="+quarter+"&sy="+syid+"&strand="+$("#strand").val()+"&semid="+semid+'&exclude='+excluded);
                }
            })


            //award setup

            const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                  })


            var all_setup = []
            var selected_id = []
            load_award_setup()
            get_list_award_setup()

            $(document).on('click','#update_button_1',function(){
                update_award_setup_lowest()
            })

            $(document).on('change','#syid',function(){
                get_list_award_setup()
            })

            $(document).on('click','#to_award_setup_form_modal',function(){
                selected_id = null
                $('#input_award').val("")
                $('#input_gto').val("")
                $('#input_gfrom').val("")
                $('#award_setup_form_button').text('Create')
                $('#award_setup_form_button').removeClass('btn-success')
                $('#award_setup_form_button').addClass('btn-primary')
                $('#award_setup_form_modal').modal();
            })

            $(document).on('click','.update_award_setup',function(){
                selected_id = $(this).attr('data-id')
                var temp_setup = all_setup.filter(x=>x.id == selected_id)
                $('#input_award').val(temp_setup[0].award)
                $('#input_gto').val(temp_setup[0].gto)
                $('#input_gfrom').val(temp_setup[0].gfrom)
                $('#award_setup_form_button').text('Update')
                $('#award_setup_form_button').removeClass('btn-primary')
                $('#award_setup_form_button').addClass('btn-success')
                $('#award_setup_form_modal').modal();
            })

            $(document).on('click','.delete_award_setup',function(){
                selected_id = $(this).attr('data-id')
                delete_award_setup()
            })

            $(document).on('click','#award_setup_form_button',function(){
                if(selected_id == null){
                    create_award_setup()
                }else{
                    update_award_setup()
                }
            })
            
            function update_award_setup_lowest(){
                $.ajax({
                    type:'GET',
                    url:'/awarsetup/update/lowest',
                    data:{
                        syid:$('#syid').val(),
                        gto:$('#input_lowest_grade').val(),
                        basegrade:$('input[name="base_grade"]:checked').val()
                    },
                    success:function(data) {
                        if(data[0].status == 1){
                            Toast.fire({
                                type: 'success',
                                title: data[0].message
                            })
                        }else{
                            Toast.fire({
                                type: 'error',
                                title: data[0].message
                            })
                        }
                    },error:function(){
                        Toast.fire({
                            type: 'error',
                            title: 'Something went wrong.'
                        })
                    }
                })
            }


            function create_award_setup(){
                $.ajax({
                    type:'GET',
                    url:'/awarsetup/create',
                    data:{
                        syid:$('#syid').val(),
                        award:$('#input_award').val(),
                        gfrom:$('#input_gfrom').val(),
                        gto:$('#input_gto').val(),
                    },
                    success:function(data) {
                        if(data[0].status == 1){
                            Toast.fire({
                                type: 'success',
                                title: data[0].message
                            })
                            get_list_award_setup()
                        }else{
                            Toast.fire({
                                type: 'error',
                                title: data[0].message
                            })
                        }
                    },error:function(){
                        Toast.fire({
                            type: 'error',
                            title: 'Something went wrong.'
                        })
                    }
                })
            }

            function update_award_setup(){
                $.ajax({
                    type:'GET',
                    url:'/awarsetup/update',
                    data:{
                        id:selected_id,
                        syid:$('#syid').val(),
                        award:$('#input_award').val(),
                        gfrom:$('#input_gfrom').val(),
                        gto:$('#input_gto').val(),
                    },
                    success:function(data) {
                        if(data[0].status == 1){
                            Toast.fire({
                                type: 'success',
                                title: data[0].message
                            })
                            get_list_award_setup()
                        }else{
                            Toast.fire({
                                type: 'error',
                                title: data[0].message
                            })
                        }
                    },error:function(){
                        Toast.fire({
                            type: 'error',
                            title: 'Something went wrong.'
                        })
                    }
                })
            }

            function delete_award_setup(){
                $.ajax({
                    type:'GET',
                    url:'/awarsetup/delete',
                    data:{
                        id:selected_id,
                    },
                    success:function(data) {
                        if(data[0].status == 1){
                            Toast.fire({
                                type: 'success',
                                title: data[0].message
                            })
                            all_setup = all_setup.filter(x=>x.id != selected_id)
                            load_award_setup()
                        }else{
                            Toast.fire({
                                type: 'error',
                                title: data[0].message
                            })
                        }
                    },error:function(){
                        Toast.fire({
                            type: 'error',
                            title: 'Something went wrong.'
                        })
                    }
                })
            }

            function get_list_award_setup(){

                $.ajax({
                    type:'GET',
                    url:'/awarsetup/list',
                    data:{
                        syid:$('#syid').val(),
                    },
                    success:function(data) {
                        all_setup = data.filter(x=>x.award != 'lowest grade')
                        all_setup = all_setup.filter(x=>x.award != 'base grade')
                        load_award_setup()

                        var lowest = data.filter(x=>x.award == 'lowest grade')
                        if(lowest.length > 0){
                            $('#input_lowest_grade').val(lowest[0].gto)
                        }

                        var base_setup = data.filter(x=>x.award == 'base grade')
                        if(base_setup.length > 0){
                            if(base_setup[0].gto == 1){
                                $('#base_decimal').prop('checked',true)
                                $('#base_rounded').prop('checked',false)
                            }else{
                                $('#base_decimal').prop('checked',false)
                                $('#base_rounded').prop('checked',true)
                            }
                        }else{
                            $('#base_decimal').prop('checked',false)
                            $('#base_rounded').prop('checked',true)
                        }
                    }
                })

            }

            function load_award_setup(){

                $("#award_setup").DataTable({
                    destroy: true,
                    bInfo: false,
                    bLengthChange: false,
                    bPaginate: false,
                    data:all_setup,
                    order: [[ 1 , "asc"]],
                    "columns": [
                                { "data": "award" },
                                { "data": "gfrom" },
                                { "data": "gto" },
                                { "data": null },
                                { "data": null }
                            
                        ],
                    columnDefs: [
                            {
                                    'targets': 1,
                                    'orderable': true, 
                                    'createdCell':  function (td, cellData, rowData, row, col) {
                                        $(td).addClass('text-center')
                                    }
                            },
                            {
                                    'targets': 2,
                                    'orderable': true, 
                                    'createdCell':  function (td, cellData, rowData, row, col) {
                                        $(td).addClass('text-center')
                                    }
                            },
                            {
                                'targets': 3,
                                'orderable': false, 
                                'createdCell':  function (td, cellData, rowData, row, col) {
                                        var buttons = '<a href="javascript:void(0)" class="update_award_setup" data-id="'+rowData.id+'"><i class="far fa-edit"></i></a>';
                                        $(td)[0].innerHTML =  buttons
                                        $(td).addClass('text-center')
                                        $(td).addClass('align-middle')
                                        
                                }
                            },
                            {
                                'targets': 4,
                                'orderable': false, 
                                'createdCell':  function (td, cellData, rowData, row, col) {
                                        var disabled = '';
                                        var buttons = '<a href="javascript:void(0)" '+disabled+' class="delete_award_setup" data-id="'+rowData.id+'"><i class="far fa-trash-alt text-danger"></i></a>';
                                        $(td)[0].innerHTML =  buttons
                                        $(td).addClass('text-center')
                                        $(td).addClass('align-middle')
                                }
                            },
                    ]
                });


            }


        })
    </script>

@endsection


 