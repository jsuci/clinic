@php

      if(!Auth::check()){ 
            header("Location: " . URL::to('/'), true, 302);
            exit();
      }
      $check_refid = DB::table('usertype')->where('id',Session::get('currentPortal'))->select('refid')->first();
      if(Session::get('currentPortal') == 17){
            $extend = 'superadmin.layouts.app2';
      }else if(Session::get('currentPortal') == 3){
            $extend = 'registrar.layouts.app';
      }else if(Session::get('currentPortal') == 6){
            $extend = 'adminPortal.layouts.app2';
      }else if(Session::get('currentPortal') == 2){
            $extend = 'principalsportal.layouts.app2';
      }
      else{
        if(isset($check_refid->refid)){
              if($check_refid->refid == 27){
                    $extend = 'academiccoor.layouts.app2';
              }
        }else{
          header("Location: " . URL::to('/'), true, 302);
          exit();
        }
       
      }

      $sy = DB::table('sy')->select('id','sydesc as text','isactive','sydesc')->get();
@endphp

@extends($extend)

@section('pagespecificscripts')

  <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
  <link rel="stylesheet" href="{{ asset('plugins/datatables-fixedcolumns/css/fixedColumns.bootstrap4.css') }}">

  <style>
        .shadow {
              box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
              border: 0 !important;
        }

        .dataTables_scrollBody, .dataTables_wrapper {
            position: static !important;
        }
        .dropdown-button {
            cursor: pointer;
            font-size: 1rem;
            display:block
        }
        .dropdown-menu i {
            font-size: 1rem;
            line-height: 0em;
            vertical-align: -15%;
            color: #212529;
            font-weight: 400;
        }
       
  </style>
@endsection



@section('content')

<section class="content-header">
  <div class="container-fluid">
        <div class="row mb-2">
              <div class="col-sm-6">
                    <h1>Buildings</h1>
              </div>
              <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/home">Home</a></li>
                    <li class="breadcrumb-item active">Buildings</li>
              </ol>
              </div>
        </div>
  </div>
</section>
<section class="content p-0">
    <div class="container-fluid">
        <div class="card shadow">
          <div class="card-body" style="font-size:.8rem !important">
            <div class="row">
              <div class="col-md-12" id="building_datatable_holder">
                
              </div>
            </div>
          </div>
      </div>
    </div>
</section>
@endsection


@section('modalSection')
<div class="modal fade" id="view_bldginfo_modal" style="display: none; padding-right: 17px;" aria-hidden="true">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header pb-2 pt-2 border-0">
                <h4 class="modal-title" style="font-size: 1.1rem !important">Building Information : <span id="room_name"></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span></button>
          </div>
            <div class="modal-body pt-0">
               <div class="row">
                  <div class="col-md-2">
                    <div class="row">
                      <div class="col-md-12">
                        <div class="card shadow h-100">
                          <div class="card-body p-2" style="font-size: .8rem! important">
                            <div class="row mt-2">
                              <div class="col-md-12 form-group mb-2">
                                <label>Building Name</label>
                                <input id="bldngDesc" class="form-control form-control-sm" onkeyup="this.value = this.value.toUpperCase();">
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-md-12 form-group mb-2">
                                <label>Building Capacity</label>
                                <input id="bldngCap" class="form-control form-control-sm" min="1" oninput="this.value=this.value.replace(/[^0-9]/g,'');" >
                              </div>
                            </div>
                            {{-- <div class="row">
                              <div class="col-md-12 form-group">
                                <label>Building</label>
                                <select name="building" id="update_roombuilding" class="form-control form-control-sm select2">
                                    <option selected value="">SELECT BUILDING</option>
                                </select>
                              </div>
                            </div> --}}
                            <div class="row mt-4">
                              <div class="col-md-12 ">
                                <button class="btn btn-success btn-sm btn-block" id="update_information" style="font-size:.8rem !important">
                                  <i class="fa fa-save"></i> Update Information
                                </button>
                                <button class="btn btn-danger btn-sm btn-block mt-2" id="update_information" style="font-size:.8rem !important">
                                    <i class="fa fa-trash"></i> Delete Information
                                  </button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-10">
                    <div class="row">
                      <div class="col-md-12">
                        <div class="card shadow">
                          <div class="card-body p-2">
                            <div class="row">
                              <div class="col-md-6">
                                <button type="button" id="print_sched" class="btn btn-sm btn-outline-primary" style="font-size:.8rem !important"  ><i class="fa fa-print mr-1" ></i>Print Schedule</button>
                              </div>
                              <div class="col-md-4 text-right">
                                <label for="" style="font-size:.9rem !important">School Year: </label> 
                              </div>
                              <div class="col-md-2">
                                  <select class="form-control form-control-sm teacher select2"  id="filter_acad_sy">
                                      @foreach ($sy as $item)
                                            @if($item->isactive == 1)
                                                  <option value="{{$item->id}}" selected="selected">{{$item->text}}</option>
                                            @else
                                                  <option value="{{$item->id}}">{{$item->text}}</option>
                                            @endif
                                      @endforeach
                                  </select>
                              </div>
                              {{-- <div class="col-md-1 text-right">
                                <label for="" style="font-size:.9rem !important">Semester: </label> 
                              </div>
                              <div class="col-md-2">
                                  <select class="form-control form-control-sm teacher select2"  id="filter_semester">
                                      @foreach ($semester as $item)
                                            @if($item->isactive == 1)
                                                  <option value="{{$item->id}}" selected="selected">{{$item->text}}</option>
                                            @else
                                                  <option value="{{$item->id}}">{{$item->text}}</option>
                                            @endif
                                      @endforeach
                                  </select>
                              </div> --}}
                            </div>
                           
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <div class="card shadow">
                          <div class="card-body p-2">
                            {{-- <div class="row">
                              <div class="col-md-2">
                                <button class="btn btn-sm btn-primary add_sched" >Add Schedule</button>
                              </div>
                                <div class="col-md-5"></div>
                                <div class="col-md-2">
                                  <button class="btn btn-sm btn-primary btn-block" id="time_templatelist">Time Template List</button>
                                </div>
                                <div class="col-md-3  mt-1">
                                    <select class="form-control form-control-sm teacher select2"  id="filter_timetemplate">
                                    </select>
                                </div>
                              
                            </div> --}}
                            <div class="row mt-3">
                              <div class="col-md-12">
                                {{-- <p class="mb-0 text-sm"><i>Click the subject name to edit or delete schedule.</i></p> --}}
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-md-12 table-responsive tableFixHead" style="height: 400px;">
                                <table class="table-sm table-bordered table table-head-fixed mb-0" id="sched_holder"  style="font-size:.7rem !important">
                                  
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
        </div>
      </div>
  </div>
@endsection



@section('footerjavascript')
      <script src="{{asset('plugins/datatables/jquery.dataTables.js') }}"></script>
      <script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
      {{-- <script src="{{asset('js/setupjs/buildings.js') }}"></script> --}}
      <script>
     
      </script>

      <script>
            $(document).ready(function(){
      
                  var keysPressed = {};
      
                  document.addEventListener('keydown', (event) => {
                        keysPressed[event.key] = true;
                        if (keysPressed['p'] && event.key == 'v') {
                              Toast.fire({
                                          type: 'warning',
                                          title: 'Date Version: 11/16/2022'
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

                  buildingtable('#building_datatable_holder',true)
                  buildingform('',true)
            })
      </script>

      <script>
            
            var buildings = []
            var buildings_datatable = []
            var selected_id = null
            var projectsetup = []
            var syncEnabled = false;
            var button_enable = null;
            var connected_stat = false

            const Toast = Swal.mixin({
                  toast: true,
                  position: 'top-end',
                  showConfirmButton: false,
                  timer: 2000,
            })

            function getProjectSetup(){
                  $.ajax({
                        type:'GET',
                        url:'/api/schoolinfo/projectsetup',
                        success:function(data) {
                              projectsetup = data
                              if(projectsetup[0].projectsetup == 'offline' && ( projectsetup[0].processsetup == 'hybrid1' || projectsetup[0].processsetup == 'hybrid2' ) ){
                                    syncEnabled = true
                                    check_online_connection()
                              }

                              if(projectsetup[0].projectsetup == 'online' && ( projectsetup[0].processsetup == 'hybrid1' || projectsetup[0].processsetup == 'hybrid2' ) ){
                                    button_enable = false
                              }else{
                                    button_enable = true
                              }
                              buildingDatatable()
                              
                        }
                  })
            }


            function check_online_connection(){
                  $.ajax({
                        type:'GET',
                        url: projectsetup[0].es_cloudurl+'/checkconnection',
                        success:function(data) {
                        connected_stat = true
                        get_last_index('building',true)
                        }, 
                        error:function(){
                        $('#online_status').text('Not Connected')
                        }
                  })
            }

            function get_last_index(tablename,first=false){
                  if(!connected_stat){
                  return false
                  }
                  $.ajax({
                        type:'GET',
                        url: '/api/building/getnewinfo',
                        data:{
                              tablename: tablename
                        },
                        success:function(data) {
                        process_create(tablename,data,first)
                        },
                  })
            }

            function process_create(tablename,createdata,first=false){
                  if(createdata.length == 0){
                        if(first){
                        get_updated(tablename)
                        get_deleted(tablename)
                        }
                        return false;
                  }
                  var b = createdata[0]
                  $.ajax({
                        type:'GET',
                        url: projectsetup[0].es_cloudurl+'/api/building/syncnew',
                        data:{
                              tablename: tablename,
                              data:b
                        },
                        success:function(data) {
                              createdata = createdata.filter(x=>x.id != b.id)
                              update_local_status(tablename,createdata,b,'create',first)
                        },
                        error:function(){
                              createdata = createdata.filter(x=>x.id != b.id)
                              update_local_status(tablename,createdata,b,'create',first)
                        }
                  })
            }

            //get_updated
            function get_updated(tablename){
                  if(!connected_stat){
                  return false
                  }
                  $.ajax({
                        type:'GET',
                        url: '/api/building/getupdated',
                        data:{
                              tablename: tablename,
                        },
                        success:function(data) {
                              process_update(tablename,data)
                        }
                  })
            }

            function process_update(tablename , updated_data){
                  if (updated_data.length == 0){
                        return false
                  }
                  var b = updated_data[0]
                  $.ajax({
                        type:'GET',
                        url:  projectsetup[0].es_cloudurl+'/api/building/syncupdate',
                        data:{
                              tablename: tablename,
                              data:b
                        },
                        success:function(data) {
                              updated_data = updated_data.filter(x=>x.id != b.id)
                              update_local_status(tablename,updated_data,b,'update')
                        },
                  })
            }


            //get deleted
            function get_deleted(tablename){
                  if(!connected_stat){
                        return false
                  }
                  $.ajax({
                        type:'GET',
                        url: '/api/building/getdeleted',
                        data:{
                              tablename: tablename
                        },
                        success:function(data) {
                        process_deleted(tablename,data)
                        }
                  })
            }
                  

            function process_deleted(tablename , deleted_data){
            if (deleted_data.length == 0){
                        return false
            }
            var b = deleted_data[0]
            $.ajax({
                  type:'GET',
                  url: projectsetup[0].es_cloudurl+'/api/building/syncdelete',
                  data:{
                        tablename: tablename,
                        data:b
                  },
                  success:function(data) {
                        deleted_data = deleted_data.filter(x=>x.id != b.id)
                        update_local_status(tablename,deleted_data,b,'delete')
                  },
            })
            }

            function update_local_status(tablename,alldata,info,status,first=false){
                  $.ajax({
                        type:'GET',
                        url:  '/api/building/updatestat',
                        data:{
                              tablename: tablename,
                              data:info
                        },
                        success:function(data) {
                        if(status == 'delete'){
                        process_update(tablename,alldata)
                        }else if(status == 'update'){
                        process_update(tablename,alldata)
                        }else if(status == 'create'){
                        process_create(tablename,alldata,data,first)
                        }
                        
                        },
                  })
            }



            function validate_bldg_input() {
                  var desc = $('#bldngDesc').val();
                  var cap = $('#bldngCap').val()

                  if (!desc || !cap) {
                        return false
                  } else {
                        return true
                  }
            }

            function getBuildings(){
                  $.ajax({
                        type:'GET',
                        url:'/api/buildings',
                        success:function(data) {
                              if(data[0].status == 1){
                                    buildings = data[0].data
                              }else{

                              }
                        }
                  })
            }

            function buildingCreate(){
                  // validate input here
                  if (!validate_bldg_input()) {
                        Toast.fire({
                              type: 'error',
                              title: 'Error empty field'
                        })
                  } else {
                        $.ajax({
                        type:'GET',
                        url:'/api/building/create',
                        data:{
                              'description':$('#bldngDesc').val(),
                              'capacity':$('#bldngCap').val()
                        },
                        success:function(data) {
                              if(data[0].status == 1){

                                    $('#bldngDesc').val("")
                                    $('#bldngCap').val("")
                                    
                                    if($('#building_form_modal')){
                                          $('#building_form_modal').modal('hide')
                                    }

                                    buildingDatatable()
                                    get_last_index('building')
                              }
                              Toast.fire({
                                    type: data[0].icon,
                                    title: data[0].message
                              })
                        }
                  })
                  }
            }

            function buildingUpdate(){
                  // validate input here
                  if (!validate_bldg_input()) {
                        Toast.fire({
                              type: 'error',
                              title: 'Error empty field'
                        })
                  } else {
                        $.ajax({
                        type:'GET',
                        url:'/api/building/update',
                        data:{
                              'description':$('#bldngDesc').val(),
                              'capacity':$('#bldngCap').val(),
                              'id':selected_id
                        },
                        success:function(data) {
                              if(data[0].status == 1){

                                    $('#bldngDesc').val("")
                                    $('#bldngCap').val("")
                                    
                                    if($('#building_form_modal')){
                                          $('#building_form_modal').modal('hide')
                                    }

                                    buildingDatatable()
                                    get_updated('building')
                              }
                              Toast.fire({
                                    type: data[0].icon,
                                    title: data[0].message
                              })
                        }
                  })
                  }

            }

            function buildingDelete(){

                  Swal.fire({
                        text: 'Are you sure you delete building?',
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Delete'
                  }).then((result) => {
                        if (result.value) {
                              $.ajax({
                                    type:'GET',
                                    url:'/api/building/delete',
                                    data:{
                                          'id':selected_id
                                    },
                                    success:function(data) {
                                          if(data[0].status == 1){
                                                buildingDatatable()
                                                get_deleted('building')
                                          }
                                          Toast.fire({
                                                type: data[0].icon,
                                                title: data[0].message
                                          })
                                    }
                              })
                        }
                  })

            
            }

            function buildingform(formholder,modal=false){


                  var buildingform = `<div class="row">
                                          <div class="col-md-12 form-group">
                                                <label>Description</label>            
                                                <input class="form-control form-control-sm" id="bldngDesc" onkeyup="this.value = this.value.toUpperCase();" >
                                          </div>
                                    </div>
                                    <div class="row">
                                          <div class="col-md-12 form-group">
                                                <label>Capacity</label>            
                                                <input class="form-control form-control-sm" id="bldngCap" oninput="this.value=this.value.replace(/[^0-9]/g,'');">
                                          </div>
                                    </div>
                                    <div class="row">
                                          <div class="col-md-12">
                                                <button class="btn btn-sm btn-primary" id="building_create_button"><i class="fa fa-save"></i> Save</button>
                                                <button class="btn btn-success btn-success btn-sm" id="building_update_button" hidden><i class="fa fa-save"></i> Update</button>
                                          </div>
                              </div>`


                  var buildingmodal = `<div class="modal fade" id="building_form_modal" style="display: none;" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                    <div class="modal-dialog modal-sm">
                                          <div class="modal-content">
                                          <div class="modal-header pb-2 pt-2 border-0">
                                                      <h4 class="modal-title" style="font-size: 1.1rem !important">Building Form</h4>
                                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                      <span aria-hidden="true">×</span></button>
                                          </div>
                                                <div class="modal-body pt-0">
                                                      `+buildingform+`
                                                </div>
                                          </div>
                                    </div>
                              </div>`

                  if(!modal){
                        $(formholder)[0].innerHTML = buildingform
                  }else{
                        $('body').append(buildingmodal)
                  }

            }

            function buildingtable(datatableholder){

                  var table = `<table class="table table-sm" id="buildings_datatable">
                                    <thead>
                                          <tr>
                                                <th width="55%">Description</th>
                                                <th width="40%">Capacity</th>
                                                <th width="5%"></th>
                                          </tr>
                                    </thead>
                                    <tbody>
                                    
                                    </tbody>
                              </table>`

                  $(datatableholder)[0].innerHTML = table
                  buildingDatatable()
            }


            function buildingDatatable(){

                  if(button_enable == null){
                        getProjectSetup()
                        return false
                  }

                  $('#buildings_datatable').DataTable({
                        destroy: true,
                        autoWidth: false,
                        lengthChange: false,
                        stateSave: true,
                        serverSide: true,
                        processing: true,
                        ajax:{
                              url: '/api/buildings/datatable',
                              type: 'GET',
                              dataSrc: function ( json ) {
                                    buildings_datatable = json.data
                                    return json.data;
                              }
                        },
                        columns: [
                                    { "data": "description" },
                                    { "data": "capacity" },
                                    { "data": null },
                              ],
                        columnDefs: [
                        {
                        'targets': 0,
                        'createdCell':  function (td, cellData, rowData, row, col) {
                              $(td).addClass('align-middle')
                        }
                        },
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

                                    if(button_enable){
                                          var buttons = ` <div class="dropdown text-center">
                                                                  <!-- <a class="dropdown-button"  data-toggle="dropdown" data-boundary="viewport" aria-haspopup="true" aria-expanded="false">
                                                                        <i class="fa fa-ellipsis-v"></i>
                                                                  </a>
                                                                  <div class="dropdown-menu" >
                                                                        <a class="dropdown-item building_edit" href="javascript:void(0)" data-id="`+rowData.id+`">
                                                                              Edit
                                                                        </a>
                                                                        <a class="dropdown-item building_delete" href="javascript:void(0)"  data-id="`+rowData.id+`">
                                                                              Delete
                                                                        </a>
                                                                  </div> -->
                                                            </div>`

                                          if(rowData.id == null){
                                                buttons = '<spa style="line-height: 1 !important; font-size:1rem !important">&nbsp;</span>'
                                          }
                                    }else{
                                          buttons = ''
                                    }

                                    $(td)[0].innerHTML =  buttons
                                    $(td).addClass('text-center')
                                    $(td).addClass('align-middle')
                              }
                        },
                        ],
                        createdRow: function (row, data, dataIndex) {
                              $(row).attr("data-id",data.id);
                              $(row).addClass("view_info");
                        },
                  });
                  
                  var label_text = $($("#buildings_datatable_wrapper")[0].children[0])[0].children[0]

                  if(button_enable){
                        $(label_text)[0].innerHTML = '<button class="btn btn-sm btn-primary" id="building_create" style="font-size:.8rem !important"> <i class="fa fa-plus"></i> Add New</button>'
                  }else{
                        $(label_text)[0].innerHTML = ''
                  }

                  

            }

            $(document).on('click','#building_create',function(){
                  $('#bldngDesc').val("")
                  $('#bldngCap').val("")

                  $('#building_create_button').removeAttr('hidden')
                  $('#building_update_button').attr('hidden','hidden')

                  if($('#building_form_modal')){
                              $('#building_form_modal').modal()
                  }
            })

            $(document).on('click','.building_edit',function(){
                  var tempId = $(this).attr('data-id')
                  selected_id = tempId
                  var temp_bldnginfo = buildings_datatable.filter(x=>x.id == tempId)

                  $('#bldngDesc').val(temp_bldnginfo[0].description)
                  $('#bldngCap').val(temp_bldnginfo[0].capacity)


                  $('#building_update_button').removeAttr('hidden')
                  $('#building_create_button').attr('hidden','hidden')
            
                  if($('#building_form_modal')){
                        $('#building_form_modal').modal()
                  }
            })

            $(document).on('click','.building_delete',function(){
                  var tempId = $(this).attr('data-id')
                  selected_id = tempId
                  buildingDelete()
            })

            $(document).on('click','#building_update_button',function(){
                  buildingUpdate()
            })

            $(document).on('click','#building_create_button',function(){
                  buildingCreate()
            })

            $(document).on('click','.view_info',function(){
                  var temp_id = $(this).attr('data-id')
                  var temp_bldnginfo = buildings_datatable.filter(x=>x.id == temp_id)
                  
                  $('#bldngDesc').val(temp_bldnginfo[0].description)
                  $('#bldngCap').val(temp_bldnginfo[0].capacity)

                  select_id = temp_id

                  if (select_id) {
                        $('#view_bldginfo_modal').modal()
                  }

                  // // $('#print_sched').attr('data-id',select_id)

                  // $('#update_roomname').val(data[0].roomname)
                  // $('#update_roomcap').val(data[0].capacity)
                  // $('#update_roombuilding').val(data[0].buildingid).change()
                  // $('#room_name').text(data[0].roomname)
                  // // $('#room_form_modal').modal()
                  // $('#create_room').text('Update')
                  // $('#create_room').removeClass('btn-primary')
                  // $('#create_room').addClass('btn-success')
                  // $('#create_room').attr('data-id',2)
            })
      </script>
@endsection

