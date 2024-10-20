<?php

namespace App\Filament\Resources\BudgetSourceResource\Pages;

use App\Filament\Resources\BudgetSourceResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageBudgetSources extends ManageRecords
{
    protected static string $resource = BudgetSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
