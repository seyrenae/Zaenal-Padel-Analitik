<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livestream Padel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-transparent text-white flex flex-col items-center justify-end h-screen pb-20">

    <!-- Container Utama -->
    <div class="bg-black/80 p-8 rounded-xl border-t-4 border-blue-500 shadow-2xl text-center min-w-[700px]">
        
    
        <div class="mb-6 rounded-xl border border-zinc-700/60 overflow-hidden bg-[#1e1e1e]">
            <table class="w-full text-center text-lg">
                <thead class="bg-zinc-800/40 text-zinc-400 border-b border-zinc-700/60">
                    <tr>
                        <th class="py-3 px-6 text-left font-medium">Tim</th>
                        <th class="py-3 px-6 font-medium">Set 1</th>
                        <th class="py-3 px-6 font-medium">Set 2</th>
                        <th class="py-3 px-6 font-medium">Game</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-700/60 bg-zinc-800/10">
                    <tr>
                        <td id="label-tim-a" class="py-3 px-6 text-left font-bold text-white uppercase">TIM A</td>
                        <td id="set-1-a" class="py-3 px-6 text-zinc-300">-</td>
                        <td id="set-2-a" class="py-3 px-6 text-zinc-300">-</td>
                        <td id="game-a" class="py-3 px-6 font-bold text-blue-400">0</td>
                    </tr>
                    <tr>
                        <td id="label-tim-b" class="py-3 px-6 text-left font-bold text-white uppercase">TIM B</td>
                        <td id="set-1-b" class="py-3 px-6 text-zinc-300">-</td>
                        <td id="set-2-b" class="py-3 px-6 text-zinc-300">-</td>
                        <td id="game-b" class="py-3 px-6 font-bold text-red-400">0</td>
                    </tr>
                </tbody>
            </table>
        </div>
        

        <div class="flex items-center justify-center gap-12 text-[7rem] font-black mb-6 px-10 leading-none">
            <div class="text-blue-400 drop-shadow-md" id="poin-a">0</div>
            <div class="text-zinc-600 text-5xl">:</div>
            <div class="text-red-400 drop-shadow-md" id="poin-b">0</div>
        </div>

        <!-- Notifikasi Animasi & Pemenang -->
        <div id="notifikasi" class="hidden bg-yellow-400 text-black font-bold text-xl px-6 py-2 rounded-full inline-block animate-bounce shadow-lg">
            <span id="nama-aksi"></span>
        </div>
    </div>

    <script type="module">
        function mulaiListen() {
            if (typeof window.Echo !== 'undefined') {
                window.Echo.channel('pertandingan-padel')
                    .listen('UpdateSkorPadel', (e) => {
                        // Update Poin Utama
                        document.getElementById('poin-a').innerText = e.poinTimA;
                        document.getElementById('poin-b').innerText = e.poinTimB;
                        
                        // Update Games
                        document.getElementById('game-a').innerText = e.gameTimA;
                        document.getElementById('game-b').innerText = e.gameTimB;
                        
                        // Update Sets berdasarkan array historiSet
                        if(e.historiSetA && e.historiSetB) {
                            // Cek jika set memiliki poin (jika belum pernah dimainkan beri tanda '-')
                            document.getElementById('set-1-a').innerText = e.historiSetA[0] !== 0 || e.historiSetB[0] !== 0 ? e.historiSetA[0] : '-';
                            document.getElementById('set-2-a').innerText = e.historiSetA[1] !== 0 || e.historiSetB[1] !== 0 ? e.historiSetA[1] : '-';
                            document.getElementById('set-1-b').innerText = e.historiSetB[0] !== 0 || e.historiSetA[0] !== 0 ? e.historiSetB[0] : '-';
                            document.getElementById('set-2-b').innerText = e.historiSetB[1] !== 0 || e.historiSetA[1] !== 0 ? e.historiSetB[1] : '-';
                        }
                        
                        // Update Nama Tim
                        document.getElementById('label-tim-a').innerText = e.namaTimA;
                        document.getElementById('label-tim-b').innerText = e.namaTimB;
                        
                        // Notifikasi (Jika match selesai, durasi tampil lebih lama dan warna berbeda)
                        const notif = document.getElementById('notifikasi');
                        document.getElementById('nama-aksi').innerText = e.aksiTerakhir;
                        
                        if (e.statusMatch === 'selesai') {
                            notif.classList.remove('bg-yellow-400', 'text-black');
                            notif.classList.add('bg-green-600', 'text-white', 'text-2xl', 'p-4');
                            notif.classList.remove('hidden');
                        } else if (e.aksiTerakhir) {
                            notif.classList.remove('hidden');
                            setTimeout(() => {
                                notif.classList.add('hidden');
                            }, 4000);
                        }
                    });
            } else {
                setTimeout(mulaiListen, 100);
            }
        }
        mulaiListen();
    </script>
</body>
</html>