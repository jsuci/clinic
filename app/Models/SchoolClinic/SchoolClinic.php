<?php

namespace App\Models\SchoolClinic;

use Illuminate\Database\Eloquent\Model;
use DB;
class SchoolClinic extends Model
{
    public static function personnel()
    {
        
        $personnel = DB::table('teacher')
            ->select(
                'teacher.id', 'teacher.userid', 'title', 'lastname','firstname','middlename','suffix','employee_personalinfo.gender','employee_personalinfo.address','employee_personalinfo.contactnum','teacher.picurl','usertype.utype','users.loggedIn','users.loggedOut'
            )
            ->join('users','teacher.userid','=','users.id')
            ->leftJoin('employee_personalinfo','teacher.id','=','employee_personalinfo.employeeid')
            ->join('usertype','teacher.usertypeid','=','usertype.id')
            ->whereIn('usertype.refid', [23,24,25])
            ->where('teacher.deleted','0')
            ->get();

        if(count($personnel)>0)
        {
            foreach($personnel as $person)
            {
                $name = "";

                if($person->title != null)
                {
                    $name.=$person->title.' ';
                }
                $name.=$person->firstname.' ';

                if($person->middlename != null)
                {
                    $name.=$person->middlename[0].'. ';
                }
                $name.=$person->lastname.' ';
                $name.=$person->suffix.' ';

                $person->name = $name;
                $person->address = strtolower($person->address);
            }
        }

        return $personnel;
    } 
    public static function users()
    {

        $employees = DB::table('teacher')
            ->select('teacher.id','teacher.userid','teacher.title','teacher.firstname','teacher.middlename','teacher.lastname','teacher.suffix','employee_personalinfo.gender','usertype.utype','teacher.usertypeid')
            ->join('usertype','teacher.usertypeid','=','usertype.id')
            ->leftJoin('employee_personalinfo','teacher.id','=','employee_personalinfo.employeeid')
            ->where('teacher.deleted','0')
            ->get();

        // return $employees;
        $students = DB::table('studinfo')
            ->select('studinfo.id','studinfo.userid','studinfo.firstname','studinfo.middlename','studinfo.lastname','studinfo.suffix','studinfo.gender','lrn')
            ->where('studinfo.deleted','0')
            ->whereIn('studinfo.studstatus',[1,2,4])
            ->get();
            
        if(count($students)>0)
        {
            foreach($students as $student)
            {
                $student->title = null;
                $student->utype = 'STUDENT';
                $student->usertypeid = 7;
            }
        }
        
        $allusers = collect();
        $allusers = $allusers->merge($employees);
        $allusers = $allusers->merge($students);


        if(count($allusers)>0)
        {
            foreach($allusers as $alluser)
            {
                if($alluser->usertypeid != 7)
                {
                    $alluser->lrn = "";
                }
                $name_showfirst = "";

                if($alluser->title != null)
                {
                    $name_showfirst.=$alluser->title.' ';
                }
                $name_showfirst.=$alluser->firstname.' ';

                if($alluser->middlename != null)
                {
                    $name_showfirst.=$alluser->middlename[0].'. ';
                }
                $name_showfirst.=$alluser->lastname.' ';
                $name_showfirst.=$alluser->suffix.' ';

                $alluser->name_showfirst = $name_showfirst;

                $name_showlast = "";

                if($alluser->title != null)
                {
                    $name_showlast.=$alluser->title.' ';
                }
                $name_showlast.=$alluser->lastname.', ';
                $name_showlast.=$alluser->firstname.' ';

                if($alluser->middlename != null)
                {
                    $name_showlast.=$alluser->middlename[0].'. ';
                }
                $name_showlast.=$alluser->suffix.' ';

                $alluser->name_showlast = $name_showlast;
            }
        }
        return $allusers->sortBy('lastname')->all();
    } 
    public static function drugs()
    {
        $drugs = DB::table('clinic_medicines')
            ->where('deleted','0')
            ->orderBy('genericname','asc')
            ->get();

        $alldrugsleft = array();

        if(count($drugs)>0)
        {
            foreach($drugs as $drug)
            {

                $medicated = DB::table('clinic_complaintmed')
                    ->where('drugid', $drug->id)
                    ->where('deleted','0')
                    ->first();

                $drug->quantityleft = $drug->quantity;
                if($medicated)
                {
                    $drug->quantityleft = $drug->quantity-$medicated->quantity;
                }
                $drug->condition    = 'BEST';
                if($drug->expirydate<date('Y-m-d'))
                {
                    $drug->condition    = 'EXP';
                }elseif($drug->expirydate>date("Y-m-d", strtotime('sunday last  week')) && $drug->expirydate<date("Y-m-d", strtotime('sunday this week')))
                {
                    $drug->condition    = 'EXPW';
                }
            }
        }

        return $drugs;

    }
}
