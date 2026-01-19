<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\VoteRecord; // Gunakan Model ini

class AdminController extends Controller
{
    public function index()
    {
        // 1. Ambil data VoteRecord (bukan User)
        // Kita gunakan 'with' agar query ringan (Eager Loading)
        $records = VoteRecord::with('user', 'candidate') // Ambil sekaligus data usernya
            ->orderByRaw("CASE WHEN status_suara = 'pending' THEN 1 ELSE 2 END") // Pending di atas
            ->orderBy('created_at', 'desc') // Terbaru
            ->paginate(10);

        // 2. Update Statistik (Hitung dari tabel vote_records)
        $stats = [
            'total_masuk' => VoteRecord::count(),
            'pending'     => VoteRecord::where('status_suara', 'pending')->count(),
            'sah'         => VoteRecord::where('status_suara', 'sah')->count(),
            'tidak_sah'   => VoteRecord::where('status_suara', 'tidak_sah')->count(),
        ];

        return view('admin.dashboard', compact('records', 'stats'));
    }

    public function verifikasi($id, $status)
    {
        // Cari Record berdasarkan ID (ID milik vote_records, bukan user_id)
        $voteRecord = VoteRecord::findOrFail($id);

        if (in_array($status, ['sah', 'tidak_sah'])) {
            $voteRecord->status_suara = $status;
            $voteRecord->save();
        }

        // Ambil nama user untuk pesan notifikasi
        $namaUser = $voteRecord->user->name ?? 'Mahasiswa';

        return redirect()->back()->with('success', "Suara dari {$namaUser} berhasil ditandai " . strtoupper($status));
    }
}
