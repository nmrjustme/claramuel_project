@extends('layouts.bookings')
@section('title', 'Customer Information')
@section('bookings')
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white p-8 rounded-lg border border-lightgray max-w-md w-full text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h2 class="text-lg font-medium text-gray-900 mb-2">Payment Submitted</h2>
            <p class="text-gray-600 mb-6">Your booking is currently under verification.</p>
        </div>
    </div>
@endsection
