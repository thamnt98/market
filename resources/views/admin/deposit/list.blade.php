@extends('layouts.base')
@section('css')
    <link href="{{ asset('css/boostrap-datepicker.css') }}" rel="stylesheet">
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
        <div class="form-search row">
            <div class="col-md-1"></div>
            <div class="col-md-10">
                <form method="get" action="{{ route('deposit.list') }}">
                    @csrf
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <input class="form-control" type="text" name="email" value="{{ $data['email'] ?? '' }}"
                                   style="height: 40px" placeholder="Email">
                        </div>
                        <div class="form-group col-md-4">
                            <div class="input-group date" data-provide="datepicker">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" style="background-color: white">
                                        <i class="fa fa-calendar" style="margin-right: 0px"></i>
                                    </span>
                                </div>
                                <input class="form-control" type="text" id="startDate" name="start_date" value="{{ $data['start_date'] ?? '' }}"
                                       style="height: 40px" placeholder="Start date" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <div class="input-group date" data-provide="datepicker">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" style="background-color: white">
                                        <i class="fa fa-calendar" style="margin-right: 0px"></i>
                                    </span>
                                </div>
                                <input class="form-control" type="text" id="endDate" name="end_date" value="{{ $data['end_date'] ?? '' }}"
                                       style="height: 40px" placeholder="End date" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" style="margin-top: 10px">Search</button>
                </form>
            </div>
            <div class="col-md-1"></div>
        </div>
        <div class="table-responsive" style="margin-top: 70px">
            <table class="table table-striped" data-pagination="true">
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
                @foreach($orders as $key => $order)
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
        {!! $orders->appends(request()->input())->links() !!}
    </div>
    <div class="modal fade" id="approve" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Xác nhận</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">Bạn muốn reject hay approve ?</div>
                <div class="modal-footer">
                    <form method="post" id="approve-order">
                        @csrf
                        <a href="#" class="btn btn-secondary btn-reject" data-dismiss="modal" >Reject</a>
                        <a href="#" onclick="$(this).closest('form').submit();" class="btn btn-primary">Approve</a>
                        <div class="input-deposite"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('javascript')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/boostrap-datepicker.js') }}"></script>
    <script>
        $('.btn-approve').on('click', function () {
            let input_value = $(this).parent().parent().find('.form-control').val();
            let currentUrl = window.location.origin
            let id = $(this).attr('data-id');
            $('.btn-reject').attr('data-id', id);
            let redirectUrl = currentUrl + '/admin/deposit/approve/' + id;
            $("#approve-order").attr('action', redirectUrl);
            $('#approve-order').find('.input-deposite').empty();
            let input = '<input type="hidden" name="usd" value ="'+input_value+'">'
            $('#approve-order').find('.input-deposite').append(input);
        })
        $.fn.datepicker.defaults.format = "yyyy/mm/dd";

        $('.btn-reject').on('click', function () {
            let currentUrl = window.location.origin
            let id = $(this).attr('data-id');
            let redirectUrl = currentUrl + '/admin/deposit/reject/' + id;
            $("#approve-order").attr('action', redirectUrl);
            $('#approve-order').submit();
        })

    </script>
@endsection
