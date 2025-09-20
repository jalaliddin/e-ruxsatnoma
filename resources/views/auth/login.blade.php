<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>Kirish - Urganchtransgaz UK</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        body {
            background-color: #f3f4f6; /* Tailwind gray-100 */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            max-width: 400px;
            margin: 6rem auto;
            background: white;
            padding: 2rem 2.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
            text-align: center;
        }
        .logo {
            width: 120px;
            margin: 0 auto 1rem;
        }
        .system-name {
            font-weight: 700;
            font-size: 1.5rem;
            color: #2563eb; /* Tailwind blue-600 */
            margin-bottom: 2rem;
        }
        input[type=email], input[type=password] {
            width: 100%;
            padding: 0.5rem 0.75rem;
            margin-bottom: 1rem;
            border: 1px solid #d1d5db; /* gray-300 */
            border-radius: 0.375rem;
            outline: none;
            font-size: 1rem;
            box-sizing: border-box;
        }
        input[type=email]:focus, input[type=password]:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
        }
        button {
            width: 100%;
            background-color: #2563eb;
            color: white;
            font-weight: 600;
            padding: 0.75rem 0;
            border-radius: 0.375rem;
            cursor: pointer;
            border: none;
            font-size: 1rem;
            transition: background-color 0.3s ease;
            box-sizing: border-box;
        }
        button:hover {
            background-color: #1d4ed8;
        }
        .error-text {
            color: #dc2626; /* red-600 */
            font-size: 0.875rem;
            margin-top: -0.75rem;
            margin-bottom: 0.75rem;
            text-align: left;
        }
        .links {
            margin-top: 1rem;
            font-size: 0.875rem;
        }
        .links a {
            color: #2563eb;
            text-decoration: none;
            margin: 0 0.5rem;
        }
        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        {{-- Logo --}}
        <img src="{{ asset('images/logo.png') }}" alt="Urganchtransgaz UK Logo" class="logo">

        {{-- Tizim nomi --}}
        <div class="system-name">"Urganchtransgaz" UK <br> E-Ruxsatnomalar Byurosi</div>

        {{-- Login form --}}
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Email">
            @error('email')
                <div class="error-text">{{ $message }}</div>
            @enderror

            <input id="password" type="password" name="password" required placeholder="Parol">
            @error('password')
                <div class="error-text">{{ $message }}</div>
            @enderror

            <button type="submit">Kirish</button>
        </form>

        <div class="links">
            <!-- <a href="{{ route('password.request') }}">Parolni unutdingizmi?</a> -->
            <!-- <a href="{{ route('register') }}">Ro‘yxatdan o‘tish</a> -->
        </div>
    </div>
</body>
</html>
