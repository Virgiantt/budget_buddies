<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Total pemasukan dan pengeluaran
        $totalIncome = Transaction::where('T_type', 'income')
            ->sum('T_amount');

        $totalExpense = Transaction::where('T_type', 'expense')
            ->sum('T_amount');

        // Total saldo awal seluruh akun
        $initialBalance = Account::sum('A_initial_balance');

        // Saldo akhir
        $balance = $initialBalance + $totalIncome - $totalExpense;

        // Transaksi terbaru beserta kategori
        $recentTransactions = Transaction::with('category')
            ->orderByDesc('T_transaction_date')
            ->limit(5)
            ->get();

        // Data grafik 7 hari terakhir
        $startDate = Carbon::today()->subDays(6);
        $endDate = Carbon::today();

        $transactions = Transaction::whereBetween(
            'T_transaction_date',
            [$startDate, $endDate]
        )->get();

        $labels = [];
        $income = [];
        $expense = [];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $day = $date->toDateString();

            $labels[] = $date->translatedFormat('d M');

            $income[] = $transactions
                ->where('T_type', 'income')
                ->filter(fn ($t) => Carbon::parse($t->T_transaction_date)->toDateString() === $day)
                ->sum('T_amount');

            $expense[] = $transactions
                ->where('T_type', 'expense')
                ->filter(fn ($t) => Carbon::parse($t->T_transaction_date)->toDateString() === $day)
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
