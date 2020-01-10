@extends('layouts.app')
@section('content')
    <div class="container" id="terms-policies">
        <div class="row justify-content-center">
            <div>
                <div class="terms-policies-card">
                    <div class="card-header bg-white">
                        <h1 class="title">Terms and Policies</h1>
                    </div>
                    <div class="card-body">
                        {!! $data !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection