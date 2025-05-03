<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Tcontenido extends Model
{
    protected $table = "tipocontenido";
    protected $primaryKey = 'idtipo';
    protected $fillable = [
        'nombre','detalle','estado'
    ];
    public $timestamps = false;
}
