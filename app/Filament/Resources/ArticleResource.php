<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    // protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'Articles';

    protected static ?string $modelLabel = 'Article';

    protected static ?string $pluralModelLabel = 'Articles';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('project_id')
                    ->relationship('project', 'name')
                    ->required(),
                Forms\Components\Select::make('component_id')
                    ->relationship('component', 'name')
                    ->nullable(),
                Forms\Components\TextInput::make('company_guid')
                    ->maxLength(255),
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('code_no_color')
                    ->maxLength(255),
                Forms\Components\TextInput::make('component_code')
                    ->maxLength(255),
                Forms\Components\TextInput::make('category_designation')
                    ->maxLength(255),
                Forms\Components\TextInput::make('consume_group_designation')
                    ->maxLength(255),
                Forms\Components\TextInput::make('consume_group_priority')
                    ->numeric(),
                Forms\Components\TextInput::make('cost_group_guid')
                    ->maxLength(255),
                Forms\Components\TextInput::make('designation')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('unit')
                    ->maxLength(255),
                Forms\Components\TextInput::make('unit_weight')
                    ->numeric()
                    ->step(0.0001),
                Forms\Components\TextInput::make('length')
                    ->numeric()
                    ->step(0.0001),
                Forms\Components\TextInput::make('width')
                    ->numeric()
                    ->step(0.0001),
                Forms\Components\TextInput::make('height')
                    ->numeric()
                    ->step(0.0001),
                Forms\Components\TextInput::make('surface')
                    ->numeric()
                    ->step(0.0001),
                Forms\Components\TextInput::make('quantity')
                    ->numeric()
                    ->step(0.0001),
                Forms\Components\TextInput::make('angle1')
                    ->numeric()
                    ->step(0.0001),
                Forms\Components\TextInput::make('angle2')
                    ->numeric()
                    ->step(0.0001),
                Forms\Components\TextInput::make('bar_length')
                    ->numeric()
                    ->step(0.0001),
                Forms\Components\TextInput::make('position')
                    ->maxLength(255),
                Forms\Components\TextInput::make('short_position')
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_extra')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('designation')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('project.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('component.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category_designation')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit_weight')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('length')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('width')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('height')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('surface')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_extra')
                    ->boolean(),
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
                Tables\Filters\SelectFilter::make('project_id')
                    ->relationship('project', 'name'),
                Tables\Filters\SelectFilter::make('component_id')
                    ->relationship('component', 'name'),
                Tables\Filters\TernaryFilter::make('is_extra'),
                Tables\Filters\Filter::make('has_weight')
                    ->query(fn ($query) => $query->where('unit_weight', '>', 0)),
                Tables\Filters\Filter::make('has_dimensions')
                    ->query(fn ($query) => $query->where(function ($q) {
                        $q->where('length', '>', 0)
                          ->orWhere('width', '>', 0)
                          ->orWhere('height', '>', 0);
                    })),
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
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'view' => Pages\ViewArticle::route('/{record}'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}

