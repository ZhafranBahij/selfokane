<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public function budget_source()
    {
        return $this->belongsTo(BudgetSource::class);
    }

    public function transaction_type()
    {
        return $this->belongsTo(TransactionType::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}
