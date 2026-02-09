<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reserva extends Model
{
    protected $fillable = [
        'foro_id', 
        'user_id', 
        'start_time', 
        'end_time', 
        'google_event_id'
    ];

    // Indicar a Laravel que estos campos son fechas reales
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Relación inversa: Una reserva pertenece a un foro.
     */
    public function foro(): BelongsTo
    {
        return $this->belongsTo(Foro::class);
    }

    /**
     * Relación: Una reserva pertenece a un usuario.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}