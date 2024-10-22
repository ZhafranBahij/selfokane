<?php

namespace Database\Seeders;

use App\Models\BudgetSource;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\TransactionType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Sequence;


class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Transaction::factory()
        ->count(50)
        ->create();
    }
}
