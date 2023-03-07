
@php
if(auth()->user()->type == 16){
      $extend = 'chairpersonportal.layouts.app2';
}else if(auth()->user()->type == 14){
      $extend = 'deanportal.layouts.app2';
}else if(auth()->user()->type == 17){
      $extend = 'superadmin.layouts.app2';
}else if(auth()->user()->type == 3){
      $extend = 'registrar.layouts.app';
}else if(auth()->user()->type == 3 || Session::get('currentPortal') == 3){
      $extend = 'registrar.layouts.app';
}
@endphp

@extends($extend)
@section('pagespecificscripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-fixedcolumns/css/fixedColumns.bootstrap4.css') }}">
<link rel="stylesheet" href="{{asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
<link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
<style>
      /* .select2-selection{
          height: calc(2.25rem + 2px) !important;
      } */
      .select2-container--default .select2-selection--single .select2-selection__rendered {
            margin-top: -9px;
      }
      .shadow {
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
            border: 0;
      }
      #et{
            height: 10px;
            visibility: hidden;
      }
</style>
@endsection


@section('content')

@php

$sy = DB::table('sy')
            ->orderBy('sydesc')
            ->select(
                  'id',
                  'sydesc as text',
                  'isactive'
            )
            ->get(); 
$semester = DB::table('semester')
                  ->select(
                        'id',
                        'semester as text',
                        'isactive'
                  )
                  ->get(); 

$schoolinfo = DB::table('schoolinfo')->first()->abbreviation;

// $gradesetup = DB::table('semester_setup')
//                         ->where('deleted',0)
//                         ->first();


$registrar = DB::table('teacher')
                  ->where('usertypeid',3)
                  ->select(
                        'teacher.id',
                        DB::raw("CONCAT(teacher.lastname,', ',teacher.firstname) as text")
                  )
                  ->get();

@endphp


<div class="modal fade" id="inputperiods_form_modal" style="display: none;" aria-hidden="true">
      <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header pb-2 pt-2 border-0">
                  <h4 class="modal-title" style="font-size: 1.1rem !important">Input Period</h4>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body pt-0">
                  <div class="row">
                        <div class="col-md-4  form-group">
                              <label for="" class="mb-1">Status</label>
                              <select class="form-control select2 form-control-sm" id="filter_status">
                                    <option value="">All</option>
                                    <option value="1">Active</option>
                                    <option value="2">Not Active</option>
                                    <option value="3">Ended</option>
                              </select>
                        </div>
                  </div>
                  <div class="row">
                        <div class="col-md-12" style="font-size:.8rem !important">
                              <table class="table table-sm table-striped table-bordered table-hovered table-hover " id="inputperiod_datatable">
                                    <thead>
                                          <tr>
                                                <th width="18%"></th>
                                                <th width="34%">Start Date</th>
                                                <th width="34%">End Date</th>
                                                <th width="7%"></th>
                                                <th width="7%"></th>
                                          </tr>
                                    </thead>
                              </table>
                        </div>  
                  </div> 
            </div>
          </div>
      </div>
</div>


<div class="modal fade" id="registrar_holder_modal" style="display: none;" aria-hidden="true">
      <div class="modal-dialog modal-sm">
            <div class="modal-content">
                  <div class="modal-header pb-2 pt-2 border-0">
                        <h4 class="modal-title"></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">×</span></button>
                  </div>
                  <div class="modal-body" style="font-size:.9rem">
                       <div class="row">
                              <div class="col-md-12 form-group">
                                    <label for="">Registar</label>
                                    <select class="form-control select2" id="printable_registrar">

                                    </select>
                              </div>
                       </div>
                       <div class="row">
                              <div class="col-md-12">
                                    <button class="btn btn-primary btn-sm" id="print_grades">Print</button>
                              </div>
                       </div>
                  </div>
            </div>
      </div>
</div>  

<div class="modal fade" id="create_inputperiods_form_modal" style="display: none;" aria-hidden="true">
      <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header pb-2 pt-2 border-0">
                  <h4 class="modal-title">Create Input Period Form</h4>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                  <div class="row">
                        <div class="col-md-12  form-group">
                              <label for="" class="mb-1">Input Period</label>
                              <input class="form-control select2 form-control-sm" id="input_inputperiod">
                        </div>
                  </div> 
                  <div class="row">
                        <div class="col-md-12  form-group">
                              <div class="icheck-primary d-inline mr-3">
                                    <input type="checkbox" id="input_start" class="input_start" >
                                    <label for="input_start">Start <span id="datestartholder"></span></label>
                              </div>
                        </div>
                  </div> 
                  <div class="row" id="input_end_holder" hidden>
                        <div class="col-md-12  form-group">
                              <div class="icheck-primary d-inline mr-3">
                                    <input type="checkbox" id="input_end" class="input_end" >
                                    <label for="input_end">End <span id="dateendholder"></span></label>
                              </div>
                        </div>
                  </div> 
                  <div class="row">
                        <div class="col-md-12">
                              <button class="btn btn-primary btn-sm" id="create_inputperiods_button">Create</button>
                              <button class="btn btn-success btn-sm" id="update_inputperiods_button" hidden>Update</button>
                        </div>
                  </div>
            </div>
          </div>
      </div>
</div>


<div class="modal fade" id="modal_8" style="display: none;" aria-hidden="true">
      <div class="modal-dialog">
            <div class="modal-content">
                  <div class="modal-header pb-2 pt-2 border-0">
                        <h4 class="modal-title">Grade Submission</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">×</span></button>
                  </div>
                  <div class="modal-body " style="font-size:.9rem">
                        <div class="row">
                              <div class="col-md-6 form-group mb-0">
                                          <select name="quarter_select" id="quarter_select" class="form-control form-control-sm">
                                                <option value="">Select Term</option>
                                                <option value="1" >Prelim</option>
                                                <option value="2" >Midterm</option>
                                                <option value="3" >PreFinal</option>
                                                <option value="4" >Final</option>
                                          </select>
                                    <small class="text-danger"><i>Select a term to view and submit grades.</i></small>
                              </div>
                              <div class="col-md-6">
                                    <button class="btn btn-primary float-right btn-sm" id="process_button">Approve</button>
                              </div>
                        </div>
                        <div class="row">
                              <div class="col-md-12 table-responsive tableFixHead" style="height: 422px;">
                                    <table class="table table-sm table-striped table-bordered mb-0 table-head-fixed"  style="font-size:.9rem" width="100%">
                                          <thead>
                                                <tr>
                                                      <th width="5%"><input type="checkbox" disabled checked="checked" class="select_all"> </th>
                                                      <th width="20%">SID</th>
                                                      <th width="60%">Student</th>
                                                      <th width="15%" class="text-centerv">Grade</th>
                                                </tr>
                                          </thead>
                                          <tbody id="datatable_8">

                                          </tbody>
                                    </table>
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
                        <h1>College Grade Completion</h1>
                  </div>
                  <div class="col-sm-6">
                  <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/home">Home</a></li>
                        <li class="breadcrumb-item active">College Grade Completion</li>
                  </ol>
                  </div>
            </div>
      </div>
</section>


<section class="content pt-0">
<div class="container-fluid">
      <div class="row ">
            <div class="col-md-12">
                  <div class="card">
                        <div class="card-body p-3" style="font-size:.9rem !important">
                              <div class="row">
                                    <div class="col-md-2  form-group">
                                          <label for="" class="mb-1">School Year</label>
                                          <select class="form-control select2 form-control-sm" id="filter_sy"></select>
                                    </div>
                                    <div class="col-md-2  form-group">
                                          <label for="" class="mb-1">Semester</label>
                                          <select class="form-control select2 form-control-sm" id="filter_sem"></select>
                                    </div>
                              </div>
                              <hr class="mt-0 mb-1">
                              <div class="row">
                                    <div class="col-md-4  form-group  mb-0">
                                          <label for="" class="mb-1">Teacher</label>
                                          <select class="form-control select2 form-control-sm" id="filter_teacher"></select>
                                    </div>
                                    <div class="col-md-4  form-group  mb-0">
                                          <label for="" class="mb-1">Subject</label>
                                          <select class="form-control select2 form-control-sm" id="filter_subjects" disabled></select>
                                    </div>
                                    <div class="col-md-4  form-group semester_holder mb-0">
                                          <label for="" class="mb-1">Student</label>
                                          <select class="form-control select2 form-control-sm" id="filter_student"></select>
                                    </div>
                              </div>
                        </div>
                  </div>
            </div>
      </div>
      <div class="row">
            <div class="col-md-12">
                  <div class="card">
                        <div class="card-body p-2 pt-1 pb-1">
                              <div class="row">
                                    <div class="col-md-12">
                                          <label class="mb-0">Input Period:</label><span class="ml-2" id="activeInputPeriodHolder"></span>
                                    </div>
                              </div>
                        </div>
                  </div>
            </div>
      </div>
      <div class="row">
            <div class="col-md-12">
                  <div class="card">
                        <div class="card-body p-2 pt-1 pb-1">
                              <div class="row" id="grades_setup_holder" hidden>
                                    <div class="col-md-3" >
                                          <label for="" class="mb-0">Term</label>
                                          <div id="setup_term_holder"></div>
                                    </div>
                                    <div class="col-md-4">
                                          <label for=""  class="mb-0">Final Grade Computation</label>
                                          <div id="setup_fgc_holder"></div>
                                    </div>
                                    <div class="col-md-3" >
                                          <label for="" class="mb-0">Grading Scale</label>
                                          <div id="setup_gs_holder"></div>
                                    </div>
                                    <div class="col-md-2" >
                                          <label for="" class="mb-0">Decimal Places</label>
                                          <div id="setup_dp_holder"></div>
                                    </div>
                              </div>
                        </div>
                  </div>
            </div>
      </div>
      
      <div class="row">
            <div class="col-md-12">
                  <div class="card">
                        <div class="card-body" style="font-size:.9rem !important">
                              <div class="row">
                                    <div class="col-md-6">
                                          <button class="btn btn-primary btn-sm inputperiod"  >Input Period</button>
                                          <a  class="btn btn-default btn-sm disabled ml-2" id="print_grades_to_modal"  ><i class="fas fa-print"></i> Print Grade</a>
                                    </div>
                                    <div class="col-md-6 text-right">
                                          <button id="grade_pending" class="btn btn-warning btn-sm" disabled data-id="3">Pending Grades</button>
                                          <button id="grade_posting" class="btn btn-info btn-sm " disabled data-id="4">Post Grades</button>
                                          <button class="btn btn-primary btn-sm save_grades" disabled>Save Grades</button>
                                    </div>
                              </div>
                              <div class="row mt-2">
                                    <div class="col-md-12">
                                          <table class="table table-sm table-bordered" style="font-size:.7rem !important">
                                                <tr>
                                                      <td class="text-center"  width="7%">SID</td>
                                                      <td width="20%">Student</td>
                                                      <td width="36%">Subject</td>
                                                      <th width="6%" class="text-center term_holder p-0 align-middle" data-term="1">Prelim</th>
                                                      <th width="6%" class="text-center term_holder p-0 align-middle" data-term="2">Midterm</th>
                                                      <th width="6%" class="text-center term_holder p-0 align-middle" data-term="3">PreFi</th>
                                                      <th width="7%" class="text-center term_holder p-0 align-middle" data-term="4">Final Term</th>
                                                      <th width="7%" class="text-center term_holder p-0 align-middle" data-term="5">Final Grade</th>
                                                      <th width="6%" class="text-center term_holder p-0 align-middle" data-term="6">Remarks</th>
                                                </tr>
                                                <tbody id="subject_holder">

                                                </tbody>
                                          </table>
                                    </div>
                              </div>
                              <div class="row">
                                    <div class="col-md-6"></div>
                                    <div class="col-md-6 text-right">
                                          <button class="btn btn-info btn-sm save_grades" disabled>Save Grades</button>
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
<script src="{{asset('plugins/moment/moment.min.js') }}"></script>
<script src="{{asset('plugins/select2/js/select2.full.min.js') }}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.js') }}"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
<script src="{{asset('plugins/datatables-fixedcolumns/js/dataTables.fixedColumns.js') }}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js') }}"></script>

<script>

      var gradesetup = [];

      function getgradesetup(){
            $.ajax({
                  type:'GET',
                  url:'/semester-setup/getactive-setup',
                  async: false,  
                  data:{
                        syid:$('#filter_sy').val(),
                        semid:$('#filter_sem').val(),
                  },
                  success:function(data) {
                        $('#grades_setup_holder').removeAttr('hidden')

                        gradesetup = data
                        if(gradesetup.length == 0){
                              $('#grades_setup_holder')[0].innerHTML = '<div class="col-md-12"><p class="mb-0 text-danger">* No available grade setup.</p></div>'
                        }else{
                              
                              gradesetup = gradesetup[0]
                              
                              var termtext = ''
                              if(gradesetup.prelim == 1){
                                    termtext += '<span class="badge badge-primary ml-1">Prelim</span>'
                              }
                              if(gradesetup.midterm == 1){
                                    termtext += '<span class="badge badge-primary ml-1">Midterm</span>'
                              }
                              if(gradesetup.prefi == 1){
                                    termtext += '<span class="badge badge-primary ml-1">Prefi</span>'
                              }
                              if(gradesetup.final == 1){
                                    termtext += '<span class="badge badge-primary ml-1">Final</span>'
                              }
                              $('#setup_term_holder')[0].innerHTML = termtext
                              $('#setup_fgc_holder').text(gradesetup.f_frontend)
                              $('#setup_dp_holder').text(gradesetup.decimalPoint)


                              if(gradesetup.isPointScaled == 1){
                                    $('#setup_gs_holder').text('Decimal Point Scale ( 1 - 5 )')
                              }else{
                                    $('#setup_gs_holder').text('Numerical Point Scale ( 60 - 100 )')
                              }

                              // $('#grades_setup_holder').remove()
                        }
                        display_columns()
                  }
            })
      }
      
      function display_columns(){
            var disprelim = 0
            var dismidterm = 0
            var disprefi = 0
            var disfinal = 0

            
            $('#quarter_select').select2({
                  allowClear:true,
                  placeholder: "Select Term",
            })

            if(gradesetup != null){
                  disprelim = gradesetup.prelim
                  dismidterm = gradesetup.midterm
                  disprefi = gradesetup.prefi
                  disfinal = gradesetup.final
            }

            if(disprelim == 0){
                  $('#quarter_select option[value="1"]').remove()
                  $('.term_holder[data-term=1]').remove()
            }
            
            if(dismidterm == 0){
                  $('#quarter_select option[value="2"]').remove()
                  $('.term_holder[data-term=2]').remove()
            }
            
            if(disprefi == 0){
                  $('#quarter_select option[value="3"]').remove()
                  $('.term_holder[data-term=3]').remove()
            }
            
            if(disfinal == 0){
                  $('#quarter_select option[value="4"]').remove()
                  $('.term_holder[data-term=4]').remove()
            }
      }

      

      $('#filter_status').select2({
            allowClear:true,
            placeholder: "All",
      })

      $(document).on('click','#grade_posting , #grade_pending',function(){
            $('#quarter_select').val("").change()
            $('.select').attr('disabled','disabled')
            $('#process_button').attr('disabled','disabled')
            $('#process_button').text($(this).text())
            $('#process_button').removeAttr('class')
            $('#process_button').addClass($(this).attr('class'))
            $('#process_button').attr('data-id',$(this).attr('data-id'))
            $('#modal_8').modal()
            $('.select').prop('checked',true)
      })

      $(document).on('change','#quarter_select',function(){
            if($(this).val() != null && $(this).val() != ""){
                  $('#process_button').removeAttr('disabled')
                  $('.select').removeAttr('disabled')
                  $('.select_all').removeAttr('disabled')
                  display_for_posting(all_subjinfo)
            }else{
                  $('#datatable_8').empty()
                  $('#process_button').attr('disabled','disabled')
                  $('.select').attr('disabled','disabled')
                  $('.select_all').attr('disabled','disabled')
            }
      })

      $(document).on('click','#process_button',function(){
            update_status($(this).attr('data-id'))
      })

      $(document).on('click','.select_all',function() {
            if($(this).prop('checked') == true){
                  $('.select').prop('checked',true)
            }else{
                  $('.select').each(function(){
                        if($(this).attr('disabled') == undefined){
                              $(this).prop('checked',false)
                        }
                  })
            }
      })

      function update_status(status){
            var selected = []
            $('.select').each(function(){
                  if($(this).prop('checked') == true && $(this).attr('disabled') == undefined && $(this).attr('data-id') != undefined){
                        selected.push($(this).attr('data-id'))
                  }
            })
            if(selected.length == 0){
                  Toast.fire({
                        type: 'info',
                        title: 'No student selected'
                  })
                  return false
            }

            var url = null

            if(status == 4){
                  url = '/college/grades/post'
            }else if(status == 3){
                  url = '/college/grades/pending/ph'
            }

            var term = ''

            if($('#quarter_select').val() == "1"){
                  term = 'prelemgrade';
            }else if($('#quarter_select').val() == "2"){
                  term = 'midtermgrade';
            }else if($('#quarter_select').val() == "3"){
                  term = 'prefigrade';
            }else if($('#quarter_select').val() == "4"){
                  term = 'finalgrade';
            }

            $.ajax({
                  type:'POST',
                  url: url,
                  data:{
                        syid:$('#filter_sy').val(),
                        semid:$('#filter_sem').val(),
                        term:term,
                        selected:selected,
                  },
                  success:function(data) {
                        if(data[0].status == 1){
                              var message = ''
                              if(status == 4){
                                    message = 'Grades Posted'
                              }else if(status == 3){
                                    message = 'Added to pending'
                                    $('.select').each(function(){
                                          if($(this).prop('checked') == true && $(this).attr('disabled') == undefined && $(this).attr('data-id') != undefined){
                                                $(this).attr('disabled','disabled')
                                          }
                                    })
                                    // display_for_posting()
                              }

                              getsubjects()
                              Toast.fire({
                                    type: 'success',
                                    title: 'Added to pending!'
                              })
                        }else{
                              Toast.fire({
                                    type: 'error',
                                    title: 'Something went wrong!'
                              })
                        }
                  },error:function(){
                        Toast.fire({
                              type: 'error',
                              title: 'Something went wrong!'
                        })
                  }
            })

      }

      

      
      function display_for_posting(data){

            var temp_sid = null
            var temp_studname = null
            var temp_studid = null
            var temp_course = null
            var temp_subjects = data[0].subjects
            var temp_grades = data[0].grades

            $('#datatable_8').empty()
          
            if($('#filter_student').val() != null && $('#filter_student').val() != ""){
                  var temp_studinfo = all_students.filter(x=>x.id == $('#filter_student').val() )
                  
                  if(temp_studinfo.length > 0){
                        temp_sid = temp_studinfo[0].sid
                        temp_studname = temp_studinfo[0].studname
                        temp_studid =  temp_studinfo[0].studid
                        temp_course = temp_studinfo[0].courseid
                  }

                  temp_subjects = temp_subjects.filter(x=>x.studid ==  $('#filter_student').val())

                  $.each(temp_subjects,function(a,b){
                      
                        var input_grade_class_midterm = 'input_grades'
                        var input_grade_class_final = 'input_grades'
                        var grade = ''
                        var is_disabled = ''
                        var temp_dataid = ''
                        
                        var stud_grade = temp_grades.filter(x=>x.studid == b.studid && x.prospectusID == b.id)


                        console.log(stud_grade)

                        if(stud_grade.length > 0){
                              if($('#quarter_select').val() == "1"){
                                    grade = stud_grade[0].prelemgrade != null ? stud_grade[0].prelemgrade : ''
                                    if(stud_grade[0].prelemstatus == $('#process_button').attr('data-id')){
                                          is_disabled = 'disabled="disabled"'
                                    }
                              }else if($('#quarter_select').val() == "2"){
                                    grade = stud_grade[0].midtermgrade != null ? stud_grade[0].midtermgrade : ''
                                    if(stud_grade[0].midtermstatus == $('#process_button').attr('data-id')){
                                          is_disabled = 'disabled="disabled"'
                                    }
                              }
                              else if($('#quarter_select').val() == "3"){
                                    grade = stud_grade[0].prefigrade != null ? stud_grade[0].prefigrade : ''
                                    if(stud_grade[0].prefistatus == $('#process_button').attr('data-id')){
                                          is_disabled = 'disabled="disabled"'
                                    }
                              }
                              else if($('#quarter_select').val() == "4"){
                                    grade = stud_grade[0].finalgrade != null ? stud_grade[0].finalgrade : ''
                                    if(stud_grade[0].finalstatus == $('#process_button').attr('data-id')){
                                          is_disabled = 'disabled="disabled"'
                                    }
                              }

                              temp_dataid = stud_grade[0].id

                            
                              
                        }

                        $('#datatable_8').append('<tr><td><input '+is_disabled+' checked="checked" type="checkbox" class="select" data-studid="'+b.studid+'" data-id="'+temp_dataid+'"></td><td>'+temp_sid+'</td><td>'+temp_studname+'</td>'+
                              '<td class="text-center align-middle '+'" data-term="midtermgrade" data-pid="'+b.id+'" data-studid="'+temp_studid+'" data-course="'+temp_course+'" data-section="'+b.sectionid+'">'+grade+'</td>'+
                              '<td hidden></td>'+
                              '</tr>')    
   
                  })

            }else if($('#filter_teacher').val() != null && $('#filter_teacher').val() != ""){


                  $.each(all_students,function(a,b){

                        temp_sid = b.sid
                        temp_studname = b.studname
                        temp_studid =  b.studid
                        temp_course = b.courseid
                        
                        $.each(temp_subjects.filter(x=>x.studid == b.studid),function(c,d){

                              var grade = ''
                              var is_disabled = ''
                              var temp_dataid = ''
                              var stud_grade = temp_grades.filter(x=>x.studid == d.studid && x.prospectusID == d.id)
                             
                              if(stud_grade.length > 0){

                                    temp_dataid = stud_grade[0].id

                                    if($('#quarter_select').val() == "1"){
                                    grade = stud_grade[0].prelemgrade != null ? stud_grade[0].prelemgrade : ''
                                    if(stud_grade[0].prelemstatus == $('#process_button').attr('data-id')){
                                          is_disabled = 'disabled="disabled"'
                                    }
                              }else if($('#quarter_select').val() == "2"){
                                    grade = stud_grade[0].midtermgrade != null ? stud_grade[0].midtermgrade : ''
                                    if(stud_grade[0].midtermstatus == $('#process_button').attr('data-id')){
                                          is_disabled = 'disabled="disabled"'
                                    }
                              }
                              else if($('#quarter_select').val() == "3"){
                                    grade = stud_grade[0].prefigrade != null ? stud_grade[0].prefigrade : ''
                                    if(stud_grade[0].prefistatus == $('#process_button').attr('data-id')){
                                          is_disabled = 'disabled="disabled"'
                                    }
                              }
                              else if($('#quarter_select').val() == "4"){
                                    grade = stud_grade[0].finalgrade != null ? stud_grade[0].finalgrade : ''
                                    if(stud_grade[0].finalstatus == $('#process_button').attr('data-id')){
                                          is_disabled = 'disabled="disabled"'
                                    }
                              }

                                
                              }

                              $('#datatable_8').append('<tr><td><input '+is_disabled+' checked="checked" type="checkbox" class="select" data-studid="'+b.studid+'" data-id="'+temp_dataid+'"></td><td>'+temp_sid+'</td><td>'+temp_studname+'</td>'+
                                    '<td class="text-center align-middle '+'" data-term="midtermgrade" data-pid="'+d.id+'" data-studid="'+temp_studid+'" data-course="'+temp_course+'" data-section="'+d.sectionid+'">'+grade+'</td>'+
                                    '<td hidden></td>'+
                                    '</tr>')  
                        
                        })

                  })

            }
      }


</script>

<script>
      var all_inputperiods = []
      var selected_inputperiods = null
      // var can_edit = true;

      function evaluate_caninput(){
            if(all_inputperiods.length == 0){
                  can_edit_status = 'Please Create Input Period'
            }else{
                  var check_started_period = all_inputperiods.filter(x=>x.startstatus == 1)
                  if(check_started_period.length == 0){
                        can_edit = true
                        can_edit_status = 'No Active Period'
                  }else{
                        var check_ended_period = check_started_period.filter(x=>x.endstatus == 0)
                        if(check_ended_period.length > 0 ){
                              can_edit = true
                              can_edit_status = 'Please End Active Period'
                        }else{
                              can_edit = true
                        }
                  }
            }
      }

      $(document).on('change','#filter_status',function(){
            if($(this).val() == ""){
                  var tempPeriod = all_inputperiods
            }else if($(this).val() == 1){
                  var tempPeriod = all_inputperiods.filter(x=>x.startstatus == 1 && x.endstatus == 0)
            }else if($(this).val() == 2){
                  var tempPeriod = all_inputperiods.filter(x=>x.startstatus == 0 && x.endstatus == 0)
            }else if($(this).val() == 3){
                  var tempPeriod = all_inputperiods.filter(x=>x.startstatus == 1 && x.endstatus == 1)
            }

            inputperiods_datatable(tempPeriod)
            
      })

      function inputperiodslist(){
          $.ajax({
              type:'GET',
              url:'/college/inputperiods/list',
              data:{
                  syid:$('#filter_sy').val(),
                  semid:$('#filter_sem').val()
              },
              success:function(data) {
                  all_inputperiods = data
                  updateInputPeriodCard()
                  evaluate_caninput()
                  inputperiods_datatable(all_inputperiods)
              },
          })
      }

      function updateInputPeriodCard(){
            if(all_inputperiods.length == 0){
                  $('#activeInputPeriodHolder')[0].innerHTML = 'No available input period. <a href="#" class="inputperiod">Click here</a> to create input period.'
            }else{

                  var checkActive = all_inputperiods.filter(x=>x.startstatus == 1 && x.endstatus == 0)

                  if(checkActive.length  == 0){
                        $('#activeInputPeriodHolder')[0].innerHTML = 'No active input period. <a href="#" class="inputperiod">Click here</a> to activate input period.'
                  }else{
                        $('#activeInputPeriodHolder')[0].innerHTML = checkActive[0].startformat2 + ' - ' + checkActive[0].endformat2
                  }

            }
      }

      function inputperiodscreate(){

            var tempstart = 0 
            var end  = 0

            if($('#input_start').prop('checked') == true){
                  tempstart =1
            }

            if($('#input_end').prop('checked') == true){
                  end =1
            }


            $.ajax({
                  type:'GET',
                  url:'/college/inputperiods/create',
                  data:{
                        date:$('#input_inputperiod').val(),
                        syid:$('#filter_sy').val(),
                        semid:$('#filter_sem').val(),
                        start:tempstart,
                        end:end,
                  },
                  success:function(data) {
                        if(data[0].status == 1){
                              all_inputperiods = data[0].data
                              inputperiods_datatable(all_inputperiods)
                              evaluate_caninput()
                              updateInputPeriodCard()
                              $('#create_inputperiods_form_modal').modal('hide')
                        }

                        Toast.fire({
                              type: data[0].icon,
                              title: data[0].message
                        })
                        
                  },
            })
      }
      function inputperiodsupdate(){

            var tempstart = 0 
            var end  = 0

            if($('#input_start').prop('checked') == true){
                  tempstart =1
            }

            if($('#input_end').prop('checked') == true){
                  end =1
            }


           

            $.ajax({
                  type:'GET',
                  url:'/college/inputperiods/update',
                  data:{
                        date:$('#input_inputperiod').val(),
                        id:selected_inputperiods,
                        syid:$('#filter_sy').val(),
                        semid:$('#filter_sem').val(),
                        start:tempstart,
                        end:end
                  },
                  success:function(data) {
                        if(data[0].status == 1){
                              all_inputperiods = data[0].data
                              updateInputPeriodCard()
                              update_form_display()
                              // evaluate_caninput()
                              // inputperiods_datatable(all_inputperiods)
                              inputperiodslist()
                        }
                        Toast.fire({
                        type: data[0].icon,
                        title: data[0].message
                        })
                  },
            })
      }
      
      function inputperiodsdelete(){
          $.ajax({
              type:'GET',
              url:'/college/inputperiods/delete',
              data:{
                  id:selected_inputperiods
              },
              success:function(data) {
                  if(data[0].status == 1){
                        all_inputperiods = data[0].data
                        inputperiodslist()
                  }
                  Toast.fire({
                      type: data[0].icon,
                      title: data[0].message
                  })
              },
          })
      }

      function update_form_display(){

            var temp_info = all_inputperiods.filter(x=>x.id == selected_inputperiods)

            $('#input_start').prop('checked',false)
            $('#input_end').prop('checked',false)
            $('#input_end_holder').attr('hidden','hidden')
            $('#input_start').removeAttr('disabled')
            $('#input_end').removeAttr('disabled')

            if(temp_info[0].startstatus == 1){
                  $('#input_start').prop('checked',true)
                  $('#input_end_holder').removeAttr('hidden')
                  $('#input_start').attr('disabled','disabled')
                  $('#datestartholder').text(' : '+temp_info[0].startdatetimeformat2)
                  if(temp_info[0].endstatus == 1){
                        $('#input_end').prop('checked',true)
                        $('#input_end').attr('disabled','disabled')
                        $('#update_inputperiods_button').attr('hidden','hidden')
                        $('#dateendholder').text(' : '+temp_info[0].startdatetimeformat2)
                  }
            }

            $('#input_inputperiod').daterangepicker({
                  timePicker: true,
                  startDate: temp_info[0].datestart,
                  endDate: temp_info[0].dateend,
                  locale: {
                        format: 'YYYY-MM-DD HH:mm A'
                  }

            })
            
      }

      function inputperiods_datatable(data){

            $("#inputperiod_datatable").DataTable({
                destroy: true,
                data:data,
                lengthChange : false,
                stateSave: true,
                autoWidth: false,
                columns: [
                        { "data": null },
                      { "data": "startformat2" },
                      { "data": "endformat2" },
                      { "data": null },
                      { "data": null },
                ],
                columnDefs: [
                  {
                    'targets': 0,
                    'orderable': false, 
                    'createdCell':  function (td, cellData, rowData, row, col) {
                              var text = ''
                              if(rowData.startstatus == 0){
                                    text = 'Not Active'
                                    $(td).addClass('bg-danger')
                              }else{
                                    if(rowData.endstatus == 0){
                                          text = 'Active'
                                          $(td).addClass('bg-success')
                                    }else{
                                          text = 'Ended'
                                          $(td).addClass('bg-warning')
                                    }
                              }
                          $(td)[0].innerHTML =  text
                          $(td).addClass('text-center')
                          $(td).addClass('align-middle')
                          
                    }
                  },
                  {
                    'targets': 3,
                    'orderable': false, 
                    'createdCell':  function (td, cellData, rowData, row, col) {
                        if(rowData.startstatus == 1 && rowData.endstatus == 1){
                              $(td).text(null)
                        }else{
                          var buttons = '<a href="javascript:void(0)" class="edit_inputperiods" data-id="'+rowData.id+'"><i class="far fa-edit"></i></a>';
                          $(td)[0].innerHTML =  buttons
                          $(td).addClass('text-center')
                          $(td).addClass('align-middle')
                        }
                          
                    }
                  },
                  {
                    'targets': 4,
                    'orderable': false, 
                    'createdCell':  function (td, cellData, rowData, row, col) {
                        if(rowData.startstatus == 1 && rowData.endstatus == 1){
                              $(td).text(null)
                        }else{
                              if(rowData.startstatus == 0){
                                    var buttons = '<a href="javascript:void(0)" class="delete_inputperiods" data-id="'+rowData.id+'"><i class="far fa-trash-alt text-danger"></i></a>';
                              }else{
                                    buttons = null
                              }
                              
                              $(td)[0].innerHTML =  buttons
                              $(td).addClass('text-center')
                              $(td).addClass('align-middle')
                        }
                    }
                  },
                ],
            });
    
            var label_text = $($('#inputperiod_datatable_wrapper')[0].children[0])[0].children[0]
            $(label_text)[0].innerHTML = '<button class="btn btn-sm btn-primary" id="create_inputperiod_modal_button"><i class="fas fa-plus"></i> Create Input Period</button>'
                        
        }


      $(document).on('change','#inputperiods',function(){
          if($(this).val() != "" && $(this).val() != "create"){
              $('.edit_inputperiods').removeAttr('hidden')
              $('.delete_inputperiods').removeAttr('hidden')
          }else{
              if($(this).val() == "create"){
                  selected_inputperiods = null
                  $('#inputperiods').val("").change()
                  $('#create_inputperiods_button').removeAttr('hidden')
                  $('#update_inputperiods_button').attr('hidden','hidden')
                  $('#inputperiods_form_modal').modal()
              }
              $('.edit_inputperiods').attr('hidden','hidden')
              $('.delete_inputperiods').attr('hidden','hidden')
          }
      })
      
      $(document).on('click','#create_inputperiods_button',function(){

            var tempstart = 0

            if($('#input_start').prop('checked') == true){
                  tempstart =1
            }

            if(tempstart == 1 ){
                  Swal.fire({
                        title: 'Are you sure you want to start input period?',
                        text: "Active input period will automaticaly end.",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Start'
                  }).then((result) => {
                        if (result.value) {
                              inputperiodscreate()
                        }
                  })
            }else{
                  inputperiodscreate()
            }
            
      })

      $(document).on('click','#update_inputperiods_button',function(){

            var tempstart = 0 
            var end  = 0

            if($('#input_start').prop('checked') == true){
                  tempstart =1
            }

            if($('#input_end').prop('checked') == true){
                  end =1
            }

            var tempInfo = all_inputperiods.filter(x=>x.id == selected_inputperiods)
            
            if(tempstart == 1 && tempInfo[0].startstatus == 0){
                  Swal.fire({
                        title: 'Are you sure you want to start input period?',
                        text: "Active input period will automaticaly end.",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Start'
                  }).then((result) => {
                        if (result.value) {
                              inputperiodsupdate()
                        }
                  })
            } else if(end == 1){
                  Swal.fire({
                        title: 'Are you sure you want to end input period?',
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'End'
                  }).then((result) => {
                        if (result.value) {
                              inputperiodsupdate()
                        }
                  })
            }else{
                  inputperiodsupdate()
            }
            
      })

      $(document).on('click','.inputperiod',function(){

          $('#inputperiods_form_modal').modal()
      })

      $(document).on('click','#create_inputperiod_modal_button',function(){
            $('#input_start').removeAttr('disabled')
            $('#input_end').removeAttr('disabled')
            $('#input_start').prop('checked',false)
            $('#input_end').prop('checked',false)
            $('#input_end_holder').attr('hidden','hidden')
            $('#create_inputperiods_button').removeAttr('hidden')
            $('#update_inputperiods_button').attr('hidden','hidden')
            $('#datestartholder').text('')
            $('#dateendholder').text('')
            $('#input_inputperiod').daterangepicker({
                  timePicker: true,
                  locale: {
                        format: 'YYYY-MM-DD HH:mm A'
                  }

            })

            $('#input_inputperiod').removeAttr('disabled')
          $('#create_inputperiods_form_modal').modal()
      })

      $(document).on('click','.update_inputperiods',function(){
            selected_inputperiods = $(this).attr('data-id')
            var temp_info = all_inputperiods.filter(x=>x.id == selected_inputperiods)
            $('#create_inputperiods_form_modal').modal()
      })
      

      $(document).on('click','.delete_inputperiods',function(){
            selected_inputperiods = $(this).attr('data-id')

          Swal.fire({
                  text: 'Are you sure you want to remove input period?',
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Remove'
          }).then((result) => {
                  if (result.value) {
                        inputperiodsdelete()
                  }
          })
      })

      $(document).on('click','.edit_inputperiods',function(){
            $('#input_inputperiod').removeAttr('disabled')
            $('#create_inputperiods_button').attr('hidden','hidden')
            $('#update_inputperiods_button').removeAttr('hidden')
            $('#inputperiods_desc').val($('#classification option:selected').text())

            selected_inputperiods = $(this).attr('data-id')
            var tempInfo = all_inputperiods.filter(x=>x.id == selected_inputperiods)


            if(tempInfo[0].startstatus != 0){
                  $('#input_inputperiod').attr('disabled','disabled')
            }


            update_form_display()
            $('#create_inputperiods_form_modal').modal()
      })
  </script>


<script>
      var sy = @json($sy);
      var sem = @json($semester);
      var all_teachers = []
      var all_students = []
      var all_subjinfo = []
      var first = true;
      var active_sy = sy.filter(x=>x.isactive == 1)[0]
      var active_sem = sem.filter(x=>x.isactive == 1)[0]

      const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                  })

      $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
      });

      function getteachers(){
            $.ajax({
                  type:'GET',
                  url:'/college/completiongrades/getteachers',
                  data:{
                        syid:$("#filter_sy").val(),
                        semid:$("#filter_sem").val()
                  },
                  success:function(data) {
                        all_teachers = data
                        $("#filter_teacher").empty();
                        $("#filter_teacher").append('<option value="">Select Teacher</option>');
                        $("#filter_teacher").select2({
                              data: all_teachers,
                              allowClear: true,
                              placeholder: "Select Teacher",
                        })
                  }
            })
      }

      function getstudents(){

            $('#subject_holder').empty()

            $.ajax({
                  type:'GET',
                  url:'/college/completiongrades/getstudents',
                  data:{
                        syid:$("#filter_sy").val(),
                        semid:$("#filter_sem").val(),
                        teacherid:$('#filter_teacher').val()
                  },
                  success:function(data) {
                        all_students = data

                        var temp_student = $('#filter_student').val()

                        $("#filter_student").empty();
                        $("#filter_student").append('<option value="">Select Student</option>');
                        $("#filter_student").select2({
                              data: all_students,
                              allowClear: true,
                              placeholder: "Select Student",
                        })

                        if(temp_student != null && temp_student != ""){
                              $('#filter_student').val(temp_student).change()
                        }


                        if($('#filter_teacher').val() == "" || $('#filter_teacher').val() == null){
                              return false
                        }

                        getsubjects()
                        // if( ( $('#filter_student').val() != null && $('#filter_student').val() != "" ) || ( $('#filter_teacher').val() != null && $('#filter_teacher').val() != "" )){
                        //       getsubjects()
                        // }
                        
                  }
            })
      }

      $('#filter_subjects').empty()
      $('#filter_subjects').append('<option value="">Subject</option>')
      $('#filter_subjects').select2({
            'data':[],
            'placeholder':'Subject',
            'allowClear':true
      })

      $(document).on('change','#filter_subjects',function(){
            displaysubjects(all_subjinfo)

      })

      function getsubjects(){

            $('#subject_holder').empty()

            $.ajax({
                  type:'GET',
                  url:'/college/completiongrades/getsubjects',
                  data:{
                        syid:$("#filter_sy").val(),
                        semid:$("#filter_sem").val(),
                        teacherid:$('#filter_teacher').val(),
                        studid:$('#filter_student').val()
                  },
                  success:function(data) {
                        $('#subject_holder').empty()
                        all_subjinfo = data

                        $('#filter_subjects').empty()
                        $('#filter_subjects').append('<option value="">Subject</option>')
                        $('#filter_subjects').select2({
                              'data':all_subjinfo[0].subjectload,
                              'placeholder':'Subject',
                              'allowClear':true
                        })
                       
                        displaysubjects(data)
                  }
            })
      }


      var registrar = @json($registrar)

      $('#printable_registrar').select2({
            'data':registrar,
            'placeholder':'Select Registrar'
      })

      function displaysubjects(data){

            var temp_sid = null
            var temp_studname = null
            var temp_studid = null
            var temp_course = null
            var temp_subjects = data[0].subjects
            var temp_grades = data[0].grades

            $('#subject_holder').empty()

            var disprelim = 0
            var dismidterm = 0
            var disprefi = 0
            var disfinal = 0


            if(gradesetup != null){
                  disprelim = gradesetup.prelim
                  dismidterm = gradesetup.midterm
                  disprefi = gradesetup.prefi
                  disfinal = gradesetup.final
            }


            if($('#filter_student').val() != null && $('#filter_student').val() != ""){
                  var temp_studinfo = all_students.filter(x=>x.id == $('#filter_student').val() )
                  
                  if(temp_studinfo.length > 0){
                        temp_sid = temp_studinfo[0].sid
                        temp_studname = temp_studinfo[0].studname
                        temp_studid =  temp_studinfo[0].studid
                        temp_course = temp_studinfo[0].courseid
                  }

                  temp_subjects = temp_subjects.filter(x=>x.studid ==  $('#filter_student').val())
                  var count = 0;

                  $.each(temp_subjects,function(a,b){
                      
                        var midgrades = ''
                        var finalgrades = ''
                        
                        var prelimgrades = ''
                        var prefigrades = ''

                        var prelimstat = '';
                        var midstat = '';
                        var prefistat = '';
                        var finalstat = '';
                        
                        var fg = '';
                        var fgremarks = '';

                        var input_grade_class_midterm = 'input_grades'
                        var input_grade_class_final = 'input_grades'
                        var input_grade_class_prefi = 'input_grades'
                        var input_grade_class_prelim = 'input_grades'
                        
                        var stud_grade = temp_grades.filter(x=>x.studid == b.studid && x.prospectusID == b.id)
                         

                        if(stud_grade.length > 0){
                                    prelimgrades = stud_grade[0].prelemgrade != null ? stud_grade[0].prelemgrade : ''
                                    prefigrades  = stud_grade[0].prefigrade != null ? stud_grade[0].prefigrade : ''
                                    midgrades = stud_grade[0].midtermgrade != null ? stud_grade[0].midtermgrade : ''
                                    finalgrades  = stud_grade[0].finalgrade != null ? stud_grade[0].finalgrade : ''

                                    fg = stud_grade[0].fg != null ? stud_grade[0].fg : ''
                                    fgremarks  = stud_grade[0].fgremarks != null ? stud_grade[0].fgremarks : ''

                                    input_grade_class_midterm = stud_grade[0].midtermclass
                                    input_grade_class_final = stud_grade[0].
                                    
                                    midstat = stud_grade[0].midtermstatus
                                    finalstat = stud_grade[0].finalstatus
                                    prefistat = stud_grade[0].prefistatus
                                    prelimstat = stud_grade[0].finalstatus

                                    var input_grade_class_midterm = stud_grade[0].midtermclass
                                    var input_grade_class_final = stud_grade[0].finalclass
                                    var input_grade_class_prefi = stud_grade[0].preficlass
                                    var input_grade_class_prelim = stud_grade[0].prelemclass
                              }

                              var subject = b.subjCode+' - '+b.subjDesc
                              subject = subject.length > 70 ? 
                                                subject.substring(0, 50 - 3) + "..." : 
                                                subject;
                          
                                                sectionid = b.sectionid
                              
                              count += 1

                              var text = '<tr><td class="text-center">'+temp_sid+'</td><td>'+temp_studname+'</td><td>'+subject+'</td>'

                              if(disprelim == 1){
                                    text += '<td  data-studid="'+b.studid+'" data-section="'+sectionid+'" class="'+input_grade_class_prelim+' text-center grade_td term_holder" data-term="1" data-pid="'+b.id+'"  data-course="'+temp_course+'" data-section="'+b.sectionid+'" data-stat="'+prelimstat+'">'+prelimgrades+'</td>'
                              }

                              if(dismidterm == 1){
                                    text += '<td  data-studid="'+b.studid+'"  data-section="'+sectionid+'" class="'+input_grade_class_midterm+' text-center grade_td term_holder" data-term="2" data-pid="'+b.id+'"  data-course="'+temp_course+'" data-section="'+b.sectionid+'" data-stat="'+midstat+'">'+midgrades+'</td>'
                              }

                              if(disprefi == 1){
                                    text += '<td  data-studid="'+b.studid+'"  data-section="'+sectionid+'" class="'+input_grade_class_prefi+' text-center grade_td term_holder" data-term="3" data-pid="'+b.id+'"  data-course="'+temp_course+'" data-section="'+b.sectionid+'" data-stat="'+prefistat+'">'+prefigrades+'</td>'
                              }

                              if(disfinal == 1){
                                    text += '<td  data-studid="'+b.studid+'"  data-section="'+sectionid+'" class="'+input_grade_class_final+'  text-center grade_td term_holder" data-term="4" data-pid="'+b.id+'"  data-course="'+temp_course+'" data-section="'+b.sectionid+'" data-stat="'+finalstat+'">'+finalgrades+'</td>'
                              }

                              var input_grade_other = input_grade_class_final.replace('input_grades ','');

                              text += '<th  data-studid="'+b.studid+'" data-section="'+sectionid+'" class="'+input_grade_other+' term_holder text-center" data-term="5" data-pid="'+b.id+'"  data-course="'+temp_course+'" data-section="'+b.sectionid+'" data-stat="'+finalstat+'">'+fg+'</th>'

                              text += '<th  data-studid="'+b.studid+'"  data-section="'+sectionid+'" class="'+input_grade_other+' term_holder text-center" data-term="6" data-pid="'+b.id+'"  data-course="'+temp_course+'" data-section="'+b.sectionid+'" data-stat="'+finalstat+'">'+fgremarks+'</th>'

                              text += '</tr>'


                              $('#subject_holder').append(text) 
   
                  })

            }else if($('#filter_teacher').val() != null && $('#filter_teacher').val() != ""){

                  count = 0
                  var d_subjects = temp_subjects

                  if($('#filter_subjects').val() != null && $('#filter_subjects').val() != ""){
                        d_subjects = temp_subjects.filter(x=>x.subjCode == $('#filter_subjects').val())
                  }

                  $.each(all_students,function(a,b){

                        temp_sid = b.sid
                        temp_studname = b.studname
                        temp_studid =  b.studid
                        temp_course = b.courseid

                        

                     
                        $.each(d_subjects.filter(x=>x.studid == b.studid),function(c,d){

                              var midgrades = ''
                              var finalgrades = ''
                              
                              var prelimgrades = ''
                              var prefigrades = ''

                              var prelimstat = '';
                              var midstat = '';
                              var prefistat = '';
                              var finalstat = '';
                              
                              var fg = '';
                              var fgremarks = '';

                              var input_grade_class_midterm = 'input_grades'
                              var input_grade_class_final = 'input_grades'
                              var input_grade_class_prefi = 'input_grades'
                              var input_grade_class_prelim = 'input_grades'

                              var stud_grade = temp_grades.filter(x=>x.studid == d.studid && x.prospectusID == d.id)
                             

                              if(stud_grade.length > 0){
                                    prelimgrades = stud_grade[0].prelemgrade != null ? stud_grade[0].prelemgrade : ''
                                    prefigrades  = stud_grade[0].prefigrade != null ? stud_grade[0].prefigrade : ''
                                    midgrades = stud_grade[0].midtermgrade != null ? stud_grade[0].midtermgrade : ''
                                    finalgrades  = stud_grade[0].finalgrade != null ? stud_grade[0].finalgrade : ''

                                    fg = stud_grade[0].fg != null ? stud_grade[0].fg : ''
                                    fgremarks  = stud_grade[0].fgremarks != null ? stud_grade[0].fgremarks : ''

                                    input_grade_class_midterm = stud_grade[0].midtermclass
                                    input_grade_class_final = stud_grade[0].
                                    
                                    midstat = stud_grade[0].midtermstatus
                                    finalstat = stud_grade[0].finalstatus
                                    prefistat = stud_grade[0].prefistatus
                                    prelimstat = stud_grade[0].finalstatus

                                    var input_grade_class_midterm = stud_grade[0].midtermclass
                                    var input_grade_class_final = stud_grade[0].finalclass
                                    var input_grade_class_prefi = stud_grade[0].preficlass
                                    var input_grade_class_prelim = stud_grade[0].prelemclass
                              }


                              var subject = d.subjCode+' - '+d.subjDesc
                              subject = subject.length > 70 ? 
                                                subject.substring(0, 50 - 3) + "..." : 
                                                subject;

                              sectionid = d.sectionid
                              
                              count += 1

                              var text = '<tr><td class="text-center">'+temp_sid+'</td><td>'+temp_studname+'</td><td>'+subject+'</td>'

                              if(disprelim == 1){
                                    text += '<td  data-studid="'+b.studid+'"data-section="'+sectionid+'" class="'+input_grade_class_prelim+' text-center grade_td term_holder" data-term="1" data-pid="'+d.id+'"  data-course="'+temp_course+'" data-section="'+d.sectionid+'" data-stat="'+prelimstat+'">'+prelimgrades+'</td>'
                              }

                              if(dismidterm == 1){
                                    text += '<td  data-studid="'+b.studid+'"  data-section="'+sectionid+'" class="'+input_grade_class_midterm+' text-center grade_td term_holder" data-term="2" data-pid="'+d.id+'"  data-course="'+temp_course+'" data-section="'+d.sectionid+'" data-stat="'+midstat+'">'+midgrades+'</td>'
                              }

                              if(disprefi == 1){
                                    text += '<td  data-studid="'+b.studid+'"  data-section="'+sectionid+'" class="'+input_grade_class_prefi+' text-center grade_td term_holder" data-term="3" data-pid="'+d.id+'"  data-course="'+temp_course+'" data-section="'+d.sectionid+'" data-stat="'+prefistat+'">'+prefigrades+'</td>'
                              }

                              if(disfinal == 1){
                                    text += '<td  data-studid="'+b.studid+'"  data-section="'+sectionid+'" class="'+input_grade_class_final+'  text-center grade_td term_holder" data-term="4" data-pid="'+d.id+'"  data-course="'+temp_course+'" data-section="'+d.sectionid+'" data-stat="'+finalstat+'">'+finalgrades+'</td>'
                              }

                              var input_grade_other = input_grade_class_final.replace('input_grades ','');

                              text += '<th  data-studid="'+b.studid+'" data-section="'+sectionid+'" class="'+input_grade_other+' term_holder text-center" data-term="5" data-pid="'+d.id+'"  data-course="'+temp_course+'" data-section="'+d.sectionid+'" data-stat="'+finalstat+'">'+fg+'</th>'

                              text += '<th  data-studid="'+b.studid+'"  data-section="'+sectionid+'" class="'+input_grade_other+' term_holder text-center" data-term="6" data-pid="'+d.id+'"  data-course="'+temp_course+'" data-section="'+d.sectionid+'" data-stat="'+finalstat+'">'+fgremarks+'</th>'

                              text += '</tr>'


                              $('#subject_holder').append(text)
                        
                        })

                  })

            }

      }

      $(document).ready(function(){

            $("#filter_sy").empty();
            $("#filter_sy").append('<option value="">Select School Year</option>');
            $("#filter_sy").select2({
                  data: sy,
                  allowClear: true,
                  placeholder: "Select School Year",
            })

            $("#filter_sem").empty();
            // $("#filter_sem").append('<option value="">Select Semester</option>');
            $("#filter_sem").select2({
                  data: sem,
                  allowClear: true,
                  placeholder: "Select Semester",
            })

            $('#filter_sy').val(active_sy.id).change()
            $('#filter_sem').val(active_sem.id).change()

            getteachers()
            getstudents()
            inputperiodslist()
            getgradesetup()

            $(document).on('change','#filter_sy , #filter_sem',function(){
                  can_edit = false
                  $('#grade_pending').attr('disabled','disabled')
                  $('#grade_posting').attr('disabled','disabled')
                  inputperiodslist()
                  getstudents()
                  getteachers()
                  getgradesetup()
            })

            $(document).on('change','#filter_teacher',function(){

                 
                  if($(this).val() != null && $(this).val() != ""){
                        $('#grade_pending').removeAttr('disabled')
                        $('#grade_posting').removeAttr('disabled')
                        $('#filter_student').val("").change()
                        $('#filter_subjects').removeAttr('disabled')
                        getstudents()
                  }else{
                        $('#filter_subjects').attr('disabled','disabled')
                        if( ( $('#filter_student').val() == null || $('#filter_student').val() == "" ) &&  ( $(this).val() == null || $(this).val() == "" )){
                              $('#grade_pending').attr('disabled','disabled')
                              $('#grade_posting').attr('disabled','disabled')
                              getstudents()
                              return false
                        }

                        if($('#filter_student').val() != null && $('#filter_student').val() != ""){
                              getstudents()
                        }
                  }
            })

            $(document).on('click','#print_grades_to_modal',function(){
                  $('#registrar_holder_modal').modal()
            })


            $(document).on('click','#print_grades',function(){
                  print_grades()
            })

            function print_grades() {
                  window.open('/college/grades/summary/print/pdf?semid='+$('#filter_sem').val()+'&syid='+$('#filter_sy').val()+'&studid='+$('#filter_student').val()+'&registrar='+$('#printable_registrar').val(), '_blank');
            }


            $(document).on('change','#filter_student',function(){
                  $('#print_grades_to_modal').addClass('disabled','disabled')
                  $('#print_grades_to_modal').attr('href','#')
                  $('#print_grades_to_modal').removeAttr('target')
                  
                  if($(this).val() != null && $(this).val() != ""){
                        $('#grade_pending').removeAttr('disabled')
                        $('#grade_posting').removeAttr('disabled')
                        $('#print_grades_to_modal').removeClass('disabled')
                        // $('#print_grade').attr('href','/college/grades/summary/print/pdf?semid='+$('#filter_sem').val()+'&syid='+$('#filter_sy').val()+'&studid='+$(this).val())
                        // $('#print_grade').attr('target','_blank')

                        if($('#filter_teacher').val() == null || $('#filter_teacher').val() == ""){
                              getsubjects()
                        }
                        else if(all_subjinfo.length != 0){
                              displaysubjects(all_subjinfo)
                              // display_for_posting(all_subjinfo)
                        }else{
                              getsubjects()
                        }
                  }else{
                        if( ( $('#filter_teacher').val() == null || $('#filter_teacher').val() == "" ) &&  ( $(this).val() == null || $(this).val() == "" )){
                              $('#grade_pending').attr('disabled','disabled')
                              $('#grade_posting').attr('disabled','disabled')
                              getstudents()
                        }
                        else if($('#filter_teacher').val() == null || $('#filter_teacher').val() == ""){
                              getsubjects()
                        }else{
                              if(all_subjinfo.length != 0){
                                    displaysubjects(all_subjinfo)
                                    // display_for_posting(all_subjinfo)
                              }
                        }
                  }
            })

      })



</script>

<script>

      var school = @json($schoolinfo);

      var isSaved = false;
      var isvalidHPS = true;
      var hps = []
      var currentIndex 
      var can_edit = false
      var can_edit_status = ''

      $(document).on('click','.input_grades',function(){
            
            if(!can_edit){
                  Toast.fire({
                        type: 'warning',
                        title: can_edit_status
                  })
            }

            if(currentIndex != undefined){
                  if(isvalidHPS){
                        if(can_edit){
                              string = $(this).text();
                              currentIndex = this;
                              $('#start').length > 0 ? dotheneedful(this) : false
                              $('td').removeAttr('style');
                              $('#start').removeAttr('id')
                              $(this).attr('id','start')
                              $(currentIndex).removeClass('bg-danger')
                              $(currentIndex).removeClass('bg-warning')
                              var start = document.getElementById('start');
                                                start.focus();
                                                start.style.backgroundColor = 'green';
                                                start.style.color = 'white';
                        }
                  }
            }
            else{
                  if(can_edit){
                        string = $(this).text();
                        currentIndex = this;
                        $('#start').length > 0 ? dotheneedful(this) : false
                        $('td').removeAttr('style');
                        $('#start').removeAttr('id')
                        $(this).attr('id','start')
                        $(currentIndex).removeClass('bg-danger')
                        $(currentIndex).removeClass('bg-warning')
                        var start = document.getElementById('start');
                                          start.focus();
                                          start.style.backgroundColor = 'green';
                                          start.style.color = 'white';

                  }
            }
            $('.updated').css("background-color",'#0080005e')
      })


      function dotheneedful(sibling) {
            if (sibling != null) {
                  currentIndex = sibling
                  $(sibling).removeClass('bg-danger')
                  $(sibling).removeClass('bg-warning')
                  if($(start).text() == 'DROPPED'){
                        $(start).addClass('bg-danger')
                  }else if($(start).text() == 'INC' || $(start).attr('data-status') == 3){
                        $(start).addClass('bg-warning')
                  }else if($(start).attr('data-stat') == 3){
                        if(!$(start).hasClass('updated')){
                              $(start).addClass('bg-warning')
                        }
                  }else{
                        start.style.backgroundColor = '';
                  }
                
                  start.style.color = '';
                  sibling.focus();
                  $('.updated').css("background-color",'#0080005e')
                  sibling.style.backgroundColor = 'green';
                  sibling.style.color = 'white';
                  start = sibling;
                  $('#message').empty();
                  string = $(currentIndex)[0].innerText
                  
            }
      }


      document.onkeydown = checkKey;

      function checkKey(e) {

            e = e || window.event;

            if (e.keyCode == '38' && currentIndex != undefined)  {

                  $('.updated').css("background-color",'#0080005e')
                  var idx = start.cellIndex;
                  var nextrow = start.parentElement.previousElementSibling;
                  if(nextrow == null || !$(nextrow.cells[idx]).hasClass('input_grades')){
                        return false;
                  }
                  $('#curText').text(string)
                  var sibling = nextrow.cells[idx];
                  if(sibling == undefined){
                        return false;
                  }
                  string = sibling.innerText;
                  dotheneedful(sibling);
            } else if (e.keyCode == '40' && currentIndex != undefined) {
                  var idx = start.cellIndex;
                  var nextrow = start.parentElement.nextElementSibling;
                  if(nextrow == null || !$(nextrow.cells[idx]).hasClass('input_grades')){
                        return false;
                  }
                  $('#curText').text(string)
                  var sibling = nextrow.cells[idx];
                  if(sibling == undefined){
                        return false;
                  }
                  string = sibling.innerText;
                  dotheneedful(sibling);
            } else if (e.keyCode == '37' && currentIndex != undefined) {
                
                  var sibling = start.previousElementSibling;
                  if(sibling == null || !$(sibling).hasClass('input_grades')){
                        return false;
                  }
                  else if($(sibling)[0].nodeName != "TD" ){
                        return false;
                  }
                  $('#curText').text(string)
                  if($(sibling)[0].cellIndex != 0){
                        string = sibling.innerText;
                        dotheneedful(sibling);
                  }

            } else if (e.keyCode == '39' && currentIndex != undefined) {
                  var sibling = start.nextElementSibling;
                  if(sibling == null || !$(sibling).hasClass('input_grades')){
                        return false;
                  }
                  else if($(sibling)[0].nodeName != "TD" ){
                        return false;
                  }
                  $('#curText').text(string)
                  if($(sibling)[0].cellIndex != 0){
                        string = sibling.innerText;
                        dotheneedful(sibling);
                  }
            }
            else if (e.keyCode == '73' && currentIndex != undefined) {
                  $(currentIndex).text("INC")
                  $(currentIndex).addClass('updated')
                  $('.save_grades').removeAttr('disabled')
                  $('#grade_submit').attr('disabled','disabled')
            }
            else if (e.keyCode == '68' && currentIndex != undefined) {
                  $(currentIndex).text("DROPPED")
                  $(currentIndex).addClass('updated')
                  $('.save_grades').removeAttr('disabled')
                  $('#grade_submit').attr('disabled','disabled')
            }
            else if( e.key == "Backspace" && currentIndex != undefined){
                  string = currentIndex.innerText
                  string = string.slice(0 , -1);

                  if(string.length == 0){
                        string = '';
                        currentIndex.innerText = string
                  }else{
                        currentIndex.innerText = parseInt(string)
                        inputIndex = currentIndex
                  }

                  if(currentIndex.innerText == 'INC' || currentIndex.innerText == 'DROPPED'){
                        string = ''
                  }

                  $(currentIndex).addClass('updated')
                  $('#save_grades').removeAttr('disabled')
                  $('#grade_submit').attr('disabled','disabled')

                  $(currentIndex).text(string)
                  $('#curText').text(string)

                  isstudtext = '[data-pid="'+$(currentIndex).attr('data-pid')+'"]'

                  

                  var temp_studid = $(currentIndex).attr('data-studid')
                  var prelim =  parseFloat($('.grade_td[data-studid="'+temp_studid+'"][data-term="1"]'+isstudtext).text());
                  var midterm =  parseFloat($('.grade_td[data-studid="'+temp_studid+'"][data-term="2"]'+isstudtext).text());
                  var prefi = parseFloat($('.grade_td[data-studid="'+temp_studid+'"][data-term="3"]'+isstudtext).text());
                  var final =  parseFloat($('.grade_td[data-studid="'+temp_studid+'"][data-term="4"]'+isstudtext).text());

                  if(gradesetup.f_frontend != '' || gradesetup.f_frontend != null){

                        var fg = eval(gradesetup.f_frontend).toFixed(gradesetup.decimalPoint)

                        if(!isNaN(fg)){
                              $('th[data-studid="'+temp_studid+'"][data-term="5"]'+isstudtext).text(fg)
                              // if(fg >= gradesetup.passingRate){
                              //       $('th[data-studid="'+temp_studid+'"][data-term="6"]'+isstudtext).text('PASSED')
                              // }else{
                              //       $('th[data-studid="'+temp_studid+'"][data-term="6"]'+isstudtext).text('FAILED')
                              // }


                              if(gradesetup.isPointScaled == 0){
                                    if(fg >= gradesetup.passingRate){
                                          $('th[data-studid="'+temp_studid+'"][data-term="6"]'+isstudtext).text('PASSED')
                                    }else{
                                          $('th[data-studid="'+temp_studid+'"][data-term="6"]'+isstudtext).text('FAILED')
                                    }

                              }else{
                                    if(fg <= gradesetup.passingRate){
                                          $('th[data-studid="'+temp_studid+'"][data-term="6"]'+isstudtext).text('PASSED')
                                    }else{
                                          $('th[data-studid="'+temp_studid+'"][data-term="6"]'+isstudtext).text('FAILED')
                                    }
                              }

                              $('th[data-studid="'+temp_studid+'"][data-term="5"]'+isstudtext).addClass('updated')
                              $('th[data-studid="'+temp_studid+'"][data-term="6"]'+isstudtext).addClass('updated')
                        }else{
                              $('th[data-studid="'+temp_studid+'"][data-term="5"]'+isstudtext).text(null)
                              $('th[data-studid="'+temp_studid+'"][data-term="6"]'+isstudtext).text(null)
                              $('th[data-studid="'+temp_studid+'"][data-term="5"]'+isstudtext).addClass('updated')
                              $('th[data-studid="'+temp_studid+'"][data-term="6"]'+isstudtext).addClass('updated')
                        }
                  }

                  $('#grade_submit').attr('disabled','disabled')
                  $('.save_grades').removeAttr('disabled')
            }
            else if ( ( ( e.key >= 0 && e.key <= 9 ) || e.key == '.' ) && currentIndex != undefined) {

                  if(e.key == '.'){
                        if(gradesetup.decimalPoint == 0){
                              return false
                        }
                        var checkForPoint = string.includes('.')
                        if(checkForPoint){
                              return false
                        }
                  }

                  var check_string = string + e.key;
                  var decimalcount = count_decimal(check_string)

                  
                  
                  if(decimalcount <= gradesetup.decimalPoint){
                        string += e.key;
                  }else{
                        string = string;
                  }


                  if(gradesetup.isPointScaled == 0){
                        if(check_string > 100){
                              string = 100 
                        }
                  }else{
                        if(check_string > 5){
                              return false
                        }
                  }

                  
                  if(currentIndex.innerText == 'INC' || currentIndex.innerText == 'DROPPED'){
                        string = ''
                  }
                  
                  $(currentIndex).addClass('updated')
                  $('#save_grades').removeAttr('disabled')
                  $('#grade_submit').attr('disabled','disabled')

                  $(currentIndex).text(string)
                  $('#curText').text(string)

                  var isstudtext = ''
                  isstudtext = '[data-pid="'+$(currentIndex).attr('data-pid')+'"]'

                  var temp_studid = $(currentIndex).attr('data-studid')
                  
                  var prelim =  parseFloat($('td[data-studid="'+temp_studid+'"][data-term="1"]'+isstudtext).text());
                  var midterm =  parseFloat($('.grade_td[data-studid="'+temp_studid+'"][data-term="2"]'+isstudtext).text());
                  var prefi = parseFloat($('.grade_td[data-studid="'+temp_studid+'"][data-term="3"]'+isstudtext).text());
                  var final =  parseFloat($('.grade_td[data-studid="'+temp_studid+'"][data-term="4"]'+isstudtext).text());

                  if(gradesetup.f_frontend != '' || gradesetup.f_frontend != null){

                        var fg = eval(gradesetup.f_frontend).toFixed(gradesetup.decimalPoint)

                        if(!isNaN(fg)){
                              $('th[data-studid="'+temp_studid+'"][data-term="5"]'+isstudtext).text(fg)
                              $('th[data-studid="'+temp_studid+'"][data-term="5"]'+isstudtext).addClass('updated')
                              $('th[data-studid="'+temp_studid+'"][data-term="6"]'+isstudtext).addClass('updated')

                              if(gradesetup.isPointScaled == 0){
                                    if(fg >= gradesetup.passingRate){
                                          $('th[data-studid="'+temp_studid+'"][data-term="6"]'+isstudtext).text('PASSED')
                                    }else{
                                          $('th[data-studid="'+temp_studid+'"][data-term="6"]'+isstudtext).text('FAILED')
                                    }

                              }else{
                                    if(fg <= gradesetup.passingRate){
                                          $('th[data-studid="'+temp_studid+'"][data-term="6"]'+isstudtext).text('PASSED')
                                    }else{
                                          $('th[data-studid="'+temp_studid+'"][data-term="6"]'+isstudtext).text('FAILED')
                                    }
                              }
                              
                              $('.grade_td[data-studid="'+temp_studid+'"][data-term="6"]'+isstudtext).addClass('updated')
                        }
                        else{
                              $('th[data-studid="'+temp_studid+'"][data-term="5"]'+isstudtext).text('')
                              $('th[data-studid="'+temp_studid+'"][data-term="6"]'+isstudtext).text('')
                              $('th[data-studid="'+temp_studid+'"][data-term="5"]'+isstudtext).addClass('updated')
                              $('th[data-studid="'+temp_studid+'"][data-term="6"]'+isstudtext).addClass('updated')

                        }
                  }

                  $('#grade_submit').attr('disabled','disabled')
                  $('.save_grades').removeAttr('disabled')

            }
      }

      function count_decimal(num) {
            const converted = num.toString();
            if (converted.includes('.')) {
            return converted.split('.')[1].length;
            };
            return 0;
      }

      $(document).on('click','.save_grades',function() {

            $('.save_grades').text('Saving Grades...')
            $('.save_grades').removeClass('btn-primary')
            $('.save_grades').addClass('btn-secondary')
            $('.save_grades').attr('disabled','disabled')

            if( $('.updated[data-term="1"]').length == 0){
                  save_midterm()
            }

            $('.updated[data-term="1"]').each(function(a,b){
                  var studid = $(this).attr('data-studid')
                  var term = $(this).attr('data-term')
                  var courseid = $(this).attr('data-course')
                  var sectionid = $(this).attr('data-section')
                  var pid = $(this).attr('data-pid')
                  var termgrade = $(this).text()
                  var td = $(this)
                  $.ajax({
                        type:'POST',
                        url: '/college/teacher/student/grades/save',
                        data:{
                              syid:$('#filter_sy').val(),
                              semid:$('#filter_sem').val(),
                              term:"prelemgrade",
                              sectionid:sectionid,
                              termgrade:termgrade,
                              studid:studid,
                              courseid:courseid,
                              pid:pid,
                        },
                        success:function(data) {
                              $(td).removeClass('updated')
                              if($(td).attr('data-stat') == 3){
                                    $(td).addClass('bg-warning')
                              }else{
                                    $(td).css("background-color",'white')
                                    $(td).css("color",'black')
                              }
                              if($('.updated[data-term="1"]').length == 0){
                                          save_midterm()
                              }
                        },
                  })
            })


      })


      function save_midterm(){
            if( $('.updated[data-term="2"]').length == 0){
                  save_prefi()
            }
            $('.updated[data-term="2"]').each(function(a,b){
                  var studid = $(this).attr('data-studid')
                  // var term = $(this).attr('data-term')
                  var courseid = $(this).attr('data-course')
                  var sectionid = $(this).attr('data-section')
                  var pid = $(this).attr('data-pid')
                  var termgrade = $(this).text()
                  var td = $(this)
                  $.ajax({
                        type:'POST',
                        url: '/college/teacher/student/grades/save',
                        data:{
                              syid:$('#filter_sy').val(),
                              semid:$('#filter_sem').val(),
                              term:"midtermgrade",
                              sectionid:sectionid,
                              termgrade:termgrade,
                              studid:studid,
                              courseid:courseid,
                              pid:pid,
                        },
                        success:function(data) {
                              $(td).removeClass('updated')
                              if($(td).attr('data-stat') == 3){
                                    $(td).addClass('bg-warning')
                              }else{
                                    $(td).css("background-color",'white')
                                    $(td).css("color",'black')
                              }
                              if($('.updated[data-term="2"]').length == 0){
                                    save_prefi()
                              }
                        }
                  })
            })

      }

      function save_prefi(){
            if( $('.updated[data-term="3"]').length == 0){
                  save_final()
            }
            $('.updated[data-term="3"]').each(function(a,b){
                  var studid = $(this).attr('data-studid')
                  // var term = $(this).attr('data-term')
                  var courseid = $(this).attr('data-course')
                  var sectionid = $(this).attr('data-section')
                  var pid = $(this).attr('data-pid')
                  var termgrade = $(this).text()
                  var td = $(this)
                  $.ajax({
                        type:'POST',
                        url: '/college/teacher/student/grades/save',
                        data:{
                              syid:$('#filter_sy').val(),
                              semid:$('#filter_sem').val(),
                              term:"prefigrade",
                              sectionid:sectionid,
                              termgrade:termgrade,
                              studid:studid,
                              courseid:courseid,
                              pid:pid,
                        },
                        success:function(data) {
                              $(td).removeClass('updated')
                              if($(td).attr('data-stat') == 3){
                                    $(td).addClass('bg-warning')
                              }else{
                                    $(td).css("background-color",'white')
                                    $(td).css("color",'black')
                              }
                              if($('.updated[data-term="3"]').length == 0){
                                    save_final()
                              }
                        }
                  })
            })

      }

      function save_final(){
            if( $('.updated[data-term="4"]').length == 0){
                  save_fg()
            }
            $('.updated[data-term="4"]').each(function(a,b){
                  var studid = $(this).attr('data-studid')
                  // var term = $(this).attr('data-term')
                  var courseid = $(this).attr('data-course')
                  var sectionid = $(this).attr('data-section')
                  var pid = $(this).attr('data-pid')
                  var termgrade = $(this).text()
                  var td = $(this)
                  $.ajax({
                        type:'POST',
                        url: '/college/teacher/student/grades/save',
                        data:{
                              syid:$('#filter_sy').val(),
                              semid:$('#filter_sem').val(),
                              term:"finalgrade",
                              sectionid:sectionid,
                              termgrade:termgrade,
                              studid:studid,
                              courseid:courseid,
                              pid:pid,
                        },
                        success:function(data) {
                              $(td).removeClass('updated')
                              if($(td).attr('data-stat') == 3){
                                    $(td).addClass('bg-warning')
                              }else{
                                    $(td).css("background-color",'white')
                                    $(td).css("color",'black')
                              }
                              if($('.updated[data-term="4"]').length == 0){
                                    save_final()
                              }
                        }
                  })
            })
      }


      function save_fg(){
            if( $('.updated[data-term="5"]').length == 0){
                  save_fgremarks()
            }
            $('.updated[data-term="5"]').each(function(a,b){
                  var studid = $(this).attr('data-studid')
                  // var term = $(this).attr('data-term')
                  var courseid = $(this).attr('data-course')
                  var sectionid = $(this).attr('data-section')
                  var pid = $(this).attr('data-pid')
                  var termgrade = $(this).text()
                  var td = $(this)
                  $.ajax({
                        type:'POST',
                        url: '/college/teacher/student/grades/save',
                        data:{
                              syid:$('#filter_sy').val(),
                              semid:$('#filter_sem').val(),
                              term:"fg",
                              sectionid:sectionid,
                              termgrade:termgrade,
                              studid:studid,
                              courseid:courseid,
                              pid:pid,
                        },
                        success:function(data) {
                              $(td).removeClass('updated')
                              if($(td).attr('data-stat') == 3){
                                    $(td).addClass('bg-warning')
                              }else{
                                    $(td).css("background-color",'white')
                                    $(td).css("color",'black')
                              }
                              if($('.updated[data-term="5"]').length == 0){
                                    save_fg()
                              }
                        }
                  })
            })

      }

      function save_fgremarks(){
            if( $('.updated[data-term="6"]').length == 0){
                  Toast.fire({
                        type: 'success',
                        title: 'Saved Successfully!'
                  })
                  $('.save_grades').attr('disabled','disabled')
                  $('.save_grades').removeClass('btn-secondary')
                  $('.save_grades').addClass('btn-primary')
                  $('.save_grades').text('Save Grades')
                  $('.grade_submit').removeAttr('disabled')
            }
            $('.updated[data-term="6"]').each(function(a,b){
                  var studid = $(this).attr('data-studid')
                  // var term = $(this).attr('data-term')
                  var courseid = $(this).attr('data-course')
                  var sectionid = $(this).attr('data-section')
                  var pid = $(this).attr('data-pid')
                  var termgrade = $(this).text()
                  var td = $(this)
                  $.ajax({
                        type:'POST',
                        url: '/college/teacher/student/grades/save',
                        data:{
                              syid:$('#filter_sy').val(),
                              semid:$('#filter_sem').val(),
                              term:"fgremarks",
                              sectionid:sectionid,
                              termgrade:termgrade,
                              studid:studid,
                              courseid:courseid,
                              pid:pid,
                        },
                        success:function(data) {
                              $(td).removeClass('updated')
                              if($(td).attr('data-stat') == 3){
                                    $(td).addClass('bg-warning')
                              }else{
                                    $(td).css("background-color",'white')
                                    $(td).css("color",'black')
                              }
                              if($('.updated[data-term="finalgrade"]').length == 0){
                                    Toast.fire({
                                          type: 'success',
                                          title: 'Saved Successfully!'
                                    })
                                    $('.save_grades').attr('disabled','disabled')
                                    $('.save_grades').removeClass('btn-secondary')
                                    $('.save_grades').addClass('btn-primary')
                                    $('.save_grades').text('Save Grades')
                                    $('.grade_submit').removeAttr('disabled')
                              }
                        }
                  })
            })

      }


</script>


@endsection


