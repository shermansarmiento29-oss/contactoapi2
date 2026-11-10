<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    use HasFactory, SoftDeletes; // Activamos los factories y el borrado lógico

    // Campos que permitimos guardar masivamente
    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'email',
    ];

    // Relación: Un contacto pertenece a un usuario
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}