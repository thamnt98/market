@extends('layouts.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap4.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css" />
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
        @if($isAdmin)
            <a style="margin-bottom: 40px" href="{{ route('account.live.create', 0) }}" class="btn btn-info">Thêm mới</a>
        @endif
        <div class="form-search row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <form method="get" action="{{ route('account.live') }}">
                    @csrf
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <input class="form-control" type="text" name="email" value="{{ $data['email'] ?? '' }}"
                                   style="height: 40px" placeholder="Email">
                        </div>
                        <div class="form-group col-md-4">
                            <input class="form-control" type="text" name="login" value="{{ $data['login'] ?? '' }}"
                                   style="height: 40px" placeholder="Login">
                        </div>
                        <div class="form-group col-md-4">
                            <input class="form-control" type="text" name="ib_id" value="{{ $data['ib_id'] ?? '' }}"
                                   style="height: 40px" placeholder="IB ID">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" style="margin-top: 10px">Search</button>
                    @can('deposit.create')
                        <a href="{{route('account.create_deposit')}}" class="btn btn-success btn-button " style="margin-top: 10px">Deposit</a>
                    @endcan
                    @can('withdrawal.create')
                        <a href="{{route('account.create_withdrawal')}}" class="btn btn-info btn-button" style="margin-top: 10px">Withdrawal</a>
                    @endcan
                </form>
            </div>
            <div class="col-md-2"></div>
        </div>
        <div class="table-responsive" style="margin-top: 70px">
            <table class="table table-striped"  id="example" data-pagination="true">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Login</th>
                    <th scope="col">IB ID</th>
                    @if(\Illuminate\Support\Facades\Auth::user()->hasAnyRole('admin', 'superAdmin'))
                        <th scope="col">Balance</th>
                        <th scope="col">Equity</th>
                    @endif
                    <th scope="col">Email</th>
                    <th scope="col">Full Name</th>
                    <th scope="col">Group</th>
                    <th scope="col">Leverage</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($accountList as $key => $account)
F                    <tr>
                        <th scope="row">{{ $key + 1 }}</th>
                        <td>{{ $account->login }}</td>
                        <td>{{ $account->ib_id }}</td>
                        @if(\Illuminate\Support\Facades\Auth::user()->hasAnyRole('admin', 'superAdmin'))
                            <td>{{ $account->mt5->oInfo->Balance }}</td>
                            <td>{{ $account->mt5->oAccount->Equity }}</td>
                        @endif
                        <td>{{ $account->user ? $account->user->email : 'Người dùng này đã bị xóa' }}</td>
                        <td>{{ $account->user ? $account->user->full_name   : "Người dùng này đã bị xóa" }}</td>
                        <td>{{ $account->group }}</td>
                        <td>{{ $account->leverage }}</td>
                        <td style="width: 30px">
                            <a href="{{ route('account.live.detail', $account->id) }}"
                               class="btn btn-sm btn-success bold uppercase" title="Edit"><i class="fa fa-edit"></i>
                            </a>
                        </td>
                    </tr>

                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('javascript')
    <script type="text/javascript" src="{{ asset('js/jquery.min.js') }}" ></script>
    <script type="text/javascript" src=" https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src=" https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#example').DataTable(
                {
                    searching:false,
                    columnDefs : [
                        { targets: 0, sortable: false},
                    ],
                    order: [[ 1, "asc" ]]
                }
            );
        } );
    </script>
@endsection
