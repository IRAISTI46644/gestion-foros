<?php

namespace App\Filament\Resources\ForoResource\Pages;

use App\Filament\Resources\ForoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListForos extends ListRecords
{
    protected static string $resource = ForoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
