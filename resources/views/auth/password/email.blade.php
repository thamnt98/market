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
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-envelope"></i>
                                </span>
                                    </div>
                                    <input class="form-control" type="text" placeholder="{{ __('E-Mail Address') }}"
                                           name="email" value="{{ old('email') }}" required>
                                    @if($errors->has('email'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('email') }}
                                        </div>
                                    @endif
                                </div>
                                <button class="btn btn-block btn-success" type="submit">{{ __('Send OTP') }}</button>
                                <br>
                                <div class="row">
                                    <div class="col-8">
                                        <span>Did you have an account?</span>
                                        <a href="{{ route('login') }}">Login here</a>
                                    </div>
                                    <div class="col-4"></div>
                                </div>
                            </form>
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
