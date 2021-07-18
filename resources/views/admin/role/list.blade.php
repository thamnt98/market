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
        @can('role.create')
            <a style="margin-bottom: 40px" href="{{ route('role.create') }}" class="btn btn-info">Thêm mới</a>
        @endcan
        <div class="table-responsive" style="margin-top: 70px">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th>Role</th>
                    <th>Amount</th>
                </tr>
                </thead>
                <tbody>
                @foreach($roles as $key => $role)
                    <tr>
                        <th scope="row">{{ $key + 1 }}</th>
                        <th>{{ $role->display_name }}</th>
                        <th>{{ $role->amount }}</th>
                        <td style="width: 14%">
                            <a href="{{ route('role.detail', $role->id) }}"
                               class="btn btn-sm btn-success bold uppercase"
                               title="Edit"><i class="fa fa-edit"></i> </a>
                        </td>
                    </tr>
                    <!-- Modal -->
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('javascript')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
@endsection
