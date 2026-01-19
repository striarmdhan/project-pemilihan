<?php

// Paksa Laravel menggunakan folder /tmp untuk storage di Vercel
$app = require __DIR__ . '/../bootstrap/app.php';

// Trik agar Laravel bisa nulis file (cache/view/log) di serverless
$app->useStoragePath('/tmp');

// Jalankan request
$request = Illuminate\Http\Request::capture();
$app->handleRequest($request);