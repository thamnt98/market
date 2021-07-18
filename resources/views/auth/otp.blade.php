@extends('dashboard.authBase')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mx-4">
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
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            <h1>{{ __('Register') }}</h1>
                            <br>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-envelope"></i>
                                </span>
                                </div>
                                <input class="form-control" type="text" placeholder="{{ __('OTP') }}"
                                       name="otp" value="{{ old('otp') }}" required>
                                @if(Session::has('otp_valid'))
                                    <div class="invalid-feedback">
                                        {{ \Illuminate\Support\Facades\Session::get('otp_valid') }}
                                    </div>
                                @endif
                            </div>
                            <button class="btn btn-block btn-success" type="submit">{{ __('Next') }}</button>
                        </form>
                    </div>
                    <div class="card-footer p-4">
                        <div class="row">
                            <div class="col-5"></div>
                            <div class="col-7 text-rightl">
                                <span>Did you have an account?</span>
                                <a href="{{ route('login') }}">Login here</a>
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
