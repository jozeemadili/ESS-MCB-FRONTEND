<header class="main-nav">
    <div class="sidebar-user text-center">
        {{-- <a class="setting-primary" href="javascript:void(0)"><i data-feather="settings"></i></a> --}}
        <img class="img-50 rounded-circle" src="{{asset('assets/images/dashboard/1.png')}}" alt="" />
        <a href="{{ Route('home') }}"> <h6 class="mt-3 f-14 f-w-600">{{ucfirst(Auth::user()->first_name)}}</h6></a>
        <!-- <p class="mb-0 font-roboto">{{ucfirst(Auth::user()->role)}} ({{Auth::user()->branch_id}})</p> -->
        <!-- <p class="mb-0 font-roboto"> {{Auth::user()->branch_id}}</p> -->
        <p class="mb-0 font-roboto"><small>Mwalimu Comercial Bank</small></p>
        <!-- <p class="mb-0 font-roboto"><small>{{substr(strtoupper(Auth::user()->company->name), 0, 34)}}</small></p> -->
    </div>
    <nav>
        <div class="main-navbar">
            <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
            <div id="mainnav">
                <ul class="nav-menu custom-scrollbar">
                    <li class="back-btn">
                        <div class="mobile-back text-end"><span>Back</span><i class="fa fa-angle-right ps-2" aria-hidden="true"></i></div>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title {{(request()->is('v1/dashboard') || request()->is('v1/summary')) ? 'active' : ''}}" href="javascript:void(0)"><i data-feather="bar-chart"></i><span>Summary</span></a>
                        <ul class="nav-submenu menu-content" style="display: {{ (request()->is('v1/dashboard') || request()->is('v1/summary')) ? 'block' : '' }};">
                            <li><a href="{{route('home')}}" class="{{routeActive('home')}}"> - Dashboard</a></li>
                          </ul>
                    </li>
                    @if(Auth::user()->role == 'ADMIN')
                    <li class="dropdown">
                        <a class="nav-link menu-title {{(request()->is('v1/products/*')) ? 'active' : ''}}" href="javascript:void(0)"><i data-feather="grid"></i><span>Products</span></a>
                        <ul class="nav-submenu menu-content" style="display: {{ (request()->is('v1/products/*')) ? 'block' : '' }};">
                            <li><a href="{{route('products-registration')}}" class="{{routeActive('products-registration')}}"> - Products</a></li>
                        </ul>
                    </li>
                    @endif
                    @if(Auth::user()->role == 'ADMIN' || Auth::user()->role == 'Credit Officer' || Auth::user()->role == 'Credit Admin' || Auth::user()->role == 'Credit Operation' || Auth::user()->role == 'Reports' || Auth::user()->role == 'Branch Operations')
                    <li class="dropdown">
                        <a class="nav-link menu-title {{(request()->is('v1/intermediary/*')) ? 'active' : ''}}" href="javascript:void(0)"><i data-feather="link"></i><span>Loan</span></a>
                        <ul class="nav-submenu menu-content" style="display: {{ (request()->is('v1/intermediary/*')) ? 'block' : '' }};">
                            <li><a href="{{route('loans-pending')}}" class="{{routeActive('loans-pending')}}">  - Waiting CO </a></li>
                            <li><a href="{{route('loans-accepted')}}" class="{{routeActive('loans-accepted')}}"> - Accepted By Bank</a></li> 
                            <li><a href="{{route('loans-approved-ca')}}" class="{{routeActive('loans-approved-ca')}}"> - Waiting CA </a></li>
                            <li><a href="{{route('loans-approved-cm')}}" class="{{routeActive('loans-approved-cm')}}"> - Waiting COP </a></li>
                            <li><a href="{{route('loans-approved')}}" class="{{routeActive('loans-approved')}}"> - All Approved </a></li>
                            <li><a href="{{route('loans-disbursed')}}" class="{{routeActive('loans-disbursed')}}"> - Disbursed</a></li>
                            <li><a href="{{route('posted-cbs')}}" class="{{routeActive('posted-cbs')}}"> - Posted CBS</a></li>
                            <li><a href="{{route('loans-applications')}}" class="{{routeActive('loans-applications')}}"> - All Loans</a></li>
                            <li><a href="{{route('loans-rejected')}}" class="{{routeActive('loans-rejected')}}"> - Rejeted</a></li>
                            <li><a href="{{route('loans-cancelled')}}" class="{{routeActive('loans-cancelled')}}"> - Cancelled</a></li>
                            
                            <!-- <li><a href="{{route('loans-applications-topup')}}" class="{{routeActive('loans-applications-topup')}}"> - TopUp Applications</a></li> -->
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title {{(request()->is('v1/intermediary/*')) ? 'active' : ''}}" href="javascript:void(0)"><i data-feather="link"></i><span>Reports</span></a>
                        <ul class="nav-submenu menu-content" style="display: {{ (request()->is('v1/intermediary/*')) ? 'block' : '' }};">
                            <li><a href="{{route('loans-applications')}}" class="{{routeActive('loans-applications')}}"> - Customise Report</a></li>
                        </ul>
                    </li>
                     @endif
                    <li class="dropdown">
                        <a class="nav-link menu-title {{(request()->is('v1/security/*')) ? 'active' : ''}}" href="javascript:void(0)"><i data-feather="settings"></i><span>Security & Settings</span></a>
                        <ul class="nav-submenu menu-content" style="display: {{ (request()->is('v1/security/*')) ? 'block' : '' }};">
                            <li><a href="{{route('security-user-profile')}}" class="{{routeActive('security-user-profile')}}"> - Your Profile</a></li>
                            @if(Auth::user()->role == 'ADMIN' || Auth::user()->email == 'cmselewa@azaniabank.co.tz'  )
                                <li><a href="{{route('portal-users')}}" class="{{routeActive('portal-users')}}"> - System Users</a></li>
                                <li><a href="{{route('branches-list')}}" class="{{routeActive('branches-list')}}"> - Branches </a></li>
                                @if(Auth::user()->role == 'ADMIN') 
                                    <li><a href="{{route('security-system-configurations')}}" class="{{routeActive('security-system-configurations')}}"> - Configurations</a></li>
                                    <li><a href="{{route('security-system-audit-trail')}}" class="{{routeActive('security-system-audit-trail')}}"> - Audit Trail</a></li>
                                @endif
                            @endif
                        </ul>
                    </li>   
                </ul>
            </div>
            <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
        </div>  
    </nav>
</header>
