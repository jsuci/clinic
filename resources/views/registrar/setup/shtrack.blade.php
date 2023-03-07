
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
@endphp

<div class="modal fade" id="track_form_modal" style="display: none;" aria-hidden="true">
      <div class="modal-dialog modal-sm">
            <div class="modal-content">
                  <div class="modal-header pb-2 pt-2 border-0">
                        <h4 class="modal-title">Track Form</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">×</span></button>
                  </div>
                  <div class="modal-body">
                        <div class="row">
                              <div class="col-md-12 form-group">
                                    <label for="">Track Name</label>
                                    <input id="input_trackname" class="form-control form-control-sm">
                              </div>
                        </div>
                        <div class="row">
                              <div class="col-md-12">
                                    <button class="btn btn-sm btn-primary" id="track_form_button">Create</button>
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
                        <h1>SHS Track</h1>
                  </div>
                  <div class="col-sm-6">
                  <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/home">Home</a></li>
                        <li class="breadcrumb-item active">SHS Track</li>
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
                                          <div class="col-md-2 form-group mb-0" >
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
                                    <div class="row">
                                         <div class="col-md-12">
                                               <button class="btn btn-primary btn-sm" id="button_to_modal_track">Create Track</button>
                                         </div>
                                    </div>
                                    <div class="row mt-2">
                                          <div class="col-md-12"  style="font-size:.7rem">
                                                <table class="table-hover table table-striped table-sm table-bordered table-head-fixed nowrap display compact" id="track_datatable" width="100%" >
                                                      <thead>
                                                            <tr>
                                                                  <th width="72%" class="pl-3">Track Name</th>
                                                                  <th width="9%" class="text-center p-0 align-middle">Strand</th>
                                                                  <th width="9%" class="text-center p-0 align-middle">Enrolled</th>
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

                  const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                  })

                  $('.select2').select2()

                  var track_list = []
                  var selected_id = null

                  load_track_datatable()
                  get_track_list()
                

                  $(document).on('click','#button_to_modal_track',function(){
                        $('#input_trackname').val("")
                        $('#track_form_modal').modal()
                        $('#track_form_button').removeClass('btn-success')
                        $('#track_form_button').addClass('btn-primary')
                        $('#track_form_button').text('Create')
                        $('#track_form_button').attr('data-proccess','create')
                  })

                  $(document).on('click','.udpate_track',function(){
                        selected_id = $(this).attr('data-id')
                        var temp_track_info = track_list.filter(x=>x.id == selected_id)
                        $('#input_trackname').val(temp_track_info[0].trackname)
                        $('#track_form_modal').modal()
                        $('#track_form_button').removeClass('btn-primary')
                        $('#track_form_button').addClass('btn-success')
                        $('#track_form_button').text('Update')
                        $('#track_form_button').attr('data-proccess','update')
                  })

                  $(document).on('click','.delete_track',function(){
                        selected_id = $(this).attr('data-id')
                        delete_track()
                  })

                  $(document).on('click','#track_form_button',function(){
                        if($(this).attr('data-proccess') == 'update'){
                              udpate_track()
                        }else if($(this).attr('data-proccess') == 'create'){
                              create_track()
                        }
                  })

                  $(document).on('change','#filter_sy',function(){
                        get_track_list()
                  })

                  $(document).on('change','#filter_semester',function(){
                        get_track_list()
                  })


                  function get_track_list(){
                        var syid = $('#filter_sy').val()
                        var semid = $('#filter_semester').val()
                        $.ajax({
                              type:'GET',
                              url:'/setup/track/list',
                                    data:{
                                    syid:syid,
                                    semid:semid,
                                    withEnrollmentCount:true
                              },
                              success:function(data) {
                                    if(data.length == 0){
                                          Toast.fire({
                                                type: 'warning',
                                                title: 'No track found.'
                                          })
                                    }else{
                                          track_list = data
                                          load_track_datatable()
                                    }
                              }
                        })
                  }

                  function create_track(){

                        var trackname = $('#input_trackname').val()

                        if(trackname == ""){
                              Toast.fire({
                                    type: 'warning',
                                    title: "Track Name is empty."
                              })
                              return false
                        }

                        $.ajax({
                              type:'GET',
                              url:'/setup/track/create',
                              data:{
                                    trackname:trackname
                              },
                              success:function(data) {
                                    if(data[0].status == 0){
                                          Toast.fire({
                                                type: 'error',
                                                title: data[0].message
                                          })
                                    }else{
                                          get_track_list()
                                          Toast.fire({
                                                type: 'success',
                                                title: data[0].message
                                          })
                                    }
                              }
                        })
                  }

                  function udpate_track(){
                        var id = selected_id
                        var trackname = $('#input_trackname').val()

                        if(trackname == ""){
                              Toast.fire({
                                    type: 'warning',
                                    title: "Track Name is empty."
                              })
                              return false
                        }
                        
                        $.ajax({
                              type:'GET',
                              url:'/setup/track/update',
                              data:{
                                    trackname:trackname,
                                    id:id
                              },
                              success:function(data) {
                                    if(data[0].status == 0){
                                          Toast.fire({
                                                type: 'warning',
                                                title: data[0].message
                                          })
                                    }else{
                                          get_track_list()
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

                  function delete_track(){
                        var id = selected_id
                        $.ajax({
                              type:'GET',
                              url:'/setup/track/delete',
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
                                          get_track_list()
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

                  function load_track_datatable(){

                        $("#track_datatable").DataTable({
                              destroy: true,
                              data:track_list,
                              lengthChange : false,
                              columns: [
                                          { "data": "trackname" },
                                          { "data": "strandcount" },
                                          { "data": "enrolled" },
                                          { "data": null },
                                          { "data": null },
                                    ],
                              columnDefs: [
                                    {
                                          'targets': 1,
                                          'orderable': false, 
                                          'createdCell':  function (td, cellData, rowData, row, col) {
                                                $(td).addClass('text-center')
                                                $(td).addClass('align-middle')
                                          }
                                    },
                                    {
                                          'targets': 2,
                                          'orderable': false, 
                                          'createdCell':  function (td, cellData, rowData, row, col) {
                                                $(td).addClass('text-center')
                                                $(td).addClass('align-middle')
                                          }
                                    },
                                    {
                                          'targets': 3,
                                          'orderable': false, 
                                          'createdCell':  function (td, cellData, rowData, row, col) {
                                                var buttons = '<a href="javascript:void(0)" class="udpate_track" data-id="'+rowData.id+'"><i class="far fa-edit"></i></a>';
                                                $(td)[0].innerHTML =  buttons
                                                $(td).addClass('text-center')
                                                $(td).addClass('align-middle')
                                                
                                          }
                                    },
                                    {
                                          'targets': 4,
                                          'orderable': false, 
                                          'createdCell':  function (td, cellData, rowData, row, col) {
                                                var disabled = '';
                                                var buttons = '<a href="javascript:void(0)" '+disabled+' class="delete_track" data-id="'+rowData.id+'"><i class="far fa-trash-alt text-danger"></i></a>';
                                                $(td)[0].innerHTML =  buttons
                                                $(td).addClass('text-center')
                                                $(td).addClass('align-middle')
                                          }
                                    },
                              ]
                              
                        });
                  
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


