@extends('dashboard.authBase')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card-group">
                <div class="card p-4">
                    @if ($message = Session::get('error'))
                        <div class="alert alert-danger alert-block" style="margin: 0px 15px 20px 15px">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <strong>{{ $message }}</strong>
                        </div>
                    @endif
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-block" style="margin: 0px 15px 20px 15px">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <strong>{{ $message }}</strong>
                        </div>
                    @endif
                    <div class="card-body">
                        <h1>Login</h1>
                        <br>
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa fa-user"></i>
                                    </span>
                                </div>
                                <input class="form-control" type="text" placeholder="{{ __('E-Mail Address') }}"
                                    name="email" value="{{ old('email') }}" required autofocus>
                                <div class="invalid-feedback">
                                    {{ $errors->first('email') }}
                                </div>
                            </div>
                            <div class="input-group mb-4">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa fa-lock"></i>
                                    </span>
                                </div>
                                <input class="form-control" type="password" placeholder="{{ __('Password') }}"
                                    name="password" required>
                                <div class="invalid-feedback">
                                    {{ $errors->first('password') }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <button class="btn btn-primary px-4" type="submit">{{ __('Login') }}</button>
                                </div>
                        </form>
                        <div class="col-6 text-right">
                            <a href="{{ route('password.forgot') }}" class="btn btn-link px-0">{{ __('Forgot Your Password?') }}</a>
                        </div>
                    </div>
                    <br>
                     <div class="row">
                        <div class="col-8">
                            <span>Dont you have an account?</span>
                            <a href="{{ route('register') }}">Register here</a>
                        </div>
                        <div class="col-4"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@endsection

@section('javascript')

@endsection
