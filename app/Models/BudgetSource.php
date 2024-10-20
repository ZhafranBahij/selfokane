<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetSource extends Model
{
    public function transaction()
    {
        return $this->hasMany(BudgetSource::class);
    }
}
