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
            /* .select2-selection--single{
                height: calc(2.25rem + 2px) !important;
            } */
            .shadow {
                  box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
                  border: 0;
            }
            .select2-container--default .select2-selection--single .select2-selection__rendered {
                  margin-top: -9px;
            }
      </style>
@endsection


@section('content')

@php
      $sy = DB::table('sy')->get(); 
      $semester = DB::table('semester')->get(); 
      $active_sy = DB::table('sy')->where('isactive',1)->first()->id;

      if(auth()->user()->type == 17){
            $teacher_acadprog = DB::table('academicprogram')
                                    ->select('id as acadprogid')
                                    ->get();
      }else{
            $teacherid = DB::table('teacher')
                              ->where('tid',auth()->user()->email)
                              ->select('id')
                              ->first()
                              ->id;

            $teacher_acadprog = DB::table('teacheracadprog')
                              ->where('teacherid',$teacherid)
                              ->where('teacherid',$teacherid)
                              ->where('syid',$active_sy)
                              ->whereIn('acadprogutype',[3,8])
                              ->distinct('acadprogid')
                              ->where('deleted',0)
                              ->get();
      }

      $acadprog = array();

      foreach($teacher_acadprog as $item){
            array_push($acadprog,$item->acadprogid);
      }

      $gradelevel = DB::table('gradelevel')
                        ->where('deleted',0)
                        ->orderBy('sortid')
                        ->whereIn('acadprogid',$acadprog)
                        ->select('id','levelname','levelname as text')
                        ->get();
@endphp

<div class="modal fade" id="modal_document" style="display: none;" aria-hidden="true">
      <div class="modal-dialog modal-sm">
            <div class="modal-content">
                  <div class="modal-body">
                        <div class="row">
                              <div class="col-md-12 form-group">
                                    <label for="">Document Description</label>
                                    <input id="input_description" class="form-control" placeholder="Document Description" onkeyup="this.value = this.value.toUpperCase();" autocomplete="off" >
                              </div>
                              <div class="col-md-12 form-group">
                                    <label for="">Document Sort</label>
                                    <input id="input_sequence" class="form-control" placeholder="Document Sequence" onkeyup="this.value = this.value.toUpperCase();" autocomplete="off">
                              </div>
                              <div class="col-md-12 form-group">
                                    <label for="">Student Type</label>
                                    <select name="stud_type" id="stud_type" class="form-control select2">
                                          <option value="" >All</option>
                                          <option value="New">New Student</option>
                                          <option value="Transferee">Transferee Student</option>
                                    </select>
                              </div>
                              <div class="col-md-12">
                                    <div class="form-group clearfix">
                                          <div class="icheck-primary d-inline">
                                                <input type="checkbox" id="input_isrequired">
                                                <label for="input_isrequired">
                                                      Required
                                                </label>
                                          </div>
                                    </div>
                              </div>
                              <div class="col-md-12">
                                    <div class="form-group clearfix">
                                          <div class="icheck-primary d-inline">
                                                <input type="checkbox" id="input_isactive" checked>
                                                <label for="input_isactive">
                                                      Active
                                                </label>
                                          </div>
                                    </div>
                              </div>
                        </div>
                  </div>
                  <div class="modal-footer border-0">
                        <div class="col-md-6">
                              <button class="btn btn-primary btn-sm" id="create_document">Create</button>
                        </div>
                        <div class="col-md-6 text-right">
                              <button class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
                        </div>
                  </div>
            </div>
      </div>
</div>    

<div class="modal fade" id="copy_document" style="display: none;" aria-hidden="true">
      <div class="modal-dialog modal-sm">
            <div class="modal-content">
                  <div class="modal-body">
                        <div class="row">
                              <div class="col-md-12 form-group">
                                    <label for="" id="copy_description"></label>
                              </div>
                              <div class="col-md-12">
                                    <div class="form-group clearfix">
                                          <div class="icheck-primary d-inline ">
                                          <input type="checkbox" id="apply_to_all">
                                          <label for="apply_to_all">ALL GRADE LEVEL
                                          </label>
                                          </div>
                                    </div>
                              </div>
                              <div class="col-md-12 form-group">
                                    <label for="">Grade Level</label>
                                    <select name="to_gradelevel" id="to_gradelevel" class="form-control select2" multiple>

                                    </select>
                              </div>
                              
                        </div>
                  </div>
                  <div class="modal-footer border-0">
                        <div class="col-md-6">
                              <button class="btn btn-primary btn-sm" id="button_to_copy">Copy</button>
                        </div>
                        <div class="col-md-6 text-right">
                              <button class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
                        </div>
                  </div>
            </div>
      </div>
</div>    

<section class="content-header">
      <div class="container-fluid">
            <div class="row mb-2">
                  <div class="col-sm-6">
                        <h1>Document Requirement</h1>
                  </div>
                  <div class="col-sm-6">
                  <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/home">Home</a></li>
                        <li class="breadcrumb-item active">Document Requirement</li>
                  </ol>
                  </div>
            </div>
      </div>
</section>
    
<section class="content pt-0">
    
      <div class="container-fluid">
            <div class="row">
                  <div class="col-md-12">
                        <div class="card shadow">
                              <div class="card-body p-1">
                                   <p class="mb-0">Note: The document requirement would let the student know what requirements needed to bring the enrollment.</p>
                              </div>
                        </div>
                  </div>
            </div>
            <div class="row">
                  <div class="col-md-3">
                        <div class="info-box shadow-lg">
                          <div class="info-box-content">
                              <div class="row">
                                    <div class="col-md-12">
                                          <label for="">Grade Level</label>
                                          <select class="form-control select2" id="filter_gradelevel">
                                                @foreach ($gradelevel as $item)
                                                      <option value="{{$item->id}}">{{$item->levelname}}</option>
                                                @endforeach
                                          </select>
                                    </div>
                              </div>
                              {{-- <div class="row mt-3">
                                    <div class="col-md-12">
                                          <button class="btn btn-primary btn-sm" id="filter_button">Filter</button>
                                    </div>
                              </div> --}}
                          </div>
                        </div>
                  </div>
                  <div class="col-md-5">
                  </div>
                  <div class="col-md-2">
                        <div class="info-box shadow-lg">
                          <span class="info-box-icon bg-success"><i class="fas fa-calendar-alt"></i></span>
            
                          <div class="info-box-content">
                            <span class="info-box-text">Required</span>
                            <span class="info-box-number" id="total_required">0</span>
                          </div>
                        </div>
                  </div>
                  <div class="col-md-2">
                        <div class="info-box shadow-lg">
                          <span class="info-box-icon bg-danger"><i class="fas fa-calendar-alt"></i></span>
            
                          <div class="info-box-content">
                            <span class="info-box-text">Active</span>
                            <span class="info-box-number" id="total_active">0</span>
                          </div>
                        </div>
                  </div>
            </div>
            <div class="row">
                  <div class="col-md-12 text-right">
                        <button class="btn btn-primary btn-sm" id="copy_all" disabled><i class="fas fa-copy" ></i> Copy</button>
                        <button class="btn btn-primary  btn-sm" id="button_document" disabled><i class="fas fa-plus"></i> Add Document Requirement</button>
                  </div>
            </div>
            <div class="row mt-3">
                  <div class="col-md-12">
                        <div class="card shadow">
                              <div class="card-body">
                                   
                                    <div class="row">
                                          <div class="col-md-12">
                                                <table class="table table-striped table-bordered table-head-fixed nowrap display table-sm p-0" id="document_setup" width="100%">
                                                      <thead>
                                                            <tr> 
                                                                  <th width="5%">Sort</th>
                                                                  <th width="40%">Description</th>
                                                                  <th width="15%">Student Type</th>
                                                                  <th width="5%">Active</th>
                                                                  <th width="10%">Required</th>
                                                                  <th width="5%"></th>
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
      

      <script>

           
            $(document).ready(function(){

                  var all_document = []
                  var selected_document
                  var process = 'create'
                  var gradelevel = @json($gradelevel)

                  $("#filter_gradelevel").empty()
                  $("#filter_gradelevel").append('<option value="">Select Grade Level</option>')
                  $("#filter_gradelevel").select2({
                        data: gradelevel,
                        allowClear: true,
                        placeholder: "Select Grade Level",
                  })

                  // $('.select2').select2()

                  const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                  })

                  loaddatatable()
                 
                  // $(document).on('click','#filter_button',function(){
                  //       $('#copy_all').removeAttr('disabled','disabled')
                  //       $('#button_document').removeAttr('disabled','disabled')
                  //       get_document()
                  // })

                  $(document).on('change','#filter_gradelevel',function(){
                        if($(this).val() == ""){
                              $('#copy_all').attr('disabled','disabled')
                              $('#button_document').attr('disabled','disabled')
                              all_document = []
                              loaddatatable()
                              return false;
                        }
                        $('#copy_all').removeAttr('disabled','disabled')
                        $('#button_document').removeAttr('disabled','disabled')
                        get_document()
                  })

                  // $(document).on('change','#filter_gradelevel',function(){
                  //       $('#copy_all').attr('disabled','disabled')
                  //       $('#button_document').attr('disabled','disabled')
                  //       all_document = []
                  //       loaddatatable()
                  // })


                  $(document).on('click','#button_document',function(){
                        process = 'create'
                        $('#input_isrequired').prop('checked',false)
                        $('#input_isactive').prop('checked',true)
                        $('#input_description').val("")
                        $('#input_sequence').val("")
                        $('#input_acadprog').val("").change()
                        $('#create_document').text('Create')  
                        $('#modal_document').modal()    
                  })

                  $(document).on('click','#create_document',function(){
                        if(process == 'create'){
                              var temp_description = $('#input_description').val()
                              var temp_description = all_document.filter(x=>x.description == temp_description)
                              if(temp_description.length > 0){
                                    Toast.fire({
                                          type: 'warning',
                                          title: 'Document requirement already exist'
                                    })
                              }else{
                                    create_document()    
                              }

                                    
                        }else if(process == 'edit'){
                              update_document()  
                        }              
                  })

                  $(document).on('click','.delete_document',function(){
                        selected_document = $(this).attr('data-id')
                        delete_document()
                  })

                  $(document).on('click','.edit_document',function(){
                        selected_document = $(this).attr('data-id')
                        process = 'edit'

                        var temp_document_id = all_document.filter(x=>x.id == selected_document)
                       
                        $('#input_description').val(temp_document_id[0].description)
                        $('#input_sequence').val(temp_document_id[0].docsort)
                        $('#input_acadprog').val(temp_document_id[0].acadprogid).change()
                        $('#stud_type').val(temp_document_id[0].doc_studtype).change()

                        if(temp_document_id[0].isActive == 1){
                              $('#input_isactive').prop('checked',true)
                        }else{
                              $('#input_isactive').prop('checked',false)
                        }

                        if(temp_document_id[0].isRequired == 1){
                              $('#input_isrequired').prop('checked',true)
                        }else{
                              $('#input_isrequired').prop('checked',false)
                        }



                        
                        $('#modal_document').modal()   
                        $('#create_document').text('Update')           
                  })

                

                  function get_document(){
                        $.ajax({
					type:'GET',
					url: '/superadmin/setup/document/list',
                              data:{
                                    levelid:$('#filter_gradelevel').val()
                              },
					success:function(data) {
                                    if(data.length == 0){
                                          Toast.fire({
                                                type: 'warning',
                                                title: "Document setup is empty."
                                          })
                                          all_document = []
                                          loaddatatable()
                                    }else{
                                          Toast.fire({
                                                type: 'info',
                                                title: data.length+" document(s) found."
                                          })
                                          all_document = data
                                          loaddatatable()
                                    }
					}
				})
                  }

                  function create_document(){

                        var isactive = 0;
                        var isrequied = 0;
                        var isvalid = true;

                        if($('#input_isrequired').prop('checked') == true){
                              isrequied = 1
                        }
                        if($('#input_isactive').prop('checked') == true){
                              isactive = 1
                        }

                        if($('#input_description').val() == ""){
                              Toast.fire({
                                    type: 'warning',
                                    title: 'Document description is empty!'
                              })    
                              isvalid = false
                        }else if($('#input_sequence').val() == ""){
                              Toast.fire({
                                    type: 'warning',
                                    title: 'Document sequence is empty!'
                              })
                              isvalid = false
                        }

                        if(isvalid){
                              $.ajax({
                                    type:'GET',
                                    url: '/superadmin/setup/document/create',
                                    data:{
                                          description:$('#input_description').val(),
                                          sequence:$('#input_sequence').val(),
                                          isactive:isactive,
                                          isrequired:isrequied,
                                          levelid:$('#filter_gradelevel').val(),
                                          studtype:$('#stud_type').val()
                                    },
                                    success:function(data) {
                                          if(data[0].status == 1){
                                                Toast.fire({
                                                      type: 'success',
                                                      title: data[0].data
                                                })
                                                $('#modal_document').modal('hide')
                                                all_document = data[0].info
                                                loaddatatable()
                                          }
                                          else if(data[0].status == 2){
                                                Toast.fire({
                                                      type: 'warning',
                                                      title: 'Document requirement already exist'
                                                }) 
                                          }
                                          else{
                                                Toast.fire({
                                                      type: 'error',
                                                      title: data[0].data
                                                })
                                          }
                                    }
                              })
                        }
                      
                  }

                  function update_document(){

                        var isactive = 0;
                        var isrequied = 0;
                        var isvalid = true;

                        if($('#input_isrequired').prop('checked') == true){
                              isrequied = 1
                        }
                        if($('#input_isactive').prop('checked') == true){
                              isactive = 1
                        }

                        var temp_document_id = all_document.filter(x=>x.id != selected_document && x.description == $('#input_description').val())

                        if(temp_document_id.length > 0){
                              Toast.fire({
                                    type: 'warning',
                                    title: 'Document requirement already exist'
                              }) 
                              isvalid = false
                        }
                        else if($('#input_description').val() == ""){
                              Toast.fire({
                                    type: 'warning',
                                    title: 'Document description is empty!'
                              })    
                              isvalid = false
                        }else if($('#input_sequence').val() == ""){
                              Toast.fire({
                                    type: 'warning',
                                    title: 'Document sequence is empty!'
                              })
                              isvalid = false
                        }
                        
                        if(isvalid){
                              $.ajax({
                                    type:'GET',
                                    url: '/superadmin/setup/document/update',
                                    data:{
                                          description:$('#input_description').val(),
                                          sequence:$('#input_sequence').val(),
                                          isactive:isactive,
                                          isrequired:isrequied,
                                          documentid:selected_document,
                                          levelid:$('#filter_gradelevel').val(),
                                          studtype:$('#stud_type').val()
                                    },
                                    success:function(data) {
                                          if(data[0].status == 1){
                                                Toast.fire({
                                                      type: 'success',
                                                      title: data[0].data
                                                })
                                                all_document = data[0].info

                                             
                                                $('#modal_document').modal('hide')
                                                loaddatatable()
                                          }else{
                                                Toast.fire({
                                                      type: 'error',
                                                      title: data[0].data
                                                })
                                          }
                                    }
                              })
                        }
                        
                  }

                  function delete_document(){
                        Swal.fire({
                              title: 'Do you want to remove document requirement?',
                              type: 'warning',
                              showCancelButton: true,
                              confirmButtonColor: '#3085d6',
                              cancelButtonColor: '#d33',
                              confirmButtonText: 'Remove'
                        }).then((result) => {
                              if (result.value) {
                                    $.ajax({
                                          type:'GET',
                                          url: '/superadmin/setup/document/delete',
                                          data:{
                                                documentid:selected_document
                                          },
                                          success:function(data) {
                                                if(data[0].status == 1){
                                                      Toast.fire({
                                                            type: 'success',
                                                            title: data[0].data
                                                      })
                                                      all_document = data[0].info
                                                      loaddatatable()
                                                }else{
                                                      Toast.fire({
                                                            type: 'error',
                                                            title: data[0].data
                                                      })
                                                }
                                          }
                                    })
                              }
                        })
                  }

                  
                  $(document).on('click','.copy_document',function(){

                        $("#to_gradelevel").val([]).change();
                        $('#apply_to_all').prop('checked',false)

                        selected_document = $(this).attr('data-id')
                        $('#copy_document').modal()
                        var current_gradelevel = $('#filter_gradelevel').val()
                        var temp_gradelevel = gradelevel.filter(x=>x.id != current_gradelevel)
                        var temp_document = all_document.filter(x=>x.id == selected_document)
                        $('#copy_description')[0].innerHTML = 'Copying <i class="text-success">'+temp_document[0].description+'</i>'
                        $("#to_gradelevel").select2({
                              data: temp_gradelevel,
                              placeholder: "Select gradelevel",
                              theme: 'bootstrap4'
                        })


                  })

                  $(document).on('click','#copy_all',function(){

                        $("#to_gradelevel").val([]).change();
                        $('#apply_to_all').prop('checked',false)

                        selected_document = null
                        $('#copy_document').modal()
                        var current_gradelevel = $('#filter_gradelevel').val()
                        var temp_gradelevel = gradelevel.filter(x=>x.id != current_gradelevel)
                        $("#to_gradelevel").select2({
                              data: temp_gradelevel,
                              placeholder: "Select gradelevel",
                              theme: 'bootstrap4'
                        })

                        var temp_gradelevel = gradelevel.filter(x=>x.id == current_gradelevel)
                        $('#copy_description')[0].innerHTML = 'Copying all requirements from <br><i class="text-success">'+temp_gradelevel[0].text+'</i>'
                  })

                  $(document).on('click','#button_to_copy',function(){
                        copy_document()
                  })
                  
                  $(document).on('click','#apply_to_all',function(){
                        var temp_gradelevel = []
                        var current_gradelevel = $('#filter_gradelevel').val()
                        if($(this).prop('checked') == true){
                              var acad_level = gradelevel.filter(x=>x.id != current_gradelevel)
                              $.each(acad_level,function(a,b){
                                    temp_gradelevel.push(b.id)
                              })
                              $("#to_gradelevel").val(temp_gradelevel).change()
                        }else{
                              $("#to_gradelevel").val(temp_gradelevel).change()
                        }
                  })

                  function copy_document(){
                        Swal.fire({
                              title: 'Do you want to copy document requirement?',
                              type: 'warning',
                              showCancelButton: true,
                              confirmButtonColor: '#3085d6',
                              cancelButtonColor: '#d33',
                              confirmButtonText: 'Copy'
                        }).then((result) => {
                              if (result.value) {
                                    $.ajax({
                                          type:'GET',
                                          url: '/superadmin/setup/document/copy',
                                          data:{
                                                documentid:selected_document,
                                                gradelevel_from:$('#filter_gradelevel').val(),
                                                gradelevel_to:$('#to_gradelevel').val()
                                          },
                                          success:function(data) {
                                                if(data[0].status == 1){
                                                      Toast.fire({
                                                            type: 'success',
                                                            title: data[0].data
                                                      })
                                                }else{
                                                      Toast.fire({
                                                            type: 'error',
                                                            title: data[0].data
                                                      })
                                                }
                                          }
                                    })
                              }
                        })
                  }

                  function loaddatatable(){
                        
                        $('#total_required').text(all_document.filter(x=>x.isRequired == 1).length)
                        $('#total_active').text(all_document.filter(x=>x.isActive == 1).length)

                        $("#document_setup").DataTable({
                                    destroy: true,
                                    autoWidth: false,
                                    pageLength: 50,
                                    paging: false,
                                    bInfo: false,
                                    data:all_document,
                                   
                                    columns: [
                                          { "data": "docsort"},
                                          { "data": "description" },
                                          { "data": "doc_studtype" },
                                          { "data": "isActive" },
                                          { "data": "isRequired" },
                                          { "data": null },
                                          { "data": null },
                                          { "data": null },
                                        
                                    ],

                                    columnDefs: [
                                                      {
										'targets': 0,
										'orderable': true, 
										'createdCell':  function (td, cellData, rowData, row, col) {
                                                                  $(td).addClass('text-center')
										}
                                    			},
                                                      {
										'targets': 2,
										'orderable': true, 
										'createdCell':  function (td, cellData, rowData, row, col) {
                                                                  var doc_studtype = rowData.doc_studtype
                                                                  if(rowData.doc_studtype == null){
                                                                        doc_studtype = 'All'
                                                                  }
                                                                  $(td).text(doc_studtype)
										}
                                    			},
                                                      {
										'targets': 3,
										'orderable': true, 
										'createdCell':  function (td, cellData, rowData, row, col) {
                                                                  if(rowData.isActive == 1){
                                                                        $(td)[0]. innerHTML = '<i class="fas fa-check-square text-success"></i>'
                                                                  }else{
                                                                        $(td)[0]. innerHTML = '<i class="fas fa-times-circle text-danger"></i>'
                                                                  }
                                                                  $(td).addClass('text-center')
										}
                                    			},
                                                      {
										'targets': 4,
										'orderable': true, 
										'createdCell':  function (td, cellData, rowData, row, col) {
                                                                  if(rowData.isRequired == 1){
                                                                        $(td)[0]. innerHTML = '<i class="fas fa-check-square text-success"></i>'
                                                                  }else{
                                                                        $(td)[0]. innerHTML = '<i class="fas fa-times-circle  text-danger"></i>'
                                                                  }
                                                                  $(td).addClass('text-center')
										}
                                    			},
                                                      {
										'targets': 5,
										'orderable': true, 
										'createdCell':  function (td, cellData, rowData, row, col) {
											var buttons = '<a href="#" class="copy_document" data-id="'+rowData.id+'"><i class="fas fa-copy text-primary"></i></a>';
											$(td)[0].innerHTML =  buttons
											$(td).addClass('text-center')
										}
                                    			},
                                                     
                                                      {
										'targets': 6,
										'orderable': true, 
										'createdCell':  function (td, cellData, rowData, row, col) {
											var buttons = '<a href="#" class="edit_document" data-id="'+rowData.id+'"><i class="far fa-edit"></i></a>';
											$(td)[0].innerHTML =  buttons
											$(td).addClass('text-center')
                                                              
										}
                                    			},
                                                      {
										'targets': 7,
										'orderable': true, 
										'createdCell':  function (td, cellData, rowData, row, col) {
											var buttons = '<a href="#" class="delete_document" data-id="'+rowData.id+'"><i class="far fa-trash-alt text-danger"></i></a>';
											$(td)[0].innerHTML =  buttons
											$(td).addClass('text-center')
										}
                                    			},
								]
                        });
                  
                  }

                  var keysPressed = {};

                  document.addEventListener('keydown', (event) => {
                        keysPressed[event.key] = true;
                        console.log(event.keyCode)
                        if (keysPressed['p'] && event.key == 'v') {
                              Toast.fire({
                                          type: 'warning',
                                          title: 'Date Version: 07/21/2021 03:53'
                                    })
                        }
                  });

                  document.addEventListener('keyup', (event) => {
                        delete keysPressed[event.key];
                  });
                
            })
      </script>
     
@endsection


