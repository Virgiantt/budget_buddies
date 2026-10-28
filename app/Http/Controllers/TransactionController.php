<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{

public function index(\Illuminate\Http\Request $request)
{
    $query = Transaction::query();

    if ($request->filled('start_date')) {
        $query->whereDate(
            'T_transaction_date',
            '>=',
            $request->start_date
        );
    }

    if ($request->filled('end_date')) {
        $query->whereDate(
            'T_transaction_date',
            '<=',
            $request->end_date
        );
    }

    return response()->json(
        $query->orderByDesc('T_transaction_date')->get()
    );
}

    // Menambahkan transaksi
    public function store(Request $request)
    {
        $data = $request->validate([
            'T_ID' => 'required|string|size:6|unique:Transactions,T_ID',
            'T_title' => 'required|string|max:100',
            'T_type' => 'required|string|max:50',
            'T_amount' => 'required|numeric|min:0',
            'T_transaction_date' => 'required|date',
            'Accounts_A_id' => 'required|string|size:6|exists:Accounts,A_id',
            'Categories_C_id' => 'required|string|size:6|exists:Categories,C_id',
        ]);

        $transaction = Transaction::create($data);

        return response()->json($transaction, 201);
    }

    // Menampilkan satu transaksi
    public function show(string $id)
    {
        return response()->json(Transaction::findOrFail($id));
    }

    // Mengubah transaksi
    public function update(Request $request, string $id)
    {
        $transaction = Transaction::findOrFail($id);

        $data = $request->validate([
            'T_title' => 'sometimes|required|string|max:100',
            'T_type' => 'sometimes|required|string|max:50',
            'T_amount' => 'sometimes|required|numeric|min:0',
            'T_transaction_date' => 'sometimes|required|date',
            'Accounts_A_id' => 'sometimes|required|string|size:6|exists:Accounts,A_id',
            'Categories_C_id' => 'sometimes|required|string|size:6|exists:Categories,C_id',
        ]);

        $transaction->update($data);

        return response()->json($transaction);
    }

    // Menghapus transaksi
    public function destroy(string $id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();

        return response()->json([
            'message' => 'Transaction berhasil dihapus'
        ]);
    }
}
