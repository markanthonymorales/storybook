@extends('layouts.app')
@section('custom_css')
<style type="text/css">
	#write .big-paper {
        border:1px solid #e2e2e2 !important;
        overflow-y: hidden;
        overflow-x: hidden;
        overflow: hidden;
    }
    #write .big-paper.disabled {
        border:1px solid #e3342f !important;
    }
    #write .pages-container span.page-number{
        margin-right: 15px;
    }
    #write .big-paper #editor p {
        width: -webkit-fill-available;
    }
    #write .group-action{
        width: 78%
    }
    #write .pages-container .card-docs {
        background-color: white;
    }
    .listing-page{
        min-height: 626px; 
        max-height: 626px; 
        overflow-y: auto
    }
    .page-container-second {
        width: 595px;
        height: 842px;
    }
    .ck-content .image>figcaption{
        display: none
    }
    .ck-editor__editable {
        min-height: 100%;
        max-height: 100%;
        width: 100%;
        /*padding: .5in .5in .5in .5in !important;*/
    }  
    .ck-editor__editable_inline {
        box-shadow: none !important;
        border:none !important;
        padding: 0;
    }
    .ck-editor__editable_inline.ck-read-only{
        border:none !important;
    }
    .pages-container.display-row.margin-sm {
        position: relative;
    }
    #write .group-action {
        display: none;
        width: 110px;
        padding: 28px 0;
        position: absolute;
        text-align: center;
        height: 100%;
        background: #e2e2e2;
        opacity: 0.5;
    }
    .pages-container:hover .group-action, .group-action:hover{
        display: block !important;
    }
    .group-action div.action {
        display: block;
        width: 100%;
        position: relative;
    }
    .group-action div.action i {
        cursor: pointer;
        font-size: 41px !important;
    }
    .group-action div.action i.el-icon-edit-outline:hover{ color: green; }
    .group-action div.action i.el-icon-error:hover{ color: red; }
    .el-tag .el-icon-close{ font-size: 12px !important }
</style>
@endsection
@section('content')
    <write-page :user-id="{{ Auth::user()->id }}" :story-data="{{ $story }}"></write-page>
@endsection