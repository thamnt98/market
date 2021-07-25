<div class="c-sidebar-brand">
    <a href="https://go.marketfinexia.com">
        <img class="c-sidebar-brand-full" src="{{ url('images/logo.png') }}"  style="height: 80%; width: 80%; margin-left: 10%"  alt="CoreUI Logo">
    </a>
</div>
<ul class="c-sidebar-nav ps">
    <li class="c-sidebar-nav-item">
        <a class="c-sidebar-nav-link" href="">
            <i class="fa fa-dashboard"></i>
            Dashboard
        </a>
    </li>
    @can('agent.show')
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ route('agent.list') }}">
                <i class="fa fa-user"></i>
                Agent
            </a>
        </li>
    @endcan
    @canany(['user.show', 'all.user.show'])
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ route('user.list') }}">
                <i class="fa fa-users"></i>
                Khách hàng
            </a>
        </li>
    @endcanany
    @canany(['account.show', 'all.account.show'])
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ route('account.live') }}">
                <i class="fa fa-home"></i>
                Tài khoản
            </a>
        </li>
    @endcanany
    @can('role.show')
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ route('role.list') }}">
                <i class="fa fa-cog"></i>
                Vai trò và quyền hạn
            </a>
        </li>
    @endcan
    @canany(['deposit.show', 'all.deposit.show'])
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ route('deposit.list') }}">
                <i class="fa fa-credit-card"></i>
                Deposit
            </a>
        </li>
    @endcanany
    @canany(['withdrawal.show', 'all.withdrawal.show'])
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ route('withdrawal.list') }}">
                <i class="fa fa-credit-card"></i>
                Withdrawal
            </a>
        </li>
    @endcanany
    @can('email.send')
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ route('email.marketing') }}">
                <i class="fa fa-envelope"></i>
                Email marketing
            </a>
        </li>
    @endcan
    @can('report.*')
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ route('report.trade') }}">
                <i class="fa fa-bar-chart" aria-hidden="true"></i>
                Report
            </a>
        </li>
    @endcan
    @can('agent.link')
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ route('agent.link') }}">
                <i class="fa fa-user"></i>
                Agent Link
            </a>
        </li>
    @endcan
    @can('user.link')
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ route('customer.link') }}">
                <i class="fa fa-user"></i>
                Customer Link
            </a>
        </li>
    @endcan
    <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
        <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
    </div>
    <div class="ps__rail-y" style="top: 0px; right: 0px;">
        <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px;"></div>
    </div>
</ul>
</div>
