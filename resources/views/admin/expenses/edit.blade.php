@extends('layouts.admin')
@section('title', 'Edit Expense')

@php
	$active = 'expenses';
@endphp

@section('content_css')
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<style>
		.form-card {
			box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
			border-radius: 0.75rem;
			background-color: white;
		}

		.form-card-header {
			border-bottom: 1px solid #e5e7eb;
			padding: 0.75rem 1rem;
		}

		.form-card-body {
			padding: 1.5rem;
		}

		.form-group {
			margin-bottom: 1rem;
		}

		.form-label {
			display: block;
			font-size: 0.875rem;
			font-weight: 500;
			color: #374151;
			margin-bottom: 0.5rem;
		}

		.form-input {
			width: 100%;
			padding: 0.625rem 0.75rem;
			border: 1px solid #d1d5db;
			border-radius: 0.5rem;
			font-size: 0.875rem;
			transition: all 0.2s;
		}

		.form-input:focus {
			outline: none;
			ring: 2px;
			ring-color: #3b82f6;
			border-color: #3b82f6;
		}

		.form-select {
			width: 100%;
			padding: 0.625rem 0.75rem;
			border: 1px solid #d1d5db;
			border-radius: 0.5rem;
			font-size: 0.875rem;
			background-color: white;
			transition: all 0.2s;
		}

		.form-select:focus {
			outline: none;
			ring: 2px;
			ring-color: #3b82f6;
			border-color: #3b82f6;
		}

		.btn-primary {
			background-color: #3b82f6;
			color: white;
			padding: 0.625rem 1.25rem;
			border-radius: 0.5rem;
			font-size: 0.875rem;
			font-weight: 500;
			transition: all 0.2s;
			display: inline-flex;
			align-items: center;
			gap: 0.5rem;
		}

		.btn-primary:hover {
			background-color: #2563eb;
		}

		.btn-secondary {
			background-color: #6b7280;
			color: white;
			padding: 0.625rem 1.25rem;
			border-radius: 0.5rem;
			font-size: 0.875rem;
			font-weight: 500;
			transition: all 0.2s;
			display: inline-flex;
			align-items: center;
			gap: 0.5rem;
		}

		.btn-secondary:hover {
			background-color: #4b5563;
		}

		.error-message {
			color: #ef4444;
			font-size: 0.75rem;
			margin-top: 0.25rem;
		}

		.category-badges {
			display: flex;
			flex-wrap: wrap;
			gap: 0.5rem;
			margin-top: 0.5rem;
		}

		.category-badge {
			padding: 0.25rem 0.75rem;
			background-color: #f3f4f6;
			border-radius: 1rem;
			font-size: 0.75rem;
			color: #374151;
			cursor: pointer;
			transition: all 0.2s;
		}

		.category-badge:hover {
			background-color: #e5e7eb;
		}

		.category-badge.active {
			background-color: #3b82f6;
			color: white;
		}
	</style>
@endsection

@section('content')
<div class="min-h-screen px-6 py-6">
	<!-- Header -->
	<div class="form-card mb-6 border-t-4 border-t-red-500">
		<div class="form-card-header">
			<div class="flex items-center gap-3">
				<i class="fas fa-edit text-red-600 text-xl"></i>
				<h1 class="text-red-600 font-bold text-xl">Edit Expense</h1>
			</div>
		</div>
	</div>

	<!-- Form Card -->
	<div class="form-card max-w-4xl">
		<div class="form-card-header">
			<h2 class="text-gray-900 font-semibold flex items-center gap-2 text-sm">
				<i class="fas fa-receipt"></i> Update Expense Details
			</h2>
		</div>

		<div class="form-card-body">
			<form method="POST" action="{{ route('admin.expenses.update', $expense) }}">
				@csrf @method('PUT')
				
				<div class="grid md:grid-cols-2 gap-6">
					<!-- Category Field -->
					<div class="form-group">
						<label for="category" class="form-label">Category *</label>
						<select name="category" id="category" required class="form-select">
							<option value="">Select a category</option>
							<option value="Utilities" {{ old('category', $expense->category) == 'Utilities' ? 'selected' : '' }}>Utilities</option>
							<option value="Supplies" {{ old('category', $expense->category) == 'Supplies' ? 'selected' : '' }}>Supplies</option>
							<option value="Maintenance" {{ old('category', $expense->category) == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
							<option value="Staff" {{ old('category', $expense->category) == 'Staff' ? 'selected' : '' }}>Staff</option>
							<option value="Marketing" {{ old('category', $expense->category) == 'Marketing' ? 'selected' : '' }}>Marketing</option>
							<option value="Office" {{ old('category', $expense->category) == 'Office' ? 'selected' : '' }}>Office</option>
							<option value="Other" {{ old('category', $expense->category) == 'Other' ? 'selected' : '' }}>Other</option>
						</select>
						@error('category')
							<p class="error-message">{{ $message }}</p>
						@enderror
						
						<div class="category-badges">
							<span class="category-badge" data-category="Utilities">Utilities</span>
							<span class="category-badge" data-category="Supplies">Supplies</span>
							<span class="category-badge" data-category="Maintenance">Maintenance</span>
							<span class="category-badge" data-category="Staff">Staff</span>
							<span class="category-badge" data-category="Marketing">Marketing</span>
							<span class="category-badge" data-category="Office">Office</span>
							<span class="category-badge" data-category="Other">Other</span>
						</div>
					</div>

					<!-- Amount Field -->
					<div class="form-group">
						<label for="amount" class="form-label">Amount *</label>
						<div class="relative">
							<span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
								₱
							</span>
							<input type="number" name="amount" id="amount" step="0.01" min="0" required 
								   value="{{ old('amount', $expense->amount) }}" 
								   class="form-input pl-8" 
								   placeholder="0.00">
						</div>
						@error('amount')
							<p class="error-message">{{ $message }}</p>
						@enderror
					</div>

					<!-- Date Field -->
					<div class="form-group">
						<label for="expense_date" class="form-label">Date *</label>
						<input type="date" name="expense_date" id="expense_date" required 
							   value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" 
							   class="form-input">
						@error('expense_date')
							<p class="error-message">{{ $message }}</p>
						@enderror
					</div>

					<!-- Description Field -->
					<div class="form-group md:col-span-2">
						<label for="description" class="form-label">Description</label>
						<textarea name="description" id="description" rows="3" 
								  class="form-input" 
								  placeholder="Enter expense description (optional)">{{ old('description', $expense->description) }}</textarea>
						@error('description')
							<p class="error-message">{{ $message }}</p>
						@enderror
					</div>
				</div>

				<!-- Form Actions -->
				<div class="flex gap-3 mt-8 pt-6 border-t border-gray-200">
					<button type="submit" class="btn-primary">
						<i class="fas fa-save"></i> Update Expense
					</button>
					<a href="{{ route('admin.expenses.index') }}" class="btn-secondary">
						<i class="fas fa-times"></i> Cancel
					</a>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection

@section('content_js')
<script>
	document.addEventListener('DOMContentLoaded', function() {
		// Category badge selection
		const categoryBadges = document.querySelectorAll('.category-badge');
		const categorySelect = document.getElementById('category');

		categoryBadges.forEach(badge => {
			badge.addEventListener('click', function() {
				const category = this.getAttribute('data-category');
				categorySelect.value = category;
				
				// Update badge states
				categoryBadges.forEach(b => b.classList.remove('active'));
				this.classList.add('active');
			});
		});

		// Sync badge states with select
		categorySelect.addEventListener('change', function() {
			const selectedCategory = this.value;
			categoryBadges.forEach(badge => {
				if (badge.getAttribute('data-category') === selectedCategory) {
					badge.classList.add('active');
				} else {
					badge.classList.remove('active');
				}
			});
		});

		// Initialize badge states
		if (categorySelect.value) {
			categoryBadges.forEach(badge => {
				if (badge.getAttribute('data-category') === categorySelect.value) {
					badge.classList.add('active');
				}
			});
		}
	});
</script>
@endsection