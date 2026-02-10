<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TicketPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true; 
    }

    /**
     * IMPORTANTE: Permitimos ver el ticket si es el dueño o el admin.
     * Esto habilitará el botón "View" en Filament.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        return $user->id === 1 || $user->id === $ticket->user_id;
    }

    public function create(User $user): bool
    {
        return true; 
    }

    /**
     * Solo el Admin (ID 1) puede usar el botón de Guardar/Editar.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        return $user->id === 1;
    }

    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->id === 1;
    }
}