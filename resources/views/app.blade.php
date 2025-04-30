<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Niggers</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @auth
        <meta name="user-id" content="{{ auth()->id() }}">
    @endauth
</head>
<body class="antialiased">
    @inertia
</body>
</html>
