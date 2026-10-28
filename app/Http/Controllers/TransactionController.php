<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    // Menampilkan riwayat transaksi milik pengguna yang login.
    public function index(Request $request)
    {
        $query = Transaction::with(['category', 'account'])
            ->whereHas('account', function ($accountQuery) {
                $accountQuery->where('Users_U_id', auth()->id());
            });

        if ($request->filled('from')) {
            $query->whereDate(
                'T_transaction_date',
                '>=',
                $request->from
            );
        }

        if ($request->filled('to')) {
            $query->whereDate(
                'T_transaction_date',
                '<=',
                $request->to
            );
        }

        if ($request->filled('type')) {
            $query->where('T_type', $request->type);
        }

        $transactions = $query
            ->orderByDesc('T_transaction_date')
            ->get();

        return view(
            'transactions.index',
            compact('transactions')
        );
    }

    // Menampilkan formulir tambah transaksi.
    public function create()
    {
        $categories = Category::where(
            'Users_U_id',
            auth()->id()
        )
            ->orderBy('C_name')
            ->get();

        $accounts = Account::where(
            'Users_U_id',
            auth()->id()
        )
            ->orderBy('A_name')
            ->get();

        return view(
            'transactions.create',
            compact('categories', 'accounts')
        );
    }

    // Menyimpan transaksi baru.
    public function store(Request $request)
    {
        $data = $request->validate([
            'T_title' => 'required|string|max:100',
            'T_type' => 'required|in:income,expense',
            'T_amount' => 'required|numeric|min:1',
            'T_transaction_date' => 'required|date',
            'Accounts_A_id' => 'required|string|exists:Accounts,A_id',
            'Categories_C_id' => 'required|string|exists:Categories,C_id',
        ]);

        $account = Account::where(
            'A_id',
            $data['Accounts_A_id']
        )
            ->where('Users_U_id', auth()->id())
            ->first();

        if (!$account) {
            abort(403, 'Kamu tidak memiliki akses ke akun tersebut.');
        }

        $category = Category::where(
            'C_id',
            $data['Categories_C_id']
        )
            ->where('Users_U_id', auth()->id())
            ->first();

        if (!$category) {
            abort(403, 'Kamu tidak memiliki akses ke kategori tersebut.');
        }

        if ($category->C_type !== $data['T_type']) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Jenis kategori harus sesuai dengan jenis transaksi.'
                );
        }

        $data['T_ID'] = $this->generateTransactionId();

        Transaction::create($data);

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaksi berhasil ditambahkan.');
    }

    // Menampilkan detail transaksi milik pengguna.
    public function show(string $id)
    {
        $transaction = Transaction::whereHas(
            'account',
            function ($query) {
                $query->where('Users_U_id', auth()->id());
            }
        )
            ->with(['category', 'account'])
            ->findOrFail($id);

        return response()->json($transaction);
    }

    // Menampilkan formulir edit transaksi milik pengguna.
    public function edit(string $id)
    {
        $transaction = Transaction::whereHas(
            'account',
            function ($query) {
                $query->where('Users_U_id', auth()->id());
            }
        )->findOrFail($id);

        $categories = Category::where(
            'Users_U_id',
            auth()->id()
        )
            ->orderBy('C_name')
            ->get();

        $accounts = Account::where(
            'Users_U_id',
            auth()->id()
        )
            ->orderBy('A_name')
            ->get();

        return view(
            'transactions.edit',
            compact('transaction', 'categories', 'accounts')
        );
    }

    // Memperbarui transaksi milik pengguna.
    public function update(Request $request, string $id)
    {
        $transaction = Transaction::whereHas(
            'account',
            function ($query) {
                $query->where('Users_U_id', auth()->id());
            }
        )->findOrFail($id);

        $data = $request->validate([
            'T_title' => 'required|string|max:100',
            'T_type' => 'required|in:income,expense',
            'T_amount' => 'required|numeric|min:1',
            'T_transaction_date' => 'required|date',
            'Accounts_A_id' => 'required|string|exists:Accounts,A_id',
            'Categories_C_id' => 'required|string|exists:Categories,C_id',
        ]);

        $account = Account::where(
            'A_id',
            $data['Accounts_A_id']
        )
            ->where('Users_U_id', auth()->id())
            ->first();

        if (!$account) {
            abort(403, 'Kamu tidak memiliki akses ke akun tersebut.');
        }

        $category = Category::where(
            'C_id',
            $data['Categories_C_id']
        )
            ->where('Users_U_id', auth()->id())
            ->first();

        if (!$category) {
            abort(403, 'Kamu tidak memiliki akses ke kategori tersebut.');
        }

        if ($category->C_type !== $data['T_type']) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Jenis kategori harus sesuai dengan jenis transaksi.'
                );
        }

        $transaction->update($data);

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    // Menghapus transaksi milik pengguna.
    public function destroy(string $id)
    {
        $transaction = Transaction::whereHas(
            'account',
            function ($query) {
                $query->where('Users_U_id', auth()->id());
            }
        )->findOrFail($id);

        $transaction->delete();

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaksi berhasil dihapus.');
    }

    // Membuat ID transaksi enam karakter yang unik.
    private function generateTransactionId(): string
    {
        do {
            $id = strtoupper(Str::random(6));
        } while (Transaction::where('T_ID', $id)->exists());

        return $id;
    }
}