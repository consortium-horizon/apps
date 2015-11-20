    <div class="navbar-default sidebar" role="navigation">
        <div class="sidebar-nav navbar-collapse">
            <ul class="nav" id="side-menu" >
                <!--<li>
                    <a href="{{ URL::to('dashboard') }}"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                </li>-->
                <!--<li>
                    <a href="{{URL::action('ProfileController@showProfile', ['userID' => Auth::user()->id])}}"><i class="fa fa-user fa-fw"></i> Profile<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li>
                            <a href="{{URL::action('ProfileController@showProfile', ['userID' => Auth::user()->id])}}">{{Auth::user()->name}}'s Profile</a>
                        </li>
                        <li>
                            <a href="#">Search a Profile</a>
                        </li>
                    </ul>
                    <!-- /.nav-second-level -->
                <!--</li>-->
                <!--<li>
                    <a href="#"><i class="fa fa-cog fa-fw"></i> Guild Management<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li>
                            <a href="#"><i class="fa fa-users fa-fw"></i> Members management <span class="fa arrow"></span></a>
                            <ul class="nav nav-third-level">
                                <li>
                                    <a href="{{URL::to('members/admin-usergroup')}}"><i class="fa fa-user-plus fa-fw" style="margin-right: 5px"></i>Change member User Group {{$userLevel}}</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-money fa-fw"></i> Finances <span class="fa arrow"></span></a>
                            <ul class="nav nav-third-level">
                                <li>
                                    <a href="{{URL::to('referents/finances/accounting')}}"><i class="glyphicon glyphicon-piggy-bank" style="margin-right: 5px"></i> Accounting</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>-->
                <li>
                    <a href="{{URL::to('participation/dashboard')}}"><i class="fa fa-line-chart fa-fw"></i> Participation<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li>
                            <a href="{{URL::to('participation/dashboard-referents')}}">Dashboard Participation</a>
                        </li>
                        <li>
                            <a href="{{URL::to('participation/new-event')}}">New event</a>
                        </li>
                    </ul>
                    <!-- /.nav-second-level -->
                </li>
            </ul>
        </div>
        <!-- /.sidebar-collapse -->
    </div>
</nav>