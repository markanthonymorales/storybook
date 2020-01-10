@extends('layouts.app')
@section('custom_css')
<style type="text/css">

    /*#reset-password { 
        margin-top: 100px;
    }
    #reset-password .card {
        box-shadow: 0 4px 8px 0 #b3b3b3, 0 6px 20px 0 #b3b3b3;
    }
    #reset-password h1 { font-size: 18px; font-weight: 700; }
    .btn-reset { padding: 5px 25px; }*/
</style>
@endsection
@section('content')
<div class="container" id="reset-password">
    <div class="row justify-content-center">
        <div>
            <div class="reset-card">
                <div class="card-header bg-white flex">
                    <div class="pull-left col-xs-6 col-md-6 p-0">
                        <a class="btn-link" href="/">
                            <i class="el-icon-arrow-left"></i>{{ __('Back') }}
                        </a>
                    </div>
                    <div class="pull-left col-xs-6 col-md-6 text-right p-0">
                        {{ __('Reset Password') }}
                    </div>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-danger btn-reset">
                                    {{ __('Send Password Reset Link') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
