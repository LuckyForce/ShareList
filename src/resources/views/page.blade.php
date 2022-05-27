<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ShareList</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ url('images/logo.png') }}">

    <!-- Meta Daten -->
    <meta name="twitter:card" content="summary_small_image">
    <meta id="image-src" name="twitter:image:src" content="{{ url('images/logo.png') }}">
    <meta id="discord" name="twitter:image" content="{{ url('images/logo.png') }}">
    <meta id="embed-title" property="og:title" content="ShareList">
    <meta id="embed-desc" property="og:description" content="ShareList, a list easy to share.">
    <meta id="embed-image" property="og:image" content="{{ url('images/logo.png') }}">
    <meta name="theme-color" content="#0fa8d6">

    <!-- Suchmaschinen Meta Daten -->
    <meta name="robots" content="index"/>
    <meta name="description" content="ShareList, a list easy to share." />
    <meta name="keywords" content="Adrian, Schauer, Eichgraben, Österreich, Wien, Niederösterreich, Spengergasse, List, ToDo, ShareList">
</head>
<body>
    <div id="app" class="h-screen flex flex-col">
        <header-component></header-component>
        <div class="color-1 flex-1">
            <router-view></router-view>
        </div>
        <footer-component></footer-component>
    </div>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>