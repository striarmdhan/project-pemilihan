<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Candidate;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Buat 1 Akun Admin/Panitia
        // User::create([
        //     'name' => 'Panitia Inti',
        //     'npm' => 'admin_pemira',
        //     'angkatan' => '2022',
        //     'email' => 'kpufhupnvjt2026@gmail.com',
        //     'password' => bcrypt('123456'), // Password admin
        //     'role' => 'admin', // ROLE PENTING
        //     'has_voted' => false, // Admin dianggap sudah vote (biar ga ikut nyoblos)
        // ]);

        Candidate::create([
            'nomor_urut' => 1,
            'nama_ketua' => 'Ahmad Fauzi',
            'nama_wakil' => 'Rizky Pratama',
            'visi'       => 'Mewujudkan organisasi mahasiswa yang aktif, inklusif, dan berorientasi pada pengembangan akademik serta karakter.',
            'misi'       => 'Meningkatkan partisipasi mahasiswa dalam kegiatan organisasi dan memperkuat sinergi antar lembaga kemahasiswaan serta mengoptimalkan program kerja yang berdampak langsung.',
            // 'foto'       => 'candidate_1.jpg',
        ]);

        Candidate::create([
            'nomor_urut' => 2,
            'nama_ketua' => 'Dinda Putri',
            'nama_wakil' => 'Muhammad Alif',
            'visi'       => 'Menciptakan lingkungan kampus yang progresif, kolaboratif, dan responsif terhadap aspirasi mahasiswa.',
            'misi'       => 'Membangun sistem komunikasi terbuka antara mahasiswa dan pengurus serta mendorong inovasi program yang relevan dengan kebutuhan akademik dan non-akademik.',
            // 'foto'       => 'candidate_2.jpg',
        ]);
    }
}
