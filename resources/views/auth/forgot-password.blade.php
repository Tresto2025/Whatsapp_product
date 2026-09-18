@extends('layouts.login', ['titlePage' => __('Doctor Go Live | Forget Password')])
@section('content')

<div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-stretch auth auth-img-bg">
            <div class="row flex-grow">
                <div class="col-lg-6 d-flex align-items-center justify-content-center">
                    <div class="auth-form-transparent text-left p-3">
                        <div class="brand-logo">
                            <img src="{{ asset('uploads') }}/logo/logo.png" alt="logo">
                        </div>
                        <h4>Forgot your password?</h4>
                        <h6 class="font-weight-light">{{ __('No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}</h6>
                        @if($errors->has('role'))
                        <div class="alert alert-danger">
                            {{ $errors->first('role') }}
                        </div>
                        @endif
                        <form class="pt-3" action="{{ route('password.email') }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="exampleInputEmail">Email</label>
                                <div class="input-group">
                                    <div class="input-group-prepend bg-transparent">
                                        <span class="input-group-text bg-transparent border-right-0">
                                            <i class="mdi mdi-account-outline text-primary"></i>
                                        </span>
                                    </div>
                                    <input id="email" type="email" class="form-control form-control-lg border-left-0 @error('email') is-invalid @enderror" name="email" placeholder="Enter Email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="my-3">
                                <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">Email Password Reset Link</button>
                            </div>
                            
                        <x-auth-session-status class="mb-4" :status="session('status')" />
                            <!--<div class="mb-2 d-flex">
                                <button type="button" class="btn btn-facebook auth-form-btn flex-grow mr-1"><i class="mdi mdi-facebook mr-2"></i>Facebook </button>
                                <button type="button" class="btn btn-google auth-form-btn flex-grow ml-1"><i class="mdi mdi-google mr-2"></i>Google </button>
                            </div>
                            <div class="text-center mt-4 font-weight-light"> Don't have an account? <a href="register-2.html" class="text-primary">Create</a></div>-->
                        </form>
                    </div>
                </div>
                <div class="col-lg-6 login-half-bg d-flex flex-row">
                    <p class="text-white font-weight-medium text-center flex-grow align-self-end">Copyright &copy; 2025 All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection