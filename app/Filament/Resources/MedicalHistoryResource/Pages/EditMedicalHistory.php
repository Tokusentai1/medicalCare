<?php

namespace App\Filament\Resources\MedicalHistoryResource\Pages;

use App\Filament\Resources\MedicalHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMedicalHistory extends EditRecord
{
    protected static string $resource = MedicalHistoryResource::class;

    public static function shouldRegisterSpotlight(): bool
    {
        return false;
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
