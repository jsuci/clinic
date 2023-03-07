<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-12 text-right">
                <button type="button" class="btn btn-primary" id="btn-export"><i class="fa fa-download"></i> Export to PDF</button>
            </div>
        </div>
    </div>
    <div class="card-body p-0" style="overflow: scroll;">
        @if($acadprogid == 5)        
            <table class="table table-bordered" style="font-size: 12px;">
                <thead class="text-center">
                    <tr>
                        <th rowspan="2" style="vertical-align: middle;">Summary Table</th>
                        @foreach($gradelevels as $eachgradelevel)
                        <th colspan="3">{{$eachgradelevel->levelname}}<br/>({{$eachgradelevel->strandcode}})</th>
                        @endforeach
                        <th colspan="3" style="vertical-align: middle;">Total</th>
                    </tr>
                    <tr>
                        @foreach($gradelevels as $eachgradelevel)
                        <th>MALE</th>
                        <th>FEMALE</th>
                        <th>TOTAL</th>
                        @endforeach
                        <th>MALE</th>
                        <th>FEMALE</th>
                        <th>TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>PROMOTED</th>
                        @foreach($gradelevels as $eachgradelevel)
                        <td class="text-center">{{$eachgradelevel->promotedmale}}</td>
                        <td class="text-center">{{$eachgradelevel->promotedfemale}}</td>
                        <td class="text-center">{{$eachgradelevel->promoted}}</th>
                        @endforeach
                        <th class="text-center">{{collect($gradelevels)->sum('promotedmale')}}</th>
                        <th class="text-center">{{collect($gradelevels)->sum('promotedfemale')}}</th>
                        <th class="text-center">{{collect($gradelevels)->sum('promoted')}}</th>
                    </tr>
                    @if($acadprogid != 3)
                    <tr>
                        <th>IRREGULAR (Grade 7 onwards only)	</th>
                        @foreach($gradelevels as $eachgradelevel)
                        <td class="text-center">{{$eachgradelevel->irregularmale}}</td>
                        <td class="text-center">{{$eachgradelevel->irregularfemale}}</td>
                        <td class="text-center">{{$eachgradelevel->irregular}}</th>
                        @endforeach
                        <th class="text-center">{{collect($gradelevels)->sum('irregularmale')}}</th>
                        <th class="text-center">{{collect($gradelevels)->sum('irregularfemale')}}</th>
                        <th class="text-center">{{collect($gradelevels)->sum('irregular')}}</th>
                    </tr>
                    @endif
                    <tr>
                        <th>RETAINED</th>
                        @foreach($gradelevels as $eachgradelevel)
                        <td class="text-center">{{$eachgradelevel->retainedmale}}</td>
                        <td class="text-center">{{$eachgradelevel->retainedfemale}}</td>
                        <td class="text-center">{{$eachgradelevel->retained}}</th>
                        @endforeach
                        <th class="text-center">{{collect($gradelevels)->sum('retainedmale')}}</th>
                        <th class="text-center">{{collect($gradelevels)->sum('retainedfemale')}}</th>
                        <th class="text-center">{{collect($gradelevels)->sum('retained')}}</th>
                    </tr>
                    @if($acadprogid == 5)
                    <tr>
                        <th>LEVEL OF POFICIENCY (K to 12 Only)	</th>
                        @foreach($gradelevels as $eachgradelevel)
                        <th class="text-center" style="vertical-align: middle;">MALE</th>
                        <th class="text-center" style="vertical-align: middle;">FEMALE</th>
                        <th class="text-center" style="vertical-align: middle;">TOTAL</th>
                        @endforeach
                        <th class="text-center" style="vertical-align: middle;">MALE</th>
                        <th class="text-center" style="vertical-align: middle;">FEMALE</th>
                        <th class="text-center" style="vertical-align: middle;">TOTAL</th>
                    </tr>
                    @endif
                    <tr>
                        <th>BEGINNING (B: 74% and below)	</th>
                        @foreach($gradelevels as $eachgradelevel)
                        <td class="text-center">{{$eachgradelevel->proficiencybmale}}</td>
                        <td class="text-center">{{$eachgradelevel->proficiencybfemale}}</td>
                        <td class="text-center">{{$eachgradelevel->proficiencyb}}</th>
                        @endforeach
                        <th class="text-center">{{collect($gradelevels)->sum('proficiencybmale')}}</th>
                        <th class="text-center">{{collect($gradelevels)->sum('proficiencybfemale')}}</th>
                        <th class="text-center">{{collect($gradelevels)->sum('proficiencyb')}}</th>
                    </tr>
                    <tr>
                        <th>DEVELOPING (D: 75%-79%)	</th>
                        @foreach($gradelevels as $eachgradelevel)
                        <td class="text-center">{{$eachgradelevel->proficiencydmale}}</td>
                        <td class="text-center">{{$eachgradelevel->proficiencydfemale}}</td>
                        <td class="text-center">{{$eachgradelevel->proficiencyd}}</th>
                        @endforeach
                        <th class="text-center">{{collect($gradelevels)->sum('proficiencydmale')}}</th>
                        <th class="text-center">{{collect($gradelevels)->sum('proficiencydfemale')}}</th>
                        <th class="text-center">{{collect($gradelevels)->sum('proficiencyd')}}</th>
                    </tr>
                    <tr>
                        <th>APPROACHING PROFICIENCY (AP: 80%-84%)	</th>
                        @foreach($gradelevels as $eachgradelevel)
                        <td class="text-center">{{$eachgradelevel->proficiencyapmale}}</td>
                        <td class="text-center">{{$eachgradelevel->proficiencyapfemale}}</td>
                        <td class="text-center">{{$eachgradelevel->proficiencyap}}</th>
                        @endforeach
                        <th class="text-center">{{collect($gradelevels)->sum('proficiencyapmale')}}</th>
                        <th class="text-center">{{collect($gradelevels)->sum('proficiencyapfemale')}}</th>
                        <th class="text-center">{{collect($gradelevels)->sum('proficiencyap')}}</th>
                    </tr>
                    <tr>
                        <th>PROFICIENT (P: 85%-89%)	</th>
                        @foreach($gradelevels as $eachgradelevel)
                        <td class="text-center">{{$eachgradelevel->proficiencypmale}}</td>
                        <td class="text-center">{{$eachgradelevel->proficiencypfemale}}</td>
                        <td class="text-center">{{$eachgradelevel->proficiencyp}}</th>
                        @endforeach
                        <th class="text-center">{{collect($gradelevels)->sum('proficiencypmale')}}</th>
                        <th class="text-center">{{collect($gradelevels)->sum('proficiencypfemale')}}</th>
                        <th class="text-center">{{collect($gradelevels)->sum('proficiencyp')}}</th>
                    </tr>
                    <tr>
                        <th>ADVANCED (A: 90% and above)	</th>
                        @foreach($gradelevels as $eachgradelevel)
                        <td class="text-center">{{$eachgradelevel->proficiencyamale}}</td>
                        <td class="text-center">{{$eachgradelevel->proficiencyafemale}}</td>
                        <td class="text-center">{{$eachgradelevel->proficiencya}}</th>
                        @endforeach
                        <th class="text-center">{{collect($gradelevels)->sum('proficiencyamale')}}</th>
                        <th class="text-center">{{collect($gradelevels)->sum('proficiencyafemale')}}</th>
                        <th class="text-center">{{collect($gradelevels)->sum('proficiencya')}}</th>
                    </tr>
                    <tr>
                        <th>TOTAL	</th>
                        @foreach($gradelevels as $eachgradelevel)
                        <th class="text-center">{{$eachgradelevel->promotedmale+$eachgradelevel->irregularmale+$eachgradelevel->retainedmale+$eachgradelevel->proficiencybmale+$eachgradelevel->proficiencydmale+$eachgradelevel->proficiencyapmale+$eachgradelevel->proficiencypmale+$eachgradelevel->proficiencyamale}}
                        </th>
                        <th class="text-center">{{$eachgradelevel->promotedfemale+$eachgradelevel->irregularfemale+$eachgradelevel->retainedfemale+$eachgradelevel->proficiencybfemale+$eachgradelevel->proficiencydfemale+$eachgradelevel->proficiencyapfemale+$eachgradelevel->proficiencypfemale+$eachgradelevel->proficiencyafemale}}</th>
                        <th class="text-center">{{$eachgradelevel->promoted+$eachgradelevel->irregular+$eachgradelevel->retained+$eachgradelevel->proficiencyb+$eachgradelevel->proficiencyd+$eachgradelevel->proficiencyap+$eachgradelevel->proficiencyp+$eachgradelevel->proficiencya}}</th>
                        @endforeach
                        <th class="text-center">{{collect($gradelevels)->sum('promotedmale')+collect($gradelevels)->sum('irregularmale')+collect($gradelevels)->sum('retainedmale')+collect($gradelevels)->sum('proficiencybmale')+collect($gradelevels)->sum('proficiencydmale')+collect($gradelevels)->sum('proficiencyapmale')+collect($gradelevels)->sum('proficiencypmale')+collect($gradelevels)->sum('proficiencyamale')}}</th>
                        <th class="text-center">{{collect($gradelevels)->sum('promotedfemale')+collect($gradelevels)->sum('irregularfemale')+collect($gradelevels)->sum('retainedfemale')+collect($gradelevels)->sum('proficiencybfemale')+collect($gradelevels)->sum('proficiencydfemale')+collect($gradelevels)->sum('proficiencyapfemale')+collect($gradelevels)->sum('proficiencypfemale')+collect($gradelevels)->sum('proficiencyafemale')}}</th>
                        <th class="text-center">{{collect($gradelevels)->sum('promoted')+collect($gradelevels)->sum('irregular')+collect($gradelevels)->sum('retained')+collect($gradelevels)->sum('proficiencyb')+collect($gradelevels)->sum('proficiencyd')+collect($gradelevels)->sum('proficiencyap')+collect($gradelevels)->sum('proficiencyp')+collect($gradelevels)->sum('proficiencya')}}</th>
                    </tr>
                </tbody>
            </table>
        @else
            <table class="table table-bordered" style="font-size: 12px;">
                <thead class="text-center">
                    <tr>
                        <th rowspan="2">Summary Table</th>
                        @foreach($gradelevels as $eachgradelevel)
                        <th colspan="3">{{$eachgradelevel->levelname}}</th>
                        @endforeach
                        <th colspan="3">Total</th>
                    </tr>
                    <tr>
                        @foreach($gradelevels as $eachgradelevel)
                        <th>MALE</th>
                        <th>FEMALE</th>
                        <th>TOTAL</th>
                        @endforeach
                        <th>MALE</th>
                        <th>FEMALE</th>
                        <th>TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>PROMOTED</th>
                        @foreach($gradelevels as $eachgradelevel)
                        <td class="text-center">{{$eachgradelevel->promotedmale}}</td>
                        <td class="text-center">{{$eachgradelevel->promotedfemale}}</td>
                        <td class="text-center">{{$eachgradelevel->promoted}}</th>
                        @endforeach
                        <th class="text-center">{{collect($gradelevels)->sum('promotedmale')}}</th>
                        <th class="text-center">{{collect($gradelevels)->sum('promotedfemale')}}</th>
                        <th class="text-center">{{collect($gradelevels)->sum('promoted')}}</th>
                    </tr>
                    @if($acadprogid != 3)
                    <tr>
                        <th>IRREGULAR (Grade 7 onwards only)	</th>
                        @foreach($gradelevels as $eachgradelevel)
                        <td class="text-center">{{$eachgradelevel->irregularmale}}</td>
                        <td class="text-center">{{$eachgradelevel->irregularfemale}}</td>
                        <td class="text-center">{{$eachgradelevel->irregular}}</th>
                        @endforeach
                        <th class="text-center">{{collect($gradelevels)->sum('irregularmale')}}</th>
                        <th class="text-center">{{collect($gradelevels)->sum('irregularfemale')}}</th>
                        <th class="text-center">{{collect($gradelevels)->sum('irregular')}}</th>
                    </tr>
                    @endif
                    <tr>
                        <th>RETAINED</th>
                        @foreach($gradelevels as $eachgradelevel)
                        <td class="text-center">{{$eachgradelevel->retainedmale}}</td>
                        <td class="text-center">{{$eachgradelevel->retainedfemale}}</td>
                        <td class="text-center">{{$eachgradelevel->retained}}</th>
                        @endforeach
                        <th class="text-center">{{collect($gradelevels)->sum('retainedmale')}}</th>
                        <th class="text-center">{{collect($gradelevels)->sum('retainedfemale')}}</th>
                        <th class="text-center">{{collect($gradelevels)->sum('retained')}}</th>
                    </tr>
                    @if($acadprogid == 5)
                    <tr>
                        <th>LEVEL OF POFICIENCY (K to 12 Only)	</th>
                        @foreach($gradelevels as $eachgradelevel)
                        <th class="text-center">MALE</th>
                        <th class="text-center">FEMALE</th>
                        <th class="text-center">TOTAL</th>
                        @endforeach
                        <th class="text-center">MALE</th>
                        <th class="text-center">FEMALE</th>
                        <th class="text-center">TOTAL</th>
                    </tr>
                    @endif
                    <tr>
                        <th>BEGINNING (B: 74% and below)	</th>
                        @foreach($gradelevels as $eachgradelevel)
                        <td class="text-center">{{$eachgradelevel->proficiencybmale}}</td>
                        <td class="text-center">{{$eachgradelevel->proficiencybfemale}}</td>
                        <td class="text-center">{{$eachgradelevel->proficiencyb}}</th>
                        @endforeach
                        <th class="text-center">{{collect($gradelevels)->sum('proficiencybmale')}}</th>
                        <th class="text-center">{{collect($gradelevels)->sum('proficiencybfemale')}}</th>
                        <th class="text-center">{{collect($gradelevels)->sum('proficiencyb')}}</th>
                    </tr>
                    <tr>
                        <th>DEVELOPING (D: 75%-79%)	</th>
                        @foreach($gradelevels as $eachgradelevel)
                        <td class="text-center">{{$eachgradelevel->proficiencydmale}}</td>
                        <td class="text-center">{{$eachgradelevel->proficiencydfemale}}</td>
                        <td class="text-center">{{$eachgradelevel->proficiencyd}}</th>
                        @endforeach
                        <th class="text-center">{{collect($gradelevels)->sum('proficiencydmale')}}</th>
                        <th class="text-center">{{collect($gradelevels)->sum('proficiencydfemale')}}</th>
                        <th class="text-center">{{collect($gradelevels)->sum('proficiencyd')}}</th>
                    </tr>
                    <tr>
                        <th>APPROACHING PROFICIENCY (AP: 80%-84%)	</th>
                        @foreach($gradelevels as $eachgradelevel)
                        <td class="text-center">{{$eachgradelevel->proficiencyapmale}}</td>
                        <td class="text-center">{{$eachgradelevel->proficiencyapfemale}}</td>
                        <td class="text-center">{{$eachgradelevel->proficiencyap}}</th>
                        @endforeach
                        <th class="text-center">{{collect($gradelevels)->sum('proficiencyapmale')}}</th>
                        <th class="text-center">{{collect($gradelevels)->sum('proficiencyapfemale')}}</th>
                        <th class="text-center">{{collect($gradelevels)->sum('proficiencyap')}}</th>
                    </tr>
                    <tr>
                        <th>PROFICIENT (P: 85%-89%)	</th>
                        @foreach($gradelevels as $eachgradelevel)
                        <td class="text-center">{{$eachgradelevel->proficiencypmale}}</td>
                        <td class="text-center">{{$eachgradelevel->proficiencypfemale}}</td>
                        <td class="text-center">{{$eachgradelevel->proficiencyp}}</th>
                        @endforeach
                        <th class="text-center">{{collect($gradelevels)->sum('proficiencypmale')}}</th>
                        <th class="text-center">{{collect($gradelevels)->sum('proficiencypfemale')}}</th>
                        <th class="text-center">{{collect($gradelevels)->sum('proficiencyp')}}</th>
                    </tr>
                    <tr>
                        <th>ADVANCED (A: 90% and above)	</th>
                        @foreach($gradelevels as $eachgradelevel)
                        <td class="text-center">{{$eachgradelevel->proficiencyamale}}</td>
                        <td class="text-center">{{$eachgradelevel->proficiencyafemale}}</td>
                        <td class="text-center">{{$eachgradelevel->proficiencya}}</th>
                        @endforeach
                        <th class="text-center">{{collect($gradelevels)->sum('proficiencyamale')}}</th>
                        <th class="text-center">{{collect($gradelevels)->sum('proficiencyafemale')}}</th>
                        <th class="text-center">{{collect($gradelevels)->sum('proficiencya')}}</th>
                    </tr>
                    <tr>
                        <th>TOTAL	</th>
                        @foreach($gradelevels as $eachgradelevel)
                        <th class="text-center">{{$eachgradelevel->promotedmale+$eachgradelevel->irregularmale+$eachgradelevel->retainedmale+$eachgradelevel->proficiencybmale+$eachgradelevel->proficiencydmale+$eachgradelevel->proficiencyapmale+$eachgradelevel->proficiencypmale+$eachgradelevel->proficiencyamale}}
                        </th>
                        <th class="text-center">{{$eachgradelevel->promotedfemale+$eachgradelevel->irregularfemale+$eachgradelevel->retainedfemale+$eachgradelevel->proficiencybfemale+$eachgradelevel->proficiencydfemale+$eachgradelevel->proficiencyapfemale+$eachgradelevel->proficiencypfemale+$eachgradelevel->proficiencyafemale}}</th>
                        <th class="text-center">{{$eachgradelevel->promoted+$eachgradelevel->irregular+$eachgradelevel->retained+$eachgradelevel->proficiencyb+$eachgradelevel->proficiencyd+$eachgradelevel->proficiencyap+$eachgradelevel->proficiencyp+$eachgradelevel->proficiencya}}</th>
                        @endforeach
                        <th class="text-center">{{collect($gradelevels)->sum('promotedmale')+collect($gradelevels)->sum('irregularmale')+collect($gradelevels)->sum('retainedmale')+collect($gradelevels)->sum('proficiencybmale')+collect($gradelevels)->sum('proficiencydmale')+collect($gradelevels)->sum('proficiencyapmale')+collect($gradelevels)->sum('proficiencypmale')+collect($gradelevels)->sum('proficiencyamale')}}</th>
                        <th class="text-center">{{collect($gradelevels)->sum('promotedfemale')+collect($gradelevels)->sum('irregularfemale')+collect($gradelevels)->sum('retainedfemale')+collect($gradelevels)->sum('proficiencybfemale')+collect($gradelevels)->sum('proficiencydfemale')+collect($gradelevels)->sum('proficiencyapfemale')+collect($gradelevels)->sum('proficiencypfemale')+collect($gradelevels)->sum('proficiencyafemale')}}</th>
                        <th class="text-center">{{collect($gradelevels)->sum('promoted')+collect($gradelevels)->sum('irregular')+collect($gradelevels)->sum('retained')+collect($gradelevels)->sum('proficiencyb')+collect($gradelevels)->sum('proficiencyd')+collect($gradelevels)->sum('proficiencyap')+collect($gradelevels)->sum('proficiencyp')+collect($gradelevels)->sum('proficiencya')}}</th>
                    </tr>
                </tbody>
            </table>
        @endif
    </div>
</div>