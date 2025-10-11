<?php

namespace App\Filament\Resources\ManDayResource\Pages;

use App\Filament\Resources\ManDayResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListManDays extends ListRecords
{
    protected static string $resource = ManDayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

