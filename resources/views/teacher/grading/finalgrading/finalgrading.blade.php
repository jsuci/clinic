
@extends('teacher.layouts.app')

@section('pagespecificscripts')
      <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
      <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
      <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
      <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
     
@endsection


@section('content')

<section class="content-header">
      <div class="container-fluid">
            <div class="row">
                  <div class="col-sm-6">
                  
                  </div>
                  <div class="col-sm-6">
                  <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/home">Home</a></li>
                        <li class="breadcrumb-item active">Final Grading</li>
                  </ol>
                  </div>
            </div>
      </div>
</section>
    
<section class="content pt-0">
      <div class="container-fluid">
            <div class="row">
                  <div class="col-md-12">
                        <div class="card">
                              <div class="card-body">
                                    <div class="row">
                                          <div class="col-md-3">
                                              <label for="">SCHOOL YEAR</label>
                                              <select name="syid" id="input_syid" class="form-control select2">
                                                  @foreach(DB::table('sy')->select('id','sydesc','isactive')->get() as $item)
                                                      @if($item->isactive == 1)
                                                          <option value="{{$item->id}}" selected="selected">{{$item->sydesc}}</option>
                                                      @else
                                                          <option value="{{$item->id}}">{{$item->sydesc}}</option>
                                                      @endif
                                                  @endforeach
                                              </select>
                                          </div>
                                          <div class="col-md-3">
                                              <div class="form-group">
                                                  <label for="">Grade Level</label>
                                                  <select class="form-control select2" id="input_gradelevel">
                                                      <option selected value="" >Grade Level</option>
                                                  </select>
                                              </div>
                                          </div>
                                          <div class="col-md-3">
                                              <div class="form-group">
                                                  <label for="">Section</label>
                                                  <select name="section" id="input_section" class="form-control select2">
                                                      <option selected value="">Section</option>
                                                  </select>
                                              </div>
                                          </div>
                                          <div class="col-md-3" id="semester_holder" hidden>
                                                <label for="">Semester</label>
                                                <select name="semester" id="input_semester" class="form-control select2">
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
                                    <div class="row">
                                          <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">Subject</label>
                                                    <select class="form-control select2" id="input_subject">
                                                        <option selected value="" >Subject</option>
                                                    </select>
                                                </div>
                                          </div>
                                          <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="">Quarter</label>
                                                    <select class="form-control select2" id="input_quarter">
                                                        <option selected value="" >Quarter</option>
                                                        <option  value="1" >1st Quarter</option>
                                                        <option  value="2" >2nd Quarter</option>
                                                        <option  value="3" >3rd Quarter</option>
                                                        <option  value="4" >4th Quarter</option>
                                                    </select>
                                                </div>
                                          </div>
                                    </div>
                                    <div class="row">
                                          <div class="col-md-6">
                                                <button class="btn btn-primary" id="button_filter">Filter</button>
                                          </div>
                                         
                                          
                                    </div>
                                    <div class="row mt-4" >
                                          <div  class=" col-md-6 table-responsive" style="height: 400px" id="grade_holder">
                                                
                                          </div>
                                          <div class="col-md-6" id="status_holder" hidden>
                                                <div class="row">
                                                      <div class="col-md-12">
                                                            <label for="" class="pl-2">Grade Information</label>
                                                            <table class="table" width="100%">
                                                                 
                                                                  <tr>
                                                                        <th width="50%">Status</th>
                                                                        <td width="50%" id="label_status">Not Submitted</td>
                                                                  </tr>
                                                                  {{-- <tr>
                                                                        <th width="50%" >Date Submitted</th>
                                                                        <td width="50%" id="label_datesubmitted"></td>
                                                                  </tr>
                                                                  <tr>
                                                                        <th width="50%">Date Posted</th>
                                                                        <td width="50%" id="label_dateposted"></td>
                                                                  </tr> --}}
                                                            </table>
                                                      </div>
                                                      <div class="col-md-12 mt-2">
                                                            <button class="btn btn-primary" id="button_generate_gradedetail" hidden>GENERATE STUDENT GRADE DETAIL</button>
                                                      </div>
                                                      <div class="col-md-12 mt-2">
                                                            <button class="btn btn-primary" id="button_generate_gradestatus" hidden>GENERATE GRADE STATUS</button>
                                                      </div>
                                                      <div class="col-md-12 mt-2">
                                                            <button class="btn btn-primary" id="buton_submit_grades" hidden>SUBMIT GRADES</button>
                                                      </div>
                                                      <div class="col-md-12 mt-2">
                                                            Process Count: <span id="p_count"></button>
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
      <script src="{{asset('plugins/select2/js/select2.full.min.js') }}"></script>
      <script src="{{asset('plugins/datatables/jquery.dataTables.js') }}"></script>
      <script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>


      <script>
            $(document).ready(function(){

                  get_advisory()
                  var selected_gradelevel = null

                  function get_advisory(){
                        var syid = $('#input_syid').val()
                        var semid = $('#input_semester').val()
                        $.ajax({
                              type:'GET',
                              url:'/teacher/section/all',
                              data:{
                                    syid:syid,
                              },
                              success:function(data) {
                                    sections = data;
                                    all_subject = data;
                                    var arrayUniqueByKey = [...new Map(sections.map(item =>
                                    [item['levelid'], item])).values()];
                                    $.each(arrayUniqueByKey,function(a,b){
                                          $('#input_gradelevel').append('<option value="'+b.levelid+'">'+b.levelname+'</option>')
                                    })
                              }
                        })
                  }

                  $(document).on('change','#input_syid',function(){
                        get_advisory()
                   })

                  $(document).on('change','#input_section',function(){
                        if(selected_gradelevel == 14 || selected_gradelevel == 15){
                              var temp_subj = all_subject.filter(x=>x.sectionid == $('#input_section').val() && x.semid == $('#input_semester').val())
                        }else{
                              var temp_subj = all_subject.filter(x=>x.sectionid == $('#input_section').val())
                        }
                        $('#input_subject').empty()
                        $('#input_subject').append('<option value="">Subject</option>')
                        $.each(temp_subj,function(a,b){
                              $('#input_subject').append('<option value="'+b.subjid+'">'+b.subjcode+' - '+ b.subjdesc+'</option>')
                        })
                        $('#button_generate_gradedetail').attr('hidden','hidden')
                        $('#grade_holder').empty()
                        $('#status_holder').attr('hidden','hidden')
                  })

                  $(document).on('change','#input_semester',function(){
                       
                        $('#button_generate_gradedetail').attr('hidden','hidden')
                        $('#grade_holder').empty()
                        $('#status_holder').attr('hidden','hidden')

                        $('#input_section').empty()
                        $('#input_section').append('<option value="">Section</option>')
                        
                        arrayUniqueByKey = sections.filter(x=>x.semid == $('#input_semester').val())
                        
                        var arrayUniqueByKey = [...new Map(arrayUniqueByKey.map(item =>
                                    [item['sectionid'], item])).values()];
                        
                        $.each(arrayUniqueByKey,function(a,b){
                              if(b.levelid == selected_gradelevel){
                                    $('#input_section').append('<option value="'+b.sectionid+'">'+b.sectionname+'</option>')
                              }
                        })

                        $('#input_subject').empty()
                        $('#input_subject').append('<option value="">Subject</option>')

                        if(selected_gradelevel == 14 || selected_gradelevel == 15){
                              $('#input_quarter').empty()
                              $('#input_quarter').append('<option value="">Quarter</option>')
                              if($('#input_semester').val() == 1){
                                    $('#input_quarter').append('<option value="1">1st Quarter</option>')
                                    $('#input_quarter').append('<option value="2">2nd Quarter</option>')
                              }else if($('#input_semester').val() == 2){
                                    $('#input_quarter').append('<option value="3">3rd Quarter</option>')
                                    $('#input_quarter').append('<option value="4">4th Quarter</option>')
                              }
                        }else{
                              $('#input_quarter').append('<option value="">Quarter</option>')
                              $('#input_quarter').empty()
                              $('#input_quarter').append('<option value="1">1st Quarter</option>')
                              $('#input_quarter').append('<option value="2">2nd Quarter</option>')
                              $('#input_quarter').append('<option value="3">3rd Quarter</option>')
                              $('#input_quarter').append('<option value="4">4th Quarter</option>')
                        }

                  })

                  $(document).on('change','#input_gradelevel',function(){
                        $('#input_section').empty()
                        selected_gradelevel = $(this).val()

                        $('#input_section').append('<option value="">Section</option>')
                        var arrayUniqueByKey = [...new Map(sections.map(item =>
                                    [item['sectionid'], item])).values()];

                        if(selected_gradelevel == 14 || selected_gradelevel == 15){
                              arrayUniqueByKey = arrayUniqueByKey.filter(x=>x.semid == $('#input_semester').val())
                        }

                        $.each(arrayUniqueByKey,function(a,b){
                              if(b.levelid == selected_gradelevel){
                                    $('#input_section').append('<option value="'+b.sectionid+'">'+b.sectionname+'</option>')
                              }
                        })

                        if(selected_gradelevel == 14 || selected_gradelevel == 15){
                              $('#semester_holder').removeAttr('hidden')
                        }else{
                              $('#semester_holder').attr('hidden','hidden')
                        }

                        if(selected_gradelevel == 14 || selected_gradelevel == 15){
                              var temp_subj = all_subject.filter(x=>x.sectionid == $('#input_section').val() && x.semid == $('#input_semester').val())
                        }else{
                              var temp_subj = all_subject.filter(x=>x.sectionid == $('#input_section').val())
                        }

                        $('#input_subject').empty()
                        $('#input_subject').append('<option value="">Subject</option>')
                        $.each(temp_subj,function(a,b){
                              $('#input_subject').append('<option value="'+b.subjid+'">'+b.subjcode+' - '+ b.subjdesc+'</option>')
                        })
                        $('#button_generate_gradedetail').attr('hidden','hidden')
                        $('#grade_holder').empty()
                        $('#status_holder').attr('hidden','hidden')

                       
                        if(selected_gradelevel == 14 || selected_gradelevel == 15){
                              $('#input_quarter').empty()
                              $('#input_quarter').append('<option value="">Quarter</option>')
                              if($('#input_semester').val() == 1){
                                    $('#input_quarter').append('<option value="1">1st Quarter</option>')
                                    $('#input_quarter').append('<option value="2">2nd Quarter</option>')
                              }else if($('#input_semester').val() == 2){
                                    $('#input_quarter').append('<option value="3">3rd Quarter</option>')
                                    $('#input_quarter').append('<option value="4">4th Quarter</option>')
                              }
                        }else{
                              $('#input_quarter').append('<option value="">Quarter</option>')
                              $('#input_quarter').empty()
                              $('#input_quarter').append('<option value="1">1st Quarter</option>')
                              $('#input_quarter').append('<option value="2">2nd Quarter</option>')
                              $('#input_quarter').append('<option value="3">3rd Quarter</option>')
                              $('#input_quarter').append('<option value="4">4th Quarter</option>')
                        }
                     
                  })

                  var selectedStudentge
                  var evalaction

                  $(document).on('click','#button_filter',function(){
                        load_grades()
                  })
            
			var gradestatus = null
			var submitted = null
			var levelid = null
									
			$(document).on('click','#buton_submit_grades',function(){
                  $.ajax({
                        type:'GET',
                        url: '/teacher/grading/final/grade/submit',
                        data:{
                              'id': gradestatus,
                              'levelid': levelid,
                        },
                        success:function(data) {
                              load_grades()
                        }
                  })
            })
			
			function load_grades(){
				var temp_acadprogid = sections.filter(x=>x.sectionid == $('#input_section').val())
                        $('#grade_holder').empty()
                        $('#status_holder').attr('hidden','hidden')
                        $('#button_generate_gradedetail').attr('hidden','hidden')
                        $('#buton_submit_grades').attr('hidden','hidden')
                        $('#button_generate_gradestatus').attr('hidden','hidden')

                        var syid = $('#input_syid').val();
                        var semid = $('#input_semester').val();
                        if(temp_acadprogid != 5){
                              semid = 1
                        }
                      
                        $.ajax({
                              type:'GET',
                              url:'/teacher/grading/final/get/grades',
                              data:{
                                    syid:syid,
                                    semid:semid,
                                    section:$('#input_section').val(),
                                    subject:$('#input_subject').val(),
                                    quarter:$('#input_quarter').val(),
                                    gradelevel:$('#input_gradelevel').val(),
                                    acadprogid:temp_acadprogid[0].acadprogid
                              },
                              success:function(data) {
                                    $('#grade_holder').empty()
                                    $('#grade_holder').append(data)
                                    $('#status_holder').removeAttr('hidden')
                                    $('#label_status').text('')
                                    $('#label_datesubmitted').text('')
                                    $('#label_dateposted').text('')
									
									gradestatus = $('.gradestable').attr('data-id')
									levelid = $('.gradestable').attr('data-levelid')

                              }
                        })
				
			}
    
            

            
      })
</script>


@endsection


