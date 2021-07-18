@extends('layouts.base')

@section('content')
    <style>
        .bg-2nd {
            background-color: #f9f9f9;
            border: 1px solid #d2d2d2;
            box-shadow: none;
            color: black;
            border-radius: 10px;
        }

        .small-box>.inner {
            padding: 10px;
        }

        .inner {
            display: -webkit-flex;
            display: flex;
            flex-wrap: nowrap;
            justify-content: space-between;
        }

        .agent_img {
            height: 70px;
            width: 70px;
        }

        .form-search {
            margin-top: 20px;
        }
        .c-main{
            padding-top: 0px;
        }
        .header-breadcrumb{
            font-size: 16px;
        }

    </style>
    <div class="container-fluid">
        <div class="header-breadcrumb">
            {{ Breadcrumbs::render('agent-manager', $agentSearch) }}
        </div>
        <div class="card">
            <div class="card-body">
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
                <div class="form-search">
                    <form method="get" action="{{ route('agent.manager-staff', $agentId) }}">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-4">
                                <input class="form-control" type="text" name="email" value="{{ $search['email'] ?? '' }}"
                                       style="height: 40px" placeholder="Email">
                            </div>
                            <div class="form-group col-md-4">
                                <input class="form-control" type="text" name="ib_id" value="{{ $search['ib_id'] ?? '' }}"
                                       style="height: 40px" placeholder="IB ID">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">Search</button>
                            </div>


                        </div>
                    </form>
                </div>
                <div class="h3_title">
                    <h3>List of agent manager </h3>
                </div>
                <div class="table-responsive" style="margin-top: 30px">
                    <table class="table table-striped" data-pagination="true">
                        <thead>
                        <tr>
                            <th scope="col">STT</th>
                            <th scope="col">IB ID</th>
                            <th scope="col">Full Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone number</th>
                            <th scope="col">Role</th>
                            <th scope="col">Status</th>
                            @can('agent.edit')
                                <th></th>
                            @endcan
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($agents as $key => $agent)
                            <tr>
                                <th scope="row">
                                    {{++$key}}
                                </th>
                                <th scope="row">{{ $agent->ib_id }}</th>
                                <th scope="row">{{ $agent->name }}</th>
                                <th scope="row">{{ $agent->email }}</th>
                                <th scope="row">{{ $agent->phone_number }}</th>
                                <th scope="row">
                                    {{ ($agent->roles->first()->display_name) }}
                                </th>
                                <th>
                                    @if (\Illuminate\Support\Facades\Auth::user()->hasPermissionTo('agent.approve'))
                                    @if ($agent->status == 1)
                                            <a style="color:white" class="btn btn-dark bold btn-active"
                                               data-toggle="modal" data-target="#active"
                                               data-id="{{ $agent->id }}" data-status="2"
                                               style="width:150px">Verified</a>
                                        @else
                                            <a style="color:white" class="btn btn-success bold btn-active"
                                               data-toggle="modal" data-target="#active"
                                               data-id="{{ $agent->id }}" data-status="1"
                                               style="width:150px">Unverified</a>
                                        @endif

                                    @else
                                        @if ($agent->status == 1)
                                            <button type="button" class="btn btn-dark" disabled>Verified</button>
                                        @else
                                            <button type="button" class="btn btn-success" disabled>Unverified</button>
                                        @endif
                                    @endif
                                </th>
                                @can('agent.edit')
                                    <th>
                                        <a href="{{ route('agent.detail', $agent->id) }}"
                                           class="btn btn-sm btn-success bold uppercase" title="Edit"><i
                                                class="fa fa-edit"></i>
                                        </a>
                                    </th>
                                @endcan
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {!! $agents->appends(request()->input())->links() !!}
    </div>
    <div class="modal fade" id="active" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Xác nhận</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <form method="post" id="active-agent">
                        @csrf
                        <a href="#" class="btn btn-secondary" data-dismiss="modal">No</a>
                        <a href="#" onclick="$(this).closest('form').submit();" class="btn btn-primary">Yes</a>
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
        $('.btn-active').on('click', function() {
            let currentUrl = window.location.origin
            let id = $(this).attr('data-id');
            let status = $(this).attr('data-status');
            if (status == 1) {
                $('.modal-body').html('Bạn có muốn kích hoạt người này không ? ')
            } else {
                $('.modal-body').html('Bạn có muốn hủy kích hoạt người này không ? ')
            }
            let redirectUrl = currentUrl + '/admin/agent/active/' + id + '?status=' + status;
            $("#active-agent").attr('action', redirectUrl);
        })

    </script>
@endsection
