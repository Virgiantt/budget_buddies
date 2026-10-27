<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        return response()->json(Account::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'A_id' => 'required|string|size:6|unique:Accounts,A_id',
            'A_name' => 'required|string|max:100',
            'A_account_type' => 'required|string|max:50',
            'A_initial_balance' => 'required|numeric|min:0',
            'Users_U_id' => 'required|string|size:6|exists:Users,U_id',
        ]);

        $account = Account::create($data);

        return response()->json($account, 201);
    }

    public function show(string $id)
    {
        $account = Account::findOrFail($id);

        return response()->json($account);
    }

    public function update(Request $request, string $id)
    {
        $account = Account::findOrFail($id);

        $data = $request->validate([
            'A_name' => 'sometimes|required|string|max:100',
            'A_account_type' => 'sometimes|required|string|max:50',
            'A_initial_balance' => 'sometimes|required|numeric|min:0',
            'Users_U_id' => 'sometimes|required|string|size:6|exists:Users,U_id',
        ]);

        $account->update($data);

        return response()->json($account);
    }

    public function destroy(string $id)
    {
        $account = Account::findOrFail($id);
        $account->delete();

        return response()->json([
            'message' => 'Account berhasil dihapus'
        ]);
    }
}
