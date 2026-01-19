<?php

// 1. Panggil Autoloader
require __DIR__ . '/../vendor/autoload.php';

// 2. Panggil Bootstrap App
$app = require __DIR__ . '/../bootstrap/app.php';

// 3. SETTINGAN VITAL VERCEL
// Kita pindahkan storage ke /tmp karena folder lain Read-Only
$storagePath = '/tmp/storage';
$app->useStoragePath($storagePath);

// 4. BUAT FOLDER SECARA MANUAL (Ini yang bikin error 500 tadi!)
// Karena /tmp selalu kosong saat start, kita harus buat strukturnya.
if (!is_dir($storagePath)) {
    mkdir($storagePath, 0777, true);
    mkdir($storagePath . '/framework/views', 0777, true);
    mkdir($storagePath . '/framework/cache', 0777, true);
    mkdir($storagePath . '/framework/sessions', 0777, true);
    mkdir($storagePath . '/logs', 0777, true);
}

// 5. Cache Configuration Fix (Opsional tapi membantu)
// Memastikan view path mengarah ke tempat yang benar
$app->config->set('view.compiled', $storagePath . '/framework/views');

// 6. Jalankan Aplikasi
$request = Illuminate\Http\Request::capture();
$app->handleRequest($request);