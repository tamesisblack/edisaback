<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    protected $table = "actividad";
    protected $primaryKey = 'idactividad';
    protected $fillable = [
        'nombre','id_tema','page','url','fecha_create','libro_idlibro','estado'
    ];
	public $timestamps = false;
}
