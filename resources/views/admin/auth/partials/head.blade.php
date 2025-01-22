<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="light" data-toggled="close">

<head>
    <!-- Meta Data -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $pageTitle }}</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset("/backend/images/brand-logos/favicon.ico") }}" type="image/x-icon">

    <!-- Main Theme Js -->
    <script src="{{ asset("/backend/js/authentication-main.js") }}"></script>

    <!-- Bootstrap Css -->
    <link id="style" href="{{ asset("/backend/libs/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" >

    <!-- Style Css -->
    <link href="{{ asset("/backend/css/styles.min.css") }}" rel="stylesheet" >

    <!-- Icons Css -->
    <link href="{{ asset("/backend/css/icons.min.css") }}" rel="stylesheet" >

    <!-- Node Waves Css -->
    <link href="{{ asset("/backendlibs/node-waves/waves.min.css") }}" rel="stylesheet" >

    <!-- Simplebar Css -->
    <link href="{{asset("/backendlibs/simplebar/simplebar.min.css")}}" rel="stylesheet" >

    <!-- Choices Css -->
    <link rel="stylesheet" href="{{ asset("/backendlibs/choices.js/public/assets/styles/choices.min.css") }}">

    <!-- Prism CSS -->
    <link rel="stylesheet" href="{{ asset("backendlibs/prismjs/themes/prism-coy.min.css") }}">
</head>
<script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha_v3.siteKey') }}"></script>
    <script>
        function onSubmit(token) {
            document.getElementById("bitForm").submit();
        }
    </script>

<body>
