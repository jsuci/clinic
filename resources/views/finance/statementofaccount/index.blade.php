@extends('finance.layouts.app')

@section('content')
<!-- DataTables -->
<link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css')}}">
<!-- daterange picker -->
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
<style>
    table{
        font-size: 12px;
    }
</style>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Statement of Account</h1>
                <!-- <h4 class="text-warning" style="text-shadow: 1px 1px 1px #000000">
                    <i class="fa fa-file-invoice nav-icon"></i> 
                    <b>STUDENT LEDGER</b></h4> -->
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item active">Statement of Account</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-2">
                <label>School Year</label>
                <select class="form-control form-control-sm" id="selectedschoolyear">
                    @foreach($schoolyears as $schoolyear)
                        <option value="{{$schoolyear->id}}" {{$schoolyear->isactive == 1 ? 'selected' : ''}}>{{$schoolyear->sydesc}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label>Semester</label>
                <select class="form-control form-control-sm" id="selectedsemester">
                    @foreach($semesters as $semester)
                        <option value="{{$semester->id}}" {{$semester->isactive == 1 ? 'selected' : ''}}>{{$semester->semester}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label>Grade Level</label>
                <select class="form-control form-control-sm" id="selectedgradelevel">
                    @foreach($gradelevels as $gradelevel)
                        <option value="{{$gradelevel->id}}">{{$gradelevel->levelname}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 div_section">
                <label>Section</label>
                <select class="form-control form-control-sm" id="selectedsection">
                    {{-- <option value="0" >All</option> --}}
                </select>
            </div>
            <div class="col-md-2 div_course" style="display: none">
                <label>Course</label>
                <select class="form-control form-control-sm" id="selectedcourse">
                    {{-- <option value="0" >All</option> --}}
                </select>
            </div>
            <div class="col-md-2">
                <label>Month Setup</label>
                <select class="form-control form-control-sm" id="selectedmonth">
                    @foreach($monthsetups as $monthsetup)
                        <option value="{{$monthsetup->id}}">{{$monthsetup->description}}</option>
                    @endforeach
                </select>
            </div>
            {{-- @if(strtolower(DB::table('schoolinfo')->first()->abbreviation) != 'hccsi')
                <div class="col-md-2">
                    @if(strtolower(DB::table('schoolinfo')->first()->abbreviation) != 'xai')
                        @if($status == 0)
                            <button type="button" id="viewnote" class="btn btn-sm btn-default float-right btn-block" data-toggle="tooltip" data-placement="left" title="No notes available"> <span style="height: 10px;width: 10px;background-color: red;border-radius: 50%;display: inline-block;"></span> Note (Inactive)</button>
                        @else
                            <button type="button" id="viewnote" class="btn btn-sm btn-default float-right btn-block" data-toggle="tooltip" data-placement="left" title="Active"> <span style="height: 10px;width: 10px;background-color: green;border-radius: 50%;display: inline-block;"></span> Note (Active)</button>
                        @endif
                    @endif
                </div>
            @endif --}}
        </div>
        <div class="row mt-2">
            <div class="col-md-12 text-right">
                <button type="button" class="btn btn-primary" id="btn-generate">Generate</button>
                {{-- <button type="button" class="btn btn-default" id="btn-export-all">
                    <i class="fa fa-file-pdf"></i> Export to PDF
                </button> --}}
            </div>
        </div>
    </div>
</div>
<div id="results-container"></div>
<div class="modal fade" id="viewnotemodal" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"></h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
            {{-- <div class="col-sm-12">
                <div class="alert alert-warning alert-dismissible">
                    <h5><i class="icon fas fa-exclamation-triangle"></i> Alert!</h5>
                    Currently working on this feature.
                  </div>
            </div> --}}
          <p><em>This note will be added at the bottom of the report to be followed by the signatories.</em></p>
          <div class="row" id="notecontainer"></div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="submitnotes">Save changes</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
@endsection
@section('footerscripts')
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
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
        $('[data-toggle="tooltip"]').tooltip()
        $('#selecteddaterange').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear',
                format: 'YYYY-MM-DD'
            }
        })
        
        $('#btn-generate').on('click', function(){
            Swal.fire({
                title: 'Fetching data...',
                onBeforeOpen: () => {
                    Swal.showLoading()
                },
                allowOutsideClick: false
            })          
            
            $.ajax({
                url: '{{ route('statementofacctgenerate')}}',
                type: 'GET',
                data: {
                    selectedschoolyear   : $('#selectedschoolyear').val(),
                    selectedsemester     : $('#selectedsemester').val(),
                    selectedgradelevel   : $('#selectedgradelevel').val(),
                    selectedmonth        : $('#selectedmonth').val(),
                    selectedcourse       : $('#selectedcourse').val(),
                    selectedsection      : $('#selectedsection').val()
                },
                success:function(data){
                    $('#export-tools').show();
                    $('#results-container').empty();
                    $('#results-container').append(data)
                    $('.paginate_button').addClass('btn btn-sm btn-default')
                    $(".swal2-container").remove();
                    $('body').removeClass('swal2-shown')
                    $('body').removeClass('swal2-height-auto')
                    var table = $("#example1").DataTable({
                        // retreive: true,
                        pageLength : 10,
                        lengthMenu: [[5, 10, 20, -1], [5, 10, 20, 'Show All']],
                        "ordering": false
                        // "bPaginate": false,
                        // "bInfo" : false,
                        // "bFilter" : false,
                        // "order": [[ 1, 'asc' ]]
                    });
                }
            })
        })
        $(document).on('click','.viewdetails', function(){
            if($(this).closest('.card').hasClass('collapsed-card'))
            {
                var selectedschoolyear = $('#selectedschoolyear').val();
                var selectedsemester = $('#selectedsemester').val();
                var selectedmonth = $('#selectedmonth').val();
                var studid = $(this).attr('id');
                $.ajax({
                    url: '{{ route('statementofacctgetaccount')}}',
                    type: 'GET',
                    data: {
                        studid: studid,
                        selectedschoolyear: selectedschoolyear,
                        selectedsemester: selectedsemester,
                        selectedmonth: selectedmonth
                    },
                    success:function(data){
                        $('#stud'+studid).empty();
                        $('#stud'+studid).append(data);
                    }
                })
            }else{
                $('#stud'+$(this).attr('id')).empty()
            }
        })
        $(document).on('click','.printstatementofacct', function(){
                var selectedschoolyear = $('#selectedschoolyear').val();
                var selectedsemester = $('#selectedsemester').val();
                var selectedmonth = $('#selectedmonth').val();
                var studid = $(this).attr('studid');
                var exporttype = $(this).attr('exporttype')
                var paramet = {
                    selectedschoolyear  : selectedschoolyear,
                    selectedsemester    : selectedsemester, 
                    selectedmonth       : selectedmonth, 
                    studid              : studid
                }
				window.open("/statementofacctexport?exporttype="+exporttype+"&"+$.param(paramet));
        })
        $(document).on('click','#viewnote', function(){
            $.ajax({
                url: '/statementofacctgetnote',
                type: 'GET',
                data: {
                    type: 1
                },
                success:function(data)
                {
                    $('#notecontainer').empty();
                    $('#notecontainer').append(data)
                }
            })
            $('#viewnotemodal').addClass('show')
            $('#viewnotemodal').css({'padding-right':'10px','display':'block'})
            $('#viewnotemodal').modal('show');
            $('body').addClass('modal-open')
            $('.modal-backdrop').addClass('show')
            $('.modal-backdrop').show()
        })
        $(document).on('click','#submitnotes', function()
        {
            
            var submit = 4;
            var notes = [];
            $('textarea').each(function(){
                if($(this).val().replace(/^\s+|\s+$/g, "").length == 0)
                {
                    submit -= 1;
                }else{
                    notes.push(
                        {
                            'id':$(this).attr('id'),
                            'content':$(this).val()
                        }
                    )
                }
            })
            if(submit>0)
            {
                if($('input[name="notestatus"]').length == 0)
                {
                    var notestatus = 1;
                }else{
                    var notestatus = $('input[name="notestatus"]:checked').val();
                }
                $.ajax({
                    url: '/statementofacctsubmitnotes',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        notes: JSON.stringify(notes),
                        notestatus : notestatus
                    },
                    success:function(data)
                    {
                        if(data == '1')
                        {
                            
                            $('#viewnotemodal').removeClass('show')
                            $('#viewnotemodal').removeAttr('style')
                            $('#viewnotemodal').css('display','none;')
                            $('body').removeClass('modal-open')
                            $('.modal-backdrop').removeClass('show')
                            $('.modal-backdrop').hide()
                        }
                    }
                })
            }

        })
        $(document).on('click','.btn-export-all', function(){
            var selectedschoolyear = $('#selectedschoolyear').val();
            var selectedsemester = $('#selectedsemester').val();
            var selectedgradelevel   = $('#selectedgradelevel').val();
            var selectedmonth = $('#selectedmonth').val();
            var selectedcourse = $('#selectedcourse').val();
            var selectedsection = $('#selectedsection').val();

            var paramet = {
                selectedschoolyear  : selectedschoolyear,
                selectedsemester    : selectedsemester, 
                exporttype    : $(this).attr('exporttype'), 
                selectedgradelevel   : $('#selectedgradelevel').val(),
                selectedmonth       : selectedmonth,
                selectedcourse : selectedcourse,
                selectedsection : selectedsection
            }
            window.open("/statementofacctexportall?"+$.param(paramet));
        });

        $(document).on('change', '#selectedgradelevel', function(){
            loadsection();
        });

        loadsection();

        function loadsection()
        {
            var levelid = $('#selectedgradelevel').val();
            var syid = $('#selectedschoolyear').val();
            var semid = $('#selectedsemester').val();

            $.ajax({
                type: "GET",
                url: "{{route('statementofacctloadsection')}}",
                data: {
                    levelid:levelid,
                    syid:syid,
                    semid:semid
                },
                // dataType: "dataType",
                success: function (data) {
                    if(levelid == 14 || levelid == 15)
                    {
                        $('.div_section').show();
                        $('.div_course').hide();
                        $('#selectedsection').html(data);
                    }
                    else if(levelid >= 17 && levelid <= 20)
                    {
                        $('.div_section').hide();
                        $('.div_course').show();
                        $('#selectedcourse').html(data);
                    }
                    else
                    {
                        $('.div_section').show();
                        $('.div_course').hide();
                        $('#selectedsection').html(data);
                    }
                }
            });
        }
    })

</script>
@endsection