@extends('layouts.app')
@section('title', 'Dashboard')
@section('dashboard')
    <style>
        /* Custom CSS for smooth transitions */
        .mega-menu {
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .group:hover .mega-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        /* Sidebar transition */
        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
        }

        .sidebar.open {
            transform: translateX(0);
        }
    </style>
    
    <!-- accommodations section -->
    @include('customer_pages.accommodation')
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"> </div>
    <script>
        // Toggle Sidebar
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarClose = document.getElementById('sidebarClose');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.add('open');
            overlay.classList.remove('hidden');
        });

        sidebarClose.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.add('hidden');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.add('hidden');
        });
    </script>
@endsection