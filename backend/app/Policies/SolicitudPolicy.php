<?php

namespace App\Policies;

use App\Models\Solicitud;
use App\Models\User;

class SolicitudPolicy
{
    public function view(User $user, Solicitud $solicitud): bool
    {
        if ($user->tienePermiso('solicitudes.gestionar')) {
            return true;
        }

        return $user->isSucursal() && $solicitud->usuario_id === $user->id;
    }
}
