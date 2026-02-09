<?php

namespace App\Filament\Pages;

use App\Models\Foro;
use App\Models\Reserva;
use Filament\Pages\Page;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class HacerReserva extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static string $view = 'filament.pages.hacer-reserva';
    protected static ?string $title = 'Reservar Espacio SICOM';

    public $selectedForoId = null;
    public $foroNombre = ''; 
    public $tipoEspacio = 'Espacio'; // Detecta si es Foro o Cabina
    public $selectedDate = null;
    public $selectedTime = null;
    
    public $horarios = ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00'];

    public function seleccionarForo($id)
    {
        $foro = Foro::find($id);
        if ($foro) {
            $this->selectedForoId = $id;
            $this->foroNombre = $foro->nombre;
            
            // Lógica para detectar el tipo de espacio basándose en el nombre
            $this->tipoEspacio = str_contains(strtolower($foro->nombre), 'cabina') ? 'Cabina' : 'Foro';
            
            $this->selectedTime = null;
        }
    }

    public function getHorariosOcupados()
    {
        if (!$this->selectedForoId || !$this->selectedDate) return [];

        return Reserva::where('foro_id', $this->selectedForoId)
            ->whereDate('start_time', $this->selectedDate)
            ->pluck('start_time')
            ->map(fn($date) => $date->format('H:i'))
            ->toArray();
    }

    public function guardarReserva()
    {
        if (!$this->selectedForoId || !$this->selectedDate || !$this->selectedTime) {
            Notification::make()->title('Faltan datos de selección')->danger()->send();
            return;
        }

        Reserva::create([
            'foro_id' => $this->selectedForoId,
            'user_id' => Auth::id(),
            'start_time' => Carbon::parse($this->selectedDate . ' ' . $this->selectedTime),
            'end_time' => Carbon::parse($this->selectedDate . ' ' . $this->selectedTime)->addHours(1),
        ]);

        Notification::make()->title('¡Reserva Exitosa!')->success()->send();
        return redirect()->to('/admin/reservas');
    }
}