<style>
    
    html{
        font-family: Arial, Helvetica, sans-serif;
    }
    table{
        border-collapse: collapse;
    }
    @page{
        margin: 50px 20px;
    }
</style>
<table style="width: 100%; font-size: 11px;" border="1">
    <thead>
        <tr>
            <th style="width: 2%;">#</th>
            <th style="width: 5%;">ID</th>
            <th style="width: 15%;">Name</th>
            <th style="width: 5%;">Gender</th>
            <th style="width: 7%;">Birthdate</th>
            <th style="width: 15%;">Address</th>
            <th style="width: 7%;">Contact No.</th>
            <th>Email Address</th>
            <th>Nationality</th>
            <th>Religion</th>
            <th style="width: 5%;">Marital status</th>
            <th>Employment of spouse</th>
            <th style="width: 3%;">No. of children</th>
        </tr>
    </thead>
    @foreach($employees as $key=>$employee)
        <tr>
            <td style="text-align: center;">{{$key+1}}</td>
            <td style="text-align: center;">{{$employee->tid}}</td>
            <td>{{$employee->lastname}}, {{$employee->firstname}} @if($employee->middlename != null){{$employee->middlename}}@endif {{$employee->suffix}}</td>
            <td style="text-align: center;">{{strtoupper($employee->gender)}}</td>
            <td style="text-align: center;">@if($employee->dob != null){{date('M d, Y', strtotime($employee->dob))}}@endif</td>
            <td>{{$employee->address}}</td>
            <td style="text-align: center;">{{$employee->contactnum}}</td>
            <td style="text-align: center;">{{$employee->email}}</td>
            <td style="text-align: center;">{{$employee->nationality}}</td>
            <td style="text-align: center;">{{$employee->religion}}</td>
            <td style="text-align: center;">{{$employee->civilstatus}}</td>
            <td>{{$employee->spouseemployment}}</td>
            <td style="text-align: center;">{{$employee->numberofchildren}}</td>
        </tr>
    @endforeach
</table>