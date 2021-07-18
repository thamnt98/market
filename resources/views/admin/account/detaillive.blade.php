@extends('layouts.base')

@section('css')
<link href="{{ asset('css/boostrap-chosen.css') }}" rel="stylesheet">
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
    <section class="mx-2 pb-3">
        <ul class="nav nav-tabs md-tabs" id="myTabMD" role="tablist">
            <li class="nav-item waves-effect waves-light">
                <a class="nav-link active" id="information-tab-md" data-toggle="tab" href="#information-md" role="tab"
                    aria-controls="information-md" aria-selected="true">Information</a>
            </li>
            @can('withdrawal.create')
                <li class="nav-item waves-effect waves-light">
                    <a class="nav-link" id="withdrawal-tab-md" data-toggle="tab" href="#withdrawal-md" role="tab"
                       aria-controls="withdrawal-md" aria-selected="false">Withdrawal</a>
                </li>
            @endcan
        </ul>
        <div class="tab-content card pt-5" id="myTabContentMD">
            <div class="tab-pane fade show active" id="information-md" role="tabpanel"
                aria-labelledby="information-tab-md" style="margin:40px">
                <form method="post" action="{{ route('account.live.update', $account->id) }}">
                    @csrf
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Login</label>
                            <input class="form-control" type="text" value="{{ $account->login }}" disabled>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Group</label>
                            <select class="form-control" name="group" @if(!$canEdit) readonly="" @endif>
                                @foreach($groups as $group)
                                @if(old('group', $account->group) == $group)
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
                            <select class="form-control" name="leverage" @if(!$canEdit) readonly="" @endif>
                                <option value="">Select one leverage</option>
                                @foreach(config('mt4.leverage') as $key => $leverage)
                                @if(old('leverage', $account->leverage) == $key)
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
                        <div class="form-group col-md-3">
                            <label>Phone number</label>
                            <input class="form-control" type="text" name="phone" @if(!$canEdit) readonly="" @endif
                                   value="{{ old('phone', $account->phone_number) }}">
                            @if($errors->has('phone'))
                                <span class="text-danger text-md-left">{{ $errors->first('phone') }}</span>
                            @endif
                        </div>
                        <div class="form-group col-md-3">
                            <label>IB ID</label>
                            <input class="form-control" type="text" name="ib_id"
                                   value="{{ old('ib_id', $account->ib_id) }}">
                            @if($errors->has('ib_id'))
                                <span class="text-danger text-md-left">{{ $errors->first('ib_id') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Full name</label>
                            <input class="form-control" type="text" value="{{ $account->user->full_name }}" disabled>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Zip code</label>
                            <input class="form-control" type="text" value="{{ $account->user->zip_code }}" disabled>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>City</label>
                            <input class="form-control" type="text" value="{{ $account->city }}" disabled>
                        </div>
                        <div class="form-group col-md-6">
                            <label>State </label>
                            <input class="form-control" type="text" value="{{ $account->user->state }}" disabled>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Address</label>
                            <input class="form-control" type="text" value="{{ $account->address }}" disabled>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Country</label>
                            <input class="form-control" type="text" value="{{ $account->user->state }}" disabled>
                            @if($errors->has('country'))
                                <span class="text-danger text-md-left">{{ $errors->first('country') }}</span>
                            @endif
                        </div>
                    </div>
                    @if($canEdit)
                        <button type="submit" class="btn btn-primary">Update</button>
                    @endif
                </form>
            </div>
            @can('withdrawal.show')
                <div class="tab-pane fade" id="withdrawal-md" role="tabpanel" aria-labelledby="withdrawal-tab-md"
                style="margin:40px">
                <div class="table-responsive">
                    <table class="table table-striped">
                    <thead>
                <tr class="text-center">
                    <th scope="col">#</th>
                    <th>Login</th>
                    <th>Email</th>
                    <th>Bank Account</th>
                    <th>Bank Name</th>
                    <th>Account Name</th>
                    <th style="min-width: 200px">Amount Money USD</th>
                    <th>Withdrawal Currency</th>
                    <th>Transaction Date</th>
                    <th>Note</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @foreach($withdrawals as $key => $withdrawal)
                    <tr class="text-center">
                        <th scope="row">{{ $key + 1 }}</th>
                        <td>{{ $withdrawal->login }}</td>
                        <td>{{ $withdrawal->user->email }}</td>
                        <td>{{ $withdrawal->bank_account }}</td>
                        <td>{{ $withdrawal->bank_name }}</td>
                        <td>{{ $withdrawal->account_name }}</td>
                        @if ($withdrawal->status != config('deposit.status.pending'))
                            <td>{{number_format($withdrawal->amount)}}</td>
                        @else
                            <td style="min-width:200px"><input type="number" name="amount" class="form-control" value={{$withdrawal->amount}}></td>
                        @endif

                        <td>{{ $withdrawal->withdrawal_currency }}</td>
                        <td>{{ $withdrawal->created_at }}</td>
                        <th>{{ $withdrawal->note }}</th>
                        <td>
                            @if($withdrawal->status == config('deposit.status.yes'))
                                <button type="button" class="btn btn-dark"
                                        disabled>{{ config('deposit.status_text')[$withdrawal->status] }}</button>
                            @elseif($withdrawal->status == config('deposit.status.no'))
                                <button type="button" class="btn btn-danger"
                                        disabled>{{ config('deposit.status_text')[$withdrawal->status] }}</button>
                            @else
                                <a style="color:white" class="btn btn-success bold btn-approve" data-toggle="modal"
                                   data-target="#approve" data-id="{{ $withdrawal->id }}"
                                   style="width:150px">{{ config('deposit.status_text')[$withdrawal->status] }}</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
                    </table>
                </div>
            </div>
            @endcan
        </div>
    </section>
</div>
@can('withdrawal.approve')
    <div class="modal fade" id="approve" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Xác nhận</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">Bạn có chắc chắn xác nhận không ?</div>
                <div class="modal-footer">
                    <form method="post" id="approve-order">
                        @csrf
                        <a href="#" class="btn btn-secondary" data-dismiss="modal">Hủy</a>
                        <a href="#" onclick="$(this).closest('form').submit();" class="btn btn-primary">Approve</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endcan
@endsection
@section('javascript')
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script>
    $('.btn-approve').on('click', function () {
        let currentUrl = window.location.origin
        let id = $(this).attr('data-id');
        let redirectUrl = currentUrl + '/admin/deposit/approve/' + id;
        $("#approve-order").attr('action', redirectUrl);
    })

</script>
@endsection
