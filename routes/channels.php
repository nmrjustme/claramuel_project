<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\FacilityBookingLog;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('booking-logs', function ($user) {
    return $user->hasRole('admin'); // Only allow admin users
});

Broadcast::channel('booking-logs.{bookingId}', function ($user, $bookingId) {
    return $user->id === FacilityBookingLog::findOrFail($bookingId)->user_id;
});