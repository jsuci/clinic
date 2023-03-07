@php
      $check_refid = DB::table('usertype')->where('id',Session::get('currentPortal'))->select('refid')->first();
      if(auth()->user()->type == 17){
            $extend = 'superadmin.layouts.app2';
      }else if(Session::get('currentPortal') == 3){
            $extend = 'registrar.layouts.app';
      }else if(Session::get('currentPortal') == 2){
            $extend = 'principalsportal.layouts.app2';
      }else{
            if(isset($check_refid->refid)){
                  if($check_refid->refid == 27){
                        $extend = 'academiccoor.layouts.app2';
                  }
            }else{
                  $extend = 'general.defaultportal.layouts.app';
            }
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
            .no-border-col{
                  border-left: 0 !important;
                  border-right: 0 !important;
            }
            /* input[type=search]{
                  height: calc(1.7em + 2px) !important;
            } */
      </style>
@endsection


@section('content')

@php
   $sy = DB::table('sy')
            ->orderBy('sydesc','desc')
            ->select(
                  'sy.*',
                  'sydesc as text'
            )
            ->get(); 
   $activesy = DB::table('sy')->where('isactive',1)->first()->id; 
   $schoolinfo = DB::table('schoolinfo')->first(); 

@endphp


<div class="modal fade" id="schooldays_copy_modal" style="display: none;" aria-hidden="true">
      <div class="modal-dialog modal-sm">
            <div class="modal-content">
                  <div class="modal-body">
                        <div class="row">
                              <div class="col-md-6 form-group">
                                    <div class="icheck-primary d-inline pt-2">
                                        <input type="checkbox" id="copy_to_gradelevel" >
                                        <label for="copy_to_gradelevel">Grade Level
                                        </label>
                                    </div>
                              </div>
                              <div class="col-md-6 form-group">
                                    <div class="icheck-primary d-inline pt-2">
                                        <input type="checkbox" id="copy_to_schoolyear" >
                                        <label for="copy_to_schoolyear">School Year
                                        </label>
                                    </div>
                              </div>
                        </div>
                        <div class="row">
                              <div class="col-md-12 form-group">
                                    <label for="">Copy from school year</label>
                                    <select class="form-control select2" id="copy_sy" disabled>
                                          {{-- <option value="" selected="selected">Select School Year</option> --}}
                                          {{-- @foreach (collect($sy)->where('isactive',0)->values() as $item)
                                                <option value="{{$item->id}}">{{$item->sydesc}}</option>
                                          @endforeach --}}
                                    </select>
                              </div>
                              <div class="col-md-12 form-group">
                                    <label for="">Copy from grade level</label>
                                    <select class="form-control select2" id="copy_gradelevel" disabled>
                                    </select>
                              </div>
                        </div>
                        <div class="row">
                              <div class="col-md-12">
                                    <button class="btn btn-primary btn-sm btn-block" id="miss_com_to_add" hidden>Add Missing Components</button>
                              </div>
                        </div>
                  </div>
                  <div class="modal-footer border-0">
                        <div class="col-md-6">
                              <button class="btn btn-primary btn-sm" id="copy_to"><i class="fas fa-copy"></i> Copy</button>
                        </div>
                        <div class="col-md-6 text-right">
                              <button class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                        </div>
                  </div>
            </div>
      </div>
</div>   

<div class="modal fade" id="attendance_setup_modal" style="display: none;" aria-hidden="true">
      <div class="modal-dialog modal-sm">
            <div class="modal-content">
                  <div class="modal-body">
                        <div class="row">
                              <div class="col-md-12 form-group">
                                    <label for="">Year</label>
                                    <input id="input_year" type="number" min="2000" max="2099" step="1" value="{{\Carbon\Carbon::now('Asia/Manila')->isoFormat('YYYY')}}" class="form-control form-control-sm"/>
                              </div>
                              <div class="col-md-12 form-group sem_holder" hidden>
                                    <label for="">Semester</label>
                                    <select class="form-control form-control-sm select2" id="input_sem">
                                          <option value="1">1st Semester</option>
                                          <option value="2">2nd Semester</option>
                                    </select>
                              </div>
                              <div class="col-md-12 form-group">
                                    <label for="">Month</label>
                                    <select class="form-control form-control-sm select2" id="input_month">
                                          <option value="1">January</option>
                                          <option value="2">February</option>
                                          <option value="3">March</option>
                                          <option value="4">April</option>
                                          <option value="5">May</option>
                                          <option value="6">June</option>
                                          <option value="7">July</option>
                                          <option value="8">August</option>
                                          <option value="9">September</option>
                                          <option value="10">October</option>
                                          <option value="11">November</option>
                                          <option value="12">December</option>
                                    </select>
                              </div>
                              <div class="col-md-12 form-group">
                                    <label for="">Days</label>
                                    <input class="form-control form-control-sm" id="input_day" oninput="this.value=this.value.replace(/[^0-9]/g,'');" autocomplete="off">
                              </div>
                              <div class="col-md-12 form-group">
                                    <label for="">Sort</label>
                                    <input class="form-control form-control-sm" id="input_sort" onkeyup="this.value = this.value.toUpperCase();" autocomplete="off" onkeydown="return /[a-z]/i.test(event.key)">
                              </div>
                              
                        </div>
                  </div>
                  <div class="modal-footer border-0">
                        <div class="col-md-6">
                              <button class="btn btn-primary btn-sm" id="attendance_setup_create">CREATE</button>
                        </div>
                        <div class="col-md-6 text-right">
                              <button class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                        </div>
                  </div>
            </div>
      </div>
</div>    



<section class="content-header">
      <div class="container-fluid">
            <div class="row mb-2">
                  <div class="col-sm-6">
                        <h1>School Days</h1>
                  </div>
                  <div class="col-sm-6">
                  <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/home">Home</a></li>
                        <li class="breadcrumb-item active">School Days</li>
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
                                          <h5 class="float-right">Active S.Y.: {{collect($sy)->where('isactive',1)->first()->sydesc}}</h5>
                                    </div>
                              </div>
                              <div class="row">
                                    <div class="col-md-2">
                                          <label for="">School Year</label>
                                          <select class="form-control select2" id="input_sy">
                                                @foreach ($sy as $item)
                                                      @if($item->isactive == 1)
                                                            <option value="{{$item->id}}" selected="selected">{{$item->sydesc}}</option>
                                                      @else
                                                            <option value="{{$item->id}}">{{$item->sydesc}}</option>
                                                      @endif
                                                @endforeach
                                          </select>
                                    </div>
                                    <div class="col-md-2">
                                          <label for="">Grade Level</label>
                                          <select class="form-control select2" id="filter_gradelevel">
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
                                    <div class="row">
                                         
                                    </div>
                                    <div class="row mt-2">
                                          <div class="col-md-12">
                                                <table class="table table-striped table-sm table-bordered " id="attendance_setup" width="100%">
                                                      <thead>
                                                            <tr>
                                                                  <th width="5%"></th>
                                                                  <th width="30%">Year</th>
                                                                  <th width="30%">Month</th>
                                                                  <th width="25%">Days</th>
                                                                  <th width="5%"></th>
                                                                  <th width="5%"></th>
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
      <script src="{{asset('plugins/moment/moment.min.js') }}"></script>

      <script>
            var school_setup = @json($schoolinfo);
            var userid = @json(auth()->user()->id)
    
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
                        Toast.fire({
                              type: 'success',
                              title: 'Created Successfully!'
                        })
                      get_attendance_setup()
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
                var date = moment().subtract(2, 'minute').format('YYYY-MM-DD HH:mm:ss');
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
                        Toast.fire({
                              type: 'success',
                              title: 'Updated Successfully!'
                        })
                        get_attendance_setup()
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
                  Toast.fire({
                        type: 'success',
                        title: 'Deleted Successfully!'
                  })
                   get_attendance_setup()
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

            const Toast = Swal.mixin({          
                  toast: true,
                  position: 'top-end',
                  showConfirmButton: false,
                  timer: 2000,
            })


            $(document).on('change','#input_sy',function(){
                  $('#filter_gradelevel').empty()
                  get_gradelevel()
            })

            get_gradelevel()

            function get_gradelevel(){

                  $.ajax({
                        type:'GET',
                        url: '/superadmin/attendance/getgradelevel',
                        data:{
                              syid:$('#input_sy').val(),
                        },
                        success:function(data) {

                              gradelevel = data
                              $('#filter_gradelevel').empty()
                              $("#filter_gradelevel").append('<option value="">Select Grade Level</option>');
                              $("#filter_gradelevel").select2({
                                    data: gradelevel,
                                    placeholder: "Select Grade Level",
                                    allowClear:true
                              })

                              gradelevel = data
                              $('#copy_gradelevel').empty()
                              $("#copy_gradelevel").append('<option value="">Select Grade Level</option>');
                              $("#copy_gradelevel").select2({
                                    data: gradelevel,
                                    placeholder: "Select Grade Level",
                                    allowClear:true
                              })
                        }
                  })

            }

            var syid = @json($activesy);
            var all_sy = @json($sy);
            var allgradelevel = []
            var all_attendance_setup = []

            function get_attendance_setup(){
                  $.ajax({
                        type:'GET',
                        url: '/superadmin/attendance/list',
                        data:{
                              schoolyear:$('#input_sy').val(),
                              levelid:$('#filter_gradelevel').val()
                        },
                        success:function(data) {
                              all_attendance_setup = data
                              Toast.fire({
                                    type: 'info',
                                    title: all_attendance_setup.length+' month(s) found!'
                              })
                              loaddatatable()
                        }
                  })
            }

            function loaddatatable(){

                  var total = 0;

                  $.each(all_attendance_setup.filter(x=>x.sort != 'ZZ'),function(a,b){
                        total += parseInt(b.days)
                  })

                  $('#total_number_of_schooldays').text(total)
                  $('#total_months').text(all_attendance_setup.length)

                  var check = all_attendance_setup.filter(x=>x.sort == 'ZZ')

                  if(check.length > 0){
                        var index = all_attendance_setup.findIndex(x=>x.sort == 'ZZ')
                        all_attendance_setup[index].days = total 
                  }else{
                        all_attendance_setup.push({
                              'sort':'ZZ',
                              'year':'',
                              'monthdesc':'Total',
                              'days':total
                        })

                  }

                  var temp_sy = all_sy.filter(x=>x.id == $('#input_sy').val())[0]



                  $("#attendance_setup").DataTable({
                        destroy: true,
                        data:all_attendance_setup,
                        lengthChange: false,
                        pageLength: 50,
                        paging: false,
                        bInfo: false,
                        autoWidth: false,
                        order: [
                                    [ 0, "asc" ],
                                    [ 1, "asc" ]
                              ],
                        columns: [
                              { "data": "sort" },
                              { "data": "year" },
                              { "data": "monthdesc" },
                              { "data": "days" },
                              { "data": null },
                              { "data": null },
                        ],
                        columnDefs: [
                                          {
                                                'targets': 0,
                                                'orderable': true, 
                                                'createdCell':  function (td, cellData, rowData, row, col) {
                                                      if(rowData.sort == 'ZZ'){
                                                            $(td).text(null)
                                                      }
                                                      $(td).addClass('text-center')
                                                }
                                          },
                                          {
                                                'targets': 1,
                                                'orderable': true, 
                                                'createdCell':  function (td, cellData, rowData, row, col) {
                                                      if(rowData.sort != 'ZZ'){
                                                            if($('#filter_gradelevel').val() == 14 || $('#filter_gradelevel').val() == 15){
                                                                  var semid = '1st Sem';
                                                                  if(rowData.semid == 2){
                                                                        var semid = '2nd Sem';
                                                                  }
                                                                  $(td)[0].innerHTML = rowData.year+' : '+'<span class="text-success">'+semid+'</span>'
                                                            }
                                                      }
                                                }
                                          },
                                          {
                                                'targets': 2,
                                                'orderable': true, 
                                                'createdCell':  function (td, cellData, rowData, row, col) {
                                                      if(rowData.sort == 'ZZ'){
                                                            $(td).addClass('text-right pr-3 font-weight-bold')
                                                            return false
                                                      }
                                                }
                                          },
                                          {
                                                'targets': 3,
                                                'orderable': false, 
                                                'createdCell':  function (td, cellData, rowData, row, col) {
                                                      if(rowData.sort == 'ZZ'){
                                                            $(td).addClass('font-weight-bold')
                                                            return false
                                                      }
                                                }
                                          },
                                          {
                                                'targets': 4,
                                                'orderable': false, 
                                                'createdCell':  function (td, cellData, rowData, row, col) {
                                                      if(rowData.sort == 'ZZ'){
                                                            $(td).text(null)
                                                            return false;
                                                      }
                                                      if(temp_sy.ended == 0){
                                                            var buttons = '<a href="#" class="edit" data-id="'+rowData.id+'"><i class="far fa-edit"></i></a>';
                                                            $(td)[0].innerHTML =  buttons
                                                            $(td).addClass('text-center')
                                                      }else{
                                                            $(td).text(null)
                                                      }
                                                      
                                                }
                                          },
                                          {
                                                'targets': 5,
                                                'orderable': false, 
                                                'createdCell':  function (td, cellData, rowData, row, col) {
                                                      if(rowData.sort == 'ZZ'){
                                                            $(td).text(null)
                                                            return false;
                                                      }
                                                      if(temp_sy.ended == 0){
                                                            var buttons = '';
                                                            buttons += '<a href="#" class="delete" data-id="'+rowData.id+'"><i class="far fa-trash-alt text-danger"></i></a>';
                                                            $(td)[0].innerHTML =  buttons
                                                            $(td).addClass('text-center')
                                                            $(td).addClass('p-0')
                                                            $(td).addClass('align-middle')
                                                      }else{
                                                            $(td).text(null)
                                                      }
                                                }
                                          },
                                    ]
                  });

                  var disabled = ''
                  if($('#filter_gradelevel').val() == ""){
                        disabled = 'disabled'
                  }

                  if(temp_sy.ended == 0){
                        var label_text = $($('#attendance_setup'+'_wrapper')[0].children[0])[0].children[0]
                        $(label_text)[0].innerHTML = ' <button class="btn btn-primary btn-sm" id="attendance_setup_button" '+disabled+'><i class="fas fa-plus" ></i> Add Month</button>  <button class="btn btn-primary btn-sm btn-warning " id="schooldays_copy_to" '+disabled+'><i class="fas fa-copy"></i> Copy From</button>'
                  }

            }

      </script>

      <script>
            $(document).ready(function(){
                
                  var selected_setup
                  var process = 'create'

                  $('.select2').select2()

                  $(document).on('change','#filter_gradelevel',function(){
                        if($(this).val() == ""){
                              $('#attendance_setup_button').attr('disabled','disabled')
                              $('#schooldays_copy_to').attr('disabled','disabled')
                              get_attendance_setup()
                        }else{
                              $('#attendance_setup_button').removeAttr('disabled')
                              $('#schooldays_copy_to').removeAttr('disabled')
                              get_attendance_setup()
                        }

                        if($(this).val() == 14 || $(this).val() == 15){
                              $('.sem_holder').removeAttr('hidden')
                        }else{
                              $('.sem_holder').attr('hidden','hidden')
                        }
                  })

                  $(document).on('click','#copy_to',function(){

                        if($('#copy_to_gradelevel').prop('checked') == true){
                              if($('#copy_gradelevel').val() == ""){
                                    Toast.fire({
                                          type: 'warning',
                                          title: 'No gradelevel selected!'
                                    })
                                    return false
                              }
                        }

                        
                        if($('#copy_to_schoolyear').prop('checked') == true){
                              if($('#copy_sy').val() == ""){
                                    Toast.fire({
                                          type: 'warning',
                                          title: 'No S.Y. selected!'
                                    })
                                    return false
                              }
                        }

                        $('#copy_to').attr('disabled','disabled')
                        $('#copy_to').text('Processing...')

                        $.ajax({
                              type:'GET',
                              url: '/superadmin/setup/schooldays/copy',
                              data:{
                                    syid_from:$('#copy_sy').val(),
                                    syid_to:$('#input_sy').val(),
                                    gradelevel_from:$('#copy_gradelevel').val(),
                                    gradelevel_to:$('#filter_gradelevel').val(),
                              },
                              success:function(data) {
                                    if(data[0].status == 1){
                                          $('#copy_to').removeAttr('disabled','disabled')
                                          $('#copy_to')[0].innerHTML = '<i class="fas fa-copy"></i> Copy'
                                          Toast.fire({
                                                type: 'success',
                                                title: data[0].copied+'(s) Months Copied!'
                                          })
                                          get_attendance_setup()
                                    }else{
                                          Toast.fire({
                                                type: 'danger',
                                                title: data[0].data
                                          }) 
                                    }
                              }
                        })
                  })

                  $(document).on('input','#input_sort',function(){
                        if($(this).val().length > 1){
                              $(this).val($(this).val().slice(0,-1))
                        }
                  })

                  $(document).on('click','#copy_to_gradelevel',function(){
                        if($(this).prop('checked') == true){
                              $('#copy_gradelevel').removeAttr('disabled','disabled')
                              $('#copy_to_schoolyear').prop('checked',false)
                              $('#copy_sy').attr('disabled','disabled')
                              $('#copy_sy').val("").change()
                        }
                       
                  })

                  $(document).on('click','#copy_to_schoolyear',function(){
                        if($(this).prop('checked') == true){
                              $('#copy_sy').removeAttr('disabled','disabled')
                              $('#copy_to_gradelevel').prop('checked',false)
                              $('#copy_gradelevel').attr('disabled','disabled')
                              $('#copy_gradelevel').val("").change()
                        }
                  })

                  loaddatatable()

                  $(document).on('click','#attendance_setup_button',function(){

                       

                        process = 'create'
                        $('#attendance_setup_create')[0].innerHTML = '<i class="fas fa-save"></i> Create'
                        $('#attendance_setup_modal').modal()    
                        $('#input_month').val(1).change()
                        $('#input_day').val("")
                        $('#input_sort').val("")                
                        
                        if(all_attendance_setup.length > 1){
                              $('#input_sort').attr('placeholder','Last sort '+all_attendance_setup[all_attendance_setup.length-2].sort)
                        }else{
                              $('#input_sort').attr('placeholder','Sort')
                        }

                      

                        $('#input_month').removeAttr('disabled')

                        

                  })

                  $(document).on('change','#input_sy',function(){
                        get_attendance_setup()
                  })

                  $(document).on('click','#attendance_setup_create',function(){
                        
                        if(process == 'create'){
                              attendance_setup_create()           
                        }else if(process == 'edit'){
                              attendance_setup_update()  
                        }
                                     
                  })

                  $(document).on('click','.delete',function(){
                        selected_setup = $(this).attr('data-id')
                        attendance_setup_delete()
                  })

                  $(document).on('click','.edit',function(){
                        selected_setup = $(this).attr('data-id')
                        process = 'edit'
                        var temp_attendance_id = all_attendance_setup.filter(x=>x.id == selected_setup)
                        $('#input_month').val(temp_attendance_id[0].month).change(),
                        $('#input_day').val(temp_attendance_id[0].days),
                        $('#input_year').val(temp_attendance_id[0].year),
                        $('#input_sort').val(temp_attendance_id[0].sort)
                        $('#attendance_setup_modal').modal()       
                        $('#attendance_setup_create')[0].innerHTML = '<i class="fas fa-save"></i> Update'
                        $('#input_month').attr('disabled','disabled')
                  })

                  
      
                  function attendance_setup_create(){

                        var valid_input = true

                        var check_duplications = all_attendance_setup.filter(x=>x.month == $('#input_month').val())
                        if(check_duplications.length > 0){
                              valid_input = false
                              Toast.fire({
                                    type: 'warning',
                                    title: 'Month already exist!'
                              })
                        }
                        else if($('#input_day').val() == ""){
                              valid_input = false
                              Toast.fire({
                                    type: 'warning',
                                    title: 'Days is empty!'
                              })
                        }
                        else if($('#input_sort').val() == ""){
                              valid_input = false
                              Toast.fire({
                                    type: 'warning',
                                    title: 'Sort is empty!'
                              })
                        }
                        else if($('#input_day').val() > 31){
                              valid_input = false
                              Toast.fire({
                                    type: 'warning',
                                    title: 'Days is exceeds 31 days!'
                              })
                        }

                        var semid = 1;
                        if($('#filter_gradelevel').val() == 14 || $('#filter_gradelevel').val() == 15){
                              var semid = $('#input_sem').val()
                        }

                        var url = '/superadmin/attendance/create'

                        if(school_setup.setup == 1 && school_setup.projectsetup == 'offline'){
                              url = school_setup.es_cloudurl+'/superadmin/attendance/create'
                              
                        }
                       
                        if(valid_input){

                              $('#attendance_setup_create').attr('disabled','disabled')
                              $('#attendance_setup_create').text('Processing...')


                              $.ajax({
					      type:'GET',
                                    url: url,
                                    data:{
                                          month:$('#input_month').val(),
                                          days:$('#input_day').val(),
                                          syid:$('#input_sy').val(),
                                          sort:$('#input_sort').val(),
                                          year:$('#input_year').val(),
                                          semid:semid,
                                          levelid:$('#filter_gradelevel').val(),
                                          userid:userid
                                    },
                                    success:function(data) {
                                          if(data[0].status == 1){
                                                $('#attendance_setup_create').removeAttr('disabled','disabled')
                                                $('#attendance_setup_create')[0].innerHTML = '<i class="fas fa-save"></i> Create'
                                                if(school_setup.setup == 1 && school_setup.projectsetup == 'offline'){
                                                      get_last_index('studattendance_setup')
                                                }else{
                                                      Toast.fire({
                                                            type: 'success',
                                                            title: 'Created Successfully!'
                                                      })
                                                      get_attendance_setup()
                                                }
                                               
                                          }
                                          else if(data[0].status == 2){
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
                        }



                       
                  }

                  function attendance_setup_update(){

                        var check_duplications = all_attendance_setup.filter(x=>x.month == $('#input_month').val() && x.id != selected_setup)
                        valid_input = true
                        if(check_duplications.length > 0){
                              valid_input = false
                              Toast.fire({
                                    type: 'warning',
                                    title: 'Month already exist!'
                              })
                        }
                        else if($('#input_day').val() == ""){
                              valid_input = false
                              Toast.fire({
                                    type: 'warning',
                                    title: 'Days is empty!'
                              })
                        }
                        else if($('#input_sort').val() == ""){
                              valid_input = false
                              Toast.fire({
                                    type: 'warning',
                                    title: 'Sort is empty!'
                              })
                        }
                        else if($('#input_day').val() > 31){
                              valid_input = false
                              Toast.fire({
                                    type: 'warning',
                                    title: 'Days is exceeds 31 days!'
                              })
                        }

                        var semid = 1;
                        if($('#filter_gradelevel').val() == 14 || $('#filter_gradelevel').val() == 15){
                              var semid = $('#input_sem').val()
                        }

                        var url = '/superadmin/attendance/update'

                        if(school_setup.setup == 1 && school_setup.projectsetup == 'offline'){
                              url = school_setup.es_cloudurl+'/superadmin/attendance/update'
                              
                        }

                       


                        if(valid_input){

                              $('#attendance_setup_create').attr('disabled','disabled')
                              $('#attendance_setup_create').text('Processing...')

                              $.ajax({
                                    type:'GET',
                                    url: url,
                                    data:{
                                          month:$('#input_month').val(),
                                          days:$('#input_day').val(),
                                          syid:$('#input_sy').val(),
                                          sort:$('#input_sort').val(),
                                          year:$('#input_year').val(),
                                          attsetupid:selected_setup,
                                          semid:semid,
                                          levelid:$('#filter_gradelevel').val(),
                                          userid:userid
                                    },
                                    success:function(data) {
                                          if(data[0].status == 1){
                                                $('#attendance_setup_create').removeAttr('disabled','disabled')
                                                $('#attendance_setup_create')[0].innerHTML = '<i class="fas fa-save"></i> Update'

                                                if(school_setup.setup == 1 && school_setup.projectsetup == 'offline'){
                                                      get_updated('studattendance_setup')
                                                }else{
                                                      Toast.fire({
                                                            type: 'success',
                                                            title: 'Updated Successfully!'
                                                      })
                                                      var attsetup_index = all_attendance_setup.findIndex(x => x.id == selected_setup)
                                                      all_attendance_setup[attsetup_index].sort = $('#input_sort').val()
                                                      all_attendance_setup[attsetup_index].days = $('#input_day').val()
                                                      all_attendance_setup[attsetup_index].syid = $('#input_sy').val()
                                                      all_attendance_setup[attsetup_index].year = $('#input_year').val()
                                                      all_attendance_setup[attsetup_index].sydesc = $('#input_sy option[value="'+$('#input_sy').val()+'"]').text()
                                                      all_attendance_setup[attsetup_index].month = $('#input_month').val()
                                                      all_attendance_setup[attsetup_index].monthdesc = $('#input_month option[value="'+$('#input_month').val()+'"]').text()
                                                      // $('#attendance_setup_modal').modal('hide')   
                                                      loaddatatable()
                                                }
                                          }else if(data[0].status == 2){
                                                Toast.fire({
                                                      type: 'warning',
                                                      title: data[0].data
                                                })
                                          }else{
                                                Toast.fire({
                                                      type: 'error',
                                                      title: 'Something went wrong!'
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
                        }
                  }


                  function attendance_setup_delete(){

                        Swal.fire({
                              title: 'Do you want to remove month?',
                              type: 'warning',
                              showCancelButton: true,
                              confirmButtonColor: '#3085d6',
                              cancelButtonColor: '#d33',
                              confirmButtonText: 'Remove'
                        }).then((result) => {
                              if (result.value) {

                                    var url = '/superadmin/attendance/delete'

                                    if(school_setup.setup == 1 && school_setup.projectsetup == 'offline'){
                                          url = school_setup.es_cloudurl+'/superadmin/attendance/delete'
                                          
                                    }

                                    $.ajax({
                                          type:'GET',
                                          url: url,
                                          data:{
                                                attsetupid:selected_setup,
                                                syid:$('#input_sy').val(),
                                                userid:userid
                                          },
                                          success:function(data) {
                                                if(data[0].status == 1){
                                                      if(school_setup.setup == 1 && school_setup.projectsetup == 'offline'){
                                                            get_deleted('studattendance_setup')
                                                      }else{
                                                            Toast.fire({
                                                                  type: 'success',
                                                                  title: 'Deleted Successfully!'
                                                            })
                                                            all_attendance_setup = all_attendance_setup.filter(x=>x.id != selected_setup)
                                                            loaddatatable()
                                                      }
                                                }else{
                                                      Toast.fire({
                                                            type: 'error',
                                                            title: 'Something went wrong!'
                                                      })
                                                }
                                          }
                                    })
                              }
                        })

                        
                  }

                  $(document).on('click','#schooldays_copy_to',function(){

                        var temp_sy = all_sy.filter(x=>x.id != $('#input_sy').val())
                        $('#copy_sy').empty()
                        $('#copy_sy').append('<option value="">Select School Year</option>')
                        $('#copy_sy').select2({
                              data:temp_sy,
                              allowClear:true,
                              placeholder: "Select School Year",
                        })

                        $('#copy_to_gradelevel').prop('checked',false)
                        $('#copy_to_schoolyear').prop('checked',false)
                        $('#copy_sy').attr('disabled','disabled')
                        $('#copy_gradelevel').attr('disabled','disabled')
                        $('#schooldays_copy_modal').modal()
                        $('#copy_gradelevel').val("").change()
                        $('#copy_sy').val("").change()
                  })

                  


            })
      </script>


@endsection


