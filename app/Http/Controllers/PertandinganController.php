<?php

namespace App\Http\Controllers;

use App\Models\Pertandingan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Untuk tabel log_poins yang belum ada Modelnya (pakai Query Builder)
use App\Events\UpdateSkorPadel;

class PertandinganController extends Controller
{
    public function setup() {
        return view('setup');
    }

    public function simpan(Request $request) {
        $request->validate([
            'nama_turnamen' => 'required', 'kategori' => 'required', 'babak' => 'required',
            'tim_a_pemain_kiri' => 'required', 'tim_a_pemain_kanan' => 'required',
            'tim_b_pemain_kiri' => 'required', 'tim_b_pemain_kanan' => 'required',
        ]);

        $pertandingan = new Pertandingan();
        $pertandingan->nama_turnamen = $request->nama_turnamen;
        $pertandingan->kategori = $request->kategori;
        $pertandingan->babak = $request->babak;
        $pertandingan->format_set = $request->format_set;
        $pertandingan->golden_point = $request->golden_point;
        
        $pertandingan->tim_a_pemain_kiri = $request->tim_a_pemain_kiri;
        $pertandingan->tim_a_pemain_kanan = $request->tim_a_pemain_kanan;
        $pertandingan->tim_b_pemain_kiri = $request->tim_b_pemain_kiri;
        $pertandingan->tim_b_pemain_kanan = $request->tim_b_pemain_kanan;
        
        $pertandingan->serve_awal = $request->serve_awal;
        $pertandingan->sisi_lapangan_awal_tim_a = $request->sisi_lapangan_awal_tim_a;
        $pertandingan->status = 'live';
        $pertandingan->save();

        return redirect('/operator/' . $pertandingan->id);
    }

    // --- FUNGSI BARU UNTUK FASE 3 ---

    public function operator($id) {
        $pertandingan = Pertandingan::findOrFail($id);
        // Buat nama tim gabungan untuk ditampilkan
        $namaTimA = $pertandingan->tim_a_pemain_kiri . ' & ' . $pertandingan->tim_a_pemain_kanan;
        $namaTimB = $pertandingan->tim_b_pemain_kiri . ' & ' . $pertandingan->tim_b_pemain_kanan;

        $stateTerakhir = $pertandingan->state_terakhir ? json_decode($pertandingan->state_terakhir, true) : null;

        return view('operator', compact('pertandingan', 'namaTimA', 'namaTimB', 'stateTerakhir'));
    }

    // Menampilkan Halaman Livestream (OBS)
    public function livestream($id) {
        $pertandingan = Pertandingan::findOrFail($id);
        $namaTimA = $pertandingan->tim_a_pemain_kiri . ' & ' . $pertandingan->tim_a_pemain_kanan;
        $namaTimB = $pertandingan->tim_b_pemain_kiri . ' & ' . $pertandingan->tim_b_pemain_kanan;
        $stateTerakhir = $pertandingan->state_terakhir ? json_decode($pertandingan->state_terakhir, true) : null;

        return view('livestream', compact('pertandingan', 'namaTimA', 'namaTimB', 'stateTerakhir'));
    }

    // Memproses Input Poin & Menyimpan ke Database
    public function tambahPoin(Request $request) {
        // 1. Simpan ke database log_poins jika bukan event UI khusus (jeda/selesai)
        if (!$request->has('skip_db') || $request->skip_db == false) {
            DB::table('log_poins')->insert([
                'pertandingan_id' => $request->match_id,
                'set_ke' => $request->currentSet ?? 1, 
                'game_ke' => ($request->gameTimA ?? 0) + ($request->gameTimB ?? 0) + 1,
                'tim_pemenang_poin' => $request->tim_pemenang,
                'server_saat_ini' => $request->server_saat_ini ?? '-',
                'pemain_penghasil_poin' => $request->pemain_penghasil ?? '-', // Fallback string karena kolom DB NOT NULL
                'jenis_akhir_poin' => $request->jenis_poin ?? '-', // Fallback string
                'jenis_pukulan' => $request->jenis_pukulan ?? '-',
                'libatkan_dinding' => $request->libatkan_dinding ?? 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Update State Terakhir di Pertandingan
        $stateJson = json_encode([
            'numPointA' => $request->numPointA ?? 0,
            'numPointB' => $request->numPointB ?? 0,
            'poinTimA' => $request->poinTimA ?? '0',
            'poinTimB' => $request->poinTimB ?? '0',
            'gameTimA' => $request->gameTimA ?? 0,
            'gameTimB' => $request->gameTimB ?? 0,
            'historiSetA' => $request->historiSetA ?? [0,0,0],
            'historiSetB' => $request->historiSetB ?? [0,0,0],
            'currentSet' => $request->currentSet ?? 1,
            'currentServer' => $request->currentServer ?? 'Tim A',
            'isPaused' => ($request->statusMatch ?? '') === 'jeda',
            'matchFinished' => ($request->statusMatch ?? '') === 'selesai'
        ]);
        DB::table('pertandingans')->where('id', $request->match_id)->update(['state_terakhir' => $stateJson]);

        // 2. Broadcast ke Livestream (OBS)
        UpdateSkorPadel::dispatch($request->all());

        return response()->json(['status' => 'sukses']);
    }

    // Memproses Undo Poin
    public function undoPoin(Request $request) {
        // Hapus log poin terakhir agar statistik tidak rusak
        $lastLog = DB::table('log_poins')
            ->where('pertandingan_id', $request->match_id)
            ->orderBy('id', 'desc')
            ->first();
            
        if ($lastLog) {
            DB::table('log_poins')->where('id', $lastLog->id)->delete();
        }

        // Update State Terakhir di Pertandingan (Untuk Undo)
        $stateJson = json_encode([
            'numPointA' => $request->numPointA ?? 0,
            'numPointB' => $request->numPointB ?? 0,
            'poinTimA' => $request->poinTimA ?? '0',
            'poinTimB' => $request->poinTimB ?? '0',
            'gameTimA' => $request->gameTimA ?? 0,
            'gameTimB' => $request->gameTimB ?? 0,
            'historiSetA' => $request->historiSetA ?? [0,0,0],
            'historiSetB' => $request->historiSetB ?? [0,0,0],
            'currentSet' => $request->currentSet ?? 1,
            'currentServer' => $request->currentServer ?? 'Tim A',
            'isPaused' => ($request->statusMatch ?? '') === 'jeda',
            'matchFinished' => ($request->statusMatch ?? '') === 'selesai'
        ]);
        DB::table('pertandingans')->where('id', $request->match_id)->update(['state_terakhir' => $stateJson]);

        // Broadcast skor yang dikembalikan ke Livestream (OBS)
        UpdateSkorPadel::dispatch($request->all());

        return response()->json(['status' => 'undo sukses']);
    }

    // Menampilkan Halaman Ringkasan (Post-Match Summary)
    public function summary($id) {
        $pertandingan = Pertandingan::findOrFail($id);
        
        $logs = DB::table('log_poins')->where('pertandingan_id', $id)->get();
        
        // 1. Kalkulasi Statistik Utama
        $totalPoin = $logs->count();
        $poinPakaiDinding = $logs->where('libatkan_dinding', '1')->count();
        $persentaseDinding = $totalPoin > 0 ? round(($poinPakaiDinding / $totalPoin) * 100) : 0;
        
        // --- 2. STATISTIK PER TIM (Untuk Tabel Tiga Kolom) ---
        $statsTimA = [
            'winners' => 0, 'forced_errors' => 0, 'unforced_errors' => 0, 'dinding' => 0, 'total_menang' => 0
        ];
        $statsTimB = [
            'winners' => 0, 'forced_errors' => 0, 'unforced_errors' => 0, 'dinding' => 0, 'total_menang' => 0
        ];

        // Daftar pemain untuk membedakan tim
        $pemainTimA = [$pertandingan->tim_a_pemain_kiri, $pertandingan->tim_a_pemain_kanan];
        $pemainTimB = [$pertandingan->tim_b_pemain_kiri, $pertandingan->tim_b_pemain_kanan];

        foreach ($logs as $log) {
            // Hitung Total Menang
            if ($log->tim_pemenang_poin === 'Tim A') $statsTimA['total_menang']++;
            if ($log->tim_pemenang_poin === 'Tim B') $statsTimB['total_menang']++;

            // Hitung Dinding Per Tim (Siapa pemenangnya, dia yang tercatat)
            if ($log->libatkan_dinding == '1') {
                if ($log->tim_pemenang_poin === 'Tim A') $statsTimA['dinding']++;
                else if ($log->tim_pemenang_poin === 'Tim B') $statsTimB['dinding']++;
            }

            // Hitung Winner & Error berdasarkan Pemain Penghasil
            if (in_array($log->pemain_penghasil_poin, $pemainTimA)) {
                if ($log->jenis_akhir_poin === 'Winner') $statsTimA['winners']++;
                if ($log->jenis_akhir_poin === 'Forced error') $statsTimA['forced_errors']++;
                if ($log->jenis_akhir_poin === 'Unforced error') $statsTimA['unforced_errors']++;
            } else if (in_array($log->pemain_penghasil_poin, $pemainTimB)) {
                if ($log->jenis_akhir_poin === 'Winner') $statsTimB['winners']++;
                if ($log->jenis_akhir_poin === 'Forced error') $statsTimB['forced_errors']++;
                if ($log->jenis_akhir_poin === 'Unforced error') $statsTimB['unforced_errors']++;
            }
        }

        // Tentukan Pemenang Match Sederhana (Berdasarkan total poin untuk prototipe)
        $pemenangMatch = $statsTimA['total_menang'] > $statsTimB['total_menang'] ? 'Tim A' : 'Tim B';

        // --- 3. STATISTIK INDIVIDU (Winner & Error per Pemain) ---
        $pemainList = [
            $pertandingan->tim_a_pemain_kiri, $pertandingan->tim_a_pemain_kanan,
            $pertandingan->tim_b_pemain_kiri, $pertandingan->tim_b_pemain_kanan
        ];
        
        $errorStats = [];
        foreach ($pemainList as $pemain) {
            $winner = $logs->where('pemain_penghasil_poin', $pemain)->where('jenis_akhir_poin', 'Winner')->count();
            // Gabungkan forced dan unforced untuk total error individu
            $error = $logs->where('pemain_penghasil_poin', $pemain)
                          ->whereIn('jenis_akhir_poin', ['Forced error', 'Unforced error', 'Kena dinding'])->count();
            
            $errorStats[$pemain] = ['winner' => $winner, 'error' => $error];
        }

        $stateTerakhir = $pertandingan->state_terakhir ? json_decode($pertandingan->state_terakhir, true) : null;
        $historiSetA = $stateTerakhir['historiSetA'] ?? [0,0,0];
        $historiSetB = $stateTerakhir['historiSetB'] ?? [0,0,0];

        // Hitung durasi pertandingan
        $durasi = $pertandingan->created_at->diff($pertandingan->updated_at);
        $durasiFormat = '';
        if ($durasi->h > 0) {
            $durasiFormat .= $durasi->h . 'j ';
        }
        $durasiFormat .= $durasi->i . 'm';
        if ($durasi->h == 0 && $durasi->i == 0) {
            $durasiFormat = '< 1m';
        }

        return view('summary', compact(
            'pertandingan', 'totalPoin', 'persentaseDinding', 
            'statsTimA', 'statsTimB', 'pemenangMatch', 'errorStats',
            'historiSetA', 'historiSetB', 'durasiFormat'
        ));
    }
}