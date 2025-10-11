<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Models\Task;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    // protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Tasks';

    protected static ?string $modelLabel = 'Task';

    protected static ?string $pluralModelLabel = 'Tasks';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\DatePicker::make('deadline')
                    ->nullable(),
                Forms\Components\Select::make('priority')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        'urgent' => 'Urgent',
                    ])
                    ->default('medium')
                    ->required(),
                Forms\Components\Toggle::make('is_finished')
                    ->default(false),
                Forms\Components\DateTimePicker::make('time_finished')
                    ->nullable(),
                Forms\Components\Select::make('from_user_id')
                    ->relationship('fromUser', 'name')
                    ->required()
                    ->label('From User'),
                Forms\Components\Select::make('to_user_id')
                    ->relationship('toUser', 'name')
                    ->required()
                    ->label('To User'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->tooltip(fn (string $state): string => $state)
                    ->searchable(),
                Tables\Columns\TextColumn::make('fromUser.name')
                    ->searchable()
                    ->sortable()
                    ->label('From'),
                Tables\Columns\TextColumn::make('toUser.name')
                    ->searchable()
                    ->sortable()
                    ->label('To'),
                Tables\Columns\TextColumn::make('deadline')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'gray',
                        'medium' => 'blue',
                        'high' => 'orange',
                        'urgent' => 'red',
                    })
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_finished')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('time_finished')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Filters\SelectFilter::make('priority')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        'urgent' => 'Urgent',
                    ]),
                Tables\Filters\TernaryFilter::make('is_finished'),
                Tables\Filters\SelectFilter::make('from_user_id')
                    ->relationship('fromUser', 'name')
                    ->label('From User'),
                Tables\Filters\SelectFilter::make('to_user_id')
                    ->relationship('toUser', 'name')
                    ->label('To User'),
                Tables\Filters\Filter::make('deadline_range')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['from'],
                                fn ($query, $date) => $query->whereDate('deadline', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn ($query, $date) => $query->whereDate('deadline', '<=', $date),
                            );
                    }),
                Tables\Filters\Filter::make('overdue')
                    ->query(fn ($query) => $query->where('deadline', '<', now())->where('is_finished', false)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('mark_complete')
                    ->icon('heroicon-o-check')
                    ->action(function (Task $record) {
                        $record->update([
                            'is_finished' => true,
                            'time_finished' => now(),
                        ]);
                    })
                    ->visible(fn (Task $record) => !$record->is_finished),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('mark_complete')
                        ->icon('heroicon-o-check')
                        ->action(function ($records) {
                            $records->each->update([
                                'is_finished' => true,
                                'time_finished' => now(),
                            ]);
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
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'view' => Pages\ViewTask::route('/{record}'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}

