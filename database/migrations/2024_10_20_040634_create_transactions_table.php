<?php

use App\Models\BudgetSource;
use App\Models\TransactionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(BudgetSource::class);
            $table->foreignIdFor(BudgetSource::class, 'budget_source_to_id')->nullable();
            $table->foreignIdFor(TransactionType::class);
            $table->integer('nominal');
            $table->string('description');
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
