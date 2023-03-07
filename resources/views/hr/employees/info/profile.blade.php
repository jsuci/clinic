
<div class="tab-pane fade show active" id="custom-content-above-home" role="tabpanel" aria-labelledby="custom-content-above-home-tab">
    <div id="emp_profile" class="pro-overview tab-pane fade active show">
        <div class="card" style="border: none;">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-10">
                        <button type="button" class="btn btn-sm btn-default btn-block"><strong>Personal Information</strong></button>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-sm btn-warning btn-block" data-toggle="modal" data-target="#edit_profile_info">
                            <i class="fa fa-edit"></i> Edit
                        </button>
                    </div>
                    <div id="edit_profile_info" class="modal custom-modal fade modal-edit-view" role="dialog" style="display: none;" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-info">
                                    <h5 class="modal-title">Personal Information</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    {{-- <form action="/hr/updatepersonalinfo" method="get"> --}}
                                        <div class="row">
                                            <div class="col-md-12">
                                                
                                                <fieldset class="form-group border p-2 mb-2">
                                                    <legend class="w-auto m-0" style="font-size: 12px; font-weight: bold;">Basic Details</legend>
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <small class="text-bold">Title</small>
                                                                <input type="text" class="form-control form-control-sm" name="title" id="profiletitle" value="{{$profileinfo->title}}"/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <small class="text-bold">First Name</small>
                                                                <input type="text" class="form-control form-control-sm" name="fname" id="profilefname" value="{{$profileinfo->firstname}}" required/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <small class="text-bold">Middle Name</small>
                                                                <input type="text" class="form-control form-control-sm" name="mname" id="profilemname" value="{{$profileinfo->middlename}}" required/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <small class="text-bold">Last Name</small>
                                                                <input type="text" class="form-control form-control-sm" name="lname" id="profilelname" value="{{$profileinfo->lastname}}" required/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <div class="form-group">
                                                                <small class="text-bold">Suffix</small>
                                                                <input type="text" class="form-control form-control-sm" name="suffix" id="profilesuffix" value="{{$profileinfo->suffix}}"/>
                                                            </div>
                                                        </div>
                                                        {{-- <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Birth Date</label>
                                                                <div class="cal-icon">
                                                                    @if($profileinfo->dob==null)
                                                                        <input class="form-control datetimepicker" type="date" name="dob"  id="profiledob" required/>
                                                                    @else
                                                                        <input class="form-control datetimepicker" type="date" name="dob"  id="profiledob"value="{{$profileinfo->dob}}" required/>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Civil Status</label>
                                                                <select class="select form-control text-uppercase" name="civilstatusid"  id="profilecivilstatusid" >
                                                                    @if($profileinfo->maritalstatusid==0)
                                                                    @foreach($civilstatus as $cstatus)
                                                                        <option value="{{$cstatus->id}}">{{$cstatus->civilstatus}}</option>
                                                                    @endforeach
                                                                    @else
                                                                    @foreach($civilstatus as $cstatus)
                                                                        <option value="{{$cstatus->id}}" {{$cstatus->id == $profileinfo->maritalstatusid ? 'selected' : ''}}>{{$cstatus->civilstatus}}</option>
                                                                    @endforeach
                                                                    @endif
                                                                </select>
                                                            </div>
                                                        </div> --}}
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <small class="text-bold">Gender</small>
                                                                @if($profileinfo->gender == null)
                                                                <select class="select form-control form-control-sm text-uppercase" name="gender" id="profilegender">
                                                                    <option value="male">Male</option>
                                                                    <option value="female">Female</option>
                                                                </select>
                                                                @else
                                                                    <select class="select form-control form-control-sm text-uppercase" name="gender" id="profilegender" >
                                                                        <option value="male" {{"male" == strtolower($profileinfo->gender) ? 'selected' : ''}}>Male</option>
                                                                        <option value="female" {{"female" == strtolower($profileinfo->gender) ? 'selected' : ''}}>Female</option>
                                                                    </select>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <small class="text-bold">Birth Date</small>
                                                                <div class="cal-icon">
                                                                    @if($profileinfo->dob==null)
                                                                        <input class="form-control datetimepicker form-control-sm" type="date" name="dob"  id="profiledob" required/>
                                                                    @else
                                                                        <input class="form-control datetimepicker form-control-sm" type="date" name="dob"  id="profiledob"value="{{$profileinfo->dob}}" required/>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <small class="text-bold">Civil Status</small>
                                                                <select class="select form-control text-uppercase form-control-sm" name="civilstatusid"  id="profilecivilstatusid" >
                                                                    @if($profileinfo->maritalstatusid==0)
                                                                    @foreach($civilstatus as $cstatus)
                                                                        <option value="{{$cstatus->id}}">{{$cstatus->civilstatus}}</option>
                                                                    @endforeach
                                                                    @else
                                                                    @foreach($civilstatus as $cstatus)
                                                                        <option value="{{$cstatus->id}}" {{$cstatus->id == $profileinfo->maritalstatusid ? 'selected' : ''}}>{{$cstatus->civilstatus}}</option>
                                                                    @endforeach
                                                                    @endif
                                                                </select>
                                                            </div>
                                                        </div> 
                                                        <div class="col-md-5">
                                                            <div class="form-group">
                                                                <small class="text-bold">Religion</small>
                                                                <select class="select form-control text-uppercase form-control-sm" name="religionid"  id="profilereligionid">
                                                                    @if($profileinfo->religionid==null || $profileinfo->religionid==0)
                                                                        @foreach($religions as $religioneach)
                                                                            <option value="{{$religioneach->id}}">{{$religioneach->religionname}}</option>
                                                                        @endforeach
                                                                    @else
                                                                    @foreach($religions as $religioneach)
                                                                        <option value="{{$religioneach->id}}" {{$religioneach->id == $profileinfo->religionid ? 'selected' : ''}}>{{$religioneach->religionname}}</option>
                                                                    @endforeach
                                                                    @endif
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <small class="text-bold">Nationality</small>
                                                                <select class="select form-control text-uppercase form-control-sm" name="nationalityid" id="profilenationalityid">
                                                                    @if($profileinfo->nationalityid == null || $profileinfo->nationalityid == '0')
                                                                        @foreach($nationality as $nationalityeach)
                                                                            <option value="{{$nationalityeach->id}}" {{strtolower($nationalityeach->nationality) == 'filipino' ? 'selected' : ''}}>{{$nationalityeach->nationality}}</option>
                                                                        @endforeach
                                                                    @else
                                                                        @foreach($nationality as $nationalityeach)
                                                                            <option value="{{$nationalityeach->id}}" {{$nationalityeach->id == $profileinfo->nationalityid ? 'selected' : ''}}>{{$nationalityeach->nationality}}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <small class="text-bold">Number of children</small>
                                                                <input type="number" class="form-control form-control-sm" name="numofchildren" id="profilenumofchildren" value="{{$profileinfo->numberofchildren}}" min="0" oninput="this.value = 
                                                                !!this.value && Math.abs(this.value) >= 0 ? Math.abs(this.value) : null">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-7">
                                                            <div class="form-group">
                                                                <small class="text-bold">Employment of Spouse</small>
                                                                <input type="text" class="form-control text-uppercase form-control-sm" name="spouseemployment"  id="profilespouseemployment"value="{{$profileinfo->spouseemployment}}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                                {{-- <table style="table">
                                                    <tr>
                                                        <th rowspan="2">Name:</th>
                                                        <td width="80%;">
                                                            <div class="row">
                                                                <div class="col-md-2">
                                                                    <div class="form-group">
                                                                        <label>Title</label>
                                                                        <input type="text" class="form-control form-control-sm" name="title" id="profiletitle" value="{{$profileinfo->title}}"/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="row">
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label>First Name</label>
                                                                        <input type="text" class="form-control form-control-sm" name="fname" id="profilefname" value="{{$profileinfo->firstname}}" required/>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group">
                                                                        <label>Middle Name</label>
                                                                        <input type="text" class="form-control form-control-sm" name="mname" id="profilemname" value="{{$profileinfo->middlename}}" required/>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label>Last Name</label>
                                                                        <input type="text" class="form-control form-control-sm" name="lname" id="profilelname" value="{{$profileinfo->lastname}}" required/>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <div class="form-group">
                                                                        <label>Suffix</label>
                                                                        <input type="text" class="form-control form-control-sm" name="suffix" id="profilesuffix" value="{{$profileinfo->suffix}}"/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table> --}}
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <small class="text-bold">Present Address</small>
                                                    <input type="text" class="form-control text-uppercase form-control-sm" name="address"  id="profileaddress" value="{{$profileinfo->address}}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <small class="text-bold">Primary Address</small>
                                                    <input type="text" class="form-control text-uppercase form-control-sm" name="address"  id="profileprimaryaddress" value="{{$profileinfo->primaryaddress}}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <small class="text-bold">Email Address</small>
                                                    <input type="email" class="form-control form-control-sm" name="email" value="{{$profileinfo->email}}" id="profileemail">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <small class="text-bold">Contact Number</small>
                                                    <input type="text" class="form-control form-control-sm" id="contactnum" name="contactnum" minlength="13" maxlength="13" data-inputmask-clearmaskonlostfocus="true" value="{{$profileinfo->contactnum}}">
                                                </div>
                                            </div>
                                            {{-- <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Employment of Spouse</label>
                                                    <input type="text" class="form-control text-uppercase" name="spouseemployment"  id="profilespouseemployment"value="{{$profileinfo->spouseemployment}}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Number of children</label>
                                                    <input type="text" class="form-control" name="numofchildren"id="profilenumofchildren" value="{{$profileinfo->numberofchildren}}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Nationality</label>
                                                    <select class="select form-control text-uppercase" name="nationalityid" id="profilenationalityid">
                                                        @if($profileinfo->nationalityid == null || $profileinfo->nationalityid == '0')
                                                            @foreach($nationality as $nationalityeach)
                                                                <option value="{{$nationalityeach->id}}" {{strtolower($nationalityeach->nationality) == 'filipino' ? 'selected' : ''}}>{{$nationalityeach->nationality}}</option>
                                                            @endforeach
                                                        @else
                                                            @foreach($nationality as $nationalityeach)
                                                                <option value="{{$nationalityeach->id}}" {{$nationalityeach->id == $profileinfo->nationalityid ? 'selected' : ''}}>{{$nationalityeach->nationality}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Religion</label>
                                                    <select class="select form-control text-uppercase" name="religionid"  id="profilereligionid">
                                                        @if($profileinfo->religionid==null || $profileinfo->religionid==0)
                                                            @foreach($religions as $religioneach)
                                                                <option value="{{$religioneach->id}}">{{$religioneach->religionname}}</option>
                                                            @endforeach
                                                        @else
                                                        @foreach($religions as $religioneach)
                                                            <option value="{{$religioneach->id}}" {{$religioneach->id == $profileinfo->religionid ? 'selected' : ''}}>{{$religioneach->religionname}}</option>
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div> --}}
                                        </div>
                                        <hr/>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <small>Lincense No.</small>
                                                    <input type="text" name="licenseno" class="form-control form-control-sm" id="profilelicenseno" value="{{$profileinfo->licno}}"/>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <small>Date Hired</small>
                                                    <input type="date" name="datehired" class="form-control form-control-sm" id="profiledatehired" value="{{$profileinfo->datehired}}"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="submit-section">
                                            <button type="button" class="btn btn-primary submit-btn float-right" data-dismiss="modal"  id="updatepersonalinformation">Update</button>
                                        </div>
                                    {{-- </form> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-12">
                        <table class="table" style="font-size: 13px;">
                            <tr>
                                <td class="p-1" style="width: 30%;"><label>Phone</label></td>
                                <td  class="p-1" id="profilecontactnumcontainer">
                                    {{$profileinfo->contactnum}}
                                </td>
                            </tr>
                            <tr>
                                <td class="p-1"><label>Email</label></td>
                                <td class="p-1" id="profileemailnumcontainer">
                                    {{$profileinfo->email}}
                                </td>
                            </tr>
                            <tr>
                                <td class="p-1"><label>Birthday</label></td>
                                <td class="p-1 text-uppercase" id="profiledobcontainer">
                                    {{$profileinfo->dobstring}}
                                </td>
                            </tr>
                            <tr>
                                <td class="p-1"><label>Gender</label></td>
                                <td class="p-1 text-uppercase" id="profilegendercontainer">
                                    {{$profileinfo->gender}}
                                </td>
                            </tr>
                            <tr>
                                <td class="p-1"><label>Present Address</label></td>
                                <td class="p-1 text-uppercase" id="profileaddresscontainer">
                                    {{$profileinfo->address}}
                                </td>
                            </tr>
                            <tr>
                                <td class="p-1"><label>Primary Address</label></td>
                                <td class="p-1 text-uppercase" id="profileprimaryaddresscontainer">
                                    {{$profileinfo->primaryaddress}}
                                </td>
                            </tr>
                            <tr>
                                <td class="p-1"><label>Email Address</label></td>
                                <td class="p-1 text-uppercase" id="profileemailaddresscontainer">
                                    {{$profileinfo->email}}
                                </td>
                            </tr>
                            <tr>
                                <td class="p-1"><label>Nationality</label></td>
                                <td class="p-1 text-uppercase" id="profilenationalitycontainer">
                                    {{$profileinfo->nationality}}
                                </td>
                            </tr>
                            <tr>
                                <td class="p-1"><label>Religion</label></td>
                                <td class="p-1 text-uppercase" id="profilereligioncontainer">
                                    {{$profileinfo->religionname}}
                                </td>
                            </tr>
                            <tr>
                                <td class="p-1"><label>Marital status</label></td>
                                <td class="p-1 text-uppercase" id="profilecivilstatuscontainer">
                                    {{$profileinfo->civilstatus}}
                                </td>
                            </tr>
                            <tr>
                                <td class="p-1"><label>Employment of spouse</label></td>
                                <td class="p-1 text-uppercase" id="profilespouseemploymentcontainer">
                                    {{$profileinfo->spouseemployment}}
                                </td>
                            </tr>
                            <tr>
                                <td class="p-1"><label>No. of children</label></td>
                                <td class="p-1 text-uppercase" id="profilenumofchldrencontainer">
                                    {{$profileinfo->numberofchildren}}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card" style="border: none;">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-10">
                        <button type="button" class="btn btn-sm btn-default btn-block"><strong>Emergency Contact</strong></button>
                        <div id="edit_emergency_contact" class="modal custom-modal fade" role="dialog" style="display: none;" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                            <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-info">
                                        <h5 class="modal-title"><strong>Emergency Contact</strong></h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form {{--action="/hr/updateemergencycontact" method="get"--}}>
                                            {{-- <input type="hidden" class="form-control" name="linkid" value="custom-content-above-home" /> --}}
                                            <label>Name</label>
                                            @if($profileinfo->emercontactname == null)
                                            <input type="text" class="form-control form-control-sm" name="emergencyname" id="emergencyname" required/>
                                            @else
                                            <input type="text" class="form-control form-control-sm" name="emergencyname" id="emergencyname" value="{{$profileinfo->emercontactname}}" required/>
                                            @endif
                                            <br>
                                            <label>Relationship</label>
                                            @if($profileinfo->emercontactname == null)
                                            <input type="text" class="form-control form-control-sm" name="relationship" id="emergencyrelationship" required/>
                                            @else
                                            <input type="text" class="form-control form-control-sm" name="relationship" id="emergencyrelationship" value="{{$profileinfo->emercontactrelation}}" required/>
                                            @endif
                                            <br>
                                            <label>Contact Number</label>
                                            @if($profileinfo->emercontactnum == null)
                                            <input type="text" class="form-control form-control-sm" id="emergencycontactnumber" name="contactnumber" minlength="11" maxlength="13" data-inputmask-clearmaskonlostfocus="true" required/>
                                            @else
                                            <input type="text" class="form-control form-control-sm" id="emergencycontactnumber" name="contactnumber" value="{{$profileinfo->emercontactnum}}" minlength="13" maxlength="13" data-inputmask-clearmaskonlostfocus="true" required/>
                                            @endif
                                            <br>
                                            <div class="submit-section">
                                                <button type="button" class="btn btn-primary submit-btn float-right" data-dismiss="modal" id="updateemergencycontact">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-sm btn-warning btn-block" data-toggle="modal" data-target="#edit_emergency_contact">
                            <i class="fa fa-edit"></i> Edit
                        </button>
                    </div>
                    <div class="col-md-12">
                        <table class="table" style="font-size: 13px;">
                            <tbody>
                                <tr>
                                    <td style="width: 10%; font-weight: bold;">Name</td>
                                    <td style="border-bottom: 1px solid #ddd; width: 30%;">{{$profileinfo->emercontactname}}</td>
                                    <td style="width: 10%; font-weight: bold;">Relationship</td>
                                    <td style="border-bottom: 1px solid #ddd;">{{$profileinfo->emercontactrelation}}</td>
                                    <td style="width: 10%; font-weight: bold;">Phone</td>
                                    <td style="border-bottom: 1px solid #ddd;">{{$profileinfo->emercontactnum}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card" style="border: none;">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-10">
                        <button type="button" class="btn btn-sm btn-default btn-block"><strong>Personal Accounts</strong></button>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-sm btn-warning btn-block" data-toggle="modal" data-target="#edit_accounts">
                            <i class="fa fa-edit"></i> Edit
                        </button>
                        <div id="edit_accounts" class="modal custom-modal fade" role="dialog" style="display: none;" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-info">
                                        <h5 class="modal-title"><strong>Update Personal Accounts</strong></h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form {{--action="/hr/updateaccounts" method="get"--}}>
                                            <div class="addrowaccounts">
                                                @if(count($profileinfo->accounts) == 0)
                                                    <div class="row">
                                                        <div class="col-md-5">
                                                            <label>Description <span class="text-danger">*</span></label>
                                                            <input type="text" name="newaccountdescription[]" class="form-control form-control-sm" required/>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <label>Account # <span class="text-danger">*</span></label>
                                                            <input type="text" name="newaccountnumber[]" class="form-control form-control-sm" required/>
                                                        </div>
                                                        <div class="col-md-2 text-left">
                                                            <label>&nbsp;</label>
                                                            <br>
                                                            <button type="button" class="btn btn-danger removeaddaccountrow"><i class="fa fa-times"></i></button>
                                                        </div>
                                                    </div>
                                                @else
                                                    @foreach($profileinfo->accounts as $employee_account)
                                                        <div class="row">
                                                            <div class="col-md-5">
                                                                <label>Description <span class="text-danger">*</span></label>
                                                                <input type="text" name="oldaccountdescription[]" class="form-control form-control-sm" value="{{$employee_account->accountdescription}}" required/>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <label>Account # <span class="text-danger">*</span></label>
                                                                <input type="text" name="oldaccountnumber[]" class="form-control form-control-sm" value="{{$employee_account->accountnum}}" required/>
                                                            </div>
                                                            <div class="col-md-2 text-left">
                                                                <label>&nbsp;</label>
                                                                <br>
                                                                <input type="hidden" name="oldaccountid[]" class="form-control form-control-sm" value="{{$employee_account->id}}" required/>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                            <br>
                                            <div class="submit-section">
                                                <button type="button" class="btn btn-primary btn-sm float-left addrowaccountsbutton">Add Account</button>
                                                <button type="submit" class="btn btn-primary btn-sm float-right" data-dismiss="modal" id="updateaccounts">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </div>
                    <div class="col-md-12">
                        <table class="table" style="font-size: 13px;">
                            <thead>
                                <tr>
                                    <th style="width: 50%;" >Description</th>
                                    <th style="width: 50%;">Account #</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($profileinfo->accounts) > 0)
                                    @foreach($profileinfo->accounts as $account)
                                        <tr id="{{$account->id}}">
                                            <td>
                                                {{$account->accountdescription}}
                                            </td>
                                            <td>
                                                {{$account->accountnum}}
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm deleteaccount" accountdescription="{{$account->accountdescription}}" accountnumber="{{$account->accountnum}}" ><i class="fa fa-trash text-secondary"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card" style="border: none;">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-10">
                        <button type="button" class="btn btn-sm btn-default btn-block"><strong>Family Information</strong></button>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-sm btn-warning btn-block" data-toggle="modal" data-target="#family_info_modal">
                            <i class="fa fa-edit"></i> Edit
                        </button>
                        <div id="family_info_modal" class="modal custom-modal fade" role="dialog" style="display: none;" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                            <div class="modal-dialog modal-dialog-centered modal-LG" role="document">
                                <div class="modal-content">
                                    <form {{--action="/hr/updatefamilyinfo" method="get"--}}>
                                        <div class="modal-header bg-info">
                                            <h5 class="modal-title"><strong>Family Information</strong></h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">×</span>
                                            </button>
                                        </div>
                                        <div class="modal-body" style="overflow: scroll;">
                                                {{-- <input type="hidden" class="form-control" name="id" value="{{$profileinfo->id}}" required/> --}}
                                                @if(count($profileinfo->familyinfo)==0)
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 30%;">Name</th>
                                                                <th>Relationship</th>
                                                                <th style="width: 20%;">Contact Number</th>
                                                                <th style="width: 5%;"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="familytbody">
                                                            <tr>
                                                                <td class="p-0 text-uppercase"><input class="form-control text-uppercase" type="text" name="familyname[]" required/></td>
                                                                <td class="p-0 text-uppercase"><input class="form-control text-uppercase" type="text" name="familyrelation[]"/></td>
                                                                <td class="p-0 text-uppercase"><input class="form-control text-uppercase familycontactnum" type="text" name="familynum[]" minlength="13" maxlength="13" data-inputmask-clearmaskonlostfocus="true"/></td>
                                                                <td class="p-0 bg-danger" style="vertical-align: middle;"><button type="button" class="btn btn-sm btn-danger btn-block deleterow"><i class="fa fa-times"></i></button></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                @else
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 30%;">Name</th>
                                                                <th>Relationship</th>
                                                                <th style="width: 20%;">Contact Number</th>
                                                                <th style="width: 5%;"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="familytbody">
                                                            @foreach($profileinfo->familyinfo as $family)
                                                                <tr>
                                                                    <td class="p-0"><input class="form-control" type="hidden" name="oldfamilyid[]" value="{{$family->id}}"/><input class="form-control" type="text" name="oldfamilyname[]" value="{{$family->famname}}" required/></td>
                                                                    <td class="p-0"><input class="form-control" type="text" name="oldfamilyrelation[]" value="{{$family->famrelation}}" /></td>
                                                                    <td class="p-0"><input class="form-control familycontactnum" type="text" name="oldfamilynum[]" value="{{$family->contactnum}}" minlength="13" maxlength="13" data-inputmask-clearmaskonlostfocus="true"/></td>
                                                                    <td class="p-0" style="vertical-align: middle;">&nbsp;</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                @endif
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-12">
                                            <span class="float-left ml-3">
                                            <button type="button" class="btn btn-sm btn-info pr-4 pl-4 addrow"><i class="fa fa-plus"></i>&nbsp; Add More</button>
                                            </span>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="modal-footer justify-content-between">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            <button type="button" class="btn btn-primary" id="updatefamilyinfo" data-dismiss="modal" >Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div> 
                    </div>
                    @if(session()->has('messageUpdated'))
                        <div class="col-md-12">
                                <div class="alert alert-success alert-dismissible col-12">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h5><i class="icon fas fa-check"></i> Alert!</h5>
                                    {{ session()->get('messageUpdated') }}
                                </div>
                        </div>
                    @endif
                    <div class="col-md-12">
                        <table class="table" style="font-size: 13px;">
                            <thead>
                                <tr>
                                    <th style="width: 50%;" >Name</th>
                                    <th>Phone</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($profileinfo->familyinfo)==0)
                                @else
                                    @foreach($profileinfo->familyinfo as $familyinfo)
                                        <tr>
                                            <td class="text-uppercase">
                                                {{$familyinfo->famname}}
                                                <br>
                                                <span class="text-muted">{{$familyinfo->famrelation}}</span>
                                            </td>
                                            <td class="text-uppercase">{{$familyinfo->contactnum}}</td>
                                            <td class="float-right">
                                                <button type="button" class="btn btn-sm deletefamilymember" familyid="{{$familyinfo->id}}" familymembername="{{$familyinfo->famname}}" id="{{$profileinfo->id}}">
                                                    <i class="fas fa-trash text-secondary"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card" style="border: none;">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-10">
                        <button type="button" class="btn btn-sm btn-default btn-block"><strong>Educational Background</strong></button>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-sm btn-success btn-block" data-toggle="modal" data-target="#profile_addeducbg">
                            <i class="fa fa-plus"></i> Add
                        </button>
                        <div id="profile_addeducbg" class="modal custom-modal fade" role="dialog" style="display: none;" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-info">
                                        <h5 class="modal-title"><strong>Educational Background</strong></h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row mb-2">
                                            <div class="col-md-12">
                                                <label>School Year</label>
                                                <input type="text" class="form-control form-control-sm" id="input-educbg-sy"/>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-md-12">
                                                <label>University</label>
                                                <input type="text" class="form-control form-control-sm" id="input-educbg-university"/>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-md-12">
                                                <label>Address</label>
                                                <input type="text" class="form-control form-control-sm" id="input-educbg-address"/>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-md-12">
                                                <label>Course</label>
                                                <input type="text" class="form-control form-control-sm" id="input-educbg-course"/>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-md-12">
                                                <label>Major</label>
                                                <input type="text" class="form-control form-control-sm" id="input-educbg-major"/>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-md-12">
                                                <label>Awards</label>
                                                <input type="text" class="form-control form-control-sm" id="input-educbg-awards"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer justify-content-between">
                                      <button type="button" class="btn btn-default" data-dismiss="modal" id="btn-close-addeducbg">Close</button>
                                      <button type="button" class="btn btn-primary" id="btn-submit-addeducbg">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </div>
                    <div class="col-md-12">
                        @if(count($profileinfo->educationalbackground)>0)
                            <table class="table" style="font-size: 13px;">
                                <thead>
                                    <tr>
                                        <th style="width: 15%;">School Year</th>
                                        {{-- <th>University</th>
                                        <th>Address</th>
                                        <th>Course</th>
                                        <th>Major</th>
                                        <th>Awards</th> --}}
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($profileinfo->educationalbackground as $educinfo)
                                        <tr class="tr-each-educinfo">
                                            <td class="p-0"><input type="text" class="form-control form-control-sm m-0 input-educbg-sy" value="{{$educinfo->schoolyear}}"/></td>
                                            <td class="p-0">
                                                <table class="table">
                                                    <tr>
                                                        <td>Course</td>
                                                        <td class="p-0">
                                                        <input type="text" class="form-control form-control-sm m-0 input-educbg-course" style="border: none;" value="{{$educinfo->coursetaken}}"/>
                                                        <td>Major</td>
                                                        <td class="p-0">
                                                        <input type="text" class="form-control form-control-sm m-0 input-educbg-major" style="border: none;" value="{{$educinfo->major}}"/>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Awards</td>
                                                        <td class="p-0" colspan="4">
                                                        <input type="text" class="form-control form-control-sm m-0 input-educbg-awards" style="border: none;" value="{{$educinfo->awards}}"/>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>University</td>
                                                        <td class="p-0">
                                                        <input type="text" class="form-control form-control-sm m-0 input-educbg-schoolname" style="border: none;" value="{{$educinfo->schoolname}}"/>
                                                        <td>Address</td>
                                                        <td class="p-0">
                                                        <input type="text" class="form-control form-control-sm m-0 input-educbg-schooladdress" style="border: none;" value="{{$educinfo->schooladdress}}"/>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-right" colspan="4">
                                                            <button type="button" class="btn btn-sm btn-default btn-edit-educinfo"><i class="fa fa-edit"></i> Edit</button>
                                                            <button type="button" class="btn btn-sm btn-default btn-update-educinfo" data-id="{{$educinfo->id}}"><i class="fa fa-share"></i> Save Changes</button>
                                                            <button type="button" class="btn btn-sm btn-default btn-delete-educinfo" data-id="{{$educinfo->id}}"><i class="fa fa-trash-alt"></i> Delete</button>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="card" style="border: none;">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-10">
                        <button type="button" class="btn btn-sm btn-default btn-block"><strong>Work Experience</strong></button>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-sm btn-success btn-block" data-toggle="modal" data-target="#experience_info">
                            <i class="fa fa-plus"></i> Add
                        </button>
                        <div id="experience_info" class="modal custom-modal fade" role="dialog" style="display: none;" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-info">
                                        <h5 class="modal-title"><strong>Work Experience</strong></h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row mb-2">
                                            <div class="col-md-12">
                                                <label>Company Name</label>
                                                <input type="text" class="form-control form-control-sm" id="input-workex-companyname"/>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-md-12">
                                                <label>Location</label>
                                                <input type="text" class="form-control form-control-sm" id="input-workex-location"/>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-md-12">
                                                <label>Job Position</label>
                                                <input type="text" class="form-control form-control-sm" id="input-workex-jobposition"/>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-md-12">
                                                <label>Period from</label>
                                                <input type="date" class="form-control form-control-sm" id="input-workex-periodfrom"/>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-md-12">
                                                <label>Period to</label>
                                                <input type="date" class="form-control form-control-sm" id="input-workex-periodto"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer justify-content-between">
                                      <button type="button" class="btn btn-default" data-dismiss="modal" id="btn-close-addworexperience">Close</button>
                                      <button type="button" class="btn btn-primary" id="btn-submit-addworexperience">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </div>
                    <div class="col-md-12">
                        @if(count($profileinfo->experiences)>0)
                            <table class="table" style="font-size: 13px;">
                                <thead>
                                    <tr>
                                        <th>Company</th>
                                        <th>Job Position</th>
                                        <th style="width: 15%;">Period</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($profileinfo->experiences as $workexpe)
                                        <tr class="tr-each-workexpe tr-each-workexpe{{$workexpe->id}}">
                                            <td class="p-0 pl-5">
                                                <table class="table">
                                                    <tr>
                                                        <td>Name</td>
                                                        <td class="p-0">
                                                        <input type="text" class="form-control form-control-sm m-0 input-workexpe-companyname" style="border: none;" value="{{$workexpe->companyname}}"/>
                                                    </tr>
                                                    <tr>
                                                        <td>Address</td>
                                                        <td class="p-0">
                                                        <input type="text" class="form-control form-control-sm m-0 input-workexpe-companyaddress" style="border: none;" value="{{$workexpe->companyaddress}}"/>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td class="p-0">
                                                <input type="text" class="form-control form-control-sm m-0 input-workexpe-position" style="border: none;" value="{{$workexpe->position}}"/>
                                            </td>
                                            <td class="p-0">
                                                <table class="table">
                                                    <tr>
                                                        <td>From</td>
                                                        <td class="p-0">
                                                        <input type="date" class="form-control form-control-sm m-0 input-workexpe-periodfrom" style="border: none;" value="{{$workexpe->periodfrom}}"/>
                                                    </tr>
                                                    <tr>
                                                        <td>To</td>
                                                        <td class="p-0">
                                                        <input type="date" class="form-control form-control-sm m-0 input-workexpe-periodto" style="border: none;" value="{{$workexpe->periodto}}"/>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            
                                            <td class="text-right" colspan="3">
                                                <button type="button" class="btn btn-sm btn-default btn-edit-workexpe" data-id="{{$workexpe->id}}"><i class="fa fa-edit"></i> Edit</button>
                                                <button type="button" class="btn btn-sm btn-default btn-update-workexpe" data-id="{{$workexpe->id}}"><i class="fa fa-share"></i> Save</button>
                                                <button type="button" class="btn btn-sm btn-default btn-delete-workexpe" data-id="{{$workexpe->id}}"><i class="fa fa-trash-alt"></i> Delete</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).on('click','#updatepersonalinformation', function(){
            var profiletitle = $('#profiletitle').val();
            var profilesuffix = $('#profilesuffix').val();
            var profilefname = $('#profilefname').val();
            var profilemname = $('#profilemname').val();
            var profilelname = $('#profilelname').val();
            var profilecivilstatusid = $('#profilecivilstatusid').val();
            var profiledob = $('#profiledob').val();
            var profilegender = $('#profilegender').val();
            var profileaddress = $('#profileaddress').val();
            var profileprimaryaddress = $('#profileprimaryaddress').val();
            var profileemail = $('#profileemail').val();
            var contactnum = $('#contactnum').val();
            var profilespouseemployment = $('#profilespouseemployment').val();
            var profilenumofchildren = $('#profilenumofchildren').val();
            var profilenationalityid = $('#profilenationalityid').val();
            var profilereligionid = $('#profilereligionid').val();
            var profiledatehired = $('#profiledatehired').val();
            var profilelicenseno = $('#profilelicenseno').val();
            $.ajax({
                url: "/hr/employees/profile/tabprofile/updatepersonalinfo",
                type: "get",
                data: {
                    employeeid: '{{$profileinfo->id}}',
                    profiletitle:profiletitle,
                    profilesuffix:profilesuffix,
                    profilefname:profilefname,
                    profilemname:profilemname,
                    profilelname:profilelname,
                    profilecivilstatusid:profilecivilstatusid,
                    profiledob:profiledob,
                    profilegender:profilegender,
                    profileaddress:profileaddress,
                    profileprimaryaddress:profileprimaryaddress,
                    profileemail:profileemail,
                    contactnum:contactnum,
                    profilespouseemployment:profilespouseemployment,
                    profilenumofchildren:profilenumofchildren,
                    profilenationalityid:profilenationalityid,
                    profilereligionid:profilereligionid,
                    profiledatehired:profiledatehired,
                    profilelicenseno:profilelicenseno
                },
                success: function (data) {
                    toastr.success('Personal Information updated successfully!')
                    $('#licno-text').text(profilelicenseno)
                    $('#custom-content-above-tabContent').empty()
                    $('#custom-content-above-profile-tab').click()
                }
            });
      })
    $(document).on('click','#updateemergencycontact', function(){
        var emergencyname = $('#emergencyname').val()
        var emergencyrelationship = $('#emergencyrelationship').val()
        var emergencycontactnumber = $('#emergencycontactnumber').val()
        $.ajax({
            url: "/hr/employees/profile/tabprofile/updateemercon",
            type: "get",
            data: {
                employeeid: '{{$profileinfo->id}}',
                emergencyname:emergencyname,
                emergencyrelationship:emergencyrelationship,
                emergencycontactnumber:emergencycontactnumber
            },
            success: function (data) {
                toastr.success('Emergency Contact updated successfully!')
                $('#custom-content-above-tabContent').empty()
                $('#custom-content-above-profile-tab').click()
            }
        });
    })
    $(document).on('click','#updatedesignationid', function(){
        var departmentid = $('#departmentid').val()
        var designationid = $('#designationid').val()
        $.ajax({
            url: "/hr/updatedesignation",
            type: "get",
            data: {
                employeeid: '{{$profileinfo->id}}',
                departmentid:departmentid,
                designationid:designationid
            },
            success: function (data) {
                toastr.success('Emergency Contact updated successfully!')
                $('#custom-content-above-tabContent').empty()
                $('#custom-content-above-profile-tab').click()
            }
        });
    })
    

      var clickedaccountrows = 1;

    $(document).on('click','.addrowaccountsbutton', function(){

        $('.addrowaccounts')
        .prepend(
            '<div class="row">'+
                '<div class="col-md-5">'+
                    '<label>Description <span class="text-danger">*</span></label>'+
                    '<input type="text" name="newaccountdescription[]" class="form-control form-control-sm" required/>'+
                '</div>'+
                '<div class="col-md-5">'+
                    '<label>Account # <span class="text-danger">*</span></label>'+
                    '<input type="text" name="newaccountnumber[]" class="form-control form-control-sm" required/>'+
                '</div>'+
                '<div class="col-md-2 text-left">'+
                    '<label>&nbsp;</label>'+
                    '<br>'+
                    '<button type="button" class="btn btn-danger removeaddaccountrow"><i class="fa fa-times"></i></button>'+
                '</div>'+
                '<hr class="col-md-12"/>'+
            '</div>'
        )

        clickedaccountrows+=1;

    });
    $(document).on('click','#updateaccounts', function(){
        var newdescriptions = [];
        var oldaccountdescription = [];
        var newaccountnumber = [];
        var oldaccountnumber = [];
        var oldaccountid = [];
        var emptyelements = [];
        $('input[name="newaccountdescription[]"]').each(function(){
            $(this).css('border','1px solid #ddd')
            if($(this).val().replace(/^\s+|\s+$/g, "").length == 0)
            {
                emptyelements.push($(this))
            }else{

                newdescriptions.push($(this).val())
            }
        })
        $('input[name="newaccountnumber[]"]').each(function(){
            $(this).css('border','1px solid #ddd')
            if($(this).val().replace(/^\s+|\s+$/g, "").length == 0)
            {
                emptyelements.push($(this))
            }else{

                newaccountnumber.push($(this).val())
            }
        })
        if($(this).closest('form').find('input[name="oldaccountdescription[]"]').length > 0)
        {
            $('input[name="oldaccountdescription[]"]').each(function(){
                $(this).css('border','1px solid #ddd')
                if($(this).val().replace(/^\s+|\s+$/g, "").length == 0)
                {
                    emptyelements.push($(this))
                }else{

                    oldaccountdescription.push($(this).val())
                }
            })
            $('input[name="oldaccountnumber[]"]').each(function(){
                $(this).css('border','1px solid #ddd')
                if($(this).val().replace(/^\s+|\s+$/g, "").length == 0)
                {
                    emptyelements.push($(this))
                }else{

                    oldaccountnumber.push($(this).val())
                }
            })
            $('input[name="oldaccountid[]"]').each(function(){
                $(this).css('border','1px solid #ddd')
                if($(this).val().replace(/^\s+|\s+$/g, "").length == 0)
                {
                    emptyelements.push($(this))
                }else{

                    oldaccountid.push($(this).val())
                }
            })
        }
        
        if(emptyelements.length == 0)
        {
            $.ajax({
                url: "/hr/employees/profile/tabprofile/updateaccounts",
                type: "GET",
                data: {
                    employeeid:   '{{$profileinfo->id}}',
                    newdescriptions:newdescriptions,
                    newaccountnumber:newaccountnumber,
                    oldaccountdescription:oldaccountdescription,
                    oldaccountnumber:oldaccountnumber,
                    oldaccountid:oldaccountid
                    },
                success: function (data) {
                    // $('#profilepic').attr('src',data)
                    toastr.success('Accounts updated successfully!')
                    $('#custom-content-above-tabContent').empty()
                    $('#custom-content-above-profile-tab').click()
                }
            });
        }else{
            $.each(emptyelements,function(){
                $(this).css('border','1px solid red')
            })
        }
          
      })
    $(document).on('click','.deleteaccount',function() {
        var accountid       = $(this).closest('tr').attr('id');
        var accountdesc     = $(this).attr('accountdescription');
        var accountnum      = $(this).attr('accountnumber');
        var thistr          = $(this).closest('tr');
        Swal.fire({
            title: 'Are you sure you want to delete the selected account info?',
            // text: "You won't be able to revert this!",
            html:
                "Account Description: <strong>" + accountdesc + '</strong>'+
                '<br>'+ 
                "Account #: <strong>" + accountnum + '</strong>'+
                '<br>'+
                "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            allowOutsideClick: false
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: '/hr/employees/profile/tabprofile/deleteaccount',
                    type:"GET",
                    dataType:"json",
                    data:{
                        accountid: accountid,
                    },
                    // headers: { 'X-CSRF-TOKEN': token },,
                    success: function(data){
                        if(data == 1)
                        {
                            thistr.remove();
                            toastr.success('Account deleted successfully!')
                            $('#custom-content-above-tabContent').empty()
                            $('#custom-content-above-profile-tab').click()
                        }else{
                            toastr.error('Soemthng went wrong!')
                        }
                    }
                })
            }
        })
    });
      
        
      $('.addrow').on('click', function(){
            $('#familytbody').append(
                '<tr>'+
                    '<td class="p-0"><input class="form-control text-uppercase" type="text" name="familyname[]" required/></td>'+
                    '<td class="p-0"><input class="form-control text-uppercase" type="text" name="familyrelation[]"/></td>'+
                    // '<td class="p-0"><input class="form-control text-uppercase" type="date" name="familydob[]"/></td>'+
                    '<td class="p-0"><input class="form-control text-uppercase familycontactnum" type="text" minlength="13" maxlength="13" data-inputmask-clearmaskonlostfocus="true" name="familynum[]"/></td>'+
                    '<td class="p-0 bg-danger" style="vertical-align: middle;"><button type="button" class="btn btn-sm btn-danger btn-block deleterow"><i class="fa fa-times"></i></button></td>'+
                '</tr>'
            );
            $(".familycontactnum").inputmask({mask: "9999-999-9999"});
        });
        $(document).on('click','.deleterow', function(){
            $(this).closest('tr').remove();
        })
        

        $(document).on('click','#updatefamilyinfo', function(){
            var thiselement = $(this);
            var familyname = [];
            var familyrelation = [];
            var familynum = [];
            var oldid = [];
            var oldfamilyname = [];
            var oldfamilyrelation = [];
            var oldfamilynum = [];
            var emptyelements = [];
        
            $('input[name="familyname[]"]').each(function(){
                $(this).css('border','1px solid #ddd')
                if($(this).val().replace(/^\s+|\s+$/g, "").length == 0)
                {
                    emptyelements.push($(this))
                }else{

                    familyname.push($(this).val())
                }
            })
            $('input[name="familyrelation[]"]').each(function(){
                $(this).css('border','1px solid #ddd')
                if($(this).val().replace(/^\s+|\s+$/g, "").length == 0)
                {
                    emptyelements.push($(this))
                }else{

                    familyrelation.push($(this).val())
                }
            })
            $('input[name="familynum[]"]').each(function(){
                $(this).css('border','1px solid #ddd')
                if($(this).val().replace(/^\s+|\s+$/g, "").length == 0)
                {
                    emptyelements.push($(this))
                }else{

                    familynum.push($(this).val())
                }
            })

            if($(this).closest('form').find('input[name="oldfamilyid[]"]').length > 0)
            {
                $('input[name="oldfamilyid[]"]').each(function(){
                    $(this).css('border','1px solid #ddd')
                    if($(this).val().replace(/^\s+|\s+$/g, "").length == 0)
                    {
                        emptyelements.push($(this))
                    }else{

                        oldid.push($(this).val())
                    }
                })
                $('input[name="oldfamilyname[]"]').each(function(){
                    $(this).css('border','1px solid #ddd')
                    if($(this).val().replace(/^\s+|\s+$/g, "").length == 0)
                    {
                        emptyelements.push($(this))
                    }else{

                        oldfamilyname.push($(this).val())
                    }
                })
                $('input[name="oldfamilyrelation[]"]').each(function(){
                    $(this).css('border','1px solid #ddd')
                    if($(this).val().replace(/^\s+|\s+$/g, "").length == 0)
                    {
                        emptyelements.push($(this))
                    }else{

                        oldfamilyrelation.push($(this).val())
                    }
                })
                $('input[name="oldfamilynum[]"]').each(function(){
                    $(this).css('border','1px solid #ddd')
                    if($(this).val().replace(/^\s+|\s+$/g, "").length == 0)
                    {
                        emptyelements.push($(this))
                    }else{

                        oldfamilynum.push($(this).val())
                    }
                })
            }
            if(emptyelements.length == 0)
            {
                
                $.ajax({
                    url: "/hr/employees/profile/tabprofile/updatefamilyinfo",
                    type: "GET",
                    data: {
                        employeeid:   '{{$profileinfo->id}}',
                        familyname:familyname,
                        familyrelation:familyrelation,
                        familynum:familynum,
                        oldid:oldid,
                        oldfamilyname:oldfamilyname,
                        oldfamilyrelation:oldfamilyrelation,
                        oldfamilynum:oldfamilynum
                        },
                    success: function (data) {
                        // $('#profilepic').attr('src',data)
                        toastr.success('Accounts updated successfully!')
                        $('#custom-content-above-tabContent').empty()
                        $('#custom-content-above-profile-tab').click()
                    }
                });
            }else{
                $.each(emptyelements,function(){
                    $(this).css('border','1px solid red')
                })
            }
      })
        $('.deletefamilymember').click(function() {
            var familymemberid      = $(this).attr('familyid');
            var familymembername    = $(this).attr('familymembername');
            var employeeid          = $(this).attr('id');
            
            Swal.fire({
                title: 'Are you sure you want to delete this family member?',
                // text: "You won't be able to revert this!",
                html:
                    "Family member: <strong>" + familymembername + '</strong>'+
                    '<br>'+
                    "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                allowOutsideClick: false
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: '/hr/employees/profile/tabprofile/deletefamilyinfo',
                        type:"GET",
                        dataType:"json",
                        data:{
                            familymemberid: familymemberid,
                            employeeid: employeeid
                        },
                        // headers: { 'X-CSRF-TOKEN': token },,
                        complete: function(){
                            
                            toastr.success('Accounts updated successfully!')
                            $('#custom-content-above-tabContent').empty()
                            $('#custom-content-above-profile-tab').click()
                        }
                    })
                }
            })
        });
        $('#btn-submit-addeducbg').on('click', function(){
            var sy              = $('#input-educbg-sy').val();
            var university      = $('#input-educbg-university').val();
            var address         = $('#input-educbg-address').val();
            var course          = $('#input-educbg-course').val();
            var major           = $('#input-educbg-major').val();
            var awards          = $('#input-educbg-awards').val();
            if(university.replace(/^\s+|\s+$/g, "").length == 0)
            {
                $('#input-educbg-university').css('border','1px solid red')
                toastr.warning('Please fill in required field!')
            }else{
                    $.ajax({
                        url: '/hr/employees/profile/tabprofile/addeducationinfo',
                        type:"GET",
                        dataType:"json",
                        data:{
                            employeeid  :   '{{$profileinfo->id}}',
                            sy          :   sy,
                            university  :   university,
                            address     :   address,
                            course      :   course,
                            major       :   major,
                            awards      :   awards
                        },
                        // headers: { 'X-CSRF-TOKEN': token },,
                        complete: function(){
                            toastr.success('Added Successfully!')
                            $('#btn-close-addeducbg').click()
                            $('.modal-backdrop').remove()
                            $('body').removeClass('modal-open')
                            $('#custom-content-above-tabContent').empty()
                            $('#custom-content-above-profile-tab').click()
                        }
                    })
            }
        })
    $(document).ready(function(){
        $('.tr-each-educinfo').find('input').prop('disabled',true)
        $('.btn-update-educinfo').prop('disabled',true)
        $('.btn-edit-educinfo').on('click', function(){
            $(this).closest('.tr-each-educinfo').find('input').prop('disabled',false)
            $(this).closest('tr').find('.btn-update-educinfo').prop('disabled',false)
        })
        $('.btn-update-educinfo').on('click', function(){
            var id = $(this).attr('data-id');
            var sy = $(this).closest('.tr-each-educinfo').find('.input-educbg-sy').val();
            var course = $(this).closest('.tr-each-educinfo').find('.input-educbg-course').val();
            var major = $(this).closest('.tr-each-educinfo').find('.input-educbg-major').val();
            var awards = $(this).closest('.tr-each-educinfo').find('.input-educbg-awards').val();
            var schoolname = $(this).closest('.tr-each-educinfo').find('.input-educbg-schoolname').val();
            var schooladdress = $(this).closest('.tr-each-educinfo').find('.input-educbg-schooladdress').val();

            if(schoolname.replace(/^\s+|\s+$/g, "").length == 0)
            {
                $(this).closest('.tr-each-educinfo').find('.input-educbg-schoolname').css('border','1px solid red')
                toastr.warning('Please fill in required field!')
            }else{
                $.ajax({
                    url: '/hr/employees/profile/tabprofile/updateeducationinfo',
                    type:"GET",
                    dataType:"json",
                    data:{
                        id          :   id,
                        sy          :   sy,
                        course  :   course,
                        major     :   major,
                        awards      :   awards,
                        schoolname       :   schoolname,
                        schooladdress      :   schooladdress
                    },
                    // headers: { 'X-CSRF-TOKEN': token },,
                    complete: function(){
                        toastr.success('Updated Successfully!')
                        $('#custom-content-above-tabContent').empty()
                        $('#custom-content-above-profile-tab').click()
                    }
                })
            }
        })
        $('.btn-delete-educinfo').on('click', function(){
            var id = $(this).attr('data-id');
            Swal.fire({
                title: 'Are you sure you want to delete this info?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                allowOutsideClick: false
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: '/hr/employees/profile/tabprofile/deleteeducationinfo',
                        type:"GET",
                        dataType:"json",
                        data:{
                                id          :   id
                        },
                        // headers: { 'X-CSRF-TOKEN': token },,
                        complete: function(){
                            toastr.success('Deleted successfully!')
                            $('#custom-content-above-tabContent').empty()
                            $('#custom-content-above-profile-tab').click()
                        }
                    })
                }
            })
        })
        $('.tr-each-workexpe').find('input').prop('disabled',true)
        $('.btn-update-workexpe').prop('disabled',true)
        $('.btn-edit-workexpe').on('click', function(){
            var id = $(this).attr('data-id');
            var currenttr = $(this).closest('tr');
            var thistr = $(this).closest('tbody').find('.tr-each-workexpe'+id);
            thistr.find('input').prop('disabled',false)
            currenttr.find('.btn-update-workexpe').prop('disabled',false)
        })
        $('#btn-submit-addworexperience').on('click', function(){
            var companyname = $('#input-workex-companyname').val();
            var location = $('#input-workex-location').val();
            var jobposition = $('#input-workex-jobposition').val();
            var periodfrom = $('#input-workex-periodfrom').val();
            var periodto = $('#input-workex-periodto').val();

            if(companyname.replace(/^\s+|\s+$/g, "").length == 0)
            {
                $('#input-workex-companyname').css('border','1px solid red')
                toastr.warning('Please fill in required field!')
            }else{
                $.ajax({
                    url: '/hr/employees/profile/tabprofile/addworexperience',
                    type:"GET",
                    dataType:"json",
                    data:{
                        employeeid      :   '{{$profileinfo->id}}',
                        companyname     :   companyname,
                        location        :   location,
                        jobposition     :   jobposition,
                        periodfrom      :   periodfrom,
                        periodto        :   periodto
                    },
                    complete: function(){
                        toastr.success('Updated Successfully!')
                        $('#btn-close-addworexperience').click()
                        $('.modal-backdrop').remove()
                        $('body').removeClass('modal-open')
                        $('#custom-content-above-tabContent').empty()
                        $('#custom-content-above-profile-tab').click()
                    }
                })
            }
        })
        $('.btn-update-workexpe').on('click', function(){
            var id = $(this).attr('data-id');
            var thistr = $(this).closest('tbody').find('.tr-each-workexpe'+id);
            var companyname = thistr.find('.input-workexpe-companyname').val();
            var companyaddress = thistr.find('.input-workexpe-companyaddress').val();
            var position = thistr.find('.input-workexpe-position').val();
            var periodfrom = thistr.find('.input-workexpe-periodfrom').val();
            var periodto = thistr.find('.input-workexpe-periodto').val();

            if(companyname.replace(/^\s+|\s+$/g, "").length == 0)
            {
                thistr.find('.input-workexpe-companyname').css('border','1px solid red')
                toastr.warning('Please fill in required field!')
            }else{
                $.ajax({
                    url: '/hr/employees/profile/tabprofile/updateworkexperience',
                    type:"GET",
                    dataType:"json",
                    data:{
                        id              :   id,
                        companyname     :   companyname,
                        location        :   companyaddress,
                        jobposition     :   position,
                        periodfrom      :   periodfrom,
                        periodto        :   periodto
                    },
                    // headers: { 'X-CSRF-TOKEN': token },,
                    complete: function(){
                        toastr.success('Updated Successfully!')
                        $('#custom-content-above-tabContent').empty()
                        $('#custom-content-above-profile-tab').click()
                    }
                })
            }
        })
        $('.btn-delete-workexpe').on('click', function(){
            var id = $(this).attr('data-id');
            Swal.fire({
                title: 'Are you sure you want to delete this info?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                allowOutsideClick: false
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                    url: '/hr/employees/profile/tabprofile/deleteworkexperience',
                        type:"GET",
                        dataType:"json",
                        data:{
                                id          :   id
                        },
                        // headers: { 'X-CSRF-TOKEN': token },,
                        complete: function(){
                            toastr.success('Deleted successfully!')
                            $('#custom-content-above-tabContent').empty()
                            $('#custom-content-above-profile-tab').click()
                        }
                    })
                }
            })
        })
    })
</script>
