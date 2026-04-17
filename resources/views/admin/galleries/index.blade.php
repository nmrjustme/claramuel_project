@extends('layouts.admin')
@section('title', 'Gallery Management')

@php
$active = 'gallery';
@endphp

@section('content_css')
<style>
    /* Enhanced CSS with improved design while maintaining theme */
    
    /* Custom radio button styling */
    input[type="radio"] {
        width: 1rem;
        height: 1rem;
    }

    input[type="radio"]:checked {
        background-color: #ef4444;
        border-color: #ef4444;
    }

    /* Status indicator in preview */
    .status-indicator {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 6px;
    }

    .status-active {
        background-color: #10b981;
    }

    .status-inactive {
        background-color: #6b7280;
    }
    
    .min-h-screen {
        min-height: 100vh;
        position: relative;
    }

    /* Enhanced Header */
    .page-header {
        background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 50%, #b91c1c 100%);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        position: relative;
        overflow: hidden;
    }
    
    .page-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23dc2626' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
        opacity: 0.3;
    }

    /* Enhanced Status Cards */
    .status-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .status-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .status-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 5px;
        height: 100%;
    }

    .status-card.total-galleries::before {
        background: linear-gradient(to bottom, #2563EB, #1D4ED8);
    }

    .status-card.active-galleries::before {
        background: linear-gradient(to bottom, #059669, #047857);
    }

    .status-card.total-images::before {
        background: linear-gradient(to bottom, #7C3AED, #6D28D9);
    }

    .status-card.featured-images::before {
        background: linear-gradient(to bottom, #dc2626, #b91c1c);
    }

    .status-card .card-content {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .status-card .text-content {
        flex: 1;
    }

    .status-card .icon-wrapper {
        width: 3rem;
        height: 3rem;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 1rem;
        transition: all 0.3s ease;
    }

    .status-card:hover .icon-wrapper {
        transform: scale(1.1);
    }

    .status-card.total-galleries .icon-wrapper {
        background: linear-gradient(135deg, #EFF6FF, #DBEAFE);
    }

    .status-card.active-galleries .icon-wrapper {
        background: linear-gradient(135deg, #ECFDF5, #D1FAE5);
    }

    .status-card.total-images .icon-wrapper {
        background: linear-gradient(135deg, #F5F3FF, #EDE9FE);
    }

    .status-card.featured-images .icon-wrapper {
        background: linear-gradient(135deg, #FEF2F2, #FECACA);
    }

    .status-card .stat-value {
        font-size: 1.875rem;
        font-weight: 800;
        line-height: 1;
        margin: 0.5rem 0 0.25rem;
        font-family: 'Inter', sans-serif;
    }

    .status-card.total-galleries .stat-value {
        color: #1E40AF;
    }

    .status-card.active-galleries .stat-value {
        color: #065F46;
    }

    .status-card.total-images .stat-value {
        color: #5B21B6;
    }

    .status-card.featured-images .stat-value {
        color: #DC2626;
    }

    .status-card .stat-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #4B5563;
        letter-spacing: 0.025em;
    }

    /* Enhanced Gallery Grid Styles */
    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.5rem;
        margin-top: 1rem;
    }

    .gallery-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
        position: relative;
    }

    .gallery-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    }

    .gallery-card-header {
        position: relative;
        height: 200px;
        overflow: hidden;
    }

    .gallery-card-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .gallery-card:hover .gallery-card-image {
        transform: scale(1.08);
    }

    .gallery-card-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to bottom, transparent 40%, rgba(0, 0, 0, 0.8));
        display: flex;
        align-items: flex-end;
        padding: 1rem;
        opacity: 0.9;
    }

    .gallery-card-badge {
        background: rgba(239, 68, 68, 0.95);
        color: white;
        padding: 0.35rem 0.85rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .gallery-card-content {
        padding: 1.5rem;
    }

    .gallery-card-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
        line-height: 1.4;
    }

    .gallery-card-description {
        color: #6b7280;
        font-size: 0.875rem;
        line-height: 1.5;
        margin-bottom: 1rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .gallery-card-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.75rem;
        color: #9ca3af;
        margin-bottom: 1rem;
    }

    .gallery-card-status {
        display: flex;
        align-items: center;
    }

    .gallery-card-actions {
        display: flex;
        gap: 0.5rem;
        margin-top: 1rem;
        flex-wrap: wrap;
    }

    /* Enhanced Button Styles */
    .btn {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        transition: all 0.2s ease;
        cursor: pointer;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.75rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        box-shadow: 0 4px 8px rgba(239, 68, 68, 0.3);
        transform: translateY(-2px);
    }

    .btn-secondary {
        background: linear-gradient(135deg, #6b7280, #4b5563);
        color: white;
    }

    .btn-secondary:hover {
        background: linear-gradient(135deg, #4b5563, #374151);
        box-shadow: 0 4px 8px rgba(107, 114, 128, 0.3);
        transform: translateY(-2px);
    }

    .btn-outline {
        background: transparent;
        border: 1px solid #d1d5db;
        color: #374151;
    }

    .btn-outline:hover {
        background: #f9fafb;
        border-color: #9ca3af;
        transform: translateY(-2px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .btn-danger {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        color: white;
    }

    .btn-danger:hover {
        background: linear-gradient(135deg, #b91c1c, #991b1b);
        box-shadow: 0 4px 8px rgba(220, 38, 38, 0.3);
        transform: translateY(-2px);
    }

    /* Enhanced Image Grid Styles */
    .image-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-top: 1rem;
    }

    .image-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }

    .image-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    .image-header {
        position: relative;
        height: 200px;
        overflow: hidden;
    }

    .image-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .image-card:hover .image-img {
        transform: scale(1.05);
    }

    .image-badge {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        background: #ef4444;
        color: white;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 700;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .image-content {
        padding: 1.25rem;
    }

    .image-title {
        font-size: 1rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
        line-height: 1.4;
    }

    .image-caption {
        color: #6b7280;
        font-size: 0.8rem;
        line-height: 1.4;
        margin-bottom: 0.75rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .image-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.75rem;
        color: #9ca3af;
        margin-bottom: 1rem;
    }

    .image-actions {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    /* Enhanced Modal Styles */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(4px);
    }

    .modal.show {
        display: flex;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .modal-content {
        background: white;
        border-radius: 16px;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        animation: slideUp 0.3s ease;
    }

    @keyframes slideUp {
        from { transform: translateY(30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .modal-header {
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f9fafb;
        border-radius: 16px 16px 0 0;
    }

    .modal-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #6b7280;
        transition: color 0.2s ease;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-close:hover {
        color: #374151;
        background: #f3f4f6;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .form-input {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        background: #f9fafb;
    }

    .form-input:focus {
        outline: none;
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        background: white;
    }

    .form-textarea {
        resize: vertical;
        min-height: 100px;
    }

    .form-select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.875rem;
        background: #f9fafb;
        transition: all 0.2s ease;
    }

    .form-select:focus {
        outline: none;
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        background: white;
    }

    .form-checkbox {
        width: 1rem;
        height: 1rem;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        transition: all 0.2s ease;
    }

    .form-checkbox:checked {
        background-color: #ef4444;
        border-color: #ef4444;
    }

    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .modal-footer {
        padding: 1.5rem;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        background: #f9fafb;
        border-radius: 0 0 16px 16px;
    }

    /* Enhanced Loading States */
    .loading {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 3rem;
        flex-direction: column;
    }

    .spinner {
        width: 2.5rem;
        height: 2.5rem;
        border: 3px solid #e5e7eb;
        border-top: 3px solid #ef4444;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Enhanced Empty States */
    .empty-state {
        text-align: center;
        padding: 4rem 1rem;
        color: #6b7280;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        border: 1px solid #e5e7eb;
    }

    .empty-state-icon {
        font-size: 4rem;
        margin-bottom: 1.5rem;
        opacity: 0.5;
    }

    /* Enhanced Filter Bar */
    .filter-bar {
        background: white;
        padding: 1.25rem;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        margin-bottom: 1.5rem;
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .search-box {
        position: relative;
        flex: 1;
        min-width: 280px;
    }

    .search-input {
        width: 100%;
        padding: 0.75rem 2.5rem 0.75rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.875rem;
        background: #f9fafb;
        transition: all 0.2s ease;
    }

    .search-input:focus {
        outline: none;
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        background: white;
    }

    .search-icon {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
    }

    /* Enhanced Tabs */
    .tabs {
        display: flex;
        border-bottom: 1px solid #e5e7eb;
        margin-bottom: 1.5rem;
        background: white;
        border-radius: 12px 12px 0 0;
        padding: 0 1rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .tab {
        padding: 1rem 1.5rem;
        background: none;
        border: none;
        border-bottom: 3px solid transparent;
        color: #6b7280;
        cursor: pointer;
        font-size: 0.9rem;
        font-weight: 600;
        transition: all 0.2s ease;
        position: relative;
    }

    .tab.active {
        color: #ef4444;
        border-bottom-color: #ef4444;
    }

    .tab:hover:not(.active) {
        color: #374151;
        background: #f9fafb;
    }

    .tab-content {
        display: block;
    }

    /* Enhanced Preview Items */
    .preview-item {
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 1rem;
        background: white;
        margin-bottom: 1rem;
        transition: all 0.2s ease;
    }

    .preview-item:hover {
        border-color: #d1d5db;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    /* Responsive Improvements */
    @media (max-width: 1024px) {
        .gallery-grid {
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        }
        
        .image-grid {
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        }
    }

    @media (max-width: 768px) {
        .gallery-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .image-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        }

        .filter-bar {
            flex-direction: column;
            align-items: stretch;
            gap: 0.75rem;
        }

        .search-box {
            min-width: auto;
        }
        
        .gallery-card-actions {
            flex-direction: column;
        }
        
        .gallery-card-actions .btn {
            justify-content: center;
            width: 100%;
        }
        
        .tabs {
            flex-direction: column;
            padding: 0;
        }
        
        .tab {
            border-bottom: none;
            border-left: 3px solid transparent;
            padding: 0.75rem 1rem;
        }
        
        .tab.active {
            border-left-color: #ef4444;
            border-bottom-color: transparent;
        }
    }

    @media (max-width: 480px) {
        .status-card .card-content {
            flex-direction: column;
        }
        
        .status-card .icon-wrapper {
            margin-left: 0;
            margin-top: 1rem;
            align-self: flex-start;
        }
        
        .image-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Add this to your existing CSS section */
.modal-backdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
}

.modal-backdrop.show {
    display: flex;
}

/* Ensure modal is above backdrop */
.modal {
    z-index: 1001;
}

/* Add to your existing CSS */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    z-index: 1000;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(4px);
    padding: 1rem;
}

.modal.show {
    display: flex;
}

/* Responsive modal content */
.modal > div {
    width: 100%;
    max-width: 95vw;
    max-height: 90vh;
    overflow-y: auto;
}

@media (min-width: 640px) {
    .modal > div {
        max-width: 90vw;
    }
}

@media (min-width: 768px) {
    .modal > div {
        max-width: 80vw;
    }
}
</style>
@endsection

@section('content')
<div class="min-h-screen px-4 sm:px-5 md:px-6 py-4 sm:py-5 md:py-6">
    <!-- Enhanced Header -->
    <div class="page-header mb-4 sm:mb-6">
        <div class="p-4 sm:p-6 md:p-8 text-white relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-white/0"></div>
            <div class="relative z-10">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold truncate">Gallery Management</h1>
                        @auth
                        <p class="opacity-90 mt-1 sm:mt-2 text-sm sm:text-base truncate">
                            Welcome back, {{ auth()->user()->firstname }}! Manage your galleries and images.
                        </p>
                        @endauth
                    </div>
                    <div class="flex items-center space-x-3 sm:space-x-4 flex-shrink-0">
                        <a href="/admin/galleries/create" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            New Gallery
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 md:gap-6 mb-4 sm:mb-6">
        <div class="status-card total-galleries">
            <div class="card-content">
                <div class="text-content">
                    <p class="stat-label">Total Galleries</p>
                    <h3 class="stat-value" id="total-galleries">{{ $totalGalleries }}</h3>
                </div>
                <div class="icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="status-card active-galleries">
            <div class="card-content">
                <div class="text-content">
                    <p class="stat-label">Active Galleries</p>
                    <h3 class="stat-value" id="active-galleries">{{ $activeGalleries }}</h3>
                </div>
                <div class="icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="status-card total-images">
            <div class="card-content">
                <div class="text-content">
                    <p class="stat-label">Total Images</p>
                    <h3 class="stat-value" id="total-images">{{ $totalImages }}</h3>
                </div>
                <div class="icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="status-card featured-images">
            <div class="card-content">
                <div class="text-content">
                    <p class="stat-label">Featured Images</p>
                    <h3 class="stat-value" id="featured-images">{{ $featuredImages }}</h3>
                </div>
                <div class="icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="tabs">
        <button class="tab active" onclick="switchTab('galleries')">Galleries</button>
        <button class="tab" onclick="switchTab('images')">All Images</button>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <div class="search-box">
            <input type="text" id="search-input" class="search-input" placeholder="Search galleries..." onkeyup="filterGalleries()">
            <svg class="search-icon h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
        <select id="category-filter" class="form-select" style="width: auto; min-width: 150px;" onchange="filterGalleries()">
            <option value="all">All Categories</option>
            @foreach($categories as $category)
                <option value="{{ $category }}">{{ $category }}</option>
            @endforeach
        </select>
        <select id="status-filter" class="form-select" style="width: auto; min-width: 150px;" onchange="filterGalleries()">
            <option value="all">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>

    <!-- Galleries Tab Content -->
    <div id="galleries-tab" class="tab-content">
        <div id="galleries-container">
            @if($galleries->count() > 0)
                <div class="gallery-grid">
                    @foreach($galleries as $gallery)
                    @php
                        // SIMPLE APPROACH: Get first image for thumbnail
                        $firstImage = $gallery->images->sortBy('sort_order')->first();
                    @endphp
                    <div class="gallery-card" data-gallery-id="{{ $gallery->id }}">
                        <div class="gallery-card-header">
                            <img src="{{ $firstImage ? asset('storage/' . $firstImage->image_path) : '/images/default-gallery.jpg' }}" 
                                 alt="{{ $firstImage ? ($firstImage->image_alt ?? $firstImage->title) : 'No image' }}" 
                                 class="gallery-card-image"
                                 onerror="this.src='/images/default-gallery.jpg'">
                            <div class="gallery-card-overlay">
                                <span class="gallery-card-badge">{{ $gallery->images_count }} images</span>
                            </div>
                        </div>
                        <div class="gallery-card-content">
                            <h3 class="gallery-card-title">{{ $gallery->title }}</h3>
                            <p class="gallery-card-description">{{ $gallery->description ?? 'No description' }}</p>
                            <div class="gallery-card-meta">
                                <span>{{ $gallery->category }}</span>
                                <div class="gallery-card-status">
                                    <span class="status-indicator {{ $gallery->is_active ? 'status-active' : 'status-inactive' }}"></span>
                                    <span class="{{ $gallery->is_active ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $gallery->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                            <div class="gallery-card-actions">
                                <a href="/admin/galleries/{{ $gallery->id }}" class="btn btn-outline btn-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    View
                                </a>
                                <button class="btn btn-outline btn-sm" onclick="showUploadModal({{ $gallery->id }})">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    Upload
                                </button>
                                <a href="/admin/galleries/{{ $gallery->id }}/edit" class="btn btn-outline btn-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit
                                </a>
                                <button class="btn btn-danger btn-sm" onclick="confirmDeleteGallery({{ $gallery->id }}, '{{ addslashes($gallery->title) }}')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                @if($galleries->hasPages())
                <div class="mt-6 flex justify-center">
                    {{ $galleries->links() }}
                </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">📷</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No galleries found</h3>
                    <p class="text-gray-500 mb-6">Create your first gallery to get started</p>
                    <a href="/admin/galleries/create" class="btn btn-primary">
                        Create Gallery
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Images Tab Content -->
    <div id="images-tab" class="tab-content" style="display: none;">
        <div id="images-container">
            <div class="loading">
                <div class="spinner"></div>
                <p class="mt-2 text-gray-500">Loading images...</p>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Upload Modal (Consistent with Show Blade) -->
<div id="upload-modal" class="modal">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl mx-4 animate-slide-up">
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-900">Upload Images to Gallery</h3>
            <button onclick="hideUploadModal()" class="text-gray-400 hover:text-gray-600 transition-colors duration-200 p-2 rounded-full hover:bg-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="p-6">
            <form id="upload-form">
                @csrf
                <input type="hidden" id="upload-gallery-id" name="gallery_id" value="">
                
                <div class="space-y-6">
                    <div class="form-group">
                        <label class="block text-sm font-semibold text-gray-900 mb-3">Select Images</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-2xl p-8 text-center hover:border-red-300 transition-colors duration-200">
                            <input type="file" id="upload-images" class="hidden" multiple accept="image/*">
                            <div class="cursor-pointer" onclick="document.getElementById('upload-images').click()">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-lg font-medium text-gray-900 mb-2">Drop images here or click to browse</p>
                                <p class="text-sm text-gray-500">Supports JPG, PNG, WebP • Max 10MB per image</p>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-3">You can edit titles, alt text, and status for each image before uploading</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="upload-category" class="block text-sm font-semibold text-gray-900 mb-2">Default Category</label>
                        <input type="text" id="upload-category" name="category" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm" placeholder="Enter category (e.g., nature, portrait, event)">
                    </div>
                    
                    <!-- File Preview & Edit Section -->
                    <div id="upload-preview" class="hidden">
                        <p class="text-sm font-semibold text-gray-900 mb-4">Image Preview & Edit:</p>
                        <div id="preview-container" class="space-y-4 max-h-96 overflow-y-auto p-4 border border-gray-200 rounded-xl bg-gray-50"></div>
                    </div>
                </div>
            </form>
        </div>
        <div class="flex justify-end gap-3 p-6 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
            <button onclick="hideUploadModal()" class="btn btn-outline">
                Cancel
            </button>
            <button onclick="uploadImages()" class="btn btn-primary">
                Upload Images
            </button>
        </div>
    </div>
</div>

<!-- Delete Gallery Confirmation Modal -->
<div id="delete-gallery-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Confirm Delete</h3>
            <button class="modal-close" onclick="hideDeleteGalleryModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="flex items-center gap-3 mb-4">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Confirm Delete</h3>
                </div>
            </div>
            <p class="text-gray-600 mb-2">Are you sure you want to delete the gallery "<span id="delete-gallery-title" class="font-medium"></span>"?</p>
            <p class="text-sm text-red-600">This action cannot be undone and all images in this gallery will be permanently removed.</p>
        </div>
        <div class="modal-footer">
            <button onclick="hideDeleteGalleryModal()" class="btn btn-outline">Cancel</button>
            <button id="confirm-gallery-delete-btn" class="btn btn-danger">Delete Gallery</button>
        </div>
    </div>
</div>

<!-- Image Edit Modal -->
<div id="image-edit-modal" class="modal">
    <!-- This will be populated by JavaScript -->
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content_js')
<script>
    // Global variables
    let currentTab = 'galleries';
    let galleries = @json($galleries->items());
    let images = [];
    let selectedFiles = [];

    // Tab switching
    function switchTab(tabName) {
        currentTab = tabName;
        
        // Update tabs
        document.querySelectorAll('.tab').forEach(tab => {
            tab.classList.remove('active');
        });
        event.target.classList.add('active');
        
        // Show/hide content
        document.getElementById('galleries-tab').style.display = tabName === 'galleries' ? 'block' : 'none';
        document.getElementById('images-tab').style.display = tabName === 'images' ? 'block' : 'none';
        
        // Load content if needed
        if (tabName === 'images') {
            if (images.length === 0) {
                loadAllImages();
            } else {
                renderImages();
            }
        }
    }

    // Load all images
    async function loadAllImages() {
        try {
            const container = document.getElementById('images-container');
            container.innerHTML = `
                <div class="loading">
                    <div class="spinner"></div>
                    <p class="mt-2 text-gray-500">Loading images...</p>
                </div>
            `;

            const response = await fetch('/admin/galleries/images/all');
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success && data.images) {
                images = data.images;
                renderImages();
            } else {
                throw new Error(data.message || 'No images found');
            }
            
        } catch (error) {
            console.error('Error loading images:', error);
            document.getElementById('images-container').innerHTML = `
                <div class="empty-state">
                    <div class="empty-state-icon">🖼️</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Error loading images</h3>
                    <p class="text-gray-500 mb-4">${error.message}</p>
                    <button class="btn btn-primary" onclick="loadAllImages()">Retry</button>
                </div>
            `;
        }
    }

    // Render images
    function renderImages(filteredImages = null) {
        const container = document.getElementById('images-container');
        const data = filteredImages || images;
        
        if (!data || data.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <div class="empty-state-icon">🖼️</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No images found</h3>
                    <p class="text-gray-500 mb-6">Upload some images to get started</p>
                    <button class="btn btn-primary" onclick="switchTab('galleries')">
                        Go to Galleries
                    </button>
                </div>
            `;
            return;
        }
        
        let html = '<div class="image-grid">';
        
        data.forEach(image => {
            const imagePath = image.image_path ? `/storage/${image.image_path}` : '/images/default-image.jpg';
            const imageAlt = image.image_alt || image.title || 'Gallery Image';
            const galleryTitle = image.gallery?.title || 'No Gallery';
            
            html += `
                <div class="image-card" data-image-id="${image.id}">
                    <div class="image-header">
                        <img src="${imagePath}" 
                             alt="${imageAlt}" 
                             class="image-img"
                             onerror="this.src='/images/default-image.jpg'">
                        ${image.is_featured ? '<span class="image-badge">Featured</span>' : ''}
                        ${!image.is_active ? '<span class="image-badge" style="background: #6b7280; top: 3.5rem;">Inactive</span>' : ''}
                    </div>
                    <div class="image-content">
                        <h4 class="image-title">${image.title || 'Untitled Image'}</h4>
                        <p class="image-caption">${image.caption || 'No description available'}</p>
                        <div class="image-meta">
                            <span>${galleryTitle}</span>
                            <span>Order: ${image.sort_order || 0}</span>
                        </div>
                        <div class="image-actions">
                            <button class="btn btn-outline" onclick="editImage(${image.id})">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </button>
                            ${image.is_featured ? 
                                `<button class="btn btn-secondary" onclick="removeFeaturedImage(${image.id})">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Featured
                                </button>` : 
                                `<button class="btn btn-primary" onclick="setFeaturedImage(${image.id})">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                    </svg>
                                    Feature
                                </button>`
                            }
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        container.innerHTML = html;
    }

    // Filter galleries
    function filterGalleries() {
        const searchTerm = document.getElementById('search-input').value.toLowerCase();
        const categoryFilter = document.getElementById('category-filter').value;
        const statusFilter = document.getElementById('status-filter').value;
        
        const filtered = galleries.filter(gallery => {
            const matchesSearch = gallery.title.toLowerCase().includes(searchTerm) || 
                                 (gallery.description && gallery.description.toLowerCase().includes(searchTerm));
            const matchesCategory = categoryFilter === 'all' || gallery.category === categoryFilter;
            const matchesStatus = statusFilter === 'all' || 
                                (statusFilter === 'active' && gallery.is_active) ||
                                (statusFilter === 'inactive' && !gallery.is_active);
            
            return matchesSearch && matchesCategory && matchesStatus;
        });
        
        renderFilteredGalleries(filtered);
    }

    function renderFilteredGalleries(filteredGalleries) {
        const container = document.getElementById('galleries-container');
        
        if (filteredGalleries.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <div class="empty-state-icon">📷</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No galleries found</h3>
                    <p class="text-gray-500 mb-6">Try adjusting your search filters</p>
                    <button class="btn btn-outline" onclick="resetFilters()">Reset Filters</button>
                </div>
            `;
            return;
        }
        
        let html = '<div class="gallery-grid">';
        
        filteredGalleries.forEach(gallery => {
            const firstImage = gallery.images && gallery.images.length > 0 
                ? gallery.images.sort((a, b) => a.sort_order - b.sort_order)[0]
                : null;
                
            html += `
                <div class="gallery-card" data-gallery-id="${gallery.id}">
                    <div class="gallery-card-header">
                        <img src="${firstImage ? '/storage/' + firstImage.image_path : '/images/default-gallery.jpg'}" 
                             alt="${firstImage ? (firstImage.image_alt || firstImage.title) : 'No image'}" 
                             class="gallery-card-image"
                             onerror="this.src='/images/default-gallery.jpg'">
                        <div class="gallery-card-overlay">
                            <span class="gallery-card-badge">${gallery.images_count} images</span>
                        </div>
                    </div>
                    <div class="gallery-card-content">
                        <h3 class="gallery-card-title">${gallery.title}</h3>
                        <p class="gallery-card-description">${gallery.description || 'No description'}</p>
                        <div class="gallery-card-meta">
                            <span>${gallery.category}</span>
                            <span class="${gallery.is_active ? 'text-green-600' : 'text-red-600'}">
                                ${gallery.is_active ? 'Active' : 'Inactive'}
                            </span>
                        </div>
                        <div class="gallery-card-actions">
                            <a href="/admin/galleries/${gallery.id}" class="btn btn-primary btn-sm">View</a>
                            <button class="btn btn-outline btn-sm" onclick="showUploadModal(${gallery.id})">Upload</button>
                            <a href="/admin/galleries/${gallery.id}/edit" class="btn btn-outline btn-sm">Edit</a>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        container.innerHTML = html;
    }

    // Reset filters
    function resetFilters() {
        document.getElementById('search-input').value = '';
        document.getElementById('category-filter').value = 'all';
        document.getElementById('status-filter').value = 'all';
        filterGalleries();
    }

    // Modal functions
    function showUploadModal(galleryId) {
        document.getElementById('upload-gallery-id').value = galleryId;
        document.getElementById('upload-form').reset();
        document.getElementById('upload-preview').style.display = 'none';
        document.getElementById('upload-modal').classList.add('show');
        
        // Reset selected files
        selectedFiles = [];
        
        // Add preview functionality
        const uploadInput = document.getElementById('upload-images');
        uploadInput.onchange = function(e) {
            handleFileSelection(e.target.files);
        };
    }

    function hideUploadModal() {
        document.getElementById('upload-modal').classList.remove('show');
        
        // Reset everything
        selectedFiles = [];
        document.getElementById('upload-images').value = '';
        document.getElementById('upload-preview').style.display = 'none';
        document.getElementById('preview-container').innerHTML = '';
        document.getElementById('upload-category').value = '';
    }

    // Upload images
    async function uploadImages() {
        if (selectedFiles.length === 0) {
            showNotification('Please select at least one image to upload', 'error');
            return;
        }

        const galleryId = document.getElementById('upload-gallery-id').value;
        const category = document.getElementById('upload-category').value || '';

        const formData = new FormData();
        
        // Add CSRF token and basic data
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        formData.append('gallery_id', galleryId);
        formData.append('category', category);

        // Add each file with its custom data
        selectedFiles.forEach((file, index) => {
            formData.append('images[]', file);
            
            // Add custom title if provided
            const customTitle = file.customData?.title;
            if (customTitle && customTitle !== file.name.replace(/\.[^/.]+$/, "")) {
                formData.append(`titles[${index}]`, customTitle);
            } else {
                formData.append(`titles[${index}]`, file.name.replace(/\.[^/.]+$/, ""));
            }
            
            // Add custom alt text if provided
            const customAlt = file.customData?.alt;
            if (customAlt) {
                formData.append(`alt_texts[${index}]`, customAlt);
            } else {
                const defaultAlt = customTitle || file.name.replace(/\.[^/.]+$/, "");
                formData.append(`alt_texts[${index}]`, defaultAlt);
            }
            
            // Add active status
            const isActive = file.customData?.is_active !== false;
            formData.append(`active_status[${index}]`, isActive ? '1' : '0');
        });

        try {
            const uploadButton = document.querySelector('#upload-modal .btn-primary');
            const originalText = uploadButton.innerHTML;
            uploadButton.innerHTML = '<div class="spinner" style="width: 16px; height: 16px; border-width: 2px; border-top-color: white;"></div> Uploading...';
            uploadButton.disabled = true;

            const response = await fetch('/admin/galleries/upload', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });

            if (response.ok) {
                const result = await response.json();
                showNotification(result.message || selectedFiles.length + ' images uploaded successfully', 'success');
                hideUploadModal();
                
                // Reset for next upload
                selectedFiles = [];
                document.getElementById('upload-images').value = '';
                
                // Reload the page to reflect changes
                setTimeout(() => window.location.reload(), 1500);
            } else {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Failed to upload images');
            }
        } catch (error) {
            console.error('Error uploading images:', error);
            showNotification('Error uploading images: ' + error.message, 'error');
            
            // Reset button state
            const uploadButton = document.querySelector('#upload-modal .btn-primary');
            if (uploadButton) {
                uploadButton.innerHTML = 'Upload Images';
                uploadButton.disabled = false;
            }
        }
    }

    // File handling functions
    function handleFileSelection(files) {
        selectedFiles = Array.from(files);
        const previewContainer = document.getElementById('preview-container');
        const previewSection = document.getElementById('upload-preview');
        
        previewContainer.innerHTML = '';
        
        if (files.length > 0) {
            previewSection.style.display = 'block';
            
            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const fileName = file.name.replace(/\.[^/.]+$/, "");
                    const fileExtension = file.name.split('.').pop();
                    const fileSize = formatFileSize(file.size);
                    
                    const previewItem = document.createElement('div');
                    previewItem.className = 'bg-white p-4 rounded-xl border border-gray-200 shadow-sm';
                    previewItem.innerHTML = `
                        <div class="flex gap-4 items-start">
                            <div class="flex-shrink-0">
                                <img src="${e.target.result}" 
                                     class="w-20 h-20 object-cover rounded-lg border border-gray-300 shadow-sm"
                                     alt="Preview">
                            </div>
                            <div class="flex-1 space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                                    <input type="text" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm"
                                           value="${fileName}"
                                           data-file-index="${index}"
                                           placeholder="Enter image title"
                                           onchange="updateFileName(${index}, this.value)">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Alt Text</label>
                                    <input type="text" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm"
                                           value="${fileName}"
                                           data-file-index="${index}"
                                           placeholder="Enter alt text for accessibility"
                                           onchange="updateFileAlt(${index}, this.value)">
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" 
                                                   name="active_${index}" 
                                                   value="1" 
                                                   checked
                                                   onchange="updateFileActiveStatus(${index}, true)"
                                                   class="h-3 w-3 text-red-600 focus:ring-red-500">
                                            <span class="text-sm text-green-600 font-medium">Active</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" 
                                                   name="active_${index}" 
                                                   value="0"
                                                   onchange="updateFileActiveStatus(${index}, false)"
                                                   class="h-3 w-3 text-red-600 focus:ring-red-500">
                                            <span class="text-sm text-gray-600 font-medium">Inactive</span>
                                        </label>
                                    </div>
                                    
                                    <button type="button" 
                                            class="text-red-600 hover:text-red-800 text-sm font-medium"
                                            onclick="removeFile(${index})">
                                        Remove
                                    </button>
                                </div>
                                
                                <div class="text-xs text-gray-500">
                                    ${fileExtension.toUpperCase()} • ${fileSize}
                                </div>
                            </div>
                        </div>
                    `;
                    previewContainer.appendChild(previewItem);
                };
                reader.readAsDataURL(file);
            });
        } else {
            previewSection.style.display = 'none';
        }
    }

    function updateFileActiveStatus(fileIndex, isActive) {
        if (selectedFiles[fileIndex]) {
            if (!selectedFiles[fileIndex].customData) {
                selectedFiles[fileIndex].customData = {};
            }
            selectedFiles[fileIndex].customData.is_active = isActive;
        }
    }

    function updateFileName(fileIndex, newTitle) {
        if (selectedFiles[fileIndex]) {
            if (!selectedFiles[fileIndex].customData) {
                selectedFiles[fileIndex].customData = {};
            }
            selectedFiles[fileIndex].customData.title = newTitle;
        }
    }

    function updateFileAlt(fileIndex, newAlt) {
        if (selectedFiles[fileIndex]) {
            if (!selectedFiles[fileIndex].customData) {
                selectedFiles[fileIndex].customData = {};
            }
            selectedFiles[fileIndex].customData.alt = newAlt;
        }
    }

    function removeFile(fileIndex) {
        selectedFiles.splice(fileIndex, 1);
        
        const previewContainer = document.getElementById('preview-container');
        const previewSection = document.getElementById('upload-preview');
        
        previewContainer.innerHTML = '';
        
        if (selectedFiles.length > 0) {
            handleFileSelection(selectedFiles);
        } else {
            previewSection.style.display = 'none';
            document.getElementById('upload-images').value = '';
        }
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Set featured image
    async function setFeaturedImage(imageId) {
        try {
            const response = await fetch(`/admin/galleries/images/${imageId}/set-featured`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                }
            });
            
            if (response.ok) {
                showNotification('Image set as featured', 'success');
                loadAllImages();
            } else {
                throw new Error('Failed to set featured image');
            }
        } catch (error) {
            console.error('Error setting featured image:', error);
            showNotification('Error setting featured image', 'error');
        }
    }

    // Remove featured image
    async function removeFeaturedImage(imageId) {
        try {
            const response = await fetch(`/admin/galleries/images/${imageId}/remove-featured`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                }
            });
            
            if (response.ok) {
                showNotification('Image removed from featured', 'success');
                loadAllImages();
            } else {
                throw new Error('Failed to remove featured image');
            }
        } catch (error) {
            console.error('Error removing featured image:', error);
            showNotification('Error removing featured image', 'error');
        }
    }

    // Edit image
    async function editImage(imageId) {
        try {
            showImageEditModal('Loading...', '<div class="loading"><div class="spinner"></div><p class="mt-2 text-gray-500">Loading image data...</p></div>');
            
            const response = await fetch(`/admin/galleries/images/${imageId}`);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (!data.image) {
                throw new Error('Image data not found in response');
            }
            
            const image = data.image;
            showImageEditModal(`Edit Image: ${image.title || 'Untitled'}`, createImageEditForm(image));
            
        } catch (error) {
            console.error('Error loading image:', error);
            showNotification(`Error loading image data: ${error.message}`, 'error');
            hideImageEditModal();
        }
    }

    function createImageEditForm(image) {
        return `
            <form id="image-edit-form" class="space-y-6">
                <input type="hidden" name="id" value="${image.id}">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="form-group">
                            <label class="form-label" for="edit-image-title">Title</label>
                            <input type="text" id="edit-image-title" name="title" class="form-input" 
                                   value="${escapeHtml(image.title || '')}" placeholder="Image title">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="edit-image-alt">Alt Text</label>
                            <input type="text" id="edit-image-alt" name="image_alt" class="form-input" 
                                   value="${escapeHtml(image.image_alt || '')}" placeholder="Description for accessibility">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="edit-image-caption">Caption</label>
                            <textarea id="edit-image-caption" name="caption" class="form-input form-textarea" 
                                      placeholder="Image caption" rows="3">${escapeHtml(image.caption || '')}</textarea>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="form-group">
                            <label class="form-label" for="edit-image-category">Category</label>
                            <input type="text" id="edit-image-category" name="category" class="form-input" 
                                   value="${escapeHtml(image.category || '')}" placeholder="Any">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="edit-image-sort-order">Sort Order</label>
                            <input type="number" id="edit-image-sort-order" name="sort_order" class="form-input" 
                                   value="${image.sort_order || 0}">
                        </div>
                        
                        <div class="space-y-3 p-4 bg-gray-50 rounded-lg">
                            <div class="checkbox-group">
                                <input type="checkbox" id="edit-image-featured" name="is_featured" 
                                       class="form-checkbox" ${image.is_featured ? 'checked' : ''}>
                                <label class="form-label" for="edit-image-featured">Featured Image</label>
                            </div>
                            
                            <div class="checkbox-group">
                                <input type="checkbox" id="edit-image-active" name="is_active" 
                                       class="form-checkbox" ${image.is_active ? 'checked' : ''}>
                                <label class="form-label" for="edit-image-active">Active</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Current Image</label>
                    <div class="mt-2 border rounded-lg p-4 bg-gray-50">
                        <img src="/storage/${image.image_path}" 
                             alt="${image.image_alt || image.title}" 
                             class="max-w-full h-auto max-h-40 mx-auto rounded-lg shadow-sm"
                             onerror="this.src='/images/default-image.jpg'">
                        <p class="text-xs text-gray-500 text-center mt-2">${image.image_path}</p>
                    </div>
                </div>
            </form>
        `;
    }

    function showImageEditModal(title, content) {
        const modalHtml = `
            <div id="image-edit-modal" class="modal show">
                <div class="modal-content" style="max-width: 700px;">
                    <div class="modal-header">
                        <h3 class="modal-title">${title}</h3>
                        <button class="modal-close" onclick="hideImageEditModal()">&times;</button>
                    </div>
                    <div class="modal-body">
                        ${content}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline" onclick="hideImageEditModal()">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="saveImageChanges()">
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        const existingModal = document.getElementById('image-edit-modal');
        if (existingModal) {
            existingModal.remove();
        }
        
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    function hideImageEditModal() {
        const modal = document.getElementById('image-edit-modal');
        if (modal) {
            modal.remove();
        }
    }

    async function saveImageChanges() {
        const form = document.getElementById('image-edit-form');
        if (!form) {
            showNotification('Edit form not found', 'error');
            return;
        }

        const formData = new FormData(form);
        const imageId = formData.get('id');
        
        if (!imageId) {
            showNotification('Image ID not found', 'error');
            return;
        }

        try {
            const titleInput = form.querySelector('input[name="title"]');
            const altInput = form.querySelector('input[name="image_alt"]');
            const captionInput = form.querySelector('textarea[name="caption"]');
            const categoryInput = form.querySelector('input[name="category"]');
            const sortOrderInput = form.querySelector('input[name="sort_order"]');
            const featuredCheckbox = form.querySelector('input[name="is_featured"]');
            const activeCheckbox = form.querySelector('input[name="is_active"]');

            const data = {
                title: titleInput?.value || '',
                image_alt: altInput?.value || '',
                caption: captionInput?.value || '',
                category: categoryInput?.value || '',
                sort_order: parseInt(sortOrderInput?.value) || 0,
                is_featured: featuredCheckbox?.checked ? true : false,
                is_active: activeCheckbox?.checked ? true : false
            };

            const saveButton = document.querySelector('#image-edit-modal .btn-primary');
            if (saveButton) {
                const originalText = saveButton.textContent;
                saveButton.innerHTML = '<div class="spinner"></div> Saving...';
                saveButton.disabled = true;
            }

            const response = await fetch(`/admin/galleries/images/${imageId}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                const result = await response.json();
                showNotification('Image updated successfully', 'success');
                hideImageEditModal();
                
                if (currentTab === 'images') {
                    await loadAllImages();
                } else {
                    setTimeout(() => window.location.reload(), 1000);
                }
                
            } else {
                const errorData = await response.json();
                throw new Error(errorData.message || `Failed to update image: ${response.status}`);
            }
            
        } catch (error) {
            console.error('Error updating image:', error);
            showNotification('Error updating image: ' + error.message, 'error');
            
            const saveButton = document.querySelector('#image-edit-modal .btn-primary');
            if (saveButton) {
                saveButton.textContent = 'Save Changes';
                saveButton.disabled = false;
            }
        }
    }

    // Gallery deletion functionality
    let galleryToDelete = null;

    function confirmDeleteGallery(galleryId, galleryTitle) {
        galleryToDelete = galleryId;
        document.getElementById('delete-gallery-title').textContent = galleryTitle;
        document.getElementById('delete-gallery-modal').classList.add('show');
    }

    function hideDeleteGalleryModal() {
        galleryToDelete = null;
        document.getElementById('delete-gallery-modal').classList.remove('show');
    }

    async function deleteGallery() {
        if (!galleryToDelete) return;

        try {
            const deleteButton = document.getElementById('confirm-gallery-delete-btn');
            const originalText = deleteButton.innerHTML;
            deleteButton.innerHTML = '<div class="spinner" style="width: 16px; height: 16px; border-width: 2px; border-top-color: white;"></div> Deleting...';
            deleteButton.disabled = true;

            const response = await fetch(`/admin/galleries/${galleryToDelete}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                }
            });

            if (response.ok) {
                const result = await response.json();
                showNotification('Gallery deleted successfully', 'success');
                hideDeleteGalleryModal();
                
                const galleryCard = document.querySelector(`[data-gallery-id="${galleryToDelete}"]`);
                if (galleryCard) {
                    galleryCard.style.transform = 'scale(0.8)';
                    galleryCard.style.opacity = '0';
                    setTimeout(() => {
                        galleryCard.remove();
                        galleries = galleries.filter(gallery => gallery.id !== galleryToDelete);
                        updateStatistics();
                        
                        if (galleries.length === 0) {
                            document.getElementById('galleries-container').innerHTML = `
                                <div class="empty-state">
                                    <div class="empty-state-icon">📷</div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No galleries found</h3>
                                    <p class="text-gray-500 mb-6">Create your first gallery to get started</p>
                                    <a href="/admin/galleries/create" class="btn btn-primary">
                                        Create Gallery
                                    </a>
                                </div>
                            `;
                        }
                    }, 300);
                } else {
                    window.location.reload();
                }
            } else {
                await deleteGalleryWithPostSpoofing();
            }
        } catch (error) {
            console.error('Error with DELETE method:', error);
            await deleteGalleryWithPostSpoofing();
        }
    }

    async function deleteGalleryWithPostSpoofing() {
        try {
            const deleteButton = document.getElementById('confirm-gallery-delete-btn');
            
            const formData = new FormData();
            formData.append('_method', 'DELETE');
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            const response = await fetch(`/admin/galleries/${galleryToDelete}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: formData
            });

            if (response.ok) {
                showNotification('Gallery deleted successfully', 'success');
                hideDeleteGalleryModal();
                
                const galleryCard = document.querySelector(`[data-gallery-id="${galleryToDelete}"]`);
                if (galleryCard) {
                    galleryCard.style.transform = 'scale(0.8)';
                    galleryCard.style.opacity = '0';
                    setTimeout(() => {
                        galleryCard.remove();
                        galleries = galleries.filter(gallery => gallery.id !== galleryToDelete);
                        updateStatistics();
                        
                        if (galleries.length === 0) {
                            document.getElementById('galleries-container').innerHTML = `
                                <div class="empty-state">
                                    <div class="empty-state-icon">📷</div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No galleries found</h3>
                                    <p class="text-gray-500 mb-6">Create your first gallery to get started</p>
                                    <a href="/admin/galleries/create" class="btn btn-primary">
                                        Create Gallery
                                    </a>
                                </div>
                            `;
                        }
                    }, 300);
                } else {
                    window.location.reload();
                }
            } else {
                const errorText = await response.text();
                throw new Error('Failed to delete gallery with both methods');
            }
        } catch (error) {
            console.error('Error deleting gallery with POST spoofing:', error);
            showNotification('Error deleting gallery: ' + error.message, 'error');
            
            const deleteButton = document.getElementById('confirm-gallery-delete-btn');
            if (deleteButton) {
                deleteButton.innerHTML = 'Delete Gallery';
                deleteButton.disabled = false;
            }
        }
    }

    function updateStatistics() {
        const totalGalleries = document.getElementById('total-galleries');
        const activeGalleries = document.getElementById('active-galleries');
        
        if (totalGalleries) {
            totalGalleries.textContent = parseInt(totalGalleries.textContent) - 1;
        }
        
        if (activeGalleries) {
            activeGalleries.textContent = Math.max(0, parseInt(activeGalleries.textContent) - 1);
        }
    }

    // Utility functions
    function escapeHtml(unsafe) {
        if (!unsafe) return '';
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function showNotification(message, type = 'info') {
        const existingNotifications = document.querySelectorAll('.notification');
        existingNotifications.forEach(notification => notification.remove());

        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg text-white z-50 ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 'bg-blue-500'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 3000);
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Set up delete confirmation
        const deleteButton = document.getElementById('confirm-gallery-delete-btn');
        if (deleteButton) {
            deleteButton.addEventListener('click', deleteGallery);
        }

        // Close modals when clicking on backdrop
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal')) {
                e.target.classList.remove('show');
            }
        });

        // Close modals with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal.show').forEach(modal => {
                    modal.classList.remove('show');
                });
            }
        });
    });
</script>
@endsection