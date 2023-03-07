@extends('teacher.layouts.app')

@section('headerjavascript')

      <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
      <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
      <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
      <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
      <style>
            .select2-container--default .select2-selection--single .select2-selection__rendered {
                margin-top: -9px;
            }
      </style>
@endsection


@section('content')
@php
    $subj_strand = DB::table('sh_sectionblockassignment')
                ->join('sh_block',function($join){
                    $join->on('sh_sectionblockassignment.blockid','=','sh_block.id');
                    $join->where('sh_block.deleted',0);
                })
                ->join('sh_strand',function($join){
                    $join->on('sh_block.strandid','=','sh_strand.id');
                    $join->where('sh_strand.deleted',0);
                })
                ->where('sh_sectionblockassignment.deleted',0)
                ->select(
                    'sectionid',
                    'strandid',
                    'strandcode'
                )->get();
@endphp
<div class="modal fade" id="proccess_count_modal" style="display: none;" aria-hidden="true" data-backdrop="static" data-keyboard="false">
      <div class="modal-dialog modal-sm">
          <div class="modal-content">
              <div class="modal-header bg-success">
                  <h4 class="modal-title">Proccessing ...</h4>
              </div>
              <div class="modal-body">
                  <div class="row">
                  <div class="col-md-6"><label>Process : </label></div>
                  <div class="col-md-6"><span id="proccess_count"></span></div>
                  </div>
              </div>
              <div class="modal-footer justify-content-between">
                  <button type="button" class="btn btn-primary" data-dismiss="modal" id="proccess_done" hidden>Done</button>
              </div>
          </div>
      </div>
</div>
<div class="modal fade" id="grade_info" style="display: none;" aria-hidden="true" data-backdrop="static" data-keyboard="false">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header bg-success">
                  <h4 class="modal-title">Below 85 grades</h4>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">×</span>
                  </button>   
              </div>
              <div class="modal-body">
                  <div class="row">
                      <div class="col-md-12">
                          <table class="table">
                              <thead>
                                  <tr>
                                      <td>Subject</td>
                                      <td class="text-center">Grade</td>
                                  </tr>
                              </thead>
                              <tbody id="below_holder">

                              </tbody>
                          </table>
                      </div>
                  </div>
              </div>
              <div class="modal-footer justify-content-between">
                  <button type="button" class="btn btn-primary" data-dismiss="modal" id="proccess_done" hidden>Done</button>
              </div>
          </div>
      </div>
</div>

<section class="content">
      <div class="container-fluid">
          <div class="row ">
                <div class="col-md-12">
                      <div class="card shadow">
                            <div class="card-header p-1 bg-primary">

                            </div>
                            <div class="card-body" >
                              <div class="row">
                                  <div class="col-md-2">
                                      <div class="form-group">
                                          <label for="">Grade Level</label>
                                          @php
                                              $sections = array();
                                          @endphp
                                          <select class="form-control select2" id="gradelevel">
                                              <option selected value="" >Select Grade Level</option>
                                              <!--@foreach ($gradelevel as $item)-->
                                              <!--      <option value="{{$item->levelid}}">{{$item->levelname}}</option>-->
                                              <!--@endforeach-->
                                          </select>
                                      </div>
  
                                  </div>
                                  <div class="col-md-2">
                                      <div class="form-group">
                                          <label for="">Section</label>
                                          <select name="section" id="section" class="form-control select2">
                                              <option selected value="" >Select Section</option>
                                          </select>
                                      </div>
                                  </div>
                                  <div class="col-md-2">
                                    <div class="form-group" id="strand_holder">
                                        <label for="">Strand</label>
                                        <select name="strand" id="strand" class="form-control select2">
                                            
                                        </select>
                                    </div>
                                </div>
                                  <div class="col-md-2"></div>
                                  <div class="col-md-2">
                                        <label for="">SCHOOL YEAR</label>
                                        <select name="syid" id="syid" class="form-control select2">
                                            @foreach(DB::table('sy')->select('id','sydesc','isactive')->get() as $item)
                                                @if($item->isactive == 1)
                                                    <option value="{{$item->id}}" selected="selected">{{$item->sydesc}}</option>
                                                @else
                                                    <option value="{{$item->id}}">{{$item->sydesc}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="">SEMESTER</label>
                                        <select name="semester" id="semester" class="form-control select2">
                                            @foreach(DB::table('semester')->select('id','semester','isactive')->get() as $item)
                                                @if($item->isactive == 1)
                                                    <option value="{{$item->id}}" selected="selected">{{$item->semester}}</option>
                                                @else
                                                    <option value="{{$item->id}}">{{$item->semester}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                     <hr>
                                  {{-- <div class="col-md-3">
                                      <div class="form-group">
                                          <label for="">Quarter</label>
                                          <select name="quarter" id="quarter" class="form-control select2">
                                              <option value="">SELECT QUARTER</option>
                                              <option value="1">Quarter 1</option>
                                              <option value="2">Quarter 2</option>
                                              <option value="3">Quarter 3</option>
                                              <option value="4">Quarter 4</option>
                                          </select>
                                      </div>
                                  </div> --}}
                              </div>
                              <hr>
                              <div class="row">
                                <div class="col-md-2">
                                    <button class="btn btn-primary btn-block btn-sm" id="filter"> <i class="fas fa-filter"></i> FILTER</button>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-primary btn-block  btn-sm" id="reload"> <i class="fas fa-sync"></i> RELOAD</button>
                                </div>
                                <div class="col-md-5"></div>
                                <div class="col-md-3">
                                    <div class="form-group mb-0">
                                        {{-- <label for="">Quarter</label> --}}
                                        <select name="quarter" id="quarter" class="form-control select2">
                                            <option value="">SELECT QUARTER</option>
                                            <option value="1">Quarter 1</option>
                                            <option value="2">Quarter 2</option>
                                            <option value="3">Quarter 3</option>
                                            <option value="4">Quarter 4</option>
                                            <option value="5">FINAL</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                              <hr>
                              <h3 class="text-center">STUDENT RANKING LIST</h3>
                              <div class="row mt-3">
                                <div class="col-md-12">
                                    <table class="table table-bordered table-sm" id="student_list">
                                        <thead>
                                            <tr>
                                                <th width="25%">Student Name</th>
                                                <th width="15%" class="text-center strand_holder" hidden>Strand</th>
                                                <th width="15%" class="text-center">Gen. Ave</th>
                                                <th width="15%" class="text-center">Composite</th>
                                                <th width="20%" class="text-center">Award</th>
                                                <th width="10%" class="text-center">Lowest</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="5" class="text-center">PLEASE SELECT FILTER</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <button class="btn btn-default float-right" id="print_student_ranking">
                                        <i class="fas fa-print" ></i> PRINT STUDENT RANKING
                                    </button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th width="80%">Subject</th>
                                                <th width="20%"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="subject_list">
                                            
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




@endsection


@section('footerjavascript')

      <script src="{{asset('plugins/select2/js/select2.full.min.js') }}"></script>
      <script src="{{asset('plugins/datatables/jquery.dataTables.js') }}"></script>
      <script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>

      @include('principalsportal.pages.awards.awardsjs')

      <script>
        $(document).ready(function(){
            
            var gradelevel = @json($gradelevel)
            
           
            load_gradelevel()
            
             $(document).on('change','#syid',function(){
                load_gradelevel()
             })
             
            function load_gradelevel(){
                var temp_syid = $('#syid').val()
                var temp_gradelevel = gradelevel.filter(x=>x.syid == temp_syid)
                $("#gradelevel").empty()
                $("#section").empty()
                
                $("#student_list").DataTable({
                              destroy: true,
                              data:[]
                    
                })
                $("#subject_list").empty();
                
                $("#gradelevel").append('<option value="">Select a Grade Level</option>')
                $("#gradelevel").select2({
                      data: temp_gradelevel,
                      allowClear: true,
                      placeholder: "Select a Grade Level",
                })
                
                
            }
            
                
            $('#print_student_ranking').click(function(){
                var gradelevel = $('#gradelevel').val();
                var section = $('#section').val();
                var quarter  = $('#quarter').val(); 
                var syid  = $('#syid').val(); 
                var semid  = $('#semester').val(); 

                var valid_filter = true

                if(section == ''){
                    Swal.fire({
                        type: 'info',
                        text: 'Please select a section!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    valid_filter = false
                    return false;
                }
                if(gradelevel == ''){
                    Swal.fire({
                        type: 'info',
                        text: 'Please select a gradelevel!',
                        timer: 1500
                    });
                    valid_filter = false
                    return false;
                }

                var excluded = []

                $('.subj_list').each(function(a,b){
                    if($(b).prop('checked') == false){
                        excluded.push($(b).attr('data-id'));
                    }
                })

                if(section == null){
                    Swal.fire({
                            type: 'info',
                            title: 'Something went wrong!',
                            text: 'Please reload the page',
                            timer: 1500
                    });
                }
                else{
                    window.open("/grades/report/studentawards?gradelevel="+gradelevel+"&section="+section+"&quarter="+quarter+"&sy="+syid+"&strand="+$("#strand").val()+"&semid="+semid+'&exclude='+excluded);
                }
            })

        })
    </script>

@endsection