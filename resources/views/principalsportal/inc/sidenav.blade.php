
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column side" data-widget="treeview" role="menu" data-accordion="false">
         
        <li class="nav-item">
            <a class="{{Request::url() == url('/home') ? 'active':''}} nav-link" href="/home">
                <i class="fas fa-tachometer-alt nav-icon"></i>
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
		<li class="nav-item">
            <a href="/school-calendar" class="nav-link">
                <i class="nav-icon fas fa-calendar"></i>
                <p>School Calendar</p>
            </a>
        </li>
                            @if(isset(DB::table('schoolinfo')->first()->withschoolfolder))
                                @if(DB::table('schoolinfo')->first()->withschoolfolder == 1)
                                <li class="nav-item">
                                    <a class="{{Request::url() == url('/schoolfolderv2/index') ? 'active':''}} nav-link" href="/schoolfolderv2/index">
                                        <i class="nav-icon fa fa-calendar"></i>
                                        <p>
                                            @if(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'bct')
                                            BCT Commons
                                            @else
                                            Doc Con
                                            @endif
                                        </p>
                                    </a>
                                </li>
                                @endif
                            @endif
       
        
                            @php
                                $countapproval = DB::table('hr_leaveemployeesappr')
                                    ->where('appuserid', auth()->user()->id)
                                    ->where('deleted','0')
                                    ->count();
                            @endphp
                            @if($countapproval > 0)
                                <li class="nav-item">
                                    <a href="/hr/leaves/index" class="nav-link {{Request::url() == url('/hr/leaves/index') ? 'active' : ''}}">
                                        <i class="fa fa-file-contract nav-icon"></i>
                                        <p>
                                            Filed Leaves
                                        </p>
                                    </a>
                                </li>
                            @endif
							<li class="nav-header text-warning">Student / Teachers</li>
		<li class="nav-item">
            <a class="nav-link" href="/student/information">
                <i class="fas fa-tachometer-alt nav-icon"></i>
                <p>
                    Students
                </p>
            </a>
        </li>
							
		<li class="nav-item">
            <a class="nav-link" href="/teacher/profile">
                <i class="fas fa-tachometer-alt nav-icon"></i>
                <p>
                    Teachers
                </p>
            </a>
        </li>
        <li class="nav-header text-warning">Setup</li>
        
        <li class="nav-item">
            <a  href="/setup/sections" class="nav-link {{Request::url() == url('/setup/sections')|| request()->is('principalPortalSectionProfile/*') ? 'active':''}}">
                <i class="fas fa-layer-group nav-icon"></i>
                <p>Sections</p>
             </a>
        </li>
              
		<li class="nav-item">
            <a class="nav-link {{Request::url() == url('/setup/subject')? 'active':''}}"" href="/setup/subject" >
                <i class="fas fa-layer-group nav-icon"></i>
                <p>
                    Subjects
                </p>
            </a>
        </li>
        <li class="nav-item">
            <a  href="/setup/subject/plot" 
                class="nav-link 
                {{Request::url() == url('/setup/subject/plot')
                 ? 'active':''}}">
                <i class="fas fa-layer-group nav-icon"></i>
                <p>Subject Plot</p>
            </a>
        </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/setup/attendance">
                            <i class="fas fa-layer-group nav-icon"></i>
                            <p>
                                School Days
                            </p>
                        </a>
                    </li>
		<li class="nav-item">
            <a href="/setup/observed/values" class="nav-link">
                <i class="fas fa-layer-group nav-icon"></i>
                <p>Observed Values</p>
            </a>
        </li>
		<li class="nav-item">
            <a href="/student/specialization" class="nav-link">
                <i class="fas fa-layer-group nav-icon"></i>
                <p>Student Specialization</p>
            </a>
        </li>
		
		
		
		
       
		
		<li class="nav-item">
            <a href="/setup/deportment-setup" class="nav-link">
                <i class="fas fa-layer-group nav-icon"></i>
                <p>Deportment Setup</p>
            </a>
        </li>
       
      
        <li class="nav-header text-warning">Grades</li>
        
        <li class="nav-item ">
            <a class="nav-link" href="/grades">
                <i class="fas fa-window-restore nav-icon" ></i>
                <p>
                    Grade Status
                </p>
            </a>
        </li>
		
		<li class="nav-item ">
            <a class="nav-link" href="/posting/grade/deportment-status">
                <i class="fas fa-window-restore nav-icon" ></i>
                <p>
                    Deportment Status
                </p>
            </a>
        </li>
		
        {{-- <li class="nav-item">
            <a class="nav-link" href="/transferedin/grades">
                <i class="fas fa-window-restore nav-icon" ></i>
                <p>
                    Transferred In Grades
                </p>
            </a>
        </li> --}}
        <li class="nav-item ">
            <a class="nav-link" href="/posting/grade">
                <i class="fas fa-window-restore nav-icon" ></i>
                <p>
                    Grades Summary
                </p>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/principalAwardsAndRecognitions">
                <i class="fas fa-window-restore nav-icon" ></i>
                <p>
                    Student Awards
                </p>
            </a>
        </li>
        <li class="nav-item">
            <a href="/principal/student/list" class="nav-link">
                <i class="fas fa-window-restore nav-icon" ></i>
                <p>Report Card</p>
            </a>
        </li>
        
        <li class="nav-header text-warning">Utility</li>
        <li class="nav-item">
            <a class="{{Request::url() == url('/textblast') ? 'active':''}} nav-link" href="/textblast">
                <i class="nav-icon far fa-paper-plane"></i>
                <p>
                    Text Blast
                </p>
            </a>
        </li>
		
        <script>
            $(document).ready(function(){
                var uri = @json(\Request::path());
                $('a[href="/'+uri+'"]').addClass('active')
            })
        </script>
        <li class="nav-header text-warning">HR</li>
                            @if(isset(DB::table('schoolinfo')->first()->withleaveapp))
                                @if(DB::table('schoolinfo')->first()->withleaveapp == 1)
                                <li class="nav-item">
                                    <a href="/leaves/apply/index"  id="dashboard" class="nav-link {{Request::url() == url('/leaves/apply/index') ? 'active' : ''}}">
                                        <i class="nav-icon fa fa-file"></i>
                                        <p>
                                            Apply Leave
                                        </p>
                                    </a>
                                </li>
                                @endif
                            @else
                                <li class="nav-item">
                                    <a href="/leaves/apply/index"  id="dashboard" class="nav-link {{Request::url() == url('/leaves/apply/index') ? 'active' : ''}}">
                                        <i class="nav-icon fa fa-file"></i>
                                        <p>
                                            Apply Leave
                                        </p>
                                    </a>
                                </li>
                            @endif
                            @if(isset(DB::table('schoolinfo')->first()->withovertimeapp))
                                @if(DB::table('schoolinfo')->first()->withovertimeapp == 1)
                                <li class="nav-item">
                                    <a href="/overtime/apply/index"  id="dashboard" class="nav-link {{Request::url() == url('/overtime/apply/index') ? 'active' : ''}}">
                                        <i class="nav-icon fa fa-file"></i>
                                        <p>
                                            Apply Overtime
                                        </p>
                                    </a>
                                </li>
                                @endif
                            @else
                                <li class="nav-item">
                                    <a href="/overtime/apply/index"  id="dashboard" class="nav-link {{Request::url() == url('/overtime/apply/index') ? 'active' : ''}}">
                                        <i class="nav-icon fa fa-file"></i>
                                        <p>
                                            Apply Overtime
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
        @include('components.privsidenav')
         {{--  <li class="nav-header text-warning">Your Portal</li>
        
        
     

        <li class="nav-item">
            <a href="/leaves/dashboard" class="nav-link {{Request::url() == url('/leaves/dashboard') ? 'active' : ''}}">
                <i class="nav-icon fa fa-file"></i>
                <p>
                    Filed Leaves
                </p>
            </a>
        </li>
        <li class="nav-item">
            <a href="/overtime/dashboard" class="nav-link {{Request::url() == url('/overtime/dashboard') ? 'active' : ''}}">
                <i class="nav-icon fa fa-file"></i>
                <p>
                    Filed Overtimes
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
        </li>  --}}
       
     
	 {{-- @php
            $priveledge = DB::table('faspriv')
                            ->join('usertype','faspriv.usertype','=','usertype.id')
                            ->select('faspriv.*','usertype.utype')
                            ->where('userid', auth()->user()->id)
                            ->where('faspriv.deleted','0')
							->where('type_active',1)
                            ->where('faspriv.privelege','!=','0')
                            ->get();

            $usertype = DB::table('usertype')->where('deleted',0)->where('id',auth()->user()->type)->first();

        @endphp
		<li class="nav-header" {{count($priveledge) > 0 ? '':'hidden'}}>Other Portal</li>
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
	 @endif --}}
        
    </ul>
<br>
<br>
</nav>