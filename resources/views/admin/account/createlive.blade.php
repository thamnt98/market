@extends('layouts.base')

@section('css')
    <style>
        a.c-sidebar-nav-link{
            height: 50px;
            padding-left: 16px;
            font-size: 13px;
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/js/bootstrap-select.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/css/bootstrap-select.min.css" rel="stylesheet" />
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
    <form method="post" action="{{ route('account.live.open') }}">
        @csrf
        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Customer</label>
                <select class="form-control selectpicker" name="customer"  data-live-search="true">
                    <option value="" selected>Choose one customer</option>
                    @foreach($users as $user)
                        @if(count($user->liveAccounts) <2)
                            @if(old('customer') == $user->id || $id == $user->id)
                                <option value="{{ $user->id }}" selected>{{ $user->email . '-' . $user->phone_number }}</option>
                            @else
                                <option value="{{ $user->id }}">{{ $user->email . '-' . $user->phone_number }}</option>
                            @endif
                        @endif
                    @endforeach
                </select>
                @if($errors->has('customer'))
                <span class="text-danger text-md-left">{{ $errors->first('customer') }}</span>
                @endif
            </div>
            <div class="form-group col-md-6">
                <label>Group</label>
                <select class="form-control" name="group">
                    <option value="">Select one group</option>
                    @foreach($groups as $group)
                        @if(old('group') == $group)
                            <option value="{{$group}}" selected>{{$group}}</option>
                        @else
                            <option value="{{$group}}">{{$group}}</option>
                        @endif
                    @endforeach
                </select>
                @if($errors->has('group'))
                <span class="text-danger text-md-left">{{ $errors->first('group') }}</span>
                @endif
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Leverage</label>
                <select class="form-control" name="leverage">
                    <option value="">Select one leverage</option>
                    @foreach(config('mt4.leverage') as $key => $leverage)
                        @if(old('leverage') == $key)
                            <option value="{{$key}}" selected>{{$leverage}}</option>
                        @else
                            <option value="{{$key}}">{{$leverage}}</option>
                        @endif
                    @endforeach
                </select>
                @if($errors->has('leverage'))
                <span class="text-danger text-md-left">{{ $errors->first('leverage') }}</span>
                @endif
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Open account</button>
    </form>
</div>

@endsection
@section('javascript')
    <script>
        $(function() {
            $('.selectpicker').selectpicker();
        });
    </script>

@endsection
