<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\BudgetSource;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\TransactionType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('budget_source_id')
                    ->label('Budget Source')
                    ->relationship('budget_source', 'name')
                    ->options(BudgetSource::all()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        TextInput::make('name')
                        ->required(),
                    ]),
                Select::make('transaction_type_id')
                    ->label('Transaction Type')
                    ->options(TransactionType::all()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->options(Category::all()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        TextInput::make('name')
                        ->required(),
                    ]),
                TextInput::make('nominal')->numeric()->required(),
                TextInput::make('description')->string()->required(),
                DatePicker::make('date')
                                ->required()
                                ->maxDate(now()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('budget_source.name'),
                TextColumn::make('transaction_type.name')
                ->color(fn (string $state): string => match ($state) {
                    'mutation' => 'warning',
                    'income' => 'success',
                    'expense' => 'danger',
                })
                ->description(fn (Transaction $record): string => $record->description)
                ->limit(50),
                TextColumn::make('nominal')
                ->money('IDR', divideBy: 0)
                ->sortable()
                ->description(fn (Transaction $record): string => $record->category->name ?? '-'),
                TextColumn::make('date')
                ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Filter::make('expense')
                    ->query(fn (Builder $query): Builder => $query->whereRelation('transaction_type', 'name', 'expense')),
                Filter::make('income')
                    ->query(fn (Builder $query): Builder => $query->whereRelation('transaction_type', 'name', 'income')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTransactions::route('/'),
        ];
    }
}
