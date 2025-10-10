@extends('layouts.admin')
@section('title', 'Expenses Management')

@php
	$active = 'expenses';
@endphp

@section('content_css')
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<style>
		.expenses-card {
			box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
			border-radius: 0.75rem;
			background-color: white;
		}

		.expenses-card-header {
			border-bottom: 1px solid #e5e7eb;
			padding: 0.75rem 1rem;
		}

		.expenses-card-body {
			padding: 1rem;
		}

		.expenses-table {
			width: 100%;
			border-collapse: collapse;
		}

		.expenses-table th {
			background-color: #f9fafb;
			padding: 0.75rem;
			font-size: 0.75rem;
			font-weight: 600;
			color: #6b7280;
			text-align: left;
			border-bottom: 1px solid #e5e7eb;
		}

		.expenses-table td {
			padding: 0.75rem;
			border-bottom: 1px solid #f3f4f6;
			font-size: 0.875rem;
		}

		.expenses-table tbody tr:hover {
			background-color: #f9fafb;
		}

		.action-buttons {
			display: flex;
			gap: 0.5rem;
		}

		.btn-edit {
			background-color: #3b82f6;
			color: white;
			padding: 0.375rem 0.75rem;
			border-radius: 0.375rem;
			font-size: 0.75rem;
			font-weight: 500;
			transition: all 0.2s;
		}

		.btn-edit:hover {
			background-color: #2563eb;
		}

		.btn-delete {
			background-color: #ef4444;
			color: white;
			padding: 0.375rem 0.75rem;
			border-radius: 0.375rem;
			font-size: 0.75rem;
			font-weight: 500;
			transition: all 0.2s;
		}

		.btn-delete:hover {
			background-color: #dc2626;
		}

		.btn-add {
			background-color: #10b981;
			color: white;
			padding: 0.5rem 1rem;
			border-radius: 0.5rem;
			font-size: 0.875rem;
			font-weight: 500;
			transition: all 0.2s;
			display: flex;
			align-items: center;
			gap: 0.5rem;
		}

		.btn-add:hover {
			background-color: #059669;
		}

		.amount-positive {
			color: #10b981;
			font-weight: 600;
		}

		.amount-negative {
			color: #ef4444;
			font-weight: 600;
		}

		.empty-state {
			padding: 3rem 1rem;
			text-align: center;
			color: #6b7280;
		}

		.empty-state i {
			font-size: 3rem;
			margin-bottom: 1rem;
			color: #d1d5db;
		}

		@media (max-width: 768px) {
			.expenses-table {
				display: block;
				overflow-x: auto;
			}

			.action-buttons {
				flex-direction: column;
				gap: 0.25rem;
			}

			.btn-edit, .btn-delete {
				padding: 0.25rem 0.5rem;
				font-size: 0.7rem;
			}
		}
	</style>
@endsection

@section('content')
<div class="min-h-screen px-6 py-6">
	<!-- Header with Stats -->
	<div class="expenses-card mb-6 border-t-4 border-t-red-500">
		<div class="expenses-card-header">
			<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
				<div class="flex items-center gap-3">
					<i class="fas fa-money-bill-wave text-red-600 text-xl"></i>
					<h1 class="text-red-600 font-bold text-xl">Expenses Management</h1>
				</div>

				<!-- Quick Stats -->
				<div class="flex flex-wrap gap-4 text-sm">
					<div class="flex items-center gap-1">
						<span class="text-gray-600">Total:</span>
						<span class="font-bold text-red-600">₱{{ number_format($totalExpenses, 2) }}</span>
					</div>
					<div class="flex items-center gap-1">
						<span class="text-gray-600">Count:</span>
						<span class="font-bold text-purple-600">{{ $expenses->total() }}</span>
					</div>
					<div class="flex items-center gap-1">
						<span class="text-gray-600">Avg/Month:</span>
						<span class="font-bold text-blue-600">₱{{ number_format($averageMonthly, 2) }}</span>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Main Content Card -->
	<div class="expenses-card mb-6">
		<div class="expenses-card-header flex justify-between items-center">
			<h2 class="text-gray-900 font-semibold flex items-center gap-2 text-sm">
				<i class="fas fa-list"></i> All Expenses
			</h2>
			<a href="{{ route('admin.expenses.create') }}" class="btn-add">
				<i class="fas fa-plus"></i> Add Expense
			</a>
		</div>

		<div class="expenses-card-body">
			@if(session('success'))
				<div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm flex items-center gap-2">
					<i class="fas fa-check-circle"></i>
					{{ session('success') }}
				</div>
			@endif

			@if($expenses->count() > 0)
				<div class="overflow-x-auto">
					<table class="expenses-table">
						<thead>
							<tr>
								<th>Date</th>
								<th>Category</th>
								<th>Description</th>
								<th class="text-right">Amount</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							@foreach($expenses as $expense)
								<tr>
									<td class="font-medium">{{ $expense->expense_date->format('M d, Y') }}</td>
									<td>
										<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
											{{ $expense->category }}
										</span>
									</td>
									<td class="max-w-xs truncate" title="{{ $expense->description }}">
										{{ $expense->description ?: 'No description' }}
									</td>
									<td class="text-right amount-negative">
										-₱{{ number_format($expense->amount, 2) }}
									</td>
									<td>
										<div class="action-buttons">
											<a href="{{ route('admin.expenses.edit', $expense) }}" class="btn-edit">
												<i class="fas fa-edit mr-1"></i> Edit
											</a>
											<form action="{{ route('admin.expenses.destroy', $expense) }}" method="POST" class="inline">
												@csrf @method('DELETE')
												<button type="button" onclick="confirmDelete(this)" class="btn-delete">
													<i class="fas fa-trash mr-1"></i> Delete
												</button>
											</form>
										</div>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>

				<!-- Pagination -->
				<div class="mt-6 flex items-center justify-between">
					<div class="text-sm text-gray-600">
						Showing {{ $expenses->firstItem() }} to {{ $expenses->lastItem() }} of {{ $expenses->total() }} results
					</div>
					<div class="flex gap-1">
						{{ $expenses->links() }}
					</div>
				</div>
			@else
				<div class="empty-state">
					<i class="fas fa-receipt"></i>
					<h3 class="text-lg font-medium text-gray-900 mb-2">No expenses found</h3>
					<p class="text-gray-500 mb-4">Get started by creating your first expense record.</p>
					<a href="{{ route('admin.expenses.create') }}" class="btn-add inline-flex">
						<i class="fas fa-plus"></i> Add Your First Expense
					</a>
				</div>
			@endif
		</div>
	</div>
</div>

<!-- Delete Confirmation Modal -->
<div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden" id="deleteModal">
	<div class="bg-white rounded-xl shadow-lg max-w-md w-full mx-4">
		<div class="p-6">
			<div class="flex items-center gap-3 mb-4">
				<div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
					<i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
				</div>
				<div>
					<h3 class="text-lg font-semibold text-gray-900">Delete Expense</h3>
					<p class="text-gray-600 text-sm">This action cannot be undone.</p>
				</div>
			</div>
			<div class="flex justify-end gap-3 mt-6">
				<button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
					Cancel
				</button>
				<button type="button" id="confirmDeleteBtn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
					Delete Expense
				</button>
			</div>
		</div>
	</div>
</div>
@endsection

@section('content_js')
<script>
	let deleteForm = null;

	function confirmDelete(button) {
		deleteForm = button.closest('form');
		document.getElementById('deleteModal').classList.remove('hidden');
	}

	function closeDeleteModal() {
		document.getElementById('deleteModal').classList.add('hidden');
		deleteForm = null;
	}

	document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
		if (deleteForm) {
			deleteForm.submit();
		}
	});

	// Close modal when clicking outside
	document.getElementById('deleteModal').addEventListener('click', function(e) {
		if (e.target === this) {
			closeDeleteModal();
		}
	});
</script>
@endsection