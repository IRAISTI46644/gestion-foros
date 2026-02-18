<?php

namespace App\Filament\Widgets;

use App\Models\Reserva;
use App\Filament\Resources\ReservaResource;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Illuminate\Database\Eloquent\Model;

class ReservaCalendar extends FullCalendarWidget
{
    /**
     * Propiedad pública obligatoria para evitar errores de nivel de acceso.
     */
    public Model | string | null $model = Reserva::class;

    /**
     * Consulta las reservas y genera la URL de edición para cada una.
     */
    public function fetchEvents(array $fetchInfo): array
    {
        return Reserva::query()
            ->with(['foro', 'usuario']) // Carga optimizada de relaciones
            ->where('start_time', '>=', $fetchInfo['start'])
            ->where('end_time', '<=', $fetchInfo['end'])
            ->get()
            ->map(function (Reserva $reserva) {
                return [
                    'id' => $reserva->id,
                    // Título: Nombre del solicitante - Espacio
                    'title' => ($reserva->usuario->name ?? 'Sicom') . " - " . ($reserva->foro->nombre ?? 'Set'),
                    'start' => $reserva->start_time,
                    'end' => $reserva->end_time,
                    
                    /**
                     * Genera la URL directa a la página de edición del recurso.
                     * Al existir 'url', FullCalendar ignora los modales y navega directamente.
                     */
                    'url' => ReservaResource::getUrl('edit', ['record' => $reserva]),
                    
                    // Colores institucionales según el tipo de espacio
                    'color' => str_contains(strtolower($reserva->foro->nombre ?? ''), 'cabina') ? '#db2777' : '#670d36',
                ];
            })
            ->toArray();
    }

    public function config(): array
    {
        return [
            'initialView' => 'timeGridWeek', 
            'locale' => 'es',
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'dayGridMonth,timeGridWeek',
            ],
            // Abrir en la misma pestaña
            'eventClick' => "function(info) {
                if (info.event.url) {
                    window.location.href = info.event.url;
                    info.jsEvent.preventDefault(); 
                }
            }",
        ];
    }
}