<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservaResource\Pages;
use App\Models\Reserva;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class ReservaResource extends Resource
{
    protected static ?string $model = Reserva::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    
    protected static ?string $navigationLabel = 'Control de Reservas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detalles de la Cita SICOM')
                    ->description('Administración manual de horarios y asignación de espacios.')
                    ->schema([
                        Grid::make(2)->schema([
                            // Selector de Foro/Cabina
                            Select::make('foro_id')
                                ->label('Espacio Seleccionado')
                                ->relationship('foro', 'nombre')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->columnSpan(1),

                            // Selector de Usuario
                            Select::make('user_id')
                                ->label('Usuario Responsable')
                                ->relationship('usuario', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->columnSpan(1),
                        ]),

                        Grid::make(2)->schema([
                            // Fecha y Hora de Inicio
                            DateTimePicker::make('start_time')
                                ->label('Inicio de la Reserva')
                                ->required()
                                ->native(false) // Calendario moderno de Filament
                                ->displayFormat('d/m/Y H:i')
                                ->minutesStep(30)
                                ->afterStateUpdated(fn ($state, $set) => $set('end_time', \Carbon\Carbon::parse($state)->addHour()))
                                ->reactive(),

                            // Fecha y Hora de Fin
                            DateTimePicker::make('end_time')
                                ->label('Fin de la Reserva')
                                ->required()
                                ->native(false)
                                ->displayFormat('d/m/Y H:i')
                                ->minutesStep(30)
                                ->after('start_time'),
                        ]),

                        // Campo informativo para integración futura
                        Forms\Components\TextInput::make('google_event_id')
                            ->label('Referencia de Calendario Externo')
                            ->disabled()
                            ->placeholder('Sincronización automática pendiente')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('foro.nombre')
                    ->label('Espacio')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('usuario.name')
                    ->label('Reservado por')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('start_time')
                    ->label('Fecha y Hora Inicio')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->description(fn (Reserva $record): string => $record->start_time->diffForHumans()),

                TextColumn::make('end_time')
                    ->label('Termina')
                    ->dateTime('H:i')
                    ->sortable(),
            ])
            ->defaultSort('start_time', 'desc')
            ->filters([
                Tables\Filters\Filter::make('proximas')
                    ->label('Ver Reservas Futuras')
                    ->query(fn (Builder $query) => $query->where('start_time', '>=', now())),
                
                Tables\Filters\SelectFilter::make('foro_id')
                    ->label('Filtrar por Espacio')
                    ->relationship('foro', 'nombre'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReservas::route('/'),
            'create' => Pages\CreateReserva::route('/create'),
            'edit' => Pages\EditReserva::route('/{record}/edit'),
        ];
    }
}