<nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column side" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
            <a  class="{{Request::url() == url('/home') ? 'active':''}} nav-link" href="/home">
                <i class="nav-icon fa fa-home"></i>
                <p>
                Home
                </p>
            </a>
        </li>
		
		<li class="nav-item">
            <a href="/user/profile" class="nav-link {{Request::url() == url('/user/profile') ? 'active' : ''}}">
				<i class="nav-icon fa fa-user text-warning"></i>
                <p>
                    Profile
                </p>
            </a>
        </li>
		
		 <li class="nav-item">
            <a  class="{{Request::url() == url('/college/attendance-showpage') ? 'active':''}} nav-link" href="/college/attendance-showpage">
                <i class="nav-icon fas fa-user-check text-danger"></i>
                <p>
                    Attendance <span class="right badge badge-warning" id="pending_grade_count"></span>
                </p>
            </a>
        </li>
		
        <li class="nav-item">
            <a  class="{{Request::fullUrl() == url('/college/teacher/student/information') ? 'active':''}} nav-link" href="/college/teacher/student/information">
                <i class="nav-icon fa fa-users text-info"></i>
                <p>
                    Student Information
                </p>
            </a>
        </li>
        <li class="nav-item">
            <a  class="{{Request::url() == url('/college/teacher/student/grades') ? 'active':''}} nav-link" href="/college/teacher/student/grades">
                <i class="nav-icon fas fa-chart-bar text-success"></i>
                <p>
                       Student Grades <span class="right badge badge-warning" id="pending_grade_count"></span>
                </p>
            </a>
        </li>
        <li class="nav-item">
            <a  class="{{Request::fullUrl() == url('/college/teacher/sched?blade=blade') ? 'active':''}} nav-link" href="/college/teacher/sched?blade=blade">
                <i class="nav-icon fa fa-clipboard-list text-secondary"></i>
                <p>
                        Class Schedule
                </p>
            </a>
        </li>
        
        <li class="nav-header text-warning">Other Portal</li>
        @php
            $priveledge = DB::table('faspriv')
                            ->join('usertype','faspriv.usertype','=','usertype.id')
                            ->select('faspriv.usertype','usertype.utype')
                            ->where('userid', auth()->user()->id)
                            ->where('faspriv.deleted','0')
							->where('type_active',1)
                            ->where('faspriv.privelege','!=','0')
                            ->get();

            $usertype = DB::table('usertype')->where('deleted',0)->where('id',auth()->user()->type)->first();
        @endphp
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
       
    </ul>
</nav>