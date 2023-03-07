@if(count($students) == 0)
<div class="col-md-12">
    <div class="alert alert-danger" role="alert">
        No students enrolled!
    </div>
</div>
@else
<style>
    table td, table th{
        padding: 1px !important;
    }
</style>
<div class="row">
    <div class="col-md-6">
        <div class="card" style="">
            <div class="card-header p-0">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered" style="font-size: 10px;">
                            <thead>
                                <tr>
                                    <th colspan="4">SUMMARY TABLE</th>
                                </tr>
                                <tr>
                                    <th>STATUS</th>
                                    <th>MALE</th>
                                    <th>FEMALE</th>
                                    <th>TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th>PROMOTED</th>
                                    <td class="text-center">{{collect($students)->where('gender','male')->where('actiontaken','1')->count()}}</td>
                                    <td class="text-center">{{collect($students)->where('gender','female')->where('actiontaken','1')->count()}}</td>
                                    <td class="text-center">{{collect($students)->where('actiontaken','1')->count()}}</td>
                                </tr>
                                <tr>
                                    <th>*Conditional
                                    </th>
                                    <td class="text-center">{{collect($students)->where('gender','male')->where('actiontaken','2')->count()}}</td>
                                    <td class="text-center">{{collect($students)->where('gender','female')->where('actiontaken','2')->count()}}</td>
                                    <td class="text-center">{{collect($students)->where('actiontaken','2')->count()}}</td>
                                </tr>
                                <tr>
                                    <th>RETAINED</th>
                                    <td class="text-center">{{collect($students)->where('gender','male')->where('actiontaken','3')->count()}}</td>
                                    <td class="text-center">{{collect($students)->where('gender','female')->where('actiontaken','3')->count()}}</td>
                                    <td class="text-center">{{collect($students)->where('actiontaken','3')->count()}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header p-0">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered" style="font-size: 10px;">
                            <thead>
                                <tr>
                                    <th colspan="4">LEARNING PROGRESS AND ACHIEVEMENT (Based on Learners' General Average)
                                    </th>
                                </tr>
                                <tr>
                                    <th>Descriptors & Grading
                                        Scale</th>
                                    <th>MALE</th>
                                    <th>FEMALE</th>
                                    <th>TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th>Did Not Meet
                                        Expectations
                                        ( 74 and below)</th>
                                    <td class="text-center">{{collect($students)->where('gender','male')->where('generalaverage','<=',74)->where('generalaverage','!=',0)->count()}}</td>
                                    <td class="text-center">{{collect($students)->where('gender','female')->where('generalaverage','<=',74)->where('generalaverage','!=',0)->count()}}</td>
                                    <td class="text-center">{{collect($students)->where('generalaverage','<=',74)->where('generalaverage','!=',0)->count()}}</td>
                                </tr>
                                <tr>
                                    <th>Fairly Satisfactory
                                        ( 75-79)
                                    </th>
                                    <td class="text-center">{{collect($students)->where('gender','male')->where('generalaverage','>=',75)->where('generalaverage','<=',79)->count()}}</td>
                                    <td class="text-center">{{collect($students)->where('gender','female')->where('generalaverage','>=',75)->where('generalaverage','<=',79)->count()}}</td>
                                    <td class="text-center">{{collect($students)->where('generalaverage','>=',75)->where('generalaverage','<=',79)->count()}}</td>
                                </tr>
                                <tr>
                                    <th>Satisfactory
                                        ( 80-84 )</th>
                                    <td class="text-center">{{collect($students)->where('gender','male')->where('generalaverage','>=',80)->where('generalaverage','<=',84)->count()}}</td>
                                    <td class="text-center">{{collect($students)->where('gender','female')->where('generalaverage','>=',80)->where('generalaverage','<=',84)->count()}}</td>
                                    <td class="text-center">{{collect($students)->where('generalaverage','>=',80)->where('generalaverage','<=',84)->count()}}</td>
                                </tr>
                                <tr>
                                    <th>Very Satisfactory
                                        ( 85 -89)</th>
                                    <td class="text-center">{{collect($students)->where('gender','male')->where('generalaverage','>=',85)->where('generalaverage','<=',89)->count()}}</td>
                                    <td class="text-center">{{collect($students)->where('gender','female')->where('generalaverage','>=',85)->where('generalaverage','<=',89)->count()}}</td>
                                    <td class="text-center">{{collect($students)->where('generalaverage','>=',85)->where('generalaverage','<=',89)->count()}}</td>
                                </tr>
                                <tr>
                                    <th>Outstanding
                                        ( 90 -100)</th>
                                    <td class="text-center">{{collect($students)->where('gender','male')->where('generalaverage','>=',90)->count()}}</td>
                                    <td class="text-center">{{collect($students)->where('gender','female')->where('generalaverage','>=',90)->count()}}</td>
                                    <td class="text-center">{{collect($students)->where('generalaverage','>=',90)->count()}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-12 text-right mb-2">
                <a href="/forms/form5?sectionid={{$gradeAndLevel[0]->sectionid}}&levelid={{$gradeAndLevel[0]->levelid}}&syid={{$sy->id}}&semid={{$sem->id ?? 0}}&acadprogid={{$acadprogid}}&currentmonth={{\Carbon\Carbon::now()->month}}&action=export&exporttype=pdf" class="btn btn-sm btn-default" target="_blank">Export as PDF</a>
                {{-- <form action="/forms/form5?sectionid={{$gradeAndLevel[0]->sectionid}}&levelid={{$gradeAndLevel[0]->levelid}}&syid={{$sy->id}}&currentmonth={{\Carbon\Carbon::now()->month}}" method="get" class="small-box-footer" target="_blank">
                    <input type="hidden" name="action" value="export"/>
                    @csrf
                    <input type="hidden" name="sectionid" value="{{$gradeAndLevel[0]->sectionid}}"/>
                    <input type="hidden" name="levelid" value="{{$gradeAndLevel[0]->levelid}}"/>
                    <input type="hidden" name="syid" value="{{$sy->id}}"/>
                    <input type="hidden" name="exporttype"/>
                    <input type="hidden" name="currentmonth" value="{{\Carbon\Carbon::now()->month}}"/>
                    <button type="button" class="btn btn-sm btn-block dropdown-toggle dropdown-icon" data-toggle="dropdown" aria-expanded="false">Export As <span class="sr-only">Toggle Dropdown</span>
                        <div class="dropdown-menu" role="menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(-1px, 37px, 0px); top: 0px; left: 0px; will-change: transform;">
                        <a class="dropdown-item" href="#" data-id="exportpdf">PDF</a>
                        <a class="dropdown-item" href="#" data-id="exportexcel">EXCEL</a>
                        </div>
                    </button>
                </form> --}}
            </div>
            <div class="col-md-12 table-responsive p-0" style="height: 500px;">
                <table class="table table-bordered table-bordered table-head-fixed" style="font-size: 11px;">
                    <thead>
                        <tr class="text-center">
                            <th style="width: 15%;">LRN</th>
                            <th style="width: 35%;">LRN LEARNER'S NAME</th>
                            <th style="width: 10%;">GENERAL AVERAGE</th>
                            <th>ACTION TAKEN<br/>Promoted, Conditional, or Retained</th>
                            <th style="width: 25%;">Did Not Meet Expectations of the ff.
                                Learning Area/s as of end of
                                current School Year</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th colspan="5">MALE</th>
                        </tr>
                        @foreach($students as $student)
                            @if(strtolower($student->gender) == 'male')
                            <tr id="{{$student->id}}" class="eachstudent">
                                <td style="vertical-align: middle;">{{$student->lrn}}</td>
                                <td style="vertical-align: middle;">{{$student->lastname}}, {{$student->firstname}} {{$student->middlename}}</td>
                                <td style="vertical-align: middle;" class="text-center">{{$student->generalaverage > 0 ? $student->generalaverage : null}}</td>
                                <td>
                                    <div class="form-group clearfix m-0">
                                        <div class="icheck-primary d-inline">
                                          <input type="radio" id="radiopromoted{{$student->id}}" name="actiontaken{{$student->id}}" value="1" @if($student->actiontaken == 1) checked @endif>
                                          <label for="radiopromoted{{$student->id}}">Promoted
                                          </label>
                                        </div>
                                        <div class="icheck-primary d-inline">
                                          <input type="radio" id="radioconditional{{$student->id}}" name="actiontaken{{$student->id}}" value="2" @if($student->actiontaken == 2) checked @endif>
                                          <label for="radioconditional{{$student->id}}">
                                              Conditional
                                          </label>
                                        </div>
                                        <div class="icheck-primary d-inline">
                                          <input type="radio" id="radioretained{{$student->id}}" name="actiontaken{{$student->id}}" value="3" @if($student->actiontaken == 3) checked @endif>
                                          <label for="radioretained{{$student->id}}">
                                              Retained
                                          </label>
                                        </div>
                                      </div>
                                </td>
                                {{-- <td><input type="text" style="font-size: 11px;" class="form-control form-control-sm input-actiontaken" value="{{$student->actiontaken}}" data-id="{{$student->id}}"/></td> --}}
                                <td style="vertical-align: middle;"></td>
                            </tr>
                            @endif
                        @endforeach
                        <tr>
                            <th colspan="5">FEMALE</th>
                        </tr>
                        @foreach($students as $student)
                            @if(strtolower($student->gender) == 'female')
                            <tr id="{{$student->id}}" class="eachstudent">
                                <td style="vertical-align: middle;">{{$student->lrn}}</td>
                                <td style="vertical-align: middle;">{{$student->lastname}}, {{$student->firstname}} {{$student->middlename}}</td>
                                <td style="vertical-align: middle;" class="text-center">{{$student->generalaverage > 0 ? $student->generalaverage : null}}</td>
                                <td>
                                    <div class="form-group clearfix m-0">
                                        <div class="icheck-primary d-inline">
                                          <input type="radio" id="radiopromoted{{$student->id}}" name="actiontaken{{$student->id}}" value="1" @if($student->actiontaken == 1) checked @endif>
                                          <label for="radiopromoted{{$student->id}}">Promoted
                                          </label>
                                        </div>
                                        <div class="icheck-primary d-inline">
                                          <input type="radio" id="radioconditional{{$student->id}}" name="actiontaken{{$student->id}}" value="2" @if($student->actiontaken == 2) checked @endif>
                                          <label for="radioconditional{{$student->id}}">
                                              Conditional
                                          </label>
                                        </div>
                                        <div class="icheck-primary d-inline">
                                          <input type="radio" id="radioretained{{$student->id}}" name="actiontaken{{$student->id}}" value="3" @if($student->actiontaken == 3) checked @endif>
                                          <label for="radioretained{{$student->id}}">
                                              Retained
                                          </label>
                                        </div>
                                      </div>
                                </td>
                                {{-- <td><input type="text" style="font-size: 11px;" class="form-control form-control-sm input-actiontaken" value="{{$student->actiontaken}}" data-id="{{$student->id}}"/></td> --}}
                                <td style="vertical-align: middle;"></td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-md-12 text-right mb-2">
                <button type="button" class="btn btn-sm btn-success" id="btn-saveactiontaken"><i class="fa fa-share"></i> Save changes</button>
            </div>
        </div>
    </div>
</div>
<script>
    
</script>
@endif
