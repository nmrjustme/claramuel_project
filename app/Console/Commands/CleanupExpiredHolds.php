<?php

namespace App\Console\Commands;

use App\Models\RoomHold;
use Illuminate\Console\Command;

class CleanupExpiredHolds extends Command
{
    protected $signature = 'room-holds:cleanup';
    protected $description = 'Clean up expired room holds';

    public function handle()
    {
        $expired = RoomHold::where('expires_at', '<', now())
                          ->where('status', 'pending')
                          ->update(['status' => 'expired']);
        
        $this->info("Cleaned up {$expired} expired holds.");
        
        // Also clean up holds older than 1 day regardless of status
        $oldHolds = RoomHold::where('created_at', '<', now()->subDay())
                           ->delete();
        
        $this->info("Removed {$oldHolds} old hold records.");
        
        return Command::SUCCESS;
    }
}