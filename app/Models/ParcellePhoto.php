<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParcellePhoto extends Model
{
    protected $fillable = [
        'parcelle_id',
        'photo_path',
        'legende',
    ];

    public function parcelle()
    {
        return $this->belongsTo(Parcelle::class);
    }
}