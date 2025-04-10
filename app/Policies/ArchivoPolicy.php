<?php

namespace App\Policies;

use App\Models\Archivo;
use App\Models\User;

class ArchivoPolicy
{
    /**
     * Ver la lista de archivos
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ver archivos');
    }

    /**
     * Ver un archivo específico
     */
    public function view(User $user, Archivo $archivo): bool
    {
        return $user->can('ver archivos');
    }

    /**
     * Subir (crear) un archivo
     */
    public function create(User $user): bool
    {
        return $user->can('subir archivos') || $user->can('crear archivos');
    }

    /**
     * Editar un archivo
     */
    public function update(User $user, Archivo $archivo): bool
    {
        return $user->can('editar archivos');
    }

    /**
     * Eliminar un archivo
     */
    public function delete(User $user, Archivo $archivo): bool
    {
        return $user->can('eliminar archivos');
    }

    /**
     * Restaurar un archivo (si usas soft deletes)
     */
    public function restore(User $user, Archivo $archivo): bool
    {
        return $user->can('editar archivos');
    }

    /**
     * Eliminar permanentemente
     */
    public function forceDelete(User $user, Archivo $archivo): bool
    {
        return $user->can('eliminar archivos');
    }
}
