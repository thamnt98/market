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

        .a_no_active {
            width: 100%;
            display: flex;
            justify-content: space-between;
            color: black;
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
            {{ Breadcrumbs::render('agent-list') }}
        </div>
        <div class="card">
            <div class="card-body">
                <div class="header-agent">
                    <div class="row">
                        <div class="@if ($admin->role == config('role.admin'))col-md-4 col-sm-12 col-xs-12
                        @else col-md-6 col-xs-6 col-sm-6 @endif">
                            <div class="total-agent">
                                <div class="small-box bg-2nd margin-less">
                                    <div class="inner">
                                        <div>
                                            <p>Total agent</p>
                                            <h3>{{ $totalAgents }}</h3>
                                        </div>
                                        <div>
                                            <img class="agent_img" src="{{ asset('/images/count_agent_icon.png') }}"
                                                 alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if ($admin->role == config('role.admin'))
                            <div class="col-md-4 col-sm-12 col-xs-12">
                                <div class="total-manager">
                                    <div class="small-box bg-2nd margin-less">
                                        <div class="inner">
                                            <div>
                                                <p>Total manager</p>
                                                <h3>{{ $agentManagers }}</h3>
                                            </div>
                                            <div>
                                                <img class="agent_img" src="{{ asset('/images/summary.png') }}" alt="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="@if ($admin->role == config('role.admin'))col-md-4 col-sm-12 col-xs-12
                        @else col-md-6 col-xs-6 col-sm-6 @endif">
                            <div class="agent-inactive">
                                <div class="small-box bg-2nd margin-less">
                                    <div class="inner">
                                        <a href="{{ route('agent.list-status-noactive', $admin->id) }}" class="a_no_active">
                                            <div>
                                                <p>Total agent no active</p>
                                                <h3>{{ $agentNoActives }}</h3>
                                            </div>
                                            <div>
                                                <img class="agent_img" src="{{ asset('/images/status_inactive.png') }}"
                                                     alt="">
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($message = Session::get('error'))
                    <div class="alert alert-danger alert-block" style="margin-top: 20px">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ $message }}</strong>
                    </div>
                @endif
                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-block" style="margin-top: 20px">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ $message }}</strong>
                    </div>
                @endif
                <div class="form-search">
                    <form method="get" action="{{ route('agent.list') }}">
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
                <div class="table-responsive" style="margin-top: 30px">
                    <h3 style="margin-bottom: 20px">Manager agent</h3>
                    <table class="table table-striped" data-pagination="true">
                        <thead>
                        <tr>
                            <th scope="col">STT</th>
                            <th scope="col">IB ID</th>
                            <th scope="col">Full Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone number</th>
                            @if ($admin->role == config('role.admin'))
                                <th scope="col">Count staff</th>
                            @endif
                            <th scope="col">Role</th>
                            <th scope="col">Status</th>
                            @if (\Illuminate\Support\Facades\Auth::user()->role == config('role.admin'))
                                <th></th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($data as $key => $agent)
                            <tr>
                                <th scope="row">
                                    {{++$key}}
                                </th>
                                <th scope="row">{{ $agent['ib_id'] }}</th>
                                <th scope="row">{{ $agent['name'] }}</th>
                                <th scope="row">{{ $agent['email'] }}</th>
                                <th scope="row">{{ $agent['phone_number'] }}</th>
                                @if ($admin->role == config('role.admin'))
                                    @if ($agent['count'] > 0)
                                        <th scope="row"><a
                                                href="{{ route('agent.manager-staff', $agent['id']) }}">{{ $agent['count'] }}</a>
                                        </th>
                                    @else
                                        <th scope="row">{{ $agent['count'] }}</th>
                                    @endif
                                @endif
                                <th scope="row">
                                    {{ ($agent->roles->first()->display_name) }}
                                </th>
                                <th>
                                    @if (\Illuminate\Support\Facades\Auth::user()->hasPermissionTo('agent.approve'))
                                        @if ($agent['status'] == 1)
                                            <a style="color:white" class="btn btn-dark bold btn-active"
                                               data-toggle="modal" data-target="#active"
                                               data-id="{{ $agent['id'] }}" data-status="2"
                                               style="width:150px">Verified</a>
                                        @else
                                            <a style="color:white" class="btn btn-success bold btn-active"
                                               data-toggle="modal" data-target="#active"
                                               data-id="{{ $agent['id'] }}" data-status="1"
                                               style="width:150px">Unverified</a>
                                        @endif

                                    @else
                                        @if ($agent['status'] == 1)
                                            <button type="button" class="btn btn-dark" disabled>Verified</button>
                                        @else
                                            <button type="button" class="btn btn-success" disabled>Unverified</button>
                                        @endif
                                    @endif
                                </th>
                                @can('agent.edit')
                                    <th>
                                        <a href="{{ route('agent.detail', $agent['id']) }}"
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
