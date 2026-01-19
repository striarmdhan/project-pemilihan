<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidate;
use App\Models\VoteRecord;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // 2. LOGIC DATA CHART / QUICK COUNT
        // Ambil kandidat + hitung jumlah suara masuk (relasi voteRecords)
        $candidates = Candidate::withCount('votes as suara')->get();

        // Hitung total seluruh suara masuk
        $total_suara = VoteRecord::count();

        // 3. Kirim ke View
        return view('dashboard', compact('candidates', 'total_suara'));
    }
}
