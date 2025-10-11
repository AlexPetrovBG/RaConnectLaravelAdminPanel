<?php

namespace App\Filament\Resources\MontageResource\Pages;

use App\Filament\Resources\MontageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMontage extends EditRecord
{
    protected static string $resource = MontageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

