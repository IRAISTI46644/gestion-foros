<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\UserStats;
use App\Filament\Widgets\ListadoUsuarios;

class GestionUsuarios extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $title = 'Administración de Usuarios';
    protected static ?string $navigationLabel = 'Usuarios SICOM';
    
    // Esta es la ruta hacia el archivo .blade.php
    protected static string $view = 'filament.pages.gestion-usuarios';

    protected function getHeaderWidgets(): array
    {
        return [
            UserStats::class,
            ListadoUsuarios::class,
        ];
    }
    

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->id === 1 || auth()->user()->direccion === 'admin';
    }
}