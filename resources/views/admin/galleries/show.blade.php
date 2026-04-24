@extends('layouts.admin')
@section('title', 'Gallery: ' . $gallery->title)

@php
$active = 'gallery';
@endphp

@section('content_css')
<style>
/* CSS styles are unchanged for brevity but kept intact */
.image-card[draggable="true"] { cursor: grab; }
.image-card[draggable="true"]:active { cursor: grabbing; }
.image-card.dragging { opacity: 0.6; transform: rotate(5deg) scale(0.95); box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
.image-card.drag-over { border: 2px dashed #ef4444; background: #fef2f2; transform: scale(1.02); }
.image-card[draggable="true"]:not(.multi-select-mode) { cursor: grab; }
.image-card[draggable="true"]:not(.multi-select-mode):active { cursor: grabbing; }
.image-card[draggable="true"]:not(.multi-select-mode):hover::before { content: '⋮⋮'; position: absolute; top: 0.75rem; right: 0.75rem; background: rgba(0,0,0,0.7); color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; z-index: 5; pointer-events: none; }
.image-card:hover .drag-handle { opacity: 1; }
.drag-handle:active { cursor: grabbing; }
.image-card.selected { border: 3px solid #ef4444; box-shadow: 0 0 0 3px rgba(239,68,68,0.3); transform: scale(0.98); }
.image-card.selected .image-overlay { opacity: 1; }
.select-checkbox { position: absolute; top: 1rem; left: 1rem; width: 1.5rem; height: 1.5rem; background: white; border: 2px solid #d1d5db; border-radius: 4px; cursor: pointer; z-index: 10; transition: all 0.2s ease; }
.select-checkbox.checked { background: #ef4444; border-color: #ef4444; }
.select-checkbox.checked::after { content: '✓'; color: white; font-weight: bold; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 0.875rem; }
.bulk-actions-bar { position: fixed; bottom: 2rem; left: 50%; transform: translateX(-50%); background: white; padding: 1rem 1.5rem; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); z-index: 100; display: none; align-items: center; gap: 1rem; border: 1px solid #e5e7eb; }
.bulk-actions-bar.show { display: flex; animation: slideUp 0.3s ease; }
.bulk-count { font-weight: 600; color: #374151; margin-right: 1rem; }
.sortable-ghost { opacity: 0.4; background: #f3f4f6; }
.multi-select-mode .image-card { cursor: pointer !important; }
.multi-select-mode .image-card[draggable="true"] { cursor: pointer !important; }
.multi-select-mode .image-card::before { display: none !important; }
@media (max-width: 768px) { .lightbox-content { max-width: 95%; max-height: 85%; } .lightbox-nav { width: 2.5rem; height: 2.5rem; font-size: 1rem; } .lightbox-close { width: 2.5rem; height: 2.5rem; top: 1rem; right: 1rem; } }
.modal-open { position: fixed; width: 100%; height: 100%; }
.multi-select-mode .image-img { cursor: pointer; }
.selection-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(239,68,68,0.1); border: 2px solid #ef4444; border-radius: 16px; pointer-events: none; opacity: 0; transition: opacity 0.2s ease; }
.image-card.selected .selection-overlay { opacity: 1; }
.lightbox-counter { position: absolute; top: 1.5rem; left: 1.5rem; background: rgba(0,0,0,0.8); color: white; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.875rem; font-weight: 600; backdrop-filter: blur(10px); }
.image-lightbox { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.95); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(5px); }
.image-lightbox.show { display: flex; animation: fadeIn 0.3s ease-in-out; }
.lightbox-content { max-width: 90%; max-height: 90%; position: relative; border-radius: 12px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
.lightbox-image { max-width: 100%; max-height: 90vh; object-fit: contain; display: block; }
.lightbox-info { position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(transparent, rgba(0,0,0,0.9)); color: white; padding: 2rem; transform: translateY(100%); transition: transform 0.3s ease; }
.lightbox-content:hover .lightbox-info { transform: translateY(0); }
.lightbox-close, .lightbox-nav { position: absolute; background: rgba(0,0,0,0.8); color: white; border: none; border-radius: 50%; width: 3.5rem; height: 3.5rem; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 1.5rem; transition: all 0.3s ease; backdrop-filter: blur(10px); }
.lightbox-close:hover, .lightbox-nav:hover { background: rgba(239,68,68,0.9); transform: scale(1.1); }
.lightbox-close { top: 1.5rem; right: 1.5rem; }
.lightbox-nav { top: 50%; transform: translateY(-50%); }
.lightbox-prev { left: 1.5rem; }
.lightbox-next { right: 1.5rem; }
.modal-backdrop { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px); padding: 1rem; overflow-y: auto; }
.modal-backdrop.show { display: flex; animation: fadeIn 0.3s ease-in-out; }
.modal-content { background: white; border-radius: 16px; width: 100%; max-width: 95vw; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,0.3); animation: slideUp 0.3s ease; margin: auto; }
@media (min-width: 480px) { .modal-content { max-width: 90vw; } }
@media (min-width: 640px) { .modal-content { max-width: 85vw; } #upload-modal .modal-content, #image-edit-modal .modal-content { max-width: 600px; } }
@media (min-width: 768px) { .modal-content { max-width: 80vw; } #upload-modal .modal-content, #image-edit-modal .modal-content { max-width: 700px; } }
@media (min-width: 1024px) { .modal-content { max-width: 75vw; } #upload-modal .modal-content { max-width: 800px; } #image-edit-modal .modal-content { max-width: 750px; } }
@media (min-width: 1280px) { .modal-content { max-width: 70vw; } #upload-modal .modal-content { max-width: 900px; } #image-edit-modal .modal-content { max-width: 800px; } }
.modal-header { padding: 1rem 1.25rem; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; background: #f9fafb; border-radius: 16px 16px 0 0; flex-wrap: wrap; gap: 0.5rem; }
@media (min-width: 640px) { .modal-header { padding: 1.25rem 1.5rem; flex-wrap: nowrap; } }
.modal-title { font-size: 1.125rem; font-weight: 700; color: #1f2937; flex: 1; min-width: 0; }
@media (min-width: 640px) { .modal-title { font-size: 1.25rem; } }
.modal-close { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #6b7280; transition: color 0.2s ease; width: 2.5rem; height: 2.5rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.modal-close:hover { color: #374151; background: #f3f4f6; }
.modal-body { padding: 1.25rem; }
@media (min-width: 640px) { .modal-body { padding: 1.5rem; } }
.modal-footer { padding: 1.25rem; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 0.75rem; background: #f9fafb; border-radius: 0 0 16px 16px; flex-wrap: wrap; }
@media (min-width: 640px) { .modal-footer { padding: 1.5rem; flex-wrap: nowrap; } }
.form-grid { display: grid; grid-template-columns: 1fr; gap: 1.25rem; }
@media (min-width: 768px) { .form-grid { grid-template-columns: 1fr 1fr; gap: 2rem; } }
.upload-dropzone { padding: 1.5rem; border: 2px dashed #d1d5db; border-radius: 16px; text-align: center; transition: all 0.3s ease; background: #f9fafb; }
@media (max-width: 640px) { .upload-dropzone { padding: 1rem; } .upload-dropzone p { font-size: 0.875rem; } .upload-dropzone .text-lg { font-size: 1rem; } }
.upload-dropzone:hover { border-color: #ef4444; background: #fef2f2; }
.preview-item { border: 1px solid #e5e7eb; border-radius: 12px; padding: 1rem; background: white; margin-bottom: 1rem; }
@media (max-width: 640px) { .preview-item { padding: 0.75rem; } .preview-grid { flex-direction: column; gap: 1rem; } .preview-image { width: 100%; max-width: 120px; margin: 0 auto; } .preview-content { width: 100%; } }
.form-input, .form-select, .form-textarea { width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.875rem; transition: all 0.2s ease; background: #f9fafb; box-sizing: border-box; }
@media (max-width: 640px) { .form-input, .form-select, .form-textarea { padding: 0.625rem; font-size: 16px; } }
.form-input:focus, .form-select:focus, .form-textarea:focus { outline: none; border-color: #ef4444; box-shadow: 0 0 0 3px rgba(239,68,68,0.1); background: white; }
.button-group { display: flex; gap: 0.75rem; flex-wrap: wrap; width: 100%; }
@media (max-width: 640px) { .button-group { flex-direction: column; } .button-group .btn { width: 100%; justify-content: center; } }
@media (min-width: 641px) { .button-group { flex-wrap: nowrap; width: auto; } }
.checkbox-group, .radio-group { display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; }
@media (max-width: 640px) { .checkbox-group, .radio-group { justify-content: space-between; width: 100%; } }
.image-preview-container { margin-top: 1rem; padding: 1rem; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0; }
@media (max-width: 640px) { .image-preview-container { padding: 0.75rem; } .image-preview-container img { max-height: 150px; } }
.form-group { margin-bottom: 1.25rem; }
@media (max-width: 640px) { .form-group { margin-bottom: 1rem; } }
.form-textarea { resize: vertical; min-height: 100px; }
@media (max-width: 640px) { .form-textarea { min-height: 80px; } }
@supports(padding: max(0px)) { .modal-backdrop { padding-left: max(1rem, env(safe-area-inset-left)); padding-right: max(1rem, env(safe-area-inset-right)); padding-bottom: max(1rem, env(safe-area-inset-bottom)); } }
body.modal-open { overflow: hidden; position: fixed; width: 100%; }
@media (max-width: 768px) { .btn:focus, .form-input:focus, .form-select:focus, .form-textarea:focus { outline: 2px solid #3b82f6; outline-offset: 2px; } }
@media (max-width: 768px) { .btn { min-height: 44px; padding: 0.75rem 1rem; } .btn-sm { min-height: 36px; padding: 0.5rem 0.75rem; } }
.modal-content::-webkit-scrollbar { width: 6px; }
.modal-content::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 0 0 16px 0; }
.modal-content::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
.modal-content::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes slideUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
.animate-fade-in { animation: fadeIn 0.3s ease-in-out; }
.animate-slide-up { animation: slideUp 0.3s ease-in-out; }
.spinner { border: 2px solid #f3f3f3; border-top: 2px solid #dc2626; border-radius: 50%; width: 16px; height: 16px; animation: spin 1s linear infinite; display: inline-block; margin-right: 8px; }
@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
.notification { position: fixed; top: 1rem; right: 1rem; padding: 1rem 1.5rem; border-radius: 0.75rem; color: white; z-index: 9999; animation: slideIn 0.3s ease-out; backdrop-filter: blur(10px); box-shadow: 0 10px 30px rgba(0,0,0,0.2); font-weight: 600; }
.notification.success { background: linear-gradient(135deg, #10b981, #059669); }
.notification.error { background: linear-gradient(135deg, #ef4444, #dc2626); }
.notification.info { background: linear-gradient(135deg, #3b82f6, #2563eb); }
@keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
.gallery-header { background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 50%, #b91c1c 100%); border-radius: 16px; overflow: hidden; position: relative; box-shadow: 0 10px 40px rgba(0,0,0,0.15); margin-bottom: 2rem; }
.gallery-header::before { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E"); opacity: 0.3; }
.image-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem; margin-top: 1rem; }
.image-card { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); transition: all 0.3s ease; border: 1px solid #e5e7eb; position: relative; }
.image-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0,0,0,0.15); }
.image-header { position: relative; height: 220px; overflow: hidden; background: #f8fafc; }
.image-img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
.image-card:hover .image-img { transform: scale(1.08); }
.image-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(to bottom, transparent 50%, rgba(0,0,0,0.7)); display: flex; align-items: flex-end; padding: 1.5rem; opacity: 0; transition: opacity 0.3s ease; }
.image-card:hover .image-overlay { opacity: 1; }
.image-badge { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; box-shadow: 0 4px 12px rgba(239,68,68,0.3); }
.image-content { padding: 1.5rem; }
.image-title { font-size: 1.125rem; font-weight: 700; color: #1f2937; margin-bottom: 0.5rem; line-height: 1.4; }
.image-caption { color: #6b7280; font-size: 0.875rem; line-height: 1.5; margin-bottom: 1rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.image-meta { display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; color: #9ca3af; margin-bottom: 1rem; }
.image-actions { display: flex; gap: 0.5rem; flex-wrap: wrap; }
.btn { padding: 0.5rem 1rem; border-radius: 10px; font-size: 0.875rem; font-weight: 600; transition: all 0.3s ease; cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
.btn-sm { padding: 0.375rem 0.75rem; font-size: 0.75rem; }
.btn-primary { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; }
.btn-primary:hover { background: linear-gradient(135deg, #dc2626, #b91c1c); box-shadow: 0 6px 20px rgba(239,68,68,0.4); transform: translateY(-2px); }
.btn-secondary { background: linear-gradient(135deg, #6b7280, #4b5563); color: white; }
.btn-secondary:hover { background: linear-gradient(135deg, #4b5563, #374151); box-shadow: 0 6px 20px rgba(107,114,128,0.4); transform: translateY(-2px); }
.btn-outline { background: transparent; border: 1px solid #d1d5db; color: #374151; }
.btn-outline:hover { background: #f9fafb; border-color: #9ca3af; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
.btn-danger { background: linear-gradient(135deg, #dc2626, #b91c1c); color: white; }
.btn-danger:hover { background: linear-gradient(135deg, #b91c1c, #991b1b); box-shadow: 0 6px 20px rgba(220,38,38,0.4); transform: translateY(-2px); }
.action-bar { background: white; border-radius: 16px; padding: 1.5rem; margin-bottom: 2rem; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: 1px solid #e5e7eb; }
.empty-state { text-align: center; padding: 4rem 2rem; background: white; border-radius: 16px; border: 2px dashed #e5e7eb; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
.empty-state-icon { font-size: 4rem; margin-bottom: 1.5rem; opacity: 0.5; }
.status-indicator { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
.status-active { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
.status-inactive { background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; }
@media (max-width: 1024px) { .image-grid { grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); } }
@media (max-width: 768px) { .image-grid { grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem; } .image-header { height: 180px; } }
@media (max-width: 640px) { .image-grid { grid-template-columns: 1fr; } .image-actions { flex-direction: column; } .image-actions .btn { justify-content: center; width: 100%; } }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-gray-50 px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="gallery-header">
        <div class="relative px-6 py-8 sm:px-8 sm:py-12">
            <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-white/0"></div>
            <div class="relative z-10">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-2">{{ $gallery->title }}</h1>
                        @if($gallery->description)
                        <p class="text-white/90 text-lg mb-4 max-w-3xl">{{ $gallery->description }}</p>
                        @endif
                    </div>
                    <div class="flex-shrink-0">
                        <span class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-white text-sm font-semibold">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ $gallery->images_count }} images
                        </span>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-6 mt-6 text-white/90">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span class="font-medium">{{ $gallery->category }}</span>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-medium {{ $gallery->is_active ? 'text-green-300' : 'text-red-300' }}">
                            {{ $gallery->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-medium">{{ $gallery->created_at->format('M j, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="action-bar">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex flex-wrap gap-3">
                <a href="/admin/galleries" class="btn btn-outline">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Galleries
                </a>
                
                <a href="/admin/galleries/{{ $gallery->id }}/edit" class="btn btn-outline">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Gallery
                </a>
            </div>
            
            <button id="upload-images-btn" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Upload Images
            </button>
        </div>
    </div>

    <div id="bulk-actions-bar" class="bulk-actions-bar">
        <span class="bulk-count" id="selected-count">0 images selected</span>
        <button id="bulk-delete-btn" class="btn btn-danger btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            Delete Selected
        </button>
        <button id="clear-selection-btn" class="btn btn-outline btn-sm">
            Clear Selection
        </button>
    </div>

    @if($gallery->images->count() > 0)
        <div class="image-grid">
            @foreach($gallery->images->sortBy('sort_order') as $image)
            <div class="image-card" data-image-id="{{ $image->id }}" draggable="true">
                <div class="image-header">
                    @php
                        // REFACTORED: Removed 'storage/' prefix here
                        $imageUrl = $image->image_path ? asset($image->image_path) : '/images/default-image.jpg';
                    @endphp
                    
                    <img src="{{ $imageUrl }}" 
                         alt="{{ $image->image_alt ?? $image->title ?? 'Gallery Image' }}" 
                         class="image-img"
                         onerror="this.src='/images/default-image.jpg'"
                         data-image-id="{{ $image->id }}">
                    
                    <div class="image-overlay">
                        <div class="flex flex-wrap gap-2">
                            @if($image->is_featured)
                                <span class="image-badge">★ Featured</span>
                            @endif
                            @if(!$image->is_active)
                                <span class="image-badge" style="background: linear-gradient(135deg, #6b7280, #4b5563);">Inactive</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="image-content">
                    <h3 class="image-title">{{ $image->title ?? 'Untitled Image' }}</h3>
                    
                    @if($image->caption)
                    <p class="image-caption">{{ $image->caption }}</p>
                    @endif
                    
                    <div class="image-meta">
                        <span class="bg-gray-100 px-3 py-1 rounded-full text-xs font-medium">
                            {{ $image->category ?: 'General' }}
                        </span>
                        <span class="text-xs">Order: {{ $image->sort_order }}</span>
                    </div>
                    
                    <div class="image-actions">
                        <button class="btn btn-outline btn-sm view-image-btn" data-image-id="{{ $image->id }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            View
                        </button>
                        
                        <button class="btn btn-outline btn-sm edit-image-btn" data-image-id="{{ $image->id }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </button>
                        
                        @if($image->is_featured)
                            <button class="btn btn-secondary btn-sm remove-featured-btn" data-image-id="{{ $image->id }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Featured
                            </button>
                        @else
                            <button class="btn btn-primary btn-sm set-featured-btn" data-image-id="{{ $image->id }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                                Feature
                            </button>
                        @endif
                        
                        <button class="btn btn-danger btn-sm delete-image-btn" data-image-id="{{ $image->id }}" data-image-title="{{ $image->title ?? 'Untitled' }}">
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
    @else
        <div class="empty-state">
            <div class="empty-state-icon">🖼️</div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No images in this gallery</h3>
            <p class="text-gray-500 mb-6 max-w-md mx-auto">Get started by uploading some images to showcase in your gallery</p>
            <button id="empty-upload-btn" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Upload Images
            </button>
        </div>
    @endif
</div>

<div id="upload-modal" class="modal-backdrop">
    <div class="modal-content animate-slide-up">
        <div class="modal-header">
            <h3 class="modal-title">Upload Images</h3>
            <button type="button" id="close-upload-modal" class="modal-close">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="modal-body">
            <form id="upload-form">
                @csrf
                <input type="hidden" id="upload-gallery-id" name="gallery_id" value="{{ $gallery->id }}">
                
                <div class="space-y-6">
                    <div class="form-group">
                        <label class="block text-sm font-semibold text-gray-900 mb-3">Select Images</label>
                        <div class="upload-dropzone">
                            <input type="file" id="upload-images" class="hidden" multiple accept="image/*">
                            <div class="cursor-pointer py-6 sm:py-8 px-4" id="upload-dropzone-trigger">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 sm:h-12 sm:w-12 mx-auto text-gray-400 mb-3 sm:mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-base sm:text-lg font-medium text-gray-900 mb-2">Drop images here or click to browse</p>
                                <p class="text-xs sm:text-sm text-gray-500 max-w-md mx-auto">Supports JPG, PNG, WebP • Max 10MB per image</p>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-3 text-center sm:text-left">You can edit titles, alt text, and status for each image before uploading</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="upload-category" class="block text-sm font-semibold text-gray-900 mb-2">Default Category</label>
                        <input type="text" id="upload-category" name="category" class="form-input" placeholder="Enter category (e.g., nature, portrait, event)">
                    </div>
                    
                    <div id="upload-preview" class="hidden">
                        <p class="text-sm font-semibold text-gray-900 mb-4">Image Preview & Edit:</p>
                        <div id="preview-container" class="space-y-4 max-h-64 sm:max-h-96 overflow-y-auto p-3 sm:p-4 border border-gray-200 rounded-xl bg-gray-50"></div>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <div class="button-group">
                <button type="button" id="cancel-upload-btn" class="btn btn-outline">
                    Cancel
                </button>
                <button type="button" id="confirm-upload-btn" class="btn btn-primary">
                    Upload Images
                </button>
            </div>
        </div>
    </div>
</div>

<div id="image-lightbox" class="image-lightbox">
    <button class="lightbox-close" id="close-lightbox">&times;</button>
    <button class="lightbox-nav lightbox-prev" id="prev-image">❮</button>
    <div class="lightbox-content">
        <img id="lightbox-image" class="lightbox-image" src="" alt="">
        <div class="lightbox-info">
            <h3 id="lightbox-title" class="text-xl font-bold mb-2"></h3>
            <p id="lightbox-caption" class="text-gray-200"></p>
            <div id="lightbox-meta" class="flex gap-4 mt-3 text-sm text-gray-300"></div>
        </div>
    </div>
    <button class="lightbox-nav lightbox-next" id="next-image">❯</button>
</div>

<div id="delete-confirm-modal" class="modal-backdrop">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 animate-slide-up">
        <div class="p-6">
            <div class="flex items-center gap-4 mb-4">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Confirm Delete</h3>
                    <p class="text-gray-600 mt-1">This action cannot be undone</p>
                </div>
            </div>
            <p class="text-gray-700 mb-4">Are you sure you want to delete the image "<span id="delete-image-title" class="font-semibold text-gray-900"></span>"?</p>
            <p class="text-sm text-red-600 bg-red-50 p-3 rounded-lg border border-red-200">
                ⚠️ This image will be permanently removed from the gallery and cannot be recovered.
            </p>
        </div>
        <div class="flex justify-end gap-3 p-6 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
            <button id="cancel-delete-btn" class="btn btn-outline">
                Cancel
            </button>
            <button id="confirm-delete-btn" class="btn btn-danger">
                Delete Image
            </button>
        </div>
    </div>
</div>

<div id="image-edit-modal" class="modal-backdrop">
    </div>

<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content_js')
<script>
const GalleryManager = {
    currentImages: @json($gallery->images),
    currentImageIndex: 0,
    imageToDelete: null,
    selectedFiles: [],
    selectedImages: new Set(),
    isMultiSelectMode: false,
    dragStartIndex: null,
    longPressTimer: null,

    init() {
        this.initEventListeners();
        this.initDragAndDrop();
    },

    initEventListeners() {
        document.getElementById('upload-images-btn')?.addEventListener('click', () => this.showUploadModal());
        document.getElementById('empty-upload-btn')?.addEventListener('click', () => this.showUploadModal());

        document.getElementById('close-upload-modal')?.addEventListener('click', () => this.hideUploadModal());
        document.getElementById('cancel-upload-btn')?.addEventListener('click', () => this.hideUploadModal());
        document.getElementById('confirm-upload-btn')?.addEventListener('click', () => this.uploadImages());

        document.getElementById('cancel-delete-btn')?.addEventListener('click', () => this.hideDeleteModal());
        document.getElementById('confirm-delete-btn')?.addEventListener('click', () => this.deleteImage());

        document.getElementById('close-lightbox')?.addEventListener('click', () => this.hideLightbox());
        document.getElementById('prev-image')?.addEventListener('click', () => this.navigateImage(-1));
        document.getElementById('next-image')?.addEventListener('click', () => this.navigateImage(1));

        document.getElementById('bulk-delete-btn')?.addEventListener('click', () => this.bulkDeleteImages());
        document.getElementById('clear-selection-btn')?.addEventListener('click', () => this.clearSelection());

        const uploadInput = document.getElementById('upload-images');
        if (uploadInput) {
            uploadInput.addEventListener('change', (e) => this.handleFileSelection(e.target.files));
        }
        
        const dropzoneTrigger = document.getElementById('upload-dropzone-trigger');
        if (dropzoneTrigger && uploadInput) {
            dropzoneTrigger.addEventListener('click', () => uploadInput.click());
        }

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('button');
            if (!btn) return;

            if (btn.classList.contains('view-image-btn')) {
                const imageId = parseInt(btn.dataset.imageId);
                this.viewFullImage(imageId);
            }
            else if (btn.classList.contains('edit-image-btn')) {
                const imageId = parseInt(btn.dataset.imageId);
                this.editImage(imageId);
            }
            else if (btn.classList.contains('set-featured-btn')) {
                const imageId = parseInt(btn.dataset.imageId);
                this.setFeaturedImage(imageId);
            }
            else if (btn.classList.contains('remove-featured-btn')) {
                const imageId = parseInt(btn.dataset.imageId);
                this.removeFeaturedImage(imageId);
            }
            else if (btn.classList.contains('delete-image-btn')) {
                const imageId = parseInt(btn.dataset.imageId);
                const imageTitle = btn.dataset.imageTitle;
                this.confirmDeleteImage(imageId, imageTitle);
            }
        });

        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('image-img') && !this.isMultiSelectMode) {
                const imageId = parseInt(e.target.dataset.imageId);
                this.viewFullImage(imageId);
            }
            
            if (e.target.classList.contains('select-checkbox')) {
                const imageCard = e.target.closest('.image-card');
                const imageId = parseInt(imageCard.dataset.imageId);
                this.toggleImageSelection(imageId, imageCard);
                e.stopPropagation();
            }
        });

        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'a' && this.isMultiSelectMode) {
                e.preventDefault();
                this.selectAllImages();
            }
            if (e.key === 'Escape' && this.isMultiSelectMode) {
                this.clearSelection();
            }
        });

        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && !this.isMultiSelectMode) {
                this.enableMultiSelectMode();
            }
        });

        document.addEventListener('keyup', (e) => {
            if (e.key === 'Control' || e.key === 'Meta') {
                this.disableMultiSelectMode();
            }
        });

        const dropZone = document.querySelector('.upload-dropzone');
        if (dropZone) {
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.classList.add('border-red-400', 'bg-red-50');
            });
            dropZone.addEventListener('dragleave', (e) => {
                e.preventDefault();
                dropZone.classList.remove('border-red-400', 'bg-red-50');
            });
            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.classList.remove('border-red-400', 'bg-red-50');
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    document.getElementById('upload-images').files = files;
                    this.handleFileSelection(files);
                }
            });
        }

        document.querySelectorAll('.modal-backdrop').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.remove('show');
                    if (modal.id === 'image-lightbox') {
                        this.hideLightbox();
                    }
                }
            });
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const lightbox = document.getElementById('image-lightbox');
                if (lightbox && lightbox.classList.contains('show')) this.hideLightbox();
                const deleteModal = document.getElementById('delete-confirm-modal');
                if (deleteModal && deleteModal.classList.contains('show')) this.hideDeleteModal();
                const uploadModal = document.getElementById('upload-modal');
                if (uploadModal && uploadModal.classList.contains('show')) this.hideUploadModal();
                const editModal = document.getElementById('image-edit-modal');
                if (editModal && editModal.classList.contains('show')) this.hideImageEditModal();
            }
        });
    },

    initDragAndDrop() {
        const imageGrid = document.querySelector('.image-grid');
        if (!imageGrid) return;

        let dragSrcElement = null;

        document.addEventListener('dragstart', (e) => {
            if (e.target.classList.contains('image-card') && !this.isMultiSelectMode) {
                dragSrcElement = e.target;
                e.target.classList.add('dragging');
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/html', e.target.outerHTML);
            }
        });

        document.addEventListener('dragover', (e) => {
            if (!dragSrcElement) return;
            e.preventDefault();
            const target = e.target.closest('.image-card');
            if (target && target !== dragSrcElement) {
                target.classList.add('drag-over');
            }
        });

        document.addEventListener('dragleave', (e) => {
            const target = e.target.closest('.image-card');
            if (target) {
                target.classList.remove('drag-over');
            }
        });

        document.addEventListener('drop', (e) => {
            if (!dragSrcElement) return;
            e.preventDefault();
            const target = e.target.closest('.image-card');
            
            if (target && dragSrcElement && target !== dragSrcElement) {
                target.classList.remove('drag-over');
                const imageGrid = document.querySelector('.image-grid');
                const images = Array.from(imageGrid.children);
                const fromIndex = images.indexOf(dragSrcElement);
                const toIndex = images.indexOf(target);
                
                if (fromIndex < toIndex) {
                    imageGrid.insertBefore(dragSrcElement, target.nextSibling);
                } else {
                    imageGrid.insertBefore(dragSrcElement, target);
                }
                
                this.updateImageOrders();
            }
        });

        document.addEventListener('dragend', (e) => {
            if (e.target.classList.contains('image-card')) {
                e.target.classList.remove('dragging');
                document.querySelectorAll('.image-card').forEach(card => {
                    card.classList.remove('drag-over');
                });
                dragSrcElement = null;
            }
        });
    },

    async updateImageOrders() {
        const imageCards = document.querySelectorAll('.image-card');
        const updates = [];
        
        imageCards.forEach((card, index) => {
            const imageId = parseInt(card.dataset.imageId);
            updates.push({ id: imageId, sort_order: index + 1 });
        });

        try {
            const response = await fetch(this.getReorderRoute(), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ updates })
            });

            if (!response.ok) throw new Error('Update failed');
            const result = await response.json();
            
            if (result.success) {
                this.showNotification('Image order updated', 'success');
                this.currentImages.forEach(image => {
                    const update = updates.find(u => u.id === image.id);
                    if (update) image.sort_order = update.sort_order;
                });
            }
        } catch (error) {
            this.showNotification('Error updating order', 'error');
        }
    },

    enableMultiSelectMode() {
        if (this.isMultiSelectMode) return;
        this.isMultiSelectMode = true;
        document.body.classList.add('multi-select-mode');
        
        document.querySelectorAll('.image-card').forEach(card => {
            if (!card.querySelector('.select-checkbox')) {
                const checkbox = document.createElement('div');
                checkbox.className = 'select-checkbox';
                checkbox.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.toggleImageSelection(parseInt(card.dataset.imageId), card);
                });
                card.style.position = 'relative';
                card.appendChild(checkbox);
                
                const overlay = document.createElement('div');
                overlay.className = 'selection-overlay';
                card.appendChild(overlay);
            }
        });
    },

    disableMultiSelectMode() {
        if (!this.isMultiSelectMode) return;
        this.isMultiSelectMode = false;
        document.body.classList.remove('multi-select-mode');
        this.clearSelection();
        
        document.querySelectorAll('.image-card').forEach(card => {
            card.querySelector('.select-checkbox')?.remove();
            card.querySelector('.selection-overlay')?.remove();
        });
    },

    toggleImageSelection(imageId, imageCard) {
        if (!this.isMultiSelectMode) this.enableMultiSelectMode();

        if (this.selectedImages.has(imageId)) {
            this.selectedImages.delete(imageId);
            imageCard.classList.remove('selected');
            imageCard.querySelector('.select-checkbox')?.classList.remove('checked');
        } else {
            this.selectedImages.add(imageId);
            imageCard.classList.add('selected');
            imageCard.querySelector('.select-checkbox')?.classList.add('checked');
        }
        
        this.updateBulkActionsBar();
    },

    selectAllImages() {
        if (!this.isMultiSelectMode) return;
        document.querySelectorAll('.image-card').forEach(card => {
            this.selectedImages.add(parseInt(card.dataset.imageId));
            card.classList.add('selected');
            card.querySelector('.select-checkbox')?.classList.add('checked');
        });
        this.updateBulkActionsBar();
    },

    clearSelection() {
        this.selectedImages.clear();
        document.querySelectorAll('.image-card').forEach(card => {
            card.classList.remove('selected');
            card.querySelector('.select-checkbox')?.classList.remove('checked');
        });
        this.updateBulkActionsBar();
        this.disableMultiSelectMode();
    },

    updateBulkActionsBar() {
        const bulkBar = document.getElementById('bulk-actions-bar');
        const selectedCount = document.getElementById('selected-count');
        if (this.selectedImages.size > 0) {
            selectedCount.textContent = `${this.selectedImages.size} image${this.selectedImages.size > 1 ? 's' : ''} selected`;
            bulkBar.classList.add('show');
        } else {
            bulkBar.classList.remove('show');
        }
    },

    getBulkDeleteRoute() { return '/admin/galleries/images/bulk-delete'; },
    getReorderRoute() { return '/admin/galleries/images/reorder'; },
    getDeleteImageRoute(imageId) { return `/admin/galleries/images/${imageId}`; },
    getSetFeaturedRoute(imageId) { return `/admin/galleries/images/${imageId}/set-featured`; },
    getRemoveFeaturedRoute(imageId) { return `/admin/galleries/images/${imageId}/remove-featured`; },
    getUploadRoute() { return '/admin/galleries/upload'; },

    async bulkDeleteImages() {
        if (this.selectedImages.size === 0) return;
        if (!confirm('Delete selected images?')) return;

        try {
            const bulkButton = document.getElementById('bulk-delete-btn');
            bulkButton.innerHTML = '<div class="spinner"></div> Deleting...';
            bulkButton.disabled = true;

            const response = await fetch(this.getBulkDeleteRoute(), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ 
                    image_ids: Array.from(this.selectedImages),
                    gallery_id: {{ $gallery->id }}
                })
            });

            if (response.ok) {
                this.showNotification('Images deleted', 'success');
                this.selectedImages.forEach(imageId => {
                    const card = document.querySelector(`[data-image-id="${imageId}"]`);
                    if (card) {
                        card.style.opacity = '0';
                        setTimeout(() => card.remove(), 300);
                    }
                });
                this.clearSelection();
            }
        } catch (error) {
            this.showNotification('Error deleting images', 'error');
        }
    },

    viewFullImage(imageId) {
        const image = this.currentImages.find(img => img.id === imageId);
        if (!image) return;

        this.currentImageIndex = this.currentImages.findIndex(img => img.id === imageId);
        const lightbox = document.getElementById('image-lightbox');
        const lightboxImage = document.getElementById('lightbox-image');

        // REFACTORED: Removed '/storage' prefix
        const imageUrl = image.image_path ? `/${image.image_path}` : '/images/default-image.jpg';
        
        lightboxImage.src = imageUrl;
        document.getElementById('lightbox-title').textContent = image.title || 'Untitled';
        document.getElementById('lightbox-caption').textContent = image.caption || '';
        document.getElementById('lightbox-meta').innerHTML = `
            <span>Category: ${image.category || 'General'}</span>
            <span>Order: ${image.sort_order}</span>
        `;
        
        lightbox.classList.add('show');
        document.body.style.overflow = 'hidden';
        this.preloadAdjacentImages();
    },

    preloadAdjacentImages() {
        const preloadImage = (index) => {
            if (index >= 0 && index < this.currentImages.length) {
                const img = new Image();
                // REFACTORED: Removed '/storage' prefix
                img.src = this.currentImages[index].image_path ? `/${this.currentImages[index].image_path}` : '/images/default-image.jpg';
            }
        };
        preloadImage(this.currentImageIndex - 1);
        preloadImage(this.currentImageIndex + 1);
    },

    navigateImage(direction) {
        this.currentImageIndex += direction;
        if (this.currentImageIndex < 0) this.currentImageIndex = this.currentImages.length - 1;
        else if (this.currentImageIndex >= this.currentImages.length) this.currentImageIndex = 0;
        this.viewFullImage(this.currentImages[this.currentImageIndex].id);
    },

    hideLightbox() {
        document.getElementById('image-lightbox')?.classList.remove('show');
        document.body.style.overflow = '';
    },

    confirmDeleteImage(imageId, imageTitle) {
        this.imageToDelete = imageId;
        document.getElementById('delete-image-title').textContent = imageTitle;
        document.getElementById('delete-confirm-modal').classList.add('show');
    },

    hideDeleteModal() {
        this.imageToDelete = null;
        document.getElementById('delete-confirm-modal').classList.remove('show');
    },

    async deleteImage() {
        if (!this.imageToDelete) return;
        try {
            const btn = document.getElementById('confirm-delete-btn');
            btn.innerHTML = '<div class="spinner"></div>';
            btn.disabled = true;

            const response = await fetch(this.getDeleteImageRoute(this.imageToDelete), {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            });

            if (response.ok) {
                this.showNotification('Deleted successfully', 'success');
                this.hideDeleteModal();
                document.querySelector(`[data-image-id="${this.imageToDelete}"]`)?.remove();
            }
        } catch (e) {
            this.showNotification('Error', 'error');
        }
    },

    async editImage(imageId) {
        try {
            this.showImageEditModal('Loading', '<div class="spinner"></div>');
            const response = await fetch(`/admin/galleries/images/${imageId}`);
            const data = await response.json();
            this.showImageEditModal('Edit Image', this.createImageEditForm(data.image));
        } catch (e) {
            this.hideImageEditModal();
        }
    },

    createImageEditForm(image) {
        return `
            <form id="image-edit-form" class="space-y-6">
                <input type="hidden" name="id" value="${image.id}">
                <div class="grid grid-cols-2 gap-4">
                    <input type="text" name="title" value="${this.escapeHtml(image.title)}" class="form-input" placeholder="Title">
                    <input type="text" name="image_alt" value="${this.escapeHtml(image.image_alt)}" class="form-input" placeholder="Alt Text">
                </div>
                <textarea name="caption" class="form-input">${this.escapeHtml(image.caption || '')}</textarea>
                <div class="grid grid-cols-2 gap-4">
                    <input type="text" name="category" value="${this.escapeHtml(image.category)}" class="form-input" placeholder="Category">
                    <input type="number" name="sort_order" value="${image.sort_order}" class="form-input">
                </div>
                <div class="flex gap-4">
                    <label><input type="checkbox" name="is_featured" ${image.is_featured ? 'checked' : ''}> Featured</label>
                    <label><input type="checkbox" name="is_active" ${image.is_active ? 'checked' : ''}> Active</label>
                </div>
                <div class="mt-4 text-center">
                    <img src="/${image.image_path}" class="h-32 mx-auto rounded shadow">
                </div>
            </form>
        `;
    },

    showImageEditModal(title, content) {
        const modalHtml = `
            <div id="image-edit-modal" class="modal-backdrop show">
                <div class="modal-content" style="max-width: 600px;">
                    <div class="modal-header"><h3>${title}</h3><button id="close-edit-modal">&times;</button></div>
                    <div class="modal-body">${content}</div>
                    <div class="modal-footer">
                        <button id="cancel-edit-btn" class="btn btn-outline">Cancel</button>
                        <button id="save-edit-btn" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        `;
        document.getElementById('image-edit-modal')?.remove();
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        
        setTimeout(() => {
            document.getElementById('close-edit-modal').onclick = () => this.hideImageEditModal();
            document.getElementById('cancel-edit-btn').onclick = () => this.hideImageEditModal();
            document.getElementById('save-edit-btn').onclick = () => this.saveImageChanges();
        }, 100);
    },

    hideImageEditModal() { document.getElementById('image-edit-modal')?.remove(); },

    async saveImageChanges() {
        const form = document.getElementById('image-edit-form');
        const data = {
            title: form.title.value,
            image_alt: form.image_alt.value,
            caption: form.caption.value,
            category: form.category.value,
            sort_order: parseInt(form.sort_order.value),
            is_featured: form.is_featured.checked,
            is_active: form.is_active.checked
        };

        try {
            await fetch(`/admin/galleries/images/${form.id.value}`, {
                method: 'PUT',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            window.location.reload();
        } catch (e) {
            this.showNotification('Error', 'error');
        }
    },

    async setFeaturedImage(id) { await fetch(this.getSetFeaturedRoute(id), { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }}); window.location.reload(); },
    async removeFeaturedImage(id) { await fetch(this.getRemoveFeaturedRoute(id), { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }}); window.location.reload(); },

    showUploadModal() { document.getElementById('upload-modal').classList.add('show'); },
    hideUploadModal() { document.getElementById('upload-modal').classList.remove('show'); },

    handleFileSelection(files) {
        this.selectedFiles = Array.from(files);
        const container = document.getElementById('preview-container');
        document.getElementById('upload-preview').classList.remove('hidden');
        container.innerHTML = '';
        
        this.selectedFiles.forEach((file, idx) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                container.innerHTML += `<div class="flex gap-4 p-2 border rounded"><img src="${e.target.result}" class="w-16 h-16 object-cover"><div class="text-sm">${file.name}</div></div>`;
            };
            reader.readAsDataURL(file);
        });
    },

    async uploadImages() {
        if (!this.selectedFiles.length) return;
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        formData.append('gallery_id', {{ $gallery->id }});
        this.selectedFiles.forEach(f => formData.append('images[]', f));

        document.getElementById('confirm-upload-btn').disabled = true;
        await fetch(this.getUploadRoute(), { method: 'POST', body: formData });
        window.location.reload();
    },

    escapeHtml(unsafe) { return unsafe ? unsafe.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;") : ''; },
    showNotification(msg, type) { alert(msg); }
};

document.addEventListener('DOMContentLoaded', () => GalleryManager.init());
</script>
@endsection