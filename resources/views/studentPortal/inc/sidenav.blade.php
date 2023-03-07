<nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
            <a  class="{{Request::url() == url('/home') ? 'active':''}} nav-link" href="/home">
                <i class="nav-icon fa fa-home"></i>
                <p>
                Home
                </p>
            </a>
        </li>
        <li class="nav-item">
            <a href="/student/enrollment/record/classschedule"  id="dashboard" class="nav-link {{Request::url() == url('/student/enrollment/record/classschedule') ? 'active' : ''}}">
                <i class="nav-icon fas fa-clipboard-list"></i>
                <p>
                    Class Schedule
                </p>
            </a>
        </li>
		<li class="nav-item">
            <a class="{{Request::url() == url('/student/enrollment/record/reportcard') ? 'active':''}} nav-link" href="/student/enrollment/record/reportcard">
                <i class="nav-icon far fa-folder-open"></i>
                <p>
                    Report Card
                </p>
            </a>
        </li>
		<li class="nav-item">
            <a class="{{Request::url() == url('/student/remedialclass') ? 'active':''}} nav-link" href="/student/remedialclass">
                <i class="nav-icon far fa-folder-open"></i>
                <p>
                    Remedial Class
                </p>
            </a>
        </li>
        <li class="nav-item">
            <a href="/student/enrollment/record/billinginformation"  id="dashboard" class="nav-link {{Request::url() == url('/student/enrollment/record/billinginformation') ? 'active' : ''}}">
                <i class="nav-icon fas fa-receipt"></i>
                <p>
                    Billing Information
                </p>
            </a>
        </li>
        <li class="nav-item">
            <a href="/student/enrollment/record/cashier"  id="dashboard" class="nav-link {{Request::url() == url('/student/enrollment/record/cashier') ? 'active' : ''}}" hidden>
                <i class="nav-icon fas fa-cash-register"></i>
                <p>
                    Payment Transactions
                </p>
            </a>
        </li>
       
        <li class="nav-item has-treeview {{Request::url() == url('/payment')|| Request::url() == url('/student/enrollment/record/online') || Request::url() == url('/student/enrollment/record/cashier')? 'menu-open' : ''}}">
            <a href="#" class="nav-link {{Request::url() == url('/payment') || Request::url() == url('/student/enrollment/record/online') || Request::url() == url('/student/enrollment/record/cashier') ? 'active' : ''}}">
            <i class="nav-icon fas fa-cash-register"></i>
            <p>
                Payment
                <i class="right fas fa-angle-left"></i>
            </p>
            </a>
            <ul class="nav nav-treeview " >
                <li class="nav-item">
                    <a href="/payment" class="nav-link {{request()->is('payment')  ? 'active' : ''}}">
                        <i class="nav-icon fa fa-circle"></i>
                        <p>
                            Payment Upload
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/student/enrollment/record/online"  id="dashboard" class="nav-link {{Request::url() == url('/student/enrollment/record/online') ? 'active' : ''}}">
                        <i class="nav-icon fa fa-circle"></i>
                        <p>
                            Uploaded Payment
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/student/enrollment/record/cashier"  id="dashboard" class="nav-link {{Request::url() == url('/student/enrollment/record/cashier') ? 'active' : ''}}">
                        <i class="nav-icon fa fa-circle"></i>
                        <p>
                            Payment Transactions
                        </p>
                    </a>
                </li>
            </ul>
        </li>
        {{-- <li class="nav-item">
            <a class="{{Request::url() == url('/studentSchedule') ? 'active':''}} nav-link" href="/studentSchedule">
                <i class="nav-icon fa fa-image"></i>
                <p>
                    Class Schedule
                </p>
            </a>
        </li> --}}
        {{-- @if( Session::get('studentInfo')->acadprogid == 6)
        <li class="nav-item">
            <a class="{{Request::url() == url('/student/pickschedule/index') ? 'active':''}} nav-link" href="/student/pickschedule/index">
                <i class="nav-icon fa fa-clipboard-list"></i>
                <p>
                        Pick Schedule
                </p>
            </a>
        </li>
            <li class="nav-item">
                <a class="{{Request::url() == url('/student/billing') ? 'active':''}} nav-link" href="/student/billing">
                    <i class="nav-icon fa fa-receipt"></i>
                    <p>
                        Billing History
                    </p>
                </a>
            </li>
        @endif --}}
      
        {{-- @if(Session::get('studentInfo')->acadprogid == 6)
            <li class="nav-item">
                <a class=" nav-link" href="/printcor/{{Crypt::encrypt(Session::get('studentInfo')->id)}}" target="_blank">
                    <i class="nav-icon fas fa-file-alt"></i>
                    <p>
                        View COR
                    </p>
                </a>
            </li>
        @endif --}}


        @php
            $teacher_eval_version = DB::table('zversion_control')->where('version','v1')->where('module',2)->where('isactive',1)->get();
        @endphp
      
       
            <li class="nav-item">
                <a class="{{Request::url() == url('/student/teacherevaluation') ? 'active':''}} nav-link" href="/student/teacherevaluation?blade=blade">
                    <i class="nav-icon fa fa-user-edit"></i>
                    <p>
                        Teacher Evaluation
                    </p>
                </a>
            </li>
        

        @php
            $getclassrooms = Db::table('virtualclassroomstud')
                ->where('studid', auth()->user()->id)
                ->where('deleted','0')
                ->get();

        @endphp
       
        <li class="nav-item">
            <a class="{{Request::url() == url('/student/preenrollment') ? 'active':''}} nav-link" href="/student/preenrollment">
                <i class="nav-icon  fas fa-stream"></i>
                <p>
                    Enrollment
                </p>
            </a>
        </li>
		
		<li class="nav-item">
            <a href="/school-calendar" class="nav-link">
                <i class="nav-icon fa fa-calendar-alt"></i>
                <p>School Calendar</p>
            </a>
        </li>
		
        {{--<li class="nav-item">
            <a class="{{Request::url() == url('/schoolCalendar') ? 'active':''}} nav-link" href="/schoolCalendar">
                <i class="nav-icon fa fa-calendar-alt"></i>
                <p>
                School Calendar
                </p>
            </a>
        </li>--}}

       
        <li class="nav-item">
            <a class="{{Request::url() == url('/student/enrollment/record/profile') ? 'active':''}} nav-link" href="/student/enrollment/record/profile">
                <i class="nav-icon fa fa-user-edit"></i>
                <p>
                Student Profile 
                </p>
            </a>
        </li>
    
	{{-- @php    
                $acadprogid = DB::table('gradelevel')
                    ->where('id', DB::table('studinfo')->where('userid', auth()->user()->id)->first()->levelid)
                    ->where('deleted','0')
                    ->get();
            @endphp
            @if(count($acadprogid)>0 && DB::table('studinfo')->where('userid', auth()->user()->id)->first()->studstatus!=0)
                @if($acadprogid[0]->acadprogid == 6)
                    <li class="nav-item">
                        <a class="{{Request::url() == url('/student/apptes/index') ? 'active':''}} nav-link" href="/student/apptes/index">
                            <i class="nav-icon fa fa-copy"></i>
                            <p>
                                TES Application
                            </p>
                        </a>
                    </li>
                @endif
	@endif --}}
    </ul>
</nav>