<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Botón de Gestión para el Admin
            Actions\Action::make('gestionar_ticket')
                ->label('Gestionar Ticket (Admin)')
                ->icon('heroicon-m-adjustments-horizontal')
                ->color('danger') 
                ->visible(fn () => Auth::user()?->id === 1) // Solo visible para el ID 1
                ->form([
                    Forms\Components\Select::make('estado')
                        ->options([
                            'abierto' => 'Abierto',
                            'en proceso' => 'En Proceso',
                            'resuelto' => 'Resuelto',
                            'rechazado' => 'Rechazado',
                        ])
                        ->required(),
                    
                    Forms\Components\Textarea::make('respuesta_admin')
                        ->label('Respuesta o Solución Técnica')
                        ->rows(6)
                        ->placeholder('Escribe aquí la respuesta para el usuario...'),
                ])
                ->action(function (array $data, $record) {
                    // Actualiza los datos del ticket
                    $record->update($data);
                    
                    // DISPARA LA NOTIFICACIÓN AL USUARIO (Importante)
                    TicketResource::afterSave($record);

                    Notification::make()
                        ->title('Ticket actualizado y notificación enviada')
                        ->success()
                        ->send();
                }),
                
            Actions\DeleteAction::make(),
        ];
    }

    // Se ejecuta si alguien edita el ticket de forma normal (fuera del botón)
    protected function afterSave(): void
    {
        TicketResource::afterSave($this->record);
    }
    
}