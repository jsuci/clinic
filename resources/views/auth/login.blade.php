@extends('layouts.app')

@section('headerscript')


    <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
    
@endsection

@section('content')

@php
    $nowInLondonTz = Carbon\Carbon::parse('Asia/Manila');
    $time =  $nowInLondonTz->isoFormat('h:m:ss');
    $hour =  $nowInLondonTz->isoFormat('h');
    $minute =  $nowInLondonTz->isoFormat('m');
    $second =  $nowInLondonTz->isoFormat('ss');
@endphp


@php
    $schoolInfo = DB::table('schoolinfo')->first();
@endphp
<style>
    .school-details{
        font-size: 14px;
    }
    .visit-website
    {
        font-size: 12px;
    }
    .tagline {
        font-size: 20px;
    }
#thiscontainer .fxt-bg-color {
    margin: 0 auto;
    width: 60%;
}
.fxt-header, .fxt-header div {
    margin: 0px !important;
    text-align: center;
    width: 100%;
}
    @media only screen and (max-width: 600px) {
  .school-details{
      font-size: 14px;
  }
    .visit-website
    {
        font-size: 14px;
    }
    .tagline {
        font-size: 18px;
    }
#thiscontainer .fxt-bg-color {
    margin: 0px !important;
    width: 100%;
}
}


/* .text {
  position: absolute;
  width: 450px;
  left: 50%;
  margin-left: -225px;
  height: 40px;
  top: 50%;
  margin-top: -20px;
}

p {
  display: inline-block;
  vertical-align: top;
  margin: 0;
}

.word {
  position: absolute;
  width: 220px;
  opacity: 0;
}

.letter {
  display: inline-block;
  position: relative;
  float: left;
  transform: translateZ(25px);
  transform-origin: 50% 50% 25px;
}

.letter.out {
  transform: rotateX(90deg);
  transition: transform 0.32s cubic-bezier(0.55, 0.055, 0.675, 0.19);
}

.letter.behind {
  transform: rotateX(-90deg);
}

.letter.in {
  transform: rotateX(0deg);
  transition: transform 0.38s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.wisteria {
  color: #8e44ad;
}

.belize {
  color: #2980b9;
}

.pomegranate {
  color: #c0392b;
}

.green {
  color: #16a085;
}

.midnight {
  color: #2c3e50;
} */
</style>

                
@php
$adimages = DB::table('adimages')->where('isactive','1')->get()

@endphp

<section class="fxt-template-animation fxt-template-layout20 m-0">
    <div class="container" id="thiscontainer">
            <div class="fxt-bg-color pt-0" style="border-radius: 20px;">
                <div class="fxt-content mt-0 text-center" style="padding: 20px 40px!important;">
                    <div class="fxt-header">
                        <div class="row mb-2">
                            <div class="col-xl-12 col-lg-12 col-12 ">
                                <a class="navbar-brand m-0" href="http://{{DB::table('schoolinfo')->first()->websitelink}}">
                                    
                                    @if(isset(DB::table('schoolinfo')->first()->schoolname))
										<!--SCHOOL LOGO-->
                                        @if(strpos(db::table('schoolinfo')->first()->picurl, 'random') !== false)
                                            @php
                                                $picurl = explode("?", db::table('schoolinfo')->first()->picurl);
                                               $picurl = $picurl[0];
                                            @endphp
                                                <img src="{{$picurl}}" width="150px">
                                        @else
                                            @if(db::table('schoolinfo')->first()->picurl != null)
                                                <img src="{{db::table('schoolinfo')->first()->picurl}}" width="150px">
                                            @endif
                                        @endif

                                    @else
                                        SCHOOL LOGO
                                    @endif
                                    
                                    
                                </a>
                            </div>
                            <div class="col-xl-12 col-lg-12 col-12 text-left school-details text-center tagline">
                                <a class="nav-link p-0 visit-website" href="http://{{DB::table('schoolinfo')->first()->websitelink}}"><i class="fas fa-home"></i> Visit school website</a>
                                @if(isset(DB::table('schoolinfo')->first()->schoolname))
                                    {{DB::table('schoolinfo')->first()->schoolname}}
                                @else
                                    SCHOOL NAME
                                @endif
                                <br/>
                                <small>
                                    @if(isset(DB::table('schoolinfo')->first()->address))
                                        {{DB::table('schoolinfo')->first()->address}}
                                    @else
                                        SCHOOL ADDRESS
                                    @endif
                                </small>
                            </div>
                        </div>
                        <br/>
                        <div class="row mb-0">
                            <div class="col-md-12 text-center" >
                                @php
                                    $taglinefirst = strtok(DB::table('schoolinfo')->first()->tagline, " ");
                                    $taglinesecond = str_replace($taglinefirst, '',DB::table('schoolinfo')->first()->tagline);
                                @endphp
                                    <em>{{DB::table('schoolinfo')->first()->tagline}}</em>
                            </div>
                        </div>
                        <div class="fxt-style-line">
                            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                        </div>
                    </div>
                    <div class="fxt-form">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="form-group">
                                <div class="fxt-transformY-50 fxt-transition-delay-1">
                                    <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Username" style="border-radius: 25px;"/>

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password"  placeholder="Password"  style="border-top-left-radius: 25px !important; border-bottom-left-radius: 25px !important;">
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <span class="input-group-append">
                                      <button type="button" onclick=" return false;" class="btn btn-default btn-flat border" id="view_pass" style="border-top-right-radius: 25px !important; border-bottom-right-radius: 25px !important;"><i class="fas fa-eye"></i></button>
                                    </span>
                                  </div>
                                  <script>
                                      $(document).ready(function(){
                                          $(document).on('click','#view_pass',function(){
                                              if($('#password').attr('type') == 'password'){
                                                  $('#password').removeAttr('type')
                                              }else{
                                                  $('#password').attr('type','password')
                                              }
                                          })
                                      })
                                  </script>
                            </div>
                            <div class="form-group">
                                    <div class="fxt-transformY-50 alert alert-warning fxt-transition-delay-4 m-0" style="text-align: justify; font-size: 12.5px; border: 1px solid gold; line-height: 15px;">
                                        <strong>{{$schoolInfo->abbreviation}}'s SMS Privacy Notice</strong><br/>By clicking the Login button, I recognize the authority of the school and the third-party service provider to process my personal information, sensitive personal information, and privileged information pursuant to the Data Privacy and Regulation of the school, its third-party service provider, and governmental applicable laws.
                                    </div>
                                <div class="fxt-transformY-50 fxt-transition-delay-4">
                                    <button type="submit" class="btn submit fxt-btn-fill" style="background-color: {{$schoolInfo->schoolcolor}}; border-radius: 25px;"> {{ __('Login') }}</button>
                                </div>
                                <div class="fxt-transformY-50 fxt-transition-delay-4">
                                    <a href="/coderecovery" class="btn btn-lg btn-primary btn-block" style=" border-radius: 25px;">Get Username/Password</a>
                                </div>
								<div class="fxt-transformY-50 fxt-transition-delay-4 mt-2">
                                    <a href="/preregv2" class="btn btn-lg btn-danger btn-block" style=" border-radius: 25px;">Pre-registration</a>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="fxt-footer">
                        <div class="fxt-transformY-50 fxt-transition-delay-5" style="font-size: 9px;">
                            {{-- Information provided here is protected by Republic Act No. 10173, otherwise known as the Data Privacy Act. --}}
                            The processing of personal information shall be allowed, subject to compliance with the requirements of Republic Act 10173 (Data Privacy Act of 2012) and other laws allowing disclosure of information to the public and adherence to the principles of transparency, legitimate purpose and proportionality.
                        </div>
                    </div>
                </div>
            </div>
    </div>
</section>

<script type="text/javascript">var words = document.getElementsByClassName('word');
    // var wordArray = [];
    // var currentWord = 0;
    
    // words[currentWord].style.opacity = 1;
    // for (var i = 0; i < words.length; i++) {
    //   splitLetters(words[i]);
    // }
    
    // function changeWord() {
    //   var cw = wordArray[currentWord];
    //   var nw = currentWord == words.length-1 ? wordArray[0] : wordArray[currentWord+1];
    //   for (var i = 0; i < cw.length; i++) {
    //     animateLetterOut(cw, i);
    //   }
      
    //   for (var i = 0; i < nw.length; i++) {
    //     nw[i].className = 'letter behind';
    //     nw[0].parentElement.style.opacity = 1;
    //     animateLetterIn(nw, i);
    //   }
      
    //   currentWord = (currentWord == wordArray.length-1) ? 0 : currentWord+1;
    // }
    
    // function animateLetterOut(cw, i) {
    //   setTimeout(function() {
    //     cw[i].className = 'letter out';
    //   }, i*80);
    // }
    
    // function animateLetterIn(nw, i) {
    //   setTimeout(function() {
    //     nw[i].className = 'letter in';
    //   }, 340+(i*80));
    // }
    
    // function splitLetters(word) {
    //   var content = word.innerHTML;
    //   word.innerHTML = '';
    //   var letters = [];
    //   for (var i = 0; i < content.length; i++) {
    //     var letter = document.createElement('span');
    //     letter.className = 'letter';
    //     letter.innerHTML = content.charAt(i);
    //     word.appendChild(letter);
    //     letters.push(letter);
    //   }
      
    //   wordArray.push(letters);
    // }
    
    // changeWord();
    // setInterval(changeWord, 4000);
</script>
@endsection
