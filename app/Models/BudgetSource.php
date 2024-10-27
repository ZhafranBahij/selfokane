<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BudgetSource extends Model
{

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function totalTransaction()
    {
        return $this->hasMany(Transaction::class)->sum('nominal');
    }

}
