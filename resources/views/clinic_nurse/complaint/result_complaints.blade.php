
@if(count($complaints)>0)
    @foreach($complaints as $complaint)
        <div class="col-md-4">
            <!-- Widget: user widget style 1 -->
            <div class="card card-widget widget-user shadow-lg">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="widget-user-header text-white bg-warning"
                style="
                /* background: url({{asset('dist/img/photo1.png')}}) center center; */
                ">
                {{-- <h3 class="widget-user-username text-right">{{$complaint->name_showlast}}</h3>
                <h5 class="widget-user-desc text-right">{{$complaint->utype}}</h5> --}}
                <h5 class="text-center">{{$complaint->name_showlast}}</h5>
                <small class="widget-user-desc text-center">{{$complaint->utype}}</small>
            </div>
            <div class="widget-user-image">
                @php
                    $number = rand(1,3);
                        if(strtolower($complaint->gender) == 'female'){
                            $avatar = 'avatar/T(F) '.$number.'.png';
                        }
                        elseif(strtolower($complaint->gender) == 'male'){
                            $avatar = 'avatar/T(M) '.$number.'.png';
                        }else{
                            $avatar = 'assets/images/avatars/unknown.png';
                        }
                @endphp
                <img class="img-circle" src="{{asset($complaint->picurl)}}" onerror="this.onerror = null, this.src='{{asset($avatar)}}'" alt="User Avatar">
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-12 text-right">
                        @if($complaint->complaintmed == 0)
                        <button type="button" class="btn btn-sm btn-default btn-complaint-addmedication" data-id="{{$complaint->id}}"><i class="fa fa-capsules"></i></button>
                        @else
                        <button type="button" class="btn btn-sm btn-info btn-complaint-editmedication" data-id="{{$complaint->id}}"><i class="fa fa-capsules"></i></button>
                        @endif
                        <button type="button" class="btn btn-sm btn-default btn-complaint-edit" data-id="{{$complaint->id}}"><i class="fa fa-edit"></i></button>
                        <button type="button" class="btn btn-sm btn-default btn-complaint-delete" data-id="{{$complaint->id}}"><i class="fa fa-trash"></i></button>
                    </div>
                    <div class="col-md-12">
                        <div class="description-block text-left text-muted">
                            <small><strong>Date :</strong> <u>{{date('M d, Y', strtotime($complaint->cdate))}} &nbsp;{{date('h:i A', strtotime($complaint->ctime))}}</u></small>
                            <br/>
                            <small ><strong>Complaint :</strong> {{$complaint->description}}</small>
                            <br/>
                            <small><strong>Action Taken :</strong></small>
                            <small>{{$complaint->actiontaken}}</small>
                            <br/>
                            <small><strong>Medication </strong></small>
                            <br/>
                            <small><strong>Brand Name :</strong></small>
                            <small>{{$complaint->brandname}}</small>
                            <br/>
                            <small><strong>Generic Name :</strong></small>
                            <small>{{$complaint->genericname}}</small>
                            <br/>
                            <small><strong>Quantity :</strong></small>
                            <small>{{$complaint->quantity}}</small>
                        </div>
                    </div>
                    {{-- <div class="col-sm-4 border-right">
                        <div class="description-block">
                        <h5 class="description-header">3,200</h5>
                        <span class="description-text">SALES</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-4 border-right">
                        <div class="description-block">
                        <h5 class="description-header">13,000</h5>
                        <span class="description-text">FOLLOWERS</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-4">
                        <div class="description-block">
                        <h5 class="description-header">35</h5>
                        <span class="description-text">PRODUCTS</span>
                        </div>
                        <!-- /.description-block -->
                    </div> --}}
                <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            </div>
            <!-- /.widget-user -->
        </div>
    @endforeach
@else
    <div class="col-md-12">
        <div class="alert alert-warning alert-dismissible">
            <h5><i class="icon fas fa-exclamation-triangle"></i> Alert!</h5>
            No complaints!
        </div>
    </div>
@endif
    