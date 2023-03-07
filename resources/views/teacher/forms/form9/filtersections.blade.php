
        @if(count($sections) == 0)
        <div class="col-md-12">
            <div class="alert alert-danger" role="alert">
                No sections assigned!
            </div>
        </div>
    @else
    <div class="row">
        @foreach($sections as $section)
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-success">
                    <div class="inner">
                        <h4>{{preg_replace('/[^0-9]/', '', $section->levelname)}} - {{$section->sectionname}}</h4>
                        <span style="font-size: 15px"><strong>{{$section->numberofstudents}}</strong></span>  Students
                        @if($section->semester == 1) - 1st Semester @elseif($section->semester == 2)- 2nd Semester @endif</p>
                        <div class="row">
                            <div class="col-6">
                                <small>Enrolled: {{$section->numberofenrolled}}</small><br/>
                                <small>Late Enrolled: {{$section->numberoflateenrolled}}</small><br/>
                                <small>Transferred In: {{$section->numberoftransferredin}}</small><br/>
                            </div>
                            <div class="col-6">
                                <small>Transferred Out: {{$section->numberoftransferredout}}</small><br/>
                                <small>Dropped Out: {{$section->numberofdroppedout}}</small><br/>
                                <small>Withdrawn: {{$section->numberofwithdraw}}</small>
                            </div>
                        </div>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <form action="/forms/{{$formtype}}" method="get" class="small-box-footer">
                        <input type="hidden" name="action" value="show"/>
                        <input type="hidden" name="sectionid" value="{{$section->sectionid}}"/>
                        <input type="hidden" name="levelid" value="{{$section->levelid}}"/>
                        <input type="hidden" name="semid" value="{{$section->semester}}"/>
                        <input type="hidden" name="syid" value="{{$syid}}"/>
                        <input type="hidden" name="currentmonth" value="{{\Carbon\Carbon::now()->month}}"/>
                        <button type="submit" class=" btn btn-sm btn-block">More info <i class="fas fa-arrow-circle-right"></i></button>
                    </form>
                </div>
                
            </div>
        @endforeach
    </div>
    <script>
        $('[data-id="exportpdf"]').on('click', function(){
            $(this).closest('form').find('input[name="exporttype"]').val('pdf')
            $(this).closest('form').submit();
        })
        $('[data-id="exportexcel"]').on('click', function(){
            $(this).closest('form').find('input[name="exporttype"]').val('excel')
            $(this).closest('form').submit();
        })
    </script>
@endif