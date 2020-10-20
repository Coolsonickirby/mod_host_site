<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <style>
        * {
            font-family: Arial, Helvetica, sans-serif;
        }
    </style>
    <script src="{{ asset('js/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    @yield('header')
</head>

<body>
    <div class="container" style="margin-top: .35%;">
        @include('include/nav')
        <br>    
        @yield('content')
        <br>
    </div>
</body>

</html>