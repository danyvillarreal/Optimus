<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Optimus</title>
    <!-- <title>{{ config('app.name', 'Laravel') }}</title> -->
    <!-- <script src="{{ asset('js/app.js') }}" defer></script> -->
    <script src="{{ asset('js/app.js') }}"></script>
    <!-- <script src="{{ asset('js/quote.js') }}"></script> -->

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">

  <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script> -->
<!-- 
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/js/select2.min.js"></script>
 -->
    @stack('quote')
    @stack('account')
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/toast.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <div class="spinner-border whole-page-overlay" id="spinner" style="width: 4rem; height: 4rem;" role="status">
            <span class="sr-only center-loader">Loading...</span>
        </div>
        <!-- <div class="whole-page-overlay" id="whole_page_loader">
            <img class="center-loader"  style="height:100px;" src="https://flevix.com/wp-content/uploads/2019/07/Ajax-Preloader.gif"/>
        </div> -->
        <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
            <div class="container">

                @guest
                    <img class="img-responsive" height="50px" src="<?php echo asset('storage/Libardo Henao.jpg'); ?>" alt=""/>
                @else
                    <img class="img-responsive" height="50px" src="{{ url('uploads/_logo') }}" alt=""/>
                @endguest
                <!-- <img class="img-responsive" height="50px" src="<?php // echo asset('storage/Libardo Henao.jpg') ?>" alt=""/> -->
                <!-- <img class="img-responsive" height="50px" src="{{Storage::disk('local')->url('1580658379Libardo Henao.jpg')}}" alt=""/> -->

                <a class="navbar-brand" href="{{ url('/') }}">
                    Optimus
                    <!-- {{ config('app.name', 'Laravel') }} -->
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                    </ul>
                    <ul class="navbar-nav ml-auto">
                        @guest
                            <!-- <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif -->
                        @else
                            <!-- <li class="nav-item dropdown"> -->
                                <!-- <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Devi<b class="caret"></b></a> -->
                                <!-- <ul class="dropdown-menu"> -->
                                    <!-- <li class=""><a class="nav-link" href="{{action('QuoteController@index')}}">Crear</a></li> -->
                                <!-- </ul> -->
                            <!-- </li> -->
                            <li class=""><a class="nav-link" href="{{action('QuoteController@quotes')}}">Quotes</a></li>
                            <li class="nav-item dropdown">
                                <li class=""><a class="nav-link" href="{{action('QuoteController@invoices')}}">Invoices</a></li>
                            </li>
                            <li class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Management<b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li class="nav-item"><a class="nav-link" href="{{action('AccountController@index')}}">Accounts</a></li>
                                    <li class="nav-item"><a class="nav-link" href="{{action('ProductController@index')}}">Products</a></li>
                                    <li class="nav-item"><a class="nav-link" href="{{action('CategoryController@index')}}">Categories</a></li>
                                    <li class="nav-item"><a class="nav-link" href="{{action('PlaceController@index')}}">Places</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Reports<b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li class=""><a class="nav-link" href="{{action('ReportController@index')}}">Quotes</a></li>
                                    <li class=""><a class="nav-link" href="{{action('ProductController@index')}}">Invoices</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">System<b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li class=""><a class="nav-link" href="{{action('DocumentTypeController@index')}}">Document types</a></li>
                                    <li class=""><a class="nav-link" href="{{action('RecordTypeController@index')}}">Record Types</a></li>
                                    <li class=""><a class="nav-link" href="{{action('UserController@index')}}">Users</a></li>
                                    <li class=""><a class="nav-link" href="{{action('OrganizationController@index')}}">Organization</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
        <main class="">
            @yield('content')
        </main>
    </div>
</body>
</html>
