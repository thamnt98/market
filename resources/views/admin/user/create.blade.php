@extends('layouts.base')

@section('css')
@endsection

@section('content')

<div class="container-fluid">
    @if ($message = Session::get('error'))
    <div class="alert alert-danger alert-block">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{ $message }}</strong>
    </div>
    @endif
    @if ($message = Session::get('success'))
    <div class="alert alert-success alert-block">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{ $message }}</strong>
    </div>
    @endif
    <form method="post" action="{{ route('user.store') }}">
        @csrf
        <div class="form-row">
            <div class="form-group col-md-6">
                <label>First name</label>
                <input class="form-control" type="text" name="first_name" value="{{ old('first_name') }}" required>
                @if($errors->has('first_name'))
                <span class="text-danger text-md-left">{{ $errors->first('first_name') }}</span>
                @endif
            </div>
            <div class="form-group col-md-6">
                <label>Last name</label>
                <input class="form-control" type="text" name="last_name" value="{{ old('last_name') }}" required>
                @if($errors->has('last_name'))
                <span class="text-danger text-md-left">{{ $errors->first('last_name') }}</span>
                @endif
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Email</label>
                <input class="form-control" type="email" name="email" value="{{ old('email') }}" required>
                @if($errors->has('email'))
                <span class="text-danger text-md-left">{{ $errors->first('email') }}</span>
                @endif
            </div>
            <div class="form-group col-md-3">
                <label>Phone number</label>
                <input class="form-control" type="text" name="phone_number" value="{{ old('phone_number') }}" required>
                @if($errors->has('phone_number'))
                <span class="text-danger text-md-left">{{ $errors->first('phone_number') }}</span>
                @endif
            </div>
            <div class="form-group col-md-3">
                <label>IB ID</label>
                <select class="form-control" name="ib_id" required>
                    @foreach($ibIds as $email => $ibId)
                        @if(old('ib_id') == $ibId)
                            <option value="{{ $ibId }}" selected>{{ $ibId }} - {{ $email}}</option>
                        @else
                            <option value="{{ $ibId }}">{{ $ibId }} - {{ $email}}</option>
                        @endif
                    @endforeach
                </select>
                @if($errors->has('ib_id'))
                    <span class="text-danger text-md-left">{{ $errors->first('ib_id') }}</span>
                @endif
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Country</label>
                <select id="country" class="form-control" name="country">
                    <option value="">Choose...</option>
                    @foreach(config('country') as $key => $country)
                        @if(old('country') == $key)
                            <option value="{{ $key }}" selected>{{ $country }}</option>
                        @else
                            <option value="{{ $key }}">{{ $country }}</option>
                        @endif
                    @endforeach
                </select>
                @if($errors->has('country'))
                <span class="text-danger text-md-left">{{ $errors->first('country') }}</span>
                @endif
            </div>
            <div class="form-group col-md-6">
                <label>Application type</label>
                <select name="application_type" id="application_type" class="form-control">
                    <option value="1">Individual</option>
                    <option value="2">Join</option>
                </select>
                @if($errors->has('application_type'))
                <span class="text-danger text-md-left">{{ $errors->first('application_type') }}</span>
                @endif
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
    </form>
</div>

@endsection
