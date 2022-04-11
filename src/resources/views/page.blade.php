<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ShareList</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app" class="items-center bg-gray-100 min-h-screen">
        <header-component></header-component>
        <router-view></router-view>
        <footer-component></footer-component>
    </div>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>