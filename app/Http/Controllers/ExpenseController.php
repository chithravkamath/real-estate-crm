<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    /**
     * Redirect listing to main billing dashboard where expenses are embedded.
     */
    public function index()
    {
        return redirect('/billing');
    }

    /**
     * Show form to add a new expense.
     */
    public function create()
    {
        return view('add-expense');
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'expense_title' => 'required|string|max:255',
            'expense_category' => 'required|string|in:Marketing,Travel,Maintenance,Office,Miscellaneous',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string',
        ], [
            'expense_date.before_or_equal' => 'The expense date cannot be in the future.',
        ]);

        $data['created_by'] = Auth::user()->name;

        $expense = Expense::create($data);

        AuditLog::create([
            'user_name' => Auth::user()->name,
            'action' => 'Create',
            'module' => 'Expenses',
            'description' => "Added expense: {$expense->expense_title} (₹{$expense->amount})"
        ]);

        return redirect('/billing')->with('success', 'Expense recorded successfully!');
    }

    /**
     * Show form to edit the specified expense.
     */
    public function edit($id)
    {
        $expense = Expense::findOrFail($id);
        return view('edit-expense', compact('expense'));
    }

    /**
     * Update the specified expense in storage.
     */
    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);

        $data = $request->validate([
            'expense_title' => 'required|string|max:255',
            'expense_category' => 'required|string|in:Marketing,Travel,Maintenance,Office,Miscellaneous',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string',
        ], [
            'expense_date.before_or_equal' => 'The expense date cannot be in the future.',
        ]);

        $expense->update($data);

        AuditLog::create([
            'user_name' => Auth::user()->name,
            'action' => 'Update',
            'module' => 'Expenses',
            'description' => "Updated expense: {$expense->expense_title}"
        ]);

        return redirect('/billing')->with('success', 'Expense record updated successfully!');
    }

    /**
     * Remove the specified expense from storage.
     */
    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        $title = $expense->expense_title;

        $expense->delete();

        AuditLog::create([
            'user_name' => Auth::user()->name,
            'action' => 'Delete',
            'module' => 'Expenses',
            'description' => "Deleted expense: {$title}"
        ]);

        return redirect('/billing')->with('success', 'Expense record deleted successfully!');
    }
}
