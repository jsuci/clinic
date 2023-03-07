<style>
    * { font-family: Arial, Helvetica, sans-serif; }
    @page { margin: 20px 20px 0px 20px;  size: 8.5in 14in ;}

    #table1 td{
        padding: 0px;
    }
    table {
        border-collapse: collapse;
    }
    #table2{
        margin-top: 2px;
        font-size: 11px;
    }

    input[type="checkbox"] {
    /* position: relative; */
    top: 2px;
    box-sizing: content-box;
    width: 14px;
    height: 14px;
    margin: 0 5px 0 0;
    cursor: pointer;
    -webkit-appearance: none;
    border-radius: 2px;
    background-color: #fff;
    border: 1px solid #b7b7b7;
    }

    input[type="checkbox"]:before {
    content: '';
    display: block;
    }

    input[type="checkbox"]:checked:before {
    width: 4px;
    height: 9px;
    margin: 0px 4px;
    border-bottom: 2px solid ;
    border-right: 2px solid ;
    transform: rotate(45deg);
    }
    .text-center{
        text-align: center;
    }
</style>
<table style="width: 100%" id="table1">
    <tr>
        <td width="15%" rowspan="4"><sup style="font-size: 9px;">SF10-JHS</sup><br/>
        <img src="{{base_path()}}/public/assets/images/department_of_Education.png" alt="school" width="70px">
        </td>
        <td style="text-align:center; font-size: 11px;">Republic of the Philippines</td>
        <td width="15%" style="text-align:right;" rowspan="4"><img src="{{base_path()}}/public/assets/images/deped_logo.png" alt="school" width="80px"></td>
    </tr>
    <tr>
        <td style="text-align:center; font-size: 11px;">Department of Education</td>
    </tr>
    <tr>
        <td style="text-align:center; font-size: 15px; font-weight: bold;">Learner's Permanent Academic Record for Junior High School (SF10-JHS)</td>
    </tr>
    <tr style="line-height: 5px;font-size: 11px;">
        <td style="text-align:center; font-style: italic;">(Formerly Form 137)</td>
    </tr>
</table>
<div style="width: 100%; line-height: 3px;">&nbsp;</div>
<table style="width: 100%" id="table2">
    <tr>
        <td colspan="8" style="text-align: center; font-size: 13px; font-weight: bold; background-color: #bfc48b; border: 1px solid black;">LEARNER'S INFORMATION</td>
    </tr>
    {{-- <tr>
        <td colspan="8">&nbsp;</td>
    </tr> --}}
    <tr>
        <td style="width: 10%;">LAST NAME:</td>
        <td style="width: 15%; border-bottom: 1px solid black;">{{$studinfo->lastname}}</td>
        <td style="width: 10%;">FIRST NAME:</td>
        <td style="width: 15%; border-bottom: 1px solid black;">{{$studinfo->firstname}}</td>
        <td style="width: 15%;">NAME EXTN. (Jr,I,II)</td>
        <td style="width: 10%; border-bottom: 1px solid black;">{{$studinfo->suffix}}</td>
        <td style="width: 10%;">MIDDLE NAME</td>
        <td style="width: 10%; border-bottom: 1px solid black;">{{$studinfo->middlename ?? null}}</td>
    </tr>
</table>
<table style="width: 100%; font-size: 11px;" id="table3">
    {{-- <tr>
        <td colspan="6">&nbsp;</td>
    </tr> --}}
    <tr>
        <td style="width: 20%;">Learner Reference Number (LRN):</td>
        <td style="width: 15%; border-bottom: 1px solid black;">{{$studinfo->lrn}}</td>
        <td style="width: 20%; text-align: right;">Birthdate (mm/dd/yyyy):</td>
        <td style="width: 15%; border-bottom: 1px solid black;">{{date('m/d/Y',strtotime($studinfo->dob))}}</td>
        <td style="width: 10%; text-align: right;">Sex:</td>
        <td style="width: 10%; border-bottom: 1px solid black;">{{$studinfo->gender}}</td>
    </tr>
</table>
<div style="width: 100%; line-height: 3px;">&nbsp;</div>
<table style="width: 100%; font-size: 13px; font-weight: bold; text-align: center;" id="table4">
    <tr>
        <td style="border: 1px solid black; background-color: #bfc48b">
            ELIGIBILITY FOR JHS ENROLMENT
        </td>
    </tr>
</table>
<div style="width: 100%; line-height: 3px;">&nbsp;</div>
<div style="width: 100%; border: 1px solid black; padding-top: 4px;">
    <table style="width: 100%; font-size: 11px;" id="table5">
        <tr style="font-style: italic;">
            <td><input type="checkbox" name="check-1"@if($eligibility->completer == 1) checked @endif>Elementary School Completer</td>
            <td>General Average: {{$eligibility->genave}}</td>
            <td>Citation: (If Any)<u>{{$eligibility->citation}}</u></td>
        </tr>
    </table>
    <table style="width: 100%; font-size: 11px;" id="table6">
        <tr>
            <td style="width: 13%;">Name of School:</td>
            <td style="width: 25%; border-bottom: 1px solid black;">{{$eligibility->schoolname}}</td>
            <td style="width: 10%;">School ID:</td>
            <td style="border-bottom: 1px solid black;">{{$eligibility->schoolid}}</td>
            <td style="width: 15%;">Address of School:</td>
            <td style="width: 20%; border-bottom: 1px solid black;">{{$eligibility->schooladdress}}</td>
        </tr>
    </table>
    <div style="width: 100%; line-height: 3px;">&nbsp;</div>
</div>
<div style="width: 100%; line-height: 3px;">&nbsp;</div>
<table style="width: 100%; font-size: 11px;" id="table7" >
    <tr>
        <td colspan="4">Other Credential Presented</td>
    </tr>
    <tr>
        <td style="width: 28%; "> &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="check-1"@if($eligibility->peptpasser == 1) checked @endif>PEPT Passer &nbsp;&nbsp;&nbsp;&nbsp;Rating:<u>&nbsp;&nbsp;&nbsp;&nbsp;{{$eligibility->peptrating}}&nbsp;&nbsp;&nbsp;&nbsp;</u></td>
        <td style="width: 28%; "> &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="check-1"@if($eligibility->alspasser == 1) checked @endif>ALS A & E Passer &nbsp;&nbsp;&nbsp;&nbsp;Rating:<u>&nbsp;&nbsp;&nbsp;&nbsp;{{$eligibility->alsrating}}&nbsp;&nbsp;&nbsp;&nbsp;</u></td>
        <td style="width: 18%;"><input type="checkbox" name="check-1">Others (Pls. Specify):</td>
        <td style="width: 16%; border-bottom: 1px solid black;">{{$eligibility->specifyothers}}</td>
    </tr>
</table>
<table style="width: 100%; font-size: 11px;" id="table8" >
    <tr>
        <td style="width: 35%; text-align: right;">Date of Examination/Assessment (mm/dd/yyyy):</td>
        <td style="width: 10%; border-bottom: 1px solid black;">@if($eligibility->examdate != null) {{date('m/d/Y',strtotime($eligibility->examdate))}} @endif</td>
        <td style="width: 28%; "> &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;Name and Address of Testing Center:</td>
        <td style="border-bottom: 1px solid black;" colspan="2">{{$eligibility->centername}}</td>
    </tr>
</table>
<div style="width: 100%; line-height: 3px;">&nbsp;</div>
<table style="width: 100%; font-size: 13px; font-weight: bold; text-align: center;" id="table9">
    <tr>
        <td style="border: 1px solid black; background-color: #bfc48b">
            SCHOLASTIC RECORD
        </td>
    </tr>
</table>
@php
    $tablescount = 2;
    $tablescount-= count($records);
    $firstpage_cert_level = 0;
    $secondpage_cert_level = 0;
@endphp
<div style="width: 100%; line-height: 3px;">&nbsp;</div>
    @if(count($records)>0)
        @foreach($records as $key=>$record)      
            @php
                $attended_lastschoolyear = null;
                $attended_lastschool     = null;
                $attended_lastschoolid   = null;
            @endphp
            @if($record[0]->sydesc != null || $record[0]->sydesc != '')
                @php
                    $attended_lastschoolyear = $record[0]->sydesc;
                    $attended_lastschool = $record[0]->schoolname;
                    $attended_lastschoolid = $record[0]->schoolid;
                @endphp
            @endif
            @if($record[1]->sydesc != null || $record[1]->sydesc != '')
                @php
                    $attended_lastschoolyear = $record[1]->sydesc;
                    $attended_lastschool = $record[1]->schoolname;
                    $attended_lastschoolid = $record[1]->schoolid;
                @endphp
            @endif
            @if($key == 1)
                <table style="width: 100%; font-size: 9px; page-break-before: always;">
                    <tr>
                        <td>SF10-JHS</td>
                        <td style="text-align: right;">Page 2 of <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u></td>
                    </tr>
                </table>
            @endif
            <table style="width: 100%; table-layout: fixed; border: 2px solid black; font-size: 11px; page-break-inside: avoid;" border="1">
                <thead>
                    <tr>
                        <td colspan="7">
                            <table style="width: 100%; font-size: 9px;">
                                <tr>
                                    <td style="width: 5%;">School:</td>
                                    <td style="width: 23%; border-bottom: 1px solid black;">{{$record[0]->schoolname}}</td>
                                    <td style="width: 7%;">School ID:</td>
                                    <td style="width: 15%; border-bottom: 1px solid black;">{{$record[0]->schoolid}}</td>
                                    <td style="width: 5%;">District:</td>
                                    <td style="width: 10%; border-bottom: 1px solid black;">{{$record[0]->schooldistrict}}</td>
                                    <td style="width: 5%;">Division:</td>
                                    <td style="width: 15%; border-bottom: 1px solid black;">{{$record[0]->schooldivision}}</td>
                                    <td style="width: 5%;">Region:</td>
                                    <td style="width: 10%; border-bottom: 1px solid black;">
                                        @if(isset($record[0]->schoolregion))
                                            @if (str_contains(strtolower($record[0]->schoolregion), 'region')) 
                                            {{str_replace('REGION', '', strtoupper($record[0]->schoolregion))}}
                                            @else
                                            {{isset($record[0]->schoolregion) ? $record[0]->schoolregion : null}}
                                            @endif
                                        @else
                                            {{isset($record[0]->schoolregion) ? $record[0]->schoolregion : null}}
                                        @endif
                                    </td>
                                </tr>
                            </table>
                            <table style="width: 100%; font-size: 9px;">
                                <tr>
                                    <td style="width: 15%;">Classified as Grade:</td>
                                    <td style="width: 10%; border-bottom: 1px solid black;">{{preg_replace('/\D+/', '', $record[0]->levelname)}}</td>
                                    <td style="width: 15%;">Section:</td>
                                    <td style="width: 30%; border-bottom: 1px solid black;">{{$record[0]->sectionname}}</td>
                                    <td style="width: 20%;">SchoolYear:</td>
                                    <td style="width: 10%; border-bottom: 1px solid black;">{{$record[0]->sydesc}}</td>
                                </tr>
                            </table>
                            <table style="width: 100%; font-size: 9px;">
                                <tr>
                                    <td style="width: 20%;">Name of Adviser/Teacher:</td>
                                    <td style="border-bottom: 1px solid black;">{{$record[0]->teachername}}</td>
                                    <td style="width: 10%;">Signature:</td>
                                    <td style="width: 20%; border-bottom: 1px solid black;"></td>
                                </tr>
                            </table>
                            <div style="width: 100%; line-height: 3px;">&nbsp;</div>
                        </td>
                    </tr>
                    <tr style="font-size: 10px !important;">
                        <th rowspan="2" style="width: 40%;">LEARNING AREAS</th>
                        <th colspan="4">Quarterly</th>
                        <th rowspan="2" style="width: 10%;">Final<br/>Rating</th>
                        <th rowspan="2" style="width: 18%;">Remarks</th>
                    </tr>
                    <tr style="font-size: 10px !important;">
                        <th style="width: 8%;">1</th>
                        <th style="width: 8%;">2</th>
                        <th style="width: 8%;">3</th>
                        <th style="width: 8%;">4</th>
                    </tr>
                </thead>
                    @php
                            $firstpage_cert_level = ($record[0]->levelid) + 1;
                            if($record[0]->promotionstatus == 2 || $record[0]->promotionstatus == 3)
                            {
                                $firstpage_cert_level = $record[0]->levelid;
                            }
                    @endphp
                @if(count($record[0]->grades)>0)
                    @foreach($record[0]->grades as $grade)
                        @if($record[0]->type == 2)
                            @if(strtolower($grade->subjtitle) != 'general average')
                                <tr @if($key == 0) style="font-size: 11px;" @else style="font-size: 9px;" @endif>
                                    <td>@if($grade->inMAPEH ==1 ) &nbsp;&nbsp;&nbsp;&nbsp; @endif @if(isset($grade->inTLE))@if($grade->inTLE ==1 )&nbsp;&nbsp;&nbsp;@endif @endif @if(strtolower($grade->subjtitle) == 't.l.e' || strtolower($grade->subjtitle) == 'mapeh' ){{strtoupper($grade->subjtitle)}}@else {{$grade->subjtitle}}@endif</td>
                                    <td class="text-center">{{$grade->quarter1}}</td>
                                    <td class="text-center">{{$grade->quarter2}}</td>
                                    <td class="text-center">{{$grade->quarter3}}</td>
                                    <td class="text-center">{{$grade->quarter4}}</td>
                                    @if(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'bct' && $grade->inMAPEH ==1)
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    @else
                                    <td class="text-center">{{$grade->finalrating}}</td>
                                    <td class="text-center">{{$grade->remarks}}</td>
                                    @endif
                                </tr>
                            @endif
                        @else
                            <tr @if($key == 0) style="font-size: 11px;" @else style="font-size: 9px;" @endif>
                                <td>@if($grade->inMAPEH ==1 ) &nbsp;&nbsp;&nbsp;&nbsp; @endif @if(isset($grade->inTLE))@if($grade->inTLE ==1 )&nbsp;&nbsp;&nbsp;@endif @endif @if(strtolower($grade->subjtitle) == 't.l.e' || strtolower($grade->subjtitle) == 'mapeh' ){{strtoupper($grade->subjtitle)}}@else {{$grade->subjtitle}}@endif</td>
                                <td class="text-center">{{$grade->quarter1}}</td>
                                <td class="text-center">{{$grade->quarter2}}</td>
                                <td class="text-center">{{$grade->quarter3}}</td>
                                <td class="text-center">{{$grade->quarter4}}</td>
                                @if(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'bct' && $grade->inMAPEH ==1)
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                                @else
                                <td class="text-center">{{$grade->finalrating}}</td>
                                <td class="text-center">{{$grade->remarks}}</td>
                                @endif
                            </tr>
                        @endif
                    @endforeach
                    @if($record[0]->type == 1)
                        @if(count($record[0]->subjaddedforauto)>0)
                            @foreach($record[0]->subjaddedforauto as $customsubjgrade)
                                <tr @if($key == 0) style="font-size: 11px;" @else style="font-size: 9px;" @endif>
                                    <td>{{$customsubjgrade->subjdesc}}</td>
                                    <td class="text-center">{{$customsubjgrade->q1}}</td>
                                    <td class="text-center">{{$customsubjgrade->q2}}</td>
                                    <td class="text-center">{{$customsubjgrade->q3}}</td>
                                    <td class="text-center">{{$customsubjgrade->q4}}</td>
                                    <td class="text-center">{{$customsubjgrade->finalrating}}</td>
                                    <td class="text-center">{{$customsubjgrade->actiontaken}}</td>
                                </tr>
                            @endforeach
                        @endif
                    @endif
                    @if(count($record[0]->grades)<12)
                        @for($x=count($record[0]->grades); $x<12; $x++)
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        @endfor
                    @endif
                @else
                    @for($x=0; $x<12; $x++)
                    <tr @if($key == 0) style="font-size: 11px;" @else style="font-size: 9px;" @endif>
                        @if($x == 0)
                        <td>
                            Filipino
                        </td>
                        @elseif($x == 1)
                        <td>
                            English
                        </td>
                        @elseif($x == 2)
                        <td>
                            Mathematics
                        </td>
                        @elseif($x == 3)
                        <td>
                            Science
                        </td>
                        @elseif($x == 4)
                        <td>
                            Araling Panlipunan (AP)
                        </td>
                        @elseif($x == 5)
                        <td>
                            Edukasyon sa Pagpapakatao (EsP)
                        </td>
                        @elseif($x == 6)
                        <td>
                            Technology and Livelihood Education (TLE)
                        </td>
                        @elseif($x == 7)
                        <td>
                            MAPEH
                        </td>
                        @elseif($x == 8)
                        <td>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Music
                        </td>
                        @elseif($x == 9)
                        <td>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Arts
                        </td>
                        @elseif($x == 10)
                        <td>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Physical Education
                        </td>
                        @elseif($x == 11)
                        <td>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Health
                        </td>
                        @endif
                        <td class="text-center">&nbsp;</td>
                        <td class="text-center">&nbsp;</td>
                        <td class="text-center">&nbsp;</td>
                        <td class="text-center">&nbsp;</td>
                        <td class="text-center">&nbsp;</td>
                        <td class="text-center">&nbsp;</td>
                    </tr>
                    @endfor
                    @for($x=0; $x<2; $x++)
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    @endfor
                @endif
                @if($record[0]->type == 1)
                    @if(DB::table('schoolinfo')->first()->schoolid == '405308') {{--fmcma--}}
                        <tr style="font-weight: bold; font-size: 9px;">
                            <td></td>
                            <td colspan="4" class="text-center"><em>General Average</em></td>
                            <td class="text-center">{{collect($record[0]->generalaverage)->first()->finalrating}}</td>
                            <td class="text-center">@if(collect($record[0]->generalaverage)->first()->finalrating > 0){{collect($record[0]->generalaverage)->first()->finalrating >= 75 ? 'PASSED' : 'FAILED'}}@endif</td>
                        </tr>
                    @else
                        <tr style="font-weight: bold; font-size: 9px;">
                            <td></td>
                            <td colspan="4" class="text-center"><em>General Average</em></td>
                            <td class="text-center">{{collect($record[0]->generalaverage)->first()->finalrating ?? ''}}</td>
                            <td class="text-center">
                                @if(isset(collect($record[0]->generalaverage)->first()->finalrating))@if(collect($record[0]->generalaverage)->first()->finalrating > 0){{ collect($record[0]->generalaverage)->first()->finalrating >= 75 ? 'PASSED' : 'FAILED'}}@endif @endif
                            </td>
                            {{-- <td class="text-center">{{number_format(collect($record[0]->grades)->sum('finalrating')/count($record[0]->grades))}}</td>
                            <td class="text-center">{{ number_format(collect($record[0]->grades)->sum('finalrating')/count($record[0]->grades)) >= 75 ? 'PASSED' : 'FAILED'}}</td> --}}
                        </tr>
                    @endif
                @elseif($record[0]->type == 2)
                    @if(count($record[0]->grades) > 1)
                        @foreach($record[0]->grades as $grade)
                            @if(strtolower($grade->subjtitle) == 'general average')
                                <tr style="font-weight: bold; font-size: 9px;">
                                    <td></td>
                                    <td colspan="4" class="text-center"><em>General Average</em></td>
                                    <td class="text-center">{{$grade->finalrating}}</td>
                                    @if(DB::table('schoolinfo')->first()->schoolid == '405308') {{--fmcma--}}
                                    <td class="text-center">{{$grade->remarks}}</td>
                                    @else
                                    <td class="text-center">@if($grade->finalrating > 0){{$grade->finalrating >= 75 ? 'PROMOTED' : 'FAILED'}}@endif</td>
                                    @endif
                                </tr>
                            @endif
                        @endforeach
                    @endif
                @endif
            </table>
            <div style="background-color: #bfc48b; height: 4px; border-right: 2px solid black; border-left: 2px solid black;"></div>
            <table style="width: 100%; table-layout: fixed; border: 2px solid black; font-size: 11px;" border="1">
                @if(count($record[0]->remedials)>0)
                    @if(collect($record[0]->remedials)->contains('type','2'))
                        @foreach($record[0]->remedials as $remedial)
                            @if($remedial->type == 2)
                                <tr>
                                    <td style="width: 30%;">Remedial Classes</td>
                                    <td colspan="4">Conducted from (mm/dd/yyyy)&nbsp;&nbsp;{{$remedial->datefrom}};&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;to  (mm/dd/yyyy)&nbsp;&nbsp;{{$remedial->dateto}}:</td>
                                </tr>
                            @endif
                        @endforeach
                    @else
                        <tr>
                            <td style="width: 30%;">Remedial Classes</td>
                            <td colspan="4">Conducted from (mm/dd/yyyy)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;to  (mm/dd/yyyy)</td>
                        </tr>
                    @endif
                    <tr>
                        <th>Learning Areas</th>
                        <th>Final Rating</th>
                        <th>Remedial Class Mark</th>
                        <th>Recomputed<br/>Final Grade</th>
                        <th style="width: 10%;">Remarks</th>
                    </tr>
                    @if(collect($record[0]->remedials)->contains('type','1'))
                        @foreach($record[0]->remedials as $remedial)
                            @if($remedial->type == 1)
                                <tr>
                                    <td>{{$remedial->subjectname}}</td>
                                    <td class="text-center">{{$remedial->finalrating}}</td>
                                    <td class="text-center">{{$remedial->remclassmark}}</td>
                                    <td class="text-center">{{$remedial->recomputedfinal}}</td>
                                    <td class="text-center">{{$remedial->remarks}}</td>
                                </tr>
                            @endif
                        @endforeach
                    @endif
                    @if($columnremedials>count(collect($record[0]->remedials)->where('type','1')))
                        @php
                            $columnremedialsdisplay = $columnremedials-count(collect($record[0]->remedials)->where('type','1'));
                        @endphp        
                        @for($x = 0; $columnremedialsdisplay>$x; $columnremedialsdisplay--)
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        @endfor
                    @endif   
                @else
                    <tr>
                        <td style="width: 30%;">Remedial Classes</td>
                        <td colspan="4">Conducted from (mm/dd/yyyy)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;to  (mm/dd/yyyy)</td>
                    </tr>
                    <tr>
                        <th>Learning Areas</th>
                        <th>Final Rating</th>
                        <th>Remedial Class Mark</th>
                        <th>Recomputed<br/>Final Grade</th>
                        <th style="width: 10%;">Remarks</th>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                @endif
            </table>
            <div style="width: 100%; line-height: 5px;">&nbsp;</div>
            <table style="width: 100%; table-layout: fixed; border: 2px solid black; font-size: 11px; page-break-inside: avoid;" border="1">
                <thead>
                    <tr>
                        <td colspan="7">
                            <table style="width: 100%; font-size: 9px;">
                                <tr>
                                    <td style="width: 5%;">School:</td>
                                    <td style="width: 23%; border-bottom: 1px solid black;">{{$record[1]->schoolname}}</td>
                                    <td style="width: 7%;">School ID:</td>
                                    <td style="width: 15%; border-bottom: 1px solid black;">{{$record[1]->schoolid}}</td>
                                    <td style="width: 5%;">District:</td>
                                    <td style="width: 10%; border-bottom: 1px solid black;">{{$record[1]->schooldistrict}}</td>
                                    <td style="width: 5%;">Division:</td>
                                    <td style="width: 15%; border-bottom: 1px solid black;">{{$record[1]->schooldivision}}</td>
                                    <td style="width: 5%;">Region:</td>
                                    <td style="width: 10%; border-bottom: 1px solid black;">{{$record[1]->schoolregion}}</td>
                                </tr>
                            </table>
                            <table style="width: 100%; font-size: 9px;">
                                <tr>
                                    <td style="width: 15%;">Classified as Grade:</td>
                                    <td style="width: 10%; border-bottom: 1px solid black;">{{preg_replace('/\D+/', '', $record[1]->levelname)}}</td>
                                    <td style="width: 15%;">Section:</td>
                                    <td style="width: 30%; border-bottom: 1px solid black;">{{$record[1]->sectionname}}</td>
                                    <td style="width: 20%;">SchoolYear:</td>
                                    <td style="width: 10%; border-bottom: 1px solid black;">{{$record[1]->sydesc}}</td>
                                </tr>
                            </table>
                            <table style="width: 100%; font-size: 9px;">
                                <tr>
                                    <td style="width: 20%;">Name of Adviser/Teacher:</td>
                                    <td style="border-bottom: 1px solid black;">{{$record[1]->teachername}}</td>
                                    <td style="width: 10%;">Signature:</td>
                                    <td style="width: 20%; border-bottom: 1px solid black;"></td>
                                </tr>
                            </table>
                            <div style="width: 100%; line-height: 3px;">&nbsp;</div>
                        </td>
                    </tr>
                    <tr style="font-size: 10px !important;">
                        <th rowspan="2" style="width: 40%;">LEARNING AREAS</th>
                        <th colspan="4">Quarterly</th>
                        <th rowspan="2" style="width: 10%;">Final<br/>Rating</th>
                        <th rowspan="2" style="width: 18%;">Remarks</th>
                    </tr>
                    <tr style="font-size: 10px !important;">
                        <th style="width: 8%;">1</th>
                        <th style="width: 8%;">2</th>
                        <th style="width: 8%;">3</th>
                        <th style="width: 8%;">4</th>
                    </tr>
                </thead>
                    @php
                            if($record[1]->promotionstatus == 1)
                            {
                                $secondpage_cert_level = ($record[1]->levelid) + 1;
                            }
                            elseif($record[1]->promotionstatus == 2 || $record[1]->promotionstatus == 3)
                            {
                                $secondpage_cert_level = $record[1]->levelid;
                            }
                    @endphp
                @if(count($record[1]->grades)>0)
                    @foreach($record[1]->grades as $grade)
                        @if($record[1]->type == 2)
                            @if(strtolower($grade->subjtitle) != 'general average')
                                <tr @if($key == 0) style="font-size: 11px;" @else style="font-size: 9px;" @endif>
                                    <td>@if($grade->inMAPEH ==1 ) &nbsp;&nbsp;&nbsp;&nbsp; @endif @if(isset($grade->inTLE))@if($grade->inTLE == 1 )&nbsp;&nbsp;&nbsp;@endif @endif  @if(strtolower($grade->subjtitle) == 't.l.e' || strtolower($grade->subjtitle) == 'mapeh' ){{strtoupper($grade->subjtitle)}}@else {{$grade->subjtitle}}@endif</td>
                                    <td class="text-center">{{$grade->quarter1}}</td>
                                    <td class="text-center">{{$grade->quarter2}}</td>
                                    <td class="text-center">{{$grade->quarter3}}</td>
                                    <td class="text-center">{{$grade->quarter4}}</td>
                                    @if(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'bct' && $grade->inMAPEH ==1)
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    @else
                                    <td class="text-center">{{$grade->finalrating}}</td>
                                    <td class="text-center">{{$grade->remarks}}</td>
                                    @endif
                                </tr>
                            @endif
                        @else
                            <tr @if($key == 0) style="font-size: 11px;" @else style="font-size: 9px;" @endif>
                                <td>@if(isset($grade->inMAPEH) ) @if($grade->inMAPEH ==1 )&nbsp;&nbsp;&nbsp;&nbsp;@endif @endif @if(isset($grade->inTLE))@if($grade->inTLE == 1 )&nbsp;&nbsp;&nbsp;@endif @endif @if(strtolower($grade->subjtitle) == 't.l.e' || strtolower($grade->subjtitle) == 'mapeh' ){{strtoupper($grade->subjtitle)}}@else {{$grade->subjtitle}}@endif</td>
                                <td class="text-center">{{$grade->quarter1}}</td>
                                <td class="text-center">{{$grade->quarter2}}</td>
                                <td class="text-center">{{$grade->quarter3}}</td>
                                <td class="text-center">{{$grade->quarter4}}</td>
                                @if(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'bct' && $grade->inMAPEH ==1)
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                                @else
                                <td class="text-center">{{$grade->finalrating}}</td>
                                <td class="text-center">{{$grade->actiontaken}}</td>
                                @endif
                            </tr>
                        @endif
                    @endforeach
                    @if($record[1]->type == 1)
                        @if(count($record[1]->subjaddedforauto)>0)
                            @foreach($record[1]->subjaddedforauto as $customsubjgrade)
                                <tr @if($key == 0) style="font-size: 11px;" @else style="font-size: 9px;" @endif>
                                    <td>{{$customsubjgrade->subjdesc}}</td>
                                    <td class="text-center">{{$customsubjgrade->q1}}</td>
                                    <td class="text-center">{{$customsubjgrade->q2}}</td>
                                    <td class="text-center">{{$customsubjgrade->q3}}</td>
                                    <td class="text-center">{{$customsubjgrade->q4}}</td>
                                    <td class="text-center">{{$customsubjgrade->finalrating}}</td>
                                    <td class="text-center">{{$customsubjgrade->actiontaken}}</td>
                                </tr>
                            @endforeach
                        @endif
                    @endif
                    @if(count($record[1]->grades)<12)
                        @for($x=count($record[1]->grades); $x<12; $x++)
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        @endfor
                    @endif
                @else
                    @for($x=0; $x<12; $x++)
                    <tr @if($key == 0) style="font-size: 11px;" @else style="font-size: 9px;" @endif>
                        @if($x == 0)
                        <td>
                            Filipino
                        </td>
                        @elseif($x == 1)
                        <td>
                            English
                        </td>
                        @elseif($x == 2)
                        <td>
                            Mathematics
                        </td>
                        @elseif($x == 3)
                        <td>
                            Science
                        </td>
                        @elseif($x == 4)
                        <td>
                            Araling Panlipunan (AP)
                        </td>
                        @elseif($x == 5)
                        <td>
                            Edukasyon sa Pagpapakatao (EsP)
                        </td>
                        @elseif($x == 6)
                        <td>
                            Technology and Livelihood Education (TLE)
                        </td>
                        @elseif($x == 7)
                        <td>
                            MAPEH
                        </td>
                        @elseif($x == 8)
                        <td>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Music
                        </td>
                        @elseif($x == 9)
                        <td>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Arts
                        </td>
                        @elseif($x == 10)
                        <td>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Physical Education
                        </td>
                        @elseif($x == 11)
                        <td>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Health
                        </td>
                        @endif
                        <td class="text-center">&nbsp;</td>
                        <td class="text-center">&nbsp;</td>
                        <td class="text-center">&nbsp;</td>
                        <td class="text-center">&nbsp;</td>
                        <td class="text-center">&nbsp;</td>
                        <td class="text-center">&nbsp;</td>
                    </tr>
                    @endfor
                    @for($x=0; $x<2; $x++)
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    @endfor
                @endif
                @if($record[1]->type == 1)
                    @if(DB::table('schoolinfo')->first()->schoolid == '405308') {{--fmcma--}}
                        <tr style="font-weight: bold; font-size: 9px;">
                            <td></td>
                            <td colspan="4" class="text-center"><em>General Average</em></td>
                            <td class="text-center">{{number_format(collect($record[1]->generalaverage)->first()->finalrating)}}</td>
                            <td class="text-center">@if(collect($record[1]->generalaverage)->first()->finalrating > 0){{collect($record[1]->generalaverage)->first()->finalrating >= 75 ? 'PASSED' : 'FAILED'}}@endif</td>
                        </tr>
                    @else
                        <tr style="font-weight: bold;font-size: 9px;">
                            <td></td>
                            <td colspan="4" class="text-center"><em>General Average</em></td>
                            <td class="text-center">{{collect($record[1]->generalaverage)->first()->finalrating ?? ''}}</td>
                            <td class="text-center">
                                @if(isset(collect($record[1]->generalaverage)->first()->finalrating))@if(collect($record[1]->generalaverage)->first()->finalrating > 0){{ collect($record[1]->generalaverage)->first()->finalrating >= 75 ? 'PASSED' : 'FAILED'}}@endif @endif
                            </td>
                            {{-- <td class="text-center" style="text-align: center;">{{number_format(collect($record[1]->grades)->where('subjtitle','!=','General Average')->sum('finalrating')/count($record[1]->grades))}}</td>
                            <td style="text-align: center;">{{number_format(collect($record[1]->grades)->where('subjtitle','!=','General Average')->sum('finalrating')/count($record[1]->grades)) > 74 ? 'PASSED' : 'FAILED'}}</td> --}}
                        </tr>
                    @endif
                @elseif($record[1]->type == 2)
                    @if(count($record[1]->grades) > 1)
                        @foreach($record[1]->grades as $grade)
                            @if(strtolower($grade->subjtitle) == 'general average')
                                <tr style="font-weight: bold;font-size: 9px;">
                                    <td></td>
                                    <td colspan="4" class="text-center"><em>General Average</em></td>
                                    <td class="text-center">{{$grade->finalrating}}</td>
                                    @if(DB::table('schoolinfo')->first()->schoolid == '405308') {{--fmcma--}}
                                    <td class="text-center">{{$grade->remarks}}</td>
                                    @else
                                    <td class="text-center">@if($grade->finalrating > 0){{$grade->finalrating >= 75 ? 'PASSED' : 'FAILED'}}@endif</td>
                                    @endif
                                </tr>
                            @endif
                        @endforeach
                    @endif
                @endif
            </table>
            <div style="background-color: #bfc48b; height: 4px; border-right: 2px solid black; border-left: 2px solid black;"></div>
            <table style="width: 100%; table-layout: fixed; border: 2px solid black; font-size: 11px;" border="1">
                @if(count($record[1]->remedials)>0)
                    @if(collect($record[1]->remedials)->contains('type','2'))
                        @foreach($record[1]->remedials as $remedial)
                            @if($remedial->type == 2)
                                <tr>
                                    <td style="width: 30%;">Remedial Classes</td>
                                    <td colspan="4">Conducted from (mm/dd/yyyy)&nbsp;&nbsp;{{$remedial->datefrom}};&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;to (mm/dd/yyyy) &nbsp;&nbsp;{{$remedial->dateto}};</td>
                                </tr>
                            @endif
                        @endforeach
                    @else
                        <tr>
                            <td style="width: 30%;">Remedial Classes</td>
                            <td colspan="4">Conducted from (mm/dd/yyyy)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;to (mm/dd/yyyy) </td>
                        </tr>
                    @endif
                    <tr>
                        <th>Learning Areas</th>
                        <th>Final Rating</th>
                        <th>Remedial Class Mark</th>
                        <th>Recomputed<br/>Final Grade</th>
                        <th style="width: 10%;">Remarks</th>
                    </tr>
                    @if(collect($record[1]->remedials)->contains('type','1'))
                        @foreach($record[1]->remedials as $remedial)
                            @if($remedial->type == 1)
                                <tr>
                                    <td>{{$remedial->subjectname}}</td>
                                    <td class="text-center">{{$remedial->finalrating}}</td>
                                    <td class="text-center">{{$remedial->remclassmark}}</td>
                                    <td class="text-center">{{$remedial->recomputedfinal}}</td>
                                    <td class="text-center">{{$remedial->remarks}}</td>
                                </tr>
                            @endif
                        @endforeach
                    @endif
                    @if($columnremedials>count(collect($record[1]->remedials)->where('type','1')))
                        @php
                            $columnremedialsdisplay = $columnremedials-count(collect($record[1]->remedials)->where('type','1'));
                        @endphp        
                        @for($x = 0; $columnremedialsdisplay>$x; $columnremedialsdisplay--)
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        @endfor
                    @endif   
                @else
                    <tr>
                        <td style="width: 30%;">Remedial Classes</td>
                        <td colspan="4">Conducted from (mm/dd/yyyy)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;to (mm/dd/yyyy) </td>
                    </tr>
                    <tr>
                        <th>Learning Areas</th>
                        <th>Final Rating</th>
                        <th>Remedial Class Mark</th>
                        <th>Recomputed<br/>Final Grade</th>
                        <th style="width: 10%;">Remarks</th>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                @endif
            </table>
            @if($key == 1)
            <div style="width: 100%; line-height: 5px;">&nbsp;</div>
            <table style="width: 100%; table-layout: fixed; border: 2px solid black; font-size: 11px; page-break-inside: avoid;" border="1">
                <thead>
                    <tr>
                        <td colspan="7">
                            <table style="width: 100%; font-size: 9px;">
                                <tr>
                                    <td style="width: 5%;">School:</td>
                                    <td style="width: 23%; border-bottom: 1px solid black;">&nbsp;</td>
                                    <td style="width: 7%;">School ID:</td>
                                    <td style="width: 15%; border-bottom: 1px solid black;">&nbsp;</td>
                                    <td style="width: 5%;">District:</td>
                                    <td style="width: 10%; border-bottom: 1px solid black;">&nbsp;</td>
                                    <td style="width: 5%;">Division:</td>
                                    <td style="width: 15%; border-bottom: 1px solid black;">&nbsp;</td>
                                    <td style="width: 5%;">Region:</td>
                                    <td style="width: 10%; border-bottom: 1px solid black;">&nbsp;</td>
                                </tr>
                            </table>
                            <table style="width: 100%; font-size: 9px;">
                                <tr>
                                    <td style="width: 15%;">Classified as Grade:</td>
                                    <td style="width: 10%; border-bottom: 1px solid black;">&nbsp;</td>
                                    <td style="width: 15%;">Section:</td>
                                    <td style="width: 30%; border-bottom: 1px solid black;">&nbsp;</td>
                                    <td style="width: 20%;">SchoolYear:</td>
                                    <td style="width: 10%; border-bottom: 1px solid black;">&nbsp;</td>
                                </tr>
                            </table>
                            <table style="width: 100%; font-size: 9px;">
                                <tr>
                                    <td style="width: 20%;">Name of Adviser/Teacher:</td>
                                    <td style="border-bottom: 1px solid black;">&nbsp;</td>
                                    <td style="width: 10%;">Signature:</td>
                                    <td style="width: 20%; border-bottom: 1px solid black;"></td>
                                </tr>
                            </table>
                            <div style="width: 100%; line-height: 3px;">&nbsp;</div>
                        </td>
                    </tr>
                    <tr style="font-size: 10px !important;">
                        <th rowspan="2" style="width: 40%;">LEARNING AREAS</th>
                        <th colspan="4">Quarterly</th>
                        <th rowspan="2" style="width: 10%;">Final<br/>Rating</th>
                        <th rowspan="2" style="width: 18%;">Remarks</th>
                    </tr>
                    <tr style="font-size: 10px !important;">
                        <th style="width: 8%;">1</th>
                        <th style="width: 8%;">2</th>
                        <th style="width: 8%;">3</th>
                        <th style="width: 8%;">4</th>
                    </tr>
                </thead>
                    @for($x=0; $x<12; $x++)
                    <tr @if($key == 0) style="font-size: 11px;" @else style="font-size: 9px;" @endif>
                        @if($x == 0)
                        <td>
                            Filipino
                        </td>
                        @elseif($x == 1)
                        <td>
                            English
                        </td>
                        @elseif($x == 2)
                        <td>
                            Mathematics
                        </td>
                        @elseif($x == 3)
                        <td>
                            Science
                        </td>
                        @elseif($x == 4)
                        <td>
                            Araling Panlipunan (AP)
                        </td>
                        @elseif($x == 5)
                        <td>
                            Edukasyon sa Pagpapakatao (EsP)
                        </td>
                        @elseif($x == 6)
                        <td>
                            Technology and Livelihood Education (TLE)
                        </td>
                        @elseif($x == 7)
                        <td>
                            MAPEH
                        </td>
                        @elseif($x == 8)
                        <td>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Music
                        </td>
                        @elseif($x == 9)
                        <td>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Arts
                        </td>
                        @elseif($x == 10)
                        <td>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Physical Education
                        </td>
                        @elseif($x == 11)
                        <td>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Health
                        </td>
                        @endif
                        <td class="text-center">&nbsp;</td>
                        <td class="text-center">&nbsp;</td>
                        <td class="text-center">&nbsp;</td>
                        <td class="text-center">&nbsp;</td>
                        <td class="text-center">&nbsp;</td>
                        <td class="text-center">&nbsp;</td>
                    </tr>
                    @endfor
                    @for($x=0; $x<2; $x++)
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    @endfor
                <tr style="font-weight: bold;font-size: 9px;">
                    <td></td>
                    <td colspan="4" class="text-center"><em>General Average</em></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                </tr>
            </table>
            <div style="background-color: #bfc48b; height: 4px; border-right: 2px solid black; border-left: 2px solid black;"></div>
            <table style="width: 100%; table-layout: fixed; border: 2px solid black; font-size: 11px;" border="1">
                <tr>
                    <td style="width: 30%;">Remedial Classes</td>
                    <td colspan="4">Conducted from (mm/dd/yyyy)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;to (mm/dd/yyyy) </td>
                </tr>
                <tr>
                    <th>Learning Ares</th>
                    <th>Final Rating</th>
                    <th>Remedial Class Mark</th>
                    <th>Recomputed<br/>Final Grade</th>
                    <th style="width: 10%;">Remarks</th>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </table>
            @endif
            <table style="width: 100%; margin-top: 5px; border: 1px solid black;" border="1">
                <tr>
                    <th style="text-align: center; background-color: #bfc48b; font-size: 13px;">CERTIFICATION</th>
                </tr>
                <tr>
                    <td style="padding: 10px 2px 0px; font-size: 11.5px;">
                        I CERTIFY that this is a true record of <u>{{$studinfo->lastname}}, {{$studinfo->firstname}} @if($studinfo->middlename != null){{$studinfo->middlename[0]}}.@endif </u> with LRN <u>{{$studinfo->lrn}}</u> and that he/she is eligible for admission to Grade <u>&nbsp;&nbsp;&nbsp; @if($key == 0 && $firstpage_cert_level > 0)
                                {{(int)filter_var(DB::table('gradelevel')->where('id', $firstpage_cert_level)->first()->levelname, FILTER_SANITIZE_NUMBER_INT)}}
                                @elseif($key == 1  && $secondpage_cert_level > 0)
                                {{(int)filter_var(DB::table('gradelevel')->where('id', $secondpage_cert_level)->first()->levelname, FILTER_SANITIZE_NUMBER_INT)}}
                                @else &nbsp;&nbsp;&nbsp;&nbsp; @endif&nbsp;&nbsp;&nbsp;</u>.
                        <br/>
                        @if($attended_lastschoolyear == null)
                        Name of School: <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  School ID: <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  Last School Year Attended: <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>
                        @else
                        Name of School: <u>{{$attended_lastschool}}</u> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  School ID: <u>{{$attended_lastschoolid}}</u> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  Last School Year Attended: <u>&nbsp;&nbsp;{{$attended_lastschoolyear}}&nbsp;&nbsp;</u>
                        @endif
                        <br/>&nbsp;
                        <br/>&nbsp;
                        <table style="width: 100%;">
                            <tr>
                                <td style="width: 30%; border-bottom: 1px solid black;"></td>
                                <td style="width: 5%;"></td>
                                <td style="border-bottom: 1px solid black; text-align: center;">{{DB::table('schoolinfo')->first()->authorized}}</td>
                                <td style="width: 10%;"></td>
                                <td style="width: 20%;"></td>
                            </tr>
                            <tr style="font-size: 11px;">
                                <td style="text-align: center;">Date</td>
                                <td></td>
                                <td style="text-align: center;">Signature of Principal/School Head over Printed Name</td>
                                <td></td>
                                <td style="text-align: center;">(Affix School Seal Here)</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            {{-- @if($key == 1)
            <table style="width: 100%; font-size: 9px;">
                <tr>
                    <td>(May add Certification box if needed)</td>
                    <td style="text-align: right;"><em>SFRT Revised 2017</em></td>
                </tr>
            </table>
            @endif --}}
        @endforeach
    @endif