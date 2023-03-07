
@extends('superadmin.layouts.app2')

@section('pagespecificscripts')
      <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
      <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
      <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
      <link rel="stylesheet" href="{{ asset('plugins/datatables-fixedcolumns/css/fixedColumns.bootstrap4.css') }}">
      <link rel="stylesheet" href="{{asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
      <style>
             .shadow {
                  box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
                  border: 0;
            }
      </style>
@endsection

@section('modalSection')
<div class="modal fade" id="modal-primary" style="display: none;" aria-hidden="true">
      <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header pb-2 pt-2 border-0">
                  <h4 class="modal-title" style="font-size: 1.1rem !important">RFID Registration Form</h4>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                  <div class="form-group">
                        <label for="">RFID Code</label>
                        <input type="text" id="idnum" name="idnum" class="form-control" oninput="this.value=this.value.replace(/[^0-9]/g,'');">
                        <p class="text-danger"><i>Tap the card in the scanner to register.</i></p>
                  </div>
            </div>
          </div>
      </div>
    </div>
@endsection

@section('content')
     

      
      <section class="content-header">
            <div class="container-fluid">
                  <div class="row mb-2">
                        <div class="col-sm-6">
                              <h1>RFID Registration</h1>
                        </div>
                        <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                              <li class="breadcrumb-item"><a href="/home">Home</a></li>
                              <li class="breadcrumb-item active">RFID Registration</li>
                        </ol>
                        </div>
                  </div>
            </div>
      </section>
      <section class="content pt-0">
            <div class="container-fluid">
                  <div class="row">
                        <div class="col-md-12">
                              <div class="card shadow" style="">
                                    <div class="card-body">
                                          <div class="row mt-2">
                                                <div class="col-md-12" style="font-size:.9rem !important">
                                                      <table class="table-hover table table-striped table-sm table-bordered" id="rfid_table" width="100%" >
                                                            <thead>
                                                                  <tr>
                                                                        <th width="80%">RFID #</th>
                                                                        <th width="20%">Registered Date</th>
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

                  var all_rfid = []
                  load_rfid_datatable()
                  rfid_list()
               

                  function rfid_list(){
                        $.ajax({
					type:'GET',
					url: '/rfid/list',
					success:function(data) {
                                    if(data.length == 0){
                                          Toast.fire({
                                                type: 'warning',
                                                title: 'No RFID found!'
                                          })
                                    }else{
                                          Toast.fire({
                                                type: 'warning',
                                                title: data.length+' RFID found!'
                                          })
                                    }
						all_rfid = data
                                    load_rfid_datatable()
					}
				})
                  }

                  const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                  })

                  function load_rfid_datatable(){

                        $("#rfid_table").DataTable({
                              destroy: true,
                              data:all_rfid,
                              lengthChange : false,
                              autoWidth: false,
                              stateSave: true,
                              columns: [
                                          { "data": "rfidcode" },
                                          { "data": "createddatetime" },
                                    ],
                              
                        });

                        var label_text = $($("#rfid_table_wrapper")[0].children[0])[0].children[0]
                        $(label_text)[0].innerHTML = '<button class="btn btn-primary btn-sm"  data-toggle="modal"  data-target="#modal-primary">Register RFID</button>'


                  }

                  function storerfid(){

                        $.ajax({
                              type:'POST',
                              url: '/storerfid/'+$('input[name=idnum]').val()+'/'+$('select[name=rfidschool]').val(),
                              data: {'_token': '{{ csrf_token() }}'},
                              success:function(data){
								  $('#idnum').val("")
                                    if(data[0].status == 2){
                                          Toast.fire({
                                                type: 'warning',
                                                title: data[0].message
                                          })
                                    }else if(data[0].status == 1){
                                          Toast.fire({
                                                type: 'success',
                                                title: data[0].message
                                          })
                                          rfid_list()
                                    }else{
                                          Toast.fire({
                                                type: 'error',
                                                title: data[0].message
                                          })
                                    }
                              }
                        })

                  }

                  $(document).on('keypress',function(e) {
                        if(e.which == 13) {
                              if($('input[name=idnum]').val() == ''){
                                    Toast.fire({
                                          type: 'error',
                                          title: 'No input field selected!'
                                    })
                                    return false
                              }
                              storerfid()
                        }
                  });

                
            })
      </script>

      
@endsection

