<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PendingRegistration;

class CleanPendingUsers extends Command
{
    protected $signature = 'users:clean-pending';

    // Deskripsi command
    protected $description = 'Menghapus data pendaftaran yang tidak diverifikasi lebih dari 24 jam';

    public function handle()
    {
        // Logika penghapusan
        $deleted = PendingRegistration::where('created_at', '<', now()->subHours(24))->delete();
        
        $this->info("Berhasil menghapus $deleted data pendaftaran yang tidak terverifikasi selama 24 jam.");
    }
}
