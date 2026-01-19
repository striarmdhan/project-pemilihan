<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\VotingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

Route::get('/activation/{token}', [AuthController::class, 'verifyAccount'])->name('auth.verify');
Route::get('/verification-sent', [AuthController::class, 'showVerificationSent'])->name('verification.sent');
Route::post('/resend-verification', [AuthController::class, 'resendVerification'])->name('verification.resend');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'updatePassword'])->name('password.update');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    Route::controller(VotingController::class)->group(function () {
        Route::get('/pemilihan', 'index')->name('voting.index');
        Route::post('/pemilihan', 'store')->name('voting.store');
    });

    Route::post('/upload-data-pemilih', [VotingController::class, 'uploadData'])->name('voting.upload_data');
});

Route::middleware(['auth', 'admin'])->prefix('panitia')->group(function () {

    // Halaman Dashboard Panitia
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    // Aksi Verifikasi (Tombol Centang/Silang)
    Route::post('/verifikasi/{id}/{status}', [AdminController::class, 'verifikasi'])->name('admin.verifikasi');
});


// Route::get('/test-email', function () {
//     try {
//         Mail::raw('Halo! Ini test email dari Laravel Brevo.', function ($msg) {
//             $msg->to('22082010217@student.upnjatim.ac.id') // Ganti email tujuan
//                 ->subject('Test Koneksi Brevo Berhasil');
//         });
//         return 'Email berhasil dikirim! Cek inbox/spam.';
//     } catch (\Exception $e) {
//         return 'Gagal kirim email: ' . $e->getMessage();
//     }
// });
