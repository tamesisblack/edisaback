<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Temporada extends Model
{
    protected $table = "temporadas";
    protected $primaryKey = 'id_temporada';
    protected $fillable = [
        'temporada','estado',
    ];
}
