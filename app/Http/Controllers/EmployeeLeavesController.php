<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use File;
use Crypt;
use DatePeriod;
use DateTime;
use DateInterval;
use Session;
class EmployeeLeavesController extends Controller
{
    public function applyindex()
    {
        $refid = DB::table('usertype')->where('id', Session::get('currentPortal'))->first()->refid;

        if(Session::get('currentPortal') == 1){

            $extends = "teacher.layouts.app";
            
        }elseif(Session::get('currentPortal') == 2){

            $extends = "principalsportal.layouts.app2";

        }elseif(Session::get('currentPortal') == 3  ||  Session::get('currentPortal') == 8){

            $extends = "registrar.layouts.app";

        }elseif(Session::get('currentPortal') == 4  ||  Session::get('currentPortal') == 15){

            $extends = "finance.layouts.app";

        }elseif(Session::get('currentPortal') == 6){

            $extends = "adminPortal.layouts.app2";

        }elseif(Session::get('currentPortal') == 10 || $refid == 26){

            $extends = "hr.layouts.app";

        }elseif(Session::get('currentPortal') == 12){

            $extends = "adminITPortal.layouts.app";

        }elseif(Session::get('currentPortal') == 14){

            $extends = "deanportal.layouts.app2";

        }elseif(Session::get('currentPortal') == 16){

            $extends = "chairpersonportal.layouts.app2";

        }elseif(Session::get('currentPortal') == 18){

            $extends = "ctportal.layouts.app2";

        }else{

            $extends = "general.defaultportal.layouts.app";

        }

        $id = DB::table('teacher')->where('userid', auth()->user()->id)->where('deleted','0')->first()->id;
        
    
        if(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'gbbc')
        {
            $leavesapplied = DB::table('employee_leaves')
                ->select('employee_leaves.id','employee_leaves.remarks','employee_leaves.payrolldone','employee_leaves.numofdays','employee_leaves.leavestatus','employee_leaves.createddatetime','hr_leaves.leave_type as leavetype','hr_leaves.id as leaveid')
                ->join('hr_leaves','employee_leaves.leaveid','=','hr_leaves.id')
                ->where('employee_leaves.employeeid', $id)
                ->where('employee_leaves.deleted','0')
                ->orderByDesc('employee_leaves.createddatetime')
                ->groupBy('employee_leaves.createddatetime')
                ->get();
                
            if(count($leavesapplied)>0)
            {
                foreach($leavesapplied as $leaveapp)
                {
                    $leaveapp->canbedeleted = 0;
                    $approvalheads = DB::table('hr_leaveemployees')
                        ->select('teacher.id','teacher.userid','teacher.lastname','teacher.firstname','teacher.middlename')
                        ->join('hr_leaveemployeesappr', 'hr_leaveemployees.id','=','hr_leaveemployeesappr.headerid')
                        ->join('teacher', 'hr_leaveemployeesappr.appuserid','=','teacher.userid')
                        ->where('hr_leaveemployees.leaveid', $leaveapp->leaveid)
                        ->where('hr_leaveemployees.employeeid', $id)
                        ->where('hr_leaveemployees.deleted','0')
                        ->where('hr_leaveemployeesappr.deleted','0')
                        ->get();
                    
                    if(count($approvalheads)>0)
                    {
    
                        foreach($approvalheads as $approvalhead)
                        {
                            $approvalhead->remarks = '';
                            $checkapproval = DB::table('employee_leavesappr')   
                                ->where('ldateid', $leaveapp->id)
                                ->where('appuserid', $approvalhead->userid)
                                ->where('deleted','0')
                                ->first();
    
                            if($checkapproval)
                            {
                                $approvalhead->remarks = $checkapproval->remarks;
                                $approvalhead->appstatus = $checkapproval->appstatus;
                            }else{
                                $approvalhead->appstatus = 0;
                            }
                        }
                        if(collect($approvalheads)->where('appstatus','0')->count() == count($approvalheads))
                        {
                            $leaveapp->canbedeleted = 0;
                        }
                        if(collect($approvalheads)->where('appstatus','1')->count() >0)
                        {
                            $leaveapp->canbedeleted = 1;
                        }
                        if(collect($approvalheads)->where('appstatus','2')->count() >0)
                        {
                            $leaveapp->canbedeleted = 1;
                            $leaveapp->leavestatus = 2;
                        }
                    }
    
                    $leaveapp->approvals = $approvalheads;
                    $numdaysapproved = 0;
    
                    $dates = DB::table('employee_leavesdetail')
                        ->select('id','ldate','dayshift','leavestatus','payrolldone')
                        ->where('headerid',$leaveapp->id)
                        ->where('deleted','0')
                        ->get();
    
                    if(count($dates)>0)
                    {
                        foreach($dates as $date)
                        {
                            if(collect($approvalheads)->where('appstatus','1')->count() == count($approvalheads))
                            {
                                if($date->dayshift == 0)
                                {
                                    $numdaysapproved+=1;
                                }else{
                                    $numdaysapproved+=0.5;
                                }
                            }
                            if(collect($approvalheads)->where('appstatus','0')->count() >0)
                            {
                                if($date->dayshift == 0)
                                {
                                    $numdaysapproved+=1;
                                }else{
                                    $numdaysapproved+=0.5;
                                }
                            }
                        }
                    }
    
    
    
                    $leaveapp->dates = $dates;
                    $leaveapp->attachments = DB::table('employee_leavesatt')
                        ->where('headerid', $leaveapp->id)
                        ->where('deleted','0')
                        ->get();
                        
                    $leaveapp->countapplied = $numdaysapproved;
                    $leaveapp->countapproved = $numdaysapproved;
                }
            }
    
            // return $leavesapplied;
            
                
        }else{

            $leavesapplied = DB::table('hr_leaveemployees')
                ->select('hr_leaveemployees.id','hr_leaveemployees.remarks','hr_leaveemployees.payrolldone','hr_leaveemployees.numofdays','hr_leaveemployees.leavestatus','hr_leaveemployees.createddatetime','hr_leaves.leave_type as leavetype','hr_leaves.id as leaveid')
                ->join('hr_leaves','hr_leaveemployees.leaveid','=','hr_leaves.id')
                ->where('hr_leaveemployees.employeeid', $id)
                ->where('hr_leaves.lyear', date('Y'))
                ->where('hr_leaveemployees.deleted','0')
                ->orderByDesc('hr_leaveemployees.createddatetime')
                ->groupBy('hr_leaveemployees.createddatetime')
                ->get();
            
            if(count($leavesapplied)>0)
            {
                foreach($leavesapplied as $leaveapp)
                {
                    $leaveapp->canbedeleted = 0;
                    $approvalheads = DB::table('hr_leaveemployees')
                        ->select('teacher.id','teacher.userid','teacher.lastname','teacher.firstname','teacher.middlename')
                        ->join('hr_leavesappr', 'hr_leaveemployees.leaveid','=','hr_leavesappr.leaveid')
                        ->join('teacher', 'hr_leavesappr.appuserid','=','teacher.userid')
                        ->where('hr_leaveemployees.leaveid', $leaveapp->leaveid)
                        ->where('hr_leaveemployees.employeeid', $id)
                        ->where('hr_leaveemployees.deleted','0')
                        ->where('hr_leavesappr.deleted','0')
                        ->get();
                        
                    if(count($approvalheads)>0)
                    {
    
                        foreach($approvalheads as $approvalhead)
                        {
                            $approvalhead->remarks = '';
                            $checkapproval = DB::table('hr_leaveemployeesappr')   
                                ->where('headerid', $leaveapp->id)
                                ->where('appuserid', $approvalhead->userid)
                                ->where('deleted','0')
                                ->first();
    
                            if($checkapproval)
                            {
                                $approvalhead->remarks = $checkapproval->remarks;
                                $approvalhead->appstatus = $checkapproval->appstatus;
                            }else{
                                $approvalhead->appstatus = 0;
                            }
                        }
                        if(collect($approvalheads)->where('appstatus','0')->count() == count($approvalheads))
                        {
                            $leaveapp->canbedeleted = 0;
                        }
                        if(collect($approvalheads)->where('appstatus','1')->count() >0)
                        {
                            $leaveapp->canbedeleted = 1;
                        }
                        if(collect($approvalheads)->where('appstatus','2')->count() >0)
                        {
                            $leaveapp->canbedeleted = 1;
                            $leaveapp->leavestatus = 2;
                        }
                    }
    
                    $leaveapp->approvals = $approvalheads;
                    $numdaysapproved = 0;
    
                    $dates = DB::table('hr_leaveempdetails')
                        ->select('id','ldate','dayshift','leavestatus','payrolldone')
                        ->where('headerid',$leaveapp->id)
                        ->where('deleted','0')
                        ->get();
    
                    if(count($dates)>0)
                    {
                        foreach($dates as $date)
                        {
                            if(collect($approvalheads)->where('appstatus','1')->count() == count($approvalheads))
                            {
                                if($date->dayshift == 0)
                                {
                                    $numdaysapproved+=1;
                                }else{
                                    $numdaysapproved+=0.5;
                                }
                            }
                            if(collect($approvalheads)->where('appstatus','0')->count() >0)
                            {
                                if($date->dayshift == 0)
                                {
                                    $numdaysapproved+=1;
                                }else{
                                    $numdaysapproved+=0.5;
                                }
                            }
                        }
                    }
    
    
    
                    $leaveapp->dates = $dates;
                    $leaveapp->attachments = DB::table('hr_leaveempattach')
                        ->where('headerid', $leaveapp->id)
                        ->where('deleted','0')
                        ->get();
                        
                    $leaveapp->countapplied = $numdaysapproved;
                    $leaveapp->countapproved = $numdaysapproved;

                    if($leaveapp->countapproved ==  count($approvalheads))
                    {                        
                        $leaveapp->leavestatus = 1;
                    }
                }
            }

        }
        // return $leavesapplied;
        
        $alloweddates = array();
    
        $leavetypes = Db::table('hr_leaves')
            ->where('deleted','0')
            ->where('isactive','1')
            ->where('hr_leaves.lyear', date('Y'))
            ->get();

        if(count($leavetypes)>0)
        {
            foreach($leavetypes as $leavetype)
            {

                $leavetype->permittedtoapply = 0;
                $leavetype->countapplied = 0;

                $checkifpermittedtoapply = DB::table('hr_leaveemployees')
                    ->where('employeeid', $id)
                    ->where('leaveid', $leavetype->id)
                    ->where('deleted','0')
                    ->count();

                // return $checkifpermittedtoapply;
                if(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'gbbc')
                {
                    if($checkifpermittedtoapply > 0)
                    {
                        $leaveid = $leavetype->id;
                        $leavetype->permittedtoapply = 1;
                        $leavetype->countapplied  = collect($leavesapplied)->where('leaveid', $leaveid)->sum('countapplied');
                        
                        
                        $leavetype->countapproved = collect($leavesapplied)->where('leaveid', $leaveid)->sum('countapproved');
                        
                        $dates = DB::table('hr_leavedates')
                            ->select('id','ldate','ldatefrom','ldateto')
                            ->where('leaveid',$leavetype->id)
                            ->where('deleted','0')
                            ->where('ldatefrom','!=', null)
                            ->where('ldateto','!=', null)
                            ->get();
    
                        if(count($dates)>0)
                        {
                            foreach($dates as $date)
                            {
                                $interval = new DateInterval('P1D');
                            
                                $realEnd = new DateTime($date->ldateto);
                                $realEnd->add($interval);
                            
                                $period = new DatePeriod(new DateTime($date->ldatefrom), $interval, $realEnd);
    
                                foreach ($period as $key => $value) {
                                    $value->format('Y-m-d')   ;    
                                    array_push($alloweddates,  $value->format('Y-m-d'));
                                }
                            }
                        }
                    }

                }else{
                    $applied = DB::table('hr_leaveemployees')
                        ->select('hr_leaveemployees.id','hr_leaveemployees.remarks','hr_leaveemployees.payrolldone','hr_leaveemployees.numofdays','hr_leaveemployees.leavestatus','hr_leaveemployees.createddatetime','hr_leaves.leave_type as leavetype','hr_leaves.id as leaveid')
                        ->join('hr_leaves','hr_leaveemployees.leaveid','=','hr_leaves.id')
                        ->where('hr_leaveemployees.employeeid', $id)
                        ->where('leaveid', $leavetype->id)
                        ->where('hr_leaveemployees.deleted','0')
                        ->orderByDesc('hr_leaveemployees.createddatetime')
                        ->groupBy('hr_leaveemployees.createddatetime')
                        ->first();

                    if($applied)
                    {
                        $dates = DB::table('hr_leaveempdetails')
                            ->select('id','ldate','dayshift','leavestatus','payrolldone')
                            ->where('headerid',$applied->id)
                            ->where('deleted','0')
                            ->count();
        
                    }else{
                        $dates = 0;
                    }
                    if($dates < $leavetype->days)
                    {
                        $leavetype->permittedtoapply = 1;
                    }
                    $leavetype->countapplied = $dates;
                }
            }
        }
        // $leavetypes = collect($leavetypes)->where('permittedtoapply','1')->values();
        // return $leavesapplied;
        return view('general.leaveapplication.index')
            ->with('id', $id)
            ->with('leavetypes', $leavetypes)
            ->with('alloweddates', $alloweddates)
            ->with('leavesapplied', $leavesapplied)
            ->with('extends', $extends);

    }
    public function applysubmit(Request $request)
    {
        // return $request->all();
        date_default_timezone_set('Asia/Manila');
        
        $employeeid     = $request->get('employeeids');
        $leaveid        = $request->get('leaveid');
        $dates          = $request->get('selecteddates');
        $remarks        = $request->get('remarks');

        $selecteddates = array();
        
        $dayshifts = array();
        
        foreach($request->except('_token','leaveid','remarks','selecteddates', 'employeeids') as $shift)
        {
            array_push($dayshifts, $shift);
        }
        foreach($dates as $datekey => $date)
        {
            if(count($dayshifts) == 0)
            {
                array_push($selecteddates,(object)array(
                    'ldate'     => $date,
                    'dayshift'  => 0
                ));
            }else{
                if (array_key_exists($datekey, $dayshifts)) {
                    array_push($selecteddates,(object)array(
                        'ldate'     => $date,
                        'dayshift'  => $dayshifts[$datekey]
                    ));
                }else{
                    array_push($selecteddates,(object)array(
                        'ldate'     => $date,
                        'dayshift'  => 0
                    ));
                }
            }
        }
        // return $selecteddates;
        if(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'gbbc')
        {
            $checkifexists = DB::table('employee_leaves')
                ->where('employeeid', $employeeid)
                ->where('leaveid', $leaveid)
                ->where('deleted','0')
                ->first();
    
            if($checkifexists)
            {
                $employeeleaveid = $checkifexists->id;
    
                foreach($selecteddates as $selecteddate)
                {
                    $checkdateifexists =  DB::table('employee_leavesdetail')
                        ->where('headerid', $checkifexists->id)
                        ->where('ldate', $selecteddate->ldate)
                        ->where('deleted','0')
                        ->get();
    
                    if(count($checkdateifexists) == 0)
                    {
                        DB::table('employee_leavesdetail')
                            ->insert([
                                'headerid'           => $checkifexists->id,
                                'ldate'              => $selecteddate->ldate,
                                'dayshift'           => $selecteddate->dayshift,
                                'createdby'          => auth()->user()->id,
                                'createddatetime'    => date('Y-m-d H:i:s')
                            ]);
                    }
                }
                
            }else{
                $id = DB::table('employee_leaves')
                    ->insertGetId([
                        'employeeid'         => $employeeid,
                        'leaveid'            => $leaveid,
                        'datefrom'            => collect($selecteddates)->first()->ldate,
                        'dateto'            => collect($selecteddates)->last()->ldate,
                        'remarks'            => $remarks,
                        'numofdays'          => count($selecteddates),
                        'createdby'          => auth()->user()->id,
                        'createddatetime'    => date('Y-m-d H:i:s')
                    ]);
    
                foreach($selecteddates as $selecteddate)
                {
                    DB::table('employee_leavesdetail')
                        ->insert([
                            'headerid'           => $id,
                            'ldate'              => $selecteddate->ldate,
                            'dayshift'           => $selecteddate->dayshift,
                            'createdby'          => auth()->user()->id,
                            'createddatetime'    => date('Y-m-d H:i:s')
                        ]);
                }
                $employeeleaveid = $id;
            }
            
            if($request->has('files'))
            {
                foreach($request->file('files') as $file)
                {
                    $filename = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
    
                    $localfolder = 'EmployeeLeaves/'.auth()->user()->email;
    
                    if (! File::exists(public_path().$localfolder)) {
            
                        $path = public_path($localfolder);
            
                        if(!File::isDirectory($path)){
                            
                            File::makeDirectory($path, 0777, true, true);
            
                        }
                        
                    }
    
                    if (strpos($request->root(),'http://') !== false) {
                        $urlFolder = str_replace('http://','',$request->root());
                    } else {
                        $urlFolder = str_replace('https://','',$request->root());
                    }
                        
                    if (! File::exists(dirname(base_path(), 1).'/'.$urlFolder.'/'.$localfolder)) {
            
                        $cloudpath = dirname(base_path(), 1).'/'.$urlFolder.'/'.$localfolder;
                        
                        if(!File::isDirectory($cloudpath)){
            
                            File::makeDirectory($cloudpath, 0777, true, true);
                            
                        }
                        
                    }
                    
            
                    $clouddestinationPath = dirname(base_path(), 1).'/'.$urlFolder.'/'.$localfolder;
                    try{
            
                        $file->move($clouddestinationPath, $filename);
                    }
                    catch(\Exception $e){
                        
                
                    }
                    
                    $destinationPath = public_path($localfolder.'/');
                    
                    try{
            
                        $file->move($destinationPath,$filename);
            
                    }
                    catch(\Exception $e){
                        
                
                    }
                    DB::table('employee_leavesatt')
                        ->insert([
                            'headerid'          => $employeeleaveid,
                            'filename'          => $filename,
                            'picurl'            => $localfolder.'/'.$filename,
                            'extension'         => $extension,
                            'createdby'         => auth()->user()->id,
                            'createddatetime'   => date('Y-m-d H:i:s')
                        ]);
                }
            }
        }else{
            $checkifexists = DB::table('hr_leaveemployees')
                ->where('employeeid', $employeeid)
                ->where('leaveid', $leaveid)
                ->where('deleted','0')
                ->first();
    
            if($checkifexists)
            {
                $employeeleaveid = $checkifexists->id;
    
                foreach($selecteddates as $selecteddate)
                {
                    $checkdateifexists =  DB::table('hr_leaveempdetails')
                        ->where('headerid', $checkifexists->id)
                        ->where('ldate', $selecteddate->ldate)
                        ->where('deleted','0')
                        ->get();
    
                    if(count($checkdateifexists) == 0)
                    {
                        DB::table('hr_leaveempdetails')
                            ->insert([
                                'headerid'           => $checkifexists->id,
                                'ldate'              => $selecteddate->ldate,
                                'dayshift'           => $selecteddate->dayshift,
                                'createdby'          => auth()->user()->id,
                                'createddatetime'    => date('Y-m-d H:i:s')
                            ]);
                    }
                }
                
            }else{
                $id = DB::table('hr_leaveemployees')
                    ->insertGetId([
                        'employeeid'         => $employeeid,
                        'leaveid'            => $leaveid,
                        'datefrom'            => collect($selecteddates)->first()->ldate,
                        'dateto'            => collect($selecteddates)->last()->ldate,
                        'remarks'            => $remarks,
                        'numofdays'          => count($selecteddates),
                        'createdby'          => auth()->user()->id,
                        'createddatetime'    => date('Y-m-d H:i:s')
                    ]);
    
                foreach($selecteddates as $selecteddate)
                {
                    DB::table('hr_leaveempdetails')
                        ->insert([
                            'headerid'           => $id,
                            'ldate'              => $selecteddate->ldate,
                            'dayshift'           => $selecteddate->dayshift,
                            'createdby'          => auth()->user()->id,
                            'createddatetime'    => date('Y-m-d H:i:s')
                        ]);
                }
                $employeeleaveid = $id;
            }
            
            if($request->has('files'))
            {
                foreach($request->file('files') as $file)
                {
                    $filename = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
    
                    $localfolder = 'EmployeeLeaves/'.auth()->user()->email;
    
                    if (! File::exists(public_path().$localfolder)) {
            
                        $path = public_path($localfolder);
            
                        if(!File::isDirectory($path)){
                            
                            File::makeDirectory($path, 0777, true, true);
            
                        }
                        
                    }
    
                    if (strpos($request->root(),'http://') !== false) {
                        $urlFolder = str_replace('http://','',$request->root());
                    } else {
                        $urlFolder = str_replace('https://','',$request->root());
                    }
                        
                    if (! File::exists(dirname(base_path(), 1).'/'.$urlFolder.'/'.$localfolder)) {
            
                        $cloudpath = dirname(base_path(), 1).'/'.$urlFolder.'/'.$localfolder;
                        
                        if(!File::isDirectory($cloudpath)){
            
                            File::makeDirectory($cloudpath, 0777, true, true);
                            
                        }
                        
                    }
                    
            
                    $clouddestinationPath = dirname(base_path(), 1).'/'.$urlFolder.'/'.$localfolder;
                    try{
            
                        $file->move($clouddestinationPath, $filename);
                    }
                    catch(\Exception $e){
                        
                
                    }
                    
                    $destinationPath = public_path($localfolder.'/');
                    
                    try{
            
                        $file->move($destinationPath,$filename);
            
                    }
                    catch(\Exception $e){
                        
                
                    }
                    DB::table('hr_leaveempattach')
                        ->insert([
                            'headerid'          => $employeeleaveid,
                            'filename'          => $filename,
                            'picurl'            => $localfolder.'/'.$filename,
                            'extension'         => $extension,
                            'createdby'         => auth()->user()->id,
                            'createddatetime'   => date('Y-m-d H:i:s')
                        ]);
                }
            }
        }        
        return back();        
    }
    public function uploadfiles(Request $request)
    {
        date_default_timezone_set('Asia/Manila');

        if($request->has('attachments'))
        {
            foreach($request->file('attachments') as $file)
            {
                $filename = $file->getClientOriginalName();

                $extension = $file->getClientOriginalExtension();

                $localfolder = 'EmployeeLeaves/'.auth()->user()->email;

                if (! File::exists(public_path().$localfolder)) {
        
                    $path = public_path($localfolder);
        
                    if(!File::isDirectory($path)){
                        
                        File::makeDirectory($path, 0777, true, true);
        
                    }
                    
                }

                if (strpos($request->root(),'http://') !== false) {
                    $urlFolder = str_replace('http://','',$request->root());
                } else {
                    $urlFolder = str_replace('https://','',$request->root());
                }
                    
                if (! File::exists(dirname(base_path(), 1).'/'.$urlFolder.'/'.$localfolder)) {
        
                    $cloudpath = dirname(base_path(), 1).'/'.$urlFolder.'/'.$localfolder;
                    
                    if(!File::isDirectory($cloudpath)){
        
                        File::makeDirectory($cloudpath, 0777, true, true);
                        
                    }
                    
                }                
        
                $clouddestinationPath = dirname(base_path(), 1).'/'.$urlFolder.'/'.$localfolder;

                try{
        
                    $file->move($clouddestinationPath, $filename);
                }
                catch(\Exception $e){
                    
            
                }
                
                $destinationPath = public_path($localfolder.'/');
                
                try{
        
                    $file->move($destinationPath,$filename);
        
                }
                catch(\Exception $e){
                    
            
                }

                if(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'gbbc')
                {
                    DB::table('employee_leavesatt')
                        ->insert([
                            'headerid'          => $request->get('employeeleaveid'),
                            'filename'          => $filename,
                            'picurl'            => $localfolder.'/'.$filename,
                            'extension'         => $extension,
                            'createdby'         => auth()->user()->id,
                            'createddatetime'   => date('Y-m-d H:i:s')
                        ]);
                }else{
                    DB::table('hr_leaveempdetails')
                        ->insert([
                            'headerid'          => $request->get('employeeleaveid'),
                            'filename'          => $filename,
                            'picurl'            => $localfolder.'/'.$filename,
                            'extension'         => $extension,
                            'createdby'         => auth()->user()->id,
                            'createddatetime'   => date('Y-m-d H:i:s')
                        ]);
                }
            }
        }

        return back();
    }
    public function getdatesallowed(Request $request)
    {
        // if($request->ajax())
        // {
            $dates = DB::table('hr_leavedates')
                ->where('leaveid', $request->get('leaveid'))
                ->where('deleted','0')
                ->where('ldatefrom','!=',null)
                ->where('ldateto','!=',null)
                ->get();

            $specificdates = collect();
            
            if(count($dates)>0)
            {
                foreach($dates as $date)
                {
                    // $date->datestr = date('M d, Y', strtotime($date->ldate));
                    $date->datestr = date('M d, Y', strtotime($date->ldatefrom)).' - '.date('M d, Y', strtotime($date->ldateto));
                    $period = new DatePeriod(
                        new DateTime($date->ldatefrom),
                        new DateInterval('P1D'),
                        new DateTime($date->ldateto.' +1 day')
                   );
                   foreach ($period as $key => $value) {
                    $specificdates = collect($specificdates)->merge($value->format('Y-m-d'));       
                }
                    // $date->datefromstr = date('M d, Y', strtotime($date->ldatefrom));
                    // $date->datetostr = date('M d, Y', strtotime($date->ldateto));
                }
            }
            
            return view('general.leaveapplication.adddates')
                ->with('specificdates',$specificdates)
                ->with('selecttext', $request->get('selecttext'))
                ->with('dates', collect($dates));
        // }
    }
    public function updateremarks(Request $request)
    {
        date_default_timezone_set('Asia/Manila');
        if($request->ajax())
        {
            if(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'gbbc')
            {
                Db::table('employee_leaves')
                    ->where('id',$request->get('empleaveid'))
                    ->update([
                        'remarks'           => $request->get('remarks'),
                        'updatedby'         => auth()->user()->id,
                        'updateddatetime'   => date('Y-m-d H:i:s')
                    ]);
            }else{
                Db::table('hr_leaveemployees')
                    ->where('id',$request->get('empleaveid'))
                    ->update([
                        'remarks'           => $request->get('remarks'),
                        'updatedby'         => auth()->user()->id,
                        'updateddatetime'   => date('Y-m-d H:i:s')
                    ]);
            }
        }
    }
    public function deleteapplication(Request $request)
    {
        date_default_timezone_set('Asia/Manila');
        if($request->ajax())
        {
            try{
                if(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'gbbc')
                {
                    Db::table('employee_leaves')
                        ->where('id',$request->get('id'))
                        ->update([
                            'deleted'           => 1,
                            'deletedby'         => auth()->user()->id,
                            'deleteddatetime'   => date('Y-m-d H:i:s')
                        ]);
                }else{
                    Db::table('hr_leaveemployees')
                        ->where('id',$request->get('id'))
                        ->update([
                            'deleted'           => 1,
                            'deletedby'         => auth()->user()->id,
                            'deleteddatetime'   => date('Y-m-d H:i:s')
                        ]);

                }
                return 1;
            }catch(\Exception $error)
            {
                return 0;
            }
        }
    }
    public function deleteldate(Request $request)
    {
        date_default_timezone_set('Asia/Manila');
        if($request->ajax())
        {
            if(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'gbbc')
            {
                Db::table('employee_leavesdetail')
                    ->where('id',$request->get('ldateid'))
                    ->update([
                        'deleted'           =>  '1',
                        'deletedby'         => auth()->user()->id,
                        'deleteddatetime'   => date('Y-m-d H:i:s')
                    ]);
            }else{
                Db::table('hr_leaveempdetails')
                    ->where('id',$request->get('ldateid'))
                    ->update([
                        'deleted'           =>  '1',
                        'deletedby'         => auth()->user()->id,
                        'deleteddatetime'   => date('Y-m-d H:i:s')
                    ]);
            }
        }
    }
    public function deletefile(Request $request)
    {
        // return $request->all();
        try{
            if(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'gbbc')
            {
                DB::table('employee_leavesatt')
                    ->where('id', $request->get('attachmentid'))
                    ->update([
                        'deleted'           => 1,
                        'deletedby'         => auth()->user()->id,
                        'deleteddatetime'   => date('Y-m-d H:i:s')
                    ]);
            }else{
                DB::table('hr_leaveempattach')
                    ->where('id', $request->get('attachmentid'))
                    ->update([
                        'deleted'           => 1,
                        'deletedby'         => auth()->user()->id,
                        'deleteddatetime'   => date('Y-m-d H:i:s')
                    ]);
            }
            return 1;
        }catch(\Exception $error)
        {
            return 0;
        }
    }

}
