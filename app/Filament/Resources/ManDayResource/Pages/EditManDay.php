<?php

namespace App\Filament\Resources\ManDayResource\Pages;

use App\Filament\Resources\ManDayResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditManDay extends EditRecord
{
    protected static string $resource = ManDayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

