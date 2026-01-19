<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Panitia - Verifikasi Suara</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .zoom-img:hover { transform: scale(1.1); }
        .zoom-img { transition: transform 0.2s; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans">

    {{-- NAVBAR --}}
    <nav class="bg-indigo-900 text-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="bg-white text-indigo-900 font-bold p-1 px-2 rounded">PANITIA</div>
                <span class="font-semibold tracking-wide">VERIFIKASI PEMIRA</span>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm hidden md:block">Halo, {{ Auth::user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="bg-red-500 hover:bg-red-600 text-white px-4 py-1.5 rounded text-sm transition font-medium shadow">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    {{-- MAIN CONTENT --}}
    <main class="max-w-7xl mx-auto mt-8 px-4 pb-20">
        
        {{-- Flash Message --}}
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm" role="alert">
                <p class="font-bold">Sukses!</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        {{-- STATISTIK CARDS --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center">
                <p class="text-gray-500 text-xs uppercase font-bold tracking-wider">Total Masuk</p>
                <p class="text-3xl font-extrabold text-gray-800">{{ $stats['total_masuk'] }}</p>
            </div>
            <div class="bg-yellow-50 p-4 rounded-xl shadow-sm border border-yellow-200 flex flex-col items-center justify-center text-center">
                <p class="text-yellow-700 text-xs uppercase font-bold tracking-wider">Perlu Cek</p>
                <p class="text-3xl font-extrabold text-yellow-600">{{ $stats['pending'] }}</p>
            </div>
            <div class="bg-green-50 p-4 rounded-xl shadow-sm border border-green-200 flex flex-col items-center justify-center text-center">
                <p class="text-green-700 text-xs uppercase font-bold tracking-wider">Suara Sah</p>
                <p class="text-3xl font-extrabold text-green-600">{{ $stats['sah'] }}</p>
            </div>
            <div class="bg-red-50 p-4 rounded-xl shadow-sm border border-red-200 flex flex-col items-center justify-center text-center">
                <p class="text-red-700 text-xs uppercase font-bold tracking-wider">Ditolak</p>
                <p class="text-3xl font-extrabold text-red-600">{{ $stats['tidak_sah'] }}</p>
            </div>
        </div>

        {{-- TABEL DATA --}}
        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h2 class="font-bold text-lg text-gray-700">Antrian Verifikasi</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600 text-xs uppercase tracking-wider">
                            <th class="px-6 py-3 font-semibold border-b">Mahasiswa</th>
                            <th class="px-6 py-3 font-semibold border-b text-center">Foto KTM</th>
                            <th class="px-6 py-3 font-semibold border-b text-center">Foto Selfie</th>
                            
                            {{-- [BARU] Kolom Pilihan --}}
                            <th class="px-6 py-3 font-semibold border-b text-center bg-indigo-50 text-indigo-800">
                                Pilihan
                            </th>

                            <th class="px-6 py-3 font-semibold border-b text-center">Status Suara</th>
                            <th class="px-6 py-3 font-semibold border-b text-center">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100">
                        @forelse($records as $record) 
                        <tr class="hover:bg-indigo-50 transition duration-150">
                            {{-- Info Mahasiswa --}}
                            <td class="px-6 py-4 align-middle">
                                <div class="font-bold text-gray-900 text-base">{{ $record->user->name }}</div>
                                <div class="text-gray-500 flex flex-col">
                                    <span>NPM: {{ $record->user->npm }}</span>
                                    <span class="text-xs">Waktu: {{ $record->created_at->format('d M H:i') }}</span>
                                </div>
                            </td>
                            
                            {{-- Foto KTM --}}
                            <td class="px-6 py-4 text-center align-middle">
                                <div class="relative group w-24 h-16 mx-auto bg-gray-200 rounded overflow-hidden cursor-pointer shadow-sm">
                                    <a href="{{ $record->user->foto_ktm }}" target="_blank">
                                        <img 
                                            src="{{ $record->user->foto_ktm }}" 
                                            alt="KTM"
                                            class="w-full h-full object-cover zoom-img"
                                        >
                                    </a>
                                </div>
                            </td>

                            {{-- Foto Selfie --}}
                            <td class="px-6 py-4 text-center align-middle">
                                <div class="relative group w-16 h-16 mx-auto bg-gray-200 rounded-full overflow-hidden cursor-pointer shadow-sm border-2 border-white">
                                    <a href="{{ $record->user->foto_diri }}" target="_blank">
                                        <img 
                                            src="{{ $record->user->foto_diri }}" 
                                            alt="Selfie"
                                            class="w-full h-full object-cover zoom-img"
                                        >
                                    </a>
                                </div>
                            </td>

                            {{-- [BARU] Data Pilihan --}}
                            <td class="px-6 py-4 text-center align-middle bg-indigo-50">
                                @if($record->candidate)
                                    <div class="font-bold text-indigo-900 text-lg">
                                        No. {{ $record->candidate->nomor_urut }}
                                    </div>
                                    <div class="text-xs text-indigo-600 font-medium truncate max-w-[150px] mx-auto">
                                        {{ $record->candidate->nama_ketua }}
                                    </div>
                                @else
                                    <span class="text-red-400 text-xs italic">Data dihapus</span>
                                @endif
                            </td>

                            {{-- Status Badge --}}
                            <td class="px-6 py-4 text-center align-middle">
                                @if($record->status_suara == 'sah')
                                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold border border-green-200">
                                        ✅ SAH
                                    </span>
                                @elseif($record->status_suara == 'tidak_sah')
                                    <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold border border-red-200">
                                        ❌ TIDAK SAH
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold border border-yellow-200 animate-pulse">
                                        ⏳ PENDING
                                    </span>
                                @endif
                            </td>

                            {{-- [MODIFIKASI] Tombol Aksi --}}
                            <td class="px-6 py-4 text-center align-middle">
                                @if($record->status_suara == 'pending')
                                    {{-- Hanya tampil jika status PENDING --}}
                                    <div class="flex justify-center gap-2">
                                        <form action="{{ route('admin.verifikasi', ['id' => $record->id, 'status' => 'sah']) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white p-2 rounded-lg shadow transform hover:scale-110 transition" title="Terima Suara">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.verifikasi', ['id' => $record->id, 'status' => 'tidak_sah']) }}" method="POST">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Yakin ingin menolak suara ini?')" class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-lg shadow transform hover:scale-110 transition" title="Tolak Suara">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    {{-- Jika sudah SAH/TOLAK, tampilkan teks ini --}}
                                    <span class="text-xs text-gray-400 font-medium italic select-none">
                                        Telah diverifikasi
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400 italic">Belum ada suara masuk.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-gray-50">
                {{ $records->links() }}
            </div>
        </div>
    </main>

</body>
</html>