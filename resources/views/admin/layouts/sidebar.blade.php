<div class="col-md-3 left_col">
    <div class="left_col scroll-view">
        <div class="navbar nav_title" style="border: 0;">
            <a href="/" class="site_title" target="_blank"><i class="fa fa-diamond"></i> <span>{{ env('APP_NAME') }}</span></a>
        </div>
        <div class="clearfix"></div>
        <!-- menu profile quick info -->
        <div class="profile clearfix">
            <div class="profile_pic">
                <img src="{{ asset('img/img.jpg') }}" class="img-circle profile_img">
            </div>
            <div class="profile_info">
                <span>Welcome,</span>
                <h2>{{ Auth::user()->name }}</h2>
            </div>
            <div class="clearfix"></div>
        </div>
        <!-- /menu profile quick info -->

        <br />

        <!-- sidebar menu -->
        <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
            <div class="menu_section">
                <h3>General</h3>
                <ul class="nav side-menu">
                    <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    @if(auth()->user()->can('clickbank-account-list'))
                    <li><a href="{{ route('admin.clickbank_accounts.index') }}"><i class="fa fa-th-list"></i> {{ trans('cruds.cb_account.title') }}</a></li>
                    @endif

                    @if(auth()->user()->can('contest-list'))
                    <li><a href="{{ route('admin.contests.index') }}"><i class="fa fa-trophy"></i> Contest</a></li>
                    @endif

                    @if(auth()->user()->can('affiliate-list'))
                    <li><a href="{{ route('admin.affiliates.index') }}"><i class="fa fa-users"></i> {{ trans('cruds.affiliate.title') }}</a></li>
                    @endif

                    @if(auth()->user()->can('team-list'))
                    <li><a href="{{ route('admin.teams.index') }}"><i class="fa fa-sitemap"></i> {{ trans('cruds.team.title') }}</a></li>
                    @endif

                    @if(auth()->user()->can('prize-list'))
                    <li><a href="{{ route('admin.prizes.index') }}"><i class="fa fa-gift"></i> {{ trans('cruds.prize.title') }}</a></li>
                    @endif

                    @role('Developer')
                    <li><a href="{{ route('admin.users.index') }}"><i class="fa fa-user"></i> {{ trans('cruds.user.title') }}</a></li>
                    @endrole

                    @role('Developer')
                    <li>
                        <a><i class="fa fa-lock"></i> Roles & Permissions <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="{{ route('admin.permissions.index') }}"> {{ trans('cruds.permission.title') }}</a></li>
                            <li><a href="{{ route('admin.roles.index') }}"> {{ trans('cruds.role.title') }}</a></li>
                        </ul>
                    </li>
                    @endrole
                </ul>
            </div>
        </div>
        <!-- /sidebar menu -->
    </div>
</div>
