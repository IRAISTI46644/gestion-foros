<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Models\Ticket;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationLabel = 'Modulo de ayuda';

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

                        Forms\Components\Textarea::make('respuesta_admin')
                            ->label('Escribir Respuesta (Solo Admin)')
                            ->visible(fn () => auth()->id() === 1) 
                            ->columnSpanFull(),

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
                    ->searchable(),

                Tables\Columns\TextColumn::make('estado')
                    ->badge()
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
                        'rechazado' => 'Rechazado',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(), 
                Tables\Actions\EditAction::make(), 
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * LÓGICA: El usuario crea el ticket -> LE LLEGA AL ADMIN (ID 1)
     */
    public static function afterCreate($record): void
    {
        // Buscamos al administrador para avisarle
        $admin = User::find(1); 

        if ($admin) {
            Notification::make()
                ->title('Nuevo Ticket en SICOM')
                ->icon('heroicon-o-ticket')
                ->danger() // Rojo para resaltar
                ->body("**{$record->user->name}** ha reportado: {$record->titulo}")
                ->actions([
                    Action::make('atender')
                        ->label('Atender Reporte')
                        ->url(static::getUrl('edit', ['record' => $record]))
                        ->markAsRead(),
                ])
                ->sendToDatabase($admin); // Llega a la campana del Admin
        }
    }

    /**
     * LÓGICA: El Admin responde/actualiza -> LE LLEGA AL USUARIO
     */
    public static function afterSave($record): void
    {
        // El destinatario ahora es el dueño del ticket
        if ($record->user) {
            Notification::make()
                ->title('Respuesta de SICOM')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->info() // Azul informativo
                ->body("Tu reporte ha sido actualizado. Estado: **" . ucfirst($record->estado) . "**")
                ->sendToDatabase($record->user) // Llega a la campana del Usuario
                ->send(); // Toast flotante inmediato
        }
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }
    public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();

    // Si el usuario NO es el administrador (ID 1), solo ve sus propios tickets
    if (auth()->id() !== 1) {
        $query->where('user_id', auth()->id());
    }

    return $query;
}
}