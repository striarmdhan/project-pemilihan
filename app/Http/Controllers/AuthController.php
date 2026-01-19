<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PendingRegistration;
use App\Mail\VerifyAccountMail;
use App\Mail\ResetPasswordMail;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    // FORM REGISTER
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // REGISTER
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'npm' => ['required', 'starts_with:2207,2307,2407,2507,2208', 'unique:users,npm'],
            'email' => [
                'required',
                'email',
                'starts_with:2207,2307,2407,2507,2208',
                'ends_with:student.upnjatim.ac.id',
                'unique:users,email',
                function ($attribute, $value, $fail) use ($request) {
                    $emailUser = explode('@', $value)[0];
                    if ($emailUser !== $request->npm) {
                        $fail('Email tidak valid! Email harus sesuai dengan NPM Anda.');
                    }
                },
            ],
            'password' => 'required|min:6|confirmed',
            // 'foto_ktm' => 'required|image|max:2048',
            'angkatan' => [
                'required',
                'numeric',
                'in:2022,2023,2024,2025',
                function ($attribute, $value, $fail) use ($request) {
                    $prefixNpm = substr($request->npm, 0, 2);
                    $suffixAngkatan = substr($value, -2);
                    if ($prefixNpm !== $suffixAngkatan) {
                        $fail("Angkatan yang dipilih ($value) tidak sesuai dengan NPM Anda ($prefixNpm).");
                    }
                },
            ],
        ], [
            'npm.starts_with' => 'Hanya mahasiswa angkatan 2022-2025 prodi ini yang boleh mendaftar.',
            'email.ends_with' => 'Wajib menggunakan email student UPN.',
            'email.starts_with' => 'Hanya mahasiswa fakultas Hukum yang bisa mendaftar',
            'npm.unique' => 'NPM ini sudah terdaftar.',
            'email.unique' => 'Email ini sudah terdaftar.',
        ]);

        $key = 'reg:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) { // Max 3x percobaan per menit
            return back()->withErrors(['email' => 'Terlalu banyak request. Tunggu sebentar.']);
        }

        RateLimiter::hit($key, 60);

        // $pathKtm = $request->file('foto_ktm')->store('ktm_uploads', 'public');

        $oldPending = PendingRegistration::where('email', $request->email)
            ->orWhere('npm', $request->npm)
            ->first();

        $token = Str::random(40);

        if ($oldPending) {
            // Storage::disk('public')->delete($oldPending->foto_ktm);
            // $oldPending->delete();

            $oldPending->update([
                'name' => $request->name,
                'npm' => $request->npm,
                'angkatan' => $request->angkatan,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                // 'foto_ktm' => $pathKtm, 
                'token' => $token,
                'created_at' => now(),
            ]);
        } else {
            PendingRegistration::create([
                'name' => $request->name,
                'npm' => $request->npm,
                'angkatan' => $request->angkatan,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                // 'foto_ktm' => $pathKtm,
                'token' => $token
            ]);
        }

        $link = route('auth.verify', ['token' => $token]); //ambil dari nama route
        Mail::to($request->email)->send(new VerifyAccountMail($link));

        return redirect()->route('verification.sent')->with([
            'email' => $request->email
        ]);
    }

    // HALAMAN UTK VERIFIKASI EMAIL
    public function showVerificationSent()
    {
        return view('auth.verification_success');
    }

    // KIRIM VERIFIKASI EMAIL ULANG
    public function resendVerification(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $dailyKey = 'resend-daily:' . $request->email;

        if (RateLimiter::tooManyAttempts($dailyKey, 3)) {
            $seconds = RateLimiter::availableIn($dailyKey);
            $hours = ceil($seconds / 3600);
            return back()->with('error', "Batas harian tercapai (3x). Silakan coba lagi dalam $hours jam.");
        }

        $throttleKey = 'resend-verif:' . $request->ip() . '|' . $request->email;

        if (RateLimiter::tooManyAttempts($throttleKey, 1)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->with('error', "Tunggu $seconds detik sebelum kirim ulang.");
        }

        $pendingUser = PendingRegistration::where('email', $request->email)->first();

        $alreadyActive = User::where('email', $request->email)->exists();

        if ($alreadyActive) {
            return redirect()->route('login')->with('status', 'Akun ini sudah aktif. Silakan login.');
        }

        if (!$pendingUser) {
            return back()->with('error', 'Data pendaftaran tidak ditemukan.');
        }

        $newToken = Str::random(40);
        $pendingUser->update(['token' => $newToken]);

        $link = route('auth.verify', ['token' => $newToken]);
        Mail::to($pendingUser->email)->send(new VerifyAccountMail($link));

        RateLimiter::hit($throttleKey, 60);
        RateLimiter::hit($dailyKey, 86400);

        return back()->with('status', 'Email aktivasi berhasil dikirim ulang! Cek inbox/spam.');
    }

    // VERIFIKASI AKUN
    public function verifyAccount($token)
    {
        $pending = PendingRegistration::where('token', $token)->first();

        if (!$pending) {
            return redirect()->route('login')->with('error', 'Link kadaluarsa atau tidak valid.');
        }

        if ($pending->updated_at->addHour()->isPast()) {
            return redirect()->route('login')->with('error', 'Link verifikasi sudah kadaluarsa. Silakan lakukan "Kirim Ulang Email".');
        }

        $user = new User();
        $user->name = $pending->name;
        $user->npm = $pending->npm;
        $user->angkatan = $pending->angkatan;
        $user->email = $pending->email;
        $user->password = $pending->password;
        $user->foto_ktm = null;
        $user->foto_diri = null;

        $user->email_verified_at = now();
        $user->has_voted = false;

        $user->save();

        $pending->delete();

        return redirect()->route('login')->with('status', 'Akun aktif! Silakan Login.');
    }

    // FORM LOGIN
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        // LOGIC 1: Handle Password Lama (Plain Text) -> Ubah ke Hash
        if ($user && $user->password === $request->password) {
            $user->password = Hash::make($request->password);
            $user->save();

            Auth::login($user);
            $request->session()->regenerate();

            // --- MODIFIKASI DIMULAI DISINI ---
            return $this->redirectBasedOnRole($user);
            // ---------------------------------
        }

        // LOGIC 2: Handle Password Hash (Normal Login)
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // --- MODIFIKASI DIMULAI DISINI ---
            return $this->redirectBasedOnRole(Auth::user());
            // ---------------------------------
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    // Tambahkan function helper ini di bawah function login
    protected function redirectBasedOnRole($user)
    {
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard'); // Arahkan ke Dashboard Panitia
        }

        // Default: Mahasiswa
        return redirect()->intended('dashboard');
    }

    // LOGIN
    // public function login(Request $request)
    // {
    //     $credentials = $request->validate([
    //         'email' => ['required', 'email'],
    //         'password' => ['required'],
    //     ]);

    //     if (Auth::attempt($credentials)) {
    //         $request->session()->regenerate();

    //         return redirect()->intended('dashboard');
    //     }

    //     return back()->withErrors([
    //         'email' => 'Email atau password yang Anda masukkan salah.',
    //     ])->onlyInput('email');
    // }

    // FORM LUPA PASSWORD
    public function showForgotPasswordForm()
    {
        return view('auth.forgot_password');
    }

    // SEND EMAIL RESET PASSWORD
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email'], [
            'email.exists' => 'Email ini tidak terdaftar di sistem kami.'
        ]);

        $dailyKey = 'reset-pass-daily:' . $request->ip() . '|' . $request->email;

        if (RateLimiter::tooManyAttempts($dailyKey, 3)) {
            $seconds = RateLimiter::availableIn($dailyKey);
            $hours = ceil($seconds / 3600);

            return back()->with('error', "Batas harian tercapai. Silakan coba lagi dalam $hours jam.");
        }

        $throttleKey = 'reset-pass-throttle:' . $request->ip() . '|' . $request->email;

        if (RateLimiter::tooManyAttempts($throttleKey, 1)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->with('error', "Tunggu $seconds detik sebelum kirim ulang.");
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        Mail::to($request->email)->send(new ResetPasswordMail($token, $request->email));

        RateLimiter::hit($throttleKey, 60);
        RateLimiter::hit($dailyKey, 86400);

        return back()->with('status', 'Link reset password telah dikirim ke email Anda.');
    }

    // FORM RESET PASSWORD
    public function showResetPasswordForm(Request $request, $token)
    {
        return view('auth.reset_password', ['token' => $token, 'email' => $request->email]);
    }

    // UPDATE PASSWORD
    public function updatePassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6|confirmed',
            'token' => 'required'
        ]);

        $checkToken = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$checkToken) {
            return back()->withErrors(['email' => 'Token tidak valid atau salah email.']);
        }

        $tokenTime = Carbon::parse($checkToken->created_at);
        if ($tokenTime->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'Token sudah kadaluarsa. Silakan minta link baru.']);
        }

        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password)
        ]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'Password berhasil diubah! Silakan login.');
    }

    // LOGOUT
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
