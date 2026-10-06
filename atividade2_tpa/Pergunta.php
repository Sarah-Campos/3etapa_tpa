<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pergunta extends Model
{
    use HasFactory;

    // Campos que podem ser salvos em massa
    protected $fillable = [
        'evento_id',
        'user_id',
        'texto',
    ];

    // Ticket #003 - Cada pergunta pertence a UM usuario (N:1)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
