<?php

namespace App\Http\Controllers;

use App\Models\Account;

class AccountPageController extends Controller
{
public function create()
{
    return view('accounts.create');
}

public function store(\Illuminate\Http\Request $request)
{
    $data = $request->validate([
        'A_name' => 'required|string|max:255',
        'A_account_type' => 'required|string|max:100',
        'A_initial_balance' => 'required|numeric|min:0',
        'Users_U_id' => 'required|exists:users,U_id',
    ]);

    \App\Models\Account::create($data);

    return redirect()->route('accounts.index')
        ->with('success', 'Akun berhasil ditambahkan.');
}

public function edit(string $id)
{
    $account = \App\Models\Account::findOrFail($id);

    return view('accounts.edit', compact('account'));
}

public function update(\Illuminate\Http\Request $request, string $id)
{
    $account = \App\Models\Account::findOrFail($id);

    $data = $request->validate([
        'A_name' => 'required|string|max:255',
        'A_account_type' => 'required|string|max:100',
        'A_initial_balance' => 'required|numeric|min:0',
    ]);

    $account->update($data);

    return redirect()->route('accounts.index')
        ->with('success', 'Akun berhasil diperbarui.');
}

public function destroy(string $id)
{
    \App\Models\Account::findOrFail($id)->delete();

    return redirect()->route('accounts.index')
        ->with('success', 'Akun berhasil dihapus.');
}
}