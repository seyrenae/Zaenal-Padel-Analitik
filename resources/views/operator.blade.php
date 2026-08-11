<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Live Tracking - Padel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body class="bg-[#121212] text-gray-200 min-h-screen p-6 flex justify-center items-start">

    <div class="w-full max-w-[500px] bg-[#1e1e1e] p-6 rounded-2xl shadow-2xl border border-zinc-800 flex flex-col mt-4">
        
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <p class="text-zinc-400 text-sm">Live tracking · set <span id="set-indicator">1</span></p>
            </div>
            <div class="flex gap-2 items-center">
                <a href="/livestream/{{ $pertandingan->id }}" target="_blank" class="bg-zinc-800 border border-zinc-700 hover:bg-zinc-700 text-zinc-300 px-3 py-1 rounded-full text-xs font-medium transition cursor-pointer">🖥️ Buka OBS</a>
                <span class="bg-green-900/30 text-green-500 px-3 py-1 rounded-full text-xs font-bold border border-green-800">Berlangsung</span>
            </div>
        </div>

        <!-- Tabel Skor Set dan Game -->
        <div class="mb-6 rounded-xl border border-zinc-700/60 overflow-hidden">
            <table class="w-full text-center text-sm">
                <thead class="bg-zinc-800/40 text-zinc-400 border-b border-zinc-700/60">
                    <tr>
                        <th class="py-3 px-4 text-left font-medium">Tim</th>
                        <th class="py-3 px-4 font-medium">Set 1</th>
                        <th class="py-3 px-4 font-medium">Set 2</th>
                        <th class="py-3 px-4 font-medium">Game</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-700/60 bg-zinc-800/10">
                    <tr>
                        <td class="py-3 px-4 text-left font-medium text-white">Tim A</td>
                        <td id="set-1-a" class="py-3 px-4 text-zinc-300">-</td>
                        <td id="set-2-a" class="py-3 px-4 text-zinc-300">-</td>
                        <td id="game-a-lokal" class="py-3 px-4 font-bold text-blue-400">0</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 text-left font-medium text-white">Tim B</td>
                        <td id="set-1-b" class="py-3 px-4 text-zinc-300">-</td>
                        <td id="set-2-b" class="py-3 px-4 text-zinc-300">-</td>
                        <td id="game-b-lokal" class="py-3 px-4 font-bold text-red-400">0</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Tombol Konteks -->
        <div class="grid grid-cols-3 gap-3 mb-6">
            <button id="btn-serve" onclick="setPukulan('Serve')" class="bg-[#0f172a] border border-blue-900 rounded-lg py-2.5 text-sm font-medium text-blue-400 transition">Serve</button>
            <button id="btn-rally" onclick="setPukulan('Rally')" class="bg-zinc-800/50 border border-zinc-700 rounded-lg py-2.5 text-sm text-zinc-400 hover:text-white transition">Rally</button>
            <button onclick="undoPoin()" class="bg-zinc-800/50 border border-zinc-700 rounded-lg py-2.5 text-sm text-zinc-400 hover:text-white transition flex justify-center items-center gap-2">
                ↶ Undo
            </button>
        </div>

        <!-- Tombol Skor Raksasa (Langkah 1) -->
        <div id="skor-raksasa-container" class="grid grid-cols-[1fr_auto_1fr] gap-4 items-center mb-4">
            <button id="btn-skor-a" onclick="tapScore('Tim A')" class="bg-[#021631] hover:bg-[#0a2347] border border-[#0f2e5a] rounded-xl py-10 flex flex-col items-center justify-center transition shadow-lg relative">
                <span class="text-blue-500/80 font-medium text-sm mb-2 flex items-center justify-center gap-1">Tim A <span id="serve-a" class="text-yellow-400 text-lg hidden">🎾</span></span>
                <span id="poin-a-lokal" class="text-7xl font-medium text-blue-400">0</span>
            </button>
            
            <div id="vs-text" class="text-zinc-600 font-medium text-xs">vs</div>

            <button id="btn-skor-b" onclick="tapScore('Tim B')" class="bg-[#380d0f] hover:bg-[#4a1215] border border-[#5c1316] rounded-xl py-10 flex flex-col items-center justify-center transition shadow-lg relative">
                <span class="text-red-500/80 font-medium text-sm mb-2 flex items-center justify-center gap-1">Tim B <span id="serve-b" class="text-yellow-400 text-lg hidden">🎾</span></span>
                <span id="poin-b-lokal" class="text-7xl font-medium text-red-400">0</span>
            </button>
        </div>
        <p id="bantuan-teks" class="text-center text-zinc-500 text-xs mb-6 font-medium">Poin saat ini di game berjalan · ketuk skor untuk mencatat poin</p>

        <!-- Panel Detail Poin (Langkah 2 - Tersembunyi default) -->
        <div id="panel-detail" class="hidden border-t border-zinc-700 pt-6 animate-fade-in">
            <p class="text-xs font-medium text-zinc-400 mb-3">Siapa yang mencetak / melakukan error? (opsional)</p>
            <div class="grid grid-cols-2 gap-3 mb-6">
                <button onclick="setPemain('{{ $pertandingan->tim_a_pemain_kiri }}', this)" class="btn-pemain bg-zinc-800 border border-zinc-700 text-gray-200 rounded-lg py-3 text-sm hover:bg-zinc-700 transition">{{ $pertandingan->tim_a_pemain_kiri }} (A)</button>
                <button onclick="setPemain('{{ $pertandingan->tim_a_pemain_kanan }}', this)" class="btn-pemain bg-zinc-800 border border-zinc-700 text-gray-200 rounded-lg py-3 text-sm hover:bg-zinc-700 transition">{{ $pertandingan->tim_a_pemain_kanan }} (A)</button>
                <button onclick="setPemain('{{ $pertandingan->tim_b_pemain_kiri }}', this)" class="btn-pemain bg-zinc-800 border border-zinc-700 text-gray-200 rounded-lg py-3 text-sm hover:bg-zinc-700 transition">{{ $pertandingan->tim_b_pemain_kiri }} (B)</button>
                <button onclick="setPemain('{{ $pertandingan->tim_b_pemain_kanan }}', this)" class="btn-pemain bg-zinc-800 border border-zinc-700 text-gray-200 rounded-lg py-3 text-sm hover:bg-zinc-700 transition">{{ $pertandingan->tim_b_pemain_kanan }} (B)</button>
            </div>

            <p class="text-xs font-medium text-zinc-400 mb-3">Jenis poin (opsional)</p>
            <div class="grid grid-cols-2 gap-3 mb-6">
                <button onclick="setJenis('Winner', this)" class="btn-jenis bg-[#062c12] border border-[#0d4a21] text-green-500 rounded-lg py-3 text-sm hover:bg-[#0a3f1a] transition">Winner</button>
                <button onclick="setJenis('Unforced error', this)" class="btn-jenis bg-[#380d0f] border border-[#5c1316] text-red-400 rounded-lg py-3 text-sm hover:bg-[#4a1215] transition">Unforced error</button>
                <button onclick="setJenis('Forced error', this)" class="btn-jenis bg-[#380d0f] border border-[#5c1316] text-red-400 rounded-lg py-3 text-sm hover:bg-[#4a1215] transition">Forced error</button>
                <button onclick="setDinding()" id="btn-dinding" class="bg-[#021631] border border-[#0f2e5a] text-blue-400 rounded-lg py-3 text-sm hover:bg-[#0a2347] transition">Kena dinding</button>
            </div>

            <button onclick="simpanLogPoin()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm py-3 transition mb-2 shadow-lg">Simpan Poin</button>
            <button onclick="lewatiDetail()" class="w-full text-zinc-500 text-xs py-2 hover:text-white transition mb-4">Abaikan Detail & Lewati</button>
        </div>

        <!-- Tombol Kontrol Bawah -->
        <div class="mt-auto border-t border-zinc-700/60 pt-6 flex justify-between items-center">
            <div class="flex gap-2">
                <button id="btn-jeda" onclick="toggleJeda()" class="bg-[#1e1e1e] hover:bg-zinc-800 border border-zinc-600 text-zinc-300 px-4 py-2 rounded-lg text-sm transition">⏸ Jeda</button>
                <button onclick="gantiSisi()" class="bg-[#1e1e1e] hover:bg-zinc-800 border border-zinc-600 text-zinc-300 px-4 py-2 rounded-lg text-sm transition">⇄ Ganti sisi</button>
                <button onclick="gantiServer()" class="bg-[#1e1e1e] hover:bg-zinc-800 border border-zinc-600 text-zinc-300 px-4 py-2 rounded-lg text-sm transition">🎾 Serve</button>
            </div>
            <!-- Menggunakan class khusus untuk tombol Akhiri Set -->
            <button onclick="akhiriSetManual()" class="bg-[#1e1e1e] hover:bg-zinc-800 border border-zinc-600 text-zinc-300 px-5 py-2 rounded-lg text-sm transition">Akhiri set / match</button>
        </div>
    </div>

    <script>
        // Setup Variabel JavaScript dari Database State (Agar tahan Refresh)
        let numPointA = {{ isset($stateTerakhir['numPointA']) ? $stateTerakhir['numPointA'] : 0 }};
        let numPointB = {{ isset($stateTerakhir['numPointB']) ? $stateTerakhir['numPointB'] : 0 }};
        let numGameA = {{ isset($stateTerakhir['gameTimA']) ? $stateTerakhir['gameTimA'] : 0 }};
        let numGameB = {{ isset($stateTerakhir['gameTimB']) ? $stateTerakhir['gameTimB'] : 0 }};
        let currentSet = {{ isset($stateTerakhir['currentSet']) ? $stateTerakhir['currentSet'] : 1 }};
        const padelScores = ["0", "15", "30", "40"];
        
        let pendingPoin = { tim: null, pemain: null, jenis: null, dinding: 0 };
        let historiSetA = @json(isset($stateTerakhir['historiSetA']) ? $stateTerakhir['historiSetA'] : [0, 0, 0]);
        let historiSetB = @json(isset($stateTerakhir['historiSetB']) ? $stateTerakhir['historiSetB'] : [0, 0, 0]);
        let jenisPukulan = 'Serve';
        
        let isGoldenPoint = {{ $pertandingan->golden_point ? 'true' : 'false' }};
        let stateHistory = [];
        let setsWonA = 0;
        let setsWonB = 0;
        let matchFinished = {{ (isset($stateTerakhir['matchFinished']) && $stateTerakhir['matchFinished']) ? 'true' : 'false' }};
        let isPaused = {{ (isset($stateTerakhir['isPaused']) && $stateTerakhir['isPaused']) ? 'true' : 'false' }};
        let isSwapped = false;
        let currentServer = '{{ isset($stateTerakhir['currentServer']) ? $stateTerakhir['currentServer'] : $pertandingan->serve_awal }}';

        function toggleJeda() {
            isPaused = !isPaused;
            const btnJeda = document.getElementById('btn-jeda');
            if (isPaused) {
                btnJeda.innerHTML = '▶ Lanjut';
                btnJeda.classList.replace('text-zinc-300', 'text-yellow-400');
            } else {
                btnJeda.innerHTML = '⏸ Jeda';
                btnJeda.classList.replace('text-yellow-400', 'text-zinc-300');
            }

            axios.post('/tambah-poin', {
                skip_db: true, // Bypass simpan ke DB log_poins
                match_id: {{ $pertandingan->id }},
                tim_pemenang: '-',
                numPointA: numPointA,
                numPointB: numPointB,
                poinTimA: document.getElementById('poin-a-lokal').innerText,
                poinTimB: document.getElementById('poin-b-lokal').innerText,
                gameTimA: numGameA,
                gameTimB: numGameB,
                historiSetA: historiSetA,
                historiSetB: historiSetB,
                currentSet: currentSet,
                namaTimA: '{{ $namaTimA }}',
                namaTimB: '{{ $namaTimB }}',
                aksi: isPaused ? 'Pertandingan Dijeda' : 'Pertandingan Dilanjutkan',
                statusMatch: isPaused ? 'jeda' : 'berjalan',
                currentServer: currentServer
            });
        }
        
        function updateServerUI() {
            if (currentServer === 'Tim A') {
                document.getElementById('serve-a').classList.remove('hidden');
                document.getElementById('serve-b').classList.add('hidden');
            } else {
                document.getElementById('serve-a').classList.add('hidden');
                document.getElementById('serve-b').classList.remove('hidden');
            }
        }
        
        document.addEventListener('DOMContentLoaded', () => {
            updateServerUI();
            updateTabelGame();
            document.getElementById('poin-a-lokal').innerText = '{{ isset($stateTerakhir['poinTimA']) ? $stateTerakhir['poinTimA'] : "0" }}';
            document.getElementById('poin-b-lokal').innerText = '{{ isset($stateTerakhir['poinTimB']) ? $stateTerakhir['poinTimB'] : "0" }}';
            document.getElementById('set-indicator').innerText = currentSet;
            
            // Set history UI
            if (historiSetA[0] > 0 || historiSetB[0] > 0) {
                document.getElementById('set-1-a').innerText = historiSetA[0];
                document.getElementById('set-1-b').innerText = historiSetB[0];
            }
            if (historiSetA[1] > 0 || historiSetB[1] > 0) {
                document.getElementById('set-2-a').innerText = historiSetA[1];
                document.getElementById('set-2-b').innerText = historiSetB[1];
            }
            
            // setsWon calc
            setsWonA = (historiSetA[0] > historiSetB[0] ? 1 : 0) + (historiSetA[1] > historiSetB[1] ? 1 : 0) + (historiSetA[2] > historiSetB[2] ? 1 : 0);
            setsWonB = (historiSetB[0] > historiSetA[0] ? 1 : 0) + (historiSetB[1] > historiSetA[1] ? 1 : 0) + (historiSetB[2] > historiSetA[2] ? 1 : 0);
            
            // Jeda state
            if (isPaused) {
                const btnJeda = document.getElementById('btn-jeda');
                btnJeda.innerHTML = '▶ Lanjut';
                btnJeda.classList.replace('text-zinc-300', 'text-yellow-400');
            }
        });

        function gantiServer() {
            currentServer = currentServer === 'Tim A' ? 'Tim B' : 'Tim A';
            updateServerUI();
            
            axios.post('/tambah-poin', {
                skip_db: true,
                match_id: {{ $pertandingan->id }},
                tim_pemenang: '-',
                numPointA: numPointA,
                numPointB: numPointB,
                poinTimA: document.getElementById('poin-a-lokal').innerText,
                poinTimB: document.getElementById('poin-b-lokal').innerText,
                gameTimA: numGameA,
                gameTimB: numGameB,
                historiSetA: historiSetA,
                historiSetB: historiSetB,
                currentSet: currentSet,
                namaTimA: '{{ $namaTimA }}',
                namaTimB: '{{ $namaTimB }}',
                aksi: 'Serve pindah ke ' + currentServer,
                statusMatch: isPaused ? 'jeda' : 'berjalan',
                currentServer: currentServer
            });
        }

        function gantiSisi() {
            isSwapped = !isSwapped;
            const container = document.getElementById('skor-raksasa-container');
            const vs = document.getElementById('vs-text');
            const btnA = document.getElementById('btn-skor-a');
            const btnB = document.getElementById('btn-skor-b');
            
            if(isSwapped) {
                container.appendChild(vs);
                container.appendChild(btnA);
            } else {
                container.appendChild(vs);
                container.appendChild(btnB);
            }
        }

        function akhiriMatch(pemenang) {
            matchFinished = true;
            alert("Pertandingan Selesai! Pemenang: " + pemenang);
            axios.post('/tambah-poin', {
                skip_db: true,
                match_id: {{ $pertandingan->id }},
                tim_pemenang: '-',
                numPointA: numPointA,
                numPointB: numPointB,
                poinTimA: document.getElementById('poin-a-lokal').innerText,
                poinTimB: document.getElementById('poin-b-lokal').innerText,
                gameTimA: numGameA,
                gameTimB: numGameB,
                historiSetA: historiSetA,
                historiSetB: historiSetB,
                currentSet: currentSet,
                namaTimA: '{{ $namaTimA }}',
                namaTimB: '{{ $namaTimB }}',
                aksi: 'Pertandingan Selesai! Pemenang: ' + pemenang,
                statusMatch: 'selesai',
                currentServer: currentServer
            }).then(() => {
                window.location.href = '/summary/' + {{ $pertandingan->id }};
            });
        }

        function saveState() {
            stateHistory.push({
                numPointA: numPointA, numPointB: numPointB,
                numGameA: numGameA, numGameB: numGameB,
                currentSet: currentSet,
                historiSetA: [...historiSetA], historiSetB: [...historiSetB],
                dispA: document.getElementById('poin-a-lokal').innerText,
                dispB: document.getElementById('poin-b-lokal').innerText,
                set1a: document.getElementById('set-1-a').innerText,
                set2a: document.getElementById('set-2-a').innerText,
                set1b: document.getElementById('set-1-b').innerText,
                set2b: document.getElementById('set-2-b').innerText,
            });
        }

        function setPukulan(pukulan) {
            jenisPukulan = pukulan;
            document.getElementById('btn-serve').className = 'bg-zinc-800/50 border border-zinc-700 rounded-lg py-2.5 text-sm text-zinc-400 hover:text-white transition';
            document.getElementById('btn-rally').className = 'bg-zinc-800/50 border border-zinc-700 rounded-lg py-2.5 text-sm text-zinc-400 hover:text-white transition';
            
            if(pukulan === 'Serve') {
                document.getElementById('btn-serve').className = 'bg-[#0f172a] border border-blue-900 rounded-lg py-2.5 text-sm font-medium text-blue-400 transition';
            } else {
                document.getElementById('btn-rally').className = 'bg-[#0f172a] border border-blue-900 rounded-lg py-2.5 text-sm font-medium text-blue-400 transition';
            }
        }

        // FUNGSI 1: KETUK SKOR (Aksi Utama)
        function tapScore(tim) {
            if (matchFinished || isPaused) return;
            if (pendingPoin.tim !== null) return; // Mencegah klik ganda sebelum disimpan/dilewati
            saveState(); // Simpan riwayat untuk fungsi Undo
            
            // Tampilkan Detail Panel
            document.getElementById('bantuan-teks').classList.add('hidden');
            document.getElementById('panel-detail').classList.remove('hidden');
            
            // Kalkulasi Logika Poin
            if(tim === 'Tim A') numPointA++;
            if(tim === 'Tim B') numPointB++;
            
            let dispA = "0"; let dispB = "0";
            let isTieBreak = (numGameA === 6 && numGameB === 6);

            if (isTieBreak) {
                // Logika Tie-break (1, 2, 3... win by 2)
                dispA = numPointA.toString();
                dispB = numPointB.toString();
                
                if (numPointA >= 7 && numPointA - numPointB >= 2) {
                    prosesMenangGame('Tim A'); return;
                } else if (numPointB >= 7 && numPointB - numPointA >= 2) {
                    prosesMenangGame('Tim B'); return;
                }
            } else {
                // Logika Deuce & Advantage
                if (numPointA >= 3 && numPointB >= 3) {
                    if (isGoldenPoint) {
                        // Golden Point: setelah 40-40, pemenang poin berikutnya menang game
                        if (numPointA === 4) {
                            prosesMenangGame('Tim A'); return;
                        } else if (numPointB === 4) {
                            prosesMenangGame('Tim B'); return;
                        } else {
                            dispA = "40"; dispB = "40"; 
                        }
                    } else {
                        // Traditional Ad
                        if (numPointA === numPointB) {
                            dispA = "40"; dispB = "40"; 
                        } else if (numPointA === numPointB + 1) {
                            dispA = "Ad"; dispB = "-"; 
                        } else if (numPointB === numPointA + 1) {
                            dispA = "-"; dispB = "Ad"; 
                        } else if (numPointA >= numPointB + 2) {
                            prosesMenangGame('Tim A'); return; 
                        } else if (numPointB >= numPointA + 2) {
                            prosesMenangGame('Tim B'); return; 
                        }
                    }
                } else {
                    // Logika Normal
                    if (numPointA === 4) {
                        prosesMenangGame('Tim A'); return; 
                    } else if (numPointB === 4) {
                        prosesMenangGame('Tim B'); return; 
                    } else {
                        dispA = padelScores[numPointA];
                        dispB = padelScores[numPointB];
                    }
                }
            }

            // Update UI
            document.getElementById('poin-a-lokal').innerText = dispA;
            document.getElementById('poin-b-lokal').innerText = dispB;
            pendingPoin.tim = tim;
        }

        // FUNGSI 2: PROSES GAME & SET
        function prosesMenangGame(tim) {
            if (tim === 'Tim A') numGameA++;
            if (tim === 'Tim B') numGameB++;
            
            numPointA = 0; numPointB = 0; // Reset Poin
            document.getElementById('poin-a-lokal').innerText = "0";
            document.getElementById('poin-b-lokal').innerText = "0";
            
            updateTabelGame();

            // Pindah serve otomatis setiap pergantian game
            currentServer = currentServer === 'Tim A' ? 'Tim B' : 'Tim A';
            updateServerUI();

            // Deteksi Kemenangan Set (Best of 3)
            if ((numGameA >= 6 && numGameA - numGameB >= 2) || numGameA === 7) {
                akhiriSetOtomatis();
            } else if ((numGameB >= 6 && numGameB - numGameA >= 2) || numGameB === 7) {
                akhiriSetOtomatis();
            }
        }

        function akhiriSetOtomatis() {
            simpanRiwayatSet();
            resetGameUI();
        }

        function akhiriSetManual() {
            if (matchFinished || isPaused) return;
            
            let pilihan = confirm("Pilih 'OK' untuk Mengakhiri SET INI saja.\nPilih 'Batal/Cancel' untuk opsi Mengakhiri Seluruh MATCH.");
            if (pilihan) {
                // Paksa akhiri set berjalan meskipun game masih 0-0
                simpanRiwayatSet();
                if (!matchFinished) resetGameUI();
            } else {
                let akhirMatch = confirm("PERINGATAN: Yakin ingin MENGAKHIRI SELURUH MATCH sekarang?");
                if (akhirMatch) {
                    // Coba tentukan pemenang sementara berdasarkan statistik
                    let pemenangSementara = 'Tim A';
                    if (setsWonB > setsWonA || (setsWonB === setsWonA && numGameB > numGameA)) {
                        pemenangSementara = 'Tim B';
                    }
                    akhiriMatch(pemenangSementara === 'Tim A' ? '{{ $namaTimA }}' : '{{ $namaTimB }}');
                }
            }
        }

        function simpanRiwayatSet() {
            // Pindahkan game ke riwayat set yang sesuai
            historiSetA[currentSet - 1] = numGameA;
            historiSetB[currentSet - 1] = numGameB;
            
            // Kalkulasi Set Padel (Best of 3)
            if (numGameA > numGameB) setsWonA++;
            else if (numGameB > numGameA) setsWonB++;
            
            document.getElementById('set-' + currentSet + '-a').innerText = numGameA;
            document.getElementById('set-' + currentSet + '-b').innerText = numGameB;
            
            if (setsWonA >= 2 || setsWonB >= 2) {
                akhiriMatch(setsWonA >= 2 ? '{{ $namaTimA }}' : '{{ $namaTimB }}');
                return;
            }
            
            currentSet++;
            if (currentSet > 3) currentSet = 3; // Batas maksimal
            
            document.getElementById('set-indicator').innerText = currentSet;
        }

        function resetGameUI() {
            numGameA = 0; numGameB = 0;
            updateTabelGame();
        }

        function updateTabelGame() {
            document.getElementById('game-a-lokal').innerText = numGameA;
            document.getElementById('game-b-lokal').innerText = numGameB;
        }

        // FUNGSI 3: INPUT DETAIL POIN (Opsional)
        function setPemain(nama, elemen) { 
            pendingPoin.pemain = nama; 
            document.querySelectorAll('.btn-pemain').forEach(el => {
                el.classList.remove('bg-zinc-600', 'border-gray-400', 'text-white');
                el.classList.add('bg-zinc-800', 'border-zinc-700', 'text-gray-200');
            });
            if(elemen) {
                elemen.classList.remove('bg-zinc-800', 'border-zinc-700', 'text-gray-200');
                elemen.classList.add('bg-zinc-600', 'border-gray-400', 'text-white');
            }
        }

        function setJenis(jenis, elemen) { 
            pendingPoin.jenis = jenis; 
            document.querySelectorAll('.btn-jenis').forEach(el => {
                el.classList.remove('ring-2', 'ring-offset-2', 'ring-offset-[#1e1e1e]', 'ring-white');
            });
            if(elemen) {
                elemen.classList.add('ring-2', 'ring-offset-2', 'ring-offset-[#1e1e1e]', 'ring-white');
            }
        }

        function setDinding() { 
            pendingPoin.dinding = pendingPoin.dinding === 1 ? 0 : 1; 
            if(pendingPoin.dinding === 1) {
                document.getElementById('btn-dinding').classList.add('ring-2', 'ring-offset-2', 'ring-offset-[#1e1e1e]', 'ring-white');
            } else {
                document.getElementById('btn-dinding').classList.remove('ring-2', 'ring-offset-2', 'ring-offset-[#1e1e1e]', 'ring-white');
            }
        }

        function lewatiDetail() {
            pendingPoin.pemain = null;
            pendingPoin.jenis = null;
            pendingPoin.dinding = 0;
            simpanLogPoin();
        }

        function simpanLogPoin() {
            console.log("Akan dikirim ke DB:", pendingPoin);
            
            axios.post('/tambah-poin', {
                match_id: {{ $pertandingan->id }},
                tim_pemenang: pendingPoin.tim,
                pemain_penghasil: pendingPoin.pemain,
                jenis_poin: pendingPoin.jenis,
                jenis_pukulan: jenisPukulan,
                libatkan_dinding: pendingPoin.dinding,
                
                // Skor saat ini:
                numPointA: numPointA,
                numPointB: numPointB,
                poinTimA: document.getElementById('poin-a-lokal').innerText,
                poinTimB: document.getElementById('poin-b-lokal').innerText,
                gameTimA: numGameA,
                gameTimB: numGameB,
                
                historiSetA: historiSetA,
                historiSetB: historiSetB,
                currentSet: currentSet,
                
                namaTimA: '{{ $namaTimA }}',
                namaTimB: '{{ $namaTimB }}',
                
                aksi: pendingPoin.tim ? `Poin untuk ${pendingPoin.tim}` : '',
                currentServer: currentServer
            }).then(response => {
                console.log('Berhasil simpan poin', response.data);
            }).catch(error => {
                console.error('Gagal simpan poin', error);
            });

            // Sembunyikan panel kembali
            document.getElementById('panel-detail').classList.add('hidden');
            document.getElementById('bantuan-teks').classList.remove('hidden');
            
            // Reset state
            pendingPoin = { tim: null, pemain: null, jenis: null, dinding: 0 };
            document.querySelectorAll('.btn-pemain').forEach(el => {
                el.classList.remove('bg-zinc-600', 'border-gray-400', 'text-white');
                el.classList.add('bg-zinc-800', 'border-zinc-700', 'text-gray-200');
            });
            document.querySelectorAll('.btn-jenis').forEach(el => {
                el.classList.remove('ring-2', 'ring-offset-2', 'ring-offset-[#1e1e1e]', 'ring-white');
            });
            document.getElementById('btn-dinding').classList.remove('ring-2', 'ring-offset-2', 'ring-offset-[#1e1e1e]', 'ring-white');
        }

        function undoPoin() {
            if (stateHistory.length > 0) {
                let lastState = stateHistory.pop();
                
                // Kembalikan state internal
                numPointA = lastState.numPointA;
                numPointB = lastState.numPointB;
                numGameA = lastState.numGameA;
                numGameB = lastState.numGameB;
                currentSet = lastState.currentSet;
                historiSetA = [...lastState.historiSetA];
                historiSetB = [...lastState.historiSetB];

                // Kembalikan UI
                document.getElementById('poin-a-lokal').innerText = lastState.dispA;
                document.getElementById('poin-b-lokal').innerText = lastState.dispB;
                
                document.getElementById('game-a-lokal').innerText = numGameA;
                document.getElementById('game-b-lokal').innerText = numGameB;
                
                document.getElementById('set-1-a').innerText = lastState.set1a;
                document.getElementById('set-2-a').innerText = lastState.set2a;
                document.getElementById('set-1-b').innerText = lastState.set1b;
                document.getElementById('set-2-b').innerText = lastState.set2b;
                document.getElementById('set-indicator').innerText = currentSet;
                
                // Sembunyikan panel detail
                document.getElementById('panel-detail').classList.add('hidden');
                document.getElementById('bantuan-teks').classList.remove('hidden');
                
                // Reset form state
                pendingPoin = { tim: null, pemain: null, jenis: null, dinding: 0 };
                document.querySelectorAll('.btn-pemain').forEach(el => {
                    el.classList.remove('bg-zinc-600', 'border-gray-400', 'text-white');
                    el.classList.add('bg-zinc-800', 'border-zinc-700', 'text-gray-200');
                });
                document.querySelectorAll('.btn-jenis').forEach(el => {
                    el.classList.remove('ring-2', 'ring-offset-2', 'ring-offset-[#1e1e1e]', 'ring-white');
                });
                document.getElementById('btn-dinding').classList.remove('ring-2', 'ring-offset-2', 'ring-offset-[#1e1e1e]', 'ring-white');
                
                // Broadcast ulang ke OBS (via endpoint khusus agar baris DB terakhir juga dihapus)
                axios.post('/undo-poin', {
                    match_id: {{ $pertandingan->id }},
                    tim_pemenang: 'Undo',
                    pemain_penghasil: '-',
                    jenis_poin: '-',
                    jenis_pukulan: '-',
                    libatkan_dinding: 0,
                    numPointA: numPointA,
                    numPointB: numPointB,
                    poinTimA: lastState.dispA,
                    poinTimB: lastState.dispB,
                    gameTimA: numGameA,
                    gameTimB: numGameB,
                    historiSetA: historiSetA,
                    historiSetB: historiSetB,
                    currentSet: currentSet,
                    namaTimA: '{{ $namaTimA }}',
                    namaTimB: '{{ $namaTimB }}',
                    aksi: 'Aksi terakhir di-Undo',
                    currentServer: currentServer
                });
            }
        }
    </script>
</body>
</html>