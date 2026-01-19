<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Registrasi Pemilihan HIMA</title>
</head>
<body>
    <h2>Form Pendaftaran</h2>

    @if(session('status'))
        <p style="color: green;">{{ session('status') }}</p>
    @endif

    <form id="registerForm" action="{{ route('register.submit') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <label>Nama Lengkap:</label><br>
        <input type="text" name="name" value="{{ old('name') }}" required><br>
        @error('name') <small style="color:red">{{ $message }}</small><br> @enderror

        <label>NPM:</label><br>
        <input type="text" name="npm" value="{{ old('npm') }}" placeholder="Contoh: 2207..." required><br>
        @error('npm') <small style="color:red">{{ $message }}</small><br> @enderror

        <label>Angkatan:</label><br>
        <select name="angkatan" required>
            <option value="">Pilih...</option>
            <option value="2022">2022</option>
            <option value="2023">2023</option>
            <option value="2024">2024</option>
            <option value="2025">2025</option>
        </select><br>
        @error('angkatan') <small style="color:red">{{ $message }}</small><br> @enderror

        <label>Email UPN:</label><br>
        <input type="email" name="email" value="{{ old('email') }}" required><br>
        @error('email') <small style="color:red">{{ $message }}</small><br> @enderror

        <label>Password:</label><br>
        <input type="password" name="password" required><br>
        
        <label>Konfirmasi Password:</label><br>
        <input type="password" name="password_confirmation" required><br>
        @error('password') <small style="color:red">{{ $message }}</small><br> @enderror

        {{-- <label>Foto KTM (Max 2MB):</label><br>
        <input type="file" name="foto_ktm" accept="image/*" required><br>
        @error('foto_ktm') <small style="color:red">{{ $message }}</small><br> @enderror --}}

        <br>
        <button type="submit" id="submitBtn">Daftar Sekarang</button>
    </form>

<script>
    const npmInput = document.querySelector('input[name="npm"]');
    const angkatanSelect = document.querySelector('select[name="angkatan"]');

    npmInput.addEventListener('input', function() {
        let val = this.value;

        if (val.length >= 2) {
            let duaDigit = val.substring(0, 2);
            let tahunLengkap = "20" + duaDigit;
            let optionExists = [...angkatanSelect.options].some(o => o.value === tahunLengkap);

            if (optionExists) {
                angkatanSelect.value = tahunLengkap;
                
            } else {
                angkatanSelect.value = "";
            }
        }
    });

    document.getElementById('registerForm').addEventListener('submit', function() {
        var btn = document.getElementById('submitBtn');
        btn.innerHTML = 'Sedang Mengirim...';
        btn.disabled = true;
        btn.style.opacity = '0.5';
        btn.style.cursor = 'not-allowed';
    });
</script>
</body>
</html>