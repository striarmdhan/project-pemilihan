<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Lupa Password</title>
</head>
<body>
    <h2>Lupa Password</h2>

    @if(session('status'))
        <p style="color: green">{{ session('status') }}</p>
    @endif

    <form action="{{ route('password.email') }}" method="POST" id="forgotPasswordForm">
        @csrf
        <label>Masukkan Email Terdaftar:</label><br>
        <input type="email" name="email" required>
        @error('email') <span style="color:red">{{ $message }}</span> @enderror
        <br><br>
        <button type="submit" id="submitBtn">Kirim Link Reset</button>
    </form>

    <script>
        document.getElementById('forgotPasswordForm').addEventListener('submit', function() {
            var btn = document.getElementById('submitBtn');
            
            // Ubah tulisan tombol
            btn.innerHTML = 'Sedang Mengirim...';
            
            // Matikan tombol agar tidak bisa diklik lagi
            btn.disabled = true;
            
            // (Opsional) Tambahkan sedikit styling biar terlihat "mati"
            btn.style.opacity = '0.5';
            btn.style.cursor = 'not-allowed';
        });

    </script>
</body>
</html>