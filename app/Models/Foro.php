<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage; // Importante para la imagen

class Foro extends Model
{
    // Campos que permitimos guardar
    protected $fillable = ['nombre', 'descripcion', 'imagen'];

    // Relación: Un foro tiene muchas reservas
    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }

    /**
     * Mejora: Obtener la URL de la imagen automáticamente.
     * Si no hay imagen, podrías retornar una por defecto.
     */
    public function getImagenUrlAttribute()
    {
        return $this->imagen 
            ? Storage::url($this->imagen) 
            : 'https://via.placeholder.com/150';
    }
}