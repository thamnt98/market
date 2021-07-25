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
    <section class="mx-2 pb-3">
        <ul class="nav nav-tabs md-tabs" id="myTabMD" role="tablist">
            <li class="nav-item waves-effect waves-light">
                <a class="nav-link active" id="information-tab-md" data-toggle="tab" href="#information-md" role="tab"
                    aria-controls="information-md" aria-selected="true">Information</a>
            </li>
            <li class="nav-item waves-effect waves-light">
                <a class="nav-link" id="account-tab-md" data-toggle="tab" href="#account-md" role="tab"
                    aria-controls="account-md" aria-selected="false">Account</a>
            </li>
            @can('deposit.create')
                <li class="nav-item waves-effect waves-light">
                    <a class="nav-link" id="deposit-tab-md" data-toggle="tab" href="#deposit-md" role="tab"
                       aria-controls="account-md" aria-selected="false">Deposit</a>
                </li>
            @endcan
        </ul>
        <div class="tab-content card pt-5" id="myTabContentMD">
            <div class="tab-pane fade show active" id="information-md" role="tabpanel"
                aria-labelledby="information-tab-md" style="margin:40px">
                <form method="post" action="{{ route('user.update', $user->id) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="firstName">First name</label>
                            <input type="text" class="form-control" id="firstName" name="first_name" @if(!$canEdit) readonly @endif
                                value="{{ old('first_name', $user->first_name) }}">
                            @if($errors->has('first_name'))
                            <span class="text-danger text-md-left">{{ $errors->first('first_name') }}</span>
                            @endif
                        </div>
                        <div class="form-group col-md-6">
                            <label for="lastName">Last name</label>
                            <input type="text" class="form-control" id="lastName" name="last_name" @if(!$canEdit) readonly @endif
                                value="{{ old('last_name', $user->last_name) }}">
                            @if($errors->has('last_name'))
                            <span class="text-danger text-md-left">{{ $errors->first('last_name') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="phoneNumber">Phone Number</label>
                            <input type="text" class="form-control" id="phoneNumber" name="phone_number" @if(!$canEdit) readonly @endif
                                value="{{ old('phone_number', $user->phone_number) }}">
                            @if($errors->has('phone_number'))
                            <span class="text-danger text-md-left">{{ $errors->first('phone_number') }}</span>
                            @endif
                        </div>
                        <div class="form-group col-md-3">
                            <label for="phoneNumber">IB ID</label>
                            <input type="text" class="form-control" id="ib_id" name="ib_id" @if(!$canEdit) readonly @endif
                                   value="{{ old('ib_id', $user->ib_id) }}">
                            @if($errors->has('ib_id'))
                                <span class="text-danger text-md-left">{{ $errors->first('ib_id') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="city">City</label>
                            <input type="text" class="form-control" id="city" name="city" @if(!$canEdit) readonly @endif
                                value="{{ old('city', $user->city) }}">
                            @if($errors->has('city'))
                            <span class="text-danger text-md-left">{{ $errors->first('city') }}</span>
                            @endif
                        </div>
                        <div class="form-group col-md-6">
                            <label for="state">State</label>
                            <input type="text" class="form-control" id="state" name="state" @if(!$canEdit) readonly @endif
                                value="{{ old('state', $user->state) }}">
                            @if($errors->has('state'))
                            <span class="text-danger text-md-left">{{ $errors->first('state') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="zipCode">Zip code</label>
                            <input type="text" class="form-control" id="zipCode" name="zip_code" @if(!$canEdit) readonly @endif
                                value="{{ old('zip_code', $user->zip_code) }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="country">State</label>
                            <select id="country" class="form-control" name="country" @if(!$canEdit) readonly @endif>
                                <option value="">Choose...</option>
                                @foreach(config('country') as $key => $country)
                                @if(old('country', $user->country) == $key)
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
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="address">Address</label>
                            <input type="text" class="form-control" id="address" name="address" @if(!$canEdit) readonly @endif
                                value="{{ old('address', $user->address) }}">
                            @if($errors->has('address'))
                            <span class="text-danger text-md-left">{{ $errors->first('address') }}</span>
                            @endif
                        </div>
                        <div class="form-group col-md-6">
                            <label for="copyOfId">Copy of id</label>
                            <input  class="form-control-file" id="copyOfId" name="copy_of_id" @if(!$canEdit) type="hidden" @else type="file"  @endif>
                            <img style="margin-top:20px; height: 75px" src="{{ $user->copy_of_id ?? asset('images/no-photo.png')}}">
                            @if($errors->has('copy_of_id'))
                            <span class="text-danger text-md-left">{{ $errors->first('copy_of_id') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="proofOfAddress">Proof of address</label>
                            <input class="form-control-file" id="proofOfAddress" name="proof_of_address" @if(!$canEdit) type="hidden" @else type="file" @endif>
                            <img style="margin-top:20px; height: 75px" src="{{ $user->proof_of_address ?? asset('images/no-photo.png')}}">
                            @if($errors->has('proof_of_address'))
                            <span class="text-danger text-md-left">{{ $errors->first('proof_of_address')}}</span>
                            @endif
                        </div>
                        <div class="form-group col-md-6">
                            <label for="addtionFile">Addtional file</label>
                            <input class="form-control-file" id="addtionFile" name="addtional_file" @if(!$canEdit) type="hidden" @else type="file" @endif>
                            <img style="margin-top:20px; height: 75px" src="{{ $user->addtional_file  ?? asset('images/no-photo.png')  }}">
                            @if($errors->has('addtional_file'))
                            <span class="text-danger text-md-left">{{ $errors->first('addtional_file')}}</span>
                            @endif
                        </div>
                    </div>
                    @if($canEdit)
                        <button type="submit" class="btn btn-primary " style="margin-top: 20px">Cập nhật</button>
                    @endif
                </form>
            </div>
            @canany(['account.show', 'all.account.show'])
                <div class="tab-pane fade" id="account-md" role="tabpanel" aria-labelledby="account-tab-md"
                style="margin:40px">
                @if(count($user->liveAccounts) <2)
                    @can('account.create')
                    <a style="margin-bottom: 40px" href="{{ route('account.live.create', $user->id) }}"
                       class="btn btn-info">Thêm mới</a>
                    @endcan
                @endif
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Login</th>
                                <th scope="col">IB ID</th>
                                <th scope="col">Group</th>
                                <th scope="col">Leverage</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->liveAccounts as $key => $liveAccount)
                            <tr>
                                <th scope="row">{{ $key + 1 }}</th>
                                <td>{{ $liveAccount->login }}</td>
                                <td>{{ $liveAccount->ib_id }}</td>
                                <td>{{ $liveAccount->group }}</td>
                                <td>{{ $liveAccount->leverage }}</td>
                                <td style="width: 14%">
                                    <a href="{{ route('account.live.detail', $liveAccount->id) }}"
                                        class="btn btn-sm btn-success bold uppercase" title="Edit"><i
                                            class="fa fa-edit"></i> </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endcanany
            @can('deposit.show')
                <div class="tab-pane fade" id="deposit-md" role="tabpanel" aria-labelledby="deposit-tab-md"
                style="margin:40px">
                    <div class="table-responsive">
                    <table class="table table-striped">
                    <thead>
                <tr>
                    <th scope="col">#</th>
                    <th>Login</th>
                    <th>Email</th>
                    <th>Name</th>
                    <th>Amount Money</th>
                    <th>Usd match</th>
{{--                    <th>Type</th>--}}
                    <th>Transaction Date</th>
                    <th>Bank Name</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @foreach($user->orders as $key => $order)
                    <tr>
                        <th scope="row">{{ $key + 1 }}</th>
                        <td>{{$order->login}}</td>
                        <td>{{ $order->user->email }}</td>
                        <td>{{ $order->user->full_name }}</td>
                        <td>{{ number_format($order->amount_money) }}</td>
                        @if ($order->status != config('deposit.status.pending'))
                            <td>
                                @if ($order->usd == null)
                                    {{round(($order->amount_money)/23000, 2)}}
                                @else
                                    {{number_format($order->usd)}}
                                @endif
                            </td>
                        @else
                            <td><input type="number" class="form-control" value= @if ($order->usd == null)
                                {{round(($order->amount_money)/23000, 2)}}
                                @else
                                {{$order->usd}}
                                @endif maxlength="6" @if ($order->status == 1) disabled @endif></td>
                        @endif
                        <td>{{ $order->created_at }}</td>
                        <td>{{ $order->bank_name }}</td>
                        <td>
                            @if($order->status == config('deposit.status.yes'))
                                <button type="button" class="btn btn-dark"
                                        disabled>{{ config('deposit.status_text')[$order->status] }}</button>
                            @elseif($order->status == config('deposit.status.no'))
                                <button type="button" class="btn btn-danger"
                                        disabled>{{ config('deposit.status_text')[$order->status] }}</button>
                            @else
                                <a style="color:white" class="btn btn-success bold btn-approve" data-toggle="modal"
                                   data-target="#approve" data-id="{{ $order->id }}"
                                   style="width:150px">{{ config('deposit.status_text')[$order->status] }}</a>
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
@can('deposit.approve')
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
