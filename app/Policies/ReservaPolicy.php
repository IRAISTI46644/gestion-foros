<?php

namespace App\Policies;

use App\Models\Reserva;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReservaPolicy
{
    use HandlesAuthorization;

    /**
     * Determina quién ve el módulo "Control de Reservas" en el menú.
     */
    public function viewAny(User $user): bool
    {
        // Solo Admin (ID 1), Dirección de TV y Dirección de Radio pueden ver.
        return $user->id === 1 || 
               $user->direccion === 'tv' || 
               $user->direccion === 'radio';
    }

    /**
     * Determina quién puede ver el detalle de una reserva específica.
     */
    public function view(User $user, Reserva $reserva): bool
    {
        // El Admin ve todas; los usuarios solo ven las que ellos hicieron.
        return $user->id === 1 || $user->id === $reserva->user_id;
    }

    /**
     * Determina quién puede crear reservas desde el panel administrativo.
     */
    public function create(User $user): bool
    {
        // Solo permitimos si son de las áreas técnicas autorizadas.
        return $user->id === 1 || 
               $user->direccion === 'tv' || 
               $user->direccion === 'radio';
    }

    /**
     * Determina quién puede editar una reserva existente.
     */
    public function update(User $user, Reserva $reserva): bool
    {
        // Solo el Admin o el dueño de la reserva pueden modificarla.
        return $user->id === 1 || $user->id === $reserva->user_id;
    }

    /**
     * Determina quién puede borrar una reserva.
     */
    public function delete(User $user, Reserva $reserva): bool
    {
        // Por seguridad en SICOM, solo el Administrador general puede borrar registros.
        return $user->id === 1;
    }
}