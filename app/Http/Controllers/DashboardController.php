<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // ID pengguna yang sedang login
        $userId = auth()->id();

        // Ambil hanya transaksi dari akun milik pengguna tersebut
        $userTransactions = function () use ($userId) {
            return Transaction::whereHas('account', function ($query) use ($userId) {
                $query->where('Users_U_id', $userId);
            });
        };

        // Total pemasukan pengguna
        $totalIncome = $userTransactions()
            ->where('T_type', 'income')
            ->sum('T_amount');

        // Total pengeluaran pengguna
        $totalExpense = $userTransactions()
            ->where('T_type', 'expense')
            ->sum('T_amount');

        // Total saldo awal akun milik pengguna
        $initialBalance = Account::where('Users_U_id', $userId)
            ->sum('A_initial_balance');

        // Saldo akhir pengguna
        $balance = $initialBalance + $totalIncome - $totalExpense;

        // Lima transaksi terbaru milik pengguna
        $recentTransactions = $userTransactions()
            ->with('category')
            ->orderByDesc('T_transaction_date')
            ->limit(5)
            ->get();

        // Data grafik tujuh hari terakhir
        $startDate = Carbon::today()->subDays(6);
        $endDate = Carbon::today();

        $transactions = $userTransactions()
            ->whereBetween('T_transaction_date', [
                $startDate->toDateString(),
                $endDate->toDateString(),
            ])
            ->get();

        $labels = [];
        $income = [];
        $expense = [];

        for (
            $date = $startDate->copy();
            $date->lte($endDate);
            $date->addDay()
        ) {
            $day = $date->toDateString();

            $labels[] = $date->translatedFormat('d M');

            $income[] = $transactions
                ->where('T_type', 'income')
                ->filter(function ($transaction) use ($day) {
                    return Carbon::parse(
                        $transaction->T_transaction_date
                    )->toDateString() === $day;
                })
                ->sum('T_amount');

            $expense[] = $transactions
                ->where('T_type', 'expense')
                ->filter(function ($transaction) use ($day) {
                    return Carbon::parse(
                        $transaction->T_transaction_date
                    )->toDateString() === $day;
                })
                ->sum('T_amount');
        }

        $weeklyChart = compact('labels', 'income', 'expense');

        return view('dashboard', compact(
            'totalIncome',
            'totalExpense',
            'initialBalance',
            'balance',
            'recentTransactions',
            'weeklyChart'
        ));
    }
}