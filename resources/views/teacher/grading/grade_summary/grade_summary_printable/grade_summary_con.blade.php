<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$section_detail->levelname. ' - ' . $section_detail->sectionname . ' '. $schoolyear_detail->sydesc.' QUARTER '.$quarter}}</title>
    <style>

       
.table {
            width: 100%;
            margin-bottom: 1rem;
            background-color: transparent;
            font-size:11px ;
        }

        table {
            border-collapse: collapse;
        }
        
        .table thead th {
            vertical-align: bottom;
        }
        
        .table td, .table th {
            padding: .75rem;
            vertical-align: top;
        }
        .table td, .table th {
            padding: .75rem;
            vertical-align: top;
        }
        
        .table-bordered {
            border: 1px solid #00000;
        }

        .table-bordered td, .table-bordered th {
            border: 1px solid #00000;
        }

        .table-sm td, .table-sm th {
            padding: .3rem;
        }

        .text-center{
            text-align: center !important;
        }
        
        .text-right{
            text-align: right !important;
        }
        
        .text-left{
            text-align: left !important;
        }
        
        .p-0{
            padding: 0 !important;
        }

        .p-1{
            padding: .25rem !important;
        }


        .mb-0{
            margin-bottom: 0;
        }

        .border-bottom{
            border-bottom:1px solid black;
        }

        .mb-1, .my-1 {
            margin-bottom: .25rem!important;
        }

        body{
            font-family: Calibri, sans-serif;
        }
        
        .align-middle{
            vertical-align: middle !important;    
        }

         
        .grades td{
            padding-top: .1rem;
            padding-bottom: .1rem;
            font-size: 9px !important;
        }

        .studentinfo td{
            padding-top: .1rem;
            padding-bottom: .1rem;
          
        }

        .text-red{
            color: red;
            border: solid 1px black;
        }

        @page { size: 8.5in 11in; margin: .25in .25in  }
        
    </style>
    
  
    
</head>
<body>  

    <table class="table mb-0 table-sm header" style="font-size:13px;">
        <tr>
            <td width="20%" rowspan="2" class="text-right align-middle p-0">
                <img src="{{base_path()}}/public/{{$schoolinfo[0]->picurl}}" alt="school" width="70px">
            </td>
            <td width="60%" class="p-0 text-center" >
                <h3 class="mb-0" style="font-size:20px !important">{{$schoolinfo[0]->schoolname}}</h3>
            </td>
            <td width="20%" rowspan="2" class="text-right align-middle p-0">
                
            </td>
        </tr>
        <tr>
            <td class="p-0 text-center">
                {{$schoolinfo[0]->address}}
            </td>
        </tr>
    </table>

    <table class="table mb-0 table-sm" style="font-size:13px;">
        <tr>
            <td width="100%" class="text-center p-0"><b>{{$section_detail->levelname}} - {{$section_detail->sectionname}}</b></td>
        </tr>
        <tr>
            <td width="100%" class="text-center p-0">MAPEH Grading Sheet School Year {{$schoolyear_detail->sydesc}}</td>
        </tr>
    </table>
    <br>
    <table class="table mb-0 table-sm" >
            <tr>
                <td width="50%"><b>Subject Teacher: </b> {{$section_detail->lastname}},  {{$section_detail->firstname}}</td>
                <td width="50%" class="text-right"></td>
            </tr>
        
    </table>
    <table class="table table-sm table-bordered" width="100%" style="font-size:.6rem !important">
        <tr>
            <td width="30%"></td>
            <td colspan="5" class="text-center">1st Grading</td>
            <td colspan="5" class="text-center">2nd Grading</td>
            <td colspan="5" class="text-center">3rd Grading</td>
            <td colspan="5" class="text-center">4th Grading</td>
            <td colspan="2" class="text-center" >Final Grading</td>
        </tr>
        <tr>
            <td></td>

            <td width="3%">M</td>
            <td width="3%">A</td>
            <td width="3%">PE</td>
            <td width="3%">H</td>
            <td width="3%">F</td>
              
            <td width="3%">M</td>
            <td width="3%">A</td>
            <td width="3%">PE</td>
            <td width="3%">H</td>
            <td width="3%">F</td>

            <td width="3%">M</td>
            <td width="3%">A</td>
            <td width="3%">PE</td>
            <td width="3%">H</td>
            <td width="3%">F</td>
              
            <td width="3%">M</td>
            <td width="3%">A</td>
            <td width="3%">PE</td>
            <td width="3%">H</td>
            <td width="3%">F</td>
         

            <td width="3%">FR</td>
            <td width="7%">Remarks</td>
        </tr>
        @php
                $male = 0;
                $female = 0;
                $count = 1;
        @endphp
        @foreach ($students as $item)
            @if($male == 0 && strtoupper($item->gender) == 'MALE')
                <tr class="bg-gray">
                    <td style="padding-left: 5px !important" colspan="23">MALE</td>
                    @php
                        $male = 1;
                    @endphp
                </tr>
            @elseif($female == 0  && strtoupper($item->gender) == 'FEMALE')
                <tr>
                    <td style="padding-left: 5px !important" colspan="23">&nbsp;</td>
                    @php
                        $female = 1;
                        $count = 1;
                    @endphp
                </tr>
                <tr class="bg-gray">
                    <td style="padding-left: 5px !important" colspan="23">FEMALE</td>
                    @php
                        $female = 1;
                        $count = 1;
                    @endphp
                </tr>
            @endif
            <tr>
                @php
                    $comp_subjects = collect($item->grades)->where('subjCom',$subjid)->values();
                @endphp
                 <td style="padding-left: 5px !important" >{{$count}}. {{$item->student}}</td>
                @foreach($comp_subjects as $comp_item)
                    <td>{{$comp_item->q1}}</td>
                    <td>{{$comp_item->q2}}</td>
                    <td>{{$comp_item->q3}}</td>
                    <td>{{$comp_item->q4}}</td>
                    <td>{{$comp_item->q4}}</td>
                @endforeach
                <td>{{collect($item->grades)->where('subjid',$subjid)->first()->finalrating}}</td>
                <td>{{collect($item->grades)->where('subjid',$subjid)->first()->actiontaken}}</td>
            </tr>
            @php
                $count += 1;
            @endphp
        @endforeach
        <tr>
            <td style="padding-left: 5px !important" colspan="23">&nbsp;</td>
        </tr>
        <tr>
            <td width="30%">Teacher Initial</td>
            <td colspan="5" class="text-center"></td>
            <td colspan="5" class="text-center"></td>
            <td colspan="5" class="text-center"></td>
            <td colspan="5" class="text-center"></td>
            <td colspan="2" class="text-center"></td>
        </tr>
      
    </table>
    <i style="font-size:.5rem !important">Date Generated: {{\Carbon\Carbon::now('Asia/Manila')->isoFormat('MMMM DD, YYYY hh:mm A')}}</i>

    {{-- <table class="table-bordered grades_display">
        <thead>
            <tr >
                <td class="border-0"></td>
                <td class="rotated_cell" width="15%">
                    <div class="rotate_text"></div>
                </td>
                @php
                    $width = 80 / collect($subjects)->count();
                @endphp
                @foreach ($subjects as $subj_item)
                    @if($section_detail->levelid != 14 && $section_detail->levelid != 15)
                        @if($quarter != 5)
                            <td class="rotated_cell" >
                                <div class="rotate_text" style="bottom: 20px !important">
                                    @if($subj_item->subjCom == null)
                                        <b>{{$subj_item->subjdesc}}</b>
                                    @else
                                        {{$subj_item->subjdesc}}
                                    @endif
                                </div>
                            </td>
                        @else
                            @if($subj_item->subjCom == null)
                                <td class="rotated_cell" >
                                    <div class="rotate_text" style="bottom: 20px !important">
                                        @if($subj_item->subjCom == null)
                                            <b>{{$subj_item->subjdesc}}</b>
                                        @else
                                            {{$subj_item->subjdesc}}
                                        @endif
                                    </div>
                                </td>
                            @endif
                        @endif
                    @else
                        <td class="rotated_cell" >
                            <div class="rotate_text" style="bottom: 20px !important">
                                 {{$subj_item->subjdesc}}
                            </div>
                        </div>
                    @endif
                @endforeach
                <td class="rotated_cell"  width="5%">
                    <div class="rotate_text"></div>
                 </td>
                 <td class="rotated_cell"  width="5%">
                    <div class="rotate_text"><b>Average</b></div>
                 </td>
           </thead> </tr>
        </thead>
        <tbody>
            @php
                $temp_quarter = $quarter;
                $quarter = 'quarter'.$temp_quarter;
                $qstatus = 'q'.$temp_quarter.'status';
            @endphp
            @php
                    $male = 0;
                    $female = 0;
                    $count = 1;
            @endphp
            @foreach ($students as $item)
            <col span="1" class="wide">
                @if($male == 0 && strtoupper($item->gender) == 'MALE')
                    <tr class="bg-gray">
                        <td class="text-center">#</td>
                        <td style="padding-left: 5px !important">MALE</td>
                         @foreach ($subjects as $subj_item)
                             @if($section_detail->levelid != 14 && $section_detail->levelid != 15)
                                @if($quarter != 'quarter5')
                                     <td class="text-center"></td>
                                @else
                                    @if($subj_item->subjCom == null)
                                         <td class="text-center"></td>
                                    @endif
                                @endif
                            @else
                                 <td class="text-center"></td>
                            @endif
                        @endforeach
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        @php
                            $male = 1;
                        @endphp
                    </tr>
                @elseif($female == 0  && strtoupper($item->gender) == 'FEMALE')
                    <tr>
                        <td></td>
                        <td>&nbsp;</td>
                        @foreach ($subjects as $subj_item)
                             @if($section_detail->levelid != 14 && $section_detail->levelid != 15)
                                @if($quarter != 'quarter5')
                                     <td class="text-center"></td>
                                @else
                                    @if($subj_item->subjCom == null)
                                         <td class="text-center"></td>
                                    @endif
                                @endif
                            @else
                                 <td class="text-center"></td>
                            @endif
                        @endforeach
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        @php
                            $female = 1;
                            $count = 1;
                        @endphp
                    </tr>
                    <tr class="bg-gray">
                        <td class="text-center">#</td>
                        <td style="padding-left: 5px !important">FEMALE</td>
                        @foreach ($subjects as $subj_item)
                            @if($section_detail->levelid != 14 && $section_detail->levelid != 15)
                                @if($quarter != 'quarter5')
                                     <td class="text-center"></td>
                                @else
                                    @if($subj_item->subjCom == null)
                                         <td class="text-center"></td>
                                    @endif
                                @endif
                            @else
                                 <td class="text-center"></td>
                            @endif
                        @endforeach
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        @php
                            $female = 1;
                            $count = 1;
                        @endphp
                    </tr>
                @endif
                <tr>
                    <td class="text-center">{{$count}}</td>
                    <td style="padding-left: 5px !important">{{$item->student}}</td>
                    @foreach ($subjects as $subj_item)
                        @php
                            if($quarter != 'quarter5'){
                                if(isset(collect($item->grades)->where('subjid',$subj_item->id)->first()->$qstatus)){
                                    $submitted = collect($item->grades)->where('subjid',$subj_item->id)->first()->$qstatus != 0 ? true : false;
                                }else{
                                    $submitted = false;
                                }
                                $grade = $submitted ? collect($item->grades)->where('subjid',$subj_item->id)->count() ? collect($item->grades)->where('subjid',$subj_item->id)->first()->qg : '' : '';
                            }else{
                                $grade = collect($item->grades)->where('subjid',$subj_item->id)->first()->qg;
                            }
                        @endphp
                        @if($section_detail->levelid != 14 && $section_detail->levelid != 15)
                            @if($quarter == 'quarter5')
                                @if($subj_item->subjCom == null)
                                    <td class="text-center {{$grade < 75 ? 'text-red':''}}">{{$grade}}</td>
                                @endif
                            @else
                                <td class="text-center {{$grade < 75 ? 'text-red':''}}">{{$grade}}</td>
                            @endif
                        @else
                           <td class="text-center {{$grade < 75 ? 'text-red':''}}">{{$grade}}</td>
                        @endif
                    @endforeach
                    <td class="text-center"></td>
                  
                    @if($section_detail->levelid == 14 || $section_detail->levelid == 15)
                        @if($activesem == 1)
                            <td class="text-center {{collect($item->grades)->where('id','G1')->where('semid','1')->first()->qg < 75 ? 'text-red':''}}">{{collect($item->grades)->where('id','G1')->where('semid','1')->first()->qg}}</td>
                        @else
                            <td class="text-center {{collect($item->grades)->where('id','G1')->where('semid','2')->first()->qg < 75 ? 'text-red':''}}">{{collect($item->grades)->where('id','G1')->where('semid','2')->first()->qg}}</td>
                        @endif
                    @else
                        <td class="text-center {{collect($item->grades)->where('id','G1')->first()->qg < 75 ? 'text-red':''}}">{{collect($item->grades)->where('id','G1')->first()->qg }}</td>
                    @endif
                    
                </tr>
                @php
                    $count += 1;
                @endphp
            @endforeach --}}
            
        </tbody>
    </table>
        

</body>
</html>