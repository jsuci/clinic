
@extends($extends.'.layouts.app')

<style>
    .dataTable                  { font-size:80%; }
    .tschoolschedule .card-body { height:250px; }
    .tschoolcalendar            { font-size: 12px; }
    .tschoolcalendar .card-body { height: 250px; overflow-x: scroll; }
    .teacherd ul li a           { color: #fff; -webkit-transition: .3s; }
    .teacherd ul li             { -webkit-transition: .3s; border-radius: 5px; background: rgba(173, 177, 173, 0.3); margin-left: 2px; }
    .sf5                        { background: rgba(173, 177, 173, 0.3)!important; border: none!important; }
    .sf5menu a:hover            { background-color: rgba(173, 177, 173, 0.3)!important; }
    .teacherd ul li:hover       { transition: .3s; border-radius: 5px; padding: none; margin: none; }

    .small-box                  { box-shadow: 1px 2px 2px #001831c9; overflow-y: auto scroll; }

    .small-box h5               { text-shadow: 1px 1px 2px gray; }

    img{
        border-radius: unset !important;
    }

    .select2-container .select2-selection--single {
            height: 40px !important;
        }
</style>
@section('content')
    @php
        use \Carbon\Carbon;
        $now = Carbon::now();
        $comparedDate = $now->toDateString();
    @endphp

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h3 class="m-0">Medical History</h3>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Medical History</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>

    <section class="content">

        <!-- Default box -->
        <div class="card">
          <div class="card-header">
              <div class="row">
                <div class="col-md-5">
                    <label>Search</label>
                    <select class="form-control select2" style="width: 100%;" id="select-user">
                        @foreach($users as $user)
                            <option value="{{$user->userid}}">{{$user->name_showlast}}</option>
                        @endforeach
                    </select>
                </div>
              </div>
          </div>
          <div class="card-body p-0">
            <table class="table table-striped projects text-center">
                <thead>
                    <tr>
                        <th style="width: 10px;">
                            #
                        </th>
                        <th style="width: 35%">
                            Patient
                        </th>
                        <th style="width: 30%">
                            Details
                        </th>
                        {{-- <th>
                            Project Progress
                        </th> --}}
                        <th style="width: 8%" class="text-center">
                            Status
                        </th>
                        <th style="width: 25%">
                            Appointed
                        </th>
                    </tr>
                </thead>
                <tbody id="resultscontainer">
                </tbody>
            </table>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </section>
    {{-- <div class="modal fade" id="modal-addexperience">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Add Option</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
              <div class="row">
                  <div class="col-md-12">
                    <small class="badge badge-danger">Question: Please Check if You Have Experienced Any of the Following:</small>
                  </div>
              </div>
              <div class="row">
                  <div class="col-md-12">
                      <label>Option</label><br/>
                      <input type="text" class="form-control" placeholder="Add option" id="input-addoption"/>
                  </div>
              </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal" id="btn-closeexperience">Close</button>
            <button type="button" class="btn btn-primary" id="btn-addoption">Add</button>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div> --}}
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
            // $('#input-daterange').daterangepicker()
        })  
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
        function gethistory(userid)
        {
            
            Swal.fire({
                title: 'Fetching data...',
                onBeforeOpen: () => {
                    Swal.showLoading()
                },
                allowOutsideClick: false
            })

            $.ajax({
                url: '/clinic/medicalhistory/gethistory',
                type: 'GET',
                data: {
                    userid  : userid
                },
                success:function(data){
                    $('#resultscontainer').empty();
                    $('#resultscontainer').append(data)
                    $(".swal2-container").remove();
                    $('body').removeClass('swal2-shown')
                    $('body').removeClass('swal2-height-auto')
                }
            })
        }
        gethistory($('#select-user').val())
        $(document).ready(function(){

            $('#select-user').on('change',function(){
                var userid   = $(this).val();
                gethistory(userid)
            })
            // filter($('#input-daterange').val());

            // $('#btn-filter').on('click', function(){

            //     var selecteddaterange = $('#input-daterange').val();
            //     filter(selecteddaterange);

            // })

            // $(document).on('click','.btn-appointmentadmit', function(){
            //     var appointmentid   = $(this).attr('data-id');
            //     Swal.fire({
            //         title: 'You are going to accept/admit this appointment.',
            //         text: 'Would you like to continue?',
            //         type: 'info',
            //         showCancelButton: true,
            //         confirmButtonColor: '#3085d6',
            //         cancelButtonColor: '#d33',
            //         confirmButtonText: 'Continue'
            //     })
            //     .then((result) => {
            //         if (result.value) {
            //             $.ajax({
            //                 url:'/clinic/appointment/admitaccept',
            //                 type:'GET',
            //                 dataType: 'json',
            //                 data: {
            //                     id      :  appointmentid
            //                 },
            //                 success:function(data) {
            //                     if(data == 1)
            //                     {
            //                         Toast.fire({
            //                             type: 'success',
            //                             title: 'Admitted successfully!'
            //                         })
            //                         filter($('#input-daterange').val());
            //                     }else if(data == 2){
            //                         Toast.fire({
            //                             type: 'warning',
            //                             title: 'Appointment is admitted already!'
            //                         })
            //                         filter($('#input-daterange').val());
            //                     }else{
            //                         Toast.fire({
            //                             type: 'error',
            //                             title: 'Something went wrong!'
            //                         })
            //                     }
            //                 }
            //             })
            //         }
            //     })
            // })
            // $(document).on('click','.btn-appointmentcancel', function(){
            //     var appointmentid   = $(this).attr('data-id');
            //     Swal.fire({
            //         title: 'You are going to drop this appointment.',
            //         text: 'Would you like to continue?',
            //         type: 'info',
            //         showCancelButton: true,
            //         confirmButtonColor: '#3085d6',
            //         cancelButtonColor: '#d33',
            //         confirmButtonText: 'Continue'
            //     })
            //     .then((result) => {
            //         if (result.value) {
            //             $.ajax({
            //                 url:'/clinic/appointment/admitcancel',
            //                 type:'GET',
            //                 dataType: 'json',
            //                 data: {
            //                     id      :  appointmentid
            //                 },
            //                 success:function(data) {
            //                     if(data == 1)
            //                     {
            //                         Toast.fire({
            //                             type: 'success',
            //                             title: 'Dropped successfully!'
            //                         })
            //                         filter($('#input-daterange').val());
            //                     }else{
            //                         Toast.fire({
            //                             type: 'error',
            //                             title: 'Something went wrong!'
            //                         })
            //                     }
            //                 }
            //             })
            //         }
            //     })
            // })
        })
    </script>
@endsection
