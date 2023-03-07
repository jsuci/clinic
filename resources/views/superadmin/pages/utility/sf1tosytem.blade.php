
@php
      if(auth()->user()->type == 17){
            $extend = 'superadmin.layouts.app2';
      }else if(auth()->user()->type == 2 || Session::get('currentPortal') == 2){
            $extend = 'principalsportal.layouts.app2';
      }else if(auth()->user()->type == 3 || Session::get('currentPortal') == 3){
            $extend = 'registrar.layouts.app';
      }else if(auth()->user()->type == 6 || Session::get('currentPortal') == 6){
            $extend = 'adminPortal.layouts.app2';
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
                  border: 0 !important;
            }
     
      </style>
@endsection


@section('content')

@php
   $sf1_column = DB::table('sf1_column')->get();

   $gradelevel = DB::table('gradelevel')
                        ->where('deleted',0)
                        ->orderBy('sortid')
                        ->select(
                              'gradelevel.levelname',
                              'gradelevel.id',
                              'acadprogid'
                        )
                        ->get(); 

      $strand = DB::table('sh_strand')
                        ->where('deleted',0)
                        ->select(
                              'sh_strand.strandcode',
                              'sh_strand.strandname',
                              'sh_strand.id'
                        )
                        ->where('active',1)
                        ->get(); 

      $college_course = DB::table('college_courses')
                        ->where('deleted',0)
                        ->select(
                              'college_courses.courseDesc',
                              'college_courses.id',
                              'college_courses.courseabrv'
                        )
                        ->get(); 
@endphp


<div class="modal fade" id="modal_1" style="display: none;" aria-hidden="true">
      <div class="modal-dialog">
            <div class="modal-content">
                  <div class="modal-header pb-2 pt-2 border-0">
                        <h4 class="modal-title" id="modal_1_title"></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                  </div>
                  <div class="modal-body" style="font-size:12px !important">
                        <table class="table table-sm display table-striped" id="student_list" width="100%" ">
                              <thead>
                                    <tr>
                                          <th width="100%">Student Name</th>
                                    </tr>
                              </thead>
                        </table>
                  </div>
            </div>
      </div>
</div>   

<section class="content-header">
      <div class="container-fluid">
            <div class="row mb-2">
                  <div class="col-sm-6">
                        <h1>SF1 to System</h1>
                  </div>
                  <div class="col-sm-6">
                  <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/home">Home</a></li>
                        <li class="breadcrumb-item active">SF1 to System</li>
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
                                          <div class="col-md-2">
                                                <button class="btn btn-sm btn-primary btn-block" id="savecolumn">Save Columns</button>
                                          </div>
                                          <div class="col-md-2">
                                                <select name="col_filter" id="col_filter" class="form-control form-control-sm">
                                                      <option value="1" selected="selected">Setup 1</option>
                                                      <option value="2">Setup 2</option>
                                                      <option value="3">Setup 3</option>
                                                      <option value="4">Setup 4</option>
                                                      <option value="5">Setup 5</option>
                                                </select>
                                          </div>
                                   </div>
                                   <div class="row mt-2">
                                         <div class="col-md-12">
                                                <table class="table table-sm table-bordered" style="font-size: .8rem !important">
                                                      <thead>
                                                            <tr>
                                                                  <th width="8%" class="align-middle">Column</th>
                                                                  <th width="7%" class="text-center">LRN</th>
                                                                  <th width="7%"  class="text-center">Name</th>
                                                                  <th width="7%"  class="text-center">Gender</th>
                                                                  <th width="7%"  class="text-center">DOB</th>
                                                                  <th width="7%"  class="text-center">Street</th>
                                                                  <th width="7%"  class="text-center">Barangay</th>
                                                                  <th width="7%" class="text-center">City</th>
                                                                  <th width="7%" class="text-center">Province</th>
                                                                  <th width="7%" class="text-center">FName</th>
                                                                  <th width="7%" class="text-center">MName</th>
                                                                  <th width="7%" class="text-center">GName</th>
                                                                  <th width="7%" class="text-center">GRelation</th>
                                                                  <th width="7%" class="text-center">Contact#</th>
                                                            </tr>
                                                      </thead>
                                                      <tbody>
                                                            <tr>
                                                                  <th class="align-middle">Letter</th>
                                                                  <td><input id="lrn" class="form-control form-control-sm column" onkeyup="this.value = this.value.toUpperCase();"></td>
                                                                  <td><input id="name" class="form-control form-control-sm column"  onkeyup="this.value = this.value.toUpperCase();"></td>
                                                                  <td><input id="gender" class="form-control form-control-sm column" onkeyup="this.value = this.value.toUpperCase();"></td>
                                                                  <td><input id="dob" class="form-control form-control-sm column" onkeyup="this.value = this.value.toUpperCase();"></td>
                                                                  <td><input id="street" class="form-control form-control-sm column" onkeyup="this.value = this.value.toUpperCase();"></td>
                                                                  <td><input id="barangay" class="form-control form-control-sm column" onkeyup="this.value = this.value.toUpperCase();"></td>
                                                                  <td><input id="city" class="form-control form-control-sm column" onkeyup="this.value = this.value.toUpperCase();"></td>
                                                                  <td><input id="province" class="form-control form-control-sm column" onkeyup="this.value = this.value.toUpperCase();"></td>
                                                                  <td><input id="fname" class="form-control form-control-sm column" onkeyup="this.value = this.value.toUpperCase();"></td>
                                                                  <td><input id="mname" class="form-control form-control-sm column" onkeyup="this.value = this.value.toUpperCase();"></td>
                                                                  <td><input id="gname" class="form-control form-control-sm column" onkeyup="this.value = this.value.toUpperCase();"></td>
                                                                  <td><input id="grelation" class="form-control form-control-sm column" onkeyup="this.value = this.value.toUpperCase();"></td>
                                                                  <td><input id="contact" class="form-control form-control-sm column" onkeyup="this.value = this.value.toUpperCase();"></td>
                                                            </tr>
                                                      </tbody>
                                                </table>
                                         </div>
                                   </div>
                                   <div class="row">
                                    <div class="col-md-12">
                                           <table class="table table-sm table-bordered" style="font-size: .8rem !important">
                                                 <thead>
                                                       <tr>
                                                             <th width="8%" class="align-middle">Column</th>
                                                             <th width="7%" class="text-center align-middel" style="font-size:.6rem !important">Mother Tongue</th>
                                                             <th width="7%"  class="text-center align-middel" style="font-size:.6rem !important">Ethnic Group</th>
                                                             <th width="7%"  class="text-center align-middel" style="font-size:.6rem !important">Religion</th>
                                                             <th width="7%"  class="text-center"></th>
                                                             <th width="7%"  class="text-center"></th>
                                                             <th width="7%"  class="text-center"></th>
                                                             <th width="7%" class="text-center"></th>
                                                             <th width="7%" class="text-center"></th>
                                                             <th width="7%" class="text-center"></th>
                                                             <th width="7%" class="text-center"></th>
                                                             <th width="7%" class="text-center"></th>
                                                             <th width="7%" class="text-center"></th>
                                                             <th width="7%" class="text-center"></th>
                                                            
                                                       </tr>
                                                 </thead>
                                                 <tbody>
                                                       <tr>
                                                             <th class="align-middle">Letter</th>
                                                             <td><input id="mothertongue" class="form-control form-control-sm column" onkeyup="this.value = this.value.toUpperCase();"></td>
                                                             <td><input id="ethnicgroup" class="form-control form-control-sm column"  onkeyup="this.value = this.value.toUpperCase();"></td>
                                                             <td><input id="religion" class="form-control form-control-sm column" onkeyup="this.value = this.value.toUpperCase();"></td>
                                                             <th width="7%"  class="text-center"></th>
                                                             <th width="7%"  class="text-center"></th>
                                                             <th width="7%"  class="text-center"></th>
                                                             <th width="7%" class="text-center"></th>
                                                             <th width="7%" class="text-center"></th>
                                                             <th width="7%" class="text-center"></th>
                                                             <th width="7%" class="text-center"></th>
                                                             <th width="7%" class="text-center"></th>
                                                             <th width="7%" class="text-center"></th>
                                                             <th width="7%" class="text-center"></th>
                                                       </tr>
                                                 </tbody>
                                           </table>
                                    </div>
                              </div>
                                    <form 
                                          action="/sf1/tosystem/upload" 
                                          id="upload_sf1" 
                                          method="POST" 
                                          enctype="multipart/form-data"
                                          >
                                    @csrf
                                          <div class="row">
                                                <div class="col-md-6">
                                                      {{-- <div class="progress">
                                                            <div class="progress-bar progress-xs progress-bar-success progress-bar-stripedactive" role="progressbar"
                                                                  aria-valuemin="0" aria-valuemax="100" style="width:0%" id="upload_progress" aria-valuenow="60">
                                                                        0%
                                                            </div>
                                                      </div> --}}
                                                      <div class="progress progress-sm active">
                                                            <div class="progress-bar bg-success progress-bar-striped" id="upload_progress" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                                            <span class="sr-only"></span>
                                                            </div>
                                                      </div>
                                                      <div class="input-group input-group-sm">
                                                            <input type="file" class="form-control" name="input_sf1" id="input_sf1">
                                                            <span class="input-group-append">
                                                            <button class="btn btn-primary btn-flat" id="upload_ecr_button" >Extract Information</button>
                                                            </span>
                                                      </div>
                                                      
                                                </div>
                                          </div>
                                    </form>
                                    <div class="row mt-2">
                                          <div class="col-md-12" style="font-size: .7rem !important">
                                                <table class="table table-sm table-bordered" width="100%"  id="student_checker" >
                                                      <thead>
                                                            <tr>
                                                                  <th width="5%" class="text-center"></th>
                                                                  <th width="5%" class="text-center">LRN</th>
                                                                  <th width="9%"  class="text-center">Last Name</th>
                                                                  <th width="5%"  class="text-center">First Name</th>
                                                                  <th width="5%"  class="text-center">Middle Name</th>
                                                                  <th width="7%"  class="text-center">Suffix</th>
                                                                  <th width="7%"  class="text-center">Gender</th>
                                                                  <th width="7%"  class="text-center">DOB</th>

                                                                  <th width="5%"  class="text-center">Mother Toungue</th>
                                                                  <th width="5%"  class="text-center">Ethnic Group</th>
                                                                  <th width="5%" class="text-center">Religion</th>

                                                                  <th width="5%"  class="text-center">Street</th>
                                                                  <th width="5%"  class="text-center">Barangay</th>
                                                                  <th width="5%" class="text-center">City</th>
                                                                  <th width="5%" class="text-center">Province</th>
                                                                  <th width="5%" class="text-center">FName</th>
                                                                  <th width="5%" class="text-center">MName</th>
                                                                  <th width="5%" class="text-center">GName</th>
                                                                  <th width="6%" class="text-center">GRelation</th>
                                                                  <th width="6%" class="text-center">Contact#</th>
                                                            </tr>
                                                      </thead>
                                                     
                                                </table>
                                          </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                          <div class="col-md-3 form-group">
                                                <label for="">Grade Level</label>
                                                <select class="form-control form-control-sm select2" name="input_gradelevel" id="input_gradelevel">
                                                      <option value="">Grade level</option>
                                                      @foreach ($gradelevel as $item)
                                                            <option value="{{$item->id}}">{{$item->levelname}}</option>
                                                      @endforeach
                                                </select>
                                          </div>
                                          <div class="col-md-6 form-group" id="strand_holder" hidden>
                                                <label for="">Strand</label>
                                                <select class="form-control form-control-sm select2" name="input_strand" id="input_strand">
                                                      <option value="">Strand</option>
                                                      @foreach ($strand as $item)
                                                            <option value="{{$item->id}}">{{$item->strandcode}} - {{$item->strandname}}</option>
                                                      @endforeach
                                                </select>
                                          </div>
                                          <div class="col-md-6 form-group" id="course_holder" hidden>
                                                <label for="">Courses</label>
                                                <select class="form-control form-control-sm select2" name="input_course" id="input_course">
                                                      <option value="">Course</option>
                                                      @foreach ($college_course as $item)
                                                            <option value="{{$item->id}}">{{$item->courseabrv}} - {{$item->courseDesc}}</option>
                                                      @endforeach
                                                </select>
                                          </div>
                                    </div>
                                    <div class="row">
                                          <div class="col-md-12">
                                                <button class="btn btn-sm btn-primary" id="save_information_button">Save Information</button>
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
                        timer: 2000,
                  })

                  var sf1_column = @json($sf1_column);
                  get_selected_col()

                  $('.select2').select2()
                

                  function get_selected_col(){


                        var selected_col = sf1_column.filter(x=>x.id == $('#col_filter').val())
                   
                        if(selected_col.length > 0){
                              $('#lrn').val(selected_col[0].lrn)
                              $('#name').val(selected_col[0].name)
                              $('#dob').val(selected_col[0].dob)
                              $('#gender').val(selected_col[0].gender)
                              $('#street').val(selected_col[0].street)
                              $('#barangay').val(selected_col[0].barangay)
                              $('#city').val(selected_col[0].city)
                              $('#province').val(selected_col[0].province)
                              $('#fname').val(selected_col[0].fname)
                              $('#mname').val(selected_col[0].mname)
                              $('#gname').val(selected_col[0].gname)
                              $('#grelation').val(selected_col[0].grelation)
                              $('#contact').val(selected_col[0].contactno)

                              $('#mothertongue').val(selected_col[0].mothertongue)
                              $('#ethnicgroup').val(selected_col[0].ethnicgroup)
                              $('#religion').val(selected_col[0].religion)

                        }else{
                              $('#lrn').val("")
                              $('#name').val("")
                              $('#dob').val("")
                              $('#gender').val("")
                              $('#street').val("")
                              $('#barangay').val("")
                              $('#city').val("")
                              $('#province').val("")
                              $('#fname').val("")
                              $('#mname').val("")
                              $('#gname').val("")
                              $('#grelation').val("")
                              $('#contact').val("")
                              $('#mothertongue').val("")
                              $('#ethnicgroup').val("")
                              $('#religion').val("")
                        }
                       
                  }

                  $(document).on('change','#col_filter',function(a,b){
                        get_selected_col()
                  })

                  $(document).on('change','#input_gradelevel',function(a,b){
                        $('#strand_holder').attr('hidden','hidden')
                        $('#course_holder').attr('hidden','hidden')
                        if($(this).val() == 14 || $(this).val() == 15){
                              $('#strand_holder').removeAttr('hidden')
                              $('#course_holder').attr('hidden','hidden')
                        }else if($(this).val() >= 17 && $(this).val() <= 21){
                              $('#strand_holder').attr('hidden','hidden')
                              $('#course_holder').removeAttr('hidden')
                        }
                  })

                  $(document).on('input','#input_sf1',function(a,b){
                        $('#upload_progress').css('width',0)
                        $('#upload_progress').text(0)
                        display_uploaded([])
                  })


                  display_uploaded

                  $(document).on('click','#savecolumn',function(a,b){

                        var data = {
                              'lrn':$('#lrn').val(),
                              'name':$('#name').val(),
                              'gender':$('#gender').val(),
                              'dob':$('#dob').val(),
                              'street':$('#street').val(),
                              'barangay':$('#barangay').val(),
                              'city':$('#city').val(),
                              'province':$('#province').val(),
                              'fname':$('#fname').val(),
                              'mname':$('#mname').val(),
                              'gname':$('#gname').val(),
                              'grelation':$('#grelation').val(),
                              'contact':$('#contact').val(),
                              'mothertongue':$('#mothertongue').val(),
                              'ethnicgroup':$('#ethnicgroup').val(),
                              'religion':$('#religion').val(),
                              'id':$('#col_filter').val()
                        }

                        $.ajax({
                              url: '/sf1/tosystem/savecolumns',
                              type: 'GET',
                              data: data,
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
                                          title: 'Something went wrong!'
                                    })
                              }
                        })

                  })

                  display_uploaded()

                  var studentcount = 0

                  $(document).on('click','#save_information_button',function(){
                        
                        if($('#input_gradelevel').val() == ""){
                              Toast.fire({
                                    type: 'warning',
                                    title: 'No Grade Level'
                              })
                              return false;
                        }


                        if($('#input_gradelevel').val() == 14 || $('#input_gradelevel').val() == 15){
                              if($('#input_strand').val() == ""){
                                    Toast.fire({
                                          type: 'warning',
                                          title: 'No Strand'
                                    })
                                    return false;
                              }
                        }else if($('#input_gradelevel').val() >= 17 && $('#input_gradelevel').val() <= 21){
                              // if($('#input_course').val() == ""){
                              //       Toast.fire({
                              //             type: 'warning',
                              //             title: 'No Course'
                              //       })
                              //       return false;
                              // }
                        }
                        

                        studentcount = 0
                        saveinfo()
                  })


                  function saveinfo(){
        
                        if($('.not_saved').length == 0){
                              Toast.fire({
                                    type: 'success',
                                    title: 'Complete!'
                              })
                        }else{

                              var row = $('.not_saved')[0]
                              var temp_row = $(row).attr('data-id')

                              var data = {
                                    'levelid':$('#input_gradelevel').val(),
                                    'lrn':$('.lrn[data-id="'+temp_row+'"]').val(),
                                    'firstname':$('.firstname[data-id="'+temp_row+'"]').val(),
                                    'lastname':$('.lastname[data-id="'+temp_row+'"]').val(),
                                    'middlename':$('.middlename[data-id="'+temp_row+'"]').val(),
                                    'suffix':$('.suffix[data-id="'+temp_row+'"]').val(),
                                    'gender':$('.gender[data-id="'+temp_row+'"]').val(),
                                    'dob':$('.dob[data-id="'+temp_row+'"]').val(),
                                    'street':$('.street[data-id="'+temp_row+'"]').val(),
                                    'barangay':$('.barangay[data-id="'+temp_row+'"]').val(),
                                    'city':$('.city[data-id="'+temp_row+'"]').val(),
                                    'province':$('.province[data-id="'+temp_row+'"]').val(),
                                    'fname':$('.fathername[data-id="'+temp_row+'"]').val(),
                                    'mname':$('.mothername[data-id="'+temp_row+'"]').val(),
                                    'gname':$('.guardinname[data-id="'+temp_row+'"]').val(),
                                    'grelation':$('.guardinname[data-id="'+temp_row+'"]').val(),
                                    'contact':$('.contactno[data-id="'+temp_row+'"]').val(),
                                    'strandid':$('#input_strand').val(),
                                    'courseid':$('#input_course').val()
                              }

                              $.ajax({
                                    url: '/sf1/tosystem/saveinfo',
                                    type: 'GET',
                                    data: data,
                                    success:function(data) {
                                          $('td[data-id="'+temp_row+'"]').removeClass()
                                          if(data[0].status == 1){
                                                studentcount += 1;
                                                $('td[data-id="'+temp_row+'"]').addClass('bg-success')
                                                $('.not_saved[data-id="'+temp_row+'"]').removeClass('not_saved')
                                                saveinfo()
                                          }
                                          else if(data[0].status == 2){
                                                studentcount += 1;
                                                $('td[data-id="'+temp_row+'"]').addClass('bg-warning')
                                                $('.not_saved[data-id="'+temp_row+'"]').removeClass('not_saved')
                                                saveinfo()
                                          }else{
                                                $('td[data-id="'+temp_row+'"]').addClass('bg-danger')
                                                $('.not_saved[data-id="'+temp_row+'"]').removeClass('not_saved')
                                                saveinfo()
                                          }
                                    },error:function(){
                                    
                                    }
                              })
                        }
                       
                  }

                  $( '#upload_sf1' )
                    .submit( function( e ) {

                      
                        if($('.column').val() == ""){
                              Toast.fire({
                                    type: 'warning',
                                    title: 'Empty column'
                              })
                              return false;
                        }

                        if($('#input_sf1').val() == ""){
                              Toast.fire({
                                    type: 'warning',
                                    title: 'No File'
                              })
                              return false;
                        }

                        var inputs = new FormData(this)

                        inputs.append('lrn',$('#lrn').val())
                        inputs.append('name',$('#name').val())
                        inputs.append('gender',$('#gender').val())
                        inputs.append('dob',$('#dob').val())
                        inputs.append('street',$('#street').val())
                        inputs.append('barangay',$('#barangay').val())
                        inputs.append('city',$('#city').val())
                        inputs.append('province',$('#province').val())
                        inputs.append('fname',$('#fname').val())
                        inputs.append('mname',$('#mname').val())
                        inputs.append('gname',$('#gname').val())
                        inputs.append('grelation',$('#grelation').val())
                        inputs.append('contact',$('#contact').val())

                        inputs.append('mothertongue',$('#mothertongue').val())
                        inputs.append('ethnicgroup',$('#ethnicgroup').val())
                        inputs.append('religion',$('#religion').val())

                        $.ajax({
                              xhr: function() {
                                    var xhr = new window.XMLHttpRequest();

                                    xhr.upload.addEventListener("progress", function(evt) {
                                    if (evt.lengthComputable) {
                                          var percentComplete = evt.loaded / evt.total;
                                          percentComplete = parseInt(percentComplete * 100);

                                          $('.progress-bar').width(percentComplete+'%');
                                          $('.progress-bar').html(percentComplete+'%');
                                          console.log(percentComplete)
                                          }
                                    }, false);

                                    

                                    return xhr;
                              },
                              url: '/sf1/tosystem/upload',
                              type: 'POST',
                              data: inputs,
                              processData: false,
                              contentType: false,
                              success:function(data) {
                                    if(data.length == 0){
                                          Toast.fire({
                                                type: 'warning',
                                                title: 'No data extracted!'
                                          })
                                    }else{
                                          display_uploaded(data)
                                    }
                              }
                            
                              ,error:function(){
                                    Toast.fire({
                                          type: 'error',
                                          title: 'Something went wrong!'
                                    })
                              }
                        })
                        e.preventDefault();
                  })


                  function display_uploaded(data){

                        $("#student_checker").DataTable({
                                    destroy: true,
                                    data:data,
                                    scrollY: '224px',
                                    lengthChange: false,
                                    scrollX: true,
                                    paging: false,
                                    order: [
                                          [ 2, "asc" ]
                                    ],
                                    columns: [
                                                { "data": "lrn" },
                                                { "data": "lrn" },
                                                { "data": "lastname" },
                                                { "data": "firstname" },
                                                { "data": "lrn" },
                                                { "data": "lrn" },
                                                { "data": "lrn" },
                                                { "data": "lrn" },
                                                { "data": "lrn" },
                                                { "data": "lrn" },
                                                { "data": "lrn" },
                                                { "data": "lrn" },
                                                { "data": "lrn" },
                                                { "data": "lrn" },
                                                { "data": "lrn" },
                                                { "data": "lrn" },
                                                { "data": "lrn" },
                                                { "data": "lrn" },
                                                { "data": "lrn" },
                                                { "data": "lrn" }
                                    ], columnDefs: [
                                          {
                                                'targets': 0,
                                                'createdCell':  function (td, cellData, rowData, row, col) {
                                                      $(td).text(null)
                                                      $(td).attr('data-id',row)
                                                      $(td).addClass('bg-secondary')
                                                }
                                          },
                                          {
                                                'targets': 1,
                                                'createdCell':  function (td, cellData, rowData, row, col) {
                                                      $(td)[0].innerHTML = '<input data-id="'+row+'" class="form-control form-control-sm not_saved lrn" value="'+rowData.lrn+'" style="width: 115px;">'
                                                }
                                          },
                                          {
                                                'targets': 2,
                                                'createdCell':  function (td, cellData, rowData, row, col) {
                                                      $(td)[0].innerHTML = '<input data-id="'+row+'" class="form-control form-control-sm lastname" value="'+rowData.lastname+'"  style="width: 260px;">'
                                                }
                                          },
                                          {
                                                'targets': 3,
                                                'createdCell':  function (td, cellData, rowData, row, col) {
                                                      $(td)[0].innerHTML = '<input data-id="'+row+'" class="form-control form-control-sm firstname" value="'+rowData.firstname+'" style="width: 260px;">'
                                                }
                                          },
                                          {
                                                'targets': 4,
                                                'createdCell':  function (td, cellData, rowData, row, col) {
                                                      $(td)[0].innerHTML = '<input data-id="'+row+'" class="form-control form-control-sm middlename" value="'+rowData.middlename+'" style="width: 260px;">'
                                                }
                                          },
                                          {
                                                'targets': 5,
                                                'createdCell':  function (td, cellData, rowData, row, col) {
                                                      $(td)[0].innerHTML = '<input data-id="'+row+'" data-id="'+row+'" class="form-control form-control-sm suffix" value="'+rowData.suffix+'">'
                                                }
                                          },
                                          {
                                                'targets': 6,
                                                'createdCell':  function (td, cellData, rowData, row, col) {
                                                      $(td)[0].innerHTML = '<input data-id="'+row+'" class="form-control form-control-sm gender" value="'+rowData.gender+'" style="width: 73px;">'
                                                }
                                          },
                                          {
                                                'targets': 7,
                                                'createdCell':  function (td, cellData, rowData, row, col) {
                                                      $(td)[0].innerHTML = '<input data-id="'+row+'" class="form-control form-control-sm dob" value="'+rowData.dob+'" style="width: 92px;">'
                                                }
                                          },
                                          {
                                                'targets': 8,
                                                'createdCell':  function (td, cellData, rowData, row, col) {
                                                      $(td)[0].innerHTML = '<input data-id="'+row+'" class="form-control form-control-sm street" value="'+rowData.mothertongue+'" style="width: 200px">'
                                                }
                                          },
                                          {
                                                'targets': 9,
                                                'createdCell':  function (td, cellData, rowData, row, col) {
                                                      $(td)[0].innerHTML = '<input data-id="'+row+'" class="form-control form-control-sm street" value="'+rowData.ethnicgroup+'" style="width: 200px">'
                                                }
                                          },
                                          {
                                                'targets': 10,
                                                'createdCell':  function (td, cellData, rowData, row, col) {
                                                      $(td)[0].innerHTML = '<input data-id="'+row+'" class="form-control form-control-sm street" value="'+rowData.religion+'" style="width: 200px">'
                                                }
                                          },
                                          {
                                                'targets': 11,
                                                'createdCell':  function (td, cellData, rowData, row, col) {
                                                      $(td)[0].innerHTML = '<input data-id="'+row+'" class="form-control form-control-sm street" value="'+rowData.street+'" style="width: 260px;">'
                                                }
                                          },
                                          {
                                                'targets': 12,
                                                'createdCell':  function (td, cellData, rowData, row, col) {
                                                      $(td)[0].innerHTML = '<input data-id="'+row+'" class="form-control form-control-sm barangay" value="'+rowData.barangay+'" style="width: 260px;">'
                                                }
                                          },
                                          {
                                                'targets': 13,
                                                'createdCell':  function (td, cellData, rowData, row, col) {
                                                      $(td)[0].innerHTML = '<input data-id="'+row+'" class="form-control form-control-sm city" value="'+rowData.city+'" style="width: 260px;">'
                                                }
                                          },
                                          {
                                                'targets': 14,
                                                'createdCell':  function (td, cellData, rowData, row, col) {
                                                      $(td)[0].innerHTML = '<input data-id="'+row+'" class="form-control form-control-sm province" value="'+rowData.province+'" style="width: 260px;">'
                                                }
                                          },
                                          {
                                                'targets': 15,
                                                'createdCell':  function (td, cellData, rowData, row, col) {
                                                      $(td)[0].innerHTML = '<input data-id="'+row+'" class="form-control form-control-sm fathername" value="'+rowData.fathername+'" style="width: 260px;">'
                                                }
                                          },
                                          {
                                                'targets': 16,
                                                'createdCell':  function (td, cellData, rowData, row, col) {
                                                      $(td)[0].innerHTML = '<input data-id="'+row+'" class="form-control form-control-sm mothername" value="'+rowData.mothername+'" style="width: 260px;">'
                                                }
                                          },
                                          {
                                                'targets': 17,
                                                'createdCell':  function (td, cellData, rowData, row, col) {
                                                      $(td)[0].innerHTML = '<input data-id="'+row+'" class="form-control form-control-sm guardinname" value="'+rowData.guardianname+'" style="width: 260px;">'
                                                }
                                          },
                                          {
                                                'targets': 18,
                                                'createdCell':  function (td, cellData, rowData, row, col) {
                                                      $(td)[0].innerHTML = '<input data-id="'+row+'" class="form-control form-control-sm guardianrelation" value="'+rowData.guardianrelation+'" style="width: 110px;">'
                                                }
                                          },
                                          {
                                                'targets': 19,
                                                'createdCell':  function (td, cellData, rowData, row, col) {
                                                      $(td)[0].innerHTML = '<input data-id="'+row+'" class="form-control form-control-sm contactno" value="'+rowData.contactno+'" style="width: 110px;">'
                                                }
                                          }
                                    ]
                              })

                              var label_text = $($("#student_checker_wrapper")[0].children[0])[0].children[0]
                              $(label_text)[0].innerHTML = '<span class="badge badge-secondary mt-2 ml-1" style="font-size:13px !important">Not Processed</span><span class="badge badge-success mt-2 ml-1" style="font-size:13px !important">Saved</span><span class="badge badge-warning mt-2 ml-1" style="font-size:13px !important">Already Exist</span>'

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

               
            })
      </script>

@endsection


