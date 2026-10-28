<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        // Kategori milik pengguna dan kategori lama yang masih bersama.
        $categories = Category::where(function ($query) {
            $query->where('Users_U_id', auth()->id())
                ->orWhereNull('Users_U_id');
        })
            ->where('C_type', 'expense')
            ->orderBy('C_name')
            ->get();

        // Hanya tampilkan anggaran milik pengguna yang sedang login.
        $budgets = Budget::with('category')
            ->where('Users_U_id', auth()->id())
            ->where('B_month', $month)
            ->where('B_year', $year)
            ->get();

        foreach ($budgets as $budget) {
            $budget->spent = Transaction::where(
                'Categories_C_id',
                $budget->Categories_C_id
            )
                ->where('T_type', 'expense')
                ->whereYear('T_transaction_date', $year)
                ->whereMonth('T_transaction_date', $month)
                ->whereHas('account', function ($query) {
                    $query->where('Users_U_id', auth()->id());
                })
                ->sum('T_amount');
        }

        return view('budgets.index', compact(
            'budgets',
            'categories',
            'month',
            'year'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'B_amount_limit' => 'required|numeric|min:1',
            'B_month' => 'required|integer|between:1,12',
            'B_year' => 'required|integer|min:2000',
            'Categories_C_id' => [
                'required',
                'string',
                'size:6',
                'exists:Categories,C_id',
            ],
        ]);

        // Kategori harus milik pengguna atau merupakan kategori lama bersama.
        $category = Category::where(function ($query) {
            $query->where('Users_U_id', auth()->id())
                ->orWhereNull('Users_U_id');
        })->findOrFail($data['Categories_C_id']);

        if ($category->C_type !== 'expense') {
            return back()
                ->withInput()
                ->with('error', 'Anggaran hanya bisa dibuat untuk kategori pengeluaran.');
        }

        // Cek duplikasi hanya pada anggaran pengguna yang sedang login.
        $existingBudget = Budget::where('Users_U_id', auth()->id())
            ->where('Categories_C_id', $data['Categories_C_id'])
            ->where('B_month', $data['B_month'])
            ->where('B_year', $data['B_year'])
            ->exists();

        if ($existingBudget) {
            return back()
                ->withInput()
                ->with('error', 'Anggaran untuk kategori dan periode tersebut sudah ada.');
        }

        do {
            $id = strtoupper(Str::random(6));
        } while (Budget::where('B_id', $id)->exists());

        $data['B_id'] = $id;
        $data['Users_U_id'] = auth()->id();

        Budget::create($data);

        return redirect()
            ->route('budgets.index', [
                'month' => $data['B_month'],
                'year' => $data['B_year'],
            ])
            ->with('success', 'Anggaran berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        $budget = Budget::where('Users_U_id', auth()->id())
            ->with('category')
            ->findOrFail($id);

        return response()->json($budget);
    }

    public function update(Request $request, string $id)
    {
        $budget = Budget::where('Users_U_id', auth()->id())
            ->findOrFail($id);

        $data = $request->validate([
            'B_amount_limit' => 'sometimes|required|numeric|min:1',
            'B_month' => 'sometimes|required|integer|between:1,12',
            'B_year' => 'sometimes|required|integer|min:2000',
            'Categories_C_id' => [
                'sometimes',
                'required',
                'string',
                'size:6',
                'exists:Categories,C_id',
            ],
        ]);

        if (isset($data['Categories_C_id'])) {
            Category::where(function ($query) {
                $query->where('Users_U_id', auth()->id())
                    ->orWhereNull('Users_U_id');
            })->findOrFail($data['Categories_C_id']);
        }

        $budget->update($data);

        return response()->json($budget);
    }

    public function destroy(string $id)
    {
        // Pengguna hanya bisa menghapus anggarannya sendiri.
        $budget = Budget::where('Users_U_id', auth()->id())
            ->findOrFail($id);

        $month = $budget->B_month;
        $year = $budget->B_year;

        $budget->delete();

        return redirect()
            ->route('budgets.index', [
                'month' => $month,
                'year' => $year,
            ])
            ->with('success', 'Anggaran berhasil dihapus.');
    }

    public function usage(string $id)
    {
        $budget = Budget::where('Users_U_id', auth()->id())
            ->with('category')
            ->findOrFail($id);

        $spent = Transaction::where(
            'Categories_C_id',
            $budget->Categories_C_id
        )
            ->where('T_type', 'expense')
            ->whereYear('T_transaction_date', $budget->B_year)
            ->whereMonth('T_transaction_date', $budget->B_month)
            ->whereHas('account', function ($query) {
                $query->where('Users_U_id', auth()->id());
            })
            ->sum('T_amount');

        $limit = (float) $budget->B_amount_limit;

        return response()->json([
            'budget' => $budget,
            'amount_limit' => $limit,
            'total_spent' => $spent,
            'remaining' => $limit - $spent,
            'percentage_used' => $limit > 0
                ? round(($spent / $limit) * 100, 2)
                : 0,
            'is_over_budget' => $spent > $limit,
        ]);
    }
}