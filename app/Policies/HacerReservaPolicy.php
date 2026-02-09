<?php

namespace App\Policies;

use App\Models\HacerReserva;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class HacerReservaPolicy
{
    use HandlesAuthorization;

    /**
     * Determina quién puede ver el acceso al módulo en el menú lateral.
     */
    public function viewAny(User $user): bool
    {
        // Solo Admin (ID 1), Dirección de TV y Dirección de Radio pueden ver.
        return $user->id === 1 || 
               $user->direccion === 'tv' || 
               $user->direccion === 'radio';
    }

    /**
     * Determina quién puede ver los detalles de una reserva.
     */
    public function view(User $user, HacerReserva $hacerReserva): bool
    {
        // El Admin ve todo; los usuarios ven solo sus propias reservas.
        return $user->id === 1 || $user->id === $hacerReserva->user_id;
    }

    /**
     * Determina quién puede crear nuevas reservas.
     */
    public function create(User $user): bool
    {
        // Solo permitimos crear si son de las áreas autorizadas.
        return $user->id === 1 || 
               $user->direccion === 'tv' || 
               $user->direccion === 'radio';
    }

    /**
     * Determina quién puede editar una reserva.
     */
    public function update(User $user, HacerReserva $hacerReserva): bool
    {
        // Solo el Admin o el dueño de la reserva pueden editarla.
        return $user->id === 1 || $user->id === $hacerReserva->user_id;
    }

    /**
     * Determina quién puede eliminar una reserva.
     */
    public function delete(User $user, HacerReserva $hacerReserva): bool
    {
        // Recomendado: solo el Admin puede borrar registros definitivos.
        return $user->id === 1;
    }
}