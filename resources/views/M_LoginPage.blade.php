<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/M_login.css') }}">
    <title>Document</title>
</head>
<body>
    <div class="main-container">
        <div class="horizontal-container">
            <div class="banner">
                <img src="{{ asset('images/Login_Banner.png') }}" alt="Banner Image">
            </div>
            <div class="Login-container">
                <div class="Login-header">
                    <p>Login</p>
                </div>
                <form id="Login-form" action="{{ route('login.submit') }}" method="POST">
                    @csrf 
                    <input type="text" id="Email" name="email" placeholder="Email" required>
                    <br>
                    <input type="password" id="Password" name="password" placeholder="Password" required>
                    <br>
                    <button type="submit" id="submitBtn">Login</button>
                </form>
                @if ($errors->any())
                <div class="error-messages">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
            </div>
        </div>
    </div>
</body>
</html>