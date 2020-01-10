@extends('layouts.app')

@section('content')
<div class="container" id="register">
    <div class="row justify-content-center">
        <div>
            <div class="register-card">
                <div class="card-header bg-white">
                    <a class="btn-link" href="/">
                        <i class="el-icon-arrow-left"></i>{{ __('Back') }}
                    </a>
                </div>
                @if(session()->has('message'))
                    <div class="alert alert-success">
                      {{ session()->get('message') }}
                    </div>
                @endif
                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="email" class="col-md-2 col-form-label text-md-right">
                                <!-- {{ __('E-Mail Address') }} -->
                            </label>

                            <div class="col-md-8">
                                <h1 class="col-form-label">
                                    {{ __('Signup') }}
                                </h1>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="firstname" class="col-md-2 col-form-label text-md-right">
                                <!-- {{ __('First Name') }} -->
                            </label>

                            <div class="col-md-8">
                                <input id="firstname" type="text" class="form-control @error('firstname') is-invalid @enderror" name="firstname" value="{{ old('firstname') }}" required autocomplete="firstname" autofocus placeholder="{{ __('First Name') }}">

                                @error('firstname')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="lastname" class="col-md-2 col-form-label text-md-right">
                                <!-- {{ __('Last Name') }} -->
                            </label>

                            <div class="col-md-8">
                                <input id="lastname" type="text" class="form-control @error('lastname') is-invalid @enderror" name="lastname" value="{{ old('lastname') }}" required autocomplete="lastname" autofocus placeholder="{{ __('Last Name') }}">

                                @error('lastname')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-2 col-form-label text-md-right">
                                <!-- {{ __('E-Mail Address') }} -->
                            </label>

                            <div class="col-md-8">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="{{ __('E-mail Address') }}">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-2 col-form-label text-md-right">
                                <!-- {{ __('Password') }} -->
                            </label>

                            <div class="col-md-8">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="{{ __('Password') }}">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-2 col-form-label text-md-right">
                                <!-- {{ __('Confirm Password') }} -->
                            </label>

                            <div class="col-md-8">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="{{ __('Confirm Password') }}">
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-4 offset-md-2">
                                <button type="submit" class="btn btn-danger btn-register">
                                    {{ __('Register') }}
                                </button>
                            </div>

                            <div class="flex col-md-4 mt-2">
                                <input type="checkbox" required name="terms_policies" title="Approve to terms and policies?" class="@error('terms_policies') is-invalid @enderror"/> <a target="_blank" href="/terms-policies" title="Terms and Policies" style="font-size: 10px; text-decoration: none;">Terms and Policies</a>
                                @error('terms_policies')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
