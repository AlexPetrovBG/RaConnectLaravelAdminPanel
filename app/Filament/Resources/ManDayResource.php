<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ManDayResource\Pages;
use App\Models\ManDay;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables;

class ManDayResource extends Resource
{
    protected static ?string $model = ManDay::class;

    // protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Man Days';

    protected static ?string $modelLabel = 'Man Day';

    protected static ?string $pluralModelLabel = 'Man Days';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->required(),
                Forms\Components\Toggle::make('is_vacation')
                    ->default(false),
                Forms\Components\Toggle::make('is_medical')
                    ->default(false),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->prefix('€')
                    ->nullable(),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
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
                Tables\Columns\IconColumn::make('is_vacation')
                    ->boolean()
                    ->label('Vacation'),
                Tables\Columns\IconColumn::make('is_medical')
                    ->boolean()
                    ->label('Medical'),
                Tables\Columns\TextColumn::make('price')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->tooltip(fn (string $state): string => $state)
                    ->searchable(),
                Tables\Columns\TextColumn::make('montages_count')
                    ->counts('montages')
                    ->label('Montages')
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
                Tables\Filters\TernaryFilter::make('is_vacation'),
                Tables\Filters\TernaryFilter::make('is_medical'),
                Tables\Filters\SelectFilter::make('user_id')
                    ->relationship('user', 'name'),
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
                Tables\Filters\Filter::make('has_price')
                    ->query(fn ($query) => $query->where('price', '>', 0)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('mark_vacation')
                        ->icon('heroicon-o-sun')
                        ->action(function ($records) {
                            $records->each->update(['is_vacation' => true]);
                        }),
                    Tables\Actions\BulkAction::make('mark_medical')
                        ->icon('heroicon-o-heart')
                        ->action(function ($records) {
                            $records->each->update(['is_medical' => true]);
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
            'index' => Pages\ListManDays::route('/'),
            'create' => Pages\CreateManDay::route('/create'),
            'view' => Pages\ViewManDay::route('/{record}'),
            'edit' => Pages\EditManDay::route('/{record}/edit'),
        ];
    }
}

