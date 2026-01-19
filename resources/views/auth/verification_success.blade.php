<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cek Email Anda</title>
    <style>
        body { font-family: sans-serif; text-align: center; padding: 50px; }
        .card { max-width: 500px; margin: auto; border: 1px solid #ddd; padding: 20px; border-radius: 8px; }
        .btn-resend { background: none; border: none; color: blue; text-decoration: underline; cursor: pointer; padding: 0; }
        .alert-success { color: green; background: #e6fffa; padding: 10px; margin-bottom: 10px;}
        .alert-error { color: red; background: #ffe6e6; padding: 10px; margin-bottom: 10px;}
    </style>
</head>
<body>

<div class="card">
    <h2>Registrasi Berhasil!</h2>
    <p>Terima kasih telah mendaftar. Kami telah mengirimkan link aktivasi ke email:</p>
    
    <h3>{{ session('email') ?? request('email') }}</h3>

    <p>Silakan buka inbox atau folder spam email Anda dan klik link tersebut untuk mengaktifkan akun.</p>

    <hr>

    @if (session('status'))
        <div class="alert-success">
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert-error">
            {{ session('error') }}
        </div>
    @endif
    <p>Belum menerima email?</p>

    <form action="{{ route('verification.resend') }}" method="POST" id="verificationResendForm">
        @csrf
        
        <input type="hidden" name="email" value="{{ session('email') ?? old('email') }}">

        @if(!session('email') && !old('email'))
            <input type="email" name="email" placeholder="Masukkan email Anda" required style="margin-bottom:10px; padding:5px;">
            <br>
        @endif

        <button type="submit" class="btn-resend" id="submitBtn">Kirim Ulang Email Aktivasi</button>
    </form>
    
    <br>
    <a href="{{ route('login') }}">Kembali ke Login</a>
</div>
<script>
    document.getElementById('verificationResendForm').addEventListener('submit', function() {
        var btn = document.getElementById('submitBtn');
        btn.innerHTML = 'Sedang Mengirim...';
        btn.disabled = true;            
        btn.style.opacity = '0.5';
        btn.style.cursor = 'not-allowed';
    });
</script>

</body>
</html>