@php

    
    $check_refid = DB::table('usertype')->where('id',Session::get('currentPortal'))->select('refid')->first();

    if(Session::get('currentPortal') == 14){    
		$extend = 'deanportal.layouts.app2';
	}else if(Session::get('currentPortal') == 3){
        $extend = 'registrar.layouts.app';
    }else if(Session::get('currentPortal') == 8){
        $extend = 'admission.layouts.app2';
    }else if(Session::get('currentPortal') == 1){
        $extend = 'teacher.layouts.app';
    }else if(Session::get('currentPortal') == 2){
        $extend = 'principalsportal.layouts.app2';
    }else if(Session::get('currentPortal') == 4){
         $extend = 'finance.layouts.app';
    }else if(Session::get('currentPortal') == 15){
         $extend = 'finance.layouts.app';
    }else if(Session::get('currentPortal') == 18){
        $extend = 'ctportal.layouts.app2';
    }else if(Session::get('currentPortal') == 10){
        $extend = 'hr.layouts.app';
    }else if(Session::get('currentPortal') == 16){
        $extend = 'chairpersonportal.layouts.app2';
    }else if(auth()->user()->type == 16){
        $extend = 'chairpersonportal.layouts.app2';
    }else{
        if(isset($check_refid->refid)){
            if($check_refid->refid == 27){
                $extend = 'academiccoor.layouts.app2';
            }else if($check_refid->refid == 22){
                $extend = 'principalcoor.layouts.app2';
            }else if($check_refid->refid == 29){
                $extend = 'idmanagement.layouts.app2';
            }else{
                $extend = 'general.defaultportal.layouts.app';
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
      </style>
@endsection


@section('content')

{{-- <div class="modal fade" id="modal_1" style="display: none;" aria-hidden="true">
      <div class="modal-dialog">
            <div class="modal-content">
                  <div class="modal-body">
                        <div class="row">
                              <div class="col-md-6 form-group">
                                    <div class="icheck-primary d-inline pt-2">
                                          <input type="checkbox" id="header" >
                                          <label for="header">Header
                                          </label>
                                    </div>
                              </div>
                        </div>
                        <div class="row">
                              <div class="col-md-12 form-group">
                                    <label for="">Description</label>
                                    <textarea class="form-control form-control-sm" id="description" rows="3"></textarea>
                              </div>
                        </div>
                        <div class="row">
                              <div class="col-md-12 form-group">
                                    <label for="">Value</label>
                                    <input type="text" class="form-control form-control-sm" id="value">
                              </div>
                        </div>
                        <div class="row" >
                              <div class="col-md-12 form-group">
                                    <label for="">Sort</label>
                                    <input type="text" class="form-control form-control-sm" id="sort">
                              </div>
                        </div>

                        <div class="row">
                              <div class="col-md-12 form-group">
                                    <label for="">Group</label>
                                    <input type="text" class="form-control form-control-sm" id="group">
                              </div>
                        </div>
                        <div class="row">
                              <div class="col-md-12" id="group_list">

                              </div>
                        </div>
                  </div>
                  <div class="modal-footer border-0">
                        <button class="btn btn-primary btn-sm" id="create_button_1"><i class="fas fa-copy"></i> CREATE</button>
                        <button class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                  </div>
            </div>
      </div>
</div> --}}

<!-- Modal for adding new item -->
<div class="modal fade" id="item-modal" tabindex="-1" aria-labelledby="item-modal-label" aria-hidden="true">
      <div class="modal-dialog">
            <div class="modal-content">
                  <div class="modal-header">
                        <h5 class="modal-title" id="item-modal-label">Add Entry</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                        </button>
                  </div>
                  <div class="modal-body">
                        {{-- <div class="form-group">
                              <label for="header-checkbox">Header</label>
                              <input id="header-checkbox" name="is_header" type="checkbox" class="form-control">
                        </div> --}}
                        <div class="form-group">
                              <label for="description-input">Description</label>
                              {{-- <input id="description-input" name="description" type="text" class="form-control"> --}}
                              <textarea id="description-input" name="description" type="text" class="form-control"></textarea>
                        </div>
                        <div class="form-check">
                              <input id="header-checkbox" name="is_header" type="checkbox" class="form-check-input">
                              <label for="header-checkbox">Header</label>
                              {{-- <label for="header-checkbox">Header</label> --}}
                        </div>
                  </div>
                  <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button id="create-item-btn" type="button" class="btn btn-primary">Create</button>
                  </div>
            </div>
      </div>
</div>

<section class="content-header">
      <div class="container-fluid">
            <div class="row mb-4">
                  <div class="col-sm-6">
                        <h1>Pre-School Grading</h1>
                  </div>
                  <div class="col-sm-6">
                  <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/home">Home</a></li>
                        <li class="breadcrumb-item active">Pre School Grading</li>
                  </ol>
                  </div>
            </div>

            <div class="row">
                  <div class="col-md-6 text-left">
                        <button id="add-item-btn" class="btn btn-primary btn-md"><i class="fas fa-plus"></i> Add Entry</button>
                        {{-- <button class="btn btn-primary btn-md" id="button_to_modal_1"><i class="fas fa-plus"></i> Add Item</button> --}}
                  </div>
                  <div class="col-md-6 text-right">
                        <div class="input-group">
                              <input type="search" class="form-control form-control-md" placeholder="Type your keywords here">
                              <div class="input-group-append">
                                    <button type="submit" class="btn btn-md btn-default">
                                          <i class="fa fa-search"></i>
                                    </button>
                              </div>
                        </div>
                  </div>
            </div>
      </div>
</section>

<section class="content px-2">
      <div class="container-fluid">

            <div id="item-container" class="row mt-4">
                  <!-- items will be added dynamically here -->
            </div>
            
            {{-- <div class="row mt-3">
                  <div class="col-md-12">
                        <div class="card shadow" >
                              <div class="card-header">
                              </div>
                              <div class="card-body">
                                    <div class="row ">
                                          <div class="col-md-12">
                                                <table class="table table-striped table-sm table-bordered table-head-fixed nowrap display p-0" width="100%">
                                                      <thead>
                                                            <tr>
                                                                  <th width="5%" class="align-middle text-center">Sort</th>
                                                                  <th width="77%" class="align-middle">Description  </th>
                                                                  <th width="4%"></th>
                                                                  <th width="4%"></th>
                                                                  <th width="10%" class="text-center"><button class="btn btn-primary btn-sm" id="button_to_modal_1"><i class="fas fa-plus"></i> Add Item</button></th>
                                                            </tr>
                                                      </thead>
                                                      <tbody id="data">

                                                      </tbody>
                                                </table>
                                          </div>
                                    </div>
                              </div>
                        </div>
                  </div>
            </div> --}}
      </div>
</section>

@endsection

@section('footerjavascript')
      <script src="{{asset('plugins/select2/js/select2.full.min.js') }}"></script>
      <script src="{{asset('plugins/datatables/jquery.dataTables.js') }}"></script>
      <script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
      <script src="{{asset('plugins/datatables-fixedcolumns/js/dataTables.fixedColumns.js') }}"></script>

      <script type="text/javascript">
            $(document).ready(function(){

                var SYID = 3;

                // add item button click event
                $('#add-item-btn').on('click', function() {
                    $('#item-modal').modal('show');
                });

                // create item on click of create button
                $('#create-item-btn').on('click', function() {
                    // get values from modal
                    var isHeader = $('#header-checkbox').prop('checked');
                    var description = $('#description-input').val();
                    var cardHtml = ''

                    if (isHeader) {
                            cardHtml = `
                            <div class="col-md-12 mb-3">
                                <div class="card">
                                        <div class="card-header bg-primary">
                                            <div class="row" style="align-items:center;">
                                                    <div class="col-md-10 text-left">
                                                        <h3 class="card-title">${description}</h3>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="row" style="justify-content:flex-end;">
                                                        <button type="button" class="btn btn-tool btn-collapse">
                                                                <i class="fa fa-plus"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-tool btn-maximize">
                                                                <i class="fas fa-expand"></i>
                                                        </button>
                                                        </div>
                                                    </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                            </div>
                                            <div class="row">
                                            </div>
                                        </div>
                                </div>
                            </div>`;
                    } else {
                            cardHtml = `
                            <div class="col-md-12 mb-3">
                                <div class="card">
                                        <div class="card-body">
                                            <p class="card-text">${description}</p>
                                        </div>
                                </div>
                            </div>`;
                    }

                    // append card to container
                    $('#item-container').append(cardHtml);

                    // hide modal
                    $('#item-modal').modal('hide');
                });

                // maximize button click event
                $(document).on('click', '.btn-maximize', function() {
                    $(this).find("i").toggleClass("fa-expand fa-compress");
                    $(this).closest(".card").toggleClass("maximized-card");
                });

                // add event listener to collapse button
                $(document).on('click', '.btn-collapse', function() {
                    $(this).find("i").toggleClass("fa-plus fa-minus");
                    $(this).closest(".card").find(".card-body").slideToggle();
                });

            })
      </script>

      {{-- <script>
            $(document).ready(function(){

                  const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                  })

                  $('.select2').select2()
                  var dataid = null
                  var actoion = null

                  $(document).on('click','#button_to_modal_1',function () {
                        dataid = ""
                        action = "create"
                        $('#create_button_1')[0].innerHTML = '<i class="fas fa-plus"></i> Create'
                        $('#create_button_1').addClass('btn-primary')
                        $('#create_button_1').removeClass('btn-success')
                        $('#description').val("")
                        $('#header').prop('checked',false)
                        $('#modal_1').modal('show')
                  })

                  $(document).on('click','.create_item_1',function () {
                        action = "create"
                        action = "create"
                        $('#create_button_1')[0].innerHTML = '<i class="fas fa-plus"></i> Create'
                        $('#create_button_1').addClass('btn-primary')
                        $('#create_button_1').removeClass('btn-success')
                        $('#description').val("")
                        $('#header').prop('checked',false)
                        dataid = $(this).attr('data-id')
                        $('#modal_1').modal('show')
                  })

                  $(document).on('click','#create_button_1',function () {
                        if(action == "create"){
                              create_1()
                        }else{
                              udpate_1()
                        }
                  })

                  function udpate_1(){
                        type = ""
                        if($('#header').prop('checked')){
                              type = "header"
                        }
                        $.ajax({
                              type:'GET',
                              url: '/grade/preschool/setup/update',
                              data:{
                                    syid:3,
                                    dataid:dataid,
                                    description:$('#description').val(),
                              },
                              success:function(data) {
                                    if(data[0].status == 1){
                                          Toast.fire({
                                                type: 'success',
                                                title: 'Updated Successfully!'
                                          })
                                          plot_setup(data[0].info)
                                    }else{
                                          Toast.fire({
                                                type: 'error',
                                                title: 'Something went wrong!'
                                          })
                                    }
                              }
                        })
                  }

                  $(document).on('click','.group_option',function () {
                        $('#group').val($(this).text())
                  })


                  $(document).on('click','.edit',function () {
                        action = "update"
                        $('#create_button_1')[0].innerHTML = '<i class="fas fa-edit"></i> Update'
                        $('#create_button_1').removeClass('btn-primary')
                        $('#create_button_1').addClass('btn-success')

                        dataid = $(this).attr('data-id')
                        var temp_data = all_setup.filter(x=>x.id == dataid)
                        if(temp_data[0].value == 0){
                              $('#header').prop('checked','checked')
                        }else{
                              $('#header').prop('checked',false)
                        }
                        $('#description').val(temp_data[0].description)
                        $('#modal_1').modal('show')
                  })


                  $(document).on('click','.delete',function () {
                        var temp_dataid = $(this).attr('data-id')
                        $.ajax({
					type:'GET',
					url: '/grade/preschool/setup/delete',
                              data:{
                                    dataid:temp_dataid,
                                    syid:3
                              },
					success:function(data) {
                                    if(data[0].status == 1){
                                          Toast.fire({
                                                type: 'success',
                                                title: 'Deleted Successfully!'
                                          })
                                          plot_setup(data[0].info)
                                          
                                    }
                                    else if(data[0].status == 2){
                                          Toast.fire({
                                                type: 'info',
                                                title: data[0].message
                                          })
                                    }else{
                                          Toast.fire({
                                                type: 'error',
                                                title: data[0].message
                                          })
                                    }
                              }
                        })

                  })

                  get_preschool_setup()
                  var all_setup = []

                  function get_preschool_setup(){
                        $.ajax({
					type:'GET',
					url: '/grade/preschool/setup/list',
                              data:{
                                    syid:3
                              },
					success:function(data) {
                                    plot_setup(data)
					}
				})
                  }

                  function plot_setup(data) {
                        all_setup = data
                        $('#data').empty()
                        $.each(data,function(a,b){
                              var padding = ""
                              var header = ""
                              var button = ""
                              if(b.value == 0){
                                    header = 'font-weight-bold'
                                    button = '<button class="create_item_1 btn btn-sm btn-primary" data-id="'+b.id+'"><i class="fas fa-plus"></i> Add Item</button>'

                                    if(b.sort.length > 1){
                                          padding = (b.group.length*2)+"rem;"
                                    }
                                    
                              }else{
                                    padding = (b.group.length*2)+"rem;"
                              }
                              $('#data').append('<tr class="'+header+' "><td class="align-middle text-center">'+b.sort+'</td><td class="align-middle" style="padding-left:'+padding+'">'+b.description+'</td><td class="align-middle text-center"><a href="javascript:void(0)" class="edit" data-id="'+b.id+'"><i class="fas fa-edit text-primary"></i></a></td><td class="align-middle text-center"><a  href="javascript:void(0)" class="delete text-danger" data-id="'+b.id+'"><i class="far fa-trash-alt"></i></a></td><td class="text-center">'+button+'</td></tr><')
                        })
                  }


                  function create_1(){

                        type = ""

                        if($('#header').prop('checked')){
                              type = "header"
                        }

                        $.ajax({
					type:'GET',
					url: '/grade/preschool/setup/create',
                              data:{
                                    syid:3,
                                    type:type,
                                    dataid:dataid,
                                    decription:$('#description').val(),
                                    value:$('#value').val(),
                                    sort:$('#sort').val(),
                              },
					success:function(data) {
                                    if(data[0].status == 1){
                                          Toast.fire({
                                                type: 'success',
                                                title: 'Created Successfully!'
                                          })
                                          get_preschool_setup()
                                          
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
      </script> --}}

      <script>
            $(document).ready(function(){
                  var keysPressed = {};
                  const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                  })
                  document.addEventListener('keydown', (event) => {
                        keysPressed[event.key] = true;
                        if (keysPressed['p'] && event.key == 'v') {
                              Toast.fire({
                                          type: 'warning',
                                          title: 'Date Version: 07/28/2021 14:34'
                                    })
                        }
                  });
                  document.addEventListener('keyup', (event) => {
                        delete keysPressed[event.key];
                  });
            })
      </script>


@endsection


