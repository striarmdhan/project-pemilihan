<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body { font-family: sans-serif; padding: 50px; text-align: center; }
        .alert-success { color: green; background: #e6fffa; padding: 10px; border: 1px solid green; margin-bottom: 15px; }
        .alert-error { color: red; background: #ffe6e6; padding: 10px; border: 1px solid red; margin-bottom: 15px; }
        input { padding: 8px; margin: 5px; width: 250px; }
        button { padding: 10px 20px; cursor: pointer; }
    </style>
</head>
<body>

    <h2>Login Peserta</h2>

    {{-- MENAMPILKAN PESAN SUKSES (Dari Redirect Register/Verifikasi) --}}
    @if(session('status'))
        <div class="alert-success">
            {{ session('status') }}
        </div>
    @endif

    {{-- MENAMPILKAN PESAN ERROR (Jika link verifikasi salah/expire) --}}
    @if(session('error'))
        <div class="alert-error">
            {{ session('error') }}
        </div>
    @endif
    
    {{-- MENAMPILKAN ERROR LOGIN (Password salah) --}}
    @error('email')
        <div class="alert-error">
            {{ $message }}
        </div>
    @enderror

    <form action="{{ route('login.submit') }}" method="POST">
        @csrf
        
        <label>Email Student:</label><br>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus>
        <br>

        <label>Password:</label><br>
        <input type="password" name="password" required>
        <br><br>

        <a href="{{ route('password.request') }}">Lupa Password?</a>
        <br><br>
        <button type="submit">MASUK</button>
    </form>

    <p>Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a></p>

</body>
</html>