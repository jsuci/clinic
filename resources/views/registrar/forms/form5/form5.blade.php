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
                                    <td class="text-center">{{collect($students)->where('gender','male')->where('generalaverage','>=',75)->where('generalaverage','<=',79)->where('generalaverage','!=',0)->count()}}</td>
                                    <td class="text-center">{{collect($students)->where('gender','female')->where('generalaverage','>=',75)->where('generalaverage','<=',79)->where('generalaverage','!=',0)->count()}}</td>
                                    <td class="text-center">{{collect($students)->where('generalaverage','>=',75)->where('generalaverage','<=',79)->where('generalaverage','!=',0)->count()}}</td>
                                </tr>
                                <tr>
                                    <th>Satisfactory
                                        ( 80-84 )</th>
                                    <td class="text-center">{{collect($students)->where('gender','male')->where('generalaverage','>=',80)->where('generalaverage','<=',84)->where('generalaverage','!=',0)->count()}}</td>
                                    <td class="text-center">{{collect($students)->where('gender','female')->where('generalaverage','>=',80)->where('generalaverage','<=',84)->where('generalaverage','!=',0)->count()}}</td>
                                    <td class="text-center">{{collect($students)->where('generalaverage','>=',80)->where('generalaverage','<=',84)->where('generalaverage','!=',0)->count()}}</td>
                                </tr>
                                <tr>
                                    <th>Very Satisfactory
                                        ( 85 -89)</th>
                                    <td class="text-center">{{collect($students)->where('gender','male')->where('generalaverage','>=',85)->where('generalaverage','<=',89)->where('generalaverage','!=',0)->count()}}</td>
                                    <td class="text-center">{{collect($students)->where('gender','female')->where('generalaverage','>=',85)->where('generalaverage','<=',89)->where('generalaverage','!=',0)->count()}}</td>
                                    <td class="text-center">{{collect($students)->where('generalaverage','>=',85)->where('generalaverage','<=',89)->where('generalaverage','!=',0)->count()}}</td>
                                </tr>
                                <tr>
                                    <th>Outstanding
                                        ( 90 -100)</th>
                                    <td class="text-center">{{collect($students)->where('gender','male')->where('generalaverage','>=',90)->where('generalaverage','!=',0)->count()}}</td>
                                    <td class="text-center">{{collect($students)->where('gender','female')->where('generalaverage','>=',90)->where('generalaverage','!=',0)->count()}}</td>
                                    <td class="text-center">{{collect($students)->where('generalaverage','>=',90)->where('generalaverage','!=',0)->count()}}</td>
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
                <a href="/forms/form5?sectionid={{$gradeAndLevel[0]->sectionid}}&levelid={{$gradeAndLevel[0]->levelid}}&syid={{$sy->id}}&semid={{$sem->id}}&acadprogid={{$acadprogid}}&currentmonth={{\Carbon\Carbon::now()->month}}&action=export&exporttype=pdf" class="btn btn-sm btn-default" target="_blank">Export as PDF</a>
            </div>
            <div class="col-md-12">
                <table class="table table-bordered" style="font-size: 11px;">
                    <thead>
                        <tr class="text-center">
                            <th style="width: 15%;">LRN</th>
                            <th style="width: 40%;">LRN LEARNER'S NAME</th>
                            <th style="width: 10%;">GENERAL AVERAGE</th>
                            <th>ACTION TAKEN</th>
                            <th style="width: 25%;">Did Not Meet Expectations of the ff.
                                Learning Area/s as of end of
                                current School Year</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5">MALE</td>
                        </tr>
                        @foreach($students as $student)
                            @if(strtolower($student->gender)=="male")
                                <tr>
                                    <td>{{$student->lrn}}</td>
                                    <td>{{$student->lastname}}, {{$student->firstname}} {{$student->middlename}}</td>
                                    <td class="text-center">{{$student->generalaverage > 0 ? $student->generalaverage : null}}</td>
                                    <td class="text-center">
                                        @if($student->actiontaken == 1)PROMOTED @elseif($student->actiontaken == 2)CONDITIONAL @elseif($student->actiontaken == 3)RETAINED @endif {{$student->fraward}}</td>
                                    <td></td>
                                </tr>
                            @endif
                        @endforeach
                        <tr>
                            <td colspan="5">FEMALE</td>
                        </tr>
                        @foreach($students as $student)
                            @if(strtolower($student->gender)=="female")
                                <tr>
                                    <td>{{$student->lrn}}</td>
                                    <td>{{$student->lastname}}, {{$student->firstname}} {{$student->middlename}}</td>
                                    <td class="text-center">{{$student->generalaverage > 0 ? $student->generalaverage : null}}</td>
                                    <td class="text-center">
                                        @if($student->actiontaken == 1)PROMOTED @elseif($student->actiontaken == 2)CONDITIONAL @elseif($student->actiontaken == 3)RETAINED @endif {{$student->fraward}}</td>
                                    <td></td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>