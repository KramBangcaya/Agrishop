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
            @if (Auth::check() && strtoupper(Auth::user()->getRoleNames()->first()) === 'SELLER')
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-bell"></i>
                        <span class="badge badge-warning navbar-badge">3</span> <!-- Add the number of notifications here -->
                    </a>

                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <!-- Dropdown Header -->
                        <span class="dropdown-item dropdown-header">You have new notifications</span>
                        <!-- Notification Items -->
                        <div class="dropdown-divider"></div>


                        <router-link to="/Purchase" class="dropdown-item">
                            <i class="fas fa-envelope mr-2"></i> New order
                            <span id="order-count" class="badge badge-warning ml-2">0</span>
                        </router-link>
                        <router-link to="/Replenishment" class="dropdown-item">
                            <i class="fas fa-envelope mr-2"></i> Product Replenishment
                            <span id="replenish-count" class="badge badge-warning ml-2">0</span>
                        </router-link>
                    </div>
                </li>
                @endif
                @if (Auth::check() && strtoupper(Auth::user()->getRoleNames()->first()) === 'ADMIN')
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-bell"></i>
                            <span class="badge badge-warning navbar-badge" id="total-notifications">0</span>  <!-- Add the number of notifications here -->
                        </a>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <!-- Dropdown Header -->
                            <span class="dropdown-item dropdown-header">You have new notifications</span>
                            <!-- Notification Items -->
                            <div class="dropdown-divider"></div>

                            <router-link to="/users_buyer" class="dropdown-item">
                                <i class="fas fa-user mr-2"></i> Buyer
                                <span id="buyer-count" class="badge badge-warning ml-2">0</span>
                            </router-link>
                            <router-link to="/users_seller" class="dropdown-item">
                                <i class="fas fa-user mr-2"></i> Seller
                                <span id="seller-count" class="badge badge-warning ml-2">0</span>
                            </router-link>
                            <router-link to="/deliquency" class="dropdown-item">
                                <i class="fas fa-check-circle mr-2"></i> Delinquency
                                <span id="delinquency-count" class="badge badge-warning ml-2">0</span>
                            </router-link>
                        </div>

                    </li>

                    @endif
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
                        <li class="nav-item">
                            <router-link to="/home" class="nav-link">
                                <i class="nav-icon fa-solid fa-house"></i>
                                <p>
                                    Dashboard
                                </p>
                            </router-link>
                        </li>
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
                                        <p>Customer Orders</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/Order" class="nav-link">
                                        <i class="far fa-ruler-combined nav-icon"></i>
                                        <p>Completed Orders</p>
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
                                        Complaints
                                    </p>
                                </router-link>
                            </li>
                            @endif
                        @can('access user')
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-users"></i>
                                    <p>
                                        Users
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview ml-3">
                                    <li class="nav-item">
                                        <router-link to="/users_buyer" class="nav-link">
                                            <i class="nav-icon fa-solid fa-b"></i>
                                            <p>Buyer</p>
                                        </router-link>
                                    </li>
                                    <li class="nav-item">
                                        <router-link to="/users_seller" class="nav-link">
                                            <i class="far fa-solid fa-s nav-icon"></i>
                                            <p>Seller</p>
                                        </router-link>
                                    </li>
                                </ul>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Function to fetch delinquency count from the API
        async function fetchDelinquencyCount() {
            try {
                // Call the API
                const response = await fetch('/deliquency/list_all');
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                // Parse the JSON response
                const data = await response.json();
                // console.log('Full Response:', data);  // Logs the entire response

                // Check if 'data' exists and is an array
                if (data && Array.isArray(data.data)) {
                    // console.log('Array Length:', data.data.length); // Logs the length of the array
                    const count = data.data.length;
                    const countElement = document.getElementById('delinquency-count');
                    if (countElement) {
                        countElement.textContent = count;  // Dynamically update the count
                    }
                } else {
                    console.log('Error: data.data is not an array');
                }
            } catch (error) {
                console.error('Error fetching delinquency count:', error);
            }
        }

        // Call the function on page load
        fetchDelinquencyCount();
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Function to fetch Seller count from the API
        async function fetchSellerCount() {
            try {
                // Call the API
                const response = await fetch('/notif/all_seller');
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                // Parse the JSON response
                const data = await response.json();
                // console.log('Full Response:', data);  // Logs the entire response

                // Check if 'data' exists and is an array
                if (data && Array.isArray(data.data)) {
                    // console.log('Array Length:', data.data.length); // Logs the length of the array
                    const count = data.data.length;
                    const countElement = document.getElementById('seller-count');
                    if (countElement) {
                        countElement.textContent = count;  // Dynamically update the count
                    }
                } else {
                    console.log('Error: data.data is not an array');
                }
            } catch (error) {
                console.error('Error fetching delinquency count:', error);
            }
        }

        // Call the function on page load
        fetchSellerCount();
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Function to fetch Seller count from the API
        async function fetchOrderCount() {
            try {
                // Call the API
                const response = await fetch('/notif/all_seller');
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                // Parse the JSON response
                const data = await response.json();
                // console.log('Full Response:', data);  // Logs the entire response

                // Check if 'data' exists and is an array
                if (data && Array.isArray(data.data)) {
                    // console.log('Array Length:', data.data.length); // Logs the length of the array
                    const count = data.data.length;
                    const countElement = document.getElementById('order-count');
                    if (countElement) {
                        countElement.textContent = count;  // Dynamically update the count
                    }
                } else {
                    console.log('Error: data.data is not an array');
                }
            } catch (error) {
                console.error('Error fetching delinquency count:', error);
            }
        }

        // Call the function on page load
        fetchOrderCount();
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Function to fetch Seller count from the API
        async function fetchReplenishCount() {
            try {
                // Call the API
                const response = await fetch('/products/replenishment_all');
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                // Parse the JSON response
                const data = await response.json();
                // console.log('Full Response:', data);  // Logs the entire response

                // Check if 'data' exists and is an array
                if (data && Array.isArray(data.data)) {
                    // console.log('Array Length:', data.data.length); // Logs the length of the array
                    const count = data.data.length;
                    const countElement = document.getElementById('replenish-count');
                    if (countElement) {
                        countElement.textContent = count;  // Dynamically update the count
                    }
                } else {
                    console.log('Error: data.data is not an array');
                }
            } catch (error) {
                console.error('Error fetching delinquency count:', error);
            }
        }

        // Call the function on page load
        fetchReplenishCount();
    });
</script>
<script>
    // Declare the counts globally to make them accessible in the function
    let buyerCount = 0;
    let sellerCount = 0;
    let delinquencyCount = 0;

    // This function will update the notification bell with the total count
    function updateTotalNotifications() {
        // Log the counts to see the values (for debugging)


        // Initialize a counter for the counts that are not equal to zero
        let nonZeroCount = 0;

        // Increment the counter if each count is greater than zero
        if (buyerCount > 0) nonZeroCount++;
        if (sellerCount > 0) nonZeroCount++;
        if (delinquencyCount > 0) nonZeroCount++;

        // Update the notification bell with the count of non-zero counts
        document.getElementById('total-notifications').textContent = nonZeroCount;
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Function to fetch delinquency count from the API
        async function fetchDelinquencyCount() {
            try {
                const response = await fetch('/deliquency/list_all');
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                const data = await response.json();
                if (data && Array.isArray(data.data)) {
                    delinquencyCount = data.data.length;
                    document.getElementById('delinquency-count').textContent = delinquencyCount;
                    updateTotalNotifications(); // Update total notifications after setting the delinquency count
                }
            } catch (error) {
                console.error('Error fetching delinquency count:', error);
            }
        }

        // Function to fetch seller count from the API
        async function fetchSellerCount() {
            try {
                const response = await fetch('/notif/all_seller');
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                const data = await response.json();
                if (data && Array.isArray(data.data)) {
                    sellerCount = data.data.length;
                    document.getElementById('seller-count').textContent = sellerCount;
                    updateTotalNotifications(); // Update total notifications after setting the seller count
                }
            } catch (error) {
                console.error('Error fetching seller count:', error);
            }
        }

        // Function to fetch buyer count from the API
        async function fetchBuyerCount() {
            try {
                const response = await fetch('/notif/all_buyer');
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                const data = await response.json();
                if (data && Array.isArray(data.data)) {
                    buyerCount = data.data.length;
                    document.getElementById('buyer-count').textContent = buyerCount;
                    updateTotalNotifications(); // Update total notifications after setting the buyer count
                }
            } catch (error) {
                console.error('Error fetching buyer count:', error);
            }
        }

        // Fetch the counts for all categories
        fetchBuyerCount();
        fetchSellerCount();
        fetchDelinquencyCount();
    });
</script>


    {{-- <script>
        window.Laravel = {!! json_encode([
           'csrfToken' => csrf_token(),
        ]) !!};
    </script> --}}

</body>

</html>
