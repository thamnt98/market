@extends('dashboard.authBase')

@section('content')

        <div class="row">
            <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12 background">
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12" style="padding: 0 15px 0px 0px">
                <div class="card justify-content-center" style="height: 100%" >
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
                    <div class="p-4">
                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            <h1 class="text-center">{{ __('Register') }}</h1>
                            <br>
                            <div class="input-group mb-4">
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

@endsection

@section('javascript')

@endsection
