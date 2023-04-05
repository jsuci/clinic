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
      else {
            if(isset($check_refid->refid)){
                  if($check_refid->refid == 27) {
                        $extend = 'academiccoor.layouts.app2';
                  }
            } else {
                  header("Location: " . URL::to('/'), true, 302);
                  exit();
            }
      }

      $sy = DB::table('sy')->select('id','sydesc as text','isactive','sydesc')->get();
@endphp

@extends($extend)

@section('pagespecificscripts')

<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-fixedcolumns/css/fixedColumns.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
<link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">


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
      .select2-selection__rendered {
            margin: -9px !important;
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
                  <h4 class="modal-title" style="font-size: 1.1rem !important">Building Information : <span id="bldg_name"></span>
                  </h4>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                  </button>
            </div>
            <div class="modal-body pt-0">
                  <div class="row">
                        <div class="col-md-2">
                              <div class="row bldgInfo">
                              <div class="col-md-12">
                                    <div class="card shadow h-100">
                                          <div class="card-body p-2" style="font-size: .8rem! important">
                                                <form id="bldgEditForm" method="post">
                                                      <div class="row mt-2">
                                                            <div class="col-md-12 form-group mb-2">
                                                                  <label>Building Name</label>
                                                                  <input type="text" name="description" class="form-control form-control-sm bldngDesc" id="bldngDesc" onkeyup="this.value = this.value.toUpperCase();">
                                                                  <div id="validateEditDesc" class="invalid-feedback">
                                                                        Please provide a building name
                                                                  </div>
                                                            </div>
                                                      </div>
                                                      <div class="row">
                                                            <div class="col-md-12 form-group mb-2">
                                                                  <label>Building Capacity</label>
                                                                  <input type="text" name="capacity" id="bldngCap" class="form-control form-control-sm bldngCap" min="1" oninput="this.value=this.value.replace(/[^0-9]/g,'');">
                                                                  <div id="validateEditCap" class="invalid-feedback">
                                                                        Please provide a capacity
                                                                  </div>
                                                            </div>
                                                      </div>
                                                      <div class="row mt-3">
                                                            <div class="col-md-12 ">
                                                                  <button type="submit" class="btn btn-success btn-sm btn-block" id="building_update_button" style="font-size:.8rem !important">
                                                                  <i class="fa fa-save"></i> Update Information </button>
                                                            </div>
                                                            <div class="col-md-12 mt-2">
                                                                  <button class="btn btn-danger btn-sm btn-block" id="building_delete_button" style="font-size:.8rem !important">
                                                                  <i class="fa fa-trash"></i> Delete Information </button>
                                                            </div>
                                                      </div>

                                                      <input type="hidden" name="id" value="" id="bldgId"/>
                                                </form>
                                          </div>
                                    </div>
                              </div>
                              </div>
                              <div class="row bldgTotalCap mt-2">
                                    <div class="col-md-12">
                                          <div class="card shadow mb-2">
                                                <div class="card-body p-2" style="font-size: .8rem! important">
                                                <div class="row">
                                                      <div class="col-md-12" id="totalCap">
                                                            <label>Total Bldg. Capacity Left</label>
                                                            <div></div>
                                                      </div>
                                                </div>
                                                </div>
                                          </div>
                                    </div>

                                    <div class="col-md-12">
                                          <div class="card shadow">
                                                <div class="card-body p-2" style="font-size: .8rem! important">
                                                <div class="row">
                                                      <div class="col-md-12" id="totalRoomCap">
                                                            <label>Total Room Capacity</label>
                                                            <div></div>
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
                                          <div class="card-body p-2" style="font-size:.8rem !important">
                                          <div class="row mt-3">
                                                <div class="col-md-12"></div>
                                          </div>
                                          <div class="row">
                                                <div class="col-md-12 table-responsive tableFixHead">
                                                      <table class="table table-sm table-striped table-bordered table-hovered table-hover no-footer dataTable" id="bldg_rooms_table">
                                                      <thead>
                                                            <tr>
                                                                  <th width="55%">Room Name</th>
                                                                  <th width="40%">Capacity</th>
                                                                  <th width="5%"></th>
                                                            </tr>
                                                      </thead>
                                                      <tbody></tbody>
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

<div class="modal fade" id="assign_room_form_modal" style="display: none;" aria-hidden="true">
      <div class="modal-dialog modal-sm">
      <div class="modal-content">
            <div class="modal-header pb-2 pt-2 border-0">
                  <h4 class="modal-title">Assign New Room</h4>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                  <div class="message"></div>
                  <div class="form-group">
                        <label>Rooms</label>
                        <a href="javascript:void(0)" hidden class="pl-2" id="edit_docdesc"><i class="far fa-edit"></i></a>
                        <a href="javascript:void(0)" hidden class="pl-2" id="delete_docdesc"><i class="far fa-trash-alt text-danger"></i></a>
                        <select name="roomname" id="assignRoom" class="form-select form-control select2">
                              <option selected value="">Select Room</option>
                        </select>
                  </div>
                  <div class="row">
                        <div class="col-md-6 text-left">
                              <button type="button" class="btn btn-primary btn-sm" id="create_room_save">Create</button>
                        </div>
                        <div class="col-md-6 text-right">
                              <button type="button" class="btn btn-success btn-sm" id="assign_room_save">Save</button>
                        </div>
                  </div>
            </div>
            
      </div>
      </div>
</div>

<div class="modal fade" id="room_form_modal" style="display: none;" aria-hidden="true">
      <div class="modal-dialog modal-sm">
          <div class="modal-content">
            <div class="modal-header pb-2 pt-2 border-0">
                  <h4 class="modal-title">Room Form</h4>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                  <div class="message"></div>
                  <div class="form-group">
                      <label>Room Name</label>
                      <input id="roomName"  name="roomName" class="form-control form-control-sm" placeholder="Room Name" onkeyup="this.value = this.value.toUpperCase();">
                      <div id="invRoomName" class="invalid-feedback">Please provide a room name</div>
                      <div class="valid-feedback">
                        Room name looks good!
                      </div>
                  
                    </div>
                  <div class="form-group">
                    <label>Room Capacity</label>
                    <input id="roomCapacity" placeholder="Room Capacity" name="roomCapacity" class="form-control form-control-sm" min="1" oninput="this.value=this.value.replace(/[^0-9]/g,'');" >
                    <div id="invRoomCap" class="invalid-feedback">Please provide a valid room capacity</div>
                    <div class="valid-feedback">
                      Room capacity looks good!
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <button  type="button" class="btn btn-primary btn-sm" id="create_room">Create</button>
                    </div>
                  </div>
              </div>
            
          </div>
      </div>
    </div>
@endsection



@section('footerjavascript')
      <script src="{{asset('plugins/moment/moment.min.js') }}"></script>
      <script src="{{asset('plugins/select2/js/select2.full.min.js') }}"></script>
      <script src="{{asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
      <script src="{{asset('plugins/datatables/jquery.dataTables.js') }}"></script>
      <script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
      <script src="{{asset('plugins/datatables-fixedcolumns/js/dataTables.fixedColumns.js') }}"></script>
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
            var rooms_datatable_instance = null
            var selected_id = null
            var selected_room_id = null
            var selected_room_name = ''
            var selected_bldg_name = ''
            var currRoomCapacity = 0
            var currBldgCapacity = 0
            var projectsetup = []
            var syncEnabled = false;
            var button_enable = null;
            var connected_stat = false
            var is_form_valid = false;
            var room_process = null;

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
                              
                        },
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

            function get_deleted(tablename) {
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

            function process_deleted(tablename , deleted_data) {
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

            function update_local_status(tablename, alldata, info, status, first=false) {
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

            // JAM: added functions
            function deserializeString(inputString) {
                  var searchParams = new URLSearchParams(inputString);
                  var outputObject = {};
                  searchParams.forEach(function(value, key) {
                        outputObject[key] = value;
                  });

                  return outputObject;
            }

            function validateSelector(selector, callback) {

                  function validateInput(input) {
                        if (!input.val().trim()) {
                              $("#building_create_button").prop("disabled", true);
                              input.removeClass("is-valid").addClass("is-invalid");
                              return false;
                        } else {
                              $("#building_create_button").prop("disabled", false);
                              input.removeClass("is-invalid").addClass("is-valid");
                              return true;
                        }
                  }

                  $(selector).on("input", () => {
                        var isValid = validateInput($(selector));
                        
                        callback(isValid);
                  });
            }

            function dynamicValidate(inputSelector, btnSelector, patternStr, callback) {

                  var inputSel = $(`${inputSelector}`)
                  var pattern = new RegExp(patternStr);

                  function validateInput(inputSel, btnSelector, pattern) {

                        if (!pattern.test(inputSel.val().trim())) {
                              if (btnSelector !== '') {
                                    $(btnSelector).prop("disabled", true);
                              }
                              inputSel.removeClass("is-valid").addClass("is-invalid");
                              return false;
                        } else {
                              if (btnSelector !== '') {
                                    $(btnSelector).prop("disabled", false);
                              }
                              inputSel.removeClass("is-invalid").addClass("is-valid");
                              return true;
                        }
                  }

                  inputSel.on("input", () => {
                        var isValid = validateInput(inputSel, btnSelector, pattern);
                        callback(isValid);
                  });
            }

            function resetValidation(selector) {
                  $(selector).removeClass('is-valid').removeClass('is-invalid');
            }

            function getRoomsExcept(selected_id){
                  // redraw selection to get new selection
                  $("#assignRoom").html(
                        `<select name="roomname" id="assignRoom" class="form-select form-control select2">
                              <option selected value="">Select Room</option>
                        </select>`
                  )

                  $.ajax({
                        type:'GET',
                        url: 'api/building/all-rooms-except',
                        data: {
                              buildingid: selected_id
                        },
                        success:function(data) {
                              $("#assignRoom").select2({
                                    data: data,
                                    allowClear: true,
                                    placeholder: "Select Room",
                                    templateResult: function(data) {
                                          // Create a new jQuery object for the option
                                          var $option = $(`<option data-capacity='${data.capacity}' value='${data.id}'>${data.text} (${data.capacity})</option>`);
                                          return $option;
                                    }
                              })
                        }
                  })
            }

            function roomAssign() {


                  $.ajax({
                        type:'GET',
                        url: 'api/rooms/assign',
                        data:{
                              roomid: $('#assignRoom').val(),
                              buildingid: selected_id,
                        },
                        success:function(data) {
                              
                              if (data[0].status == 1) {
                                    Toast.fire({
                                          type: 'success',
                                          title: 'Room Assigned!'
                                    })

                                    // update selection
                                    getRoomsExcept(selected_id)


                                    // update rooms datatable
                                    buildingRoomDatatable({
                                          selector: '#view_bldginfo_modal',
                                          initialState: true
                                    })

                                    // update building datatable
                                    buildingDatatable()

                                    // update totals
                                    updateTotalBldgLeftRoomCap()


                                    // close assign new room modal
                                    $('#assign_room_form_modal').modal('hide')

                              } else {
                                    Toast.fire({
                                          type: 'error',
                                          title: 'Something went wrong!'
                                    })
                              }
                        },
                        error:function(){
                              $('#create_room').removeAttr('disabled')
                              Toast.fire({
                                    type: 'error',
                                    title: 'Something went wrong!'
                              })
                        }
                  })
            }

            function updateTotalBldgLeftRoomCap() {
                  $.ajax({
                        type:'GET',
                        url:'/api/building/rooms',
                        data: {
                              buildingid: selected_id,
                              datatable: false
                        },
                        success: function(data) {

                              jsonData = JSON.parse(data)
                              var totalBldgCapacityLeft = jsonData['data'][0]['totalBldgCapacityLeft']
                              var totalRoomCapacity = jsonData['data'][0]['totalRoomCapacity']


                              $('#totalCap div').html(totalBldgCapacityLeft);
                              $('#totalRoomCap div').html(totalRoomCapacity);

                        },
                        error: function(data) {
                              Toast.fire({
                                    type: 'error',
                                    title: 'Something went wrong!'
                              })
                        }
                  })
            }

            function roomUnassign() {

                  Swal.fire({
                        text: `Are you sure you want to unassign ${selected_room_name} to ${selected_bldg_name}?`,
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33', //'#3085d6'
                        cancelButtonColor: '#6c757d', //'#d33'
                        confirmButtonText: 'Unassign'
                  }).then((result) => {
                        if (result.value) {
                              $.ajax({
                                    type:'GET',
                                    url:'/api/room/unassign',
                                    data:{
                                          'id':selected_room_id
                                    },
                                    success: function(data) {
                                          if (data[0].status == 1) {
                                                // update rooms datatable
                                                buildingRoomDatatable({
                                                      selector: '#view_bldginfo_modal',
                                                      initialState: false
                                                })

                                                // update buildings datable
                                                buildingDatatable()


                                                // update totals
                                                updateTotalBldgLeftRoomCap()

                                                // update selection
                                                getRoomsExcept(selected_id)
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

            function updatePagination(options) {
                  var text = $(`${options.selector} .dataTables_info`).text()
                  const match = text.match(/\d+/g);

                  if (match) {
                        const entries = parseInt(match[2]);
                        const maxItemsPerPage = 10


                        // $(`#view_bldginfo_modal [data-dt-idx='${page}']`).click()

                        if (options) {
                              if (options.initialState) {
                                    const prevSelector = $(`${options.selector} .page-link:contains("Previous")`)
                                    const pageOneSelector = $(`${options.selector} .page-link:contains("1")`)

                                    if (pageOneSelector.text() === '1') {
                                          pageOneSelector.click()
                                    } else {
                                          prevSelector.click()
                                    }
                                    
                              } else {
                                    var page = String(Math.ceil(entries / maxItemsPerPage))
                                    otherPageSelector = $(`${options.selector} .page-link:contains("${page}")`)


                                    
                                    if (otherPageSelector.text() === page) {
                                          otherPageSelector.click()
                                    }
                              }
                        }

                  }
            }

            function buildingCreate(data){

                  // Disable the button while the AJAX request is being processed
                  $('#building_create_button').prop('disabled', true);

                  $.ajax({
                        type:'GET',
                        url:'/api/building/create',
                        data: data,
                        success:function(data) {
                              if(data[0].status == 1){

                                    $('#bldgCreateDesc').val("")
                                    $('#bldgCreateCap').val("")
                                    
                                    if($('#building_form_modal')){
                                          $('#building_form_modal').modal('hide')
                                    }

                                    buildingDatatable()
                                    get_last_index('building')
                              } else {
                                    $('#bldgCreateDesc').removeClass('is-valid')
                                    $('#bldgCreateDesc').addClass('is-invalid')
                                    $('#validateBldgDesc').text(data[0].message)
                              }

                              Toast.fire({
                                    type: data[0].icon,
                                    title: data[0].message,
                                    timer: 3000
                              })

                              // Re-enable the button after the AJAX request is complete
                              $('#building_create_button').prop('disabled', false);

                        },
                        error: function() {
                              // Re-enable the button if there is an error with the AJAX request
                              $('#building_create_button').prop('disabled', false);
                        }
                  })
            }

            function buildingUpdate(data){
                  $.ajax({
                        type: 'GET',
                        url: '/api/building/update',
                        data: data,
                        success: function(resp) {
                              if(resp[0].status == 1){

                                    updateTotalBldgLeftRoomCap()
                                    buildingDatatable()
                                    get_updated('building')
                              }
                              Toast.fire({
                                    type: resp[0].icon,
                                    title: resp[0].message
                              })
                        }
                  })
            }

            function buildingDelete(){

                  Swal.fire({
                        text: 'Are you sure you want to delete this building?',
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33', //'#3085d6'
                        cancelButtonColor: '#6c757d', //'#d33'
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

                                                buildingDatatable({
                                                      selector: '#building_datatable_holder',
                                                      initialState: false
                                                })

                                                // get_deleted('building')

                                                $('#view_bldginfo_modal').modal('hide')
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
                                                <label for="description">Building Name</label>            
                                                <input type="text" name="description" class="bldgCreateInput form-control form-control-sm bldgCreateDesc" id="bldgCreateDesc" onkeyup="this.value = this.value.toUpperCase();">
                                                <div class="valid-feedback">
                                                      Building name looks good!
                                                </div>
                                                <div id="validateBldgDesc" class="invalid-feedback">
                                                      Please provide a building name
                                                </div>
                                          </div>
                                    </div>
                                    <div class="row">
                                          <div class="col-md-12 form-group">
                                                <label for="capacity">Capacity</label>            
                                                <input type="text" name="capacity" class="bldgCreateInput form-control form-control-sm bldgCreateCap" id="bldgCreateCap" oninput="this.value=this.value.replace(/[^0-9]/g,'');">
                                                <div class="valid-feedback">
                                                      Capacity looks good!
                                                </div>
                                                <div id="validateBldgCap" class="invalid-feedback">
                                                      Please provide a valid capacity
                                                </div>
                                          </div>
                                    </div>
                                    <div class="row">
                                          <div class="col-md-12">
                                                <button type="submit" class="btn btn-sm btn-success" id="building_create_button"><i class="fa fa-save"></i> Save</button>
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
                                                      <form id="bldgCreateForm" method="post">
                                                            `+buildingform+`
                                                      </form>
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

                  var table = `<table class="table table-sm table-striped table-bordered table-hovered table-hover no-footer dataTable" id="buildings_datatable">
                                    <thead>
                                          <tr>
                                                <th width="40%">Description</th>
                                                <th width="20%">Capacity</th>
                                                <th width="20%">Total Bldg. Capacity Left</th>
                                                <th width="20%">Total Room Capacity</th>

                                          </tr>
                                    </thead>
                                    <tbody>
                                    
                                    </tbody>
                              </table>`

                  $(datatableholder)[0].innerHTML = table
                  buildingDatatable()
            }

            function buildingDatatable(options) {

                  var dtDeferred = $.Deferred();

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
                              // url: '/api/buildings/datatable',
                              url: 'api/buildings-rooms/datatable',
                              type: 'GET',
                              dataSrc: function ( json ) {
                                    buildings_datatable = json.data
                                    return json.data;
                              }
                        },
                        initComplete: function(settings, json) {
                              // Resolve the Deferred object
                              dtDeferred.resolve();
                        },
                        columns: [
                                    { "data": "description" },
                                    { "data": "capacity" },
                                    { "data": "totalBldgCapacityLeft" },
                                    { "data": "totalRoomCapacity" }
                              ],
                        columnDefs: [
                        {
                              'orderable': true,
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
                                          $(td).addClass('align-middle')
                                          // $(td).html('<div class="dropdown text-center"><spa style="line-height: 1 !important; font-size:1rem !important">&nbsp;</span></div>')
                              }
                        },
                        {
                              'targets': 3,
                              'orderable': false, 
                              'createdCell':  function (td, cellData, rowData, row, col) {
                                    $(td).addClass('align-middle')
                                    // $(td).html('<div class="dropdown text-center"><spa style="line-height: 1 !important; font-size:1rem !important">&nbsp;</span></div>')
                              }
                        }
                        ],
                        createdRow: function (row, data, dataIndex) {
                              $(row).attr("data-id",data.id);
                              $(row).addClass("view_info");
                              $(row).css("cursor", "pointer")
                        },
                  });
                  
                  var label_text = $($("#buildings_datatable_wrapper")[0].children[0])[0].children[0]

                  if(button_enable){
                        $(label_text)[0].innerHTML = '<button class="btn btn-sm btn-primary" id="building_create" style="font-size:.8rem !important"> <i class="fa fa-plus"></i> Add New</button>'
                  }else{
                        $(label_text)[0].innerHTML = ''
                  }

                  dtDeferred.promise().then(function() {
                        // code to execute after the DataTable has finished initializing

                        if (options) {
                              updatePagination({
                                    selector: options.selector,
                                    initialState: options.initialState
                              });
                        }
                  });


            }

            function buildingRoomDatatable(options) {

                  var rooms_table;
                  var dtDeferred = $.Deferred();

                  rooms_table = $('#bldg_rooms_table').DataTable({
                        destroy: true,
                        autoWidth: false,
                        lengthChange: false,
                        stateSave: true,
                        serverSide: true,
                        processing: true,
                        ajax: {
                              url: '/api/building/rooms',
                              type: 'GET',
                              data: {
                                    buildingid: selected_id,
                                    datatable: true
                              },
                              dataSrc: function ( json ) {
                                    return json.data;
                              }
                        },
                        columns: [
                              { "data": "roomname" },
                              { "data": "capacity" },
                              { "data": null }
                        ],
                        initComplete: function(settings, json) {
                              // Resolve the Deferred object
                              dtDeferred.resolve();
                        },
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
                                          $(td).addClass('align-middle');
                                    }
                              },
                              {
                                    'targets': 2,
                                    'orderable': false, 
                                    'createdCell':  function (td, cellData, rowData, row, col) {
                                          $(td).addClass('text-center');
                                          $(td).html(
                                                `<a href="#" id="delete_room"><i class="fa fa-trash text-danger"></i></a>`
                                          )
                                    }
                              }
                        ],
                        createdRow: function (row, data, dataIndex) {
                              $(row).attr("data-id",data.id);
                              $(row).addClass("view_room_info");
                        }
                  });

                  var label_text = $($("#bldg_rooms_table_wrapper")[0].children[0])[0].children[0]
                  $(label_text)[0].innerHTML = '<button class="btn btn-sm btn-primary" id="assign_room_button" style="font-size:.8rem !important"> <i class="fa fa-plus"></i> Assign Room</button>'


                  dtDeferred.promise().then(function() {
                        // code to execute after the DataTable has finished initializing

                        if (options) {
                              updatePagination({
                                    selector: options.selector,
                                    initialState: options.initialState
                              });
                        }
                  });
            }

            function create_room() {
                  var isvalid = true

                  if ($('#roomName').hasClass('is-invalid')) {
                        Toast.fire({
                              type: 'warning',
                              title: 'Room Name empty',
                              timer: 5000
                        })

                        isvalid = false
                  }
                  else if ($('#roomCapacity').hasClass('is-invalid')) {
                        Toast.fire({
                              type: 'warning',
                              title: 'Room Capacity empty',
                              timer: 5000
                        })

                        isvalid = false
                  }

                  if (isvalid) {
                        $("#create_room").prop("disabled", true);

                        $.ajax({
                              type:'GET',
                              url: '/rooms/create',
                              data:{
                                    // do not attach to a building
                                    building: null,
                                    roomname: $('#roomName').val(),
                                    capacity: $('#roomCapacity').val()
                              },
                              success:function(data) {
                                    console.log(data)
                                    if (data[0].status == 1) {

                                          // update rooms selection
                                          getRoomsExcept(selected_id)

                                          // close add room modal
                                          $('#room_form_modal').modal('hide')
                                    }

                                    $("#create_room").prop("disabled", false);

                                    Toast.fire({
                                          type: data[0].icon,
                                          title: data[0].message
                                    })
                              }
                        })
                  }
            }

            // BUILDING

            // 'Add New' button
            $(document).on('click','#building_create',function() {
                  $('#bldgCreateDesc').val("")
                  $('#bldgCreateCap').val("")

                  resetValidation('#bldgCreateDesc')
                  resetValidation('#bldgCreateCap')

                  $("#building_create_button").prop("disabled", false);

                  if($('#building_form_modal')) {
                              $('#building_form_modal').modal({
                                    show: true
                              })

                              var descInput = null
                              var capInput = null

                              var descInput = dynamicValidate(
                                    '#bldgCreateDesc',
                                    '#building_create_button',
                                    /\S+/,
                                    (result) => {
                                          descInput = result
                                    })

                              var capInput = dynamicValidate(
                                    '#bldgCreateCap',
                                    '#building_create_button',
                                    /\S+/,
                                    (result) => {
                                          capInput = result
                                    })
                  }
            })

            // Button: Delete Information
            $(document).on('click','#building_delete_button',function(event){
                  event.preventDefault();
                  buildingDelete()
            })

            // Button: Update Information
            $(document).on('submit','#bldgEditForm',function(event){
                  event.preventDefault();
                  var formData = $(this).serialize();

                  subData = deserializeString(formData)

                  // calculate first before sending
                  const totalBldgCap = subData['capacity']
                  const totalRoomCap = $('#totalRoomCap div').text().trim()

                  const computedBldgCap = parseInt(totalBldgCap) - parseInt(totalRoomCap)

                  if (is_form_valid || (subData['description'] && subData['capacity'])) {
                        if (computedBldgCap >= 0) {
                              buildingUpdate(formData)
                        } else {
                              Toast.fire({
                                    type: 'error',
                                    title: 'Building Update Error: Total Room Capacity is greater than Total Bldg Capacity Left',
                                    timer: 8000
                              })
                        }
                        
                  } else {

                        if (subData['description'] === '') {
                              $('#bldgDesc').addClass('is-invalid')
                        } else if (subData['capacity'] === '') {
                              $('#bldgCap').addClass('is-invalid')
                        } else {
                              $('#bldgDesc').addClass('is-invalid')
                              $('#bldgCap').addClass('is-invalid')
                        }
                        Toast.fire({
                                    type: 'error',
                                    title: 'Error missing field(s)'
                        })


                  }
            })

            // 'Building Form' Save button
            $(document).on('submit','#bldgCreateForm',function(event){
                  event.preventDefault();
                  var formData = $(this).serialize();

                  subData = deserializeString(formData)
                  
                  if (is_form_valid || (subData['description'] && subData['capacity'])) {
                        buildingCreate(formData)
                  } else {
                        if (subData['description'] === '') {
                              $('#bldgCreateDesc').addClass('is-invalid')
                        }
                        
                        if (subData['capacity'] === '') {
                              $('#bldgCreateCap').addClass('is-invalid')
                        }
                        
                        if (!subData['description'] && !subData['capacity']) {
                              $('#bldgCreateDesc').addClass('is-invalid')
                              $('#bldgCreateCap').addClass('is-invalid')
                        }

                        Toast.fire({
                                    type: 'error',
                                    title: 'Error missing field(s)'
                        })
                  }
            })

            // Building Row Click
            $(document).on('click','.view_info',function(){
                  var temp_id = $(this).attr('data-id')
                  var temp_bldnginfo = buildings_datatable.filter(x=>x.id == temp_id)
                  selected_bldg_name = temp_bldnginfo[0].description

                  selected_id = temp_id

                  if (selected_id) {
                        
                        $('#bldngDesc').val(selected_bldg_name)
                        $('#bldngCap').val(temp_bldnginfo[0].capacity)
                        $('#bldg_name').html(selected_bldg_name)
                        $('#bldgId').val(selected_id)


                        buildingRoomDatatable({
                              selector: '#view_bldginfo_modal',
                              initialState: true
                        })

                        resetValidation('#bldngDesc')
                        resetValidation('#bldngCap')

                        var descInput = null
                        var capInput = null


                        var descInput = dynamicValidate(
                              '#bldngDesc',
                              '#building_create_button',
                              /\S+/,
                              (result) => {
                                    descInput = result
                              })

                        var capInput = dynamicValidate(
                              '#bldngCap',
                              '#building_create_button',
                              /\S+/,
                              (result) => {
                                    capInput = result
                              })

                        updateTotalBldgLeftRoomCap();

                        getRoomsExcept(selected_id)

                        $('#view_bldginfo_modal').modal({
                              backdrop: 'static',
                              keyboard: false,
                              show: true,
                        });


                  }
            })

            // ROOM

            // 'Assign New Room' Save button
            $('#assign_room_save').on('click', function(){

                  // calculate first before sending
                  const totalBldgCap = $('#totalCap div').text().trim()
                  const computedBldgCap = parseInt(totalBldgCap) - parseInt(currRoomCapacity)

                  if (computedBldgCap >= 0 && $('#assignRoom').val() !== '') {
                        roomAssign()
                  } else {

                        if (computedBldgCap < 0) {
                              Toast.fire({
                                    type: 'error',
                                    title: 'Room Assignment Error: Building capacity limit reached.',
                                    timer: 5000
                              })
                        }

                        if ($('#assignRoom').val() === '') {
                              Toast.fire({
                                    type: 'error',
                                    title: 'Room Assignment Error: Field cannot be empty.',
                                    timer: 5000
                              })
                        }
                  }
                  
            })

            // 'Assign New Room' Create button
            $('#create_room_save').on('click', function(){
                  // set room_process
                  room_process = 'create_room'

                  // reset values and styles of input
                  $('#roomName').val('')
                  $('#roomCapacity').val('')

                  $('#roomName').removeClass('is-valid')
                  $('#roomName').removeClass('is-invalid')
                  $('#roomCapacity').removeClass('is-valid')
                  $('#roomCapacity').removeClass('is-invalid')

                  // open modal
                  $('#room_form_modal').modal()

                  // dynamic validation
                  dynamicValidate('#roomName', '', /\S+/, (result) => {return result})
                  dynamicValidate('#roomCapacity', '', /\S+/, (result) => {return result})
            })

            // 'Room Form' Create button
            $('#create_room').on('click', function(){
                  create_room()
            })

            // Show Room Form Modal
            $(document).on('click','#assign_room_button',function(){
                  // getRoomsExcept(selected_id)

                  $('#assign_room_form_modal').modal({
                        backdrop: 'static',
                        keyboard: false,
                        show: true
                  })

                  $('#assignRoom').on('select2:select', function(e) {
                        // Get the capacity value for the selected option
                        currRoomCapacity = e.params.data.capacity;
                  });
            })

            // Assign room on hover
            $(document).on('mouseover','.view_room_info',function(){
                  selected_room_id = $(this).attr('data-id')
                  selected_room_name = $(this).find('td:nth-child(1)').text()
            })

            // Delete room button
            $(document).on('click','#delete_room',function(){
                  roomUnassign()
            })

      </script>
@endsection

