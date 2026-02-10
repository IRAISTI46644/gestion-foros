<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\Select;
use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Forms\Get;

class CustomRegister extends BaseRegister
{
    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->schema([
                    $this->getNameFormComponent(),
                    $this->getEmailFormComponent(),
                    $this->getPasswordFormComponent(),
                    $this->getPasswordConfirmationFormComponent(),
                    
                    // 1. Selector de Dirección Principal
                    Select::make('direccion')
                        ->label('Dirección General')
                        ->options([
                            // Acceso HelpDesk Únicamente
                            'administ' => 'Dirección de Administración y Finanzas',
                            'juridico' => 'Dirección de Asuntos Jurídicos',
                            'difusion' => 'Subdirección de Difusión y Promoción',
                            'it' => 'Dirección de Infraestructura Tecnológica',
                            'noticias' => 'Noticias',
                            // Acceso HelpDesk + Reservas
                            'tv' => 'Dirección de Televisión',
                            'radio' => 'Dirección de Radio',
                        ])
                        ->live()
                        ->required(),

                    // 2. Selector de Sub-área (Sub-direcciones y Departamentos)
                    Select::make('area')
                        ->label('Sub-área / Departamento')
                        ->required()
                        ->visible(fn (Get $get) => filled($get('direccion')))
                        ->options(fn (Get $get): array => match ($get('direccion')) {
                            'administ' => [
                                'Recursos Financieros' => 'Recursos Financieros',
                                'Factor Humano' => 'Factor Humano',
                                'Recursos Materiales' => 'Recursos Materiales',
                                'Evaluacion' => 'Desarrollo Administrativo y Evaluación',
                            ],
                            'juridico' => ['Procedimientos Legales' => 'Procedimientos Legales'],
                            'difusion' => [
                                'Departamento de Difusión' => 'Departamento de Difusión',
                                'Proyectos Especiales' => 'Proyectos Especiales',
                            ],
                            'it' => [
                                'Ingeniería de Televisión' => 'Ingeniería de Televisión',
                                'Ingeniería de Radio' => 'Ingeniería de Radio',
                                'Tecnologías de la Información' => 'Tecnologías de la Información',
                            ],
                            'noticias' => [
                                'Edición' => 'Edición',
                                'Redacción' => 'Redacción',
                                'Reporteros' => 'Reporteros',
                                'Redes Sociales Noticias' => 'Redes Sociales Noticias',
                            ],
                            'tv' => [
                                'Producción' => 'Producción',
                                'Islas de Edición' => 'Islas de Edición',
                                'Programación y Continuidad' => 'Programación y Continuidad',
                                'Deportes' => 'Deportes',
                            ],
                            'radio' => [
                                'Producción y Operación' => 'Producción y Operación',
                                'Continuidad' => 'Continuidad',
                            ],
                            default => [],
                        }),
                ])
                ->statePath('data'),
        ];
    }
}