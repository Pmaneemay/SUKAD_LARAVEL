<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/C_TaskBar.css') }}">
    <script src="{{ asset('js/C_taskbar.js') }}"></script>
    <title>Team Management Page</title>
</head>
<body>
    <x-taskbar />
</body>
</html>