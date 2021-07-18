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
        <form method="post" action="{{ route('agent.update', $agent->id) }}">
            @csrf
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>IB ID</label>
                    <input class="form-control" type="text" value="{{ old('ib_id', $agent->ib_id) }}" name="ib_id">
                    @if($errors->has('ib_id'))
                        <span class="text-danger text-md-left">{{ $errors->first('ib_id') }}</span>
                    @endif
                </div>
                <div class="form-group col-md-6">
                    <label>Full name</label>
                    <input class="form-control" type="text" value="{{ old('name', $agent->name) }}" name="name">
                    @if($errors->has('name'))
                        <span class="text-danger text-md-left">{{ $errors->first('name') }}</span>
                    @endif
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Email</label>
                    <input class="form-control" type="text" value="{{ $agent->email }}" disabled>
                </div>
                <div class="form-group col-md-6">
                    <label>Phone number</label>
                    <input class="form-control" type="text" value="{{ old('phone_number', $agent->phone_number) }}"
                           name="phone_number">
                    @if($errors->has('phone_number'))
                        <span class="text-danger text-md-left">{{ $errors->first('phone_number') }}</span>
                    @endif
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Role</label>
                    <select class="form-control" name="role" id="role">
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" @if(old('role', $agent->roles->first()->name) == $role->name) selected @endif>{{ $role->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label>Status</label>
                    <select class="form-control" name="status">
                        <option value="1" @if(old('status', $agent->status) == 1) selected @endif>Verified</option>
                        <option value="2" @if(old('status', $agent->status) == 2) selected @endif>Unverified</option>
                    </select>
                    @if($errors->has('status'))
                        <span class="text-danger text-md-left">{{ $errors->first('status') }}</span>
                    @endif
                </div>
            </div>
            <div class="form-row">
                <div class="belong-manager form-group col-md-6">
                    <label>Manager</label>
                    <select class="form-control" name="admin_id">
                        @if($agent->admin_id == 1))
                        <option value="1">Select one manager</option>
                        @endif
                        @foreach($managers as $manager)
                            <option value="{{ $manager->id }}" @if(old('admin_id', $agent->admin_id) == $manager->id) selected @endif>{{ $manager->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>US Stock Commsion ($/lots)</label>
                    <input class="form-control" type="text" value="{{ old('us_stock_commission', $commission->us_stock_commission) }}"
                           name="us_stock_commission">
                    @if($errors->has('us_stock_commission'))
                        <span class="text-danger text-md-left">{{ $errors->first('us_stock_commission') }}</span>
                    @endif
                </div>
                <div class="form-group col-md-4">
                    <label>Forex Commsion ($/lots)</label>
                    <input class="form-control" type="text" value="{{ old('forex_commission', $commission->forex_commission) }}"
                           name="forex_commission">
                    @if($errors->has('forex_commission'))
                        <span class="text-danger text-md-left">{{ $errors->first('forex_commission') }}</span>
                    @endif
                </div>
                <div class="form-group col-md-4">
                    <label>Other Commsion ($/lots)</label>
                    <input class="form-control" type="text" value="{{ old('other_commission', $commission->other_commission) }}"
                           name="other_commission">
                    @if($errors->has('other_commission'))
                        <span class="text-danger text-md-left">{{ $errors->first('other_commission') }}</span>
                    @endif
                </div>
            </div>
            <div class="form-row staff-commission">
                <div class="form-group col-md-4">
                    <label>Staff US Stock Commsion ($/lots)</label>
                    <input class="form-control" type="text" value="{{ old('staff_us_stock_commission', $commission->staff_us_stock_commission) }}"
                           name="staff_us_stock_commission">
                    @if($errors->has('staff_us_stock_commission'))
                        <span class="text-danger text-md-left">{{ $errors->first('staff_us_stock_commission') }}</span>
                    @endif
                </div>
                <div class="form-group col-md-4">
                    <label>Staff Forex Commsion ($/lots)</label>
                    <input class="form-control" type="text" value="{{ old('staff_forex_commission', $commission->staff_forex_commission) }}"
                           name="staff_forex_commission">
                    @if($errors->has('staff_forex_commission'))
                        <span class="text-danger text-md-left">{{ $errors->first('staff_forex_commission') }}</span>
                    @endif
                </div>
                <div class="form-group col-md-4">
                    <label>Staff Other Commsion ($/lots)</label>
                    <input class="form-control" type="text" value="{{ old('staff_other_commission', $commission->staff_other_commission) }}"
                           name="staff_other_commission">
                    @if($errors->has('staff_other_commission'))
                        <span class="text-danger text-md-left">{{ $errors->first('staff_other_commission') }}</span>
                    @endif
                </div>
            </div>
            <button type="submit" class="btn btn-primary " style="margin-top: 20px">Cập nhật</button>
        </form>
    </div>
@endsection
@section('javascript')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script>
        if ($('#role').val() == 'standardManager') {
            $('.staff-commission').removeClass('hidden');
            $('.belong-manager').addClass('hidden');
        } else {
            $('.staff-commission').addClass('hidden');
            $('.belong-manager').removeClass('hidden');
        }
        $('#role').on('change', function () {
            if ($('#role').val() == 'standardManager') {
                $('.staff-commission').removeClass('hidden');
                $('.belong-manager').addClass('hidden');
            } else {
                $('.staff-commission').addClass('hidden');
                $('.belong-manager').removeClass('hidden');
            }
        })
    </script>
@endsection
