<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RoomHold;

class CleanupExpiredRoomHolds extends Command
{
    protected $signature = 'room-holds:cleanup';
    protected $description = 'Remove expired room holds from database';

    public function handle()
    {
        $expired = RoomHold::where('expires_at', '<', now())
                          ->orWhere('status', 'expired')
                          ->delete();
        
        $this->info("Cleaned up {$expired} expired room holds.");
        return Command::SUCCESS;
    }
}
