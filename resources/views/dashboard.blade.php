<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pemira - Fakultas Hukum</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    {{-- NAVBAR --}}
    <nav class="bg-white shadow-sm border-b border-gray-200 z-10 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold text-blue-900">E-Voting UPN</span>
                </div>
                <div class="flex items-center gap-4">
                     <div class="text-right hidden sm:block">
                        <div class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-gray-500">{{ Auth::user()->npm }}</div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-medium transition">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    {{-- MAIN CONTENT --}}
    <main class="max-w-4xl mx-auto mt-10 px-4 pb-20">
        
        {{-- Flash Message --}}
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
                <p class="font-bold">Berhasil!</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif
        @if(session('warning'))
             <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6 rounded shadow-sm">
                <p class="font-bold">Perhatian</p>
                <p>{{ session('warning') }}</p>
            </div>
        @endif

        {{-- Header User --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8 border border-gray-100">
            <div class="p-8 text-center sm:text-left sm:flex sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Selamat Datang, {{ explode(' ', Auth::user()->name)[0] }}!</h1>
                    <p class="mt-2 text-gray-600">Sistem Pemilihan Raya Mahasiswa Fakultas Hukum.</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ Auth::user()->has_voted ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        Status: {{ Auth::user()->has_voted ? 'Sudah Memilih' : 'Belum Memilih' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- KONDISI 1: SUDAH MEMILIH --}}
        @if(Auth::user()->has_voted)
            <div class="bg-green-50 border border-green-200 rounded-xl p-10 text-center shadow-inner">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 mb-6">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-green-900 mb-2">Terima Kasih!</h2>
                <p class="text-green-700 max-w-lg mx-auto text-lg">
                    Suara Anda telah berhasil direkam. Anda dapat logout sekarang.
                </p>
            </div>

        {{-- KONDISI 2: BELUM MEMILIH --}}
        @else
            <div class="grid md:grid-cols-2 gap-6">
                {{-- Panduan --}}
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-lg mb-4 text-gray-800 border-b pb-2">Panduan Pemilihan</h3>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Satu akun hanya satu suara.
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Pilihan bersifat final.
                        </li>
                    </ul>
                </div>

                {{-- Action Button --}}
                <div class="bg-gradient from-blue-900 to-blue-800 p-8 rounded-xl shadow-lg text-white flex flex-col justify-center items-center text-center">
                    <h3 class="font-bold text-2xl mb-2 text-black">Siap Memilih?</h3>
                    <p class="text-blue-900 text-sm mb-8">Gunakan hak suara Anda.</p>
                    
                    {{-- LOGIKA TOMBOL BARU --}}
                    @if(empty(Auth::user()->foto_ktm) || empty(Auth::user()->foto_diri))
                        {{-- Jika Data Belum Lengkap: Muncul Tombol untuk Buka Modal --}}
                        <div class="w-full">
                            <p class="text-xs text-yellow-500 font-semibold mb-2 animate-pulse">
                                ⚠ Data verifikasi belum lengkap
                            </p>
                            <button onclick="openModal()" class="w-full px-6 py-3 bg-yellow-500 text-blue-900 font-bold rounded-lg hover:bg-yellow-400 transition shadow-md">
                                Lengkapi Data & Pilih &rarr;
                            </button>
                        </div>
                    @else
                        {{-- Jika Data Sudah Lengkap: Langsung ke Halaman Vote --}}
                        <a href="{{ route('voting.index') }}" class="w-full sm:w-auto px-8 py-4 bg-white text-blue-900 font-bold rounded-lg hover:bg-gray-100 transition shadow-md flex items-center justify-center gap-2">
                            Mulai Pemilihan 
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    @endif
                </div>
            </div>
        @endif
        <div class="mt-12 mb-8">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="p-6 border-b bg-red-900 text-white">
                    <h3 class="font-bold text-lg flex items-center gap-2">
                        Hasil Quick Count
                    </h3>
                </div>
                <div class="p-6">
                    @if($total_suara > 0)
                        <div class="grid md:grid-cols-2 gap-8 items-center">
                            {{-- 1. BAR CHART (Persentase) --}}
                            <div class="w-full bg-white p-4 rounded-lg shadow">
                                {{-- UBAH class h-64 menjadi h-80 atau h-96 agar Bar Chart terlihat jelas --}}
                                <div class="relative h-96 w-full">
                                    {{-- PENTING: ID disini harus 'barChart' sesuai dengan JS --}}
                                    <canvas id="barChart"></canvas>
                                </div>
                            </div>
                            {{-- 2. DETAIL PROGRESS BAR (Angka Pasti) --}}
                            <div class="space-y-6">
                                <div class="space-y-4">
                                    @foreach($candidates as $candidate)
                                        @php
                                            // Hitung Persentase
                                            $persen = ($candidate->suara / $total_suara) * 100;
                                        @endphp
                                        <div>
                                            <div class="flex justify-between items-end mb-1">
                                                <span class="font-bold text-gray-700">#{{ $candidate->nomor_urut }} {{ $candidate->nama_ketua }}</span>
                                                <span class="font-bold text-gray-900">{{ number_format($persen, 1) }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-3">
                                                {{-- Warna berbeda untuk tiap kandidat berdasarkan index loop --}}
                                                <div class="bg-indigo-600 h-3 rounded-full transition-all duration-1000" 
                                                     style="width: {{ $persen }}%; background-color: {{ $loop->iteration % 2 == 0 ? '#EC4899' : '#4F46E5' }}"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Tampilan jika belum ada suara sama sekali --}}
                        <div class="text-center py-10">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900">Belum ada suara masuk</h3>
                            <p class="text-gray-500">Jadilah yang pertama memilih!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <footer class="mt-12 py-6 text-center text-gray-400 text-sm border-t">
        &copy; {{ date('Y') }} KPU Fakultas Hukum
    </footer>

    {{-- ========================================== --}}
    {{-- MODAL (SEKARANG HIDDEN BY DEFAULT)         --}}
    {{-- ========================================== --}}
    @if(!Auth::user()->has_voted && (empty(Auth::user()->foto_ktm) || empty(Auth::user()->foto_diri)))
    
    {{-- Tambahkan class 'hidden' dan id='uploadModal' --}}
    <div id="uploadModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            {{-- Overlay --}}
            <div class="fixed inset-0 bg-gray-900 bg-opacity-90 transition-opacity"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- Modal Panel --}}
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                
                {{-- Tombol Close (Opsional, untuk UX lebih baik) --}}
                <div class="absolute top-0 right-0 pt-4 pr-4">
                    <button type="button" onclick="closeModal()" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('voting.upload_data') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                    @csrf
                    
                    <div class="bg-blue-50 px-4 py-3 border-b border-blue-100 sm:px-6">
                        <h3 class="text-lg leading-6 font-bold text-blue-900" id="modal-title">Verifikasi Identitas</h3>
                    </div>

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                        {{-- 1. KTM --}}
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-2">1. Upload Foto KTM</label>
                            <input type="file" name="foto_ktm" id="foto_ktm" accept="image/*" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer border border-gray-300 rounded-lg">
                        </div>

                        <hr class="border-gray-200 mb-6">

                        {{-- 2. Selfie --}}
                        <div class="mb-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">2. Ambil Foto Diri (Selfie)</label>
                            
                            {{-- Camera Area --}}
                            <div class="relative bg-black rounded-lg overflow-hidden w-full h-64 flex items-center justify-center mb-4">
                                <video id="videoElement" autoplay playsinline class="absolute inset-0 w-full h-full object-cover hidden transform -scale-x-100"></video>
                                <canvas id="canvasElement" class="hidden"></canvas>
                                <img id="photoPreview" class="absolute inset-0 w-full h-full object-cover hidden transform -scale-x-100" alt="Hasil Foto">
                                <div id="cameraPlaceholder" class="text-gray-400 text-sm flex flex-col items-center">
                                    <p>Kamera belum aktif</p>
                                </div>
                            </div>

                            {{-- Buttons --}}
                            <div class="flex gap-3 justify-center">
                                <button type="button" id="btnOpenCamera" onclick="startCamera()" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-semibold hover:bg-blue-700 shadow">Buka Kamera</button>
                                <button type="button" id="btnCapture" onclick="takePhoto()" class="hidden px-4 py-2 bg-red-600 text-white rounded-md text-sm font-semibold hover:bg-red-700 animate-pulse shadow">Jepret</button>
                                <button type="button" id="btnRetake" onclick="resetCamera()" class="hidden px-4 py-2 bg-gray-600 text-white rounded-md text-sm font-semibold hover:bg-gray-700 shadow">Ulangi</button>
                            </div>

                            <input type="file" name="foto_diri" id="foto_diri_input" class="hidden" required>
                            <p id="status_foto" class="text-center text-xs text-red-500 mt-3 font-semibold bg-red-50 p-1 rounded">Status: Foto belum diambil</p>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t">
                        <button type="submit" id="btnSubmit" disabled class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-gray-400 text-base font-medium text-white sm:ml-3 sm:w-auto sm:text-sm cursor-not-allowed">
                            Simpan & Lanjut
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    {{-- Script JavaScript Lengkap --}}
    <script>
        const modal = document.getElementById('uploadModal');

        function openModal() {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Matikan scroll body
        }

        function closeModal() {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto'; // Nyalakan scroll body
            // Matikan kamera jika user menutup modal saat kamera nyala
            stopStream();
        }

        // --- LOGIKA KAMERA (Sama seperti sebelumnya) ---
        const video = document.getElementById('videoElement');
        const canvas = document.getElementById('canvasElement');
        const photoPreview = document.getElementById('photoPreview');
        const cameraPlaceholder = document.getElementById('cameraPlaceholder');
        const fileInput = document.getElementById('foto_diri_input');
        const btnOpenCamera = document.getElementById('btnOpenCamera');
        const btnCapture = document.getElementById('btnCapture');
        const btnRetake = document.getElementById('btnRetake');
        const btnSubmit = document.getElementById('btnSubmit');
        const statusFoto = document.getElementById('status_foto');
        let stream = null;

        async function startCamera() {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } }, 
                    audio: false 
                });
                video.srcObject = stream;
                video.classList.remove('hidden');
                cameraPlaceholder.classList.add('hidden');
                btnOpenCamera.classList.add('hidden');
                btnCapture.classList.remove('hidden');
                photoPreview.classList.add('hidden');
            } catch (err) {
                alert("Gagal membuka kamera. Izinkan akses kamera di browser.");
            }
        }

        function takePhoto() {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const context = canvas.getContext('2d');
            context.translate(canvas.width, 0);
            context.scale(-1, 1);
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            const dataUrl = canvas.toDataURL('image/jpeg');
            photoPreview.src = dataUrl;
            
            canvas.toBlob(function(blob) {
                const file = new File([blob], "selfie_" + Date.now() + ".jpg", { type: "image/jpeg" });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;
                
                statusFoto.innerText = "Status: Foto berhasil diambil";
                statusFoto.classList.replace('text-red-500', 'text-green-600');
                statusFoto.classList.replace('bg-red-50', 'bg-green-50');
                
                btnSubmit.disabled = false;
                btnSubmit.classList.remove('bg-gray-400', 'cursor-not-allowed');
                btnSubmit.classList.add('bg-blue-900', 'hover:bg-blue-800');
            }, 'image/jpeg', 0.9);

            video.classList.add('hidden');
            photoPreview.classList.remove('hidden');
            btnCapture.classList.add('hidden');
            btnRetake.classList.remove('hidden');
            stopStream();
        }

        function resetCamera() {
            fileInput.value = "";
            statusFoto.innerText = "Status: Foto belum diambil";
            statusFoto.classList.replace('text-green-600', 'text-red-500');
            statusFoto.classList.replace('bg-green-50', 'bg-red-50');
            btnSubmit.disabled = true;
            btnSubmit.classList.add('bg-gray-400', 'cursor-not-allowed');
            btnSubmit.classList.remove('bg-blue-900');
            btnRetake.classList.add('hidden');
            startCamera();
        }

        function stopStream() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
        }
    </script>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const ctx = document.getElementById('barChart');
            if (!ctx) return;

            const labels = @json($candidates->pluck('nama_ketua'));
            const dataSuara = @json($candidates->pluck('suara'));
            const totalSuara = {{ $total_suara }};

            const dataPersentase = dataSuara.map(suara => {
                return totalSuara > 0 ? ((suara / totalSuara) * 100).toFixed(1) : 0;
            });

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: dataPersentase,
                        backgroundColor: [
                            'rgba(79, 70, 229, 0.8)',
                            'rgba(236, 72, 153, 0.8)',
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(245, 158, 11, 0.8)'
                        ],
                        borderColor: '#ffffff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 14,
                                padding: 16
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const index = context.dataIndex;
                                    return context.label + ': ' +
                                        context.raw + '%';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>


</body>
</html>