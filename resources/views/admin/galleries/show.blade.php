@extends('layouts.admin')
@section('title', 'Gallery: ' . $gallery->title)

@php
$active = 'gallery';
@endphp

@section('content_css')
<style>

/* Enhanced drag and drop styles */
.image-card[draggable="true"] {
    cursor: grab;
}

.image-card[draggable="true"]:active {
    cursor: grabbing;
}

.image-card.dragging {
    opacity: 0.6;
    transform: rotate(5deg) scale(0.95);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.image-card.drag-over {
    border: 2px dashed #ef4444;
    background: #fef2f2;
    transform: scale(1.02);
}

/* Make entire card show grab cursor when in normal mode */
.image-card[draggable="true"]:not(.multi-select-mode) {
    cursor: grab;
}

.image-card[draggable="true"]:not(.multi-select-mode):active {
    cursor: grabbing;
}

/* Show drag indication on hover */
.image-card[draggable="true"]:not(.multi-select-mode):hover::before {
    content: '⋮⋮';
    position: absolute;
    top: 0.75rem;
    right: 0.75rem;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    z-index: 5;
    pointer-events: none;
}

.image-card:hover .drag-handle {
    opacity: 1;
}

.drag-handle:active {
    cursor: grabbing;
}

    /* Add to your existing CSS */
/* Multi-select styles */
.image-card.selected {
    border: 3px solid #ef4444;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.3);
    transform: scale(0.98);
}

.image-card.selected .image-overlay {
    opacity: 1;
}

.select-checkbox {
    position: absolute;
    top: 1rem;
    left: 1rem;
    width: 1.5rem;
    height: 1.5rem;
    background: white;
    border: 2px solid #d1d5db;
    border-radius: 4px;
    cursor: pointer;
    z-index: 10;
    transition: all 0.2s ease;
}

.select-checkbox.checked {
    background: #ef4444;
    border-color: #ef4444;
}

.select-checkbox.checked::after {
    content: '✓';
    color: white;
    font-weight: bold;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 0.875rem;
}

/* Bulk actions bar */
.bulk-actions-bar {
    position: fixed;
    bottom: 2rem;
    left: 50%;
    transform: translateX(-50%);
    background: white;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    z-index: 100;
    display: none;
    align-items: center;
    gap: 1rem;
    border: 1px solid #e5e7eb;
}

.bulk-actions-bar.show {
    display: flex;
    animation: slideUp 0.3s ease;
}

.bulk-count {
    font-weight: 600;
    color: #374151;
    margin-right: 1rem;
}

/* Drag and drop styles */
.image-card.dragging {
    opacity: 0.5;
    transform: scale(0.95);
}

.image-card.drag-over {
    border: 2px dashed #ef4444;
    background: #fef2f2;
}

.sortable-ghost {
    opacity: 0.4;
    background: #f3f4f6;
}

/* Fix for drag and drop in multi-select mode */
.multi-select-mode .image-card {
    cursor: pointer !important;
}

.multi-select-mode .image-card[draggable="true"] {
    cursor: pointer !important;
}

/* Ensure drag handle doesn't show in multi-select mode */
.multi-select-mode .image-card::before {
    display: none !important;
}

/* Fix for lightbox on mobile */
@media (max-width: 768px) {
    .lightbox-content {
        max-width: 95%;
        max-height: 85%;
    }
    
    .lightbox-nav {
        width: 2.5rem;
        height: 2.5rem;
        font-size: 1rem;
    }
    
    .lightbox-close {
        width: 2.5rem;
        height: 2.5rem;
        top: 1rem;
        right: 1rem;
    }
}

/* Fix for modal scrolling on iOS */
.modal-open {
    position: fixed;
    width: 100%;
    height: 100%;
}

.multi-select-mode .image-img {
    cursor: pointer;
}

/* Selection overlay */
.selection-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(239, 68, 68, 0.1);
    border: 2px solid #ef4444;
    border-radius: 16px;
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.2s ease;
}

.image-card.selected .selection-overlay {
    opacity: 1;
}

/* Enhanced lightbox for multiple images */
.lightbox-counter {
    position: absolute;
    top: 1.5rem;
    left: 1.5rem;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
    backdrop-filter: blur(10px);
}
    /* Lightbox styles */
    .image-lightbox {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.95);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(5px);
    }

    .image-lightbox.show {
        display: flex;
        animation: fadeIn 0.3s ease-in-out;
    }

    .lightbox-content {
        max-width: 90%;
        max-height: 90%;
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    .lightbox-image {
        max-width: 100%;
        max-height: 90vh;
        object-fit: contain;
        display: block;
    }

    .lightbox-info {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(0, 0, 0, 0.9));
        color: white;
        padding: 2rem;
        transform: translateY(100%);
        transition: transform 0.3s ease;
    }

    .lightbox-content:hover .lightbox-info {
        transform: translateY(0);
    }

    .lightbox-close,
    .lightbox-nav {
        position: absolute;
        background: rgba(0, 0, 0, 0.8);
        color: white;
        border: none;
        border-radius: 50%;
        width: 3.5rem;
        height: 3.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 1.5rem;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .lightbox-close:hover,
    .lightbox-nav:hover {
        background: rgba(239, 68, 68, 0.9);
        transform: scale(1.1);
    }

    .lightbox-close {
        top: 1.5rem;
        right: 1.5rem;
    }

    .lightbox-nav {
        top: 50%;
        transform: translateY(-50%);
    }

    .lightbox-prev {
        left: 1.5rem;
    }

    .lightbox-next {
        right: 1.5rem;
    }

    /* Enhanced Modal Responsiveness */
    .modal-backdrop {
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
        overflow-y: auto;
    }

    .modal-backdrop.show {
        display: flex;
        animation: fadeIn 0.3s ease-in-out;
    }

    .modal-content {
        background: white;
        border-radius: 16px;
        width: 100%;
        max-width: 95vw;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        animation: slideUp 0.3s ease;
        margin: auto;
    }

    @media (min-width: 480px) {
        .modal-content {
            max-width: 90vw;
        }
    }

    @media (min-width: 640px) {
        .modal-content {
            max-width: 85vw;
        }
        
        #upload-modal .modal-content,
        #image-edit-modal .modal-content {
            max-width: 600px;
        }
    }

    @media (min-width: 768px) {
        .modal-content {
            max-width: 80vw;
        }
        
        #upload-modal .modal-content,
        #image-edit-modal .modal-content {
            max-width: 700px;
        }
    }

    @media (min-width: 1024px) {
        .modal-content {
            max-width: 75vw;
        }
        
        #upload-modal .modal-content {
            max-width: 800px;
        }
        
        #image-edit-modal .modal-content {
            max-width: 750px;
        }
    }

    @media (min-width: 1280px) {
        .modal-content {
            max-width: 70vw;
        }
        
        #upload-modal .modal-content {
            max-width: 900px;
        }
        
        #image-edit-modal .modal-content {
            max-width: 800px;
        }
    }

    .modal-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f9fafb;
        border-radius: 16px 16px 0 0;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    @media (min-width: 640px) {
        .modal-header {
            padding: 1.25rem 1.5rem;
            flex-wrap: nowrap;
        }
    }

    .modal-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #1f2937;
        flex: 1;
        min-width: 0;
    }

    @media (min-width: 640px) {
        .modal-title {
            font-size: 1.25rem;
        }
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #6b7280;
        transition: color 0.2s ease;
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .modal-close:hover {
        color: #374151;
        background: #f3f4f6;
    }

    .modal-body {
        padding: 1.25rem;
    }

    @media (min-width: 640px) {
        .modal-body {
            padding: 1.5rem;
        }
    }

    .modal-footer {
        padding: 1.25rem;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        background: #f9fafb;
        border-radius: 0 0 16px 16px;
        flex-wrap: wrap;
    }

    @media (min-width: 640px) {
        .modal-footer {
            padding: 1.5rem;
            flex-wrap: nowrap;
        }
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.25rem;
    }

    @media (min-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
    }

    .upload-dropzone {
        padding: 1.5rem;
        border: 2px dashed #d1d5db;
        border-radius: 16px;
        text-align: center;
        transition: all 0.3s ease;
        background: #f9fafb;
    }

    @media (max-width: 640px) {
        .upload-dropzone {
            padding: 1rem;
        }
        
        .upload-dropzone p {
            font-size: 0.875rem;
        }
        
        .upload-dropzone .text-lg {
            font-size: 1rem;
        }
    }

    .upload-dropzone:hover {
        border-color: #ef4444;
        background: #fef2f2;
    }

    .preview-item {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 1rem;
        background: white;
        margin-bottom: 1rem;
    }

    @media (max-width: 640px) {
        .preview-item {
            padding: 0.75rem;
        }
        
        .preview-grid {
            flex-direction: column;
            gap: 1rem;
        }
        
        .preview-image {
            width: 100%;
            max-width: 120px;
            margin: 0 auto;
        }
        
        .preview-content {
            width: 100%;
        }
    }

    .form-input, .form-select, .form-textarea {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        background: #f9fafb;
        box-sizing: border-box;
    }

    @media (max-width: 640px) {
        .form-input, .form-select, .form-textarea {
            padding: 0.625rem;
            font-size: 16px;
        }
    }

    .form-input:focus, .form-select:focus, .form-textarea:focus {
        outline: none;
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        background: white;
    }

    .button-group {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        width: 100%;
    }

    @media (max-width: 640px) {
        .button-group {
            flex-direction: column;
        }
        
        .button-group .btn {
            width: 100%;
            justify-content: center;
        }
    }

    @media (min-width: 641px) {
        .button-group {
            flex-wrap: nowrap;
            width: auto;
        }
    }

    .checkbox-group, .radio-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    @media (max-width: 640px) {
        .checkbox-group, .radio-group {
            justify-content: space-between;
            width: 100%;
        }
    }

    .image-preview-container {
        margin-top: 1rem;
        padding: 1rem;
        background: #f8fafc;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }

    @media (max-width: 640px) {
        .image-preview-container {
            padding: 0.75rem;
        }
        
        .image-preview-container img {
            max-height: 150px;
        }
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    @media (max-width: 640px) {
        .form-group {
            margin-bottom: 1rem;
        }
    }

    .form-textarea {
        resize: vertical;
        min-height: 100px;
    }

    @media (max-width: 640px) {
        .form-textarea {
            min-height: 80px;
        }
    }

    @supports(padding: max(0px)) {
        .modal-backdrop {
            padding-left: max(1rem, env(safe-area-inset-left));
            padding-right: max(1rem, env(safe-area-inset-right));
            padding-bottom: max(1rem, env(safe-area-inset-bottom));
        }
    }

    body.modal-open {
        overflow: hidden;
        position: fixed;
        width: 100%;
    }

    @media (max-width: 768px) {
        .btn:focus, .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }
    }

    @media (max-width: 768px) {
        .btn {
            min-height: 44px;
            padding: 0.75rem 1rem;
        }
        
        .btn-sm {
            min-height: 36px;
            padding: 0.5rem 0.75rem;
        }
    }

    .modal-content::-webkit-scrollbar {
        width: 6px;
    }

    .modal-content::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 0 0 16px 0;
    }

    .modal-content::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    .modal-content::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideUp {
        from { transform: translateY(30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .animate-fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }

    .animate-slide-up {
        animation: slideUp 0.3s ease-in-out;
    }

    .spinner {
        border: 2px solid #f3f3f3;
        border-top: 2px solid #dc2626;
        border-radius: 50%;
        width: 16px;
        height: 16px;
        animation: spin 1s linear infinite;
        display: inline-block;
        margin-right: 8px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .notification {
        position: fixed;
        top: 1rem;
        right: 1rem;
        padding: 1rem 1.5rem;
        border-radius: 0.75rem;
        color: white;
        z-index: 9999;
        animation: slideIn 0.3s ease-out;
        backdrop-filter: blur(10px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        font-weight: 600;
    }

    .notification.success {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .notification.error {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .notification.info {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    /* Enhanced Gallery Styles */
    .gallery-header {
        background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 50%, #b91c1c 100%);
        border-radius: 16px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        margin-bottom: 2rem;
    }

    .gallery-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
        opacity: 0.3;
    }

    .image-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-top: 1rem;
    }

    .image-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
        position: relative;
    }

    .image-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .image-header {
        position: relative;
        height: 220px;
        overflow: hidden;
        background: #f8fafc;
    }

    .image-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .image-card:hover .image-img {
        transform: scale(1.08);
    }

    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to bottom, transparent 50%, rgba(0, 0, 0, 0.7));
        display: flex;
        align-items: flex-end;
        padding: 1.5rem;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .image-card:hover .image-overlay {
        opacity: 1;
    }

    .image-badge {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    .image-content {
        padding: 1.5rem;
    }

    .image-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
        line-height: 1.4;
    }

    .image-caption {
        color: #6b7280;
        font-size: 0.875rem;
        line-height: 1.5;
        margin-bottom: 1rem;
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
        flex-wrap: wrap;
    }

    /* Enhanced Button Styles */
    .btn {
        padding: 0.5rem 1rem;
        border-radius: 10px;
        font-size: 0.875rem;
        font-weight: 600;
        transition: all 0.3s ease;
        cursor: pointer;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
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
        box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
        transform: translateY(-2px);
    }

    .btn-secondary {
        background: linear-gradient(135deg, #6b7280, #4b5563);
        color: white;
    }

    .btn-secondary:hover {
        background: linear-gradient(135deg, #4b5563, #374151);
        box-shadow: 0 6px 20px rgba(107, 114, 128, 0.4);
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
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .btn-danger {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        color: white;
    }

    .btn-danger:hover {
        background: linear-gradient(135deg, #b91c1c, #991b1b);
        box-shadow: 0 6px 20px rgba(220, 38, 38, 0.4);
        transform: translateY(-2px);
    }

    /* Action Bar */
    .action-bar {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 16px;
        border: 2px dashed #e5e7eb;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }

    .empty-state-icon {
        font-size: 4rem;
        margin-bottom: 1.5rem;
        opacity: 0.5;
    }

    /* Status Indicators */
    .status-indicator {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-active {
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    .status-inactive {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #d1d5db;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .image-grid {
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        }
    }

    @media (max-width: 768px) {
        .image-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1rem;
        }
        
        .image-header {
            height: 180px;
        }
    }

    @media (max-width: 640px) {
        .image-grid {
            grid-template-columns: 1fr;
        }
        
        .image-actions {
            flex-direction: column;
        }
        
        .image-actions .btn {
            justify-content: center;
            width: 100%;
        }
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-gray-50 px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Gallery Header -->
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

    <!-- Action Bar -->
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

            <!-- Bulk Actions Bar -->
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

    <!-- Images Grid -->
    @if($gallery->images->count() > 0)
        <div class="image-grid">
                @foreach($gallery->images->sortBy('sort_order') as $image)
            <div class="image-card" data-image-id="{{ $image->id }}" draggable="true">
                <div class="image-header">
                    @php
                        $imageUrl = $image->image_path ? asset('storage/' . $image->image_path) : '/images/default-image.jpg';
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
        <!-- Empty State -->
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

<!-- Enhanced Responsive Image Upload Modal -->
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
                    <!-- File Dropzone -->
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
                    
                    <!-- Category Input -->
                    <div class="form-group">
                        <label for="upload-category" class="block text-sm font-semibold text-gray-900 mb-2">Default Category</label>
                        <input type="text" id="upload-category" name="category" class="form-input" placeholder="Enter category (e.g., nature, portrait, event)">
                    </div>
                    
                    <!-- File Preview Section -->
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

<!-- Full Image Lightbox -->
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

<!-- Delete Confirmation Modal -->
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

<!-- Image Edit Modal -->
<div id="image-edit-modal" class="modal-backdrop">
    <!-- This will be populated by JavaScript -->
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content_js')
<script>
// Gallery Manager Object - Enhanced with Multi-select, Drag & Drop, and Click-to-View
const GalleryManager = {
    // Global variables
    currentImages: @json($gallery->images),
    currentImageIndex: 0,
    imageToDelete: null,
    selectedFiles: [],
    selectedImages: new Set(),
    isMultiSelectMode: false,
    dragStartIndex: null,
    longPressTimer: null,

    // Initialize the gallery manager
    init() {
        console.log('Gallery Manager initialized with', this.currentImages.length, 'images');
        this.initEventListeners();
        this.initDragAndDrop();
    },

    // Initialize all event listeners
    initEventListeners() {
        // Upload buttons
        document.getElementById('upload-images-btn')?.addEventListener('click', () => this.showUploadModal());
        document.getElementById('empty-upload-btn')?.addEventListener('click', () => this.showUploadModal());

        // Upload modal buttons
        document.getElementById('close-upload-modal')?.addEventListener('click', () => this.hideUploadModal());
        document.getElementById('cancel-upload-btn')?.addEventListener('click', () => this.hideUploadModal());
        document.getElementById('confirm-upload-btn')?.addEventListener('click', () => this.uploadImages());

        // Delete modal buttons
        document.getElementById('cancel-delete-btn')?.addEventListener('click', () => this.hideDeleteModal());
        document.getElementById('confirm-delete-btn')?.addEventListener('click', () => this.deleteImage());

        // Lightbox buttons
        document.getElementById('close-lightbox')?.addEventListener('click', () => this.hideLightbox());
        document.getElementById('prev-image')?.addEventListener('click', () => this.navigateImage(-1));
        document.getElementById('next-image')?.addEventListener('click', () => this.navigateImage(1));

        // Bulk action buttons
        document.getElementById('bulk-delete-btn')?.addEventListener('click', () => this.bulkDeleteImages());
        document.getElementById('clear-selection-btn')?.addEventListener('click', () => this.clearSelection());

        // File handling
        const uploadInput = document.getElementById('upload-images');
        if (uploadInput) {
            uploadInput.addEventListener('change', (e) => this.handleFileSelection(e.target.files));
        }
        
        const dropzoneTrigger = document.getElementById('upload-dropzone-trigger');
        if (dropzoneTrigger && uploadInput) {
            dropzoneTrigger.addEventListener('click', () => uploadInput.click());
        }

        // Event delegation for dynamic image buttons
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

        // Image click for lightbox and multi-select
        document.addEventListener('click', (e) => {
            // Click on image for lightbox (only in normal mode)
            if (e.target.classList.contains('image-img') && !this.isMultiSelectMode) {
                const imageId = parseInt(e.target.dataset.imageId);
                this.viewFullImage(imageId);
            }
            
            // Click on select checkbox
            if (e.target.classList.contains('select-checkbox')) {
                const imageCard = e.target.closest('.image-card');
                const imageId = parseInt(imageCard.dataset.imageId);
                this.toggleImageSelection(imageId, imageCard);
                e.stopPropagation();
            }
        });

        // Keyboard events for multi-select
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + A to select all
            if ((e.ctrlKey || e.metaKey) && e.key === 'a' && this.isMultiSelectMode) {
                e.preventDefault();
                this.selectAllImages();
            }
            
            // Escape to clear selection
            if (e.key === 'Escape' && this.isMultiSelectMode) {
                this.clearSelection();
            }
        });

        // Enable multi-select mode on Ctrl/Cmd key down
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && !this.isMultiSelectMode) {
                this.enableMultiSelectMode();
            }
        });

        // Disable multi-select mode on Ctrl/Cmd key up
        document.addEventListener('keyup', (e) => {
            if (e.key === 'Control' || e.key === 'Meta') {
                this.disableMultiSelectMode();
            }
        });

        // Drag and drop for upload
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

        // Close modals on backdrop click
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

        // Close lightbox with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const lightbox = document.getElementById('image-lightbox');
                if (lightbox && lightbox.classList.contains('show')) {
                    this.hideLightbox();
                }
                
                const deleteModal = document.getElementById('delete-confirm-modal');
                if (deleteModal && deleteModal.classList.contains('show')) {
                    this.hideDeleteModal();
                }
                
                const uploadModal = document.getElementById('upload-modal');
                if (uploadModal && uploadModal.classList.contains('show')) {
                    this.hideUploadModal();
                }
                
                const editModal = document.getElementById('image-edit-modal');
                if (editModal && editModal.classList.contains('show')) {
                    this.hideImageEditModal();
                }
            }
        });
    },

    // Initialize drag and drop for reordering
    initDragAndDrop() {
        const imageGrid = document.querySelector('.image-grid');
        if (!imageGrid) return;

        let dragSrcElement = null;

        // Drag start
        document.addEventListener('dragstart', (e) => {
            if (e.target.classList.contains('image-card') && !this.isMultiSelectMode) {
                dragSrcElement = e.target;
                e.target.classList.add('dragging');
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/html', e.target.outerHTML);
            }
        });

        // Drag over
        document.addEventListener('dragover', (e) => {
            if (!dragSrcElement) return;
            e.preventDefault();
            const target = e.target.closest('.image-card');
            if (target && target !== dragSrcElement) {
                target.classList.add('drag-over');
            }
        });

        // Drag leave
        document.addEventListener('dragleave', (e) => {
            const target = e.target.closest('.image-card');
            if (target) {
                target.classList.remove('drag-over');
            }
        });

        // Drop
        document.addEventListener('drop', (e) => {
            if (!dragSrcElement) return;
            e.preventDefault();
            const target = e.target.closest('.image-card');
            
            if (target && dragSrcElement && target !== dragSrcElement) {
                target.classList.remove('drag-over');
                
                // Get the image grid
                const imageGrid = document.querySelector('.image-grid');
                const images = Array.from(imageGrid.children);
                const fromIndex = images.indexOf(dragSrcElement);
                const toIndex = images.indexOf(target);
                
                if (fromIndex < toIndex) {
                    imageGrid.insertBefore(dragSrcElement, target.nextSibling);
                } else {
                    imageGrid.insertBefore(dragSrcElement, target);
                }
                
                // Update sort orders
                this.updateImageOrders();
            }
        });

        // Drag end
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

    // In the updateImageOrders method:
async updateImageOrders() {
    const imageCards = document.querySelectorAll('.image-card');
    const updates = [];
    
    imageCards.forEach((card, index) => {
        const imageId = parseInt(card.dataset.imageId);
        const newOrder = index + 1;
        
        updates.push({
            id: imageId,
            sort_order: newOrder
        });
    });

    try {
        console.log('Updating image order:', updates);
        
        const response = await fetch(this.getReorderRoute(), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ updates })
        });

        // Check if the response is OK
        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`HTTP ${response.status}: ${errorText}`);
        }

        const result = await response.json();
        
        if (result.success) {
            this.showNotification('Image order updated successfully', 'success');
            // Update current images array
            this.currentImages.forEach(image => {
                const update = updates.find(u => u.id === image.id);
                if (update) {
                    image.sort_order = update.sort_order;
                }
            });
        } else {
            throw new Error(result.message || 'Failed to update image order');
        }
    } catch (error) {
        console.error('Error updating image order:', error);
        this.showNotification('Error updating image order: ' + error.message, 'error');
    }
},

    // Multi-select functionality
    enableMultiSelectMode() {
        if (this.isMultiSelectMode) return;
        
        this.isMultiSelectMode = true;
        document.body.classList.add('multi-select-mode');
        
        // Add select checkboxes to all images
        document.querySelectorAll('.image-card').forEach(card => {
            if (!card.querySelector('.select-checkbox')) {
                const checkbox = document.createElement('div');
                checkbox.className = 'select-checkbox';
                checkbox.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const imageId = parseInt(card.dataset.imageId);
                    this.toggleImageSelection(imageId, card);
                });
                card.style.position = 'relative';
                card.appendChild(checkbox);
                
                // Add selection overlay
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
        
        // Remove checkboxes and overlays
        document.querySelectorAll('.image-card').forEach(card => {
            const checkbox = card.querySelector('.select-checkbox');
            if (checkbox) checkbox.remove();
            
            const overlay = card.querySelector('.selection-overlay');
            if (overlay) overlay.remove();
        });
    },

    toggleImageSelection(imageId, imageCard) {
        if (!this.isMultiSelectMode) {
            this.enableMultiSelectMode();
        }

        if (this.selectedImages.has(imageId)) {
            this.selectedImages.delete(imageId);
            imageCard.classList.remove('selected');
            const checkbox = imageCard.querySelector('.select-checkbox');
            if (checkbox) checkbox.classList.remove('checked');
        } else {
            this.selectedImages.add(imageId);
            imageCard.classList.add('selected');
            const checkbox = imageCard.querySelector('.select-checkbox');
            if (checkbox) checkbox.classList.add('checked');
        }
        
        this.updateBulkActionsBar();
    },

    selectAllImages() {
        if (!this.isMultiSelectMode) return;
        
        document.querySelectorAll('.image-card').forEach(card => {
            const imageId = parseInt(card.dataset.imageId);
            this.selectedImages.add(imageId);
            card.classList.add('selected');
            const checkbox = card.querySelector('.select-checkbox');
            if (checkbox) checkbox.classList.add('checked');
        });
        
        this.updateBulkActionsBar();
    },

    clearSelection() {
        this.selectedImages.clear();
        document.querySelectorAll('.image-card').forEach(card => {
            card.classList.remove('selected');
            const checkbox = card.querySelector('.select-checkbox');
            if (checkbox) checkbox.classList.remove('checked');
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

    // Route helper methods - FIXED VERSION
getBulkDeleteRoute() {
    return '/admin/galleries/images/bulk-delete';
},

getReorderRoute() {
    return '/admin/galleries/images/reorder';
},

// Update these route methods in your GalleryManager
getEditImageRoute(imageId) {
    // Use the same endpoint as your index page
    return `/admin/galleries/images/${imageId}`;
},

getUpdateImageRoute(imageId) {
    return `/admin/galleries/images/${imageId}`;
},

getDeleteImageRoute(imageId) {
    return `/admin/galleries/images/${imageId}`;
},

getSetFeaturedRoute(imageId) {
    return `/admin/galleries/images/${imageId}/set-featured`;
},

getRemoveFeaturedRoute(imageId) {
    return `/admin/galleries/images/${imageId}/remove-featured`;
},

getUploadRoute() {
    return '/admin/galleries/upload';
},

    async bulkDeleteImages() {
        if (this.selectedImages.size === 0) return;

        const confirmed = confirm(`Are you sure you want to delete ${this.selectedImages.size} image${this.selectedImages.size > 1 ? 's' : ''}? This action cannot be undone.`);
        
        if (!confirmed) return;

        try {
            const bulkButton = document.getElementById('bulk-delete-btn');
            const originalText = bulkButton.innerHTML;
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
                const result = await response.json();
                this.showNotification(result.message || `${this.selectedImages.size} images deleted successfully`, 'success');
                
                // Remove deleted images from DOM
                this.selectedImages.forEach(imageId => {
                    const imageCard = document.querySelector(`[data-image-id="${imageId}"]`);
                    if (imageCard) {
                        imageCard.style.transform = 'scale(0.8)';
                        imageCard.style.opacity = '0';
                        setTimeout(() => {
                            imageCard.remove();
                            // Update current images array
                            this.currentImages = this.currentImages.filter(img => img.id !== imageId);
                        }, 300);
                    }
                });
                
                this.clearSelection();
            } else {
                throw new Error('Failed to delete images');
            }
        } catch (error) {
            console.error('Error bulk deleting images:', error);
            this.showNotification('Error deleting images', 'error');
        } finally {
            const bulkButton = document.getElementById('bulk-delete-btn');
            if (bulkButton) {
                bulkButton.innerHTML = 'Delete Selected';
                bulkButton.disabled = false;
            }
        }
    },

    // Lightbox functions
    viewFullImage(imageId) {
        const image = this.currentImages.find(img => img.id === imageId);
        if (!image) {
            this.showNotification('Image not found', 'error');
            return;
        }

        this.currentImageIndex = this.currentImages.findIndex(img => img.id === imageId);
        
        const lightbox = document.getElementById('image-lightbox');
        const lightboxImage = document.getElementById('lightbox-image');
        const lightboxTitle = document.getElementById('lightbox-title');
        const lightboxCaption = document.getElementById('lightbox-caption');
        const lightboxMeta = document.getElementById('lightbox-meta');

        const imageUrl = image.image_path ? `/storage/${image.image_path}` : '/images/default-image.jpg';
        
        lightboxImage.src = imageUrl;
        lightboxImage.alt = image.image_alt || image.title || 'Gallery Image';
        
        if (lightboxTitle) {
            lightboxTitle.textContent = image.title || 'Untitled Image';
        }
        
        if (lightboxCaption) {
            lightboxCaption.textContent = image.caption || '';
            lightboxCaption.style.display = image.caption ? 'block' : 'none';
        }
        
        if (lightboxMeta) {
            lightboxMeta.innerHTML = `
                <span>Category: ${image.category || 'General'}</span>
                <span>Order: ${image.sort_order}</span>
                ${image.is_featured ? '<span class="text-yellow-300">★ Featured</span>' : ''}
            `;
        }
        
        // Add or update counter
        let counter = lightbox.querySelector('.lightbox-counter');
        if (!counter) {
            counter = document.createElement('div');
            counter.className = 'lightbox-counter';
            lightbox.querySelector('.lightbox-content').appendChild(counter);
        }
        counter.textContent = `${this.currentImageIndex + 1} / ${this.currentImages.length}`;
        
        lightbox.classList.add('show');
        document.body.style.overflow = 'hidden';
        
        this.preloadAdjacentImages();
    },

    preloadAdjacentImages() {
        const preloadImage = (index) => {
            if (index >= 0 && index < this.currentImages.length) {
                const img = new Image();
                const image = this.currentImages[index];
                img.src = image.image_path ? `/storage/${image.image_path}` : '/images/default-image.jpg';
            }
        };
        
        preloadImage(this.currentImageIndex - 1);
        preloadImage(this.currentImageIndex + 1);
    },

    navigateImage(direction) {
        this.currentImageIndex += direction;
        
        if (this.currentImageIndex < 0) {
            this.currentImageIndex = this.currentImages.length - 1;
        } else if (this.currentImageIndex >= this.currentImages.length) {
            this.currentImageIndex = 0;
        }
        
        const image = this.currentImages[this.currentImageIndex];
        this.viewFullImage(image.id);
    },

    hideLightbox() {
        const lightbox = document.getElementById('image-lightbox');
        if (lightbox) {
            lightbox.classList.remove('show');
            // Remove counter
            const counter = lightbox.querySelector('.lightbox-counter');
            if (counter) counter.remove();
        }
        document.body.style.overflow = '';
    },

    // Delete image functionality
    confirmDeleteImage(imageId, imageTitle) {
        this.imageToDelete = imageId;
        const titleElement = document.getElementById('delete-image-title');
        const modal = document.getElementById('delete-confirm-modal');
        
        if (titleElement && modal) {
            titleElement.textContent = imageTitle;
            modal.classList.add('show');
        }
    },

    hideDeleteModal() {
        this.imageToDelete = null;
        const modal = document.getElementById('delete-confirm-modal');
        if (modal) {
            modal.classList.remove('show');
        }
    },

    async deleteImage() {
        if (!this.imageToDelete) return;

        try {
            const deleteButton = document.getElementById('confirm-delete-btn');
            const originalText = deleteButton.innerHTML;
            deleteButton.innerHTML = '<div class="spinner"></div> Deleting...';
            deleteButton.disabled = true;

            // In the deleteImage method:
            const response = await fetch(this.getDeleteImageRoute(this.imageToDelete), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                }
            });

            if (response.ok) {
                const result = await response.json();
                this.showNotification('Image deleted successfully', 'success');
                this.hideDeleteModal();
                
                // Remove image from DOM
                const imageCard = document.querySelector(`[data-image-id="${this.imageToDelete}"]`);
                if (imageCard) {
                    imageCard.style.transform = 'scale(0.8)';
                    imageCard.style.opacity = '0';
                    setTimeout(() => {
                        imageCard.remove();
                        // Update current images array
                        this.currentImages = this.currentImages.filter(img => img.id !== this.imageToDelete);
                    }, 300);
                }
            } else {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Failed to delete image');
            }
        } catch (error) {
            console.error('Error deleting image:', error);
            this.showNotification('Error deleting image: ' + error.message, 'error');
        } finally {
            const deleteButton = document.getElementById('confirm-delete-btn');
            if (deleteButton) {
                deleteButton.innerHTML = 'Delete Image';
                deleteButton.disabled = false;
            }
        }
    },

    // In the editImage method:
async editImage(imageId) {
    try {
        console.log('Editing image ID:', imageId);
        
        // Show loading state in modal
        this.showImageEditModal('Loading Image', '<div class="loading"><div class="spinner"></div><p class="mt-2 text-gray-500">Loading image data...</p></div>');
        
        // Use the same endpoint as your index page
        const response = await fetch(`/admin/galleries/images/${imageId}`);
        
        console.log('Response status:', response.status);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Server response:', errorText);
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Image data loaded:', data);
        
        if (!data.image) {
            throw new Error('Image data not found in response');
        }
        
        const image = data.image;
        this.showImageEditModal(`Edit Image: ${image.title || 'Untitled'}`, this.createImageEditForm(image));
        
    } catch (error) {
        console.error('Error loading image:', error);
        this.showNotification(`Error loading image data: ${error.message}`, 'error');
        this.hideImageEditModal();
    }
},

// Create edit form - FIXED VERSION
createImageEditForm(image) {
    return `
        <form id="image-edit-form" class="space-y-6">
            <input type="hidden" name="id" value="${image.id}">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div class="form-group">
                        <label class="form-label" for="edit-image-title">Title</label>
                        <input type="text" id="edit-image-title" name="title" class="form-input" 
                               value="${this.escapeHtml(image.title || '')}" placeholder="Image title">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="edit-image-alt">Alt Text</label>
                        <input type="text" id="edit-image-alt" name="image_alt" class="form-input" 
                               value="${this.escapeHtml(image.image_alt || '')}" placeholder="Description for accessibility">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="edit-image-caption">Caption</label>
                        <textarea id="edit-image-caption" name="caption" class="form-input form-textarea" 
                                  placeholder="Image caption" rows="3">${this.escapeHtml(image.caption || '')}</textarea>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="form-group">
                        <label class="form-label" for="edit-image-category">Category</label>
                        <input type="text" id="edit-image-category" name="category" class="form-input" 
                               value="${this.escapeHtml(image.category || '')}" placeholder="Any">
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
},

   // Show edit modal - FIXED VERSION
showImageEditModal(title, content) {
    const modalHtml = `
        <div id="image-edit-modal" class="modal-backdrop show">
            <div class="modal-content animate-slide-up" style="max-width: 700px;">
                <div class="modal-header">
                    <h3 class="modal-title">${title}</h3>
                    <button class="modal-close" id="close-edit-modal">&times;</button>
                </div>
                <div class="modal-body">
                    ${content}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" id="cancel-edit-btn">Cancel</button>
                    <button type="button" class="btn btn-primary" id="save-edit-btn">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('image-edit-modal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add new modal to body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Add event listeners for the new modal
    setTimeout(() => {
        document.getElementById('close-edit-modal')?.addEventListener('click', () => this.hideImageEditModal());
        document.getElementById('cancel-edit-btn')?.addEventListener('click', () => this.hideImageEditModal());
        document.getElementById('save-edit-btn')?.addEventListener('click', () => this.saveImageChanges());
    }, 100);
},

// Hide edit modal
hideImageEditModal() {
    const modal = document.getElementById('image-edit-modal');
    if (modal) {
        modal.remove();
    }
},

    async saveImageChanges() {
    const form = document.getElementById('image-edit-form');
    if (!form) {
        this.showNotification('Edit form not found', 'error');
        return;
    }

    const formData = new FormData(form);
    const imageId = formData.get('id');
    
    if (!imageId) {
        this.showNotification('Image ID not found', 'error');
        return;
    }

    try {
        // Get form values directly from inputs
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

        console.log('Saving image data:', data);

        const saveButton = document.querySelector('#image-edit-modal .btn-primary');
        if (saveButton) {
            const originalText = saveButton.innerHTML;
            saveButton.innerHTML = '<div class="spinner"></div> Saving...';
            saveButton.disabled = true;
        }

        // Use the same endpoint as your index page
        const response = await fetch(`/admin/galleries/images/${imageId}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify(data)
        });

        console.log('Save response status:', response.status);

        if (response.ok) {
            const result = await response.json();
            console.log('Save response data:', result);
            
            this.showNotification('Image updated successfully', 'success');
            this.hideImageEditModal();
            
            // Reload the page to reflect changes
            setTimeout(() => {
                window.location.reload();
            }, 1000);
            
        } else {
            const errorText = await response.text();
            console.error('Save error response:', errorText);
            throw new Error(`Failed to update image: ${response.status}`);
        }
        
    } catch (error) {
        console.error('Error updating image:', error);
        this.showNotification('Error updating image: ' + error.message, 'error');
        
        const saveButton = document.querySelector('#image-edit-modal .btn-primary');
        if (saveButton) {
            saveButton.innerHTML = 'Save Changes';
            saveButton.disabled = false;
        }
    }
},

    // Featured image functions
    async setFeaturedImage(imageId) {
        try {
            const response = await fetch(this.getSetFeaturedRoute(imageId), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                }
            });
            if (response.ok) {
                this.showNotification('Image set as featured', 'success');
                setTimeout(() => window.location.reload(), 500);
            } else {
                throw new Error('Failed to set featured image');
            }
        } catch (error) {
            console.error('Error setting featured image:', error);
            this.showNotification('Error setting featured image', 'error');
        }
    },

    async removeFeaturedImage(imageId) {
        try {
            const response = await fetch(this.getRemoveFeaturedRoute(imageId), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                }
            });
            if (response.ok) {
                this.showNotification('Image removed from featured', 'success');
                setTimeout(() => window.location.reload(), 500);
            } else {
                throw new Error('Failed to remove featured image');
            }
        } catch (error) {
            console.error('Error removing featured image:', error);
            this.showNotification('Error removing featured image', 'error');
        }
    },

    // Upload modal functions
    showUploadModal() {
        const modal = document.getElementById('upload-modal');
        if (modal) {
            modal.classList.add('show');
        }
        
        this.selectedFiles = [];
        const fileInput = document.getElementById('upload-images');
        if (fileInput) {
            fileInput.value = '';
        }
        
        const preview = document.getElementById('upload-preview');
        if (preview) preview.classList.add('hidden');
        
        const previewContainer = document.getElementById('preview-container');
        if (previewContainer) previewContainer.innerHTML = '';
    },

    hideUploadModal() {
        const modal = document.getElementById('upload-modal');
        if (modal) {
            modal.classList.remove('show');
        }
        
        this.selectedFiles = [];
        const uploadInput = document.getElementById('upload-images');
        if (uploadInput) uploadInput.value = '';
        
        const preview = document.getElementById('upload-preview');
        if (preview) preview.classList.add('hidden');
        
        const previewContainer = document.getElementById('preview-container');
        if (previewContainer) previewContainer.innerHTML = '';
        
        const categoryInput = document.getElementById('upload-category');
        if (categoryInput) categoryInput.value = '';
    },

    // File handling
    handleFileSelection(files) {
        this.selectedFiles = Array.from(files);
        const previewContainer = document.getElementById('preview-container');
        const previewSection = document.getElementById('upload-preview');
        
        if (previewContainer) previewContainer.innerHTML = '';
        
        if (files.length > 0 && previewSection) {
            previewSection.classList.remove('hidden');
            
            this.selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const fileName = file.name.replace(/\.[^/.]+$/, "");
                    const fileExtension = file.name.split('.').pop();
                    const fileSize = this.formatFileSize(file.size);
                    
                    const previewItem = document.createElement('div');
                    previewItem.className = 'preview-item';
                    previewItem.innerHTML = `
                        <div class="flex flex-col sm:flex-row gap-4 items-start preview-grid">
                            <div class="flex-shrink-0 preview-image">
                                <img src="${e.target.result}" 
                                     class="w-full h-24 sm:h-20 object-cover rounded-lg border border-gray-300 shadow-sm"
                                     alt="Preview">
                            </div>
                            <div class="flex-1 space-y-3 preview-content">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                                    <input type="text" 
                                           class="form-input text-sm"
                                           value="${fileName}"
                                           data-file-index="${index}"
                                           placeholder="Enter image title"
                                           onchange="GalleryManager.updateFileName(${index}, this.value)">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Alt Text</label>
                                    <input type="text" 
                                           class="form-input text-sm"
                                           value="${fileName}"
                                           data-file-index="${index}"
                                           placeholder="Enter alt text for accessibility"
                                           onchange="GalleryManager.updateFileAlt(${index}, this.value)">
                                </div>
                                
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                    <div class="flex items-center gap-4">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" 
                                                   name="active_${index}" 
                                                   value="1" 
                                                   checked
                                                   onchange="GalleryManager.updateFileActiveStatus(${index}, true)"
                                                   class="h-3 w-3 text-red-600 focus:ring-red-500">
                                            <span class="text-sm text-green-600 font-medium">Active</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" 
                                                   name="active_${index}" 
                                                   value="0"
                                                   onchange="GalleryManager.updateFileActiveStatus(${index}, false)"
                                                   class="h-3 w-3 text-red-600 focus:ring-red-500">
                                            <span class="text-sm text-gray-600 font-medium">Inactive</span>
                                        </label>
                                    </div>
                                    
                                    <button type="button" 
                                            class="text-red-600 hover:text-red-800 text-sm font-medium remove-file-btn self-start sm:self-center"
                                            data-file-index="${index}">
                                        Remove
                                    </button>
                                </div>
                                
                                <div class="text-xs text-gray-500">
                                    ${fileExtension.toUpperCase()} • ${fileSize}
                                </div>
                            </div>
                        </div>
                    `;
                    if (previewContainer) {
                        previewContainer.appendChild(previewItem);
                    }

                    // Add remove file event listener
                    const removeBtn = previewItem.querySelector('.remove-file-btn');
                    removeBtn.addEventListener('click', () => this.removeFile(index));
                };
                reader.readAsDataURL(file);
            });
        } else if (previewSection) {
            previewSection.classList.add('hidden');
        }
    },

    updateFileActiveStatus(fileIndex, isActive) {
        if (this.selectedFiles[fileIndex]) {
            if (!this.selectedFiles[fileIndex].customData) {
                this.selectedFiles[fileIndex].customData = {};
            }
            this.selectedFiles[fileIndex].customData.is_active = isActive;
        }
    },

    updateFileName(fileIndex, newTitle) {
        if (this.selectedFiles[fileIndex]) {
            if (!this.selectedFiles[fileIndex].customData) {
                this.selectedFiles[fileIndex].customData = {};
            }
            this.selectedFiles[fileIndex].customData.title = newTitle;
        }
    },

    updateFileAlt(fileIndex, newAlt) {
        if (this.selectedFiles[fileIndex]) {
            if (!this.selectedFiles[fileIndex].customData) {
                this.selectedFiles[fileIndex].customData = {};
            }
            this.selectedFiles[fileIndex].customData.alt = newAlt;
        }
    },

    removeFile(fileIndex) {
        this.selectedFiles.splice(fileIndex, 1);
        
        const previewContainer = document.getElementById('preview-container');
        const previewSection = document.getElementById('upload-preview');
        
        if (previewContainer) previewContainer.innerHTML = '';
        
        if (this.selectedFiles.length > 0) {
            this.handleFileSelection(this.selectedFiles);
        } else if (previewSection) {
            previewSection.classList.add('hidden');
            const uploadInput = document.getElementById('upload-images');
            if (uploadInput) uploadInput.value = '';
        }
    },

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    },

    async uploadImages() {
        if (this.selectedFiles.length === 0) {
            this.showNotification('Please select at least one image to upload', 'error');
            return;
        }

        const galleryId = {{ $gallery->id }};
        const categoryInput = document.getElementById('upload-category');
        const category = categoryInput ? categoryInput.value : 'any';

        const formData = new FormData();
        
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        formData.append('gallery_id', galleryId);
        formData.append('category', category);

        this.selectedFiles.forEach((file, index) => {
            formData.append('images[]', file);
            
            const customTitle = file.customData?.title;
            if (customTitle && customTitle !== file.name.replace(/\.[^/.]+$/, "")) {
                formData.append(`titles[${index}]`, customTitle);
            } else {
                formData.append(`titles[${index}]`, file.name.replace(/\.[^/.]+$/, ""));
            }
            
            const customAlt = file.customData?.alt;
            if (customAlt) {
                formData.append(`alt_texts[${index}]`, customAlt);
            } else {
                const defaultAlt = customTitle || file.name.replace(/\.[^/.]+$/, "");
                formData.append(`alt_texts[${index}]`, defaultAlt);
            }
            
            const isActive = file.customData?.is_active !== false;
            formData.append(`active_status[${index}]`, isActive ? '1' : '0');
        });

        try {
            const uploadButton = document.getElementById('confirm-upload-btn');
            const originalText = uploadButton.innerHTML;
            uploadButton.innerHTML = '<div class="spinner"></div> Uploading...';
            uploadButton.disabled = true;

            const response = await fetch(this.getUploadRoute(), {
                method: 'POST',
                body: formData
            });

            if (response.ok) {
                const result = await response.json();
                this.showNotification(result.message || this.selectedFiles.length + ' images uploaded successfully', 'success');
                this.hideUploadModal();
                
                setTimeout(() => window.location.reload(), 1500);
            } else {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Failed to upload images');
            }
        } catch (error) {
            console.error('Error uploading images:', error);
            this.showNotification('Error uploading images: ' + error.message, 'error');
        } finally {
            const uploadButton = document.getElementById('confirm-upload-btn');
            if (uploadButton) {
                uploadButton.innerHTML = 'Upload Images';
                uploadButton.disabled = false;
            }
        }
    },

    // Utility functions
    escapeHtml(unsafe) {
        if (!unsafe) return '';
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    },

    showNotification(message, type = 'info') {
        // Remove existing notifications
        const existingNotifications = document.querySelectorAll('.notification');
        existingNotifications.forEach(notification => notification.remove());

        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 4000);
    }
};

// Initialize the gallery manager when the page loads
document.addEventListener('DOMContentLoaded', function() {
    GalleryManager.init();
});

// Add touch support for mobile devices
document.addEventListener('touchstart', function(e) {
    // Long press to enable multi-select on mobile
    if (e.target.closest('.image-card')) {
        GalleryManager.longPressTimer = setTimeout(() => {
            GalleryManager.enableMultiSelectMode();
        }, 500);
    }
}, { passive: true });

document.addEventListener('touchend', function(e) {
    clearTimeout(GalleryManager.longPressTimer);
}, { passive: true });

document.addEventListener('touchmove', function(e) {
    clearTimeout(GalleryManager.longPressTimer);
}, { passive: true });
</script>
@endsection