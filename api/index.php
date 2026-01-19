<?php

// 1. Panggil Autoloader
require __DIR__ . '/../vendor/autoload.php';

// 2. Panggil Bootstrap App
$app = require __DIR__ . '/../bootstrap/app.php';

// 3. Trik Vercel: Pindahkan storage ke /tmp
// Kita set path storage UTAMA saja. Laravel otomatis akan cari 
// view, cache, dan log di dalam folder ini.
$storagePath = '/tmp/storage';
$app->useStoragePath($storagePath);

// 4. BUAT FOLDER SECARA MANUAL
// Kita buat folder fisiknya supaya Laravel tidak error saat mau nulis file.
if (!is_dir($storagePath)) {
    mkdir($storagePath, 0777, true);
    mkdir($storagePath . '/framework/views', 0777, true);
    mkdir($storagePath . '/framework/cache', 0777, true);
    mkdir($storagePath . '/framework/sessions', 0777, true);
    mkdir($storagePath . '/logs', 0777, true);
}

// --- BAGIAN YANG DIHAPUS: $app->config->set(...) ---
// Bagian itu yang bikin error ReflectionException tadi. Kita hapus saja.

// 5. Jalankan Aplikasi
$request = Illuminate\Http\Request::capture();
$app->handleRequest($request);