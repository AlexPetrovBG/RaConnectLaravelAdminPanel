<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MontageResource\Pages;
use App\Models\Montage;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables;

class MontageResource extends Resource
{
    protected static ?string $model = Montage::class;

    // protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Montages';

    protected static ?string $modelLabel = 'Montage';

    protected static ?string $pluralModelLabel = 'Montages';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->required(),
                Forms\Components\TextInput::make('duration')
                    ->numeric()
                    ->suffix('hours')
                    ->nullable(),
                Forms\Components\Toggle::make('confirmed')
                    ->default(false),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Select::make('order_id')
                    ->relationship('order', 'number')
                    ->required(),
                Forms\Components\Select::make('man_day_id')
                    ->relationship('manDay', 'date')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order.number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration')
                    ->numeric()
                    ->suffix('h')
                    ->sortable(),
                Tables\Columns\IconColumn::make('confirmed')
                    ->boolean(),
                Tables\Columns\TextColumn::make('manDay.date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('confirmed'),
                Tables\Filters\SelectFilter::make('user_id')
                    ->relationship('user', 'name'),
                Tables\Filters\SelectFilter::make('order_id')
                    ->relationship('order', 'number'),
                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['from'],
                                fn ($query, $date) => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn ($query, $date) => $query->whereDate('date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('confirm')
                    ->icon('heroicon-o-check')
                    ->action(function (Montage $record) {
                        $record->update(['confirmed' => true]);
                    })
                    ->visible(fn (Montage $record) => !$record->confirmed),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('confirm')
                        ->icon('heroicon-o-check')
                        ->action(function ($records) {
                            $records->each->update(['confirmed' => true]);
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMontages::route('/'),
            'create' => Pages\CreateMontage::route('/create'),
            'view' => Pages\ViewMontage::route('/{record}'),
            'edit' => Pages\EditMontage::route('/{record}/edit'),
        ];
    }
}

