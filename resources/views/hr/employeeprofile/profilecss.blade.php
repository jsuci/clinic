
<style>
  .credentialimg{
    border-radius: 0% !important;
}
.btn-circle {
width: 45px;
height: 45px;
line-height: 45px;
text-align: center;
padding: 0;
border-radius: 50%;
}

.btn-circle i {
position: relative;
top: -1px;
}

.btn-circle-sm {
width: 35px;
height: 35px;
line-height: 35px;
font-size: 0.9rem;
}

.btn-circle-lg {
width: 55px;
height: 55px;
line-height: 55px;
font-size: 1.1rem;
}

.btn-circle-xl {
width: 70px;
height: 70px;
line-height: 70px;
font-size: 1.3rem;
}
.edit-icon {
background-color: #ffc107;
border: 1px solid #e3e3e3;
border-radius: 24px;
color: #bbb;
float: right;
font-size: 12px;
/* line-height: 24px; */
/* min-height: 26px; */
text-align: center ;
width: 26px;
padding: 5px;
}
.edit-pic-icon {
background-color: #ffc107;
border: 1px solid #e3e3e3;
border-radius: 24px;
color: #bbb;
/* float: right; */
font-size: 12px;
line-height: 24px;
min-height: 26px;
text-align: center ;
/* width: 26px; */
padding: 5px;
/* position: absolute; */
/* right: 10px; */
/* left: 175px; */

/* bottom: 7px; */
}
.profile-view .pro-edit {
position: absolute;
right: 0;
top: 0;
}
/* .fas {
display: inline-block;
font-size: inherit;
text-rendering: auto;
-webkit-font-smoothing: antialiased;
-moz-osx-font-smoothing: grayscale;
} */
/* Chrome, Safari, Edge, Opera */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
-webkit-appearance: none;
margin: 0;
}
.ribbon-wrapper {
height: 45px;
overflow: hidden;
position: absolute;
right: -2px;
top: -2px;
width: 55px;
z-index: 10;
}
.ribbon-wrapper .ribbon {
box-shadow: 0 0 3px rgba(0,0,0,.3);
font-size: .8rem;
line-height: 12%;
padding: .375rem 0;
position: relative;
right: -2px;
text-align: center;
text-shadow: 0 -1px 0 rgba(0,0,0,.4);
text-transform: uppercase;
top: 10px;
-webkit-transform: rotate(45deg);
transform: rotate(45deg);
width: 75px;
}
/* Firefox */
input[type=number] {
-moz-appearance:textfield;
}
.alert {
font-family: sans-serif;
  padding: 15px;
margin-bottom: 20px;
border: 1px solid transparent;
border-radius: 4px;
}

.alert-success {
color: #3c763d;
background-color: #dff0d8;
border-color: #d6e9c6;
}


/*DEMO*/
.preview {
margin: 10px;
display: none;
}
.preview--rounded {
width: 160px;
height: 160px;
border-radius: 50%;
}
/* IMMUTABLE */
.hide {
display: none !important;
}
* {
box-sizing: border-box;
}
.photo__zoom {
position: relative;
padding-left: 22px;
padding-right: 22px;
/**
* Zoom
*/
/**
* Zoom handler
*/
/**
* FOCUS
*/
/**
* Zoom track
*/
/**
* ICONS
*/
}
.photo__zoom input[type=range] {
-webkit-appearance: none;
width: 100%;
background: transparent;
height: 18px;
}
.photo__zoom input[type=range]::-webkit-slider-thumb {
-webkit-appearance: none;
}
.photo__zoom input[type=range]:focus {
outline: none;
}
.photo__zoom input[type=range]::-ms-track {
width: 100%;
cursor: pointer;
background: transparent;
border-color: transparent;
color: transparent;
}
.photo__zoom input[type=range]:focus::-ms-thumb {
border-color: #268eff;
box-shadow: 0 0 1px 0px #268eff;
}
.photo__zoom input[type=range]:focus::-moz-range-thumb {
border-color: #268eff;
box-shadow: 0 0 1px 0px #268eff;
}
.photo__zoom input[type=range]:focus::-webkit-slider-thumb {
border-color: #268eff;
box-shadow: 0 0 1px 0px #268eff;
}
.photo__zoom input[type=range]::-webkit-slider-thumb {
-webkit-appearance: none;
margin-top: -9px;
box-sizing: border-box;
cursor: pointer;
width: 18px;
height: 18px;
display: block;
border-radius: 50%;
background: #eee;
border: 1px solid #ddd;
}
.photo__zoom input[type=range]::-webkit-slider-thumb:hover {
border-color: #c1c1c1;
}
.photo__zoom input[type=range]::-ms-thumb {
margin-top: 0;
box-sizing: border-box;
cursor: pointer;
width: 18px;
height: 18px;
display: block;
border-radius: 50%;
background: #eee;
border: 1px solid #ddd;
}
.photo__zoom input[type=range]::-ms-thumb:hover {
border-color: #c1c1c1;
}
.photo__zoom input[type=range]::-moz-range-thumb {
margin-top: 0;
box-sizing: border-box;
cursor: pointer;
width: 18px;
height: 18px;
display: block;
border-radius: 50%;
background: #eee;
border: 1px solid #ddd;
}
.photo__zoom input[type=range]::-moz-range-thumb:hover {
border-color: #c1c1c1;
}
.photo__zoom input[type=range]::-webkit-slider-runnable-track {
width: 100%;
height: 1px;
cursor: pointer;
background: #eee;
border: 0;
}
.photo__zoom input[type=range]::-moz-range-track {
width: 100%;
height: 1px;
cursor: pointer;
background: #eee;
border: 0;
}
.photo__zoom input[type=range]::-ms-track {
width: 100%;
height: 1px;
cursor: pointer;
background: #eee;
border: 0;
}
.photo__zoom input[type=range].zoom--minValue::before,
.photo__zoom input[type=range].zoom--maxValue::after {
color: #f8f8f8;
}
.photo__zoom input[type=range]::before,
.photo__zoom input[type=range]::after {
position: absolute;
content: "\f03e";
display: block;
font-family: 'FontAwesome';
color: #aaa;
transition: color 0.3s ease;
}
.photo__zoom input[type=range]::after {
font-size: 18px;
right: -2px;
top: 2px;
}
.photo__zoom input[type=range]::before {
font-size: 14px;
left: 4px;
top: 4px;
}
/**
* FRAME STYLE
*/
.photo__frame--circle {
border: 1px solid #e2e2e2;
border-radius: 50%;
}
.photo__helper {
position: relative;
background-repeat: no-repeat;
background-color: transparent;
padding: 15px 0;
}
.photo__helper .canvas--helper {
position: absolute;
top: 0;
left: 0;
right: 0;
bottom: 0;
}
.photo__frame img,
.photo__helper {
-webkit-touch-callout: none;
-webkit-user-select: none;
-khtml-user-select: none;
-moz-user-select: none;
-ms-user-select: none;
user-select: none;
}
.profile {
position: relative;
font-family: 'HelveticaNeueLTPro-Roman', sans-serif;
font-size: 85%;
/* width: 300px; */
}
.photo {
text-align: center;
margin-bottom: 15px;
}
.photo input[type=file] {
display: none;
}
.photo__options {
margin-top: 15px;
position: relative;
text-align: left;
}
.photo__options .remove {
padding: 0;
padding: 0;
display: inline-block;
text-decoration: none;
color: #ddd;
font-size: 18px;
width: 20%;
text-align: center;
vertical-align: middle;
}
.photo__options .remove:hover {
color: #000;
}
.photo__zoom {
vertical-align: middle;
width: 80%;
display: inline-block;
}
.photo__frame {
cursor: move;
overflow: hidden;
position: relative;
display: inline-block;
width: 160px;
height: 160px;
}
.photo__frame img,
.photo__helper img {
position: relative;
}
.photo__frame .message {
position: absolute;
left: 5px;
right: 5px;
top: 50%;
transform: translateY(-50%);
display: inline-block;
color: #268eff;
z-index: 3;
}
.photo__frame .is-dragover {
display: none;
}
.message p {
font-size: 0.9em;
}
.photo__options {
list-style: none;
}
.photo__options li {
display: inline-block;
text-align: center;
width: 50%;
}
.photo--empty .photo__frame {
cursor: pointer;
}
/**
* IMG states
*/
.profile.is-dragover .photo__frame img,
.photo--empty img,
.photo--error img,
.photo--error--file-type img,
.photo--error--image-size img,
.photo--loading img {
display: none;
}
/**
* States
*/
/** SELECT PHOTO MESSAGE */
.message--desktop,
.message--mobile {
display: none;
}
/* MOBILE */
.is-mobile .message--mobile {
display: inline-block;
}
.is-mobile .message--desktop {
display: none;
}
/* DESKTOP */
.is-desktop .message--desktop {
display: inline-block;
}
.is-desktop .message--mobile {
display: none;
}
/* DEFAULT */
.message.is-empty,
.message.is-loading,
.message.is-wrong-file-type,
.message.is-wrong-image-size,
.message.is-something-wrong,
.message.is-dragover {
display: none;
}
/* EMPTY */
.photo--empty .photo__options {
display: none;
}
.photo--empty .message.is-empty {
display: inline-block;
}
.photo--empty .photo__frame:hover {
background: #268eff;
}
.photo--empty .photo__frame:hover .message {
color: #fff;
}
/* LOADING */
.photo--loading .message.is-loading {
display: inline-block;
}
.photo--loading .message.is-empty,
.photo--loading .message.is-wrong-file-type,
.photo--loading .message.is-dragover,
.photo--loading .message.is-wrong-image-size,
.photo--loading .photo__options {
display: none;
}
/* ERROR */
/* UNKNOWN */
.photo--error .message.is-empty,
.photo--error .message.is-loading,
.photo--error .message.is-dragover,
.photo--error .message.is-wrong-image-size,
.photo--error .photo__options {
display: none;
}
.photo--error .message.is-something-wrong {
display: inline-block;
}
/* FILE TYPE*/
.photo--error--file-type .message.is-empty,
.photo--error--file-type .message.is-loading,
.photo--error--file-type .message.is-dragover,
.photo--error--file-type .message.is-wrong-image-size,
.photo--error--file-type .photo__options {
display: none;
}
.photo--error--file-type .message.is-wrong-file-type {
display: inline-block;
}
/* IMAGE SIZE */
.photo--error--image-size .message.is-empty,
.photo--error--image-size .message.is-loading,
.photo--error--image-size .message.is-dragover,
.photo--error--image-size .message.is-wrong-file-type,
.photo--error--image-size .photo__options {
display: none;
}
.photo--error--image-size .message.is-wrong-image-size {
display: inline-block;
}
/* DRAGOVER */
.profile.is-dragover .photo__frame .is-dragover {
display: inline-block;
}
.profile.is-dragover .message.is-empty,
.profile.is-dragover .message.is-loading,
.profile.is-dragover .message.is-wrong-file-type,
.profile.is-dragover .message.is-wrong-image-size {
display: none;
}

@media screen and (max-width : 1920px){
.div-only-mobile{
visibility:hidden;
}
}
@media screen and (max-width : 906px){
.desk{
visibility:hidden;
}
.div-only-mobile{
visibility:visible;
}
.credentialsviewbutton{
width: 100%; display:block;
}
.credentialsdeletebutton{
width: 100%; display:block;
}
}

</style>