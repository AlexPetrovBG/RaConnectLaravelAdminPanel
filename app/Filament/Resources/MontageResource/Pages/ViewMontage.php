<?php

namespace App\Filament\Resources\MontageResource\Pages;

use App\Filament\Resources\MontageResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMontage extends ViewRecord
{
    protected static string $resource = MontageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

