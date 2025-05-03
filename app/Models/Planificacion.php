<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Planificacion extends Model
{
    protected $table = "planificacion";
    protected $primaryKey = 'idplanificacion';
    protected $fillable = [
        'nombre','detalle','url','fecha_create','asignatura_idasignatura'
    ];
	public $timestamps = false;
}
