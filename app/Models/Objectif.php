<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Objectif extends Model
{
    protected $fillable = [
        'user_id',
        'saison',
        'objectif_production',
        'objectif_ca',
        'objectif_surface',
    ];

    protected $casts = [
        'objectif_production' => 'decimal:2',
        'objectif_ca' => 'decimal:2',
        'objectif_surface' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}