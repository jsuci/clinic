
<div class="card shadow" style="border: none !important; box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;">
    <div class="card-header">
        <div class="row">
            <div class="col-md-12 text-right">
                <button type="button" class="btn btn-outline-info" id="btn-exporttopdf">Export to PDF</button>
            </div>
        </div>
    </div>
    <div class="card-body pt-0">
        <div class="row">
            <div class="col-md-12 p-0">
                <table class="table table-hover m-0" style="font-size: 13px;">
                    <thead class="text-center">
                        <tr>
                            <th rowspan="2" style="width: 20%;">Date</th>
                            <th colspan="2">AM</th>
                            <th colspan="2">PM</th>
                            <th rowspan="2" style="width: 20%;">Remarks</th>
                            <th rowspan="2" style="width: 15%;"></th>
                        </tr>
                        <tr>
                            <th>IN</th>
                            <th>OUT</th>
                            <th>IN</th>
                            <th>OUT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendance as $eachdayatt)
                            <tr>
                                <th style="vertical-align: middle;">{{date('m/d/Y - l', strtotime($eachdayatt->date))}}</th>
                                <td class="text-center" style="vertical-align: middle; font-size: 17px;">
                                    
                                    {{$eachdayatt->timeinam != null ? date('h:i', strtotime($eachdayatt->timeinam)) : ''}}
                                
                                </td>
                                <td class="text-center" style="vertical-align: middle; font-size: 17px;">
                                    
                                    {{$eachdayatt->timeoutam != null ? date('h:i', strtotime($eachdayatt->timeoutam)) : ''}}
                                
                                </td>
                                <td class="text-center" style="vertical-align: middle; font-size: 17px;">
                                    
                                    {{$eachdayatt->timeinpm != null ? date('h:i', strtotime($eachdayatt->timeinpm)) : ''}}
                                
                                </td>
                                <td class="text-center" style="vertical-align: middle; font-size: 17px;">                                    
                                    {{$eachdayatt->timeoutpm != null ? date('h:i', strtotime($eachdayatt->timeoutpm)) : ''}}                                
                                </td>
                                <td class="text-center">
                                    <textarea class="form-control">{{$eachdayatt->remarks}}</textarea>
                                </td>
                                <td style="vertical-align: middle;"><button class="btn btn-secondary btn-sm btn-submitremarks" data-date="{{$eachdayatt->date}}"><i class="fa fa-share"></i> Submit Remarks</button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $('#date-string').text("{{date('F d, Y', strtotime($datefrom))}} - {{date('F d, Y', strtotime($dateto))}}")
</script>