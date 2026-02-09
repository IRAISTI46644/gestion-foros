<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Ticket extends Model
{
    protected $fillable = [
        'user_id', 
        'titulo', 
        'descripcion', 
        'categoria', 
        'prioridad', 
        'estado', 
        'fecha_limite',
        'respuesta_admin'
    ];

    protected $casts = [
        'fecha_limite' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($ticket) {
            if (Auth::check()) {
                $ticket->user_id = Auth::id();
            }

            // Si el formulario no envía prioridad, asignamos 'baja' por defecto
            if (empty($ticket->prioridad)) {
                $ticket->prioridad = 'baja';
            }

            $horas = match ($ticket->prioridad) {
                'alta' => 4,
                'media' => 12,
                'baja' => 24,
                default => 24,
            };

            $ticket->fecha_limite = now()->addHours($horas);
            
            // Aseguramos un estado inicial para que la tabla no falle
            if (empty($ticket->estado)) {
                $ticket->estado = 'abierto';
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}