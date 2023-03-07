<?php

namespace App\Http\Controllers\AdministratorControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Validator;
use File;
use Image;
use Session;
class RfidAssignmentController extends Controller
{
    public function adminstudentrfidassignindex(Request $request)
    {
            
        if($request->has('action'))
        {
            
            $search = $request->get('search');
            $search = $search['value'];

            $students = DB::table('studinfo')
                ->select('studinfo.id','sid','lastname','firstname','middlename','rfid','studentstatus.description as studentstatus','picurl')
                ->leftJoin('studentstatus','studinfo.studstatus','=','studentstatus.id')
                ->where('studinfo.deleted','0');

            if($search != null){
                    $students = $students->where(function($query) use($search){
                                      $query->orWhere('firstname','like','%'.$search.'%');
                                      $query->orWhere('lastname','like','%'.$search.'%');
                                      $query->orWhere('sid','like','%'.$search.'%');
                                });
              }
            
            $students = $students->take($request->get('length'))
                ->skip($request->get('start'))
                ->orderBy('lastname','asc')
                // ->whereIn('studinfo.studstatus',[1,2,4])
                ->get();
    
            $studentscount = DB::table('studinfo')
                ->select('studinfo.id','sid','lastname','firstname','middlename','rfid','studentstatus.description as studentstatus','picurl')
                ->leftJoin('studentstatus','studinfo.studstatus','=','studentstatus.id')
                ->where('studinfo.deleted','0')
                ->orderBy('lastname','asc');
                
            if($search != null){
                    $studentscount = $studentscount->where(function($query) use($search){
                                    $query->orWhere('firstname','like','%'.$search.'%');
                                    $query->orWhere('lastname','like','%'.$search.'%');
                                    $query->orWhere('sid','like','%'.$search.'%');
                                });
            }
            
            $studentscount = $studentscount->take($request->get('length'))
                ->orderBy('lastname','asc')
                ->count();
                
            return @json_encode((object)[
                'data'=>$students,
                'recordsTotal'=>$studentscount,
                'recordsFiltered'=>$studentscount
          ]);
        }else{
            return view('adminPortal.pages.rfidassignment.student.index');
        }
        // return view('adminPortal.pages.rfidassignment.student.index')
        //     ->with('students',$students);
    }
    public function adminstudentrfidassignuploadphoto(Request $request)
    {

        $session_student = DB::table('studinfo')->where('id', $request->get('studid'))->first();
        $message = [
            'image.required'=>'Student Picture is required',
        ];

        $validator = Validator::make($request->except(['studid', '_token']), [
            'image' => ['required']
        ], $message);

        if ($validator->fails()) {

            toast('Error!','error')->autoClose(2000)->toToast($position = 'top-right');

            $data = array(
                (object)
              [
                'status'=>'0',
                'message'=>'Error',
                'errors'=>$validator->errors(),
                'inputs'=>$request->all()
            ]);

            return $data;
            
        }
        else{


            $studendinfo = DB::table('studinfo')
                        ->where('deleted',0)
                        ->where('id',$session_student->id)
                        ->select('sid','id')
                        ->first();

            $link = DB::table('schoolinfo')
                            ->select('essentiellink')
                            ->first()
                            ->essentiellink;

            if($link == null){
                return array( (object)[
                    'status'=>'0',
                    'message'=>'Error',
                    'errors'=>array(),
                    'inputs'=>$request->all()
                ]);
            }

            $urlFolder = str_replace('http://','',$link);
			$urlFolder = str_replace('https://','',$urlFolder);

                if (! File::exists(public_path().'storage/STUDENT')) {
                    $path = public_path('storage/STUDENT');
                    if(!File::isDirectory($path)){
                        File::makeDirectory($path, 0777, true, true);
                    }
                }

                if (! File::exists(dirname(base_path(), 1).'/'.$urlFolder.'storage/STUDENT')) {
                    $cloudpath = dirname(base_path(), 1).'/'.$urlFolder.'/storage/STUDENT';
                    if(!File::isDirectory($cloudpath)){
                        File::makeDirectory($cloudpath, 0777, true, true);
                    }
                }

                $date = \Carbon\Carbon::now('Asia/Manila')->isoFormat('MMDDYYYYHHmmss');
                $data = $request->image;
                list($type, $data) = explode(';', $data);
                list(, $data)      = explode(',', $data);
                $data = base64_decode($data);
                $extension = 'png';
                $destinationPath = public_path('storage/STUDENT/'.$studendinfo->sid.'.'.$extension);
                $clouddestinationPath = dirname(base_path(), 1).'/'.$urlFolder.'/storage/STUDENT/'.$studendinfo->sid.'.'.$extension;
                file_put_contents($clouddestinationPath, $data);
                file_put_contents($destinationPath, $data);

                DB::table('studinfo')
                        ->where('id',$studendinfo->id)
                        ->take(1)
                        ->update(['picurl'=>'storage/STUDENT/'.$studendinfo->sid.'.'.$extension ]);

                $session_student->picurl = 'storage/STUDENT/'.$studendinfo->sid.'.'.$extension;

                $data = array(
                    (object)
                  [
                    'status'=>'1',
                ]);
    
                return $data;

            }

    }
    public function adminstudentrfidassignupdate(Request $request)
    {
        if($request->ajax())
        {
            // return $request->all();
            $studentid = $request->get('studentid');
            $rfid = $request->get('rfid');
            // $checkifregistered = DB::table('rfidcard')
            //     ->where('rfidcode', $rfid)
            //     ->where('deleted','0')
            //     ->first();
    
            // if($checkifregistered)
            // {
                $checkifassigned = DB::table('studinfo')
                    ->where('rfid', $rfid)
                    ->where('deleted','0')
                    ->first();
    
                if($checkifassigned)
                {
                    return 2;
                }else{
                    DB::table('studinfo')
                        ->where('id', $studentid)
                        ->update([
                            'rfid'      => $rfid,
                            'updateddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                        ]);
                    return 1;
                }
            // }else{
            //     return 0;
            // }
        }
    }
    public function adminstudentrfidassignreset(Request $request)
    {
        if($request->ajax())
        {
            // return $request->all();
            $studentid = $request->get('studentid');
            DB::table('studinfo')
                ->where('id', $studentid)
                ->update([
                    'rfid'      => null,
                    'updateddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                ]);
        }
    }
    public function adminemployeesetupindex(Request $request)
    {
            
        if($request->has('action'))
        {
            
            $search = $request->get('search');
            $search = $search['value'];

            $employees = DB::table('teacher')
                ->select('teacher.id','lastname','firstname','middlename','rfid','picurl','tid')
                ->where('teacher.isactive','1')
                ->where('teacher.deleted','0');

            if($search != null){
                    $employees = $employees->where(function($query) use($search){
                                      $query->orWhere('firstname','like','%'.$search.'%');
                                      $query->orWhere('lastname','like','%'.$search.'%');
                                      $query->orWhere('tid','like','%'.$search.'%');
                                });
              }
            
            $employees = $employees->take($request->get('length'))
                ->skip($request->get('start'))
                ->orderBy('lastname','asc')
                // ->whereIn('studinfo.studstatus',[1,2,4])
                ->get();
    
            $employeescount = DB::table('teacher')
                ->select('teacher.id','lastname','firstname','middlename','rfid','picurl','tid')
                ->where('teacher.isactive','1')
                ->where('teacher.deleted','0')
                ->orderBy('lastname','asc');
                
            if($search != null){
                    $employeescount = $employeescount->where(function($query) use($search){
                                    $query->orWhere('firstname','like','%'.$search.'%');
                                    $query->orWhere('lastname','like','%'.$search.'%');
                                    $query->orWhere('tid','like','%'.$search.'%');
                                });
            }
            
            $employeescount = $employeescount->take($request->get('length'))
                ->orderBy('lastname','asc')
                ->count();
                
            return @json_encode((object)[
                'data'=>$employees,
                'recordsTotal'=>$employeescount,
                'recordsFiltered'=>$employeescount
          ]);
        }else{
            return view('adminPortal.pages.rfidassignment.employees.index');
        }

    }
    public function  adminemployeesetupupdate(Request $request)
    {
        if($request->ajax())
        {
            $employeeid = $request->get('employeeid');
            $rfid = $request->get('rfid');
                $checkifassigned = DB::table('teacher')
                    ->where('rfid', $rfid)
                    ->where('deleted','0')
                    ->first();
    
                if($checkifassigned)
                {
                    return 2;
                }else{
                    DB::table('teacher')
                        ->where('id', $employeeid)
                        ->update([
                            'rfid'      => $rfid,
                            'updateddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                        ]);
                    return 1;
                }
            // }else{
            //     return 0;
            // }
        }
    }
    public function adminemployeesetupreset(Request $request)
    {
        if($request->ajax())
        {
            // return $request->all();
            $employeeid = $request->get('employeeid');
            DB::table('teacher')
                ->where('id', $employeeid)
                ->update([
                    'rfid'      => null,
                    'updateddatetime'=>\Carbon\Carbon::now('Asia/Manila')
                ]);
        }
    }
}
