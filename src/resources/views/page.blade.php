<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ShareList</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ url('images/logo.png') }}">
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