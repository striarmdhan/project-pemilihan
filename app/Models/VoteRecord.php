<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoteRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'candidate_id',
    ];

    // Relasi (Opsional, buat nanti kalau mau bikin report siapa milih siapa)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
