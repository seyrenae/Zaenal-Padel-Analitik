<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateSkorPadel implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $poinTimA;
    public $poinTimB;
    public $gameTimA;
    public $gameTimB;
    public $historiSetA;  // BARU: Array Riwayat Set A
    public $historiSetB;  // BARU: Array Riwayat Set B
    public $namaTimA;
    public $namaTimB;
    public $aksiTerakhir;
    public $statusMatch; // BARU: Untuk mengecek apakah match selesai
    public $matchId; // BARU: ID Pertandingan untuk channel dinamis
    public $currentServer;

    public function __construct($requestPayload)
    {
        $this->matchId = $requestPayload['match_id'] ?? 1;
        $this->poinTimA = $requestPayload['poinTimA'] ?? '0';
        $this->poinTimB = $requestPayload['poinTimB'] ?? '0';
        $this->gameTimA = $requestPayload['gameTimA'] ?? 0;
        $this->gameTimB = $requestPayload['gameTimB'] ?? 0;
        $this->historiSetA = $requestPayload['historiSetA'] ?? [0, 0, 0];
        $this->historiSetB = $requestPayload['historiSetB'] ?? [0, 0, 0];
        $this->namaTimA = $requestPayload['namaTimA'] ?? 'Tim A';
        $this->namaTimB = $requestPayload['namaTimB'] ?? 'Tim B';
        $this->aksiTerakhir = $requestPayload['aksi'] ?? '';
        $this->statusMatch = $requestPayload['statusMatch'] ?? 'berjalan';
        $this->currentServer = $requestPayload['currentServer'] ?? 'Tim A';
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('pertandingan-padel.' . $this->matchId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'UpdateSkorPadel';
    }
}