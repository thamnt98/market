<!DOCTYPE html>
<!--
* CoreUI Free Laravel Bootstrap Admin Template
* @version v2.0.1
* @link https://coreui.io
* Copyright (c) 2020 creativeLabs Łukasz Holeczek
* Licensed under MIT (https://coreui.io/license)
-->

<html lang="en">
<head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="author" content="Łukasz Holeczek">
    <title>  -  Forex Brokers | FX Trading Software | While Label</title>
    <meta name="theme-color" content="#ffffff">
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
    <!-- Global site tag (gtag.js) - Google Analytics-->
    <script async="" src="https://www.googletagmanager.com/gtag/js?id=UA-118965717-3"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());
        // Shared ID
        gtag('config', 'UA-118965717-3');
        // Bootstrap ID
        gtag('config', 'UA-118965717-5');
    </script>

    <link href="{{ asset('css/coreui-chartjs.css') }}" rel="stylesheet">

</head>
<body class="c-app">

@yield('content')

<!-- CoreUI and necessary plugins-->
<script src="{{ asset('js/coreui.bundle.min.js') }}"></script>

@yield('javascript')

</body>
</html>
