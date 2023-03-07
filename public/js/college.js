  
            $(document).ready(function(){

                  var section
                  var student = '{{$student->sid}}'
          
             
                  console.log('{{$student->sid}}')
                  $(document).on('click','.sectionselection td',function(){

                        section = $(this).attr('data-value');
                        // $('#enroll_button').attr('href','/enroll/student/'+'{{Str::slug($student->sid)}}/'+$(this).attr('data-value'))

                        $.ajax({
                                type:'GET',
                                url:'/enrollement/sectscshed',
                                data:{
                                    data:$(this).attr('data-value'),
                                },
                                success:function(data) {
                                   $('#classschedule').empty();
                                   $('#classschedule').append(data);
                                }
                            })


                       
                  })
             
                  $(document).on('click','.dropsubject',function(){
                        Swal.fire({
                              title: 'Are you sure?',
                              text: "You won't be able to revert this!",
                              type: 'info',
                              showCancelButton: true,
                              confirmButtonColor: '#3085d6',
                              cancelButtonColor: '#d33',
                              confirmButtonText: 'Yes, drop it!'
                              }).then((result) => {
                              if (result.value) {
                                   
                                    fetch('{{Request::root()}}'+'/college/enrollment/dropsubject/'+$(this).attr('data-value'))
                                    
                              }
                        })
                  })

                  $(document).on('click','#enrollstudent',function(){
                        Swal.fire({
                              title: 'Enroll Student?',
                              // text: '{{$student->firstname}}',
                              type: 'info',
                              showCancelButton: true,
                              confirmButtonColor: '#3085d6',
                              cancelButtonColor: '#d33',
                              confirmButtonText: 'Yes, drop it!'
                              }).then((result) => {
                              if (result.value) {
                                   
                                    fetch('{{Request::root()}}'+'/enroll/student/'+section+'/'+student)
                                    
                              }
                        })
                  })
            })

    