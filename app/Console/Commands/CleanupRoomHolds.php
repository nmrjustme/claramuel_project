<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RoomHoldService;

class CleanupRoomHolds extends Command
{
    protected $signature = 'room-holds:cleanup';
    protected $description = 'Clean up expired room holds';

    public function handle(RoomHoldService $roomHoldService): void
    {
        $this->info('Cleaning up expired room holds...');
        $roomHoldService->cleanupExpiredHolds();
        $this->info('Done!');
    }
}