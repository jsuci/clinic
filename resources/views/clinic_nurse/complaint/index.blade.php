
@extends('clinic_nurse.layouts.app')

  <!-- Select2 -->
  {{-- <link rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
  <link rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}"> --}}
<style>

    .select2-container .select2-selection--single {
            height: 40px !important;
        }
</style>
@section('content')
    @php
        use \Carbon\Carbon;
        $now = Carbon::now();
        $comparedDate = $now->toDateString();
        date_default_timezone_set('Asia/Manila');
    @endphp

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h3 class="m-0">Complaints</h3>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Complaints</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>

    <section class="content">
        <div class="container-fluid">
        <!-- Info boxes -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>&nbsp;</label><br/>
                                    <button type="button" class="btn btn-info btn-block" id="btn-create"><i class="fa fa-plus"></i> Create</button>
                                </div>
                                <div class="col-md-6">
                                    <label>Date range</label>
                                    <input type="text" class="form-control float-right" id="reservation">
                                </div>
                                <div class="col-md-3 text-right">
                                    <label>&nbsp;</label><br/>
                                    <button type="button" class="btn btn-primary" id="btn-generate"><i class="fa fa-sync"></i> Generate</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="results-container" class="row"></div>
        </div>
    </section>
    <div class="modal fade" id="modal-addcomplaint">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Add Complaint</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
              <div class="row">
                  <div class="col-md-12 mb-2">
                      <label>Complainant <span class="text-danger">*</span></label><br/>
                      <select class="form-control select2" style="width: 100%;" id="addcomplaint-complainant">
                      </select>
                      {{-- <input type="text" class="form-control" placeholder="Add option" id="input--add-"/> --}}
                  </div>
                  <div class="col-md-12 mb-2">
                      <label>Description <span class="text-danger">*</span></label><br/>
                      <textarea class="form-control" id="addcomplaint-description"></textarea>
                  </div>
                  <div class="col-md-6">
                      <label>Date <span class="text-danger">*</span></label><br/>
                      <input type="date" class="form-control" id="addcomplaint-date" value="{{date('Y-m-d')}}"/>
                  </div>
                  <div class="col-md-6">
                      <label>Time <span class="text-danger">*</span></label><br/>
                      <input type="time" class="form-control" id="addcomplaint-time" value="{{date('H:i')}}"/>
                  </div>
                  <div class="col-md-12 mt-2">
                      <label>Action Taken <span class="text-danger"></span></label><br/>
                      <textarea class="form-control" id="addcomplaint-actiontaken"></textarea>
                  </div>
              </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal" id="btn-close-addcomplaint">Close</button>
            <button type="button" class="btn btn-primary" id="btn-submit-addcomplaint">Submit</button>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="modal-editcomplaint">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Edit Complaint</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
              <div class="row">
                  <div class="col-md-12 mb-2">
                      <label>Complainant <span class="text-danger">*</span></label><br/>
                      <select class="form-control select2" style="width: 100%;" id="editcomplaint-complainant">
                      </select>
                      {{-- <input type="text" class="form-control" placeholder="Add option" id="input--add-"/> --}}
                  </div>
                  <div class="col-md-12 mb-2">
                      <label>Description <span class="text-danger">*</span></label><br/>
                      <textarea class="form-control" id="editcomplaint-description"></textarea>
                  </div>
                  <div class="col-md-6">
                      <label>Date <span class="text-danger">*</span></label><br/>
                      <input type="date" class="form-control" id="editcomplaint-date" />
                  </div>
                  <div class="col-md-6">
                      <label>Time <span class="text-danger">*</span></label><br/>
                      <input type="time" class="form-control" id="editcomplaint-time" />
                  </div>
                  <div class="col-md-12 mt-2">
                      <label>Action Taken <span class="text-danger"></span></label><br/>
                      <textarea class="form-control" id="editcomplaint-actiontaken"></textarea>
                  </div>
              </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal" id="btn-close-editcomplaint">Close</button>
            <button type="button" class="btn btn-primary" id="btn-submit-editcomplaint">Save Changes</button>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="modal-addmedication">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Add Medication</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
              <div class="row">
                  <div class="col-md-8 mb-2">
                        <label>Drug name <span class="text-danger">*</span></label><br/>
                        <select class="form-control select2" style="width: 100%;" id="addmedication-drugid">
                            
                        </select>
                  </div>
                  <div class="col-md-4 mb-2">
                        <label>Quantity <span class="text-danger">*</span></label><br/>
                        <input type="number" class="form-control" id="addmedication-quantity" value="0"/>
                  </div>
                  <div class="col-md-12 mt-2">
                      <label>Remarks</label><br/>
                      <textarea class="form-control" id="addmedication-remarks"></textarea>
                  </div>
              </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal" id="btn-close-addmedication">Close</button>
            <button type="button" class="btn btn-primary" id="btn-submit-addmedication"><i class="fa fa-share"></i> Submit</button>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="modal-editmedication">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Edit Medication</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
              <div class="row">
                  <div class="col-md-8 mb-2">
                        <label>Drug name <span class="text-danger">*</span></label><br/>
                        <select class="form-control select2" style="width: 100%;" id="editmedication-drugid">
                            
                        </select>
                  </div>
                  <div class="col-md-4 mb-2">
                        <label>Quantity <span class="text-danger">*</span></label><br/>
                        <input type="number" class="form-control" id="editmedication-quantity" value="0"/>
                  </div>
                  <div class="col-md-12 mt-2">
                      <label>Remarks</label><br/>
                      <textarea class="form-control" id="editmedication-remarks"></textarea>
                  </div>
              </div>
          </div>
          <div class="modal-footer">
                  <div class="col-md-4 text-left">
                    <button type="button" class="btn btn-default" data-dismiss="modal" id="btn-close-editmedication">Close</button>
                </div>
                  <div class="col-md-8 text-right">
                    <button type="button" class="btn btn-danger" id="btn-delete-editmedication"><i class="fa fa-trash"></i> Delete</button>
                    <button type="button" class="btn btn-primary" id="btn-submit-editmedication"><i class="fa fa-edit"></i> Save Changes</button>
                  </div>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    @endsection
    @section('footerjavascript')
    <script>
        $(function () {
            //Initialize Select2 Elements
            $('.select2').select2()
        
            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })
            
            $('#reservation').daterangepicker()
        })  
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
        
        
        $(document).ready(function(){

            $('#btn-create').on('click', function(){
                $('#modal-addcomplaint').modal('show')
                $.ajax({
                    url:    '/clinic/complaints/getallusers',
                    type:   'GET',
                    success:function(data)
                    {
                        $('#addcomplaint-complainant').empty()
                        $('#addcomplaint-complainant').append(data)
                    }
                })
            })

            getcomplaints($('#reservation').val())

            function getcomplaints(selecteddaterange)
            {
                Swal.fire({
                    title: 'Fetching data...',
                    onBeforeOpen: () => {
                        Swal.showLoading()
                    },
                    allowOutsideClick: false
                })

                $.ajax({
                    url:    '/clinic/complaints/getcomplaints',
                    type:   'GET',
                    data: {
                        selecteddaterange      :   selecteddaterange
                    },
                    success:function(data)
                    {
                        $('#results-container').empty()
                        $('#results-container').append(data)
                        $(".swal2-container").remove();
                        $('body').removeClass('swal2-shown')
                        $('body').removeClass('swal2-height-auto')
                    }
                })
            }

            $('#btn-submit-addcomplaint').on('click', function(){
                var checkvalidation = 0;
                var addcomplainant  = $('#addcomplaint-complainant').val();
                var adddescription  = $('#addcomplaint-description').val();
                var adddate         = $('#addcomplaint-date').val();
                var addtime         = $('#addcomplaint-time').val();
                var addactiontaken  = $('#addcomplaint-actiontaken').val();
                if(adddescription.replace(/^\s+|\s+$/g, "").length == 0)
                {
                    checkvalidation = 1;
                    $('#addcomplaint-description').css('border','1px solid red')
                }
                if(adddate.replace(/^\s+|\s+$/g, "").length == 0)
                {
                    checkvalidation = 1;
                    $('#addcomplaint-date').css('border','1px solid red')
                }
                if(addtime.replace(/^\s+|\s+$/g, "").length == 0)
                {
                    checkvalidation = 1;
                    $('#addcomplaint-time').css('border','1px solid red')
                }
                if(checkvalidation == 0)
                {
                    $.ajax({
                        url:    '/clinic/complaints/add',
                        type:   'GET',
                        dataType:   'json',
                        data: {
                            addcomplainant      :   addcomplainant,
                            adddescription      :   adddescription,
                            adddate             :   adddate,
                            addtime             :   addtime,
                            addactiontaken      :   addactiontaken
                        },
                        success:function(data)
                        {
                            if(data == 0)
                            {
                                Toast.fire({
                                    type: 'warning',
                                    title: 'Complainant for the selected date already exists!'
                                })
                            }else if(data == 1){
                                
                                Toast.fire({
                                    type: 'success',
                                    title: 'Added successfully!'
                                })
                                $('#modal-addcomplaint').find('input').val('')
                                $('#modal-addcomplaint').find('textarea').val('')
                                $('#btn-close-addcomplaint').click();
                                getcomplaints($('#reservation').val())
                            }else{
                                Toast.fire({
                                    type: 'error',
                                    title: 'Something went wrong!'
                                })
                            }
                        }
                    })
                }else{
                    Toast.fire({
                        type: 'warning',
                        title: 'Please fill in the required fields!'
                    })
                }
            })

            $('#btn-generate').on('click', function(){
                getcomplaints($('#reservation').val())
            })
            $(document).on('click', '.btn-complaint-edit', function(){
                var complaintid = $(this).attr('data-id');
                $('#btn-submit-editcomplaint').attr('data-id', complaintid)
                var userid;
                $('#modal-editcomplaint').modal('show')
                $.ajax({
                    url:'/clinic/complaints/getinfo',
                    type:'GET',
                    dateType: 'json',
                    data: {
                        id      :  complaintid
                    },
                    success:function(data) {
                        userid = data.userid;
                        $('#editcomplaint-description').val(data.description)
                        $('#editcomplaint-date').val(data.cdate)
                        $('#editcomplaint-time').val(data.ctime)
                        $('#editcomplaint-actiontaken').val(data.actiontaken)
                    }
                })
                $.ajax({
                    url:    '/clinic/complaints/getallusers',
                    type:   'GET',
                    success:function(data)
                    {
                        
                        $('#editcomplaint-complainant').empty()
                        $('#editcomplaint-complainant').append(data)
                        $('#editcomplaint-complainant option[value="'+userid+'"]').prop('selected', true)
                        // $('#editcomplaint-complainant').val(userid)
                    }
                })
            })

            $(document).on('click','#btn-submit-editcomplaint', function(){
                var checkvalidation = 0;
                var complaintid      = $(this).attr('data-id'); 
                var editcomplainant  = $('#editcomplaint-complainant').val();
                var editdescription  = $('#editcomplaint-description').val();
                var editdate         = $('#editcomplaint-date').val();
                var edittime         = $('#editcomplaint-time').val();
                var editactiontaken  = $('#editcomplaint-actiontaken').val();
                if(editdescription.replace(/^\s+|\s+$/g, "").length == 0)
                {
                    checkvalidation = 1;
                    $('#editcomplaint-description').css('border','1px solid red')
                }
                if(editdate.replace(/^\s+|\s+$/g, "").length == 0)
                {
                    checkvalidation = 1;
                    $('#editcomplaint-date').css('border','1px solid red')
                }
                if(edittime.replace(/^\s+|\s+$/g, "").length == 0)
                {
                    checkvalidation = 1;
                    $('#editcomplaint-time').css('border','1px solid red')
                }
                if(checkvalidation == 0)
                {
                    $.ajax({
                        url:    '/clinic/complaints/edit',
                        type:   'GET',
                        dataType:   'json',
                        data: {
                            complaintid      :   complaintid,
                            editcomplainant      :   editcomplainant,
                            editdescription      :   editdescription,
                            editdate             :   editdate,
                            edittime             :   edittime,
                            editactiontaken      :   editactiontaken
                        },
                        success:function(data)
                        {
                            if(data == 0)
                            {
                                Toast.fire({
                                    type: 'warning',
                                    title: 'Complainant for the selected date already exists!'
                                })
                            }else if(data == 1){
                                
                                Toast.fire({
                                    type: 'success',
                                    title: 'Added successfully!'
                                })
                                $('#modal-addcomplaint input, textarea').val('')
                                $('#btn-close-addcomplaint').click();
                                getcomplaints($('#reservation').val())
                            }else{
                                Toast.fire({
                                    type: 'error',
                                    title: 'Something went wrong!'
                                })
                            }
                        }
                    })
                }else{
                    Toast.fire({
                        type: 'warning',
                        title: 'Please fill in the required fields!'
                    })
                }
            })

            $(document).on('click', '.btn-complaint-delete', function(){
                var complaintid = $(this).attr('data-id');
                Swal.fire({
                    title: 'Do you want to delete this complaint?',
                    type: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Delete'
                })
                .then((result) => {
                    if (result.value) {
                        $.ajax({
                            url:'/clinic/complaints/delete',
                            type:'GET',
                            data: {
                                '_token': '{{ csrf_token() }}',
                                id      :  complaintid
                            },
                            complete:function() {
                                Toast.fire({
                                    type: 'success',
                                    title: 'Deleted successfully!'
                                })
                                getcomplaints($('#reservation').val())
                            }
                        })
                    }
                })
            })
            $(document).on('click','.btn-complaint-addmedication', function(){
                var complaintid = $(this).attr('data-id');
                $('#btn-submit-addmedication').attr('data-id', complaintid)
                $('#modal-addmedication').modal('show')
                $.ajax({
                    url:'/clinic/complaints/getdrugs',
                    type:'GET',
                    dateType: 'json',
                    data: {
                        id      :  complaintid
                    },
                    success:function(data) {
                        $('#addmedication-drugid').empty();
                        if(data.length>0)
                        {
                            $.each(data, function(key, value){
                                if(value.quantityleft > 0)
                                {
                                    $('#addmedication-drugid').append(
                                        '<option value="'+value.id+'">('+value.condition+') '+value.genericname+' - '+value.brandname+' ('+value.quantityleft+'/'+value.quantity+')</option>'
                                    );
                                }
                            })
                        }

                        // userid = data.userid;
                        // $('#editcomplaint-description').val(data.description)
                        // $('#editcomplaint-date').val(data.cdate)
                        // $('#editcomplaint-time').val(data.ctime)
                        // $('#editcomplaint-actiontaken').val(data.actiontaken)
                    }
                })

            })
            $(document).on('click','#btn-submit-addmedication', function(){
                var complaintid = $(this).attr('data-id');
                var drugid = $('#addmedication-drugid').val();
                var quantity = $('#addmedication-quantity').val()
                var remarks = $('#addmedication-remarks').val()
                


                var checkvalidation = 0;
                if(quantity.replace(/^\s+|\s+$/g, "").length == 0 || quantity == 0)
                {
                    checkvalidation = 1;
                    $('#addmedication-quantity').css('border','1px solid red')
                }else{
                    checkvalidation = 0;
                    $('#addmedication-quantity').removeAttr('style')
                }

                if(checkvalidation == 0)
                {
                    $.ajax({
                        url:'/clinic/complaints/addmed',
                        type:'GET',
                        dateType: 'json',
                        data: {
                            complaintid      :  complaintid,
                            drugid           :  drugid,
                            quantity         :  quantity,
                            remarks          :  remarks
                        },
                        success:function(data) {
                            if(data == 1)
                            {

                                Toast.fire({
                                    type: 'success',
                                    title: 'Added successfully!'
                                })
                                getcomplaints($('#reservation').val())
                                $('#btn-close-addmedication').click()
                                // $('#modal-addcomplaint input, textarea').val('');
                                $('#modal-addmedication').find('input').val('')
                                $('#modal-addmedication').find('textarea').val('')
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
            $(document).on('click','.btn-complaint-editmedication', function(){
                var complaintid = $(this).attr('data-id');
                $('#btn-submit-editmedication').attr('data-id', complaintid)
                $('#btn-delete-editmedication').attr('data-id', complaintid)
                $('#modal-editmedication').modal('show')
                $.ajax({
                    url:'/clinic/complaints/getdrugs',
                    type:'GET',
                    dateType: 'json',
                    data: {
                        id      :  complaintid
                    },
                    success:function(data) {
                        $('#editmedication-drugid').empty();
                        if(data.length>0)
                        {
                            $.each(data, function(key, value){
                                var selectedoption = '';
                                if(value.selected == 1)
                                {
                                    var selectedoption = 'selected';
                                }
                                if(value.quantityleft == 0) 
                                {
                                    if(value.selected == 1)
                                    {
                                        $('#editmedication-drugid').append(
                                            '<option value="'+value.id+'" '+selectedoption+'>('+value.condition+') '+value.genericname+' - '+value.brandname+' ('+value.quantityleft+'/'+value.quantity+')</option>'
                                        );
                                       $('#editmedication-quantity').val(value.quantityadded)
                                       $('#editmedication-remarks').val(value.remarks)
                                    }
                                }else{
                                    if(value.selected == 1)
                                    {
                                        $('#editmedication-drugid').append(
                                            '<option value="'+value.id+'" '+selectedoption+'>('+value.condition+') '+value.genericname+' - '+value.brandname+' ('+value.quantityleft+'/'+value.quantity+')</option>'
                                        );
                                       $('#editmedication-quantity').val(value.quantityadded)
                                       $('#editmedication-remarks').val(value.remarks)
                                    }else{
                                        $('#editmedication-drugid').append(
                                            '<option value="'+value.id+'" '+selectedoption+'>('+value.condition+') '+value.genericname+' - '+value.brandname+' ('+value.quantityleft+'/'+value.quantity+')</option>'
                                        );
                                    }
                                }
                            })
                        }

                        // userid = data.userid;
                        // $('#editcomplaint-description').val(data.description)
                        // $('#editcomplaint-date').val(data.cdate)
                        // $('#editcomplaint-time').val(data.ctime)
                        // $('#editcomplaint-actiontaken').val(data.actiontaken)
                    }
                })

            })
            $(document).on('click','#btn-submit-editmedication', function(){
                var complaintid = $(this).attr('data-id');
                var drugid = $('#editmedication-drugid').val();
                var quantity = $('#editmedication-quantity').val()
                var remarks = $('#editmedication-remarks').val()

                var checkvalidation = 0;
                if(quantity.replace(/^\s+|\s+$/g, "").length == 0 || quantity == 0)
                {
                    checkvalidation = 1;
                    $('#editmedication-quantity').css('border','1px solid red')
                }else{
                    checkvalidation = 0;
                    $('#editmedication-quantity').removeAttr('style')
                }

                if(checkvalidation == 0)
                {
                    $.ajax({
                        url:'/clinic/complaints/editmed',
                        type:'GET',
                        dateType: 'json',
                        data: {
                            complaintid      :  complaintid,
                            drugid           :  drugid,
                            quantity         :  quantity,
                            remarks          :  remarks
                        },
                        success:function(data) {
                            if(data == 1)
                            {

                                Toast.fire({
                                    type: 'success',
                                    title: 'Updated successfully!'
                                })
                                getcomplaints($('#reservation').val())
                                $('#btn-close-editmedication').click()
                                // $('#modal-addcomplaint input, textarea').val('');
                                $('#modal-editmedication').find('input').val('')
                                $('#modal-editmedication').find('textarea').val('')
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
            $(document).on('click','#btn-delete-editmedication', function(){
                var complaintid = $(this).attr('data-id');
                Swal.fire({
                    title: 'Do you want to delete this medication?',
                    type: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Delete'
                })
                .then((result) => {
                    if (result.value) {
                        $.ajax({
                            url:'/clinic/complaints/deletemed',
                            type:'GET',
                            dataType: 'json',
                            data: {
                                id      :  complaintid
                            },
                            success:function(data) {
                                if(data == 1)
                                {
                                    Toast.fire({
                                        type: 'success',
                                        title: 'Deleted successfully!'
                                    })
                                    getcomplaints($('#reservation').val())
                                    $('#btn-close-addmedication').click();
                                    $('#btn-close-editmedication').click();
                                    $('#modal-editmedication').find('input').val('')
                                    $('#modal-editmedication').find('textarea').val('')
                                    $('#modal-addmedication').find('input').val('')
                                    $('#modal-addmedication').find('textarea').val('')
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
            })
        })
    </script>
@endsection
