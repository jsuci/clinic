<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title>Document</title>
<style>
    html{
        /* text-transform: uppercase; */
        
    font-family: Arial, Helvetica, sans-serif;
    }
.logo{
    width: 100%;
    table-layout: fixed;
}
.header{
    width: 100%;
}
table tr {
    page-break-inside: auto !important;
}

.text-center {
    text-align: center !important;
}

.text-left {
    text-align: left !important;
}

.align-top {
    vertical-align: top !important;
}

.dashed-border {
    border-top: 1px dashed black !important;
    border-bottom: 1px dashed black !important;
}

</style>
@php
 
 $numlimit = count($students)/2;   
 if (strpos($numlimit,'.') !== false) {
     $numlimit+=0.5;
 }
@endphp
</head>
<body>
    <div style="width: 100%; text-align: center; font-size: 12px;">
        {{DB::table('schoolinfo')->first()->schoolname}}
        <br/>
           {{ucwords(strtolower(DB::table('schoolinfo')->first()->address))}}
        <br/>
        <br/>
        @if(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'spct')
            GRADING SHEET
        @else
            OFFICIAL CLASS LIST
        @endif
        <br/>
        {{$semester}} S.Y. {{$sydesc}}
    </div>
    <br>
    @if(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'spct')
        <table style="width: 100%; font-size: 11px; border-collapse: collapse; text-align: left !important;">
            <tr> 
                <th style="width: 15%;">Subjects Code:</th>     
                <td><u>{{collect($schedules)->first()->subjCode}}</u></td>
                <th style="width: 15%;">Credit Units:</th>
                <td>{{collect($schedules)->first()->lecunits + collect($schedules)->first()->labunits}}</td>
            </tr>
            <tr> 
                <th>Descriptive Title:</th>     
                <td><u>{{collect($schedules)->first()->subjDesc}}</u></td>
                <th>Time:</th>
                <td><u>{{date('h:i A',strtotime(collect($schedules)->first()->stime))}} - {{date('h:i A',strtotime(collect($schedules)->first()->etime))}}</u></td>
            </tr>
        </table>
        <br>
        
        <table  style="width:100%; font-size: 11px; border-collapse: collapse;" cellpadding="0" cellspacing="0" >
            <thead style="text-align: left !important;">
                <tr>
                    <th  width="3%" class="text-center">NO</th>
                    <th style="width: 2%;">&nbsp;</th>
                    <th style="width: 35%;">Name</th>
                    <th style="width: 2%;">&nbsp;</th>
                    <th width="10%">Program</th>
                    <th style="width: 2%;">&nbsp;</th>
                    <th  width="10%">Year Level</th>
                    <th style="width: 2%;">&nbsp;</th>
                    <th width="10%">Midterm</th>
                    <th style="width: 2%;">&nbsp;</th>
                    <th  width="10%">Final</th>
                    <th style="width: 2%;">&nbsp;</th>
                    <th  width="10%">Sem Grade</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $num = 1;
                @endphp
                    @foreach ($students as $key => $student)
                            <tr>
                                <td class="text-center">{{$num}}</td>
                                <td></td>
                                <td>{{$student->lastname}}, {{$student->firstname}} {{$student->middlename}} {{$student->suffix}}</td>
                                <td></td>
                                <td>{{$student->courseabrv}}</td>
                                <td></td>
                                <td >{{str_replace("COLLEGE","",$student->levelname)}}</td>
                                <td></td>
                                <td style="border-bottom: 1px solid black;"></td>
                                <td></td>
                                <td style="border-bottom: 1px solid black;"></td>
                                <td></td>
                                <td style="border-bottom: 1px solid black;"></td>
                            </tr>
                            @php
                                $num += 1;
                            @endphp
                    @endforeach
        
            </tbody>
        </table>
        <br/>
        <br/>
        <br/>
        <table style="width: 100%; font-size: 12px;">
            <tr>
                <td style="border-bottom: 1px solid black;"></td>
                <td style="width: 10%;"></td>
                <td style="border-bottom: 1px solid black;"></td>
                <td style="width: 10%;"></td>
                <td style="border-bottom: 1px solid black;"></td>
            </tr>
            <tr>
                <td style="text-align: center;">Instructor</td>
                <td></td>
                <td style="text-align: center;">Dean</td>
                <td></td>
                <td style="text-align: center;">Registrar</td>
            </tr>
            <tr>
                <td>Date Signed:</td>
                <td></td>
                <td>Date Signed:</td>
                <td></td>
                <td>Date Signed:</td>
            </tr>
        </table>
    @else
        <table style="width: 100%; font-size: 12px; border-collapse: collapse; text-align: left !important;">
            <tr> 
                <th width="15%">Subject:</th>     
                <td width="85%">{{collect($schedules)->first()->subjCode}} - <i>{{collect($schedules)->first()->subjDesc}}</i></td>
            </tr>
        </table>
        <table style="width: 100%; font-size: 12px; border-collapse: collapse; text-align: left !important;">
            <tr> 
                <th width="15%">Section / Course:</th>     
                <td width="85%">{{collect($schedules)->first()->sectionDesc}} - <i>{{collect($schedules)->first()->courseabrv}}</i></td>
            </tr>
        </table>
        <table style="width: 100%; font-size: 12px; border-collapse: collapse; text-align: left !important;">
            <tr> 
                <th width="15%" class="align-top">Schedule:</th>     
                <td width="85%">
                    @foreach ($schedules[0]->schedule as $item)
                        {{$item->time}} - <i>{{$item->day}}</i> <br>
                    @endforeach
                </td>
            </tr>
        </table>
        <br>
        @php
            $num = 1;
            $num2 = $numlimit+1;
            $studarray = $students;
        @endphp
        {{-- @if(count($students) <= 40) --}}
            <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                <thead>
                    <tr>
                        <th width="5%" class="dashed-border text-left">NO</th>
                        <th width="65%" class="dashed-border text-left">Student's Name</th>
                        <th  width="15%" class="dashed-border text-left">Year Level</th>
                        <th width="15%" class="dashed-border text-left">Course</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $num = 1;
                    @endphp
                    @foreach ($students as $key => $student)
                            <tr>
                                <td>{{$num}}</td>
                                <td>{{$student->lastname}}, {{$student->firstname}} {{$student->middlename}} {{$student->suffix}}</td>
                                <td>{{str_replace("COLLEGE","",$student->levelname)}}</td>
                                <td>{{$student->courseabrv}}</td>
                            </tr>
                            @php
                                $num += 1;
                            @endphp
                    @endforeach
                </tbody>
               
            </table>
        {{-- @else
            <table style="width: 100%; border-collapse: collapse; font-size: 11px; page-break-inside: auto;">
                <thead>
                    <tr>
                        <th style="width: 5%;" class="dashed-border text-left">NO</th>
                        <th style="width: 30%; " class="dashed-border text-left">STUDENT'S NAME</th>
                        <th style="width: 15%;" class="dashed-border text-left">COURSE</th>
                        <th style="width: 5%;" class="dashed-border text-left">NO</th>
                        <th style="width: 30%;" class="dashed-border text-left">STUDENT'S NAME</th>
                        <th style="width: 15%;" class="dashed-border text-left">COURSE</th>
                    </tr>
                </thead>
                @foreach ($students as $student)
                    @if($num<=$numlimit)
                        <tr>
                            <td>{{$num}}</td>
                            <td>{{ucwords(strtolower($student->lastname))}}, {{ucwords(strtolower($student->firstname))}} @if($student->middlename !=null) {{$student->middlename[0]}}. @endif {{ucwords(strtolower($student->suffix))}}</td>
                            <td>{{$student->courseabrv}}</td>
                            @php
                                $student->done = 1;
                                $num += 1;
                            @endphp
                            
                            @if($num2<=count($students))
                                <td>{{$num2}}</td>
                                <td>
                                    {{ucwords(strtolower($students[$num2-1]->lastname))}}, {{ucwords(strtolower($students[$num2-1]->firstname))}} @if($students[$num2-1]->middlename !=null) {{$students[$num2-1]->middlename[0]}}. @endif {{ucwords(strtolower($students[$num2-1]->suffix))}}
                                </td>
                                <td>
                                    {{$students[$num2-1]->courseabrv}}
                                </td>
                                @php
                                    $students[$num2-1]->done = 1;
                                    $num2+= 1;
                            @endphp
                            @endif
                        </tr>
                    @endif
                @endforeach
            </table>
        @endif --}}
    @endif

</body>
</html>