<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::where('user_id', auth()->id())
            ->orderBy('date', 'desc')
            ->get();

        return view('dashboard', compact('expenses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required|string',
            'date' => 'required|date',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        Expense::create([
            'user_id' => auth()->id(),
            'name'=>$request->name,
            'date' => $request->date,
            'category' => $request->category,
            'amount' => $request->amount,
        ]);

        return redirect()->route('dashboard')->with('success', 'Expense added successfully.');
    }

    public function update(Request $request, $id)
    {
        $expense = Expense::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        $request->validate([
            'name' => 'required|string',
            'date' => 'required|date',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        $expense->update([
            'name' => $request->name,
            'date' => $request->date,
            'category' => $request->category,
            'amount' => $request->amount,
        ]);

        return redirect()->route('dashboard')->with('success', 'Expense updated successfully.');
    }

    public function destroy($id)
    {
        $expense = Expense::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $expense->delete();

        return redirect()->route('dashboard')->with('success', 'Expense deleted.');
    }
}
