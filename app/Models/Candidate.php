<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_urut',
        'nama_ketua',
        'nama_wakil',
        'visi',
        'misi',
        'foto',
    ];

    public function votes()
    {
        return $this->hasMany(VoteRecord::class); // Sesuaikan nama model vote kamu
    }

    // HITUNG SEMUA SUARA (Quick Count - Termasuk Pending)
    // Cara panggil: $candidate->total_suara_masuk
    // public function getTotalSuaraMasukAttribute()
    // {
    //     return $this->votes()->count();
    // }

    // // HITUNG SUARA SAH (Official Result - Penentu Pemenang)
    // // Cara panggil: $candidate->total_suara_sah
    // public function getTotalSuaraSahAttribute()
    // {
    //     return $this->votes()->where('status_suara', 'sah')->count();
    // }
}
