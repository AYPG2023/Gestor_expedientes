<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermisosSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = [
            // Gestión de usuarios
            'create_user',
            'edit_user',
            'delete_user',
            'view_user',

            // Creacion de roles
            'create_role',
            'delete_role',
            'view_role',

            // Archivos
            'upload_files',
            'download_files',
            'delete_files',
            'edit_files',

            // Creacion de carpetas
            'create_folders',
            'delete_folders',
            'edit_folders',

            //Union de archivos
            'merge_files',

            // Creación de reportes
            'create_reports',
            'view_reports',
            'delete_reports',
            'edit_reports',

            // Gestión de permisos
            'create_permissions',
            'edit_permissions',
            'delete_permissions',
            'view_permissions',

        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }
    }
}
