<?php

namespace App\Http\Controllers\RegistrarControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use TCPDF;
use PDF;
class TORController extends Controller
{
    public function index(Request $request)
    {
        // $students = DB::table('college_enrolledstud')
        //     ->select('studinfo.id','studinfo.firstname','studinfo.middlename','studinfo.lastname','studinfo.suffix','studinfo.gender')
        //     ->join('studinfo','college_enrolledstud.studid','=','studinfo.id')
        //     ->where('college_enrolledstud.deleted','0')
        //     ->where('studinfo.deleted','0')
        //     ->whereIn('college_enrolledstud.studstatus',[1,2,4])
        //     ->orderBy('lastname','asc')
        //     ->distinct()
        //     ->get();
            
        // return view('registrar.forms.tor.index')
        //     ->with('students', $students);
        if($request->has('action'))
        {
            $search = $request->get('search');
            $search = $search['value'];

            $students = DB::table('college_enrolledstud')
                // ->select('studinfo.id','sid','lrn','lastname','firstname','middlename','suffix','levelid','gradelevel.levelname')
                // ->leftJoin('gradelevel','studinfo.levelid','=','gradelevel.id')
                ->select('studinfo.id','sid','lrn','studinfo.firstname','studinfo.middlename','studinfo.lastname','studinfo.suffix','studinfo.gender','gradelevel.levelname')
                ->join('studinfo','college_enrolledstud.studid','=','studinfo.id')
                ->join('gradelevel','college_enrolledstud.yearLevel','=','gradelevel.id')
                ->where('college_enrolledstud.deleted','0')
                ->where('gradelevel.acadprogid','6')
                ->where('studinfo.lastname','!=',null)
                ->where('studinfo.deleted','0')
                ->whereIn('college_enrolledstud.studstatus',[1,2,4]);
            
            if($search != null){
                    $students = $students->where(function($query) use($search){
                                        $query->orWhere('firstname','like','%'.$search.'%');
                                        $query->orWhere('lastname','like','%'.$search.'%');
                                        $query->orWhere('sid','like','%'.$search.'%');
                                        $query->orWhere('levelname','like','%'.$search.'%');
                                });
            }
            
            $students = $students->take($request->get('length'))
                ->distinct('studinfo.id')
                ->skip($request->get('start'))
                ->orderBy('lastname','asc')
                // ->whereIn('studinfo.studstatus',[1,2,4])
                ->get();
                
            $studentscount = DB::table('college_enrolledstud')
            ->select('studinfo.id','sid','lrn','studinfo.firstname','studinfo.middlename','studinfo.lastname','studinfo.suffix','studinfo.gender','gradelevel.levelname')
            ->join('studinfo','college_enrolledstud.studid','=','studinfo.id')
            ->join('gradelevel','college_enrolledstud.yearLevel','=','gradelevel.id')
            ->where('college_enrolledstud.deleted','0')
            ->where('studinfo.lastname','!=',null)
            ->where('studinfo.deleted','0')
            ->where('gradelevel.acadprogid','6')
            ->whereIn('college_enrolledstud.studstatus',[1,2,4]);
                
            if($search != null){
                    $studentscount = $studentscount->where(function($query) use($search){
                                    $query->orWhere('firstname','like','%'.$search.'%');
                                    $query->orWhere('lastname','like','%'.$search.'%');
                                    $query->orWhere('sid','like','%'.$search.'%');
                                    $query->orWhere('levelname','like','%'.$search.'%');
                                });
            }
            
            
            
            $studentscount = $studentscount
                ->orderBy('lastname','asc')
                ->distinct('studinfo.id')
                // ->whereIn('studinfo.studstatus',[1,2,4])
                ->count();

            if($studentscount > 0)
            {
                foreach($students as $key=>$student)
                {
                    $last_fullname = $student->lastname.', '.$student->firstname.' '.(isset($student->middlename[0]) ? $student->middlename[0].'.' : '').' '.$student->suffix;
                    $first_fullname = $student->firstname.' '.(isset($student->middlename[0]) ? $student->middlename[0].'.' : '').' '.$student->lastname.' '.$student->suffix;
                    $student->last_fullname = $last_fullname;
                    $student->first_fullname = $first_fullname;
                    $student->no = $key+1;
                }
            }
                
            return @json_encode((object)[
                'data'=>$students,
                'recordsTotal'=>$studentscount,
                'recordsFiltered'=>$studentscount
            ]);

        }else{
            $signatories = DB::table('signatory')
                ->where('createdby', auth()->user()->id)
                ->where('deleted','0')
                ->where('form','tor')
                ->whereIn('title',['School Treasurer','OIC - Registrar'])
                ->get();
                // return $signatories;
            return view('registrar.forms.tor.index_v2')
                ->with('signatories',$signatories);
            //     ->with('students', $students);;
        }
    }
    public function getrecords(Request $request)
    {
        $studentid          = $request->get('studid');
        $studentinfo = DB::table('studinfo')
            ->select('studinfo.*','nationality.nationality','religion.religionname')
            ->leftJoin('nationality', 'studinfo.nationality','=','nationality.id')
            ->leftJoin('religion', 'studinfo.religionid','=','religion.id')
            ->where('studinfo.id', $studentid)->first();


        $schoolyears = Db::table('sy')
            ->select('id as syid','sydesc'
            ,'isactive'
            )
            ->orderByDesc('sydesc')
            ->get();
            
        $courses = Db::table('college_courses')
            ->where('deleted','0')
            ->orderBy('courseDesc','asc')
            ->get();
        $records = \App\Models\College\TOR::getrecords($studentid, $schoolyears);
        
        if(count($records)>0)
        {
            foreach($records as $record)
            {
                // return collect($record);
                $texts = DB::table('college_tortexts')
                    ->where('studid',$studentid)
                    ->where('sydesc',$record->sydesc)
                    ->where('semid',$record->semid)
                    ->where('deleted','0')
                    ->get();
                $record->texts = $texts;                
            }
        }
        
        $details = DB::table('college_tordetail')
            ->select('college_tordetail.*','dob','gender','mothername','fathername','pob','college_tordetail.acrno','parentaddress','guardianaddress','college_tordetail.citizenship','college_tordetail.civilstatus')
            ->where('studid', $studentid)
            ->join('studinfo','college_tordetail.studid','studinfo.id')
            ->where('college_tordetail.deleted','0')
            ->first();

        if(!$details)
        {
            $details = (object)array(
                'studid'            => null,
                'parentguardian'    => null,
                'address'           => null,
                'elemcourse'        => null,
                'elemdatecomp'      => null,
                'secondcourse'      => null,
                'seconddatecomp'    => null,
                'admissiondate'     => null,
                'degree'            => null,
                'basisofadmission'  => null,
                'major'             => null,
                'specialorder'      => null,
                'remarks'           => null,
                'elemsy'            => null,
                'secondsy'          => null,
                'thirdsy'           => null,
                'graduationdate'    => null,
                'admissiondatestr'  => null,
                'collegeof'         => null,
                'entrancedata'      => null,
                'intermediategrades'=> null,
                'intermediatecourse'=> null,
                'intermediatesy'=> null,
                'secondarygrades'   => null,
                'dob'               => null,
                'gender'               => null,
                'mothername'               => null,
                'fathername'               => null,
                'pob'               => null,
                'acrno'             => null,
                'citizenship'       => null,
                'civilstatus'       => null,
                'parentaddress'     => null,
                'guardianaddress'   => null
            );
        } 
        $signatories = DB::table('signatory')
            ->where('createdby', auth()->user()->id)
            ->where('deleted','0')
            ->where('form','tor')
            ->get();;
        $getphoto = DB::table('studdisplayphoto')
                ->where('studid', $studentid)
                ->where('deleted','0')
                ->first();

        if(count($records)>0)
        {
            foreach($records as $record)
            {
                $record->sort = $record->sydesc.' '.$record->semid;
            }
        }
        $records = collect($records)->sortBy('sort')->values();
        $torrecords = 'torrecords';
        if(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'ccsa')
        {
            $torrecords = 'tordetails.torrecords_ccsa';
        }
        return view('registrar.forms.tor.'.$torrecords)
            ->with('signatories', $signatories)
            ->with('getphoto', $getphoto)
            ->with('studentid', $studentid)
            ->with('records', $records)
            ->with('studentinfo', $studentinfo)
            ->with('details', $details)
            ->with('schoolyears', $schoolyears)
            ->with('courses', $courses);
    }
    public function getrecord(Request $request)
    {
        $schoolyears = Db::table('sy')
            ->select('id as syid','sydesc'
            ,'isactive'
            )
            ->orderByDesc('sydesc')
            ->get();
            
        $courses = Db::table('college_courses')
            ->where('deleted','0')
            ->orderBy('courseDesc','asc')
            ->get();
        $recordinfo = DB::table('college_tor')
            ->select(
                'id',
                'syid',
                'sydesc',
                'semid',
                'courseid',
                'coursename',
                'schoolid',
                'schoolname',
                'schooladdress'
            )
            ->where('id',$request->get('torid'))
            ->where('deleted','0')
            ->first();
        // return collect($recordinfo);
        return view('registrar.forms.tor.record_edit')
            ->with('schoolyears', $schoolyears)
            ->with('courses', $courses)
            ->with('recordinfo', $recordinfo);
    }
    public function updaterecord(Request $request)
    {
        // return $request->all();

        try{
            if($request->get('syid') == 0)
            {
                $syid = 0;
                $sydesc = $request->get('customsy');
            }else{
                $syid = $request->get('syid');
                $sydesc = DB::table('sy')->where('id', $syid)->first()->sydesc;
            }
            if($request->get('courseid') == 0)
            {
                $courseid = 0;
                $coursedesc = $request->get('customcourse');
            }else{
                $courseid = $request->get('courseid');
                $coursedesc = DB::table('college_courses')->where('id', $courseid)->first()->courseDesc;
            }
            DB::table('college_tor')
            ->where('id',$request->get('torid'))
            ->update([
                'syid'             => $syid,
                'sydesc'           => $sydesc,
                'semid'            => $request->get('semid'),
                'courseid'         => $courseid,
                'coursename'       => $coursedesc,
                'schoolid'         => $request->get('schoolid'),
                'schoolname'       => $request->get('schoolname'),
                'schooladdress'    => $request->get('schooladdress')
            ]);
            return 1;
        }catch(\Exception $error)
        {

        }
    }
    public function savedetail(Request $request)
    {
        date_default_timezone_set('Asia/Manila');
        $studentid          = $request->get('studid');
        $parentguardian     = $request->get('parentguardian');
        $address            = $request->get('address');
        $elemcourse         = $request->get('elemcourse');
        $elemdatecomp       = $request->get('elemdatecomp');
        $secondcourse       = $request->get('secondcourse');
        $seconddatecomp     = $request->get('seconddatecomp');
        $admissiondate      = $request->get('admissiondate');
        $degree             = $request->get('degree');
        $basisofadmission   = $request->get('basisofadmission');
        $major              = $request->get('major');
        $specialorder       = $request->get('specialorder');
        $graduationdate     = $request->get('graduationdate');
        $elemschoolyear     = $request->get('elemschoolyear');
        $secondschoolyear   = $request->get('secondschoolyear');
        $thirdschoolyear    = $request->get('thirdschoolyear');
        $remarks            = $request->get('remarks');
        
        $dateadmitted       = $request->get('dateadmitted');
        $collegeof          = $request->get('collegeof');
        $entrancedata       = $request->get('entrancedata');
        $intermediategrades = $request->get('intermediategrades');
        $secondarygrades    = $request->get('secondarygrades');
        
        $placeofbirth       = $request->get('placeofbirth');
        $acrno              = $request->get('acrno');
        $citizenship        = $request->get('citizenship');
        $civilstatus        = $request->get('civilstatus');
        $parentaddress      = $request->get('parentaddress');
        $guardianaddress    = $request->get('guardianaddress');

        
        $entrancedate           = $request->get('entrancedate');
        $schoolnameprimary      = $request->get('schoolnameprimary');
        $schooladdressprimary   = $request->get('schooladdressprimary');
        $schoolyearprimary      = $request->get('schoolyearprimary');
        $schoolnamejunior       = $request->get('schoolnamejunior');
        $schooladdressjunior    = $request->get('schooladdressjunior');
        $schoolyearjunior       = $request->get('schoolyearjunior');
        $schoolnamesenior       = $request->get('schoolnamesenior');
        $schooladdresssenior    = $request->get('schooladdresssenior');
        $schoolyearsenior       = $request->get('schoolyearsenior');

        $nstpserialno       = $request->get('nstpserialno');

        $admissionsem       = $request->get('admissionsem');
        $admissionsy       = $request->get('admissionsy');
        $intermediatecourse       = $request->get('intermediatecourse');
        $intermediatesy       = $request->get('intermediateschoolyear');

        $graduationdegree       = $request->get('graduationdegree');
        $graduationmajor       = $request->get('graduationmajor');
        $graduationhonors       = $request->get('graduationhonors');




        if(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'hccsi')
        {
            
            DB::table('studinfo')
                ->where('id',$studentid)
                ->update([
                    'pob'               => $placeofbirth,
                    'acrno'             => $acrno,
                    'citizenship'       => $citizenship,
                    'civilstatus'       => $civilstatus,
                    'parentaddress'     => $parentaddress,
                    'guardianaddress'   => $guardianaddress
                ]);
        }
        
        $checkifexists = DB::table('college_tordetail')
            ->where('studid', $studentid)
            ->where('deleted','0')
            ->get();

        if(count($checkifexists) == 0)
        {
            DB::table('college_tordetail')
                ->insert([
                    'studid'            => $studentid,
                    'acrno'            => $acrno,
                    'parentguardian'    => $parentguardian,
                    'address'           => $address,
                    'elemcourse'        => $elemcourse,
                    'elemdatecomp'      => $elemdatecomp,
                    'secondcourse'      => $secondcourse,
                    'seconddatecomp'    => $seconddatecomp,
                    'admissiondate'     => $admissiondate,
                    'degree'            => $degree,
                    'citizenship'       => $citizenship,
                    'civilstatus'       => $civilstatus,
                    'basisofadmission'  => $basisofadmission,
                    'major'             => $major,
                    'specialorder'      => $specialorder,
                    'elemsy'            => $elemschoolyear,
                    'secondsy'          => $secondschoolyear,
                    'thirdsy'           => $thirdschoolyear,
                    'remarks'           => $remarks,
                    'graduationdate'    => $graduationdate,
                    'admissiondatestr'  => $dateadmitted,
                    'collegeof'         => $collegeof,
                    'entrancedata'      => $entrancedata,
                    'entrancedate'      => $entrancedate,
                    'intermediategrades'=> $intermediategrades,
                    'secondarygrades'   => $secondarygrades,
                    'createdby'         => auth()->user()->id,
                    'createddatetime'   => date('Y-m-d H:i:s'),
                    'primaryschoolname'   => $schoolnameprimary,
                    'primaryschooladdress'   => $schooladdressprimary,
                    'primaryschoolyear'   => $schoolyearprimary,
                    'juniorschoolname'   => $schoolnamejunior,
                    'juniorschooladdress'   => $schooladdressjunior,
                    'juniorschoolyear'   => $schoolyearjunior,
                    'seniorschoolname'   => $schoolnamesenior,
                    'seniorschooladdress'   => $schooladdresssenior,
                    'seniorschoolyear'   => $schoolyearsenior,
                    'nstpserialno'   => $nstpserialno,
                    'admissionsem'   => $admissionsem,
                    'admissionsy'   => $admissionsy,
                    'intermediatecourse'   => $intermediatecourse,
                    'intermediatesy'   => $intermediatesy,
                    'graduationdegree'   => $graduationdegree,
                    'graduationmajor'   => $graduationmajor,
                    'graduationhonors'   => $graduationhonors
                ]);
        }else{
            DB::table('college_tordetail')
                ->where('studid',$studentid)
                ->where('deleted',0)
                ->update([
                    'acrno'            => $acrno,
                    'parentguardian'    => $parentguardian,
                    'address'           => $address,
                    'elemcourse'        => $elemcourse,
                    'elemdatecomp'      => $elemdatecomp,
                    'secondcourse'      => $secondcourse,
                    'seconddatecomp'    => $seconddatecomp,
                    'admissiondate'     => $admissiondate,
                    'degree'            => $degree,
                    'citizenship'       => $citizenship,
                    'civilstatus'       => $civilstatus,
                    'basisofadmission'  => $basisofadmission,
                    'major'             => $major,
                    'specialorder'      => $specialorder,
                    'graduationdate'    => $graduationdate,
                    'elemsy'            => $elemschoolyear,
                    'secondsy'          => $secondschoolyear,
                    'thirdsy'           => $thirdschoolyear,
                    'remarks'           => $remarks,
                    'admissiondatestr'  => $dateadmitted,
                    'collegeof'         => $collegeof,
                    'entrancedata'      => $entrancedata,
                    'entrancedate'      => $entrancedate,
                    'intermediategrades'=> $intermediategrades,
                    'secondarygrades'   => $secondarygrades,
                    'updatedby'         => auth()->user()->id,
                    'updateddatetime'   => date('Y-m-d H:i:s'),
                    'primaryschoolname'   => $schoolnameprimary,
                    'primaryschooladdress'   => $schooladdressprimary,
                    'primaryschoolyear'   => $schoolyearprimary,
                    'juniorschoolname'   => $schoolnamejunior,
                    'juniorschooladdress'   => $schooladdressjunior,
                    'juniorschoolyear'   => $schoolyearjunior,
                    'seniorschoolname'   => $schoolnamesenior,
                    'seniorschooladdress'   => $schooladdresssenior,
                    'seniorschoolyear'   => $schoolyearsenior,
                    'nstpserialno'   => $nstpserialno,
                    'admissionsem'   => $admissionsem,
                    'admissionsy'   => $admissionsy,
                    'intermediatecourse'   => $intermediatecourse,
                    'intermediatesy'   => $intermediatesy,
                    'graduationdegree'   => $graduationdegree,
                    'graduationmajor'   => $graduationmajor,
                    'graduationhonors'   => $graduationhonors
                ]);
        }
        return 1;

    }
    public function savetext(Request $request)
    {
        date_default_timezone_set('Asia/Manila');
        $id = $request->get('id');
        $semid = $request->get('semid');
        $sydesc = $request->get('sydesc');
        $studid = $request->get('studid');
        $thistext = $request->get('thistext');
        if($id == 0)
        {
            DB::table('college_tortexts')
                ->insert([
                    'studid'           =>  $studid,
                    'semid'            =>  $semid,
                    'sydesc'           =>  $sydesc,
                    'description'         =>  $thistext,
                    'createdby'        => auth()->user()->id,
                    'createddatetime'  => date('Y-m-d H:i:s')
                ]);
            
        }else{
            DB::table('college_tortexts')
                ->where('id', $id)
                ->update([
                    'description'         =>  $thistext,
                    'updatedby'        => auth()->user()->id,
                    'updateddatetime'  => date('Y-m-d H:i:s')
                ]);
            
        }
        return 1;
    }
    public function savesignatories(Request $request)
    {
        // return $request->all();
        $signatories = json_decode($request->get('signatories'));
        if(count($signatories)>0)
        {
            foreach($signatories as $signatory)
            {
                $checkifsignatoriesexist = DB::table('signatory')
                    ->where('createdby', auth()->user()->id)
                    ->where('deleted','0')
                    ->where('title',$signatory->title)
                    ->where('form','tor')
                    ->first();

                if($checkifsignatoriesexist)
                {
                    if($signatory->name == null)
                    {
                        DB::table('signatory')
                            ->update([
                                'deleted'  => 1,
                                'deleteddatetime'  => date('Y-m-d H:i:s'),
                                'deletedby'  => auth()->user()->id
                            ]);
                    }else{
                        if($signatory->name != $checkifsignatoriesexist->name)
                        {
                            DB::table('signatory')
                                ->update([
                                    'title'  => $signatory->title,
                                    'form'  => 'tor',
                                    'name'  => $signatory->name,
                                    'updateddatetime'  => date('Y-m-d H:i:s'),
                                    'updatedby'  => auth()->user()->id
                                ]);
                        }
                    }
                }else{
                    DB::table('signatory')
                        ->insert([
                            'title'  => $signatory->title,
                            'form'  => 'tor',
                            'name'  => $signatory->name,
                            'createddatetime'  => date('Y-m-d H:i:s'),
                            'createdby'  => auth()->user()->id
                        ]);
                }
            }
            return 1;
        }
    }
    public function deletetext(Request $request)
    {
        date_default_timezone_set('Asia/Manila');
        
        $id = $request->get('id');
        DB::table('college_tortexts')
        ->where('id', $id)
        ->update([
            'deleted'         =>  1,
            'deletedby'        => auth()->user()->id,
            'deleteddatetime'  => date('Y-m-d H:i:s')
        ]);
        return 1;
    }

    public function addnewrecord(Request $request)
    {
        date_default_timezone_set('Asia/Manila');
        // return $request->all();
        $studentid          = $request->get('studid');
        $schoolid           = $request->get('schoolid');
        $schoolname         = $request->get('schoolname');
        $schooladdress      = $request->get('schooladdress');
        $syid               = $request->get('syid');
        $customsy           = $request->get('customsy');
        $semid              = $request->get('semid');
        $courseid           = $request->get('courseid');
        $customcourse       = $request->get('customcourse');

        if($syid != 0)
        {
            $customsy       = DB::table('sy')
                                ->where('id', $syid)
                                ->first()->sydesc;
        }
        if($courseid != 0)
        {
            $customcourse       = DB::table('college_courses')
                                ->where('id', $courseid)
                                ->first()->courseDesc;
        }
        $checkifexists      = DB::table('college_tor')
                                ->where('studid', $studentid)
                                ->where('syid', $syid)
                                ->where('sydesc','like','%'.$customsy.'%')
                                ->where('semid', $semid)
                                ->where('courseid', $courseid)
                                ->where('coursename','like','%'.$customcourse.'%')
                                ->where('deleted','0')
                                ->count();

        if($checkifexists == 0)
        {
            DB::table('college_tor')
                ->insert([
                    'studid'            => $studentid,
                    'syid'              => $syid,
                    'sydesc'            => $customsy,
                    'semid'             => $semid,
                    'courseid'          => $courseid,
                    'coursename'        => $customcourse,
                    'schoolid'          => $schoolid,
                    'schoolname'        => $schoolname,
                    'schooladdress'     => $schooladdress,
                    'createdby'         => auth()->user()->id,
                    'createddatetime'   => date('Y-m-d H:i:s')
                ]);

            return 1;
        }else{
            return 0;
        }
    }
    public function addnewdata(Request $request)
    {
        // return $request->all();
        $studentid     = $request->get('studid');
        $torid         = $request->get('torid');
        $subjid         = $request->get('subjid');
        $subjcode      = $request->get('subjcode');
        $subjunit      = $request->get('subjunit');
        $subjdesc      = $request->get('subjdesc');
        $subjgrade     = $request->get('subjgrade');
        $subjreex     = $request->get('subjreex');
        $subjcredit    = $request->get('subjcredit');

        $checkifexists = DB::table('college_torgrades')
                        ->where('torid', $torid)
                        ->where('subjdesc','like','%'.$subjdesc.'%')
                        ->where('deleted','0')
                        ->count();

    
        if($checkifexists == 0)
        {
            $subjdataid = Db::table('college_torgrades')
                ->insertGetId([
                    'torid'             => $torid,
                    'subjid'          => $subjid,
                    'subjcode'          => $subjcode,
                    'subjdesc'          => $subjdesc,
                    'subjgrade'         => $subjgrade,
                    'subjreex'         => $subjreex,
                    'subjunit'          => $subjunit,
                    'subjcredit'        => $subjcredit,
                    'createdby'         => auth()->user()->id,
                    'createddatetime'   => date('Y-m-d H:i:s')
                ]);
            return $subjdataid;
        }else{
            return '0';
        }

    }
    public function getsubjects(Request $request)
    {
        if($request->has('action'))
        {
            $info = DB::table('college_prospectus')
                ->select('college_prospectus.id','semesterID as semid','yearID as levelid','lecunits','labunits','subjDesc as subjdesc','subjCode as subjcode','psubjsort')
                ->where('id', $request->get('subjectid'))
                // ->where('courseID',  $request->get('courseid'))
                ->where('college_prospectus.deleted','0')
                ->first();

            return collect($info);
        }else{
            $curriculum = DB::table('college_studentcurriculum')
                ->where('studid', $request->get('studentid'))
                ->where('deleted','0')
                ->first();
            // return collect($curriculum);
            $subjects = array();
            if($curriculum)
            {
                $subjects = DB::table('college_prospectus')
                    ->select('college_prospectus.id','semesterID as semid','yearID as levelid','lecunits','labunits','subjDesc as subjdesc','subjCode as subjcode','psubjsort')
                    ->where('curriculumID', $curriculum->id)
                    // ->where('courseID',  $request->get('courseid'))
                    ->where('college_prospectus.deleted','0')
                    ->get();
            }
            return collect($subjects);
        }
    }
    public function editsubjgrade(Request $request)
    {
        $studentid     = $request->get('studid');
        $torid         = $request->get('torid');
        $subjgradeid   = $request->get('subjgradeid');
        $subjcode      = $request->get('subjcode');
        $subjunit      = $request->get('subjunit');
        $subjdesc      = $request->get('subjdesc');
        $subjgrade     = $request->get('subjgrade');
        $subjreex     = $request->get('subjreex');
        $subjcredit    = $request->get('subjcredit');

        try{
            Db::table('college_torgrades')
                ->where('id',$subjgradeid)
                ->update([
                    // 'torid'             => $torid,
                    'subjcode'          => $subjcode,
                    'subjdesc'          => $subjdesc,
                    'subjgrade'         => $subjgrade,
                    'subjreex'         => $subjreex,
                    'subjunit'          => $subjunit,
                    'subjcredit'        => $subjcredit,
                    'updatedby'         => auth()->user()->id,
                    'updateddatetime'   => date('Y-m-d H:i:s')
                ]);
            return '1';
        }catch(\Exception $error)
        {
            return '0';
        }
    }
    public function deletesubjgrade(Request $request)
    {
        $subjgradeid   = $request->get('subjgradeid');

        try{
            Db::table('college_torgrades')
                ->where('id',$subjgradeid)
                ->update([
                    'deleted'           => 1,
                    'deletedby'         => auth()->user()->id,
                    'deleteddatetime'   => date('Y-m-d H:i:s')
                ]);
            return '1';
        }catch(\Exception $error)
        {
            return '0';
        }
    }
    public function deleterecord(Request $request)
    {
        $torid   = $request->get('torid');

        try{
            Db::table('college_tor')
                ->where('id',$torid)
                ->update([
                    'deleted'           => 1,
                    'deletedby'         => auth()->user()->id,
                    'deleteddatetime'   => date('Y-m-d H:i:s')
                ]);
            return '1';
        }catch(\Exception $error)
        {
            return '0';
        }
    }
    public function exporttopdf(Request $request)
    {
        date_default_timezone_set('Asia/Manila');
        $studentid = $request->get('studid');
        $studentinfo = DB::table('studinfo')
            ->select('studinfo.*','nationality.nationality','religion.religionname')
            ->leftJoin('nationality', 'studinfo.nationality','=','nationality.id')
            ->leftJoin('religion', 'studinfo.religionid','=','religion.id')
            ->where('studinfo.id', $studentid)->first();

        $clearedby      = $request->get('clearedby');
        $preparedby      = $request->get('preparedby');
        $checkedby   = $request->get('checkedby');
        $registrar      = $request->get('registrar');
        $assistantreg   = $request->get('assistantreg');
        $or             = $request->get('or');
        $dateissued     = $request->get('dateissued');

        $checkifsignatoriesexist = DB::table('signatory')
            ->where('createdby', auth()->user()->id)
            ->where('deleted','0')
            ->where('form','tor')
            ->get();

        if(collect($checkifsignatoriesexist)->where('title','Registrar')->count() > 0)
        {
            DB::table('signatory')
                ->where('id', collect($checkifsignatoriesexist)->where('title','Registrar')->first()->id)
                ->update([
                    'name'  => $registrar
                ]);
        }else{
            DB::table('signatory')
                ->insert([
                    'form'  => 'tor',
                    'name'  => $registrar,
                    'title'  => 'Registrar',
                    'createddatetime'  => date('Y-m-d H:i:s'),
                    'createdby'  => auth()->user()->id
                ]);
        }
        if(collect($checkifsignatoriesexist)->where('description','Prepared by')->count() > 0)
        {
            DB::table('signatory')
                ->where('id', collect($checkifsignatoriesexist)->where('description','Prepared by')->first()->id)
                ->update([
                    'name'  => $preparedby
                ]);
        }else{
            DB::table('signatory')
                ->insert([
                    'form'  => 'tor',
                    'name'  => $preparedby,
                    'description'  => 'Prepared by',
                    'createddatetime'  => date('Y-m-d H:i:s'),
                    'createdby'  => auth()->user()->id
                ]);
        }
        if(collect($checkifsignatoriesexist)->where('description','Checked by')->count() > 0)
        {
            DB::table('signatory')
                ->where('id', collect($checkifsignatoriesexist)->where('description','Checked by')->first()->id)
                ->update([
                    'name'  => $checkedby
                ]);
        }else{
            DB::table('signatory')
                ->insert([
                    'form'  => 'tor',
                    'name'  => $checkedby,
                    'description'  => 'Checked by',
                    'createddatetime'  => date('Y-m-d H:i:s'),
                    'createdby'  => auth()->user()->id
                ]);
        }



        $schoolyears = Db::table('sy')
            ->select('id as syid','sydesc'
            ,'isactive'
            )
            ->orderByDesc('sydesc')
            ->get();
            
        $records = \App\Models\College\TOR::getrecords($studentid, $schoolyears); 
        // return collect($records);
        $numberofrows = 0;
        if(count($records)>0)
        {
            foreach($records as $record)
            {
                $numberofrows += (count($record->subjdata) + 2);
                $record->sort = $record->sydesc.' '.$record->semid;
            }
        }
        $records = collect($records)->sortBy('sort')->values();
        // return $records;
        $lastcourseid = collect($records)->last()->courseid ?? 0;
        $collegedeanname = '';
        if(collect($records)->where('id','0')->count() > 0)
        {
            $deaninfo = DB::table('college_courses')
                ->select('teacher.*')
                ->where('college_courses.id', collect($records)->where('id','0')->last()->courseid)
                ->join('college_colleges','college_courses.collegeid','=','college_colleges.id')
                ->join('teacher','college_colleges.dean','=','teacher.id')
                ->first();

            if($deaninfo)
            {
                $collegedeanname .= ($deaninfo->title != null ? $deaninfo->title.' ' : '');
                $collegedeanname .= $deaninfo->firstname.' ';
                $collegedeanname .= ($deaninfo->middlename != null ? $deaninfo->middlename[0].'. ' : ' ');
                $collegedeanname .= $deaninfo->lastname.' ';
                $collegedeanname .= ($deaninfo->suffix != null ? $deaninfo->suffix : '');
            }
        }
        
        
        if(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'ndsc')
        {
            if(count($records)>0)
            {
                foreach($records as $record)
                {
                    // return collect($record);
                    $texts = DB::table('college_tortexts')
                        ->where('studid',$studentid)
                        ->where('sydesc',$record->sydesc)
                        ->where('semid',$record->semid)
                        ->where('deleted','0')
                        ->get();
                    $record->texts = $texts;
                    
                }
            }
        }
        // return $records;
        $details = DB::table('college_tordetail')
        ->select('college_tordetail.*','dob','gender','mothername','fathername','pob','college_tordetail.acrno','parentaddress','guardianaddress')
        ->where('studid', $studentid)
        ->join('studinfo','college_tordetail.studid','studinfo.id')
        ->where('college_tordetail.deleted','0')
        ->first();

        if(!$details)
        {
            $details = (object)array(
                'studid'            => null,
                'parentguardian'    => null,
                'address'           => null,
                'elemcourse'        => null,
                'elemdatecomp'      => null,
                'secondcourse'      => null,
                'seconddatecomp'    => null,
                'admissiondate'     => null,
                'degree'            => null,
                'basisofadmission'  => null,
                'major'             => null,
                'specialorder'      => null,
                'elemsy'           => null,
                'secondsy'           => null,
                'thirdsy'           => null,
                'remarks'           => null,
                'graduationdate'    => null,
                'admissiondatestr'    => null,
                'collegeof'    => null,
                'entrancedata'    => null,
                'intermediategrades'    => null,
                'secondarygrades'    => null,
                'secondarygrades'   => null,
                'dob'               => null,
                'gender'               => null,
                'mothername'               => null,
                'fathername'               => null,
                'pob'               => null,
                'acrno'             => null,
                'citizenship'       => null,
                'civilstatus'       => null,
                'parentaddress'     => null,
                'guardianaddress'   => null
            );
        }
        if($studentinfo->dob != null)
        {
            $studentinfo->dob = date('m/d/Y', strtotime($studentinfo->dob));
        }
        if($details->elemdatecomp != null)
        {
            $details->elemdatecomp = date('m/d/Y', strtotime($details->elemdatecomp));
        }
        if($details->seconddatecomp != null)
        {
            $details->seconddatecomp = date('m/d/Y', strtotime($details->seconddatecomp));
        }
        if($details->admissiondate != null)
        {
            $details->admissiondate = date('m/d/Y', strtotime($details->admissiondate));
        }
        if($details->graduationdate != null)
        {
            $details->graduationdate = date('m/d/Y', strtotime($details->graduationdate));
        }
        $schoolinfo = Db::table('schoolinfo')
            ->select(
                'schoolinfo.schoolid',
                'schoolinfo.schoolname',
                'schoolinfo.authorized',
                'schoolinfo.picurl',
                'refcitymun.citymunDesc as division',
                'schoolinfo.district',
                'schoolinfo.address',
                'refregion.regDesc as region'
            )
            ->leftJoin('refregion','schoolinfo.region','=','refregion.regCode')
            ->leftJoin('refcitymun','schoolinfo.division','=','refcitymun.citymunCode')
            ->first();
            
        $maxsubjcount = 0;

        if(count($records) == 0)
        {
            $maxsubjcount = 11;
        }else{
            $maxsubjcount = collect($records)->pluck('subjcount')->max();
        }
        $getphoto = DB::table('studdisplayphoto')
              ->where('studid', $studentid)
              ->where('deleted','0')
              ->first();
        // return $request->all();
        // return collect($records)->groupBy('schoolname');
        if(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'sbc')
        {
            $signatories = DB::table('signatory')
            ->where('createdby', auth()->user()->id)
            ->where('deleted','0')
            ->where('form','tor')
            ->get();;
            // return $records;
            $pdf = PDF::loadview('registrar/pdf/pdf_tor_sbc_dompdf',compact('getphoto','schoolinfo','studentinfo','records','maxsubjcount','details','registrar','assistantreg','or','dateissued','lastcourseid','signatories','preparedby','checkedby'))->setPaper('legal','portrait'); 
            return $pdf->stream('TOR.pdf');
        }
        elseif(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'gbbc')
        {
            $pdf = PDF::loadview('registrar/pdf/pdf_tor_gbbc_dompdf',compact('schoolinfo','studentinfo','records','maxsubjcount','details','registrar','assistantreg','or','dateissued'))->setPaper('legal','portrait'); 
            return $pdf->stream('TOR.pdf');
        }
        elseif(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'ndsc')
        {
            
            $pdf = PDF::loadview('registrar/pdf/pdf_tor_ndsc_dompdf',compact('schoolinfo','studentinfo','records','maxsubjcount','details','registrar','assistantreg','or','dateissued'))->setPaper('legal','portrait'); 
            $pdf->getDomPDF()->set_option("enable_php", true);
            return $pdf->stream('TOR.pdf');
        }
        elseif(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'hccsi')
        {
            
            $getphoto = DB::table('studdisplayphoto')
                  ->where('studid', $studentid)
                  ->where('deleted','0')
                  ->first();
            $pdf = PDF::loadview('registrar/pdf/pdf_tor_hccsi',compact('schoolinfo','studentinfo','records','maxsubjcount','details','registrar','assistantreg','or','dateissued','getphoto')); 
            $pdf->getDomPDF()->set_option("enable_php", true);
            return $pdf->stream('TOR.pdf');
        }
        elseif(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'sait')
        {
            // return collect($getphoto);
            
            $pdf = PDF::loadview('registrar/pdf/pdf_tor_sait',compact('schoolinfo','studentinfo','records','maxsubjcount','details','registrar','assistantreg','or','dateissued','getphoto'))->setPaper('legal','portrait'); 
            $pdf->getDomPDF()->set_option("enable_php", true);
            return $pdf->stream('TOR.pdf');
        }
        elseif(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'mci')
        {
                //   return $records;
            $pdf = PDF::loadview('registrar/pdf/pdf_tor_mci',compact('schoolinfo','studentinfo','records','maxsubjcount','details','registrar','assistantreg','or','dateissued','getphoto')); 
            $pdf->getDomPDF()->set_option("enable_php", true);
            return $pdf->stream('TOR.pdf');
        }
        elseif(strtolower(DB::table('schoolinfo')->first()->abbreviation) == 'ccsa')
        {
            $signatories = DB::table('signatory')
                ->where('createdby', auth()->user()->id)
                ->where('deleted','0')
                ->where('form','tor')
                ->whereIn('title',['School Treasurer','OIC - Registrar'])
                ->get();
            // return $request->all();
            $format = $request->get('format');
            $remarks = $request->get('remarks');
            $preparedncheckedby = $request->get('preparedncheckedby');
            $verifiednreleasedby = $request->get('verifiednreleasedby');
            $clearedby = $request->get('clearedby');
                //   return $records;
            $pdf = PDF::loadview('registrar/pdf/pdf_tor_ccsa',compact('schoolinfo','studentinfo','records','maxsubjcount','details','registrar','assistantreg','or','dateissued','getphoto','format','remarks','preparedncheckedby','verifiednreleasedby','clearedby','collegedeanname','signatories','numberofrows')); 
            $pdf->getDomPDF()->set_option("enable_php", true);
            // ->set_option("DOMPDF_ENABLE_CSS_FLOAT", true);
            return $pdf->stream('TOR.pdf');
        }
        else
        {
            // sait
            $pdf = PDF::loadview('registrar/pdf/pdf_tor_sait',compact('schoolinfo','studentinfo','records','maxsubjcount','details','registrar','assistantreg','or','dateissued','getphoto'))->setPaper('legal','portrait'); 
            $pdf->getDomPDF()->set_option("enable_php", true);
            return $pdf->stream('TOR.pdf');
        }
        
        // $pdf = PDF::loadview('registrar/pdf/pdf_tor_gbbc',compact('schoolinfo','studentinfo','records','maxsubjcount'))->setPaper('legal','portrait'); 
        // return $pdf->stream('TOR.pdf');

        // $pdf = new TOR_TCPDF_GBBC(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // // set document information
        // $pdf->SetCreator('CK');
        // $pdf->SetAuthor('CK Children\'s Publishing');
        // $pdf->SetTitle($schoolinfo->schoolname.' - Summary');
        // $pdf->SetSubject('Summary');
        
        // // $pdf->setPrintHeader(false);
        // // set header and footer fonts
        // $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        // $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        
        // // set default monospaced font
        // $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        
        // // set margins
        // $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        // $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        // $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        
        // // set auto page breaks
        // // $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        
        // // set image scale factor
        // $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        
        // // set some language-dependent strings (optional)
        // if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
        //     require_once(dirname(__FILE__).'/lang/eng.php');
        //     $pdf->setLanguageArray($l);
        // }
        
        // // ---------------------------------------------------------
        
        // // set font
        // $pdf->SetFont('dejavusans', '', 10);
        // $pdf->SetMargins(5, 30, 5, true);
        // $pdf->SetAutoPageBreak(true, 90);
        // // - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        // // Print a table
        // // return $data;
        // // add a page
        // // return $selectedacadprog;
        // $pdf->AddPage('P','Letter');

        // $view = \View::make('registrar/pdf/pdf_tor_gbbc',compact('schoolinfo','studentinfo','records','maxsubjcount','details'));
        // $html = $view->render();
        // $pdf->writeHTML($html, true, false, true, false, '');
        // // $image_file =  asset($studentinfo->picurl); 
        // // return $image_file;
        // // $pdf->Image('@'.file_get_contents($image_file),25,5,22,22);
        // // ---------------------------------------------------------
        // //Close and output PDF document
        // $pdf->Output('TOR.pdf', 'I');
    }
}
// results_torrecords.blade.php
class TOR_TCPDF_GBBC extends TCPDF {

    //Page header
    public function Header() {
        $schoollogo = DB::table('schoolinfo')->first();
        $image_file = public_path().'/'.$schoollogo->picurl; 
        $extension = explode('.', $schoollogo->picurl);
        $this->Image('@'.file_get_contents($image_file),25,5,22,22);
        
        $image_fileheader = base_path().'/public/assets/images/gbbc/tor-header.png'; 
        $this->Image('@'.file_get_contents($image_fileheader),50,5,140,22);
        
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-85);
        // // Page number
        $registrarname = '';

        $registrar = DB::table('teacher')
            ->where('userid', auth()->user()->id)
            ->first();
            
        if($registrar)
        {
            if($registrar->firstname != null)
            {
                $registrarname.=$registrar->firstname.' ';
            }
            if($registrar->middlename != null)
            {
                $registrarname.=$registrar->middlename[0].'. ';
            }
            if($registrar->lastname != null)
            {
                $registrarname.=$registrar->lastname;
            }
        }

        $footertable = '<table style="width: 100%; font-size: 12px; margin-left: 20px; font-weight: bold;">
                            <thead>
                                <tr>
                                    <th style="width: 2%;"></th>
                                    <th style="width: 10%;">Remarks:</th>
                                    <th style="border-bottom: 1px solid black; width: 83%;"></th>
                                    <th style="width: 5%;"></th>
                                </tr>
                            </thead>
                            <tr>
                                <td style="width: 2%;">&nbsp;</td>
                                <td colspan="2" style="width: 93%; border-bottom: 1px solid black;">&nbsp;</td>
                                <td style="width: 5%;">&nbsp;</td>
                            </tr>
                        </table>
                        <table style="width: 100%; font-size: 13px;">
                            <thead>
                                <tr>
                                    <th style="width: 2%;">&nbsp;</th>
                                    <th style="width: 93%;">GRADING SYSTEM:</th>
                                    <td style="width: 5%;">&nbsp;</td>
                                </tr>
                            </thead>
                        </table>
                        <table style="width: 100%; font-size: 12px;">
                            <thead>
                                <tr>
                                    <th style="width: 2%;"></th>
                                    <th style="width: 29%;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;95-100-Denotes Excellent</th>
                                    <th style="width: 28%;">80-84&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- &nbsp;Denotes Satisfactory</th>
                                    <th style="width: 20%;">W&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;Withdrawn</th>
                                    <th style="width: 21%;">&nbsp;&nbsp;FD&nbsp;&nbsp;-&nbsp;Failure Debarred</th>
                                </tr>
                                <tr>
                                    <th style="width: 2%;"></th>
                                    <th style="width: 29%;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;90-94&nbsp;&nbsp;-Denotes Very Good</th>
                                    <th style="width: 28%;">75-79&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- &nbsp;Denotes Fair</th>
                                    <th style="width: 20%;">Inc.&nbsp;&nbsp;-&nbsp;&nbsp;Incomplete</th>
                                    <th style="width: 21%;">&nbsp;&nbsp;Drp&nbsp;&nbsp;-&nbsp;&nbsp;Dropped</th>
                                </tr>
                                <tr>
                                    <th style="width: 2%;"></th>
                                    <th style="width: 29%;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;85-89-Denotes Good</th>
                                    <th style="width: 28%;">74 & below&nbsp;&nbsp;- &nbsp;Signifies Failure</th>
                                    <th style="width: 20%;">WF&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;Withdrawn Failure</th>
                                    <th style="width: 21%;"></th>
                                </tr>
                            </thead>
                        </table>
                        <table style="width: 100%; font-size: 13px;">
                            <thead>
                                <tr>
                                    <th style="width: 2%;">&nbsp;</th>
                                    <th style="width: 7%; font-weight: bold;">Note:</th>
                                    <th style="width: 91%; font-weight: bold;"></th>
                                </tr>
                                <tr>
                                    <th style="width: 2%;">&nbsp;</th>
                                    <th style="width: 7%;">&nbsp;</th>
                                    <th style="width: 91%;">
                                        &nbsp;&nbsp;&nbsp;This transcript is valid only when it bears the seal of the College
                                    </th>
                                </tr>
                                <tr>
                                    <th style="width: 2%;">&nbsp;</th>
                                    <th style="width: 7%;">&nbsp;</th>
                                    <th style="width: 91%;">
                                        &nbsp;&nbsp;&nbsp;and the original signature in ink of the Registrar.
                                    </th>
                                </tr>
                                <tr>
                                    <th style="width: 2%;">&nbsp;</th>
                                    <th style="width: 7%;">&nbsp;</th>
                                    <th style="width: 91%;">
                                        &nbsp;&nbsp;&nbsp;Any erasures or alteration made on the entries of this form renders this transcript null and void.
                                    </th>
                                </tr>
                            </thead>
                        </table>
                        <table style="width: 100%;">
                            <thead style="font-size: 13px;">
                                <tr>
                                    <th style="width: 100%;">&nbsp;</th>
                                </tr>
                                <tr>
                                    <th style="width: 25%;">&nbsp;</th>
                                    <th style="width: 75%;">Prepared by:</th>
                                </tr>
                            </thead>
                        </table>
                        <table style="width: 100%; font-size: 13px;">
                            <tr>
                                <th style="width: 30%;">&nbsp;</th>
                                <th style="width: 40%; text-align: center;"></th>
                                <th style="width: 30%;">&nbsp;</th>
                            </tr>
                        </table>
                        <table style="width: 100%; font-size: 13px;">
                            <tr>
                                <td style="width: 30%; font-size: 11px;"><em>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;School Seal</em></td>
                                <td style="width: 40%; font-size: 11px; text-align: center;">Assistant Registrar</td>
                                <td style="width: 30%; text-align: center;">'.$registrarname.'</td>
                            </tr>
                            <tr>
                                <td style="width: 30%; font-size: 11px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;O.R #:</td>
                                <td style="width: 40%; font-size: 11px; text-align: center;"></td>
                                <td style="width: 30%; text-align: center; font-size: 11px;">Registrar</td>
                            </tr>
                            <tr>
                                <td style="width: 30%; font-size: 11px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Date Issued:</td>
                                <td style="width: 40%; font-size: 11px; text-align: center;"></td>
                                <td style="width: 30%; text-align: center;"></td>
                            </tr>
                        </table>
                        ';
        $this->writeHTML($footertable, false, true, false, true);   
    }
    public function signatories(Request $request)
    {
        
    }
}
