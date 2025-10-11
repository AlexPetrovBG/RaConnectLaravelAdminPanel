<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentResource\Pages;
use App\Models\Document;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables;
use App\Filament\Forms\Components\TenantFileUpload;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    // protected static ?string $navigationIcon = 'heroicon-o-document';

    protected static ?string $navigationLabel = 'Documents';

    protected static ?string $modelLabel = 'Document';

    protected static ?string $pluralModelLabel = 'Documents';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TenantFileUpload::make('file_name')
                    ->label('File')
                    ->required()
                    ->acceptedFileTypes(['application/pdf', 'image/*', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                    ->maxSize(10240), // 10MB
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->prefix('$')
                    ->nullable(),
                Forms\Components\DatePicker::make('date')
                    ->required(),
                Forms\Components\Toggle::make('is_paid')
                    ->default(false),
                Forms\Components\TextInput::make('type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('contragent_type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('contragent_id')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('order_id')
                    ->relationship('order', 'number')
                    ->nullable(),
                Forms\Components\Select::make('document_category_id')
                    ->relationship('documentCategory', 'humanlike_name')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('file_name')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function (string $state): string {
                        return basename($state);
                    }),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_paid')
                    ->boolean(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contragent_type')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order.number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('documentCategory.humanlike_name')
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
                Tables\Filters\TernaryFilter::make('is_paid'),
                Tables\Filters\SelectFilter::make('type'),
                Tables\Filters\SelectFilter::make('contragent_type'),
                Tables\Filters\SelectFilter::make('order_id')
                    ->relationship('order', 'number'),
                Tables\Filters\SelectFilter::make('document_category_id')
                    ->relationship('documentCategory', 'humanlike_name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Document $record) {
                        if ($record->fileExists()) {
                            return response()->download($record->getFilePath());
                        }
                        return null;
                    })
                    ->visible(fn (Document $record) => $record->fileExists()),
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
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'view' => Pages\ViewDocument::route('/{record}'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
        ];
    }
}
