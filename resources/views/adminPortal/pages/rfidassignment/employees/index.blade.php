
@extends('adminPortal.layouts.app2')

@section('pagespecificscripts')
    <link rel="stylesheet" href="{{asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/pagination.css')}}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
  
      <style>
            .dropdown-toggle::after {
                  display: none;
                  margin-left: .255em;
                  vertical-align: .255em;
                  content: "";
                  border-top: .3em solid;
                  border-right: .3em solid transparent;
                  border-bottom: 0;
                  border-left: .3em solid transparent;
            }
            .btn-group.special {
  display: flex;
}

.special .btn {
  flex: 1
}
td {
    padding: 2px !important;
}
      </style>

@endsection


@section('modalSection')
  <div class="modal fade" id="passModal" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-sm">
      <form id="checkPassForm" method="POST" action="/matchPassword">
        <div class="modal-content">
                <div class="modal-body">
                    <div class="message"></div>
                    <div class="form-group">
                        <label>Enter Password</label>
                        <input type="password"  id="password"  name="password" class="form-control">
                        <span class="invalid-feedback" role="alert">
                            <strong>Password does not match</strong>
                        </span>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="submit" class="btn btn-primary">RESET</button>
                </div>
          </div>
      </form>
    </div>
  </div>
@endsection


@section('content')
      <section class="content-header">
            <div class="container-fluid">
                  <div class="row">
                        <div class="col-sm-6">
                        
                        </div>
                        <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                              <li class="breadcrumb-item"><a href="/home">Home</a></li>
                              <li class="breadcrumb-item active">Employee RFID Assignment</li>
                        </ol>
                        </div>
                  </div>
            </div>
      </section>
      <section class="content pt-0">
            <div class="container-fluid">
                {{-- <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-12" style="overflow: scroll;">
                                <div class="btn-group btn-group-sm special" role="group" aria-label="Basic example">
                                    @foreach( range('A', 'Z') as $element) 
                                    <button type="button" class="btn btn-primary btn-eachletter @if(strtolower($element) == 'a') active @endif" >{{$element}}</button>
                                    @endforeach
                                </div>
                            </div>
                            
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <label>Search</label>
                                <input class="filter form-control" placeholder="Search student" />
                            </div>
                        </div>
                    </div>
                    <div class="card-body" style="height: 700px; overflow: scroll;">
                        @foreach($students as $student)
                            @php
                            
                                $avatar = 'assets/images/avatars/unknown.png';
                            @endphp
                            <div class="row mb-2 p-2" style="border: 1px solid #ddd; border-radius: 5px;">
                                <div class="col-md-3">
                                    <img src="{{asset($student->picurl)}}" onerror="this.onerror = null, this.src='{{asset($avatar)}}'" class="" alt="User Image" width="100px">

                                </div>
                                <div class="col-md-9"></div>
                            </div>
                        @endforeach
                    </div>
                </div> --}}
                  {{-- <div class="row">
                        <div class="col-12"> --}}
                              <div class="card">
                                    {{-- <div class="card-header">
                                          <h5 class="card-title">STUDENTS</h5>
                                    </div> --}}
                                    <div class="card-body">
                                        <table id="example2" class="table table-hover" style="font-size: 12px;">
                                            <thead>
                                                <tr>
                                                    {{-- <th></th> --}}
                                                    <th>Employee</th>
                                                    {{-- <th>Status</th> --}}
                                                    {{-- <th style="width: 20%;">Upload Photo</th> --}}
                                                    {{-- <th style="width: 5%;"></th> --}}
                                                    <th style="width: 25%;">RFID</th>
                                                    <th style="width: 5%;"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{-- @foreach($students as $student)
                                                    <tr>
                                                        <td>{{$student->sid}}</td>
                                                        <td>{{$student->lastname}}, {{$student->firstname}} {{$student->middlename}}
                                                        </td>
                                                        <td>{{$student->studentstatus}}</td>
                                                        <td> <input type="text" class="form-control" data-id="{{$student->id}}" value="{{$student->rfid}}"/></td>
                                                        <td><button type="button" class="btn btn-sm btn-warning btn-reset" data-id="{{$student->id}}"><i class="fa fa-undo"></i></button></td>
                                                    </tr>
                                                @endforeach --}}
                                            </tbody>
                                        </table>
                                    </div>
                              </div>   
                        {{-- </div>
                  </div> --}}
            </div>
      </section>
      
        {{-- <div id="edit_profile_pic" class="modal custom-modal fade" role="dialog" style="display: none;" aria-hidden="true" data-backdrop="static" data-keyboard="false">
          <div class="modal-dialog modal-dialog-centered modal-md" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                       <h5 class="modal-title"><strong>Profile Photo</strong></h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                         </button>
                     </div>
                <div class="modal-body">
                     <div class="row">                                                
                        <div class="col-md-12 text-center">                                
                            <div id="upload-demo"></div>                                    
                             </div>                                    
                         </div>
                    <input type="file" id="upload" class="form-control form-control-sm" style="overflow: hidden;">
                    <br>
                    <br>
                    <button class="btn btn-success upload-result">Upload Image</button>
                     </div>
                </div>
             </div>
        </div> --}}
@endsection

@section('footerjavascript')
    <script src="{{asset('js/pagination.js')}}"></script> 
    <script src="{{asset('plugins/sweetalert2/sweetalert2.all.min.js')}}"></script>
      
    <script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <!-- DataTables  & Plugins -->
    <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
    <script src="{{asset('plugins/jszip/jszip.min.js')}}"></script>
    <script src="{{asset('plugins/pdfmake/pdfmake.min.js')}}"></script>
    <script src="{{asset('plugins/pdfmake/vfs_fonts.js')}}"></script>
    <script src="{{asset('plugins/datatables-buttons/js/buttons.html5.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-buttons/js/buttons.print.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-buttons/js/buttons.colVis.min.js')}}"></script>

    <script>
        $(document).ready(function(){
            var onerror_url = @json(asset('dist/img/download.png'));
            function getemployees(){
                
                $('#example2').DataTable({
                    "paging": true,
                    // "lengthChange": false,
                    "searching": true,
                    "ordering": false,
                    "info": true,
                    "autoWidth": false,
                    "responsive": true,
                    "destroy": true,
                    serverSide: true,
                    processing: true,
                    // ajax:'/student/preregistration/list',
                    ajax:{
                        url: '/adminemployeesetup/index',
                        type: 'GET',
                        data: {
                            action : 'getemployees'
                        }
                    },
                    columns: [
                        { "data": null },
                        // { "data": 'studentstatus'},
                        // { "data": null },
                        { "data": null },
                        // { "data": null },
                        { "data": null }
                    ],
                    columnDefs: [
                        {
                            'targets': 0,
                            'orderable': false, 
                            'createdCell':  function (td, cellData, rowData, row, col) {
                                $(td)[0].innerHTML = ' <div class="row">'+
                                    '<div class="col-md-3">'+
                                        '<img src="/'+rowData.picurl+'?random='+new Date().getTime()+'" class="" alt="User Image" onerror="this.src=\''+onerror_url+'\'" width="40px"/>'+

                                        '</div>'+
                                        '<div class="col-md-9">'+
                                            '<div class="row">'+
                                                '<div class="col-md-12">'+rowData.lastname+', '+rowData.firstname+'</div>   ' +
                                                '<div class="col-md-12">'+ '<small class="text-primary">'+rowData.tid+'</small></div>   ' +
                                            '</div>'+
                                            
                                        
                                        '</div>'+
                                    '</div>'
                                    // $(td).addClass('align-middle')
                            }
                        },
                        // {
                        //     'targets': 1,
                        //     'orderable': false, 
                        //     'createdCell':  function (td, cellData, rowData, row, col) {
                        //             $(td).addClass('align-middle')
                        //         $(td)[0].innerHTML = '<button type="button" class="btn btn-outline-primary btn-block edit-pic-icon btn-sm" data-id="'+rowData.id+'>'+
                        //                 '<i class="fa fa-upload"></i> Upload Photo'+
                        //                 '</button>';
                                        
                        //             $(td).addClass('align-middle')
                        //     }
                        // },
                        {
                            'targets': 1,
                            'orderable': false, 
                            'createdCell':  function (td, cellData, rowData, row, col) {
                                if(rowData.rfid == null)
                                {
                                    rowData.rfid = '';
                                }
                                // $(td).css('vertical-align','middle !important')
                                $(td)[0].innerHTML = '<input type="text" class="form-control form-control-sm" data-id="'+rowData.id+'" value="'+rowData.rfid+'"/>';
                                    $(td).addClass('align-middle')
                            }
                        },
                        {
                            'targets': 2,
                            'orderable': false, 
                            'createdCell':  function (td, cellData, rowData, row, col) {
                                $(td)[0].innerHTML = '<button type="button" class="btn btn-sm btn-warning btn-reset" data-id="'+rowData.id+'"><i class="fa fa-undo"></i></button>';
                                    $(td).addClass('align-middle')
                            }
                        },
                    ]
                });
            }
            getemployees();
            $(document).on('click','.edit-pic-icon', function(){
                var studid = $(this).attr('data-id')
                $('#edit_profile_pic').modal('show')
                $('#upload-demo').empty()
                $('#upload').val('')
                $uploadCrop = $('#upload-demo').croppie({
                    enableExif: true,
                    viewport: {
                        width: 304,
                        height: 289,
                        // type: 'circle'        
                    },
                    boundary: {
                        width: 304,
                        height: 289
                    }
                });
                $('#upload').on('change', function () { 
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $uploadCrop.croppie('bind', {
                            url: e.target.result
                        }).then(function(){
                            console.log('jQuery bind complete');
                        });
                    }
                    reader.readAsDataURL(this.files[0]);
                });
                $('.upload-result').on('click', function (ev) {
                    $uploadCrop.croppie('result', {
                        type: 'canvas',
                        size: 'viewport'
                    }).then(function (resp) {
                        $.ajax({
                            url: "/adminemployeesetup/uploadphoto",
                            type: "POST",
                            data: {
                                "_token": "{{ csrf_token() }}",
                                "image"     :   resp,
                                "studid":   studid
                                },
                            success: function (data) {
                                window.location.reload();
                            }
                        });
                    });        
                });
            })
        })


        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
        var activeletter = 'A';
        function getemployees()
        {

        }
            $('.btn-eachletter').on('click', function(){
                $('.btn-eachletter').removeClass('active');
                $(this).addClass('active');
                activeletter = $(this).text();
            })
        $(document).on('keypress','input', function(e) {
            
            if(e.which == 13) {

                if($(this).val().replace(/^\s+|\s+$/g, "").length == 0){
                    
                    Toast.fire({
                        type: 'error',
                        title: 'Cannot be empty!'
                    })

                    $(this).val('')

                }
                else{

                    var thisinput = $(this);
                    var studentid   = $(this).attr('data-id');
                    var rfid        = $(this).val();

                    $.ajax({
                        url: '/adminemployeesetup/update',
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            employeeid   :studentid,
                            rfid        : rfid
                        }, success:function(data)
                        {
                            if(data == 1)
                            {

                                Toast.fire({
                                    type: 'success',
                                    title: 'RFID assigned successfully!'
                                })

                            }
                            else if(data == 2)
                            {

                                Toast.fire({
                                    type: 'warning',
                                    title: 'RFID is assigned already!'
                                })
                                thisinput.val('')
                            }
                            else{

                                Toast.fire({
                                    type: 'error',
                                    title: 'RFID not yet registered!'
                                })
                                thisinput.val('')

                            }
                        }
                    })
                    
                }
            
            }
        });
        $(document).on('click','.btn-reset', function(){
            var employeeid   = $(this).attr('data-id');
            Swal.fire({
                title: 'Are you sure you want to reset employee\'s RFID?',
                html:
                    "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, reset it!',
                allowOutsideClick: false
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: '/adminemployeesetup/reset',
                        type:"GET",
                        dataType:"json",
                        data:{
                            employeeid: employeeid
                        },
                        complete: function(){

                            Toast.fire({
                                type: 'success',
                                title: 'RFID reset successfully!'
                            })
                            $('input[data-id='+employeeid+']').val('')

                        }
                    })
                }
            })
        })
    </script>
  
    
    
@endsection

