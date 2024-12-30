<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @php
    use Illuminate\Support\Facades\Storage;
    @endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{ asset('/images/AgrishopLogo.png') }}" rel="icon">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/css/adminlte.min.css"> --}}
    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">
    {{-- <link href="public/css/app.css" rel="stylesheet"> --}}
    <script type="text/javascript">
        window.Laravel = {
            csrfToken: "{{ csrf_token() }}",
            jsPermissions: {!! auth()->check() ? auth()->user()->jsPermissions() : 0 !!}
        }
    </script>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper" id="app">
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars"></i></a>
                </li>
            </ul>
        </nav>
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <div class="container ">
                <a href="{{ url('/home') }}">
                <img src="/images/AgrishopLogo.png" alt="AdminLTE Logo" class="user-panel brand-image img-fluid mx-auto"
                    style="opacity: .8">
                </a>
            </div>
            <div class="sidebar">
                <router-link to="/Account" class="nav-link">
                    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                        <div class="image">
                            <img src="/storage/{{substr(Auth::user()->user_photo, 2, -2)}}" onerror="this.src='/images/default_image.png'" class="img-circle elevation-2 mt-2" alt="User Image">
                        </div>
                        <div class="info">
                            <span class="font-weight-bold"
                                style="color:rgb(202, 202, 202)">{{ strtoupper(Auth::user()->name) }}</span> <br>
                            <span style="color:#595959"><small>
                                    @if (Auth::check())
                                        {{ strtoupper(Auth::user()->getRoleNames()->first() ?? 'Not Verified') }}
                                    @endif
                                </small></span>
                        </div>
                    </div>
                </router-link>
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        @if (Auth::check() && strtoupper(Auth::user()->getRoleNames()->first()) === 'SELLER')
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa fa-address-book"></i>
                                <p>
                                    Product Management
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview ml-3">
                                <li class="nav-item">
                                    <router-link to="/Products" class="nav-link">
                                        <i class="nav-icon fas fa fa-cutlery"></i>
                                        <p>Products</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/Replenishment" class="nav-link">
                                        <i class="nav-icon fas fa fa-plus-square"></i>
                                        <p>Replenishment</p>
                                    </router-link>
                                </li>
                                <li class="nav-item has-treeview">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon fas fa fa-tag"></i>
                                        <p>
                                            Others
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview ml-3">
                                        <li class="nav-item">
                                            <router-link to="/Category" class="nav-link">
                                                <i class="far fa-table-list nav-icon"></i>
                                                <p>Category</p>
                                            </router-link>
                                        </li>
                                        <li class="nav-item">
                                            <router-link to="/Measurement" class="nav-link">
                                                <i class="far fa-ruler-combined nav-icon"></i>
                                                <p>Measurement</p>
                                            </router-link>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        @endif
                        @if (Auth::check() && strtoupper(Auth::user()->getRoleNames()->first()) === 'SELLER')
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa fa-bell"></i>
                                <p>
                                    Manage Orders
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview ml-3">
                                <li class="nav-item">
                                    <router-link to="/Purchase" class="nav-link">
                                        <i class="nav-icon fa fa-credit-card"></i>
                                        <p>Purchase Order</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/Order" class="nav-link">
                                        <i class="far fa-ruler-combined nav-icon"></i>
                                        <p>Complete Orders</p>
                                    </router-link>
                                </li>
                            </ul>
                        </li>
                        @endif
                        @if (Auth::check() && strtoupper(Auth::user()->getRoleNames()->first()) === 'SELLER')
                            <li class="nav-item">
                                <router-link to="/Report" class="nav-link">
                                    <i class="nav-icon fas fa-users"></i>
                                    <p>
                                        Reports
                                    </p>
                                </router-link>
                            </li>
                            @endif
                        @can('access user')
                            <li class="nav-item">
                                <router-link to="/users" class="nav-link">
                                    <i class="nav-icon fas fa-users"></i>
                                    <p>
                                        Users
                                    </p>
                                </router-link>
                            </li>
                        @endcan
                        @can('access user')
                            <li class="nav-item">
                                <router-link to="/deliquency" class="nav-link">
                                    <i class="nav-icon fas fa-user-tag"></i>
                                    <p>
                                        Deliquency
                                    </p>
                                </router-link>
                            </li>
                        @endcan
                        @can('access permission')
                            <li class="nav-item">
                                <router-link to="/permission" class="nav-link">
                                    <i class="nav-icon fas fa-user-lock"></i>
                                    <p>
                                        Permission
                                    </p>
                                </router-link>
                            </li>
                        @endcan
                        @can('access role')
                            <li class="nav-item">
                                <router-link to="/role" class="nav-link">
                                    <i class="nav-icon fas fa-user-tag"></i>
                                    <p>
                                        Role
                                    </p>
                                </router-link>
                            </li>
                        @endcan
                        @can('access registrar')
                            <li class="nav-item">
                                <router-link to="/registrar" class="nav-link">
                                    <i class="nav-icon fas fa-user-tag"></i>
                                    <p>
                                        Registrar
                                    </p>
                                </router-link>
                            </li>
                        @endcan
                        @can('access program')
                            <li class="nav-item">
                                <router-link to="/program" class="nav-link">
                                    <i class="nav-icon fas fa-user-tag"></i>
                                    <p>
                                        Program
                                    </p>
                                </router-link>
                            </li>
                        @endcan
                        <li class="nav-item">
                            <a href="{{ route('logout') }}" class="nav-link"
                                onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">
                                <i class="nav-icon fas fa-power-off"></i>
                                <p>
                                    Logout
                                </p>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>
        <div class="content-wrapper">
            {{-- content vue here --}}
            <router-view></router-view>
        </div>
        <footer class="main-footer">
            <div class="float-right d-none d-sm-inline">
                One-Stop Shop for Quality Agricultural Products
            </div>
            Copyright &copy; 2024 <strong>AgriShop.com</strong> All rights
            reserved.
        </footer>
    </div>
    @auth
        <script>
            window.user = @json(auth()->user());
            window.role = @json(auth()->user()->role);
        </script>
    @endauth
    {{-- <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/js/adminlte.min.js"></script> --}}
    <script src="{{ asset('/js/app.js') }}"></script>
    {{-- <script>
        window.Laravel = {!! json_encode([
           'csrfToken' => csrf_token(),
        ]) !!};
    </script> --}}

</body>

</html>
