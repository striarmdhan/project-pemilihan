<?php

// 1. Panggil Autoloader DULU (Wajib ada!)
require __DIR__ . '/../vendor/autoload.php';

// 2. Baru panggil Bootstrap App
$app = require __DIR__ . '/../bootstrap/app.php';

// 3. Trik Vercel: Pindahkan storage ke folder sementara (/tmp)
// Karena Vercel itu Read-Only, cuma folder /tmp yang boleh ditulis.
$app->useStoragePath('/tmp');

// 4. Jalankan Aplikasi
$request = Illuminate\Http\Request::capture();
$app->handleRequest($request);