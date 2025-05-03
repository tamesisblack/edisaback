<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Tarea extends Model
{
    protected $table = "tarea";
    protected $primaryKey = 'idtarea';
    protected $fillable = [
        'detalle', 'fecha_inicio', 'fecha_fin', 'archivo_idarchivo', 'actividad_idactividad', 'curso_idcurso','nombre_actividad','tipo','estado'
    ];
	public $timestamps = false;
}
