<?php

namespace App\Http\Controllers\HRControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use PDF;
use \Carbon\Carbon;
use Carbon\CarbonPeriod;
use Crypt;
use File;
use DateTime;
use DateInterval;
use DatePeriod;
use App\Models\HR\HRDeductions;
use App\Models\HR\HREmployeeAttendance;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
class HREmployeesController extends Controller
{
    
    public function index(Request $request)
    {
        
        $employees = DB::table('teacher')
            ->select(
                'teacher.id',
                'teacher.userid',
                'teacher.title',
                'teacher.lastname',
                'teacher.firstname',
                'teacher.middlename',
                'teacher.suffix',
                'teacher.licno',
                'employee_personalinfo.gender',
                'employee_personalinfo.dob',
                'employee_personalinfo.address',
                'employee_personalinfo.primaryaddress',
                'employee_personalinfo.email',
                'employee_personalinfo.contactnum',
                'employee_personalinfo.spouseemployment',
                'employee_personalinfo.numberofchildren',
                'employee_personalinfo.date_joined',
                'nationality.nationality',
                'religion.religionname as religion',
                'civilstatus.civilstatus',
                'usertype.utype',
                'teacher.isactive',
                'teacher.picurl',
                'teacher.tid',
                'teacher.deleted'
                )
            ->leftJoin('usertype','teacher.usertypeid','=','usertype.id')
            ->leftJoin('employee_personalinfo','teacher.id','=','employee_personalinfo.employeeid')
            ->leftJoin('nationality','employee_personalinfo.nationalityid','=','nationality.id')
            ->leftJoin('religion','employee_personalinfo.religionid','=','religion.id')
            ->leftJoin('civilstatus','employee_personalinfo.maritalstatusid','=','civilstatus.id')
            ->orderby('lastname', 'asc')
            ->where('teacher.deleted','0')
            ->get();

        if(count($employees)>0)
        {
            foreach($employees as $employee)
            {
                mb_internal_encoding('UTF-8');
                $employee->firstname = ucwords(strtolower($employee->firstname));
                $employee->middlename = ucwords(strtolower($employee->middlename));
                $employee->lastname = ucwords(strtolower($employee->lastname));
                $salaryamount = DB::table('employee_basicsalaryinfo')
                    ->join('employee_basistype','employee_basicsalaryinfo.salarybasistype','=','employee_basistype.id')
                    ->where('employeeid', $employee->id)
                    ->where('employee_basicsalaryinfo.deleted','0')
                    ->first();

                if($salaryamount)
                {
                    $employee->salaryamount = $salaryamount->amount;
                    $employee->basistype = $salaryamount->type;
                }else{
                    $employee->salaryamount = '';
                    $employee->basistype = '';
                }
            }
        }

        if(!$request->has('action'))
        {
            return view('hr.employees.index')
                ->with('employees',$employees);
        }else{
            if($request->exporttype == 'pdf')
            {
                $pdf = PDF::loadview('hr/employees/employeelist_pdf',compact('employees'))->setPaper('8.5x14','landscape'); 
                return $pdf->stream('Employee List');
            }else{
                
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $center = ['alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ]];
                
                $sheet->getStyle('A1:A4')->getAlignment()->setHorizontal('center');
                foreach (range('A', 'X') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                 }
                // $sheet->mergeCells('A1:E1');
                // $sheet->setCellValue('A1', 'Name');
                // $sheet->setCellValue('B1', 'Birthday');
                // $sheet->setCellValue('C1', 'Date Hired');
                // $sheet->setCellValue('D1', 'Years in Service');
                // $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                // $sheet->mergeCells('A2:E2');
                    
                // $sheet->setCellValue('A1', 'Total :');
                $sheet->setCellValue('A1', '');
                $sheet->setCellValue('B1', 'Name');
                $sheet->setCellValue('C1', 'Birthday');
                $sheet->setCellValue('D1', 'Date Hired');
                $sheet->setCellValue('E1', 'Years in Service');
                $sheet->setCellValue('F1', 'Primary Address');
                $sheet->setCellValue('G1', 'Present Address');
                $sheet->setCellValue('H1', 'Civil Status');
                $sheet->setCellValue('I1', 'Religion');
                $sheet->setCellValue('J1', 'Gender');
                $sheet->setCellValue('K1', 'License Number');
                $sheet->setCellValue('L1', 'Undergraduate Course');
                $sheet->setCellValue('M1', 'Year Graduated');
                $sheet->setCellValue('N1', 'School');
                $sheet->setCellValue('O1', 'Post-Graduate Course');
                $sheet->setCellValue('P1', 'Year Graduated');
                $sheet->setCellValue('Q1', 'Units Taken');
                $sheet->setCellValue('R1', 'School');
                $sheet->setCellValue('S1', 'SSS Number');
                $sheet->setCellValue('T1', 'PHIC Number');
                $sheet->setCellValue('U1', 'Pag-Ibig Number');
                $sheet->setCellValue('V1', 'Present Salary');
                $sheet->setCellValue('W1', 'Current Position');
                $sheet->setCellValue('X1', 'Other Assignments');
                $startcellno = 2;

                foreach($employees as $key => $employee)
                {
                    if($employee->date_joined == null)
                    {
                        $yearsinservice = "";
                    }else{
                        $date1 = $employee->date_joined;
                        $date2 = date('Y-m-d');
                        $dateDifference = abs(strtotime($date2) - strtotime($date1));
                        
                        $years  = floor($dateDifference / (365 * 60 * 60 * 24));
                        $months = floor(($dateDifference - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                        // $days   = floor(($dateDifference - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 *24) / (60 * 60 * 24));
                        
                        $yearsinservice = $years." year(s) and ".$months." month(s)";
                    }

                    $sss = '';
                    if(DB::table('employee_accounts')->where('employeeid', $employee->id)->where('accountdescription','like','%sss%')->where('deleted','0')->first())
                    {
                        $phic = DB::table('employee_accounts')->where('employeeid', $employee->id)->where('accountdescription','like','%sss%')->where('deleted','0')->first()->accountnum;
                    }
                    $phic = '';
                    if(DB::table('employee_accounts')->where('employeeid', $employee->id)->where('accountdescription','like','%phic%')->where('deleted','0')->first())
                    {
                        $phic = DB::table('employee_accounts')->where('employeeid', $employee->id)->where('accountdescription','like','%phic%')->where('deleted','0')->first()->accountnum;
                    }

                    $ibig = '';
                    if(DB::table('employee_accounts')->where('employeeid', $employee->id)->where('accountdescription','like','%ibig%')->where('deleted','0')->first())
                    {
                        $ibig = DB::table('employee_accounts')->where('employeeid', $employee->id)->where('accountdescription','like','%ibig%')->where('deleted','0')->first()->accountnum;
                    }
                    if($employee->dob != null)
                    {
                        $employee->dob  = date('m/d/Y', strtotime($employee->dob));
                    }
                    if($employee->date_joined != null)
                    {
                        $employee->date_joined  = date('m/d/Y', strtotime($employee->date_joined));
                    }
                    $assignments = DB::table('faspriv')
                    ->select('utype')
                    ->where('userid', $employee->userid)
                    ->join('usertype','faspriv.usertype','=','faspriv.usertype')
                    ->where('faspriv.deleted','0')
                    ->get();
                    $sheet->setCellValue('A'.$startcellno, $key+1);
                    $sheet->setCellValue('B'.$startcellno, $employee->title.' '.$employee->lastname.', '.$employee->firstname.' '.$employee->middlename.' '.$employee->suffix);
                    $sheet->setCellValue('C'.$startcellno, $employee->dob);
                    $sheet->setCellValue('D'.$startcellno, $employee->date_joined);
                    $sheet->setCellValue('E'.$startcellno, $yearsinservice);
                    $sheet->setCellValue('F'.$startcellno, $employee->primaryaddress);
                    $sheet->setCellValue('G'.$startcellno, $employee->address);
                    $sheet->setCellValue('H'.$startcellno, $employee->civilstatus);
                    $sheet->setCellValue('I'.$startcellno, $employee->religion);
                    $sheet->setCellValue('J'.$startcellno, strtoupper($employee->gender));
                    $sheet->setCellValue('K'.$startcellno, $employee->licno);
                    $sheet->setCellValue('L'.$startcellno, '');
                    $sheet->setCellValue('M'.$startcellno, '');
                    $sheet->setCellValue('N'.$startcellno, '');
                    $sheet->setCellValue('O'.$startcellno, '');
                    $sheet->setCellValue('P'.$startcellno, '');
                    $sheet->setCellValue('Q'.$startcellno, '');
                    $sheet->setCellValue('R'.$startcellno, '');
                    $sheet->setCellValue('S'.$startcellno, $sss);
                    $sheet->setCellValue('T'.$startcellno, $phic);
                    $sheet->setCellValue('U'.$startcellno, $ibig);
                    $sheet->setCellValue('V'.$startcellno, $employee->salaryamount.' / '.$employee->basistype);
                    $sheet->setCellValue('W'.$startcellno, $employee->utype);
                    $sheet->setCellValue('X'.$startcellno, collect(collect($assignments)->pluck('utype'))->implode(','));
                    $startcellno+=1;
                }
                // $sheet->mergeCells('A'.$startcell.':B'.$startcell);
                // $sheet->setCellValue('A'.$startcell, 'Total :');
                // $sheet->getStyle('A'.$startcell.':E'.$startcell)->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                // $sheet->getStyle('A'.$startcell.':E'.$startcell)->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                
                
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment; filename="Employees.xlsx"');
                $writer->save("php://output");
            }
        }
    }
    public function getdesignations(Request $request)
    {
        $designations = DB::table('usertype')
            ->where('departmentid',$request->get('departmentid'))
            ->where('deleted','0')
            ->where('utype','!=', 'PARENT')
            ->where('utype','!=', 'STUDENT')
            ->where('utype','!=', 'ADMINADMIN')
            ->where('utype','!=', 'COLLEGE ADMIN')
            ->where('utype','!=', 'SUPER ADMIN')
            ->get();
        
        return $designations;
    }
    public function getacademicprograms(Request $request)
    {
            
        $academicprogram = DB::table('academicprogram')
            ->get();
        
        return $academicprogram;
    }
    public function addnewemployeeindex(Request $request)
    {
        $civilstatus = DB::table('civilstatus')
            ->get();
        
        $departments = DB::table('hr_school_department')
            ->where('deleted','0')
            ->get();
        
        $religions = DB::table('religion')
            ->get();

        $nationalities = DB::table('nationality')
            ->get();

        $fixeddesignations = DB::table('usertype')
            ->where('deleted','0')
            ->where('utype','!=', 'PARENT')
            ->where('utype','!=', 'STUDENT')
            ->get();
        
        return view('hr.employees.addnewemployee')
            ->with('civilstatus', $civilstatus)
            ->with('departments', $departments)
            ->with('fixeddesignations', $fixeddesignations)
            ->with('religions', $religions)
            ->with('nationalities', $nationalities);
    }
    public function addnewemployeesave(Request $request)
    {
        $createdby = DB::table('teacher')
            ->where('userid', auth()->user()->id)
            ->first();

        $checkifexists = DB::table('teacher')
            ->where('firstname','like','%'.$request->get('firstname'))
            ->where('lastname','like','%'.$request->get('lastname'))
            ->where('usertypeid',$request->get('designationid'))
            ->get();

        if(count($checkifexists) == 0){

            $newemployeeid = DB::table('teacher')
                ->insertGetId([
                    'title'                 =>  $request->get('title'),
                    'lastname'              =>  $request->get('lastname'),
                    'firstname'             =>  $request->get('firstname'),
                    'middlename'            =>  $request->get('middlename'),
                    'suffix'                =>  $request->get('suffix'),
                    'licno'                 =>  $request->get('licensenumber'),
                    'deleted'               =>  0,
                    'createdby'             =>  $createdby->id,
                    'createddatetime'       =>  date('Y-m-d H:i:s'),
                    'datehired'             =>  $request->get('datehired'),
                    'isactive'              =>  1,
                    'usertypeid'            =>  $request->get('designationid'),
                    'phonenumber'           =>  $request->get('contactnumber')
                ]);
            
            $newuserid = DB::table('users')
                ->insertGetId([
                    'name'                  =>  $request->get('lastname').', '.$request->get('firstname'),
                    'email'                 =>  Carbon::now()->isoFormat('YYYY').sprintf('%04d',$newemployeeid),
                    'type'                  =>  $request->get('designationid'),
                    'deleted'               =>  '0',
                    'password'              =>  Hash::make('123456')
                ]);
            
            DB::table('teacher')
                ->where('id',$newemployeeid)
                ->update([
                    'userid'                => $newuserid,
                    'tid'                   => Carbon::now()->isoFormat('YYYY').sprintf('%04d',$newemployeeid)
                ]);
            
            $presentaddress = '';
            if($request->get('presstreet') != null)
            {
                $presentaddress .= $request->get('presstreet').',';
            }
            if($request->get('presbarangay') != null)
            {
                $presentaddress .= $request->get('presbarangay').',';
            }
            if($request->get('prescity') != null)
            {
                $presentaddress .= $request->get('prescity').',';
            }
            if($request->get('presprovince') != null)
            {
                $presentaddress .= $request->get('presprovince').',';
            }
            DB::table('employee_personalinfo')
                ->insert([
                    'employeeid'            =>  $newemployeeid,
                    'nationalityid'         =>  $request->get('nationalityid'),
                    'religionid'            =>  $request->get('religionid'),
                    'dob'                   =>  $request->get('dob'),
                    'gender'                =>  $request->get('gender'),
                    'address'               =>  $presentaddress,
                    'presstreet'               =>  $request->get('presstreet'),
                    'presbarangay'               =>  $request->get('presbarangay'),
                    'prescity'               =>  $request->get('prescity'),
                    'presprovince'               =>  $request->get('presprovince'),
                    'contactnum'            =>  $request->get('contactnumber'),
                    'email'                 =>  $request->get('emailaddress'),
                    'maritalstatusid'       =>  $request->get('civilstatus'),
                    'spouseemployment'      =>  $request->get('spouseemployment'),
                    'numberofchildren'      =>  $request->get('numofchildren'),
                    'emercontactname'       =>  $request->get('emergencycontactname'),
                    'emercontactrelation'   =>  $request->get('emergencycontactrelation'),
                    'emercontactnum'        =>  $request->get('emergencycontactnumber'),
                    'departmentid'          =>  $request->get('departmentid'),
                    'designationid'         =>  $request->get('designationid'),
                    'date_joined'           =>  $request->get('datehired'),
                    'created_by'            =>  $createdby->id,
                    'created_on'            =>  date('Y-m-d H:i:s')
                ]);

            if($request->get('officeid')>0)
            {
                DB::table('hr_officesemp')
                    ->insert([
                        'employeeid'        =>  $newemployeeid,
                        'officeid'          =>  $request->get('officeid'),
                        'createdby'         => auth()->user()->id,
                        'createddatetime'   => date('Y-m-d H:i:s')
                    ]);
            }

            $getdesignationinfo = DB::table('usertype')
                ->where('id', $request->get('designationid'))
                ->first();

            $getsyid = DB::table('sy')
                ->where('isactive', '1')
                ->first();
            

            if($getdesignationinfo)
            {
                if($getdesignationinfo->constant == '1'){
    
                    if(strtolower($getdesignationinfo->utype) == 'teacher' || strtolower($getdesignationinfo->utype) == 'principal'){
    
                        // if($request->get('academicprogram') == true){
                            foreach($request->get('academicprogram') as $acadprogid){
    
                                DB::table('teacheracadprog')
                                    ->insert([
                                        'teacherid'         =>  $newemployeeid,
                                        'acadprogid'        =>  $acadprogid,
                                        'syid'              =>  $getsyid->id,
                                        'deleted'           =>  '0',
                                        'createddatetime'   =>  date('Y-m-d H:i:s'),
                                        'createdby'         =>  $createdby->id
                                    ]);
    
                            }
                    }
                    
                    if(strtolower($getdesignationinfo->utype) == 'principal'){
    
                        $getacademicprogram    = DB::table('academicprogram')
                            ->get();
    
                        foreach($getacademicprogram as $oldacadprog){
    
                            $matchacadprog = 0;
    
                            foreach($request->get('academicprogram') as $acadprogid){
    
                                if($oldacadprog->id  == $acadprogid){
    
                                    $matchacadprog+=1;
    
                                }
    
                            }
    
                            if($matchacadprog == 1){
    
                                $formerprincipalid = $oldacadprog->principalid;
                                
                                DB::table('academicprogram')
                                    ->where('id', $oldacadprog->id)
                                    ->update([
                                        'principalid'      =>  $newemployeeid
                                    ]);
    
    
                                $checkexistingacadprogassigned = DB::table('academicprogram')
                                    ->where('principalid', $formerprincipalid)
                                    ->get();
    
                                if(count($checkexistingacadprogassigned) == 0){
    
                                    $designationtoteacher = DB::table('usertype')
                                        ->where('utype', 'TEACHER')
                                        ->first();
                                        
                                    $ads =  DB::table('teacher')
                                        ->where('id', $formerprincipalid)
                                        ->update([
                                            'usertypeid'    =>  $designationtoteacher->id
                                        ]);
    
                                    if($ads){
                                        // return 'success';
                                    }else{
                                        // return 'failed';
                                    }
    
                                }
    
                            }
    
                        }
    
                    }
    
                }
            }
            
            return back()->with('feedback','1');

        }else{

            return back();

        }

    }
    // public function addnewemployee($id, Request $request)
    // {

    //     $action = Crypt::decrypt($id);

    //     if($action == 'dashboard'){

    //         $civilstatus = DB::table('civilstatus')
    //             ->get();
            
    //         $departments = DB::table('hr_school_department')
    //             ->where('deleted','0')
    //             ->get();
            
    //         $religions = DB::table('religion')
    //             ->get();

    //         $nationalities = DB::table('nationality')
    //             ->get();

    //         $fixeddesignations = DB::table('usertype')
    //             ->where('deleted','0')
    //             ->where('utype','!=', 'PARENT')
    //             ->where('utype','!=', 'STUDENT')
    //             ->get();
            
    //         return view('hr.addnewemployee')
    //             ->with('civilstatus', $civilstatus)
    //             ->with('departments', $departments)
    //             ->with('fixeddesignations', $fixeddesignations)
    //             ->with('religions', $religions)
    //             ->with('nationalities', $nationalities);
            
    //     }
    //     elseif($action == 'getdesignations'){

    //         $designations = DB::table('usertype')
    //             ->where('departmentid',$request->get('departmentid'))
    //             ->where('deleted','0')
    //             ->where('utype','!=', 'PARENT')
    //             ->where('utype','!=', 'STUDENT')
    //             ->where('utype','!=', 'ADMINADMIN')
    //             ->where('utype','!=', 'COLLEGE ADMIN')
    //             ->where('utype','!=', 'SUPER ADMIN')
    //             ->get();
            
    //         return $designations;

    //     }
    //     elseif($action == 'getacademicprogram'){
            
    //         $academicprogram = DB::table('academicprogram')
    //             ->get();
            
    //         return $academicprogram;

    //     }
    //     elseif($action == 'addemployee'){

    //         $createdby = DB::table('teacher')
    //             ->where('userid', auth()->user()->id)
    //             ->first();

    //         $checkifexists = DB::table('teacher')
    //             ->where('firstname','like','%'.$request->get('firstname'))
    //             ->where('lastname','like','%'.$request->get('lastname'))
    //             ->where('usertypeid',$request->get('designationid'))
    //             ->get();

    //         if(count($checkifexists) == 0){

    //             $newemployeeid = DB::table('teacher')
    //                 ->insertGetId([
    //                     'lastname'              =>  strtolower($request->get('lastname')),
    //                     'firstname'             =>  strtolower($request->get('firstname')),
    //                     'middlename'            =>  strtolower($request->get('middlename')),
    //                     'suffix'                =>  strtolower($request->get('suffix')),
    //                     'licno'                 =>  $request->get('licensenumber'),
    //                     'deleted'               =>  0,
    //                     'createdby'             =>  $createdby->id,
    //                     'createddatetime'       =>  date('Y-m-d H:i:s'),
    //                     'datehired'             =>  $request->get('datehired'),
    //                     'isactive'              =>  1,
    //                     'usertypeid'            =>  $request->get('designationid'),
    //                     'phonenumber'           =>  $request->get('contactnumber')
    //                 ]);
                
    //             $newuserid = DB::table('users')
    //                 ->insertGetId([
    //                     'name'                  =>  strtolower($request->get('lastname')).', '.strtolower($request->get('firstname')),
    //                     'email'                 =>  Carbon::now()->isoFormat('YYYY').sprintf('%04d',$newemployeeid),
    //                     'type'                  =>  $request->get('designationid'),
    //                     'deleted'               =>  '0',
    //                     'password'              =>  Hash::make('123456')
    //                 ]);

    //             DB::table('teacher')
    //                 ->where('id',$newemployeeid)
    //                 ->update([
    //                     'userid'                => $newuserid,
    //                     'tid'                   => Carbon::now()->isoFormat('YYYY').sprintf('%04d',$newemployeeid)
    //                 ]);

    //             DB::table('employee_personalinfo')
    //                 ->insert([
    //                     'employeeid'            =>  $newemployeeid,
    //                     'nationalityid'         =>  $request->get('nationalityid'),
    //                     'religionid'            =>  $request->get('religionid'),
    //                     'dob'                   =>  $request->get('dob'),
    //                     'gender'                =>  $request->get('gender'),
    //                     'address'               =>  $request->get('homeaddress'),
    //                     'contactnum'            =>  $request->get('contactnumber'),
    //                     'email'                 =>  $request->get('emailaddress'),
    //                     'maritalstatusid'       =>  $request->get('civilstatus'),
    //                     'spouseemployment'      =>  $request->get('spouseemployment'),
    //                     'numberofchildren'      =>  $request->get('numofchildren'),
    //                     'emercontactname'       =>  $request->get('emergencycontactname'),
    //                     'emercontactrelation'   =>  $request->get('emergencycontactrelation'),
    //                     'emercontactnum'        =>  $request->get('emergencycontactnumber'),
    //                     'departmentid'          =>  $request->get('departmentid'),
    //                     'designationid'         =>  $request->get('designationid'),
    //                     'date_joined'           =>  $request->get('datehired'),
    //                     'created_by'            =>  $createdby->id,
    //                     'created_on'            =>  date('Y-m-d H:i:s')
    //                 ]);

    //             $getdesignationinfo = DB::table('usertype')
    //                 ->where('id', $request->get('designationid'))
    //                 ->first();

    //             $getsyid = DB::table('sy')
    //                 ->where('isactive', '1')
    //                 ->first();
                
    //             if($getdesignationinfo->constant == '1'){

    //                 if(strtolower($getdesignationinfo->utype) == 'teacher' || strtolower($getdesignationinfo->utype) == 'principal'){

    //                     // if($request->get('academicprogram') == true){
    //                         foreach($request->get('academicprogram') as $acadprogid){
    
    //                             DB::table('teacheracadprog')
    //                                 ->insert([
    //                                     'teacherid'         =>  $newemployeeid,
    //                                     'acadprogid'        =>  $acadprogid,
    //                                     'syid'              =>  $getsyid->id,
    //                                     'deleted'           =>  '0',
    //                                     'createddatetime'   =>  date('Y-m-d H:i:s'),
    //                                     'createdby'         =>  $createdby->id
    //                                 ]);
    
    //                         }
    //                     // }else{
    //                     //     return redirect()->back()->with('feedbacknoacadprog','');
    //                     // }
                        
    //                 }
                    
    //                 if(strtolower($getdesignationinfo->utype) == 'principal'){

    //                     $getacademicprogram    = DB::table('academicprogram')
    //                         ->get();

    //                     foreach($getacademicprogram as $oldacadprog){

    //                         $matchacadprog = 0;

    //                         foreach($request->get('academicprogram') as $acadprogid){

    //                             if($oldacadprog->id  == $acadprogid){

    //                                 $matchacadprog+=1;

    //                             }

    //                         }

    //                         if($matchacadprog == 1){

    //                             $formerprincipalid = $oldacadprog->principalid;
                                
    //                             DB::table('academicprogram')
    //                                 ->where('id', $oldacadprog->id)
    //                                 ->update([
    //                                     'principalid'      =>  $newemployeeid
    //                                 ]);


    //                             $checkexistingacadprogassigned = DB::table('academicprogram')
    //                                 ->where('principalid', $formerprincipalid)
    //                                 ->get();

    //                             if(count($checkexistingacadprogassigned) == 0){

    //                                 $designationtoteacher = DB::table('usertype')
    //                                     ->where('utype', 'TEACHER')
    //                                     ->first();
                                        
    //                                 $ads =  DB::table('teacher')
    //                                     ->where('id', $formerprincipalid)
    //                                     ->update([
    //                                         'usertypeid'    =>  $designationtoteacher->id
    //                                     ]);

    //                                 if($ads){
    //                                     // return 'success';
    //                                 }else{
    //                                     // return 'failed';
    //                                 }

    //                             }

    //                         }

    //                     }

    //                 }

    //             }
                
    //             return back()->with('feedback','1');

    //         }else{

    //             return back();

    //         }

    //     }

    // }
    public function imageCropPost(Request $request)

    {
        $sy = DB::table('sy')
            ->where('isactive','1')
            ->first();

        $urlFolder = str_replace('http://','',$request->root());

        if (! File::exists(public_path().'employeeprofile/'.$sy->sydesc)) {

            $path = public_path('employeeprofile/'.$sy->sydesc);

            if(!File::isDirectory($path)){
                
                File::makeDirectory($path, 0777, true, true);

            }else{
                
            }
            
        }
        
        if (! File::exists(dirname(base_path(), 1).'/'.$urlFolder.'/employeeprofile/'.$sy->sydesc)) {

            $cloudpath = dirname(base_path(), 1).'/'.$urlFolder.'/employeeprofile/'.$sy->sydesc;
            
            if(!File::isDirectory($cloudpath)){

                File::makeDirectory($cloudpath, 0777, true, true);
                
            }
            
        }
        

            
        $data = $request->image;

        list($type, $data) = explode(';', $data);

        list(, $data)      = explode(',', $data);

        $data = base64_decode($data);

        $extension = 'png';

        $clouddestinationPath = dirname(base_path(), 1).'/'.$urlFolder.'/employeeprofile/'.$sy->sydesc.'/'.$request->username.'_'.$request->lastname.'.'.$extension;
        
        try{

            file_put_contents($clouddestinationPath, $data);
            
        }
        catch(\Exception $e){
           
    
        }

        $destinationPath = public_path('employeeprofile/'.$sy->sydesc.'/'. $request->username.'_'.$request->lastname.'.'.$extension);
        
        file_put_contents($destinationPath, $data);

        DB::table('teacher')
            ->where('id',$request->employeeid)
            ->update([
                'picurl' => 'employeeprofile/'.$sy->sydesc.'/'. $request->username.'_'.$request->lastname.'.'.$extension
            ]);

        return response()->json(['success'=>'done']);

    }
    public function employeecredential(Request $request)
    {
        // return $action;
        date_default_timezone_set('Asia/Manila');

        // $action = Crypt::decrypt($action);


        $employeename = DB::table('teacher')
            ->join('users', 'teacher.id','=','users.id')
            ->where('teacher.id', $request->get('employeeid'))
            ->first();
            
        // if($action == 'add'){

            $credentialdescription = Db::table('employee_credentialtypes')
                ->where('id', $request->get('credentialid'))
                ->first();

            $urlFolder = str_replace('http://','',$request->root());

            if (! File::exists(public_path().'employeecredentials/'.$credentialdescription->description)) {

                $path = public_path('employeecredentials/'.$credentialdescription->description);

                if(!File::isDirectory($path)){
                    
                    File::makeDirectory($path, 0777, true, true);

                }
                
            }
            
            if (! File::exists(dirname(base_path(), 1).'/'.$urlFolder.'/employeecredentials/'.$credentialdescription->description)) {

                $cloudpath = dirname(base_path(), 1).'/'.$urlFolder.'/employeecredentials/'.$credentialdescription->description;
                
                if(!File::isDirectory($cloudpath)){

                    File::makeDirectory($cloudpath, 0777, true, true);
                    
                }
                
            }


            $file = $request->file('credential');
            
            $extension = $file->getClientOriginalExtension();
    
            $clouddestinationPath = dirname(base_path(), 1).'/'.$urlFolder.'/employeecredentials/'.$credentialdescription->description.'/';
    
    
            try{
    
                $file->move($clouddestinationPath, $employeename->email.'-'.strtoupper($employeename->lastname).' '.strtoupper($employeename->firstname[0].'.').$extension);
    
            }
            catch(\Exception $e){
               
        
            }
    
            $destinationPath = public_path('employeecredentials/'.$credentialdescription->description.'/');
    
            
            try{
    
                $file->move($destinationPath, $employeename->email.'-'.strtoupper($employeename->lastname).' '.strtoupper($employeename->firstname[0].'.').$extension);
    
            }
            catch(\Exception $e){
               
        
            }
    
            // copy($destinationPath.$employeename->email.'-'.strtoupper($employeename->lastname).' '.strtoupper($employeename->firstname[0].'.').$extension, $destinationPath.$employeename->email.'-'.strtoupper($employeename->lastname).' '.strtoupper($employeename->firstname[0].'.').$extension);
    
            $uploadedby = DB::table('teacher')
                ->where('userid', auth()->user()->id)
                ->first();
    
            $checkifexists = DB::table('employee_credentials')
                ->where('employeeid', $request->get('employeeid'))
                ->where('credentialtypeid',$request->get('credentialid'))
                ->where('deleted','0')
                ->get();
    
            if(count($checkifexists) == 0){
    
                DB::table('employee_credentials')
                    ->insert([
                        'employeeid'            => $request->get('employeeid'),
                        'credentialtypeid'      => $request->get('credentialid'),
                        'filepath'              => 'employeecredentials/'.$credentialdescription->description.'/'.$employeename->email.'-'.strtoupper($employeename->lastname).' '.strtoupper($employeename->firstname[0].'.').$extension,
                        'extension'             => $extension,
                        'uploadedby'            => $uploadedby->id,
                        'uploadeddatetime'      => date('Y-m-d H:i:s')
                    ]);
    
            }else{
    
                DB::table('employee_credentials')
                    ->where('employeeid',$request->get('employeeid'))
                    ->where('credentialtypeid',$request->get('credentialid'))
                    ->update([
                        'filepath'              => 'employeecredentials/'.$credentialdescription->description.'/'.strtoupper($employeename->lastname).' '.strtoupper($employeename->firstname[0].'.').$extension,
                        'extension'             => $extension,
                        'uploadedby'            => $uploadedby->id,
                        'uploadeddatetime'      => date('Y-m-d H:i:s')
                    ]);
    
            }
    
            return back()->with('linkid',$request->get('linkid'));

        // }
        // elseif($action == 'delete'){
            DB::table('employee_credentials')
                ->where('employeeid',$request->get('employeeid'))
                ->where('id',$request->get('credentialid'))
                ->update([
                    'deleted'   => 1
                ]);
        // }

    }
    // public function employeecredentialdelete(Request $request)
    // {
    //     // return $request->all();
    //     DB::table('employee_credentials')
    //     ->where('employeeid',$request->get('employeeid'))
    //     ->where('credentialtypeid',$request->get('credentialid'))
    //     ->update([
    //         'deleted'   => 1
    //     ]);
    // }
    public function employeedtrtab($action,Request $request)
    {
        date_default_timezone_set('Asia/Manila');


        $myid = DB::table('teacher')
            ->select('id')
            ->where('userid', auth()->user()->id)
            ->where('isactive','1')
            ->where('deleted','0')
            ->first();

        if($action == 'index'){
            $employee = DB::table('teacher')
                ->where('id',$request->get('employeeid'))
                ->first();

            $currentmonthworkdays   = array();

            $beginmonth             = new DateTime(date('Y-m-01'));

            $endmonth               = new DateTime(date('Y-m-t'));

            $endmonth               = $endmonth->modify( '+1 day' ); 
            
            $intervalmonth          = new DateInterval('P1D');

            $daterangemonth         = new DatePeriod($beginmonth, $intervalmonth ,$endmonth);

            foreach($daterangemonth as $datemonth){

                    array_push($currentmonthworkdays,$datemonth->format("Y-m-d"));

            }

            $employeeattendance     = array();

            foreach($currentmonthworkdays as $currentmonthworkday){
                    // foreach($workdays as $currentmonthworkday){
                $att = HREmployeeAttendance::getattendance($currentmonthworkday, $employee);
                if($att->amin == '00:00:00')
                {
                    $att->amin = "";
                }
                if($att->amout == '00:00:00')
                {
                    $att->amout = "";
                }
                if($att->pmin == '00:00:00')
                {
                    $att->pmin = "";
                }
                if($att->pmout == '00:00:00')
                {
                    $att->pmout = "";
                }

                array_push($employeeattendance,(object)array(
                    'date'          =>  date('M d, Y',strtotime($currentmonthworkday)),
                    'day'           =>  date('l',strtotime($currentmonthworkday)),
                    'timerecord'    =>  $att
                    ));

            }

            return view('hr.employees.info.dtr')
                ->with('profileinfoid', $request->get('employeeid'))
                ->with('currentmonthfirstday',date('m-01-Y'))
                ->with('currentmonthlastday',date('m-t-Y'))
                ->with('employeeattendance',$employeeattendance);
        }
        elseif($action == 'changeperiod'){

            $employee = DB::table('teacher')
                ->where('id',$request->get('employeeid'))
                ->first();

            if($request->has('period'))
            {
                $perioddate           = explode(' - ', $request->get('period'));
            }else{
                $perioddate           = explode(' - ', date('m-01-Y').' - '.date('m-t-Y'));
            }
            
            $periodfrom           = explode('-',$perioddate[0]);
    
            // $datefrom             = $periodfrom[2].'-'.$periodfrom[0].'-'.$periodfrom[1];
    
            $periodto             = explode('-',$perioddate[1]);
            
            // $dateto               = $periodto[2].'-'.$periodto[0].'-'.$periodto[1];
    
            $workdays = array();
    
            $datefrom             = new DateTime($periodfrom[2].'-'.$periodfrom[0].'-'.$periodfrom[1]);
    
            $dateto               = new DateTime($periodto[2].'-'.$periodto[0].'-'.$periodto[1]);
    
            $dateto               = $dateto->modify( '+1 day' ); 
            
            $intervaldate         = new DateInterval('P1D');
    
            $daterange            = new DatePeriod($datefrom, $intervaldate ,$dateto);
    
            foreach($daterange as $period){
    
                    array_push($workdays,$period->format("Y-m-d"));
    
            }
    
            $employeeattendance     = array();
    
            $detecttimeschedsetup = DB::table('deduction_tardinesssetup')
                ->where('status','1')
                ->first();
                
            foreach($workdays as $workday){
                // foreach($workdays as $currentmonthworkday){
                    // $att = HREmployeeAttendance::getattendance($workday, $employee)

                    $att = HREmployeeAttendance::getattendance($workday, $employee);
                    if($att->amin == '00:00:00')
                    {
                        $att->amin = "";
                    }
                    if($att->amout == '00:00:00')
                    {
                        $att->amout = "";
                    }
                    if($att->pmin == '00:00:00')
                    {
                        $att->pmin = "";
                    }
                    if($att->pmout == '00:00:00')
                    {
                        $att->pmout = "";
                    }
    
                    array_push($employeeattendance,(object)array(
                        'date'          =>  date('M d, Y',strtotime($workday)),
                        'day'           =>  date('l',strtotime($workday)),
                        'timerecord'    =>  $att
                        ));

            }
    
            return $employeeattendance;

        }else{

            // return $request->all();
            // return date('Y-m-d', strtotime($request->get('tdate')));
            DB::table('teacherattendance')
                ->where('teacher_id', $request->get('employeeid'))
                ->where('tdate', date('Y-m-d', strtotime($request->get('tdate'))))
                ->update([
                    'deleted'   => 1
                ]);

        }

    }
    public function employeeprofile($action, Request $request)
    {
        date_default_timezone_set('Asia/Manila');
        
            if($action == 'updaterfid'){

                // $checkifexists = Db::table('teacher')
                //     ->where('rfid',$request->get('rfid') )
                //     ->get();

                // if(count($checkifexists) == 0){

                    DB::table('teacher')
                        ->where('id', $request->get('employeeid'))
                        ->update([
                            'rfid'              => $request->get('rfid'),
                            'updateddatetime'   => date('Y-m-d H:i:s')
                        ]);

                        return back();

                // }else{

                //     return back()->with('rfidexists','RFID Exists!');

                // }

            }

            $teacherid = $action;
            
            $civilstatus = Db::table('civilstatus')
                ->where('deleted','0')
                ->get();

            $nationality = Db::table('nationality')
                ->where('deleted','0')
                ->get();

            $religion = Db::table('religion')
                ->where('deleted','0')
                ->get();

            $profile = Db::table('teacher')
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
                    'teacher.rfid',
                    'teacher.employmentstatus',
                    'usertype.utype'
                    )
                ->join('usertype','teacher.usertypeid','=','usertype.id')
                ->where('teacher.id', $teacherid)
                ->first();
            
            $employee_info = Db::table('employee_personalinfo')
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
                    'employee_personalinfo.date_joined as datehired'
                    )
                // ->join('nationality','employee_personalinfo.nationalityid','=','nationality.id')
                // ->join('religion','employee_personalinfo.religionid','=','religion.id')
                // ->join('civilstatus','employee_personalinfo.maritalstatusid','=','civilstatus.id')
                ->where('employee_personalinfo.employeeid',$teacherid)
                ->get();
                
            if(count($employee_info) > 0){
                
                foreach($employee_info as $empinfo){

                    if($empinfo->nationalityid == 0){

                        $empinfo->nationality = "";

                    }else{

                        $getnationality = Db::table('nationality')
                            ->where('id', $empinfo->nationalityid)
                            ->first();
                            
                        $empinfo->nationality = $getnationality->nationality;
    
                    }

                    if($empinfo->religionid == 0){

                        $empinfo->religionname = "";

                    }else{

                        $getreligionname = Db::table('religion')
                            ->where('id', $empinfo->religionid)
                            ->first();
                            
                        $empinfo->religionname = $getreligionname->religionname;
    
                    }

                    if($empinfo->maritalstatusid == 0){

                        $empinfo->civilstatus = "";

                    }else{

                        $getcivilstatus = Db::table('civilstatus')
                            ->where('id', $empinfo->maritalstatusid)
                            ->first();

                        $empinfo->civilstatus = $getcivilstatus->civilstatus;
    
                    }

                    if($empinfo->dob == null){

                        $empinfo->dobstring = "";

                    }else{

                        $empinfo->dobstring = date('F d, Y', strtotime($empinfo->dob));
                    }

                    if($empinfo->datehired == null){

                        $empinfo->datehired = "";

                        $empinfo->datehiredstring = "";

                    }else{

                        $empinfo->datehired = date('Y-m-d', strtotime($empinfo->datehired));

                        $empinfo->datehiredstring = date('F d, Y', strtotime($empinfo->datehired));

                    }
                    
                }
                
            }
            
            // $employee_benefits = Db::table('employee_benefits')
            //     ->select(
            //         'employee_benefits.id as employee_benefitsid',
            //         'benefits.benefits',
            //         'employee_benefits.benefitnum'
            //         )
            //     ->join('benefits','employee_benefits.benefitsid','=','benefits.id')
            //     ->where('employeeid',$teacherid)
            //     ->where('employee_benefits.deleted','0')
            //     ->get();
            $employee_accounts = Db::table('employee_accounts')
                ->where('employeeid',$teacherid)
                ->where('employee_accounts.deleted','0')
                ->get();
                
            $employee_familyinfo = Db::table('employee_familyinfo')
                ->where('employeeid',$teacherid)
                ->where('deleted','0')
                ->get();

            $employee_educationinfo = Db::table('employee_educationinfo')
                ->where('employeeid',$teacherid)
                ->where('deleted','0')
                ->get();

            $employee_experience = Db::table('employee_experience')
                ->where('employeeid',$teacherid)
                ->where('deleted','0')
                ->get();
                
            $employee_basicsalaryinfo = Db::table('employee_basicsalaryinfo')
                ->select(
                    'employee_basicsalaryinfo.id',
                    'employee_basicsalaryinfo.amount',
                    'employee_basicsalaryinfo.paymenttype',
                    'employee_basistype.id as basistypeid',
                    'employee_basistype.type',
                    'employee_basicsalaryinfo.noofmonths',
                    'employee_basicsalaryinfo.projectbasedtype',
                    'employee_basicsalaryinfo.hoursperday',
                    'employee_basicsalaryinfo.hoursperweek',
                    'employee_basicsalaryinfo.mondays',
                    'employee_basicsalaryinfo.tuesdays',
                    'employee_basicsalaryinfo.wednesdays',
                    'employee_basicsalaryinfo.thursdays',
                    'employee_basicsalaryinfo.fridays',
                    'employee_basicsalaryinfo.saturdays',
                    'employee_basicsalaryinfo.sundays',
                    'employee_basicsalaryinfo.mondayhours',
                    'employee_basicsalaryinfo.tuesdayhours',
                    'employee_basicsalaryinfo.wednesdayhours',
                    'employee_basicsalaryinfo.thursdayhours',
                    'employee_basicsalaryinfo.fridayhours',
                    'employee_basicsalaryinfo.saturdayhours',
                    'employee_basicsalaryinfo.sundayhours',
                    'employee_basicsalaryinfo.holidays'
                    )
                ->join('employee_basistype','employee_basicsalaryinfo.salarybasistype','=','employee_basistype.id')
                ->where('employee_basicsalaryinfo.employeeid',$teacherid)
                ->where('employee_basicsalaryinfo.deleted','0')
                ->where('employee_basistype.deleted','0')
                ->get();

            
            $timeschedule = Db::table('employee_customtimesched')
                ->where('employeeid',$teacherid)
                ->where('deleted','0')
                ->get();
            
            if(count($timeschedule) == 0){
                Db::table('employee_customtimesched')
                    ->insert([
                        'employeeid'    => $teacherid,
                        'amin'          => '08:00:00',
                        'amout'         => '12:00:00',
                        'pmin'          => '01:00:00',
                        'pmout'         => '05:00:00'
                    ]);

                $timeschedule = Db::table('employee_customtimesched')
                    ->where('employeeid',$teacherid)
                    ->where('deleted','0')
                    ->get();
            }   
            elseif(count($timeschedule) > 0){

                foreach($timeschedule as $timesched){

                    foreach($timesched as $key => $value){

                        if($key == 'amin'){

                            if($value == null){

                                $timesched->amin = '08:00';

                            }

                        }
                        elseif($key == 'amout'){

                            if($value == null){

                                $timesched->amout = '12:00';

                            }

                        }
                        elseif($key == 'pmin'){

                            if($value == null){

                                $timesched->pmin = '01:00';

                            }

                        }
                        elseif($key == 'pmout'){
                            if($value == null){
                                $timesched->pmout = '05:00';
                            }
                        }
                    }
                }
            }

            $tardinesssetup = DB::table('deduction_tardinesssetup')
                ->where('status','1')
                ->get();
                
            $department = Db::table('hr_school_department')
                ->where('deleted','0')
                ->get();
    
                
            $designations = Db::table('usertype')
                ->select(
                    'id',
                    'utype as designation',
                    'departmentid'
                )
                ->where('deleted','0')
                ->get();
    
            // $benefits = Db::table('benefits')
            //     ->where('deleted','0')
            //     ->get();


            // if(count($deductiontypes) > 0){

            // foreach($deductiontypes as $deductiontype){

            //     $mydeductions = Db::table('employee_deductionstandard')
            //         ->select(
            //             'employee_deductionstandard.id as contributiondetailid',
            //             'employee_deductionstandard.ersamount',
            //             'employee_deductionstandard.eesamount',
            //             'employee_deductionstandard.status'
            //             )
            //         ->where('employee_deductionstandard.employeeid',$teacherid)
            //         ->where('employee_deductionstandard.deduction_typeid',$deductiontype->id)
            //         ->get();

            //     // return $mydeductions;

            //     if($deductiontype->constant == 0){

            //         if(count($mydeductions) == 0){

            //             // array_push($mystandarddeductions, (object)array(
            //             //     'contributionid'        => $deductiontype->id,
            //             //     'description'           => $deductiontype->description,
            //             //     'contributiondetailid'  => '',
            //             //     'ersamount'             => '',
            //             //     'eesamount'             => '',
            //             //     'status'                => ''
            //             // ));

            //         }else{
            //             array_push($mystandarddeductions, (object)array(
            //                 'contributionid'        => $deductiontype->id,
            //                 'description'           => $deductiontype->description,
            //                 'contributiondetailid'  => $mydeductions[0]->contributiondetailid,
            //                 'ersamount'             => $mydeductions[0]->ersamount,
            //                 'eesamount'             => $mydeductions[0]->eesamount,
            //                 'status'                => $mydeductions[0]->status
            //             ));
            //         }

            //     }





            // }
            // return 'asd';

            $deductiontypes = Db::table('deduction_standard')
                ->where('deleted','0')
                ->get();
            if(count($employee_basicsalaryinfo) > 0){
                if($employee_basicsalaryinfo[0]->deductionsetup != 1)
                {
                    $mystandarddeductions = HRDeductions::updatestandarddeductions($teacherid,$employee_basicsalaryinfo[0]->amount, $employee_basicsalaryinfo[0]->type);
                }
                // return $mystandarddeductions;

                    // return $monthlypi;
                // }
                // else if($deductiontype->id == '2'){


                // }
                // else if($deductiontype->id == '3'){
                    
                // return $monthlyss;
                // }
                // else if($deductiontype->id == '4')
                // {
                    // return $employee_basicsalaryinfo;
                    
                // }
                // else{
                //     if(count($mydeductions) == 0){

                //         array_push($mystandarddeductions, (object)array(
                //             'contributionid'        => $deductiontype->id,
                //             'description'           => $deductiontype->description,
                //             'contributiondetailid'  => '',
                //             'ersamount'             => '',
                //             'eesamount'             => '',
                //             'status'                => ''
                //         ));

                //     }else{
                //         array_push($mystandarddeductions, (object)array(
                //             'contributionid'        => $deductiontype->id,
                //             'description'           => $deductiontype->description,
                //             'contributiondetailid'  => $mydeductions[0]->contributiondetailid,
                //             'ersamount'             => $mydeductions[0]->ersamount,
                //             'eesamount'             => $mydeductions[0]->eesamount,
                //             'status'                => $mydeductions[0]->status
                //         ));
                //     }

                // }
                // return $monthlyss;

            }else{
                $mystandarddeductions = [];
            }

            // }
            // return $mystandarddeductions;
            $myotherdeductions = Db::table('employee_deductionother')
                // ->select(
                //     'employee_deductionother.id',
                //     'employee_deductionother.description',
                //     'employee_deductionother.amount',
                //     'employee_deductionother.term',
                //     'employee_deductionother.dateissued',
                //     'employee_deductionotherdetail.amountpaid',
                //     'employee_deductionotherdetail.datepaid'
                //     )
                // ->join('employee_deductionotherdetail','employee_deductionother.id','=','employee_deductionotherdetail.headerid')
                ->where('employee_deductionother.employeeid',$teacherid)
                ->where('employee_deductionother.deleted','0')
                ->get();
                
            foreach($myotherdeductions as $myotherdeduction){

                foreach($myotherdeduction as $myotherdeductionkey => $myotherdeductionvalue){

                    if($myotherdeductionkey == 'dateissued'){

                        $myotherdeduction->dateissued = date('F d,Y h:i:s A', strtotime($myotherdeductionvalue));

                    }
                    elseif($myotherdeductionkey == 'amount'){

                        $myotherdeduction->amount = number_format($myotherdeductionvalue, 2, '.', ',');

                    }
                    elseif($myotherdeductionkey == 'amountpaid'){

                        $myotherdeduction->amountpaid = number_format($myotherdeductionvalue, 2, '.', ',');

                    }

                }

            }

            $standardallowances = Db::table('allowance_standard')
                ->where('deleted','0')
                ->get();
                
            $mystandardallowances = array();

            if(count($standardallowances) > 0){

                foreach($standardallowances as $standardallowance){

                    $myallowances = Db::table('employee_allowancestandard')
                        ->select(
                            'employee_allowancestandard.id as employeeallowancestandardid',
                            'employee_allowancestandard.amount',
                            'employee_allowancestandard.status'
                            )
                        ->where('employee_allowancestandard.employeeid',$teacherid)
                        ->where('employee_allowancestandard.allowance_standardid',$standardallowance->id)
                        ->get();

                    if(count($myallowances) == 0){

                        array_push($mystandardallowances, (object)array(
                            'allowance_standardid'          => $standardallowance->id,
                            'description'                   => $standardallowance->description,
                            'employeeallowancestandardid'   => '',
                            'amount'                        => '',
                            'status'                        => ''
                        ));

                    }else{
                        array_push($mystandardallowances, (object)array(
                            'allowance_standardid'          => $standardallowance->id,
                            'description'                   => $standardallowance->description,
                            'employee_allowancestandard'   => $myallowances[0]->employeeallowancestandardid,
                            'amount'                        => $myallowances[0]->amount,
                            'status'                        => $myallowances[0]->status
                        ));
                    }

                }

            }
                
            $myallowances = Db::table('employee_allowanceother')
                ->where('employeeid',$teacherid)
                ->where('deleted','0')
                ->get();
                
            $deductiondetails = Db::table('deduction_standarddetail')
                ->where('deleted','0')
                ->get();

            $salarybasistypes = Db::table('employee_salary')
                ->where('deleted','0')
                ->get();

            $salarydeductionbasistypes = Db::table('employee_basistype')
                ->where('deleted','0')
                ->get();
                
            $credentials = Db::table('employee_credentialtypes')
                ->where('deleted','0')
                ->get();

            $employeecredentials = DB::table('employee_credentials')
                ->where('employeeid',$teacherid)
                ->where('deleted','0')
                ->get();

            $currentmonthworkdays   = array();

            $beginmonth             = new DateTime(date('Y-m-01'));

            $endmonth               = new DateTime(date('Y-m-t'));

            $endmonth               = $endmonth->modify( '+1 day' ); 
            
            $intervalmonth          = new DateInterval('P1D');

            $daterangemonth         = new DatePeriod($beginmonth, $intervalmonth ,$endmonth);

            foreach($daterangemonth as $datemonth){

                    array_push($currentmonthworkdays,$datemonth->format("Y-m-d"));

            }

            $employeeattendance     = array();

            foreach($currentmonthworkdays as $currentmonthworkday){

                $getattendance = Db::table('teacherattendance')
                    ->where('teacher_id', $teacherid)
                    ->where('tdate', $currentmonthworkday)
                    ->get();

                if(count($getattendance) == 0){

                    array_push($employeeattendance,(object)array(
                        'date'          =>  date('M d, Y',strtotime($currentmonthworkday)),
                        'day'           =>  date('l',strtotime($currentmonthworkday)),
                        'timerecord'    =>  (object)array(
                                                'amin'  =>  "",
                                                'amout' =>  "",
                                                'pmin'  =>  "",
                                                'pmout' =>  ""
                                            )
                        ));

                }else{

                    array_push($employeeattendance,(object)array(
                        'date'          =>  date('M d, Y',strtotime($currentmonthworkday)),
                        'day'           =>  date('l',strtotime($currentmonthworkday)),
                        'timerecord'    =>  (object)array(
                                                'amin'  =>  date('h:i:s',strtotime($getattendance[0]->in_am)),
                                                'amout' =>  date('h:i:s',strtotime($getattendance[0]->out_am)),
                                                'pmin'  =>  date('h:i:s',strtotime($getattendance[0]->in_pm)),
                                                'pmout' =>  date('h:i:s',strtotime($getattendance[0]->out_pm))
                                            )
                        ));

                }

            }

            $employeerateelevation = DB::table('hr_rateelevation')
                ->where('employeeid',$teacherid)
                ->where('deleted','0')
                ->get();

            // return $deductiontypes;
            return view('hr.employeeprofile')
                ->with('civilstatus',$civilstatus)
                ->with('nationality',$nationality)
                ->with('religion',$religion)
                ->with('profile',$profile)
                ->with('employee_info',$employee_info)
                ->with('employee_accounts',$employee_accounts)
                ->with('employee_familyinfo',$employee_familyinfo)
                ->with('employee_educationinfo',$employee_educationinfo)
                ->with('employee_experience',$employee_experience)
                ->with('employee_basicsalaryinfo',$employee_basicsalaryinfo)
                ->with('employee_timeschedule',$timeschedule)
                ->with('tardinesssetup',$tardinesssetup)
                ->with('department',$department)
                ->with('designations',$designations)
                // ->with('getdesignations',$getdesignations)
                // ->with('benefits',$benefits)
                // ->with('benefitsnotapplied',$benefitsnotapplied)
                ->with('deductiontypes',$deductiontypes)
                ->with('deductiondetails',$deductiondetails)
                ->with('mycontributions',$mystandarddeductions)
                ->with('standardallowances',$standardallowances)
                ->with('myotherdeductions',$myotherdeductions)
                ->with('mystandardallowances',$mystandardallowances)
                ->with('myallowances',$myallowances)
                ->with('salarybasistypes',$salarydeductionbasistypes)
                ->with('salarybasisranks',$salarybasistypes)
                ->with('credentials',$credentials)
                ->with('employeecredentials',$employeecredentials)
                ->with('currentmonthfirstday',date('m-01-Y'))
                ->with('currentmonthlastday',date('m-t-Y'))
                ->with('employeeattendance',$employeeattendance)
                ->with('employeerateelevation',$employeerateelevation);

    }
    public function employeeinfo($action, Request $request)
    {
        
        date_default_timezone_set('Asia/Manila');

        if($action == 'updateheaderinfo'){
            
            DB::table('teacher')
                ->where('id', $request->get('id'))
                ->update([
                    'lastname'      =>  $request->get('lname'),
                    'firstname'     =>  $request->get('fname'),
                    'middlename'    =>  $request->get('mname')
                ]);

            $checkifexists = DB::table('employee_personalinfo')
                ->where('employeeid',$request->get('id'))
                ->get();
                
            if(count($checkifexists)==0){

                Db::table('employee_personalinfo')
                    ->insert([
                        'employeeid'        => $request->get('id'),
                        'dob'               => $request->get('dob'),
                        'gender'            => $request->get('gender'),
                        'address'           => $request->get('address'),
                        'contactnum'        => $request->get('contactnum'),
                        'email'             => $request->get('email'),
                        'spouseemployment'  => $request->get('spouseemployment'),
                        'numberofchildren'  => $request->get('numofchildren'),
                        // 'designationid'     => $request->get('designationid'),
                        'maritalstatusid'   => $request->get('civilstatusid'),
                        'religionid'        => $request->get('religionid'),
                        'nationalityid'     => $request->get('nationalityid'),
                        'date_joined'       => $request->get('datehired')
                    ]);

                DB::table('teacher')
                    ->where('id', $request->get('id'))
                    ->update([
                        'datehired'         => $request->get('datehired')
                    ]);

            }
            else{

                DB::table('employee_personalinfo')
                    ->where('employeeid', $request->get('id'))
                    ->update([
                        'dob'               =>  $request->get('dob'),
                        'gender'            =>  $request->get('gender'),
                        'address'           =>  $request->get('address'),
                        'contactnum'        =>  $request->get('contactnum'),
                        'email'             =>  $request->get('email'),
                        'spouseemployment'  =>  $request->get('spouseemployment'),
                        'numberofchildren'  =>  $request->get('numofchildren'),
                        // 'designationid'     => $request->get('designationid'),
                        'maritalstatusid'   => $request->get('civilstatusid'),
                        'religionid'        => $request->get('religionid'),
                        'nationalityid'     => $request->get('nationalityid'),
                        'date_joined'       => $request->get('datehired')
                    ]);

                DB::table('teacher')
                    ->where('id', $request->get('id'))
                    ->update([
                        'datehired'         => $request->get('datehired')
                    ]);

            }

            return back()->with('linkid',$request->get('linkid'));

        }
        elseif($action == 'updateemergencycontact'){

            $checkifexists = DB::table('employee_personalinfo')
                ->where('employeeid',$request->get('id'))
                ->get();

            if(count($checkifexists)==0){

                Db::table('employee_personalinfo')
                    ->insert([
                        'employeeid'            => $request->get('id'),
                        'emercontactname'       => $request->get('emergencyname'),
                        'emercontactrelation'   => $request->get('relationship'),
                        'emercontactnum'        => $request->get('contactnumber')
                    ]);

            }else{

                DB::table('employee_personalinfo')
                    ->where('employeeid', $request->get('id'))
                    ->update([
                        'emercontactname'       =>  $request->get('emergencyname'),
                        'emercontactrelation'   =>  $request->get('relationship'),
                        'emercontactnum'        =>  $request->get('contactnumber')
                    ]);

                }

            return back()->with('linkid',$request->get('linkid'));

        }
        elseif($action == 'getdesignations'){
            
            $designations = Db::table('usertype')
                ->select(
                    'usertype.id',
                    'usertype.utype as designation',
                    'departmentid'
                )
                ->where('departmentid', $request->get('departmentid'))
                ->where('deleted','0')
                ->get();
                
            if(count($designations) == 0){

                $designations = 0;

                return $designations;

            }else{

                return $designations;

            }

        }
        elseif($action == 'updatedesignation'){
            // return $request->all();
            $checkifexists = Db::table('employee_personalinfo')
                ->where('employeeid',$request->get('id'))
                ->get();
                
            if(count($checkifexists) == 0){

                DB::table('employee_personalinfo')
                    ->insert([
                        'employeeid'        => $request->get('id'),
                        'departmentid'      => $request->get('departmentid'),
                        'designationid'     => $request->get('designationid')
                    ]);

            }else{

                DB::table('employee_personalinfo')
                    ->where('employeeid', $request->get('id'))
                    ->update([
                        'departmentid'      => $request->get('departmentid'),
                        'designationid'     => $request->get('designationid')
                    ]);

                // $userid = Db::table('teacher')
                // ->where('id',$request->get('id'))
                // ->first()->userid;
                DB::table('teacher')
                    ->where('id', $request->get('id'))
                    ->update([
                        'usertypeid'        => $request->get('designationid')
                    ]);
            }
            

            return back();

        }
        elseif($action == 'updateaccounts'){
            // return $request->all();
            $createdby      = DB::table('teacher')
                            ->where('userid', auth()->user()->id)
                            ->first()
                            ->id;
            if($request->get('oldaccountid') == true){

                
                foreach($request->get('oldaccountid') as $oldaccountkey => $accountid){

                    DB::table('employee_accounts')
                        ->where('id',$accountid)
                        ->update([
                            'accountdescription'    => $request->get('oldaccountdescription')[$oldaccountkey],
                            'accountnum'            => $request->get('oldaccountnumber')[$oldaccountkey]
                        ]);

                }

            }
            
            if($request->get('newaccountdescription') == true){

                foreach($request->get('newaccountdescription') as $newaccountkey => $description){

                    $checkifexists = DB::table('employee_accounts')
                                    ->where('employeeid',$request->get('id'))
                                    ->where('accountdescription', 'like','%'.$description)
                                    ->get();
    
                    if(count($checkifexists) == 0){
    
                        DB::table('employee_accounts')
                            ->insert([
                                'employeeid'            => $request->get('id'),
                                'accountdescription'    => strtoupper($description),
                                'accountnum'            => $request->get('newaccountnumber')[$newaccountkey],
                                'createdby'             => $createdby,
                                'createddatetime'       => date('Y-m-d H:i:s')
                            ]);
    
                    }
    
                }
                    
            }

            return back()->with('linkid', $request->get('linkid'));          

        }
        elseif($action == 'deleteaccount'){
            // return $request->all();
            DB::table('employee_accounts')
                ->where('id',$request->get('accountid'))
                ->update([
                    'deleted'   => '1'
                ]);

        }

    }
    // public function employeebenefits($action, Request $request)
    // {
        
    //     $employee_benefits = Db::table('employee_benefits')
    //         ->where('employeeid',$request->get('id'))
    //         ->where('deleted','0')
    //         ->get();
            
    //     foreach($employee_benefits as $updateben){

    //         if(!$request->has($updateben->id)){
                
    //             DB::table('employee_benefits')
    //                 ->where('employeeid', $request->get('id'))
    //                 ->where('benefitsid', $updateben->id)
    //                 ->update([
    //                     'deleted'       =>  1
    //                 ]);

    //         }

    //     }
        
    //     foreach($request->except('id','updatebenefits') as $key => $value){

    //         $checkifexists = DB::table('employee_benefits')
    //             ->where('employeeid', $request->get('id'))
    //             ->where('benefitsid', $key)
    //             ->where('deleted', '0')
    //             ->get();

    //         if(count($checkifexists)==0){

    //             $checkifexists2 = DB::table('employee_benefits')
    //                 ->where('employeeid', $request->get('id'))
    //                 ->where('benefitsid', $key)
    //                 ->where('deleted', '1')
    //                 ->get();

    //             if(count($checkifexists2)==0){

    //                 DB::table('employee_benefits')
    //                     ->insert([
    //                         'employeeid' => $request->get('id'),
    //                         'benefitsid' => $key,
    //                         'benefitnum' => $value
    //                     ]);

    //             }else{

    //                 DB::table('employee_benefits')
    //                     ->where('employeeid', $request->get('id'))
    //                     ->where('benefitsid', $key)
    //                     ->update([
    //                         'benefitnum'    =>  $value,
    //                         'deleted'       =>  1
    //                     ]);
    //                 }
    //         }else{

    //             DB::table('employee_benefits')
    //                 ->where('employeeid', $request->get('id'))
    //                 ->where('benefitsid', $key)
    //                 ->update([
    //                     'benefitnum'    =>  $value,
    //                     'deleted'       =>  1
    //                 ]);

    //             }

    //     }

    //     return back();

    // }
    public function employeefamilyinfo($action, Request $request)
    {
        
        date_default_timezone_set('Asia/Manila');

        $getMyid = DB::table('teacher')
            ->select('id')
            ->where('userid', auth()->user()->id)
            // ->where('isactive','1')
            // ->where('deleted','0')
            ->first();

        if($action == 'delete'){

            Db::table('employee_familyinfo')
                ->where('employeeid',$request->get('employeeid'))
                ->where('id',$request->get('familymemberid'))
                ->where('deleted','0')
                ->update([
                    'deleted' => '1',
                    'updated_by' => $getMyid->id,
                    'updated_on' => date('Y-m-d H:i:s')
                ]);

            // return redirect()->back()->with("messageUpdated", 'Family Information Update!'.' '.$request->get('familyname').' has been deleted successfully');

        }

        $employee_familyinfo = Db::table('employee_familyinfo')
            ->select('id')
            ->where('employeeid',$request->get('id'))
            ->where('deleted','0')
            ->get();
            
        if($request->get('oldid') == true){

            foreach($request->get('oldid') as $key => $value){
                
                Db::table('employee_familyinfo')
                    ->where('employeeid',$request->get('id'))
                    ->where('id',$value)
                    ->where('deleted','0')
                    ->update([
                        'famname'       => $request->get('oldfamilyname')[$key],
                        'famrelation'   => $request->get('oldfamilyrelation')[$key],
                        'dob'           => $request->get('oldfamilydob')[$key],
                        'contactnum'    => $request->get('oldfamilynum')[$key],
                        'updated_by'    => $getMyid->id,
                        'updated_on'    => date('Y-m-d H:i:s')
                    ]);

            }

        }

        $familyarray = array();

        if($request->get('familyname') == true){

            foreach($request->get('familyname') as $key => $value){
                
                array_push($familyarray,(object)array(
                    'familyname'        => $value,
                    'familyrelation'    => $request->get('familyrelation')[$key],
                    'familydob'         => $request->get('familydob')[$key],
                    'familynum'         => $request->get('familynum')[$key]
                ));

            }

            foreach($familyarray as $family){

                $checkifexists = Db::table('employee_familyinfo')
                    ->where('employeeid',$request->get('id'))
                    ->where('famname','like','%'.$family->familyname)
                    ->where('deleted','0')
                    ->get();

                if(count($checkifexists)==0){

                    Db::table('employee_familyinfo')
                        ->insert([
                            'employeeid'    => $request->get('id'),
                            'famname'       => $family->familyname,
                            'famrelation'   => $family->familyrelation,
                            'dob'           => $family->familydob,
                            'contactnum'    => $family->familynum,
                            'updated_by'    => $getMyid->id,
                            'updated_on'    => date('Y-m-d H:i:s')
                        ]);

                }else{

                    Db::table('employee_familyinfo')
                        ->where('employeeid',$request->get('id'))
                        ->where('deleted','0')
                        ->update([
                            'famname'       => $family->familyname,
                            'famrelation'   => $family->familyrelation,
                            'dob'           => $family->familydob,
                            'contactnum'    => $family->familynum,
                            'updated_by'    => $getMyid->id,
                            'updated_on'    => date('Y-m-d H:i:s')
                        ]);

                }

            }

        }

        return back();

    }
    public function employeeeducationinfo($action, Request $request)
    {

        date_default_timezone_set('Asia/Manila');

        $getMyid = DB::table('teacher')
            ->select('id')
            ->where('userid', auth()->user()->id)
            // ->where('isactive','1')
            // ->where('deleted','0')
            ->first();

        $employeeeducationinfo = Db::table('employee_educationinfo')
            ->where('deleted','0')
            ->get();

        if($action == 'updateeducationinfo'){

            if($request->get('oldid') == true){

                foreach($employeeeducationinfo as $educinfo){

                    $exists = 0;

                    if(in_array($educinfo->id, $request->get('oldid'))){

                    }
                    else{

                        DB::table('employee_educationinfo')
                            ->where('employeeid',$request->get('id'))
                            ->where('id',$educinfo->id)
                            ->update([
                                'deleted'       => '1',
                                'updated_by'    => $getMyid->id,
                                'updated_on'    => date('Y-m-d H:i:s')
                            ]);

                    }

                }

                foreach($request->get('oldid') as $key => $value){

                    DB::table('employee_educationinfo')
                        ->where('employeeid', $request->get('id'))
                        ->where('id',$value)
                        ->update([
                            'schoolname'        => $request->get('oldschoolname')[$key],
                            'schooladdress'     => $request->get('oldaddress')[$key],
                            'coursetaken'       => $request->get('oldcoursetaken')[$key],
                            'major'             => $request->get('oldmajor')[$key],
                            'completiondate'    => $request->get('olddatecompleted')[$key],
                            'updated_by'        => $getMyid->id,
                            'updated_on'        => date('Y-m-d H:i:s')
                        ]);

                }

            }
            else{
                
                if(count($request->except('id'))==0){

                    foreach($employeeeducationinfo as $education){
                        
                        $erer = DB::table('employee_educationinfo')
                            ->where('employeeid', $request->get('id'))
                            ->where('id',$education->id)
                            ->update([
                                'deleted'       => '1',
                                'updated_by'    => $getMyid->id,
                                'updated_on'    => date('Y-m-d H:i:s')
                            ]);
                            
                    }

                }else{

                    foreach($request->get('schoolname') as $key => $value){

                        $checkifexists = Db::table('employee_educationinfo')
                            ->where('completiondate',$request->get('datecompleted')[$key])
                            ->get();

                        if(count($checkifexists) == 0){

                            DB::table('employee_educationinfo')
                                ->insert([
                                    'employeeid'        => $request->get('id'),
                                    'schoolname'        => $value,
                                    'schooladdress'     => $request->get('address')[$key],
                                    'coursetaken'       => $request->get('coursetaken')[$key],
                                    'major'             => $request->get('major')[$key],
                                    'completiondate'    => $request->get('datecompleted')[$key],
                                    'updated_by'        => $getMyid->id,
                                    'updated_on'        => date('Y-m-d H:i:s')
                                ]);

                        }else{

                            DB::table('employee_educationinfo')
                                ->where('employeeid', $request->get('id'))
                                ->where('id',$checkifexists[0]->id)
                                ->update([
                                    'schoolname'        => $value,
                                    'schooladdress'     => $request->get('address')[$key],
                                    'coursetaken'       => $request->get('coursetaken')[$key],
                                    'major'             => $request->get('major')[$key],
                                    'completiondate'    => $request->get('datecompleted')[$key],
                                    'updated_by'        => $getMyid->id,
                                    'updated_on'        => date('Y-m-d H:i:s')
                                ]);

                        }

                    }

                }

            }

        }

        return back();

    }
    public function employeeexperience($action, Request $request)
    {

        date_default_timezone_set('Asia/Manila');
        
        $getMyid = DB::table('teacher')
            ->select('id')
            ->where('userid', auth()->user()->id)
            // ->where('isactive','1')
            // ->where('deleted','0')
            ->first();
            
        if($action == 'updateexperience'){
            $employeeexperience = Db::table('employee_experience')
                ->where('employeeid',$request->get('id'))
                ->where('deleted','0')
                ->get();
                
            if($request->get('oldid') == true){
                
                foreach($request->get('oldid') as $key => $value){
                        DB::table('employee_experience')
                            ->where('employeeid', $request->get('id'))
                            ->where('id',$value)
                            ->update([
                                'companyname'       => $request->get('oldcompanyname')[$key],
                                'companyaddress'    => $request->get('oldlocation')[$key],
                                'position'          => $request->get('oldjobposition')[$key],
                                'periodfrom'        => $request->get('oldperiodfrom')[$key],
                                'periodto'          => $request->get('oldperiodto')[$key],
                                'updated_by'        => $getMyid->id,
                                'updated_on'        => date('Y-m-d H:i:s')
                            ]);
                }
    
            }
            else{
                
                if(count($request->except('id'))==0){
                    
                    foreach($employeeexperience as $experience){
                        
                        $erer = DB::table('employee_experience')
                            ->where('employeeid', $request->get('id'))
                            ->where('id',$experience->id)
                            ->update([
                                'deleted'       => '1',
                                'updated_by'    => $getMyid->id,
                                'updated_on'    => date('Y-m-d H:i:s')
                            ]);
                            
                    }
    
                }
                else{
                    
                    foreach($request->get('companyname') as $key => $value){
    
                        $checkifexists = Db::table('employee_experience')
                            ->where('companyname','like','%'.$value)
                            ->where('employeeid',$request->get('id'))
                            ->where('deleted','0')
                            ->get();
                            
                        if(count($checkifexists) == 0){
    
                            DB::table('employee_experience')
                                ->insert([
                                    'employeeid'        => $request->get('id'),
                                    'companyname'       => $value,
                                    'companyaddress'    => $request->get('location')[$key],
                                    'position'          => $request->get('jobposition')[$key],
                                    'periodfrom'        => $request->get('periodfrom')[$key],
                                    'periodto'          => $request->get('periodto')[$key],
                                    'updated_by'        => $getMyid->id,
                                    'updated_on'        => date('Y-m-d H:i:s')
                                ]);
    
                        }else{
    
                            DB::table('employee_experience')
                                ->where('employeeid', $request->get('id'))
                                ->where('id',$checkifexists[0]->id)
                                ->update([
                                    'companyname'       => $value,
                                    'companyaddress'    => $request->get('location')[$key],
                                    'position'          => $request->get('position')[$key],
                                    'periodfrom'        => $request->get('periodfrom')[$key],
                                    'periodto'          => $request->get('periodto')[$key],
                                    'updated_by'        => $getMyid->id,
                                    'updated_on'        => date('Y-m-d H:i:s')
                                ]);
    
                        }
    
                    }
    
                }
    
            }
        }else{

            // return $request->all();
            DB::table('employee_experience')
                ->where('employeeid', $request->get('employeeid'))
                ->where('id', $request->get('experienceid'))
                ->update([
                    'deleted'   => 1
                ]);
        }

        return back();

    }
    // public function employeebasicsalaryinfo(Request $request)
    // {
    //     // return $request->all();
    //     date_default_timezone_set('Asia/Manila');
        
    //     $getMyid = DB::table('teacher')
    //         ->select('id')
    //         ->where('userid', auth()->user()->id)
    //         // ->where('isactive','1')
    //         // ->where('deleted','0')
    //         ->first();

    //     $employeebasicsalaryinfo = Db::table('employee_basicsalaryinfo')
    //         ->where('employeeid',$request->get('employeeid'))
    //         ->where('deleted','0')
    //         ->get();

    //     $salarytype = Db::table('employee_basistype')
    //         ->where('id',$request->get('salarybasistype'))
    //         ->get();
            
    //     $monday = 0;
    //     $tuesday = 0;
    //     $wednesday = 0;
    //     $thursday = 0;
    //     $friday = 0;
    //     $saturday = 0;
    //     $sunday = 0;
    //     $mondaytotalhours = 0;
    //     $tuesdaytotalhours = 0;
    //     $wednesdaytotalhours = 0;
    //     $thursdaytotalhours = 0;
    //     $fridaytotalhours = 0;
    //     $saturdaytotalhours = 0;
    //     $sundaytotalhours = 0;
    //     $holiday = 0;

    //     if(strtolower($salarytype[0]->type) == 'hourly'){
            
    //         if($request->get('hoursperweek') == 0){

    //             return back();

    //         }
    //         else{

    //             foreach($request->get('daysrender') as $dayrenderkey => $dayrendervalue){

    //                 if($dayrendervalue == 'monday'){

    //                     $monday += 1;
    //                     $mondaytotalhours += $request->get('nodaysrender')[$dayrenderkey];

    //                 }
    //                 elseif($dayrendervalue == 'tuesday'){

    //                     $tuesday += 1;
    //                     $tuesdaytotalhours += $request->get('nodaysrender')[$dayrenderkey];

    //                 }
    //                 elseif($dayrendervalue == 'wednesday'){

    //                     $wednesday += 1;
    //                     $wednesdaytotalhours += $request->get('nodaysrender')[$dayrenderkey];

    //                 }
    //                 elseif($dayrendervalue == 'thursday'){

    //                     $thursday += 1;
    //                     $thursdaytotalhours += $request->get('nodaysrender')[$dayrenderkey];

    //                 }
    //                 elseif($dayrendervalue == 'friday'){

    //                     $friday += 1;
    //                     $fridaytotalhours += $request->get('nodaysrender')[$dayrenderkey];

    //                 }
    //                 elseif($dayrendervalue == 'saturday'){

    //                     $saturday += 1;
    //                     $saturdaytotalhours += $request->get('nodaysrender')[$dayrenderkey];

    //                 }
    //                 elseif($dayrendervalue == 'sunday'){

    //                     $sunday += 1;
    //                     $sundaytotalhours += $request->get('nodaysrender')[$dayrenderkey];

    //                 }

    //             }
    //             if(count($employeebasicsalaryinfo) == 0){

    //                 Db::table('employee_basicsalaryinfo')
    //                     ->insert([
    //                         'employeeid'        => $request->get('employeeid'),
    //                         'amount'            => $request->get('salaryamount'),
    //                         'paymenttype'       => $request->get('paymenttype'),
    //                         'salarybasistype'   => $request->get('salarybasistype'),
    //                         'hoursperweek'      => $request->get('hoursperweek'),
    //                         'mondays'           => $monday,
    //                         'tuesdays'          => $tuesday,
    //                         'wednesdays'        => $wednesday,
    //                         'thursdays'         => $thursday,
    //                         'fridays'           => $friday,
    //                         'sundays'           => $sunday,
    //                         'saturdays'         => $saturday,
    //                         'mondayhours'       => $mondaytotalhours,
    //                         'tuesdayhours'      => $tuesdaytotalhours,
    //                         'wednesdayhours'    => $wednesdaytotalhours,
    //                         'thursdayhours'     => $thursdaytotalhours,
    //                         'fridayhours'       => $fridaytotalhours,
    //                         'sundayhours'       => $sundaytotalhours,
    //                         'saturdayhours'     => $saturdaytotalhours,
    //                         'holidays'          => $holiday
    //                     ]);

    //             }else{

    //                 Db::table('employee_basicsalaryinfo')
    //                     ->where('employeeid',$request->get('employeeid'))
    //                     ->update([
    //                         'employeeid'        => $request->get('employeeid'),
    //                         'amount'            => $request->get('salaryamount'),
    //                         'paymenttype'       => $request->get('paymenttype'),
    //                         'salarybasistype'   => $request->get('salarybasistype'),
    //                         'hoursperday'       => $request->get('hoursperweek'),
    //                         'mondays'           => $monday,
    //                         'tuesdays'          => $tuesday,
    //                         'wednesdays'        => $wednesday,
    //                         'thursdays'         => $thursday,
    //                         'fridays'           => $friday,
    //                         'sundays'           => $sunday,
    //                         'saturdays'         => $saturday,
    //                         'mondayhours'       => $mondaytotalhours,
    //                         'tuesdayhours'      => $tuesdaytotalhours,
    //                         'wednesdayhours'    => $wednesdaytotalhours,
    //                         'thursdayhours'     => $thursdaytotalhours,
    //                         'fridayhours'       => $fridaytotalhours,
    //                         'sundayhours'       => $sundaytotalhours,
    //                         'saturdayhours'     => $saturdaytotalhours,
    //                         'holidays'          => $holiday
    //                     ]);

    //             }

    //         }

    //     }
    //     elseif(strtolower($salarytype[0]->type) == 'daily'){
            
    //         if(count($employeebasicsalaryinfo) == 0){

    //             Db::table('employee_basicsalaryinfo')
    //                 ->insert([
    //                     'employeeid'        => $request->get('employeeid'),
    //                     'amount'            => $request->get('salaryamount'),
    //                     'paymenttype'       => $request->get('paymenttype'),
    //                     'salarybasistype'   => $request->get('salarybasistype'),
    //                     'hoursperday'       => $request->get('hoursperday')
    //                 ]);

    //         }
    //         else{

    //             Db::table('employee_basicsalaryinfo')
    //                 ->where('employeeid',$request->get('employeeid'))
    //                 ->update([
    //                     'amount'            => $request->get('salaryamount'),
    //                     'paymenttype'       => $request->get('paymenttype'),
    //                     'salarybasistype'   => $request->get('salarybasistype'),
    //                     'hoursperday'       => $request->get('hoursperday')
    //                 ]);

    //         }

    //     }
    //     elseif(strtolower($salarytype[0]->type) == 'monthly'){

    //         if($request->get('workonsat') == true){

    //             $saturday = 1;

    //         }else{

    //             $saturday = 0;

    //         }
    //         if($request->get('workonsun') == true){

    //             $sunday = 1;

    //         }else{

    //             $sunday = 0;

    //         }

    //         if(count($employeebasicsalaryinfo) == 0){

    //             Db::table('employee_basicsalaryinfo')
    //                 ->insert([
    //                     'employeeid'        => $request->get('employeeid'),
    //                     'amount'            => $request->get('salaryamount'),
    //                     'paymenttype'       => $request->get('paymenttype'),
    //                     'salarybasistype'   => $request->get('salarybasistype'),
    //                     'hoursperday'       => $request->get('hoursperday'),
    //                     'saturdays'         => $saturday,
    //                     'sundays'           => $sunday
    //                 ]);

    //         }
    //         else{

    //             Db::table('employee_basicsalaryinfo')
    //                 ->where('employeeid',$request->get('employeeid'))
    //                 ->update([
    //                     'amount'            => $request->get('salaryamount'),
    //                     'paymenttype'       => $request->get('paymenttype'),
    //                     'salarybasistype'   => $request->get('salarybasistype'),
    //                     'hoursperday'       => $request->get('hoursperday'),
    //                     'saturdays'         => $saturday,
    //                     'sundays'           => $sunday
    //                 ]);

    //         }

    //     }elseif(strtolower($salarytype[0]->type) == 'project'){

    //         if($request->get('projectradiosettingtype') == 'perday'){
                
    //             if(count($employeebasicsalaryinfo) == 0){

    //                 Db::table('employee_basicsalaryinfo')
    //                     ->insert([
    //                         'employeeid'        => $request->get('employeeid'),
    //                         'amount'            => $request->get('perdayamount'),
    //                         'paymenttype'       => $request->get('paymenttype'),
    //                         'salarybasistype'   => $request->get('salarybasistype'),
    //                         'projectbasedtype'  => $request->get('projectradiosettingtype'),
    //                         'hoursperday'       => $request->get('perdayhours')
    //                     ]);

    //             }
    //             else{

    //                 Db::table('employee_basicsalaryinfo')
    //                     ->where('employeeid',$request->get('employeeid'))
    //                     ->update([
    //                         'amount'            => $request->get('perdayamount'),
    //                         'paymenttype'       => $request->get('paymenttype'),
    //                         'salarybasistype'   => $request->get('salarybasistype'),
    //                         'projectbasedtype'  => $request->get('projectradiosettingtype'),
    //                         'hoursperday'       => $request->get('perdayhours')
    //                     ]);

    //             }

    //         }
    //         elseif($request->get('projectradiosettingtype') == 'persalaryperiod'){
                
    //             if(count($employeebasicsalaryinfo) == 0){

    //                 Db::table('employee_basicsalaryinfo')
    //                     ->insert([
    //                         'employeeid'        => $request->get('employeeid'),
    //                         'amount'            => $request->get('persalaryperiodamount'),
    //                         'paymenttype'       => $request->get('paymenttype'),
    //                         'salarybasistype'   => $request->get('salarybasistype'),
    //                         'projectbasedtype'  => $request->get('projectradiosettingtype')
    //                     ]);

    //             }
    //             else{

    //                 Db::table('employee_basicsalaryinfo')
    //                     ->where('employeeid',$request->get('employeeid'))
    //                     ->update([
    //                         'amount'            => $request->get('persalaryperiodamount'),
    //                         'paymenttype'       => $request->get('paymenttype'),
    //                         'salarybasistype'   => $request->get('salarybasistype'),
    //                         'projectbasedtype'  => $request->get('projectradiosettingtype')
    //                     ]);

    //             }

    //         }
    //         elseif($request->get('projectradiosettingtype') == 'permonth'){
                
    //             if(count($employeebasicsalaryinfo) == 0){

    //                 Db::table('employee_basicsalaryinfo')
    //                     ->insert([
    //                         'employeeid'        => $request->get('employeeid'),
    //                         'amount'            => $request->get('permonthamount'),
    //                         'paymenttype'       => $request->get('paymenttype'),
    //                         'salarybasistype'   => $request->get('salarybasistype'),
    //                         'projectbasedtype'  => $request->get('projectradiosettingtype'),
    //                         'hoursperday'       => $request->get('permonthhours')
    //                     ]);

    //             }
    //             else{

    //                 Db::table('employee_basicsalaryinfo')
    //                     ->where('employeeid',$request->get('employeeid'))
    //                     ->update([
    //                         'amount'            => $request->get('permonthamount'),
    //                         'paymenttype'       => $request->get('paymenttype'),
    //                         'salarybasistype'   => $request->get('salarybasistype'),
    //                         'projectbasedtype'  => $request->get('projectradiosettingtype'),
    //                         'hoursperday'       => $request->get('permonthhours')
    //                     ]);

    //             }

    //         }

    //         return back()->with('linkid',$request->get('linkid'));

    //     }
        
    //     return back()->with('linkid',$request->get('linkid'));

    // }
    // public function employeecustomattendancesetting($action,Request $request)
    // {
    //     date_default_timezone_set('Asia/Manila');
    //     return $action;
    // }
    public function employeecontributions(Request $request)
    {
        date_default_timezone_set('Asia/Manila');
        
        $getMyid = DB::table('teacher')
            ->select('id')
            ->where('userid', auth()->user()->id)
            // ->where('isactive','1')
            // ->where('deleted','0')
            ->first();

        foreach($request->except('employeeid','deductiontypes','ersamounts','eesamounts') as $statuskey => $statusvalue){

            // $status = 0;

            foreach($request->get('deductiontypes') as $deductiontypekey => $deductiontypevalue){
            
                // return $request->except('employeeid','deductiontypes','ersamounts','eesamounts');
                if(str_replace('contributionstatus', '', $statuskey) == $deductiontypevalue){

                    if($statusvalue == 'active'){

                        $status = 1;

                    }
                    elseif($statusvalue == 'inactive'){

                        $status = 0;

                    }
                    
                    $checkifexists = Db::table('employee_deductionstandard')
                        ->where('employeeid', $request->get('employeeid'))
                        ->where('deduction_typeid', $deductiontypevalue)
                        ->get();
                        
                    if(count($checkifexists) == 0){
        
                        DB::table('employee_deductionstandard')
                            ->insert([
                                'employeeid'        => $request->get('employeeid'),
                                'deduction_typeid'  => $deductiontypevalue,
                                'ersamount'         => $request->get('ersamounts')[$deductiontypekey],
                                'eesamount'         => $request->get('eesamounts')[$deductiontypekey],
                                'status'            => $status,
                                'updatedby'         => $getMyid->id,
                                'updateddatetime'   => date('Y-m-d H:i:s')
                            ]);
        
                    }
                    else{
                        // return $checkifexists;
                        DB::table('employee_deductionstandard')
                            ->where('employeeid',$request->get('employeeid'))
                            ->where('deduction_typeid',$deductiontypevalue)
                            ->update([
                                'ersamount'         => $request->get('ersamounts')[$deductiontypekey],
                                'eesamount'         => $request->get('eesamounts')[$deductiontypekey],
                                'status'            => $status,
                                'updatedby'         => $getMyid->id,
                                'updateddatetime'   => date('Y-m-d H:i:s')
                            ]);
        
                    }


                }

                
            }

        }

        return back()->with('linkid',$request->get('linkid'));
        
    }
    public function employeedeductions($id,Request $request)
    {

        date_default_timezone_set('Asia/Manila');
        
        if($id == 'getdeductiondetail'){

            $deductiondetails = Db::table('deduction_standarddetail')
                ->where('deduction_typeid',$request->get('deductiontypeid'))
                ->get();

            return $deductiondetails;

        }

    }
    // public function employeecustomtimesched($id,Request $request)
    // {
        
    //     date_default_timezone_set('Asia/Manila');
        
    //     $getMyid = DB::table('teacher')
    //         ->select('id')
    //         ->where('userid', auth()->user()->id)
    //         // ->where('isactive','1')
    //         // ->where('deleted','0')
    //         ->first();
    //     // return $request->all();
    //     $timeshift = Crypt::decrypt($id);
        
    //     $checkifexists = Db::table('employee_customtimesched')
    //         ->where('employeeid',$request->get('employeeid'))
    //         ->where('deleted','0')
    //         ->get();
    //     if(count($checkifexists) == 0){

    //         if($timeshift == 'am_in'){
    //             DB::table('employee_customtimesched')
    //                 ->insert([
    //                     'employeeid'    => $request->get('employeeid'),
    //                     'amin'          => $request->get('am_in'),
    //                     'createdby'     => $getMyid->id,
    //                     'createdon'     =>  date('Y-m-d H:i:s')
    //                 ]);
                    
    //         }
    //         elseif($timeshift == 'am_out'){

    //             DB::table('employee_customtimesched')
    //                 ->insert([
    //                     'employeeid'    => $request->get('employeeid'),
    //                     'amout'        => $request->get('am_out'),
    //                     'createdby'     => $getMyid->id,
    //                     'createdon'     =>  date('Y-m-d H:i:s')
    //                 ]);
                    
    //         }
    //         elseif($timeshift == 'pm_in'){

    //             DB::table('employee_customtimesched')
    //                 ->insert([
    //                     'employeeid'    => $request->get('employeeid'),
    //                     'pmin'          => $request->get('pm_in'),
    //                     'createdby'     => $getMyid->id,
    //                     'createdon'     =>  date('Y-m-d H:i:s')
    //                 ]);
                    
    //         }
    //         elseif($timeshift == 'pm_out'){

    //             DB::table('employee_customtimesched')
    //                 ->insert([
    //                     'employeeid'    => $request->get('employeeid'),
    //                     'pmout'         => $request->get('pm_out'),
    //                     'createdby'     => $getMyid->id,
    //                     'createdon'     =>  date('Y-m-d H:i:s')
    //                 ]);
                    
    //         }
    //     }else{

    //         if($timeshift == 'am_in'){
    //             $explode = explode(':', $request->get('am_in'));
    //             if($explode[0] == '00')
    //             {
    //                 $amin = null;
    //             }else{
    //                 $amin = $request->get('am_in');
    //             }
    //             DB::table('employee_customtimesched')
    //                 ->where('employeeid', $request->get('employeeid'))
    //                 ->update([
    //                     'amin'          => $amin
    //                 ]);
                    
    //         }
    //         elseif($timeshift == 'am_out'){

    //             $explode = explode(':', $request->get('am_out'));
    //             if($explode[0] == '00')
    //             {
    //                 $amout = null;
    //             }else{
    //                 $amout = $request->get('am_out');
    //             }
    //             DB::table('employee_customtimesched')
    //                 ->where('employeeid', $request->get('employeeid'))
    //                 ->update([
    //                     'amout'        => $amout
    //                 ]);
                    
    //         }
    //         elseif($timeshift == 'pm_in'){

    //             $explode = explode(':', $request->get('pm_in'));
    //             if($explode[0] == '00')
    //             {
    //                 $pmin = null;
    //             }else{
    //                 $pmin = $request->get('pm_in');
    //             }
    //             DB::table('employee_customtimesched')
    //                 ->where('employeeid', $request->get('employeeid'))
    //                 ->update([
    //                     'pmin'          => $pmin
    //                 ]);
                    
    //         }
    //         elseif($timeshift == 'pm_out'){
    //             $explode = explode(':', $request->get('pm_out'));
    //             if($explode[0] == '00')
    //             {
    //                 $pmout = null;
    //             }else{
    //                 $pmout = $request->get('pm_out');
    //             }
    //             DB::table('employee_customtimesched')
    //                 ->where('employeeid', $request->get('employeeid'))
    //                 ->update([
    //                     'pmout'         => $pmout
    //                 ]);
                    
    //         }

    //     }

    // }
    public function employeeotherdeductionsinfo(Request $request)
    {

        date_default_timezone_set('Asia/Manila');
        // return $request->all();
        $getMyid = DB::table('teacher')
            ->select('id')
            ->where('userid', auth()->user()->id)
            // ->where('isactive','1')
            // ->where('deleted','0')
            ->first();

        foreach($request->get('description') as $otherdeductkey => $otherdeductvalue){

            $checkifexists = Db::table('employee_deductionother')
                ->where('description','like','%'.$otherdeductvalue)
                ->where('employeeid',$request->get('employeeid'))
                ->where('deleted','0')
                ->get();

            if(count($checkifexists) == 0){

                if($request->get('totalamount')[$otherdeductkey] != null && $request->get('term')[$otherdeductkey] != null){

                    if($request->get('startdates')[$otherdeductkey]> date('Y-m-d'))
                    {
                        $status = 0;
                    }
                    elseif($request->get('startdates')[$otherdeductkey]<=date('Y-m-d'))
                    {
                        $status = 1;
                    }
                    $getOtherDeductid = Db::table('employee_deductionother')
                    ->insertGetId([
                        'employeeid'        => $request->get('employeeid'),
                        'description'       => $otherdeductvalue,
                        'amount'            => $request->get('totalamount')[$otherdeductkey],
                        'status'            => $status,
                        'term'              => $request->get('term')[$otherdeductkey],
                        'dateissued'        => $request->get('startdates')[$otherdeductkey]
                    ]);

                Db::table('employee_deductionotherdetail')
                    ->insert([
                        'headerid'          => $getOtherDeductid,
                        'amountpaid'        => '0',
                        'status'            => $status,
                        'updatedby'         => $getMyid->id,
                        'updateddatetime'   => $request->get('startdates')[$otherdeductkey]
                    ]);
                }
                
            }else{
                // return back()->with('linkid',$request->get('linkid'));
            }

        }
        
        return back()->with('linkid',$request->get('linkid'));

    }
    public function employeeotherdeductionsinfostatusupdate(Request $request)
    {
        // return $request->all();
        Db::table('employee_deductionother')
            ->where('employeeid', $request->get('employeeid'))
            ->where('id', $request->get('otherdeductionid'))
            ->update([
                'status'    => $request->get('newstatus')
            ]);
    }
    // public function employeeotherdeductionsinfoedit(Request $request)
    // {
        
    //     Db::table('employee_deductionother')
    //         ->where('id',$request->get('otherdeductionid'))
    //         ->where('employeeid',$request->get('employeeid'))
    //         ->update([
    //             'description'   =>  $request->get('description'),
    //             'amount'        =>  str_replace( ',', '', $request->get('amount') ),
    //             'term'          =>  $request->get('term')
    //         ]);

    //     return back()->with('linkid',$request->get('linkid'));

    // }
    // public function employeeotherdeductionsinfodelete(Request $request)
    // {
        
    //     Db::table('employee_deductionother')
    //         ->where('id',$request->get('otherdeductionid'))
    //         ->where('employeeid',$request->get('employeeid'))
    //         ->update([
    //             'deleted'   =>  '1'
    //         ]);

    //     return back()->with('linkid',$request->get('linkid'));

    // }
    public function employeestandardallowances(Request $request)
    {

        date_default_timezone_set('Asia/Manila');
        
        $getMyid = DB::table('teacher')
            ->select('id')
            ->where('userid', auth()->user()->id)
            // ->where('isactive','1')
            // ->where('deleted','0')
            ->first();
        foreach($request->get('allowanceid') as $allowancekey => $allowancevalue){
            
            $status = 0;

            foreach($request->except('employeeid','allowanceid','amounts','linkid') as $statuskey => $statusvalue){
                
                if(str_replace('allowancestatus', '', $statuskey) == $allowancevalue){

                    if($statusvalue == 'active'){

                        $status = 1;

                    }
                    elseif($statusvalue == 'inactive'){

                        $status = 0;

                    }

                }
                
            }

            $checkifexists = Db::table('employee_allowancestandard')
                ->where('employeeid', $request->get('employeeid'))
                ->where('allowance_standardid', $allowancevalue)
                ->get();

            if(count($checkifexists) == 0){

                DB::table('employee_allowancestandard')
                    ->insert([
                        'employeeid'            => $request->get('employeeid'),
                        'allowance_standardid'  => $allowancevalue,
                        'amount'                => $request->get('amounts')[$allowancekey],
                        'status'                => $status
                        
                    ]);

            }
            else{

                DB::table('employee_allowancestandard')
                    ->where('employeeid',$request->get('employeeid'))
                    ->where('allowance_standardid',$allowancevalue)
                    ->update([
                        'amount'         => $request->get('amounts')[$allowancekey],
                        'status'         => $status
                    ]);

            }

        }
        
        return back()->with('linkid',$request->get('linkid'));
        
    }
    public function employeeallowanceinfo(Request $request)
    {

        date_default_timezone_set('Asia/Manila');
        
        $getMyid = DB::table('teacher')
            ->select('id')
            ->where('userid', auth()->user()->id)
            // ->where('isactive','1')
            // ->where('deleted','0')
            ->first();

        foreach($request->get('description') as $allowancekey => $allowancevalue){

            $checkifexists = Db::table('employee_allowanceother')
                ->where('description','like','%'.$allowancevalue)
                ->where('employeeid',$request->get('employeeid'))
                ->get();

            if(count($checkifexists) == 0){

                Db::table('employee_allowanceother')
                    ->insert([
                        'employeeid'    => $request->get('employeeid'),
                        'description'   => $allowancevalue,
                        'amount'        => $request->get('amount')[$allowancekey],
                        'term'          => $request->get('term')[$allowancekey]
                    ]);
                
            }

        }

        return back()->with('linkid',$request->get('linkid'));

    }
    public function employeeotherallowanceinfoedit(Request $request)
    {
        
        Db::table('employee_allowanceother')
            ->where('id',$request->get('otherallowanceid'))
            ->where('employeeid',$request->get('employeeid'))
            ->update([
                'description'   =>  $request->get('description'),
                'amount'        =>  str_replace( ',', '', $request->get('amount') ),
                'term'          =>  $request->get('term')
            ]);

        return back()->with('linkid',$request->get('linkid'));

    }
    public function employeeotherallowanceinfodelete(Request $request)
    {
        
        Db::table('employee_allowanceother')
            ->where('id',$request->get('otherallowanceid'))
            ->where('employeeid',$request->get('employeeid'))
            ->update([
                'deleted'   => '1'
            ]);

        return back()->with('linkid',$request->get('linkid'));

    }
    public function employeerateelevation(Request $request)
    {
        date_default_timezone_set('Asia/Manila');
        // return $request->all();
        // if($request->get('oldamount') == $request->get('rateelevationamount')){
        //     return back();
        // }
        
        $checkifrequestexists = DB::table('hr_rateelevation')
            ->where('employeeid', $request->get('id'))
            ->where('status','0')
            ->where('deleted','0')
            ->get();

        if(count($checkifrequestexists) == 0){
            DB::table('hr_rateelevation')
                ->insert([
                    'employeeid'        => $request->get('id'),
                    'oldsalary'        => $request->get('oldamount'),
                    'newsalary'        => $request->get('rateelevationamount'),
                    'createddatetime'   => date('Y-m-d H:i:s')
                ]);
        }else{
            DB::table('hr_rateelevation')
                ->where('employeeid', $request->get('id'))
                ->where('status','0')
                ->where('deleted','0')
                ->update([
                    'newsalary'        => $request->get('rateelevationamount'),
                    'createddatetime'   => date('Y-m-d H:i:s')
                ]);
        }

        return back()->with('linkid', $request->get('linkid'));

    }
    // public function employeecashadvanceinfo(Request $request)
    // {

    //     date_default_timezone_set('Asia/Manila');
        
    //     foreach($request->get('amount') as $key => $value){
            
    //         $checkifexists = Db::table('employee_cashadvanceinfo')
    //             ->where('employeeid',$request->get('employeeid'))
    //             ->where('amount',$value)
    //             ->where('basistypeid',$request->get('basistypeids')[$key])
    //             ->where('dateissued',date('Y-m-d H:i:s'))
    //             ->get();

    //         if(count($checkifexists) == 0){

    //             Db::table('employee_cashadvanceinfo')
    //                 ->insert([
    //                     'employeeid'        => $request->get('employeeid'),
    //                     'amount'            => $value,
    //                     'basistypeid'       => $request->get('basistypeids')[$key],
    //                     'dateissued'        => date('Y-m-d H:i:s')
    //                 ]);
                
    //         }

    //     } 
        
    //     return back();

    // }
    public function salary($id,Request $request)
    {

        $id = Crypt::decrypt($id);

        date_default_timezone_set('Asia/Manila');

        if($id == 'dashboard'){

            $salaries  = Db::table('employee_salary')
                ->where('deleted','0')
                ->get();

            return view('hr.salary')
                ->with('salaries',$salaries);

        }

        if($id == 'addsalary'){

            foreach($request->get('salarydescription') as $key => $value){
                
                $checkifexists = Db::table('employee_salary')
                    ->where('description','like','%'.$value)
                    ->where('deleted','0')
                    ->get();

                if(count($checkifexists) == 0){

                    DB::table('employee_salary')
                        ->insert([
                            'description'   => $value,
                            'amount'        => $request->get('amount')[$key]
                        ]);

                }

            }

            return back();

        }
        
    }
    // public function leaves($id, Request $request)
    // {
    //     // return $request->all();
    //     date_default_timezone_set('Asia/Manila');

    //     if(auth()->user()->type == '1'){

    //         $extends = "teacher.layouts.app";
            
    //     }elseif(auth()->user()->type == '2'){

    //         $extends = "principalsportal.layouts.app2";

    //     }elseif(auth()->user()->type == '3' || auth()->user()->type == '8' ){

    //         $extends = "registrar.layouts.app";

    //     }elseif(auth()->user()->type == '4' || auth()->user()->type == '15'){

    //         $extends = "finance.layouts.app";

    //     }elseif(auth()->user()->type == '6'){

    //         $extends = "adminPortal.layouts.app2";

    //     }elseif(auth()->user()->type == '10'){

    //         $extends = "hr.layouts.app";

    //     }elseif(auth()->user()->type == '12'){

    //         $extends = "adminITPortal.layouts.app";

    //     }
    //     // $extends = "hr.layouts.app";
    //     // return 'asdas';
    //     if($id == 'dashboard'){

    //         $getMyid = DB::table('teacher')
    //             ->select('id')
    //             ->where('userid', auth()->user()->id)
    //             ->first();

    //         $hr_approvals = DB::table('hr_leavesappr')
    //             ->where('deleted','0')
    //             ->get();


    //         $employees = DB::table('teacher')
    //             ->select('teacher.*')
    //             ->join('employee_basicsalaryinfo','teacher.id','=','employee_basicsalaryinfo.employeeid')
    //             ->where('teacher.isactive','1')
    //             ->where('teacher.deleted','0')
    //             ->get();

    //         if(auth()->user()->type == 10)
    //         {

    //             $leaves = array();

    //             $approvals = DB::table('hr_leavesappr')
    //                 ->where('deleted','0')
    //                 ->get();
                    
    //             $filedleaves = DB::table('employee_leaves')
    //                 ->select(
    //                     'employee_leaves.*',
    //                     'hr_leaves.leave_type',
    //                     'teacher.lastname',
    //                     'teacher.firstname',
    //                     'teacher.middlename',
    //                     'teacher.suffix'
    //                     )
    //                 ->join('teacher','employee_leaves.employeeid','=','teacher.id')
    //                 ->join('hr_leaves','employee_leaves.leaveid','=','hr_leaves.id')
    //                 ->where('hr_leaves.deleted','0')
    //                 ->where('employee_leaves.deleted','0')
    //                 ->get();
                    
    //             // return $filedleaves;
    //             if(count($filedleaves)>0)
    //             {
    //                 foreach($filedleaves as $filedleave)
    //                 {

    //                     if(count($approvals) == 0)
    //                     {

    //                         array_push($leaves, $filedleave);

    //                     }else{
    //                         $checkstatus = DB::table('hr_leavesapprdetails')
    //                             ->where('employeeleaveid', $filedleave->id)
    //                             // ->where('statusby', $approval->employeeid)
    //                             ->where('deleted', '0')
    //                             ->get();
    //                         // return $checkstatus;
                            
    //                         $disapproved = 0;

    //                         if(count($approvals) == count($checkstatus))
    //                         {

    //                             foreach($checkstatus as $checkstat)
    //                             {
                                    
                                    
    //                                 if($checkstat->status == 3)
    //                                 {
    //                                     $disapproved+=1;
    //                                 }
                                    

    //                             }

    //                         }

    //                         $approvalstats = array();
    //                         foreach($approvals as $checkapprove)
    //                         {
    //                             $checkstatus = DB::table('hr_leavesapprdetails')
    //                             ->where('employeeleaveid', $filedleave->id)
    //                             ->where('statusby', $checkapprove->employeeid)
    //                             ->where('deleted', '0')
    //                             ->first();

    //                             $nameofapproval = DB::table('teacher')
    //                                 ->where('id',$checkapprove->employeeid)
    //                                 ->first();

    //                             if($nameofapproval->middlename!= null)
    //                             {
    //                                 $nameofapproval->middlename = $nameofapproval->middlename[0].'.';
    //                             }

    //                             if($checkstatus)
    //                             {
    //                                 $checkstatus->name = $nameofapproval->firstname.' '.$nameofapproval->middlename.' '.$nameofapproval->lastname.' '.$nameofapproval->suffix;
    //                                 array_push($approvalstats,$checkstatus);
    //                             }else{
    //                                 array_push($approvalstats,(object)array(
    //                                     'employeeleaveid'   => $filedleave->id,
    //                                     'status'            => 2,
    //                                     'statusby'          =>  $checkapprove->employeeid,
    //                                     'name'              =>  $nameofapproval->firstname.' '.$nameofapproval->middlename.' '.$nameofapproval->lastname.' '.$nameofapproval->suffix
    //                                 ));
    //                             }
                                

    //                         }
    //                         $filedleave->approvals = $approvalstats;

    //                         array_push($leaves, $filedleave);

    //                     }

    //                 }

    //             }
    //             // return $leaves;
    
    //         }else{

    //             $leaves = array();

    //             // $getdepartments = DB::table('hr_departmentheads')
    //             //     ->where('deptheadid', $getMyid->id)
    //             //     ->where('deleted','0')
    //             //     ->get();

    //             $approvals = DB::table('hr_leavesappr')
    //                 ->where('employeeid', $getMyid->id)
    //                 ->where('deleted','0')
    //                 ->get();
                    
    //             // if(count($approval)>0)
    //             // {
    //                 $filedleaves = DB::table('employee_leaves')
    //                     ->select(
    //                         'employee_leaves.*',
    //                         'hr_leaves.leave_type',
    //                         'teacher.lastname',
    //                         'teacher.firstname',
    //                         'teacher.middlename',
    //                         'teacher.suffix'
    //                         )
    //                     ->join('teacher','employee_leaves.employeeid','=','teacher.id')
    //                     ->join('hr_leaves','employee_leaves.leaveid','=','hr_leaves.id')
    //                     ->where('hr_leaves.deleted','0')
    //                     ->where('employee_leaves.deleted','0')
    //                     ->get();

    //                 // return $filedleaves;
                        
    //                 if(count($filedleaves)>0)
    //                 {
    //                     foreach($filedleaves as $filedleave)
    //                     {
                                
    //                         if(count($approvals)>0)
    //                         {
    //                             foreach($approvals as $approval)
    //                             {
    //                                 $checkstatus = DB::table('hr_leavesapprdetails')
    //                                     ->where('employeeleaveid', $filedleave->id)
    //                                     ->where('statusby', $approval->employeeid)
    //                                     ->first();

    //                                 if($checkstatus)
    //                                 {
    //                                     $filedleave->status = $checkstatus->status;
    //                                 }else{
    //                                     $filedleave->status = 2;
    //                                 }
    //                             }
    //                         }
    //                         // $filedleave->approvals = $approvals;
    //                         array_push($leaves, $filedleave);
    //                     }
    //                 }
    //             // }
    //         }
    //         // return $leaves;
                
    //         $leavetypes = DB::table('hr_leaves')
    //                         ->where('isactive','1')
    //                         ->where('deleted','0')
    //                         ->get();

    //         return view('hr.leaves')
    //                 ->with('leavetypes',$leavetypes)
    //                 ->with('employees',$employees)
    //                 ->with('leaves',$leaves)
    //                 ->with('extends',$extends);

    //     }elseif($id == 'editrequest'){
            
    //         date_default_timezone_set('Asia/Manila');

    //         $payroll_id = DB::table('payroll')
    //             ->where('status','1')
    //             ->first();

            
    //         $date = explode(' - ',$request->get('date'));

    //         $period = new DatePeriod(
    //             new DateTime($date[0]),
    //             new DateInterval('P1D'),
    //             new DateTime($date[1])
    //         );
    //         $teacherid = Db::table('teacher')
    //             ->select('teacher.id')
    //             ->join('employee_leaves','teacher.id','=','employee_leaves.employeeid')
    //             ->where('employee_leaves.id', $request->get('requestid'))
    //             ->first();


    //         $checksched = Db::table('employee_basicsalaryinfo')
    //             ->where('employeeid', $teacherid->id)
    //             ->first();

    //     //    return collect($checksched);
    //         $numofdays=1;

    //         foreach ($period as $key => $value) {
    //             if(strtolower($value->format('D')) == 'mon')
    //             {
    //                 if($checksched->mondays == 1)
    //                 {
    //                     $numofdays+=1;
    //                 }
    //             } 
    //             if(strtolower($value->format('D')) == 'tue')
    //             {
    //                 if($checksched->tuesdays == 1)
    //                 {
    //                     $numofdays+=1;
    //                 }
    //             } 
    //             if(strtolower($value->format('D')) == 'wed')
    //             {
    //                 if($checksched->wednesdays == 1)
    //                 {
    //                     $numofdays+=1;
    //                 }
    //             } 
    //             if(strtolower($value->format('D')) == 'thu')
    //             {
    //                 if($checksched->thursdays == 1)
    //                 {
    //                     $numofdays+=1;
    //                 }
    //             } 
    //             if(strtolower($value->format('D')) == 'fri')
    //             {
    //                 if($checksched->fridays == 1)
    //                 {
    //                     $numofdays+=1;
    //                 }
    //             } 
    //             if(strtolower($value->format('D')) == 'sat')
    //             {
    //                 if($checksched->saturdays == 1)
    //                 {
    //                     $numofdays+=1;
    //                 }
    //             } 
    //             if(strtolower($value->format('D')) == 'sun')
    //             {
    //                 if($checksched->sundays == 1)
    //                 {
    //                     $numofdays+=1;
    //                 }
    //             } 
    //         }
                
    //         Db::table('employee_leaves')
    //             ->where('id',$request->get('requestid'))
    //             ->update([
    //                 // 'leaveid'   =>  $request->get('requestid'),
    //                 'datefrom'          =>  $date[0],
    //                 'dateto'            =>  $date[1],
    //                 'numofdays'         =>  $numofdays,
    //                 'reason'            =>  $request->get('content'),
    //                 'updatedby'         =>  auth()->user()->id,
    //                 'updateddatetime'   =>  date('Y-m-d H:i:s')
    //             ]);

    //         return redirect()->back()->with("messageAdd", 'Leave application form updated succesfully!');

    //     }elseif($id == 'deleterequest'){
    //         date_default_timezone_set('Asia/Manila');
    //         // return $request->all();

    //         Db::table('employee_leaves')
    //             ->where('id',$request->get('requestid'))
    //             ->update([
    //                 'deleted'    =>  '1'
    //             ]);
    //         // DB::update('update job_leavesdetail set deleted = ? where id = ?',['1',$request->get('requestid')]);

    //         return redirect()->back()->with("messageAdd", 'Leave application form deleted succesfully!');

    //     }elseif($id == 'changestatus'){
            
    //         if($request->get('status')=='Approve'){ 
    //             $status = 1;
    //         }elseif($request->get('status')=='Disapprove'){
    //             $status = 3;
    //         }

    //         $teacherid = Db::table('teacher')
    //             ->select('teacher.id')
    //             ->where('userid', auth()->user()->id)
    //             ->first();

    //         $leaves = DB::table('employee_leaves')
    //             ->select(
    //                 'teacher.lastname',
    //                 'teacher.firstname',
    //                 'teacher.middlename',
    //                 'teacher.suffix'
    //                 )
    //             ->join('teacher','employee_leaves.employeeid','=','teacher.id')
    //             ->where('employee_leaves.id',$request->get('leaveid'))
    //             ->first();
                
    //         $date_submitted = date('Y-m-d H:i:s');

    //         if(auth()->user()->type == 10)
    //         {
    //             if($request->get('status')=='Approve'){
    
    //                 DB::update('update employee_leaves set status = ?, updateddatetime = ? where id = ?',['1',$date_submitted,$request->get('leaveid')]);
    
    //                 return redirect()->back()->with("messageApproved", ''.$leaves->firstname.' '.$leaves->middlename.' '.$leaves->lastname.' '.$leaves->suffix.' '.'leave form has been approved!');
    //             }
    //             elseif($request->get('status')=='Disapprove'){
    
    //                 DB::update('update employee_leaves set  status = ?, updateddatetime = ? where id = ?',['3',$date_submitted,$request->get('leaveid')]);
    
    //                 return redirect()->back()->with("messageDispproved", ''.$leaves->firstname.' '.$leaves->middlename.' '.$leaves->lastname.' '.$leaves->suffix.' '.'leave form has been disapproved!');
    
    //             }

    //         }else{
    //             // return $request->all();

    //             $checkifexists = Db::table('hr_leavesapprdetails')
    //                 ->where('statusby',$teacherid->id)
    //                 ->where('employeeleaveid', $request->get('leaveid'))
    //                 ->where('deleted','0')
    //                 ->get();

    //             if(count($checkifexists) == 0)
    //             {
    //                 DB::table('hr_leavesapprdetails')
    //                     ->insert([
    //                         'employeeleaveid'       => $request->get('leaveid'),
    //                         'status'                => $status,
    //                         'statusby'              => $teacherid->id,
    //                         'createdby'             => auth()->user()->id,
    //                         'createddatetime'       => date('Y-m-d H:i:s')
    //                     ]);

    //             }else{

    //                 DB::table('hr_leavesapprdetails')
    //                     ->where('id', $checkifexists[0]->id)
    //                     ->update([
    //                         'status'    => $status,
    //                         'updatedby' => auth()->user()->id,
    //                         'updateddatetime'   => date('Y-m-d H:i:s')
    //                     ]);

    //             }
    //             return back();
    //         }

    //     }
    // }
    public function globalapplyleave(Request $request)
    {

        // return $request->all();
        $payrollid = DB::table('payroll')
            ->where('status','1')
            ->first();

        $date = explode(' - ', $request->get('leavedaterange'));


        foreach($request->get('leaveapplicants') as $applicant){

            $getusertypeid = DB::table('teacher')
                ->where('id', $applicant)
                ->get();

            if(count($getusertypeid) == 0){

                $usertypeid = 0;

            }else{

                $usertypeid = $getusertypeid[0]->usertypeid;

            }
            

            $checkifpendingexists = DB::table('employee_leaves')
                ->where('employeeid', $applicant)
                ->where('deleted', '0')
                ->where('status', '2')
                ->get();

            if(count($checkifpendingexists) == 0){
                $period = new DatePeriod(
                    new DateTime($date[0]),
                    new DateInterval('P1D'),
                    new DateTime($date[1])
               );
               
               $checksched = Db::table('employee_basicsalaryinfo')
                   ->where('employeeid', $applicant)
                   ->first();

               $numofdays=1;
               foreach ($period as $key => $value) {
                   if(strtolower($value->format('D')) == 'mon')
                   {
                        if($checksched->mondays == 1)
                        {
                            $numofdays+=1;
                        }
                   } 
                   if(strtolower($value->format('D')) == 'tue')
                   {
                        if($checksched->tuesdays == 1)
                        {
                            $numofdays+=1;
                        }
                   } 
                   if(strtolower($value->format('D')) == 'wed')
                   {
                        if($checksched->wednesdays == 1)
                        {
                            $numofdays+=1;
                        }
                   } 
                   if(strtolower($value->format('D')) == 'thu')
                   {
                        if($checksched->thursdays == 1)
                        {
                            $numofdays+=1;
                        }
                   } 
                   if(strtolower($value->format('D')) == 'fri')
                   {
                        if($checksched->fridays == 1)
                        {
                            $numofdays+=1;
                        }
                   } 
                   if(strtolower($value->format('D')) == 'sat')
                   {
                        if($checksched->saturdays == 1)
                        {
                            $numofdays+=1;
                        }
                   } 
                   if(strtolower($value->format('D')) == 'sun')
                   {
                        if($checksched->sundays == 1)
                        {
                            $numofdays+=1;
                        }
                   } 
                }
                // $dateto = strtotime($date[1]);
                // $datefrom = strtotime($date[0]);
                // $datediff = $dateto - $datefrom;
                DB::table('employee_leaves')
                    ->insert([
                        'employeeid'        => $applicant,
                        'usertypeid'        => $usertypeid,
                        'payrollid'         => $payrollid->id,
                        'leaveid'           => $request->get('leavetype'),
                        'datefrom'          => $date[0],
                        'dateto'            => $date[1],
                        'status'            => '2',
                        'numofdays'         => $numofdays,
                        'reason'            => $request->get('leaveremarks'),
                        'createdby'         => auth()->user()->id,
                        'createddatetime'   => date('Y-m-d H:i:s')
                    ]);
                    
                    // Db::table('employee_leaves')
                    //     ->insert([
                    //         'employeeid'        => $teacherid->id,
                    //         'usertypeid'        => auth()->user()->type,
                    //         'payrollid'         => $payrollid->id,
                    //         'leaveid'           => $request->get('applyleavetype'),
                    //         'datefrom'          => $date[0],
                    //         'dateto'            => $date[1],
                    //         'status'            => '2',
                    //         // 'numofdays' =>  round($datediff / (60 * 60 * 24)),
                    //         'numofdays'         =>  $numofdays,
                    //         'reason'            => $request->get('content'),
                    //         'createdby'         => auth()->user()->id,
                    //         'createddatetime'   => date('Y-m-d H:i:s')
                    //     ]);

            }
            

        }

        return back();
    }
    // public function leavesettings()
    // {
        
    //     $leaves = DB::table('hr_leaves')
    //         ->where('deleted','0')
    //         ->get();

    //     return view('hr.leavesettings')
    //         ->with('leaves',$leaves);

    // }
    // public function leavesettingsupdates($action,Request $request)
    // {

    //     if($action == 'updatestatus'){

    //         DB::update('update hr_leaves set isactive = ? where id = ?',[$request->get('newisactive'),$request->get('leaveid')]);
    //         return back();

    //     }
    //     elseif($action == 'updatedays'){

    //         DB::update('update hr_leaves set days = ? where id = ?',[$request->get('days'),$request->get('leaveid')]);
    //         return back();

    //     }
    //     elseif($action == 'updatewithorwithoutpay'){
            
    //         DB::table('hr_leaves')
    //             ->where('id', $request->get('leaveid'))
    //             ->update([
    //                 'withpay'   => $request->get('withpay')
    //             ]);
    //         return back();

    //     }
    //     elseif($action == 'addleave'){

    //         $checkifexists = DB::table('hr_leaves')
    //             ->where('leave_type','like','%'.$request->get('leave_type'))
    //             ->get();
                
    //         if(count($checkifexists)==0){

    //             DB::insert('insert into hr_leaves (leave_type, days, isactive, deleted) values(?,?,?,?)',[$request->get('leave_type'),$request->get('days'),'1','0']);

    //             return redirect()->back()->with("messageAdd", 'Leave type added successfully!');

    //         }
    //         else{

    //             return redirect()->back()->with("messageExists", 'Leave type already exist!');

    //         }

    //     }
    //     elseif($action == 'deleteleave'){

    //         DB::update('update hr_leaves set deleted = ? where id = ?',['1',$request->get('leaveid')]);

    //         return redirect()->back()->with("messageDelete", 'Leave type deleted successfully!');

    //     }

    // }
    public function attendance($id, Request $request)
    {
        date_default_timezone_set('Asia/Manila');
        
        $id = Crypt::decrypt($id);
        
        if($id == 'dashboard'){

            if($request->get('changedate') == true){

                $changedate = explode('-',$request->get('changedate'));

                $date = $changedate[2].'-'.$changedate[0].'-'.$changedate[1];

            }else{

                $date = date('Y-m-d');

            }
            
            $getMyid = DB::table('teacher')
                ->select('id')
                ->where('userid', auth()->user()->id)
                // ->where('isactive','1')
                // ->where('deleted','0')
                ->first();
            
            $employees = DB::table('teacher')
                ->select(
                    'teacher.id',
                    'teacher.firstname',
                    'teacher.middlename',
                    'teacher.lastname',
                    'teacher.suffix',
                    'teacher.picurl',
                    'employee_personalinfo.gender',
                    'usertype.utype'
                    )
                ->join('usertype','teacher.usertypeid','=','usertype.id')
                ->leftJoin('employee_personalinfo','teacher.id','=','employee_personalinfo.employeeid')
                ->where('teacher.deleted','0')
                ->where('teacher.isactive','1')
                ->get();
                
            $attendancearray = array();

            foreach($employees as $employee){
    
                $getattendance = Db::table('teacherattendance')
                    // ->select('')
                    ->join('teacher','teacherattendance.teacher_id','=','teacher.id')
                    ->where('teacherattendance.tdate', $date)
                    ->where('teacherattendance.teacher_id', $employee->id)
                    ->get();
                    
                if(count($getattendance)  == 0){

                    array_push($attendancearray,(object)array(
                        'employeeinfo'      => $employee,
                        'attendance'        => (object)array(
                                                    'in_am'         =>     "00:00",
                                                    'out_am'        =>     "00:00",
                                                    'in_pm'         =>     "00:00",
                                                    'out_pm'        =>     "00:00"
                                                )
                    ));

                }else{

                    $inam = explode(':',$getattendance[0]->in_am);

                    if($inam[0] == "00" ){
                       
                        $getattendance[0]->in_am = '12:'.$inam[1];
                    }

                    if($getattendance[0]->in_am == null){

                        $getattendance[0]->in_am = "00:00";

                    }

                    $outam = explode(':',$getattendance[0]->out_am);

                    if($outam[0] == "00"){

                        DB::table('teacherattendance')
                            ->where('id', $getattendance[0]->id)
                            ->update([
                                'out_am'    => null
                            ]);

                        $getattendance[0]->out_am = '00:'.$outam[1];

                    }

                    if($getattendance[0]->out_am == null){

                        $getattendance[0]->out_am = "00:00";

                    }

                    $inpm = explode(':',$getattendance[0]->in_pm);

                    if($inpm[0] == "00"){

                        DB::table('teacherattendance')
                            ->where('id', $getattendance[0]->id)
                            ->update([
                                'in_pm'    => null
                            ]);

                        $getattendance[0]->in_pm = '00:'.$inpm[1];

                    }

                    if($getattendance[0]->in_pm == null){

                        $getattendance[0]->in_pm = "00:00";

                    }

                    $outpm = explode(':',$getattendance[0]->out_pm);

                    // if($outpm[0] == "00"){
                    if($outpm[0] == "00"){
                        if($outpm[1] == "00")
                        {
                            DB::table('teacherattendance')
                                ->where('id', $getattendance[0]->id)
                                ->update([
                                    'out_pm'    => null
                                ]);

                            $getattendance[0]->out_pm = '00:00';

                        }else{

                            $getattendance[0]->out_pm = '12:'.$outpm[1];
                            
                        }

                    }

                    if($getattendance[0]->out_pm == null){

                        $getattendance[0]->out_pm = "00:00";

                    }

                    array_push($attendancearray,(object)array(
                        'employeeinfo'      => $employee,
                        'attendance'        => (object)array(
                                                    'in_am'         =>     $getattendance[0]->in_am,
                                                    'out_am'        =>     $getattendance[0]->out_am,
                                                    'in_pm'         =>     $getattendance[0]->in_pm,
                                                    'out_pm'        =>     $getattendance[0]->out_pm
                                                )
                    ));

                }
    
            }

            foreach($attendancearray as $eachatt){

                foreach($eachatt->attendance as $key => $value){

                    if($key == 'in_am'){

                        $eachattinam = explode(':',$value);
        
                        if(count($eachattinam) == 3){
        
                            $eachatt->attendance->in_am = $eachattinam[0].':'.$eachattinam[1];
        
                        }

                    }
                    elseif($key == 'out_am'){

                        $eachattoutam = explode(':',$value);
        
                        if(count($eachattoutam) == 3){
        
                            $eachatt->attendance->out_am = $eachattoutam[0].':'.$eachattoutam[1];
        
                        }

                    }
                    elseif($key == 'in_pm'){

                        $eachattinpm = explode(':',$value);
        
                        if(count($eachattinpm) == 3){
        
                            $eachatt->attendance->in_pm = $eachattinpm[0].':'.$eachattinpm[1];
        
                        }
                        
                    }
                    elseif($key == 'out_pm'){

                        $eachattoutpm = explode(':',$value);
        
                        if(count($eachattoutpm) == 3){
        
                            $eachatt->attendance->out_pm = $eachattoutpm[0].':'.$eachattoutpm[1];
        
                        }
                        
                    }

                }

            }

            if($request->get('changedate') == true){
                
                $attendance = array();

                array_push($attendance,date('m-d-Y', strtotime($date)));

                array_push($attendance,$attendancearray);

                return $attendance;

            }else{
                
                return view('hr.attendance')
                    ->with('currentdate',date('m-d-Y', strtotime($date)))
                    ->with('attendance',$attendancearray);

            }

        }
        elseif($id == 'am_in'){
            
            $dateexplode = explode('-',$request->get('selecteddate'));

            $checkifexists = Db::table('teacherattendance')
                ->where('teacher_id', $request->get('employeeid'))
                ->where('tdate', $dateexplode[2].'-'.$dateexplode[0].'-'.$dateexplode[1])
                ->get();

            if($request->get('am_in') == "00:00" || $request->get('am_in') == "00:00:00"){

                $am_in = null;

            }else{
                
                $am_in = $request->get('am_in');

            }

            if(count($checkifexists) == 0){

                DB::table('teacherattendance')
                    ->insert([
                        'teacher_id'    =>  $request->get('employeeid'),
                        'in_am'         =>  $am_in,
                        // 'out_am'        =>  null,
                        // 'in_pm'         =>  null,
                        // 'out_am'        =>  null,
                        'tdate'         =>  $dateexplode[2].'-'.$dateexplode[0].'-'.$dateexplode[1]
                    ]);

            }else{
                
                $hey = DB::table('teacherattendance')
                    ->where('teacher_id',$request->get('employeeid'))
                    ->where('tdate',$dateexplode[2].'-'.$dateexplode[0].'-'.$dateexplode[1])
                    ->update([
                        'in_am'         =>  $am_in
                    ]);

            }

            return $request->all();

        }
        elseif($id == 'am_out'){
            
            $dateexplode = explode('-',$request->get('selecteddate'));

            $checkifexists = Db::table('teacherattendance')
                ->where('teacher_id', $request->get('employeeid'))
                ->where('tdate', $dateexplode[2].'-'.$dateexplode[0].'-'.$dateexplode[1])
                ->get();
                
            if($request->get('am_out') == "00:00" || $request->get('am_out') == "00:00:00"){

                $am_out = null;

            }else{

                $am_out = $request->get('am_out');

            }
            if(count($checkifexists) == 0){

                
                $adasdas  = DB::table('teacherattendance')
                    ->insert([
                        'teacher_id'    =>  $request->get('employeeid'),
                        // 'in_am'         =>  null,
                        'out_am'        =>  $am_out,
                        // 'in_pm'         =>  null,
                        // 'out_am'        =>  null,
                        'tdate'         =>  $dateexplode[2].'-'.$dateexplode[0].'-'.$dateexplode[1]
                    ]);

            }else{
                
                $hey = DB::table('teacherattendance')
                    ->where('teacher_id',$request->get('employeeid'))
                    ->where('tdate',$dateexplode[2].'-'.$dateexplode[0].'-'.$dateexplode[1])
                    ->update([
                        'out_am'         =>  $am_out
                    ]);

            }

            return $request->all();

        }
        elseif($id == 'pm_in'){
            
            $dateexplode = explode('-',$request->get('selecteddate'));

            $checkifexists = Db::table('teacherattendance')
                ->where('teacher_id', $request->get('employeeid'))
                ->where('tdate', $dateexplode[2].'-'.$dateexplode[0].'-'.$dateexplode[1])
                ->get();

            if($request->get('pm_in') == "00:00" || $request->get('pm_in') == "00:00:00"){

                $pm_in = null;

            }else{

                $pm_in = $request->get('pm_in');

            }
            if(count($checkifexists) == 0){

                DB::table('teacherattendance')
                    ->insert([
                        'teacher_id'    =>  $request->get('employeeid'),
                        // 'in_am'         =>  null,
                        // 'out_am'        =>  null,
                        'in_pm'         =>  $pm_in,
                        // 'out_pm'        =>  null,
                        'tdate'         =>  $dateexplode[2].'-'.$dateexplode[0].'-'.$dateexplode[1]
                    ]);

            }else{
                
                $hey = DB::table('teacherattendance')
                    ->where('teacher_id',$request->get('employeeid'))
                    ->where('tdate',$dateexplode[2].'-'.$dateexplode[0].'-'.$dateexplode[1])
                    ->update([
                        'in_pm'         =>  $pm_in
                    ]);

            }

            return $request->all();

        }
        elseif($id == 'pm_out'){
            
            $dateexplode = explode('-',$request->get('selecteddate'));

            $checkifexists = Db::table('teacherattendance')
                ->where('teacher_id', $request->get('employeeid'))
                ->where('tdate', $dateexplode[2].'-'.$dateexplode[0].'-'.$dateexplode[1])
                ->get();

            if($request->get('pm_out') == "00:00" || $request->get('pm_out') == "00:00:00"){

                $pm_out = null;

            }else{

                $pm_out = $request->get('pm_out');

            }

            if(count($checkifexists) == 0){

                DB::table('teacherattendance')
                    ->insert([
                        'teacher_id'    =>  $request->get('employeeid'),
                        // 'in_am'         =>  null,
                        // 'out_am'        =>  null,
                        // 'in_pm'         =>  null,
                        'out_pm'        =>  $pm_out,
                        'tdate'         =>  $dateexplode[2].'-'.$dateexplode[0].'-'.$dateexplode[1]
                    ]);

            }else{
                
                $hey = DB::table('teacherattendance')
                    ->where('teacher_id',$request->get('employeeid'))
                    ->where('tdate',$dateexplode[2].'-'.$dateexplode[0].'-'.$dateexplode[1])
                    ->update([
                        'out_pm'         =>  $pm_out
                    ]);

            }

            return $request->all();

        }

    }
    // public function departments($action, Request $request)
    // {
        
    //     date_default_timezone_set('Asia/Manila');

    //     $my_id = DB::table('teacher')
    //         ->select('id')
    //         ->where('userid',auth()->user()->id)
    //         ->where('isactive','1')
    //         ->first();

    //     if($action == 'dashboard'){

    //         $departments = Db::table('hr_school_department')
    //             ->where('deleted','0')
    //             ->get();
            
    //         return view('hr.department')
    //             ->with('departments',$departments);

    //     }
    //     elseif($action == 'adddepartment'){

    //         $checkifexists = Db::table('hr_school_department')
    //             ->where('department','like','%'.$request->get('department'))
    //             ->where('deleted','0')
    //             ->get();
                
    //         if(count($checkifexists) == 0){

    //             DB::table('hr_school_department')
    //                 ->insert([
    //                     'department'    => $request->get('department'),
    //                     'deleted'       => 0,
    //                     'created_by'    => $my_id->id,
    //                     'created_on'    => date('Y-m-d H:i:s')
    //                 ]);

    //             return redirect()->back()->with("messageAdded", $request->get('department').' department added successfully!');

    //         }
    //         else{

    //             return redirect()->back()->with("messageExists", $request->get('department').' already exists!');

    //         }

    //     }
    //     elseif($action == 'editdepartment'){
            
    //         Db::update('update hr_school_department set department = ?, updated_by = ?, updated_on = ? where id = ?',[$request->get('department'),$my_id->id,date('Y-m-d H:i:s'),$request->get('departmentid')]);

    //         return redirect()->back()->with("messageEdited", $request->get('department').' department updated successfully!');

    //     }
    //     elseif($action == 'deletedepartment'){
            
    //         Db::update('update hr_school_department set deleted = ?, updated_by = ?, updated_on = ? where id = ?',['1',$my_id->id,date('Y-m-d H:i:s'),$request->get('departmentid')]);

    //         return redirect()->back()->with("messageDeleted", $request->get('department').' department deleted successfully!');

    //     }

    // }

    // public function designations($action, Request $request)
    // {
    //     date_default_timezone_set('Asia/Manila');

    //     $my_id = DB::table('teacher')
    //         ->select('id')
    //         ->where('userid',auth()->user()->id)
    //         ->where('isactive','1')
    //         ->first();

    //     if($action == 'dashboard'){

    //         $departments = Db::table('hr_school_department')
    //             ->where('deleted','0')
    //             ->get();

    //         $designations = Db::table('usertype')
    //             ->select(
    //                 'usertype.id',
    //                 'usertype.utype as designation',
    //                 'departmentid',
    //                 'constant'
    //                 )
    //             ->where('usertype.deleted','0')
    //             ->where('usertype.utype','!=','PARENT')
    //             ->where('usertype.utype','!=','STUDENT')
    //             ->where('usertype.utype','!=','SUPER ADMIN')
    //             ->get();
                
    //         foreach($designations as $designation){

    //             if($designation->departmentid == null){

    //                 $designation->departmentid = 0;

    //             }else{

    //                 $designation->departmentid = $designation->departmentid;
                    
    //             }
                
    //         }
            
    //         return view('hr.designations')
    //             ->with('departments',$departments)
    //             ->with('designations',$designations);

    //     }
    //     elseif($action == 'adddesignation'){
            
    //         $checkifexists = Db::table('usertype')
    //             ->where('utype','like','%'.$request->get('designation'))
    //             ->where('deleted','0')
    //             ->get();
                
    //         if(count($checkifexists) == 0){

    //             $refid = DB::table('usertype')
    //                 ->insertGetId([
    //                     'utype'         => strtoupper($request->get('designation')),
    //                     'departmentid'  => $request->get('departmentid'),
    //                     'constant'      => 0,
    //                     'deleted'       => 0,
    //                     'created_by'    => $my_id->id,
    //                     'created_on'    => date('Y-m-d H:i:s')
    //                 ]);
                
    //             // DB::table('usertype')
    //             //     ->where('id', $refid)
    //             //     ->update([
    //             //         'refid'     => $refid
    //             //     ]);

    //             return redirect()->back()->with("messageAdded", $request->get('designation').' designation added successfully!');

    //         }
    //         else{

    //             return redirect()->back()->with("messageExists", $request->get('designation').' already exists!');

    //         }

    //     }
    //     elseif($action == 'editdesignation'){
            
    //         DB::table('usertype')
    //             ->where('id',$request->get('designationid'))
    //             ->update([
    //                 'utype'             => $request->get('designation'),
    //                 'updated_by'        => $my_id->id,
    //                 'updated_on'        => date('Y-m-d H:i:s'),
    //             ]);
    //         // Db::update('update hr_designation set designation = ?, updated_by = ?, updated_on = ? where id = ?',[$request->get('designation'),$my_id->id,date('Y-m-d H:i:s'),$request->get('designationid')]);

    //         return redirect()->back()->with("messageEdited", $request->get('designation').' designation updated successfully!');

    //     }
    //     elseif($action == 'deletedesignation'){

    //         DB::table('usertype')
    //             ->where('id',$request->get('designationid'))
    //             ->update([
    //                 'deleted'           => '1',
    //                 'updated_by'        => $my_id->id,
    //                 'updated_on'        => date('Y-m-d H:i:s'),
    //             ]);
            
    //         // Db::update('update hr_designation set deleted = ?, updated_by = ?, updated_on = ? where id = ?',['1',$my_id->id,date('Y-m-d H:i:s'),$request->get('designationid')]);

    //         return redirect()->back()->with("messageDeleted", $request->get('department').' department deleted successfully!');

    //     }
    //     elseif($action == 'editdepartment'){
    //         // return $request->all();
    //         DB::table('usertype')
    //             ->where('id',$request->get('designationid'))
    //             ->update([
    //                 'departmentid'      => $request->get('departmentid'),
    //                 'updated_by'        => $my_id->id,
    //                 'updated_on'        => date('Y-m-d H:i:s'),
    //             ]);
    //         // Db::update('update hr_designation set departmentid = ?, updated_by = ?, updated_on = ? where id = ?',[$request->get('departmentid'),$my_id->id,date('Y-m-d H:i:s'),$request->get('designationid')]);

    //         return redirect()->back()->with("messageEdited", $request->get('designation')."'s department updated successfully!");

    //     }

    // }
    public function employeestatus($id, Request $request)
    {

        $action = Crypt::decrypt($id);

        if($action == 'dashboard'){

            $employees = DB::table('teacher')
                ->where('isactive', '1')
                ->where('datehired', '!=', null)
                ->get();

            foreach($employees as $employee){

                foreach($employee as $key => $value){

                    if($key == 'datehired'){

                        $employee->datehiredmodified = date('F d, Y', strtotime($value));
                        // Declare and define two dates 
                        $date1 = strtotime($value); 
                        $date2 = strtotime(date('Y-m-d')); 
                        
                        // Formulate the Difference between two dates 
                        $diff = abs($date2 - $date1); 
                        
                        
                        // To get the year divide the resultant date into 
                        // total seconds in a year (365*60*60*24) 
                        $years = floor($diff / (365*60*60*24)); 
                        
                        
                        // To get the month, subtract it with years and 
                        // divide the resultant date into 
                        // total seconds in a month (30*60*60*24) 
                        $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24)); 
                        
                        
                        // To get the day, subtract it with years and 
                        // months and divide the resultant date into 
                        // total seconds in a days (60*60*24) 
                        $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24)); 
                        
                        
                        // Print the result 
                        // return substr(printf("%d years, %d months, %d days", $years, $months, $days), 0, -2);
                        $employee->updatedperiod = sprintf("%d years, %d months, %d days", $years, $months, $days); 

                    }

                }

            }


            $employeescasual = DB::table('teacher')
                ->where('isactive', '1')
                ->where('datehired', '!=', null)
                ->where('employmentstatus', '1')
                ->get();
            
            $employeesprovisionary = DB::table('teacher')
                ->where('isactive', '1')
                ->where('datehired', '!=', null)
                ->where('employmentstatus', '2')
                ->get();
            
            $employeesregular = DB::table('teacher')
                ->where('isactive', '1')
                ->where('datehired', '!=', null)
                ->where('employmentstatus', '3')
                ->get();
                
            $employeesparttime = DB::table('teacher')
                ->where('isactive', '1')
                ->where('datehired', '!=', null)
                ->where('employmentstatus', '4')
                ->get();
                
            $employeessubstitute = DB::table('teacher')
                ->where('isactive', '1')
                ->where('datehired', '!=', null)
                ->where('employmentstatus', '5')
                ->get();
                
            return view('hr.employeestatus')
                ->with('employees', $employees)
                ->with('employeescasual', count($employeescasual))
                ->with('employeesprovisionary', count($employeesprovisionary))
                ->with('employeesregular', count($employeesregular))
                ->with('employeesparttime', count($employeesparttime))
                ->with('employeessubstitute', count($employeessubstitute));

        }
        elseif($action == 'update'){
            
            DB::table('teacher')
                ->where('id', $request->get('employeeid'))
                ->update([
                    'employmentstatus'  => $request->get('employmentstatus')
                ]);

            return back();

        }

    }
    public function statusindex(Request $request)
    {
        $statustypes = DB::table('hr_empstatus')
            // ->where('title','like','%'.$request->get('title').'%')
            ->where('deleted','0')
            ->get();

        if(count($statustypes)>0)
        {
            foreach($statustypes as $statustype)
            {
                $statustype->count = DB::table('teacher')
                    ->where('isactive', '1')
                    // ->where('datehired', '!=', null)
                    ->where('employmentstatus', $statustype->id)
                    ->count();
                
            }
        }
        if($request->get('action') == 'getstatustypes')
        {
            return view('hr.employees.employmentstatus.resultstatustypes')
                ->with('statustypes', $statustypes);
        }else{
            return view('hr.employees.employmentstatus.index')
                ->with('statustypes', $statustypes);
        }
    }
    public function statustypes(Request $request)
    {
        // return $request->all();
        $checkifexists = DB::table('hr_empstatus')
            ->where('description','like','%'.$request->get('title').'%')
            ->where('deleted','0')
            ->first();

        if($request->get('action') == 'addstatus')
        {
            if($checkifexists)
            {
                return 0;
            }else{
                DB::table('hr_empstatus')
                    ->insert([
                        'description'                 => $request->get('title'),
                        'createdby'             => auth()->user()->id,
                        'createddatetime'       => date('Y-m-d H:i:s')
                    ]);
    
                return 1;
            }
        }
        elseif($request->get('action') == 'editstatus')
        {
            try{
                // return $request->get('offenseid');
                DB::table('hr_empstatus')
                    ->where('id', $request->get('statusid'))
                    ->update([
                        'description'           => $request->get('title'),
                        'updatedby'       => auth()->user()->id,
                        'updateddatetime' => date('Y-m-d H:i:s')
                    ]);
                return 1;
            }catch(\Exception $error)
            {
                return 0;
            }
        }
        elseif($request->get('action') == 'deletestatus')
        {
            try{
                // return $request->get('offenseid');
                DB::table('hr_empstatus')
                    ->where('id', $request->get('statusid'))
                    ->update([
                        'deleted'         => 1,
                        'deletedby'       => auth()->user()->id,
                        'deleteddatetime' => date('Y-m-d H:i:s')
                    ]);
                return 1;
            }catch(\Exception $error)
            {
                return 0;
            }
        }
        elseif($request->get('action') == 'editempstatus')
        {
            try{
                // return $request->get('offenseid');
                DB::table('teacher')
                    ->where('id', $request->get('employeeid'))
                    ->update([
                        'employmentstatus'         => $request->get('statusid')
                        // 'updatedby'                 => auth()->user()->id,
                        // 'updateddatetime'           => date('Y-m-d H:i:s')
                    ]);
                return 1;
            }catch(\Exception $error)
            {
                return 0;
            }
        }
    }
    public function empstatusgenerate(Request $request)
    {
        $employees = DB::table('teacher')
            ->where('deleted','0')
            ->where('isactive', '1')
            // ->where('datehired', '!=', null)
            ->orderBy('lastname','asc')
            ->get();

        

        $statustypes = DB::table('hr_empstatus')
            ->where('deleted','0')
            ->get();

        if($request->get('statusid') > 0)
        {
            $employees = collect($employees)->where('employmentstatus', $request->get('statusid'))->values();
        }
        if(count($employees)>0)
        {
            foreach($employees as $employee)
            {
                if($employee->datehired == null)
                {
                    $employee->yearsinservice =" ";
                }else{
                    $date1 = $employee->datehired;
                    $date2 = date('Y-m-d');
                    $dateDifference = abs(strtotime($date2) - strtotime($date1));
                    
                    $years  = floor($dateDifference / (365 * 60 * 60 * 24));
                    $months = floor(($dateDifference - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                    // $days   = floor(($dateDifference - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 *24) / (60 * 60 * 24));
                    
                    $yearsinservice = $years." year(s) & ".$months." month(s)";
                    $employee->yearsinservice = $yearsinservice;
                }
                
            }
        }
        if(!$request->has('action'))
        {
            return view('hr.employees.employmentstatus.resultsemployees')
                ->with('employees', $employees)
                ->with('statustypes', $statustypes);
        }else{
            $statusid = $request->get('statusid');
            $pdf = PDF::loadview('hr/employees/employmentstatus/pdf_employeesempstatus',compact('employees','statustypes','statusid'))->setPaper('portrait');

            return $pdf->stream('Employees Employment Status.pdf');

        }

    }
    public function cgrowth(Request $request)
    {
        date_default_timezone_set('Asia/Manila');
        $employees = DB::table('teacher')
            ->where('deleted','0')
            ->where('isactive', '1')
            // ->where('datehired', '!=', null)
            ->orderBy('lastname','asc')
            ->get();

        if(!$request->has('action'))
        {    
            return view('hr.employees.careergrowth.index')
                ->with('employees', $employees);
        }else{
            if($request->get('action') == 'getresults')
            {
                $employeeinfo = collect($employees)->where('id', $request->get('employeeid'))->first();

                $currentdesignation = DB::table('usertype')
                    ->where('id', $employeeinfo->usertypeid)
                    ->first();

                $promotions = DB::table('employee_promotion')
                    ->select('employee_promotion.*','usertype.utype')
                    ->join('usertype','employee_promotion.usertypeidto','=','usertype.id')
                    ->where('employee_promotion.employeeid', $request->get('employeeid'))
                    ->orderByDesc('pyear')
                    ->where('employee_promotion.deleted','0')
                    ->get();

                $usertypes = DB::table('usertype')
                    ->orderBy('utype','asc')
                    ->where('deleted','0')
                    ->where('id','!=',9)
                    ->where('id','!=',7)
                    ->where('id','!=',$employeeinfo->usertypeid)
                    ->get();
                    
                if(count($promotions)>0)
                {
                    $usertypes = collect($usertypes)->where('id', '!=',$promotions[0]->usertypeidto)->values()->all();
                }
                return view('hr.employees.careergrowth.results')
                    ->with('usertypes', $usertypes)
                    ->with('currentdesignation', $currentdesignation)
                    ->with('employeeinfo', $employeeinfo)
                    ->with('promotions', $promotions);
                    
            }
            elseif($request->get('action') == 'promote')
            {
                $checkifexists = DB::table('employee_promotion')
                    ->where('employeeid', $request->get('employeeid'))
                    ->where('usertypeidto', $request->get('usertypeid'))
                    ->orderByDesc('pyear')
                    ->where('deleted','0')
                    ->first();

                if(!$checkifexists)
                {
                    DB::table('employee_promotion')
                        ->insert([
                            'employeeid'            => $request->get('employeeid'),
                            'pyear'                 => date('Y'),
                            'usertypeidfrom'        => $request->get('currenttypeid'),
                            'usertypeidto'          => $request->get('usertypeid'),
                            'position'              => '',
                            'createdby'             => auth()->user()->id,
                            'createddatetime'       => date('Y-m-d H:i:s')
                        ]);

                    return 1;
                }
            }
        }
    }

}
