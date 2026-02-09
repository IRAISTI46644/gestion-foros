<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Foro;
use Illuminate\Auth\Access\HandlesAuthorization;

class ForoPolicy
{
    use HandlesAuthorization;

    /**
     * Determina quién puede ver la lista de foros y el acceso en el menú lateral.
     */
    public function viewAny(User $user): bool
    {
        // El Admin (ID 1) ve todo.
        // TV y Radio tienen acceso a ver y reservar foros.
        // Importante: Los valores 'tv' y 'radio' deben ser los mismos que en CustomRegister.
        return $user->id === 1 || 
               $user->direccion === 'tv' || 
               $user->direccion === 'radio';
    }

    /**
     * Opcional: Si quieres que solo ellos puedan crear registros de foros.
     */
    public function create(User $user): bool
    {
        return $user->id === 1 || $user->direccion === 'tv' || $user->direccion === 'radio';
    }
}