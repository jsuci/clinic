
  <aside class="main-sidebar sidebar-dark-primary elevation-4 asidebar">
    <div class="ckheader">
        <a href="#" class="brand-link sidehead">
            <img src="{{asset(DB::table('schoolinfo')->first()->picurl)}}" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">{{DB::table('schoolinfo')->first()->abbreviation}}</span>
        </a>
    </div>
	@php
            $hr_profile = Db::table('teacher')
                ->select(
                    'teacher.id',
                    'teacher.lastname',
                    'teacher.middlename',
                    'teacher.firstname',
                    'teacher.suffix',
                    'teacher.licno',
                    'teacher.tid',
                    'teacher.deleted',
                    'teacher.isactive',
                    'teacher.picurl',
                    'usertype.utype',
                    'usertype.refid'
                    )
                ->join('usertype','teacher.usertypeid','=','usertype.id')
                ->where('teacher.userid', auth()->user()->id)
                ->first();
                
            $hr_info = Db::table('employee_personalinfo')
                ->select(
                    'employee_personalinfo.id as employee_personalinfoid',
                    'employee_personalinfo.nationalityid',
                    'employee_personalinfo.religionid',
                    'employee_personalinfo.dob',
                    'employee_personalinfo.gender',
                    'employee_personalinfo.address',
                    'employee_personalinfo.contactnum',
                    'employee_personalinfo.email',
                    'employee_personalinfo.maritalstatusid',
                    'employee_personalinfo.spouseemployment',
                    'employee_personalinfo.numberofchildren',
                    'employee_personalinfo.emercontactname',
                    'employee_personalinfo.emercontactrelation',
                    'employee_personalinfo.emercontactnum',
                    'employee_personalinfo.departmentid',
                    'employee_personalinfo.designationid',
                    'employee_personalinfo.date_joined'
                    )
                ->where('employee_personalinfo.employeeid',$hr_profile->id)
                ->get();
                $number = rand(1,3);
                if(count($hr_info)==0){
                    $avatar = 'assets/images/avatars/unknown.png';
                }
                else{
                    if(strtoupper($hr_info[0]->gender) == 'FEMALE'){
                        $avatar = 'avatar/T(F) '.$number.'.png';
                    }
                    else{
                        $avatar = 'avatar/T(M) '.$number.'.png';
                    }
                }
                $refid = DB::table('usertype')
                    ->where('id', Session::get('currentPortal'))
                    ->first()->refid;
                    
                $countapproval = DB::table('hr_leavesappr')
                    ->where('appuserid', auth()->user()->id)
                    ->where('deleted','0')
                    ->count();

                $countundertimeapproval = DB::table('undertime_approval')
                    ->where('appruserid', auth()->user()->id)
                    ->where('deleted','0')
                    ->count();
                $isDepartmentHead = false;

                $checkdepthead = DB::table('hr_departmentheads')
                    ->where('deptheadid', $hr_profile->id)
                    ->where('deleted','0')
                    ->first();

                if($checkdepthead)
                {
                    $isDepartmentHead = true;
                }
                $isinSignatory = false;

                $checksign = DB::table('sait_leavesignatories')
                    ->where('userid',auth()->user()->id)
                    ->where('deleted','0')
                    ->first();

                if($checksign)
                {
                    $isinSignatory = true;
                }
            @endphp
    <!-- Sidebar -->
    <div class="sidebar">
     
					<div class="row">
						<div class="col-md-12">
						  <div class="text-center">
							<img class="profile-user-img img-fluid img-circle" src="{{asset($hr_profile->picurl)}}" onerror="this.onerror = null, this.src='{{asset($avatar)}}'" alt="User Image" width="100%" style="width:130px; border-radius: 12% !important;">
						  </div>
						</div>
					</div>
					<div class="row  user-panel">
						<div class="col-md-12 info text-center">
						  <a class=" text-white mb-0 ">{{auth()->user()->name}}</a>
						  <h6 class="text-warning text-center">{{auth()->user()->email}}</h6>
						</div>
					</div>
     
      <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column side" data-widget="treeview" role="menu" data-accordion="false">
                    @if($hr_profile->refid == 26)
                    <li class="nav-header text-warning text-center"><h5>{{$hr_profile->utype}}</h5></li>
                    @else
                    <li class="nav-header text-warning text-center"><h5>{{DB::table('usertype')
                        ->where('id', Session::get('currentPortal'))
                        ->first()->utype}}</h5></li>
                    @endif
                    <li class="nav-item">
                        <a href="/home"  id="dashboard" class="nav-link {{Request::url() == url('/home') ? 'active' : ''}}">
                            <i class="nav-icon fa fa-home"></i>
                            <p>
                                Home
                            </p>
                        </a>
                    </li>
					<li class="nav-item">
						<a href="/user/profile" class="nav-link {{Request::url() == url('/user/profile') ? 'active' : ''}}">
							<i class="nav-icon fa fa-user"></i>
							<p>
								Profile
							</p>
						</a>
					</li>
                    @if(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'sait')
					<li class="nav-item">
						<a href="/school-calendar" class="nav-link {{Request::url() == url('/school-calendar') ? 'active' : ''}}">
							<i class="nav-icon fas fa-calendar"></i>
							<p>
								School Calendar
							</p>
						</a>
					</li>
                    @endif
                    @if(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'sait' || strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'lchsi')
                        <li class="nav-item has-treeview {{ Request::fullUrl() == url('/administrator/schoolfolders') || Request::fullUrl() == url('/administrator/schoolfolders')? 'menu-open':''}}">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-layer-group"></i>
                                <p>
                                    INTRANET
                                <i class="fas fa-angle-left right" style="right: 5%;
                            top: 28%;"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview udernavs">
                                <li class="nav-item">
                                    <a class="{{Request::url() == url('/mydocs/index') || Request::url() == url('/mydocs/filesindex') ? 'active':''}} nav-link" href="/mydocs/index">
                                        <i class="nav-icon fa fa-calendar"></i>
                                        <p>
                                            My Documents
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="{{Request::url() == url('/administrator/schoolfolders') ? 'active':''}} nav-link" href="/administrator/schoolfolders">
                                        <i class="nav-icon fa fa-calendar"></i>
                                        <p>
                                            Doc Con
                                        </p>
                                    </a>
                                </li>
                            </ul>                            
                        </li>
                    @endif
                    @if($refid == 26 && Session::get('currentPortal') != 10)
                    <li class="nav-item has-treeview {{Request::url() == url('/hr/employees/index') || Request::url() == url('/hr/employees/statusindex') ||  Request::url() == url('/requirements/dashboard')  ? 'menu-open' : ''}}">
                        <a href="#"class="nav-link {{Request::url() == url('/hr/employees/index') || Request::url() == url('/hr/employees/statusindex') ||  Request::url() == url('/requirements/dashboard')   ? 'active' : ''}}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>
                                Employees
                            <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview udernavs">
                            <li class="nav-item">
                                <a href="/hr/employees/index" class="nav-link {{Request::url() == url('/hr/employees/index') ? 'active' : ''}}">
                                    <i class="fa fa-list nav-icon"></i>
                                    <p>
                                        Profile
                                    </p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/hr/employees/statusindex" class="nav-link {{Request::url() == url('/hr/employees/statusindex') ? 'active' : ''}}">
                                    <i class="fa fa-user-check nav-icon"></i>
                                    <p>
                                        Employment Status
                                    </p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/requirements/dashboard" class="nav-link {{Request::url() == url('/requirements/dashboard') ? 'active' : ''}}">
                                    <i class="fa fa-file-import nav-icon"></i>
                                    <p>
                                        Employment Reqs.
                                    </p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    @if(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'sait')
                        @if($isDepartmentHead == true || $isinSignatory == true)
                        <li class="nav-header text-warning"><i class="fa fa-cogs text-warning"></i> Applications</li>
                        @endif
                        @if($isDepartmentHead == true || $isinSignatory == true)
                        <li class="nav-item">
                            <a href="/hr/leaves/index" class="nav-link {{Request::url() == url('/hr/leaves/index') ? 'active' : ''}}">
                                <i class="fa fa-file-contract nav-icon"></i>
                                <p>
                                    Filed Leaves
                                </p>
                            </a>
                        </li>
                        @endif
                    @else
                        @if($countapproval>0 || $countundertimeapproval>0)
                        <li class="nav-header text-warning"><i class="fa fa-cogs text-warning"></i> Applications</li>
                        @endif
                        @if($countapproval>0)
                        <li class="nav-item">
                            <a href="/hr/leaves/index" class="nav-link {{Request::url() == url('/hr/leaves/index') ? 'active' : ''}}">
                                <i class="fa fa-file-contract nav-icon"></i>
                                <p>
                                    Filed Leaves
                                </p>
                            </a>
                        </li>
                        @endif
                    @endif
                    @if($countundertimeapproval>0)
                    <li class="nav-item">
                        <a href="/approval/undertime/index" class="nav-link {{Request::url() == url('/approval/undertime/index') ? 'active' : ''}}">
                            <i class="fa fa-file-archive nav-icon"></i>
                            <p>
                                Filed Undertimes
                            </p>
                        </a>
                    </li>
                    @endif
                        <li class="nav-header text-warning">Setup</li>
                        <li class="nav-item">
                            <a href="/holidays" class="nav-link {{Request::url() == url('/holidays') ? 'active' : ''}}">
                                <i class="fa fa-calendar-alt nav-icon"></i>
                                <p>
                                    Holidays
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/newdeductionsetup/{{Crypt::encrypt('dashboard')}}" class="nav-link {{Request::url() == url('/standarddeductions/dashboard') ? 'active' : ''}}">

                                <i class="fa fa-minus-square nav-icon"></i>
                                <p>
                                    Deductions
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/hr/tardinesscomp/index" class="nav-link {{Request::url() == url('/hr/tardinesscomp/index') ? 'active' : ''}}">

                                <i class="fa fa-minus-square nav-icon"></i>
                                <p>
                                    Tardiness Brackets
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/standardallowances/{{Crypt::encrypt('dashboard')}}" class="nav-link {{Request::url() == url('/standardallowances/dashboard') ? 'active' : ''}}">
                                <i class="fa fa-plus-square nav-icon"></i>
                                <p>
                                    Allowances
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/hr/settings/leaves?action=index" class="nav-link {{Request::url() == url('/hr/settings/leaves') ? 'active' : ''}}">
                                <i class="fa fa-file-archive nav-icon"></i>
                                <p>
                                    Leave Settings
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/hr/settings/undertime" class="nav-link {{Request::url() == url('/hr/settings/undertime') ? 'active' : ''}}">
                                <i class="fa fa-file-archive nav-icon"></i>
                                <p>
                                    Undertime Settings
                                </p>
                            </a>
                        </li>
                        <li class="nav-header text-warning">Payroll</li>
                        <li class="nav-item">
                            {{-- <a href="/hr/payroll/index" class="nav-link {{Request::url() == url('/hr/payroll/index') ? 'active' : ''}}"> --}}
                            <a href="/hr/payrollv3/index" class="nav-link {{Request::url() == url('/hr/payrollv3/index') ? 'active' : ''}}">
                                <i class="fa fa-money-bill nav-icon"></i>
                                <p>
                                    Payroll
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/hr/payrollv3/payrollhistory" class="nav-link {{Request::url() == url('/hr/payrollv3/payrollhistory') ? 'active' : ''}}">
                                <i class="fa fa-file-invoice nav-icon"></i>
                                <p>
                                    Payroll History
                                </p>
                            </a>
                        </li>
                        {{-- <li class="nav-item">
                                <a href="/hr/payrollsummary/index" class="nav-link {{Request::url() == url('/hr/payrollsummary/index') ? 'active' : ''}}">
                                <i class="fa fa-file-invoice nav-icon"></i>
                                <p>
                                    Payroll Summary
                                </p>
                            </a>
                        </li> --}}
                        <li class="nav-item">
                            <a href="/hrreports/thirteenthmonth/{{Crypt::encrypt('view')}}" class="nav-link {{Request::url() == url('/hrreports/thirteenthmonth/'.Crypt::encrypt("view").'') ? 'active' : ''}}">
                                <span class="nav-icon fa-stack">
                                    <!-- The icon that will wrap the number -->
                                    <span class="fa fa-square-o fa-stack-1x"></span>
                                    <!-- a strong element with the custom content, in this case a number -->
                                    <strong class="fa-stack" style="font-size:11px;">
                                        13<sup>th</sup>    
                                    </strong>
                                </span>
                                <p>
                                    13<sup>th</sup> Month
                                </p>
                            </a>
                        </li>
                    @else
                        <li class="nav-item has-treeview {{Request::url() == url('/hr/employees/index') || Request::url() == url('/hr/employees/statusindex') ||  Request::url() == url('/requirements/dashboard')  ? 'menu-open' : ''}}">
                            <a href="#"class="nav-link {{Request::url() == url('/hr/employees/index') || Request::url() == url('/hr/employees/statusindex') ||  Request::url() == url('/requirements/dashboard')   ? 'active' : ''}}">
                                <i class="nav-icon fas fa-users"></i>
                                <p>
                                    Employees
                                <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview udernavs">
                                <li class="nav-item">
                                    <a href="/hr/employees/index" class="nav-link {{Request::url() == url('/hr/employees/index') ? 'active' : ''}}">
                                        <i class="fa fa-list nav-icon"></i>
                                        <p>
                                            Profile
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="/hr/employees/statusindex" class="nav-link {{Request::url() == url('/hr/employees/statusindex') ? 'active' : ''}}">
                                        <i class="fa fa-user-check nav-icon"></i>
                                        <p>
                                            Employment Status
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="/requirements/dashboard" class="nav-link {{Request::url() == url('/requirements/dashboard') ? 'active' : ''}}">
                                        <i class="fa fa-file-import nav-icon"></i>
                                        <p>
                                            Employment Reqs.
                                        </p>
                                    </a>
                                </li>
                                {{-- <li class="nav-item">
                                    <a href="/hr/employees/cgrowth" class="nav-link {{Request::url() == url('/hr/employees/cgrowth') ? 'active' : ''}}">
                                        <i class="fa fa-address-book nav-icon"></i>
                                        <p>
                                            Career Growth
                                        </p>
                                    </a>
                                </li> --}}
                            </ul>
                        </li>
                        <li class="nav-item has-treeview {{Request::url() == url('/hr/attendance/index') || Request::url() == url('/hr/attendance/indexv2') || Request::url() == url('/hr/absences/index') || Request::url() == url('/hr/tardiness/index') || Request::url() == url('/hr/attendance/summaryindex') ? 'menu-open' : ''}}">
                            <a href="#"class="nav-link {{Request::url() == url('/hr/attendance/index') || Request::url() == url('/hr/attendance/indexv2') || Request::url() == url('/hr/absences/index') || Request::url() == url('/hr/tardiness/index') || Request::url() == url('/hr/attendance/summaryindex') ? 'active' : ''}}">
                                <i class="nav-icon fas fa-file"></i>
                                <p>
                                    Attendance
                                <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview udernavs">
                                {{-- <li class="nav-item">
                                    <a href="/hr/attendance/index" class="nav-link {{Request::url() == url('/hr/attendance/index') ? 'active' : ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Monitoring</p>
                                    </a>
                                </li> --}}
                                <li class="nav-item">
                                    <a href="/hr/attendance/indexv2" class="nav-link {{Request::url() == url('/hr/attendance/indexv2') ? 'active' : ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Monitoring</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="/hr/attendance/summaryindex" class="nav-link {{Request::url() == url('/hr/attendance/summaryindex') ? 'active' : ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Export Logs</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="/hr/absences/index" class="nav-link {{Request::url() == url('/hr/absences/index') ? 'active' : ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Absences</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="/hr/tardiness/index" class="nav-link {{Request::url() == url('/hr/tardiness/index') ? 'active' : ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Tardiness / Under Time</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @if($countapproval>0)
                        <li class="nav-header text-warning"><i class="fa fa-cogs text-warning"></i> Applications</li>
                        <li class="nav-item">
                            <a href="/hr/leaves/index" class="nav-link {{Request::url() == url('/hr/leaves/index') ? 'active' : ''}}">
                                <i class="fa fa-file-contract nav-icon"></i>
                                <p>
                                    Filed Leaves
                                </p>
                            </a>
                        </li>
                        @endif
                        @if($countundertimeapproval>0)
                        <li class="nav-item">
                            <a href="/approval/undertime/index" class="nav-link {{Request::url() == url('/approval/undertime/index') ? 'active' : ''}}">
                                <i class="fa fa-file-archive nav-icon"></i>
                                <p>
                                    Filed Undertimes
                                </p>
                            </a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a href="/hr/overtime/index" class="nav-link {{Request::url() == url('/hr/overtime/index') ? 'active' : ''}}">
                                <i class="fa fa-file-contract nav-icon"></i>
                                <p>
                                    Filed Overtimes
                                </p>
                            </a>
                        </li>
                        <li class="nav-header text-warning text-bold">Setup</li>
                        <li class="nav-item">
                            <a href="/hr/settings/offices/index" class="nav-link {{Request::url() == url('/hr/settings/offices/index') ? 'active' : ''}}">
                                <i class="fa fa-building nav-icon"></i>
                                <p>
                                    Offices
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/hr/settings/departments/dashboard" class="nav-link {{Request::url() == url('/hr/settings/departments/dashboard') ? 'active' : ''}}">
                                <i class="fa fa-building nav-icon"></i>
                                <p>
                                    Departments
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/hr/settings/designations/dashboard" class="nav-link {{Request::url() == url('/hr/settings/designations/dashboard') ? 'active' : ''}}">
                                <i class="fa fa-users-cog nav-icon"></i>
                                <p>
                                    Designations
                                </p>
                            </a>
                        </li>
                    @endif
                        @php
                            $priveledge = DB::table('faspriv')
                                            ->join('usertype','faspriv.usertype','=','usertype.id')
                                            ->select('faspriv.*','usertype.utype')
                                            ->where('userid', auth()->user()->id)
                                            ->where('faspriv.deleted','0')
                                            ->where('faspriv.privelege','!=','0')
                                            ->get();
                
                            $usertype = DB::table('usertype')->where('deleted',0)->where('id',auth()->user()->type)->first();
                
                        @endphp
                        @if(count($priveledge)>0)
                            <li class="nav-header text-warning">Your Portals</li>
                
                            @foreach ($priveledge as $item)
                                @if($item->usertype != Session::get('currentPortal'))
                                    <li class="nav-item">
                                        <a class="nav-link portal" href="/gotoPortal/{{$item->usertype}}" id="{{$item->usertype}}">
                                            <i class=" nav-icon fas fa-cloud"></i>
                                            <p>
                                                {{$item->utype}}
                                            </p>
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                            @if($usertype->id != Session::get('currentPortal'))
                            <li class="nav-item">
                                <a class="nav-link portal" href="/gotoPortal/{{$usertype->id}}">
                                    <i class=" nav-icon fas fa-cloud"></i>
                                    <p>
                                        {{$usertype->utype}}
                                    </p>
                                </a>
                            </li>
                            @endif
                        @endif
                        <li class="nav-header text-warning">My Applications</li>
                        <li class="nav-item">
                            <a href="/leaves/apply/index"  id="dashboard" class="nav-link {{Request::url() == url('/leaves/apply/index') ? 'active' : ''}}">
                                <i class="nav-icon fa fa-file"></i>
                                <p>
                                    Apply Leave
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/overtime/apply/index"  id="dashboard" class="nav-link {{Request::url() == url('/overtime/apply/index') ? 'active' : ''}}">
                                <i class="nav-icon fa fa-file"></i>
                                <p>
                                    Apply Overtime
                                </p>
                            </a>
                        </li>
                        @if(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'gbbc')
                        <li class="nav-item">
                            <a href="/undertime/apply"  id="dashboard" class="nav-link {{Request::url() == url('/undertime/apply') ? 'active' : ''}}">
                                <i class="nav-icon fa fa-file"></i>
                                <p>
                                    Apply Undertime
                                </p>
                            </a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a href="/dtr/attendance/index" class="nav-link {{Request::url() == url('/dtr/attendance/index') ? 'active' : ''}}">
                                <i class="nav-icon fa fa-file"></i>
                                <p>
                                    Daily Time Record
                                </p>
                            </a>
                        </li>
                        <li class="nav-header text-warning"><i class="fa fa-folder-open text-warning"></i> REPORTS</li>
                            <li class="nav-item">
                                <a href="/hrreports/teacherevaluation" class="nav-link {{Request::url() == url('/hrreports/teacherevaluation') ? 'active' : ''}}">
                                    <i class="nav-icon fa fa-users"></i>
                                    <p>
                                        Teacher Evaluation
                                    </p>
                                </a>
                            </li>
                </ul>
            </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>