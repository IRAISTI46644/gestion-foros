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
            // Botón de Gestión rápida para el Administrador
            Actions\Action::make('gestionar_ticket')
                ->label('Gestionar Ticket (Admin)')
                ->icon('heroicon-m-adjustments-horizontal')
                ->color('danger') 
                ->visible(fn () => Auth::user()?->id === 1) // Solo el Admin ID 1 lo ve
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
                    // 1. Guardamos la respuesta y el nuevo estado
                    $record->update($data);
                    
                    // 2. Disparamos la notificación al usuario
                    TicketResource::afterSave($record);

                    // 3. Alerta de éxito visual para el Admin
                    Notification::make()
                        ->title('Ticket actualizado y notificación enviada')
                        ->success()
                        ->send();
                }),
                
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * IMPORTANTE: Este método se ejecuta si editas el ticket 
     * usando el botón "Guardar" normal de abajo.
     */
    protected function afterSave(): void
    {
        // Evitamos que se pierda la notificación en ediciones estándar
        TicketResource::afterSave($this->record);
    }
}