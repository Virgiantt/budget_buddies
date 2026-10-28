<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AccountPageController extends Controller
{
    public function index()
    {
        $accounts = Account::where(
            'Users_U_id',
            auth()->id()
        )->orderBy('A_name')->get();

        return view('accounts.index', compact('accounts'));
    }

    public function create()
    {
        return redirect()->route('accounts.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'A_name' => 'required|string|max:255',
            'A_account_type' => 'required|in:Tunai,Bank,E-wallet',
            'A_initial_balance' => 'required|numeric|min:0',
        ]);

        do {
            $id = strtoupper(Str::random(6));
        } while (Account::where('A_id', $id)->exists());

        $data['A_id'] = $id;
        $data['Users_U_id'] = auth()->id();

        Account::create($data);

        return redirect()
            ->route('accounts.index')
            ->with('success', 'Akun berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $account = Account::where('Users_U_id', auth()->id())
            ->findOrFail($id);

        return view('accounts.edit', compact('account'));
    }

    public function update(Request $request, string $id)
    {
        $account = Account::where('Users_U_id', auth()->id())
            ->findOrFail($id);

        $data = $request->validate([
            'A_name' => 'required|string|max:255',
            'A_account_type' => 'required|in:Tunai,Bank,E-wallet',
            'A_initial_balance' => 'required|numeric|min:0',
        ]);

        $account->update($data);

        return redirect()
            ->route('accounts.index')
            ->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $account = Account::where('Users_U_id', auth()->id())
            ->findOrFail($id);

        if ($account->transactions()->exists()) {
            return redirect()
                ->route('accounts.index')
                ->with('error', 'Akun tidak bisa dihapus karena masih memiliki transaksi.');
        }

        $account->delete();

        return redirect()
            ->route('accounts.index')
            ->with('success', 'Akun berhasil dihapus.');
    }
}