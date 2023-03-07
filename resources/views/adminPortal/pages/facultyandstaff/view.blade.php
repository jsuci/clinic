@php
      if(auth()->user()->type == 17){
            $extend = 'superadmin.layouts.app2';
      }else if(auth()->user()->type == 3 || Session::get('currentPortal') == 3){
            $extend = 'registrar.layouts.app';
      }else if(auth()->user()->type == 6 || Session::get('currentPortal') == 6){
            $extend = 'adminPortal.layouts.app2';
      }
@endphp

@extends($extend)

@section('pagespecificscripts')
    <link rel="stylesheet" href="{{asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
    <style>
        .btn-outline-dafault {
            color: white;
            border-color: white;
        }

        
    </style>
    <style>
        .select2-selection--single{
            height: calc(2.25rem + 2px) !important;
        }
        .shadow {
              box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
              border: 0;
        }
    </style>
@endsection

@php
    $schoolinfo = DB::table('schoolinfo')->first();
    $sy = DB::table('sy')->get();
@endphp

@section('modalSection')

    <div class="modal fade" id="add-faculty" style="display: none; padding-right: 17px;" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
                <div class="modal-header pb-2 pt-2 border-0">
                    <h4 class="modal-title">Faculty & Staff Form</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                    <label for="exampleInputEmail1">Title</label>
                    <input placeholder="Title" class="form-control" id="title"  name="title" onkeyup="this.value = this.value.toUpperCase();">
                    </div>
                    <div class="form-group">
                    <label for="exampleInputEmail1">First Name</label>
                    <input placeholder="First name" class="form-control" id="fn"  name="fn" onkeyup="this.value = this.value.toUpperCase();">
                    <span class="invalid-feedback" role="alert">
                        <strong>First name is required.</strong>
                    </span>
                    </div>
                    <div class="form-group">
                    <label for="exampleInputEmail1">Middle Name</label>
                    <input placeholder="Middle name" class="form-control" id="mn"  name="mn" onkeyup="this.value = this.value.toUpperCase();">
                        <span class="invalid-feedback" role="alert">
                            <strong>Middle Name is required.</strong>
                        </span>
                    </div>
                    <div class="form-group">
                    <label for="exampleInputEmail1">Last Name</label>
                    <input placeholder="Last name" class="form-control" id="ln"  name="ln" onkeyup="this.value = this.value.toUpperCase();">
                    <span class="invalid-feedback" role="alert">
                        <strong>Last Name is required.</strong>
                    </span>
                    </div>
                    <div class="form-group">
                    <label for="exampleInputEmail1">Suffix</label>
                    <input placeholder="Suffix" class="form-control" id="suffix"  name="suffix" onkeyup="this.value = this.value.toUpperCase();">
                    </div>
                    <div class="form-group">
                    <label for="exampleInputEmail1">User Type</label>
                        <select class="form-control teacher select2" id="ut" name="ut">
                            <option value="" selected>Select User Type</option>
                            @foreach($usertype as $item)
                                <option value="{{$item->id}}">{{$item->utype}}</option>
                            @endforeach
                        </select>
                        <span class="invalid-feedback" role="alert">
                            <strong>User type is required.</strong>
                        </span>
                    </div>
                </div>
                <div class="modal-footer justify-content-between mf" >
                    <button class="btn btn-primary us">Save</button>
                </div>
            </div>
        </div>
  </div>
 

  
@php
    $academic_prog = DB::table('academicprogram')->select('id','acadprogcode','acadprogcode as text')->get();
    $courses = DB::table('college_courses')->where('deleted',0)->get();
    $colleges = DB::table('college_colleges')->where('deleted',0)->get();
    $usertype = DB::table('usertype')
                    ->where('deleted',0)
                    ->whereNotIn('id',[7,9,17,12,6])
                    ->get();
    
@endphp


@endsection


@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h1>Account Information</h1>
            </div>
            <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="/home">Home</a></li>
                <li class="breadcrumb-item"><a href="/manageaccounts">Accounts</a></li>
            </ol>
            </div>
        </div>
    </div>
</section>
<section class="content">
    <div class="container-fluid">


        
        <div class="row">
            <div class="col-md-9">

                <div class="row" id="privilege_card">
                    <div class="col-md-12">
                        <div class="card shadow">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5>Portals</h5>
                                    </div>
                                </div>
                                <div class="row mt-2" id="academic_program_holder" >
                                    @foreach ($usertype as $item)
                                        @if($item->id != 2)
                                            <div class="col-md-6" id="">
                                                <div class="icheck-success d-inline">
                                                    <input  type="checkbox" id="fas_priv{{$item->id}}" class="fas_priv" data-id="{{$item->id}}"> 
                                                    <label for="fas_priv{{$item->id}}"> {{$item->utype}}</label>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" id="teacher_card_1" hidden>
                    <div class="col-md-12">
                        <div class="card shadow">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-10">
                                        <h5>Academic Program</h5>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="teacher_acad_sy" id="teacher_acad_sy" class="form-control form-control-sm">
                                            @foreach ($sy as $item)
                                                @if($item->isactive == 1)
                                                        <option value="{{$item->id}}" selected="selected">{{$item->sydesc}}</option>
                                                @else
                                                        <option value="{{$item->id}}">{{$item->sydesc}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row mt-2" id="academic_program_holder" >
                                    @foreach ($academic_prog as $item)
                                        <div class="col-md-6" id="">
                                            <div class="icheck-success d-inline">
                                                <input  type="checkbox" id="t{{$item->id}}" class="teacher_acad" data-id="{{$item->id}}"> 
                                                    <label for="t{{$item->id}}"> {{$item->text}}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row" id="principal_card_1" hidden>
                    <div class="col-md-12">
                        <div class="card shadow">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5>Principal (Academic Program)</h5>
                                    </div>
                                </div>
                                <div class="row mt-2" id="academic_program_holder" >
                                    @foreach (collect($academic_prog)->where('id','!=',6) as $item)
                                        <div class="col-md-6" id="">
                                            <div class="icheck-success d-inline">
                                                <input  type="checkbox" id="principal{{$item->id}}" class="pricipal_acad" data-id="{{$item->id}}"> 
                                                    <label for="principal{{$item->id}}"> {{$item->text}}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row" id="college_card" hidden>
                    <div class="col-md-12">
                        <div class="card shadow">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5>Colleges</h5>
                                    </div>
                                </div>
                                <div class="row mt-2" id="academic_program_holder" >
                                    @foreach ($colleges as $item)
                                        <div class="col-md-6" id="">
                                            <div class="icheck-success d-inline">
                                                <input  type="checkbox" id="college{{$item->id}}" class="college" data-id="{{$item->id}}"> 
                                                    <label for="college{{$item->id}}"> {{$item->collegeDesc}}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row" id="course_card" hidden>
                    <div class="col-md-12">
                        <div class="card shadow">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5>Courses</h5>
                                    </div>
                                </div>
                                <div class="row mt-2" id="academic_program_holder" >
                                    <div class="col-md-12">
                                        <table class="table-hover table table-striped table-sm table-bordered " id="course_table" width="100%">
                                            <thead>
                                                  <tr>
                                                        <th width="3%"></th>
                                                        <th width="97%">Course</th>
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
            <div class="col-md-3">
                @if($facultyInfo[0]->isactive == 1)
                    <div class="ribbon-wrapper ribbon-lg mr-2" hidden id="isRibbon">
                @else
                    <div class="ribbon-wrapper ribbon-lg mr-2" id="isRibbon">
                @endif
                    <div class="ribbon bg-danger">                           
                        INACTIVE            
                    </div>
                </div>
                <div class="card card-primary shadow">
                    <div class="card-header bg-success">
                      <h3 class="card-title">About Me</h3>
                    </div>
                    <div class="card-body">
                        <strong><i class="fas fa-user mr-1"></i>Name</strong>
                        <p class="text-muted" id="name">--</p>
                        <hr>
                        <strong><i class="fas fa-user-cog mr-1"></i>User Type</strong>
                        <p class="text-muted" id="usertype">--</p>
                        <hr>
                        <strong><i class="fas fa-id-badge mr-1"></i>License #</strong>
                        <p class="text-muted" id="license">--</p>
                        <hr>
                        <strong><i class="fas fa-id-badge mr-1"></i>Teacher ID</strong>
                        <p class="text-muted" id="tid">--</p>
                        @if( ( $schoolinfo->projectsetup == 'online' &&  $schoolinfo->processsetup == 'all' ) || $schoolinfo->projectsetup == 'offline' )
                             <span><button type="button" class="btn btn-sm btn-outline-primary ee btn-block" id="{{$facultyInfo[0]->id}}" ><i class="far fa-edit mr-1" ></i>Edit Information</button></span>
                            {{-- @if($facultyInfo[0]->isactive == 1) --}}
                                <span><button type="button" class="btn btn-sm btn-outline-danger status btn-block mt-2" id="{{$facultyInfo[0]->id}}" data-status="0" hidden><i class="fa fa-ban mr-1" ></i>Deactivate Account</button></span>
                            {{-- @else --}}
                                <span><button type="button" class="btn btn-sm btn-outline-success status btn-block mt-2" id="{{$facultyInfo[0]->id}}" data-status="1" hidden><i class="fa fa-ban mr-1" ></i>Activate Account</button></span>
                            {{-- @endif --}}

                           
                        @endif
                        </div>
                  </div>
            </div>
        </div>
    </div>
</section>


@endsection


@section('footerjavascript')

    <script src="{{asset('plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{asset('plugins/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{asset('plugins/moment/moment.min.js') }}"></script>
    <script src="{{asset('plugins/sweetalert2/sweetalert2.all.min.js')}}"></script>
    
    <script>
        var school_setup = @json($schoolinfo);
        var status = @json($facultyInfo[0]->isactive)

        if(status == 0){
            $('.status[data-status="1"]').removeAttr('hidden')
        }else{
            $('.status[data-status="0"]').removeAttr('hidden')
        }

        function get_last_index(tablename){
            $.ajax({
                    type:'GET',
                    url: '/monitoring/tablecount',
                    data:{
                        tablename: tablename
                    },
                    success:function(data) {
                        lastindex = data[0].lastindex
                        update_local_table_display(tablename,lastindex)
                    },
            })
        }

        function update_local_table_display(tablename,lastindex){
            $.ajax({
                    type:'GET',
                    url: school_setup.es_cloudurl+'/monitoring/table/data',
                    data:{
                        tablename:tablename,
                        tableindex:lastindex
                    },
                    success:function(data) {
                        if(data.length > 0){
                                process_create(tablename,0,data)
                        }
                    },
                    error:function(){
                        $('td[data-tablename="'+tablename+'"]')[0].innerHTML = 'Error!'
                    }
            })
        }

        function process_create(tablename,process_count,createdata){
            if(createdata.length == 0){
                return false;
            }
            var b = createdata[0]
            $.ajax({
                    type:'GET',
                    url: '/synchornization/insert',
                    data:{
                        tablename: tablename,
                        data:b
                    },
                    success:function(data) {
                        process_count += 1
                        createdata = createdata.filter(x=>x.id != b.id)
                        process_create(tablename,process_count,createdata)
                    },
                    error:function(){
                        process_count += 1
                        createdata = createdata.filter(x=>x.id != b.id)
                        process_create(tablename,process_count,createdata)
                    }
            })
        }

        //get_updated
        function get_updated(tablename){
            var date = moment().subtract(1, 'minute').format('YYYY-MM-DD HH:mm:ss');
            $.ajax({
                type:'GET',
                url: school_setup.es_cloudurl+'/monitoring/table/data/updated',
                data:{
                        tablename: tablename,
                        date: date
                },
                success:function(data) {
                    process_update(tablename,data)
                }
            })
        }


        function process_update(tablename , updated_data){
            if (updated_data.length == 0){
                // loadTeacherInfo(teacherinfo[0].id)
                return false
            }

            var b = updated_data[0]

            $.ajax({
                type:'GET',
                url: '/synchornization/update',
                data:{
                    tablename: tablename,
                    data:b
                },
                success:function(data) {
                    updated_data = updated_data.filter(x=>x.id != b.id)
                    process_update(tablename,updated_data)
                },
            })
        }

        //get_updated
        function get_deleted(tablename){
            var date = moment().subtract(1, 'minute').format('YYYY-MM-DD HH:mm:ss');
            $.ajax({
                type:'GET',
                url: school_setup.es_cloudurl+'/monitoring/table/data/deleted',
                data:{
                        tablename: tablename,
                        date: date
                },
                success:function(data) {
                    process_deleted(tablename,data)
                }
            })
        }

        function process_deleted(tablename , deleted_data){
            if (deleted_data.length == 0){
                // loadTeacherInfo(teacherinfo[0].id)
                return false
            }

            var b = deleted_data[0]

            $.ajax({
                type:'GET',
                url: '/synchornization/delete',
                data:{
                    tablename: tablename,
                    data:b
                },
                success:function(data) {
                    deleted_data = deleted_data.filter(x=>x.id != b.id)
                    process_deleted(tablename,deleted_data)
                },
            })
        }


    </script>



    <script>

         
        $(document).ready(function(){

          


            $(document).on('click','.ee',function(){
                $('#add-faculty').modal()
            })

            $(document).on('click','.us',function(){
                update_info()
            })

            $(document).on('change','#ut',function(){
                var acad = []
                $.each(loaded_data[0].acad,function(a,b){
                    acad.push(b.id)
                })
                $('#acadprog').val(acad).change()
                if($(this).val() == 1 || $(this).val() == 2 || $(this).val() == 3){
                    $('#input_acad_holder').removeAttr('hidden')
                }else{
                    $('#input_acad_holder').attr('hidden','hidden')
                }
            })
            

            function update_info(){

                var valid_input = true
                if($('#fn').val() == ""){
                    Toast.fire({
                        type: 'warning',
                        title: 'First name is required!'
                    })
                    valid_input = false
                }
                if($('#ln').val() == ""){
                    Toast.fire({
                        type: 'warning',
                        title: 'Last Name is required!'
                    })
                    valid_input = false
                }
                if($('#ut').val() == ""){
                    Toast.fire({
                        type: 'warning',
                        title: 'User type is required!'
                    })
                    valid_input = false
                }

                // if( $('#ut').val() == 1 || $('#ut').val() == 2 || $('#ut').val() == 3){
                //     if($('#acadprog').val() == ""){
                //         Toast.fire({
                //             type: 'warning',
                //             title: 'Academic Program is required!'
                //         })
                //         valid_input = false
                //     }
                // }else{
                //     $('#acadprog').val([]).change()
                // }
                
                var url = '/administrator/setup/accounts/update/information'

                if(school_setup.setup == 1 && ( school_setup.processsetup == 'hybrid1' || school_setup.processsetup == 'hybrid2' )){
                    url = school_setup.es_cloudurl+'administrator/setup/accounts/update/information'
                }
                
                if(valid_input){

                    $.ajax({
                        type:'GET',
                        url:url,
                        data:{
                            teacher:loaded_data[0].id,
                            title:$("#title").val(),
                            fname:$('#fn').val(),
                            lname:$('#ln').val(),
                            mname:$('#mn').val(),
                            suffix:$('#suffix').val(),
                            lcn:$('#lcn').val(),
                            utype:$('#ut').val(),
                            acad:$('#acadprog').val(),
                            updateuserid:updateuserid
                        },
                        success:function(data) {
                            if(data[0].status == 1){
                                Toast.fire({
                                    type: 'success',
                                    title: data[0].data
                                })
                               
                                if(school_setup.setup == 1 && ( school_setup.processsetup == 'hybrid1' || school_setup.processsetup == 'hybrid2' )){

                                    get_deleted('teacheracadprog')
                                    get_deleted('faspriv')
                                    get_updated('teacher')
                                    get_updated('users')
                                    get_last_index('teacheracadprog')

                                    loaded_data[0].lastname = $('#ln').val()
                                    loaded_data[0].firstname = $('#fn').val()
                                    loaded_data[0].middlename = $('#mn').val()
                                    loaded_data[0].suffix = $('#suffix').val()
                                    loaded_data[0].title = $('#title').val()
                                    loaded_data[0].usertypeid = $('#ut').val()
                                    loaded_data[0].utype = $('#ut option:selected').text()

                                    var temp_middle = '';
                                    if(loaded_data[0].middlename.length > 0){
                                        temp_middle = loaded_data[0].middlename[0]+'.'
                                    }

                                    loaded_data[0].fullname = loaded_data[0].title+' '+loaded_data[0].firstname+' '+temp_middle+' '+loaded_data[0].lastname+' '+loaded_data[0].suffix;
                                
                                    loaded_data[0].faspriv =  loaded_data[0].faspriv.filter(x=>x.usertype != loaded_data[0].usertypeid)

                                    if(loaded_data[0].faspriv.filter(x=>x.usertype == 1 || x.usertype == 2 || x.usertype == 3).length == 0){
                                        loaded_data[0].acad = loaded_data[0].acad.filter(x=>x.id == 6);
                                    }
                                    if(loaded_data[0].faspriv.filter(x=>x.usertype == 18).length == 0){
                                        loaded_data[0].acad = loaded_data[0].acad.filter(x=>x.id != 6);
                                    }

                                    load_info(loaded_data)

                                }else{
                                    loadTeacherInfo(loaded_data[0].id)
                                }
                              
                            }else{
                                Toast.fire({
                                    type: 'error',
                                    title: data[0].data
                                })
                            }
                        },
                        error:function(){
                            Toast.fire({
                                type: 'error',
                                title: 'Something went wrong!'
                            })
                        }
                    })

                    $.ajax({
                        type:'GET',
                        url:'/updateaccount/name',
                        data:{
                            utype:$('#ut').val(),
                            fname:$('#fn').val(),
                            lname:$('#ln').val(),
                            updateuserid:updateuserid
                        },
                        success:function(data) {

                        }
                    })
                }
                
            }
           
            var updateuserid = @json(auth()->user()->id);


            $(document).on('click','.status',function(){

                var url = '/administrator/setup/accounts/update/active'

                if(school_setup.setup == 1 && ( school_setup.processsetup == 'hybrid1' || school_setup.processsetup == 'hybrid2' )){
                    url = school_setup.es_cloudurl+'administrator/setup/accounts/update/active'
                }

                var status = $(this).attr('data-status')

                var teacherid = $(this).attr('id')

                $.ajax({
                    type:'GET',
                    url:url,
                    data:{
                        teacher:teacherid,
                        status:status
                    },
                    success:function(data) {
                        if(data[0].status == 1){
                            Toast.fire({
                                type: 'success',
                                title: data[0].data
                            })

                            if(status == 0){
                                $('#isRibbon').removeAttr('hidden')
                                $('.status[data-status="0"]').attr('hidden','hidden')
                                $('.status[data-status="1"]').removeAttr('hidden')
                            }else{
                                $('#isRibbon').attr('hidden','hidden')
                                $('.status[data-status="1"]').attr('hidden','hidden')
                                $('.status[data-status="0"]').removeAttr('hidden')
                               
                            }

                          
                        }else{
                            Toast.fire({
                                type: 'error',
                                title: 'Something went wrong!'
                            })
                        }
                    }
                })
            })

            $(document).on('click','.fas_priv',function(){

                if( school_setup.projectsetup == 'online' &&  school_setup.processsetup != 'all' ){
                    Toast.fire({
                        type: 'info',
                        title: 'Access Denied!'
                    })
                    return false
                }

                var status = 1
                usertype = $(this).attr('data-id')

                if($(this).prop('checked') == false){
                    status = 0
                }

                var url = '/administrator/setup/accounts/update/privilege'

                if(school_setup.setup == 1 && ( school_setup.processsetup == 'hybrid1' || school_setup.processsetup == 'hybrid2' )){
                    url = school_setup.es_cloudurl+'administrator/setup/accounts/update/privilege'
                }

                $.ajax({
                    type:'GET',
                    url:url,
                    data:{
                        userid:userid,
                        usertype:usertype,
                        status:status,
                        updateuserid:updateuserid
                    },
                    success:function(data) {
                        if(data[0].status == 1){
                            Toast.fire({
                                type: 'success',
                                title: 'Privilege Updated!'
                            })
                           
                            if(status == 0){
                                loaded_data[0].faspriv = (loaded_data[0].faspriv).filter(x=>x.usertype != usertype)
                                if(school_setup.setup == 1 && ( school_setup.processsetup == 'hybrid1' || school_setup.processsetup == 'hybrid2' )){
                                    get_deleted('faspriv')
                                    get_deleted('teacheracadprog')
                                }
                                if(usertype == 14){
                                    var college = @json($colleges);
                                    $.each(college,function(a,b){
                                        if(b.dean == teacherinfo[0].id){
                                            $('.college[data-id="'+b.id+'"]').prop('checked',false)
                                            update_college_assignment(b.id,0,false)
                                            loaded_data[0].colleges = []
                                        }
                                    })
                                }else if(usertype == 16){
                                    var course = @json($courses);
                                    $.each(course,function(a,b){
                                        if(b.courseChairman == teacherinfo[0].id){
                                            $('.course[data-id="'+b.id+'"]').prop('checked',false)
                                            update_course_assignment(b.id,0,false)
                                            loaded_data[0].courses = []
                                        }
                                    })
                                }
                                console.log(loaded_data)
                                load_info(loaded_data)
                            }else{
                                loaded_data[0].faspriv.push({
                                    userid:userid,
                                    usertype:usertype
                                })
                                if(school_setup.setup == 1 && ( school_setup.processsetup == 'hybrid1' || school_setup.processsetup == 'hybrid2' )){
                                    get_last_index('faspriv')
                                    get_updated('teacheracadprog')
                                }
                                load_info(loaded_data)
                            }
                        }else{
                            Toast.fire({
                                type: 'error',
                                title: 'Something went wrong!'
                            })
                        }
                    }
                })
            })

            $(document).on('click','.teacher_acad',function(){


                if( school_setup.projectsetup == 'online' &&  school_setup.processsetup != 'all' ){
                    Toast.fire({
                        type: 'info',
                        title: 'Access Denied!'
                    })
                    return false
                }
                
                var status = 1
                acadprog = $(this).attr('data-id')
                if($(this).prop('checked') == false){
                    status = 0
                }

                var url = '/administrator/setup/accounts/update/fasacadprog'

                if(school_setup.setup == 1 && ( school_setup.processsetup == 'hybrid1' || school_setup.processsetup == 'hybrid2' )){
                    url = school_setup.es_cloudurl+'administrator/setup/accounts/update/fasacadprog'
                }

                $.ajax({
                    type:'GET',
                    url:url,
                    data:{
                        syid:$('#teacher_acad_sy').val(),
                        teacher:loaded_data[0].id,
                        acadprog:acadprog,
                        status:status
                    },
                    success:function(data) {
                        if(data[0].status == 1){
                            if(school_setup.setup == 1 && ( school_setup.processsetup == 'hybrid1' || school_setup.processsetup == 'hybrid2' )){
                                if(status == 0){
                                    get_deleted('teacheracadprog')
                                }else{
                                    get_last_index('teacheracadprog')
                                }
                            }
                            Toast.fire({
                                type: 'success',
                                title: 'Updated Successfully!'
                            })
                        }else if(data[0].status == 2){
                            Toast.fire({
                                type: 'warning',
                                title: data[0].data
                            })
                        }
                        else{
                            Toast.fire({
                                type: 'error',
                                title: 'Something went wrong!'
                            })
                        }
                    }
                })
            })

            $(document).on('click','.pricipal_acad',function(){

                if( school_setup.projectsetup == 'online' &&  school_setup.processsetup != 'all' ){
                    Toast.fire({
                        type: 'info',
                        title: 'Access Denied!'
                    })
                    return false
                }
                
                var status = 1
                acadprog = $(this).attr('data-id')
                if($(this).prop('checked') == false){
                    status = 0
                }
                $.ajax({
                    type:'GET',
                    url:'/administrator/setup/accounts/update/principal',
                    data:{
                        teacher:loaded_data[0].id,
                        acadprog:acadprog,
                        status:status
                    },
                    success:function(data) {
                        if(data[0].status == 1){
                            Toast.fire({
                                type: 'success',
                                title: 'Updated Successfully!'
                            })
                        }else{
                            Toast.fire({
                                type: 'error',
                                title: 'Something went wrong!'
                            })
                        }
                    }
                })
            })

            $(document).on('click','.college',function(){

                if( school_setup.projectsetup == 'online' &&  school_setup.processsetup != 'all' ){
                    Toast.fire({
                        type: 'info',
                        title: 'Access Denied!'
                    })
                    return false
                }
                

                var status = 1
                collegeid = $(this).attr('data-id')
                if($(this).prop('checked') == false){
                    status = 0
                }
                update_college_assignment(collegeid,status)
            })


            function update_college_assignment(college,status,prompt=true){
                $.ajax({
                    type:'GET',
                    url:'/administrator/setup/accounts/update/dean',
                    data:{
                        teacher:loaded_data[0].id,
                        collegeid:college,
                        status:status
                    },
                    success:function(data) {
                        if(prompt){
                            if(data[0].status == 1){
                                Toast.fire({
                                    type: 'success',
                                    title: 'Updated Successfully!'
                                })
                            }else{
                                Toast.fire({
                                    type: 'error',
                                    title: 'Something went wrong!'
                                })
                            }
                        }
                    }
                })
            }

            $(document).on('click','.course',function(){
                if( school_setup.projectsetup == 'online' &&  school_setup.processsetup != 'all' ){
                    Toast.fire({
                        type: 'info',
                        title: 'Access Denied!'
                    })
                    return false
                }
                
                var status = 1
                courseid = $(this).attr('data-id')
                if($(this).prop('checked') == false){
                    status = 0
                }
                update_course_assignment(courseid,status)
            })

            function update_course_assignment(course,status,prompt=true){
                $.ajax({
                    type:'GET',
                    url:'/administrator/setup/accounts/update/chairperson',
                    data:{
                        teacher:loaded_data[0].id,
                        courseid:course,
                        status:status
                    },
                    success:function(data) {
                        if(prompt){
                            if(data[0].status == 1){
                                Toast.fire({
                                    type: 'success',
                                    title: 'Updated Successfully!'
                                })
                                
                            }else{
                                Toast.fire({
                                    type: 'error',
                                    title: 'Something went wrong!'
                                })
                            }
                        }
                    }
                })
            }

            $('.select2').select2()

            var acad = @json($academic_prog);

            $("#acadprog").select2({
                data: acad,
                placeholder: "Select a academic program",
                theme: 'bootstrap4'
            })

            loadTeacherInfo(teacherinfo[0].id)
          

        })
    </script>

    <script>

        const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                  })


            var teacherinfo = @json($facultyInfo);
            var courses = @json($courses);
            var userid = null
            var loaded_data

        function loadTeacherInfo(id){

            $.ajax({
                type:'GET',
                url:'/administrator/setup/accounts/list',
                data:{
                teacherid:id
                },
                success:function(data) {
                    loaded_data = data
                    userid = data[0].userid
                    load_info(data)
                }
            })
        }

            function load_info(data){

                $.each(courses,function(a,b){
                    b.status = 0;
                })

                // $('.teacher_acad').prop('checked',false)

                $('.fas_priv').removeAttr('disabled','disabled')
                $('.fas_priv').prop('checked',false)

                // $('#teacher_card').attr('hidden','hidden')
                $('#principal_card').attr('hidden','hidden')
                $('#college_card').attr('hidden','hidden')
                $('#course_card').attr('hidden','hidden')

                $("#title").val(data[0].title)
                $('#fn').val(data[0].firstname)
                $('#ln').val(data[0].lastname)
                $('#mn').val(data[0].middlename)
                $('#suffix').val(data[0].suffix)
                $('#lcn').val(data[0].licno)
                $('#ut').val(data[0].usertypeid).change()


                $("#name").text(data[0].fullname)
                $('#usertype').text(data[0].utype)
                $('#license').text(data[0].licno)
                $('#tid').text(data[0].tid)



                var acad = []

                if(data[0].usertypeid == 1 || data[0].usertypeid == 2 ||  data[0].usertypeid == 3){
                    $('#input_acad_holder').removeAttr('hidden')
                }else{
                    $('#input_acad_holder').attr('hidden','hidden')
                }


                // if(data[0].usertypeid == 1 || data[0].usertypeid == 3 || data[0].usertypeid == 18 || data[0].refid == 22){
                //     $('#teacher_card').removeAttr('hidden')
                // }
                if(data[0].usertypeid == 2){
                    $('#principal_card').removeAttr('hidden')
                    $('#acad_progholder').removeAttr('hidden')

                }
                if(data[0].usertypeid == 14){
                    // $('#college_card').removeAttr('hidden')
                }
                if(data[0].usertypeid == 16){
                    // $('#course_card').removeAttr('hidden')
                }


                $('.fas_priv[data-id="'+data[0].usertypeid+'"]').attr('disabled','disabled')
                $('.fas_priv[data-id="'+data[0].usertypeid+'"]').prop('checked',true)
                    
                $.each(data[0].faspriv,function(a,b){
                    if(b.usertype == 1 || b.usertype == 3 || b.usertype == 4 || data[0].refid == 22){
                        $('#teacher_card').removeAttr('hidden')
                    }
                    if(b.usertype == 2){
                        $('#principal_card').removeAttr('hidden')
                    }
                    if(b.usertype == 14){
                        // $('#college_card').removeAttr('hidden')
                    }
                    if(b.usertype == 16){
                        // $('#course_card').removeAttr('hidden')
                    }
                    
                    $('.fas_priv[data-id="'+b.usertype+'"]').prop('checked',true)
                
                })

              
                $.each(data[0].prinacad,function(a,b){
                    $('.pricipal_acad[data-id="'+b.id+'"]').prop('checked',true)
                    if(data[0].usertypeid == 2){
                        acad.push(b.id)
                    }
                })

                $.each(data[0].courses,function(a,b){
                    var get_index = courses.findIndex(x=>x.id == b.id)
                    courses[get_index].status = 1
                })

                $.each(data[0].colleges,function(a,b){
                    $('.college[data-id="'+b.id+'"]').prop('checked',true)
                })

                $('#acadprog').val(acad).change()
                load_courses()

            }

            function load_courses(){

            $("#course_table").DataTable({
            destroy: true,
            data:courses,
            lengthChange: false,
            order: [
                    [ 1, "desc" ]
            ],
            columns: [
                    { "data": "status" },
                    { "data": "courseDesc"}
                    
            ],
            columnDefs: [
                    {
                        'targets': 0,
                        'orderable': true, 
                        'createdCell':  function (td, cellData, rowData, row, col) {
                            var checked = ''
                            if(rowData.status == 1){
                                checked = 'checked="checked"'
                            }

                            var text = '<div class="icheck-success d-inline">'+
                                            '<input '+checked+' type="checkbox" id="course'+rowData.id+'"  class="course" data-id="'+rowData.id+'">'+
                                            '<label for="course'+rowData.id+'"></label>'
                                        '</div>'

                            $(td).addClass('text-center')
                            $(td).addClass('align-middle')
                            $(td).addClass('pl-2')
                            $(td)[0].innerHTML = text

                        }
                    },
                    {
                        'targets': 1,
                        'orderable': true, 
                        'createdCell':  function (td, cellData, rowData, row, col) {
                            var text = rowData.courseDesc+'<p class="text-muted mb-0" style="font-size:.7rem">'+rowData.courseabrv+'</p>';
                            $(td)[0].innerHTML =  text 
                        }
                    },

                
                    
            ]

            });
            }
    </script>

    <script>

        var sy = @json($sy);

        $(document).on('change','#teacher_acad_sy',function(){
            $('.teacher_acad').prop('checked',false)
            var check = sy.filter(x=>x.id == $(this).val())
            if(check[0].ended == 1){
                $('.teacher_acad').attr('disabled','disabled')
            }else{
                $('.teacher_acad').removeAttr('disabled')
            }
            get_teacher_academicprogram()
        })

        get_teacher_academicprogram()
        function get_teacher_academicprogram(){
            $.ajax({
                type:'GET',
                url: '/fas/teachercad/list',
                data:{
                    syid:$('#teacher_acad_sy').val(),
                    teacherid:teacherinfo[0].id
                },
                success:function(data) {
                    if(data.length > 0){
                        Toast.fire({
                            type: 'success',
                            title: data.length +' Academic Program(s) found!'
                        })
                    }else{
                        Toast.fire({
                            type: 'warning',
                            title: 'No Academic Program Found!'
                        })
                    }
                    
                    $.each(data,function(a,b){
                        $('.teacher_acad[data-id="'+b.acadprogid+'"]').prop('checked',true)
                    })

                },
            })
        }

    </script>

@endsection


