

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4 asidebar">
    <!-- Brand Logo -->
    <!-- <a href="/home" class="brand-link">
      <img src="{{asset('dist/img/AdminLTELogo.png')}}"
           alt="AdminLTE Logo"
           class="brand-image img-circle elevation-3"
           style="opacity: .8">
      <span class="brand-text font-weight-light">AdminLTE 3</span>
    </a> -->
    <div class="ckheader">
        <a href="#" class="brand-link sidehead">
            <img src="{{asset(DB::table('schoolinfo')->first()->picurl)}}"
               {{-- alt="{{DB::table('schoolinfo')->first()->abbreviation}}" --}}
               class="brand-image img-circle elevation-3"
               style="opacity: .8">
               <span class="brand-text font-weight-light">{{DB::table('schoolinfo')->first()->abbreviation}}</span>
          {{-- <span class="brand-text font-weight-light" style="position: absolute;top: 50%;font-size: 16px!important;color:#ffc107"><b>HR'S PORTAL</b></span> --}}
        </a>
    </div>
    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user (optional) -->
      <!-- <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{asset('dist/img/user2-160x160.jpg')}}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
            <a href="#" class="d-block">{{auth()->user()->name}}</a>
        </div>
      </div> -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
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
            @endphp
          <img src="{{asset($hr_profile->picurl)}}" onerror="this.onerror = null, this.src='{{asset($avatar)}}'" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info" style="    margin-top: -7px;">
            <a href="#" class="d-block">{{strtoupper(auth()->user()->name)}}</a>
            <h6 class="text-white m-0 text-warning">{{auth()->user()->email}}</h6>
        </div>
      </div>
      <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column side" data-widget="treeview" role="menu" data-accordion="false">
                    <!-- Add icons to the links using the .nav-icon class
                    with font-awesome or any other icon font library -->
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
                    @if(strtolower(DB::table('schoolinfo')->first()->payrolltype) == '1')
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
                        @if($refid == 26)
                            <li class="nav-item">
                                <a href="/holidays" class="nav-link {{Request::url() == url('/holidays') ? 'active' : ''}}">
                                    <i class="fa fa-calendar-alt nav-icon"></i>
                                    <p>
                                        Holidays
                                    </p>
                                </a>
                            </li>
                            {{-- <li class="nav-item">
                                <a href="/hrbracketing" class="nav-link {{Request::url() == url('/hrbracketing') ? 'active' : ''}}">
                                    <i class="fa fa-list-alt nav-icon"></i>
                                    <p>
                                        Bracketing
                                    </p>
                                </a>
                            </li> --}}
                            <li class="nav-item">
                                <a href="/newdeductionsetup/{{Crypt::encrypt('dashboard')}}" class="nav-link {{Request::url() == url('/standarddeductions/dashboard') ? 'active' : ''}}">
                                {{-- <a href="/standarddeductions/{{Crypt::encrypt('dashboard')}}" class="nav-link {{Request::url() == url('/standarddeductions/dashboard') ? 'active' : ''}}"> --}}

                                    <i class="fa fa-minus-square nav-icon"></i>
                                    <p>
                                        Deductions
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
                            <li class="nav-header text-warning"><i class="fa fa-money-bill text-warning"></i> PAYROLL</li>
                            <li class="nav-item">
                                <a href="/hr/payroll/index" class="nav-link {{Request::url() == url('/hr/payroll/index') ? 'active' : ''}}">
                                    <i class="fa fa-money-bill nav-icon"></i>
                                    <p>
                                        Payroll
                                    </p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/hr/payrollsummary/index" class="nav-link {{Request::url() == url('/hr/payrollsummary/index') ? 'active' : ''}}">
                                    <i class="fa fa-file-invoice nav-icon"></i>
                                    <p>
                                        Payroll Summary
                                    </p>
                                </a>
                            </li>
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
                        <li class="nav-item has-treeview {{Request::url() == url('/hr/attendance/index') || Request::url() == url('/hr/absences/index') || Request::url() == url('/hr/tardiness/index') ? 'menu-open' : ''}}">
                            <a href="#"class="nav-link {{Request::url() == url('/hr/attendance/index') || Request::url() == url('/hr/absences/index') || Request::url() == url('/hr/tardiness/index') ? 'active' : ''}}">
                                <i class="nav-icon fas fa-file"></i>
                                <p>
                                    Attendance
                                <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview udernavs">
                                <li class="nav-item">
                                    <a href="/hr/attendance/index" class="nav-link {{Request::url() == url('/hr/attendance/index') ? 'active' : ''}}">
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
                        @php
                            $countapproval = DB::table('hr_leaveemployeesappr')
                                ->where('appuserid', auth()->user()->id)
                                ->where('deleted','0')
                                ->count();
                        @endphp
                        @if($countapproval > 0 )
                            <li class="nav-item">
                                <a href="/hr/leaves/index" class="nav-link {{Request::url() == url('/hr/leaves/index') ? 'active' : ''}}">
                                    <i class="fa fa-file-contract nav-icon"></i>
                                    <p>
                                        Filed Leaves
                                    </p>
                                </a>
                            </li>
                        @endif
                            <li class="nav-header text-warning"><i class="fa fa-cogs text-warning"></i> SETTINGS</li>
                            {{-- <li class="nav-item">
                                <a href="/requirements/dashboard" class="nav-link {{Request::url() == url('/requirements/dashboard') ? 'active' : ''}}">
                                    <i class="fa fa-file-import nav-icon"></i>
                                    <p>
                                        Employment Reqs.
                                    </p>
                                </a>
                            </li> --}}
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
                            <li class="nav-item">
                                <a href="/hr/settings/leaves?action=index" class="nav-link {{Request::url() == url('/hr/settings/leaves') ? 'active' : ''}}">
                                    <i class="fa fa-file-archive nav-icon"></i>
                                    <p>
                                        Leave Settings
                                    </p>
                                </a>
                            </li>
                        @endif
                    @else
                        <li class="nav-header text-warning"><i class="fa fa-users text-warning"></i> EMPLOYEES</li>
                        @php
                            date_default_timezone_set('Asia/Manila');
                            $date = date('Y-m-d');
                        @endphp
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
                        {{-- <li class="nav-item">
                            <a href="/hr/employees/index" class="nav-link {{Request::url() == url('/hr/employees/index') ? 'active' : ''}}">
                                <i class="fa fa-users nav-icon"></i>
                                <p>
                                    Employees
                                </p>
                            </a>
                        </li> --}}
                        <li class="nav-item has-treeview {{Request::url() == url('/hr/attendance/index') || Request::url() == url('/hr/absences/index') || Request::url() == url('/hr/tardiness/index') ? 'menu-open' : ''}}">
                            <a href="#"class="nav-link {{Request::url() == url('/hr/attendance/index') || Request::url() == url('/hr/absences/index') || Request::url() == url('/hr/tardiness/index') ? 'active' : ''}}">
                                <i class="nav-icon fas fa-file"></i>
                                <p>
                                    Attendance
                                <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview udernavs">
                                <li class="nav-item">
                                    <a href="/hr/attendance/index" class="nav-link {{Request::url() == url('/hr/attendance/index') ? 'active' : ''}}">
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
                        <li class="nav-item">
                            <a href="/hr/leaves/index" class="nav-link {{Request::url() == url('/hr/leaves/index') ? 'active' : ''}}">
                                <i class="fa fa-file-contract nav-icon"></i>
                                <p>
                                    Filed Leaves
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/hr/overtime/index" class="nav-link {{Request::url() == url('/hr/overtime/index') ? 'active' : ''}}">
                                <i class="fa fa-file-contract nav-icon"></i>
                                <p>
                                    Filed Overtimes
                                </p>
                            </a>
                        </li>
                        {{-- <li class="nav-item">
                            <a href="/employeestatus/{{Crypt::encrypt('dashboard')}}" class="nav-link {{Request::url() == url('/employeestatus'.'/'.Crypt::encrypt('dashboard')) ? 'active' : ''}}">
                                <i class="fa fa-user-check nav-icon"></i>
                                <p>
                                    Employee Status
                                </p>
                            </a>
                        </li> --}}
                        <li class="nav-header text-warning"><i class="fa fa-cogs text-warning"></i> SETTINGS</li>
                        {{-- <li class="nav-item">
                            <a href="/requirements/dashboard" class="nav-link {{Request::url() == url('/requirements/dashboard') ? 'active' : ''}}">
                                <i class="fa fa-file-import nav-icon"></i>
                                <p>
                                    Employment Reqs.
                                </p>
                            </a>
                        </li> --}}
                        <li class="nav-item">
                            <a href="/hr/settings/departments/dashboard" class="nav-link {{Request::url() == url('/hr/settings/departments/dashboard') ? 'active' : ''}}">
                                <i class="fa fa-building nav-icon"></i>
                                <p>
                                    Departments
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/hr/settings/offices/dashboard" class="nav-link {{Request::url() == url('/hr/settings/offices/dashboard') ? 'active' : ''}}">
                                <i class="fa fa-building nav-icon"></i>
                                <p>
                                    Offices
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
                        <li class="nav-header text-warning"><i class="fa fa-money-bill text-warning"></i> PAYROLL</li>
                        {{-- <li class="nav-item">
                            <a href="/hr/payrollv2/index" class="nav-link {{Request::url() == url('/hr/payrollv2/index') ? 'active' : ''}}">
                                <i class="fa fa-money-bill nav-icon"></i>
                                <p>
                                    Payroll
                                </p>
                            </a>
                        </li> --}}
                        {{-- <li class="nav-item">
                            <a href="/hr/payroll/index" class="nav-link {{Request::url() == url('/hr/payroll/index') ? 'active' : ''}}">
                                <i class="fa fa-money-bill nav-icon"></i>
                                <p>
                                    Payroll
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/hr/payrollsummary/index" class="nav-link {{Request::url() == url('/hr/payrollsummary/index') ? 'active' : ''}}">
                                <i class="fas fa-file-invoice nav-icon"></i>
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
                    @endif
                    

        <li class="nav-header text-warning">Your Portal</li>
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
                    <li class="nav-item">
                        <a href="/employeedailytimerecord/dashboard" class="nav-link {{Request::url() == url('/employeedailytimerecord/dashboard') ? 'active' : ''}}">
                            <i class="nav-icon fa fa-file"></i>
                            <p>
                                Daily Time Record
                            </p>
                        </a>
                    </li>
                    <li class="nav-header text-warning"><i class="fa fa-folder-open text-warning"></i> REPORTS</li>
                    @if(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'bct')
                        <li class="nav-item">
                            <a href="/hrreports/teacherevaluation" class="nav-link {{Request::url() == url('/hrreports/teacherevaluation') ? 'active' : ''}}">
                                <i class="nav-icon fa fa-users"></i>
                                <p>
                                    Teacher Evaluation
                                </p>
                            </a>
                        </li>
                    @endif
                    {{-- <li class="nav-item">
                        <a href="/hrreports/summaryofemployees/dashboard" class="nav-link {{Request::url() == url('/hrreports/summaryofemployees/dashboard') ? 'active' : ''}}">
                            <i class="nav-icon fa fa-users"></i>
                            <p>
                                Filter Employees
                            </p>
                        </a>
                    </li> --}}
                </ul>
                <br>
                <br>
                <br>
            </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>