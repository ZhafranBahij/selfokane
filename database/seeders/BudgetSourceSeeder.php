<?php

namespace Database\Seeders;

use App\Models\BudgetSource;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BudgetSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $budget_source = [
            'flazz',
            'dana',
            'telkomsel',
            'bca',
            'wallet',
            'gopay',
        ];

        foreach ($budget_source as $key => $value) {
            BudgetSource::create([
                'name' => $value,
            ]);
        }


    }
}
