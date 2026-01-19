<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reset Password</title>
</head>
<body>
    <p>Halo,</p>
    <p>Kami menerima permintaan reset password untuk akun Anda.</p>
    <p>Klik link di bawah ini untuk membuat password baru:</p>

    <a href="{{ route('password.reset', ['token' => $token, 'email' => $email]) }}">
        RESET PASSWORD
    </a>

    <p>Jika Anda tidak merasa meminta ini, abaikan saja email ini.</p>
</body>
</html>