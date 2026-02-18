<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    /**
     * Se ejecuta justo después de que el usuario crea el ticket.
     * Enviamos la notificación al Administrador (ID 1).
     */
    protected function afterCreate(): void
    {
        // Llamamos al método estático definido en el Resource para notificar al admin
        TicketResource::afterCreate($this->record);
    }
}