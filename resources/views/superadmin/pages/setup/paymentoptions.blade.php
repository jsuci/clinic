
@php
      if(!Auth::check()){
            header("Location: " . URL::to('/'), true, 302);
            Session::flush();
            exit();
      }
      $extend = '';
      
      if(auth()->user()->type == 17){
            $extend = 'superadmin.layouts.app2';
      }else if(auth()->user()->type == 2){
            $extend = 'principalsportal.layouts.app2';
      }else if(Session::get('currentPortal') == 3){
        $extend = 'registrar.layouts.app';
      }else if(auth()->user()->type == 3){
        $extend = 'registrar.layouts.app';
      }else if(Session::get('currentPortal') == 4){
         $extend = 'finance.layouts.app';
      }else if(Session::get('currentPortal') == 15){
            $extend = 'finance.layouts.app';
      }else if(auth()->user()->type == 4){
            $extend = 'finance.layouts.app';
      }else if(auth()->user()->type == 15 ){
            $extend = 'finance.layouts.app';
      }else if(Session::get('currentPortal') == 6){
            $extend = 'adminPortal.layouts.app2';
      }

      if($extend == ''){
            header("Location: " . URL::to('/'), true, 302);
            exit();
      }
@endphp

@extends($extend)

@section('pagespecificscripts')
      <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
      <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
      <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
      <link rel="stylesheet" href="{{ asset('plugins/datatables-fixedcolumns/css/fixedColumns.bootstrap4.css') }}">
      <link rel="stylesheet" href="{{asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
      <link rel="stylesheet" href="{{asset('plugins/toastr/toastr.min.css')}}">
      <link rel="stylesheet" href="{{asset('plugins/croppie/croppie.css')}}">
      <style>
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

      $schoolinfo = DB::table('schoolinfo')->first(); 
      $paymenttype = DB::table('paymenttype')
                        ->where('deleted',0)
                        ->where('isonline',1)
                        ->select(
                              'id',
                              'description as text'
                        )
                        ->get();
@endphp


<div class="modal fade" id="upload_modal" style="display: none;" aria-hidden="true">
      <div class="modal-dialog">
            <div class="modal-content">
                  <div class="modal-header pb-2 pt-2 border-0">
                        <h4 class="modal-title" id="modal_1_title"></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                  </div>
                  <div class="modal-body" style="font-size:12px !important">
                        <div class="row">
                              <div class="col-md-12">
                                    <label for="">Payment Type</label>
                                    <input disabled class="form-group form-control-sm form-control payment_type_holder" >
                              </div>
                        </div>
                        <div class="row">
                              <div class="col-md-12">
                                    <img id="demo" width="100%">
                              </div>
                        </div>
                        
                        <div class="row mt-4">
                              <div class="col-md-12">
                                    <form 
                                          action="/setup/payment/options/image/upload" 
                                          id="upload_payment_option" 
                                          method="POST" 
                                          enctype="multipart/form-data"
                                          >
                                          @csrf
                                          <div class="row">
                                                <div class="col-md-12">
                                                      <div class="progress progress-sm active">
                                                            <div class="progress-bar bg-success progress-bar-striped" id="upload_progress" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                                            <span class="sr-only"></span>
                                                            </div>
                                                      </div>
                                                      <input class="form-control" name="payment_option_id" id="payment_option_id" hidden>
                                                      <div class="input-group input-group-sm">
                                                            <input type="file" class="form-control" name="input_paymentoptions" id="input_paymentoptions">
                                                            <span class="input-group-append">
                                                            <button class="btn btn-primary btn-flat" id="upload_ecr_button" >Upload Image</button>
                                                            </span>
                                                      </div>
                                                      
                                                </div>
                                          </div>
                                    </form>
                              </div>
                        </div>
                  </div>
            </div>
      </div>
</div>   


<div class="modal fade" id="payment_option_form" style="display: none;" aria-hidden="true">
      <div class="modal-dialog">
            <div class="modal-content">
                  <div class="modal-header pb-2 pt-2 border-0">
                        <h4 class="modal-title">Payment Option Form</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                  </div>
                  <div class="modal-body">
                        <div class="row form-group">
                              <div class="col-md-12">
                                    <label for="">Payment Type</label>
                                    <select name="" id="input_paymenttype" class="form-control form-control-sm"></select>
                              </div>
                        </div>
                        <div class="row form-group" hidden id="input_bankname_holder">
                              <div class="col-md-12">
                                    <label for="">Bank Name</label>
                                    <input id="input_bankname" class="form-control form-control-sm">
                              </div>
                        </div>
                        <div class="row form-group" hidden id="input_acctname_holder">
                              <div class="col-md-12">
                                    <label for="">Account Name</label>
                                    <input id="input_acctname" class="form-control form-control-sm">
                              </div>
                        </div>
                        <div class="row form-group" hidden id="input_acctnumber_holder">
                              <div class="col-md-12">
                                    <label for="">Account Number</label>
                                    <input id="input_acctnumber" class="form-control form-control-sm">
                              </div>
                        </div>
                        <div class="row form-group" hidden id="input_mobilenumber_holder">
                              <div class="col-md-12">
                                    <label for="">Mobile Number</label>
                                    <input id="input_mobilenumber" class="form-control form-control-sm">
                              </div>
                        </div>
                        <div class="row">
                              <div class="col-md-12">
                                    <button class="btn btn-sm btn-primary" id="create_paymentoption" disabled>Create</button>
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
                        <h1>Payment Options</h1>
                  </div>
                  <div class="col-sm-6">
                  <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/home">Home</a></li>
                        <li class="breadcrumb-item active">Payment Options</li>
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
                                         <div class="col-md-12" style="font-size:.8rem !important">
                                               <table class="table table-sm  table-striped" id="payment_options_table">
                                                      <thead class="thead-light">
                                                            <tr>
                                                                  <th width="22%" class="text-center">Image</th>
                                                                  <th width="15%">Type</th>
                                                                  <th width="53%">Payment Option Information</th>
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
      <script src="{{asset('plugins/toastr/toastr.min.js')}}"></script>
      <script src="{{asset('plugins/inputmask/min/jquery.inputmask.bundle.min.js')}}"></script>
      <script src="{{asset('plugins/croppie/croppie.js')}}"></script>
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
                              title: 'Payment Option Created!'
                        })
                        get_paymentoptions()
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
                              title: 'Payment Option Updated!'
                        })
                        get_paymentoptions()
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
                        title: 'Payment Option Deleted!'
                  })
                        get_paymentoptions()
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

            var all_paymemt_options = []

            get_paymentoptions()          
            function get_paymentoptions(){
                  $.ajax({
                        type:'GET',
                        url: '/setup/payment/options/list',
                        success:function(data) {
                              all_paymemt_options = data
                              load_payment_option()
                        }
                  })
            }

            function load_payment_option(){

                  $("#payment_options_table").DataTable({
                        destroy: true,
                        data:all_paymemt_options,
                        lengthChange: false,
                        stateSave: true,
                        autoWidth:false,
                        columns: [
                              { "data": "optionDescription" },
                              { "data": "description"  },
                              { "data": null },
                              { "data": null },
                              { "data": null },
                        ],
                        columnDefs: [
                                          {
                                                'targets': 0,
                                                'orderable': false, 
                                                'createdCell':  function (td, cellData, rowData, row, col) {
                                                      if(rowData.picurl != null){
                                                            
                                                            if(school_setup.setup == 1 && school_setup.projectsetup == 'offline'){
                                                                  var src = school_setup.es_cloudurl+'/'+rowData.picurl
                                                            }else{
                                                                  var src = '/'+rowData.picurl
                                                            }
                                                            var buttons = '<a data-id="'+rowData.id+'" href="javascript:void(0)" class="update_image" ><img width="40%"  src="'+src+'" alt=""></a>';
                                                      }else{
                                                            var buttons = '<a class="update_image" href="javascript:void(0)" data-id="'+rowData.id+'" style="font-size:.8rem !important">Update Image</a>';
                                                      }
                                                
                                                      $(td)[0].innerHTML =  buttons
                                                      $(td).addClass('text-center')
                                                      $(td).addClass('align-middle')
                                                }
                                          },
                                          {
                                                'targets': 1,
                                                'orderable': true, 
                                                'createdCell':  function (td, cellData, rowData, row, col) {
                                                      if(rowData.paymenttype == 3){
                                                            var info = rowData.description+'<br><span class="text-primary">'+rowData.optionDescription+'</span> '
                                                            $(td)[0].innerHTML =  info
                                                      }
                                                      $(td).addClass('align-middle')
                                                }
                                          },
                                          {
                                                'targets': 2,
                                                'orderable': false, 
                                                'createdCell':  function (td, cellData, rowData, row, col) {
                                                      if(rowData.paymenttype == 3){
                                                            //bank
                                                            var info = '<span class="text-primary">Account Name:</span> '+rowData.accountName+'<br><span class="text-primary">Account #:</span> '+rowData.accountNum
                                                      }else if(rowData.paymenttype == 4){
                                                            //gcash
                                                            var info = '<span class="text-primary">Acount Name: </span>'+rowData.accountName+'<br><span class="text-primary">Contact #:</span> '+rowData.mobileNum
                                                      }else if(rowData.paymenttype == 5){
                                                            //palawan
                                                            var info = '<span class="text-primary">Acount Name:</span> '+rowData.accountName+'<br><span class="text-primary">Contact #:</span> '+rowData.mobileNum
                                                      }

                                                      $(td)[0].innerHTML =  info
                                                      $(td).addClass('align-middle')
                                                }
                                          },
                                          {
                                                'targets': 3,
                                                'orderable': false, 
                                                'createdCell':  function (td, cellData, rowData, row, col) {
                                                      var buttons = '<a href="#" class="edit_section" data-id="'+rowData.id+'"><i class="far fa-edit text-primary"></i></a>';
                                                      $(td)[0].innerHTML =  buttons
                                                      $(td).addClass('text-center')
                                                      $(td).addClass('align-middle')
                                                }
                                          },
                                          {
                                                'targets': 4,
                                                'orderable': false, 
                                                'createdCell':  function (td, cellData, rowData, row, col) {
                                                      var buttons = '<a href="#" class="delete_section" data-id="'+rowData.id+'"><i class="far fa-trash-alt text-danger"></i></a>';
                                                      $(td)[0].innerHTML =  buttons
                                                      $(td).addClass('text-center')
                                                      $(td).addClass('align-middle')
                                                }
                                          },
                                    ]
                  });

                  var label_text = $($("#payment_options_table_wrapper")[0].children[0])[0].children[0]
                  $(label_text)[0].innerHTML = '<button class="btn btn-primary btn-sm" id="create_paymentoption_to_modal" ><i class="fas fa-plus"></i> Create Payment Option</button>'

            }

      </script>
      <script>
            $(document).ready(function(){
                  
                  var all_paymenttype = @json($paymenttype);

                  $("#input_paymenttype").empty()
                  $("#input_paymenttype").append('<option value="">Select Payment Type<option>')
                  $("#input_paymenttype").select2({
                        data: all_paymenttype,
                        allowClear: true,
                        placeholder: "Select Payment Type",
                  })

                  $("#input_mobilenumber").inputmask({mask: "9999-999-9999"});

                  $("#input_paymentoptions").change(function(){
                        var selectedFile = this.files[0];
                        var idxDot = selectedFile.name.lastIndexOf(".") + 1;
                        var extFile = selectedFile.name.substr(idxDot, selectedFile.name.length).toLowerCase();
                        if (extFile == "jpg" || extFile == "jpeg" || extFile == "png") {
                              var reader = new FileReader();
                              reader.onload = function (e) {$('#demo').attr('src', e.target.result)}
                              reader.readAsDataURL(this.files[0]);
                        } else {
                              Swal.fire({
                                    title: 'INVALID FORMAT',
                                    type: 'error',
                                    showConfirmButton: false,
                                    timer: 1500
                              })
                              $(this).val('')
                        }
                  });


                  $(document).on('click','#create_paymentoption_to_modal',function(){
                        if($('#update_paymentoption').length > 0){
                              $('#update_paymentoption').text('Create')
                              $('#update_paymentoption').removeClass('btn-success')
                              $('#update_paymentoption').addClass('btn-primary')
                              $('#update_paymentoption').attr('id','create_paymentoption')
                        }

                        var temp_payment_option = all_paymemt_options.filter(x=>x.id == selected_payment_opion)
                        $('#input_paymenttype').val("").change()
                        $('#input_bankname').val("")
                        $('#input_acctname').val("")
                        $('#input_acctnumber').val("")
                        $('#input_mobilenumber').val("")
                        $('#payment_option_form').modal()
                  })

                  var selected_payment_opion = null
                  $(document).on('click','.edit_section',function(){
                        selected_payment_opion = $(this).attr('data-id')
                        var temp_payment_option = all_paymemt_options.filter(x=>x.id == selected_payment_opion)
                        $('#input_paymenttype').val(temp_payment_option[0].paymenttype).change()
                        $('#input_bankname').val(temp_payment_option[0].optionDescription)
                        $('#input_acctname').val(temp_payment_option[0].accountName)
                        $('#input_acctnumber').val(temp_payment_option[0].accountNum)
                        $('#input_mobilenumber').val(temp_payment_option[0].mobileNum)
                        $('#create_paymentoption').text('Update')
                        $('#create_paymentoption').removeClass('btn-primary')
                        $('#create_paymentoption').addClass('btn-success')
                        $('#create_paymentoption').attr('id','update_paymentoption')
                        $('#payment_option_form').modal()
                  })

                  $(document).on('click','.update_image',function(){
                        $('#upload_progress').css('width',0)
                        $('#demo').removeAttr('src')
                        $('#input_paymentoptions').val("")
                        var selected_payment_option = $(this).attr('data-id')
                        var temp_payment_option = all_paymemt_options.filter(x=>x.id == selected_payment_option)
                        $('#payment_option_id').val(temp_payment_option[0].id)
                        if(temp_payment_option[0].paymenttype == 3){
                              $('.payment_type_holder').val(temp_payment_option[0].description + ':'+temp_payment_option[0].optionDescription)
                        }else{
                              $('.payment_type_holder').val(temp_payment_option[0].description)
                        }

                        $('#payment_type_holder').val()
                        $('#upload_modal').modal()
                  })

                  $(document).on('click','.delete_section',function(){
                        selected_payment_opion = $(this).attr('data-id')
                        var url = '/setup/payment/options/delete'
                        if(school_setup.setup == 1 && school_setup.projectsetup == 'offline'){
                              url = school_setup.es_cloudurl+'/setup/payment/options/delete'
                        }
                        $.ajax({
                              url: url,
                              type: 'GET',
                              data:{
                                    paymentoptionid:selected_payment_opion
                              },
                              success:function(data) {
                                    if(data[0].status == 1){
                                          if(school_setup.setup == 1 && school_setup.projectsetup == 'offline'){
                                                get_deleted('onlinepaymentoptions')
                                          }else{
                                                Toast.fire({
                                                      type: 'success',
                                                      title: 'Payment Option Deleted!'
                                                })
                                                get_paymentoptions()
                                          }
                                    }else{
                                          Toast.fire({
                                                type: 'error',
                                                title: data[0].message
                                          })
                                    }
                              }
                        })
                  })

                  $( '#upload_payment_option' )
                    .submit( function( e ) {

                        if($('#input_paymentoptions').val() == ""){
                              Toast.fire({
                                    type: 'warning',
                                    title: 'No File Selected!'
                              })
                              return false
                        }

                        var inputs = new FormData(this)

                        var url = '/setup/payment/options/image/upload'

                        // if(school_setup.setup == 1 && school_setup.projectsetup == 'offline'){
                        //       url = school_setup.es_cloudurl+'/setup/payment/options/image/upload'
                              
                        // }
                        
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
                              url: url,
                              data:inputs,
                              type: 'POST',
                              processData: false,
                              contentType: false,
                              success:function(data) {
                                    if(data[0].status == 1){
                                          // if(school_setup.setup == 1 && school_setup.projectsetup == 'offline'){
                                          //       get_updated('onlinepaymentoptions')
                                          // }else{
                                                Toast.fire({
                                                      type: 'success',
                                                      title: 'Payment Option Updated!'
                                                })
                                                get_paymentoptions()
                                          // }
                                    }else{
                                          Toast.fire({
                                                type: 'error',
                                                title: data[0].message
                                          })
                                    }
                              }
                        })
                  
                        e.preventDefault();
                  })

                  $(document).on('click','#update_paymentoption',function(){

                        if($('#input_paymenttype').val() == 3){
                              if($('#input_bankname').val() == ""){
                                    Toast.fire({
                                          type: 'warning',
                                          title: 'Bank Name is required!'
                                    })
                                    return false
                              }
                              if($('#input_acctname').val() == ""){
                                    Toast.fire({
                                          type: 'warning',
                                          title: 'Account Name is required!'
                                    })
                                    return false
                              }
                              if($('#input_acctnumber').val() == ""){
                                    Toast.fire({
                                          type: 'warning',
                                          title: 'Account Number is required!'
                                    })
                                    return false
                              }
                        }else{
                              if($('#input_acctname').val() == ""){
                                    Toast.fire({
                                          type: 'warning',
                                          title: 'Account Name is required!'
                                    })
                                    return false
                              }
                              if($('#input_mobilenumber').val() == ""){
                                    Toast.fire({
                                          type: 'warning',
                                          title: 'Mobile Number is required!'
                                    })
                                    return false
                              }
                        }

                        var url = '/setup/payment/options/update'

                        if(school_setup.setup == 1 && school_setup.projectsetup == 'offline'){
                              url = school_setup.es_cloudurl+'/setup/payment/options/update'
                              
                        }

                        $.ajax({
                              type:'GET',
                              url: url,
                              data:{
                                    paymentoptionid:selected_payment_opion,
                                    bankname:$('#input_bankname').val(),
                                    acctname:$('#input_acctname').val(),
                                    acctnum:$('#input_acctnumber').val(),
                                    mobilenum:$('#input_mobilenumber').val(),
                                    paymenttype:$('#input_paymenttype').val(),
                              },
                              success:function(data) {
                                    if(data[0].status == 1){
                                          if(school_setup.setup == 1 && school_setup.projectsetup == 'offline'){
                                                get_updated('onlinepaymentoptions')
                                          }else{
                                                Toast.fire({
                                                      type: 'success',
                                                      title: 'Payment Option Updated!'
                                                })
                                                get_paymentoptions()
                                          }
                                    }else{
                                          Toast.fire({
                                                type: 'error',
                                                title: data[0].message
                                          })
                                    }
                              }
                        })

                  })
                  
                  $(document).on('click','#create_paymentoption',function(){

                        if($('#input_paymenttype').val() == 3){
                              if($('#input_bankname').val() == ""){
                                    Toast.fire({
                                          type: 'warning',
                                          title: 'Bank Name is required!'
                                    })
                                    return false
                              }
                              if($('#input_acctname').val() == ""){
                                    Toast.fire({
                                          type: 'warning',
                                          title: 'Account Name is required!'
                                    })
                                    return false
                              }
                              if($('#input_acctnumber').val() == ""){
                                    Toast.fire({
                                          type: 'warning',
                                          title: 'Account Number is required!'
                                    })
                                    return false
                              }
                        }else{
                              if($('#input_acctname').val() == ""){
                                    Toast.fire({
                                          type: 'warning',
                                          title: 'Account Name is required!'
                                    })
                                    return false
                              }
                              if($('#input_mobilenumber').val() == ""){
                                    Toast.fire({
                                          type: 'warning',
                                          title: 'Mobile Number is required!'
                                    })
                                    return false
                              }
                        }

                        var url = '/setup/payment/options/create'

                        if(school_setup.setup == 1 && school_setup.projectsetup == 'offline'){
                              url = school_setup.es_cloudurl+'/setup/payment/options/create'
                              
                        }


                        $.ajax({
                              type:'GET',
                              url: url,
                              data:{
                                    bankname:$('#input_bankname').val(),
                                    acctname:$('#input_acctname').val(),
                                    acctnum:$('#input_acctnumber').val(),
                                    mobilenum:$('#input_mobilenumber').val(),
                                    paymenttype:$('#input_paymenttype').val(),
                              },
                              success:function(data) {
                                    if(data[0].status == 1){
                                          if(school_setup.setup == 1 && school_setup.projectsetup == 'offline'){
                                                get_last_index('onlinepaymentoptions')
                                          }else{
                                                Toast.fire({
                                                      type: 'success',
                                                      title: 'Payment Option Updated!'
                                                })
                                                get_paymentoptions()
                                          }
                                    }else{
                                          Toast.fire({
                                                type: 'error',
                                                title: data[0].message
                                          })
                                    }
                              }
                        })
                  })

                  $(document).on('change','#input_paymenttype',function(){

                        $('#input_bankname_holder').attr('hidden','hidden')
                        $('#input_acctname_holder').attr('hidden','hidden')
                        $('#input_acctnumber_holder').attr('hidden','hidden')
                        $('#input_mobilenumber_holder').attr('hidden','hidden')
                        
                        if($(this).val() == ""){
                              $('#create_paymentoption').attr('disabled','disabled')
                              return false;
                        }
                        $('#create_paymentoption').removeAttr('disabled')
                        if($(this).val() == 3){
                              $('#input_bankname_holder').removeAttr('hidden')
                              $('#input_acctname_holder').removeAttr('hidden')
                              $('#input_acctnumber_holder').removeAttr('hidden')
                        }else{
                              $('#input_acctname_holder').removeAttr('hidden')
                              $('#input_mobilenumber_holder').removeAttr('hidden')
                        }
                  })

                  
            })

      </script>
@endsection


