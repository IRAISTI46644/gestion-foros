<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    /**
     * Se ejecuta justo después de que el usuario crea el ticket.
     */
    protected function afterCreate(): void
    {
        // Notifica al Admin (ID 1) sobre el nuevo reporte
        TicketResource::afterCreate($this->record);
    }
}