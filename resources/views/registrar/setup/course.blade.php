
@php
      if(auth()->user()->type == 17){
            $extend = 'superadmin.layouts.app2';
      }else if(auth()->user()->type == 3 || Session::get('currentPortal') == 3){
            $extend = 'registrar.layouts.app';
      }
@endphp


@extends($extend)

@section('pagespecificscripts')
      <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
      <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
      <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
      <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
      <link rel="stylesheet" href="{{ asset('plugins/datatables-fixedcolumns/css/fixedColumns.bootstrap4.css') }}">
      <link rel="stylesheet" href="{{asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
      <style>
            .select2-container--default .select2-selection--single .select2-selection__rendered {
                  margin-top: -9px;
            }
            .shadow {
                  box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
                  border: 0;
            }
      </style>
@endsection


@section('content')


@php
      $sy = DB::table('sy')->orderBy('sydesc')->get(); 
      $semester = DB::table('semester')->get(); 

      $teachers = DB::table('teacher')
                        ->where('usertypeid',16)
                        ->where('deleted',0)
                        ->select(
                              'teacher.id',
                              'lastname',
                              'firstname',
                              'middlename',
                              'suffix',
                              'title',
                              'tid'
                        )
                        ->get();
            
      $faspriv = DB::table('faspriv')
                        ->where('usertype',16)
                        ->where('faspriv.deleted',0)
                        ->join('teacher',function($join){
                              $join->on('faspriv.userid','=','teacher.userid');
                              $join->where('teacher.deleted',0);
                        })
                        ->select(
                              'teacher.id',
                              'lastname',
                              'firstname',
                              'middlename',
                              'suffix',
                              'title',
                              'tid'
                        )
                        ->get();

      $cph_list = array();

      foreach($teachers as $item){
            $temp_title = '';
            $temp_middle = '';
            $temp_suffix = '';
            if(isset($item->middlename)){
                  $temp_middle = $item->middlename[0].'.';
            }
            if(isset($item->title)){
                  $temp_title = $item->title.'. ';
            }
            if(isset($item->suffix)){
                  $temp_suffix = ', '.$item->suffix;
            }
            $item->text = $item->tid.' - '.$item->firstname.' '.$temp_middle.' '.$item->lastname.$temp_suffix.', '.$temp_title;
            array_push($cph_list , $item);      
      }

      foreach($faspriv as $item){
            $temp_title = '';
            $temp_middle = '';
            $temp_suffix = '';
            if(isset($item->middlename)){
                  $temp_middle = ' '.$item->middlename[0].'.';
            }
            if(isset($item->title)){
                  $temp_title = ', '.$item->title.'. ';
            }
            if(isset($item->suffix)){
                  $temp_suffix = ', '.$item->suffix;
            }
            $item->text = $item->tid.' - '.$item->firstname.$temp_middle.' '.$item->lastname.$temp_suffix.$temp_title;
            array_push($cph_list , $item);      
      }
@endphp

<div class="modal fade" id="courses_form_modal" style="display: none;" aria-hidden="true">
      <div class="modal-dialog">
            <div class="modal-content">
                  <div class="modal-header pb-2 pt-2 border-0">
                        <h4 class="modal-title">Course Form</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">×</span></button>
                  </div>
                  <div class="modal-body pt-0">
                        <div class="row">
                              <div class="col-md-12 form-group">
                                    <label for="">Courses Name</label>
                                    <input id="input_coursesname" class="form-control form-control-sm">
                              </div>
                        </div>
                        <div class="row">
                              <div class="col-md-12 form-group">
                                    <label for="">Courses Abbreviation</label>
                                    <input id="input_abbreviation" class="form-control form-control-sm">
                              </div>
                        </div>
                        <div class="row">
                              <div class="col-md-12 form-group">
                                    <label for="">College</label>
                                    <select id="input_college" class="form-control form-control-sm" placeholder="Hello"></select>
                              </div>
                        </div>
                        <div class="row">
                              <div class="col-md-12 form-group">
                                    <label for="">Chairperson/Program Head Assistant</label>
                                    <select id="input_cph" class="form-control form-control-sm" multiple>
                                    </select>
                              </div>
                        </div>
                        <div class="row">
                              <div class="col-md-12 form-group">
                                    <label for="">Chairperson/Program Head</label>
                                    <select id="input_cph_head" class="form-control form-control-sm">
                                    </select>
                              </div>
                        </div>
                        <div class="row">
                              <div class="col-md-12">
                                    <button class="btn btn-sm btn-primary" id="courses_form_button">Create</button>
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
                        <h1>Courses</h1>
                  </div>
                  <div class="col-sm-6">
                  <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/home">Home</a></li>
                        <li class="breadcrumb-item active">Courses</li>
                  </ol>
                  </div>
            </div>
      </div>
</section>
    
<section class="content pt-0">
      <div class="container-fluid">
            <div class="row">
                  <div class="col-md-12">
                        <div class="info-box shadow-lg">
                              <div class="info-box-content">
                                    <div class="row">
                                          <div class="col-md-4">
                                               <h5><i class="fa fa-filter"></i> Filter</h5> 
                                          </div>
                                          <div class="col-md-8">
                                                
                                          </div>
                                    </div>
                                    <div class="row">
                                          <div class="col-md-2  form-group mb-0">
                                                <label for="">School Year</label>
                                                <select class="form-control select2" id="filter_sy">
                                                      @foreach ($sy as $item)
                                                            @if($item->isactive == 1)
                                                                  <option value="{{$item->id}}" selected="selected">{{$item->sydesc}}</option>
                                                            @else
                                                                  <option value="{{$item->id}}">{{$item->sydesc}}</option>
                                                            @endif
                                                      @endforeach
                                                </select>
                                          </div>
                                          <div class="col-md-2 form-group mb-0" hidden>
                                                <label for="">Semester</label>
                                                <select class="form-control select2" id="filter_semester">
                                                      @foreach ($semester as $item)
                                                            <option {{$item->isactive == 1 ? 'checked' : ''}} value="{{$item->id}}">{{$item->semester}}</option>
                                                      @endforeach
                                                </select>
                                          </div>
                                    </div>
                                    
                              </div>
                        </div>
                  </div>
            </div>
            <div class="row">
                  <div class="col-md-12">
                        <div class="card shadow" style="">
                              <div class="card-body">
                                    <div class="row mt-2">
                                          <div class="col-md-12"  style="font-size:.7rem">
                                                <table class="table-hover table table-striped table-sm table-bordered display compact" id="courses_datatable" width="100%" >
                                                      <thead>
                                                            <tr>
                                                                  <th width="50%">Courses Name</th>
                                                                  <th width="10%">Abbreviation</th>
                                                                  <th width="17%">Program Head</th>
                                                                  <th width="10%">College</th>
                                                                  <th width="7%" class="text-center p-0 align-middle">Enrolled</th>
                                                                  <th width="3%"></th>
                                                                  <th width="3%"></th>
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
</section>

@endsection

@section('footerjavascript')
      <script src="{{asset('plugins/select2/js/select2.full.min.js') }}"></script>
      <script src="{{asset('plugins/datatables/jquery.dataTables.js') }}"></script>
      <script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
      <script src="{{asset('plugins/datatables-fixedcolumns/js/dataTables.fixedColumns.js') }}"></script>

      <script>
            $(document).ready(function(){

                  const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                  })

                  $('.select2').select2()

                  var cph_list = @json($cph_list)
                  
                  $("#input_cph").empty()
                  $("#input_cph").select2({
                        data: cph_list,
                        placeholder: "Chairperson/Program Head",
                        theme: 'bootstrap4'
                  })

                  $("#input_cph_head").empty()
                  $("#input_cph_head").append('Select Chairperson/Program Head')
                  $("#input_cph_head").select2({
                        data: cph_list,
                        allowClear:true,
                        placeholder: "Chairperson/Program Head",
                        theme: 'bootstrap4'
                  })

                  var courses_list = []
                  var selected_id = null

                  load_courses_datatable()
                  get_courses_list()
                  get_college_list()

                  $(document).on('click','#button_to_modal_courses',function(){

                        cph_list = @json($cph_list)
                  
                        $("#input_cph").empty()
                        $("#input_cph").select2({
                              data: cph_list,
                              placeholder: "Chairperson/Program Head",
                              theme: 'bootstrap4'
                        })

                        $("#input_cph_head").empty()
                        $("#input_cph_head").append('Select Chairperson/Program Head')
                        $("#input_cph_head").select2({
                              data: cph_list,
                              allowClear:true,
                              placeholder: "Chairperson/Program Head",
                              theme: 'bootstrap4'
                        })

                        $('#input_coursesname').val("")
                        $('#input_abbreviation').val("")
                        $('#input_college').val("").change()
                        $('#input_cph_head').val("").change()
                        $('#input_cph').val("").change()
                        $('#courses_form_modal').modal()
                        $('#courses_form_button').removeClass('btn-success')
                        $('#courses_form_button').addClass('btn-primary')
                        $('#courses_form_button').text('Create')
                        $('#courses_form_button').attr('data-proccess','create')
                  })

                  $(document).on('click','.udpate_courses',function(){
                        selected_id = $(this).attr('data-id')
                        var temp_courses_info = courses_list.filter(x=>x.id == selected_id)
                        $('#input_coursesname').val(temp_courses_info[0].courseDesc)
                        $('#input_abbreviation').val(temp_courses_info[0].courseabrv)
                        $('#input_college').val(temp_courses_info[0].collegeid).change()

                        var temp_programhead = []
                        $.each(temp_courses_info[0].programhead,function(a,b){
                              var temp_check = cph_list.filter(x=>x.id == b.id)

                              if(temp_check.length == 0){
                                    cph_list.push(b)
                              }

                              if(b.id != temp_courses_info[0].courseChairman){
                                    temp_programhead.push(b.id)
                              }
                             
                        })

                        $("#input_cph").empty()
                        $("#input_cph").select2({
                              data: cph_list,
                              placeholder: "Chairperson/Program Head",
                              theme: 'bootstrap4'
                        })

                        $("#input_cph_head").empty()
                        $("#input_cph_head").append('Select Chairperson/Program Head')
                        $("#input_cph_head").select2({
                              data: cph_list,
                              allowClear:true,
                              placeholder: "Chairperson/Program Head",
                              theme: 'bootstrap4'
                        })

                        $('#input_cph').val(temp_programhead).change()
                        $('#input_cph_head').val(temp_courses_info[0].courseChairman).change()
                        

                        $('#courses_form_modal').modal()
                        $('#courses_form_button').removeClass('btn-primary')
                        $('#courses_form_button').addClass('btn-success')
                        $('#courses_form_button').text('Update')
                        $('#courses_form_button').attr('data-proccess','update')
                  })

                  $(document).on('click','.delete_courses',function(){
                        selected_id = $(this).attr('data-id')
                        delete_courses()
                  })

                  $(document).on('click','#courses_form_button',function(){
                        if($(this).attr('data-proccess') == 'update'){
                              udpate_courses()
                        }else if($(this).attr('data-proccess') == 'create'){
                              create_courses()
                        }
                  })

                  $(document).on('change','#filter_sy',function(){
                        get_courses_list()
                  })

                  $(document).on('change','#filter_semester',function(){
                        get_courses_list()
                  })

                  function get_college_list(){
                        var syid = $('#filter_sy').val()
                        var semid = $('#filter_semester').val()
                        $.ajax({
                              type:'GET',
                              url:'/setup/college/list',
                              success:function(data) {
                                    if(data.length == 0){
                                          Toast.fire({
                                                type: 'warning',
                                                title: 'No courses found.'
                                          })
                                    }else{
                                          $("#input_college").append('<option value="">Select College</option>')
                                          $("#input_college").select2({
                                                data: data,
                                                allowClear: true,
                                                placeholder: "Select College",
                                          })
                                    }
                              }
                        })
                  }


                  function get_courses_list(){
                        var syid = $('#filter_sy').val()
                        var semid = $('#filter_semester').val()
                        $.ajax({
                              type:'GET',
                              url:'/setup/course/list',
                                    data:{
                                    syid:syid,
                                    semid:semid,
                                    withEnrollmentCount:true
                              },
                              success:function(data) {
                                    if(data.length == 0){
                                          Toast.fire({
                                                type: 'warning',
                                                title: 'No courses found.'
                                          })
                                    }else{
                                          var total_enrolled = 0;
                                          $.each(data,function(a,b){
                                                total_enrolled += parseInt(b.enrolled)
                                          })
                                          // Toast.fire({
                                          //       type: 'warning',
                                          //       title: total_enrolled+' student(s) enrolled!'
                                          // })
                                          courses_list = data
                                          load_courses_datatable()
                                    }
                              }
                        })
                  }

                  function create_courses(){

                        var coursedesc = $('#input_coursesname').val()
                        var courseabrv = $('#input_abbreviation').val()
                        var collegeid = $('#input_college').val()

                        if(coursedesc == ""){
                              Toast.fire({
                                    type: 'warning',
                                    title: "courses Name is empty."
                              })
                              return false
                        }

                        if(courseabrv == ""){
                              Toast.fire({
                                    type: 'warning',
                                    title: "courses Abbreviation is empty."
                              })
                              return false
                        }

                        if(collegeid == ""){
                              Toast.fire({
                                    type: 'warning',
                                    title: "College is empty."
                              })
                              return false
                        }


                        $.ajax({
                              type:'GET',
                              url:'/setup/course/create',
                              data:{
                                    syid:$('#filter_sy').val(),
                                    semid:$('#filter_semester').val(),
                                    cphid:$('#input_cph').val(),
                                    cphidhead:$('#input_cph_head').val(),
                                    coursedesc:coursedesc,
                                    courseabrv:courseabrv,
                                    collegeid:collegeid
                              },
                              success:function(data) {
                                    if(data[0].status == 0){
                                          Toast.fire({
                                                type: 'error',
                                                title: data[0].message
                                          })
                                    }else{
                                          get_courses_list()
                                          Toast.fire({
                                                type: 'success',
                                                title: data[0].message
                                          })
                                          $('#courses_form_modal').modal('hide')
                                    }
                              }
                        })
                  }

                  function udpate_courses(){
                        var id = selected_id
                        var coursedesc = $('#input_coursesname').val()
                        var courseabrv = $('#input_abbreviation').val()
                        var collegeid = $('#input_college').val()

                        if(coursedesc == ""){
                              Toast.fire({
                                    type: 'warning',
                                    title: "courses Name is empty."
                              })
                              return false
                        }

                        if(courseabrv == ""){
                              Toast.fire({
                                    type: 'warning',
                                    title: "courses Abbreviation is empty."
                              })
                              return false
                        }

                        if(collegeid == ""){
                              Toast.fire({
                                    type: 'warning',
                                    title: "College is empty."
                              })
                              return false
                        }

                        
                        $.ajax({
                              type:'GET',
                              url:'/setup/course/update',
                              data:{
                                    syid:$('#filter_sy').val(),
                                    semid:$('#filter_semester').val(),
                                    cphid:$('#input_cph').val(),
                                    cphidhead:$('#input_cph_head').val(),
                                    coursedesc:coursedesc,
                                    courseabrv:courseabrv,
                                    collegeid:collegeid,
                                    id:id
                              },
                              success:function(data) {
                                    if(data[0].status == 0){
                                          Toast.fire({
                                                type: 'warning',
                                                title: data[0].message
                                          })
                                    }else{
                                          get_courses_list()
                                          Toast.fire({
                                                type: 'success',
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

                  function delete_courses(){
                        var id = selected_id
                        $.ajax({
                              type:'GET',
                              url:'/setup/course/delete',
                              data:{
                                    id:id
                              },
                              success:function(data) {
                                    if(data[0].status == 0){
                                          Toast.fire({
                                                type: 'warning',
                                                title: data[0].message
                                          })
                                    }else{
                                          get_courses_list()
                                          Toast.fire({
                                                type: 'success',
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

                  function load_courses_datatable(){

                        $("#courses_datatable").DataTable({
                              destroy: true,
                              data:courses_list,
                              lengthChange : false,
                              stateSave: true,
                              columns: [
                                          { "data": "courseDesc" },
                                          { "data": "courseabrv" },
                                          { "data": "programhead" },
                                          { "data": "collegeabrv" },
                                          { "data": "enrolled" },
                                          { "data": null },
                                          { "data": null },
                                    ],
                              columnDefs: [
                                    {
                                          'targets': 1,
                                          'orderable': false, 
                                          'createdCell':  function (td, cellData, rowData, row, col) {
                                                
                                                $(td).addClass('align-middle')
                                          }
                                    },
                                    {
                                          'targets': 2,
                                          'orderable': false, 
                                          'createdCell':  function (td, cellData, rowData, row, col) {
                                                var temp_programhead = rowData.programhead
                                                var temp_text = ''

                                                if(temp_programhead.length > 0){
                                                      $.each(temp_programhead,function(a,b){
                                                            temp_text += b.text
                                                            if(temp_programhead.length-1 != a){
                                                                  temp_text += ' / '
                                                            }
                                                      })
                                                      $(td).text(temp_text)
                                                }else{
                                                      $(td).text(null)
                                                }
                                                $(td).addClass('align-middle')
                                          }
                                    },
                                    {
                                          'targets': 3,
                                          'orderable': false, 
                                          'createdCell':  function (td, cellData, rowData, row, col) {
                                                $(td).addClass('align-middle')
                                          }
                                    },
                                    {
                                          'targets': 4,
                                          'orderable': false, 
                                          'createdCell':  function (td, cellData, rowData, row, col) {
                                                $(td).addClass('text-center')
                                                $(td).addClass('align-middle')
                                          }
                                    },
                                    {
                                          'targets': 5,
                                          'orderable': false, 
                                          'createdCell':  function (td, cellData, rowData, row, col) {
                                                var buttons = '<a href="javascript:void(0)" class="udpate_courses" data-id="'+rowData.id+'"><i class="far fa-edit"></i></a>';
                                                $(td)[0].innerHTML =  buttons
                                                $(td).addClass('text-center')
                                                $(td).addClass('align-middle')
                                                
                                          }
                                    },
                                    {
                                          'targets': 6,
                                          'orderable': false, 
                                          'createdCell':  function (td, cellData, rowData, row, col) {
                                                var disabled = '';
                                                var buttons = '<a href="javascript:void(0)" '+disabled+' class="delete_courses" data-id="'+rowData.id+'"><i class="far fa-trash-alt text-danger"></i></a>';
                                                $(td)[0].innerHTML =  buttons
                                                $(td).addClass('text-center')
                                                $(td).addClass('align-middle')
                                          }
                                    },
                              ]
                              
                        });

                        
                        var label_text = $($("#courses_datatable_wrapper")[0].children[0])[0].children[0]
                        $(label_text)[0].innerHTML = '<button class="btn btn-primary btn-sm" id="button_to_modal_courses"><i class="fas fa-plus"></i> Create Course</button>'
                        
                  
                  }

            })
      </script>

      {{-- IU --}}
      <script>

            $(document).ready(function(){

                  var keysPressed = {};

                  document.addEventListener('keydown', (event) => {
                        keysPressed[event.key] = true;
                        if (keysPressed['p'] && event.key == 'v') {
                              Toast.fire({
                                          type: 'warning',
                                          title: 'Date Version: 07/26/2021 16:34'
                                    })
                        }
                  });

                  document.addEventListener('keyup', (event) => {
                        delete keysPressed[event.key];
                  });


                  const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                  })

                  $(document).on('input','#per',function(){
                        if($(this).val() > 100){
                              $(this).val(100)
                              Toast.fire({
                                    type: 'warning',
                                    title: 'Subject percentage exceeds 100!'
                              })
                        }
                  })
            })
      </script>

@endsection


