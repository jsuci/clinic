<style>

    img {
        border-radius: unset !important;
    }
</style>
<div class="row">
    <div class="col-md-3 mb-2">
        <label>Registrar</label>
        <input type="text" class="form-control form-control-sm" id="input-registrar" placeholder="Registrar" value="{{collect($signatories)->where('title','Registrar')->first()->name ?? ''}}"/>
    </div>
    @if(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'sbc')
    <div class="col-md-3 mb-2">
        <label>Prepared by</label>
        <input type="text" class="form-control form-control-sm" id="input-preparedby" placeholder="Prepared by" value="{{collect($signatories)->where('description','Prepared by')->first()->name ?? ''}}"/>
    </div>
    <div class="col-md-3 mb-2">
        <label>Checked by</label>
        <input type="text" class="form-control form-control-sm" id="input-checkedby" placeholder="Checked by" value="{{collect($signatories)->where('description','Checked by')->first()->name ?? ''}}"/>
    </div>
    @else
    <div class="col-md-3 mb-2">
        <label>Assistant Registrar</label>
        <input type="text" class="form-control form-control-sm" id="input-assistantreg" placeholder="Assistant Registrar"/>
    </div>
    <div class="col-md-3 mb-2">
        <label>O.R #</label>
        <input type="text" class="form-control form-control-sm" id="input-or" placeholder="O.R #:"/>
    </div>
    @endif
    <div class="col-md-3 mb-2">
        <label>Date Issued:</label><input type="date" class="form-control form-control-sm p-1" id="input-date-issued" placeholder="Date Issued:" value="{{date('Y-m-d')}}"/>
    </div>
    <div class="col-md-12 text-right mb-2">
        <button type="button" class="btn btn-secondary btn-sm" id="btn-exporttopdf"><i class="fa fa-file-pdf"></i> Export TOR to PDF</button>
    </div>
</div>
<hr/>
@php

    if(strtoupper($studentinfo->gender) == 'FEMALE'){
        $avatar = 'avatar/S(F) 1.png';
    }
    else{
        $avatar = 'avatar/S(M) 1.png';
    }
@endphp
<div class="row">
    <div class="col-md-12">
        <div class="callout callout-info pt-0">
            <div class="row">
                <div class="col-md-12 text-right">
                    @if($getphoto)
                        @if (file_exists(base_path().'/public/'.$getphoto->picurl))
                        <img src="{{URL::asset($getphoto->picurl.'?random="'.\Carbon\Carbon::now('Asia/Manila')->isoFormat('MMDDYYHHmmss'))}}"  style="width: 140px; height: 140px; margin: 0px;" />
                        @else
                            @if (file_exists(base_path().'/public/'.$studentinfo->picurl))
                                <img src="/{{$studentinfo->picurl.'?random="'.\Carbon\Carbon::now('Asia/Manila')->isoFormat('MMDDYYHHmmss')}}" style="width: 140px; height: 140px; margin: 0px;" /> 
                            @else
                            <img src="{{asset($avatar)}}" alt="student" style="width: 140px; height: 140px; margin: 0px;" >
                            @endif
                        @endif
                    @else
                        
                        @if (file_exists(base_path().'/public/'.$studentinfo->picurl))
                            <img src="/{{$studentinfo->picurl.'?random="'.\Carbon\Carbon::now('Asia/Manila')->isoFormat('MMDDYYHHmmss')}}" style="width: 140px; height: 140px; margin: 0px;" /> 
                        @else
                            
                        <img src="{{asset($avatar)}}" alt="student" style="width: 140px; height: 140px; margin: 0px;" >
                        @endif
                    @endif
                </div>
            </div>
            <div class="row p-0">
                <div class="col-md-6 p-0"><h4>Details</h4></div>
                {{-- <div class="col-md-6 text-right">
                    <button type="button" class="btn btn-default btn-sm" id="btn-details-save"><i class="fa fa-share"></i> Save Changes</button>
                </div> --}}
            </div>
            @if(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'ndsc')
                <div class="row p-0" style="font-size: 13px !important;">
                    <div class="col-md-2 p-0" style="vertical-align: bottom;">
                        <label class="m-0">Address</label>
                    </div>
                    <div class="col-md-4 p-0">
                        <input type="text" class="form-control form-control-sm m-0" value="{{$details->address}}" id="input-address" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                    <div class="col-md-2 p-0" style="vertical-align: bottom;">
                        <label class="m-0">Date Admitted</label>
                    </div>
                    <div class="col-md-4 p-0">
                        <input type="text" class="form-control form-control-sm m-0" value="{{$details->admissiondatestr}}" id="input-dateadmitted" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                </div>
                <div class="row p-0" style="font-size: 13px !important;">
                    <div class="col-md-2 p-0">
                        <label class="m-0">College of</label>
                    </div>
                    <div class="col-md-4 p-0">
                        <input type="text" class="form-control form-control-sm" value="{{$details->collegeof}}" id="input-collegeof" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                    <div class="col-md-2 p-0" style="vertical-align: bottom;">
                        <label class="m-0">Entrance Data</label>
                    </div>
                    <div class="col-md-4 p-0">
                        <input type="text" class="form-control form-control-sm" value="{{$details->entrancedata}}" id="input-entrancedata" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                </div>
                <div class="row p-0 mt-2" style="font-size: 13px !important;">
                    <div class="col-md-4 p-0" style="vertical-align: bottom;">
                        <label class="m-0">Intermediate Grades Completed At/Year:</label>
                    </div>
                    <div class="col-md-8 p-0">
                        <input type="text" class="form-control form-control-sm m-0" value="{{$details->intermediategrades}}" id="input-intermediategrades" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                </div>
                <div class="row p-0" style="font-size: 13px !important;">
                    <div class="col-md-4 p-0" style="vertical-align: bottom;">
                        <label class="m-0">Secondary Grades Completed At/Year:</label>
                    </div>
                    <div class="col-md-8 p-0">
                        <input type="text" class="form-control form-control-sm m-0" value="{{$details->secondarygrades}}" id="input-secondarygrades" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                </div>
                <div class="row p-0">
                    <div class="col-md-12 p-0">
                        <table class="m-0" style="width: 100%; font-size: 12px !important; table-layout: fixed;">
                            <tr>
                                <td style="width: 10%;vertical-align: bottom;">Remarks:</td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" value="{{$details->remarks}}" id="input-remarks" style="border: none; border-bottom: 1px solid #ddd;"/>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            @elseif(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'gbbc')
                <div class="row p-0" style="font-size: 11.5px !important;">
                    <div class="col-md-2 p-0">
                        <label>Parent or Guardian</label>
                    </div>
                    <div class="col-md-4 p-0">
                        <input type="text" class="form-control form-control-sm" value="{{$details->parentguardian}}" id="input-parentguardian" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                    <div class="col-md-2 p-0">
                        <label>Address</label>
                    </div>
                    <div class="col-md-4 p-0">
                        <input type="text" class="form-control form-control-sm" value="{{$details->address}}" id="input-address" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                </div>
                <div class="row p-0">
                    <div class="col-md-12 p-0">
                        <table class="m-0" style="width: 100%; font-size: 11.5px !important; table-layout: fixed;">
                            <tr>
                                <td style="width: 15%; vertical-align: bottom;" class="p-0">Elementary Course</td>
                                <td style="width: 20%; vertical-align: bottom;" class="p-0">
                                    <input type="text" class="form-control form-control-sm" value="{{$details->elemcourse}}" id="input-elemcourse" style="border: none; border-bottom: 1px solid #ddd;"/>
                                </td>
                                <td style="width: 15%; vertical-align: bottom;" class="p-0 text-right">Date Completed</td>
                                <td class="p-0" style=" vertical-align: bottom;">
                                    <input type="date" class="form-control form-control-sm" value="{{$details->elemdatecomp}}" id="input-elemdatecomp" style="border: none; border-bottom: 1px solid #ddd;"/>
                                </td>
                                <td style="width: 10%; vertical-align: bottom;" class="p-0 text-right">School Year</td>
                                <td class="p-0" style=" vertical-align: bottom;">
                                    <input type="text" class="form-control form-control-sm" value="{{$details->elemsy}}" id="input-elemschoolyear" style="border: none; border-bottom: 1px solid #ddd;"/>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="row p-0">
                    <div class="col-md-12 p-0">
                        <table class="m-0" style="width: 100%; font-size: 11.5px !important; table-layout: fixed;">
                            <tr>
                                <td style="width: 15%; vertical-align: bottom;" class="p-0">Secondary Course</td>
                                <td style="width: 20%; vertical-align: bottom;" class="p-0">
                                    <input type="text" class="form-control form-control-sm" value="{{$details->secondcourse}}" id="input-secondcourse" style="border: none; border-bottom: 1px solid #ddd;"/>
                                </td>
                                <td style="width: 15%; vertical-align: bottom;" class="p-0 text-right">Date Completed</td>
                                <td class="p-0" style=" vertical-align: bottom;">
                                    <input type="date" class="form-control form-control-sm" value="{{$details->seconddatecomp}}" id="input-seconddatecomp" style="border: none; border-bottom: 1px solid #ddd;"/>
                                </td>
                                <td style="width: 10%; vertical-align: bottom;" class="p-0 text-right">School Year</td>
                                <td class="p-0" style=" vertical-align: bottom;">
                                    <input type="text" class="form-control form-control-sm" value="{{$details->secondsy}}" id="input-secondschoolyear" style="border: none; border-bottom: 1px solid #ddd;"/>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="row p-0">
                    <div class="col-md-12 p-0">
                        <table class="m-0" style="width: 100%; font-size: 11.5px !important; table-layout: fixed;">
                            <tr>
                                <td style="width: 15%; vertical-align: bottom;" class="p-0">Collegiate Course</td>
                                <td style="width: 20%; vertical-align: bottom;" class="p-0">
                                    <input type="text" class="form-control form-control-sm" value="{{$details->degree}}" id="input-degree" style="border: none; border-bottom: 1px solid #ddd;"/>
                                </td>
                                <td style="width: 15%; vertical-align: bottom;" class="p-0 text-right">Major</td>
                                <td class="p-0" style=" vertical-align: bottom;">
                                    <input type="text" class="form-control form-control-sm" value="{{$details->major}}" id="input-major" style="border: none; border-bottom: 1px solid #ddd;"/>
                                </td>
                                <td style="width: 10%; vertical-align: bottom;" class="p-0 text-right">School Year</td>
                                <td class="p-0" style=" vertical-align: bottom;">
                                    <input type="text" class="form-control form-control-sm" value="{{$details->thirdsy}}" id="input-thirdschoolyear" style="border: none; border-bottom: 1px solid #ddd;"/>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>Admission Date</td>
                                <td class="p-0" style=" vertical-align: bottom;">
                                    <input type="date" class="form-control form-control-sm" value="{{$details->admissiondate}}" id="input-admissiondate" style="border: none; border-bottom: 1px solid #ddd;"/>
                                </td>
                                <td>Basis of Admission</td>
                                <td class="p-0" style=" vertical-align: bottom;" colspan="2">
                                    <input type="date" class="form-control form-control-sm" value="{{$details->basisofadmission}}" id="input-basisofadmission" style="border: none; border-bottom: 1px solid #ddd;"/>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>Special Order</td>
                                <td class="p-0" style=" vertical-align: bottom;">
                                    <input type="text" class="form-control form-control-sm" value="{{$details->specialorder}}" id="input-specialorder" style="border: none; border-bottom: 1px solid #ddd;"/>
                                </td>
                                <td>Graduation Date</td>
                                <td class="p-0" style=" vertical-align: bottom;" colspan="2">
                                    <input type="date" class="form-control form-control-sm" value="{{$details->graduationdate}}" id="input-graduationdate" style="border: none; border-bottom: 1px solid #ddd;"/>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="row p-0">
                    <div class="col-md-12 p-0">
                        <table class="m-0" style="width: 100%; font-size: 12px !important; table-layout: fixed;">
                            <tr>
                                <td style="width: 10%;vertical-align: bottom;">Remarks:</td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" value="{{$details->remarks}}" id="input-remarks" style="border: none; border-bottom: 1px solid #ddd;"/>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            @elseif(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'hccsi' || strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'mci')
                <div class="row" style="font-size: 13px;">
                    <div class="col-md-2">Date of Birth:</div>
                    <div class="col-md-4">
                        <input type="text" class="form-control form-control-sm" value="{{$details->dob}}" style="border: none; border-bottom: 1px solid #ddd;" disabled/>
                    </div>
                    <div class="col-md-2">Sex:</div>
                    <div class="col-md-4">
                        <input type="text" class="form-control form-control-sm" value="{{$details->gender}}" style="border: none; border-bottom: 1px solid #ddd;" disabled/>
                    </div>
                </div>
                <div class="row" style="font-size: 13px;">
                    <div class="col-md-2">Place of Birth:</div>
                    <div class="col-md-4">
                        <input type="text" class="form-control form-control-sm" value="{{$details->pob}}" id="input-placeofbirth" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                    <div class="col-md-2">ACR No. (If Alien):</div>
                    <div class="col-md-4">
                        <input type="text" class="form-control form-control-sm" value="{{$details->acrno}}" id="input-acrno" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                </div>
                <div class="row" style="font-size: 13px;">
                    <div class="col-md-2">Citizenship:</div>
                    <div class="col-md-4">
                        <input type="text" class="form-control form-control-sm" value="{{$details->citizenship}}" id="input-citizenship" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                    <div class="col-md-2">Civil Status:</div>
                    <div class="col-md-4">
                        <input type="text" class="form-control form-control-sm" value="{{$details->civilstatus}}" id="input-civilstatus" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                </div>
                <div class="row" style="font-size: 13px;">
                    <div class="col-md-2">Name of Father:</div>
                    <div class="col-md-4">
                        <input type="text" class="form-control form-control-sm" value="{{$details->fathername}}" style="border: none; border-bottom: 1px solid #ddd;"disabled/>
                    </div>
                    <div class="col-md-2">Name of Mother:</div>
                    <div class="col-md-4">
                        <input type="text" class="form-control form-control-sm" value="{{$details->mothername}}" style="border: none; border-bottom: 1px solid #ddd;"disabled/>
                    </div>
                </div>
                <div class="row" style="font-size: 13px;">
                    <div class="col-md-2">Parents' Address:</div>
                    <div class="col-md-10">
                        <input type="text" class="form-control form-control-sm" value="{{$details->parentaddress}}" id="input-parentaddress" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                </div>
                <div class="row" style="font-size: 13px;">
                    <div class="col-md-2">Name of Guardian:</div>
                    <div class="col-md-4">
                        <input type="text" class="form-control form-control-sm" value="{{$details->parentguardian}}" id="input-parentguardian" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                    <div class="col-md-3">Guardian's Address:</div>
                    <div class="col-md-3">
                        <input type="text" class="form-control form-control-sm" value="{{$details->guardianaddress}}" id="input-guardianaddress" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                </div>
                <div class="row" style="font-size: 13px;">
                    <div class="col-md-4">Elementary Course Completed at:</div>
                    <div class="col-md-4">
                        <input type="text" class="form-control form-control-sm" value="{{$details->elemcourse}}" id="input-elemcourse" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                    <div class="col-md-2">Year:</div>
                    <div class="col-md-2">
                        <input type="text" class="form-control form-control-sm" value="{{$details->elemsy}}" id="input-elemschoolyear" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                </div>
                <div class="row" style="font-size: 13px;">
                    <div class="col-md-4">Intermediate Course Completed at:</div>
                    <div class="col-md-4">
                        <input type="text" class="form-control form-control-sm" value="{{$details->intermediatecourse}}" id="input-intermediatecourse" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                    <div class="col-md-2">Year:</div>
                    <div class="col-md-2">
                        <input type="text" class="form-control form-control-sm" value="{{$details->intermediatesy}}" id="input-intermediateschoolyear" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                </div>
                <div class="row" style="font-size: 13px;">
                    <div class="col-md-4">Secondary Course Completed at:</div>
                    <div class="col-md-4">
                        <input type="text" class="form-control form-control-sm" value="{{$details->secondcourse}}" id="input-secondcourse" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                    <div class="col-md-2">Year:</div>
                    <div class="col-md-2">
                        <input type="text" class="form-control form-control-sm" value="{{$details->secondsy}}" id="input-secondschoolyear" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                </div>
                <div class="row" style="font-size: 13px;">
                    <div class="col-md-3">Basis of Admission:</div>
                    <div class="col-md-4">
                        <input type="text" class="form-control form-control-sm" value="{{$details->basisofadmission}}" id="input-basisofadmission" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                    <div class="col-md-2">Date of Admission:</div>
                    <div class="col-md-3">
                        <input type="text" class="form-control form-control-sm" value="{{$details->admissiondatestr}}" id="input-dateadmitted" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                </div>
                <div class="row" style="font-size: 13px;">
                    <div class="col-md-2">Degree/Course:</div>
                    <div class="col-md-10">
                        <input type="text" class="form-control form-control-sm" value="{{$details->degree}}" id="input-degree" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                </div>
            @elseif(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'ccsa')
                {{-- <div class="row">
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-md-12">{{$studentinfo->lastname}}</div>
                            <div class="col-md-12">Last Name</div>
                        </div>
                    </div>
                    <div class="col-md-3"></div>
                    <div class="col-md-3"></div>
                    <div class="col-md-3"></div>
                </div> --}}
                <div class="row">
                    <div class="col-md-12">
                        <table style="width: 100%; tablea-layout: fixed; font-size: 14px;">
                            <tr>
                                <td>Date of Birth:</td>
                                <td></td>
                                <td>Place of Birth:</td>
                                <td></td>
                                <td>Sex:</td>
                                <td></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="row" style="font-size: 13px;">
                    <div class="col-md-2">Date of Birth:</div>
                    <div class="col-md-4">
                        <input type="text" class="form-control form-control-sm" value="{{$details->dob}}" style="border: none; border-bottom: 1px solid #ddd;" disabled/>
                    </div>
                    <div class="col-md-2">Sex:</div>
                    <div class="col-md-4">
                        <input type="text" class="form-control form-control-sm" value="{{$details->gender}}" style="border: none; border-bottom: 1px solid #ddd;" disabled/>
                    </div>
                </div>
                <div class="row" style="font-size: 13px;">
                    <div class="col-md-2">Place of Birth:</div>
                    <div class="col-md-4">
                        <input type="text" class="form-control form-control-sm" value="{{$details->pob}}" id="input-placeofbirth" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                    <div class="col-md-2">ACR No. (If Alien):</div>
                    <div class="col-md-4">
                        <input type="text" class="form-control form-control-sm" value="{{$details->acrno}}" id="input-acrno" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                </div>
                <div class="row" style="font-size: 13px;">
                    <div class="col-md-2">Citizenship:</div>
                    <div class="col-md-4">
                        <input type="text" class="form-control form-control-sm" value="{{$details->citizenship}}" id="input-citizenship" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                    <div class="col-md-2">Civil Status:</div>
                    <div class="col-md-4">
                        <input type="text" class="form-control form-control-sm" value="{{$details->civilstatus}}" id="input-civilstatus" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                </div>
                <div class="row" style="font-size: 13px;">
                    <div class="col-md-2">Name of Father:</div>
                    <div class="col-md-4">
                        <input type="text" class="form-control form-control-sm" value="{{$details->fathername}}" style="border: none; border-bottom: 1px solid #ddd;"disabled/>
                    </div>
                    <div class="col-md-2">Name of Mother:</div>
                    <div class="col-md-4">
                        <input type="text" class="form-control form-control-sm" value="{{$details->mothername}}" style="border: none; border-bottom: 1px solid #ddd;"disabled/>
                    </div>
                </div>
                <div class="row" style="font-size: 13px;">
                    <div class="col-md-2">Parents' Address:</div>
                    <div class="col-md-10">
                        <input type="text" class="form-control form-control-sm" value="{{$details->parentaddress}}" id="input-parentaddress" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                </div>
                <div class="row" style="font-size: 13px;">
                    <div class="col-md-2">Name of Guardian:</div>
                    <div class="col-md-4">
                        <input type="text" class="form-control form-control-sm" value="{{$details->parentguardian}}" id="input-parentguardian" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                    <div class="col-md-3">Guardian's Address:</div>
                    <div class="col-md-3">
                        <input type="text" class="form-control form-control-sm" value="{{$details->guardianaddress}}" id="input-guardianaddress" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                </div>
                <div class="row" style="font-size: 13px;">
                    <div class="col-md-4">Elementary Course Completed at:</div>
                    <div class="col-md-4">
                        <input type="text" class="form-control form-control-sm" value="{{$details->elemcourse}}" id="input-elemcourse" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                    <div class="col-md-2">Year:</div>
                    <div class="col-md-2">
                        <input type="text" class="form-control form-control-sm" value="{{$details->elemsy}}" id="input-elemschoolyear" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                </div>
                <div class="row" style="font-size: 13px;">
                    <div class="col-md-4">Intermediate Course Completed at:</div>
                    <div class="col-md-4">
                        <input type="text" class="form-control form-control-sm" value="{{$details->intermediatecourse}}" id="input-intermediatecourse" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                    <div class="col-md-2">Year:</div>
                    <div class="col-md-2">
                        <input type="text" class="form-control form-control-sm" value="{{$details->intermediatesy}}" id="input-intermediateschoolyear" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                </div>
                <div class="row" style="font-size: 13px;">
                    <div class="col-md-4">Secondary Course Completed at:</div>
                    <div class="col-md-4">
                        <input type="text" class="form-control form-control-sm" value="{{$details->secondcourse}}" id="input-secondcourse" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                    <div class="col-md-2">Year:</div>
                    <div class="col-md-2">
                        <input type="text" class="form-control form-control-sm" value="{{$details->secondsy}}" id="input-secondschoolyear" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                </div>
                <div class="row" style="font-size: 13px;">
                    <div class="col-md-3">Basis of Admission:</div>
                    <div class="col-md-4">
                        <input type="text" class="form-control form-control-sm" value="{{$details->basisofadmission}}" id="input-basisofadmission" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                    <div class="col-md-2">Date of Admission:</div>
                    <div class="col-md-3">
                        <input type="text" class="form-control form-control-sm" value="{{$details->admissiondatestr}}" id="input-dateadmitted" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                </div>
                <div class="row" style="font-size: 13px;">
                    <div class="col-md-2">Degree/Course:</div>
                    <div class="col-md-10">
                        <input type="text" class="form-control form-control-sm" value="{{$details->degree}}" id="input-degree" style="border: none; border-bottom: 1px solid #ddd;"/>
                    </div>
                </div>
            @else
                <!-- SAIT -->
                <div class="row p-0" style="font-size: 13px !important;">
                    <div class="col-md-12">
                        <table style="width: 100%; table-layout: fixed;">
                            <tr>
                                <td style="width: 12%;">Date of Birth</td>
                                <td >: {{$studentinfo->dob}}</td>
                                <td style="width: 12%;">Sex</td>
                                <td>: {{$studentinfo->gender}}</td>
                                <td style="width: 12%;">Date of Entrance</td>
                                <td>: <input type="date" style="width: 90%;" value="{{$details->entrancedate ?? ''}}" id="input-entrancedate"/></td>
                            </tr>
                            <tr>
                                <td style="width: 12%;">Birth Place</td>
                                <td>: {{$studentinfo->pob ?? ''}}</td>
                                <td style="width: 12%;">Citizenship</td>
                                <td>:  <input type="text" style="width: 90%;" value="{{$details->citizenship ?? ''}}" id="input-citizenship"/></td>
                                <td style="width: 12%;">Entrance Data</td>
                                <td>: <input type="text" style="width: 90%;" value="{{$details->entrancedata ?? ''}}" id="input-entrancedata"/></td>
                            </tr>
                            <tr>
                                <td style="width: 12%;">Father’s Name </td>
                                <td>: {{$studentinfo->fathername ?? ''}}</td>
                                <td style="width: 12%;">Civil Status</td>
                                <td>:  <input type="text" style="width: 90%;" value="{{$details->civilstatus ?? ''}}" id="input-civilstatus"/></td>
                                <td style="width: 12%;">Religion</td>
                                <td>: {{$studentinfo->religionanme ?? ''}}</td>
                            </tr>
                            <tr>
                                <td style="width: 12%;">Mother’s Name </td>
                                <td>: {{$studentinfo->mothername ?? ''}}</td>
                                <td style="width: 12%;">Home Address</td>
                                <td colspan="3">: {{$studentinfo->street}}, {{$studentinfo->barangay}}, {{$studentinfo->city}}, {{$studentinfo->province}}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-12 text-center">
                        <h6 class="text-bold">RECORDS OF PRELIMINARY EDUCATION</h6>
                    </div>
                    <div class="col-md-12">
                        <table class="" style="table-layout: fixed; width: 100%;" border="1">
                            <thead>
                                <tr>
                                    <th style="width: 15%;">Completed Courses</th>
                                    <th>Name of School</th>
                                    <th>Address</th>
                                    <th style="width: 15%;">School Year</th>
                                </tr>
                            </thead>
                            <tr>
                                <td>Primary</td>
                                <td><input type="text"  style="width: 100%;" value="{{$details->primaryschoolname ?? ''}}" id="input-schoolname-primary"/></td>
                                <td><input type="text"  style="width: 100%;" value="{{$details->primaryschooladdress ?? ''}}" id="input-schooladdress-primary"/></td>
                                <td><input type="text"  style="width: 100%;" value="{{$details->primaryschoolyear ?? ''}}" id="input-schoolyear-primary"/></td>
                            </tr>
                            <tr>
                                <td>Junior High School</td>
                                <td><input type="text"  style="width: 100%;" value="{{$details->juniorschoolname ?? ''}}" id="input-schoolname-junior"/></td>
                                <td><input type="text"  style="width: 100%;" value="{{$details->juniorschooladdress ?? ''}}" id="input-schooladdress-junior"/></td>
                                <td><input type="text"  style="width: 100%;" value="{{$details->juniorschoolyear ?? ''}}" id="input-schoolyear-junior"/></td>
                            </tr>
                            <tr>
                                <td>Senior High School</td>
                                <td><input type="text"  style="width: 100%;" value="{{$details->seniorschoolname ?? ''}}" id="input-schoolname-senior"/></td>
                                <td><input type="text"  style="width: 100%;" value="{{$details->seniorschooladdress ?? ''}}" id="input-schooladdress-senior"/></td>
                                <td><input type="text"  style="width: 100%;" value="{{$details->seniorschoolyear ?? ''}}" id="input-schoolyear-senior"/></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-12">
                        <table class="" style="table-layout: fixed; width: 100%;">
                            <tr>
                                <td style="width: 15%;">Date of Graduation</td>
                                <td><input type="date" style="width:" value="{{$details->graduationdate}}" id="input-graduationdate" style="border: none; border-bottom: 1px solid #ddd;"/></td>
                                <td class="text-right">NSTP Serial No.: &nbsp;&nbsp;&nbsp;</td>
                                <td><input type="text"  style="width: 100%;" value="{{$details->nstpserialno ?? ''}}" id="input-nstpserialno"/></td>
                            </tr>
                        </table>
                        
                    </div>
                </div>
            @endif
            <div class="col-md-12 text-right mt-2">
                <button type="button" class="btn btn-outline-primary" id="btn-details-save"><i class="fa fa-share"></i>&nbsp; Save Changes</button>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 mb-2">
        <button type="button" class="btn btn-info btn-sm" id="btn-addnewrecord" data-toggle="modal" data-target="#modal-newrecord"><i class="fa fa-plus"></i> Add new record</button>
    </div>
    @if(count($records)>0)
        @foreach($records as $record)
            <div class="col-md-12 @if($record->type == 'auto') auto-disabled @endif" >
                <div class="card">
                    <div class="card-header">
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <h6><strong>{{$record->sydesc}} / @if($record->semid == 1)1st Semester @elseif($record->semid == 2) 2nd Semester @elseif($record->semid == 3) Summer @endif </strong></h6>
                            </div>
                            <div class="col-md-8">
                                <h6><strong>{{$record->coursename}}</strong></h6>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <div class="row">
                                    <div class="col-2">
                                        <label>School ID</label>
                                        <input type="text" class="form-control form-control-sm" value="{{$record->schoolid}}" style="border: none;" readonly/>
                                    </div>
                                    <div class="col-4">
                                        <label>School Name</label>
                                        <input type="text" class="form-control form-control-sm" value="{{$record->schoolname}}" style="border: none;" readonly/>
                                    </div>
                                    <div class="col-6">
                                        <label>School Address</label>
                                        <input type="text" class="form-control form-control-sm" value="{{$record->schooladdress}}" style="border: none;" readonly/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 text-right mb-2">
                                {{-- @if(count($record->subjdata)==0) --}}
                                {{-- <button class="btn btn-sm btn-default btn-adddata text-success" data-torid="{{$record->id}}" data-semid="{{$record->semid}}" data-sydesc="{{$record->sydesc}}" data-courseid="{{$record->courseid}}"><i class="fa fa-plus"></i> Add Data</button> --}}
                                {{-- @else --}}
                                    <button type="button" {{$record->type == 'auto' ? 'hidden' : ''}} class="btn btn-sm btn-default btn-adddata text-success" data-torid="{{$record->id}}" data-semid="{{$record->semid}}" data-sydesc="{{$record->sydesc}}" data-courseid="{{$record->courseid}}"><i class="fa fa-plus"></i> Add Data</button>
                                    <button type="button" {{$record->type == 'auto' ? 'hidden' : ''}} class="btn btn-sm btn-default btn-editrecord" data-torid="{{$record->id}}"><i class="fa fa-edit text-warning"></i> Edit Record Info</button>
                                    <button type="button" {{$record->type == 'auto' ? 'hidden' : ''}} class="btn btn-default btn-sm text-danger btn-deleterecord" data-torid="{{$record->id}}"><i class="fa fa-trash"></i> Delete this record</button>
                                   
                                {{-- @endif --}}
                            </div>
                        </div>
                    </div>
                    @if(count($record->subjdata)>0)
                        <div class="card-body p-0">
                            <div class="row mb-2">
                                <div class="col-md-12">
                                    <table class="table" style="font-size: 14px;">
                                        <thead class="text-center">
                                            <tr>
                                                <th style="width: 15%;">Subject Code</th>
                                                <th style="width: 10%;">Units</th>
                                                <th>Description</th>
                                                <th style="width: 11%;">Grade</th>
                                                @if(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'ndsc' || strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'ccsa')
                                                <th style="width: 11%;">Re-Ex</th>
                                                @endif
                                                <th style="width: 11%;">Credits</th>
                                                @if($record->type != 'auto')
                                                <th style="width: 15%;"></th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody id="">
                                            @foreach(collect($record->subjdata)->unique('subjcode') as $subj)
                                                <tr>
                                                    <td class="p-0"><input type="text" class="form-control form-control-sm input-subjcode" placeholder="Code" value="{{$subj->subjcode}}" disabled/></td>
                                                    <td class="p-0"><input type="number" class="form-control form-control-sm input-subjunit" placeholder="Units" value="{{$subj->subjunit}}" disabled/></td>
                                                    <td class="p-0"><input type="text" class="form-control form-control-sm input-subjdesc" placeholder="Description" value="{{$subj->subjdesc}}" disabled/></td>
                                                    <td class="p-0"><input type="number" class="form-control form-control-sm input-subjgrade" placeholder="Grade" value="{{$subj->subjgrade}}" disabled/></td>
                                                    @if(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'ndsc' || strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'ccsa')
                                                    <td class="p-0"><input type="number" class="form-control form-control-sm input-subjreex" placeholder="Re-Ex" value="{{$subj->subjreex ?? null}}" disabled/></td>
                                                    @endif
                                                    <td class="p-0"><input type="number" class="form-control form-control-sm input-subjcredit" placeholder="Credit" value="{{$subj->subjcredit}}" disabled/></td>
                                                    @if($record->type != 'auto')
                                                    <td class="p-0 text-right"><button type="button" class="btn btn-default btn-sm btn-editdata" data-subjgradeid="{{$subj->id}}"><i class="fa fa-edit text-warning"></i></button><button type="button" class="btn btn-default btn-sm btn-editdata-save" data-subjgradeid="{{$subj->id}}" disabled><i class="fa fa-share text-success"></i></button><button type="button" class="btn btn-default btn-sm btn-delete-subjdata" data-subjgradeid="{{$subj->id}}" disabled><i class="fa fa-trash text-danger"></i></button></td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @if(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'ndsc')
                <div class="col-md-12">
                    <div class="card p-0">
                        <div class="card-body p-0">
                            <div class="col-md-12 p-0">
                                <div class="alert alert-info m-0" role="alert">
                                    Note: Insert texts here. Please separate each line.
                                    </div>
                            </div>
                            @if(count($record->texts)>0)
                                @foreach($record->texts as $eachtext)                            
                                    <div class="row mb-2">
                                        <div class="col-9">
                                            <input type="text" class="form-control form-control-sm" class="texts" value="{{$eachtext->description}}"/>
                                        </div>
                                        <div class="col-3 text-right">
                                            <button type="button" class="btn btn-sm btn-default text-success btn-save-text" data-id="{{$eachtext->id}}" data-sydesc="{{$eachtext->sydesc}}" data-semid="{{$eachtext->semid}}"><i class="fa fa-share"></i> Save changes</button>
                                            <button type="button" class="btn btn-sm btn-default text-danger btn-delete-text" data-id="{{$eachtext->id}}"><i class="fa fa-trash-alt"></i> Remove</button>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                            <div class="col-md-12 text-right mt-2 mb-2">
                                <button type="button" class="btn btn-outline-success btn-addtexts" data-sydesc="{{$record->sydesc}}" data-semid="{{$record->semid}}"><i class="fa fa-plus"></i> Add Text</button>
                            </div>
                            <div class="col-md-12 container-texts"></div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @endif
    
    
    <div class="modal fade"  id="modal-newrecord" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">New Record</h4>
                    <button type="button" id="closeremarks" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <label>School ID</label>
                            <input type="text" class="form-control" id="input-schoolid"/>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <label>School Name</label>
                            <input type="text" class="form-control" id="input-schoolname"/>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <label>School Address</label>
                            <input type="text" class="form-control" id="input-schooladdress"/>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label>Select School Year</label>
                            <select class="form-control select2" id="select-sy">
                                <option value="0">Not on this selection</option>
                                @foreach($schoolyears as $schoolyear)
                                    <option value="{{$schoolyear->syid}}">{{$schoolyear->sydesc}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6" id="div-customsy">
                            <label>Custom School Year</label>
                            <input type="text" class="form-control" id="input-sy"/>
                            <small id="small-inputsy" class="text-danger">*Please fill in custom school year</small>
                        </div>
                        <small id="small-selectsy" class="text-danger col-md-12">*Please select school year. If not on the selection, please specify in the next highlighted field</small>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label>Select Semester</label>
                            <select class="form-control select2" id="select-sem">
                                @foreach(DB::table('semester')->where('deleted','0')->get() as $semester)
                                    <option value="{{$semester->id}}">{{$semester->semester}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <label>Select Course</label>
                            <select class="form-control select2" id="select-course">
                                <option value="0">Not on this selection</option>
                                @foreach($courses as $course)
                                    <option value="{{$course->id}}">{{$course->courseDesc}}</option>
                                @endforeach
                            </select>
                            <small id="small-selectcourse" class="text-danger">*Please select course. If not on the selection, please specify in the next highlighted field </small>
                        </div>
                    </div>
                    <div class="row mb-2" id="div-customcourse">
                        <div class="col-md-12">
                            <label>Custom Course</label>
                            <input type="text" class="form-control" id="input-coursename"/>
                            <small id="small-inputcoursename">*Please fill in custom course</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" id="btn-close-addnewrecord" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btn-submit-addnewrecord">Submit</button>
                </div>
            </div>            
        </div>
    <!-- /.modal-content -->
    </div>
    
    
    
    <div class="modal fade"  id="modal-updaterecord" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">New Record</h4>
                    <button type="button" id="closeremarks" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body" id="container-editrecord">
                    
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" id="btn-close-updaterecord" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btn-submit-updaterecord">Save Changes</button>
                </div>
            </div>            
        </div>
    <!-- /.modal-content -->
    </div>
</div>
<!-- /.modal-dialog -->
<script>
    $('.btn-addtexts').on('click', function(){
        var thiscard = $(this).closest('.card');
        var thiscontainer = thiscard.find('.container-texts');
        var sydesc = $(this).attr('data-sydesc');
        var semid = $(this).attr('data-semid');
        thiscontainer.append(
            '<div class="row mb-2">'+
                '<div class="col-9">'+
                    '<input type="text" class="form-control form-control-sm" class="texts"/>'  +  
                '</div>'+
                '<div class="col-3 text-right">'+
                    '<button type="button" class="btn btn-sm btn-default text-danger btn-remove"><i class="fa fa-times"></i> Remove</button>&nbsp;&nbsp;'+
                    '<button type="button" class="btn btn-sm btn-default text-success btn-save-text" data-id="0" data-sydesc="'+sydesc+'" data-semid="'+semid+'"><i class="fa fa-share"></i> Save</button>'+
                '</div>'+
            '</div>'
        )
    })
    $(document).on('click','.btn-remove', function(){
        $(this).closest('.row').remove()
    })
</script>