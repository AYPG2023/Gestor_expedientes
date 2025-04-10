<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */


    public function run(): void
    {
        $areas = [
            ' Dep.Recursos Humanos',
            ' Dep.Finanzas',
            ' Dep.TI',
            'Archivo General',
            ' Dep.Jurídico',
            'Coordinación',
            'Dirección',
            ' Dep.Administrativo',
            'Dep.Social',
            ' Dep.Cartera',
            ' Dep.Registro'
        ];

        foreach ($areas as $nombre) {
            Area::firstOrCreate(['nombre' => $nombre]);
        }
    }
}
