@extends('layouts.app')
@section('footer')
    <script src="https://js.stripe.com/v3/"></script>
@endsection
@section('custom_css')
<style type="text/css">
    .el-badge__content.is-fixed { top: 16px; }
    #landingPage .title, #landingPage .author{
        width: -webkit-fill-available;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        text-align: center;
    }
    #landingPage .dialog-cover .paragraph-form{
        /*cursor: pointer;*/
        height: -webkit-fill-available;
        max-height: 245px;
        min-height: 193px;
        text-overflow: ellipsis;
        overflow: hidden;
    }
    #landingPage .book-cover, #landingPage .preview .preview-container{
        cursor: pointer;
        -webkit-touch-callout: none; /* iOS Safari */
        -webkit-user-select: none; /* Safari */
        -khtml-user-select: none; /* Konqueror HTML */
        -moz-user-select: none; /* Firefox */
        -ms-user-select: none; /* Internet Explorer/Edge */
        user-select: none; /* Non-prefixed version, currently
                          supported by Chrome and Opera */
    }
</style>
@endsection
@section('content')
<div >
    <div>
        <div>
        	<form action="POST">
                @csrf   
            </form>
            <home-page :user-data="{{ Auth::user() }}"></home-page>
        </div>
    </div>
</div>
@endsection
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          