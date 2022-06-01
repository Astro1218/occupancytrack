<div id="kt_header" class="header  header-fixed ">
    <div class="  d-flex align-items-stretch justify-content-between headerLine">
        <div class="d-flex align-items-stretch">
            <div class="header-logo">
                <a href="{{ route('home') }}">
                    <img alt="Logo" style="max-height: 110px !important;" src="{{ asset('assets/logos/LOOGO-1024x322.png') }}" class="logo-default max-h-40px">
                </a>
            </div>
            <div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
                <div id="kt_header_menu" class="header-menu header-menu-left header-menu-mobile  header-menu-layout-default ">
                    <ul class="menu-nav ">
                        <li class="menu-item  menu-item-submenu menu-item-rel" data-menu-toggle="click" aria-haspopup="true">
                            <a href="{{ route('home') }}" class="gotoPage button-wrapper btn-rounded">
                                <span class="menu-text">
                                    <span class="iconify"><i class="fa fa-home" style="color: #fff;"></i></span>
                                    Home
                                </span>
                                <i class="menu-arrow"></i>
                            </a>
                        </li>

                        <li class="menu-item  menu-item-submenu menu-item-rel" data-menu-toggle="click" aria-haspopup="true">
                            <div class="btn-group headergroup">
                                <button type="button" class="btn btn-rounded dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <span class="iconify"><i style="color: #fff;" class="far fa-user"></i></span>
                                    {{ auth()->user()->name }}
                                </button>
                                <div class="dropdown-menu" x-placement="bottom-start">
                                    <a href="/profile" class="dropdown-item">
                                        <span class="menu-text boxed-btn btn-rounded">
                                            <span class="iconify"><i style="color: #fff;" class="fa fa-user"></i></span>
                                            Profile
                                        </span>
                                        <i class="menu-arrow"></i>
                                    </a>
                                    @if (auth()->user()->leveluser > 0 || auth()->user()->community_id == 10)
                                        <a href="{{ route('usermanage') }}" class="dropdown-item">
                                            <span class="menu-text boxed-btn btn-rounded">
                                                <span class="iconify"><i style="color: #fff;" style="color: #fff;" class="far fa-user-circle"></i></span>
                                                Admin
                                            </span>
                                            <i style="color: #fff;" class="menu-arrow"></i>
                                        </a>
                                    @endif
                                    <div class="dropdown-divider"></div>
                                    <script>
                                        function signoutfunc(event) {
                                            event.preventDefault(); document.getElementById('logout-form').submit();
                                            localStorage.clear();
                                        }
                                    </script>
                                    <a href="javascript:void(0)" class="dropdown-item" onclick="signoutfunc(event)">
                                        <span class="menu-text boxed-btn btn-rounded">
                                            <span class="iconify"><i style="color: #fff;" class="fas fa-power-off"></i></span>
                                            Sign Out
                                        </span>
                                        <i style="color: #fff;" class="menu-arrow"></i>
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
