<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bilik Suara - Pemira</title>
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- SweetAlert2 untuk Popup Konfirmasi yang Cantik --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 min-h-screen pb-10">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
        
        <div class="text-center mb-10">
            <h1 class="text-3xl font-extrabold text-gray-900">SURAT SUARA ELEKTRONIK</h1>
            <p class="mt-2 text-gray-600">Silakan pilih pasangan calon ketua & wakil ketua himpunan pilihan Anda.</p>
            <div class="mt-4">
                <a href="{{ route('dashboard') }}" class="text-sm text-blue-600 hover:underline">&larr; Kembali ke Dashboard</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 justify-center">
            
            @foreach($candidates as $candidate)
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-shadow duration-300 border border-gray-200 flex flex-col">
                
                <div class="bg-blue-900 text-white text-center py-2 font-bold text-xl">
                    NOMOR URUT {{ $candidate->nomor_urut }}
                </div>

                <div class="h-64 w-full bg-gray-200 overflow-hidden relative group">
                    @if($candidate->foto)
                        <img src="{{ asset('storage/' . $candidate->foto) }}" alt="Paslon {{ $candidate->nomor_urut }}" class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="flex items-center justify-center h-full text-gray-400">
                            <span class="text-6xl font-bold">?</span>
                        </div>
                    @endif
                </div>

                <div class="p-6 flex-grow">
                    <h2 class="text-xl font-bold text-gray-900 text-center mb-1">{{ $candidate->nama_ketua }}</h2>
                    @if($candidate->nama_wakil)
                        <h3 class="text-lg font-semibold text-gray-600 text-center mb-4">& {{ $candidate->nama_wakil }}</h3>
                    @endif

                    <div class="space-y-3 text-sm text-gray-700 mt-4">
                        <div>
                            <strong class="text-blue-900 block uppercase tracking-wide text-xs">Visi:</strong>
                            <p class="italic">"{{ Str::limit($candidate->visi, 100) }}"</p>
                        </div>
                        <div>
                            <strong class="text-blue-900 block uppercase tracking-wide text-xs">Misi:</strong>
                            <ul class="list-disc list-inside pl-1">
                                @foreach(explode("\n", $candidate->misi) as $misi)
                                    @if(trim($misi) != '')
                                        <li>{{ Str::limit($misi, 50) }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-gray-50 border-t border-gray-100 mt-auto">
                    <form action="{{ route('voting.store') }}" method="POST" id="form-vote-{{ $candidate->id }}">
                        @csrf
                        <input type="hidden" name="candidate_id" value="{{ $candidate->id }}">
                        
                        <button type="button" onclick="confirmVote({{ $candidate->id }}, '{{ $candidate->nomor_urut }}')" 
                            class="w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 px-4 rounded-lg shadow transition transform active:scale-95">
                            COBLOS NO. {{ $candidate->nomor_urut }}
                        </button>
                    </form>
                </div>
            </div>
            @endforeach

        </div>
    </div>

    <script>
        function confirmVote(id, nomor) {
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Anda akan memilih Paslon Nomor Urut " + nomor + ". Pilihan tidak dapat diubah!",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1e3a8a', // Blue 900
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Saya Yakin!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-vote-' + id).submit();
                }
            })
        }
    </script>

</body>
</html>