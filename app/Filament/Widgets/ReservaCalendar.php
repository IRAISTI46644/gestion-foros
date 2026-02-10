<?php

namespace App\Filament\Widgets;

use App\Models\Reserva;
use App\Filament\Resources\ReservaResource;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class ReservaCalendar extends FullCalendarWidget
{
    /**
     * Esta función consulta las reservas de la base de datos y las dibuja.
     */
    public function fetchEvents(array $fetchInfo): array
    {
        return Reserva::query()
            ->where('start_time', '>=', $fetchInfo['start'])
            ->where('end_time', '<=', $fetchInfo['end'])
            ->get()
            ->map(function (Reserva $reserva) {
                return [
                    'id' => $reserva->id,
                    'title' => "{$reserva->foro->nombre} - " . ($reserva->usuario->name ?? 'Sicom'),
                    'start' => $reserva->start_time,
                    'end' => $reserva->end_time,
                    'url' => ReservaResource::getUrl('edit', ['record' => $reserva]),
                    // Rosa para cabinas, Azul para foros (Compatible con Modo Oscuro)
                    'color' => str_contains(strtolower($reserva->foro->nombre), 'cabina') ? '#db2777' : '#3b82f6',
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
        ];
    }
}