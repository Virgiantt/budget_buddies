<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Illuminate\Http\Request;

class BudgetController extends Controller
{

    public function index()
    {
        return response()->json(Budget::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'B_id' => 'required|string|size:6|unique:Budgets,B_id',
            'B_amount_limit' => 'required|numeric|min:0',
            'B_month' => 'required|integer|between:1,12',
            'B_year' => 'required|integer|min:2000',
            'Categories_C_id' => 'required|string|size:6|exists:Categories,C_id',
        ]);

        $budget = Budget::create($data);

        return response()->json($budget, 201);
    }

    public function show(string $id)
    {
        return response()->json(Budget::findOrFail($id));
    }

    public function update(Request $request, string $id)
    {
        $budget = Budget::findOrFail($id);

        $data = $request->validate([
            'B_amount_limit' => 'sometimes|required|numeric|min:0',
            'B_month' => 'sometimes|required|integer|between:1,12',
            'B_year' => 'sometimes|required|integer|min:2000',
            'Categories_C_id' => 'sometimes|required|string|size:6|exists:Categories,C_id',
        ]);

        $budget->update($data);

        return response()->json($budget);
    }

    // Menghapus budget
    public function destroy(string $id)
    {
        $budget = Budget::findOrFail($id);
        $budget->delete();

        return response()->json([
            'message' => 'Budget berhasil dihapus'
        ]);
    }
}
