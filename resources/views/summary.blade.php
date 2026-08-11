<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post-Match Summary - #{{ $pertandingan->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#121212] text-gray-200 min-h-screen py-10 flex justify-center items-start">

    <div class="w-full max-w-3xl bg-[#1e1e1e] p-8 rounded-2xl shadow-2xl border border-zinc-800">
        
        <!-- Banner Pemenang -->
        <div class="bg-orange-800/80 border border-orange-700 p-6 rounded-xl text-center mb-8">
            <p class="text-orange-200 text-sm mb-1">Pertandingan selesai</p>
            <h2 class="text-4xl font-bold text-white">{{ $pemenangMatch }} menang!</h2>
        </div>

        <!-- Tabel Skor Per Set -->
        <div class="mb-8 overflow-hidden rounded-xl border border-zinc-700">
            <table class="w-full text-center text-sm">
                <thead class="bg-zinc-800 text-zinc-400">
                    <tr>
                        <th class="py-3 px-4 text-left">Tim</th>
                        <th class="py-3 px-4">Set 1</th>
                        <th class="py-3 px-4">Set 2</th>
                        <th class="py-3 px-4">Set 3</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-700 bg-zinc-900/50">
                    <tr>
                        <td class="py-4 px-4 text-left font-bold text-blue-400">Tim A</td>
                        <td class="py-4 px-4 font-medium text-white">6</td>
                        <td class="py-4 px-4 font-medium text-white">4</td>
                        <td class="py-4 px-4 font-medium text-white">6</td>
                    </tr>
                    <tr>
                        <td class="py-4 px-4 text-left font-bold text-red-400">Tim B</td>
                        <td class="py-4 px-4 font-medium text-white">3</td>
                        <td class="py-4 px-4 font-medium text-white">6</td>
                        <td class="py-4 px-4 font-medium text-white">2</td>
                    </tr>
                </tbody>
            </table>
            <p class="text-xs text-zinc-500 mt-2 text-center">*Skor set hardcode untuk keperluan desain UI prototipe</p>
        </div>

        <!-- Kartu Ringkasan (Top Stats) -->
        <div class="grid grid-cols-2 gap-4 mb-8">
            <div class="bg-zinc-800/50 border border-zinc-700 p-5 rounded-xl">
                <p class="text-sm font-medium text-zinc-400 mb-1">Durasi main</p>
                <h3 class="text-2xl font-bold text-white">1j 42m</h3>
            </div>
            <div class="bg-zinc-800/50 border border-zinc-700 p-5 rounded-xl">
                <p class="text-sm font-medium text-zinc-400 mb-1">Poin pakai dinding</p>
                <h3 class="text-2xl font-bold text-white">{{ $persentaseDinding }}%</h3>
            </div>
        </div>

        <!-- Tabel Statistik Pertandingan (Tim A vs Tim B) -->
        <h3 class="text-sm font-medium text-zinc-400 mb-3">Statistik pertandingan</h3>
        <div class="mb-8">
            <div class="flex justify-between text-xs text-zinc-500 font-bold mb-4 px-4">
                <span class="w-1/4 text-left">Tim A</span>
                <span class="w-2/4 text-center">Statistik</span>
                <span class="w-1/4 text-right">Tim B</span>
            </div>
            
            <div class="space-y-4 px-4">
                <div class="flex justify-between items-center border-b border-zinc-800 pb-3">
                    <span class="w-1/4 text-left font-bold text-white">{{ $statsTimA['winners'] }}</span>
                    <span class="w-2/4 text-center text-sm text-zinc-300">Winners</span>
                    <span class="w-1/4 text-right font-bold text-white">{{ $statsTimB['winners'] }}</span>
                </div>
                <div class="flex justify-between items-center border-b border-zinc-800 pb-3">
                    <span class="w-1/4 text-left font-bold text-white">{{ $statsTimA['forced_errors'] }}</span>
                    <span class="w-2/4 text-center text-sm text-zinc-300">Forced errors</span>
                    <span class="w-1/4 text-right font-bold text-white">{{ $statsTimB['forced_errors'] }}</span>
                </div>
                <div class="flex justify-between items-center border-b border-zinc-800 pb-3">
                    <span class="w-1/4 text-left font-bold text-white">{{ $statsTimA['unforced_errors'] }}</span>
                    <span class="w-2/4 text-center text-sm text-zinc-300">Unforced errors</span>
                    <span class="w-1/4 text-right font-bold text-white">{{ $statsTimB['unforced_errors'] }}</span>
                </div>
                <div class="flex justify-between items-center border-b border-zinc-800 pb-3">
                    <span class="w-1/4 text-left font-bold text-white">{{ $statsTimA['dinding'] }}</span>
                    <span class="w-2/4 text-center text-sm text-zinc-300">Poin kena dinding</span>
                    <span class="w-1/4 text-right font-bold text-white">{{ $statsTimB['dinding'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="w-1/4 text-left font-bold text-blue-400">{{ $statsTimA['total_menang'] }}</span>
                    <span class="w-2/4 text-center text-sm text-zinc-300">Total poin menang</span>
                    <span class="w-1/4 text-right font-bold text-red-400">{{ $statsTimB['total_menang'] }}</span>
                </div>
            </div>
        </div>

        <!-- Tabel Winner & Error Per Pemain -->
        <h3 class="text-sm font-medium text-zinc-400 mb-3 mt-10">Winner & error per pemain</h3>
        <div class="mb-8 px-4">
            <div class="flex justify-between text-xs text-zinc-500 font-bold mb-4">
                <span class="w-1/2">Pemain</span>
                <span class="w-1/4 text-center">Winner</span>
                <span class="w-1/4 text-right">Error</span>
            </div>
            <ul class="space-y-4">
                @foreach($errorStats as $namaPemain => $stats)
                <li class="flex justify-between items-center border-b border-zinc-800 pb-3 last:border-0 last:pb-0">
                    <span class="text-sm text-gray-300 font-medium w-1/2">{{ $namaPemain }}</span>
                    <span class="w-1/4 text-center text-sm text-green-500 font-bold">{{ $stats['winner'] }}</span>
                    <span class="w-1/4 text-right text-sm text-red-400 font-bold">{{ $stats['error'] }}</span>
                </li>
                @endforeach
            </ul>
        </div>

        <!-- Catatan Pertandingan -->
        <h3 class="text-sm font-medium text-zinc-400 mb-3">Catatan pertandingan (opsional)</h3>
        <textarea rows="3" class="w-full bg-[#121212] border border-zinc-700 p-4 rounded-xl text-white focus:border-blue-500 outline-none mb-8 placeholder-zinc-600" placeholder="Momen penting, insiden, kondisi lapangan..."></textarea>

        <!-- Tombol Aksi Akhir -->
        <div class="flex justify-between items-center border-t border-zinc-700 pt-6">
            <div class="flex gap-3">
                <button class="bg-[#121212] hover:bg-zinc-800 text-gray-300 border border-zinc-700 px-5 py-2.5 rounded-lg text-sm font-medium transition">
                    ✎ Koreksi
                </button>
                <button class="bg-[#121212] hover:bg-zinc-800 text-gray-300 border border-zinc-700 px-5 py-2.5 rounded-lg text-sm font-medium transition">
                    ↓ Export
                </button>
            </div>
            <button onclick="window.location.href='/';" class="bg-white hover:bg-gray-200 text-black px-8 py-3 rounded-lg text-sm font-bold shadow-lg transition">
                Simpan ke arsip
            </button>
        </div>

    </div>

</body>
</html>