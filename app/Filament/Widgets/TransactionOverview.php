<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class TransactionOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $income = Transaction::query()
        ->whereRelation('transaction_type', [
            ['name', 'income']
        ])
        ->sum('nominal');

        $expense = Transaction::query()
        ->whereRelation('transaction_type', [
            ['name', 'expense']
        ])
        ->sum('nominal');

        // $grouping = Transaction::query()
        //             ->groupBy('budget_source_id')
        //             ->get();
        // $grouping = Transaction::query()
        //                 ->select(DB::raw('SUM(nominal) as nominal'))
        //                 ->groupBy('budget_source_id', 'transaction_type_id')
        //                 ->get();
        // dd($grouping);

        $total = $income - $expense;

        return [
            // Total
            Stat::make('Total', 'Rp. '.number_format($total) ?? 0),

            // Income
            Stat::make('Income', 'Rp. '.number_format($income) ?? 0),

            // Expense
            Stat::make('Expense', 'Rp. '.number_format($expense) ?? 0),
        ];
    }
}
