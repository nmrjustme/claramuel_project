<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\FacilityBookingLog;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('admin.bookings', function ($user) {
    return !is_null($user); // Or your specific authorization logic
    // For more complex authorization:
    // return ['id' => $user->id, 'name' => $user->name];
});

Broadcast::channel('admin.bookings.{bookingId}', function ($user, $bookingId) {
    return $user->id === FacilityBookingLog::findOrFail($bookingId)->user_id;
});

