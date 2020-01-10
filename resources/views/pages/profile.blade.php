@extends('layouts.app')
@section('custom_css')
<style>
  .avatar-uploader .el-upload {
    cursor: pointer;
    position: relative;
    overflow: hidden;
    width: 150px;
    height: 150px;
    border-radius: 50%;
  }
  .avatar-uploader .el-upload:hover {
    border-color: #409EFF;
  }
  .avatar-uploader-icon {
    font-size: 28px;
    color: #fff;
    width: 100%;
    height: 100%;
    line-height: 178px;
    text-align: center;
  }
  .avatar {
    width: 100%;
    height: auto;
    display: block;
    object-fit: cover;
  }
  .btn {
    cursor: pointer;
  }
  .el-radio-button.is-active span {
        background-color: #f33838  !important;
        border-color: #f33838  !important;
        box-shadow: -1px 0 0 0 #f33838  !important;
   }
   .el-form-item__error{
        position: relative !important;
   }
   .ck.ck-editor__editable_inline p {
      line-height: 1.6;
      font-size: 0.9rem;
  }
   @media only screen and (max-width: 505px) {
      button.el-button.float-right{
        float: left !important;
        margin-bottom: 10px;
      }
      .el-radio-button__inner{
        padding: 6px 8px !important;
        font-size: 12px !important;
      }
   }
   @media only screen and (max-width: 389px) {
      .el-radio-button__inner{
        padding: 6px 8px !important;
        font-size: 10px !important;
      }
   }
   @media only screen and (max-width: 320px) {
      .el-radio-button__inner{
        padding: 6px 5px !important;
        font-size: 10px !important;
      }
   }
</style>
@endsection
@section('content')
    <profile-page :user-data="{{ Auth::user() }}" :contact-data="{{ $contacts }}" :stories-data="{{ Auth::user()->story }}" :books-data="{{ Auth::user()->books }}" :config-data="{{ config('custom.class')::get() }}" :address-data="{{ Auth::user()->address }}"></profile-page>
@endsection