@extends('layouts.app')

@section('content')
    <write-page-test :user-id="{{ Auth::user()->id }}" :story-data="{{ Auth::user()->story }}"></write-page-test>
    <script type="text/javascript" src="{{ asset('html-page-layout/src/index.js') }}"></script>
@endsection