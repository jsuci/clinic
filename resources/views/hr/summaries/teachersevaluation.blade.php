
@extends('hr.layouts.app')

@section('pagespecificscripts')

      <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
      <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
      <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
      <link rel="stylesheet" href="{{asset('plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css')}}">

@endsection
<style>
      .pagination{
            float: right;
      }
      #teacher_table tbody td:last-child{
            text-align: center;
      }
      #teacher_table_filter{
            float: right;
      }
      .shadow {
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
            border: 0 !important;
      }
</style>

@php
      $sy = DB::table('sy')
                  ->orderBy('sydesc')
                  ->select(
                        'id',
                        'sydesc',
                        'sydesc as text',
                        'isactive',
                        'ended'
                  )
                  ->get(); 

@endphp

{{-- @section('modalSection') --}}

<div class="modal fade" id="teacher_evaluation_modal" style="display: none;" aria-hidden="true" data-backdrop="static" data-keyboard="false">
      <div class="modal-dialog modal-xl">
          <div class="modal-content">
                  <div class="modal-header pb-2 pt-2 border-0">
                        <h4 class="modal-title" style="font-size: 1.1rem !important">Teacher Evaluation (<span id="teacher_name"></span>)</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                  </div>
              <div class="modal-body" id="subject_assignment_table">
                  
              </div>
              <div class="modal-footer">
                    <button type="button" class="btn btn-primary exporteval btn-sm" data-et="excel"><i class="fa fa-file-excel"></i> EXCEL</button>
              </div>
          </div>
      </div>
</div>

<div class="modal fade" id="teacher_evaluation_setup_modal" style="display: none;" aria-hidden="true" data-backdrop="static" data-keyboard="false">
      <div class="modal-dialog modal-sm">
          <div class="modal-content">
                  <div class="modal-header pb-2 pt-2 border-0">
                        <h4 class="modal-title" style="font-size: 1.1rem !important">Teacher Evaluation Setup</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                  </div>
              <div class="modal-body">
                  <table class="table table-sm table-striped" width="100%">
                        <thead>
                              <tr>
                                    <th width="70%">Period</th>
                                    <th width="30%">Status</th>
                              </tr>
                        </thead>
                        <tbody  id="evlstp_table">
                         
                        </tbody>
                  </table>
              </div>
          </div>
      </div>
</div>

<div class="modal fade" id="section_evaluation_monitoring" style="display: none;" aria-hidden="true" data-backdrop="static" data-keyboard="false">
      <div class="modal-dialog modal-xl">
          <div class="modal-content">
                  <div class="modal-header pb-2 pt-2 border-0">
                        <h4 class="modal-title" style="font-size: 1.1rem !important">Teacher Evaluation Monitoring</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                  </div>
              <div class="modal-body" >
                  <div class="row">
                        <div class="col-md-12">
                              <table class="table table-sm table-striped" id="section_table" width="100%">
                                    <thead>
                                          <tr>
                                                <th width="12%">Grade Level</th>
                                                <th width="28%">Section</th>
                                                <th width="12%" class="text-center">Stud. Count</th>
                                                <th width="12%" class="text-center">Subj. Count</th>
                                                <th width="12%" class="text-center">Eval. Targer</th>
                                                <th width="12%" class="text-center">Eval. count</th>
                                                <th width="12%" class="text-center">Percentage</th>
                                          </tr>
                                    </thead>
                                    <tbody>
                                     
                                    </tbody>
                              </table>
                        </div>
                  </div>
              </div>
          </div>
      </div>
</div>


@section('content')


<section class="content-header">
      <div class="container-fluid">
            <div class="row">
                  <div class="col-sm-6">
                  
                  </div>
                  <div class="col-sm-6">
                  <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/home">Home</a></li>
                        <li class="breadcrumb-item active">Teacher Evaluation</li>
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
                                    <div class="col-md-4">
                                         <h5><i class="fa fa-filter"></i> Filter</h5> 
                                    </div>
                                    <div class="col-md-8">
                                          <h5 class="float-right">Active S.Y.: {{collect($sy)->where('isactive',1)->first()->sydesc}}</h5>
                                    </div>
                              </div>
                              <div class="row">
                                    <div class="col-md-2  form-group mb-0">
                                          <label for="">School Year</label>
                                          <select class="form-control select2 form-control-sm" id="filter_sy">
                                                @foreach ($sy as $item)
                                                      @if($item->isactive == 1)
                                                            <option value="{{$item->id}}" selected="selected">{{$item->sydesc}}</option>
                                                      @else
                                                            <option value="{{$item->id}}">{{$item->sydesc}}</option>
                                                      @endif
                                                @endforeach
                                          </select>
                                    </div>
                                    <div class="col-md-2  form-group mb-0">
                                          <label for="">Period</label>
                                          <select class="form-control select2 form-control-sm" id="year_filter">
                                                <option value="1" selected="selected">Mid Year</option>
                                                <option value="2" >Year End</option>
                                          </select>
                                    </div>
                              </div>
                          </div>
                        </div>
                  </div>
            </div>
            <div class="row">
                  <div class="col-12">
                        <div class="card shadow">
                              {{-- <div class="card-header bg-primary">
                                    <h5 class="card-title">TEACHERS EVALUATION</h5>
                              </div> --}}
                              <div class="card-body">
                                    <div class="row">
                                          <div class="col-md-12">
                                                <table class="table table-sm table-striped" id="teacher_table">
                                                      <thead>
                                                            <tr>
                                                                  <th width="80%">Teacher</th>
                                                                  <th width="20%"></th>
                                                            </tr>
                                                      </thead>
                                                      <tbody>
                                                       
                                                      </tbody>
                                                </table>
                                          </div>
                                    </div>
                                  
                              </div>
                        </div>   
                  </div>
            </div>
        </div>
</section>

<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
<script src="{{asset('plugins/sweetalert2/sweetalert2.all.min.js')}}"></script>

@include('superadmin.pages.gradingsystem.teacherEvaluation.teachereval_script')
<script>
      const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                  })
</script>


<script>
      $(document).on('click','#evaluation_setup',function(){
            $('#teacher_evaluation_setup_modal').modal()
            getEvlStp()
      })

      $(document).on('click','.statusbtn',function(){

            var evlStup = $(this).attr('data-id')
            var evlStupStatus = $(this).attr('data-status')

            // if(evlStup == 1){
            //       message = 'Are you sure you want to '
            // }else{

            // }

            Swal.fire({
                  text: 'Are you sure you want to update Status?',
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Update'
            }).then((result) => {
                  if (result.value) {
                        $.ajax({
                              type:'GET',
                              url: '/teacherevaluation/evalsetup/update',
                              data:{
                                    evlStup:evlStup,
                                    evlStupStatus:evlStupStatus
                              },
                              success:function(data) {
                                    if(data[0].status == 1){
                                          Toast.fire({
                                                type: 'success',
                                                title: 'Status Updated'
                                          })
                                          getEvlStp()
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

      function getEvlStp(){
            
            $.ajax({
                  type:'GET',
                  url: '/teacherevaluation/evalsetup',
                  success:function(data) {
                        $('#evlstp_table').empty()
                        $.each(data,function(a,b){
                              if(b.status == 1){
                                    var statusbtn = '<button class="btn-block btn btn-sm btn-success text-sm statusbtn" data-status="0" data-id="'+b.id+'" style="font-size:.7rem !important">Active</button>'
                              }else{
                                    var statusbtn = '<button class="btn-block btn btn-sm btn-danger text-sm statusbtn" data-status="1" data-id="'+b.id+'"  style="font-size:.7rem !important">Inactive</button>'
                              }
                              $('#evlstp_table').append('<tr><td class="align-middle">'+b.period+'</td><td>'+statusbtn+'</td></tr>')
                        })
                  
                  }
            })
      }

</script>

<script>

      $(document).on('click','#evaluation_monitoring',function(){
            $('#section_evaluation_monitoring').modal()
            monitoring_table([])
            get_monitoring()
      })

      function get_monitoring(){
            $.ajax({
                  type:'GET',
                  url: '/hrreports/evaluation/monitoring',
                  data:{
                        'semid' : $('#year_filter').val(),
                        'syid': $('#filter_sy').val()
                  },
                  success:function(data) {
                        monitoring_table(data)
                  
                  }
            })
      }

      function monitoring_table(sections){
            $("#section_table").DataTable({
                  destroy: true,
                  data:sections,
                  columns: [
                              { "data": "sortid" },
                              { "data": "sectionname" },
                              { "data": null },
                              { "data": null },
                              { "data": null },
                              { "data": null },
                              { "data": null }
                        ],
                  columnDefs: [
                              {
                                    'targets': 0,
                                    'orderable': true, 
                                    'createdCell':  function (td, cellData, rowData, row, col) {

                                          $(td)[0].innerHTML =  rowData.levelname

                                    }
                              },
                              {
                                    'targets': 2,
                                    'orderable': false, 
                                    'createdCell':  function (td, cellData, rowData, row, col) {

                                          if(!rowData.multiple_strand){
                                                $(td)[0].innerHTML =  rowData.enrolledcount
                                                $(td).addClass('text-center')
                                                $(td).addClass('align-middle')
                                          }else{
                                                $(td)[0].innerHTML = null
                                          }
                                          

                                    }
                               },
                               {
                                    'targets': 3,
                                    'orderable': false, 
                                    'createdCell':  function (td, cellData, rowData, row, col) {
                                          if(!rowData.multiple_strand){
                                                $(td)[0].innerHTML =  rowData.subjcount
                                                $(td).addClass('text-center')
                                                $(td).addClass('align-middle')
                                          }else{
                                                $(td)[0].innerHTML = null
                                          }
                                    }
                               },
                               
                               {
                                    'targets': 4,
                                    'orderable': false, 
                                    'createdCell':  function (td, cellData, rowData, row, col) {
                                          if(!rowData.multiple_strand){
                                                $(td)[0].innerHTML =  rowData.enrolledcount * rowData.subjcount
                                                $(td).addClass('text-center')
                                                $(td).addClass('align-middle')
                                          }else{
                                                $(td)[0].innerHTML = null
                                          }

                                    }
                               },
                               {
                                    'targets': 5,
                                    'orderable': false, 
                                    'createdCell':  function (td, cellData, rowData, row, col) {
                                          if(!rowData.multiple_strand){
                                                $(td)[0].innerHTML =  rowData.evaluationcount
                                                $(td).addClass('text-center')
                                                $(td).addClass('align-middle')
                                          }else{
                                                $(td)[0].innerHTML = null
                                          }
                                    }
                               },
                               {
                                    'targets': 6,
                                    'orderable': false, 
                                    'createdCell':  function (td, cellData, rowData, row, col) {
                                          if(!rowData.multiple_strand){
                                                var temp_over = rowData.enrolledcount * rowData.subjcount

                                                var percentage = ( rowData.evaluationcount / temp_over ) * 100

                                                // var progress_bar = `<div class="progress">
                                                //                         <div class="progress-bar bg-success" role="progressbar" aria-valuenow="40" aria-valuemin="0"
                                                //                         aria-valuemax="100" style="width: `+percentage.toFixed(2)+`%">
                                                //                         </div>
                                                //                   </div>
                                                //                   <p class="mb-0">Evaluation Percentage : `+percentage.toFixed(2)+`%</p>`

                                                // var temp_over = rowData.enrolledcount * rowData.subjcount
                                                // $(td)[0].innerHTML = temp_over + ' / ' + rowData.evaluationcount

                                                if(temp_over == 0){
                                                      $(td)[0].innerHTML = '00.00%'
                                                }else{
                                                      $(td)[0].innerHTML = ('0'+percentage.toFixed(2) + '%').slice(-6)
                                                }

                                                $(td).addClass('text-center')
                                                $(td).addClass('align-middle')
                                          }else{
                                                $(td)[0].innerHTML = null
                                          }
                                    }
                               }
                        ]
            })

            var label_text = $($('#section_table_wrapper')[0].children[0])[0].children[0]
            $(label_text)[0].innerHTML = ''
      }

</script>

<script>
      $(document).on('click','.vieweval', function(){
            var teacherid          = $(this).attr('data-id');

            var teacherinfo = teachers.filter(x=>x.id == teacherid)

            $('#teacher_name').text(teacherinfo[0].tid+' - '+teacherinfo[0].teacher)

            // $('#commentscontainer').empty()
            // $.ajax({
            //       url: '/hrreports/viewcomments',
            //       type: 'GET',
            //       data: {
            //             teacherid : teacherid,
            //             yearfilter : $('#year_filter').val()
            //       }, success:function(data)
            //       {
            //             $('#commentscontainer').empty()
            //             if(data.length>0)
            //             {
            //                 var count = 0
            //                   $.each(data, function(key, value){
            //                       if(value != null){
            //                           count += 1;
            //                           $('#commentscontainer').append(
            //                                   '<div class="col-md-12">'+
            //                                         '<p>'+count+'. '+value+'</p>'+
            //                                   '</div>'
            //                             )
            //                       }
            //                   })
            //             }
            //       }
            // })

      })
      
      $('.exporteval').on('click', function(){
            var exporttype         = $(this).attr('data-et');
            var teacherid          = $(this).attr('data-id');
            var ddvalues           = [];

            $('.dd').each(function(){
                  ddvalues.push($(this).attr('data-id'));
                  // console.log(ddvalues)
            })
            var paramet = {
                  exporttype  : exporttype,
                  ddvalues    : ddvalues,
                  teacherid   : teacherid,
                  syid : $('#filter_sy').val(),
                  yearfilter : $('#year_filter').val()
            }
                  // console.log($.param(paramet))
            window.open("/hrreports/viewevaluation?"+$.param(paramet));
        })
</script>
@endsection


