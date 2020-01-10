<!-- @php $js='js/landingpage.js'; @endphp
@php $id='landingpage'; @endphp -->
@extends('layouts.app')
@section('custom_css')
<style type="text/css">
    .pb-4, .py-4, .pt-4{ padding-top: 0 !important; padding-bottom: 0 !important; }
</style>
@endsection
@section('content')
    @guest
    <div class="no-margin" style="height: 100% !important">
        <landing-page></landing-page>
    </div>
    @endif
@endsection