<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset</title>
    <link href="{{asset('assets/icons/fontawesome/css/all.css')}}" rel="stylesheet">
    <link href="{{asset('assets/css/main.css')}}" rel="stylesheet">
    <script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>


    <style>
        body {
            height: 100vh;
            padding: 0;
            margin: 0;
        }
        .resethome {
            background-repeat: no-repeat;
            background-size: cover; 
            background-position: center; */
            clip-path: polygon(0 0,100% 0, 100% 80%,0 100%); 
            background: linear-gradient(481deg, #fbfbfb 75%, #eaa5a5 25%);
        }
        .reset {
            box-shadow: 1px 1px 4px #031d2ba6;
            margin-top: 20%;
        }

        @media screen and (max-width : 700px){
            .resethome {
                background: none;
                margin-left: 1em;
            }
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed resethome">
    <div class="row col-lg-12">
        <div class="col-md-4"></div>
        <div class="col-md-4 col-xs-12">
        <div class="card reset" >
        
        <div class="card-header loginheadercard" style="background-color: #fff; letter-spacing: 10px;color: #16aaff;justify-content:center">{{ __('Change Password') }} 
          
        </div>
            <div class="sclogoheader" style="position: relative;text-align:center">
                <img class="ckhlogo" src="{{asset('assets\images\bkshire.png')}}" alt="">
            </div>
            <div class="card-body ">
                
            <form action="/changepass" method="GET">
                    @csrf
                    <div class="form-group">
                        <label for="exampleInputEmail1">New Password</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="new-password" value="{{old('password')}}">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Retype Password</label>
                        <input type="password" class="form-control" name="password_confirmation" value="{{old('password_confirmation')}}">
                    </div>
                    <!--<div class="form-group">-->
                    <!--    <label><strong>SELECT VERIFICATION QUESTION</strong></label>-->
                    <!--    <select name="question" id="question" class="form-control @error('question') is-invalid @enderror">-->
                    <!--        <option value="" selected>SELECT QUESTION</option>-->
                    <!--        @if(auth()->user()->type == 7 || auth()->user()->type == 9)-->
                    <!--            <option value="1">Guardian's Contact Number</option>-->
                    <!--            <option value="4">Birth Date</option>-->
                    <!--        @else-->
                    <!--            <option value="5">Professional License Number</option>-->
                    <!--        @endif-->
                    <!--    </select>-->
                    <!--    @error('question')-->
                    <!--        <span class="invalid-feedback" role="alert">-->
                    <!--            <strong>{{ $message }}</strong>-->
                    <!--        </span>-->
                    <!--    @enderror-->
                    <!--</div>-->
                    <!--<div class="form-group">-->
                    <!--    <label><strong>ANSWER</strong></label>-->
                    <!--    <input name="answer" id="answer" class="form-control @error('answer') is-invalid @enderror" value="{{old('answer')}}">-->
                    <!--    @error('answer')-->
                    <!--        <span class="invalid-feedback" role="alert">-->
                    <!--            <strong>{{ $message }}</strong>-->
                    <!--        </span>-->
                    <!--    @enderror-->

                    <!--</div>-->
                    
                    <!--@error('wrong')-->
                    <!--    <span class="text-danger">-->
                    <!--        <strong>{{ $message }}</strong>-->
                    <!--    </span>-->
                    <!--@enderror-->
                <button  type="submit" class="btn btn-success w-100 mt-3">Change Password</button>
            </form>

                <a  type="button" onclick="event.preventDefault(); $('#logout-form').submit();"  class="btn btn-danger text-white w-100 mt-3">Cancel</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
               
        </div>
    </div>
        </div>
        <div class="col-md-4"></div>
    </div>
    @include('sweetalert::alert') 
    <script>
        $(document).ready(function(){
            
            $(document).on('change','#question',function(){
                        $('#answerholder').empty();
                        $('#answerholder').append('<label><strong>ANSWER</strong></label>')

                        if($(this).val() == 1){

                            //   $('#answerholder').append(
                            //         '<input id="answer" name="answer" class="form-control" placeholder="Guardian\'s Contact Number" minlength="11" maxlength="11" data-inputmask-clearmaskonlostfocus="true">'
                            //   )

                            $('#answer').attr('minlength' , '11')
                            $('#answer').attr('maxlength' , '11')

                            $('#answer').removeAttr('type')
                            $('#answer').attr('placeholder','09XXXXXXXXX')

                        }

                        else if($(this).val() == 4){

                            $('#answer').attr('type' , 'date')

                            //   $('#answerholder').append(
                            //         '<input id="answer" name="answer" class="form-control" placeholder="Mother\'s Name" minlength="11" maxlength="11" data-inputmask-clearmaskonlostfocus="true">'+
                            //         '<label class="pl-2 strong"><em>format: lastname, firstname</em></label>'
                            //   )
                        }
                        else if($(this).val() == 4){
                              
                              $('#answerholder').append(
                                    '<input type="date" id="answer" name="answer" class="form-control" >'
                                  
                              )
                             
                        }
                  })

                  @if ($errors->any())
                        $('#question').val('{{old('question')}}').change()
                        // $('#answer').val('{{old('answer')}}').change()
                  @endif

            })



    </script>
</body>
</html>