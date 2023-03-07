

@php
      if(auth()->user()->type == 17){
            $extend = 'superadmin.layouts.app2';
      }else if(auth()->user()->type == 2 || Session::get('currentPortal') == 2){
            $extend = 'principalsportal.layouts.app2';
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
      <link rel="stylesheet" href="{{asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
      <style>
            .select2-container--default .select2-selection--single .select2-selection__rendered {
                  margin-top: -9px;
            }
            .shadow {
                  box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
                  border: 0 !important;
            }
            .no-border-col{
                  border-left: 0 !important;
                  border-right: 0 !important;
            }
      </style>
@endsection


@section('content')

@php
   $sy = DB::table('sy')->get(); 
   $activesy = DB::table('sy')->where('isactive',1)->first()->id; 
@endphp


<section class="content-header">
      <div class="container-fluid">
            <div class="row">
                  <div class="col-sm-6">
                  
                  </div>
                  <div class="col-sm-6">
                  <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/home">Home</a></li>
                        <li class="breadcrumb-item active">Student Specialization</li>
                  </ol>
                  </div>
            </div>
      </div>
</section>
    
<section class="content pt-0">
      <div class="container-fluid">

            <div class="row">
                  <div class="col-md-8">
                        <div class="row">
                              <div class="col-md-12">
                                    <div class="info-box shadow-lg">
                                          <div class="info-box-content">
                                                <div class="row">
                                                      <div class="col-md-3  form-group">
                                                            <label for="">School Year</label>
                                                            <select class="form-control select2" id="filter_schoolyear">
                                                                  @foreach ($sy as $item)
                                                                        @if($item->isactive == 1)
                                                                              <option value="{{$item->id}}" selected="selected">{{$item->sydesc}}</option>
                                                                        @else
                                                                              <option value="{{$item->id}}">{{$item->sydesc}}</option>
                                                                        @endif
                                                                  @endforeach
                                                            </select>
                                                      </div>
                                                      <div class="col-md-6  form-group">
                                                            <label for="">Subject</label>
                                                            <select class="form-control select2" id="input_subject">
                                                                  <option value="">Select a subject</option>
                                                            </select>
                                                      </div>
                                                </div>
                                                {{-- <div class="row">
                                                      <div class="col-md-3">
                                                            <button class="btn btn-info btn-block btn-sm mt-2" id="button_search"><i class="fas fa-filter"></i> Filter</button>
                                                      </div>
                                                </div> --}}
                                          </div>
                                    </div>
                              </div>
                        </div>
                  </div>
            </div>
            <div class="row">
                 
                  <div class="col-md-3">
                        <div class="card shadow">
                              <div class="card-header ">
                                    <h3 class="card-title">
                                          Student Selection
                                    </h3>
                              </div>
                              <div class="card-body">
                                    <div class="row">
                                          <div class="col-md-12 form-group mb-0">
                                                <label for="">Student</label>
                                                <select class="form-control select2" id="filter_student">
                                                      <option value="">Select a student</option>
                                                </select>
                                          </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                          <div class="col-md-12 form-group mb-1">
                                                <div class="icheck-primary d-inline pt-2">
                                                    <input type="checkbox" id="q1" checked class="form-control">
                                                    <label for="q1">Quarter 1
                                                    </label>
                                                </div>
                                          </div>
                                          <div class="col-md-12 form-group mb-1">
                                                <div class="icheck-primary d-inline pt-2">
                                                    <input type="checkbox" id="q2" checked class="form-control">
                                                    <label for="q2">Quarter 2
                                                    </label>
                                                </div>
                                          </div>
                                          <div class="col-md-12 form-group mb-1">
                                                <div class="icheck-primary d-inline pt-2">
                                                    <input type="checkbox" id="q3" checked class="form-control">
                                                    <label for="q3">Quarter 3
                                                    </label>
                                                </div>
                                          </div>
                                          <div class="col-md-12 form-group  mb-1">
                                                <div class="icheck-primary d-inline pt-2">
                                                    <input type="checkbox" id="q4" checked class="form-control">
                                                    <label for="q4">Quarter 4
                                                    </label>
                                                </div>
                                          </div>
                                    </div>
                                    <hr>
                                    <div class="row mt-3">
                                          <div class="col-md-12">
                                                <button class="btn btn-primary btn-sm" id="student_specialization_create" disabled="disabled">Add Student</button>
                                          </div>
                                    </div>
                              </div>
                        </div>
                  </div>
                  <div class="col-md-9">
                        <div class="card shadow">
                              <div class="card-header ">
                                    <h3 class="card-title">
                                          Student Specialization List
                                    </h3>
                              </div>
                              <div class="card-body">
                                   
                                    <div class="row">
                                          <div class="col-md-12">
                                                <table class="table table-sm" id="student_specialization" width="100%">
                                                      <thead>
                                                            <tr>
                                                                  <th width="35%">Learner</th>
                                                                  <th width="35%">Section / Grade Level</th>
                                                                  <th width="5%">Q1</th>
                                                                  <th width="5%">Q2</th>
                                                                  <th width="5%">Q3</th>
                                                                  <th width="5%">Q4</th>
                                                                  <th width="10%"></th>
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


      <script>
            $(document).ready(function(){

                  var syid = @json($activesy);
                  // loaddatatable()

                  const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                  })

                  $('.select2').select2()

                  var all_student_specialization = []
                  var selected_learner
                  var process = 'create'

                  // get_student_specialization()

                  $(document).on('click','#student_specialization_button',function(){
                        process = 'create'
                        $('#student_specialization_create').text('Create')  
                        $('#student_specialization_modal').modal()    

                        $('#input_month').val(1).change()
                        $('#input_sy').val(syid).change()
                        $('#input_day').val("")
                        $('#input_sort').val("")                  
                  })

                  $(document).on('click','#student_specialization_create',function(){
                        student_specialization_create()         
                  })

                  $(document).on('click','.delete',function(){
                        selected_learner = $(this).attr('data-id')
                        student_specialization_delete()
                  })

                  $(document).on('change','#input_subject',function(){
                        filter_change()
                        $('#student_specialization_create').removeAttr('disabled')
                  })


                  $(document).on('change','#filter_schoolyear',function(){
                        get_subjects()
                        all_students = []
                        $("#filter_student").empty();
                        $("#filter_student").append('<option value="">Select a student</option>')
                        $("#filter_student").select2({
                              data: all_students,
                              placeholder: "Select a student",
                        })
                        subjects_studspec = []
                        loaddatatable()
                        $('#student_specialization_create').attr('disabled','disabled')
                  })



                  $(document).on('click','.edit',function(){
                        selected_setup = $(this).attr('data-id')
                        process = 'edit'
                        var temp_attendance_id = all_student_specialization.filter(x=>x.id == selected_setup)
                        $('#input_month').val(temp_attendance_id[0].month).change(),
                        $('#input_day').val(temp_attendance_id[0].days),
                        $('#input_sy').val(temp_attendance_id[0].syid).change(),
                        $('#input_sort').val(temp_attendance_id[0].sort)
                        $('#student_specialization_modal').modal()   
                        $('#student_specialization_create').text('Update')           
                  })

                  // function get_student_specialization(){
                  //       $.ajax({
			// 		type:'GET',
			// 		url: '/superadmin/attendance/list',
			// 		success:function(data) {
			// 			all_student_specialization = data
                  //                   loaddatatable()
			// 		}
			// 	})
                  // }

                  var all_students = []
               
                  function get_students(){
                        var acadprog = all_subjects.filter(x=>x.id == $('#input_subject').val())
                        $.ajax({
					type:'GET',
					url: '/superadmin/student/specialization/students',
                              data:{
                                    syid:$('#filter_schoolyear').val(),
                                    subjid:$('#input_subject').val()
                              },
					success:function(data) {
						all_students = data
                                    loaddatatable()
                                 
					}
				})
                  }

                 
      
                  function student_specialization_create(){


                        var q1 = 0;
                        var q2 = 0;
                        var q3 = 0;
                        var q4 = 0;

                        if($('#q1').prop('checked') == true){
                              q1 = 1
                        }
                        if($('#q2').prop('checked') == true){
                              q2 = 1
                        }
                        if($('#q3').prop('checked') == true){
                              q3 = 1
                        }
                        if($('#q4').prop('checked') == true){
                              q4 = 1
                        }

                        $.ajax({
					type:'GET',
					url: '/superadmin/student/specialization/create',
					data:{
                                    subjid:$('#input_subject').val(),
                                    syid:$('#filter_schoolyear').val(),
                                    q1:q1,
                                    q2:q2,
                                    q3:q3,
                                    q4:q4,
                                    studid:$('#filter_student').val()
                              },
					success:function(data) {
                                    subjects_studspec = data[0].info
						if(data[0].status == 1){

							Toast.fire({
                                                type: 'success',
                                                title: 'Added Successfully!'
                                          })
                                          subjects_studspec = data[0].info
                                          console.log()
                                          loaddatatable(subjects_studspec)

						}else{
							Toast.fire({
                                                type: 'warning',
                                                title: data[0].data
                                          })
						}
					}
				})
                  }

                  function student_specialization_update(){
                        $.ajax({
					type:'GET',
					url: '/superadmin/attendance/update',
					data:{
						month:$('#input_month').val(),
                                    days:$('#input_day').val(),
                                    syid:$('#input_sy').val(),
                                    sort:$('#input_sort').val(),
                                    attsetupid:selected_setup
					},
					success:function(data) {
						if(data[0].status == 1){
							Swal.fire({
								type: 'success',
								title: data[0].data,
							});

                                          var attsetup_index = all_student_specialization.findIndex(x => x.id == selected_setup)
							all_student_specialization[attsetup_index].sort = $('#input_sort').val()
                                          all_student_specialization[attsetup_index].days = $('#input_day').val()
							all_student_specialization[attsetup_index].syid = $('#input_sy').val()
                                          all_student_specialization[attsetup_index].sydesc = $('#input_sy option[value="'+$('#input_sy').val()+'"]').text()
							all_student_specialization[attsetup_index].month = $('#input_month').val()
                                          all_student_specialization[attsetup_index].monthdesc = $('#input_month option[value="'+$('#input_month').val()+'"]').text()

                                          loaddatatable()

						}else{
							Swal.fire({
								type: 'error',
								title: data[0].data,
							});
						}
					}
				})
                  }


                  function student_specialization_delete(){
                        $.ajax({
					type:'GET',
					url: '/superadmin/student/specialization/delete',
					data:{
						id:selected_learner
					},
					success:function(data) {
						if(data[0].status == 1){
							Toast.fire({
                                                type: 'success',
                                                title: data[0].data
                                          })
                                          subjects_studspec = subjects_studspec.filter(x=>x.id != selected_learner)
                                          loaddatatable()
						}else{
							Toast.fire({
                                                type: 'warning',
                                                title: data[0].data
                                          })
						}
					}
				})
                  }


                  function loaddatatable(){

                        $.each(subjects_studspec,function (a,b) {
                              all_students = all_students.filter(x=>x.studid != b.studid)
                        })

                        $.each(all_students,function(a,b){
                              b.text = b.lastname + ', '+b.firstname
                              b.id = b.studid
                        })
                        
                        $("#filter_student").select2({
                              data: all_students,
                              placeholder: "Select a student",
                        })

                        $("#student_specialization").DataTable({
                                    destroy: true,
                                    data:subjects_studspec,
                                    lengthChange: false,
                                    columns: [
                                          { "data": null},
                                          { "data": null},
                                          { "data": null},
                                          { "data": null},
                                          { "data": null},
                                          { "data": null},
                                          { "data": "search"}
                                    ],

                                    columnDefs: [
                                                      {
										'targets': 0,
										'orderable': true, 
										'createdCell':  function (td, cellData, rowData, row, col) {
                                                                  var text = '<a class="mb-0">'+rowData.student+'</a><p class="text-muted mb-0" style="font-size:.7rem">'+rowData.sid+'</p>';
                                                                  $(td)[0].innerHTML =  text
                                                                 
										}
                                    			},
                                                      {
										'targets': 1,
										'orderable': true, 
										'createdCell':  function (td, cellData, rowData, row, col) {
                                                                  var text = '<a class="mb-0">'+rowData.sectionname+'</a><p class="text-muted mb-0" style="font-size:.7rem">'+rowData.levelname+'</p>';
                                                                  $(td)[0].innerHTML =  text
                                                                 
										}
                                    			},
                                                      {
										'targets': 2,
										'orderable': true, 
										'createdCell':  function (td, cellData, rowData, row, col) {
											if(rowData.q1 == 1){
                                                                        $(td)[0].innerHTML = '<i class="far fa-check-circle text-success"></i>'
                                                                  }else{
                                                                        $(td)[0].innerHTML = '<i class="far fa-times-circle text-danger"></i>'
                                                                  }
                                                                  $(td).addClass('align-middle')
										}
                                    			},
                                                      {
										'targets': 3,
										'orderable': true, 
										'createdCell':  function (td, cellData, rowData, row, col) {
											if(rowData.q2 == 1){
                                                                        $(td)[0].innerHTML = '<i class="far fa-check-circle text-success"></i>'
                                                                  }else{
                                                                        $(td)[0].innerHTML = '<i class="far fa-times-circle text-danger"></i>'
                                                                  }
                                                                  $(td).addClass('align-middle')
										}
                                    			},
                                                      {
										'targets': 4,
										'orderable': true, 
										'createdCell':  function (td, cellData, rowData, row, col) {
											if(rowData.q3 == 1){
                                                                        $(td)[0].innerHTML = '<i class="far fa-check-circle text-success"></i>'
                                                                  }else{
                                                                        $(td)[0].innerHTML = '<i class="far fa-times-circle text-danger"></i>'
                                                                  }
                                                                  $(td).addClass('align-middle')
										}
                                    			},
                                                      {
										'targets': 5,
										'orderable': true, 
										'createdCell':  function (td, cellData, rowData, row, col) {
											if(rowData.q4 == 1){
                                                                        $(td)[0].innerHTML = '<i class="far fa-check-circle text-success"></i>'
                                                                  }else{
                                                                        $(td)[0].innerHTML = '<i class="far fa-times-circle text-danger"></i>'
                                                                  }
                                                                  $(td).addClass('align-middle')
										}
                                    			},
									{
										'targets': 6,
										'orderable': true, 
										'createdCell':  function (td, cellData, rowData, row, col) {
											var buttons = '<a href="#" class="delete ml-4" data-id="'+rowData.id+'"><i class="far fa-trash-alt text-danger"></i></a>';
											$(td)[0].innerHTML =  buttons
											$(td).addClass('text-center')
                                                                  $(td).addClass('align-middle')
										}
                                    			},
								]
                        });
                  
                  }


                  //subjects
                  var all_subjects = []
                  get_subjects()
                  function get_subjects(){
                        $.ajax({
					type:'GET',
					url: '/superadmin/student/specialization/subjects',
                              data:{
                                    syid:$('#filter_schoolyear').val()
                              },
					success:function(data) {
						all_subjects = data
                                   
                                    $.each(all_subjects,function(a,b){
                                          var subj_num = 'S'+('000'+b.id).slice (-3)
                                          b.text = subj_num + ' - ' + b.text
                                    })
                                    $("#input_subject").empty()
                                    $("#input_subject").append('<option value="">Select a subject</option>')
                                    $("#input_subject").select2({
                                          data: all_subjects,
                                          placeholder: "Select a subject",
                                    })
					}
				})
                  }

                  function filter_change(){
                        $('#student_specialization_create').removeAttr('disabled')
                        get_students()
                        get_subjects_studspec()
                  }

                  //subjects
                  var subjects_studspec = []
                  function get_subjects_studspec(){
                        $.ajax({
					type:'GET',
					url: '/superadmin/student/specialization/data',
                              data:{
                                    subjid:$('#input_subject').val(),
                                    syid:$('#filter_schoolyear').val()
                              },
					success:function(data) {
						subjects_studspec = data
                                    loaddatatable()
					}
				})
                  }



            })
      </script>


@endsection


