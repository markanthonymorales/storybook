@extends('layouts.app')
@section('custom_css')
<style type="text/css">
    /*#login { 
        margin-top: 100px;
    }
    #login .card {
        box-shadow: 0 4px 8px 0 #b3b3b3, 0 6px 20px 0 #b3b3b3;
    }
    #login h1 { font-size: 18px; font-weight: 700; }
    .btn-login { padding: 5px 25px; }*/

    
</style>
@endsection

@section('content')

<div class="container" id="login">
    <div class="row justify-content-center">
        <div >
            <div class="login-card">
                <div class="card-header bg-white">
                    <a class="btn-link" href="/">
                        <i class="el-icon-arrow-left"></i>{{ __('Back') }}
                    </a>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="email" class="col-md-2 col-form-label text-md-right"></label>

                            <div class="col-md-8">
                                <h1 class="col-form-label">
                                    {{ __('Welcome') }}
                                </h1>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-2 col-form-label text-md-right"></label>

                            <div class="col-md-8">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="{{ __('E-Mail Address') }}">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-2 col-form-label text-md-right"></label>

                            <div class="col-md-8">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="{{ __('Password') }}">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-10 offset-md-2">
                                <button type="submit" class="btn btn-danger btn-login">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-10 offset-md-2">
                                <label for="password" class="col-form-label">
                                    Don't Have an Account ?<a class="btn-link" href="{{ route('register') }}">
                                        {{ __('Sign Up') }}
                                    </a>
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
