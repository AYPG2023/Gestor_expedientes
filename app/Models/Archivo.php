<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Archivo extends Model
{
    protected $fillable = [
        'nombre',
        'ruta',
        'tipo',
        'tamano',
        'user_id',
        'subido_en',
        'modificado_en',
    ];

    public $timestamps = true;

    protected $casts = [
        'subido_en' => 'datetime',
        'modificado_en' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function carpeta()
    {
        return $this->belongsTo(Carpeta::class);
    }


}

// Compare this snippet from app/Models/User.php: