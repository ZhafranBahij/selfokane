<?php

namespace App\Filament\Widgets;

use App\Models\BudgetSource;
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

        $group_by_budget = Transaction::query()
                        ->with(['budget_source'])
                        ->selectRaw('SUM(nominal) as total_nominal, budget_source_id')
                        ->groupBy('budget_source_id')
                        ->get();

        $group_by_category = Transaction::query()
                        ->with(['category'])
                        ->selectRaw('SUM(nominal) as total_nominal, category_id')
                        ->groupBy('category_id')
                        ->get();

        $total = $income + $expense;

        $new_stat = [];

        foreach ($group_by_category as $key => $value) {
            $new_stat[] = Stat::make($value->category->name, 'Rp. '.number_format($value->total_nominal) ?? 0);
        }

        foreach ($group_by_budget as $key => $value) {
            $new_stat[] = Stat::make($value->budget_source->name, 'Rp. '.number_format($value->total_nominal) ?? 0);
        }

        return [
            // Total
            Stat::make('Total', 'Rp. '.number_format($total) ?? 0),

            // Income
            Stat::make('Income', 'Rp. '.number_format($income) ?? 0),

            // Expense
            Stat::make('Expense', 'Rp. '.number_format($expense) ?? 0),

            // Another
            ...$new_stat,
        ];
    }
}
