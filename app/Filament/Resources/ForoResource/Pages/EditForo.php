<?php

namespace App\Filament\Resources\ForoResource\Pages;

use App\Filament\Resources\ForoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditForo extends EditRecord
{
    protected static string $resource = ForoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
