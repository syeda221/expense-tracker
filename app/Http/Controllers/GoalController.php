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

        return redirect()->back()->with('success', 'Funds added to goal successfully.');
    }
}
