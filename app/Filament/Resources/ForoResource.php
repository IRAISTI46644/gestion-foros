<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ForoResource\Pages;
use App\Models\Foro;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class ForoResource extends Resource
{
    protected static ?string $model = Foro::class;

    // Cambiamos el icono por uno de calendario/mapa
    protected static ?string $navigationIcon = 'heroicon-o-home-modern';
    
    protected static ?string $navigationLabel = 'Gestión de Foros';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información del Espacio')
                    ->description('Configura los detalles del foro o cabina disponible para reserva.')
                    ->schema([
                        TextInput::make('nombre')
                            ->label('Nombre del Foro')
                            ->required()
                            ->placeholder('Ej. Foro 1, Cabina de Audio...')
                            ->maxLength(300),

                        Textarea::make('descripcion')
                            ->label('Descripción / Equipo')
                            ->placeholder('Detalla qué equipo hay en este espacio...')
                            ->rows(3),

                        
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
               

                TextColumn::make('nombre')
                    ->label('Nombre del Espacio')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->limit(50) // Corta el texto largo
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Aquí podrías añadir filtros por estado si lo deseas
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
        return [
            // Aquí podrías añadir una relación para ver las reservas de este foro
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListForos::route('/'),
            'create' => Pages\CreateForo::route('/create'),
            'edit' => Pages\EditForo::route('/{record}/edit'),
        ];
    }
    public static function canViewAny(): bool
    {
        // Solo permite el acceso si el ID del usuario es 1 (Administrador).
        return auth()->id() === 1;
        
        // Opcionalmente, puedes usar el correo si prefieres:
        // return auth()->user()?->email === 'tu-correo@admin.com';
    }
}