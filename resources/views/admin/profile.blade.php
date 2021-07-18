@extends('layouts.base')
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
        <form method="post" action="{{ route('profile.update') }}">
            @csrf
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Full name</label>
                    <input class="form-control" type="text" value="{{ old('name', $user->name) }}" name="name">
                    @if($errors->has('name'))
                        <span class="text-danger text-md-left">{{ $errors->first('name') }}</span>
                    @endif
                </div>
                <div class="form-group col-md-6">
                    <label>Email</label>
                    <input class="form-control" type="text" value="{{ $user->email }}" disabled>
                </div>

            </div>
            <div class="form-row">
                @if($user->role == config('role.staff'))
                    <div class="form-group col-md-6">
                        <label>IB ID</label>
                        <input class="form-control" type="text" value="{{ old('ib_id', $user->ib_id) }}" name="ib_id">
                        @if($errors->has('ib_id'))
                            <span class="text-danger text-md-left">{{ $errors->first('ib_id') }}</span>
                        @endif
                    </div>
                @endif
                <div class="form-group col-md-6">
                    <label>Phone number</label>
                    <input class="form-control" type="text" value="{{ old('phone_number', $user->phone_number) }}"
                           name="phone_number">
                    @if($errors->has('phone_number'))
                        <span class="text-danger text-md-left">{{ $errors->first('phone_number') }}</span>
                    @endif
                </div>
            </div>
            @if($user->role == config('role.staff'))
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Status</label>
                        <select class="form-control" name="status" disabled>
                            <option value="1" @if(old('status', $user->status) == 1) selected @endif>Verified</option>
                            <option value="2" @if(old('status', $user->status) == 2) selected @endif>Unverified</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Commsion ($/lots)</label>
                        <input class="form-control" type="text" value="{{ old('commission', $user->commission) }}"
                               disabled>
                    </div>
                </div>
                @if(is_null($user->admin_id))
                    <div class="form-row">
                        <div class="staff-commission form-group col-md-6">
                            <label>Staff Commisison ($/lots)</label>
                            <input class="form-control" type="text"
                                   value="{{ old('staff_commission', $user->staff_commission) }}" disabled>
                        </div>
                    </div>
                @endif
            @endif
            <button type="submit" class="btn btn-primary " style="margin-top: 20px">Cập nhật</button>
        </form>
    </div>
@endsection
