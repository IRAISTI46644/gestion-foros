<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Filament\Notifications\Notification;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationLabel = 'Help Desk / Tickets';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detalles del Reporte')
                    ->description('Información técnica enviada al equipo de SICOM.')
                    ->schema([
                        Forms\Components\TextInput::make('titulo')
                            ->disabled(fn ($record) => $record !== null)
                            ->required()
                            ->label('Asunto'),

                        Forms\Components\Select::make('categoria')
                            ->options([
                                'equipo' => 'Equipo no disponible',
                                'falla' => 'Fallas técnicas',
                                'red' => 'Problemas de red',
                            ])
                            ->required()
                            ->disabled(fn ($record) => $record !== null),

                        Forms\Components\Textarea::make('descripcion')
                            ->rows(5)
                            ->required()
                            ->columnSpanFull()
                            ->disabled(fn ($record) => $record !== null),

                        Forms\Components\Placeholder::make('respuesta_admin_display')
                            ->label('Respuesta oficial de SICOM')
                            ->visible(fn ($record) => $record !== null && !empty($record->respuesta_admin))
                            ->content(fn ($record) => new HtmlString('
                                <div style="background-color: #0f172a; border-left: 4px solid #800020; padding: 1.5rem; border-radius: 1rem;">
                                    <p style="color: white; font-size: 0.875rem;">' . e($record?->respuesta_admin) . '</p>
                                </div>
                            '))
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Solicitante')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('titulo')
                    ->label('Asunto')
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.area')
                    ->label('Departamento')
                    ->badge()
                    ->colors([
                        'info' => 'audio',
                        'warning' => 'produccion',
                        'success' => 'sistemas',
                        'gray' => 'camaras',
                    ])
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('estado')
                    ->colors([
                        'warning' => 'abierto',
                        'info' => 'en proceso',
                        'success' => 'resuelto',
                        'danger' => 'rechazado',
                    ]),

                Tables\Columns\TextColumn::make('fecha_limite')
                    ->label('Vence')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Filtrar por Usuario')
                    ->relationship('user', 'name'),
                
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'abierto' => 'Abierto',
                        'en proceso' => 'En Proceso',
                        'resuelto' => 'Resuelto',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    // Método para disparar la alerta de creación
    public static function afterCreate($record): void
    {
        $admins = \App\Models\User::all(); // Solo para probar si llegan las notis
        Notification::make()
            ->title('Nuevo Ticket de Soporte')
            ->danger()
            ->icon('heroicon-o-ticket')
            ->body("**{$record->user->name}** reportó: {$record->titulo}")
            ->sendToDatabase($admins);
    }

    // Método para disparar la alerta de actualización
    public static function afterSave($record): void
    {
        Notification::make()
            ->title('Tu ticket ha sido actualizado')
            ->info()
            ->body("Estado actual: **{$record->estado}**")
            ->sendToDatabase($record->user);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }
}