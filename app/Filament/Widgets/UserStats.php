<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStats extends BaseWidget
{
    /**
     * Define cuántas columnas ocupa el widget completo en el grid.
     * 'full' asegura que use todo el ancho disponible.
     */
    protected int | string | array $columnSpan = 'full';

    /**
     * Define en cuántas columnas se dividen las tarjetas internas.
     * En escritorio (lg) se verán 3, en tablet (md) 2, y en móvil 1 automáticamente.
     */
    protected function getColumns(): int
    {
        return 3;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Usuarios Totales', User::count())
                ->description('Personal registrado en SICOM')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Personal TV', User::where('direccion', 'tv')->count())
                ->description('Dirección de Televisión')
                ->color('info'),
            
            Stat::make('Personal Radio', User::where('direccion', 'radio')->count())
                ->description('Dirección de Radio')
                ->color('warning'),
        ];
    }
}