<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpensesController extends Controller
{
    public function index()
    {
        $expenses = Expense::latest()->paginate(10);
        
        // Calculate totals
        $totalExpenses = Expense::sum('amount');
        $averageMonthly = Expense::where('expense_date', '>=', now()->subMonth())
            ->avg('amount') ?? 0;

        return view('admin.expenses.index', compact(
            'expenses', 
            'totalExpenses', 
            'averageMonthly'
        ));
    }

    public function create()
    {
        return view('admin.expenses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'description' => 'nullable|string'
        ]);

        Expense::create($validated);

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Expense added successfully.');
    }

    public function edit(Expense $expense)
    {
        return view('admin.expenses.edit', compact('expense'));
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'description' => 'nullable|string'
        ]);

        $expense->update($validated);

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }
}