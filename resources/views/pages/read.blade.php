@extends('layouts.app')
@section('custom_css')
<style type="text/css">
    .page-container {
        width: 595px;
        height: 842px;
    }
    .share-link{
        margin-right: 5px;
    }
    .carousel-arrow i, .additional_information i{ cursor: pointer; }
    .big-paper.is_cover{
    	background-position: center;
      	background-repeat: no-repeat;
      	background-size: contain;
    }
</style>
@endsection
@section('content')
    <read-page :read-story="{{ $data }}" type-data="{{ $type }}"></read-page>
@endsection