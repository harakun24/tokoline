<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>TokoLine - {{ $title }}</title>
    <link rel="shortcut icon" href="{{ asset('images/icon-tokoline.png') }}" type="image/x-icon">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="bg-[#e0e0e0] {{ $exclass ?? '' }}">
    {{ $slot }}
</body>

</html>
