<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    // protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Orders';

    protected static ?string $modelLabel = 'Order';

    protected static ?string $pluralModelLabel = 'Orders';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\DatePicker::make('install_date')
                    ->nullable(),
                Forms\Components\DatePicker::make('install_date_confirmed')
                    ->nullable(),
                Forms\Components\TextInput::make('price_to_customer')
                    ->numeric()
                    ->prefix('$')
                    ->nullable(),
                Forms\Components\TextInput::make('price_to_supplier')
                    ->numeric()
                    ->prefix('$')
                    ->nullable(),
                Forms\Components\TextInput::make('budget')
                    ->numeric()
                    ->prefix('$')
                    ->nullable(),
                Forms\Components\TextInput::make('montage_time')
                    ->numeric()
                    ->suffix('hours')
                    ->nullable(),
                Forms\Components\Toggle::make('is_requested')
                    ->default(false),
                Forms\Components\Toggle::make('is_confirmed')
                    ->default(false),
                Forms\Components\Toggle::make('is_delivered')
                    ->default(false),
                Forms\Components\Toggle::make('is_finished')
                    ->default(false),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Select::make('client_id')
                    ->relationship('client', 'name')
                    ->required(),
                Forms\Components\Select::make('place_id')
                    ->relationship('place', 'name')
                    ->required(),
                Forms\Components\Select::make('project_id')
                    ->relationship('project', 'name')
                    ->nullable(),
                Forms\Components\Select::make('order_category_id')
                    ->relationship('orderCategory', 'humanlike_name')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('client.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('place.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('install_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_to_customer')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_to_supplier')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_requested')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_confirmed')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_delivered')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_finished')
                    ->boolean(),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
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
                Tables\Filters\TernaryFilter::make('is_requested'),
                Tables\Filters\TernaryFilter::make('is_confirmed'),
                Tables\Filters\TernaryFilter::make('is_delivered'),
                Tables\Filters\TernaryFilter::make('is_finished'),
                Tables\Filters\SelectFilter::make('client_id')
                    ->relationship('client', 'name'),
                Tables\Filters\SelectFilter::make('user_id')
                    ->relationship('user', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
