<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TicketPolicy
{
    use HandlesAuthorization;

    /**
     * Todos los usuarios registrados pueden entrar al HelpDesk.
     */
    public function viewAny(User $user): bool
    {
        return true; 
    }

    /**
     * Un usuario solo puede ver su propio ticket, a menos que sea Admin.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        return $user->id === 1 || $user->id === $ticket->user_id;
    }

    /**
     * Cualquier empleado de cualquier área puede crear un ticket.
     */
    public function create(User $user): bool
    {
        return true; 
    }

    /**
     * Solo el Admin puede actualizar (responder/cerrar) los tickets.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        return $user->id === 1;
    }

    /**
     * Solo el Admin puede borrar tickets si es necesario.
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->id === 1;
    }
}