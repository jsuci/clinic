@extends('adminPortal.layouts.app2')


@section('pagespecificscripts')
    <link rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
@endsection

@section('content')
    
    {{-- <section class="content-header">
    </section> --}}
    <section class="content">
        <form action="{{isset($schoolInfo) ? '/updateschoolinfo' :'/insertinfo'}}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
            <div class="col-md-8 container-fluid">
                <div class="card h-100 shadow">
                    <div class="card-header bg-info">
                    <h3 class="card-title"><i class="fas fa-coins"></i> <b>SCHOOL INFORMATION</b></h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="card-body ">
                            <div class="form-group">
                                <label><b>School Id</b></label>
                                <input placeholder="SCHOOL ID" value="{{isset($schoolInfo->schoolid) ? $schoolInfo->schoolid :''}}"  name="schoolid" class="form-control" onkeyup="this.value = this.value.toUpperCase();">
                            </div>
                            <div class="form-group">
                                <label><b>School Name</b></label>
                                <input placeholder="SCHOOL NAME" value="{{isset($schoolInfo->schoolname) ? $schoolInfo->schoolname :''}}" name="schoolname" class="form-control" onkeyup="this.value = this.value.toUpperCase();">
                            </div>
                            <div class="form-group">
                                <label><b>School Abbreviation</b></label>
                                <input placeholder="ABBREVIATION" value="{{isset($schoolInfo->abbreviation) ? $schoolInfo->abbreviation :''}}" name="abbreviation" class="form-control" onkeyup="this.value = this.value.toUpperCase();">
                            </div>
                            <div class="form-group">
                                <label>Region</label>
                                <input type="text" class="form-control"  name="region" id="region" value="{{isset($schoolInfo->regiontext) ? $schoolInfo->regiontext :''}}" onkeyup="this.value = this.value.toUpperCase();">
                            </div>
                            <div class="form-group">
                                <label>Division</label>
                                <input type="text" class="form-control"  name="division" id="division" value="{{isset($schoolInfo->divisiontext) ? $schoolInfo->divisiontext :''}}" onkeyup="this.value = this.value.toUpperCase();">
                            </div>
                            <div class="form-group">
                                <label><b>District</b></label>
                                <input placeholder="SCHOOL DISTRICT"  value="{{isset($schoolInfo->districttext) ? $schoolInfo->districttext :''}}" type="text" name="district" class="form-control" onkeyup="this.value = this.value.toUpperCase();">
                            </div>
                            <div class="form-group">
                                <label><b>Address</b></label>
                                <input placeholder="SCHOOL ADDRESS" value="{{isset($schoolInfo->address) ? $schoolInfo->address :''}}" name="address" class="form-control" onkeyup="this.value = this.value.toUpperCase();">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button  type="submit" class="btn {{isset($schoolInfo) ? 'btn-success' :'btn-info'}}" ><i class="fas fa-paper-plane" ></i> 
                                {{isset($schoolInfo) ? 'UPDATE' :'SUBMIT'}}</button>
                    </div>
                </div>
                </div>
                <div class="col-md-4 ">
                    <div class="card h-100 shadow">
                        <div class="card-header bg-info">
                            <div class="card-title">
                            <h3 class="card-title"><b><i style="color: #ffc107" class="fab fa-pushed"></i>MORE INFO</b></h3>
                                
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                            </div>
                                <div class="form-group">
                                    <label for=""><b>School Tag Line</b></label>
                                    <textarea placeholder="SCHOOL TAGLINE" class="form-control" name="schooltagline" rows="3">{{$schoolInfo->tagline}}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for=""><b>School Logo</b></label>
                                    <input type="file" name="schoollogo" id="schoollogo" class="form-control @error('schoollogo') is-invalid @enderror">
                                    @if($errors->has('schoollogo'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('schoollogo') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                @if(isset($schoolInfo->picurl))
                                    <img id="logoDisplay" src="{{asset($schoolInfo->picurl)}}" alt="" class="w-100">
                                @else
                                    <img id="logoDisplay" src="{{asset($schoolInfo->picurl)}}" alt="" class="w-100">
                                @endif
                        </div>
                    </div>
                </div>
            </div>
      </form>
</section>
@endsection

@section('footerjavascript')

    <script src="{{asset('plugins/select2/js/select2.full.min.js')}}"></script>

    <script>
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        })


        $(document).ready(function(){

            function readURL(input) {
                    if (input.files && input.files[0]) {
                        var reader = new FileReader();
                        
                        reader.onload = function (e) {
                            $('#logoDisplay').attr('src', e.target.result);
                        }
                        
                        reader.readAsDataURL(input.files[0]);
                    }
            }

            $("#schoollogo").change(function(){
                    readURL(this);
            });

        
            // @if(isset($schoolInfo->division))

            //     $.ajax({
            //             type:'GET',
            //             url:'/admingetcity',
            //             data:{
            //                 data:'{{$schoolInfo->region}}',
            //             },
            //             success:function(data) {
            //                 $('#division').empty();
            //                 $('#division').append('<option>SELECT DIVISION</option>');
            //                 $.each(data,function(key,value){
            //                     if(value.citymunCode == '{{$schoolInfo->division}}'){
            //                         $('#division').append('<option selected value='+value.citymunCode+'>'+value.citymunDesc+'</option>');
            //                     }
            //                     else{
            //                         $('#division').append('<option value='+value.citymunCode+'>'+value.citymunDesc+'</option>');
            //                     }

                            
            //                 })  
            //         }
            //     })
            // @endif


            // $(document).on('change','#region',function(){
            //     $.ajax({
            //         type:'GET',
            //         url:'/admingetcity',
            //         data:{
            //             data:$(this).val(),
            //         },
            //         success:function(data) {
            //             $('#division').empty();
            //             $('#division').append('<option>SELECT DIVISION</option>');
            //             $.each(data,function(key,value){
                            
            //                 $('#division').append('<option value='+value.citymunCode+'>'+value.citymunDesc+'</option>');
            //             })  
            //         }
            //     })
            // })

        })


    </script>
@endsection