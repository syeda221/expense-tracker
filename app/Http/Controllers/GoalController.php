<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
    /**
     * Add funds to a specific goal.
     */
    public function addFunds(Request $request, Goal $goal)
    {
        if ($goal->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $goal->saved_amount += $validated['amount'];
        $goal->save();

        \App\Models\Expense::create([
            'user_id' => Auth::id(),
            'amount' => $validated['amount'],
            'description' => 'Contribution to ' . $goal->name,
            'category' => 'Savings',
            'merchant' => 'Goal Transfer',
            'payment_method' => 'Transfer',
            'expense_date' => now(),
            'ai_confidence' => 1.0,
            'is_recurring' => false,
        ]);

        return redirect()->back()->with('success', 'Funds added to goal successfully.');
    }

    /**
     * Delete a specific goal.
     */
    public function destroy(Goal $goal)
    {
        if ($goal->user_id !== Auth::id()) {
            abort(403);
        }

        $goal->delete();

        return redirect()->back()->with('success', 'Goal deleted successfully.');
    }
}
