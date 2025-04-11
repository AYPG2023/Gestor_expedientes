<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carpeta extends Model
{
    //
    protected $fillable = ['nombre'];

    public function archivos()
    {
        return $this->hasMany(Archivo::class);
    }
}
