@extends('finance.layouts.app')

@section('content')
<!-- Select2 -->
<link rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/toastr/toastr.min.css')}}">
<style>
    
    .select2-container {
            z-index: 9999;
            margin: 0px;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #007bff;
    border-color: #006fe6;
    color: #fff;
    padding: 0 10px;
    margin-top: .31rem;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: rgba(255,255,255,.7);
    float: right;
    margin-left: 5px;
    margin-right: -2px;
}
</style>
<div class="row p-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-info">
                <label>Select Configuration</label> 
                <div class="input-group mb-3 col-md-6">
                    <select class="form-control" id="configselection">
                        @if(count($configtypes) == 0)
                            <option value="">No Reports Found</option>
                        @else
                            <option value="">Select Report</option>
                            @foreach($configtypes as $configtype)
                                <option value="{{$configtype->id}}">{{$configtype->description}}</option>
                            @endforeach
                        @endif
                    </select>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-default" id="addReport"><i class="fa fa-plus"></i> Create</button>
                    </div>
                  </div>
            </div>
            <div class="card-body" id="configcontainer">
                <div id="buttonscontainer">
                    <button type="button" class="btn btn-default" id="exportsetup"><i class="fa fa-download"></i> Export</button>
                </div>
                <div id="particularscontainer">

                </div>
                <button type="button" class="btn btn-primary mt-2" id="addparticular"><i class="fa fa-plus"></i> Add Header</button>
            </div>
        </div>
    </div>
</div>
<!-- jQuery -->
<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
<!-- Select2 -->
<script src="{{asset('plugins/select2/js/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/toastr/toastr.min.js')}}"></script>
    <script>
        $(document).ready(function(){
            const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 500
            });
            $(document).on('click','#addReport', function(){
                Swal.fire({
                    title: 'New Report',
                    input: 'text',
                    inputAttributes: {
                        id: 'newreport'
                    },
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Create',
                    allowOutsideClick: false,
                    preConfirm: () => {
                        if($("#newreport").val().replace(/^\s+|\s+$/g, "").length == 0 ){
                            Swal.showValidationMessage(
                                "Please fill in the required section!"
                            );
                        }
                    }
                }).then((result) => {
                    if (result.value) {
                            // $('form[name=createbook]').submit();
                            $.ajax({
                                url: '{{ route('createreport')}}',
                                type:"GET",
                                dataType:"json",
                                data:{
                                    newreport      : $('#newreport').val(),
                                },
                                // headers: { 'X-CSRF-TOKEN': token },,
                                success: function(data){
                                    if(data[0] == 0)
                                    {
                                        $('#configselection').append(
                                            '<option value="'+data[1]+'">'+data[2]+'</option>'
                                        )
                                        toastr.success('Created successfully','Report')
                                    }else{
                                        toastr.warning('Report already exists', 'Report')
                                    }
                                    
                                }
                            })
                    }
                })
            })
            $('.select2').select2({
                minimumResultsForSearch: 15,
            });

            $('#configcontainer').hide();
            
            var setupid = 0;
            var headerid = 0;
            var subid = 0;

            var particularid = 0;
            var particular = '';
            var itemid = 0;
            var item = '';
            var detail = '';

            function addparticular(classinfo){
                
                particular  =   '<div id="rowparticular'+particularid+'" class="rowparticular mt-2" style="border-bottom: 1px solid #ddd;" class="p-2">'+
                                    '<div class="row">'+
                                        '<div class="col-md-12">'+
                                        
                                            '<div class="input-group">';
                                            particular+= '<select class="form-control">';
                                            if(classinfo.length > 0)
                                            {
                                                $.each(classinfo, function(key, value){
                                                    particular+= '<option value="'+value.id+'">'+value.classification+'</option>';
                                                })
                                            }
                                                particular+= '</select>';
                                                
                                                particular+= '<div class="input-group-append">'+
                                                    '<button type="button" class="btn btn-sm btn-default m-0 saveparticular"><i class="fa fa-upload text-success"></i></button>'+
                                                    '<button type="button" class="btn btn-sm btn-default m-0 removeparticular"><i class="fa fa-times text-danger"></i></button>'+
                                                '</div>'+
                                            '</div>'+
                                        '</div>'+
                                    '</div>'+
                                '</div>';
                            

            }
            function addsub(groups){
                var subselection = '<select class="form-control form-control-sm">';
                $.each(groups, function(key, value){
                    subselection+= '<option value="'+value.id+'">'+value.group+'</option>'
                })
                subselection+='</select>';
                item =  '<div class="row rowitem mt-2">'+
                            '<div class="col-md-2">&nbsp;</div>'+
                            
                            '<div class="col-md-8">'+
                                '<div class="input-group input-group-sm">'+
                            '<div class="input-group-prepend">'+
                                '<span class="input-group-text form-control form-control-sm">New Subheader</span>'+
                            '</div>'+
                                    subselection+
                                    '<div class="input-group-append">'+
                                        '<button type="button" class="btn btn-default btn-sm savesub"><i class="fa fa-upload text-success"></i></button>'+
                                        '<button type="button" class="btn btn-sm btn-default m-0 removeitem"><i class="fa fa-times text-danger"></i></button>'+
                                    '</div>'+
                                '</div>'+
                            '</div>'+
                        '</div>'+
                        '<div id="detailcontainerheader"></div>';
            }
            function adddetail(maps){
                var mapselection = '<select class="form-control form-control-sm">';
                $.each(maps, function(key, value){
                    mapselection+= '<option value="'+value.id+'">'+value.mapname+'</option>'
                })
                mapselection+='</select>';
                detail =  '<div class="row rowitem mt-2">'+
                            '<div class="col-md-3 text-muted text-center">&nbsp;</div>'+
                            '<div class="col-md-7">'+
                                '<div class="input-group input-group-sm">'+
                                '<div class="input-group-prepend">'+
                                    '<span class="input-group-text form-control form-control-sm">New Detail</span>'+
                                '</div>'+
                                    '<input type="text" class="form-control form-control-sm" placeholder="Desciption"/>'+
                                    '<div class="input-group-append">'+
                                        '<span class="input-group-text form-control form-control-sm">Map</span>'+
                                    '</div>'+
                                    '<div class="input-group-append">'+
                                    mapselection+
                                    '</div>'+
                                    '<div class="input-group-append">'+
                                        '<button type="button" class="btn btn-default btn-sm savedetail"><i class="fa fa-upload text-success"></i></button>'+
                                        '<button type="button" class="btn btn-sm btn-default m-0 removeitem"><i class="fa fa-times text-danger"></i></button>'+
                                    '</div>'+
                                '</div>'+
                            '</div>'+
                        '</div>';
            }
                 

            $(document).on('change','#configselection', function(){

                if($(this).val() == "")
                {
                    $('#configcontainer').hide();
                    $('#particularscontainer').empty();
                }else{
                    setupid = $(this).val()
                    $('#configcontainer').show();
                    $.ajax({
                        url: '{{ route('getheaders')}}',
                        type:"GET",
                        data:{
                            setupid      : setupid
                        },
                        success:function(data)
                        {
                            $('#particularscontainer').empty();
                            $('#particularscontainer').append(data)
                            // console.log(data)
                        }
                    })
                }
                
            })

            $(document).on('click','#addparticular', function(){

                particularid=$('#configcontainer').find('.rowparticular').length + 1;

                $.ajax({
                    url: '{{ route('getaccheaders')}}',
                    type:"GET",
                    dataType:"json",
                    data:{
                        newreport      : $('#newreport').val(),
                    },
                    success:function(data)
                    {
                        addparticular(data)

                        $('#particularscontainer').append(particular)
                    }
                })
                

            })
            $(document).on('click', '.saveparticular', function(){
                var selectedclassid = $(this).closest('.input-group').find('select').val();
                var thiselement = $(this);
                var appendsubs = thiselement.closest('.rowparticular');
                var inputgroupcontainer = thiselement.closest('.input-group');
                $.ajax({
                    url: '{{ route('saveheader')}}',
                    type:"GET",
                    dataType:"json",
                    data:{
                        classid    : selectedclassid,
                        setupid    : setupid
                    },
                    success:function(data)
                    {
                        if(data[0] == 0)
                        {
                            inputgroupcontainer.empty()
                            inputgroupcontainer.append(
                                '<input type="text" class="form-control" disabled value="'+data[1].classification+'" headerid="'+data[1].headerid+'"/>'+
                                '<div class="input-group-append">'+
                                    '<button type="button" class="btn btn-sm btn-default m-0 viewheader"><i class="fa fa-eye text-success"></i></button>'+
                                    '<button type="button" class="btn btn-sm btn-default m-0 deleteheader"><i class="fa fa-times text-danger"></i></button>'+
                                '</div>'
                            )
                            // console.log(thiselement.closest('.rowparticular'))
                            appendsubs.append(
                                '<div id="subcontainerhead'+data[1].headerid+'"></div>'
                            )
                            toastr.success('Saved successfully','New header')
                        }else{
                            toastr.warning('Header already exists', 'Header')
                        }
                    }
                })

            })
            $(document).on('click','.viewheader', function(){
                headerid = $(this).closest('.input-group').find('input').attr('headerid')
                $.ajax({
                    url: '{{ route('getsubs')}}',
                    type:"GET",
                    data:{
                        headerid      : headerid,
                    },
                    success:function(data)
                    {
                        $('#subcontainerhead'+headerid).empty()
                        $('#subcontainerhead'+headerid).append(data)
                        // console.log(data)
                    }
                })
            })
            $(document).on('click','.addsub', function(){
                $.ajax({
                    url: '{{ route('getgroups')}}',
                    type:"GET",
                    dataType: 'json',
                    success:function(data)
                    {
                        addsub(data);
                        $('#subcontainer'+headerid).append(item)     
                    }
                })

            })
            $(document).on('click', '.savesub', function(){
                var groupid = $(this).closest('.input-group').find('select').val();
                var thiselement = $(this);
                var inputgroupcontainer = thiselement.closest('.input-group');
                $.ajax({
                    url: '{{ route('savesub')}}',
                    type:"GET",
                    dataType: 'json',
                    data:{
                        groupid: groupid,
                        headerid:headerid
                    },
                    success:function(data)
                    {

                        if(data[0] == 0)
                        {
                            $('#detailcontainer').attr('id','detailcontainer'+data[1].subid)
                            inputgroupcontainer.empty()
                            inputgroupcontainer.append(
                                
                                '<div class="input-group-prepend">'+
                                    '<span class="input-group-text form-control form-control-sm">Subheader</span>'+
                                '</div>'+
                                '<input type="text" class="form-control" disabled style="background-color: #f1f5bf" value="'+data[1].group+'" subid="'+data[1].subid+'"/>'+
                                '<div class="input-group-append">'+
                                    '<button type="button" class="btn btn-sm btn-default m-0 viewsub"><i class="fa fa-eye text-success"></i></button>'+
                                    '<button type="button" class="btn btn-sm btn-default m-0 deletesub"><i class="fa fa-times text-danger"></i></button>'+
                                '</div>'
                            )
                            // $('#subcontainer').attr('id', 'subcontainer'+data[1].subid)
                           
                            toastr.success('Saved successfully','New Subheader')
                        }else{
                            toastr.warning('Subheader already exists', 'Subheader')
                        }
                    }
                })
            })
            $(document).on('click','.viewsub', function(){
                subid = $(this).closest('.input-group').find('input').attr('subid')
                $.ajax({
                    url: '{{ route('getdetails')}}',
                    type:"GET",
                    data:{
                        subid      : subid,
                    },
                    success:function(data)
                    {
                        $('#detailcontainerheader'+subid).empty()
                        $('#detailcontainerheader'+subid).append(data)
                    }
                })
            })
            $(document).on('click','.adddetail', function(){
                $.ajax({
                    url: '{{ route('getmaps')}}',
                    type:"GET",
                    dataType: 'json',
                    success:function(data)
                    {
                        // console.log(data)
                        adddetail(data);
                        $('#detailscontainer'+subid).append(detail)     
                    }
                })

            })
            $(document).on('click','.savedetail', function(){
                var mapid = $(this).closest('.input-group').find('select').val();
                var description = $(this).closest('.input-group').find('input').val();
                var thiselement = $(this);
                var inputgroupcontainer = thiselement.closest('.input-group');
                if(description.replace(/^\s+|\s+$/g, "").length == 0)
                {
                    toastr.warning('Required', 'Detail description')
                }else{
                    $.ajax({
                        url: '{{ route('savedetail')}}',
                        type:"GET",
                        dataType: 'json',
                        data:{
                            description: description,
                            mapid: mapid,
                            subid:subid
                        },
                        success:function(data)
                        {
                            console.log(data)
                            if(data[0] == 0)
                            {
                                inputgroupcontainer.empty()
                                inputgroupcontainer.append(
                                    '<div class="input-group-prepend">'+
                                        '<span class="input-group-text form-control form-control-sm">Detail</span>'+
                                    '</div>'+
                                    '<input type="text" class="form-control form-control-sm" disabled value="'+data[1].description+'" detailid="'+data[1].detailid+'"/>'+
                                    '<div class="input-group-append">'+
                                        '<span class="input-group-text form-control form-control-sm">Map</span>'+
                                    '</div>'+
                                    '<div class="input-group-append">'+
                                    '<input type="text" class="form-control form-control-sm" disabled value="'+data[1].mapname+'" detailid="'+data[1].detailid+'"/>'+
                                    '</div>'+
                                    '<div class="input-group-append">'+
                                        '<button type="button" class="btn btn-sm btn-default m-0 editdetail"><i class="fa fa-edit text-warning"></i></button>'+
                                        '<button type="button" class="btn btn-sm btn-default m-0 deletesub"><i class="fa fa-times text-danger"></i></button>'+
                                    '</div>'
                                )
                            
                                toastr.success('Saved successfully','Mapping')
                            }else{
                                toastr.warning('Detail already exists', 'Mapping')
                            }
                        }
                    })
                }
            })
            $(document).on('click','.editdetail', function(){
                var updatedetailid = $(this).closest('.input-group').find('input').attr('detailid');
                var thiscontainer = $(this).closest('.input-group');
                $.ajax({
                    url: '{{ route('getdetailinfo')}}',
                    type:"GET",
                    data: {
                        detailid: updatedetailid
                    },
                    success:function(data)
                    {
                        thiscontainer.empty();
                        thiscontainer.append(data)
                        // console.log(data)
                    }
                })
            })
            $(document).on('click','.updatedetail', function(){
                var updatedetailid = $(this).closest('.input-group').find('input').attr('detailid');
                var thiscontainer = $(this).closest('.input-group');
                var newdescription = $(this).closest('.input-group').find('input').val();
                var newmapid = $(this).closest('.input-group').find('select').val();
                $.ajax({
                    url: '{{ route('updatedetail')}}',
                    type:"GET",
                    data: {
                        detailid    : updatedetailid,
                        description : newdescription,
                        mapid       : newmapid
                    },
                    success:function(data)
                    {
                        if(data[0] == 0 || data[0] == 2)
                        {
                            thiscontainer.empty();
                            thiscontainer.append(
                                '<div class="input-group-prepend">'+
                                    '<span class="input-group-text form-control form-control-sm">Detail</span>'+
                                '</div>'+
                                '<input type="text" value="'+data[1].description+'" class="form-control form-control-sm" detailid="'+data[1].detailid+'" disabled/>'+
                                '<div class="input-group-append">'+
                                    '<span class="input-group-text form-control form-control-sm">Map</span>'+
                                '</div>'+
                                '<div class="input-group-append">'+
                                    '<input type="text" value="'+data[1].mapname+'" class="form-control form-control-sm" detailid="'+data[1].detailid+'" disabled/>'+
                                '</div>'+
                                '<div class="input-group-append">'+
                                    '<button type="button" class="btn btn-sm btn-default m-0 editdetail"><i class="fa fa-edit text-warning"></i></button>'+
                                    '<button type="button" class="btn btn-sm btn-default m-0 deletedetail"><i class="fa fa-times text-danger"></i></button>'+
                                '</div>'
                            )
                            if(data[0] == 0)
                            {
                                toastr.success('Updated successfully','Mapping')
                            }else{
                                toastr.info('No changes saved','Mapping')
                            }
                        }else{
                            toastr.warning('Already exists!','Mapping')
                        }
                        // console.log(data)
                    }
                })
            })
            $(document).on('click','.removeitem', function(){

                Swal.fire({
                    title: 'Are you sure you want to remove this item?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Remove',
                }).then((result) => {
                    if (result.value) {
                        $(this).closest('.rowitem').remove();
                    }
                })

            })
            $(document).on('click','.removeparticular', function(){

                Swal.fire({
                    title: 'Are you sure you want to remove this particular?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Remove',
                }).then((result) => {
                    if (result.value) {
                        $(this).closest('.rowparticular').remove();
                    }
                })
                
            })
            $(document).on('click', '.deleteheader', function(){
                var deleteheaderid = $(this).closest('.input-group').find('input').attr('headerid');
                var thisrow = $(this).closest('.rowparticular');
                Swal.fire({
                    title: 'Are you sure you want to delete this header?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Delete',
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: '{{ route('deleteheader')}}',
                            type:"GET",
                            data: {
                                headerid: deleteheaderid
                            },
                            dataType: 'json',
                            complete:function()
                            {
                                toastr.success('Deleted successfully!','Header')
                                thisrow.remove();
                            }
                        })
                    }
                })
            })
            $(document).on('click', '.deletesub', function(){
                var deletesubid = $(this).closest('.input-group').find('input').attr('subid');
                var thisrow = $(this).closest('.row');
                Swal.fire({
                    title: 'Are you sure you want to delete this subheader?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Delete',
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: '{{ route('deletesub')}}',
                            type:"GET",
                            data: {
                                subid: deletesubid
                            },
                            dataType: 'json',
                            complete:function()
                            {
                                toastr.success('Deleted successfully!','Subheader')
                                $('#detailcontainer'+deletesubid).remove()
                                thisrow.remove();
                            }
                        })
                    }
                })
            })
            $(document).on('click', '.deletedetail', function(){
                var deletedetailid = $(this).closest('.input-group').find('input').attr('detailid');
                var thisrow = $(this).closest('.row');
                Swal.fire({
                    title: 'Are you sure you want to delete this mapping detail?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Delete',
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: '{{ route('deletedetail')}}',
                            type:"GET",
                            data: {
                                detailid: deletedetailid
                            },
                            dataType: 'json',
                            complete:function()
                            {
                                toastr.success('Deleted successfully!','Subheader')
                                thisrow.remove();
                            }
                        })
                    }
                })
            })

            $(document).on('click','#exportsetup', function(){
                var setupid = $('#configselection').val();
				window.open("{{ route('setupexport')}}?setupid="+setupid);
            })
            
        });
    </script>
  @endsection