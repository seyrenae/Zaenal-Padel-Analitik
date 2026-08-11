<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Pertandingan Baru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Styling khusus untuk radio button yang disamarkan jadi tombol */
        .toggle-radio:checked + label {
            background-color: #1e3a8a; /* Biru gelap Tailwind */
            border-color: #3b82f6;
            color: white;
        }
    </style>
</head>
<body class="bg-[#121212] text-gray-200 min-h-screen p-8 flex justify-center">

    <div class="w-full max-w-2xl bg-[#1e1e1e] p-8 rounded-2xl shadow-2xl border border-zinc-800">
        <!-- Konteks Turnamen -->
        <div class="flex justify-between items-center border-b border-zinc-700 pb-4 mb-6">
            <div>
                <p class="text-zinc-400 text-sm">Padel Cup Surabaya · Grup A</p>
                <h2 class="text-2xl font-bold text-white mt-1">Pertandingan baru</h2>
            </div>
            <button class="border border-zinc-600 text-zinc-300 px-4 py-2 rounded-lg text-sm hover:bg-zinc-800 transition">
                Ganti turnamen
            </button>
        </div>
        
        <form action="/setup" method="POST">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <!-- Dummy input untuk bypass validasi backend prototipe sebelumnya -->
            <input type="hidden" name="nama_turnamen" value="Padel Cup Surabaya">
            <input type="hidden" name="kategori" value="Open">
            <input type="hidden" name="babak" value="Grup A">
            <input type="hidden" name="format_set" value="Best of 3">
            <input type="hidden" name="golden_point" value="1">

            <!-- Data Tim dan Pemain -->
            <div class="mb-8 space-y-6">
                <div>
                    <h3 class="text-sm font-bold text-zinc-400 mb-3">Tim A</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="text" name="tim_a_pemain_kiri" class="w-full bg-[#121212] border border-zinc-700 p-3 rounded-lg text-white focus:border-blue-500 outline-none" placeholder="Cari pemain kiri..." required>
                        <input type="text" name="tim_a_pemain_kanan" class="w-full bg-[#121212] border border-zinc-700 p-3 rounded-lg text-white focus:border-blue-500 outline-none" placeholder="Cari pemain kanan..." required>
                    </div>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-zinc-400 mb-3">Tim B</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="text" name="tim_b_pemain_kiri" class="w-full bg-[#121212] border border-zinc-700 p-3 rounded-lg text-white focus:border-blue-500 outline-none" placeholder="Cari pemain kiri..." required>
                        <input type="text" name="tim_b_pemain_kanan" class="w-full bg-[#121212] border border-zinc-700 p-3 rounded-lg text-white focus:border-blue-500 outline-none" placeholder="Cari pemain kanan..." required>
                    </div>
                </div>
            </div>

            <!-- Konfirmasi Awal (Toggle) -->
            <div class="border-t border-zinc-700 pt-6 space-y-6 mb-8">
                <!-- Serve Toggle -->
                <div>
                    <p class="text-sm text-zinc-400 mb-3">Siapa serve duluan?</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <input type="radio" name="serve_awal" id="serveA" value="Tim A" class="hidden toggle-radio" checked>
                            <label for="serveA" class="block text-center border border-zinc-600 rounded-lg py-3 cursor-pointer hover:bg-zinc-800 transition">Tim A</label>
                        </div>
                        <div>
                            <input type="radio" name="serve_awal" id="serveB" value="Tim B" class="hidden toggle-radio">
                            <label for="serveB" class="block text-center border border-zinc-600 rounded-lg py-3 cursor-pointer hover:bg-zinc-800 transition">Tim B</label>
                        </div>
                    </div>
                </div>
                
                <!-- Sisi Lapangan Toggle -->
                <div>
                    <p class="text-sm text-zinc-400 mb-3">Sisi lapangan Tim A</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <input type="radio" name="sisi_lapangan_awal_tim_a" id="sisiKiri" value="Kiri" class="hidden toggle-radio" checked>
                            <label for="sisiKiri" class="block text-center border border-zinc-600 rounded-lg py-3 cursor-pointer hover:bg-zinc-800 transition">Kiri</label>
                        </div>
                        <div>
                            <input type="radio" name="sisi_lapangan_awal_tim_a" id="sisiKanan" value="Kanan" class="hidden toggle-radio">
                            <label for="sisiKanan" class="block text-center border border-zinc-600 rounded-lg py-3 cursor-pointer hover:bg-zinc-800 transition">Kanan</label>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full bg-white text-black font-bold py-4 rounded-xl shadow-lg hover:bg-gray-200 transition text-lg">
                Mulai pertandingan
            </button>
            <p class="text-center text-xs text-zinc-500 mt-4">Format Best of 3 · Golden point aktif — dari pengaturan turnamen</p>
        </form>
    </div>
</body>
</html>