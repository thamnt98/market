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
                            <h1>Forgot password</h1>
                            <br>
                            <form method="POST" action="{{ route('password.forgot') }}">
                                @csrf
                                <div class="input-group mb-4">
                                    <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa fa-lock"></i>
                                    </span>
                                    </div>
                                    <input class="form-control" type="password" placeholder="{{ __('New password') }}"
                                           name="password" required>
                                    <div class="invalid-feedback">
                                        {{ $errors->first('password') }}
                                    </div>
                                </div>
                                <div class="input-group mb-4">
                                    <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa fa-lock"></i>
                                    </span>
                                    </div>
                                    <input class="form-control" type="password" placeholder="{{ __('Confirm password') }}"
                                           name="password_confirmation" required>
                                    <div class="invalid-feedback">
                                        {{ $errors->first('password_confirmation') }}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <button class="btn btn-primary px-4" type="submit">{{ __('Change Password') }}</button>
                                    </div>
                            </form>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-8">
                                <span>Did you have an account?</span>
                                <a href="{{ route('login') }}">Login here</a>
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
