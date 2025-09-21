@extends('layouts.bookings')
@section('title', 'Payment Failed')
@section('bookings')
<x-header />
<div class="container mx-auto px-4 sm:px-6 py-8 sm:py-12 max-w-7xl">
      <!-- Progress Steps -->
      <x-progress-step :currentStep="3" :steps="[
            ['label' => 'Select Rooms'],
            ['label' => 'Your Details'],
            ['label' => 'Payment'],
            ['label' => 'Completed']
      ]" />

      <div class="flex items-center justify-center mt-10 sm:mt-16 px-2">
            <div class="max-w-lg w-full rounded-lg p-8 text-center border border-lightgray sm:p-10 flex flex-col items-center sm:max-w-md md:max-w-lg transition-all duration-300">

                  <!-- Error Icon -->
                  <div class="flex justify-center mb-6 animate-bounce">
                        <div class="w-20 h-20 flex items-center justify-center rounded-full bg-red-50 shadow-inner">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-red-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M18.364 5.636l-12.728 12.728M5.636 5.636l12.728 12.728" />
                              </svg>
                        </div>
                  </div>

                  <!-- Title -->
                  <h1 class="text-3xl font-extrabold text-red-600 mb-3">Payment {{ ucfirst($reason) }}</h1>

                  <!-- Message -->
                  <p class="text-gray-700 mb-2 text-lg">
                        Unfortunately, your payment could not be processed.
                  </p>
                  <p class="text-gray-500 mb-6 text-sm">
                        Reason: <span class="font-semibold text-gray-800">{{ ucfirst($reason) }}</span>
                  </p>

                  <!-- Order reference -->
                  <div class="bg-gray-100 rounded-lg p-4 text-left mb-6 w-full">
                        <p class="text-sm text-gray-600 mb-1">Order Reference:</p>
                        <p class="text-lg font-semibold text-gray-900 tracking-wide">{{ $order }}</p>
                  </div>

                  <!-- Action Buttons -->
                  <div class="flex flex-col md:flex-row gap-4 justify-center w-full">
                        <a href="{{ route('dashboard.bookings') }}"
                              class="w-full md:w-auto px-6 py-3 bg-red-600 text-white rounded-lg shadow-md hover:bg-red-700 hover:shadow-lg transition-all duration-200">
                              Try to book again
                        </a>
                        <a href="/contact"
                              class="w-full md:w-auto px-6 py-3 bg-gray-100 text-gray-700 rounded-lg shadow-md hover:bg-gray-200 hover:shadow-lg transition-all duration-200">
                              Contact Support
                        </a>
                  </div>
            </div>
      </div>
</div>
@endsection
