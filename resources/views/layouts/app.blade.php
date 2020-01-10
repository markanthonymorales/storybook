<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="stripe-key" content="{{ config('services.stripe.key') }}">
    <!-- <meta name="api-token" content="Bearer {{ session('api-token') }}"> -->

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="shortcut icon" href="{{ asset('img/favicon.png') }}">

    <!-- Scripts -->
    <!-- @if(!isset($js))
        @php
            $js='js/app.js'; 
        @endphp
    @endif -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    @yield('custom_js')

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    <link href="{{ asset('css/tailwind.min.css') }}" rel="stylesheet">

    @yield('custom_css')
</head>
<body>
    <!-- @if(!isset($id))
        @php
            $id='app'; 
        @endphp
    @endif -->

    <div id="app" class="bg-white"
    @guest
    style="height: 100%;"
    @endguest
    >
        <nav class=" bg-white">
            <div class="no-margin">
                <div class="" id="navbarSupportedContent">
                    <nav-menu 
                    @auth
                    :auth-data="{{ Auth::user() }}"
                    @else
                    :auth-data="null"
                    @endauth :route-name="'{{ Route::current()->getName() }}'"></nav-menu>
                    @auth
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    @endauth
                </div>
            </div>
        </nav>
        @if(session()->has('download_message'))
            <div class="fixed-bottom alert alert-danger text-center mb-0">
              {{ session()->get('download_message') }}
            </div>
        @endif
        <main 
        @guest
        @else
            @if(!request()->is('profile'))
                class="py-4"
            @endif
        @endguest>
            @yield('content')
        </main>

        <!-- footer -->
        @guest
            <div class="footer flex flex-col bg-gray-400 text-xs p-8 text-center" style="display: none; justify-content: center; align-items: center !important;">
        @else
            <div class="footer flex flex-col bg-gray-400 text-xs p-8 text-center" style="justify-content: center; align-items: center !important;">
        @endguest
            <img src="{{ asset('img/poetray_me-logo.png') }}" style="height: 30px;" class="mb-2">
            <span class="font-normal">
                <?php
                    echo config('custom.class')::getValue('footer');
                ?>
            </span>
        </div>
    </div>    
    @yield('footer')
</body>
</html>
