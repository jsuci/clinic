
@extends('superadmin.layouts.app2')

@section('pagespecificscripts')
    <link rel="stylesheet" href="{{asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/summernote/summernote-bs4.css')}}">

      <style>
            .dropdown-toggle::after {
                  display: none;
                  margin-left: .255em;
                  vertical-align: .255em;
                  content: "";
                  border-top: .3em solid;
                  border-right: .3em solid transparent;
                  border-bottom: 0;
                  border-left: .3em solid transparent;
            }
            .shadow {
                  box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
                  border: 0;
            }
      </style>
@endsection

@section('modalSection')

@endsection

@section('content')


<div class="modal fade" id="viewRequirements" style="display: none;" >
      <div class="modal-dialog modal-lg">
            <div class="modal-content">
                  <div class="modal-header bg-primary">
                        <h5 class="mb-0">View</h5>
                        <div class="card-tools">
                             <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#viewRequirementsForm">CREATE REQUIREMENTS </button>
                        </div>
                  </div>
                  <div class="modal-body" id="reqtableholder">
                      
                  </div>
                  <div class="moda-footer">

                  </div>
            </div>
      </div>
</div>

<div class="modal fade" id="viewRequirementsForm" style="display: none;" >
      <div class="modal-dialog">
            <div class="modal-content">
                  <div class="modal-header bg-primary">
                        <h5 class="mb-0">Preregistration Requirements Form</h5>
                  </div>
                  <form id="preregreqform">
                        <div class="modal-body">
                              <div class="form-group row">
                                    <label for="inputEmail3" class="col-sm-4 col-form-label">Description</label>
                                    <div class="col-sm-8">
                                    <input class="form-control" name="description" id="description" placeholder="Description">
                                    </div>
                              </div>
                              <div class="form-group row">
                                    <label for="inputEmail3" class="col-sm-4 col-form-label">Acad. Prog</label>
                                    <div class="col-sm-8">
                                          <select name="acadprogid" id="acadprogid"  class="form-control">
                                                <option value="">Please Specify</option>
                                                @foreach (DB::table('academicprogram')->get() as $item)
                                                      <option value="{{$item->id}}">{{$item->progname}}</option>
                                                @endforeach
                                    </select>
                                    </div>
                              </div>
                              <div class="form-group row">
                                    <label for="inputEmail3" class="col-sm-4 col-form-label">Active State</label>
                                    <div class="col-sm-8">
                                    <select name="isactive" id="isactive" class="form-control">
                                                <option value="">Please Specify</option>
                                                <option value="1">Active</option>
                                                <option value="0">InActive</option>
                                    </select>
                                    </div>
                              </div>
                              <div class="form-group row">
                                    <label for="inputEmail3" class="col-sm-4 col-form-label">Required State</label>
                                    <div class="col-sm-8">
                                    <select name="isrequired" id="isrequired" class="form-control">
                                                <option value="">Please Specify</option>
                                                <option value="1">Required</option>
                                                <option value="0">Not Required</option>
                                    </select>
                                    </div>
                              </div>
                        </div>
                        <div class="modal-footer">
                              <button type="sumbit" class="btn btn-primary">SUBMIT</button>
                        </div>
                  </form>
            </div>
      </div>
</div>
<script>
    
</script>

<section class="content-header">
      <div class="container-fluid">
            <div class="row">
                  <div class="col-sm-6">
                        <h1>School Information Setup</h1>
                  </div>
                  <div class="col-sm-6">
                  <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/home">Home</a></li>
                        <li class="breadcrumb-item active">School Info</li>
                  </ol>
                  </div>
            </div>
      </div>
</section>


    
<section class="content pt-0">
        <div class="container-fluid">
            <div class="row">
                  <div class="col-12">
                        <div class="card shadow">
                              <div class="card-body">
                                    <div class="row">
                                          <div class="form-group col-md-12">
                                                <label for="">Database name</label>
                                                <input class="form-control form-control-sm" readonly value="{{$databasename}}">
                                          </div>
                                          <div class="form-group col-md-4">
                                                <label for="">School ID</label>
                                                <input class="form-control form-control-sm" readonly value="{{$schoolinfo->schoolid}}">
                                          </div>
                                          <div class="form-group col-md-8">
                                                <label for="">School Name</label>
                                                <input class="form-control form-control-sm" readonly value="{{$schoolinfo->schoolname}}">
                                          </div>
                                          <div class="form-group col-md-4">
                                                <label for="">School Region</label>
                                                <input class="form-control form-control-sm" readonly value="{{$schoolinfo->regiontext}}">
                                          </div>
                                          <div class="form-group col-md-4">
                                                <label for="">School Division</label>
                                                <input class="form-control form-control-sm" readonly value="{{$schoolinfo->divisiontext}}">
                                          </div>
                                          <div class="form-group col-md-4">
                                                <label for="">School District</label>
                                                <input class="form-control form-control-sm" readonly value="{{$schoolinfo->districttext}}">
                                          </div>
                                          <div class="form-group col-md-12">
                                                <label for="">School Address</label>
                                                <input class="form-control form-control-sm" readonly value="{{$schoolinfo->address}}">
                                          </div>
                                          <div class="form-group col-md-12 ">
                                                <label for="">School Tagline</label>
                                                <textarea class="form-control text-area" rows="3" readonly>{{$schoolinfo->tagline}}</textarea>
                                          </div>
                                          {{-- <div class=" row mb-0 col-md-12 pt-2 pb-2 pr-0 border-bottom border-top" >
                                                <div class="col-md-4">
                                                      <button class="btn btn-primary btn-sm" id="udpateckpass" ><i class="fas fa-sync-alt"></i> Update ckgroup Password</button>
                                                </div>
                                          </div> --}}
                                       
                                          <div class=" row mb-0 col-md-12 pt-2 pb-2 pr-0 border-bottom border-top" >
                                                <div class="col-md-3">
                                                      <button class="btn btn-primary  btn-sm btn-block" id="generateAdmin" ><i class="fas fa-sync-alt"></i> Generate Admin IT Password</button>
                                                </div>
                                                <div class="col-md-9 pl-0">
                                                      <input class="form-control form-control-sm" id="password" placeholder="Admin IT Password" value="{{isset($admin_pass->passwordstr) ? $admin_pass->passwordstr : ''}}">
                                                </div>
                                          </div>

                                          <div class=" row mb-0 col-md-12 pt-2 pb-2 pr-0 border-bottom border-top" >
                                                <div class="col-md-3">
                                                      <button class="btn btn-primary  btn-sm btn-block" id="generateAdminAdmin" ><i class="fas fa-sync-alt"></i> Generate Admin Admin Password</button>
                                                </div>
                                                <div class="col-md-9 pl-0">
                                                      <input class="form-control form-control-sm" id="adminadmin_password" placeholder="Admin IT Password" value="{{isset($adminadmin_pass->passwordstr) ? $adminadmin_pass->passwordstr : ''}}">
                                                </div>
                                          </div>
                                          
                                        
                                         {{-- <form id="updateschoolmodeoflearningForm" class="form-group row mb-0 col-md-12 pt-2 pb-2 pr-0 border-bottom border-top" 
                                          action="/admin/update/school/modeOfLearning" 
                                          method="GET"
                                          >
                                                <label for="inputEmail3" class="col-md-3 col-form-label">With Mode of Learning</label>
                                                <div class="col-md-5 pt-2 pl-0">
                                                      <div class="icheck-primary d-inline">
                                                            <input type="radio" name="withMOL" id="withMOLa" @if($schoolinfo->withMOL == 1)checked @endif value="1">
                                                            <label for="withMOLa"> Yes
                                                            </label>
                                                      </div>
                                                      <div class="icheck-primary d-inline ml-3">
                                                            <input type="radio" name="withMOL" id="withMOLb" @if($schoolinfo->withMOL == 0)checked @endif value="0">
                                                            <label for="withMOLb"> No
                                                            </label>
                                                      </div>
                                                </div>
                                                <div class="col-md-4 text-right pr-0">
                                                      <button type="button" class="btn btn-primary  btn-sm" id="updateschoolmodeoflearning"><i class="far fa-edit"></i> UPDATE</button>
                                                </div>
                                          </form>--}}
                                          
                                          <form id="updatelockfeesForm" class="form-group row mb-0 col-md-12 pt-2 pb-2 pr-0 border-bottom border-top" 
                                          action="/lockfees" 
                                          method="GET"
                                          >
                                                <label for="inputEmail3" class="col-md-3 col-form-label">Lock Fees</label>
                                                <div class="col-md-5 pt-2 pl-0">
                                                      <div class="icheck-primary d-inline">
                                                            <input type="radio" name="lockfees" id="lockfeesa" @if($schoolinfo->lockfees == 1)checked @endif value="1">
                                                            <label for="lockfeesa"> Yes
                                                            </label>
                                                      </div>
                                                      <div class="icheck-primary d-inline ml-3">
                                                            <input type="radio" name="lockfees" id="lockfeesb" @if($schoolinfo->lockfees == 0)checked @endif value="0">
                                                            <label for="lockfeesb"> No
                                                            </label>
                                                      </div>
                                                </div>
                                                <div class="col-md-4 text-right pr-0">
                                                      <button type="button" class="btn btn-primary  btn-sm" id="updatelockfees"><i class="far fa-edit"></i> UPDATE</button>
                                                </div>
                                          </form>
                                         

                                          <form id="updateschoolescForm" class="form-group row mb-0 col-md-12 pt-2 pb-2 pr-0 border-bottom border-top" 
                                          action="/admin/update/school/esc" 
                                          method="GET"
                                          >
                                                <label for="inputEmail3" class="col-md-3 col-form-label">With ESC</label>
                                                <div class="col-md-5 pt-2 pl-0">
                                                      <div class="icheck-primary d-inline">
                                                            <input type="radio" name="withESC" id="withESCa" @if($schoolinfo->withESC == 1)checked @endif value="1">
                                                            <label for="withESCa"> Yes
                                                            </label>
                                                      </div>
                                                      <div class="icheck-primary d-inline ml-3">
                                                            <input type="radio" name="withESC" id="withESCb" @if($schoolinfo->withESC == 0)checked @endif value="0">
                                                            <label for="withESCb"> No
                                                            </label>
                                                      </div>
                                                </div>
                                                <div class="col-md-4 text-right pr-0">
                                                      <button type="button" class="btn btn-primary  btn-sm" id="updateESC"><i class="far fa-edit"></i> UPDATE</button>
                                                </div>
                                          </form>
                                          <form id="updateschoolcolor" class="form-group row col-md-12 pt-2 pb-2 border-bottom pr-0 border-bottom" action="/admin/update/school/schoolcolor" method="GET">
                                                <label for="inputEmail3" class="col-md-3 col-form-label">School Color</label>
                                                <div class="input-group col-md-9 p-0">
                                                      <input 
                                                            class="form-control" 
                                                            name="schoolcolor"
                                                            value="{{$schoolinfo->schoolcolor}}">
                                                      <span class="input-group-append">
                                                            <button type="button" class="btn btn-primary btn-sm" id="updateschoolcolorbutton"><i class="far fa-edit"></i> UPDATE</button>
                                                      </span>
                                                </div>
                                          </form>

                                          <div class="card-body pad p-2 col-md-12">
                                                <form id="updateterms" action="/admin/update/school/terms" method="POST">
                                                      @csrf
                                                      <label >Terms and Agreements</label>
                                                      <div class="mb-3">
                                                            <textarea class="textarea" placeholder="Place some text here" name="terms"
                                                                  2. >{!! html_entity_decode($schoolinfo->terms)!!}</textarea>

                                                            <button type="button" class="btn btn-primary btn-sm" id="updatetermsbutton"><i class="far fa-edit"></i> UPDATE TERMS AND AGREEMENTS</button>
                                                      </div>
                                                </form>
                                          </div>
                                     
                                    </div>
                              </div>
                        </div>
                  </div>
            </div>
      </section>
@endsection

@section('footerjavascript')
<script src="{{asset('plugins/sweetalert2/sweetalert2.all.min.js')}}"></script>
<script src="{{asset('plugins/summernote/summernote-bs4.min.js')}}"></script>

<script>
  $(function () {
    $('.textarea').summernote({
      toolbar: [
                  ['style', ['style']],
                  ['font', ['bold', 'italic', 'underline', 'clear']],
                  ['fontname', ['fontname']],
                  ['color', ['color']],
                  ['para', ['ul', 'ol', 'paragraph']],
                  ['height', ['height']],
                  ['table', ['table']],
                  ['insert', ['link', 'picture', 'hr']],
                  ['view', ['fullscreen', 'codeview']],
                  ['help', ['help']],
                  ['fontsize', ['fontsize']],
                  ],
      })
  })
</script>
<script>

      var selectedReq = null;

      loadRequirements()

      function loadRequirements(){

            $.ajax({
                  type:'POST',
                  url: '/requirementslist?table=table',
                  data: {'_token': '{{ csrf_token() }}'},
                  success:function(data){
                        $('#reqtableholder').empty()
                        $('#reqtableholder').append(data)
                  
                  }
            })

      }
      
       $(document).on('click','#updatewebsitebutton',function(){
            Swal.fire({
                  title: 'Are you sure?',
                  type: 'info',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'UPDATE'
            })
            .then((result) => {
                  if (result.value) {
                        event.preventDefault(); 
                        $('#updateWebsiteLink').submit()
                  }
            })
        })

        $(document).on('click','#updateessentiel',function(){
            Swal.fire({
                  title: 'Are you sure?',
                  type: 'info',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'UPDATE'
            })
            .then((result) => {
                  if (result.value) {
                        event.preventDefault(); 
                        $('#essentiellink').submit()
                  }
            })
        })

        $(document).on('click','#updateescloudurl',function(){
            Swal.fire({
                  title: 'Are you sure?',
                  type: 'info',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'UPDATE'
            })
            .then((result) => {
                  if (result.value) {
                        event.preventDefault(); 
                        $('#es_cloud_link').submit()
                  }
            })
        })


        

        $(document).on('click','#updateschoolmodeoflearning',function(){
            Swal.fire({
                  title: 'Are you sure?',
                  type: 'info',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'UPDATE'
            })
            .then((result) => {
                  if (result.value) {
                        event.preventDefault(); 
                        $('#updateschoolmodeoflearningForm').submit()
                  
                  }
            })
        })

        $(document).on('click','#updatelockfees',function(){
            Swal.fire({
                  title: 'Are you sure?',
                  type: 'info',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'UPDATE'
            })
            .then((result) => {
                  if (result.value) {
                        event.preventDefault(); 
                        $('#updatelockfeesForm').submit()
                  
                  }
            })
        })



        

        $(document).on('click','#updateESC',function(){

            Swal.fire({
                  title: 'Are you sure?',
                  type: 'info',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'UPDATE'
            })
            .then((result) => {
                  if (result.value) {
                        event.preventDefault(); 
                        $('#updateschoolescForm').submit()
                  
                  }
            })
        })

        $(document).on('click','#updatesetup',function(){

            Swal.fire({
                  title: 'Are you sure?',
                  type: 'info',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'UPDATE'
            })
            .then((result) => {
                  if (result.value) {
                        event.preventDefault(); 
                        $('#schoolsetup').submit()
                  
                  }
            })
      })


        $(document).on('click','#updatetermsbutton',function(){
            Swal.fire({
                  title: 'Are you sure?',
                  type: 'info',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'UPDATE'
            })
            .then((result) => {
                  if (result.value) {
                        event.preventDefault(); 
                        $('#updateterms').submit()
                  
                  }
            })
        })

            $(document).on('click','#updateschoolcolorbutton',function(){
                  Swal.fire({
                        title: 'Are you sure?',
                        type: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'UPDATE'
                  })
                  .then((result) => {
                        if (result.value) {
                              event.preventDefault(); 
                              $('#updateschoolcolor').submit()
                        
                        }
                  })
            })

            $(document).on('click','.setActiveState',function(){
                  Swal.fire({
                        title: 'Are you sure?',
                        type: 'primary',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'SET AS ACTIVE'
                  })
                  .then((result) => {
                        if (result.value) {
                              $.ajax({
                                    type:'POST',
                                    url: '/requirementslist?settoactive=settoactive&reqid='+$(this).attr('data-id'),
                                    data: {'_token': '{{ csrf_token() }}'},
                                    success:function(data){

                                          loadRequirements()
                                    
                                    }
                              })
                        
                        }
                  })
            })

            $(document).on('click','.removeActiveState',function(){
                  Swal.fire({
                        title: 'Are you sure?',
                        type: 'primary',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'SET AS INACTIVE'
                  })
                  .then((result) => {
                        if (result.value) {
                              $.ajax({
                                    type:'POST',
                                    url: '/requirementslist?settoinactive=settoinactive&reqid='+$(this).attr('data-id'),
                                    data: {'_token': '{{ csrf_token() }}'},
                                    success:function(data){
                                          
                                          loadRequirements()
                                    
                                    }
                              })
                        
                        }
                  })
            })

            // updaterequirement

            $(document).on('click','.updaterequirement',function(){

                  $('#viewRequirementsForm').modal()
                  selectedReq = $(this).attr('data-id')

                  $.ajax({
                        type:'POST',
                        url: '/requirementslist?info=info&reqid='+$(this).attr('data-id'),
                        data: {'_token': '{{ csrf_token() }}'},
                        success:function(data){


                              
                              $('#description').val(data[0].description)
                              $('#acadprogid').val(data[0].acadprogid)
                              $('#isactive').val(data[0].isActive)
                              $('#isrequired').val(data[0].isRequired)
                        
                        }
                  })

            })

            $(document).on('click','#generateAdmin',function(){

                  Swal.fire({
                        title: 'Are you sure?',
                        text: "This will override the ADMIN password.",
                        type: 'primary',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'GENERATE'
                  })
                  .then((result) => {
                        if (result.value) {
                              $.ajax({
                                    type:'GET',
                                    url: '/generateAdminPass',
                                    success:function(data){
                                          $('#password').val(data[0].code)
                                          $('#hashed').val(data[0].hash)
                                    }
                              })
                        }
                  })

            })


            $(document).on('click','#generateAdminAdmin',function(){

                  Swal.fire({
                        title: 'Are you sure?',
                        text: "This will override the ADMIN ADMIN password.",
                        type: 'primary',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'GENERATE'
                  })
                  .then((result) => {
                        if (result.value) {
                              $.ajax({
                                    type:'GET',
                                    url: '/generateAdminAdminPass',
                                    success:function(data){
                                          $('#adminadmin_password').val(data[0].code)
                                          $('#hashed').val(data[0].hash)
                                    }
                              })
                        }

                  })

            })

            $(document).on('click','#udpateckpass',function(){

                  Swal.fire({
                        title: 'Are you sure?',
                        text: "This will revert the ckgroup password to default.",
                        type: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'UPDATE'
                  })
                  .then((result) => {

                        if (result.value) {

                              $.ajax({
                                    type:'GET',
                                    url: '/generateckpass',
                                    success:function(data){

                                          Swal.fire({
                                                type: 'success',
                                                title: 'Update Successfully',
                                                showConfirmButton: false,
                                                timer: 1500
                                          })
                                    }
                              })

                        }

                  })

            })



            

            $(document).on('click','.removerequirement',function(){
                  Swal.fire({
                        title: 'Are you sure?',
                        type: 'primary',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'REMOVE'
                  })
                  .then((result) => {
                        
                        if (result.value) {
                              $.ajax({
                                    type:'POST',
                                    url: '/requirementslist?remove=remove&reqid='+$(this).attr('data-id'),
                                    data: {'_token': '{{ csrf_token() }}'},
                                    success:function(data){
                                          
                                          loadRequirements()
                                    
                                    }
                              })
                        
                        }
                  })
            })

            $('#preregreqform').submit(function(e){


                  $.ajax({
                        type:'POST',
                        url: '/requirementslist?create=create&description='+$('#description').val()+'&acadprog='+$('#acadprogid').val()+'&isactive='+$('#isactive').val()+'&isrequired='+$('#isrequired').val()+'&reqid='+selectedReq,
                        data: {'_token': '{{ csrf_token() }}'},
                        success:function(data){
                              selectedReq = null
                              loadRequirements()
                              $('#viewRequirementsForm').modal('hide')
                              $('#preregreqform')[0].reset();
                        
                        }
                  })

                  

                  e.preventDefault()
            })

</script>
    
@endsection

