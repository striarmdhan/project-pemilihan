<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\User;
use App\Models\VoteRecord;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Cloudinary\Cloudinary;

use function PHPSTORM_META\type;

class VotingController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Cek apakah sudah vote
        if ($user->has_voted) {
            return redirect()->route('dashboard');
        }

        // 2. CEK FOTO (Logika Baru)
        if (empty($user->foto_ktm) || empty($user->foto_diri)) {
            return redirect()->route('dashboard')->with('warning', 'Silakan lengkapi data foto terlebih dahulu.');
        }

        $candidates = Candidate::orderBy('nomor_urut', 'asc')->get();
        return view('voting.index', compact('candidates'));
    }

    // PAKE INI composer require cloudinary/cloudinary_php
    public function uploadData(Request $request)
    {
        $request->validate([
            'foto_ktm' => 'required|image|max:5120',
            'foto_diri' => 'required|image|max:5120',
        ]);

        $user = User::find(Auth::id());

        // Inisialisasi Cloudinary Native SDK
        $cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => config('cloudinary.cloud_name'),
                'api_key'    => config('cloudinary.api_key'),
                'api_secret' => config('cloudinary.api_secret'),
            ],
        ]);

        try {
            // === Upload KTM ===
            if ($request->hasFile('foto_ktm')) {
                $resultKtm = $cloudinary->uploadApi()->upload(
                    $request->file('foto_ktm')->getRealPath(),
                    [
                        'folder' => 'pemira/ktm',
                        'type' => 'upload',          
                        'resource_type' => 'image',
                    ]
                );

                $user->foto_ktm = $resultKtm['secure_url'];
            }

            // === Upload Selfie ===
            if ($request->hasFile('foto_diri')) {
                $resultSelfie = $cloudinary->uploadApi()->upload(
                    $request->file('foto_diri')->getRealPath(),
                    [
                        'folder' => 'pemira/selfie',
                        'type' => 'upload',
                        'resource_type' => 'image'
                        
                    ]
                );

                $user->foto_diri = $resultSelfie['secure_url'];
            }

            $user->save();
        } catch (\Throwable $e) {
            Log::error('Cloudinary upload error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return back()->withErrors('Upload gagal, silakan coba lagi');
        }

        return redirect()->route('dashboard')
            ->with('success', 'Data berhasil dilengkapi');
    }

    public function store(Request $request)
    {
        $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
        ]);

        $user = Auth::user();

        if ($user->has_voted) {
            return redirect()->route('dashboard')->with('error', 'Anda sudah menggunakan hak suara!');
        }

        DB::beginTransaction();

        try {
            // 1. Tambahkan 1 suara ke hitungan cepat (Quick Count) di tabel Candidate
            // $candidate = Candidate::where('id', $request->candidate_id)->lockForUpdate()->first();
            // $candidate->increment('suara');

            // 2. REKAM JEJAK PILIHAN (Logika Baru)
            // Menyimpan data: User A memilih Calon B
            VoteRecord::create([
                'user_id' => $user->id,
                'candidate_id' => $request->candidate_id
            ]);

            // 3. Update status user
            $userToUpdate = User::find($user->id);
            $userToUpdate->has_voted = true;
            $userToUpdate->save();

            DB::commit();

            return redirect()->route('dashboard')->with('success', 'Terima kasih! Suara Anda telah direkam.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }
}
