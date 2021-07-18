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
                                    <i class="fa fa-user"></i>
                                </span>
                            </div>
                            <input class="form-control" type="text" placeholder="{{ __('First name') }}" name="first_name"
                                value="{{ $data['first_name'] ?? '' }}" required autofocus>
                            @if($errors->has('first_name'))
                            <div class="invalid-feedback">
                                {{ $errors->first('first_name') }}
                            </div>
                            @endif
                        </div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-user"></i>
                                </span>
                            </div>
                            <input class="form-control" type="text" placeholder="{{ __('Last name') }}" name="last_name"
                                   value="{{ $data['last_name'] ?? '' }}" required autofocus>
                            @if($errors->has('last_name'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('last_name') }}
                                </div>
                            @endif
                        </div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-envelope"></i>
                                </span>
                            </div>
                            <input class="form-control" type="text" placeholder="{{ __('E-Mail Address') }}"
                                name="email" value="{{ \Illuminate\Support\Facades\Session::get('email') }}" required readonly>
                        </div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-phone"></i>
                                </span>
                            </div>
                            <input class="form-control" type="text" placeholder="{{ __('Phone number') }}"
                                   name="phone_number" value="{{ $data['phone_number'] ?? '' }}" required>
                            @if($errors->has('phone_number'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('phone_number') }}
                                </div>
                            @endif
                        </div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-lock"></i>
                                </span>
                            </div>
                            <input class="form-control" type="password" placeholder="{{ __('Password') }}"
                                name="password" required>
                            @if($errors->has('password'))
                            <div class="invalid-feedback">
                                {{ $errors->first('password') }}
                            </div>
                            @endif
                        </div>
                        <div class="input-group mb-4">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-lock"></i>
                                </span>
                            </div>
                            <input class="form-control" type="password" placeholder="{{ __('Confirm Password') }}"
                                name="password_confirmation" required>
                            @if($errors->has('password_confirmation'))
                            <div class="invalid-feedback"> {{ $errors->first('password_confirmation') }} </div>
                            @endif
                        </div>
                        <button class="btn btn-block btn-success" type="submit">{{ __('Register') }}</button>
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
