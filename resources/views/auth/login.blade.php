@extends('layouts.login', ['titlePage' => __('Doctor Go Live')])
@section('content')
<style>
    /* Wrapper */
.custom_css_forremeber {
    display: flex;
    justify-content: space-between;
    align-items: center; /* vertical alignment */
    margin-top: 10px;
    margin-bottom: 10px;
}

/* Fix checkbox alignment */
.custom_css_forremeber .form-check {
    display: flex;
    align-items: center;
    margin: 0;
}

/* Override Bootstrap absolute positioning */
.custom_css_forremeber .form-check-input {
    position: static !important;
    margin: 0 6px 0 0; /* spacing between checkbox and label text */
}

/* Label styling */
.custom_css_forremeber .form-check-label {
    margin: 0;
    font-size: 14px;
    line-height: 1.2;
}

/* Forgot password link */
.custom_css_forremeber .auth-link {
    font-size: 14px;
    color: #000; /* adjust to match design */
    text-decoration: none;
}
.custom_css_forremeber .auth-link:hover {
    text-decoration: underline;
}
.auth form .auth-link {
  font-size: 14px;
}
</style>
<div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-stretch auth auth-img-bg">
            <div class="row flex-grow">
                <div class="col-lg-6 d-flex align-items-center justify-content-center">
                    <div class="auth-form-transparent text-left p-3">
                        <div class="brand-logo">
                            <!--<h4>Appointment</h4>-->
                            <img src="{{ asset('uploads') }}/logo/logo.png" alt="logo">
                        </div>
                        <h4 style="color: #28bf96;">Welcome back!</h4>
                        <h6 class="font-weight-light">Happy to see you again!</h6>
                        @if($errors->has('role'))
                        <div class="alert alert-danger">
                            {{ $errors->first('role') }}
                        </div>
                        @endif
                        <form class="pt-3" action="{{ route('login') }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="exampleInputEmail">Username</label>
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
                            <div class="form-group">
                                <label for="exampleInputPassword">Password</label>
                                <div class="input-group">
                                    <div class="input-group-prepend bg-transparent">
                                        <span class="input-group-text bg-transparent border-right-0">
                                            <i class="mdi mdi-lock-outline text-primary"></i>
                                        </span>
                                    </div>
                                    <input id="password" type="password" class="form-control form-control-lg border-left-0 @error('password') is-invalid @enderror" placeholder="Enter Password" name="password" required autocomplete="current-password">
                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="my-2 d-flex justify-content-between align-items-center custom_css_forremeber">
                                <div class="form-check">
                                    <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input" name="remember" id="remember"> Remember me </label>
                                </div>
                                <a href="{{route('password.request')}}" class="auth-link text-black">Forgot password?</a>
                            </div>
                            <div class="my-3">
                                <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">LOGIN</button>
                            </div>
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
