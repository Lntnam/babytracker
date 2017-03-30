<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">

    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">

    <link href="{{ asset('css/app.css') }}" rel="stylesheet" type="text/css">
</head>
<body>

<nav class="navbar fixed-top navbar-toggleable-md navbar-light bg-faded">
    <div class="navbar-header">
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse"
                data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="#">{{ $name }}</a>
    </div>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="{!! route('dashboard') !!}"><i class="fa fa-tachometer"
                                                                         aria-hidden="true"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{!! route('MealReport') !!}"><i class="fa fa-line-chart"
                                                                          aria-hidden="true"></i> Meal Report</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{!! route('SleepReport') !!}"><i class="fa fa-line-chart"
                                                                           aria-hidden="true"></i> Sleep Report</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{!! route('WeightReport') !!}"><i class="fa fa-line-chart"
                                                                            aria-hidden="true"></i> Weight Report</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container">
    @yield('content')
</div>

<script src="{{ asset('js/jquery-3.2.0.min.js') }}"></script>
<script src="{{ asset('js/tether.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
@yield('scripts')
</body>
</html>
