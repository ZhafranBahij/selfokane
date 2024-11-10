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
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Range;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\SelectFilter;

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
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                        if ($get('transaction_type_id') == 2) {
                            $set('nominal', $get('nominal') * -1) ;
                            return;
                        }
                        if ($get('transaction_type_id') == 1) {
                            $set('nominal', abs($get('nominal'))) ;
                        }
                    })
                    ->live(onBlur: true)
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
                TextInput::make('nominal')
                    ->numeric()
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                        if ($get('transaction_type_id') == 2) {
                            $set('nominal', abs($get('nominal')) * -1) ;
                            return;
                        }
                        if ($get('transaction_type_id') == 1) {
                            $set('nominal', abs($get('nominal'))) ;
                        }
                    })
                    ->live(onBlur: true)
                    ->required(),
                TextInput::make('description')
                    ->string()
                    ->required(),
                DatePicker::make('date')
                                ->required()
                                ->maxDate(now()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('budget_source.name')
                ->searchable(),
                TextColumn::make('transaction_type.name')
                ->color(fn (string $state): string => match ($state) {
                    'mutation' => 'warning',
                    'income' => 'success',
                    'expense' => 'danger',
                })
                ->searchable()
                ->description(fn (Transaction $record): string => $record->description)
                ->limit(50),
                TextColumn::make('nominal')
                ->summarize([
                    Average::make(),
                    Sum::make(),
                ])
                ->money('IDR', divideBy: 0)
                ->sortable()
                ->description(fn (Transaction $record): string => $record->category->name ?? '-'),
                TextColumn::make('date')
                ->searchable()
                ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Filter::make('expense')
                    ->query(fn (Builder $query): Builder => $query->whereRelation('transaction_type', 'name', 'expense')),
                Filter::make('income')
                    ->query(fn (Builder $query): Builder => $query->whereRelation('transaction_type', 'name', 'income')),
                Filter::make('this_month')
                    ->query(fn (Builder $query): Builder => $query->whereMonth('date', now())),
                Filter::make('this_year')
                    ->query(fn (Builder $query): Builder => $query->whereYear('date', now())),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('start_date'),
                        DatePicker::make('end_date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['end_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
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
