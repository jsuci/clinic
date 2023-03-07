



<script>
    $(document).ready(function(){

        $('.select2').select2()

        var strand = @json($subj_strand)

        if($(window).width()<500){
            $('.search').addClass('w-100 mt-2')
            $('.acadid').addClass('w-100')
            $('.col-md-2, .col-md-8').addClass('mb-3')
            $('.search').removeClass('w-25')
        }

        var selectedSection = null
        var selectedQuarter = null
        var selectedGradeLevel = null
        var selectedRankingType = null
        var selectedsy = null
        var selectedsem = null

        var students = [];
        var array_length
        var start = 0;
        var end = 0
        var proccess = 0;

        $(document).on('click','#view_detail',function(){

            $('#grade_info').modal()
            var studid = $(this).attr('data-id')
            var studentInfo = students.filter(x => x.id == studid)

            $('#below_holder').empty();

            $.each(studentInfo[0].ranking[0].belowgrades,function(a,b){

                    $('#below_holder').append('<tr><td>'+b.subject+'</td><td class="text-center">'+b.grade+'</td></tr>')
                
            })

            

        })

        $(document).on('click','#view_ranking',function(){

            var valid = true
            selectedQuarter = $('#quarter').val()

            if(selectedGradeLevel == null || selectedQuarter == ''){

                valid = false
                Swal.fire({
                    type: 'info',
                    title: 'Please select grade level!',
                });

            }

            if(selectedQuarter == null || selectedQuarter ==  ''){

                valid = false
                Swal.fire({
                    type: 'info',
                    title: 'Please select quarter!',
                });

            }

            if(valid){

                array_length = students.length
                start = 0
                end = 10
                proccess = 0

                $('#proccess_count_modal').modal();
                $('#proccess_done').attr('hidden','hidden')
                $('#proccess_count_modal .modal-title').text('Proccessing')
                $('#proccess_count').empty()

            
                gen_ranking()

            }
          
        })


        function gen_ranking(){

            var students_sliced = students.slice(start,end);
            var limit = 0;
            var initailaverage = $('#initial_grade').val();

            $.each(students_sliced,function(a,b){

                $.ajax({
                    type:'GET',
                    url:'/searchStudentWithHonors',
                    data:{
                        ranking:'ranking',
                        studid:b.id,
                        requiredave:initailaverage
                    },
                    success:function(data) {

                        b.ranking = data
                        limit += 1;

                        if(array_length == proccess + 1){

                            loaddatatable(students)

                            proccess += 1
                            $('#proccess_count').text( proccess +' / ' + array_length)
                            $('#proccess_count_modal .modal-title').text('Complete')
                            
                            $('#proccess_done').removeAttr('hidden')

                        }
                        else{

                            proccess += 1
                            $('#proccess_count').text(  proccess +' / ' + array_length)

                        }
                     
                        if(limit == 10){
                            start += 10
                            end += 10
                            gen_ranking()

                        }
                       
                    }
                })

            })

        }

		
		var utype = @json(auth()->user()->type);

        $(document).on('change','#gradelevel',function(){

            $('#section').empty()

            if($(this).val() == ""){
                return false;
            }

            var sections = @json($allsections)

            var temp_syid = $('#syid').val()
            var temp_gradelevel = $('#gradelevel').val()
			
			if(utype == 2 || utype == 3){
				var temp_sections = sections.filter(x=>x.levelid == temp_gradelevel)
			}else{
				var temp_sections = sections.filter(x=>x.syid == temp_syid && x.levelid == temp_gradelevel)
			}
            
            

            selectedGradeLevel = $(this).val()

            $('#section').append('<option value="">SELECT SECTION</option>')
		
            $.each(temp_sections,function(a,b){
                $('#section').append('<option value="'+b.id+'">'+b.sectionname+'</option>')
            })

            $('#subject_list').empty()
            students = []
            data = []
            loaddatatable(data)
         
        })

        $(document).on('change','#quarter',function(){
            selectedQuarter = $(this).val()
            data = students
            loaddatatable(data)
        })

        $(document).on('click','#reload',function(){
            selectedQuarter = $('#quarter').val()
            data = students
            loaddatatable(data)
        })

        

        function loaddatatable(data){

            var new_data = data.filter(x=>x.student != 'SUBJECTS')
            var computed_data = []
            var setup_with_distinct = true;
            var setup_with_sp = true;
            var min_dist = parseFloat(85)
            var max_dist =  parseFloat(89.999)
            var min_sp =  parseFloat(80)
            var max_sp =  parseFloat(84.999)

            $.each(new_data,function(a,b){
                var gen_ave = null;
                var composite = null;
                var award = null;
                var lowest = null; 
                temp_data =  b.grades.filter(x=>x.subjid == 'G1')
                if($('#quarter').val() == 1){
                    gen_ave = temp_data[0].q1
                    composite = temp_data[0].q1comp
                    award = temp_data[0].q1award
                    lowest = temp_data[0].lq1
                }
                if($('#quarter').val() == 2){
                    gen_ave = temp_data[0].q2
                    composite = temp_data[0].q2comp
                    award = temp_data[0].q2award
                    lowest = temp_data[0].lq2
                }
                if($('#quarter').val() == 3){
                    gen_ave = temp_data[0].q3
                    composite = temp_data[0].q3comp
                    award = temp_data[0].q3award
                    lowest = temp_data[0].lq3
                }
                if($('#quarter').val() == 4){
                    gen_ave = temp_data[0].q4
                    composite = temp_data[0].q4comp
                    award = temp_data[0].q4award
                    lowest = temp_data[0].lq4
                }
                if($('#quarter').val() == 5){
                    gen_ave = temp_data[0].finalrating
                    composite = temp_data[0].fcomp
                    award = temp_data[0].fraward
                }
                computed_data.push({
                    'name':b.student,
                    'strand':b.strand,
                    'genave':gen_ave,
                    'temp_comp':composite,
                    'award':award,
                    'lowest':lowest
                })
            })

            $("#student_list").DataTable({
                              destroy: true,
                              data:computed_data,
                              "order": [[ 3, "desc" ]],
                              "columns": [
                                          { "data": "name" },
                                          { "data": null },
                                          { "data": "genave" },
                                          { "data": "temp_comp" },
                                          { "data": "award" },
                                          { "data": "lowest" },
                                        
                                    ],
                                columnDefs: [
                                {
                                        'targets': 1,
                                        'orderable': true, 
                                        'createdCell':  function (td, cellData, rowData, row, col) {
                                            $(td).addClass('text-center')

                                            var strandinfo = strand.filter(x=>x.strandid == rowData.strand)

                                            if(strandinfo.length > 0){
                                                $(td).text(strandinfo[0].strandcode)
                                            }else{
                                                $(td).text(null)
                                            }
                                           
                                            if($('#gradelevel').val() == 14 || $('#gradelevel').val() == 15){
                                                $(td).removeAttr('hidden')
                                            }else{
                                                $(td).attr('hidden','hidden')
                                            }
                                        }
                                },
                                {
                                        'targets': 2,
                                        'orderable': true, 
                                        'createdCell':  function (td, cellData, rowData, row, col) {
                                            $(td).addClass('text-center')
                                        }
                                },
                                {
                                        'targets': 3,
                                        'orderable': true, 
                                        'createdCell':  function (td, cellData, rowData, row, col) {
                                            $(td).addClass('text-center')
                                        }
                                        
                                },
                                {
                                        'targets': 4,
                                        'orderable': true, 
                                        'createdCell':  function (td, cellData, rowData, row, col) {
                                            $(td).addClass('text-center')
                                        }
                                },
                                {
                                        'targets': 5,
                                        'orderable': true, 
                                        'createdCell':  function (td, cellData, rowData, row, col) {
                                            $(td).addClass('text-center')
                                        }
                                },
                                ]
                              
                        });

                  
        }


        $(document).on('change','#gradelevel',function(){
            $('#view_ranking').attr('disabled','disabled')
            $('#quarter').empty();
            $('#quarter').append('<option value="">Select Quarter</option>')
            if($(this).val() == 14 || $(this).val() == 15){
                if($('#semester').val() == 1){
                    $('#quarter').append('<option value="1">1st Quarter</option>')
                    $('#quarter').append('<option value="2">2nd Quarter</option>')
                    $('#quarter').append('<option value="5">Final Rating</option>')
                }else{
                    $('#quarter').append('<option value="3">3rd Quarter</option>')
                    $('#quarter').append('<option value="4">4th Quarter</option>')
                    $('#quarter').append('<option value="5">Final Rating</option>')
                }
            }else{
                $('#quarter').append('<option value="1">1st Quarter</option>')
                $('#quarter').append('<option value="2">2nd Quarter</option>')
                $('#quarter').append('<option value="3">3rd Quarter</option>')
                $('#quarter').append('<option value="4">4th Quarter</option>')
                $('#quarter').append('<option value="5">Final Rating</option>')
            }

            if($(this).val() == 14 || $(this).val() == 15){
                $('.strand_holder').removeAttr('hidden')
            }else{
                $('.strand_holder').attr('hidden','hidden')
            }

        })

        $(document).on('change','#semester',function(){
                $('#quarter').empty();
                $('#quarter').append('<option value="">Select Quarter</option>')
                if($('#gradelevel').val() == 14 || $('#gradelevel').val() == 15){
                    if($('#semester').val() == 1){
                        $('#quarter').append('<option value="1">1st Quarter</option>')
                        $('#quarter').append('<option value="2">2nd Quarter</option>')
                        $('#quarter').append('<option value="5">Final Rating</option>')
                    }else{
                        $('#quarter').append('<option value="3">3rd Quarter</option>')
                        $('#quarter').append('<option value="4">4th Quarter</option>')
                        $('#quarter').append('<option value="5">Final Rating</option>')
                    }
                }else{
                    $('#quarter').append('<option value="1">1st Quarter</option>')
                    $('#quarter').append('<option value="2">2nd Quarter</option>')
                    $('#quarter').append('<option value="3">3rd Quarter</option>')
                    $('#quarter').append('<option value="4">4th Quarter</option>')
                    $('#quarter').append('<option value="5">Final Rating</option>')
                }
            })

        $(document).on('change','#section',function(){

            $('#view_ranking').attr('disabled','disabled')
            var temp_section = $(this).val()
            var temp_sy = $('#syid').val()
            var temp_strand = strand.filter(x=>x.sectionid == temp_section && x.syid == temp_sy)
            $("#strand").empty()
            $.each(temp_strand,function(a,b){
                    b.text = b.strandcode
                    b.id = b.strandid
            })
            $("#strand").append('<option value="">Select a strand</option>')
            $("#strand").select2({
                    data: temp_strand,
                    allowClear: true,
                    placeholder: "Select a strand",
            })

            data = []
            loaddatatable(data)
            $('#print_student_ranking').attr('disabled','disabled')


        })

        $(document).on('change','#quarter',function(){
            $('#view_ranking').attr('disabled','disabled')
        })

        var usertype = @json(auth()->user()->type)

        $(document).on('click','#filter',function(){

     

            var valid_filter = true;

            if($('#gradelevel').val() == ''){

                valid_filter = false

                Swal.fire({
                    type: 'info',
                    title: 'Please select grade level!',
                });

                return false;

            }

            if(usertype == 2 || usertype == 3){
                
            }else{
                if($('#section').val() == ''){
                    valid_filter = false

                    Swal.fire({
                        type: 'info',
                        title: 'Please select section',
                    });

                    return false;
                }
            }

         

            if($('#quarter').val() == ''){

                valid_filter = false

                Swal.fire({
                    type: 'info',
                    title: 'Please select quarter',
                });

                return false;

            }


            if(valid_filter){

                selectedSection = $('#section').val()
                selectedQuarter = $('#quarter').val()
                selectedGradeLevel = $('#gradelevel').val()
                selectedRankingType = $('#awardtype').val()
                selectedsy = $('#syid').val()

                selectedsem = 1
                if(selectedGradeLevel == 14 || selectedGradeLevel == 15){
                    selectedsem = $('#semester').val()
                }

                

                var strand  = $('#strand').val(); 

                $.ajax({
                    type:'GET',
                    url:'/searchStudentWithHonors',
                    data:{
                        students:'students',
                        gradelevel:selectedGradeLevel,
                        section:selectedSection,
                        quarter:selectedQuarter,
                        ranking:selectedRankingType,
                        from:$('#gradefrom').val(),
                        to:$('#gradeto').val(),
                        sy:selectedsy,
                        semid:selectedsem,
                        strand:strand
                    },
                    success:function(data) {
                        var count_student = data.filter(x=>x.student != 'SUBJECTS')
                        var subject_list = data.filter(x=>x.student == 'SUBJECTS')
                        $('#subject_list').empty()
                       
                        $.each(subject_list[0].grades,function(a,b){
                            if(b.subjid != "" && b.inMAPEH == 0){
                                $('#subject_list').append('<tr><td>'+b.subjtitle+'</td><td class="text-center"><input type="checkbox" class="subj_list" data-id="'+b.subjid+'" checked="checked"></td></tr>')
                            }
                          
                        })

                        if(count_student.length  == 0){
                            Swal.fire({
                                type: 'info',
                                title: 'No student enrolled!',
                            });
                            data = []
                            loaddatatable(data)
                            $('#print_student_ranking').attr('disabled','disabled')
                        }
                        else{
                            $('#print_student_ranking').removeAttr('disabled')
                            students = data
                            loaddatatable(data)
                        }
                    }
                })

            }

        })

    })
</script>

<script>
    $(document).ready(function(){
          var keysPressed = {};
          const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
          })
          document.addEventListener('keydown', (event) => {
                keysPressed[event.key] = true;
                if (keysPressed['p'] && event.key == 'v') {
                      Toast.fire({
                                  type: 'warning',
                                  title: 'Date Version: 11/26/2021'
                            })
                }
          });
          document.addEventListener('keyup', (event) => {
                delete keysPressed[event.key];
          });
    })
</script>
