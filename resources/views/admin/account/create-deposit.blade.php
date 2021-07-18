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
        <form method="post" action="{{ route('account.create.deposit') }}">
            @csrf
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Customer</label>
                    <select class="form-control selectpicker" name="customer" id="choose_customer" data-live-search="true">
                        <option value="">Choose one customer</option>
                        @foreach($users as $user)
                            @if(old('customer') == $user->id)
                                <option value="{{ $user->id }}" selected>{{ $user->email . '-' . $user->phone_number }}</option>
                            @else
                                <option value="{{ $user->id }}">{{ $user->email . '-' . $user->phone_number }}</option>
                            @endif
                        @endforeach
                    </select>
                    @if($errors->has('customer'))
                        <span class="text-danger text-md-left">{{ $errors->first('customer') }}</span>
                    @endif
                </div>
                <div class="form-group col-md-6">
                    <label>Login</label>
                    <select class="form-control list_login" name="login">
                        @if (Session::has('listLogin'))
                            @foreach (Session::get('listLogin') as $login)
                                <option value="{{$login}}">{{$login}}</option>
                            @endforeach
                        @endif
                    </select>
                    @if($errors->has('login'))
                        <span class="text-danger text-md-left">{{ $errors->first('login') }}</span>
                    @endif
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Amount money</label>
                    <input type="text" class="form-control" name="amount_money" value="{{old('amount_money')}}" placeholder="Enter money">
                    @if($errors->has('amount_money'))
                        <span class="text-danger text-md-left">{{ $errors->first('amount_money') }}</span>
                    @endif
                </div>
                <div class="form-group col-md-6">
                    <label>Choose payment</label>
                    <select class="form-control" name="type">
                        @foreach(config('mt4.payment') as $key => $type)
                            @if(old('type') == $key)
                                <option value="{{$key}}" selected>{{$type}}</option>
                            @else
                                <option value="{{$key}}">{{$type}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Create deposit</button>
        </form>
    </div>

@endsection

@section('javascript')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script>
        $(document).ready(function(){
            $('#choose_customer').change(function(){
                let value = $(this).val();
                $.ajax({
                    url: "{{route('account.list.login')}}",
                    method : 'post',
                    data : {
                        "_token": "{{ csrf_token() }}",
                        'customer' : value
                    },
                    success : function(response){
                        $('.list_login').empty();
                        $('.list_login').html(response);
                        console.log(response);
                    }
                })
            })
        })
    </script>
@endsection
